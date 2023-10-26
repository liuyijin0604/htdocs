<?php

class HyhtAPI {
	private static $test_url = 'http://112.74.102.185:8095/NewWaybillInterface-HYHTTest/waybill.do'; //Test URL

	private static $url = 'http://112.74.102.185:8097/NewWaybillInterface-HYHT/waybill.do'; //Production URL

	private $api_id, $api_key, $is_test;

	public $result, $err;

	public $retry = 3;

	public $logging = true;

	public function __construct($id, $key = 'hyht', $test = false){
		$this->api_id = $id;
		$this->api_key = $key;
		$this->is_test = $test;
	}

	public function getNumber($ps){
		$ds = [];

		foreach($ps as $p){
			$ds[] = [
				"phone" => "089866774906",
				"name" => "海龟",
				"txLogisticID" => $p->hbn,
				"nameP" => $p->cnee->name,
				"itemName" => $p->GoodsNames(),
				"number" => array_sum($p->eitems['q']),
				"addressP" => $p->cnee->address,
				"phoneP" => $p->cnee->tel,
				"cityP" => $p->cnee->city.$p->cnee->suburb,
				"provP" => $p->cnee->state,
				"clientName" => "海龟",
				"yto_emp_code" => $this->api_id,
			];
		}

		return $this->request('waybill', $ds);
	}

	protected function request($method, $data, $rc = 0){
		$this->result = false;
		$c = new curl($this->is_test? self::$test_url : self::$url);
		$json = json_encode($data);
		$qs = $c->asPostString([
			'method' => $method,
			'data' => $json,
			'ztdkey' => strtoupper(md5($this->api_key.$json.$this->api_key)),
		]);

		$c->setopt(CURLOPT_TIMEOUT, 10);
		$c->setopt(CURLOPT_POST, true);
		$c->setopt(CURLOPT_POSTFIELDS, $qs);
		$this->log('Request: '.$qs);
		if(!$c->exec()){
			if($rc < $this->retry){
				return $this->request($method, $data, $rc+1);
			}else{
				return false;
			}
		}

		$this->log('Response: '.$c->result);
		$this->result = json_decode($c->result, true);
		return $this->result;
	}

	public function log($m){
		if(!$this->logging) return false;
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'hyht_api.log';
		@file_put_contents($lf, date('Y-m-d H:i:s').' '.$m."\n", FILE_APPEND);
	}

//end of class
}