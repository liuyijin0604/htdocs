<?php

class SzPortalController extends Controller{

	protected $nonAjax=array('exloclist', 'download', 'palletRpt', 'pltMark');

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	public function actionDelete($id) {
		$model = BillingLine::model()->findByPk($id);

		if (empty($model) && ($model->actual_amount == 0) && ($model->billing_id == 0)) {
			$model->addError('id', 'Actual Amount is not 0 or Bill is exist');
		} else {
			$model->status = 11;
			$model->save();
		}

		$this->ajaxResult($model);
	}
	
	public function actionNotes($id){
		$model=$this->loadModel($id);
		if(!empty($_POST['notes'])){
			$log = Log::add($model, 6, array('notes' => $_POST['notes']));
			$this->ajaxResult($log);
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){
		$model=new ExcoConsol;

		if(isset($_POST['ExcoConsol'])){
			$this->ajaxResult($model); //disabled
			$model->attributes=$_POST['ExcoConsol'];
			$model->status = 10;
			if(empty($_POST['recs'])){
				$model->addError('id', 'No shipment selected!');
			}else{
				$model->save();
				$ap = array();
				foreach($_POST['recs'] as $pid){
					$p = ExParcel::model()->findByPk($pid);
					$p->consol_id = $model->id;
					$fa = $p->cnee->fullAddress();
					$p->status = 20;
					foreach($ap as $i=>$a){
						if($p->cnee->state != $a->cnee->state) continue;
						if($p->cnee->city != $a->cnee->city) continue;
						$pct = 0;
						similar_text($fa, $a->cnee->fullAddress(), $pct);
						if($pct >= 98){
							$p->bwf = $p->bwf | 2;
							$a->bwf = $a->bwf | 2;
							$a->save();
							break;
						}
					}
					$p->save();
					$ap[$p->id] = $p;
				}
				//tracking for left over;
				$rs = ExParcel::model()->findAll('status = 18');
				foreach($rs as $r){
					if(Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $r->id, ':t' => 19)) == 0){
						$dpt = empty($r->odepot)? '' : $r->odepot->suburb;
						$r->addTracking(19, '库存货ID、收件人地址或电话重复，留库待发下航班', $dpt);
					}
				}
			}
			$this->ajaxResult($model, array('id','no'));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionMissingAccrual(){
		$model = new ExcoConsol('search');
		$criteria = new CDbCriteria;
	   // $criteria->condition =  "created <= '" . date('Y-m-d',strtotime('-30 days')) ."' and `no` not in ( select billing_ref from billing_line where type = " . BillingLine::BILLING_TYPE_IMPORT . ")";
		$criteria->condition =  "created >= '2017-07-01' and `no` not in ( select billing_ref from billing_line where type = " . BillingLine::BILLING_TYPE_IMPORT . ")";

		$modeldp =  new CActiveDataProvider($model, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'t.id DESC',
			),
			'pagination'=>array(
				'pageSize' => 30,
			)
		));

