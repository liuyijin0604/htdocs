<?php
class CcicOriginAPI {
	private $url = 'http://www.ccicorigin.com/v1/express'; //Production URL
	private $api_user = 1168;
	private $api_token = 'bNXPPJwQFk1OCDRNuPc69SOBmTls11PWL7eBFCofEtFVnvbYKmo6B+IBUaAL1TwN';

	public $result, $err, $logging;

	public $retry = 3;

	public function __construct($logging = false){
		$this->logging = $logging;
	}

	public function getNumber($q=5000){
		if($this->request('/getcode/'.$q, 'GET')){
			$r = json_decode($this->result);
			if(empty($r->Codes)) return false;
			foreach($r->Codes as $c){
				$ot = new OriginTrace;
				$ot->pvdr = 10;
				$ot->no = $c;
				$ot->save();
			}
			return true;
		}
		return false;
	}

	public function sendData($d){
		$this->log('Sending '.$d['AWBNo']);
		return $this->request('', 'POST', $d);
	}

	public function request($ep, $method="POST", $data=null, $rc = 0){
		$c = new curl($this->url.$ep);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_HTTPHEADER, array('X-Auth-User: '.$this->api_user, 'X-Auth-Token: '.$this->api_token, 'Content-Type: application/json'));
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 300);
		if($method=="POST"){
			$c->setopt(CURLOPT_POST, true);
			if(!empty($data)) $c->setopt(CURLOPT_POSTFIELDS, json_encode($data));
		}

		if(!$c->exec()){
			if($rc < $this->retry){
				return $this->request($ep, $method, $data, $rc+1);
			}else{
				return false;
			}
		}

		$this->log('Response: '.$c->result);
		$this->result = $c->result;
		return true;
	}

	public function log($m){
		if(!$this->logging) return false;
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'ccic_api.log';
		@file_put_contents($lf, date('Y-m-d H:i:s').' '.$m."\n", FILE_APPEND);
	}

//end of class
}