<?php
class HvlvJavaAPI extends APILog
{
	protected $domain = 'http://localhost:8700/';
	protected $apiDomain = 'http://localhost:8819/';//'http://os.toplogistics.com.au:8819/';
	protected $api_user = 'pcae';
	protected $api_key = 'd47bf1edad3c73d2748231e0bf2b891e1737cbde232009f488854b5f325a7c27';
	protected $type = "";
	protected $courier = "";
	protected $prefix = "";
	const RATE_TYPE = [
		106=>'SYD',
		218=>'MEL',
		530=>'BNE',
		811=>'PER'
	];


	public function __construct($type="SYD",$courier = "",$prefix="")
	{
		$this->type = $type;
		$this->courier = $courier;
		$this->prefix = $prefix;
		parent::__construct("tla",$type);
	}
	public function recognize($barcode)
	{
		$params = [];
		$params['data'] = ["barcode"=>"".$barcode];
		return $this->httpPost('shipment/recognize', $params);
	}

	public function scanShipment($barcode,$userId,$type,$warehouseId)
	{
		$params = [];
		$params['data'] = ["barcode"=>"".$barcode,"userId"=>$userId,"type"=>$type,"warehouse"=>"".$warehouseId];
		return $this->httpPostTry('shipment/scan', $params);
	}

	public function checkAlliedHomeFee($shipment)
	{
		$params = [];
		//pre process data
		if($shipment->weight<1)
		{
			$shipment->weight=1;
		}

		if(!empty($shipment->packs))
		{
			$packages = $shipment->packs;
		}else
		{
			$packages = json_decode($shipment->packages,true);
		}
		$i = 0;
		if(is_array($packages))
		{
			foreach ($packages as $key => $package) {
				if($package['weight']<1) $packages[$key]['weight'] = 1;
			}
		}else
		{
			$packages = [];
		}
		
		$shipment->packs = $packages;


		$data = $this->prepareData($shipment);
		$data->courier = $this->courier;
		$data->type = $this->type;
		$data->prefix = $this->prefix;
		$params['data'] = $data;
		return $this->httpPost('api/getAlliedCharge',$params,$this->apiDomain);
	}

	public function createShipment($shipment)
	{
		$params = [];
		$data = $this->prepareData($shipment);
		$data->courier = $this->courier;
		$data->type = $this->type;
		$data->prefix = $this->prefix;
		$params['data'] = $data;
		return $this->httpPost('api/createShipment',$params,$this->apiDomain);
	}

	public function manifestShipment($shipment)
	{
		$params = [];
		$data = $this->prepareData($shipment);
		$data->courier = $this->courier;
		$data->type = $this->type;
		$params['data'] = $data;
		//return $params['data'];
		return $this->httpPost('api/manifestShipment',$params,$this->apiDomain);
	}

	public function trackShipment($shipment)
	{
		$params = [];
		$data = $this->prepareData($shipment);
		$data->courier = $this->courier;
		$data->type = $this->type;
		$data->prefix = $this->prefix;
		$params['data'] = $data;
		return $this->httpPost('api/trackShipment',$params,$this->apiDomain,false);
	}


	public function prepareData($shipment,$isGen = true,$isCreate = false)
	{
		$data = new stdClass;
		$shipper = new stdClass;
		$consignee = new stdClass;

		$cnor = Addr::model()->findByPk($shipment->cnor_id);
		if($isCreate)
		{
			$shipper->name = $cnor->name;
			$shipper->address = $cnor->address;
			$shipper->state = $cnor->state;
			$shipper->city = $cnor->city;
			$shipper->suburb = $cnor->suburb;
			$shipper->postcode = $cnor->postcode;
			$shipper->phone = $cnor->tel;
			$shipper->email = $cnor->email;
		}
		else
		{
			$shipper->name = "Top Logistics Australia";
			$shipper->company = "Top Logistics Australia";
			$shipper->address = $this->fromInfo['address'];
			$shipper->state = $this->fromInfo['state'];
			$shipper->city = $this->fromInfo['suburb'];
			$shipper->suburb = $this->fromInfo['suburb'];
			$shipper->postcode = $this->fromInfo['postcode'];
			$shipper->phone = Org::IM_COMPANY_PHONE;
			$shipper->email = "impors@toplogistics.com.au";
		}

		$cnee = $shipment->cnee;
		$consignee->name = $cnee->name;
		$consignee->address = $cnee->address;
		$consignee->state = $cnee->state;
		$consignee->city = $cnee->city;
		$consignee->suburb = $cnee->suburb;
		$consignee->postcode = $cnee->postcode;
		$consignee->phone = $cnee->tel;
		$consignee->company = $cnee->company;
		$consignee->email = $cnee->email;

		$data->cust_ref = $shipment->cref;
		$data->type = 10;
		$data->packs = $shipment->pkg;
		$data->weight = $shipment->weight;
		$data->packages = [];
		if(!empty($shipment->packs))
		{
			$packages = $shipment->packs;
		}else
		{
			$packages = json_decode($shipment->packages,true);
		}
		$i = 0;
		if(is_array($packages))
		{
			foreach ($packages as $package) {
				$data->packages[$i] = $package;
				$data->packages[$i]['cbm'] = number_format(($data->packages[$i]['length']*$data->packages[$i]['width']*$data->packages[$i]['height']/1000000),4,'.','');
				$i++;
			}
		}
		$data->shipper = $shipper;
		$data->consignee = $consignee;
		// $itemArr = [];
		// if(!empty($shipment->eitems))
		// {
		// 	$items = json_encode($shipment->eitems);
		// 	$items = json_decode($items,true);
		// }else
		// {
		// 	$items = json_decode($shipment->items,true);
		// }
		
		// foreach ($items['g'] as $i => $value) {
		// 	$itemArr[] = ["type"=>"O", "name"=>$items['g'][$i], "name_zh"=>@$items['g_zh'][$i], "qty"=> $items['q'][$i], "price"=> $items['v'][$i],"hs"=> @$items['hs'][$i], "sku"=> ""];
		// }
		// $data->items = $itemArr;
		$data->chargecode = "";
		$data->barcode = 1;
		$data->direct = 0;
		$data->connote = $shipment->hbn;
		$data->date = (!empty($shipment->mdata['scan_time'])?$shipment->mdata['scan_time']:date("Y-m-d H:i:s"));
		$data->instruction = "No split deliveries;ATL;";
		$data->hbn = $shipment->hbn;
		$data->cref = $shipment->cref;
		$data->agent_id = $shipment->agent_id;
		$data->fast_horse_ref = !empty($shipment->mdata['fast_horse_ref']) ? $shipment->mdata['fast_horse_ref'] : '';
		if(!empty($isGen))
		{
			$data->ref = $shipment->ref;
		}
		return $data;
	}

