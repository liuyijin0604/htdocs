<?php

class ExcoConsolController extends Controller{

	protected $nonAjax=array('export', 'exloclist', 'download', 'palletRpt', 'pltMark');

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
		if(isset($_POST['ExcoConsol'])){
			if(empty($_POST['ps'])){
				$model->addError('id', 'No port selected!');
			}else{
				$cs = ExChannel::model()->findAll('status = 50');
				$dup_allowed = [];
				$p2c = [];
				foreach($cs as $c){
					if(!empty($c->mdata['dupq']) && $c->mdata['dupq'] > 1)
						$dup_allowed[$c->code] = $c->mdata['dupq'];

					$c2p[$c->code] = $c->pod;
				}
				foreach($_POST['ps'] as $k=>$ids){
					$model=new ExcoConsol('create');
					$model->attributes=$_POST['ExcoConsol'];
					$model->status = 10;
					$model->pol = ExcoConsol::$pols[$_POST['ExcoConsol']['dpt_id']];
					$model->pod = isset($c2p[$k])? $c2p[$k] : $k;
					$model->poc = $k;
					$model->save();
					$ap = array();
					if(!empty($ids)){
						$trans = Yii::app()->db->beginTransaction();
						try{
							foreach(explode(',', $ids) as $pid){
								$p = ExParcel::model()->findByPk($pid);
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

		$model=new ExcoConsol;

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

	public function actionExport($id){
		$model=$this->loadModel($id);
		$xls = new oExcel;
		$i = 1;
		$of = $model->no.(empty($model->awb)? '' : '_'.$model->awb).'_Manifest';
		$sql = "SELECT t.id FROM `shipment` t 
				LEFT JOIN storage_log sl ON sl.model = 'ExParcel' AND out_dt IS NULL AND sl.fid = t.id
				LEFT JOIN storage s ON sl.sid = s.id
				WHERE t.consol_id = ".$id." GROUP BY t.id ORDER BY s.name, t.hbn";
		$shipments = Yii::app()->db->createCommand($sql)->queryAll();

		$bjGoodsName = function($g){
			$g = preg_replace('/[\&]+/','', $g);
			$g = preg_replace('/(1|2|3|4|一|二|三|四)段/','', $g);
			$g = preg_replace('/(全|脱)脂/','', $g);
			$g = preg_replace('/可瑞康|爱他美金装|白金版爱他美奶/','Nutricia纽迪希亚', $g);
			$g = str_replace('羊奶粉','奶粉', $g);
			return $g;
		};

		$bjBrandName = function($b){
			$b = preg_replace('/[\&]+/','', $b);
			$b = preg_replace('/Karicare|可瑞康|爱他美/', 'Nutricia纽迪希亚', $b);
			return $b;
		};
		$exc = ExChannel::model()->find('code = :c', [':c' => $model->poc]);

		switch($_GET['type']){
			case 'TZM'://TZM
			$of .= '-TZ.xlsx';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('运单');
			$xls->addRow($i++, array('关联单号', '转单号', '重量', '收件人', '身份证件号码', '收件人电话号码', '收件人地址', '省份', '城市', '邮编', '物品序号', '物品名称（包裹内的单种物品）', '品牌', '规格', '税号', '单种物品单价', '单种物品数量', '单种物品净重', '单种物品单位', '单种物品总价', '单种物品税金', '包裹总税金', '币别'));
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				if(!empty($_GET['o'])){
					unset($p->mdata['altItems']);
					unset($p->mdata['altTel']);
					unset($p->mdata['altAddr']);
				}
				$p->altGoods();
				$p->altCnee();
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}

				if(empty($p->eitems['g'][0])) die($p->hbn.' 缺少商品信息');
				foreach($p->eitems['g'] as $gi => $g){
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = intval($p->eitems['q'][$gi]) == 0 ? 1 : intval(trim($p->eitems['q'][$gi]));

					if(empty($pd)){
						$v = floatval($p->eitems['v'][$gi]);
						$hs = $p->eitems['hs'][$gi];
					}else{
						$v = empty($pd->mdata['price_'.$model->poc])? $pd->price : $pd->mdata['price_'.$model->poc];
						if(!empty($p->mdata['altItems']['v'][$gi])) $v = $p->mdata['altItems']['v'][$gi];
						$hs = empty($pd->mdata['hs_'.$model->poc])? $pd->hs : $pd->mdata['hs_'.$model->poc];
						$p->eitems['g'][$gi] = empty($pd->mdata['name_'.$model->poc])? $pd->name_zh : $pd->mdata['name_'.$model->poc];
						$p->eitems['m'][$gi] = $pd->model;
						$p->eitems['b'][$gi] = $pd->brand;
						$p->eitems['w'][$gi] = $pd->weight * $qty;
					}
					$t = HsPoc::getTariff($hs, $exc, $v, floatval($p->eitems['w'][$gi]) / $qty);
					$xls->addRow($i++, array('="'.$p->hbn.'"', '="'.$p->ref.'"', $p->shipWeight(), $p->cnee->name, '="'.$cnid.'"', '="'.$p->cnee->tel.'"', $p->cnee->getCnFullAddress(), $p->cnee->state, $p->cnee->city, '="'.$p->cnee->postcode.'"', ($gi+1), $g, $p->eitems['b'][$gi], $p->eitems['m'][$gi], $hs, $v, $qty, $p->eitems['w'][$gi], $p->eitems['u'][$gi], $v * $qty, $t[2] * $qty, $gi == 0? $p->calTariff($model->poc) : 0, 'RMB'));
				}
			}
			break;
			case 'CNPEK'://BJ
			$of .= '-BJ.xlsx';
			$xls->addRow($i++, ["主管海关代码","业务类型","业务时间","订单编号","运单号","备注","运输\n方式","进出境口岸代码","进出境\n日期","申报单位\n代码","申报单位\n名称","申报口岸","申报日期","主运单号","清单编号","包装种类代码","发货人\n城市","收货人\n身份证","收货人\n国别","收货人\n城市","件数","物流企业代码\n海关注册代码","物流企业名称","运输工具名称","发货人\n姓名","发货人国家代码","收货人\n姓名","收货人所在国家(地区)代码","收货人\n地址","毛重","起运港\n代码","指运港\n代码","发货人\n地址","发货人\n电话","收货人\n电话","商品数量(件数)","商品简要信息","保价费","运费","跨境商户名称","序号","商品\n项号","商品\n税号","商品\n名称","规格\n型号","申报数量","计量单位","原产国","商品单价","商品总价","币制代码","成交数量","成交单位",""]);
			$pc = 1;
			$eco_states = ['河北省','天津市','山东省','河南省','江苏省','上海市','浙江省','安徽省','福建省','重庆市','四川省','广东省'];
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$weight = empty($p->mdata['dcwt'])? $p->shipWeight() : $p->mdata['dcwt'];
				$weight = ($weight < 1)? 1 : $weight;
				$whd = $p->genWHD();
				$tq = array_sum($p->eitems['q']);
				$frt = 20 + ceil($weight - 1) * 8;
				$r = array('="0100"', 'N', date('Y-m-d\TH:i:s'), 'MZ89265787'.$p->hbn, $p->ref, '', '="9"', '="0114"', $model->etd, '', '', '="0114"', $model->etd, $model->awb, '', '="2"', '', '="'.(empty($p->cnee->cnid)? '' : $p->cnee->cnid->no).'"', '="142"', $p->cnee->city, '="'.$tq.'"', 'WL36963566', '北京市邮政速递有限公司', $model->flight, 'Iexpress', '="601"', $p->cnee->name, '="142"', $p->cnee->getCnFullAddress(), $weight.'kg', '="601"', '="142"', '7/89 DERBY ST, SILVERWATER, NSW 2128', '="0280856368"', '="'.$p->cnee->tel.'"', '="'.$tq.'"', '', '', $frt, '大安东方国际贸易（北京）有限公司',);
				foreach($p->eitems['g'] as $gi => $g){
					$qty = floatval($p->eitems['q'][$gi]);
					if(empty($qty)) $qty = -1;
					$itv = $p->eitems['type'][$gi] == 'B'? 125 * $qty : $p->eitems['v'][$gi];
					if(!empty($p->eitems['pid'][$gi])){
						$ep = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						if(!empty($ep->mdata['price_CNPEK'])){
							$itv = $ep->mdata['price_CNPEK'] * $qty;
						}
					}
					$u = HS::getUpr($p->eitems['hs'][$gi], 'uc');
					if(!$u) $u = $p->eitems['u'][$gi];
					$u2 = $u;
					$g = $bjGoodsName($g);
					$b = $bjBrandName($p->eitems['b'][$gi]);
					if($p->eitems['type'][$gi] == 'B'){
						$u2 = 122;
					}elseif($p->eitems['type'][$gi] == 'M'){
						$u2 = 125;
					}
					$uq = ($p->eitems['u'][$gi] == '千克')? $p->eitems['w'][$gi] : $qty;

					$ri = array_merge($r, ['="'.($gi+1).'"', date('ymd').sprintf('%04d',$i), '="'.$p->eitems['hs'][$gi].'"', $g, $b, '="'.$uq.'"', '="'.$u.'"', '="601"', '="'.(round($itv / $qty * 100) /100).'"', '="'.$itv.'"', '="142"', '="'.$p->eitems['q'][$gi].'"', '="'.$u2.'"', in_array($p->cnee->state, $eco_states)? 'J' : 'B']);
					$xls->addRow($i++, $ri);
				}
			}
			break;
			case 'BJODR'://BJ Order
			$of .= '-BJOrder.xlsx';
			$xls->addRow($i++, ["主管海关代码", "业务类型", "业务时间", "进出口标示", "电商服务平台企业代码\n海关注册代码", "电商服务平台名称", "跨境商户企业代码\n海关注册代码", "跨境商户企业名称", "订单编号", "客户姓名", "客户证件号码", "备注", "总费用", "商品货款", "其他杂费", "海关税费", "支付企业代码,海关注册代码", "支付企业名称", "支付交易号", "物流企业代码,海关注册代码", "物流企业名称", "物流电子运单号", "发货人名称", "发货人地址", "发货人电话", "发货人所在国家（地区）代码", "收货人名称", "收货人地址", "收货人电话", "收货人所在国家(地区）代码", "支付备注", "商品货号", "商品名称", "商品规格类型", "商品条形码", "海关10位商品编码", "原产国", "币制", "计量单位", "申报数量", "成交单价", "折扣浮动价格", "是否赠品", "海关行邮税率"]);
				$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$whd = $p->genWHD();
				$weight = empty($p->mdata['dcwt'])? $p->shipWeight() : $p->mdata['dcwt'];
				$weight = ($weight < 1)? 1 : $weight;
				$tq = 0;
				$tv = 0;
				foreach($p->eitems['q'] as $gi=>$q){
					$tq += $q;
					$iv = $p->eitems['type'][$gi] == 'B'? 125 * $q : $p->eitems['v'][$gi];
					if(!empty($p->eitems['pid'][$gi])){
						$ep = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						if(!empty($ep->mdata['price_CNPEK'])){
							$iv = $ep->mdata['price_CNPEK'] * $q;
						}
					}
					$tv += $iv;
				}

				$r = array('="0100"', 'N', date('Y-m-d\TH:i:s'), 'I', 'DS48868815', '摩登百货香港贸易有限公司', 'MZ89265787', '大安东方国际贸易（北京）有限公司', 'MZ89265787'.$p->hbn, '', '', '', $tv, $tv, '="0"', '="0"', 'ZF99999999', '其他', date('ymd', strtotime($p->created)).sprintf('%04d',$i), 'WL36963566', '北京市邮政速递物流有限公司', $p->ref, 'Iexpress', '7/89 DERBY ST, SILVERWATER, NSW 2128', '="0286669222"', '="601"', $p->cnee->name, $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"', '142', '',);
				foreach($p->eitems['g'] as $gi => $g){
					$qty = $p->eitems['q'][$gi];
					$qty = floatval($qty);
					if(empty($qty)) $qty = -1;
					$itv = $p->eitems['type'][$gi] == 'B'? 125 * $qty : $p->eitems['v'][$gi];
					if(!empty($p->eitems['pid'][$gi])){
						$ep = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						if(empty($ep->mdata['price_CNPEK'])){
							$hs = $p->eitems['hs'][$j];
						}else{
							$itv = $ep->mdata['price_CNPEK'] * $qty;
							$hs = empty($pd->mdata['hs_CNPEK'])? $pd->hs : $pd->mdata['hs_CNPEK'];
						}
					}
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$j]);

					$u = HS::getUpr($hs, 'uc');
					if(!$u) $u = $p->eitems['u'][$gi];
					$u2 = $u;
					if($p->eitems['type'][$gi] == 'B'){
						$u2 = 122;
					}elseif($p->eitems['type'][$gi] == 'M'){
						$u2 = 125;
					}
					$g = $bjGoodsName($g);
					$b = $bjBrandName($p->eitems['b'][$gi]);
					$ri = array_merge($r, [date('ymd').sprintf('%04d',$i), $g, $b, '', '="'.$hs.'"', '="601"', '="142"', '="'.$u2.'"', '="'.$p->eitems['q'][$gi].'"', '="'.(round($itv / $qty * 100) / 100).'"', '', 'N', '']);
					$xls->addRow($i++, $ri);
				}
			}
			break;
			case 'BJINB'://BJ Inbound
			$of .= '-BJInbound.xls';
			$xls->format = 'Excel5';
			$xls->addRow($i++, ["*关联号码","*寄件人姓名","*寄件人联系方式","*寄件人地址","*收件人姓名","收件人证件号码","*收件人电话","*收件人地址","*收件人邮编","*申报价值（美元）","*长（cm）","*宽（cm）","*高（cm）","*体积重量（kg）","*实际重量（kg）","*计费重量（kg）","*内件品名","*进口口岸局名称","*进口口岸局代码","原寄地","*收寄日期","*大客户名称","*原产地"]);
				$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$whd = $p->genWHD();
				$xls->addRow($i++, [$p->hbn, $p->cnor->name, '="'.(empty($p->cnor->tel)? '0286669222' : $p->cnor->tel).'"', '6C The Crescent, Kingsgrove, NSW 2208, AUSTRALIA', $p->cnee->name, '="'.(empty($p->cnee->cnid)? '' : $p->cnee->cnid->no).'"', '="'.$p->cnee->tel.'"', $p->cnee->getCnFullAddress(), '="'.$p->cnee->postcode.'"', $p->tariff, $whd[0], $whd[1], $whd[2], (round($whd[0] * $whd[1] * $whd[2] / 80) / 100), $p->shipWeight(), $p->shipWeight(), $p->GoodsNames(), '北京', 'BJBJS001', '澳大利亚', date('Y-m-d'), '大安东方国际贸易（北京）有限公司', '澳大利亚']);
			}
			break;
			case 'BJSUM'://BJ Order
			$of .= '-BJSummary.xlsx';
			$xls->addRow($i++, ["主运单号:", "", "客户名:", "日期: ".date('Y-m-d'), "国别", "澳大利亚"]);
			$xls->addRow($i++, ["序号","运单号","品名","数量","毛重","金额"]);
			$pc = 1;
			$stv = 0;
			$sqty = 0;
			$stw = 0;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$whd = $p->genWHD();
				$weight = empty($p->mdata['dcwt'])? $p->shipWeight() : $p->mdata['dcwt'];
				$weight = ($weight < 1)? 1 : $weight;
				$tv = 0;
				foreach($p->eitems['q'] as $gi=>$q){
					$qty = $p->eitems['q'][$gi];
					$qty = floatval($qty);
					$iv = $p->eitems['type'][$gi] == 'B'? 125 * $qty : $p->eitems['v'][$gi];
					if(!empty($p->eitems['pid'][$gi])){
						$ep = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						if(!empty($ep->mdata['price_CNPEK'])){
							$iv = $ep->mdata['price_CNPEK'] * $qty;
						}
					}
					$tv += $iv;
				}

				$stv += $tv;
				$stw += $weight;
				$mitems = [];
				foreach($p->eitems['g'] as $gi => $g){
					$g = preg_replace('/[\&]+/','', $g);
					$g = preg_replace('/(1|2|3|4|一|二|三|四)段/','', $g);
					if(!isset($mitems[$g])) $mitems[$g] = 0;
					$mitems[$g] += $p->eitems['q'][$gi];
				}
				foreach($mitems as $g=>$q){
					$xls->addRow($i++, [$pc, $p->ref, $g, '="'.$q.'"', $weight.'kg', $tv]);
					$sqty +=$p-> eitems['q'][$gi];
					$pc++;
				}
			}
			$xls->addRow($i++, ['', '', '合计', $sqty, $stw, $stv]);
			break;
			case 'CNSHA'://SH
			$of .= '-SH.xlsx';
			$xls->addRow($i++, array('序号','客户代码','航班号','总运单号','始发国/地区','起运港','目的港','航班起飞时间（当地时间）','CNPEX快件单号','境内配送单号','收件人姓名','证件类型','收件人身份证或有效证件号','收件人省份','收件人城市','收件人公司','收件人地址','收件人邮编','收件人电话','快件毛重（KG）','长（厘米）','宽（厘米）','高（厘米）','总申报价值','货币','内件品名1','规格型号1','内件数量1','数量单位1','内件毛重1','内件净重1','重量单位1','内件价值1','税号1','原产国/消费国1','产品序列号1','内件品名2','规格型号2','内件数量2','数量单位2','内件毛重2','内件净重2','重量单位2','内件价值2','税号2','原产国/消费国2','产品序列号2','内件品名3','规格型号3','内件数量3','数量单位3','内件毛重3','内件净重3','重量单位3','内件价值3','税号3','原产国/消费国3','产品序列号3','内件品名4','规格型号4','内件数量4','数量单位4','内件毛重4','内件净重4','重量单位4','内件价值4','税号4','原产国/消费国4','产品序列号4','内件品名5','规格型号5','内件数量5','数量单位5','内件毛重5','内件净重5','重量单位5','内件价值5','税号5','原产国/消费国5','产品序列号5','内件品名6','规格型号6','内件数量6','数量单位6','内件毛重6','内件净重6','重量单位6','内件价值6','税号6','原产国/消费国6','产品序列号6','内件品名7','规格型号7','内件数量7','数量单位7','内件毛重7','内件净重7','重量单位7','内件价值7','税号7','原产国/消费国7','产品序列号7','是否保价','保价金额','是否液体','报关类别','是否包装','是否需要耗材','包装箱规格','寄件人姓名','寄件人公司','寄件人地址'));
			$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$whd = $p->genWHD();
				$r = array($pc++, '', $model->flight, $model->awb, '601-澳大利亚', '601-澳大利亚', '142-中国境内', '', $p->ref, $p->hbn, $p->cnee->name, '1-身份证', '="'.$p->cnee->cnid->no.'"', $p->cnee->state, $p->cnee->city, '', $p->cnee->getCnFullAddress(), '="'.$p->cnee->postcode.'"', '="'.$p->cnee->tel.'"', round($p->shipWeight()*100)/100, $whd[0], $whd[1], $whd[2], $p->value, '142-CNY');
				foreach($p->eitems['g'] as $gi => $g){
					$qty = ($p->eitems['u'][$gi] == '千克')? $p->eitems['w'][$gi] : $p->eitems['q'][$gi];
					$r = array_merge($r, array($g, $p->eitems['b'][$gi].$p->eitems['m'][$gi], $qty, $p->eitems['u'][$gi], $p->eitems['w'][$gi], $p->eitems['w'][$gi], 'KG', $p->eitems['v'][$gi], '="'.$p->eitems['hs'][$gi].'"', '601-澳大利亚', ''));
				}

				while($gi++ < 6){
					$r = array_merge($r, array('', '', '', '', '', '', '', '', '', '', ''));
				}
				$r = array_merge($r, array($p->insurance>0? 'Y':'N', $p->insurance, '', 'B', '', '', '', $p->cnor->name, 'Top Logistics Australia', '6C The Crescent, Kingsgrove, NSW 2208, AUSTRALIA'));
				$xls->addRow($i++, $r);
			}
			break;
			case 'CNCTU'://CD
			$of .= '-CD.xlsx';
			$xls->addRow($i++, array('序号','航空主单号码','中外运分单号','客户参考号','发货人',"发货人\n电话","发货\n地址","发货\n国家",'收货人',"收货人\n电话","收货\n地址","收货\n省份",'收货人身份证号码',"包裹\n件数","包裹\n重量","包裹价格\n（人民币）","内物\n序号",'内物品名','规格型号',"内物\n行邮税税号","内物\n数量","内物\n单位","内物单价\n（人民币）","内物总价\n（人民币）", "关税金额\n（人民币）",'备注'));
			$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$p->altGoods();
				$weight = $p->shipWeight(true);
				$pv = 0;
				foreach($p->eitems['g'] as $gi => $g){
					$tv = $p->eitems['v'][$gi];
					$ep = empty($p->eitems['pid'][$gi])? new ExProdb : ExProdb::model()->findByPk($p->eitems['pid'][$gi]);

					if(empty($p->eitems['b'][$gi])) $p->eitems['b'][$gi] = $ep->model;
					if(empty($p->eitems['m'][$gi])) $p->eitems['m'][$gi] = $ep->model;
					if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = $ep->unit;
					if(empty($p->eitems['w'][$gi])) $p->eitems['w'][$gi] = $ep->weight * $p->eitems['q'][$gi];
					if(empty($p->eitems['hs'][$gi])) $p->eitems['hs'][$gi] = $ep->hs;

					$qty = ($p->eitems['u'][$gi] == '千克')? $p->eitems['w'][$gi] : $p->eitems['q'][$gi];
					$qty = floatval($qty);
					if(empty($qty)) $qty = -1;
					
					if(!empty($ep->mdata['price_CNCTU'])){
						$tv = $ep->mdata['price_CNCTU'] * $p->eitems['q'][$gi];
					}
					if(!empty($ep->mdata['name_CNCTU'])){
						$g = $ep->mdata['name_CNCTU'];
					}
					if($p->eitems['type'][$gi] == 'B'){
						$g .= '/罐';
					}elseif($p->eitems['type'][$gi] == 'M'){
						$g .= '/袋';
					}
					$pv += $tv;

					if($gi == 0){
						$mli = $i;
						$xls->addRow($i++, array($pc++, $model->awb, $p->ref, $p->hbn, $p->cnor->name, (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"'), (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), '澳大利亚', $p->cnee->name, '="'.$p->cnee->tel.'"', $p->cnee->getCnFullAddress(), $p->cnee->state, '="'.(empty($p->cnee->cnid)? '': strtoupper($p->cnee->cnid->no)).'"', $p->pkg, $weight, $pv, $gi+1, $g, $g, '="'.$p->eitems['hs'][$gi].'"', $qty, $p->eitems['u'][$gi], round($tv / $qty), round($tv), round($tv*0.15), ''));
					}else{
						$xls->addRow($i++, array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $gi+1, $g, $g, '="'.$p->eitems['hs'][$gi].'"', $qty, $p->eitems['u'][$gi], round($tv / $qty), round($tv), $tv/10, ''));
					}
					$xls->setCell('P'.$mli, $pv);
				}
			}
			break;
			case 'CNCA2'://YP GZ
			$of .= '-YP_GZ.xlsx';
			$xls->addRow($i++, array('订单号/运单号','订单总价','币制','重量','内物件数','收件人身份证号','收件人','省','市','区/县','收件地址','邮编','电子邮件','收件人电话','收件人所在国','发件人','发件人地址','发件人电话','发件人所在国','品名（含规格品牌型号等）','商品条码','原产国','数量','单价','总价','单位','币制','备注'));
			$xls->addRow($i++, array('OrderNo','Payment','Curr','GrossWt(kg)','PackNo','ConsigneeID','ConsigneeName','State','City','Suburb','ConsigneeAddress','ZipCode','E-Mail','ConsigneePhone','ConsigneeCountry','Shipper','ShipperAddress','ShipperTelephone','ShipperCountry','ShelfGName','SKU CODE','Origin Country','GQty','DeclPrice','DeclTotal','GUnit','Curr','NoteS'));

			$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$tq = array_sum($p->eitems['q']);
				foreach($p->eitems['g'] as $gi => $g){
					$tv = $p->eitems['v'][$gi];
					$qty = floatval($p->eitems['q'][$gi]);
					if(empty($qty)) $qty = -1;
					if(!empty($p->eitems['pid'][$gi])){
						$ep = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						if(!empty($ep->mdata['price_CNCA2'])){
							$tv = $ep->mdata['price_CNCA2'] * $qty;
						}
						if(!empty($ep->mdata['name_CNCA2'])){
							$g = $ep->mdata['name_CNCA2'];
						}
					}
					$u = $p->eitems['type'] == 'M'? '袋' : '罐';
					$row = array($g, '', '澳大利亚', $qty, round($tv / $qty *100)/100 , $tv, $p->eitems['u'][$gi], '人民币', $p->hbn);
					if($gi == 0){
						$row = array_merge(array('="'.$p->ref.'"', $p->getDvalue(), '人民币', $p->shipWeight(), $tq, '="'.strtoupper($p->cnee->cnid->no).'"', $p->cnee->name, $p->cnee->state, $p->cnee->city, $p->cnee->suburb, $p->cnee->getCnFullAddress(), '="'.$p->cnee->postcode.'"', '', '="'.$p->cnee->tel.'"', '中国',	$p->cnor->name, (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"'), '澳大利亚'), $row);
					}else{
						$row = array_merge(array_fill(0, 19, ''), $row);
					}
					
					$xls->addRow($i++, $row);
				}
				$pc++;
			}
			break;
			case 'CNCA3': //ZH GZ
				$of .= '-GZ.xlsx';
				$xls->addRow($i++, array('序号','原客户单号 ','单号','发件人','发件人地址','发件人电话','收件人','收件人电话','城市','邮编','收件人地址','数量','价值','重量','行邮税号','物品名称','品牌','规格型号','物品数量','单位','单位代码','单价','币别','身份证件号码','税金（数量*单位*税率）(可不填）','运输工具（可不填）','客户编号','购物小票号码（可不填）','价格网址（可不填）','发货人国别'));

				$pc = 1;
				function psn($p, $i){
					$g = $p->eitems['g'][$i];
					$g = str_ireplace($p->eitems['b'][$i], '', $g);
					if($p->eitems['u'][$i] != '千克'){
						$g = str_ireplace($p->eitems['m'][$i], '', $g);
					}
					if($p->eitems['type'][$i] == 'B'){
						$g = str_replace('奶粉', '婴儿奶粉', $g);
						$g = str_replace('白金版', '', $g);
						$g = str_replace('金装', '', $g);
					}else{
						$g = str_ireplace($p->eitems['m'][$i], '', $g);
					}
					$g = preg_replace('/(\d+)(g|片|ml|粒|克)$/i', '', $g);
					return trim($g);
				}
				foreach($model->shipments as $p){
					$rows = [];
					$tv = 0;
					foreach($p->eitems['g'] as $gi => $g){
						if(empty($p->eitems['pid'][$gi])){
							$pd = ExProdb::model()->find('name_zh LIKE :g', [':g' => $g]);
						}else{
							$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						}
						$qty = floatval($p->eitems['q'][$gi]) == 0 ? 1 : $p->eitems['q'][$gi];
						if(empty($pd)){
							$v = $p->eitems['t'][$gi];
							$hs = $p->eitems['hs'][$gi];
							$m = $p->eitems['u'][$gi] == '千克'? ($p->eitems['w'][$gi]/$qty * 1000).'G' : $p->eitems['m'][$gi];
						}else{
							$v = empty($pd->mdata['price_CNCA3'])? $pd->tax : $pd->mdata['price_CNCA3'];
							$hs = empty($pd->mdata['hs_CNCA3'])? $pd->hs : $pd->mdata['hs_CNCA3'];
							$m = $pd->unit == '千克'? ($pd->weight * 1000).'G' : $pd->model;
							if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = $pd->unit;
							if(empty($p->eitems['b'][$gi])) $p->eitems['b'][$gi] = $pd->brand;
							if(empty($p->eitems['w'][$gi])) $p->eitems['w'][$gi] = $pd->weight * $qty;
						}
						if(empty($m)){
							if(preg_match('/((\d+)(g|片|ml|粒|克))$/i', $p->eitems['g'][$i], $mm)){
								$m = $mm[1];
							}
						}
						if(empty($m) && !empty($pd->weight)){
							$m = ($pd->weight * 1000).'g';
						}
						$u = HS::getUC($p->eitems['u'][$gi]);
						$tv += $p->eitems['u'][$gi] == '千克'? $v * $p->eitems['w'][$gi] : $v * $p->eitems['q'][$gi];

						if(empty($qty)) $qty = -1;
						$rcp = $p->receiptData();
						$idno = empty($p->cnee->cnid)? '' : $p->cnee->cnid->no;
						if(!empty($p->mdata['AltCnee'])){
							$alcn = Addr::model()->findByPk($p->mdata['AltCnee']);
							$p->cnee->name = $alcn->name;
							$idno = $alcn->cnid->no;
						}
						$row = array('="'.$hs.'"', psn($p, $gi), $p->eitems['b'][$gi], $m, $p->eitems['u'][$gi] == '千克'? $p->eitems['w'][$gi] : $qty, $p->eitems['u'][$gi], '="'.$u.'"', $v, '502', '="'.$idno.'"', '', '', '散货', $rcp['no'], '', 601);
						if($gi == 0){
							$rows[] = array_merge(array($pc, '="'.$p->hbn.'"', '="'.$p->ref.'"', $p->cnor->name, (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"'), $p->cnee->name,'="'.$p->cnee->tel.'"', $p->cnee->city, '="'.$p->cnee->postcode.'"', $p->cnee->getCnFullAddress(), $p->itemsTotQty(), '', $p->shipWeight()), $row);
						}else{
							$rows[] = array_merge(array('','','','','','','','','','','','','',''), $row);
						}
					}
					$rows[0][12] = $tv;
					foreach($rows as $row){
						$xls->addRow($i++, $row);
					}
					$pc++;
				}
			break;
			case 'CNCHQ': //IE CQ
				$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.(empty($model->awb)? '' : '_'.$model->awb).'_x3.zip';
				$zip = new ZipArchive;
				$zip->open($zf, ZipArchive::CREATE);
				$pc = 1;
				function psn($p, $i, $mb = false){
					$g = $p->eitems['g'][$i];
					
					if($p->eitems['type'][$i] == 'B'){
						$g = str_replace('白金版', '', $g);
						$g = str_replace('金装', '', $g);
						//$g = ($mb? $p->eitems['b'][$i] : '').'婴儿奶粉';
					}elseif($p->eitems['type'][$i] == 'M'){
						$g = ($mb? $p->eitems['b'][$i] : '').'成人奶粉';
					}elseif($p->eitems['type'][$i] == 'O'){
						$g = str_ireplace($p->eitems['m'][$i], '', $g);
						$g = str_ireplace($p->eitems['b'][$i], '', $g);
						$g = preg_replace('/(\d+)(g|片|ml|粒|克)$/i', '', $g);
					}

					return trim($g);
				}
				
				$vrows = [];
				$irows = [];
				$frows = [];
				$rows = [];
				foreach($model->shipments as $p){
					$nrows = [];
					$tqty = 0;
					$tduty = 0;
					$tnw = 0;
					if(empty($p->cnee->cnid_id)){
						$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
					}else{
						$cnid = $p->cnee->cnid->no;
					}
					foreach($p->eitems['g'] as $gi => $g){
						$pd = empty($p->eitems['pid'][$gi])? false : ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						$qty = floatval($p->eitems['q'][$gi]) == 0 ? 1 : $p->eitems['q'][$gi];
						$tqty += $qty;
						if(empty($pd)){
							$v = round($p->eitems['v'][$gi] / $qty *100)/100;
							$hs = $p->eitems['hs'][$gi];
						}else{
							$v = empty($pd->mdata['price_CNCHQ'])? $pd->price : $pd->mdata['price_CNCHQ'];
							$hs = empty($pd->mdata['hs_CNCHQ'])? $pd->hs : $pd->mdata['hs_CNCHQ'];
							$p->eitems['g'][$gi] = empty($pd->mdata['name_CNCHQ'])? $pd->name_zh : $pd->mdata['name_CNCHQ'];
							$p->eitems['m'][$gi] = $pd->model;
							$p->eitems['b'][$gi] = $pd->brand;
						}
						$gm = '';
						if($p->eitems['type'][$gi] == 'B'){
							$gm = '900g';
						}elseif($p->eitems['type'][$gi] == 'M'){
							$gm = '1000g';
						}
						
						if(empty($qty)) $qty = -1;
						$tr = HS::getUpr($hs, 'rate') * 100;
						$duty = round($v * $qty * $tr) / 100;
						$tduty += $duty;
						$u = HS::getUpr($hs, 'uc');
						if(empty($u)) $u = $p->eitems['u'][$gi];

						$rows[] = ['="'.$p->ref.'"', 1, $p->shipWeight(), $p->cnee->name, '="'.$cnid.'"', $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"', '没有', '="'.$hs.'"', psn($p, $gi), $p->eitems['b'][$gi], $p->eitems['m'][$gi], $qty, $p->eitems['w'][$gi], $p->eitems['u'][$gi], round($v * $qty * 100) / 100, '', ''];

						if($u == '035' && $p->eitems['type'][$gi] != 'O'){
							if(empty($p->eitems['w'][$gi])) $p->eitems['w'][$gi] = 1;
							$v = round($v * $qty / $p->eitems['w'][$gi] * 100)/100;
							$qty = $p->eitems['w'][$gi];
						}
						
						$g = psn($p, $gi);
						if($gi == 0){
							$nrows[] = [$pc, '="'.$p->ref.'"', '="'.$hs.'"', $g, $p->eitems['b'][$gi], $gm, $v* $qty, $v, $qty, '="'.sprintf('%03d', $u).'"', $p->shipWeight(), $p->eitems['w'][$gi], '', '', '', $p->cnee->name, $p->cnee->getCnFullAddress(), $p->cnee->tel, 1, '="'.$cnid.'"', 1, 0, 0, '="'.$p->hbn.'"'];
						}else{
							$nrows[] = ['', '', '="'.$hs.'"', $g, $p->eitems['b'][$gi], $gm, $v* $qty, $v, $qty, '="'.sprintf('%03d', $u).'"', '', $p->eitems['w'][$gi], '', '', '', '', '', '', '', '', '', 0, '', ''];
						}
						$tnw += $p->eitems['w'][$gi];
					}
					$nrows[0][22] = $tqty;
					foreach($nrows as $ri=>$row){
						$row[21] = round($row[11] / $tnw * $nrows[0][10] * 100) / 100;
						$row[11] = $ri == 0? $tnw : '';
						$vrows[] = $row;
					}

					$irows[] = ['="'.$cnid.'"', $p->cnee->name, $p->cnee->tel];
					$frows[] = ['="'.$p->ref.'"', '="'.$p->hbn.'"', $p->GoodsNames(), '物品', $p->shipWeight(), 0, 0, $p->cnee->city, $p->cnee->postcode, '', $p->cnee->name, $p->cnee->getCnFullAddress(), $p->cnee->tel, '', '','', '', 0, 0, 0, 0, '', $tqty, '', '', ''];
					$pc++;
				}

				$xls->addRow($i++, array('申报分单物品明细表'));
				$xls->addRow($i++, array('分运单号', '分单总件数', '分单毛总（KG）', '收件人', '收件人证件号', '收件人地址', '收件人电话', '木质包装材料/动植物铺垫材料(有/没有)', '行邮税号', '货物品名', '品牌', '规格型号', '数量', '净重（KG）', '计件单位', '申报价格', '箱号', '备注'));
				foreach($rows as $row){
					$xls->addRow($i++, $row);
				}
				$zip->addFromString($model->no.'_CIQ.xlsx', $xls->output(false, 'Excel2007', false));

				//new file
				$xls = new oExcel;
				$sheet = $xls->getActiveSheet();
				$sheet->setTitle('申报单');
				$i = 1;

				$xls->addRow($i++, ['进出境快件个人物品申报单']);
				$xls->addRow($i++, ['运营人名称', '中国邮政速递物流股份有限公司重庆市分公司', '', '', '', '', '	进/出口岸',	'8011', '', '', '', '', '', '', '运输工具航次', $model->flight]);
				$xls->addRow($i++, ['进/出口日期', $model->etd, '', '', '', '', '', '', '', '', '', '', '', '', '总运单号码', $model->awb]);
				$xls->addRow($i++, ['发件人', 'Top Logistics', '', '发件人国别', '澳大利亚', '发件人城市', '悉尼', '', '报关员代码', '', '000000000002656476', '', '货主单位名称', '', '中国邮政速递物流股份有限公司重庆市分公司', '码头/货场代码', '', '产销国', '澳大利亚', '运输方式代码',	'5']);
				$xls->addRow($i++, ['发送方编号', '5003980036', '',	'口岸代码', '8011',	'抵运港', '', '', '申报口岸代码', '', '', '', '包装种类', '2', '贸易国别（起/抵运地）', '澳大利亚', '运输工具名称', '飞机', '', '']);
				$xls->addRow($i++, ['申报单位代码', '5003980036', '', '申报单位名称', '中国邮政速递物流股份有限公司重庆市分公司', '', '', '', '', '', '', '', '经营单位性质', '', '8', '物流企业编号', '11', '数据交换平台用户编号','10', '', '']);
				$xls->addRow($i++, ['录入人', '孙茂智', '', '录入单位名称', '中国邮政速递物流股份有限公司重庆市分公司', '', '', '', '', '', '', '', '录入单位代码', '5003980031', '物流企业名称', '12', '', '', '', '']);						
				$xls->addRow($i++, ['序号', '分运单号', '税号', '品名', '品牌', '规格型号', '实际价值（RMB）', '单价', '件数', '申报计量单位', '毛重（KG）', '净重（KG)', '税率', '税额', '实收税额', '收件人', '收件人地址', '收件人电话', '袋号', '收发件人证件号', '收发件人证件类型', '分毛重', '总件数', '备注']);
				$xls->mergeCells('A1:X1');

				//$ttls =  array('序号', '分运单号', '件数', '毛重', '净重', '发件人', '收件人', '发件人国别', '发件人城市', '收发件人证件类型', '收发件人证件号', '价值', '商品序号', '商品编号（行邮税号）', '商品名称（B类叫：物品名称）', '商品规格、型号', '产销国', '成交总价', '申报单价', '申报总价', '申报数量', '申报计量单位', 'HBN');

				foreach($vrows as $r){
					$xls->addRow($i++, $r);
				}
				$zip->addFromString($model->no.'_申报单.xlsx', $xls->output(false, 'Excel2007', false));

				//new file
				$xls = new oExcel;
				$sheet = $xls->getActiveSheet();
				$sheet->setTitle('身份证验证');
				$i = 1;
				$xls->addRow($i++, ['身份证号', '姓名', '电话号码']);
				foreach($irows as $r){
					$xls->addRow($i++, $r);
				}
				$zip->addFromString($model->no.'_身份证验证.xlsx', $xls->output(false, 'Excel2007', false));

				//new file
				/*
				$xls = new oExcel;
				$sheet = $xls->getActiveSheet();
				$i = 1;
				$xls->addRow($i++, ['邮件号','*配货单号','客户订单号','*寄件人姓名','*寄件人联系方式','寄件人联系方式（2）','*寄件人地址','寄件人公司','寄件省','寄件市','寄件县','寄件人邮编','*收件人姓名','*收件人联系方式','收件人联系方式（2）','*收件人地址','收件人公司','到件省/直辖市','到件城市','到件县/区','收件人邮编','物品重量','物品长度','物品宽度','物品高度','打单时间','备注','业务类型','代收货款','收件人付费','应收货款/邮资','应收货款/邮资（大写）','内件性质','内件数','内件信息','留白一','留白二','付费类型','所负责任','保价金额','保险金额','其他费']);
					$pc = 1;
				foreach($model->shipments as $p){
					$whd = $p->genWHD();
					$xls->addRow($i++, ['', '="'.$p->ref.'"', '="'.$p->hbn.'"', $p->cnor->name, '="'.(empty($p->cnor->tel)? '0286669222' : $p->cnor->tel).'"', '', '6C The Crescent, Kingsgrove, NSW 2208, AUSTRALIA', '', '', '', '', '', $p->cnee->name, '="'.$p->cnee->tel.'"', '', $p->cnee->getCnFullAddress(), '', $p->cnee->state, $p->cnee->city, $p->cnee->suburb, '="'.$p->cnee->postcode.'"', $p->shipWeight(), $whd[0], $whd[1], $whd[2], '', '', '快递包裹', '', '', '', '', '', empty($p->eitems['q'])? 0 : array_sum($p->eitems['q']), $p->GoodsNames(), '="'.substr($p->ref,-4).'"', '', '', '', '', '', '']);
				}
				$zip->addFromString($model->no.'_换单.xlsx', $xls->output(false, 'Excel2007', false));
				*/

				//new file
				$xls = new oExcel;
				$sheet = $xls->getActiveSheet();
				$i = 1;
				$xls->addRow($i++, ['邮件号*', '内件号', '内件名称', '内件性质*', '重量*', '保险金额', '保价金额', '收件人城市*', '收件人邮编*', '收件人单位', '收件人姓名', '收件人街道', '收件人电话1', '收件人电话2', '寄件人姓名', '寄件人单位', '寄件人邮编', '寄件人电话1', '寄件人电话2', '寄件人街道', '其他费', '体积长', '体积宽', '体积高', '返单邮件号', '内件数', 'ETA时间', '运输方式', '带换货标识']);
				foreach($frows as $r){
					$xls->addRow($i++, $r);
				}
				$zip->addFromString($model->awb.'_非代收表.xlsx', $xls->output(false, 'Excel2007', false));
				

				$zip->close();

				header("Cache-Control: maxage=1");
				header("Content-Description: File Transfer");
				header("Content-type: application/octet-stream");
				header('Content-Disposition: attachment; filename="'.basename($zf).'"');
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".filesize($zf));
				readfile($zf);
				unlink($zf);
				Yii::app()->end();
			break;
			case 'CQYD': //CQ YD
				$of .= '-CQYD.xlsx';
				$xls->addRow($i++, array('', '韵达快递换单表'));		
	$xls->addRow($i++, array('', '航空主运单号： '.$model->awb, '', '', '航运袋数：', '', '		实际件数（箱数）：	'.sizeof($model->shipments), '', '国内快递填写', '', '', '货物保价费'));

				$xls->addRow($i++, array('序号', '收件人姓名', '收件人地址', '收件人电话', '品名', '货物重量（Kg）', '袋号', '箱号', '国内快递换单号', '国内快递计算重量', '国内快递结算价格', '保价金额', '保价费（按保价金额的1.5%）'));
				$pc = 1;
				function psn($p, $i){
					$g = $p->eitems['g'][$i];
					if($p->eitems['type'][$i] == 'B'){
						$g = str_replace('奶粉', '婴儿奶粉', $g);
						$g = str_replace('白金版', '', $g);
						$g = str_replace('金装', '', $g);
					}elseif($p->eitems['type'][$i] == 'O'){
						$g = str_ireplace($p->eitems['m'][$i], '', $g);
						$g = str_ireplace($p->eitems['b'][$i], '', $g);
						$g = preg_replace('/(\d+)(g|片|ml|粒|克)$/i', '', $g);
					}

					if(preg_match('/水光针|羊胎素/', $g)){
						$g = '面膜';
					}
					return trim($g);
				}
				foreach($model->shipments as $p){
					$gs = [];
					foreach($p->eitems['g'] as $gi => $g){
						$gs[] = psn($p, $gi);
					}
					
					$xls->addRow($i++, array($pc, $p->cnee->name, $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"', implode(' ', $gs), $p->shipWeight(), '', '="'.$p->ref.'"'));
					$pc++;
				}
			break;
			case 'CNTSN'://YP TJ
			$of .= '-YP_TJ.xlsx';
			$xls->addRow($i++, array('主单号', $model->awb,	'航班号', $model->flight, '发件人', 'Top Logistics Australia', '发件人地址', '6C The Crescent, Kingsgrove, NSW 2208, AUSTRALIA', '件数', Manifest::model()->count('type = 60 AND consol_id = :cid', [':cid' => $model->id]), '重量', $model->totWeight(), '分单数', $model->totShipments()));
			$xls->addRow($i++, array('分单信息', '', '', '', '', '', '', '', '', '税单信息'));		
			$xls->mergeCells('A2:I2');
			$xls->mergeCells('J2:N2');

			$xls->addRow($i++, array('序号','分单号','件数','重量','姓名','证件号','地址','电话','国内快递','商品编码','物品名称','单价','数量','商品重量','溢装原主单号'));
			$pc = 1;
			$hs_rates = [];
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$p->altGoods();
				$tq = array_sum($p->eitems['q']);
				foreach($p->eitems['g'] as $gi => $g){
					$tv = $p->eitems['v'][$gi];
					$qty = floatval($p->eitems['q'][$gi]);
					$hs = $p->eitems['hs'][$gi];
					if(empty($qty)) $qty = -1;
					if(!empty($p->eitems['pid'][$gi])){
						$ep = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						if(!empty($ep->mdata['price_CNTSN'])){
							$tv = $ep->mdata['price_CNTSN'] * $qty;
						}
						if(!empty($ep->mdata['name_CNTSN'])){
							$g = $ep->mdata['name_CNTSN'];
						}
						if(!empty($ep->mdata['hs_CNTSN'])){
							$hs = $ep->mdata['hs_CNTSN'];
						}
					}
					if(empty($hs_rates[$hs])){
						$hs_rates[$hs] = HS::getUpr($hs,'rate') * 100;
					}
					$p->altCnee();
					$cnee = $p->cnee->name;
					$idno = empty($p->cnee->cnid)? '' : $p->cnee->cnid->no;
					$u = $p->eitems['type'] == 'M'? '袋' : '罐';
					$row = array('="'.$hs.'"', $g, round($tv / $qty *100)/100, $qty, $p->eitems['w'][$gi]);

					if($gi == 0){
						//, $p->cnor->name, (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"'))
						$row = array_merge(array($pc, '="'.$p->ref.'"', 1, $p->shipWeight(), $cnee, '="'.strtoupper($idno).'"', $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"', 'YTO'), $row, ['']);
						//, ($cnee != $p->cnee->name? $p->cnee->name : '')
					}else{
						$row = array_merge(['', '="'.$p->ref.'"', '', '', '', '', '', '', ''], $row);
					}
					
					$xls->addRow($i++, $row);
				}
				$pc++;
			}
			break;
			case 'CNKMG-old'://KM old
			$of .= '-KM-old.xlsx';
			$xls->addRow($i++, array('序号','客户代码','业务类型','原始单号','总单号码','派送单号','袋号','收件人名','证件号码','收件省份','收件城市','收件人地址','收件人邮编','收件人电话','快件毛重','快件净重','总申报价值','内件品名','内件描述(品名/品牌/规格/数量)','申报价值','税号','内件品名','内件描述(品名/品牌/规格/数量)','申报价值','税号','内件品名','内件描述(品名/品牌/规格/数量)','申报价值','税号','内件品名','内件描述(品名/品牌/规格/数量)','申报价值','税号','内件品名','内件描述(品名/品牌/规格/数量)','申报价值','税号','内件品名','内件描述(品名/品牌/规格/数量)','申报价值','税号'));

			$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$p->altGoods();
				$gv = [];
				$zs = sizeof($p->eitems['g']);
				$tv = 0;
				foreach($p->eitems['g'] as $j => $g){
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$j]);
					if(empty($pd)){
						$hs = $p->eitems['hs'][$j];
						$v = 5 * $p->eitems['t'][$j];
					}else{
						$hs = empty($pd->mdata['hs_CNKMG'])? $pd->hs : $pd->mdata['hs_CNKMG'];
						$v = empty($pd->mdata['price_CNKMG'])? $pd->tax * 5 : $pd->mdata['price_CNKMG'];
					}
					$hs = empty($hs)? 'na' : $hs;
					if(isset($gv[$hs])){
						$gv[$hs][0] .= ', '.$g.'*'.$p->eitems['q'][$j];
					}else{
						$gv[$hs][0] = $g.'*'.$p->eitems['q'][$j];
					}

					if(!isset($gv[$hs][1])) $gv[$hs][1] = 0;
					$gv[$hs][1] += round($v * $p->eitems['q'][$j]);
					$tv += round($v * $p->eitems['q'][$j]);
				}

				$tw = 0;
				if(!empty($p->eitems['w'])){
					foreach($p->eitems['w'] as $j => $w){
						$tw += $w;
					}
				}
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$r = array($pc, 'KMG-KJ-NZ-001', '昆明-EMS经济', '="'.$p->hbn.'"', '', '="'.$p->ref.'"', '', $p->cnee->name, '="'.$cnid.'"', $p->cnee->state, $p->cnee->city, $p->cnee->getCnFullAddress(), '="'.$p->cnee->postcode.'"', '="'.$p->cnee->tel.'"', $p->shipWeight(), $tw, $tv);
				foreach($gv as $hs=>$gs){
					$gn = explode(', ', $gs[0]);
					$r = array_merge($r, [preg_replace('/[\w\s\-\'\*]+/', '', $gn[0]), $gs[0], $gs[1], '="'.$hs.'"']);
				}
				$xls->addRow($i++, $r);
				$pc++;
			}
			break;
			case 'CNKMG'://KM 2016
			$of .= '-KM.xlsx';
			$xls->addRow($i++, array('序号', '客户代码', '业务类型', '总单号码', '派送单号', '袋号', '收件人名', '证件号码', '收件省份', '收件城市', '收件地址', '收件人邮编', '收件人电话', '快件毛重（KG）', '快件净重', '总申报价值(CNY)', 
				'内件品名', '内件描述(品名/品牌/规格/数量)', '单价', '内件毛重', '内件数量', '申报价值', '税号',
				'内件品名', '内件描述(品名/品牌/规格/数量)', '单价', '内件毛重', '内件数量', '申报价值', '税号',
				'内件品名', '内件描述(品名/品牌/规格/数量)', '单价', '内件毛重', '内件数量', '申报价值', '税号',
				'内件品名', '内件描述(品名/品牌/规格/数量)', '单价', '内件毛重', '内件数量', '申报价值', '税号',
				'内件品名', '内件描述(品名/品牌/规格/数量)', '单价', '内件毛重', '内件数量', '申报价值', '税号',
				'内件品名', '内件描述(品名/品牌/规格/数量)', '单价', '内件毛重', '内件数量', '申报价值', '税号', '原始单号'));
			$pc = 1;

			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$p->altGoods();
				$gv = [];
				$zs = sizeof($p->eitems['g']);
				$tv = 0;
				$tw = 0;
				$tq = 0;
				if(!empty($p->mdata['AltCnee'])){
					$alcn = Addr::model()->findByPk($p->mdata['AltCnee']);
					$p->cnee->name = $alcn->name;
					$p->cnee->cnid_id = $alcn->cnid_id;
				}
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}

				foreach($p->eitems['g'] as $j => $g){
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$j]);
					$q = floatval($p->eitems['q'][$j]);
					$q = empty($q)? 1 : $q;
					if($p->eitems['type'][$j] == 'B'){
						$g = '奶粉'.(empty($pd)? $p->eitems['m'][$j] : $pd->model).'900g';
					}elseif($p->eitems['type'][$j] == 'M'){
						$g = '成人奶粉'.($p->eitems['w'][$j]/$q).'kg';
					}elseif($p->eitems['type'][$j] == 'O'){
						$g = str_ireplace($p->eitems['m'][$j], '', $g);
						$g = str_ireplace($p->eitems['b'][$j], '', $g);
						$g = preg_replace('/(\d+)(g|片|ml|粒|克)$/i', '', $g);
					}
					if(empty($pd)){
						$hs = $p->eitems['hs'][$j];
						$v = 5 * floatval($p->eitems['t'][$j]);
					}else{
						$hs = empty($pd->mdata['hs_CNKMG'])? $pd->hs : $pd->mdata['hs_CNKMG'];
						$v = empty($pd->mdata['price_CNKMG'])? $pd->tax * 5 : floatval($pd->mdata['price_CNKMG']);
					}
					$hs = empty($hs)? 'na' : $hs;

					if(isset($gv[$hs])){
						$gv[$hs][0] .= ', '.$g.'*'.floatval($p->eitems['q'][$j]);
					}else{
						$gv[$hs][0] = $g.'*'.floatval($p->eitems['q'][$j]);
					}

					if(!isset($gv[$hs][1])) $gv[$hs][1] = 0;
					$gv[$hs][1] += round($v * floatval($p->eitems['q'][$j]));

					if(!isset($gv[$hs][2])) $gv[$hs][2] = 0;
					$gv[$hs][2] += floatval($p->eitems['w'][$j]);

					if(!isset($gv[$hs][3])) $gv[$hs][3] = 0;
					$gv[$hs][3] += $q;

					$tv += round($v * floatval($p->eitems['q'][$j]));
					$tw += floatval($p->eitems['w'][$j]);
					$tq += $q;
				}

				$gwt = $p->shipWeight();
				if($tw > 0){
					foreach($gv as $hs=>$gs){
						$gs[$hs][2] = round($gs[$hs][2] / $tw * $gwt * 10) / 10;
					}
				}
				$r = array($pc, 'KMG-KJ-AU-001', '昆明-快递包裹', $model->awb, '="'.$p->ref.'"', '', $p->cnee->name, '="'.$cnid.'"', $p->cnee->state, $p->cnee->city, $p->cnee->getCnFullAddress(), '="'.$p->cnee->postcode.'"', '="'.$p->cnee->tel.'"', $gwt, $tw, $tv);
				$c = 0;

				foreach($gv as $hs=>$gs){
					$r = array_merge($r, [preg_replace('/[\w\s\-\'\*]+/', '', $gs[0]), $gs[0], round($gs[1] / $gs[3]*100)/100, round($gwt / $tq * $gs[3] * 100)/ 100, $gs[3], $gs[1], '="'.$hs.'"']);
					$c++;
					if($c > 4) break;
				}
				while($c++ < 6){
					$r = array_merge($r, ['','','','','','','']);
				}
				$r[] = '="'.$p->hbn.'"';
				$xls->addRow($i++, $r);
				$pc++;
			}
			break;
			case 'KMEMS'://KM-EMS
			$of .= '-EMS.xls';
			$xls->format = 'Excel5';
			$xls->addRow($i++, ['服务类型', '原始单号', '配送单号', '寄件人名', '寄件电话', '寄件地址', '收件人名', '收件电话', '收件地址', '收件邮编', '快件重量', '内件品名']); //,'申报总价','长','宽','高'
			$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$dim = $p->genWHD();
				$gd = '保健品';
				switch($p->goodsType()){
					case 'M':
					case 'B':
					$gd = '奶粉';
					break;
					case 'X':
					$gd = '保健品';
					break;
				}

				$xls->addRow($i++, array($p->cnee->state == '云南省'? '标准' : '经济', '="'.$p->hbn.'"', '="'.$p->ref.'"', substr($p->cnor->name,0,20), (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"'), 'ST PETERS NSW 2044', $p->cnee->name, '="'.$p->cnee->tel.'"', $p->cnee->getCnFullAddress(), '="'.$p->cnee->postcode.'"', $p->shipWeight(), $gd));
				$pc++;
			}
			break;
			case 'CNXMN'://XM
			case 'CNXM2'://XM2
			$of .= '-XM.xls';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('申报清单');
			$xls->addRow($i++, ['包裹号', '订单批次号', '订单号', '运单号', '贸易国别', '发件方', '发件方电话', '发件方地址', '始发地', '收件方', '收件方身份证号码', '收件方电话', '购买人姓名', '购买人电话', '省', '市', '收件方地址', '品牌', '中文品名', '规格', '商品条形码', '数量', '计量单位', '净重KG', '毛重KG']);

			$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);

				$tw = 0;
				if(!empty($p->eitems['w'])){
					foreach($p->eitems['w'] as $j => $w){
						$tw += floatval($w);
					}
				}
				if(!empty($p->mdata['AltCnee'])){
					$alcn = Addr::model()->findByPk($p->mdata['AltCnee']);
					$p->cnee->name = $alcn->name;
					$p->cnee->cnid_id = $alcn->cnid_id;
				}
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$noid = '';
				$bld = '';
				if(empty($p->cnee->cnid_id)){
					if(empty($p->cnee->cnid_no)){
						$cnid = '';
						$noid = 'No ID';
					}else{
						$cnid = $p->cnee->cnid_no;
						$noid = 'No Photo';
					}
				}else{
					$cnid = $p->cnee->cnid->no;
					$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
				}

				foreach($p->eitems['g'] as $gi => $g){
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$b = '';
					$sku = '';
					if($pd){
						$g = $pd->mdata['name_CNXMN'];
						$sku = $pd->mdata['id_CNXMN'];
						$b = $pd->brand;
					}
					if($model->poc == 'CNXMN' || empty($g)){
						$cltent = $p->mdata['client_entry'];
						if(!empty($cltent['items'])){
							$g = $cltent['items']['g'][$gi];
						}
					}
					$r = [$pc, $model->awb, 'P'.sprintf('%010d', $p->id), '="'.$p->ref.'"', '澳大利亚', 'ADDEX', '0422 109 106', '6C The Crescent, Kingsgrove, NSW 2208', '悉尼', $p->cnee->name, '="'.$cnid.'"', '="'.$p->cnee->tel.'"', $p->cnee->name, $p->cnee->tel, str_replace('市', '', $p->cnee->state), str_replace('地区', '市', $p->cnee->city), $p->cnee->getCnFullAddress(), $b, $g, $p->eitems['m'][$gi], '="'.$sku.'"', $p->eitems['q'][$gi], $p->eitems['u'][$gi], $p->eitems['w'][$gi], $p->shipWeight(), $p->hbn, $bld];
					$xls->addRow($i++, $r);
				}
				
				$pc++;
			}
			$xls->output($of, 'Excel5');
			break;
			case 'CNSJA':
			$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.(empty($model->awb)? '' : '_'.$model->awb).'_x2.zip';
			$zip = new ZipArchive;
			$zip->open($zf, ZipArchive::CREATE);
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('申报单');
			$xls->addRow($i++, ['序号', '单号', '发件人地址', '发件人', '发件人电话', '收件人姓名', '收件人身份证号码', '始发地国家', '收件人地址', '收件人邮编', '联系电话', '货物名称', '税号', '重量（kg）', '规格']);
			$pc = 1;
			$srows = [];
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$gs = [];
				$hss = [];
				$w = $p->shipWeight();

				if(empty($p->cnee->cnid_id)){
					$cnid = $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}

				foreach($p->eitems['g'] as $gi => $g){
					if(empty($p->eitems['q'][$gi])) continue;
					$pd = empty($p->eitems['pid'][$gi])? false : ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = floatval($p->eitems['q'][$gi]) == 0 ? 1 : floatval(trim($p->eitems['q'][$gi]));
					if(empty($pd)){
						$pd = ExProdb::model()->find('name = :n', [':n' => $g]);
					}
					if(empty($pd)){
						$v = $p->eitems['v'][$gi];
						$hs = $p->eitems['hs'][$gi];
					}else{
						$v = empty($pd->mdata['price_CNJMN'])? $pd->price : $pd->mdata['price_CNJMN'];
						$hs = empty($pd->mdata['hs_CNJMN'])? $pd->hs : $pd->mdata['hs_CNJMN'];
						$p->eitems['g'][$gi] = empty($pd->mdata['name_CNJMN'])? $pd->name_zh : $pd->mdata['name_CNJMN'];
						$p->eitems['m'][$gi] = $pd->model;
						$p->eitems['b'][$gi] = $pd->brand;
						$p->eitems['w'][$gi] = $pd->weight * $qty;
					}
					$gs[] = $p->eitems['g'][$gi].','.$qty.'*'.$v;
					$hss[] = $hs;
				}
				$xls->addRow($i++, array($pc, '="'.$p->ref.'"', (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), $p->cnor->name, (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"'), $p->cnee->name, '="'.$cnid.'"', '澳大利亚', $p->cnee->getCnFullAddress(), '="'.$p->cnee->zip.'"', '="'.$p->cnee->tel.'"', implode(';', $gs), implode(';', $hss), $w, implode('|', $p->eitems['m']), $p->hbn));
				$srows[] = [$pc, $p->ref, $w];
				$pc++;
			}
			$zip->addFromString($model->no.'_申报单.xlsx', $xls->output(false, 'Excel2007', false));

			//list file
			$xls = new oExcel;
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('装箱单');
			$i = 1;
			$xls->addRow($i++, ['总单号',$model->awb, '航班号', $model->flight]);
			$xls->addRow($i++, ['序号', '分运单号', '重量']);
			foreach($srows as $r){
				$xls->addRow($i++, $r);
			}
			$zip->addFromString($model->no.'_装箱单.xlsx', $xls->output(false, 'Excel2007', false));
			$zip->close();

			header("Cache-Control: maxage=1");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header('Content-Disposition: attachment; filename="'.basename($zf).'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($zf));
			readfile($zf);
			unlink($zf);
			Yii::app()->end();
			break;
			case 'A2U': //A2U
			$of .= '-A2U.xlsx';
			$xls->addRow($i++, array('序号','内件号','麻袋号','中华单号','寄件人','寄件人地址','寄件人电话','面单收件人','面单收件人地址','面单收件人电话','面单收件人','面单收件人地址','面单收件人电话','省份','物品描述','申报总价值(澳币）','申报总价值(人民币=澳币4.6）','数量','重量(kg)','运输途径','税号','税率','生产厂商','国别','品牌','段数','克数','身份证号'));
			$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$gs = [];
				$tv = 0;
				$tq = 0;
				$w = $p->shipWeight();
				foreach($p->eitems['g'] as $gi=>$g){
					$q = floatval($p->eitems['q'][$gi]);
					$q = empty($q)? 1 : $q;
					$tq += $q;
					if($p->eitems['type'][$gi] == 'B'){
						$g = str_ireplace($p->eitems['b'][$gi], '', $g);
						$g = str_replace('婴儿', '', $g);
						$g = str_replace('白金版', '', $g);
						$g = str_replace('金装', '', $g);
						$tv += 60 * $q;
					}elseif($p->eitems['type'][$gi] == 'M'){
						$g = '成人奶粉';
						$tv += 30 * $q;
					}else{
						$g = str_ireplace($p->eitems['b'][$gi], '', $g);
						$g = str_ireplace($p->eitems['m'][$gi], '', $g);
						$g = preg_replace('/(\(|（.+\)|）)/i', '', $g);
						$tv += 20 * $q;
					}
					$gs[] = $g.' '.$p->eitems['q'][$gi];
				}

				if($tq == 3 && $w == 3.5) $w = 3.6;

				$xls->addRow($i++, array($pc, '="'.$p->hbn.'"', '', '', '', '', '', $p->cnee->name, $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"', $p->cnee->name, $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"', $p->cnee->state, implode(' ', $gs), round($tv / 4.6), $tv, $tq, $w, '', '', '', '', '', implode(', ', $p->eitems['b']), '', '', '="'.$p->cnee->cnid->no.'"'));
				$pc++;
			}
			break;
			case 'STO': //STO
			$of .= '-STO.xlsx';
			$xls->addRow($i++, array('序号','代理点','客户单号','转单号','品名','件数','数量','重量','价值','保险','币制','商品编号','收货人公司/人名','收货人电话','省','市','区','收货人地址','发货人公司/人名','发货人地址','发货人电话'));
			$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$gs = [];
				foreach($p->eitems['g'] as $gi=>$g){
					$b = '';
					if(!empty($p->eitems['pid'][$gi])){
						$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						$b = ' ('.$pd->brand.')';
					}
					$gs[] = $g.' '.$p->eitems['q'][$gi].$b;
				}

				$xls->addRow($i++, array($pc, 'PCA', '="'.$p->hbn.'"', '="'.$p->ref.'"', implode(', ', $gs), 1, array_sum($p->eitems['q']), $p->shipWeight(), $p->value, '', 142, '', $p->cnee->name, '="'.$p->cnee->tel.'"', $p->cnee->state, $p->cnee->city, $p->cnee->suburb, $p->cnee->address, substr($p->cnor->name,0,20), 'ST PETERS NSW 2044', (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"')));
				$pc++;
			}
			break;
			case 'CA2INV'://GZ Sino
			$of .= '-GZ_Sino_Invoice.xlsx';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('发票');
			$xls->setColWidth(array(25,20,35,15,20,20));
			$xls->addRow($i++, array('COMMERCIAL INVOICE'));
			$xls->mergeCells('A1:F1');
			$xls->addRow($i++, array('******************************************************'));
			$xls->mergeCells('A2:F2');
			$xls->centerAlignment('A1:A2');

			$xls->addRow($i++, array('Payment By:(付款方式)', 'TT', '', 'Inv. No.:(发票号)', 'UPG'.date('Ymd')));
			$xls->addRow($i++, array('Messrs:(买方)', 'GUANGDONG CROSS-BORDER D.COMMERCIAL TRADING CO.,LTD', '', 'Date:(日期)', date('Y-m-d')));
			$xls->addRow($i++, array('Address:(地址)', "ROOM117,1ST FLOOR GENERAL BUILDING, NORTH DISTRICT OF LOGISTICS DISTRICT, GUANGZHOU BAIYUN INT'AIRPORT"));
			$xls->addRow($i++, array('From:(从)', "U PIN GROUP PTY LTD, Australia", '', 'To:(到)', 'GUANGZHOU'));

			$xls->addRow($i++, array());


			$xls->addRow($i++, array('Marks & Nos.唛头','Packages箱数','Description品名','Qty数量','Unit Price单价','Amount总额'));
			$xls->addRow($i++, array('','Cartons','','PCS','CIF','GUANGZHOU'));

			$pc = 1;
			$gtq = 0;
			$gtv = 0;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				foreach($p->eitems['g'] as $gi => $g){
					$tv = $p->eitems['v'][$gi];
					$qty = floatval($p->eitems['q'][$gi]);
					if(empty($qty)) $qty = -1;
					if(!empty($p->eitems['pid'][$gi])){
						$ep = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						if(!empty($ep->mdata['price_CNCA2'])){
							$tv = $ep->mdata['price_CNCA2'] * $qty;
						}
						if(!empty($ep->mdata['name_CNCA2'])){
							$g = $ep->mdata['name_CNCA2'];
						}
					}
					$tv = round($tv / 5 * 100) / 100;
					$gtv += $tv;
					$gtq += $qty;
					$row = array('', 1, $g, $qty, round($tv / $qty *100)/100 , $tv);
					if($gi > 0){
						$row[1] = '';
					}
					
					$xls->addRow($i++, $row);
				}
				$pc++;
			}
			$xls->addRow($i++, array('Total', $pc,'', $gtq, '', $gtv));
			//second tab
			$xls->createSheet('装箱单');
			$xls->goSheet(1);
			$i = 1;
			$xls->setColWidth(array(25,20,35,15,20,20));
			$xls->addRow($i++, array('PACKING LIST'));
			$xls->mergeCells('A1:F1');
			$xls->addRow($i++, array('******************************************************'));
			$xls->mergeCells('A2:F2');
			$xls->centerAlignment('A1:A2');

			$xls->addRow($i++, array('Payment By:(付款方式)', 'TT', '', 'Inv. No.:(发票号)', 'UPG'.date('Ymd')));
			$xls->addRow($i++, array('Messrs:(买方)', 'GUANGDONG CROSS-BORDER D.COMMERCIAL TRADING CO.,LTD', '', 'Date:(日期)', date('Y-m-d')));
			$xls->addRow($i++, array('Address:(地址)', "ROOM117,1ST FLOOR GENERAL BUILDING, NORTH DISTRICT OF LOGISTICS DISTRICT, GUANGZHOU BAIYUN INT'AIRPORT"));
			$xls->addRow($i++, array('From:(从)', "U PIN GROUP PTY LTD, Australia", '', 'To:(到)', 'GUANGZHOU'));
			$xls->addRow($i++, array('Marks & Nos.唛头','Packages箱数','Description品名',"Q'ty数量",'N.W.净重','G.W.毛重'));

			$pc = 1;
			$gtq = 0;
			$gtv = 0;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				foreach($p->eitems['g'] as $gi => $g){
					$qty = floatval($p->eitems['q'][$gi]);
					if(empty($qty)) $qty = -1;
					if(!empty($p->eitems['pid'][$gi])){
						$ep = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						if(!empty($ep->mdata['name_CNCA2'])){
							$g = $ep->mdata['name_CNCA2'];
						}
					}
					$gtv += $tv;
					$gtq += $qty;
					$row = array('', 1, $g, $qty, $p->eitems['w'][$gi], 1.25 * $qty);
					if($gi > 0){
						$row[1] = '';
					}
					
					$xls->addRow($i++, $row);
				}
				$pc++;	
			}
			break;
			case 'CNTAO':
			case 'CNTA2'://IE QD
			case 'CNHFI'://IE QD
			$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.(empty($model->awb)? '' : '_'.$model->awb).'_x3.zip';
			$zip = new ZipArchive;
			$zip->open($zf, ZipArchive::CREATE);
			$rus = ['rando441','tutoo12','aotm100','felix2016','qamy7575','fluke0000','13xinqin6','cool22','aol2016','maple05'];

			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('订单');
			$ttls = array('是否收件人缴税','进出口类型','贸易方式','电商平台代码','订单编号','买方名称','购买人证件类型','购买人证件号','买方电话','收货人名称','收货人电话','收货人地址','订购人注册号','订单备注','商品序号','SKU(电商平台具体商品编号)','商品数量','运单号');
			$xls->addRow($i++, $ttls);
			$rows = [];
			$prows = [];
			$srows = [];
			//$p2700 = [11,404,478,516,562,615,624,690,699,703,817,826,866,867,911,912,982,1006,1012,1023,1046,1057,1064,1083,1105,1171,1172,1173,1179,1180,1233,1234,1248,1281,1285,1307,1319,1324,1356];
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$p->altGoods();
				$weight = empty($p->mdata['dcwt'])? $p->shipWeight() : $p->mdata['dcwt'];
				$dc = $p->rating($model->poc, $model->exrate);
				$tq = array_sum($p->eitems['q']);
				$ttv = 0;
				
				if(!empty($p->mdata['AltCnee'])){
					$alcn = Addr::model()->findByPk($p->mdata['AltCnee']);
					$p->cnee->name = $alcn->name;
					$p->cnee->cnid_id = $alcn->cnid_id;
				}
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}

				$ru = $rus[intval(preg_replace('/[xX]+/', '', $cnid)) % sizeof($rus)];
				$ddno = '="DDDA'.date('ymd', strtotime($model->etd.' -2 DAY')).'01'.sprintf('%07d', $p->id).'"';

				foreach($p->eitems['g'] as $gi => $g){
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$hs = empty($pd->mdata['hs_CNTAO'])? $pd->hs : $pd->mdata['hs_CNTAO'];

					if(!empty($pd)) $p->eitems['g'][$gi]= $pd->name_zh;
					
					/*if(!HsPoc::isActive($hs, 'CNTAO')){
						$tv -= $v * $q;
						$pd = ExProdb::model()->findByPk($p2700[rand(0,sizeof($p2700)-1)]);
						$g = $pd->name_zh;
						$hs = '27000000';
					}*/

					if(empty($p->eitems['w'][$gi])){
						$qty = $p->eitems['q'][$gi];
						if(empty($pd)){
							switch($p->eitems['type'][$gi]){
								case 'B':
									$p->eitems['w'][$gi] = $qty * 0.9;
								break;
								case 'M':
									$p->eitems['w'][$gi] = $qty;
								break;
							}
						}else{
							$p->eitems['w'][$gi] = $pd->weight * $qty;
						}

						if(empty($p->eitems['w'][$gi])) die($p->hbn.' '.$g.' weight error');
					}

					$u = HS::getUpr($hs, 'uc');
					$hs = empty($pd->mdata['hs_CNTAO'])? $pd->hs : $pd->mdata['hs_CNTAO'];

					$v = $u == '035'? round($pd->mdata['price_CNTAO'] / $p->eitems['w'][$gi] * $p->eitems['q'][$gi] * 100) / 100 : round($pd->mdata['price_CNTAO'] * 100) / 100;
					//$v = round($pd->mdata['price_CNTAO'] * 100) / 100;
					$q = $u == '035'? round($p->eitems['w'][$gi] * 100)/100 : $p->eitems['q'][$gi];
					//$q = $p->eitems['q'][$gi];
					$tv = $v * $q;
					if($ttv + $tv > 1000) continue;
					$sku = empty($pd->mdata['id_CNTAO'])? $g : $pd->mdata['id_CNTAO'];
					$rows[] = array(0, 'I', 0, '3702980293', $ddno, $p->cnee->name, 1, '="'.$cnid.'"', $p->cnee->tel, $p->cnee->name, $p->cnee->tel, $p->cnee->getCnFullAddress(), $ru, '', $gi+1, '="'.$sku.'"', $q, '="'.$p->ref.'"');

					$ttv += $tv;
				}
				//shipping
				$srows[] = array('="0532"', '4220', '', 5, $p->getAwb(), $p->getFlight(), $p->getFlight(), $model->eta, '', '', '', '', '', 2, $p->GoodsNames(), '="'.$p->ref.'"', 0, 1, $weight, $p->cnee->name, $p->cnee->getCnFullAddress(), $p->cnee->tel, '', '', '', '', $ddno);

				//payment
				//$h1 = sprintf('%010d', substr(preg_replace('/[^\d]+/', '', md5($p->hbn.$gi)), 0, 8));
				$prows[] = array($p->created, $ddno, round($tv*100)/100, '', $p->cnee->name, '01', '="'.$cnid.'"');
				//if($ttv >= 1000) echo $p->hbn.' > 1000<br />';
			}

			foreach($rows as $r){
				$xls->addRow($i++, $r);
			}

			$zip->addFromString(iconv('UTF-8', 'GB18030', $model->no.'_订单.xls'), $xls->output(false, 'Excel5', false));

			
			$ppg = 299;
			for($j = 0; $j < ceil(sizeof($rows) / $ppg); $j++){
				$xls = new oExcel;
				ini_set('precision', 12);
				$i = 1;
				$xls->addRow($i++, $ttls);
				for($k=$j*$ppg; $k<$j*$ppg+$ppg; $k++){
					if(empty($rows[$k])) break;
					$xls->addRow($i++, $rows[$k]);
				}
				$zip->addFromString(iconv('UTF-8', 'GB18030', $model->no.'_订单-'.($j+1).'.xls'), $xls->output(false, 'Excel5', false));
			}

			//second tab
			$xls = new oExcel;
			ini_set('precision', 12);
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('运单');
			//$xls->createSheet('运单');
			//$xls->goSheet(1);
			$i = 1;
			$ttls = array('申报口岸','进境口岸','指定其他监管场所','运输方式','提运单号','运输工具名称','航次','进境日期','账册编号','集装箱号','集装箱型号','海运方式备注','物流公司集装袋编号','包装种类代码','主要货物信息','运单号','保价费','总件数','重量','收货人名称','收货人地址','收货人电话','收货人国家','发货人名称','发货人地址','运单备注','订单编号','备注');
			$xls->addRow($i++, $ttls);

			foreach($srows as $r){
				$xls->addRow($i++, $r);
			}
			$zip->addFromString(iconv('UTF-8', 'GB18030', $model->no.'_运单.xls'), $xls->output(false, 'Excel5', false));
			
			$ppg = 299;
			$jd = 0;
			for($j = 0; $j < ceil(sizeof($srows) / $ppg); $j++){
				$xls = new oExcel;
				ini_set('precision', 12);
				$i = 1;
				$xls->addRow($i++, $ttls);
				for($k=$j*$ppg+$jd; $k<$j*$ppg+$ppg+$jd; $k++){
					if(empty($srows[$k])) break;
					$xls->addRow($i++, $srows[$k]);
				}
				/*$kd = 1;
				while($kd < 10){
					if(empty($srows[$k+$kd])) break;
					if($srows[$k+$kd][13] != $srows[$k][13]) break;
					$xls->addRow($i++, array_slice($srows[$k+$kd], 0, -1));
					$kd++;
				}
				if($kd > 1) $jd += $kd;
				*/
				$zip->addFromString(iconv('UTF-8', 'GB18030', $model->no.'_运单-'.($j+1).'.xls'), $xls->output(false, 'Excel5', false));
			}

			//third tab
			$xls = new oExcel;
			ini_set('precision', 12);
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('支付信息');
			//$xls->createSheet('支付信息');
			//$xls->goSheet(2);
			$i = 1;
			$ttls =  array('支付时间','订单编号','支付金额','付款人账户','付款人姓名','付款人证件类型','付款人证件号','备注');
			$xls->addRow($i++, $ttls);
			foreach($prows as $r){
				$xls->addRow($i++, $r);
			}
			$zip->addFromString(iconv('UTF-8', 'GB18030', $model->no.'_支付.xls'), $xls->output(false, 'Excel5', false));

			$ppg = 299;
			for($j = 0; $j < ceil(sizeof($prows) / $ppg); $j++){
				$xls = new oExcel;
				ini_set('precision', 12);
				$i = 1;
				$xls->addRow($i++, $ttls);
				for($k=$j*$ppg; $k<$j*$ppg+$ppg; $k++){
					if(empty($prows[$k])) break;
					$xls->addRow($i++, $prows[$k]);
				}
				$zip->addFromString(iconv('UTF-8', 'GB18030', $model->no.'_支付-'.($j+1).'.xls'), $xls->output(false, 'Excel5', false));
			}

			$zip->close();

			header("Cache-Control: maxage=1");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header('Content-Disposition: attachment; filename="'.basename($zf).'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($zf));
			readfile($zf);
			unlink($zf);
			Yii::app()->end();
			break;
			case 'CNCSX'://JX CS
			case 'CNCS3'://JX HN
			$of .= '-CSX.xlsx';
			//second tab
			$xls->createSheet('OrderItems');
			$xls->goSheet(1);
			$i = 1;
			$xls->addRow($i++, array('关联单号','货号','物品名称','物品英文名称','物品简称','规格 ','品牌','物品数量','单位','计量单位','物品单价','币别','物品总净重(KG)','行邮税号'));
			$srows = [];
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$p->altGoods();
				if(!empty($p->mdata['AltCnee'])){
					$alcn = Addr::model()->findByPk($p->mdata['AltCnee']);
					$p->cnee->name = $alcn->name;
					$p->cnee->cnid_id = $alcn->cnid_id;
				}
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$tw = 0;
				$tt = 0;
				$fg = '';
				foreach($p->eitems['g'] as $gi => $g){
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = floatval($p->eitems['q'][$gi]);
					$qty = empty($qty)? 1 : $qty;
					if(empty($p->eitems['b'][$gi])) $p->eitems['b'][$gi] = $pd->model;
					if(empty($p->eitems['m'][$gi])) $p->eitems['m'][$gi] = $pd->model;
					if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = $pd->unit;
					if(empty($p->eitems['w'][$gi])) $p->eitems['w'][$gi] = $pd->weight * $qty;
					if(empty($p->eitems['hs'][$gi])) $p->eitems['hs'][$gi] = $pd->hs;
					switch($p->eitems['type'][$gi]){
						case 'B':
							$u = '罐';
							$m = (($p->eitems['q'][$gi] / $qty) * 900).'g/罐';
							if(preg_match('/((1|2|3|4|一|二|三|四)段)/', $g, $bdw)){
								$g = '婴儿奶粉'.$bdw[1];
							}
						break;
						case 'M':
							$u = preg_match('/小安素/', $g)? '罐' : '袋';
							$m = (($p->eitems['q'][$gi] / $qty) * 1000).'g/'.$u;
						break;
						case 'O':
							$u = $p->eitems['u'][$gi];
							$m = $p->eitems['m'][$gi];
						break;
					}
					$price = floatval($pd->mdata['price_CNCSX']);
					if($price >= 100) $price = 40;
					$st = $price * $qty;

					$xls->addRow($i++, array('="'.$p->hbn.'"', '="'.$p->ref.'"', $g, empty($pd)? '' : $pd->name, '', $m, $p->eitems['b'][$gi], $qty, $u, $p->eitems['u'][$gi], $price, 'RMB', $p->eitems['w'][$gi], '="'.$p->eitems['hs'][$gi].'"'));
					$tw += $p->eitems['w'][$gi];
					$tt += $st;
					if($gi == 0) $fg = $g;
				}
				$weight = $p->shipWeight();
				if($tw > $weight) $weight = $p->chargeWeight() - 0.1;
				$srows[] = ['="'.$p->hbn.'"', '', 1, '="'.$p->ref.'"', $p->cnor->name, (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"'), $p->cnee->name, '="'.$cnid.'"', '="'.$p->cnee->tel.'"', $p->cnee->city, $p->cnee->state, '="'.$p->cnee->postcode.'"', $p->cnee->getCnFullAddress(), $fg, $weight, $tt, 'AU'];
			}
			//$xls->setCurrency('G2:G'.$i, '#,##0.00');
			$xls->setCurrency('J2:L'.$i, '#,##0.00');
			$xls->setCurrency('L2:L'.$i, '#,##0.00');
			$xls->goSheet(0);
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('Order');
			$i = 1;
			$xls->addRow($i++, array('关联单号','内件小票号','托盘号','EMS单号','发件人','发件人地址','发件人电话','收件人','收件人身份证号','收件人电话','城市','省份','邮编','收件人地址','本包裹名称','本包裹总毛重量(KG)','申报价格','国家简称'));
			foreach($srows as $r){
				$xls->addRow($i++, $r);
			}

			$xls->setCurrency('P2:Q'.$i, '#,##0.00');
			break;
			/*case 'CNCS2'://JX CS
			$of .= '-CS2.xlsx';
			$xls->addRow($i++, ['序号', '分运单号码', '品名', '牌子规格型号', '行邮税号', '件数', '重量(KG)', '数量', '单位', '币种', '价  值（RMB）', '发货人', '发货人地址', '发货人电话', '国别地区', '收货人', '收货人证件号码', '收货人地址', '收货人电话', '法定数量', '法定单位', '净重', '总运单号', '分运单号', '大客户号']);
			$rows = [];
			$id = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$p->altGoods();

				foreach($p->eitems['g'] as $gi => $g){
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = floatval($p->eitems['q'][$gi]);
					$qty = empty($qty)? 1 : $qty;
					if(empty($p->eitems['b'][$gi])) $p->eitems['b'][$gi] = $pd->model;
					if(empty($p->eitems['m'][$gi])) $p->eitems['m'][$gi] = $pd->model;
					if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = $pd->unit;
					if(empty($p->eitems['w'][$gi])) $p->eitems['w'][$gi] = $pd->weight * $qty;
					if(empty($p->eitems['hs'][$gi])) $p->eitems['hs'][$gi] = $pd->hs;
					$uq = $qty;
					switch($p->eitems['type'][$gi]){
						case 'B':
							$u = '罐';
							$m = (($p->eitems['q'][$gi] / $qty) * 900).'g/罐';
							if(preg_match('/([1|2|3|4|一|二|三|四]+段)/', $g, $bdw)){
								$g = '婴儿奶粉'.$bdw[1];
							}
							$uq = $p->eitems['w'][$gi];
							$uu = '千克';
						break;
						case 'M':
							$u = preg_match('/小安素|蓝胖子|Maxigenes/', $g)? '罐' : '袋';
							$m = (($p->eitems['q'][$gi] / $qty) * 1000).'g/'.$u;
							$uq = $p->eitems['w'][$gi];
							$uu = '千克';
						break;
						case 'O':
							$u = $p->eitems['u'][$gi];
							$m = $p->eitems['m'][$gi];
							$uu = $u;
						break;
					}
					$price = $pd->mdata['price_CNCSX'];
					$st = $price * $qty;

					if(!empty($p->mdata['AltCnee'])){
						$alcn = Addr::model()->findByPk($p->mdata['AltCnee']);
						$p->cnee->name = $alcn->name;
						$p->cnee->cnid_id = $alcn->cnid_id;
					}

					if(empty($p->cnee->cnid_id)){
						$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
					}else{
						$cnid = $p->cnee->cnid->no;
					}

					$xls->addRow($i++, [$id, '="'.$p->ref.'"', $g, $p->eitems['b'][$gi], '="'.$p->eitems['hs'][$gi].'"', 1, $p->shipWeight(), $qty, $u, 'RMB', $price, $p->cnor->name, (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"'), 601, $p->cnee->name, '="'.$cnid.'"', $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"', $uq, $uu, $uq, $model->awb, $p->hbn, '']);
				}
				$id++;
			}

			break;*/
			case 'CNJNA'://KJ JN
			$of .= '-JN.xlsx';

			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('Orders');
			$ttls = array('运单号', '目的地', '发货渠道', '件数', '重量', '收件人', '收件人证照号', '收件地址', '收件人电话', '订单编号', '总单号', '支付交易编号', '支付金额', '订单关税', '物品描述', '物品数量', '物品单价', '订单声明价', '订单投保价', '申报货币', '原产地', '物品别名', 'HS编码', '物品单位', '物品净重','备注');
			$xls->addRow($i++, $ttls);
			$rows = [];
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$ddno = '="PE'.sprintf('%07d', $p->id).'"';
				$h1 = sprintf('%010d', substr(preg_replace('/[^\d]+/', '', md5($p->hbn)), 0, 8));
				$payno = date('Ymd', strtotime($p->created)).sprintf('%010d',$p->id).$h1;
				
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$vs = [];
				$hss = [];
				$qs = [];
				$gs = [];
				$ms = [];
				$us = [];
				$ws = [];
				$ttv = 0;
				foreach($p->eitems['g'] as $gi => $g){
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$hss[] = empty($pd->mdata['hs_CNJNA'])? $pd->hs2 : $pd->mdata['hs_CNJNA'];
					$v = empty($pd->mdata['price_CNJNA'])? round($pd->price * 100) / 100 : round($pd->mdata['price_CNJNA'] * 100) / 100;
					$q = floatval($p->eitems['q'][$gi]);
					$qs[] = empty($q)? 1 : $q;
					$vs[] = $v;
					$gs[] = $g;
					$us[] = empty($pd->unit)? $p->eitems['u'][$gi] : $pd->unit;
					$ws[] = sprintf('%0.2f', empty($pd->weight)? $p->eitems['w'][$gi] : $pd->weight);
					if($pd->type == 10){
						$ms[] = ($pd->weight * 1000).'G';
					}else{
						$ms[] = empty($pd->model)? $p->eitems['m'][$gi] : $pd->model;
					}
					$ttv += $v * $q;
				}
				$xls->addRow($i++, ['="'.$p->ref.'"', '', '', 1, $p->shipWeight(), $p->cnee->name, '="'.$cnid.'"', $p->cnee->getCnFullAddress(), $p->cnee->tel, $ddno, $model->awb, '="'.$payno.'"', $ttv, round($ttv * 0.119), implode(',', $gs), implode(',', $qs), implode(',', $vs), $ttv, 0, 'CNY', 'AU', implode(',', $ms), '="'.implode(',', $hss).'"', implode(',', $us), implode(',', $ws), $p->hbn]);
			}
			break;
			case 'QDEMS'://QD EMS
			$of .= '-QDEMS.xls';
			$xls->format = 'Excel5';
			$xls->addRow($i++, ['邮件号','*配货单号','客户订单号','*寄件人姓名','*寄件人联系方式','寄件人联系方式（2）','*寄件人地址','寄件人公司','寄件省','寄件市','寄件县','寄件人邮编','*收件人姓名','*收件人联系方式','收件人联系方式（2）','*收件人地址','收件人公司','到件省/直辖市','到件城市','到件县/区','收件人邮编','物品重量','物品长度','物品宽度','物品高度','打单时间','备注','业务类型','代收货款','收件人付费','应收货款/邮资','应收货款/邮资（大写）','内件性质','内件数','内件信息','留白一','留白二','付费类型','所负责任','保价金额','保险金额','其他费']);
				$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$whd = $p->genWHD();
				$xls->addRow($i++, ['', $p->hbn, $p->ref, $p->cnor->name, '="'.(empty($p->cnor->tel)? '0286669222' : $p->cnor->tel).'"', '', '6C The Crescent, Kingsgrove, NSW 2208, AUSTRALIA', '', '', '', '', '', $p->cnee->name, '="'.$p->cnee->tel.'"', '', $p->cnee->getCnFullAddress(), '', $p->cnee->state, $p->cnee->city, $p->cnee->suburb, '="'.$p->cnee->postcode.'"', $p->shipWeight(), $whd[0], $whd[1], $whd[2], '', '', strpos($p->cnee->state, '山东') === false? '快递包裹' : '标准快递' , '', '', '', '', '', '', $p->GoodsNames(), '', '', '', '', '', '', '']);
			}
			break;
			case 'CNSZX': //Shenzhen
				$of .= '-SZ.xlsx';
				$xls->addRow($i++, array('序号', '报关单号', '总运单号', '袋号', '快件单号', '发件人', '发件人地址', '发件人电话', '收件人', '收件人电话', '城市', '邮编', '收件人地址', '内件名称', '数量', '总价(RMB)', '重量(KG)', '税号', '物品名称', '品牌', '数量', '单位', '单价(RMB)', '币别', '身份证号码', '运输工具', '客户编号'));
				$pc = 1;
				function psn($p, $i){
					$g = $p->eitems['g'][$i];
					$g = str_replace('酸奶粉', '酸奶冲剂', $g);
					$g = str_ireplace($p->eitems['b'][$i], '', $g);
					$g = str_ireplace($p->eitems['m'][$i], '', $g);
					$g = preg_replace('/\s*(\d+.*)$/', '', $g);
					return trim($g);
				}
				foreach($model->shipments as $p){
					$gs = [];
					foreach($p->eitems['g'] as $gi => $g){
						$gs[] = psn($p, $gi);
					}

					$goods = implode('，', $gs);

					foreach($p->eitems['g'] as $gi => $g){
						$qty = floatval($p->eitems['q'][$gi]);
						if(empty($qty)) $qty = -1;
						$xls->addRow($i++, array($pc, '', $model->awb, 1, '="'.$p->hbn.'"', $p->cnor->name, $p->cnor->fullAddress(), '="'.$p->cnor->tel.'"', $p->cnee->name, '="'.$p->cnee->tel.'"', $p->cnee->city, '="'.$p->cnee->postcode.'"', $p->cnee->getCnFullAddress(), $goods, $p->itemsTotQty(), round($p->value), $p->shipWeight(), '="'.$p->eitems['hs'][$gi].'"', psn($p, $gi).$p->eitems['q'][$gi].$p->eitems['u'][$gi], '', $p->eitems['q'][$gi], $p->eitems['u'][$gi], round($p->eitems['v'][$gi] / $qty * 100) / 100, '502', '="'.$p->cnee->cnid->no.'"', '', ''));
					}
					$pc++;
				}
				/*$xls->setCurrency('P2:P'.$i, '#,##0.00');
				$xls->setCurrency('Q2:Q'.$i, '#,##0.00');
				$xls->setCurrency('U2:U'.$i, '#,##0.00');
				$xls->setCurrency('W2:W'.$i, '#,##0.00');*/
			break;
			case 'SZODR':
				$of = $model->no.'_SZ_Order.xlsx';
				$xls->addRow($i++, array('*寄件人姓名','*寄件人联系方式','寄件人联系方式（2）','*寄件人地址','*收件人姓名','*收件人联系方式','*收件人地址','*配货单号','收件人联系方式（2）','收件人邮编','收件人公司','到件省/直辖市','到件城市','到件县/区','物品重量	物品长度','物品宽度','物品高度','打单时间','备注','*业务类型','*内件性质','*内件信息','*留白一','留白二','寄件人邮编','应收货款/邮资','应收货款/邮资（大写）','付费类型','所负责任','保价金额','保险金额','其他费','内件数','寄件人公司','客户订单号'));
				$pc = 1;
				foreach($model->shipments as $p){
					$xls->addRow($i++, array($p->cnor->name, '="'.$p->cnor->tel.'"', '', $p->cnor->fullAddress(), $p->cnee->name, '="'.$p->cnee->tel.'"', $p->cnee->getCnFullAddress() , '="'.$p->hbn.'"', '', $p->cnee->postcode, '', $p->cnee->state, $p->cnee->city, $p->cnee->suburb, $p->shipWeight(), '', '', '', '', '经济快递', '物品', $p->GoodsNames(false, ','), '申报重量: '.$p->shipWeight().'kg 申报金额: '.(round($p->value * 100)/100).'RMB 代码：YYFA-38'));
					$pc++;
				}
			break;
			case 'CNCAN'://GZ XY
			$of .= '-GZ.xlsx';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('运单');
			$xls->addRow($i++, array('总运单号','内部单号','快件单号','发件人','发件人地址','发件人电话号码','收件人','身份证件号码','收件人电话号码','收件人地址','内件名称（包裹详情）','税号','物品名称（包裹内的单种物品）','规格型号','单种物品总净重（KG)','单种物品总毛重(KG)','单种物品单价','单种物品总数量','单位','币别','HS编码'));

			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				if(!empty($_GET['o'])) unset($p->mdata['altItems']);
				$p->altGoods();

				$p->altCnee();
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$cnee = $p->cnee->name;

				if(empty($p->mdata['AltCnee'])){
					if(empty($p->cnee->cnid_id)){
						$noidphoto = 'Y';
					}else{
						$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
					}
				}else{
					$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
				}

				$tnt = 0;
				if(is_array($p->eitems['w'])){
					foreach($p->eitems['w'] as $gi => $w){
						if(empty($w)) continue;
						$tnt += $w;
					}
				}
				$swt = $p->shipWeight(true);

				if($tnt <= 0 || $tnt > $swt) $tnt = min($swt * 0.9, $swt - 0.2);
				if($tnt <= 0) $tnt = 0.1;

				if(empty($p->eitems['g'][0])) die($p->hbn.' 缺少商品信息');
				foreach($p->eitems['g'] as $gi => $g){
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = intval($p->eitems['q'][$gi]) == 0 ? 1 : intval(trim($p->eitems['q'][$gi]));

					if(floatval($p->eitems['w'][$gi]) == 0) die($p->hbn.' item weight problem');

					if(empty($pd)){
						$v = floatval($p->eitems['v'][$gi]);
						$hs = $p->eitems['hs'][$gi];
					}else{
						$v = empty($pd->mdata['price_'.$model->poc])? $pd->price : $pd->mdata['price_'.$model->poc];
						if(!empty($p->mdata['altItems']['v'][$gi])) $v = $p->mdata['altItems']['v'][$gi];
						$hs = empty($pd->mdata['hs_'.$model->poc])? $pd->hs : $pd->mdata['hs_'.$model->poc];
						$p->eitems['g'][$gi] = empty($pd->mdata['name_'.$model->poc])? $pd->name_zh : $pd->mdata['name_'.$model->poc];
						$p->eitems['m'][$gi] = $pd->model;
						$p->eitems['b'][$gi] = $pd->brand;
						$p->eitems['w'][$gi] = $pd->weight * $qty;
					}
					$t = HsPoc::getTariff($hs, $exc, $v, floatval($p->eitems['w'][$gi]) / $qty);
					$xls->addRow($i++, array($model->awb, '="'.$p->hbn.'"', '="'.$p->ref.'"', $p->cnor->name, (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), '="'.(empty($p->cnor->tel)? '0299257100' : $p->cnor->tel).'"', $cnee, '="'.$cnid.'"', '="'.$p->cnee->tel.'"', $p->cnee->getCnFullAddress(), $p->GoodsNames(), '="'.$hs.'"', $g.'*'.$qty, $p->eitems['m'][$gi], $p->eitems['w'][$gi], round(floatval($p->eitems['w'][$gi]) / $tnt * $swt, 2), $v, $qty, $p->eitems['u'][$gi], 'RMB', '="'.$hs.'"'));
				}
			}
			break;
			case 'CNHAK'://HK XY
			$of .= '-HK_XY.xlsx';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('运单');
			$xls->addRow($i++, array('分运单号', '商品编号', '商品名称', '申报数量', '申报单位', '申报总价', '用途', '商品规格、型号', '产销国', '货物总价值', '币制', '件数', '毛重', '净重', '收件人', '收发件人证件类型', '收发件人证件号', '发件人', '贸易国别（起/抵运地）', '发件人国别', '发件人城市', '包装种类', '收件人地址', '收件人电话', '运单发件人公司', '运单发件人联系人', '运单发件人地址', '运单发件人城市', '运单发件人国家', '运单发件人电话', '运单收件人公司', '运单收件人联系人', '运单收件人地址', '运单收件人邮编', '运单收件人城市', '运单收件人国家', '运单收件人电话'));
			$ds = [];
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				if(!empty($_GET['o'])) unset($p->mdata['altItems']);
				$p->altGoods();
				$p->altCnee();
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$cnee = $p->cnee->name;

				if(empty($p->mdata['AltCnee'])){
					if(!empty($p->cnee->cnid_id)){
						$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
					}
				}else{
					$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
				}


				$tnt = 0;
				if(is_array($p->eitems['w'])){
					foreach($p->eitems['w'] as $gi => $w){
						if(empty($w)) continue;
						$tnt += $w;
					}
				}
				$swt = $p->shipWeight(true);

				if($tnt <= 0 || $tnt > $swt) $tnt = min($swt * 0.9, $swt - 0.2);
				if($tnt <= 0) $tnt = 0.1;

				$tv = 0;

				if(empty($p->eitems['g'][0])) die($p->hbn.' 缺少商品信息');
				$srs = [];
				foreach($p->eitems['g'] as $gi => $g){
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = intval($p->eitems['q'][$gi]) == 0 ? 1 : intval(trim($p->eitems['q'][$gi]));

					if(floatval($p->eitems['w'][$gi]) == 0) die($p->hbn.' item weight problem');

					if(empty($pd)){
						$v = floatval($p->eitems['v'][$gi]);
						$hs = $p->eitems['hs'][$gi];
					}else{
						$v = empty($pd->mdata['price_'.$model->poc])? $pd->price : $pd->mdata['price_'.$model->poc];
						if(!empty($p->mdata['altItems']['v'][$gi])) $v = $p->mdata['altItems']['v'][$gi];
						$hs = empty($pd->mdata['hs_'.$model->poc])? $pd->hs : $pd->mdata['hs_'.$model->poc];
						$p->eitems['g'][$gi] = empty($pd->mdata['name_'.$model->poc])? $pd->name_zh : $pd->mdata['name_'.$model->poc];
						$p->eitems['m'][$gi] = $pd->model;
						$p->eitems['b'][$gi] = $pd->brand;
						$p->eitems['w'][$gi] = $pd->weight * $qty;
					}
					$t = HsPoc::getTariff($hs, $exc, $v, floatval($p->eitems['w'][$gi]) / $qty);
					$tv += $qty * $v;
					$srs[] = $gi==0? ['="'.$p->ref.'"', '="'.$hs.'"', $g, $qty, $p->eitems['u'][$gi], $v * $qty, '', $p->eitems['m'][$gi], '601', 0, '142', '1', $swt, $tnt, $cnee, '1', '="'.$cnid.'"', $p->cnor->name, '601', '601', '悉尼', '', $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"'] : ['="'.$p->hbn.'"', '="'.$hs.'"', $g, $qty, $p->eitems['u'][$gi], $v * $qty, '', $p->eitems['m'][$gi], '601'];
				}
				$srs[0][9] = $tv;
				$ds = array_merge($ds, $srs);
			}
			foreach($ds as $r){
				$xls->addRow($i++, $r);
			}
			break;
			case 'CNHA2': //HK BC
			$of .= '-HK_BC.xlsx';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('运单');
			$xls->addRow($i++, array('订单编号', '物流企业备案号（固定）', '电商平台备案号（固定）', '企业运单编号', '物流状态', '运费', '运费币制', '运输方式', '运输工具名称', '包装种类', '保价费', '保价费币制', '分运单净重', '分运单毛重', '箱件数', '主要商品(英文品牌+中文品名)', '商品货号（自行定义同一种产品固定一个即可）', '原产国（地区）/最终目的国（地区）代码', '计量单位', '净重/数量', '商品总价', '载货清单号', '商品备案号', '商品单价 货物的单价，RMB金额（元）', '商品币制', '子订单备注', '进出口标识', '是否退运', '原运单号', '退运原因', '运输工具航次(班)号', '码头/货场代码（为物流监控备用）', '商品毛重', '"仓单申报类型N表\n示新增M修改"', '第一法定数量CD类必填，AB类不填', '第一法定计量单位CD类必填，AB类不填', '商品规格型号', '国检备案号', '产品连接'));

			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				if(!empty($_GET['o'])) unset($p->mdata['altItems']);
				$p->altGoods();
				$p->altCnee();
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$cnee = $p->cnee->name;

				if(empty($p->mdata['AltCnee'])){
					if(!empty($p->cnee->cnid_id)){
						$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
					}
				}else{
					$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
				}

				$tnt = 0;
				$tq = 0;
				if(is_array($p->eitems['w'])){
					foreach($p->eitems['w'] as $gi => $w){
						$tq += intval(trim($p->eitems['q'][$gi]));
						if(empty($w)) continue;
						$tnt += $w;
					}
				}
				$swt = $p->shipWeight(true);

				if($tnt <= 0 || $tnt > $swt) $tnt = min($swt * 0.9, $swt - 0.2);
				if($tnt <= 0) $tnt = 0.1;

				$tv = 0;

				if(empty($p->eitems['g'][0])) die($p->hbn.' 缺少商品信息');
				foreach($p->eitems['g'] as $gi => $g){
					$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = intval($p->eitems['q'][$gi]) == 0 ? 1 : intval(trim($p->eitems['q'][$gi]));

					if(floatval($p->eitems['w'][$gi]) == 0) die($p->hbn.' item weight problem');

					if(empty($pd)){
						$v = floatval($p->eitems['v'][$gi]);
						$hs = $p->eitems['hs'][$gi];
					}else{
						$v = empty($pd->mdata['price_'.$model->poc])? $pd->price : $pd->mdata['price_'.$model->poc];
						if(!empty($p->mdata['altItems']['v'][$gi])) $v = $p->mdata['altItems']['v'][$gi];
						$hs = empty($pd->mdata['hs_'.$model->poc])? $pd->hs : $pd->mdata['hs_'.$model->poc];
						$p->eitems['g'][$gi] = empty($pd->mdata['name_'.$model->poc])? $pd->name_zh : $pd->mdata['name_'.$model->poc];
						$p->eitems['m'][$gi] = $pd->model;
						$p->eitems['b'][$gi] = $pd->brand;
						$p->eitems['w'][$gi] = $pd->weight * $qty;
					}
					$u = HS::getUpr($hs, 'uc');

					$t = HsPoc::getTariff($hs, $exc, $v, floatval($p->eitems['w'][$gi]) / $qty);
					$tv += $qty * $v;
					$xls->addRow($i++, ['="'.$p->hbn.'"', '460118Z047', '31119699A3', '="'.$p->ref.'"', '0', '0', '142', '4', '', '4', '0', '142', $p->eitems['w'][$gi], round(floatval($p->eitems['w'][$gi]) / $tnt * $swt, 2), $tq, $g, 'HNSFI'.sprintf('%06d', $p->eitems['pid'][$gi]), '501', $p->eitems['u'][$gi], $qty, $v * $qty, '', '="'.empty($pd)? $hs : $pd->hs2.'"', $v, '142', '', 'I', '', '', '', '', '4258', round(floatval($p->eitems['w'][$gi]) / $tnt * $swt, 2), 'N', $qty, $u, $p->eitems['m'][$gi], '', '']);
				}
			}
			break;
			case 'CZTruck':
			$of .= '-CZTruck.xlsx';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('Order');
			$xls->addRow($i++, array('序号', '总运单号', '分运单号', '快件公司', '航班', '航次', '进口日期', '到达卸货地时间', '物品', '商品类别', '货物件数', '货物毛重'));
			$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$xls->addRow($i++, array($pc++, $model->awb, '="'.$p->ref.'"', '="PCA"', $model->flight, $model->etd, $model->etd, $model->eta, $p->GoodsNames(), '普通物品', 1, $p->shipWeight()));
			}
			break;
			case 'Timely':
			$of .= '-Timely.xlsx';
			$xls->addRow($i++, array('运单号', '转运单号', '取件日期', '信息完整日期', '发货日期', '重复件数'));
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$days = $p->getPerformanceDates();
				$xls->addRow($i++, ['="'.$p->hbn.'"', '="'.$p->ref.'"', $days[0], $days[5], $model->etd, $p->dupCount()]);
			}
			break;
			case 'CNZNG'://zhanjiang
			$of .= '-ZNG.xlsx';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('Order');
			$xls->addRow($i++, array('序号', '客户账号', '标签', '所属部门', '内单号', '转单号', '参考号', '件数', '重量', '物品描述', '备用四', '备用三', '物品数量', '物品单价', '声明价值', '收件人', '收件电话', '留用串', '收件邮编', '收件人地址', '收件城市', '收件省份', '批次', '发件人', '发件电话', '发件邮编', '发件地址', '发件国家', '发件省州', '发件城市', '备用一', '备用二', '备用五'));
			$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$v = $p->getDvalue('CNCSX');
				$tq = $p->goodsType() == 'B'? array_sum($p->eitems['w']) : array_sum($p->eitems['q']);
				$ggg = [];
				foreach($p->eitems['g'] as $gi=>$g){
					$ggg[] = $p->eitems['b'][$gi].$g.($p->eitems['type'] == 'B' ? $p->eitems['w'][$gi] : $p->eitems['q'][$gi]);
				}
				$xls->addRow($i++, array($pc++, '', '', '', '="'.$p->hbn.'"', '="'.$p->ref.'"', '', 1, $p->shipWeight(), implode("、", $ggg), implode('/', $p->eitems['b']), implode('/', $p->eitems['m']), $tq, $v, $v, $p->cnee->name, '="'.$p->cnee->tel.'"', '="'.$p->cnee->cnid->no.'"', '="'.$p->cnee->postcode.'"', $p->cnee->getCnFullAddress(), $p->cnee->city, $p->cnee->state, '', $p->cnor->name, '="'.(empty($p->cnor->tel)? '0299257100' : $p->cnor->tel).'"', '2019', (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), '澳大利亚', 'AU', 'AU', '', '', '客户代码：ZYGH'));
			}
			$xls->setCurrency('N2:O'.$i, '#,##0.00');
			break;
			case 'CNXIA': //XA
			$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.(empty($model->awb)? '' : '_'.$model->awb).'_x2.zip';
			$zip = new ZipArchive;
			$zip->open($zf, ZipArchive::CREATE);
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('申报清单');
			function psn($p, $i, $mb = true){
				$g = $p->eitems['g'][$i];
				
				if($p->eitems['type'][$i] == 'B'){
					/*$g = str_replace('白金版', '', $g);
					$g = str_replace('金装', '', $g);
					$g = preg_replace('/\s*(一|二|三|四|1|2|3|4)段$/', '', $g);
					*/
					$g = $p->eitems['b'][$i].str_replace(['一', '二', '三', '四'], ['1', '2', '3', '4'], $p->eitems['m'][$i]).'婴儿奶粉';
				}elseif($p->eitems['type'][$i] == 'M'){
					$g = ($mb? $p->eitems['b'][$i] : '').'成人奶粉';
					$g = str_ireplace('Devondale', '德运', $g);
					$g = str_ireplace('Maxigenes', '美可卓', $g);
				}elseif($p->eitems['type'][$i] == 'O'){
					$g = str_ireplace($p->eitems['m'][$i], '', $g);
					$g = str_ireplace($p->eitems['b'][$i], '', $g);
					$g = preg_replace('/(\d+)(g|片|ml|粒|克)$/i', '', $g);
					$g = $p->eitems['b'][$i].$g;
				}

				return trim($g);
			}

