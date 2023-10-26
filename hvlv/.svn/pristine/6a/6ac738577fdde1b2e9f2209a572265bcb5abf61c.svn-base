<?php
class AliCloudAPI {
	private $api_key = '23604536';
	private $api_secret = 'e19bbfd647ce96e6998494debe11122f';
	private $api_appcode = 'a93113b3e0cc4c7286bd9c366df8f55d';
	private $debug = true;

	public $result, $err;
	public $retry = 3;

	public function __construct($debug = false){
		$this->debug = $debug;
	}

	public function idchecky($name, $idno){
		return $this->request('http://idchecky.market.alicloudapi.com', '/idcard/v1/authInfo?id_card='.trim($idno).'&id_holder='.trim($name));
	}

	public function idValid($name, $idno){
		return $this->request('http://1.api.apistore.cn', '/idcard?cardNo='.trim($idno).'&realName='.trim($name));
	}

	public function idCardPho($name, $idno){
		return $this->request('http://idcardpho.market.alicloudapi.com', '/idcardinfo/photo?idcard='.trim($idno).'&realname='.trim($name));
	}

	protected function request($host, $url, $method='GET', $sig=false, $post=[], $body=''){
		$this->result = false;
		$c = new curl($host.$url);
		$c->setopt(CURLOPT_TIMEOUT, 10);

		if(strtoupper($method) == 'POST'){
			$qs = $c->asPostString($post);
			$c->setopt(CURLOPT_POST, true);
			$c->setopt(CURLOPT_POSTFIELDS, $qs);
		}
		if(1 == strpos("@".$host, "https://")){
        	$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
        	$c->setopt(CURLOPT_SSL_VERIFYHOST, false);
    	}
		
		$date = date(DATE_RFC2822);
    	if($sig){
			$xts = round(microtime(true)*1000);
			$sts = strtoupper($method)."\n".'application/json'."\n\n".'application/json'."\n".$date."\n".'X-Ca-Key:'.$this->api_key."\n".'X-Ca-Timestamp:'.$xts."\n".$url;
			$sig = base64_encode(hash_hmac('sha256', $sts , $this->api_secret, true));

			$c->setopt(CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json', 'Date: '.$date, 'X-Ca-Timestamp: '.$xts, 'X-Ca-Key: '.$this->api_key, 'X-Ca-Signature-Headers: X-Ca-Key,X-Ca-Timestamp', 'X-Ca-Signature: '.$sig]);
		}else{
			$c->setopt(CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json', 'Date: '.$date, 'Authorization: APPCODE '.$this->api_appcode]);
		}
		$this->log('Request: '.$host.$url."\n");
		if(!$c->exec()) return false;
		$this->log('Response: '.$c->result."\n");
		$this->result = json_decode($c->result);
		return $this->result;
	}

	public function log($m){
		if(!$this->debug) return false;
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'alicloud_api.log';
		@file_put_contents($lf, date('Y-m-d H:i:s').' '.$m."\n", FILE_APPEND);
	}

//end of class
}