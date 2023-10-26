<?php

class WinWebAPI{
	private static $url = 'http://integration.winwebconnect.com/api/v1/';
	private static $callback = 'http://api.pcaexpress.com.au/winweb';

	private $api_user = 'trackinguser@toplogistics.com.au';
	private $api_pwd = 'win@123456';
	private $agent_id = '105294';
	private $cookie, $result;

	public $logging = true;
	public $retry = 3;

	public function auth(){
		$this->request('Login', ['Username' => $this->api_user, 'Password' => $this->api_pwd]);
	}

	public function addAwbs($awbs){
		$qs = [];
		if(is_string($awbs)) $awbs = [$awbs];
		foreach($awbs as $awb){
			$qs[] = ['Query' => [
			'SearchClass' => 'Air',
			'SearchKey' => 'awbno',
			'SearchValue' => $awb,
			'UpdateUntil' => date('Y-m-d', strtotime('+20 day')).'T00:00:00.000Z',
			'WebhookURL' => self::$callback.'?awbno='.$awb,
			]];
		}
		$this->request('uatapi/batchlist/agents/'.$this->agent_id, ['Queries' => $qs]);
	}

	public function getTracking($awb){
		$this->request('uatapi/batchlist/agents/'.$this->agent_id.'/'.substr($awb, 0 ,3).'/awbno/'.$awb, null, false);
		return $this->result;
	}

	public function getCarriers(){
		$this->request('uatapi/carriers', null, false);
		return $this->result;
	}

	public function request($ep, $q, $post = true, $rc = 0){
		$this->log('Endpoint: '.$ep);
		$c = new curl(self::$url.$ep);
		//$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 10);
		$c->setopt(CURLOPT_POST, $post);
		$c->setopt(CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=UTF-8', 'Accept: application/json']);
		if(!empty($this->cookie)) $c->setopt(CURLOPT_COOKIE, $this->cookie);
		if($post && !empty($q)){
			$qs = json_encode($q);
			$this->log('Request: '.$qs."\n");
			$c->setopt(CURLOPT_POSTFIELDS, $qs);
		}

		if(!$c->exec()){
			if($rc < $this->retry){
				return $this->request($ep, $q, $post, $rc+1);
			}else{
				return false;
			}
		}

		$this->log('Response: '.$c->result."\n");
		$this->result = json_decode($c->result);
		if(!empty($c->header['Set-Cookie'])) $this->cookie = $c->header['Set-Cookie'];
		return true;
	}

	public function log($m){
		if(!$this->logging) return false;
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'winweb_api.log';
		@file_put_contents($lf, date('Y-m-d H:i:s').' '.$m."\n", FILE_APPEND);
	}
}