			$xls->addRow($i++, array('分运单号', '申报类型', '物品名称', '商品编码', '净重(KG)', '毛重(KG)', '规格/型号', '币制', '申报数量', '申报总价', '申报计量单位', '收件人国家', '收件人', '收件人地址', '收件人电话', '发件人国家', '发件人', '发件人地址', '发件人电话', '收发件人证件类型', '收发件人证件号', '包装种类', '生产国别', '贸易国别','参考单号'));
			$srows = [];
			$qrows = [];
			//$orows = [];
			$ln = 1;
			$pc = 1;
			foreach($model->shipments as $p){
				$p->altGoods();
				$tv = 0;
				$tqty = 0;
				$tduty = 0;
				$tnw = 0;
				$noidphoto = '';
				$bld = '';
				$p->altCnee();
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$cnee = $p->cnee->name;

				if(empty($p->mdata['AltCnee'])){
					if(!empty($p->cnee->cnid_id)){
						$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
					}
				}else{
					$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
				}

				$cnee = preg_replace('/\s+/', '', $cnee);

				$tnt = 0;
				if(is_array($p->eitems['w'])){
					foreach($p->eitems['w'] as $gi => $w){
						if(empty($w)) continue;
						$tnt += $w;
					}
				}
				$swt = $p->shipWeight(true);

				if($tnt <= 0 || $tnt > $swt) $tnt = min($swt * 0.9, $swt - 0.2);
				if($tnt <= 0) $tnt = 0.1;
				$ors = [];
				foreach($p->eitems['g'] as $gi => $g){
					if(empty($p->eitems['q'][$gi])) continue;
					$pd = empty($p->eitems['pid'][$gi])? false : ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = floatval($p->eitems['q'][$gi]) == 0 ? 1 : floatval(trim($p->eitems['q'][$gi]));
					if(empty($pd)){
						$pd = ExProdb::model()->find('name = :n', [':n' => $g]);
					}
					if(empty($pd)){
						$v = $p->eitems['v'][$gi];
						$hs = $p->eitems['hs'][$gi];
					}else{
						$v = empty($pd->mdata['price_CNXIA'])? $pd->price : $pd->mdata['price_CNXIA'];
						$hs = empty($pd->mdata['hs_CNXIA'])? $pd->hs : $pd->mdata['hs_CNXIA'];
						$p->eitems['g'][$gi] = empty($pd->mdata['name_CNXIA'])? $pd->name_zh : $pd->mdata['name_CNXIA'];
						$p->eitems['m'][$gi] = $pd->model;
						$p->eitems['b'][$gi] = $pd->brand;
						$p->eitems['w'][$gi] = $pd->weight * $qty;
					}
					
					if(empty($qty)) $qty = -1;
					$tr = HS::getUpr($hs, 'rate') * 100;
					$duty = round($v * $qty * $tr) / 100;
					$tduty += $duty;
					$u = HS::getUpr($hs, 'uc');
					if(empty($u)) $u = $p->eitems['u'][$gi];
					if($hs == '1019900') $u = '140';
					$g = psn($p, $gi);
					$dqty = $u == '035'? $p->eitems['w'][$gi] : $qty;
					$uv = $u == '035'? round($v * $qty / $p->eitems['w'][$gi] * 10)/10 : $v;

					$dl = '保健品';
					$wqt = $p->eitems['w'][$gi];
					if($p->eitems['type'][$gi] == 'B'){
						$dl = '婴儿奶粉';
						$p->eitems['m'][$gi] = $p->eitems['m'][$gi].' 900g';
						$uv = 95;
						if(preg_match('/羊奶/', $g)){
							$uv = 99;
						}elseif(preg_match('/可瑞康|karicare/', $g)){
							$uv = 92;
						}elseif(preg_match('/a2|贝拉米|bellamy/i', $g)){
							$uv = 98;
						}
						$v = $uv * $p->eitems['w'][$gi] / $qty;
						if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
						if($qty < 3){
							$awt = 1.2 * $qty;
						}elseif($qty == 3){
							$awt = 3.5;
						}elseif($qty == 4){
							$awt = 4.6;
						}elseif($qty == 6){
							$awt = 7;
						}
					}elseif($p->eitems['type'][$gi] == 'M'){
						$dl = '成人奶粉';
						$uv = preg_match('/a2/i', $g)? 55: 50;
						$v = $uv * $p->eitems['w'][$gi] / $qty;
						if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
						$p->eitems['m'][$gi] = '均段'.round($p->eitems['w'][$gi] / $qty * 1000).'g';
						$awt = $qty + 0.2;
					}else{
						$wqt = $qty;
					}
					
					if($p->eitems['w'][$gi] > $swt) $swt = $awt;
					if($p->eitems['w'][$gi] > $swt) $swt = $p->eitems['w'][$gi];

					//$xls->addRow($i++, ['="'.$p->ref.'"', trim(str_replace($p->eitems['b'][$gi], '', $g)).','.$p->eitems['b'][$gi], 'B', '', '1', round($p->eitems['w'][$gi] / $tnt * $swt, 2), $v*$qty, '142', $cnee, $p->cnee->getCnFullAddress(true), '="'.$p->cnee->tel.'"', '1', '="'.$cnid.'"', 'AUEV GROUP Pty Ltd', '601', '1 Monfries Place, Nicholls ACT 2913', '="1800518000"', '601', '142', '601', '="'.$hs.'"', $p->eitems['b'][$gi].'/'.$p->eitems['m'][$gi], $wqt, $p->eitems['w'][$gi], '="'.$u.'"', '', '2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $p->eitems['w'][$gi], '', '', '', $p->eitems['w'][$gi], '', '', '', '', $gi+1]);

					$xls->addRow($i++, ['="'.$p->ref.'"', 'B', trim(str_replace($p->eitems['b'][$gi], '', $g)).','.$p->eitems['b'][$gi], '="'.$hs.'"', '1', $p->eitems['w'][$gi], round($p->eitems['w'][$gi] / $tnt * $swt, 2), $p->eitems['b'][$gi].'/'.$p->eitems['m'][$gi], '142', $qty, $v*$qty, $p->eitems['u'][$gi], 142, $cnee, $p->cnee->getCnFullAddress(true), '="'.$p->cnee->tel.'"', '601', 'AUEV GROUP Pty Ltd', '1 Monfries Place, Nicholls ACT 2913', '="1800518000"', '1', '="'.$cnid.'"', 2, '601', '601', $p->hbn]);

					$qrows[] = ['="'.$p->ref.'"', trim(str_replace($p->eitems['b'][$gi], '', $g)), 'B', '', 1, $p->eitems['w'][$gi], $v*$qty, 142, $cnee, $p->cnee->getCnFullAddress(true), '="'.$p->cnee->tel.'"', 1, '="'.$cnid.'"', 'AUEV GROUP Pty Ltd', '悉尼', '1 Monfries Place, Nicholls ACT 2913', '="1800518000"', '澳大利亚', '中国', '澳大利亚', '="'.$hs.'"', $p->eitems['m'][$gi], $qty, $p->eitems['w'][$gi], $p->eitems['u'][$gi], '', 2];

					/*if($gi == 0){
						$ors[] = [$pc, $model->awb, '="'.$p->ref.'"', 601, '', '', '', '', '', '', 0, 1, $swt, $tnt, 'AUEV GROUP Pty Ltd', 601, 601, $cnee, 1, '="'.$cnid.'"', '', $gi+1, 1, '="'.$hs.'"', trim(str_replace($p->eitems['b'][$gi], '', $g)).','.$p->eitems['b'][$gi], $p->eitems['m'][$gi], 601, $v, $v*$qty, '', $qty, $u, '', '', '', '', round($p->eitems['w'][$gi] / $tnt * $swt, 2)];
					}else{
						$ors[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $gi+1, 1, '="'.$hs.'"', trim(str_replace($p->eitems['b'][$gi], '', $g)).','.$p->eitems['b'][$gi], $p->eitems['m'][$gi], 601, $v, $v*$qty, '', $qty, $u, '', '', '', '', round($p->eitems['w'][$gi] / $tnt * $swt, 2)];
					}*/
					$tqty += $qty;
					$tv += $v*$qty;
					$ln ++;
				}	
				$srows[] = [$pc, '="'.$p->ref.'"', $cnee, $p->cnee->getCnFullAddress(true), '="'.$p->cnee->tel.'"', $swt];
				
				/*$ors[0][10] = $tv;
				foreach($ors as $or){
					$orows[] = $or;
				}*/
				$pc++;
			}
			$xls->setCurrency("F2:G".$i, "#,##0.00");
			$zip->addFromString($model->no.'_申报单_LEDEX.xlsx', $xls->output(false, 'Excel2007', false));

