<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApiKdnAction extends CAction {
	public $data, $debug = false;
	private $_ebid = 1256523;

	public function run() {
		$this->data = json_decode($_POST['RequestData'], true);
		if($this->debug) $this->log(json_encode($this->data));
		if(empty($this->data['EBusinessID']) || $this->data['EBusinessID'] != $this->_ebid){
			throw new CHttpException(400, 'Bad Request, API Access Only!');
		}
		if(empty($this->data['Data'])){
			$this->respond();
		}else{
			$this->saveData();
		}
	}


	public function log($l){
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.'kdn_api.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}

	public function saveData(){
		$trans = Yii::app()->db->beginTransaction();
		try{
		foreach($this->data['Data'] as $d){
			if(hash('crc32b', $d['LogisticCode'].'**KdN2PCA**') != $d['CallBack']) continue;
			$ts = Tranship::model()->find('connote = :c', [':c' => $d['LogisticCode']]);
			if(empty($ts)){
				$p = Shipment::model()->find('ref = :c AND ((type = 20 AND status = 80) OR (type = 50 AND status = 50))', [':c' => $d['LogisticCode']]);
				if(!empty($p)){
					$ts = new Tranship;
					$ts->type = 80;
					$ts->pid = $p->id;
					$cc = $this->poc2cc(empty($p->consol_id)? 'FWDEMS' : $p->consol->poc, $p->ref);
					if($cc[1] == 'sto' && preg_match('/^81\d+/', $p->ref)) $cc[0] = 495;
					$ts->org_id = $cc[0];
					$ts->connote = $p->ref;
					$ts->status = 19;
					$ts->time = date('Y-m-d H:i:s');
					$ts->save();
				}
			}
			if(empty($ts)){
				continue;
			}else{
				$a = Tracking::model()->find("type = 40 AND pid = ".$ts->pid);
				$mindt = strtotime($a->dt);
				$p = $ts->shipment;
			}

			if(empty($d['Traces']) || !is_array($d["Traces"])){
				$this->log(json_encode($d));
				continue;
			}

			foreach($d['Traces'] as $t){
				if(empty($t['AcceptTime']) || empty($t['AcceptStation']) || preg_match('/澳大利亚|悉尼|墨尔本|海外|正亚/', $t['AcceptStation']) || $mindt > strtotime($t['AcceptTime'])) continue;
				if($p->status == 80){
					if($p->consol->status < 80){
						$p->consol->status = 80;
						$p->consol->save();
					}
					$p->status = 90;
					$p->addTracking(60, '清关完成开始派送', '', $t['AcceptTime']);
					$c = Tracking::model()->find("type = 50 AND pid = ".$p->id);
					if(strtotime($c->dt) > strtotime($t['AcceptTime'])){ //quick clearance
						$dt = strtotime($c->dt) - $mindt;
						if($dt > 0){
							$dt = floor($dt / 2) - 7200 + rand(0, 14400) + strtotime($a->dt);
							$c->dt = date('Y-m-d H:i:s', $dt);
							$c->save();
						}
					}
					unset($p->mdata['ems_last_pending']);
					$p->save();
				}
				$typ = 70;
				if($d['State'] == 3 && preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $t['AcceptStation'])){
					$ts->status = 99;
					$ts->save();
					$ts->shipment->status = 99;
					$ts->shipment->save();
					$typ = 90;
				}
				$ts->shipment->addTracking($typ, $t['AcceptStation'], '', $t['AcceptTime'], $ts->id);
			}
		}
		$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}

		$this->respond();
	}

	public function poc2cc($poc, $rn=''){
		$orate = OrgRate::model()->find('type = 10 AND code = :c AND vfrom <= CURRENT_DATE() AND (vto IS NULL OR vto >= CURRENT_DATE())', [':c' => $poc]);
		if(empty($orate)){
			return [128, 'yzpy'];
		}else{
			return [$orate->org_id, $orate->org->code];
		}

		$r = [128, 'ems'];
		switch($poc){
			case 'CNCTU':
			case 'CNTSN':
			case 'CNJNA':
			case 'CNJMN':
			case 'CNJM2':
			//case 'CNXIA':
			case 'CNSJA':
			case 'CNHFI':
				$r = [495, 'yto'];
			break;
			case 'FWDEMS':
				$pc = substr($rn, 0, 4);
				if($pc == '5605'){
					$r = [866, 'hhtt'];
				}else{
					$r = [495, 'yto'];
				}
			break;
			/*case 'CNJMN':
				$r = [382, 'yd'];
			break;*/
			case 'HKHKG':
				$pc = substr($rn, 0, 4);
				$r = [488, 'zto'];
				switch($pc){
					case '7569':
						$r = [494, 'sto'];
					break;
					case '9727':
						$r = [496, 'sf'];
					break;
				}
			break;
			case 'CNSZX':
				$pc = substr($rn, 0, 6);
				if($pc == '359495') $r = [488, 'zto'];
			break;
			//case 'CNXMN':
			//case 'CNJM2':
			case 'CNCA2':
			case 'SFDIR':
				$r = [496, 'sf'];
			break;
			case 'STO':
				$r = [494, 'sto'];
			break;
		}
		if($r[0] == 128 && preg_match('/^611\d+/', $rn)){
			$r = [496, 'sf'];
		}
		return $r;
	}

	public function respond(){
		$o = [
			"EBusinessID" => $this->_ebid,
			"UpdateTime" => date('Y/m/d H:i:s'),
			"Success" => true,
			"Reason" => '',
	    ];
		echo json_encode($o);
		Yii::app()->end();
	}
}
