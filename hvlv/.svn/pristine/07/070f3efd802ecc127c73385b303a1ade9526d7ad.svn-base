<?php
class UflAPI{

	public function outboundUpdate($cust, $ono, $status, $wholesale_data = []){
		$method = 'POST';
		$url = 'https://service.ufreight.com:8443/pca/order_status/update/rest_reception';
		$data = [
			"customer" => $cust,
			"orderNo" => $ono,
			"warehouseCode" => "PCASY",
			"status" => $status,
		];
		$data = array_merge($data, $wholesale_data);
		return $this->request($url, $data, $method);
	}

	public function request($url, $data, $method='POST')
	{
		$hdr = ['Content-Type: application/json', 'Accept: application/json', 'Authorization: Basic cGNhZXhwcmVzczpwd2Q0cGNhZXhwcmVzcw=='];
		$ds = '';
		$c = new curl($url);

		if (!empty($data)) {
			$ds = AppHelper::safeJsonEncode($data);
			if(in_array($method, ['POST', 'PUT'])){
				$c->setopt(CURLOPT_POSTFIELDS, $ds);
				$hdr[] = 'Content-Length: ' . strlen($ds);
			}else{
				ksort($data);
				$c->setopt(CURLOPT_URL, $url.'?'.$c->asPostString($data));
			}
		}
		$this->log2file('Request:'. $ds);

		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		if (!$c->exec()) {
			throw new Exception('cUrl Error: '.$c->err);
		}
		$this->log2file('Response:'. $c->result);
		return json_decode($c->result, true);
	}
	
	protected function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'ufl_api.log';
		return file_put_contents($lf, date('Y-m-d H:i:s').' '.$m . "\n", FILE_APPEND);
	}

}
