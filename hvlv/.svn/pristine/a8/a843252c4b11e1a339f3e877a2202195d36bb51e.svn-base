<?php
class ScanController extends PController{
	//runner scans
	public function actionIndex(){
		if(preg_match('/scan$/',$_SERVER['REQUEST_URI'])){
			$this->redirect('scan/');
		}
		$this->renderPartial('index');
	}

	public function actionAuth(){
		$g = $this->getScanner($_GET['sn'], $_GET['md']);
		echo json_encode($g);
	}

	public function actionLob(){
		if(!empty($_POST['bc'])){
			$o = new StdClass;
			$o->bc = strtoupper(trim($_POST['bc']));

			$model = $this->getParcel($_POST['bc']);
			if($model && $model->status <= 70){
				if($model->status < 70){
					$model->status = 70;
					$model->save();
				}
				$scanner = $this->getScanner($_GET['sn'], $_GET['md']);
				$t = $model->addTracking(80, 'Loaded onboard for delivery', 'Botany', '', 0, $scanner->id);
				$o->nf = 0;
				$o->ti = $t->id;
			}else{
				$o->nf = 1;
			}
			echo json_encode($o);
			Yii::app()->end();
		}
		$this->renderPartial('lob');
	}
	
	public function actionUndoLob(){
		if(!empty($_GET['bc'])){
			$o = new StdClass;
			$model = $this->getParcel($_GET['bc']);
			if($model && $model->status == 70){
				$scanner = $this->getScanner($_GET['sn'], $_GET['md']);
				$trk = Tracking::model()->findByPk($_GET['tid']);
				if(strtotime($trk->dt) < time() - 1800){
					$o->msg = 'Sorry, undo expired.';
				}else{
					if($trk && $trk->srid == $scanner->id && $trk->pid == $model->id){
						$trk->delete();
						$o->msg = 'Undo LOB: '.$_GET['bc'];
					}
				}
			}else{
				$o->msg = ' not found!';
			}
			echo json_encode($o);
		}
	}	

	public function actionPod()	{
		if(!empty($_POST['bc'])){
			$o = new StdClass;
			$o->bc = trim($_POST['bc']);
			$model = $this->getParcel($_POST['bc']);
			if($model && $model->status == 70){
				$sig = empty($_POST['sig'])? '' : str_replace('data:image/png;base64,', '', $_POST['sig']);
				$scanner = $this->getScanner($_GET['sn'], $_GET['md']);
				$t = $model->addTracking(90, 'POD signed by '.$_POST['sname'], $model->state.' '.$model->postcode, '', 0, $scanner->id, json_encode(array('signature' => $sig)));
				if($model->status < 90){
					$model->status = 90;
					$model->save();
				}
				$o->nf = 0;
				$o->by = $_POST['sname'];
			}else{
				$o->nf = 1;
			}
			echo json_encode($o);
			Yii::app()->end();
		}
		$this->renderPartial('pod');
	}

	public function actionMyc(){
		if(!empty($_POST['bc'])){
			$o = new StdClass;
			$o->bc = trim($_POST['bc']);
			$model = $this->getParcel($_POST['bc']);
			if($model && $model->status == 70){
				$scanner = $this->getScanner($_GET['sn'], $_GET['md']);
				$t = $model->addTracking(85, 'Missed you card left', $model->state.' '.$model->postcode, '', 0, $scanner->id);
				$o->nf = 0;
				$o->ti = $t->id;
			}else{
				$o->nf = 1;
			}
			echo json_encode($o);
			Yii::app()->end();
		}
		$this->renderPartial('myc');
	}
	
