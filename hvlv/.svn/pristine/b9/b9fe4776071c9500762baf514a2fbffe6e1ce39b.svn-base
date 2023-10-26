<?php
class ApiShunfeng extends CAction {

	public function run() {
		$this->data = simplexml_load_string($_POST['RequestData']);
		if($this->debug) $this->log(json_encode($this->data));
		$this->saveData();
	}

	public function log($l){
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.'shunfeng_api.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}

	public function saveData(){
		if(!empty($this->data->Body)){
			foreach($this->data->Body->WayBillRoute as $rt){
				$rta = $rt->attributes();
				$ts = Tranship::model()->find('connote = :c', [':c' => $rta->mailno]);
				if(empty($ts)){
					$p = Shipment::model()->find('ref = :c AND ((type = 20 AND status = 80) OR (type = 50 AND status = 50))', [':c' => $rta->mailno]);
					if(!empty($p)){
						$ts = new Tranship;
						$ts->type = 80;
						$ts->pid = $p->id;
						$ts->org_id = 496;
						$ts->connote = $p->ref;
						$ts->status = 19;
						$ts->time = date('Y-m-d H:i:s');
						$ts->save();
						$p->status = 90;
						$p->addTracking(60, '清关完成开始派送', '', $rta->acceptTime);

						$c = Tracking::model()->find("type = 50 AND pid = ".$p->id);
						if(strtotime($c->dt) > strtotime($rta->acceptTime)){ //quick clearance
							$a = Tracking::model()->find("type = 40 AND pid = ".$p->id);
							$dt = strtotime($c->dt) - strtotime($a->dt);
							if($dt > 0){
								$dt = floor($dt / 2) - 7200 + rand(0, 14400) + strtotime($a->dt);
								$c->dt = date('Y-m-d H:i:s', $dt);
								$c->save();
							}
						}
						unset($p->mdata['ems_last_pending']);
						$p->save();
					}
				}
				if(empty($ts)) continue;
				$typ = 70;
				if(preg_match('/(?<!未)(妥投|签收)/', $rta->emark)){
					$ts->status = 99;
					$ts->save();
					$ts->shipment->status = 99;
					$ts->shipment->save();
					$typ = 90;
				}
				$ts->shipment->addTracking($typ, $rta->acceptAddress, '', $rta->AcceptTime, $ts->id);
			}
		}

		$this->respond();
	}

	public function respond(){
		echo '<Response service="RoutePushService"><Head>OK</Head></Response>';
		Yii::app()->end();
	}
}
