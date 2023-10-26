<?php
ini_set('soap.wsdl_cache_ttl', 600);
class YtoAPI {
	private static $test_url = 'http://58.32.246.71:8000/CommonOrderModeBPlusServlet.action'; //Test URL

	private static $url = 'http://service.yto56.net.cn/CommonOrderModeBPlusServlet.action'; //Production URL

	private $api_id, $api_key, $is_test, $debug;

	public $result, $err;

	public $retry = 3;

	public $logging = true;

	public function __construct($id, $key = null, $debug = false, $test = false){
		$this->api_id = $id;
		$this->api_key = $key;
		$this->is_test = $test;
		$this->debug = $debug;
	}

	public function getNumber(&$p){
		$xml = new SimpleXMLElement('<RequestOrder></RequestOrder>', LIBXML_NOXMLDECL);
		$xml->addChild('clientID', $this->api_id);
		$xml->addChild('logisticProviderID', 'YTO');
		$xml->addChild('customerId', $this->api_id);
		$xml->addChild('txLogisticID', $p->hbn);
		$xml->addChild('orderType', 1);
		$xml->addChild('serviceType', 1);

		$sender = $xml->addChild('sender');
		if(empty($p->cnor->name)) $p->cnor->name = 'PCA';
		$sender->addChild('name', substr(htmlspecialchars($p->cnor->name),0,32));
		$sender->addChild('postCode', preg_replace('/[\w\s]+/', '', $p->cnor->postcode));
		$sender->addChild('phone', empty($p->cnor->tel)? '1800518000' : $p->cnor->tel);
		$sender->addChild('prov', empty($p->cnor->state)? 'NSW' : $p->cnor->state);
		$sender->addChild('city', empty($p->cnor->suburb)? 'Sydney' : $p->cnor->suburb);
		$sender->addChild('address', empty($p->cnor->address)? 'U12, 1801 Botany Road' : htmlspecialchars($p->cnor->address));

		$receiver = $xml->addChild('receiver');
		$receiver->addChild('name', $p->cnee->name);
		$receiver->addChild('postCode', $p->cnee->postcode);
		$receiver->addChild('phone', $p->cnee->tel);
		$receiver->addChild('prov', $p->cnee->state);
		$receiver->addChild('city', $p->cnee->city.','.$p->cnee->suburb);
		$receiver->addChild('address', htmlspecialchars($p->cnee->address));

		$items = $xml->addChild('items');
		foreach($p->eitems['g'] as $gi => $g){
			$itm = $items->addChild('item');
			$q = intval($p->eitems['q'][$gi]);
			$itm->addChild('itemName', htmlspecialchars($g));
			$itm->addChild('number', empty($q)? 1 : $q);
		}
		$this->request($xml->asXML());
		if($this->result === false){
			$this->err = 'API Connection Error';
			return false;
		}elseif($this->result->success == 'false'){
			$this->err = $p->hbn.' '.$this->result->code.': '.$this->result->reason;
			return false;
		}
		
		return $this->result->mailNo;
	}

