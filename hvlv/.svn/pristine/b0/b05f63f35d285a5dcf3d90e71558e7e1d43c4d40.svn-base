<?php

class iExpCommand extends CConsoleCommand {
	private $db;
	private $args;
	private $debug = false;
	public $agtids = [121];
	public $ts, $day, $hr, $mn;

	public function run($args) {
		$this->db = Yii::app()->getDb();
		$this->args = $args;
		$this->ts = time();
		$this->day = date('N');
		$this->hr = date('h');
		$this->mn = ltrim(date('i'), 0);
		foreach($this->args as $ag){
			if($ag == '-d') $this->debug = true;
		}
		$pid = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'iexp.pid';
		if(is_file($pid) && filectime($pid) > time() - 1800 && !$this->debug) return false;
		file_put_contents($pid, '1');
		$rs = Org::model()->findAll('t.by = 121 AND t.status = 1');
		foreach($rs as $r){
			$this->agtids[] = $r->id;
		}
		if(!empty($args[0]) && method_exists($this, $args[0])){
			$this->{$args[0]}();
		}else{
			$this->getIDs();
			$this->pushParcels();
		}
		unlink($pid);
	}

	protected function restCall($url, $data){
		$data_string = json_encode($data);
		$ch = curl_init($url);
		if($this->debug) echo 'Sending: '.$data_string."\n";
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/json',
		    'Content-Length: ' . strlen($data_string))
		);
		$r = curl_exec($ch);
		if($this->debug) echo 'Received: '.$r."\n";
		return empty($r)? '' : json_decode($r);
	}

	protected function IDlog($l, $at = true, $fresh = false){
		$clog = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'ids_running.log';
		file_put_contents($clog, ($at? date('Y-m-d H:i:s').' - ' : ''). $l, $fresh? 0 : FILE_APPEND);
	}

	public function getIDs(){
		$rs = ExParcel::model()->with('cnee')->findAll(array(
			'condition' => 't.status < 18 AND t.agent_id IN ('.implode(',', $this->agtids).') AND cnee.cnid_id = 0',
			'group' => 'cnee.name',
		));
		$bdir = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		$dw = 600;
		$log = '';
		$this->IDlog("===== Fetch ID Task Started =====.\n", true, true);
		foreach($rs as $r){
			if(empty($r->cnee) || empty($r->cnee->name) || $r->cnee->cnid_id > 0) continue;
			$j = $this->restCall('http://www.iexpresslogistics.com.au/ExpressAdmin/OrderHandler/ReceiveIdCard', array('name' => $r->cnee->name));
			$j = json_decode($j);
			$this->IDlog('Looking ID for :'.$r->cnee->name.' - ');
			if(!empty($j->status)){
				$ids = json_decode($j->msg);
				$this->IDlog('Found '.sizeof($ids)."\n", false);
				foreach($ids as $id){
					$m = CnID::model()->find('no = :no', array(':no' => $id->IdCard));
					if(empty($m)) $m = new CnID;
					if(empty($m->joint)){
						$this->IDlog('Downloading: '.$id->IdCard);
						$pi = pathinfo($id->Img1);
						$m->name = $r->cnee->name;
						$m->no = $id->IdCard;
						$m->mdata['fie'] = 1;
						$m->status = 10;
						//$m->save();
						$p1 = $bdir.$id->IdCard.'-1.'.strtolower($pi['extension']);
						$p2 = $bdir.$id->IdCard.'-2.'.strtolower($pi['extension']);
						@copy($id->Img1, $p1);
						@copy($id->Img2, $p2);
						$img1 = AppHelper::resizeImg($p1, $dw);
						if(!$img1){
							$log .= date('Y-m-d H:i:s')." - Error image ".$id->Img1."\n";
						}else{
							imagejpeg($img1, $p1);
							$m->front = FileRepo::storeFile($p1, $m->no.'-1.jpg', 60, $m->id);
						}
						$img2 = AppHelper::resizeImg($p2, $dw);
						if(!$img2){
							$log .= date('Y-m-d H:i:s')." - Error image ".$id->Img2."\n";
						}else{
							imagejpeg($img2, $p2);
							$m->back = FileRepo::storeFile($p2, $m->no.'-2.jpg', 60, $m->id);
						}
						@unlink($p1);
						@unlink($p2);
						
						if($img1 && $img2) $m->joinPhoto();
						$m->save();
						$this->IDlog(" - Saved\n", false);
					}else{
						$this->IDlog('Error: ID '.$id->IdCard.' has name '.$m->name."\n");
					}
				}
				if(sizeof($ids) == 1){
					$m->matchCnee();
				}
			}else{
				$this->IDlog("Not Found.\n", false);
			}
		}
		if(!empty($log)) file_put_contents($bdir.'ids_error.log', $log, FILE_APPEND);
		
		$this->IDlog("Done\n");
		echo "Done\n";
	}

	public function ieStatus($s){
		switch($s){
			case 14:
				return '到库分检';
			break;
			case 15:
				return '到库分检';
			break;
			case 20:
				return '登机装运';
			break;
			case 70:
				return '货物转运中，等待清关';
			break;
			case 80:
				return '中转港海关清关处理中';
			break;
			case 90:
				return '分拨完毕，到门派送中';
			break;
			case 99:
				return '派送完毕，货物已签收';
			break;
		}
		return '';
	}

	protected function log2file($m){
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'iexp_data_push.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

	protected function sendTracking($r){
		if($r->type != 20 || !in_array($r->agent_id, $this->agtids)) return false;
		$o = new stdClass;
		$o->msg_type = 'tracking';
		$o->no = $r->hbn;
		$o->status = $this->ieStatus($r->status);
		$o->tracks = [];
		$mts = empty($r->mdata['max_tracking_sent'])? 0 : $r->mdata['max_tracking_sent'];
		foreach($r->tracks as $tk){
			if($tk->id <= $mts) continue;
			$o->tracks[] = [$tk->activity, $tk->depot, $tk->dt];
		}
		if(!empty($o->tracks)){
			$j = $this->restCall('http://www.iexpresslogistics.com.au/ExpressAdmin/OrderHandler/ReceiveOrder', $o);
			$j = json_decode($j);
			$this->log2file(date('Y-m-d H:i:s').': '.$r->hbn.': Sent tracking up to '.$tk->dt);
			if(empty($j->status)){
				$this->log2file(" - failed\n");
			}else{
				$this->log2file(" - done\n");
				$r->mdata['max_tracking_sent'] = $tk->id;
				$r->nolog = true;
				$r->update(['meta']);
			}
		}
	}

	public function pushParcels(){
		$rs = ExParcel::model()->findAll('agent_id IN ('.implode(',', $this->agtids).') AND status < 99');
		$i = 0;
		foreach($rs as $r){
			$logc = Log::model()->count("model = 'ExParcel' AND type = 8 AND lid = :id", array(':id' => $r->id));
			if(empty($logc)){
				$o = new stdClass;
				$o->msg_type = 'new';
				$o->no = $r->hbn;
				$j = $this->restCall('http://www.iexpresslogistics.com.au/ExpressAdmin/OrderHandler/ReceiveOrder', $o);
				$j = json_decode($j);
				$this->log2file(date('Y-m-d H:i:s').': '.$r->hbn.": Sending shipment new");
				if(empty($j->status)){
					$this->log2file(" - failed\n");
				}else{
					$this->log2file(" - done\n");
					Log::add($r, 8, array('status' => $r->getStatus(), 'note' => "Send new shipment data out."));
				}
			}elseif($logc == 1 && $r->status >= 18){
				$o = new stdClass;
				$o->msg_type = 'update';
				$o->no = $r->hbn;
				$o->cpno = '';
				$o->tsno = '';
				$o->pkg = $r->pkg;
				$o->weight = $r->weight;
				$o->value = $r->dvalue;
				$o->insurance = $r->insurance;
				$o->cnor = new stdClass;
				$o->cnor->name = $r->cnor->name;
				$o->cnor->tel = $r->cnor->tel;
				$o->cnor->addr = $r->cnor->address;
				$o->cnee = new stdClass;
				$o->cnee->name = $r->cnee->name;
				$o->cnee->tel = $r->cnee->tel;
				$o->cnee->state = $r->cnee->state;
				$o->cnee->city = $r->cnee->city;
				$o->cnee->addr = $r->cnee->address;
				$o->cnee->postcode = $r->cnee->postcode;
				$o->items = array();
				foreach($r->eitems['g'] as $gi => $g){
					$o->items[] = array($r->eitems['type'][$gi], $g, $r->eitems['q'][$gi], $r->eitems['w'][$gi]);
				}
				$j = $this->restCall('http://www.iexpresslogistics.com.au/ExpressAdmin/OrderHandler/ReceiveOrder', $o);
				$j = json_decode($j);
				$this->log2file(date('Y-m-d H:i:s').': '.$r->hbn.": Sending shipment update");
				if(empty($j->status)){
					$this->log2file(" - failed\n");
				}else{
					$this->log2file(" - done\n");
					Log::add($r, 8, array('status' => $r->getStatus(), 'note' => "Send updated shipment data out."));
				}
			}elseif($logc == 2 && $r->status >= 90){
				$o = new stdClass;
				$o->msg_type = 'transfer';
				$o->no = $r->hbn;
				$o->cpno = $r->ref;
				$j = $this->restCall('http://www.iexpresslogistics.com.au/ExpressAdmin/OrderHandler/ReceiveOrder', $o);
				$j = json_decode($j);
				$this->log2file(date('Y-m-d H:i:s').': '.$r->hbn.": Sending tranship code");
				if(empty($j->status)){
					$this->log2file(" - failed\n");
				}else{
					$this->log2file(" - done\n");
					Log::add($r, 8, array('status' => $r->getStatus(), 'note' => "Send cpno out."));
				}

			}

			//loop through tacking, and send relavent ones.
			if(!empty($r->tracks)) $this->sendTracking($r);

			if($i++%20==0) sleep(1);
		}

		//for delivered parcels
		$rs = Tranship::model()->findAll('`status` = 99 AND `type` = 80 AND `time` > DATE_SUB(NOW(), INTERVAL 30 DAY)');
		foreach($rs as $r){
			$this->sendTracking($r->shipment);
			if($i++%20==0) sleep(1);
		}
	}
}
