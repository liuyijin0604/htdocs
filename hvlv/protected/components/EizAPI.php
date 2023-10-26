<?php
class EizAPI
{
	const token='$2y$10$nmMMeR9d161n2YXd9Dla..8RWLrItP8exViHZRYEwx0k1/ZS9PH.O';
	public $SHIPPING_METHOD_ID = 0;
	public $TOKEN = "";
	public $CONSIGNMENT_PREFIX = '';
	public $API_KEY = '';
	public $ORG_ID = 0;
	public $fromInfo = [];
	const CREATE_URL = 'https://app.eiz.com.au/api/auth/fulfillments/v2/createAndSolidFulfillments';
	const MANIFEST_URL = 'https://app.eiz.com.au/api/auth/fulfillments/v2/submitConsignments';
	const QUOTE_URL = 'https://app.eiz.com.au/api/auth/fulfillments/v2/quote';
	const CANCEL_URL = 'https://app.eiz.com.au/api/auth/fulfillments/v2/remove';
	const RATE_TYPE = array(
					ImportChargeCode::EIZ_TOLL_SYD_CODE=>'syd',
					ImportChargeCode::EIZ_TOLL_MEL_CODE=>'mel',
					ImportChargeCode::EIZ_TOLL_BNE_CODE=>'bne',
					ImportChargeCode::EIZ_ALLIED_SYD_CODE=>'syd_allied',
					ImportChargeCode::EIZ_ALLIED_MEL_CODE=>'mel_allied',
					ImportChargeCode::EIZ_ALLIED_BNE_CODE=>'bne_allied',
					ImportChargeCode::EIZ_ALLIED_PER_CODE=>'per_allied',
					ImportChargeCode::EIZ_ALLIED_ADL_CODE=>'adl_allied',

					ImportChargeCode::EIZ_BORDER_SYD_CODE=>'syd_border',
					ImportChargeCode::EIZ_BORDER_MEL_CODE=>'mel_border',
					ImportChargeCode::EIZ_BORDER_BNE_CODE=>'bne_border',
					ImportChargeCode::EIZ_BORDER_PER_CODE=>'per_border',
					ImportChargeCode::EIZ_BORDER_ADL_CODE=>'adl_border'
					);