	public function createOrder(&$p, $pdc=''){
		$d = [
			'reference_no' => $p->hbn,
			'shipping_method' => $pdc, // 'AU0006', AU0003 HZ BC;
			'order_weight' => $p->shipWeight(),
			'order_pieces' => 1,
			'shipper' => [
				'shipper_name' => $p->cnor->name,
				'shipper_countrycode' => 'AU',
				'shipper_city' => '悉尼',
				'shipper_street' => 'U12, 1801 Botnay Road, NSW 2019',
				'shipper_telephone' => '0299257100',
			],
			'consignee' => [
				'consignee_name' => $p->cnee->name,
				'consignee_countrycode' => 'CN',
				'consignee_province' => $p->cnee->state,
				'consignee_city' => $p->cnee->city,
				'consignee_district' => empty($p->cnee->suburb)? $p->cnee->city : $p->cnee->suburb,
				'consignee_street' => $p->cnee->address,
				'consignee_postcode' => $p->cnee->postcode,
				'consignee_telephone' => $p->cnee->tel,
				'consignee_mobile' => $p->cnee->tel,
				'consignee_certificatetype' => 'ID',
				'consignee_certificatecode' => empty($p->cnee->cnid_id)? $p->cnee->cnid_no : $p->cnee->cnid->no,
			],
			'invoice' => [],
		];

		foreach($p->eitems['g'] as $gi=>$g){
			if(empty($p->eitems['q'][$gi])) continue;
			$q = floatval($p->eitems['q'][$gi]);
			$q = empty($q)? 1 : $q;
			$uc = empty($p->eitems['v'][$gi])? 35 : round($p->eitems['v'][$gi] / $q * 0.6);
			if(empty($p->eitems['pid'][$gi])){
				$ge = 'Healthy Product';
				$sku = '';
			}else{
				$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
				$ge = $pd->name;
				$sku = $pd->sku;
				if(!empty($pd->mdata['price_CNJM3'])) $uc = $pd->mdata['price_CNJM3'];
			}
			$d['invoice'][] = [
				'invoice_enname' => $ge,
				'invoice_cnname' => $g,
				'invoice_quantity' => $q,
				'invoice_unitcharge' => $uc,
				'invoice_url' => $sku,
			];
		}
		return $this->wsRequest('createorder', $d);
	}

	public function createOmsOrder(&$p, $pdc=''){
		if(empty($p->cnee->city)) $p->cnee->city = $p->cnee->state;
		$d = [
			'reference_no' => $p->hbn,
			'shipping_method' => $pdc, // 'AU0006', AU0003 HZ BC;
			'order_weight' => $p->swt,
			'order_pieces' => 1,
			'shipper' => [
				'shipper_name' => $p->cnor->name,
				'shipper_countrycode' => 'AU',
				'shipper_city' => '悉尼',
				'shipper_street' => 'U12, 1801 Botnay Road, NSW 2019',
				'shipper_telephone' => '0299257100',
			],
			'consignee' => [
				'consignee_name' => $p->cnee->name,
				'consignee_countrycode' => 'CN',
				'consignee_province' => $p->cnee->state,
				'consignee_city' => $p->cnee->city,
				'consignee_district' => $p->cnee->city,
				'consignee_street' => $p->cnee->addr,
				'consignee_postcode' => $p->cnee->postcode,
				'consignee_telephone' => $p->cnee->tel,
				'consignee_mobile' => $p->cnee->tel,
				'consignee_certificatetype' => 'ID',
				'consignee_certificatecode' => $p->cnee->cnid,
			],
			'invoice' => [],
		];
		$d['invoice'][] = [
			'invoice_enname' => 'Healthy Product',
			'invoice_cnname' => '保健品',
			'invoice_quantity' => 1,
			'invoice_unitcharge' => '50',
			'invoice_url' => '12345678',
		];

		return $this->wsRequest('createorder', $d);
	}

	public function getTrackingNumber($t){
		$p = ['reference_no' => $t];
		return $this->wsRequest('gettrackingnumber', $p);
	}

	public function getDistribute($t){
		$p = ['shipping_method_no' => $t];
		return $this->wsRequest('getdistribute', $p);
	}

	protected function wsRequest($m, $p){
		$client=new SoapClient($this->is_test? 'http://47.90.107.70:8000/webservice/PublicService.asmx?WSDL' : 'http://oms.ytoglobal.com/webservice/PublicService.asmx?WSDL'); //production
		//$this->log('WS '.$m.' Request: '.json_encode($p));
		$r = $client->ServiceEntrance(['appToken' => $this->api_id, 'appKey' => $this->api_key, 'serviceMethod' => $m, 'paramsJson' => json_encode($p)]);
		//$this->log('Response: '.empty($r->ServiceEntranceResult)? 'null' : $r->ServiceEntranceResult);
		return $r;
	}

	protected function request($data, $rc = 0){
		$this->result = false;
		$c = new curl($this->is_test? self::$test_url : self::$url);
		$qs = $c->asPostString([
			'logistics_interface' => $data,
			'data_digest' => base64_encode(md5($data.$this->api_key, true)),
			'clientId' => $this->api_id,
			'type' => 'offline',
		]);

		//$this->log('Query String: '.$qs."\n");

		//$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 10);
		$c->setopt(CURLOPT_POST, true);
		$c->setopt(CURLOPT_POSTFIELDS, $qs);
		if(!$c->exec()){
			if($rc < $this->retry){
				return $this->request($data, $rc+1);
			}else{
				return false;
			}
		}

		$this->log('Response: '.$c->result."\n");
		$this->result = simplexml_load_string($c->result);
		return true;
	}