			//sino file
			/*
			$xls = new oExcel;
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('申报单');
			$i = 1;
			$xls->addRow($i++, ['报关类别', 'B', '进出口', 'I', '申报口岸', '9002', '经营单位', '货主单位名称', '中外运空运发展股份有限公司西北分公司', '申报单位', '0', '6101981014', '中外运空运发展股份有限公司西北分公司', '运输工具', '飞机', $model->flight, '5', '监管方式', '3010', '征免性质分类', '101', '成交方式', '包装种类', '2', '录入', '8930000004256', '6101981014', '中外运空运发展股份有限公司西北分公司', '报关员代码', '90000132', '码头/货场代码', '515111', '币制', '142', '进出口日期', $model->etd]);
			$xls->addRow($i++, ['序号', '总运单号', '分运单号', '贸易国别', '运费标记', '运费币制', '运费／率', '保险费标记', '保险费币制', '保险费／率', '总金额', '总件数', '总毛重', '总净重', '发件人姓名', '发件人国别', '发件人城市', '收件人姓名', '证件类型', '证件号', '随附单据代码', '随附单据编号', '序号', '商品编号', '商品名称', '商品规格、型号', '产销国', '申报单价', '申报总价', '征减免税方式', '申报数量', '申报计量单位', '第一（法定）数量', '第一(法定)计量单位', '第二数量', '第二计量单位', '商品毛重']);
			foreach($orows as $r){
				$xls->addRow($i++, $r);
			}
			$zip->addFromString($model->no.'_申报单_SINO.xlsx', $xls->output(false, 'Excel2007', false));
			*/