	public function __construct($type=false,$testing=false,$subType = false)
	{
		if($testing)
		{
			$this->SHIPPING_METHOD_ID = 20;
			$this->TOKEN = '$2y$10$nmMMeR9d161n2YXd9Dla..8RWLrItP8exViHZRYEwx0k1/ZS9PH.O';
			if(empty($type)||$type=='syd')
			{
				$this->fromInfo=[
					'suburb'=>"BANKSTOWN AERODROME",
					'postcode'=>"2200",
					'address'=>"1/233 Milperra Rd",
					'state'=>"NSW"
				];
			}elseif($type=='mel')
			{
				$this->fromInfo=[
					'suburb'=>"Sunshine",
					'postcode'=>"3020",
					'address'=>"3B/8 Judge St",
					'state'=>"VIC"
				];
			}elseif($type=='bne')
			{
				$this->fromInfo=[
					'suburb'=>"Moorooka",
					'postcode'=>"4105",
					'address'=>"basement 2/ 30 Gow St",
					'state'=>"QLD"
				];
			}else if($type=='syd_allied')
			{
				$this->fromInfo=[
					'suburb'=>"BANKSTOWN AERODROME",
					'postcode'=>"2200",
					'address'=>"1/233 Milperra Rd",
					'state'=>"NSW"
				];
			}elseif($type=='mel_allied')
			{
				$this->fromInfo=[
					'suburb'=>"Sunshine",
					'postcode'=>"3020",
					'address'=>"3B/8 Judge St",
					'state'=>"VIC"
				];
			}elseif($type=='bne_allied')
			{
				$this->fromInfo=[
					'suburb'=>"Moorooka",
					'postcode'=>"4105",
					'address'=>"basement 2/ 30 Gow St",
					'state'=>"QLD"
				];
			}elseif($type=='per_allied')
			{
				$this->fromInfo=[
					'suburb'=>"Welshpool",
					'postcode'=>"6106",
					'address'=>"Warehouse, 8 Glenferrie Road",
					'state'=>"WA"
				];
			}
			else if($type=='syd_border')
			{
				$this->fromInfo=[
					'suburb'=>"BANKSTOWN AERODROME",
					'postcode'=>"2200",
					'address'=>"1/233 Milperra Rd",
					'state'=>"NSW"
				];
			}elseif($type=='mel_border')
			{
				$this->fromInfo=[
					'suburb'=>"Sunshine",
					'postcode'=>"3020",
					'address'=>"3B/8 Judge St",
					'state'=>"VIC"
				];
			}elseif($type=='bne_border')
			{
				$this->fromInfo=[
					'suburb'=>"Moorooka",
					'postcode'=>"4105",
					'address'=>"basement 2/ 30 Gow St",
					'state'=>"QLD"
				];
			}elseif($type=='per_border')
			{
				$this->fromInfo=[
					'suburb'=>"Welshpool",
					'postcode'=>"6106",
					'address'=>"Warehouse, 8 Glenferrie Road",
					'state'=>"WA"
				];
			}
		}else
		{
			if(empty($type)||$type=='syd')
			{
				$this->SHIPPING_METHOD_ID = 188;
				$this->fromInfo=[
					'suburb'=>"BANKSTOWN AERODROME",
					'postcode'=>"2200",
					'address'=>"1/233 Milperra Rd",
					'state'=>"NSW"
				];
			}elseif($type=='mel')
			{
				$this->SHIPPING_METHOD_ID = 107;
				$this->fromInfo=[
					'suburb'=>"Sunshine",
					'postcode'=>"3020",
					'address'=>"3B/8 Judge St",
					'state'=>"VIC"
				];
			}elseif($type=='bne')
			{
				$this->SHIPPING_METHOD_ID = 107;
				$this->fromInfo=[
					'suburb'=>"Moorooka",
					'postcode'=>"4105",
					'address'=>"basement 2/ 30 Gow St",
					'state'=>"QLD"
				];
			}
			else if($type=='syd_allied')
			{
				$this->SHIPPING_METHOD_ID = 498;
				$this->fromInfo=[
					'suburb'=>"BANKSTOWN AERODROME",
					'postcode'=>"2200",
					'address'=>"1/233 Milperra Rd",
					'state'=>"NSW"
				];
			}elseif($type=='mel_allied')
			{
				$this->SHIPPING_METHOD_ID = 498;
				$this->fromInfo=[
					'suburb'=>"Sunshine",
					'postcode'=>"3020",
					'address'=>"3B/8 Judge St",
					'state'=>"VIC"
				];
			}elseif($type=='bne_allied')
			{
				$this->SHIPPING_METHOD_ID = 498;
				$this->fromInfo=[
					'suburb'=>"Moorooka",
					'postcode'=>"4105",
					'address'=>"basement 2/ 30 Gow St",
					'state'=>"QLD"
				];
			}elseif($type=='per_allied')
			{
				$this->SHIPPING_METHOD_ID = 498;
				$this->fromInfo=[
					'suburb'=>"Welshpool",
					'postcode'=>"6106",
					'address'=>"Warehouse, 8 Glenferrie Road",
					'state'=>"WA"
				];
			}else if($type=='syd_border')
			{
				$this->SHIPPING_METHOD_ID = 487;
				$this->fromInfo=[
					'suburb'=>"BANKSTOWN AERODROME",
					'postcode'=>"2200",
					'address'=>"1/233 Milperra Rd",
					'state'=>"NSW"
				];
			}elseif($type=='mel_border')
			{
				$this->SHIPPING_METHOD_ID = 486;
				$this->fromInfo=[
					'suburb'=>"Sunshine",
					'postcode'=>"3020",
					'address'=>"3B/8 Judge St",
					'state'=>"VIC"
				];
			}elseif($type=='bne_border')
			{
				$this->SHIPPING_METHOD_ID = 488;
				$this->fromInfo=[
					'suburb'=>"Moorooka",
					'postcode'=>"4105",
					'address'=>"basement 2/ 30 Gow St",
					'state'=>"QLD"
				];
			}elseif($type=='per_border')
			{
				$this->SHIPPING_METHOD_ID = 489;
				$this->fromInfo=[
					'suburb'=>"Welshpool",
					'postcode'=>"6106",
					'address'=>"Warehouse, 8 Glenferrie Road",
					'state'=>"WA"
				];
			}
			$this->TOKEN = '$2y$10$nmMMeR9d161n2YXd9Dla..8RWLrItP8exViHZRYEwx0k1/ZS9PH.O';
		}

	}
	
