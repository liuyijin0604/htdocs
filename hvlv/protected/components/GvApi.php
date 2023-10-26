<?php
class GvApi
{
	const PRODUCTION_TOKEN_ID="80010149";
	const PRODUCTION_TOKEN_NAME="TLA";
	const PRODUCTION_TOKEN = "fc8aa0c7387a88f5beff460ed06b2dbd";
	const PRODUCTION_URL="http://api.globavend.com";
	const TEST_URL = "http://test-api.globavend.net";
	const CHANNELCODE = "2346E";
	public $result;
	public $err;
	public $is_test;
	public $debug;
	public $orgRate;
	public $testUrl;
	private $fromInfo;

	public function __construct($debug = false, $test = false,$orgRateId=0)
	{
		$this->is_test = $test;
		$this->debug = $debug;
		$this->orgRate = OrgRate::model()->findByPk($orgRateId);
		if($this->is_test)
		{
			$this->testUrl = self::TEST_URL;
		}else
		{
			$this->testUrl = self::PRODUCTION_URL;
		}

		if(!empty($this->orgRate))
		{
			$facility = $this->findTheFacility($this->orgRate);
			$type = str_replace('WW', '', $facility);
			if(empty($type)||$type=='SYD')
			{
				$this->fromInfo=[
					'suburb'=>"BANKSTOWN AERODROME",
					'postcode'=>"2200",
					'address'=>"1/233 Milperra Rd",
					'state'=>"NSW"
				];
			}elseif($type=='MEL')
			{
				$this->fromInfo=[
					'suburb'=>"Sunshine",
					'postcode'=>"3020",
					'address'=>"3B/8 Judge St",
					'state'=>"VIC"
				];
			}elseif($type=='BNE')
			{
				$this->fromInfo=[
					'suburb'=>"Coopers Plains",
					'postcode'=>"4108",
					'address'=>"6/55 Musgrave Rd",
					'state'=>"QLD"
				];
			}elseif($type=='PER')
			{
				$this->fromInfo=[
					'suburb'=>"Welshpool",
					'postcode'=>"6106",
					'address'=>"Warehouse, 8 Glenferrie Road",
					'state'=>"WA"
				];
			}
		}
	}
	
	public static function getAPIInstance($courierId)
	{
		return new GvAPI(false,false,$courierId);
	}
		
	public function findTheFacility($orgRate = false)
	{
		if($orgRate!=null)
		{
			return $orgRate->mdata['facility'];
		}else
		{
			return $this->orgRate->mdata['facility'];
		}
	}
	 
	/**
	 * create shipment Order;
	 * @param  $ss is an Array of shipment
	 */
	public function createShipment($shipment)
	{
		$params=[];
		if(!empty($shipment->mdata['org_id'])&&$shipment->mdata['org_id']==Org::ORGID_COURIER_GV_AUPOST)
		{
			$longRefs = $shipment->getParcelLongRef($shipment->mdata['org_id']);
			if(is_array($longRefs))
			{
				foreach ($longRefs as $key => $longRef) {
					$params= $this->prepareData($shipment,$longRef);
					if(empty($params)) return false;
					$result=$this->httpPost($this->testUrl.'/sys/order/batchCreateOrder', $params);
				}
			}else
			{
				$params= $this->prepareData($shipment,$longRefs);
				if(empty($params)) return false;
				$result=$this->httpPost($this->testUrl.'/sys/order/batchCreateOrder', $params);
			}
		}
		return $result;
	}