			//trans file
			$xls = new oExcel;
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('转运清单');
			$i = 1;
			$xls->addRow($i++, ['序号', '分运单号', '收货人名称(货主单位名称)', '电话', '地址', '申报重量']);
			foreach($srows as $r){
				$xls->addRow($i++, $r);
			}
			$zip->addFromString($model->no.'_国内派送转运清单.xlsx', $xls->output(false, 'Excel2007', false));

			//ciq file
			$xls = new oExcel;
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('商检单');
			$i = 1;
			$xls->addRow($i++, ['分运单号', '货品名称', '申报类型', '成交方式', '件数', '申报重量', '申报价值', '币制', '收货人名称(货主单位名称)', '地址', '电话', '收发件人证件类型', '收发件人证件号', '发件人名称', '发件人城市', '地址', '电话', '生产国别', '收件人国家', '发件人国家', '商品编号', '规格型号', '数量', '重量', '单位', '贸易方式', '包装种类', '经营单位编码', '经营单位名称', '货主单位地区代码', '货主单位代码', '合同号', '运费标记', '运费币制', '运费/率', '保险费标记', '保险费币制', '保险费/率', '杂费标记', '杂费币制', '杂费/率', '净重', '关联编号字段', '码头/货场代码', '用途', '第一(法定)数量', '第二数量', '第一(法定)计量单位', '第二计量单位', '随附单证类型', '随附单证号码']);
			foreach($qrows as $r){
				$xls->addRow($i++, $r);
			}
			$zip->addFromString($model->no.'_商检单.xlsx', $xls->output(false, 'Excel2007', false));
			$zip->close();

