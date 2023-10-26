<?php
class GlobavendAPI
{
	private $url = 'http://47.75.48.99:82/';
	private $api_user = 'PCA';
	private $api_id = '80010102';
	private $api_token = '8ade1639e53e88aa77d2df6880f40f8c';


	public function __construct($test = false)
	{
	}

	public function createOrder($rs)
	{
		$data = [
			'uid' => $this->api_id,
			'order' => [],
		];

		if(!is_array($rs)) $rs = [$rs];

		foreach($rs as $r){
			$sp = Postcode::autoSuburbByPostcode($r->cnee->suburb, $r->cnee->state, $r->cnee->postcode, 'AU');
			$shipdata = [
				'customerOrderNumber' => $r->hbn,
				'channelCode' => 'Eparcel',
				'countryCode' => 'AU',
				'insuranceAmount' => 0.0,
				'goodsType' => '普货',
				'cargoTotalWeight' => $r->weight,
				'cargoTotalValue' => max(@floatval($r->eitems['v'][0])*intval($r->eitems['q'][0]), 1),
				'dContact' => AppHelper::semiAngle($r->cnee->name),
				'dTel' => $r->cnee->tel,
				'dAddress' => $r->cnee->address,
				'dCountry' => 'AU',
				'dProvince' => $r->cnee->state,
				'dCity' => $r->cnee->suburb,
				'dPostCode' => $r->cnee->postcode,
				'sCompany' => $r->cnor->company,
				'sContact' => $r->cnor->name,
				'sTel' => $r->cnor->tel,
				'sMobile' => $r->cnor->tel,
				'sEmail' => $r->cnor->email,
				'sAddress' => $r->cnor->address,
				'sCountry' => $r->cnor->country,
				'sProvince' => $r->cnor->state,
				'sCity' => $r->cnor->suburb,
				'sPostCode' => $r->cnor->postcode,
				'OrderDeclaration' => [],
			];
			$ic = array_sum(array_filter($r->eitems['q'], function($v){ return !empty($v);}));
			foreach($r->eitems['g'] as $gi=>$g){
				if(empty($g) || empty($r->eitems['q'][$gi])) continue;
				$shipdata['OrderDeclaration'][] = [
					'dNameEn' => substr($g, 0, 60),
					'dNameCn' => mb_substr(empty($r->eitems['g_zh'][$gi])? '' : $r->eitems['g_zh'][$gi], 0, 60),
					'dWeight' => round($r->weight/$ic, 2),
					'dQuantity' => $r->eitems['q'][$gi],
					'dValue' => $r->eitems['v'][$gi],
				];
			}
			$data['order'][] =  $shipdata;
		}

		return $this->_request('sys/order/batchCreateOrder', $data);
	}

	public function getLabel($rs){
		$data = [
			'uid' => $this->api_id,
			'orderLabel' => [],
		];
		if(!is_array($rs)) $rs = [$rs];
		foreach($rs as $r){
			$data['orderLabel'][] =  [
				'trackingNumber' => '019931265099999891'.(empty($r->mdata['article_id'])? $r->ref : $r->mdata['article_id']),
			];
		}

		return $this->_request('sys/order/getLabel', $data);
	}

	public function _request($action, $data = '')
	{
		$c = new curl($this->url . $action);
		$c->setopt(CURLOPT_CUSTOMREQUEST, 'POST');
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 60);
		if (!empty($data)) {
			$data = json_encode($data);
			$pd = [
				'data' => $data,
				'secretKey' => base64_encode(md5($this->api_id.$this->api_token.$data)),
			];
			$c->setopt(CURLOPT_POSTFIELDS, $c->asPostString($pd));
		}
		$st = microtime(true);
		$ld = $method.': ' . $this->url . $action. "\nRequest Time: ".date('Y-m-d H:i:s').(empty($data)? '' : "\nData: " . $data);

		if (!$c->exec()) {
			$this->log2file($ld."\nError: " . $c->err."\nRequest Took: ".(microtime(true) - $st).'s');
			throw new Exception('cUrl Error: '.$c->err);
		}

		$this->log2file($ld."\nResponse: " . trim($c->result)."\nRequest Took: ".(microtime(true) - $st).'s');

		return empty($c->result)? false : json_decode($c->result, true);
	}

	protected function log2file($m)
	{
		$dir = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR  . 'globavend';
		if(!is_dir($dir)){
			mkdir($dir);
		}
		$lf =  $dir. DIRECTORY_SEPARATOR . 'gv_' . date('Y-m-d') . '.log';
		return file_put_contents($lf, date('Y-m-d H:i:s').' '.$m."\n", FILE_APPEND);
	}
}