	public function actionUndoMyc(){
		if(!empty($_GET['bc'])){
			$model = $this->getParcel($_GET['bc']);
			$o = new StdClass;
			if($model && $model->status == 70){
				$scanner = $this->getScanner($_GET['sn'], $_GET['md']);
				$trk = Tracking::model()->findByPk($_GET['tid']);
				if(strtotime($trk->dt) < time() - 1800){
					$o->msg = 'Sorry, undo expired.';
				}else{
					if($trk && $trk->srid == $scanner->id && $trk->pid == $model->id){
						$trk->delete();
						$o->msg = 'Undo MYC: '.$_GET['bc'];
					}
				}
			}else{
				$o->nf = 1;
			}
			echo json_encode($o);
		}
	}

	public function actionPub(){
		if(!empty($_POST)){
			$o = new StdClass;
			$o->bc = empty($_POST['bc'])? '' : strtoupper(trim($_POST['bc']));
			$o->nf = 1;
			$scanner = $this->getScanner($_GET['sn'], $_GET['md']);

			if(!empty($_POST['sig'])){
				$o->nf = 0;
				$o->mid = $_POST['mid'];
				$man = Manifest::model()->findByPk($_POST['mid']);
				if($man){
					$man->mdata['sig'] = $_POST['sig'];
					$man->mdata['box'] = $_POST['box'];
					$man->save();
				}
			}elseif(!empty($_POST['mid'])){
				$o->nf = 0;
				$o->bcs = [];
				$o->mid = $_POST['mid'];
				$man = Manifest::model()->findByPk($_POST['mid']);
				foreach($_POST['bcs'] as $bc){
					if(preg_match('/^AGT\-0*(\d+)\-PUS$/', $bc)){
						$o->bcs[$bc] = [9];
						continue;
					}
					$model = $this->getParcel($bc);
					$o->bcs[$bc] = [2];
					if(empty($model)){
						$model = new ExParcel;
						$model->hbn = strtoupper($bc);
						$model->status = 12;
						$model->agent_id = $man->fwd_id;
						$cnor = new Addr;
						$cnor->country = 'Australia';
						$cnor->save();
						$model->cnor_id = $cnor->id;
						$cnee = new Addr;
						$cnee->country = 'PR China';
						$cnee->save();
						$model->cnee_id = $cnee->id;
						$model->save();
					}elseif(in_array($model->status, [9,10,100])){
						$model->status = 12;
						$model->save();
					}else{
						if(!$man->hasMap($model)) $o->bcs[$bc] = [9];
					}

					if($o->bcs[$bc][0] != 9){
						$man->map($model);
						$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $model->id, ':t' => 12));
						if(empty($ht)){
							$t = $model->addTracking(12, 'Consignment Picked Up', '', '', 0, $scanner->id);
							$o->bcs[$bc][1] = $t->id;
						}
					}
				}

				$o->tt = $man->countLines();
			}elseif(!empty($_POST['agt'])){//create batch
				$agt = Org::model()->findByPk(preg_replace('/^(\d+):.*$/', '\\1', $_POST['agt']));
				if($agt){
					$ref = $agt->id.'-'.date('ymdH');
					$model = Manifest::model()->find([
						'condition' => 'ref LIKE :r',
						'params' => [':r' => substr($ref,0,-2).'%'],
						'order' => 'ref DESC'
					]);
					$exp = false;
					if(!empty($model->ref)){
						list($a, $h) = explode('-', $model->ref);
						$h = preg_replace('/(\d{6})(\d{2})/', '20\\1 \\2:00', $h);
						$exp = (time() - strtotime($h)) > 14400;
					}
					if(!$model || $exp){
						$model = new Manifest('create');
						$model->type = 40;
						$model->fwd_id = $agt->id;
						$model->ref = $ref;
						$model->by_id = $scanner->user_id;
						$model->dpt_id = $scanner->wh_id;
						$model->mdata['scanner'] = $scanner->id;
						if(!empty($agt->extra['sergra'])){
							$model->mdata['sergra'] = $agt->extra['sergra'];
						}
						$model->save();
					}
					$o->mi = $model->id;
					$o->ref = $model->ref;
					$o->nf = 0;
				}
			}