	public function getTrackingEvents($ref)
	{
		$params = [];
		$params['data'] = ["ref"=>"".$ref];
		return $this->httpPost('api/tracking',$params,$this->apiDomain);
	}

	public function orderAochenStorage($data)
	{
		$params = [];
		$params['data'] = $data;
		return $this->httpPost('api/orderAochenStorage', $params,$this->apiDomain);
	}

	public function checkAochenOrder($data)
	{
		$params = [];
		$params['data'] = $data;
		return $this->httpPost('api/checkAochenOrder', $params,$this->apiDomain);
	}

	
	protected function getRequestUrl($endpoint, $params = [],$domain="")
	{
		if(empty($domain))
		{
			$domain = $this->domain;
		}

		if ($params) {
			$endpoint .= '?';
			foreach ($params as $k => $v) {
				$endpoint .= $k . '=' . $v . '&';
			}
		}

		return $domain . '/' . $endpoint;
	}

	protected function httpGet($endpoint, $params = [])
	{
		$this->genSign($params);
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl($endpoint, $params));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$httpResponse = curl_exec($ch);
		curl_close($ch);

		if (!$httpResponse) {
			return ['done' => false];
		}

		// Extract the response details
		$this->log2file(json_encode($params),'java');
		$this->log2file($httpResponse,'java');
		$httpParsedResponseAr = json_decode($httpResponse, true,'java','tla');

		if (sizeof($httpParsedResponseAr) == 0) {
			return ['done' => false];
		}