		$this->render('missing_accrual_list',['modeldp' => $modeldp,'model' => $model]);
	}

	public function actionCreatePlus(){
		$model=new ImcoConsol;
		if(isset($_POST['ImcoConsol'])){
			if(empty($_POST['ps'])){
				$model->addError('id', 'No port selected!');
			}else
			{
				$cs = SzpChannel::model()->findAll('status = 50');
				$dup_allowed = [];
				$p2c = [];
				foreach($cs as $c){
					if(!empty($c->mdata['dupq']) && $c->mdata['dupq'] > 1)
						$dup_allowed[$c->code] = $c->mdata['dupq'];
					$c2p[$c->code] = $c->pod;
				}

				foreach($_POST['ps'] as $k=>$ids){
					$model=new ImcoConsol('create');
					$model->attributes=$_POST['ImcoConsol'];
					$model->status = ImcoConsol::NEW_TYPE;
					$model->pod = isset($c2p[$k])? $c2p[$k] : $k;
					$model->poc = $k;
					$model->service = ImcoConsol::AIRCONSOL;
					$model->mdata["szportalCreate"] = true;
					$model->eta = "0000-00-00";
					if(!empty($_POST['cplm']))
					{
						$model->mdata["szpConsolMaxWeight"]= $_POST['cplm'][$k];
					}

					$the_service=0;
					if (isset($_POST['selected_service'.$k])) {
						foreach ($_POST['selected_service'.$k] as $select) {
							$the_service=$the_service|$select;
						}
					}
					$model->mdata["selected_service"] = $the_service;
					$model->save();
					$ap = array();
					if(!empty($ids)){
						$trans = Yii::app()->db->beginTransaction();
						try{
							foreach(explode(',', $ids) as $pid){
								$p = ImParcel::model()->findByPk($pid);
								$p->consol_id = $model->id;
								$fa = $p->cnee->fullAddress();
								$p->status = 20;
								foreach($ap as $i=>$a){
									if($p->cnee->state != $a->cnee->state) continue;
									if($p->cnee->city != $a->cnee->city) continue;
									if(isset($dup_allowed[$model->poc])) continue;
									$pct = 0;
									similar_text($fa, $a->cnee->fullAddress(), $pct);
									if($pct >= 98){
										$p->bwf = $p->bwf | 2;
										$a->bwf = $a->bwf | 2;
										$a->save();
										break;
									}
								}
								$p->save();
								$ap[$p->id] = $p;
							}
							$trans->commit();
						} catch (Exception $ex) {
							$trans->rollback();
							throw $ex;
						}
					}
				}
			}
			$this->ajaxResult($model, array('id','no'));
		}

		$this->render('create_plus',array(
			'model'=>$model,
		));
	}
	
	public function actionCpSmart(){
		$o = new StdClass;
		foreach($_GET['cp'] as $p=>$wl){
			$o->{$p} = new StdClass;
			$o->{$p}->ids = [];
			$o->{$p}->wl = $wl;
			$o->{$p}->w = 0;
			$o->{$p}->mp = 0;
			$o->{$p}->mw = 0;
			$o->{$p}->ew = 0;
			$o->{$p}->ec = 0;
		}
		$o->sum = new StdClass;
		$dpts = $_GET['dpt'] == 106? [106, 218, 529, 530] : [intval($_GET['dpt'])];
		$o->sum->tot = ExParcel::model()->count('status = 18 AND odpt_id IN ('.implode(',', $dpts).')');
		$o->sum->dup = 0;

		$cs = ExChannel::model()->findAll('status = 50');
		$dup_allowed = [];
		foreach($cs as $c){
			if(!empty($c->mdata['dupq']) && $c->mdata['dupq'] > 1)
				$dup_allowed[$c->code] = $c->mdata['dupq'];
		}
		$dupool = [];

		function isDup(&$dp, $c, $p, $debug = false){
			if(!isset($dp[$c])) $dp[$c] = ['id' => [], 'idno' => [], 'tel' => [], 'fa' => []];

			$vks = ['id' => $p->cnee->cnid_id, 'idno' => empty($p->cnee->cnid_id)? $p->cnee->cnid_no : $p->cnee->cnid->no, 'tel' => $p->cnee->tel, 'fa' => md5($p->cnee->fullAddress().$p->cnee->name)];
			$vcs = [];
			foreach($dp[$c] as $k => $l){
				if(empty($vks[$k])) continue;

				$ac = array_count_values($l);
				$vcs[$k] = isset($ac[$vks[$k]])? $ac[$vks[$k]] : 0;

				$dp[$c][$k][] = $vks[$k];
			}

			if($debug) var_dump($vcs);

			return empty($vcs)? 0 : max($vcs);
		}

		/*function rcvd($pid){
			$sql = 'SELECT dt FROM tracking WHERE pid = :id AND `type` IN (14,15,18) GROUP BY pid ORDER BY dt ASC';
			$dt = Yii::app()->db->createCommand($sql)->bindValues([':id' => $pid])->queryScalar();
			return ceil((time() - strtotime($dt)) / 86400) - 1;
		}*/

		$rs = ExParcel::model()->findAll('status = 18 AND odpt_id IN ('.implode(',', $dpts).')');
		// AND cnee.cnid_no != ''
		$rs2 = ExParcel::model()->with('cnee')->findAll("status IN (12, 15) AND odpt_id IN (".implode(',', $dpts).")");
		if(!empty($rs2)) $rs = array_merge($rs, $rs2);
		$ecd = [];
		$ecp = [];
		$dups = [];
		$nocs = [];
		$wt0s = [];
		$tow = 0;
		foreach($rs as $p){
			if(in_array($p->agent_id, [672])) continue;
			if(!empty($p->mdata['nsn']) && $p->mdata['nsn'] > time()) continue;
			if((floatval($p->weight) == 0 || empty($p->eitems['g'])) && $p->status > 12){
				$wt0s[$p->id] = $p->hbn;
				continue;
			}

			if($_GET['dpt'] != $p->odpt_id && ($p->bwf & 256) > 0){
				//$l = $p->getLocation(true);
				//if(empty($l) || $l->wid != $_GET['dpt']){
					$o->sum->tot--;
					continue;
				//}
			}

			$brs = $p->bestRates();
			$cs = array_values($brs);
			if((empty($brs) || $brs[$cs[0]] == 999) && ($p->status == 18 || $p->bcOnly())){
				$nocs[$p->id] = $p->hbn;
				continue;
			}
			$tow += $p->weight;
			$pc = 0;
			$cst = 0;
			$dup = false;
			foreach($brs as $k=>$v){
				if($v == 999 || ($pc > 0 && empty($_GET['otb']))){
					break;
				}

				if(isset($o->{$k})){
					if(isDup($dupool, $k, $p) < (empty($dup_allowed[$k])? 1 : $dup_allowed[$k])){
						$ot = &$o->{$k};
						if($ot->wl == 0 || ($ot->w + $p->weight) < $ot->wl){
							if($cst > 0 && $v > $cst){
								if(!isset($ecd[$k])) $ecd[$k] = [];
								$ecd[$k][$p->id] = $v - $cst;
								$ecp[$p->id] = $p;
							}else{
								$ot->ids[] = $p->id;
								$ot->w += $p->weight;
								break;
							}
						}else{
							$ot->mw += $p->weight;
							$ot->mp++;
						}
					}else{
						$dup = true;
					}
				}
				if($pc == 0) $cst = $v;
				$pc++;
			}
			
			if($dup) $dups[$p->id] = 1;
		}

		//add extra;
		foreach($ecp as $pid => $p){
			if(empty($_GET['dto']) && isset($dups[$pid])) continue;
			$md = [];
			foreach($_GET['cp'] as $k=>$wl){
				if(empty($ecd[$k][$pid])) continue;
				$md[$k] = $ecd[$k][$pid];
			}
			asort($md);
			foreach($md as $k=>$ec){
				$ot = &$o->{$k};
				if($ot->wl == 0 || ($ot->w + $p->weight) < $ot->wl){
					$ot->ids[] = $p->id;
					$ot->w += $p->weight;
					$ot->ec += $ec;
					$ot->ew += $p->weight;
					unset($ecp[$pid]);
					unset($dups[$pid]);
					break;
				}
			}
		}

		/*
		foreach($_GET['cp'] as $k=>$wl){
			if(empty($ecd[$k])) continue;
			$ot = &$o->{$k};
			asort($ecd[$k]);
			foreach($ecd[$k] as $pid => $ec){
				if(empty($ecp[$pid])) continue;
				$p = $ecp[$pid];
				if($ot->wl == 0 || ($ot->w + $p->weight) < $ot->wl){
					$ot->ids[] = $p->id;
					$ot->w += $p->weight;
					$ot->ec += $ec;
					$ot->ew += $p->weight;
					unset($ecp[$pid]);
					unset($dups[$pid]);
					unset($nocs[$pid]);
				}else{
					break;
				}
			}
		}
		*/

		$o->sum->tow = round($tow);
		$o->sum->dup = sizeof($dups);
		$o->sum->wt0 = $wt0s;
		$o->sum->noc = $nocs;

		foreach($_GET['cp'] as $p=>$wl){
			$o->{$p}->p = sizeof($o->{$p}->ids);
			$o->{$p}->ids = implode(',', $o->{$p}->ids);
			$o->{$p}->ec = round($o->{$p}->ec);
		}

		echo json_encode($o);
	}
	
	public function actionEdi($id){
		$model=$this->loadModel($id);
		if(!empty($_GET['act'])){
			switch($_GET['act']){
				case 'send':
					$edi = $model->newESM(isset($_GET['sub'])? $_GET['sub'] : -1);
					if($edi->send()){
						$model->status = 30;
						$model->save();
					}
				break;
				case 'withdraw':
					$edi = $model->withdrawESM();
					if($edi->send()){
						$model->status = 50;
						$model->save();
					}
				break;
			}
			$this->ajaxResult($edi);
		}
	}

	public function actionSuggest(){
		$rs = ExcoConsol::model()->findAll(array(
				'condition' => '`no` LIKE :n OR awb LIKE :n',
				'params' => array(':n' => '%'.$_GET['term'].'%'),
				'limit'=>20,
				));
		$a = array();
		foreach($rs as $r){
			$a[] = array(
				'value' => $r->id,
				'label' => $r->no.' '.$r->pol,
			);
		}
		echo json_encode($a);
	}

	public function actionUnlock($id){
		$model=$this->loadModel($id);
		if($model->status == 20){
			$model->status = 10;
			$model->custom_log_note = 'Consol unlocked';
			$model->save();
			$this->ajaxResult($model);
		}
	}


	public function actionCompleteConsol($id){
		$model=$this->loadModel($id);
		if($model->status == 20){
			$model->mdata["completeConsol"]=true;
			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionAddParcel($id){
		$model=$this->loadModel($id);
		if(isset($_POST['recs'])){
			if(empty($_POST['recs'])){
				$model->addError('awb', 'Please select shipment');
			}else{
				$exc = ExChannel::model()->find('code = :c', [':c' => $model->poc]);
				$dupq = $exc->mdata['dupq'];

				$trans = Yii::app()->db->beginTransaction();
				try{
					foreach($_POST['recs'] as $r){
						$p = ExParcel::model()->findByPk($r);
						if(in_array($p->status, [12,15,18])){
							$p->status = 20;
							$p->consol_id = $id;
							$fa = $p->cnee->fullAddress();
							if(empty($dupq)){
								//check similar address
								foreach($model->shipments as $r){
									$pct = 0;
									similar_text($fa, $r->cnee->fullAddress(), $pct);
									if($pct >= 98){
										$p->bwf = $p->bwf | 2;
										$r->bwf = $r->bwf | 2;
										$r->save();
									}
								}
							}
							$p->save();
						}
						
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
			$this->ajaxResult($model);
		}
		$this->render('addparcel',array(
			'model'=>$model,
		));
	}

	public function actionBulkParcels($id){
		$model=$this->loadModel($id);
		//if($model->status >= 20) return;
		$exc = ExChannel::model()->find('code = :c', [':c' => $model->poc]);

		if(!empty($_POST['act'])){
			switch($_POST['act']){
				case 'add':
					if(!empty($_POST['mhbns'])){
						$ns = preg_split('/[\s,;]+/', trim($_POST['mhbns']));
						$dupq = !empty($exc->mdata['dupq']) && $exc->mdata['dupq'] > 1? $exc->mdata['dupq'] : 1;
						$dupool = ['id' => [], 'idno' => [], 'tel' => [], 'fa' => []];
						function isDup(&$dp, $p){
							$vks = ['id' => $p->cnee->cnid_id, 'idno' => $p->cnee->cnid_no, 'tel' => $p->cnee->tel, 'fa' => md5($p->cnee->fullAddress())];
							$vcs = [];
							foreach($dp as $k => $l){
								if(empty($vks[$k])) continue;

								$ac = array_count_values($l);
								$vcs[$k] = isset($ac[$vks[$k]])? $ac[$vks[$k]] : 0;

								$dp[$k][] = $vks[$k];
							}

							return max($vcs);
						}
						foreach($model->shipments as $p) isDup($dupool, $p);

						$c2 = [];
						foreach($ns as $hbn){
							$p = ExParcel::model()->find("hbn = :h", [':h' => $hbn]);
							if(in_array($p->agent_id, [672])) continue;
							if(empty($_POST['dup']) && !empty($p->mdata['nsn']) && $p->mdata['nsn'] > time()) continue;
							$warn = '';
							if(empty($p)){ //not found
								$c2[] = '<input type="checkbox" disabled /> '.$hbn.' not found';
								continue;
							}
							if($p->consol_id == $model->id){ //already in
								$c2[] = '<input type="checkbox" disabled /> '.$hbn.' already in this consol';
								continue;
							}
							if($p->status < 18){ //status
								if(in_array($p->status, [12,15]) && empty($p->cnee->cnid_id)){ //'CNKMG', 
									$warn = ' no ID';
								}elseif(in_array($p->status, [12,15])){//'CNKMG', 
									$warn .= ' not Info Ready '.$p->getWarnings();
								}
							}elseif($p->status >= 25){
								$c2[] = '<label class="chkbox_disabled"><input type="checkbox" value="'.$p->id.'" name="addids[]" disabled /> '.$hbn.' incorrect status - '.$p->getStatus().'</label>';
								continue;
							}
							
							if(floatval($p->weight) == 0){ //0 weight
								$warn .= ' 0 weight';
							}

							$_GET['dpt'] = ExcoConsol::pol2dpt($model->pol);
							$brs = $p->bestRates();
							if((empty($brs[$model->poc]) || $brs[$model->poc] == 999)){ //poc goods
								$c2[] = '<label class="chkbox_disabled"><input type="checkbox" value="'.$p->id.'" name="addids[]" disabled /> '.$hbn.' not allowed in this channel - '.$p->goodstype().'</label>';
								continue;
							}
							
							if($p->consol_id > 0){ //in other consol
								$warn .= ' already in Consol - '.$p->consol->no.' ('.$p->consol->getStatus().')';
							}

							if(empty($p->cnee->cnid_id)){
								if(empty($p->cnee->cnid_no) && empty($exc->mdata['altCnee'])){ //'CNKMG', 
									$warn .= ' no CnID';
								}elseif(!in_array($model->poc, ['CNTAO', 'CNXMN']) && !empty($p->cnee->cnid_no)){
									$warn .= ' no CnID Photo';
								}elseif(!in_array($model->poc, ['STO', 'HKHKG', 'A2U'])){
									$c2[] = '<label class="chkbox_disabled"><input type="checkbox" value="'.$p->id.'" name="addids[]" disabled /> '.$hbn.' no CnID</label>';
									continue;
								}
							}

							if(isDup($dupool, $p) >= $dupq){ //duplicate
								if(!in_array($model->poc, ['STO', 'HKHKG', 'A2U'])){
									$c2[] = '<label class="chkbox_disabled"><input type="checkbox" value="'.$p->id.'" name="addids[]" disabled /> '.$hbn.' duplicate</label>';
									continue;
								}
							}
							$bpoc = array_keys($brs);
							$cst = array_shift($brs);
							if(!in_array($model->poc, ['STO', 'HKHKG']) && $brs[$model->poc] > $cst){//cost alert
								if($p->weight == 0) $p->weight = 1;
								$warn .= ' Cost '.(round(($brs[$model->poc] - $cst) / $p->weight * 100) / 100).'/kg more than '.$bpoc[0];
							}
							$c2[] = '<label><input type="checkbox" value="'.$p->id.'" name="addids[]" class="chkbox" '.(empty($warn)? 'checked ':'').'/> '.$hbn.$warn.'</label>';
						}
						$o = new stdClass;
						$o->s2 = '<p>'.implode("</p>\n<p>", $c2).'</p>';
						$o->done = true;
						$o->msg = 'Please check parcels to add';
						echo json_encode($o);
						Yii::app()->end();
					}elseif(!empty($_POST['addids'])){
						$c = 0;
						$trans = Yii::app()->db->beginTransaction();
						try{
							foreach($_POST['addids'] as $id){
								$p = ExParcel::model()->findByPk($id);
								if(!empty($p->consol_id)) $p->custom_log_note = 'Moved from consol '.$p->consol->no;
								$p->consol_id = $model->id;
								if($p->status < 20 || $p->status == 101) $p->status = 20;
								$p->save();
								$c++;
							}
							$trans->commit();
						} catch (Exception $ex) {
							$trans->rollback();
							throw $ex;
						}
						$msg = $c.' shipments added';
						$this->ajaxResult($model, [], $msg);
					}
				break;
				case 'brm':
					if(!empty($_POST['mhbns'])){
						$ns = preg_split('/[\s,;]+/', trim($_POST['mhbns']));
						$rs = ExParcel::model()->findAll("consol_id = :cid AND hbn IN ('".implode("','", $ns)."')", [':cid' => $model->id]);
						if(empty($rs)){
							$msg = 'No shipment found!';
						}else{
							$trans = Yii::app()->db->beginTransaction();
							try{
								foreach($rs as $p){
									$p->consol_id = 0;
									if($p->status <100)	$p->status = empty($p->cnee->cnid_id)? 15 : 18;
									$p->custom_log_note = 'Removed from consol '.$model->no;
									if(!empty($_POST['rmref'])) $p->ref = '';
									unset($p->mdata['AltCnee']);
									$p->save();
								}
								$trans->commit();
							} catch (Exception $ex) {
								$trans->rollback();
								throw $ex;
							}
							$msg = sizeof($rs).' shipments removed';
						}
					}
					$this->ajaxResult($model, [], $msg);
				break;
				case 'rbw':
					$rs = ExParcel::model()->findAll([
						'condition' => 'consol_id = :cid',
						'params' => [':cid' => $model->id],
						'order' => 'weight '.($_POST['st']==1? 'DESC' : 'ASC').', created DESC',
						]);
					if(empty($rs)){
						$msg = 'No shipment found!';
					}else{
						$c = 0;
						$tw = 0;
						$trans = Yii::app()->db->beginTransaction();
						try{
							foreach($rs as $p){
								if($tw + $p->weight > $_POST['weight']) break;
								$p->consol_id = 0;
								if($p->status <100)	$p->status = empty($p->cnee->cnid_id)? 15 : 18;
								$p->custom_log_note = 'Removed from consol '.$model->no;
								$p->save();
								$c++;
								$tw += $p->weight;
							}
							$trans->commit();
						} catch (Exception $ex) {
							$trans->rollback();
							throw $ex;
						}
						$msg = $c.' shipments removed';
					}
					$this->ajaxResult($model, [], $msg);
				break;
			}
		}

		$this->render('bulkparcel',array(
			'model'=>$model,
		));
	}

	public function actionSwapParcel($id){
		$model=ExParcel::model()->findByPk($id);
		if(isset($_POST['pid'])){
			$m = ExParcel::model()->findByPk($_POST['pid']);
			$m->status = 20;
			$m->consol_id = $model->consol_id;
			$m->save();
			$model->consol_id = 0;
			$model->status = 18;
			$model->save();
			$this->ajaxResult($m);
		}
		$this->render('swapparcel',array(
			'model'=>$model,
		));
	}

	public function actionMvPallet($id){
		$model=ExcoConsol::model()->findByPk($id);
		if(!empty($_POST['pids']) && !empty($_POST['cid'])){
			$tn = false;
			if($_POST['cid'] == 'NEW'){
				$tc = new ExcoConsol;
				$tc->attributes = [
					'dpt_id' => $model->dpt_id,
					'pol' => $model->pol,
					'pod' => $model->pod,
					'poc' => $model->poc,
					'exrate' => $model->exrate,
				];
				$tc->save();
				$_POST['cid'] = $tc->id;
				$tn = true;
			}

			if(empty($_POST['cid'])) return;
			$rs = Manifest::model()->findAll('id IN ('.implode(',', $_POST['pids']).') AND consol_id = :c AND type = 60', [':c' => $id]);

			$tc = ExcoConsol::model()->findByPk($_POST['cid']);
			if(!$tn){ //check duplicate
				$ch = ExChannel::model()->findAll('code = :c', [':c' => $tc->poc]);
				$dup_allowed = !empty($ch->mdata['dupq']) && $ch->mdata['dupq'] > 1? $ch->mdata['dupq'] : 1;

				$dupool = ['id' => [], 'idno' => [], 'tel' => [], 'fa' => []];
				$isDup = function($p)use(&$dupool){
					$vks = ['id' => $p->cnee->cnid_id, 'idno' => empty($p->cnee->cnid_id)? $p->cnee->cnid_no : $p->cnee->cnid->no, 'tel' => $p->cnee->tel, 'fa' => md5($p->cnee->fullAddress())];
					$vcs = [];
					foreach($dupool as $k => $l){
						if(empty($vks[$k])) continue;

						$ac = array_count_values($l);
						$vcs[$k] = isset($ac[$vks[$k]])? $ac[$vks[$k]] : 0;

						$dupool[$k][] = $vks[$k];
					}

					return empty($vcs)? 0 : max($vcs);
				};

				foreach($tc->shipments as $p) $isDup($p);
				$dup = false;
				foreach($rs as $r){
					foreach($r->getFids() as $pid){
						$p = ExParcel::model()->findByPk($pid);
						if($isDup($p) >= $dup_allowed){
							$dup = true;
							break 2;
						}
					}
				}
				if($dup){
					$model->addError('id', 'This move will cause duplicate parcel');
					$this->ajaxResult($model);
				}
			}

			$mxm = Manifest::model()->find('consol_id = :c AND type = 60 ORDER BY ref DESC', [':c' => $_POST['cid']]);

			$mxid = empty($mxm)? 1 : ltrim($mxm->ref, '0') + 1;
			$db = Yii::app()->getDb();
			foreach($rs as $r){
				$r->consol_id = $_POST['cid'];
				if(empty($r->mdata['mvfrom'])){
					$r->mdata['mvfrom'] = $id;
					$r->mdata['opltno'] = $r->ref;
				}
				$r->ref = sprintf('%02d', $mxid++);
				$r->save();
				$sql = "UPDATE shipment SET consol_id = ".$_POST['cid']." WHERE id IN (SELECT fid FROM mani_map WHERE mani_id = ".$r->id.")";
				$db->createCommand($sql)->execute();
				//log to target consol
				$tc->custom_log_note = 'Pallet '.$r->ref.' from '.$model->no.' Plt#'.$r->mdata['opltno'];
				$tc->save();
			}
			$this->ajaxResult($model);
		}
		$this->render('mvpallet',array(
			'model'=>$model,
		));
	}

	public function actionPalletRpt($id){
		$model=$this->loadModel($id);
		$xls = new oExcel;
		$i = 1;
		$of = $model->no.'_Pallets.xlsx';
		$rs = Manifest::model()->findAll('type = 60 AND consol_id = :cid', [':cid' => $model->id]);
		$xls->addRow($i++, array('序号','板号','单号','转单号','重量'));
		$sn = 1;
		if(!empty($rs)){
			foreach($rs as $m){
				$tw = 0;
				foreach($m->lines as $l){
					$r = $l->mm();
					$wt = $r->shipWeight();
					$xls->addRow($i++, array($sn++,$m->ref,$r->hbn,$r->ref,$wt));
					$tw += $wt;
				}
				$xls->addRow($i++, array('', $m->ref, 'Total', '', round($tw*100)/100));
				$xls->addRow($i++, array(''));
			}
		}
		$xls->output($of);
	}

	public function actionPltMark($id){
		$model=$this->loadModel($id);
		$rs = Manifest::model()->findAll('type = 60 AND consol_id = :cid', [':cid' => $model->id]);
		oPDF::renderPDF('pallet_mark', array('model' => $model, 'plts' => $rs));
	}

	public function actionPalletClearParcels($id){
		$sql = 'SELECT id FROM shipment WHERE consol_id = :id AND id NOT IN (SELECT fid FROM mani_map WHERE mani_id IN (SELECT id FROM manifest WHERE consol_id = :id AND type = 60))';
		$shipments = Yii::app()->db->createCommand($sql)->bindvalues([':id' => $id])->queryAll();
		$model=$this->loadModel($id);
		foreach($shipments as $s){
			$p = ExParcel::model()->findByPk($s['id']);
			$p->bwf = $p->bwf & (~2);
			$p->consol_id = 0;
			if($p->status <100)	$p->status = 15;
			$p->custom_log_note = 'Removed from consol '.$model->no;
			$p->rfcProblem();
			$p->save();
		}
		$this->ajaxResult($model);
	}

	

	public function actionUpdateByNo($no){
		$consol = ExcoConsol::model()->find('no = :no',[':no' => $no]);
		if ( !empty($consol) ) {
			$this->actionUpdate($consol->id);
		} else {
			throw new CHttpException(404,'The requested page does not exist.');
		}
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id){
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ImcoConsol'])){
			$model->attributes=$_POST['ImcoConsol'];
			$err = false;
			$msg = false;

			if(!empty($_POST['confirm'])){

				$cs = SzpChannel::model()->find('code = :poc', [':poc' => $model->poc]);

				if(!in_array($model->poc, ['HKHKG', 'STO', 'A2U'])){
					$tnns = 0;
					foreach($model->shipments as $p){
						if(!empty($p->ref)) continue;
						$tnns++;
						$bwf = $p->bwf & (~ 1);
						$bwf = $bwf & (~ 2);
						$bwf = $bwf & (~ 128);
						//if($model->pod == 'CNTAO') $bwf = $bwf & (~ 8);

						if($bwf > 0){
							$model->addError('no', 'Please make sure all shipments are verified.');
							$err = true;
							break;
						}
					}
				}

				if(!$err && !in_array($model->poc, ['CNHAK', 'CNHA2'])){
					$avl = ConnoteRange::totalAvailable('ExChannel', $cs->id);
					if($avl < $tnns){
						$model->addError('no', 'Not enough connotes available, only '.$avl);
						$err = true;
					}else{
						$msg = ($avl - $tnns).' Connote numbers available';
					}
				}

				if(!$err){
				
					$trans = Yii::app()->db->beginTransaction();
					try{
						$ids = [0];
						foreach($model->shipments as $i => $p){
							$up = false;
							if($p->status < 25){
								$p->status = 25;
								$up = true;
							}
							/*if(in_array($model->poc, ['CNXMN', 'CNXM2', 'CNCSX', 'CNCS2']) && !$p->hasOriginTrace()){
								Yii::app()->getDb()->createCommand('UPDATE origin_trace SET pid = '.$p->id.' WHERE pid = 0 LIMIT 1')->execute();
							}*/

							if(empty($p->ref)){
								if(in_array($model->poc, ['CNHAK', 'CNHA2'])){
									if(!empty($temp)){
										$p->ref = $temp[$p->hbn]['mailNo'];
										$p->mdata['dtb'] = $temp[$p->hbn]['shortAddress'];
										$up = true;
									}
								}else{
									$p->ref = ConnoteRange::newNumber('ExChannel', $cs->id);
									$up = true;
								}
							}
							//alt cnee
							if(!empty($cs->mdata['altCnee']) && empty($p->mdata['AltCnee'])){

								if(empty($p->cnee->cnid_id) || in_array($p->cnee->cnid_id, $ids)){ //dup
									$ss = [];
									if(strlen($p->cnee->name) > 9){
										$ss[] = substr($p->cnee->name, 0, 9).'%';
										$ss[] = substr($p->cnee->name, 0, 6).'%'.substr($p->cnee->name, 9);
									}
									if(strlen($p->cnee->name) > 6){
										$ss[] = substr($p->cnee->name, 0, 6).'%';
										$ss[] = substr($p->cnee->name, 0, 3).'%'.substr($p->cnee->name, 6);
									}
									$ss[] = substr($p->cnee->name, 0, 3).'%';

									if(strlen($p->cnee->name) > 6)	$ss[] = '%'.substr($p->cnee->name, 6);
									$ss[] = '%'.substr($p->cnee->name, 3);

									foreach($ss as $s){
										$nc = Addr::model()->with('cnid')->find(['condition' => 'cnid.status IN (15, 18, 20) AND t.cnid_id > 0 AND t.name LIKE :n AND t.cnid_id NOT IN (SELECT cnid_id FROM addr a INNER JOIN shipment s ON s.cnee_id = a.id WHERE consol_id = :cid) AND t.cnid_id NOT IN ('.implode(',', $ids).')', 'params' => [':n' => $s, ':cid' => $model->id], 'order' => 'RAND()']);
										if(!empty($nc)) break;
									}

									if(!empty($nc)){
										$p->mdata['AltCnee'] = $nc->id;
										$up = true;
										//echo $p->cnee->name.' => '.$nc->name."\n";
										$ids[] = $nc->cnid_id;
									}
							}else{
								$ids[] = $p->cnee->cnid_id;
							}
						}
							if($up) $p->save();
						}
						$trans->commit();
					} catch (Exception $ex) {
						$trans->rollback();
						throw $ex;
					}

					$model->status = 20;
					//if(!empty($sinc) && !empty($fp)) writeUnlock($fp, "<?php\nreturn ".var_export($sinc, true).';');
				}else{
					$this->ajaxResult($model);
				}
			}

			if(isset($_POST['mdata']) && isset($_POST['mdata']['awb_check_wt'])){
				$model->mdata['awb_check_wt'] = $_POST['mdata']['awb_check_wt'];
			}
			$model->save();
			$this->ajaxResult($model, [], $msg);
		}

		if(isset($_POST['subc'])){
			$model->mdata['subc'] = $_POST['subc'];
			$model->save();
			$this->ajaxResult($model);
		}

		// in case from fix cost lines data
		// navigate to billing tab directly
		if ( isset($_GET['afid']) ) {
			$_GET['actab'] = 4;
			Yii::app()->session['ex_afid'] = $_GET['afid'];
		}

		if(isset($_GET['tab'])){
			Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);
			$aflines = null;
			if ($model->status > 40 && $_GET['tab'] == 'billing' ) {
				$model->addBillingAccrual();
				if ( isset(Yii::app()->session['ex_afid']) ) {
					$aflines = new AFInvoiceReconciliationLine();
					$aflines->unsetAttributes();
					$aflines->invoice_id = Yii::app()->session['ex_afid'];
					unset(Yii::app()->session['ex_afid']);
				}
			}
			$this->render('tab_'.$_GET['tab'], array('model'=>$model,'aflines' => $aflines));
		}else{
			$this->render('update',array('model'=>$model));
		}
	}

	public function actionShowThread($id){
		$thread = Thread::model()->findByPk($id);
		$j = ['comp' => false, 'sub' => 0, 'html' => '', 'msg' => ''];
		if($thread->status >= 99){
			$j['comp'] = empty($thread->mdata['sub']);
			$err = [];
			foreach($thread->workers as $w){
				if(!empty($w->logs['err'])) $err = array_merge($err, $w->logs['err']);
			}
			if(empty($err)){
				if(empty($thread->mdata['sub'])){
					$j['msg'] = 'All workers completed successfully!';
				}else{
					$j['sub'] = $thread->mdata['sub'];
					$j['msg'] = 'Current workers done, another task is running, pelase wait for the next message.';
				}
			}else{
				$j['msg'] = 'Please check following error(s) and try again: <br />'.implode('<br />', $err);
			}
		}else{
			$ws = [10 => 0, 20 => 0, 99 => 0, 101 => 0, 102 => 0];
			foreach($thread->workers as $w){
				$ws[$w->status]++;
			}
			$j['html'] = '<b>Comfirming in background</b></br>Workers: '.$thread->totalWorkers().' &nbsp; Running: '.$ws[20]. ' &nbsp; Error: '.$ws[101].' &nbsp Completed: '.$ws[99];
		}
		echo json_encode($j);
	}


	public function actionGoodsRatio($id){
		$model=$this->loadModel($id);

		//check dups
		$cs = ExChannel::model()->find('status = 50 AND code = :poc', [':poc' => $model->poc]);
		$dupq = empty($c->mdata['dupq'])? 1 : $c->mdata['dupq'];
		$dp = ['id' => [], 'idno' => [], 'tel' => [], 'fa' => []];
		$isDup = function($p)use(&$dp){
			$vks = ['id' => $p->cnee->cnid_id, 'idno' => empty($p->cnee->cnid_id)? $p->cnee->cnid_no : $p->cnee->cnid->no, 'tel' => $p->cnee->tel, 'fa' => md5($p->cnee->fullAddress())];
			$vcs = [];
			foreach($dp as $k => $l){
				if(empty($vks[$k])) continue;

				$ac = array_count_values($l);
				$vcs[$k] = isset($ac[$vks[$k]])? $ac[$vks[$k]] : 0;

				$dp[$k][] = $vks[$k];
			}

			return empty($vcs)? 0 : max($vcs);
		};

		$rs = ['B1' => [0,0,0,0], 'B2' => [0,0,0,0], 'M' => [0,0,0,0], 'O' => [0,0,0,0], 'U' => [0,0,0,0], 'P' => [0,0,0,0]];
		$dups = [];
		$svs = [];
		foreach($model->shipments as $p){
			$p->altGoods();
			$typ = $p->goodsType();
			if(!isset($svs[$p->styp])) $svs[$p->styp] = 0;
			$svs[$p->styp]++;
			$gm = implode('', $p->eitems['g']);
			$d = 'O';

			if($typ == 'M'){
				if(preg_match('/小安素/', $gm)) $d = 'P';
				else $d = 'M';
			}elseif($typ == 'B'){
				if(preg_match('/一段|二段|1段|2段/', $gm)) $d = 'B1';
				else $d = 'B2';
			}else{
				if(preg_match('/UGG|鞋|靴|围巾|Scarf/i', $gm)) $d = 'U';
			}

			$rs[$d][0]++;
			$rs[$d][1] += $p->weight;

			$t = $p->calTariff($model->poc);
			if($t > 0){
				$rs[$d][2]++;
				$rs[$d][3]+=$t;
			}

			//check dup
			if($isDup($p) >= $dupq) $dups[] = $p->hbn;
		}

		$r = [
			'dup' => $dups,
			'svs' => $svs,
			'tt' => count($model->shipments),
			'tw' => $model->totWeight(),
			'rs' => $rs,
		];
		echo json_encode($r);
	}

	public function actionDownload($id){
		$pid = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'excodl_'.md5(Yii::app()->getSession()->getSessionId().$_GET['type']).'.pid';
		if(is_file($pid) && filectime($pid) > time() - 300) return false;
		file_put_contents($pid, '1');

		$model=$this->loadModel($id);

		$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.'-'.$model->awb.'-'.strtoupper($_GET['type']).(empty($_GET['joint'])? '' : '-joint').date('YmdHi').(empty($_GET['alt'])? '' : '_alt').'.zip';
		$zipName = '';
		$zip = new ZipArchive;
		$zip->open($zf, ZipArchive::CREATE);
		$copied = [];
		$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		if(!file_exists($td)) mkdir($td);

		switch($_GET['type']){
			case 'id':
				if(!isset($_GET['joint'])) $_GET['joint'] = 0;
				if(!isset($_GET['word'])) $_GET['word'] = 0;
				$sum = $_GET['joint'] == 1? ['HBN,Ref,ID #,Joint file']: ['HBN,Ref,ID #,Front file,Back file'];

				if($model->poc == 'CNCA3' && empty($_GET['joint'])){
					$zip->addEmptyDir('id_front');
					$zip->addEmptyDir('id_back');
				}elseif($model->poc == 'CNXI2'){
					$zipName = 'TL50-'.$model->getPno().'-'.$model->awb.'-'.$model->totShipments().'-身份证.zip';
				}

				if($_GET['word'] == 1){
					$wrd = new oWord;
					$wsec = $wrd->createSection();
					$wtable = $wsec->addTable();
				}

				foreach($model->shipments as $si => $p){
					if(empty($_GET['alt']) && !empty($p->mdata['AltCnee'])){
						$p->altCnee();
						//$p->cnee = Addr::model()->findByPk($p->mdata['AltCnee']);
					}

					$cnid = $p->cnee->cnid;
					if(!empty($cnid)){
						$cnid_new = !in_array($cnid->id, $copied);
						$_sum = [$p->hbn, $p->ref, '="'.$cnid->no.'"'];
						if($_GET['joint'] == 1){
							if(empty($cnid->joint)){
								$cnid->joinPhoto();
								$cnid->refresh();
							}
							if($cnid->photo_joint){
								switch($model->poc){
									case 'CNCA2':
									case 'CNCA3':
									case 'CNXI2':
									case 'CNHAK':
										$jn = $p->ref.'.jpg';
										$cnid_new = true;
									break;
									case 'CNJJI':
									case 'CNFZH':
										$jn = $p->ref.'.s.jpg';
										$cnid_new = true;
									break;
									case 'CNCHQ':
										$jn = ($si+1).'.jpg';
										$cnid_new = true;
									break;
									case 'CNKMG':
										$jn = iconv('UTF-8', 'GB18030', $p->cnee->name).$cnid->no.'.jpg';
									break;
									default:
										$jn = $cnid->no.'.jpg';
									break;
								}
								if($cnid_new) $zip->addFile($cnid->photo_joint->getFile(), $jn);
								$_sum[] = $jn;
							}
						}elseif($_GET['word'] == 1){
							if($si%2 == 0) $wtable->addRow(7000);
							$c = $wtable->addCell(4500);
							$c->addText('序号: '.($si+1));
							$c->addImage($cnid->photo_front->getFile(), array('width'=>300, 'height'=>200));
							$c->addImage($cnid->photo_back->getFile(), array('width'=>300, 'height'=>200));
						}else{
							if($cnid->photo_front){
								$frtname = $model->poc == 'CNCA3'? 'id_front/'.$p->ref.'.jpg' : $cnid->no.'_1.jpg';
								if($cnid_new) $zip->addFile($cnid->photo_front->getFile(), $frtname);
								$_sum[] = $frtname;
							}
							if($cnid->photo_back){
								$backname = $model->poc == 'CNCA3'? 'id_back/'.$p->ref.'.jpg' : $cnid->no.'_2.jpg';
								if($cnid->photo_front->hash == $cnid->photo_back->hash){//add same file by string
									if($cnid_new) $zip->addFromString($backname, file_get_contents($cnid->photo_back->getFile()));
								}else{
									if($cnid_new) $zip->addFile($cnid->photo_back->getFile(), $backname);
								}
								$_sum[] = $backname;
							}
						}
						$sum[] = implode(',', $_sum);
						$copied[] = $p->cnee->cnid_id;
					}else{
						$sum[] = implode(',', [$p->hbn, $p->ref, empty($p->cnee->cnid_no)? '' : '="'.$p->cnee->cnid_no.'"']);
						if($_GET['word'] == 1){
										  if($si%2 == 0) $wtable->addRow(7000);
										  $c = $wtable->addCell(4500);
										  $c->addText($si+1);
								}
					}
				}
				if($_GET['word'] == 1){
					$zip->addFromString('Chinese_ID.docx', $wrd->output(null,false));
				}
				$zip->addFromString('_Sum.csv', implode("\n", $sum));
			break;
			case 'id_valid':
				foreach($model->shipments as $p){
					if(empty($p->cnee->cnid_id)) continue;
					$tf = tempnam($td, "idv");
					oPDF::renderImage('cnid_validation', ['name' => $p->cnee->name, 'idno' => $p->cnee->cnid->no], 2, $tf);
					$zip->addFile($tf, $p->cnee->cnid->no.'.jpg');
				}
			break;
			case 'auth':
				foreach($model->shipments as $p){
					$zip->addFromString($p->ref.'_sqx.pdf', oPDF::renderPDF('auth', ['p' => $p], 0));
				}
			break;
			case 'rcpt':
				
				foreach($model->shipments as $p){//gen receipt
					$d = $p->receiptData();
					$p->altGoods();
					
									  
					$tf = tempnam($td, "rcpt");
										
//					$html = $this->renderPartial('../_pdf/receipt_'.$d['tpl'], array('r' => $p, 'd' => $d), true);
					if(isset(Yii::app()->controller)){
					   $html = $this->renderPartial('../_pdf/receipt_'.$d['tpl'], array('r' => $p, 'd' => $d), true);
					}else{
						$path = Yii::getPathOfAlias('application.views._pdf').'/receipt_'.$d['tpl'].'.php';  
						$html= CConsoleCommand::renderFile($path, array('r' => $p, 'd' => $d), true);
					}
										
					if(!empty($_GET['ft']) && $_GET['ft'] == 'pdf'){
						oPDF::html2pdf($html, 2, $tf);
						$zip->addFile($tf, (empty($p->ref)? $p->hbn : $p->ref).'.pdf');
					}elseif(!empty($_GET['ft']) && $_GET['ft'] == 'doc'){
						$zip->addFile($tf, (empty($p->ref)? $p->hbn : $p->ref).'.doc');
					}else{
						oPDF::html2image($html, 2, $tf);
						if(!empty($_GET['photo'])) $real = RcptMaker::makeReal($tf, $tf);
						//iconv('utf-8', 'gb2312', '小票');
						$fn = ($model->poc == 'CNKMG'? $p->goodsType().'/' : '').(empty($p->ref)? $p->hbn : $p->ref).($real? '' : '_NR');
						if(in_array($model->poc, ['CNXM2', 'CNJJI', 'CNFZH'])) $fn .= '.x';
						if(is_file($tf)) $zip->addFile($tf, $fn.'.jpg');
						else Yii::log('Problem creating receipt for '.$fn, 'error');
					}
				}
			break;
			case 'label':
				$tpl = $model->getLabelTpl();
				foreach($model->shipments as $p){//gen labels
					$p->altGoods();
					$p->altCnee();
					$p->labelGoods();
					
					//if(!empty($_GET['alt'])){
						//if(!empty($p->mdata['AltCnee'])){
							//continue;
							//$alcn = Addr::model()->findByPk($p->mdata['AltCnee']);
							//$p->cnee->name = $alcn->name;
						//}
					//}
					if(isset(Yii::app()->controller)){
						$html = $this->renderPartial('../expLabel/'.$tpl, array('p'  => $p), true); 
					}else{
						$path = Yii::getPathOfAlias('application.views.expLabel').'/'.$tpl.'.php';
					   
						$html= CConsoleCommand::renderFile($path, array('p'  => $p), true);
					}
					$tf = tempnam($td, "clabel");
					if(!empty($_GET['ft']) && $_GET['ft'] == 'pdf'){
						oPDF::html2pdf($html, 2, $tf);
						$zip->addFile($tf, (empty($p->ref)? $p->hbn : $p->ref).'.pdf');
					}else{
						oPDF::html2image($html, 2, $tf);
						$fn = empty($p->ref)? $p->hbn : $p->ref;
						switch($model->poc){
							case 'CNXMN':
							case 'CNXM2':
							case 'CNJJI':
							case 'CNFZH':
								$fn = $fn.'.y';
							break;
						}
						$zip->addFile($tf, $fn.'.jpg');
					}
				}
			break;
			case 'pdf':
				$rs = $model->shipments;
				if($model->poc == 'HKHKG'){
					foreach($rs as $i=>$r){
						if(!preg_match('/^PBS\d+/', $r->hbn)) unset($rs[$i]);
					}
				}
				oPDF::renderPDF('label_A6', array('tpl' => '_label-ex', 'empty' => false, 'rs' => $rs));
				unlink($pid);
				Yii::app()->end();
			break;
			case '3in1':
				$tpl = $model->getLabelTpl();

				foreach($model->shipments as $p){//gen labels
					$p->altGoods();
					if(!empty($_GET['alt'])){
						if(empty($p->mdata['AltCnee'])) continue;
						$alcn = Addr::model()->findByPk($p->mdata['AltCnee']);
						$p->cnee->name = $alcn->name;
					}

					//label
//					$html = $this->renderPartial('../expLabel/'.$tpl, array('p'  => $p), true);
					if(isset(Yii::app()->controller)){
						$html = $this->renderPartial('../expLabel/'.$tpl, array('p'  => $p), true); 
					}else{
						$path = Yii::getPathOfAlias('application.views.expLabel').'/'.$tpl.'.php';
						$html= CConsoleCommand::renderFile($path, array('p'  => $p), true);
					}
					$tf = tempnam($td, "clabel");
					oPDF::html2image($html, 2, $tf);
					$fn = empty($p->ref)? $p->hbn : $p->ref;

					//id
					if(!empty($p->mdata['AltCnee'])){
						$p->cnee = Addr::model()->findByPk($p->mdata['AltCnee']);
					}
					$cnid = $p->cnee->cnid;
					$idp = '';
					if(!empty($cnid)){
						if(empty($cnid->joint)){
							$cnid->joinPhoto();
							$cnid->refresh();
						}
						$idp = empty($cnid->joint)? '' : $cnid->photo_joint->getFile();
					}
					
					//rcpt
					$d = $p->receiptData();
					$tf2 = tempnam($td, "rcpt");
//					$html = $this->renderPartial('../_pdf/receipt_'.$d['tpl'], array('r' => $p, 'd' => $d), true);
										
					if(isset(Yii::app()->controller)){
						$html = $this->renderPartial('../_pdf/receipt_'.$d['tpl'], array('r' => $p, 'd' => $d), true);
					}else{
						 $path = Yii::getPathOfAlias('application.views._pdf').'/receipt_'.$d['tpl'].'.php';  
						 $html= CConsoleCommand::renderFile($path, array('r' => $p, 'd' => $d), true);
					}
					oPDF::html2image($html, 2, $tf2);
					$magick = '/usr/bin/convert';
					AppHelper::exec($magick.' '.$tf.' '.$idp.' '.$tf2.' +append -quality 75 '.$tf);
					if(is_file($tf)) $zip->addFile($tf, $fn.'.jpg');
					else Yii::log('Problem creating receipt for '.$fn, 'error');
				}
			break;
		}

		$zip->close();
		if(!is_file($zf)) throw new CHttpException(400, 'File is empty');

		if(empty($_GET['consol_copy'])){
			header("Cache-Control: maxage=1");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header('Content-Disposition: attachment; filename="'.(empty($zipName)? basename($zf) : $zipName).'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($zf));
			readfile($zf);
			unlink($zf);
			AppHelper::unlinkRecursive($td);
			unlink($pid);
			Yii::app()->end();
		}else{
			$name_to=$model->no.'-'.$model->awb.'-'.strtoupper($_GET['type']).(empty($_GET['joint'])? '' : '-joint').'.zip';
			$r= FileRepo::model()->find('name=:name and fid=:fid',array(':name'=>$name_to,':fid'=>$id));
			if(empty($r))  FileRepo::storeFile($zf,$name_to, 80, $id);
			unlink($zf);
			unlink($pid);
		}
	}

	public function actionRemoveParcel($id){
		$p=ExParcel::model()->findByPk($id);
		if($p){
			$con = $p->consol;
			if((int) $p->bwf & 2){
				$p->bwf = $p->bwf & (~2);
				$rs = ExParcel::model()->findAll('consol_id = :cid AND bwf & 2 AND id != :id', array(':cid' => $con->id, ':id' => $id));
				$fa = $p->cnee->fullAddress();
				$siars = array();
				foreach($rs as $r){
					$pct = 0;
					similar_text($fa, $r->cnee->fullAddress(), $pct);
					if($pct >= 98){
						$siars[] = $r;
					}
				}
				if(sizeof($siars) == 1){
					$r = $siars[0];
					$r->bwf = $r->bwf & (~2);
					$r->custom_log_note = 'Warning [Similar Address] removed since '.$p->hbn.' is removed from consol '.$con->no;
					$r->save();
				}
			}
			$p->consol_id = 0;
			if($p->status <100)	$p->status = 15;
			$p->rfcProblem();
			$p->custom_log_note = 'Removed from consol '.$con->no;
			$p->save();
		}
		echo 'done';
	}

	public function actionRemoveGdcw($id){
		$model=$this->loadModel($id);
		foreach($model->shipments as $p){
			$bwf = (int) $p->bwf;
			if($bwf & 8 || $bwf & 16){
				$p->consol_id = 0;
				if($p->status <100)	$p->status = 15;
				$p->custom_log_note = 'Removed from consol '.$model->no;
				$p->save();
			}
		}
		$this->ajaxResult($model);
	}

	public function actionExloclist($id){
		$sql = "SELECT s.name AS store, t.hbn, 1 AS sn, c.name, c.address, c.state, t.ref, t.items FROM `shipment` t 
				LEFT JOIN storage_log sl ON sl.model = 'ExParcel' AND out_dt IS NULL AND sl.fid = t.id
				LEFT JOIN storage s ON sl.sid = s.id
				INNER JOIN addr c ON c.id = t.cnee_id
				WHERE t.consol_id = ".$id." ORDER BY s.name, t.hbn";
		$c = Yii::app()->db->createCommand($sql);

		function goodsType($itms){
			if(empty($itms['type'])) return 'X';
			$typs = array_unique($itms['type']);
			$typ = sizeof($typs) == 1? array_pop($typs) : 'X';
			if(empty($typ)) $typ = 'X';
			return $typ;
		}

		function goods($itms){
			if(empty($itms['g'])) return '';
			$gs = [];
			foreach($itms['g'] as $i => $g){
				$gs[] = $itms['q'][$i].' x '.$g;
			}
			return implode(', ', $gs);
		}

		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Location','WBN','Sn#','Name','Address','State', 'Type', 'Goods', 'Check Content'));
		foreach($c->queryAll() as $r){
			$r['sn'] = $i - 1;
			$items = unserialize($r['items']);
			$ck = false;
			if(empty($items['type'])){
				echo $r['hbn'].' goods details';
				exit(0);
			}
			foreach($items['type'] as $j => $t){
				if(!in_array($t, ['B', 'M'])) continue;
				$q = floatval($items['q'][$j]);
				if(empty($q) || empty($items['pid'][$j])){
					$ck = true;
					break;
				}
			}
			$r['ref'] = goodsType($items);
			$r['items'] = goods($items);
			$r['check'] = $ck? 'Yes' : '';
			$xls->addRow($i++, $r);
		}
		$model=$this->loadModel($id);
		$xls->output($model->no.'_location_list.xlsx');
	}

	public function getConsolSeq(){
		$c = Yii::app()->db->createCommand('SELECT sn FROM  t WHERE id = '.$this->id);

		return $c->queryScalar();
	}

	public function actionConfirm($id){
		$model=$this->loadModel($id);
		$o = new StdClass;
		$o->comp = false;
		if(!empty($_POST['pm'])){
			$cfm = true;
			foreach($_POST['pm'] as $pid=>$s){
				if($s == 20){
					$cfm = false;
					continue;
				}
				$p = ExParcel::model()->findByPk($pid);
				if($p && $p->consol_id == $id){
					$p->status = $s;
					$p->consol_id = 0;
					$p->save();
				}
			}
			if($cfm){
				$model->status = 20;
				$model->save();
				$o->html = 'All shipments confirmed';
				$o->comp = true;
			}else{
				$o->html = 'NOT all shipments confirmed';
				$o->comp = true;
			}
		}elseif(!empty($_FILES['manifest']) && is_uploaded_file($_FILES['manifest']['tmp_name'])){
			$err = [];
			$war = [];
			$xls = new oExcel;
			if(!$xls->supported($_FILES['manifest']['name'])){
				foreach($xls->getError() as $e){
					$err[] = $e;
				}
			}else{
				$xls->load($_FILES['manifest']['tmp_name']);
				$data = $xls->getAll();
				ini_set('precision', 12);
				$hla = implode(',', $data[1]);
				$hl2 = implode(',', array_slice($data[1],0,2));
				unset($data[1]);
				$ps = [];
				if($hl2 == "运单号,转运单号"){
					foreach($data as $r){
						$r[1] = trim($r[1]);
						$r[2] = trim($r[2]);
						if(empty($r[1])) continue;
						$p = ExParcel::model()->find('consol_id = :id AND hbn = :h', array(':id' => $id, ':h' => $r[1]));
						//if(empty($r[2])) $err[] = $r[2].' has no HTNO';
						if(empty($p)){
							$war[] = $r[1].' not found in this consol';
						}elseif($p->status < 90){
							$p->ref = $r[2];
							if($p->status < 25) $p->status = 25;
							$ps[] = $p;
						}
					}
				}elseif($hl2 == "转运单号,次转运单号"){ //次转运
					foreach($data as $r){
						$r[1] = trim($r[1]);
						$r[2] = trim($r[2]);
						if(empty($r[1])) continue;
						$p = ExParcel::model()->find('consol_id = :id AND ref = :h', array(':id' => $id, ':h' => $r[1]));
						if(empty($p)){
							$war[] = $r[1].' not found in this consol';
						}elseif($p->status < 90 && !empty($r[2])){
							$p->ref = $r[2];
							$p->mdata['ref1'] = $r[1];
							$ps[] = $p;
						}
					}
				}elseif($hla == "关联单号,转单号,重量,收件人,身份证件号码,收件人电话号码,收件人地址,省份,城市,邮编,物品序号,物品名称（包裹内的单种物品）,品牌,规格,税号,单种物品单价,单种物品数量,单种物品净重,单种物品单位,单种物品总价,单种物品税金,包裹总税金,币别"){ //申报单
					$ds = [];
					foreach($data as $r){
						$r[1] = trim($r[1]);
						$r[2] = trim($r[2]);
						if(empty($r[1])) continue;
						$ds[$r[1]][] = $r;
					}
					
					foreach($ds as $hbn => $rs){
						$p = ExParcel::model()->find('consol_id = :id AND hbn = :h', array(':id' => $id, ':h' => $hbn));
						if(empty($p)){
							$err[] = $hbn.' not found in this consol';
							continue;
						}
						$p->mdata['altItems'] = [];
						unset($p->mdata['altAddr']);
						unset($p->mdata['altTel']);
						foreach($rs as $gi => $r){
							$pid = ExProdb::getIDByName($r[12]);
							if(empty($pid)){
								$err[] = $r[1].' '.$r[12].' 产品库无此品名';
								continue;
							}
							$qty = intval($r[17]);
							$uv = floatval($r[16]);
							if($p->eitems['pid'][$gi] != $pid) $p->mdata['altItems']['pid'][$gi] = $pid;
							if($p->eitems['q'][$gi] != $qty) $p->mdata['altItems']['q'][$gi] = $qty;
							if($p->eitems['v'][$gi] != $uv) $p->mdata['altItems']['v'][$gi] = $uv;
							if($gi == 0){
								if($p->cnee->tel != $r[6]) $p->mdata['altTel'] = $r[6];
								if($p->cnee->getCnFullAddress() != $r[7]) $p->mdata['altAddr'] = $r[7];
							}
						}
						if(empty($p->mdata['altItems'])) unset($p->mdata['altItems']);
						if(!empty($p->mdata['altItems']) || !empty($p->mdata['altAddr']) || !empty($p->mdata['altTel'])){
							$p->custom_log_note = 'Set Alt Info';
							$ps[] = $p;
						}
					}
				}else{
					$err[] = 'Manifest Column Mismatch!';
				}
			}

			$o->err = empty($err)? false : implode('<br />', $err);
			$o->war = empty($war)? false : implode('<br />', $war);

			if(empty($err)){
				$trans = Yii::app()->db->beginTransaction();
				try{
					foreach($ps as $p){
						$p->save();
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
				$rs = ExParcel::model()->findAll('consol_id = :id AND status = 20', array(':id' => $id));
				if(empty($rs)){
					if($model->status < 20) $model->status = 20;
					$model->save();
					$o->html = 'All shipments confirmed';
					$o->comp = true;
				}else{
					$o->html = $this->renderPartial('confirm_result', array('rs'=>$rs), true);
				}
			}
			unset($xls);
		}
		echo json_encode($o);
	}

	public function actionOutscan($id){
		if(isset($_POST['barcode'])){
			$hbn = $_POST['barcode'];
			$r = new StdClass;
			$r->msg = 'Not Found';
			$r->color = '#c00';
			$r->sound = 'not_found.mp3';

			$p = ExParcel::model()->find('hbn = :n AND consol_id = :cid', array(':n' => $hbn, ':cid' => $id));
			if($p){
				$sn = $p->getConsolSeq();
				$r->msg = "Found Shipment: ".$hbn.' - '.$sn;
				$r->color = '#0c0';
				$r->sound = $sn.'.mp3';
				$sl = StorageLog::findItem($p);
				if($sl) $sl->out();
			}

			echo json_encode($r);
			Yii::app()->end();
		}
	}

	public function actionOutsa($id){
		$model=$this->loadModel($id);

		$trans = Yii::app()->db->beginTransaction();
		try{
			$ss = [];
			foreach($model->shipments as $p){
				$sl = StorageLog::findItem($p);
				if($sl){
					$sl->out(false);
					$ss[] = $sl->sid;
					// $p->custom_log_note = 'moved out '.$sl->storage->name;
					// $p->save();
				}
			}
			foreach($ss as $sid){
				$s = Storage::model()->findByPk($sid);
				$s->updateCap();
			}
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}
		echo 'Done';
	}
		
	public function actionTrans(){
		if(!empty($_GET['w'])){
			echo $this->Translate($_GET['w']);
		}elseif(!empty($_POST['t'])){
			foreach($_POST['t'] as $o => $t){
				if(empty($t)) continue;
				$trans = new Translation;
				$trans->type = 10;
				$trans->o = $o;
				$trans->t = $t;
				$trans->save();
			}
			$this->ajaxResult($trans);
		}
	}
	
	function Translate($word,$pair = 'zh-CN|en'){
		$word = urlencode($word);
		list($sl, $tl) = explode('|', $pair);
		$url = 'http://translate.google.com/translate_a/t?client=t&text='.$word.'&hl='.$sl.'&sl='.$sl.'&tl='.$tl.'&multires=1&otf=2&pc=1&ssel=0&tsel=0&sc=1';
		$ch = curl_init ($url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'Content-Type: application/x-www-form-urlencoded; charset=utf-8'));
		$r = curl_exec ($ch);
		$name_en = explode('"',$r);
		return $name_en[1];
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new ImcoConsol('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ImcoConsol']))
			$model->attributes=$_GET['ImcoConsol'];
			$model->szportalCreate = true;
		$this->render('list',array(
			'model'=>$model,
		));
	}
		
		/**
		 * save to-do task for download files for port. (copy link)
		 * 
		 */
		public  function actionTodoTask(){
			if(!empty($_GET['id'])){
			   $r= TaskToRun::model()->find('pid=:pid',array(':pid'=>$_GET['id']));
			   if(empty($r)){
				   $model=new TaskToRun;
				   $model->pid=$_GET['id'];
				   $model->date=date('Y-m-d H:i:s');
				  
					$model->save(); 
				  $this->ajaxResult ($model);
			   }else{
				   if(!empty($_GET['create'])&&$_GET['create']==1){
						 $r->finish=0;
						 $r->date=date('Y-m-d H:i:s');
						 $r->save();
						 $this->ajaxResult($r);
				   }
				   echo '0';  //means the record already exist
			   }
				
			}
			
		}
		
		   public function actionUpdo() {
		 $rs = TaskToRun::model()->findAll('finish=0');
		 $portRequirement = ['CNXIA' => [["type" => "id", "joint" => 1], ["type" => "id_valid"], ["type" => "3in1"]],  //XA
			'CNXM2' => [["type" => "id", "joint" => 1],["type" => 'label']],                                     //XMXY
			'CNXMN' => [["type" => "id", "joint" => 1]],                                                                    //XMBC  
			'CNCSX' => [["type" => "id", "joint" => 1],["type"=>"rcpt"],["type" => 'label', "ft" => "pdf"]],           //JXCS
			'CNCS3' => [["type" => "id", "joint" => 1],["type"=>"rcpt"],["type" => 'label', "ft" => "pdf"]],            //JXHN
			'CNJMN'=>[["type" => "id", "joint" => 1]],                                                                 //JMXY          
			'CNJM2'=>[["type" => "id", "joint" => 1]],                                                                //JMBC              
		];
//                      [["type" => "id", "joint" => 1],     //joint id
//                      ["type"=>"id_valid"],                //id validation
//                      ["type" => 'label', "ft" => "pdf"],   //Couriers label(PDF)
//                      ["type"=>"3in1"],                     // xi'an 3in1
//                      ["type"=>"rcpt"],                     //shopping receipt
//                      ["type"=>"rcpt","ft"=>"pdf"],         //shopping receipt (PDF)
//                      ["type"=>"rcpt","ft"=>"doc"],         //shopping receipt (WORD)     
//                      ["type" => 'label']]                   //Couriers label(JPG

		if (!empty($rs)) {
			foreach ($rs as $r) {
				$consol = Consol::model()->find('id=:id ', array(':id' => $r->pid));
				if (!empty($consol)) {
					if (!empty($consol->poc)) {
						if (!empty($portRequirement[$consol->poc])) {
							$requirement = $portRequirement[$consol->poc];
							foreach ($requirement as $req) {
//                             
								unset($_GET);
								foreach ($req as $k => $v) {
									$_GET[$k] = $v;
								}
								$_GET['id'] = $consol->id;
								$_GET['consol_copy'] = 1;
								$this->actionDownload($consol->id);
							}
						}
					}
				}
				$r->finish = 1;
				$r->save();
			}
		}
	}


		/**
	 * Ref.
	 */
	public function actionRef(){
		$this->render('ref');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=ImcoConsol::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='ex-consol-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