	public function track($tn){
		return $this->jsonRequest('yto.Marketing.WaybillTrace', '[{"Number":"'.$tn.'"}]');
	}

	public function sendTracking($ts){
		$xml = new SimpleXMLElement('<Message></Message>', LIBXML_NOXMLDECL);
		$header = $xml->addChild('Header');
		$header->addChild('SeqNo', 'PCA'.round(microtime(true) * 1000));
		$header->addChild('TimeStamp', date('Y-m-d H:i:s'));

		$trks = $xml->addChild('Trackings');

		$ttmap = [10 => 10, 12 => 12, 15 => 15, 18 => 18, 20 => 20, 25 => 25, 38 => 60, 40 => 70, 50 => 80];

		foreach($ts as $t){
			$tk = $trks->addChild('Tracking');
			$tk->addChild('WaybillNo', $t->shipment->ref);
			$tk->addChild('ReferenceOrderNo', $t->shipment->hbn);
			$tk->addChild('EventCode', $this->is_test? 'CC' : (empty($ttmap[$t->type])? 'CC' : $ttmap[$t->type]));
			$tk->addChild('EventDetail', $t->activity);
			$tk->addChild('EventLocation', '澳洲转运中心');
			$tk->addChild('City', '悉尼');
			$tk->addChild('EventTime', $t->dt);
			$tk->addChild('ServiceCode', $this->api_id);
			$tk->addChild('CountryCode', $t->type < 40? 'AU' : 'CN');
		}
		$this->globalRequest($xml->asXML());
	}

	public function globalRequest($data){
		$this->result = false;
		$data = trim($data);
		$c = new curl('http://'.($this->is_test? 'test.' : '').'edi.ytoglobal.com/outerEdiTrack/receiveEdiTrack');
		$qs = $c->asPostString([
			'message' => $data,
			'sign' => base64_encode(md5($data.$this->api_id,true)),
			'client_id' => $this->api_id,
		]);
		//var_dump($data);
		//var_dump($qs);

		//$this->log('Query String: '.$qs."\n");

		$c->setopt(CURLOPT_TIMEOUT, 10);
		$c->setopt(CURLOPT_POST, true);
		$c->setopt(CURLOPT_POSTFIELDS, $qs);
		if(!$c->exec()){
			if($rc < $this->retry){
				return $this->request($data, $rc+1);
			}else{
				return false;
			}
		}

		$this->log('Response: '.$c->result."\n");
		$this->result = simplexml_load_string($c->result);
		return true;
	}

	public function jsonRequest($method, $param, $rc = 0){
		$data = [
			'app_key' => $this->api_key,
			'format' => 'jason',
			'method' => $method,
			'timestamp' => date('Y-m-d H:i:s'),
			'user_id' => $this->api_id,
			'v' => '1.0',
		];

		$s = $this->api_key;
		foreach($data as $k=>$v){
			$s .= $k.$v;
		}

		$data['sign'] = md5($s);
		$data['param'] = $param;

		$c = new curl('http://58.32.246.70:8088/MarketingInterface/');

		$qs = $c->asPostString($data);
		//$this->log('Query String: '.$qs);

		//$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 10);
		$c->setopt(CURLOPT_POST, true);
		$c->setopt(CURLOPT_POSTFIELDS, $qs);
		if(!$c->exec()){
			if($rc < $this->retry){
				return $this->jsonRequest($method, $param, $rc+1);
			}else{
				return false;
			}
		}

		$this->log('Response: '.$c->result);
		$this->result = json_decode($c->result);
		return true;
	}

	public function log($m){
		if(!$this->logging) return false;
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'yto_api.log';
		@file_put_contents($lf, date('Y-m-d H:i:s').' '.$m."\n", FILE_APPEND);
	}

//end of class
}