	public function prepareData($shipment,$longRef)
	{
		$predataRequest = new stdClass();
		$predata=new stdClass();
		$predata->trackingNumber = $longRef;
	 	$predata->customerOrderNumber = $longRef;
	 	$predata->channelCode = self::CHANNELCODE;
        $predata->countryCode = "AU";
        $cnee_addr = $this->removeSpecialChars($shipment->cnee->address);
        $predata->dAddress = $cnee_addr;
        $predata->dCity = $shipment->cnee->suburb;
        $predata->dContact = substr($this->removeSpecialChars($shipment->cnee->name), 0, 35);
        $predata->dCompany = substr($this->removeSpecialChars($shipment->cnee->company), 0, 49);
        $predata->dProvince = $shipment->cnee->state;
        $predata->dCountry = 'AU';
        $predata->dPostCode = $shipment->cnee->postcode;
        $predata->dTel = substr($shipment->cnee->tel, 0, 20);
        $predata->dEmail = substr($shipment->cnee->email, 0, 50);

        $name=$shipment->agent_id;
		$shipper = Org::model()->findByPk($this->orgRate->mdata['ddpt_id']);
		$org=$shipment->agent;
		if (!empty($org->extra['delivery_label_name'])) {
			$name=$org->extra['delivery_label_name'];
		}
		if($shipper->suburb=='BANKSTOWN AERODROME')
		{
			$shipper->postcode = '2200';
		}

		$predata->shipperName=$name;
		$predata->shipperAddressLine1=substr($this->fromInfo['address'], 0, 80);
		$predata->shipperCity=substr($this->fromInfo['suburb'], 0, 80);
		$predata->shipperState=substr($this->fromInfo['state'], 0, 80);
		$predata->shipperPostcode=substr($this->fromInfo['postcode'], 0, 80);
		$predata->shipperCountry='Australia';
		$predata->shipperPhone=substr($shipper->phone, 0, 20);


        $predata->sAddress = substr($this->fromInfo['address'], 0, 80);
        $predata->sCompany = "Top Logistics Australia";
        $predata->sCity = substr($this->fromInfo['suburb'], 0, 80);
        $predata->sContact = "Top Logistics Australia";
        $predata->sProvince = substr($this->fromInfo['state'], 0, 80);
        $predata->sCountry = "AU";
        $predata->sPostCode = substr($this->fromInfo['postcode'], 0, 80);
        $predata->sTel = substr($shipper->phone, 0, 20);
        $predata->sEmail = "imports@toplogistics.com.au";
        $predata->goodsType ="General cargo";
        $predata->cargoTotalValue = 1;
        $predata->cargoTotalWeight = $shipment->weight/$shipment->pkg;
        $predata->insuranceAmount = $shipment->insurance;
        $predata->totalPieces = 1;

        $orderDeclarationDto = new stdClass();
        $orderDeclarationDto->dNameEn = substr($shipment->getGoods(), 0, 50);
        $orderDeclarationDto->dQuantity = 1;
        $orderDeclarationDto->dValue = 1;
        $orderDeclarationDto->dWeight = $shipment->weight/$shipment->pkg;
        

        $predata->orderDeclaration = [$orderDeclarationDto];
        $predataRequest->uid = self::PRODUCTION_TOKEN_ID;
        $predataRequest->order = [$predata];
        $data = json_encode($predataRequest);
        $md5 = md5(self::PRODUCTION_TOKEN_ID.self::PRODUCTION_TOKEN.$data);
        $vc = base64_encode($md5);

        $params = [];
        $params['secretKey'] = $vc;
		$params['data'] = $data;

		return $params;
	}

	public function removeSpecialChars($s){
		return preg_replace(['/[^\d\w\'\"\,\;\.\/\(\):&# \-]+/', '/[\’]+/'], ['', '\''] , $s);
	}


	protected function httpPost($endpoint, $params = [])
	{
		// $this->genSign($params);
		// Set the curl parameters
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		// $header = [];
		// $header[] = 'Content-Type:application/json';
		// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$httpResponse = curl_exec($ch);
		curl_close($ch);

		if (!$httpResponse) {
			return ['done' => false];
		}

		// Extract the response details
		$this->log2file(json_encode($params));
		$this->log2file($httpResponse);
		$httpParsedResponseAr = json_decode($httpResponse, true);

		if(!is_array($httpParsedResponseAr))
		{
			return ['done' => false,'status' => 0];
		}

		if (sizeof($httpParsedResponseAr) == 0) {
			return ['done' => false];
		}

		return array_merge($httpParsedResponseAr, ['done' => true]);
	}
	 
	protected function log2file($m, $file='ELabel')
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'GVAPI' . DIRECTORY_SEPARATOR;
		$lf .= 'GV_api_'.$file. date('Y-m-d') . '.log';
		return file_put_contents($lf, $m."\n", FILE_APPEND);
	}

}
