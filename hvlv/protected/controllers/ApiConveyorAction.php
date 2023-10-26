<?php
class ApiConveyorAction extends CAction {
	public $ctlr, $debug;
	public $logging = true;

	public function run() {
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);

		if(!empty($this->ctlr->data->action) && method_exists($this, $this->ctlr->data->action)){
			$this->log($this->ctlr->data->action);
			$this->{$this->ctlr->data->action}();
		}else{
			throw new CHttpException(400, 'API method not found!');
		}
	}

	public function expCheckIn(){
		$d = $this->ctlr->data;

		if(!empty($d->shipments)){
			$trans = Yii::app()->db->beginTransaction();
			try{
				foreach($d->shipments as $s){
					$this->_expReceive($s);
				}
				$trans->commit();
			} catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
		}
		
		$this->ctlr->output(true);
	}

	protected function _expReceive($s){
		$d = $this->ctlr->data;
		$p = Shipment::model()->find('hbn = :n OR (type = 25 AND cref = :n)', array(':n' => $s->hbn));
		$this->log('Rcvd '.$s->hbn);
		$alloc = true;
		$savep = false;

		if(empty($p)){
			$p = ExParcel::model()->find('ref = :hbn', [':hbn' => $s->hbn]);
			if(!empty($p)) return;
			$p = new ExParcel('create');
			$p->hbn = $s->hbn;
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
		}else{
			if($p->type == 20){
				$p = ExParcel::model()->findByPk($p->id);
				if($p->status == 12){
					$p->status = empty($p->cnee->name)? 14 : 15;
					$savep = true;
				}elseif(in_array($p->status, [8,9,10])){
					$p->status = 15;
					$savep = true;
				}elseif(in_array($p->status, [100, 104])){
					$p->status = 101;
					$savep = true;
				}
				
				$sl = StorageLog::findItem($p);
				if($sl){
					$alloc = false;
					if($sl->storage->wid != $d->wid){
						$sl->out();
						$alloc = true;
					}
				}
				if((empty($p->weight) || floatval($p->weight) == 0) && !empty($s->wt)){
					$p->weight = $s->wt;
        			$p->update(['weight']);
				}
				//if($p->status >= 60 && $p->status < 101) //duplicate
			}elseif($p->type == 25){
				$p = ExAfs::model()->findByPk($p->id);
				$alloc = false;
				if((empty($p->weight) || floatval($p->weight) == 0) && !empty($s->wt)){
					$p->weight = $s->wt;
        			$p->update(['weight']);
        			if (!empty($p->consol_id) && $p->consol->owner_id == 794) {
            			DxtPushQueue::addStockIn($p->id);
            		}
				}
			}elseif($p->type == 10){
				$p = ImParcel::model()->findByPk($p->id);
				$alloc = false;
				if(!empty($s->wt)){
					$p->weight = $s->wt;
        			$p->update(['weight']);
				}
			}
		}

		//dt $s->dt;
		//if($alloc) Storage::allocate($d->wid, 60, $p, 'Unsorted');

		if(empty($p->odpt_id) || $p->odpt_id == 1){
			$p->odpt_id = $d->wid;
			$savep = true;
		}

		//save weight
		if(!empty($s->wt) && empty($p->wtck)){
			$p->wtck = $s->wt;
			if(empty($p->weight)) $p->weight = $s->wt;
			$savep = true;
		}

		if($savep) $p->save();

		return true;
	} 

	public function getConsols(){
		$d = $this->ctlr->data;
		if(!empty($d->consol_id)){
			$rs = Consol::model()->findAll(['condition' => 'id = :id', 'params' => [':id' => $d->consol_id]]);
		}else{
			$rs = ExcoConsol::model()->findAll(['condition' => 'status > 10 AND status < 70 AND created > DATE_SUB(NOW(), INTERVAL 30 DAY) AND pol = :pol', 'order' => 'id DESC', 'params' => [':pol' => $d->pol]]);
		}
		$o = [];
		foreach($rs as $r){
			if($r->status > 30 && !$r->isOccupied()) continue;
			$sc = Shipment::model()->count('consol_id = :id', [':id' => $r->id]);
			if($sc == 0) continue;
			$c = new StdClass;
			$c->id = $r->id;
			$c->no = $r->no;
			$c->poc = $r->poc;
			$c->pkgs = $sc;
			$c->tpl = $r->getLabelTpl();
			$o[] = $c;
		}

		$this->ctlr->output($o);
	}

	public function getShipments(){
		$con = Consol::model()->findByPk($this->ctlr->data->cid);
		switch($con->type){
			case 15:
				$mdl = ImParcel::model();
			break;
			case 20:
				$mdl = ExAfs::model();
			break;
			case 25:
				$mdl = ExParcel::model();
			break;
		}
		$rs = $mdl->findAll('consol_id = :id', [':id' => $this->ctlr->data->cid]);
		
		$o = [];
		$rmap = include(Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'ref_map.php');
		foreach($rs as $r){
			$r->altGoods();
			$p = new StdClass;
			$p->id = $r->id;
			$p->cid = $r->consol_id;
			$p->pallet = Yii::app()->db->createCommand('SELECT f.ref FROM mani_map m INNER JOIN manifest f ON f.id = m.mani_id WHERE f.type = 60 AND m.fid = '.$r->id)->queryScalar();
			$p->hbn = $r->hbn;
			$p->bc2 = empty($rmap[$r->hbn])? '' : $rmap[$r->hbn];
			//$p->hbn = $r->hbn;
			$p->ref = in_array($con->poc, ['CNCT2'])? $r->hbn : $r->ref;
			$r->labelGoods();
			$p->eitems = $r->eitems;
			$p->weight = $r->weight;
			if($con->type == 25){
				$p->whd = $r->genWHD();
				$p->swt = $r->shipWeight();
				$p->value = $r->getDvalue();
				$rate = $r->rating($con->poc, $con->exrate, true);
				$p->rzone = empty($rate[1])? '' : $rate[1]->zone;
			}
			$p->cnor = new StdClass;
			$p->cnor->name = $r->cnor->name;
			$p->cnor->tel = $r->cnor->tel;
			$p->cnor->fullAddress = $r->cnor->fullAddress();
			$p->cnee = new StdClass;
			$p->cnee->name = $r->cnee->name;
			$p->cnee->tel = $r->cnee->tel;
			$p->cnee->state = $r->cnee->state;
			$p->cnee->city = $r->cnee->city;
			$p->cnee->suburb = $r->cnee->suburb;
			$p->cnee->postcode = $r->cnee->postcode;
			$p->cnee->address = $r->cnee->address;
			$p->mdata = [];
			$p->mdata['aid'] = $r->agent_id;
			$p->mdata['ot'] = $r->hasOriginTrace();
			$p->mdata['cref'] = $r->cref;
			if(in_array($con->type, [20, 25])){
				$p->cnee->fullAddress = $r->cnee->getCnFullAddress();
			}elseif($con->type == 15){
				$p->cnee->apAddress = $r->cnee->apAddress();
				$p->mdata['receipted'] = empty($r->mdata['receipted'])? false : $r->mdata['receipted'];
			}

			if(!empty($r->mdata['AltCnee'])){
				$alcn = Addr::model()->findByPk($r->mdata['AltCnee']);
				$p->cnee->name = $alcn->name;
				/*if(in_array($con->poc, ['CNXI2'])){
					$p->cnee->tel = $alcn->tel;
					$p->cnee->state = $alcn->state;
					$p->cnee->city = $alcn->city;
					$p->cnee->suburb = $alcn->suburb;
					$p->cnee->postcode = $alcn->postcode;
					$p->cnee->address = $alcn->address;
					$p->cnee->fullAddress = $alcn->getCnFullAddress();
				}*/
			}

			if(in_array($con->poc, ['CNCA2', 'CNTSN', 'CNJNA', 'CNCTU', 'CNJMN', 'CNJM2'])){ //yto dtb
				$p->mdata['dtb'] = empty($r->mdata['dtb'])? $r->cnee->city : $r->mdata['dtb'];
				$p->mdata['cbc'] = empty($r->mdata['cbc'])? '' : $r->mdata['cbc'];
			}elseif(in_array($con->poc, ['CNJJI','CNFZH'])){
				$fjm = jjFJM::model()->getCode($p->cnee);
				$p->mdata['dtb'] = $fjm['FJBM'];
				$p->mdata['cbc'] = $fjm['SFMC'] == $fjm['CSMC']? $fjm['SFMC'] : $fjm['SFMC'].$fjm['CSMC'];
			}/*elseif(in_array($con->poc, ['CNJM2'])){ //sf dtb
				$p->mdata['dtb'] = empty($r->mdata['dtb'])? $r->cnee->city : $r->mdata['dtb'];
			}*/elseif($con->type == 20){
				$p->mdata['bc2'] = $r->scan;
			}
			/*json_encode($p);
			if(json_last_error() > 0){
				$jle = json_last_error_msg();
				Yii::log($p->hbn.':'.$jle, 'error');
				throw new CHttpException(500, 'Internal Server Error: '.$p->hbn.':'.$jle);
			}*/
			$o[] = $p;
		}

		$this->ctlr->output($o);
	}

	public function getProblem(){
		$d = $this->ctlr->data;
		$o = [];
		$rs = ExParcel::model()->findAll('status IN (12, 14, '.(empty($d->stock_take)? '' : '15, ').'35, 101, 102, 103) AND odpt_id IN (106, 530)');
		foreach($rs as $r){
			$ln = $r->getLastLog();
			$dd = empty($ln)? 0 : ceil((time() - strtotime($ln->time)) / 86400);
			if(empty($dd) || $dd > 60) continue;
			$p = new StdClass;
			$p->id = $r->id;
			$p->hbn = $r->hbn;
			$p->ref = $r->ref;
			$p->status = $r->status;
			$p->status_txt = $r->getStatus();
			//return reason
			if($p->status == 102){
				$n = $r->agent_id;
				$l = Log::model()->find('type = 6 AND lid = :pid AND model="ExParcel" AND meta LIKE :m', [':pid' => $p->id, ':m' => '%退件原因%']);
				if(!empty($l)) $n .= ': '.$r->cnor->name.'/'.$r->cnee->name.' - '.$l->extra['notes'];
				$p->mdata['notes'] = $n;
			}
			if($p->status == 12){
				 if(empty($r->cnee->name)){
				 	$p->status = 14;
					$p->status_txt = 'Rcvd. No Info';
				 }else{
				 	continue;
				 }
			}
			$p->relabel = in_array($r->status, [35, 102])? 1 : 0;
			$o[] = $p;
		}

		$rs = ExParcel::model()->findAll('status IN (12, 15, 18) AND weight = 0 AND items != ""');
		foreach($rs as $r){
			$p = new StdClass;
			$p->id = $r->id;
			$p->hbn = $r->hbn;
			$p->ref = $r->ref;
			$p->status = $r->status;
			$p->status_txt = 'No Weight';
			$p->relabel = 0;
			$o[] = $p;
		}

		$rs = ExParcel::model()->findAll('agent_id IN (SELECT id FROM org WHERE meta LIKE :t) AND status > 10 AND status < 20 AND weight != 0 AND wtck = 0', [':t' => '%"wt_check";s:1:"1%']);
		foreach($rs as $r){
			$t = $r->goodsType();
			if(in_array($t, ['B', 'M'])) continue;
			$p = new StdClass;
			$p->id = $r->id;
			$p->hbn = $r->hbn;
			$p->ref = $r->ref;
			$p->status = $r->status;
			$p->status_txt = 'Weight Check';
			$p->relabel = 0;
			$o[] = $p;
		}

		//long delays
		$rs = ExParcel::model()->findAll('status IN (15, 18, 35) AND created < DATE_SUB(NOW(), INTERVAL 30 DAY) AND odpt_id IN (106, 530)');
		foreach($rs as $r){
			$p = new StdClass;
			$p->id = $r->id;
			$p->hbn = $r->hbn;
			$p->ref = $r->ref;
			$p->status = $r->status;
			$p->status_txt = 'Long Delay';
			$p->relabel = 1;
			$o[] = $p;
		}
		$this->ctlr->output($o);
	}

	public function getDuplicate(){
		$d = $this->ctlr->data;
		$o = [];
		$db = Yii::app()->getDb();
		$nwd = strtotime(date('Y-m-d').' +'.(date('N') == 5? 3:1).' day');

		//proof before consol
		$sql = 'SELECT COUNT(id) AS c FROM log WHERE model = \'ExcoConsol\' AND time > DATE_SUB(NOW(),  INTERVAL 1 DAY) AND meta = \'a:1:{s:6:"status";s:9:"Confirmed";}\'';
		$c = $db->createCommand($sql)->queryScalar();
		if(empty($c)) $this->ctlr->output($o);

		//max parcel per day for each channel in past 7 days
		$sql = 'SELECT poc, MAX(c) AS c FROM (SELECT poc, COUNT(id) AS c FROM consol WHERE type = 25 AND status > 10 AND created > DATE_SUB(NOW(),  INTERVAL 7 DAY) GROUP BY poc, created) t1 GROUP BY poc';
		$rs = $db->createCommand($sql)->queryAll();
		$mxpd = [];
		foreach($rs as $r){
			$ec = ExChannel::model()->find('code = :p', [':p' => $r['poc']]);
			if(!empty($ec->mdata['dupq']) && $ec->mdata['dupq'] > 1){
				$mxpd[$r['poc']] = $r['c'] * $ec->mdata['dupq'];
			}else{
				$mxpd[$r['poc']] = $r['c'];
			}
		}

		//parcels
		$criteria = new CDbCriteria();
		$criteria->addInCondition("status", [12, 15, 18]);
		$criteria->compare('odpt_id', $d->wid);
		$criteria->order = 'created ASC';
		$rs = ExParcel::model()->findAll($criteria);
		$dp = [];
		//$dsum =[];
		$pbq = [];

		$addDup = function(&$p, $dno)use(&$o){
			if($dno < 2) return;
			if($dno > 5) $dno = 5;
			if($dno > (5 - date('N'))) $dno += 2;

			$p->mdata['nsn'] = strtotime(date('Y-m-d').' +'.$dno.' day');
			$p->custom_log_note = 'Duplicate, no scan until '.date('Y-m-d', $p->mdata['nsn']);
			$p->save();
			
			$op = new StdClass;
			$op->id = $p->id;
			$op->hbn = $p->hbn;
			$op->status = $p->status;
			$op->loc = date('N', $p->mdata['nsn']);
			$o[] = $op;
		};

		foreach($rs as $p){
			$brs = $p->bestRates(false, false);
			$cs = array_keys($brs);
			if(empty($brs) || $brs[$cs[0]] == 999) continue;

			foreach($brs as $k=>$v){
				$poc = $k;
				break;
			}

			//check mapd
			if(!in_array($poc, $mxpd)){
				$ec = ExChannel::model()->find('code = :p', [':p' => $poc]);
				if(!empty($ec->mdata['dupq']) && $ec->mdata['dupq'] > 1){
					$mxpd[$poc] = 2 * $ec->mdata['dupq'];
				}else{
					$mxpd[$poc] = 2;
				}
			}

			//mark no
			$vks = ['idno' => empty($p->cnee->cnid_id)? $p->cnee->cnid_no : $p->cnee->cnid->no, 'tel' => $p->cnee->tel, 'fa' => md5($p->cnee->fullAddress())];
			$pnc = ['idno' => 0, 'tel' => 0, 'fa' => 0];

			foreach($vks as $k => $v){
				if(empty($v)) continue;
				if(!isset($dp[$poc])) $dp[$poc] = ['idno' => [], 'tel' => [], 'fa' => []];
				if(!isset($dp[$poc][$k][$v])) $dp[$poc][$k][$v] = 0;
				$dp[$poc][$k][$v]++;
				$pnc[$k] = $dp[$poc][$k][$v];
			};

			if(!empty($p->mdata['nsn']) && $p->mdata['nsn'] > $nwd){
				$op = new StdClass;
				$op->id = $p->id;
				$op->hbn = $p->hbn;
				$op->status = $p->status;
				$op->loc = date('N', $p->mdata['nsn']);
				$o[] = $op;
				continue;
			}

			$dno = ceil(max($pnc) / $mxpd[$poc]);

			if($dno > 1){ //nsn
				$addDup($p, $dno);
			}elseif(!empty($p->mdata['nsn']) && $p->mdata['nsn'] < time()){ //add to push back queue
				$pbq[$p->id] = [$poc, $vks];
			}
		}
		
		foreach($pbq as $pid => $ar){
			list($poc, $vks) = $ar;
			$pnc = ['idno' => 0, 'tel' => 0, 'fa' => 0];

			foreach($vks as $k => $v){
				if(empty($v)) continue;
				$pnc[$k] = $dp[$poc][$k][$v];
			}

			$dno = ceil(max($pnc) / $mxpd[$poc]);
			$p = ExParcel::model()->findByPk($pid);

			if($dno > 1){ //push back
				$addDup($p, $dno);
			}else{ //reset
				$op = new StdClass;
				$op->id = $p->id;
				$op->hbn = $p->hbn;
				$op->status = $p->status;
				$op->loc = "0";
				$o[] = $op;
			}
		}

		//print_r($dsum);

		$this->ctlr->output($o);
	}

	public function getStorage(){
		$d = $this->ctlr->data;
		$ps = Storage::model()->findAll(array(
				'condition' => "status = 1 AND cap < 100 AND wid = :wid AND type = :type AND name REGEXP '^[E-L]09-[1-6]'",
				'params' => array(':wid' => $d->wid, ':type' => 60),
				'order' => 'wt, id',
			));
		$o = [];
		foreach($rs as $r){
			$avl = floor((100 - $r->cap)/100 * $r->cap_item);
			if($avl < 1) continue;
			$p = new StdClass;
			$p->id = $r->id;
			$p->no = $r->name;
			$p->avl = $avl;
			$o[] = $p;
			continue;
		}

		$this->ctlr->output($o);
	}

	public function expStore(){
		$d = $this->ctlr->data;
		$db = Yii::app()->getDb();
		foreach($d->shipments as $b => $l){
			$p = ExParcel::model()->find('hbn = :hbn AND status = 18', [':hbn' => $b]);
			if(empty($p)) continue;
			$sql = "UPDATE storage_log SET out_dt = NOW() WHERE fid = ".$p->id." AND model = 'ExParcel' AND out_dt IS NULL";
			$db->createCommand($sql)->execute();
			$s = Storage::model()->find('wid = :wid AND status = 1 AND type = 60 AND name = :n', [':wid' => $d->wid, ':n' => $l]);
			$s->addItem($p);
		}
		$this->ctlr->output(true);
	}

	public function expPicked(){
		$d = $this->ctlr->data;
		$nwd = strtotime(date('Y-m-d').' +'.(date('N') == 5? 3:1).' day');

		$trans = Yii::app()->db->beginTransaction();
		try{
		foreach($d->shipments as $b => $dt){
			$p = ExParcel::model()->find('hbn = :hbn', [':hbn' => $b]);
			$savep = false;
			$this->log('Picked '.$b);

			if(empty($p)){
				$p = ExParcel::model()->find('ref = :hbn', [':hbn' => $b]);
				if(!empty($p)) continue;
				$p = new ExParcel('create');
				$p->hbn = $b;
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
			}

			if(empty($p->odpt_id)){
				$p->odpt_id = $d->wid;
				$savep = true;
			}

			if($p->status == 12){
				$p->status = empty($p->cnee->name)? 14 : 15;
				$savep = true;
			}else{
				//$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $p->id, ':t' => 15));
				//if(empty($ht)) $p->addTracking(15, '货物入库，等待发运', $dpt, $dt[0]);
				$p->custom_log_note = 'Warehouse Scan';
				$savep = true;
			}

			if(!empty($p->mdata['nsn']) && $p->mdata['nsn'] <= $nwd){
				unset($p->mdata['nsn']);
				$savep = true;
			}

			if(!empty($dt[1]) && $dt[1] != 0){
				$p->wtck = $dt[1];
				if(empty($p->weight) || $p->weight == 0) $p->weight = $dt[1];
				$savep = true;
			}
			
			if($p->status > 60 && $p->status < 100){
				$p->status = 101;
				$p->custom_log_note = 'Picked at '.substr($dt[0], 0,19);
			}elseif(in_array($p->status, [100, 104])){
				$p->status = 101;
				$savep = true;
			}
			if($savep) $p->save();
		}
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}
		$this->ctlr->output(true);
	}

	public function expPltz(){
		$d = $this->ctlr->data;
		$err = false;
		$msgs = [];
		$trans = Yii::app()->db->beginTransaction();
		try{
		foreach($d->consols as $cid => $ps){
			$c = ExcoConsol::model()->findByPk($cid);
			if(empty($c)){
				$err = true;
				$msgs[] = 'Consol '.$cid.' not found';
				break;
			}

			//palletize
			foreach($ps as $plt => $ss){
				$m = Manifest::model()->find('consol_id = :c AND ref = :n', [':c' => $cid, ':n' => sprintf('%02d', $plt)]);
				if(empty($m)){
					$m = new Manifest;
					$m->type = 60;
					$m->ref = sprintf('%02d', $plt);
					$m->consol_id = $cid;
					$m->save();
				}

				foreach($ss as $sid){
					$p = ExParcel::model()->find('id = :sid AND consol_id = :cid', [':cid' => $c->id,':sid' => $sid]);
					if(empty($p)){
						$msgs[] = 'Shipment '.$sid.' not found';
						continue;
					}
					$sql = 'SELECT m.id FROM mani_map m INNER JOIN manifest f ON f.id = m.mani_id WHERE m.fid = :fid AND f.type = 60 AND f.consol_id = :cid';
					$mid = Yii::app()->db->createCommand($sql)->bindValues([':fid' => $sid, ':cid' => $cid])->queryScalar();
					if(empty($mid)){
						$m->map($p);
					}else{
						$mm = ManiMap::model()->findByPk($mid);
						$mm->mani_id = $m->id;
						$mm->save();
					}
				}
			}
		}
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}
		$o = new StdClass;
		$o->done = !$err;
		$o->msgs = $msgs;

		$this->ctlr->output($o);
	}

	public function log($l){
		if(empty($this->logging)) return false;
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.'conveyor_api.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}
}