		return array_merge($httpParsedResponseAr, ['done' => true]);
	}

	protected function httpPost($endpoint, $params = [],$domain="",$log = true)
	{
		if($log)
		{
			$this->log2file(date("Y-m-d H:i:s").":".json_encode($params),'java');
		}
		$this->genSign($params);
		// Set the curl parameters
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl($endpoint,[],$domain));
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
		if($log)
		{
			$this->log2file(date("Y-m-d H:i:s").":".$httpResponse,'java');
		}
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

	protected function httpPostTry($endpoint, $params = [],$domain="")
	{
		$this->genSign($params);
		try{
			// Set the curl parameters
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl($endpoint,[],$domain));
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
				Emailog::sendEmailTo('gero@toplogistics.com.au',date("Y-m-d H:i:s").": ".$this->getRequestUrl($endpoint,[],$domain)." url can not be reached"," Java Server error");
				$this->log2file(date("Y-m-d H:i:s").": ".$this->getRequestUrl($endpoint,[],$domain)." url can not be reached",'java');
				return ['done' => false];
			}

			// Extract the response details
			$this->log2file(date("Y-m-d H:i:s").":".json_encode($params),'java');
			$this->log2file(date("Y-m-d H:i:s").":".$httpResponse,'java');
			$httpParsedResponseAr = json_decode($httpResponse, true);

			if(!is_array($httpParsedResponseAr))
			{
				return ['done' => false,'status' => 0];
			}

			if (sizeof($httpParsedResponseAr) == 0) {
				return ['done' => false];
			}

			return array_merge($httpParsedResponseAr, ['done' => true]);
		} catch (Exception $ex) {
			Emailog::sendEmailTo('gero@toplogistics.com.au',date("Y-m-d H:i:s").": ".$ex->getMessage()," Java Server error");
			$this->log2file(date("Y-m-d H:i:s").": ".$ex->getMessage(),'java');
			return [];
		}

		return [];
	}

	protected function httpPut($endpoint, $params = [])
	{
		$this->genSign($params);
		// Set the curl parameters
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl($endpoint));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		// $header = [];
		// $header[] = 'Content-Type:application/json';
		// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$httpResponse = curl_exec($ch);
		curl_close($ch);

		if (!$httpResponse) {
			return ['done' => false];
		}

		// Extract the response details
		$this->log2file(json_encode($params),'java');
		$this->log2file($httpResponse,'java');
		$httpParsedResponseAr = json_decode($httpResponse, true);

		if(!is_array($httpParsedResponseAr))
		{
			return ['done' => false,'status' => 0];
		}

		if (sizeof($httpParsedResponseAr) == 0) {
			return ['done' => false,'status' => 0];
		}

		return array_merge($httpParsedResponseAr, ['done' => true]);
	}

	protected function genSign(&$params)
	{
		$params['data'] = json_encode($params['data']);
		ksort($params);
		$s = $this->api_key;
		foreach ($params as $k => $v) {
			if ($k == 'sign' || empty($v)) continue;
			$s .= $k . $v;
		}
		$s .= $this->api_key;
		$params['sign'] = strtoupper(md5($s));
	}


    public function canDeliver($shipment,$orgRate)
	{
		$or = OrgRate::model()->findByPk($orgRate);
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$postcode = $shipment->cnee->postcode;
		$suburb = $shipment->cnee->suburb;
		$weight = $shipment->courierWeight();
		$hasOne = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid and suburb!="" ', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id]);
		if(!empty($hasOne))
		{
			$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code AND upper(suburb)=:suburb', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode,':suburb' => strtoupper($suburb)]);
		}else
		{
			$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode]);
		}

		$chargeCode = 'xxxxxxxxx';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}
		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo <= :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate]);

		if (!empty($zrs))
		{
			return true;
		}else
		{
			return false;
		}
	}

    public static function getCost($orgRate, $suburb, $postcode, $weight,$noRemote=false,$isApi = false,$isNoFuel = false,$shipment = null)
	{
		$or = OrgRate::model()->findByPk($orgRate);
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$postcode = intval($postcode)+0;
		$hasOne = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid and suburb!="" ', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id]);
		if(!empty($hasOne))
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
		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo <= :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate]);
		if (!empty($zrs)) {
			// get maximum one
			foreach ($zrs as $zr) {
				$temp = $zr['base'] + $zr['item'];
				if ($zr['nkg'] > 0) {
					$wl = $weight-$zr['min_incl'];
					$temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
				} else {
					$weight = $weight-$zr['min_incl'];
					$temp +=  $weight * $zr['perkg'];
				}
				if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
					$temp = $zr['minimum'];
				}
				$price = max($price, $temp);
			}

			if($isApi)
			{
				// $orgRateService = new OrgRateService();
				// $levyCostRate = $orgRateService->shipmentRemotePercentageRate($shipment,$or->id);
				// if(!empty($levyCostRate))
				// {
				// 	$levySurcharge = $price*$levyCostRate;
				// 	$price+=$levySurcharge;
				// }
			}


			if(!$noRemote)
			{
				$remoteCostRate = RemoteChargeRate::model()->find("rate_id = :rateId and (postcode =:postcode or CONCAT('0',postcode)=:postcode) and upper(suburb) = :suburb and (perkg+base)>0",[":rateId"=>$or->id,":postcode"=>$postcode,":suburb"=>strtoupper($suburb)]);
				if(!empty($remoteCostRate))
				{
					$remotrSurcharge = $weight*$remoteCostRate->perkg+$remoteCostRate->base;
					$price+=$remotrSurcharge;
				}
			}


			if(!empty($or->mdata["fuel"])&&!$isNoFuel)
			{
				$price=$price+$price*$or->mdata["fuel"];
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

		$hasOne = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid and suburb!="" ', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id]);
		if(!empty($hasOne))
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

	public function cancelFastHorseOrder($shipment)
	{
		$params = [];
		$data = new stdClass;
		$data->ref = $shipment->ref;
		$params['data'] = $data;
		return $this->httpPost('api/cancelFastHorseOrder', $params, $this->apiDomain);
	}

	public function decryptFastHorseTracking($data)
	{
		$params = [];
		$params['data'] = $data;
		return $this->httpPost('api/decryptFastHorseOrder', $params, $this->apiDomain);
	}

	public function canDeliverImile($shipment)
	{
		$params = [];
		$data = $this->prepareData($shipment);
		$data->courier = $this->courier;
		$data->type = $this->type;
		$data->prefix = $this->prefix;
		$params['data'] = $data;
		return $this->httpPost('api/canDeliverImile',$params,$this->apiDomain);
	}
}
