<?php
//opcache_invalidate(__FILE__);
class ApiCainiaoAction extends CAction {
	public $ctlr, $debug = true;

	public $pkey = '2018aedc1e6b3b2ba3475dcb1b67f03725f96ce7';

	public function run() {
		$this->ctlr = $this->getController();
		if($this->debug) $this->log(json_encode($_POST));
		if(empty($_POST['data_digest'])) $this->respond(false, 'S12', '非法的请求参数');
		$this->auth();
		$m = CnlMsg::saveMsg($_POST);
		$this->respond();
	}

	public function log($l){
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.'cainiao_api.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}

	public function auth(){
		if(!isset($_POST['logistics_interface']) || base64_encode(md5($_POST['logistics_interface'].$this->pkey, true)) != $_POST['data_digest']){
			if($this->debug) $this->log('digest = '.base64_encode(md5($_POST['logistics_interface'].$this->pkey, true)));
			$this->respond(false, 'S02', '非法的数字签名');
		}
		return true;
	}

	/*
	logistics_interface	请求报文内容（API对应的XML或JSON格式的报文）
	data_digest	请求签名（通过控制台产生的签名秘钥、报文内容计算得到， 计算方法 ）
	msg_type	消息类型(即API名称)
	msg_id	消息ID(可以由业务系统随机产生)
	partner_code	合作伙伴编码（CP调用的菜鸟内部系统的资源编码）
	from_code	调用方编码（即控制台上APPKEY绑定的资源编码）
	*/

	public function respond($s=true, $c='', $m=''){
		$o = [
			"success" => $s,
			"errorCode" => $c,
			"errorMsg" => $m,
	    ];
		echo json_encode($o);
		Yii::app()->end();
	}
}
