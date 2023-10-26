<?php
class CainiaoLinkAPI {
	
	public $cp_code = 'CP419065';
	public $app = 'pcaexpress';
	public $key = '2018aedc1e6b3b2ba3475dcb1b67f03725f96ce7';
	private $url = 'https://link.cainiao.com/gateway/link.do';
	private $test_key = 'eceaeb0d1b10fbc45a2603bdd3e51c18b4c10090';
	private $test_url = 'https://linkdaily.tbsandbox.com/gateway/link.do';

	private static $resource_code_map = [
		'GFP_ECUS' => 'GATE_13455325', //清关
		'GFP_EX' => 'TRUNK_13455813', //拖车
		'GFP_INTL' => 'TRUNK_13454762', //干线
		'GFP_WH' => 'Tran_Store_13455497', //中转仓
	];

	private static $test_resource_code_map = [
		'GFP_ECUS' => 'GATE_575103', //清关
		'GFP_EX' => 'TRUNK_575100', //拖车
		'GFP_INTL' => 'TRUNK_575099', //干线
		'GFP_WH' => 'Tran_Store_575102', //中转仓
	];

	private $debug = true;
	public $result, $err;
	public $retry = 3;

	public function __construct($debug = false){
		$this->debug = $debug;
	}

	public static function getResourceCode($type, $test=false){
		foreach($test? self::$test_resource_code_map : self::$resource_code_map as $k=>$v){
			if(strpos('@'.$type, $k) == 1) return $v;
		}
		return false;
	}

	/*
	logistics_interface 	请求报文内容（API对应的XML或JSON格式的报文）
	logistic_provider_id 	来源CP编号(即控制台上APPKEY绑定的资源编码)
	msg_type 	消息类型(即API名称)
	data_digest 	请求签名（通过控制台产生的签名秘钥、报文内容计算得到， 计算方法 ）
	to_code 	目的方编码（可选，如不填使用该msg_type默认目的方）
	???msg_id	消息ID(可以由业务系统随机产生)
	*/

	public function request($data, $type, $mid='', $test=false){
		$this->result = false;
		$url = $test? $this->test_url : $this->url;
		$c = new curl($url);
		//$c->setopt(CURLOPT_TIMEOUT, 10);
		$post = [
			'logistics_interface' => $data,
			'logistic_provider_id' => self::getResourceCode($type, $test),
			'msg_type' => $type,
			'data_digest' => base64_encode(md5($data.($test? $this->test_key : $this->key), true)),
		];
		if(empty($post['logistic_provider_id'])){
			$this->log('Error: '.$type.' has no resource code');
			return false;
		}
		if(!empty($mid)) $post['msg_id'] = $mid;
		$qs = $c->asPostString($post);
		$c->setopt(CURLOPT_POST, true);
		$c->setopt(CURLOPT_POSTFIELDS, $qs);

		if(strpos("@".$url, "https://") == 1){
			$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
			$c->setopt(CURLOPT_SSL_VERIFYHOST, false);
		}

		$this->log('Request: '.$url."\n". $qs);
		if(!$c->exec()){
			$this->log('Error: '.$c->err);
			return false;
		}
		$this->log('Response: '.$c->result."\n");
		$this->result = json_decode($c->result);
		return $this->result;
	}

	public function log($m){
		if(!$this->debug) return false;
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'cainiao_api.log';
		@file_put_contents($lf, date('Y-m-d H:i:s').' '.$m."\n", FILE_APPEND);
	}

//end of class
}