	public static function canDeliver($shipment,$orgRate)
	{
		$or = OrgRate::model()->findByPk($orgRate);
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$postcode = $shipment->cnee->postcode;
		$suburb = $shipment->cnee->suburb;
		$weight = $shipment->weight;

		if(in_array($or->code,ImportChargeCode::EIZ_TOLL_CODES))
		{
			$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code AND suburb=:suburb', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode,':suburb' => $suburb]);
		}else
		{
			$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode]);
		}
		$chargeCode = 'xxxxxxxxx';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}
		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate]);
		if (!empty($zrs))
		{
			return true;
		}else
		{
			return false;
		}
	}

	public static function getCost($orgRate, $suburb, $postcode, $weight)
	{
		$or = OrgRate::model()->findByPk($orgRate);
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$postcode = intval($postcode)+0;
		$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode]);
		$zoneMap2 = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code AND suburb=:suburb', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode,':suburb' => $suburb]);
		if(!empty($zoneMap2))
		{
			$zoneMap = $zoneMap2;
		}
		$chargeCode = 'xxxxxxxxx';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}
		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate]);
		if (!empty($zrs)) {
			// get maximum one
			foreach ($zrs as $zr) {
				$temp = $zr['base'] + $zr['item'];
				if ($zr['nkg'] > 0) {
					$wl = $weight;
					$temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
				} else {
					$temp +=  $weight * $zr['perkg'];
				}
				if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
					$temp = $zr['minimum'];
				}
				$price = max($price, $temp);
			}
		}else
		{
			$o->code = 1;
			$o->msg="no rate for this serivce";
			return $o;
		}

		$o->msg = "success";
		$o->data = $price;
		return $o;
	}

	public static function getZone($shipment)
	{
		$or = OrgRate::model()->findByPk($shipment->mdata['org_rate_id']);
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$postcode = $shipment->cnee->postcode;
		$suburb = $shipment->cnee->suburb;
		$weight = $shipment->weight;
		if(in_array($or->code,ImportChargeCode::EIZ_TOLL_CODES))
		{
			$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code AND suburb=:suburb', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode,':suburb' => $suburb]);
		}else
		{
			$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode]);
		}

		$z1 = '';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$z1 = $zoneMap['z1'];
		}
		return $z1;
	}

	public static function getAlliedDepotCode($shipment)
	{
		$postcode = $shipment->cnee->postcode;
		$suburb = $shipment->cnee->suburb;
		$state = $shipment->cnee->state;
		$zoneMap = AlliedDepot::model()->find('postcode = :postcode and state =:state and suburb=:suburb', [':postcode' =>$postcode ,':state' => $state,':suburb' => trim(strtoupper($suburb))]);
		return empty($zoneMap)?"":$zoneMap->depot;
	}

	public function removeSpecialChars($s){
		return preg_replace(['/[^\d\w\'\"\,\;\.\/\(\):&# \-]+/', '/[\’]+/'], ['', '\''] , $s);
	}

	public function prepareData($shipment)
	{
		$params = new stdClass();
		$params->from_name = substr(Org::COMPANY_NAME_SL, 0, 40);
		$params->from_companyName = substr(Org::COMPANY_NAME_SL, 0, 40);
		$params->from_email = 'imports@toplogistics.com.au';
		$params->from_address1 = $this->fromInfo['address'];
		$params->from_suburb = $this->fromInfo['suburb'];
		$params->from_state = $this->fromInfo['state'];
		$params->from_postcode = $this->fromInfo['postcode'];
		$params->from_phone = Org::IM_COMPANY_PHONE;
		$params->from_country = 'Australia';
		$params->consignments = [];
		$consignment = new stdClass;
		$consignment->shippingMethod_id = $this->SHIPPING_METHOD_ID;
		$consignment->shipTo_ref = $shipment->hbn;
		$consignment->shipTo_name = $shipment->cnee->name;
		if(!empty($shipment->cnee->company))
		{
			$consignment->shipTo_companyName = $shipment->cnee->company;
		}
		$consignment->shipTo_phone = $shipment->cnee->tel;
		$consignment->shipTo_address1 = $shipment->cnee->address;
		$consignment->shipTo_state = $shipment->cnee->state;
		$consignment->shipTo_suburb= $shipment->cnee->suburb;
		$consignment->shipTo_postcode = $shipment->cnee->postcode;
		$consignment->shipTo_country = $shipment->cnee->country;
		$consignment->data = [];

		if(!empty($shipment->packs))
		{
			foreach ($shipment->packs as $key => $package) {
				$consignmentData = new stdClass();
				$consignmentData->qty = 1;
				$consignmentData->weight = $package['weight'];
				$consignmentData->length = $package['length'];
				$consignmentData->width = $package['width'];
				$consignmentData->height = $package['height'];
				$consignment->data[] = $consignmentData;
			}
		}else
		{
			if(!empty($shipment->cbm))
			{
				$thisCBM = (($shipment->weight/$shipment->pkg)/rand(251,280));
				$cbm = $thisCBM*1000000;
				$x = pow($cbm/(3*3*4), 1/3);
				$consignmentData = new stdClass();
				$consignmentData->qty = $shipment->pkg;
				$consignmentData->weight = number_format($shipment->weight/$shipment->pkg);
				if(4*$x>=120)
				{
					$x = pow($thisCBM/(3*3*3), 1/3);
					$consignmentData->length = number_format(3*$x,0,'.','');
					$consignmentData->width = number_format(3*$x,0,'.','');
					$consignmentData->height = number_format(3*$x,0,'.','');
				}else
				{
					$consignmentData->length = number_format(3*$x,0,'.','');
					$consignmentData->width = number_format(3*$x,0,'.','');
					$consignmentData->height = number_format(4*$x,0,'.','');
				}
				$consignment->data[] = $consignmentData;
			}else
			{
				$this->log2file('Time: '.date('Y-m-d H:i:s')."ref:".$shipment->hbn." empty cbm dim and packages");
				return [];
			}
		}
		$params->consignments[] = $consignment;
		return $params;
	}
	public function prepareQuoteData($shipment,$methods)
	{
		$params = new stdClass();
		$params->ids = $methods;
		$params->parcels = [];
		if(!empty($shipment->packs))
		{
			foreach ($shipment->packs as $key => $package) {
				$consignmentData = new stdClass();
				$consignmentData->qty = 1;
				$consignmentData->weight = $package['weight'];
				$consignmentData->length = $package['length'];
				$consignmentData->width = $package['width'];
				$consignmentData->height = $package['height'];
				$params->parcels[] = $consignmentData;
			}
		}else
		{
			if(!empty($shipment->cbm))
			{
				$thisCBM = (($shipment->weight/$shipment->pkg)/rand(251,280));
				$cbm = $thisCBM*1000000;
				$x = pow($cbm/(3*3*4), 1/3);
				$consignmentData = new stdClass();
				$consignmentData->qty = $shipment->pkg;
				$consignmentData->weight = number_format($shipment->weight/$shipment->pkg);
				if(4*$x>=120)
				{
					$x = pow($thisCBM/(3*3*3), 1/3);
					$consignmentData->length = number_format(3*$x,0,'.','');
					$consignmentData->width = number_format(3*$x,0,'.','');
					$consignmentData->height = number_format(3*$x,0,'.','');
				}else
				{
					$consignmentData->length = number_format(3*$x,0,'.','');
					$consignmentData->width = number_format(3*$x,0,'.','');
					$consignmentData->height = number_format(4*$x,0,'.','');
				}
				$params->parcels[] = $consignmentData;
			}else
			{
				$this->log2file('Time: '.date('Y-m-d H:i:s')."ref:".$shipment->hbn." empty cbm dim and packages");
				return [];
			}
		}

		$params->shipFrom = new stdClass();
		$params->shipFrom->from_suburb =$this->fromInfo['suburb'];
		$params->shipFrom->from_postcode = $this->fromInfo['postcode'];
		$params->shipFrom->from_state = $this->fromInfo['state'];
		$params->shipFrom->from_country ="AU";

		$params->shipTo = new stdClass();
		$params->shipTo->shipTo_suburb =  $shipment->cnee->suburb;
		$params->shipTo->shipTo_postcode =  $shipment->cnee->postcode;
		$params->shipTo->shipTo_state = $shipment->cnee->state;
		$params->shipTo->shipTo_country ="AU";
		return $params;
	}

	public static function getAPIInstance($courierId)
	{
		$code = OrgRate::model()->findByPk($courierId)->code;
		return new EizAPI(EizAPI::RATE_TYPE[$code]);
	}

	public static function getAPIInstanceByCode($code)
	{
		return new EizAPI(EizAPI::RATE_TYPE[$code]);
	}


	public function createShipment($s)
	{
		$data=$this->prepareData($s);
		if(empty($data))
		{
			return false;
		}
		$result=$this->request(self::CREATE_URL, 'POST', $data);
		return $result;
	}

	public function manifestShipment($ss)
	{
		$data = ["ids"=>[]];
		foreach ($ss as $key => $s) {
			$data["ids"][] = $s->mdata['eiz_id'];
		}
		$result=$this->request(self::MANIFEST_URL, 'POST', $data);
		return $result;
	}

	public function cancelShipment($ss)
	{
		$data = ["ids"=>[]];
		foreach ($ss as $key => $s) {
			$data["ids"][] = $s->mdata['eiz_id'];
		}
		$result=$this->request(self::CANCEL_URL, 'DELETE', $data);
		return $result;
	}

	public function getQuote($ss,$methods=false)
	{
		if(empty($methods))
		{
			$methods = [$this->SHIPPING_METHOD_ID];
		}
		$data=$this->prepareQuoteData($ss,$methods);
		if(empty($data))
		{
			return false;
		}
		$result=$this->request(self::QUOTE_URL, 'POST', $data);
		return $result;
	}

	public function getQuotePrice($ss)
	{
		$finalResult = [];
		$result = $this->getQuote($ss,[498,188,107]);
		if(!empty($result->data))
		{
			$checks = $result->data->availableQuotes;
			foreach ($checks as $key => $c) {
				if($c->shippingMethodId==498)
				{
					$finalResult['allied'] = ['courier'=>"Allied","amount"=>$c->amount,"surcharge"=>$c->surcharge];
				}elseif($c->shippingMethodId==107||$c->shippingMethodId==188)
				{
					$finalResult['toll'] = ['courier'=>"Toll","amount"=>$c->amount,"surcharge"=>$c->surcharge];
				}
			}
		}
		return $finalResult;
	}

	public function getQuoteTollPrice($ss)
	{
		$finalResult = [];
		$result = $this->getQuote($ss,[107]);
		if(!empty($result->data))
		{
			$checks = $result->data->availableQuotes;
			foreach ($checks as $key => $c) {
				if($c->shippingMethodId==107)
				{
					$finalResult['toll'] = ['courier'=>"Toll","amount"=>$c->amount,"surcharge"=>$c->surcharge];
				}
			}
		}
		if(!empty($finalResult['toll']))
		{
			return $finalResult['toll']["amount"];
		}
		return 0;
	}

	public function getQuoteAlliedPrice($ss)
	{
		$finalResult = [];
		$result = $this->getQuote($ss,[498]);
		if(!empty($result->data))
		{
			$checks = $result->data->availableQuotes;
			foreach ($checks as $key => $c) {
				if($c->shippingMethodId==498)
				{
					$finalResult['allied'] = ['courier'=>"Allied","amount"=>$c->amount,"surcharge"=>$c->surcharge];
				}
			}
		}
		if(!empty($finalResult['allied']))
		{
			return $finalResult['allied']["amount"];
		}
		return 0;
	}

	public Static function getQuotePriceStatic($ss,$orgRate)
	{
		$result = new stdClass();
		$code = $orgRate->code;
		$type = EizAPI::RATE_TYPE[$code];
		$eizAPi = new EizAPI($type);
		$finalResult = [];
		$myResult = $eizAPi->getQuote($ss);
		if(!empty($myResult->data))
		{
			$checks = $myResult->data->availableQuotes;
			foreach ($checks as $key => $c) {
				if(!empty($c->amount))
				{
					$result->code = 0;
					$result->data = $c->amount;
					return $result;
				}else
				{
					$result->code = 1;
				}
			}
		}
		$result->code = 2;
		return $result;
	}

	public function getTrackingEvents($ss)
	{
		$data=["ConnoteList"=>[]];
		foreach ($ss as $s) {
			if(!empty($s->ref))
			{
				$data["ConnoteList"][]=["Connote"=>$s->ref];
			}
		}
		$result=$this->request(self::TRACKING_URL, 'POST', $data);
		return $result;
	}

	public function request($action, $method='POST', $data=null)
	{
		$this->result=false;
		$url= $action;
		$c=new curl($url);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$hdr = $this->build_headers($method, $url);
		if (!empty($data)) {
			$data = AppHelper::safeJsonEncode($data);
			$c->setopt(CURLOPT_POSTFIELDS, $data);
		}
		// var_dump($hdr);
		$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		if (!$c->exec()) {
			$re = new stdclass();
			return $re;
		}
		$this->rescode=$c->rcode;
		$this->result = json_decode($c->result);
		$this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".urldecode($data)."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result.PHP_EOL.PHP_EOL);
		return $this->result;
	}

	private function build_headers($method, $path, $acceptType='application/json')
	{
		//  echo $walltech_date."<br>".$auth."<br>".$hash."<br>";
		return [ 'Content-Type: application/json',
			'Accept: '.$acceptType,
			'Authorization: Bearer '.$this->TOKEN
		];
	}
	 
	protected function log2file($m, $file='ELabel')
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'allied' . DIRECTORY_SEPARATOR;
		$lf .= 'allied_api_'.$file. date('Y-m-d') . '.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

	public function getLabelBarcodes($p)
	{
		$barcodes =[];
		$eizInfo = $p->mdata['eiz']['label'];
		$header = "";
		foreach ($eizInfo as $key => $e) {
			$url =  'https://s3.ap-southeast-2.amazonaws.com/eiz.labels/labels/'.$e['url'];
			$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR, 'pp');//'D:\xampp\htdocs\hvlv\protected\libs\text.pdf';
			$f2 = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR, 'check');
			file_put_contents($f, file_get_contents($url));
			$result = oPDF::pdftotxt( $f,$f2);
			if(preg_match('/(\(00\) )(\d{17})/', $result,$m))
			{
				$barcodes[] = '00'.$m[2];
			}
			if(empty($header))
			{
				$result = preg_split('/\r|\n/', $result);
				$header = [$result[2],$result[4],$result[6]];
			}
		}
		return [$barcodes,$header];
	}
	

}