			echo json_encode($o);
			Yii::app()->end();
		}
		$this->renderPartial('pub');
	}
	
	public function actionUndoPub(){
		if(!empty($_GET['bc'])){
			$o = new StdClass;
			$o->bc = trim($_GET['bc']);
			$model = $this->getParcel($o->bc);
			if($model && $model->status == 12){
				$scanner = $this->getScanner($_GET['sn'], $_GET['md']);
				$trk = Tracking::model()->findByPk($_GET['tid']);
				if(strtotime($trk->dt) < time() - 3600){
					$o->msg = 'Sorry, undo expired.';
				}else{
					if($trk && $trk->srid == $scanner->id && $trk->pid == $model->id) $trk->delete();
					$model->status = 100;
					$model->save();
					$o->msg = 'Undo PUB: '.$_GET['bc'];
					$ms = ManiMap::belong2($model);
					foreach($ms as $m){
						if($m->manifest->type == 40){
							$o->tt = $m->manifest->countLines() - 1;
							$o->mid = $m->manifest->id;
							$m->delete();
							break;
						}
					}
				}
			}else{
				$o->nf = 1;
			}
			
			echo json_encode($o);
		}
	}
	
	public function actionPrintPub(){
		if(!empty($_GET['mid'])){
			$man = Manifest::model()->findByPk($_GET['mid']);
			$this->renderPartial('print_pub', ['man' => $man]);
		}
	}

	public function actionAgtSuggest(){
		$rs = Org::model()->findAll(array(
				'condition' => 'status = 1 AND type IN (60, 65) AND (id = :t OR name LIKE :n OR code LIKE :n)',
				'params' => array(':n' => '%'.$_GET['term'].'%', ':t' => $_GET['term']),
				'order' => 'name',
				'limit' => 20,
				));
		$a = array();
		
		foreach($rs as $r){
			$a[] = array(
				'value' => $r->id.':'.$r->code.' - '.$r->name,
			);
		}
		echo json_encode($a);
	}

	//warehouse scans
	public function actionWarehouse(){
		$this->renderPartial('warehouse');
	}

	public function actionWh_ot()	{
		if(!empty($_POST['bc'])){
			$o = new StdClass;
			$o->nf = 1;
			$o->bc = trim($_POST['bc']);
			
			//aupost barcode
			if(preg_match('/019931265099999891([\d\w]{3}|[\d\w]{5})(\d{7})(\d{2})(\d{5})(\d{2})0\d{1}/', $_POST['bc'], $m) || preg_match('/997\d{5}([\d\w]{3})(\d{7})(\d{2})(\d{4})0\d{4}/', $_POST['barcode'], $m)){
				$hbn = strtoupper($m[1].$m[2]);
				$sn = ltrim($m[3],'0');
				$p = $this->getParcel($hbn);
				$_POST['bc'] = strtoupper($hbn.$m[3]);
				if(empty($p)) $p = ImParcel::model()->find('ref = :r', [':r' => $hbn]);
			}else{
				if(strpos($o->bc, '-') > 0){
					list($hbn, $sn) = explode('-', $o->bc);
				}else{
					$hbn = $o->bc;
					$sn = 0;
				}
				$p = $this->getParcel($hbn);
			}
			if(empty($p) || empty($p->consol_id)){
				echo json_encode($o);
				Yii::app()->end();
			}
			if($p && $p->consol->type == 30){//local
					$o->msg = $p->getCarrier().', '.($p->zrate->orgrate->type == 20? 'Satchel, ':'').$p->getCarrierSeq($sn);
					$o->va = strtolower(str_replace(' ','_', $p->getCarrier()).'-'.($p->zrate->orgrate->type == 20? 'satchel-':'').$p->getCarrierSeq($sn));
					if($p->status < 65){
						$p->status = 65;
						$p->save();
					}
					$o->nf = 0;
					$s = Storage::allocate(empty($_POST['wid'])? 106 : $_POST['wid'], 50, $p);
			}elseif($p && $p->consol->type == 15){
				if($p->status < 50){
					$o->msg = 'HELD'.', '.$p->getCarrier().', '.($p->zrate->orgrate->type == 20? 'Satchel, ':'').$p->getCarrierSeq($sn);
					$o->va = strtolower('held-'.str_replace(' ','_', $p->getCarrier()).'-'.($p->zrate->orgrate->type == 20? 'satchel-':'').$p->getCarrierSeq($sn));
					$s = Storage::allocate(empty($_POST['wid'])? 106 : $_POST['wid'], 40, $p);
				}else{
					$o->msg = strtoupper($p->getStatus()).', '.$p->getCarrier().', '.($p->zrate->orgrate->type == 20? 'Satchel, ':'').$p->getCarrierSeq($sn);
					$o->va = strtolower($p->getStatus().'-'.str_replace(' ','_', $p->getCarrier()).'-'.($p->zrate->orgrate->type == 20? 'satchel-':'').$p->getCarrierSeq($sn));
					$s = Storage::allocate(empty($_POST['wid'])? 106 : $_POST['wid'], 50, $p);
				}
				$p->mdata['scan_time'] = date('Y-m-d H:i:s');
				$p->scan_data[50][strtoupper($_POST['bc'])] = date('Y-m-d H:i:s');
				$p->save();
				$o->nf = 0;
			}
			echo json_encode($o);
			Yii::app()->end();
		}
		$this->renderPartial('wh_ot');
	}

	public function actionWh_gp()	{
		if(isset($_POST['bc'])){
			$o = new StdClass;
			$o->nf = 1;
			
			//aupost barcode
			if(preg_match('/019931265099999891([\d\w]{3}|[\d\w]{5})(\d{7})(\d{2})(\d{5})(\d{2})0\d{1}/', $_POST['bc'], $m)){
				$_POST['bc'] = $m[1].$m[2].'-'.ltrim($m[3],'0');
			}

			$o->bc = trim($_POST['bc']);
			
			if(strpos($o->bc, '-') > 0){
				list($hbn, $sn) = explode('-', $o->bc);
			}else{
				$hbn = $o->bc;
				$sn = 0;
			}
			$p = $this->getParcel($hbn);

			if(empty($p) || empty($p->consol_id)){//try tranship no.
				$ts = Tranship::model()->find('connote = :hbn', [':hbn' => $hbn]);
				if($ts){
					$p = $ts->shipment;
					$o->bc = $p->hbn.' ('.$o->bc.')';
				}
			}

			if(empty($p) || empty($p->consol_id)){
				echo json_encode($o);
				Yii::app()->end();
			}
			if($p && $p->consol->type == 30){//local
					$o->msg = $p->getCarrier();
					$o->va = strtolower(str_replace(' ','_', $p->getCarrier()));
					$o->nf = 0;
					$o->id = $p->id;
			}elseif($p && $p->consol->type == 15){
				if($p->status < 50){
					$o->msg = 'HELD'.', '.$p->getCarrier();
					$o->va = strtolower('held-'.str_replace(' ','_', $p->getCarrier()));
				}else{
					$o->msg = strtoupper($p->getStatus()).', '.$p->getCarrier();
					$o->va = strtolower($p->getStatus().'-'.str_replace(' ','_', $p->getCarrier()));
					$o->id = $p->id;
				}
				$o->nf = 0;
			}
			echo json_encode($o);
			Yii::app()->end();
		}elseif(!empty($_POST['GatePass'])){
			$scanner = $this->getScanner($_GET['sn'], $_GET['md']);
			$m = new GatePass('create');
			$m->setAttributes($_POST['GatePass']);
			$m->sids = $_POST['sids'];
			$m->dpt_id = $scanner->wh_id;
			$m->by_id = $scanner->user_id;
			$m->save();

			$o = new StdClass;
			$o->ref = empty($m->ref)? $m->id : $m->ref;
			$o->by = $m->driver;
			$o->tot = $m->countLines();

			echo json_encode($o);
			Yii::app()->end();
		}

		$this->renderPartial('wh_gp');
	}

	public function actionWh_es(){
		$scanner = $this->getScanner($_GET['sn'], $_GET['md']);

		if(!empty($_POST['bc'])){
			$alloc = true;
			$savep = false;
			$o = new StdClass;
			$o->bc = trim($_POST['bc']);
			$p = $this->getParcel($o->bc);
			if(empty($p)){
				$p = new ExParcel('create');
				$p->hbn = $o->bc;
				$p->status = 14;
				$cnor = new Addr;
				$cnor->country = 'Australia';
				$cnor->save();
				$p->cnor_id = $cnor->id;
				$cnee = new Addr;
				$cnee->country = 'PR China';
				$cnee->save();
				$p->cnee_id = $cnee->id;
				$savep = true;
				$o->msg = 'New Item: '.$o->bc;
				$p->nolog = true;
				$p->save();
				$p->nolog = false;
			}else{
				$o->msg = "Found Shipment: ".$o->bc;
				
				if($p->status == 12){
					$p->type = 20;
					$p->status = empty($p->cnee->name)? 14 : 15;
					$savep = true;
				}elseif(in_array($p->status,[9,10])){
					$p->status = 15;
					$savep = true;
				}

				if($p->status >= 60 && $p->status < 100){
						$o->msg .= ', duplicate consignment';
						$o->va = 'warning-duplicate';
						$alloc = false;
				}else{
					$sl = StorageLog::findItem($p);
					if($sl){
						$o->msg .= ', already at '.$sl->storage->name;
						$o->va = 'already_at-'.AppHelper::codeToSound($sl->storage->name);
						$alloc = false;
					}
				}
			}

			if(empty($p->odpt_id)){// || $p->odpt_id != $scanner->wh_id
				$p->odpt_id = $scanner->wh_id;
				$savep = true;
			}

			if($alloc){
				$s = Storage::allocate($scanner->wh_id, 60, $p, $_POST['bsid']);
				if($s){
					$o->msg .= ', store at '.$s->name;
					$o->va = AppHelper::codeToSound($s->name);
					$p->custom_log_note = 'stored at '.$s->name;
					$savep = true;
				}else{
					$o->msg .= '. Unable to allocation storage space all full!';
					$o->va = 'storage_full';
				}
			}

			echo json_encode($o);
			if($savep) $p->save();

			Yii::app()->end();
		}
		$this->renderPartial('wh_es', array('scanner' => $scanner));
	}

	public function actionEsblSuggest(){
		$scanner = $this->getScanner($_GET['sn'], $_GET['md']);

		$rs = Storage::model()->findAll('status = 1 AND wid = :id AND type = 60 AND (name LIKE :t OR code LIKE :t) LIMIT 20', [':id' => $scanner->wh_id, ':t' => $_GET['term'].'%']);
		$a = array();
		foreach($rs as $r){
			$a[] = array(
				'value' => $r->name,
			);
		}
		echo json_encode($a);
	}

	public function actionWh_sc(){
		$scanner = $this->getScanner($_GET['sn'], $_GET['md']);
		if(!empty($_POST['bc'])){
			$o = new StdClass;
			$o->bc = trim($_POST['bc']);
			$s = Storage::model()->find('wid = :wid AND status = 1 AND type = 60 AND name = :n OR code = :n', [':wid' => $scanner->wh_id, ':n' => $_POST['loc']]);
			$p = $this->getParcel($o->bc);
			
			function vacant($s){
				if($s->cap_item == 1 && !empty($s->items)){
					$sl = $s->items[0];
					$sl->out();
					$m = new $sl->model;
					$p = $m::model()->findByPk($sl->fid);
					$p->custom_log_note = 'moved out '.$s->name;
					$p->save();
					return '<br />'.$p->hbn.' moved out '.$s->name;
				}
				return '';
			}

			if($s && $p){
				$o->nf = 0;
				$sl = StorageLog::findItem($p);
				if(empty($sl)){ //no location
					$o->msg = ' new to '.$_POST['loc'];
					$o->msg .= vacant($s);
					$s->addItem($p,1);
					$p->custom_log_note = 'stored at '.$_POST['loc'];
					if(empty($p->odpt_id)) $p->odpt_id = $scanner->wh_id;
					if($p->status < 14) $p->status = empty($p->cnee->name)? 14 : 15;
					$p->save();
				}elseif($_POST['loc'] != $sl->storage->name){
					$o->msg = $sl->storage->name.' to '.$_POST['loc'];
					$o->msg .= vacant($s);
					$o->msg .= vacant($sl->storage);
					$s->addItem($p,1);
					$p->custom_log_note = 'moved to '.$_POST['loc'];
					$p->save();
				}else{
					$sl->ckd = 1;
					$sl->save();
					$o->msg = 'ok';
				}
			}else{
				$o->nf = 1;
			}
			echo json_encode($o);
			Yii::app()->end();
		}
		$this->renderPartial('wh_sc', array('scanner' => $scanner));
	}

	public function actionWhsc_empt(){
		if(empty($_POST['emp'])){
			$rs = StorageLog::model()->findAll('out_dt IS NULL and ckd = 0');
			foreach($rs as $r){
				$m = new $r->model;
				$p = $m::model()->findByPk($r->fid);
				if(empty($r->storage) || empty($p)) continue;
				echo '<input type="checkbox" class="emp" name="emp" value="'.$r->id.'" checked /> '.$r->storage->name.' '.$p->hbn.'<br />';
			}
		}else{
			$emps = [];
			foreach($_POST['emp'] as $i){
				$emps[] = (int) $i;
			}
			$sql = "UPDATE storage_log SET out_dt = NOW() WHERE out_dt IS NULL AND ckd = 0 AND id IN (".implode(',', $emps).")";
			Yii::app()->db->createCommand($sql)->execute();
			$sql = "UPDATE storage_log SET ckd = 0 WHERE ckd = 1";
			Yii::app()->db->createCommand($sql)->execute();
		}
	}

	public function actionWh_et(){
		$scanner = $this->getScanner($_GET['sn'], $_GET['md']);

		if(!empty($_POST)){
			$o = new StdClass;
			$o->bc = $_POST['b1'].' <=> '.$_POST['b2'];
			$p = ExParcel::model()->find('consol_id = :cid AND ((hbn = :b1 AND ref = :b2) OR (hbn = :b2 AND ref = :b1))', [':cid' => $_POST['cid'], ':b1' => $_POST['b1'], ':b2' => $_POST['b2']]);
			$o->nf = 0;
			$o->msg = 'ok';
			
			if(!$p){
				$o->msg = 'mismatch';
				$o->va = 'mismatch';
			}
			echo json_encode($o);
			Yii::app()->end();
		}
		$this->renderPartial('wh_et', array('scanner' => $scanner));
	}

	protected function whecTally($id){
		$m = Manifest::model()->findByPk($id);
		$tw = 0;
		$ids = empty($m)? [] : $m->getFids();
		if(!empty($ids)){
			$sql = 'SELECT SUM(weight) FROM shipment WHERE id IN('.implode(',', $ids).')';
			$tw = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		return 'Count: '.sizeof($ids).' Weight: '.(round($tw*100)/100).'kg';
	}

	public function actionWh_ep(){
		$scanner = $this->getScanner($_GET['sn'], $_GET['md']);

		if(!empty($_POST['bc'])){
			$o = new StdClass;
			$o->bc = trim($_POST['bc']);
			$p = ExParcel::model()->find('hbn = :hbn', [':cid' => $_POST['cid'], ':hbn' => $_POST['bc']]);

			$o->nf = 0;
			$o->msg = 'ok';
			
			if(!$p){
				$o->nf = 1;
			}
			echo json_encode($o);
			Yii::app()->end();
		}
		$this->renderPartial('wh_ep', array('scanner' => $scanner));
	}

	public function actionWh_ec(){
		$scanner = $this->getScanner($_GET['sn'], $_GET['md']);

		if(!empty($_GET['newPallet'])){
			$model = new Manifest('create');
			$model->type = 60;
			$model->ref = sprintf('%02d', (Manifest::model()->count('type = 60 AND consol_id = :cid', [':cid' => $_GET['newPallet']]) + 1));
			$model->consol_id = $_GET['newPallet'];
			$model->by_id = $scanner->user_id;
			$model->save();
			Yii::app()->end();
		}elseif(!empty($_GET['getPallets'])){
			echo '<option value="">##</option>';
			$rs = Manifest::model()->findAll('type = 60 AND consol_id = :cid', [':cid' => $_GET['getPallets']]);
			foreach($rs as $r){
				echo '<option value="'.$r->id.'">'.$r->ref.'</option>';
			}
			Yii::app()->end();
		}elseif(!empty($_GET['getTally'])){
			echo $this->whecTally($_GET['getTally']);
			Yii::app()->end();
		}elseif(!empty($_POST['bc']) && !empty($_POST['pid'])){
			$o = new StdClass;
			$o->bc = trim($_POST['bc']);
			$o->pid = $_POST['pid'];
			$o->nf = 0;
			$o->dw = 0;
			$o->sid = 0;
			$man = Manifest::model()->findByPk($o->pid);
			$o->pn = $man->ref;
			$csl = ExcoConsol::model()->findByPk($_POST['cid']);
			if(in_array($csl->poc, ['CNCTU', 'CNCT2', 'HKHKG', 'STO'])){
				$model = ExParcel::model()->find('hbn = :h', array(':h' => $o->bc));
			}else{
				$model = ExParcel::model()->find('ref = :h', array(':h' => $o->bc));
			}
			if(empty($model) || $model->consol_id != $_POST['cid']){
				$o->nf = 1;
			}else{
				$o->sid = $model->id;
				$mpd = $man->hasMap($model);
				if($mpd){
					$o->dw = 1;
					$o->va = 'beep_warn';
				}else{
					$rs = Manifest::model()->findAll('type = 60 AND consol_id = :cid AND id != :mid', [':cid' => $_POST['cid'], ':mid' => $o->pid]);
					if(!empty($rs)){
						foreach($rs as $r){
							if($r->hasMap($model)){
								$o->dw = 2;
								$o->va = 'beep_err';
								$o->pid = $r->id;
								$o->pn = $r->ref;
								break;
							}
						}
					}

					if($o->dw == 0){
						$man->map($model);
						$o->tt = $this->whecTally($o->pid);
					}
				}
			}
			echo json_encode($o);
			Yii::app()->end();
		}
		$this->renderPartial('wh_ec', array('scanner' => $scanner));
	}
	
	public function actionUndoWhEc(){
		if(!empty($_GET['pid'])){
			$o = new StdClass;
			$o->pid = $_GET['pid'];
			$o->bc = $_GET['bc'];
			$o->nf = 0;
			$model = ExParcel::model()->find('ref = :h', array(':h' => $o->bc));
			if(!$model) $model = ExParcel::model()->find('hbn = :h', array(':h' => $o->bc));
			
			$man = ManiMap::model()->find('fid = :fid AND mani_id = :mid', [':fid' => $model->id, ':mid' => $o->pid]);
			if($man) $man->delete();
			$o->tt = $this->whecTally($o->pid);
			$o->msg = 'Undo EC '.$o->bc;
			
			echo json_encode($o);
		}
	}
	
	
	private function getParcel($h){
		return Shipment::model()->find('hbn = :h', array(':h' => $h));
	}
	
	public function getScanner($sn, $m){
		return Scanner::model()->find('serial = :s AND model = :m', array(':s' => $sn, ':m' => $m));
	}

}