			header("Cache-Control: maxage=1");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header('Content-Disposition: attachment; filename="'.basename($zf).'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($zf));
			readfile($zf);
			unlink($zf);
			Yii::app()->end();
			break;
			case 'CNFZH': //FZ
			$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.(empty($model->awb)? '' : '_'.$model->awb).'_x2.zip';
			$zip = new ZipArchive;
			$zip->open($zf, ZipArchive::CREATE);
			
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('申报清单');
			function psn($p, $i, $mb = true){
				$g = $p->eitems['g'][$i];
				
				if($p->eitems['type'][$i] == 'B'){
					/*$g = str_replace('白金版', '', $g);
					$g = str_replace('金装', '', $g);
					$g = preg_replace('/\s*(一|二|三|四|1|2|3|4)段$/', '', $g);
					*/
					//$g = $p->eitems['b'][$i].str_replace(['一', '二', '三', '四'], ['1', '2', '3', '4'], $p->eitems['m'][$i]).'婴儿奶粉';
				}elseif($p->eitems['type'][$i] == 'M'){
					$g = ($mb? $p->eitems['b'][$i] : '').'成人奶粉';
					$g = str_ireplace('Devondale', '德运', $g);
					$g = str_ireplace('Maxigenes', '美可卓', $g);
				}elseif($p->eitems['type'][$i] == 'O'){
					$g = str_ireplace($p->eitems['m'][$i], '', $g);
					$g = str_ireplace($p->eitems['b'][$i], '', $g);
					$g = preg_replace('/(\d+)(g|片|ml|粒|克)$/i', '', $g);
					$g = $p->eitems['b'][$i].$g;
				}

				return trim($g);
			}
			$xls->addRow($i++, ['进出境快件个人物品申报单']);
			$xls->addRow($i++, ['运营人名称', '', '福建易北速递有限公司', '', '发件人国别代码:', '', '601', '',	'贸易国别（起/抵运地）：', '', '601', '', '', '', '', '', '', '', '', '进/出口岸：', '3507', '运输工具名称(中文)：', '', '运输工具名称(英文)：', '', '航次号：', $model->flight]);
			$xls->addRow($i++, ['进/出口日期', $model->etd, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '总运单号码', $model->awb, '', '', '客户标识：']);
			
			$xls->addRow($i++, array('序号', '分运单号', '物品名称', '品牌', '规格', '数量', '单价', '单位', '价值', '件数', '重量', '净重', '毛重', '个人完税税号', '税率', '税额', '实收税额', 'HS编码', '发件人', '发件人电话', '发件人地址', '收件人姓名', '身份证号码', '省份', '收件地址', '收件人电话', '产销国', '验证代码', '备注'));
			$srows = [];
			$ln = 1;
			$pc = 1;
			$xhd = false;
			foreach($model->shipments as $p){
				$p->altGoods();
				$tv = 0;
				$tqty = 0;
				$tduty = 0;
				$tnw = 0;
				$noidphoto = '';
				$bld = '';
				$p->altCnee();
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$cnee = $p->cnee->name;
				$addr = $p->cnee->getCnFullAddress();

				if(empty($p->mdata['AltCnee'])){
					if(!empty($p->cnee->cnid_id)){
						$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
					}
				}else{
					$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
				}

				$cnee = preg_replace('/\s+/', '', $cnee);

				$tnt = 0;
				if(is_array($p->eitems['w'])){
					foreach($p->eitems['w'] as $gi => $w){
						if(empty($w)) continue;
						$tnt += $w;
					}
				}
				$swt = $p->shipWeight(true);

				if($tnt <= 0 || $tnt > $swt) $tnt = min($swt * 0.9, $swt - 0.2);
				if($tnt <= 0) $tnt = 0.1;
				$ors = [];
				if($p->hasUnmappedGoods()) $p->mapGoods();
				if(empty($p->eitems['g'][0])) die($p->hbn.' goods detail');
				foreach($p->eitems['g'] as $gi => $g){
					if(empty($p->eitems['q'][$gi])) continue;
					$pd = empty($p->eitems['pid'][$gi])? false : ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = floatval($p->eitems['q'][$gi]) == 0 ? 1 : floatval(trim($p->eitems['q'][$gi]));
					if(empty($pd)){
						$pd = ExProdb::model()->find('name = :n', [':n' => $g]);
					}
					if(empty($pd)){
						$v = floatval($p->eitems['v'][$gi]);
						$hs = $p->eitems['hs'][$gi];
					}else{
						$v = empty($pd->mdata['price_CNFZH'])? $pd->price : $pd->mdata['price_CNFZH'];
						if(!empty($p->mdata['altItems']['v'][$gi])) $v = $p->mdata['altItems']['v'][$gi];
						$hs = empty($pd->mdata['hs_CNFZH'])? $pd->hs : $pd->mdata['hs_CNFZH'];
						$p->eitems['g'][$gi] = empty($pd->mdata['name_CNFZH'])? $pd->name_zh : $pd->mdata['name_CNFZH'];
						$p->eitems['m'][$gi] = $pd->model;
						$p->eitems['b'][$gi] = $pd->brand;
						$p->eitems['w'][$gi] = $pd->weight * $qty;
					}
					
					if(empty($qty)) $qty = -1;
					//$tr = HS::getUpr($hs, 'rate') * 100;
					//$duty = round($v * $qty * $tr) / 100;
					//$tduty += $duty;
					$u = HS::getUpr($hs, 'uc');
					if(empty($u)) $u = $p->eitems['u'][$gi];
					if($hs == '1019900') $u = '140';
					$g = psn($p, $gi);
					$dqty = $u == '035'? $p->eitems['w'][$gi] : $qty;
					$uv = $u == '035'? round($v * $qty / $p->eitems['w'][$gi] * 10)/10 : $v;

					$dl = '保健品';
					$wqt = $p->eitems['w'][$gi];

					if(floatval($p->eitems['w'][$gi]) == 0) die($p->hbn.' item weight problem');
					if($p->eitems['type'][$gi] == 'B'){
						$dl = '婴儿奶粉';
						$p->eitems['m'][$gi] = $p->eitems['m'][$gi].' 900g';
						//$v = $uv * $p->eitems['w'][$gi] / $qty;
						if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
						if($qty < 3){
							$awt = 1.2 * $qty;
						}elseif($qty == 3){
							$awt = 3.5;
						}elseif($qty == 4){
							$awt = 4.6;
						}elseif($qty == 6){
							$awt = 7;
						}
						if($p->eitems['w'][$gi] > $swt) $swt = $awt;
					}elseif($p->eitems['type'][$gi] == 'M'){
						$dl = '成人奶粉';
						//$v = $uv * $p->eitems['w'][$gi] / $qty;
						if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
						$p->eitems['m'][$gi] = '均段'.round($p->eitems['w'][$gi] / $qty * 1000).'g';
						$awt = $qty + 0.2;
						if($p->eitems['w'][$gi] > $swt) $swt = $awt;
					}else{
						$wqt = $qty;
					}
					
					if(floatval($p->eitems['w'][$gi]) > $tnt) $p->eitems['w'][$gi] = $tnt;

					$t = HsPoc::getTariff($hs, $exc, $v, $p->eitems['w'][$gi] / $qty);

					$xls->addRow($i++, [($gi>0? '' : $pc), '="'.$p->ref.'"', trim(str_replace($p->eitems['b'][$gi], '', $g)), $p->eitems['b'][$gi], $p->eitems['m'][$gi], $qty, $v, $u, $qty*$v, 1, $swt, $p->eitems['w'][$gi], round(floatval($p->eitems['w'][$gi]) / $tnt * $swt, 2), '="'.$hs.'"', $t[1], round($t[2] * $qty *100)/100, ($gi==0? $p->calTariff() : ''), '="'.(empty($pd)? $hs : $pd->hs2).'"', 'PACE', '="1800518000"', '6C The Crescent, Kingsrove', $cnee, '="'.$cnid.'"', $p->cnee->state, $addr, '="'.$p->cnee->tel.'"', 601, '', '']);

					$tqty += $qty;
					$tv += $v*$qty;
					$ln ++;
				}	
				$srows[] = [$p->hbn, 'PCAE', '="1800518000"', '6C The Crescent, Kingsgrove', $cnee, $addr, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '="'.$p->ref.'"', $swt, '', '', '', '="'.$cnee_tel.'"', '="'.$p->cnee->tel.'"', $p->cnee->state, $p->cnee->city, $p->cnee->suburb];
				$pc++;
			}

			$zip->addFromString($model->no.'-'.$model->awb.'-'.$model->totShipments().'-申报清单.xlsx', $xls->output(false, 'Excel2007', false));

			//trans file
			$xls = new oExcel;
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('派送清单');
			$i = 1;
			$xls->addRow($i++, ['订单号', '寄件人姓名', '寄件人手机号', '寄件人详细地址', '收件人姓名', '收件人详细地址', '长', '宽', '高', '协议客户分仓名称', '内件性质', '运输方式', '寄件人省', '寄件人城市', '寄件人区县', '申报类别', '申报币制代码', '其他费', '同城标识', '报关报检费', '口岸服务费', '口岸杂费', '收件人单位', '邮件号', '重量', '收件人安全电话', '寄件人固话', '保险金额', '收件人固话', '收件人手机号', '收件人省', '收件人城市', '收件人区县', '交寄人证件类型', '交寄人证件号码', '封装费', '备注', '保险保价标志', '保价金额', '代收款标志', '代收款金额', '回单标识', '回单运单号', '一票多件标识', '主单号', '一票多件计费方式', '商品名称', '商品类型', '商品数量', '商品单价', '商品重量', '内件号', '内件名称', '投递提示', '交寄人身份信息伪码', '投递签收要求', '收件人邮编', '寄件人邮编', '付款方式', '申报信息来源']);
			foreach($srows as $r){
				$xls->addRow($i++, $r);
			}

			$zip->addFromString($model->no.'-'.$model->awb.'-'.$model->totShipments().'-派送清单.xlsx', $xls->output(false, 'Excel2007', false));

			
			$zip->close();

			header("Cache-Control: maxage=1");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header('Content-Disposition: attachment; filename="'.basename($zf).'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($zf));
			readfile($zf);
			unlink($zf);
			Yii::app()->end();
			break;
			case 'CNXI2': //XA2
			$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.(empty($model->awb)? '' : '_'.$model->awb).'_x2.zip';
			$zip = new ZipArchive;
			$zip->open($zf, ZipArchive::CREATE);
			$pno = $model->getPno();
			
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('申报清单');
			function psn($p, $i, $mb = true){
				$g = $p->eitems['g'][$i];
				
				if($p->eitems['type'][$i] == 'B'){
					/*$g = str_replace('白金版', '', $g);
					$g = str_replace('金装', '', $g);
					$g = preg_replace('/\s*(一|二|三|四|1|2|3|4)段$/', '', $g);
					*/
					$g = $p->eitems['b'][$i].str_replace(['一', '二', '三', '四'], ['1', '2', '3', '4'], $p->eitems['m'][$i]).'婴儿奶粉';
				}elseif($p->eitems['type'][$i] == 'M'){
					$g = ($mb? $p->eitems['b'][$i] : '').'成人奶粉';
					$g = str_ireplace('Devondale', '德运', $g);
					$g = str_ireplace('Maxigenes', '美可卓', $g);
				}elseif($p->eitems['type'][$i] == 'O'){
					$g = str_ireplace($p->eitems['m'][$i], '', $g);
					$g = str_ireplace($p->eitems['b'][$i], '', $g);
					$g = preg_replace('/(\d+)(g|片|ml|粒|克)$/i', '', $g);
					$g = $p->eitems['b'][$i].$g;
				}

				return trim($g);
			}

			$xls->addRow($i++, array('分运单号', '货物品名', '申报类型', '成交方式', '件数', '申报重量', '申报价值', '币制', '收货人姓名(货主单位名称)', '地址', '电话', '收件人证件类型', '收件人证件号', '发件人名称', '发件人城市', '地址_1', '电话_1', '生产国别', '收件人国家', '发件人国家', '商品编号', '规格型号', '数量', '重量', '单位', '贸易方式', '包装种类', '经营单位编号', '经营单位名称', '货主单位地区代码', '货主单位代码', '合同号', '运费标记', '运费币制', '运费/率', '保险费标记', '保险费币制', '保险费/率', '杂费标记', '杂费币制', '杂费/率', '净重', '关联编号字段', '码头/货场代码', '用途', '第一(法定)数量', '第二数量', '第一(法定)计量单位', '第二计量单位', '随附单证类型', '随附单证号码'));
			$srows = [];
			$hdrows = [];
			$qrows = [];
			//$orows = [];
			$ln = 1;
			$pc = 1;
			$xhd = false;
			foreach($model->shipments as $p){
				$p->altGoods();
				$tv = 0;
				$tqty = 0;
				$tduty = 0;
				$tnw = 0;
				$noidphoto = '';
				$bld = '';
				$p->altCnee();
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$cnee = $p->cnee->name;

				if(empty($p->mdata['AltCnee'])){
					if(!empty($p->cnee->cnid_id)){
						$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
					}
				}else{
					$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
				}

				$cnee = preg_replace('/\s+/', '', $cnee);

				$tnt = 0;
				if(is_array($p->eitems['w'])){
					foreach($p->eitems['w'] as $gi => $w){
						if(empty($w)) continue;
						$tnt += $w;
					}
				}
				$swt = $p->shipWeight(true);

				if($tnt <= 0 || $tnt > $swt) $tnt = min($swt * 0.9, $swt - 0.2);
				if($tnt <= 0) $tnt = 0.1;
				$ors = [];
				if($p->hasUnmappedGoods()) $p->mapGoods();
				if(empty($p->eitems['g'][0])) die($p->hbn.' goods detail');
				foreach($p->eitems['g'] as $gi => $g){
					if(empty($p->eitems['q'][$gi])) continue;
					$pd = empty($p->eitems['pid'][$gi])? false : ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = floatval($p->eitems['q'][$gi]) == 0 ? 1 : floatval(trim($p->eitems['q'][$gi]));
					if(empty($pd)){
						$pd = ExProdb::model()->find('name = :n', [':n' => $g]);
					}
					if(empty($pd)){
						$v = floatval($p->eitems['v'][$gi]);
						$hs = $p->eitems['hs'][$gi];
					}else{
						$v = empty($pd->mdata['price_CNXIA'])? $pd->price : $pd->mdata['price_CNXIA'];
						if(!empty($p->mdata['altItems']['v'][$gi])) $v = $p->mdata['altItems']['v'][$gi];				
						$hs = empty($pd->mdata['hs_CNXIA'])? $pd->hs : $pd->mdata['hs_CNXIA'];
						$p->eitems['g'][$gi] = empty($pd->mdata['name_CNXIA'])? $pd->name_zh : $pd->mdata['name_CNXIA'];
						$p->eitems['m'][$gi] = $pd->model;
						$p->eitems['b'][$gi] = $pd->brand;
						$p->eitems['w'][$gi] = $pd->weight * $qty;
					}
					
					if(empty($qty)) $qty = -1;
					//$tr = HS::getUpr($hs, 'rate') * 100;
					//$duty = round($v * $qty * $tr) / 100;
					//$tduty += $duty;
					$u = HS::getUpr($hs, 'uc');
					if(empty($u)) $u = $p->eitems['u'][$gi];
					if($hs == '1019900') $u = '140';
					$g = psn($p, $gi);
					$dqty = $u == '035'? $p->eitems['w'][$gi] : $qty;
					$uv = $u == '035'? round($v * $qty / $p->eitems['w'][$gi] * 10)/10 : $v;

					$dl = '保健品';
					$wqt = $p->eitems['w'][$gi];

					if(floatval($p->eitems['w'][$gi]) == 0) die($p->hbn.' item weight problem');
					if($p->eitems['type'][$gi] == 'B'){
						$dl = '婴儿奶粉';
						$p->eitems['m'][$gi] = $p->eitems['m'][$gi].' 900g';
						$uv = 95;
						if(preg_match('/羊奶/', $g)){
							$uv = 99;
						}elseif(preg_match('/可瑞康|karicare/', $g)){
							$uv = 92;
						}elseif(preg_match('/a2|贝拉米|bellamy/i', $g)){
							$uv = 98;
						}
						$v = $uv * $p->eitems['w'][$gi] / $qty;
						if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
						if($qty < 3){
							$awt = 1.2 * $qty;
						}elseif($qty == 3){
							$awt = 3.5;
						}elseif($qty == 4){
							$awt = 4.6;
						}elseif($qty == 6){
							$awt = 7;
						}
						if($p->eitems['w'][$gi] > $swt) $swt = $awt;
					}elseif($p->eitems['type'][$gi] == 'M'){
						$dl = '成人奶粉';
						$uv = preg_match('/a2/i', $g)? 55: 50;
						$v = $uv * $p->eitems['w'][$gi] / $qty;
						if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
						$p->eitems['m'][$gi] = '均段'.round($p->eitems['w'][$gi] / $qty * 1000).'g';
						$awt = $qty + 0.2;
						if($p->eitems['w'][$gi] > $swt) $swt = $awt;
					}else{
						$wqt = $qty;
					}
					
					if(floatval($p->eitems['w'][$gi]) > $tnt) $p->eitems['w'][$gi] = $tnt;

					$xls->addRow($i++, ['="'.$p->ref.'"', trim(str_replace($p->eitems['b'][$gi], '', $g)), 'B', '', '1', round(floatval($p->eitems['w'][$gi]) / $tnt * $swt, 2), $v*$qty, '142', $cnee, $addr, '="'.$cnee_tel.'"', '1', '="'.$cnid.'"', 'Anzu', '601', '6C The Crescent, Kingsgrove', '="1800518000"', '601', '142', '601', '="'.$hs.'"', $p->eitems['b'][$gi].'/'.$p->eitems['m'][$gi], $wqt, $p->eitems['w'][$gi], $p->eitems['u'][$gi], '', 2, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $wqt]);

					$tqty += $qty;
					$tv += $v*$qty;
					$ln ++;
				}	
				$srows[] = ['="'.$p->ref.'"', '="'.$p->hbn.'"', 'Anzu', '="1800518000"', '6C The Crescent, Kingsgrove', $cnee, '="'.$cnee_tel.'"', $addr, $swt, "1", '快递包裹', '物品', $p->GoodsNames(), '', '', $pno];
				$paddr = $p->cnee->getCnFullAddress(true);
				$hdrows[] = ['="'.$p->ref.'"', '="'.$p->hbn.'"', 'Anzu', '="1800518000"', '6C The Crescent, Kingsgrove', $p->cnee->name, '="'.$p->cnee->tel.'"', $paddr, $swt, "1", '快递包裹', '物品', $p->GoodsNames(), '', ($addr != $paddr? '需换单！' : ''), $pno];
				$pc++;
			}

			$zip->addFromString('TL50-'.$pno.'-'.$model->awb.'-'.$model->totShipments().'-申报清单.xlsx', $xls->output(false, 'Excel2007', false));

			//trans file
			$xls = new oExcel;
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('派送清单');
			$i = 1;
			$xls->addRow($i++, ['邮件号', '*配货单号', '*寄件人姓名', '*寄件人联系方式', '*寄件人地址', '*收件人姓名', '*收件人联系方式', '*收件人地址', '物品重量', '备注', '业务类型', '内件性质', '内件信息', '箱号', '备注', '批次']);
			foreach($srows as $r){
				$xls->addRow($i++, $r);
			}

			$zip->addFromString('TL50-'.$pno.'-'.$model->awb.'-'.$model->totShipments().'-派送清单.xlsx', $xls->output(false, 'Excel2007', false));

			// if($xhd){
			// 	$xls = new oExcel;
			// 	$sheet = $xls->getActiveSheet();
			// 	$sheet->setTitle('派送清单');
			// 	$i = 1;
			// 	$xls->addRow($i++, ['邮件号', '*配货单号', '*寄件人姓名', '*寄件人联系方式', '*寄件人地址', '*收件人姓名', '*收件人联系方式', '*收件人地址', '物品重量', '备注', '业务类型', '内件性质', '内件信息', '箱号', '备注', '批次']);
			// 	foreach($hdrows as $r){
			// 		$xls->addRow($i++, $r);
			// 	}

			// 	$zip->addFromString('TL50-'.$pno.'-'.$model->awb.'-'.$model->totShipments().'-换单清单！.xlsx', $xls->output(false, 'Excel2007', false));
			// }
			
			$zip->close();

			header("Cache-Control: maxage=1");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header('Content-Disposition: attachment; filename="'.basename($zf).'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($zf));
			readfile($zf);
			unlink($zf);
			Yii::app()->end();

			break;
			case 'XAEMS':
				$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.(empty($model->awb)? '' : '_'.$model->awb).'_x2.zip';
				$zip = new ZipArchive;
				$zip->open($zf, ZipArchive::CREATE);
				$pno = $model->getPno();

				function psn($p, $i, $mb = true){
					$g = $p->eitems['g'][$i];
					
					if($p->eitems['type'][$i] == 'B'){
						/*$g = str_replace('白金版', '', $g);
						$g = str_replace('金装', '', $g);
						$g = preg_replace('/\s*(一|二|三|四|1|2|3|4)段$/', '', $g);
						*/
						$g = $p->eitems['b'][$i].str_replace(['一', '二', '三', '四'], ['1', '2', '3', '4'], $p->eitems['m'][$i]).'婴儿奶粉';
					}elseif($p->eitems['type'][$i] == 'M'){
						$g = ($mb? $p->eitems['b'][$i] : '').'成人奶粉';
						$g = str_ireplace('Devondale', '德运', $g);
						$g = str_ireplace('Maxigenes', '美可卓', $g);
					}elseif($p->eitems['type'][$i] == 'O'){
						$g = str_ireplace($p->eitems['m'][$i], '', $g);
						$g = str_ireplace($p->eitems['b'][$i], '', $g);
						$g = preg_replace('/(\d+)(g|片|ml|粒|克)$/i', '', $g);
						$g = $p->eitems['b'][$i].$g;
					}

					return trim($g);
				}
				$srows = [];
				$hdrows = [];
				$qrows = [];
				//$orows = [];
				$ln = 1;
				$pc = 1;
				$xhd = false;
				foreach($model->shipments as $p){
					$p->altGoods();
					$tv = 0;
					$tqty = 0;
					$tduty = 0;
					$tnw = 0;
					$noidphoto = '';
					$bld = '';
					$p->altCnee();

					if(empty($p->cnee->cnid_id)){
						$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
					}else{
						$cnid = $p->cnee->cnid->no;
					}
					$cnee = $p->cnee->name;

					if(empty($p->mdata['AltCnee'])){
						if(!empty($p->cnee->cnid_id)){
							$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
						}
					}else{
						$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
					}

					$cnee = preg_replace('/\s+/', '', $cnee);

					$tnt = 0;
					if(is_array($p->eitems['w'])){
						foreach($p->eitems['w'] as $gi => $w){
							if(empty($w)) continue;
							$tnt += $w;
						}
					}
					$swt = $p->shipWeight(true);

					if($tnt <= 0 || $tnt > $swt) $tnt = min($swt * 0.9, $swt - 0.2);
					if($tnt <= 0) $tnt = 0.1;
					$ors = [];
					if($p->hasUnmappedGoods()) $p->mapGoods();
					if(empty($p->eitems['g'][0])) die($p->hbn.' goods detail');
					foreach($p->eitems['g'] as $gi => $g){
						if(empty($p->eitems['q'][$gi])) continue;
						$pd = empty($p->eitems['pid'][$gi])? false : ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
						$qty = floatval($p->eitems['q'][$gi]) == 0 ? 1 : floatval(trim($p->eitems['q'][$gi]));
						if(empty($pd)){
							$pd = ExProdb::model()->find('name = :n', [':n' => $g]);
						}
						if(empty($pd)){
							$v = floatval($p->eitems['v'][$gi]);
							$hs = $p->eitems['hs'][$gi];
						}else{
							$v = empty($pd->mdata['price_CNXIA'])? $pd->price : $pd->mdata['price_CNXIA'];
							$hs = empty($pd->mdata['hs_CNXIA'])? $pd->hs : $pd->mdata['hs_CNXIA'];
							$p->eitems['g'][$gi] = empty($pd->mdata['name_CNXIA'])? $pd->name_zh : $pd->mdata['name_CNXIA'];
							$p->eitems['m'][$gi] = $pd->model;
							$p->eitems['b'][$gi] = $pd->brand;
							$p->eitems['w'][$gi] = $pd->weight * $qty;
						}
						
						if(empty($qty)) $qty = -1;
						//$tr = HS::getUpr($hs, 'rate') * 100;
						//$duty = round($v * $qty * $tr) / 100;
						//$tduty += $duty;
						$u = HS::getUpr($hs, 'uc');
						if(empty($u)) $u = $p->eitems['u'][$gi];
						if($hs == '1019900') $u = '140';
						$g = psn($p, $gi);
						$dqty = $u == '035'? $p->eitems['w'][$gi] : $qty;
						$uv = $u == '035'? round($v * $qty / $p->eitems['w'][$gi] * 10)/10 : $v;

						$dl = '保健品';
						$wqt = $p->eitems['w'][$gi];

						if(floatval($p->eitems['w'][$gi]) == 0) die($p->hbn.' item weight problem');
						if($p->eitems['type'][$gi] == 'B'){
							$dl = '婴儿奶粉';
							$p->eitems['m'][$gi] = $p->eitems['m'][$gi].' 900g';
							$uv = 95;
							if(preg_match('/羊奶/', $g)){
								$uv = 99;
							}elseif(preg_match('/可瑞康|karicare/', $g)){
								$uv = 92;
							}elseif(preg_match('/a2|贝拉米|bellamy/i', $g)){
								$uv = 98;
							}
							$v = $uv * $p->eitems['w'][$gi] / $qty;
							if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
							if($qty < 3){
								$awt = 1.2 * $qty;
							}elseif($qty == 3){
								$awt = 3.5;
							}elseif($qty == 4){
								$awt = 4.6;
							}elseif($qty == 6){
								$awt = 7;
							}
							if($p->eitems['w'][$gi] > $swt) $swt = $awt;
						}elseif($p->eitems['type'][$gi] == 'M'){
							$dl = '成人奶粉';
							$uv = preg_match('/a2/i', $g)? 55: 50;
							$v = $uv * $p->eitems['w'][$gi] / $qty;
							if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
							$p->eitems['m'][$gi] = '均段'.round($p->eitems['w'][$gi] / $qty * 1000).'g';
							$awt = $qty + 0.2;
							if($p->eitems['w'][$gi] > $swt) $swt = $awt;
						}else{
							$wqt = $qty;
						}
						
						if(floatval($p->eitems['w'][$gi]) > $tnt) $p->eitems['w'][$gi] = $tnt;

						$xls->addRow($i++, ['="'.$p->ref.'"', trim(str_replace($p->eitems['b'][$gi], '', $g)).','.$p->eitems['b'][$gi], 'B', '', '1', round(floatval($p->eitems['w'][$gi]) / $tnt * $swt, 2), $v*$qty, '142', $cnee, $addr, '="'.$cnee_tel.'"', '1', '="'.$cnid.'"', 'Anzu', '601', '6C The Crescent, Kingsgrove', '="1800518000"', '601', '142', '601', '="'.$hs.'"', $p->eitems['b'][$gi].'/'.$p->eitems['m'][$gi], $wqt, $p->eitems['w'][$gi], $p->eitems['u'][$gi], '', 2, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $wqt]);

						$tqty += $qty;
						$tv += $v*$qty;
						$ln ++;
					}	
					$srows[] = ['="'.$p->ref.'"', '="'.$p->hbn.'"', 'Anzu', '="1800518000"', '6C The Crescent, Kingsgrove', $cnee, '="'.$cnee_tel.'"', $addr, $swt, "1", '快递包裹', '物品', $p->GoodsNames(), '', '', $pno];
					$paddr = $p->cnee->getCnFullAddress(true);
					$hdrows[] = ['="'.$p->ref.'"', '="'.$p->hbn.'"', 'Anzu', '="1800518000"', '6C The Crescent, Kingsgrove', $p->cnee->name, '="'.$p->cnee->tel.'"', $paddr, $swt, "1", '快递包裹', '物品', $p->GoodsNames(), '', ($addr != $paddr? '需换单！' : ''), $pno];
					$pc++;
				}

				if($xhd){
					$xls = new oExcel;
					$sheet = $xls->getActiveSheet();
					$sheet->setTitle('换单清单');
					$i = 1;
					$xls->addRow($i++, ['邮件号', '*配货单号', '*寄件人姓名', '*寄件人联系方式', '*寄件人地址', '*收件人姓名', '*收件人联系方式', '*收件人地址', '物品重量', '备注', '业务类型', '内件性质', '内件信息', '箱号', '备注', '批次']);
					foreach($hdrows as $r){
						$xls->addRow($i++, $r);
					}

					$zip->addFromString('TL50-'.$pno.'-'.$model->awb.'-'.$model->totShipments().'-换单清单！.xlsx', $xls->output(false, 'Excel2007', false));
				} else {
					$xls = new oExcel;
					$sheet = $xls->getActiveSheet();
					$sheet->setTitle('派送清单');
					$i = 1;
					$xls->addRow($i++, ['邮件号', '*配货单号', '*寄件人姓名', '*寄件人联系方式', '*寄件人地址', '*收件人姓名', '*收件人联系方式', '*收件人地址', '物品重量', '备注', '业务类型', '内件性质', '内件信息', '箱号', '备注', '批次']);
					foreach($srows as $r){
						$xls->addRow($i++, $r);
					}

					$zip->addFromString('TL50-'.$pno.'-'.$model->awb.'-'.$model->totShipments().'-派送清单.xlsx', $xls->output(false, 'Excel2007', false));
				}
			
				$zip->close();

				header("Cache-Control: maxage=1");
				header("Content-Description: File Transfer");
				header("Content-type: application/octet-stream");
				header('Content-Disposition: attachment; filename="'.basename($zf).'"');
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".filesize($zf));
				readfile($zf);
				unlink($zf);
				Yii::app()->end();
			case 'CNJMN': //BDT JM
			case 'CNJM2': //BDT JM
			$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.(empty($model->awb)? '' : '_'.$model->awb).'_x2.zip';
			$zip = new ZipArchive;
			$zip->open($zf, ZipArchive::CREATE);
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('申报清单');
			function psn($p, $i, $mb = true){
				$pd = new ExProdb;
				$g = $p->eitems['g'][$i];
				
				if($p->eitems['type'][$i] == 'B'){
					/*$g = str_replace('白金版', '', $g);
					$g = str_replace('金装', '', $g);
					$g = preg_replace('/\s*(一|二|三|四|1|2|3|4)段$/', '', $g);
					*/
					$g = $p->eitems['b'][$i].str_replace(['一', '二', '三', '四'], ['1', '2', '3', '4'], $p->eitems['m'][$i]).'婴儿奶粉';
				}elseif($p->eitems['type'][$i] == 'M'){
					$g = ($mb? $p->eitems['b'][$i] : '').'成人奶粉';
					//$g = str_ireplace('Devondale', '德运', $g);
					//$g = str_ireplace('Maxigenes', '美可卓', $g);
				}elseif($p->eitems['type'][$i] == 'O'){
					$g = str_ireplace($p->eitems['m'][$i], '', $g);
					$g = str_ireplace($p->eitems['b'][$i], '', $g);
					$g = preg_replace('/(\d+)(g|片|ml|粒|克)$/i', '', $g);
					$g = $p->eitems['b'][$i].$g;
				}
				$g = str_replace($p->eitems['b'][$i], $pd->brandEn($p->eitems['b'][$i]), $g);

				return trim($g);
			}

			$xls->addRow($i++, array('序号', '经营单位编号', '经营单位名称', '分运单号', '货物名称', '商品编码', '件数', '净重(KG)', '毛重(KG)', '规格/型号', '成交总价', '申报单价', '申报总价', '价值', '合同号', '申报数量', '申报计量单位', '第一（法定）数量', '第一（法定）计量单位', '第二（法定）数量', '第二（法定）计量单位', '收件人', '收件人地址', '货主单位名称', '货主单位代码', '贸易国别', '产销国', '电话号码(固话)', '发件人国别', '发件人', '发件人地址', '货主单位地区代码', '收发件人证件号', '收发件人证件类型', '包装种类', '用途', '随附单证类型', '随附单证编号', '类别', '无身份证照片', '身份证问题'));

			$srows = [];
			$ln = 1;
			$pc = 1;
			foreach($model->shipments as $p){
				$p->altGoods();
				$tv = 0;
				$tqty = 0;
				$tduty = 0;
				$tnw = 0;
				$noidphoto = '';
				$bld = '';
				$p->altCnee();
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$cnee = $p->cnee->name;

				if(empty($p->mdata['AltCnee'])){
					if(!empty($p->cnee->cnid_id)){
						$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
					}
				}else{
					$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
				}

				$cnee = preg_replace('/\s+/', '', $cnee);

				$cnor_name = $p->cnor->name;
				if(preg_match('/^\d+$/', $cnor_name) || preg_match('/Australia|Group|Ltd|International/i', $cnor_name)){
					$cnor_name = 'John';
				}

				$tnt = 0;
				if(is_array($p->eitems['w'])){
					foreach($p->eitems['w'] as $gi => $w){
						if(empty($w)) continue;
						$tnt += $w;
					}
				}
				$swt = $p->shipWeight(true);

				if($tnt <= 0) $tnt = min($swt * 0.9, $swt - 0.2);
				if($tnt <= 0) $tnt = 0.1;

				foreach($p->eitems['g'] as $gi => $g){
					if(empty($p->eitems['q'][$gi])) continue;
					$pd = empty($p->eitems['pid'][$gi])? false : ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = floatval($p->eitems['q'][$gi]) == 0 ? 1 : floatval(trim($p->eitems['q'][$gi]));
					if(empty($pd)){
						$pd = ExProdb::model()->find('name = :n', [':n' => $g]);
					}
					if(empty($pd)){
						$v = $p->eitems['v'][$gi];
						$hs = $p->eitems['hs'][$gi];
					}else{
						$v = empty($pd->mdata['price_CNJMN'])? $pd->price : $pd->mdata['price_CNJMN'];
						$hs = empty($pd->mdata['hs_CNJMN'])? $pd->hs : $pd->mdata['hs_CNJMN'];
						$p->eitems['g'][$gi] = empty($pd->mdata['name_CNJMN'])? $pd->name_zh : $pd->mdata['name_CNJMN'];
						$p->eitems['m'][$gi] = $pd->model;
						$p->eitems['b'][$gi] = $pd->brand;
						$p->eitems['w'][$gi] = $pd->weight * $qty;
					}
					
					if(empty($qty)) $qty = -1;
					$tr = HS::getUpr($hs, 'rate') * 100;
					$duty = round($v * $qty * $tr) / 100;
					$tduty += $duty;
					$u = HS::getUpr($hs, 'uc');
					if(empty($u)) $u = $p->eitems['u'][$gi];
					if($hs == '1019900') $u = '140';
					$g = psn($p, $gi);
					$dqty = $u == '035'? $p->eitems['w'][$gi] : $qty;
					$uv = $u == '035'? round($v * $qty / $p->eitems['w'][$gi] * 10)/10 : $v;

					$dl = '保健品';
					if($p->eitems['type'][$gi] == 'B'){
						$dl = '婴儿奶粉';
						$p->eitems['m'][$gi] = $p->eitems['m'][$gi].' 900g';
						$uv = 95;
						if(preg_match('/羊奶/', $g)){
							$uv = 99;
						}elseif(preg_match('/可瑞康|karicare/', $g)){
							$uv = 92;
						}elseif(preg_match('/a2|贝拉米|bellamy/i', $g)){
							$uv = 98;
						}
						$v = $uv * $p->eitems['w'][$gi] / $qty;
						if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
						if($qty < 3){
							$awt = 1.2 * $qty;
						}elseif($qty == 3){
							$awt = 3.5;
						}elseif($qty == 4){
							$awt = 4.6;
						}elseif($qty == 6){
							$awt = 7;
						}
					}elseif($p->eitems['type'][$gi] == 'M'){
						$dl = '成人奶粉';
						$uv = preg_match('/a2/i', $g)? 55: 50;
						$v = $uv * $p->eitems['w'][$gi] / $qty;
						if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
						$p->eitems['m'][$gi] = '均段'.round($p->eitems['w'][$gi] / $qty * 1000).'g';
						$awt = $qty + 0.2;
					}

					
					if($p->eitems['w'][$gi] > $swt) $swt = $awt;
					if($p->eitems['w'][$gi] > $swt) $swt = $p->eitems['w'][$gi];

					$xls->addRow($i++, [$ln, '', '', '="'.$p->ref.'"', $g, '="'.$hs.'"', $qty, $p->eitems['w'][$gi], ($p->eitems['w'][$gi] / $tnt * $swt), $p->eitems['m'][$gi], $v*$qty, $uv, $v*$qty, $v*$qty, '', $dqty, '="'.$u.'"', '', '', '', '', $cnee, mb_substr($p->cnee->getCnFullAddress(true), 0, 25), $cnee, '="4407986007"', 601, 601, '="'.$p->cnee->tel.'"', 601, $cnor_name, trim(substr((empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : trim($p->cnor->fullAddress())), 0, 50), " ,."), 44079, '="'.$cnid.'"', 1, 2, 11, 1, $gi+1, 'B', $noidphoto, $bld]);
					
					$srows[] = [$pc, '="'.$model->awb.'"', '', '="'.$p->ref.'"', $cnor_name, 'SYD', (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"'), $cnee, '="'.$p->cnee->tel.'"', $p->cnee->state.$p->cnee->city, $p->cnee->postcode, mb_substr($p->cnee->getCnFullAddress(true), 0, 25), $dl, $qty, $v*$qty, $p->shipWeight(), '="'.$hs.'"', $g, '', $qty, $p->eitems['u'][$gi], $v, 'CNY', '="'.$cnid.'"', $noidphoto, $bld];
					
					$tqty += $qty;
					$ln ++;
				}
				$pc++;
			}
			$xls->setCurrency("H2:I".$i, "#,##0.00");
			$xls->setCurrency("K2:N".$i, "#,##0.00");
			$xls->setCurrency("P2:P".$i, "#,##0.00");
			$zip->addFromString($model->no.'_申报单.xlsx', $xls->output(false, 'Excel2007', false));

			//trans file
			$xls = new oExcel;
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('转关单');
			$i = 1;
			$xls->addRow($i++, ['NO	报关单号', '总运单号', '袋号', '快件单号', '发件人', '发件人地址', '电话号码', '收件人', '电话号码', '城市', '邮编', '收件人地址', '内件名称', '数量', '总价（AUD）', '毛重（KG）', '税号', '物品名称', '品牌', '数量', '单位', ' 单价 ', '币别', '备注', '无身份证照片']);
			foreach($srows as $r){
				$xls->addRow($i++, $r);
			}
			$xls->setCurrency("O2:P".$i, "#,##0.00");
			$xls->setCurrency("V2:V".$i, "#,##0.00");
			$zip->addFromString($model->no.'_转关单.xlsx', $xls->output(false, 'Excel2007', false));

			$zip->close();

			header("Cache-Control: maxage=1");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header('Content-Disposition: attachment; filename="'.basename($zf).'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($zf));
			readfile($zf);
			unlink($zf);
			Yii::app()->end();
			break;
			case 'CNMHN': //MH
			$of .= '-MH.xlsx';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('申报清单');
			function psn($p, $i, $mb = true){
				$g = $p->eitems['g'][$i];
				
				if($p->eitems['type'][$i] == 'B'){
					/*$g = str_replace('白金版', '', $g);
					$g = str_replace('金装', '', $g);
					$g = preg_replace('/\s*(一|二|三|四|1|2|3|4)段$/', '', $g);
					*/
					//$g = $p->eitems['b'][$i].str_replace(['一', '二', '三', '四'], ['1', '2', '3', '4'], $p->eitems['m'][$i]).'婴儿奶粉';
				}elseif($p->eitems['type'][$i] == 'M'){
					$g = ($mb? $p->eitems['b'][$i] : '').'成人奶粉';
					$g = str_ireplace('Devondale', '德运', $g);
					$g = str_ireplace('Maxigenes', '美可卓', $g);
				}elseif($p->eitems['type'][$i] == 'O'){
					$g = str_ireplace($p->eitems['m'][$i], '', $g);
					$g = str_ireplace($p->eitems['b'][$i], '', $g);
					$g = preg_replace('/(\d+)(g|片|ml|粒|克)$/i', '', $g);
					$g = $p->eitems['b'][$i].$g;
				}

				return trim($g);
			}
			$xls->addRow($i++, ['中华人民共和国海关进出境快件个人物品申报单']);
			$xls->addRow($i++, ['运营人名称：昆明普斯特速递货运有限责任公司磨憨分公司', '', '', '', '', '', '', '', '', '', '', '', '', '', '',	'报关单编号：']);
			$xls->addRow($i++, ['抵达时间：', '', $model->etd, '', '', '航次号：'.$model->flight, '', '', '', '', '', '', '', '提运单号：', '', '', $model->awb, '', '', '', '产销国：136']);
			$xls->addRow($i++, array('序号', '分运单号', '税 号', '商品名称', '件数', '毛重KG', '净重KG', '内件毛重KG', '内件数量', '申报单价', '申报总价', '计量单位', '税率', '应收税额', '实收税额', '补收税额', '收件人', '收件地址', '联系电话', '身份证号码', '备注(内件明细)', '查验情况', '处理结果'));
			$ln = 1;
			$pc = 1;
			$xhd = false;
			foreach($model->shipments as $p){
				$p->altGoods();
				$tv = 0;
				$tqty = 0;
				$tduty = 0;
				$tnw = 0;
				$noidphoto = '';
				$bld = '';
				$p->altCnee();
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$cnee = $p->cnee->name;

				if(empty($p->mdata['AltCnee'])){
					if(!empty($p->cnee->cnid_id)){
						$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
					}
				}else{
					$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
				}

				$cnee = preg_replace('/\s+/', '', $cnee);

				$tnt = 0;
				if(is_array($p->eitems['w'])){
					foreach($p->eitems['w'] as $gi => $w){
						if(empty($w)) continue;
						$tnt += $w;
					}
				}
				$swt = $p->shipWeight(true);

				if($tnt <= 0 || $tnt > $swt) $tnt = min($swt * 0.9, $swt - 0.2);
				if($tnt <= 0) $tnt = 0.1;
				$ors = [];
				if($p->hasUnmappedGoods()) $p->mapGoods();
				if(empty($p->eitems['g'][0])) die($p->hbn.' goods detail');
				foreach($p->eitems['g'] as $gi => $g){
					if(empty($p->eitems['q'][$gi])) continue;
					$pd = empty($p->eitems['pid'][$gi])? false : ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = floatval($p->eitems['q'][$gi]) == 0 ? 1 : floatval(trim($p->eitems['q'][$gi]));
					
					if(empty($pd)){
						$pd = ExProdb::model()->find('name_zh = :n', [':n' => $g]);
					}

					if(empty($pd)){
						$v = floatval($p->eitems['v'][$gi]);
						$hs = $p->eitems['hs'][$gi];
					}else{
						$v = empty($pd->mdata['price_CNMHN'])? $pd->price : $pd->mdata['price_CNMHN'];
						if(!empty($p->mdata['altItems']['v'][$gi])) $v = $p->mdata['altItems']['v'][$gi];
						$hs = empty($pd->mdata['hs_CNMHN'])? $pd->hs : $pd->mdata['hs_CNMHN'];
						$p->eitems['g'][$gi] = empty($pd->mdata['name_CNMHN'])? $pd->name_zh : $pd->mdata['name_CNMHN'];
						$p->eitems['m'][$gi] = $pd->model;
						$p->eitems['b'][$gi] = $pd->brand;
						$p->eitems['w'][$gi] = $pd->weight * $qty;
						$p->eitems['u'][$gi] = $pd->unit;
					}
					
					if(empty($qty)) $qty = -1;
					//$tr = HS::getUpr($hs, 'rate') * 100;
					//$duty = round($v * $qty * $tr) / 100;
					//$tduty += $duty;
					$u = $p->eitems['u'][$gi];
					$g = psn($p, $gi);
					$dqty = $u == '035'? $p->eitems['w'][$gi] : $qty;
					if(floatval($p->eitems['w'][$gi]) == 0) die($p->hbn.' item weight problem');
					//$uv = $u == '035'? round($v * $qty / $p->eitems['w'][$gi] * 10)/10 : $v;

					$dl = '保健品';
					$wqt = $p->eitems['w'][$gi];
					if($p->eitems['type'][$gi] == 'B'){
						$dl = '婴儿奶粉';
						$p->eitems['m'][$gi] = $p->eitems['m'][$gi].' 900g';
						//$v = $uv * $p->eitems['w'][$gi] / $qty;
						if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
						if($qty < 3){
							$awt = 1.2 * $qty;
						}elseif($qty == 3){
							$awt = 3.5;
						}elseif($qty == 4){
							$awt = 4.6;
						}elseif($qty == 6){
							$awt = 7;
						}
						if($p->eitems['w'][$gi] > $swt) $swt = $awt;
					}elseif($p->eitems['type'][$gi] == 'M'){
						$dl = '成人奶粉';
						//$v = $uv * $p->eitems['w'][$gi] / $qty;
						if(empty($p->eitems['u'][$gi])) $p->eitems['u'][$gi] = '千克';
						$p->eitems['m'][$gi] = '均段'.round($p->eitems['w'][$gi] / $qty * 1000).'g';
						$awt = $qty + 0.2;
						if($p->eitems['w'][$gi] > $swt) $swt = $awt;
					}else{
						$wqt = $qty;
					}
					
					if(floatval($p->eitems['w'][$gi]) > $tnt) $p->eitems['w'][$gi] = $tnt;

					$t = HsPoc::getTariff($hs, $exc, $v, $p->eitems['w'][$gi] / $qty);
			
					$xls->addRow($i++, [($gi>0? '' : $pc), ($gi>0? '' : '="'.$p->ref.'"'), '="'.$hs.'"', trim(str_replace($p->eitems['b'][$gi], '', $g)), '="1"', $swt, $p->eitems['w'][$gi], round(floatval($p->eitems['w'][$gi]) / $tnt * $swt, 2), $qty, $v, $qty*$v, $u, ($t[1] * 100).'%', round($t[2] * $qty *100)/100, ($gi==0? $p->calTariff() : 0), 0, ($gi>0? '' : $cnee), ($gi>0? '' : $addr), ($gi>0? '' : '="'.$cnee_tel.'"'), ($gi>0? '' : '="'.$cnid.'"'), $p->eitems['b'][$gi].trim(str_replace($p->eitems['b'][$gi], '', $g.$p->eitems['m'][$gi])).'*'.$qty]);

					$tqty += $qty;
					$tv += $v*$qty;
					$ln ++;
				}
				$pc++;
			}
			break;
			case 'HKHKG'://Hongkong
			$of .= '-HK.xlsx';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('发货清单');
			$xls->addRow($i++, array('运单号','转运单号','发货人',"发货人\n电话","发货\n地址","发货\n国家",'收货人',"收货人\n电话","收货\n地址","收货\n省份",'收货人身份证号码',"包裹\n件数","包裹\n重量","包裹价格\n（人民币）",'内物品名','备注'));
			$pc = 1;
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);
				$weight = empty($p->mdata['dcwt'])? $p->shipWeight() : $p->mdata['dcwt'];
				$weight = ($weight < 1)? 1 : $weight;
				$weight = round($weight * 100) /100;

				$xls->addRow($i++, array($p->hbn, $p->ref, $p->cnor->name, (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"'), (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), '澳大利亚', $p->cnee->name, '="'.$p->cnee->tel.'"', $p->cnee->getCnFullAddress(), $p->cnee->state, '="'.empty($p->cnee->cnid)? '': $p->cnee->cnid->no.'"', $p->pkg, $weight, $p->value, $p->GoodsNames(false ,', ', true), ''));
			}

			//second tab
			$xls->createSheet('申报清单');
			$xls->goSheet(1);
			$i = 1;
			$xls->addRow($i++, array('运单号','转运单号','发货人',"发货人\n电话","发货\n地址","发货\n国家",'收货人',"收货人\n电话","收货\n地址","收货\n省份",'收货人身份证号码',"包裹\n件数","包裹\n重量","包裹价格\n（人民币）",'内物品名','备注'));
			foreach($shipments as $sid){
				$p = ExParcel::model()->findByPk($sid);

				$weight = empty($p->mdata['dcwt'])? $p->shipWeight() : $p->mdata['dcwt'];
				$weight = ($weight < 1)? 1 : $weight;
				$weight = round($weight * 100) /100;

				/*if(preg_match('/^PBS\d+/', $p->hbn)){
					$cnee = $p->randCnee($id);
				}else{
					$cnee = $p->cnee;
				}*/

				$xls->addRow($i++, array($p->hbn, $p->ref, $p->cnor->name, (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"'), (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), '澳大利亚', $cnee->name, '="'.$cnee->tel.'"', $cnee->getCnFullAddress(), $cnee->state, '="'.empty($cnee->cnid)? '': $cnee->cnid->no.'"', $p->pkg, $weight, $p->value, $p->GoodsNames(false ,', ', true), ''));
			}
			break;
			case 'CNFZ2':
			$of .= '-FZ.xlsx';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('申报单');
			$xls->addRow($i++, array('目的地：', 'XMN', '客户：', ''));
			$xls->addRow($i++, array('主提单号：', $model->awb, '航空件数：', $model->totPacks()));
			$xls->addRow($i++, array('航班号：', $model->flight));
			$xls->addRow($i++, array('航班日期：', $model->etd));

			//$xls->addRow($i++, array('起运地', 'SYD', '提单号', $model->no, '运输工具名称', $model->flight, '航班号', $model->flight, '进出口日期', date('Y-m-d'), '申报日期', date('Y-m-d')));
			$xls->addRow($i++, array('序号', '分运单号', '货物品名', '件数', '毛重（提单重量）', '净重（实际重量）', '数量', '实际数量', '单位', '货币编码', '单价', '个人完税税号', '型号', '国别代码', '原产国', 'HS编码', '收件人ID', '收件人', '地址', '收件人电话', 'TO', '落地配单号', '寄件人公司', '寄件人', '寄件人地址', '寄件人电话', 'FROM', '货主城市'));

			$pc = 1;
			$srows = [];
			foreach ($model->shipments as $p) {
				$p->altGoods();
				$p->altCnee();
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$cnee = $p->cnee->name;

				if(empty($p->mdata['AltCnee'])){
					if(!empty($p->cnee->cnid_id)){
						$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
					}
				}else{
					$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
				}

				$tnt = 0;
				if(is_array($p->eitems['w'])){
					foreach($p->eitems['w'] as $gi => $w){
						if(empty($w)) continue;
						$tnt += $w;
					}
				}
				$swt = $p->shipWeight(true);

				if($tnt <= 0 || $tnt > $swt) $tnt = min($swt * 0.9, $swt - 0.2);
				if($tnt <= 0) $tnt = 0.1;
				
				foreach ($p->eitems['g'] as $gi => $g){
					$pd = empty($p->eitems['pid'][$gi])? false : ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = floatval($p->eitems['q'][$gi]) == 0 ? 1 : floatval(trim($p->eitems['q'][$gi]));
					if(empty($pd)){
						$v = floatval($p->eitems['v'][$gi]);
					}else{
						$v = floatval(empty($pd->mdata['price_CNFZ2'])? $pd->price : $pd->mdata['price_CNFZ2']);
						if(!empty($p->mdata['altItems']['v'][$gi])) $v = $p->mdata['altItems']['v'][$gi];
					}
					if(in_array($p->eitems['type'][$gi], ['B', 'M'])){
						$wqt = floatval(empty($p->eitems['w'][$gi])? 0.9*$qty : $p->eitems['w'][$gi]);
						if(empty($wqt)) die($p->hbn.' invalid product weight');
						$v = round($v / $wqt * $qty * 10) / 10;
						$u = '千克';
						$gu = $p->eitems['type'][$gi] == 'B' || preg_match('/小安素|Pedisure|蓝胖子|Maxigenes/', $g)? '罐' : '袋';
						$mo = $p->eitems['b'][$gi] . '-' . ($pd->weight * 1000). 'g/'.$gu;

						if($p->eitems['type'][$gi] == 'B'){
							$g = str_replace(' ', '', $g);
							$g = str_replace('奶粉', '婴儿奶粉', $g);
							if($p->eitems['b'][$gi] == '爱他美') $p->eitems['b'][$gi] = 'Aptamil';
						}
					}else{
						$wqt = $qty;
						$u = $p->eitems['u'][$gi];
						$mo = $p->eitems['b'][$gi].'-'.$p->eitems['m'][$gi];
						$gu = $u;
					}

					$g = $p->eitems['b'][$gi].str_replace($p->eitems['b'][$gi], '', $g).'*'.$qty.$gu;
					$xls->addRow($i++, array(($gi == 0? $pc++ : ''), '="'.$p->ref.'"', $g, 1, round(floatval($p->eitems['w'][$gi]) / $tnt * $swt, 2), $p->eitems['w'][$gi], $wqt, $qty, $u, 'RMB', $v, '="'.$p->eitems['hs'][$gi].'"', $p->eitems['b'][$gi].'牌, '.$p->eitems['m'][$gi], '601', '澳大利亚', empty($pd)? '' : '="'.$pd->hs2.'"', '="'.$cnid.'"', $p->cnee->name, $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"',  $p->cnee->state, '="'.$p->ref.'"', '', empty($p->cnor->name) ? 'PCA Express' : preg_replace('/发(件|货)人[：\s]*/', '', $p->cnor->name), (empty($p->cnor->address) ? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), '="'.$p->cnor->tel.'"', 'SYD', $p->cnee->city, '', '邮政小包'));
				}
				//$srows[] = [$p->cnee->name, $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"', '="'.$p->ref.'"', '="'.$p->shipWeight().'"', $p->GoodsNames()];
			}
			break;
			case 'CNJJI':
			$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.(empty($model->awb)? '' : '_'.$model->awb).'_x2.zip';
			$zip = new ZipArchive;
			$zip->open($zf, ZipArchive::CREATE);
			$of .= '-JJ.xlsx';
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('申报单');
			$xls->addRow($i++, array('厦门东港国际运输有限公司——申报清单(包裹)'));
			$xls->addRow($i++, array('目的地：', '晋江', '客户：', 'DG301'));
			$xls->addRow($i++, array('主提单号：', $model->awb, '航空件数：', $model->totPacks()));
			$xls->addRow($i++, array('航班号：', $model->flight));
			$xls->addRow($i++, array('航班日期：', $model->etd));

			$xls->addRow($i++, array('起运地', 'SYD', '提单号', $model->no, '运输工具名称', $model->flight, '航班号', $model->flight, '进出口日期', date('Y-m-d'), '申报日期', date('Y-m-d')));
			$xls->addRow($i++, array('序号', '分运单号', '货物品名', '件数', '重量KG', '数量', '单位', '货币编码', '价格', '个人完税税号', '型号', '国别代码', '原产国城市', 'HS编码', '收件人ID', '收件人', '地址', '收件人电话', 'TO', '落地配单号', '寄件人公司', '寄件人', '寄件人地址', '寄件人电话', 'FROM', '货主城市', '报关方式', '快递公司'));

			$pc = 1;
			$srows = [];
			foreach ($model->shipments as $p) {
				$p->altGoods();
				$p->altCnee();
				if(empty($p->cnee->cnid_id)){
					$cnid = empty($p->cnee->cnid_no)? '' : $p->cnee->cnid_no;
				}else{
					$cnid = $p->cnee->cnid->no;
				}
				$cnee = $p->cnee->name;

				if(empty($p->mdata['AltCnee'])){
					if(!empty($p->cnee->cnid_id)){
						$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
					}
				}else{
					$bld = implode(', ', AppHelper::bwf2warning($p->cnee->cnid, false));
				}
				
				foreach ($p->eitems['g'] as $gi => $g){
					$pd = empty($p->eitems['pid'][$gi])? false : ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
					$qty = floatval($p->eitems['q'][$gi]) == 0 ? 1 : floatval(trim($p->eitems['q'][$gi]));
					if(empty($pd)){
						$v = floatval($p->eitems['v'][$gi]);
					}else{
						$v = floatval(empty($pd->mdata['price_CNJJI'])? $pd->price : $pd->mdata['price_CNJJI']);
						if(!empty($p->mdata['altItems']['v'][$gi])) $v = $p->mdata['altItems']['v'][$gi];
					}
					if(in_array($p->eitems['type'][$gi], ['B', 'M'])){
						$wqt = floatval(empty($p->eitems['w'][$gi])? 0.9*$qty : $p->eitems['w'][$gi]);
						if(empty($wqt)) die($p->hbn.' invalid product weight');
						$v = round($v / $wqt * $qty * 10) / 10;
						$u = '千克';
						$mo = $p->eitems['b'][$gi] . '-' . ($pd->weight * 1000). 'g/'.($p->eitems['type'][$gi] == 'B' || preg_match('/小安素|Pedisure|蓝胖子|Maxigenes/', $g)? '罐' : '袋');
						if($p->eitems['type'][$gi] == 'B'){
							$g = str_replace(' ', '', $g);
							$g = str_replace('奶粉', '婴儿奶粉', $g);
							if($p->eitems['b'][$gi] == '爱他美') $p->eitems['b'][$gi] = 'Aptamil';
						}
					}else{
						$wqt = $qty;
						$u = $p->eitems['u'][$gi];
						$mo = $p->eitems['b'][$gi].'-'.$p->eitems['m'][$gi];
					}

					$g = $p->eitems['b'][$gi] . '-' .str_replace($p->eitems['b'][$gi], '', $g);
					if($gi == 0){
						$xls->addRow($i++, array($pc++, '="'.$p->ref.'"', $g, 1, $p->eitems['w'][$gi], $qty, $u, 'RMB', $v, '="'.$p->eitems['hs'][$gi].'"', $p->eitems['b'][$gi].'牌, '.$p->eitems['m'][$gi], '601', '悉尼', empty($pd)? '' : '="'.$pd->hs2.'"', '="'.$cnid.'"', $p->cnee->name, $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"',  $p->cnee->state, '="'.$p->ref.'"', '', empty($p->cnor->name) ? 'PCA Express' : preg_replace('/发(件|货)人[：\s]*/', '', $p->cnor->name), (empty($p->cnor->address) ? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), '="'.$p->cnor->tel.'"', 'SYD', $p->cnee->city, '', '邮政小包'));
					}else{
						$xls->addRow($i++, array('', '', $g, '', $p->eitems['w'][$gi], $qty, $u, 'RMB', $v, '="'.$p->eitems['hs'][$gi].'"', $p->eitems['b'][$gi].'牌, '.$p->eitems['m'][$gi], '601', '悉尼', empty($pd)? '' : '="'.$pd->hs2.'"'));
					}
				}
				$srows[] = [$p->cnee->name, $p->cnee->getCnFullAddress(), '="'.$p->cnee->tel.'"', '="'.$p->ref.'"', '="'.$p->shipWeight().'"', $p->GoodsNames()];
			}

			$zip->addFromString($model->no.'_申报单.xlsx', $xls->output(false, 'Excel2007', false));

			//list file
			$xls = new oExcel;
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('装箱单');
			$i = 1;
			$xls->addRow($i++, ['收件人', '收件人地址', '收件人电话', '邮政单号', '重量', '货物品名']);
			foreach($srows as $r){
				$xls->addRow($i++, $r);
			}
			$zip->addFromString($model->no.'_装箱单.xlsx', $xls->output(false, 'Excel2007', false));
			$zip->close();

			header("Cache-Control: maxage=1");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header('Content-Disposition: attachment; filename="'.basename($zf).'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($zf));
			readfile($zf);
			unlink($zf);
			Yii::app()->end();
			break;
		}

		$xls->output($of);
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

		if(isset($_POST['ExcoConsol'])){
			$model->attributes=$_POST['ExcoConsol'];
			$err = false;
			$msg = false;
			if(!empty($_POST['confirm'])){
				/*function emsCheckSum($a){
					$a = str_pad((string) $a, 8, "0", STR_PAD_LEFT);
					$cs = $a[0] * 8 + $a[1] * 6 + $a[2] * 4 + $a[3] * 2 + $a[4] * 3 + $a[5] * 5 + $a[6] * 9 + $a[7] * 7;
					$cs = 11 - ($cs % 11);
					if($cs == 10) $cs = 0;
					if($cs == 11) $cs = 5;
					return $a.$cs;
				}

				function newEmsNo(&$pool, $b=false){
					$n = '';
					foreach($pool as $k=>$rg){
						if($rg[1] == $rg[2]) continue;
						if(!$b || $rg[0] == $b){
							$n = $rg[0].emsCheckSum($rg[1]).$rg[3];
							$pool[$k][1]++;
							break;
						}
					}
					return $n;
				}

				$fp = null;

				function readLock(&$fp, $cf){
					if(function_exists('opcache_invalidate')) opcache_invalidate($cf);
					$fp = fopen($cf, 'r+');
					if(flock($fp, LOCK_EX | LOCK_NB)){
						//sleep(5);
						return include($cf);
					} else {
						$model = new ExcoConsol;
						$model->addError('no', 'Connotes locked due to another consol is been confirmed, please try again later.');
						$c = Yii::app()->controller;
						$c->ajaxResult($model);
					}
				}

				function writeUnlock(&$fp, $d){
					ftruncate($fp, 0);
					if(!defined('YII_TEST')) fwrite($fp, $d);
					fflush($fp);
					flock($fp, LOCK_UN);
				}*/

				$cs = ExChannel::model()->find('code = :poc', [':poc' => $model->poc]);

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
					if(in_array($model->poc, ['CNHAK', 'CNHA2'])){
						$api = new HyhtAPI('99.074;海龟', 'hyht', false);
						$res = $api->getNumber($model->shipments);
						$temp = [];
						foreach ($res as $r) {
							$temp[$r['txLogisticID']] = $r;
						}
					}
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
								Yii::app()->db->createCommand('UPDATE origin_trace SET pid = '.$p->id.' WHERE pid = 0 LIMIT 1')->execute();
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
		$pid = Yii::app()->cache->get('pid_'.md5(Yii::app()->getSession()->getSessionId().$_GET['type']));
		if(!empty($pid)) return false;
		Yii::app()->cache->set('pid_'.md5(Yii::app()->getSession()->getSessionId().$_GET['type']), 1, 300);

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
				/*if($model->poc == 'CNCTU'){
					$prmap = [
						'A2婴儿奶粉3段900g' => 'A2_3段.jpg',
						'A2成人奶粉' => 'A2_成人.jpg',
						'徳运全脂奶粉' => '德运.jpg',
						'徳运脱脂奶粉' => '德运.jpg',
						'DEVONDALE 成人奶粉' => '德运.jpg',
						'Aptamil金装婴儿奶粉4段900g' => '爱他美_4段.jpg',
						'Aptamil金装婴儿奶粉3段900g' => '爱他美_3段.jpg',
						'bellamy\'s婴儿奶粉3段900g' => '贝拉米_3段.jpg',
						'bellamys婴儿奶粉3段900g' => '贝拉米_3段.jpg',
						'雀巢 三段' => '雀巢_3段.jpg',
						'Maxigenes全脂成人奶粉' => '蓝胖子.jpg',
						'Karicare婴儿奶粉3段900g' => '可瑞康_3段.jpg',
						'Karicare婴儿奶粉4段900g' => '可瑞康_4段.jpg',
						'S26金装婴儿奶粉4段900g' => 'S-26_3段.jpg',
						'S26金装婴儿奶粉3段900g' => 'S-26_3段.jpg',
						'COLES成人奶粉' => 'Coles奶粉.jpg',
					];
					foreach($model->shipments as $p){//gen receipt
						$p->altGoods();
						$pn = $p->eitems['g'][0];
						$pd = ExProdb::model()->findByPk($p->eitems['pid'][0]);
						if(!empty($pd->mdata['name_CNCTU'])) $pn = $pd->mdata['name_CNCTU'];
						if(!empty($prmap[$pn])){
							$zip->addFile(Yii::app()->basePath.DIRECTORY_SEPARATOR.'cd_rcpt'.DIRECTORY_SEPARATOR.$prmap[$pn], (empty($p->ref)? $p->hbn : $p->ref).'.jpg');
						}
					}
					break;
				}*/

				/*$omf = Yii::app()->basePath.DIRECTORY_SEPARATOR.$model->awb.'.xlsx';
				if(!is_file($omf)) $omf = Yii::app()->basePath.DIRECTORY_SEPARATOR.$model->awb.'.xls';
				if(is_file($omf)){
					if(in_array($model->poc, ['CNJMN', 'CNJM2'])){
						$odata = oExcel::getAllData($omf);
						$pdow = [];
						foreach($odata as $odr){
							$pdow[$odr[4]][] = $odr;
						}
					}
				}

				$por = Yii::app()->basePath.DIRECTORY_SEPARATOR.$model->awb.'_price.php';
				if(is_file($por)) $pdow = include($por);
				*/
				
				foreach($model->shipments as $p){//gen receipt
					$d = $p->receiptData();
					$p->altGoods();
					/*if($model->poc == 'CNCA3'){
						if(!empty($p->eitems['pid'])){
							foreach($p->eitems['pid'] as $i => $t){
								$ep = ExProdb::model()->findByPk($p->eitems['pid'][$i]);
								if(!empty($ep->mdata['price_CNCA3'])){
									$p->eitems['t'][$i] = $ep->mdata['price_CNCA3'];
								}
							}
						}
					}
					if(!empty($pdow[$p->ref])){
						if(in_array($model->poc, ['CNJMN', 'CNJM2'])){
							foreach($pdow[$p->ref] as $oi => $odr){
								if(preg_match('/^(.+)([1234]{1}段)婴儿奶粉$/', $odr[5], $m)){
									$pd = ExProdb::model()->find('type = 10 AND brand = :b AND model = :m', [':b' => $m[1], ':m' => str_replace(['1', '2', '3', '4'], ['一', '二', '三', '四'], $m[2])]);
									$odr[12] = $odr[12] * $pd->weight;
								}else{
									$pd = ExProdb::model()->findByPk($p->eitems['pid'][$oi]);
								}

								if(!empty($pd)){
									$p->eitems['pid'][$oi] = $pd->id;
									if($pd->mdata['price_CNJMN'] != $odr[12]){
										$pd->mdata['price_CNJMN'] = $odr[12];
										$pd->noaup = true;
										$pd->save();
									}
								}
								if(preg_match('/休闲鞋/', $odr[5], $m)){
									$p->eitems['gen'][$oi] = 'Sports/Leisure Footware';
								}
							}
						}else{
							foreach($pdow[$p->ref] as $oi => $v){
								$p->eitems['pid'][$oi] = 0;
								$p->eitems['t'][$oi] = $v;
							}
						}
					}*/
									  
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
			$items = json_decode($r['items'], true);
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
		$model=new ExcoConsol('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ExcoConsol']))
			$model->attributes=$_GET['ExcoConsol'];
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
		$model=ExcoConsol::model()->findByPk($id);
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
