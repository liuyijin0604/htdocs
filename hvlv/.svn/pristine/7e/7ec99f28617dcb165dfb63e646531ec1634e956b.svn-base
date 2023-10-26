<?php
class FastwayAPI
{
	const TRACKING_DIRECT_URL = 'https://www.fastway.com.au/courier-services/track-your-parcel?l=';

	private static $api_accounts = [
		// 'syd' => ['id' => '46424', 'key' => 'c17c64585c67180170dd504b75ca44ad'],
		// 'mel' => ['id' => '63400', 'key' => '559c566e2cbb9640f290f2fee53c5be6'],
		'syd' => [
			'id' => 'fw-fl2-SYD0160059-19a58ea694c6',
			'secret' => 'd91ddeeb-be55-400c-833d-d40cea0fb964',
			'senderInfo' => [
					'SenderName' => 'Top Logistics',
					'SenderCompany' => '',
					'SenderAddr1' => '1/233 Milperra Rd',
					'SenderAddr2' => '',
					'SenderSuburb' => 'Bankstown Aerodrome',
					'SenderPostcode' => '2200',
					'SenderPhone' => '0290668200',
					'SenderEmail' => 'donotreply@toplogistics.com.au'
			]
		],
		'mel' => [
			'id' => 'fw-fl2-MEL0004661-874c367b9ea8',
			'secret' => '58c8bec3-3f1f-4594-a593-91d09dde4a45',
			'senderInfo' => [
					'SenderName' => 'Top Logistics',
					'SenderCompany' => '',
					'SenderAddr1' => '3B/8 Judge St',
					'SenderAddr2' => '',
					'SenderSuburb' => 'Sunshine',
					'SenderPostcode' => '3020',
					'SenderPhone' => '0290668200',
					'SenderEmail' => 'donotreply@toplogistics.com.au'
			]
		],
	];

	public $result;
	public $rescode;
	public $err;
	public $is_test;
	private $uid = '';
	private $secret = '';

	//Old API compatibility
	private $userID = '46424';
	private $apikey = 'c17c64585c67180170dd504b75ca44ad';
	public $origin = 'SYD';
	public $senderInfo = [];
	private static $api_url = 'https://au.api.fastway.org/v4/';

	private static $scope = 'fw-fl2-api-au';
	private static $url = 'https://api.myfastway.com.au';
	private static $token_url = 'https://identity.fastway.org/connect/token';
	
	private static $test_url = 'https://uat.api.myfastway.com.au';
	private static $test_token_url = 'https://uat.identity.fastway.org/connect/token';

	private $token;

	public static function getDirectTrackingUrl($no)
	{
		return self::TRACKING_DIRECT_URL . $no;
	}

	public function __construct($debug = false, $test = false, $type = 'syd')
	{
		if (isset(self::$api_accounts[$type])) {
			$this->uid = self::$api_accounts[$type]['id'];
			$this->secret = self::$api_accounts[$type]['secret'];
			// $this->origin = strtoupper($type);
			// $this->senderInfo = self::$api_accounts[$type]['senderInfo'];
		}
		$this->is_test = $test;
		$this->debug = $debug;
	}

	protected function _prepEvents($scans)
	{
		if (empty($scans)) {
			return [];
		}
		$events = [];
		foreach ($scans as $scan) {
			$events[] = [
				'date' => preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '\\3-\\2-\\1', $scan->Date), // scan date
				'type' => $scan->Type, // scan type : 'P' pickup, 'T' transit, 'D' delivery
				'where' => trim($scan->Name), // currently location
				'desc' => trim($scan->StatusDescription), // status description
				'desc1' => trim($scan->Description),
				'premove' => in_array($scan->Status, ['PP2', 'PP5']),
			];
		}
		return $events;
	}

	/**
	* get tracking events for specified consignment
	* refer to :
	* http://au.api.fastway.org/v2/docs/detail?ControllerName=tracktrace&ActionName=detail&api_key=
	* http://au.api.fastway.org/v2/tracktrace/detail/3a0000253492/6?api_key=YOUR_API_KEY
	* @param $lblNumber
	*/
	public function getTracking($lblNumber)
	{
		$action = 'tracktrace/detail';
		$action .= '/' . $lblNumber . '/1';  // 1 is country code as Australia
		$action .= '?api_key=' . $this->apikey;
		$result = $this->request($action);

		$events = [];
		if (!empty($result->result)) {
			$events = $this->_prepEvents($result->result->Scans);
		}
		return $events;
	}

	public function massTracking($ls)
	{
		$action = 'tracktrace/massdetail';
		$action .= '?api_key=' . $this->apikey;
		$res = $this->request($action, 'POST', 'LabelNoList='.implode(';', $ls));
		if (empty($res->result)) {
			return [];
		}
		$rs = [];
		foreach ($res->result as $r) {
			$rs[$r->LabelNumber] = $this->_prepEvents($r->Scans);
		}
		return $rs;
	}

	public function addTracking($lblNumber, $scanCode, $ts=null)
	{
		if (!in_array($scanCode, ['PP2', 'PP5'])) {
			return;
		}
		$action = 'electroniclabels/generatetrackingevent?LabelNo='.$lblNumber.'&ScanCode='.$scanCode.'&CourierNo=NAC&RFCode=SYD&TimeStamp='.rawurlencode(date('d/m/y H:i:s', empty($ts)? time() : $ts)).'&api_key=198ae99d1afc819dc01af69c2b2b58b2';
		$r = $this->request($action);
		return isset($r->result)? $r->result : false;
	}

	/**
	* add a consignment to Fastway
	* return consignement information
	* @param $shipment
	*/
	public function addConsignment(&$shipment)
	{

		// check the destination if in non-delivery areas
		if (!self::isInDeliveryArea($shipment->cnee->postcode, $shipment->cnee->suburb)) {
			$this->log2file('Time: '.date('Y-m-d H:i:s') . "\n" . 'shipment : ' . $shipment->hbn." not in delivery area\n\n");
			return false;
		}

		$packaging =$this->getProductCode($shipment->cnee->postcode, $this->roundWeight($shipment->weight));
		if ($shipment->cnee->postcode=='3451'&&strtoupper($shipment->cnee->suburb)=="CAMPBELLS CREEK") {
			if ($this->roundWeight($shipment->weight)<=5) {
				$packaging=16; //Small Flat Rate Box
			}
		}
		if ($shipment->cnee->postcode=='4751'&&strtoupper($shipment->cnee->suburb)=="GREENMOUNT") {
			if ($this->roundWeight($shipment->weight)<=5) {
				$packaging=16; //Small Flat Rate Box
			}
		}
		if ($shipment->cnee->postcode=='4510'&&preg_match("/^BELLMERE$|^BEACHMERE$/i", $shipment->cnee->suburb)) {
			if ($this->roundWeight($shipment->weight)<=5) {
				$packaging=16; //Small Flat Rate Box
			}
		}
		if ($packaging < 0) {
			$this->log2file('Time: '.date('Y-m-d H:i:s') . "\n" . 'shipment : ' . $shipment->hbn  ." product code not found\n\n");
			return false;
		}

		$action = 'fastlabel/addconsignment';
		$data = 'UserID=' . $this->userID;
		// $data .= '&ContactName='. $shipment->cnee->name;
		$data .= '&ContactMobile=' . $shipment->cnee->tel;
		$data .= '&CompanyName='.$shipment->cnee->name;
		$data .= '&Address1=' . $shipment->cnee->address;
		$data .= '&Suburb='. $shipment->cnee->suburb;
		$data .= '&Postcode=' . $shipment->cnee->postcode;

		$data .= '&Items[0].Weight=' . $this->roundWeight($shipment->weight);
		//$data .= '&Items[0].Quantity='. $shipment->pkg;
		$data .= '&Items[0].Quantity=1';
		$data .= '&Items[0].Packaging='.$packaging;
		$data .= '&api_key=' . $this->apikey;

		$result = $this->request($action, 'POST', $data);
		$shipment->mdata['manifest_weight']=$this->roundWeight($shipment->weight);
		$shipment->updateMeta();
		if ($result) {
			return $result->result;
		}
		return false;
	}

	public function roundWeight($w,$state = false)
	{
		if(!empty($state)&&strtoupper($state) == "WA")
		{
			if ($w <= 0.3) {
				return 0.3+round((rand(0,100)/100)*0.2,2);
			}
		}
		return $w;
		
		if ($w <= 0.3) {
			return $w;
		} elseif ($w <= 0.5) {
			return round($w * 12) / 20;
		} elseif ($w <= 1) {
			return 0.35+round(rand(0,100)/100*0.15,2);
		} elseif ($w <= 2) {
			return 0.45+round(rand(0, 100)/100*0.05, 2);//round($w * 5) / 10;//0.35+round(rand(0,100)/100*0.14,2);
		} elseif ($w<=3) {
			return 0.85+round(rand(0, 100)/100*0.14, 2);
		} elseif ($w<=4) {
			return 1.85+ round(rand(0, 100)/100*0.14, 2);//0.85+ round(rand(0,100)/100*0.14,2);
		} elseif ($w <= 5) {
			return 2.85+round(rand(0, 100)/100*0.14, 2);
		} elseif ($w > 5 && $w <= 9) {
			return 5;
		} elseif ($w <= 12.5) {
			return 9;
		} elseif ($w > 12.5) {
			return $w - 3.5;
		} else {
			return $w;
		}
	}

	/**
	* based on shipment check to see if Fastway can delivery it
	* @param $shipment
	* @return bool
	*/
	public function weCanDelivery($shipment)
	{
		return self::isInDeliveryArea($shipment->cnee->postcode, $shipment->cnee->suburb) && $this->getProductCode($shipment->cnee->postcode, $shipment->weight) > 0;
	}

	/**
	* The below table is what we will be using.
	Min Weight (kg)	Max Weight (kg)	Product Type	Code	Location
	0	            0.5	                A5   	7	Fastway National Network
	0.5	            1	                    A4	   6	Fastway National Network
	1	            2	      Small Flat Rate Box	16	Fastway National Network  (0-5kg)
	2	            3	                    A3	   5	Fastway National Network
	3	            5	                    A2	   4	Fastway National Network
	5	            10	     Medium Flat Rate Box	17	Fastway National Network  (5-10kg)
	10	            20	    Large Flat Rate Box	18	Fastway National Network   (10-20kg)
	0	            25	        Brown	           1	Fastway Local

	A few points on the above table;
	-	Max Weight columns are specifying the absolute maximum weight you can consign with for that product
	-	Product Type, you can ignore this as it’s our internal product description.  You will be overriding this
	-	Code, this is the number you will use when consigning with the API.
	-	Location, denotes where the parcel will go to.  So as a basic and easy rule to remember;
	o	Any parcel that is being delivered locally (Sydney to Sydney) use code 1.
	o	Any other parcel which is being delivered by a Fastway Courier;
	1.	Get the weight
	2.	Compare it to the table
	3.	Use the applicable code

	Using Code to choose packaging is as simple as using the determined number.  So if a package was 3kg, the best cost option for you is product code 5. Your API call would look something like this
	-	Green is the weight
	-	Yellow is the product code
	http://au.api.fastway.org/v3/fastlabel/addconsignment?UserID=&CompanyName=Sydney%20Company&Address1=Sydney%20Address%201&Suburb=Sydney&Postcode=2000&Items[0].Weight=3&Items[0].reference=SYD-Reference&Items[0].Quantity=1&Items[0].Packaging=5&api_key=
	* @param $postcode
	* @param $weight
	*/
	public function getProductCode($postcode, $weight)
	{
		if (ZoneMap::model()->count('org_id = 115 AND zone_id = 1 AND z1 = :z1 AND pc_lo <= :pc AND pc_hi >= :pc', [':pc' => $postcode, ':z1' => $this->origin]) > 0) { //local
			return 1;
		} else {
			// this array table bases on Fastway API specification
			$weightsCode = [
				[0,0.5,7],[0.5,1,6],[1,2,16],[2,3,5],[3,5,4],[5,10,17],[10,20,18]
			];

			foreach ($weightsCode as $code) {
				if ($weight > $code[0] && $weight <= $code[1]) {
					return $code[2];
				}
			}
		}

		if ($this->debug == 1) {
			$this->log2file('Cannot find Product Type for postcode: '. $postcode . ' , weight : ' . $weight);
		}
		return -1; // invalid , not found
	}

	/**
	* Because Fastway does not delivery for all areas
	* we need to check to see if the destination is in their delivery areas based on specified
	* suburb, postcode and state
	* @param $suburb
	* @param $postcode
	* @param $state
	* @return bool
	*/
	public static function isInDeliveryArea($postcode, $suburb = false)
	{
		$fad = Yii::app()->cache->get('fastwayArea');
		if (empty($fad)) {
			$fad = json_decode(file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'fastway_area.json'), true);
			Yii::app()->cache->set('fastwayArea', $fad, 864000);
		}

		return !empty($fad[$postcode]) && ($suburb === false || isset($fad[$postcode][strtoupper($suburb)]));
	}

	public static function sortingCode($postcode, $suburb){
		$fad = Yii::app()->cache->get('fastwayArea');
		if (empty($fad)) {
			$fad = json_decode(file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'fastway_area.json'), true);
			Yii::app()->cache->set('fastwayArea', $fad, 864000);
		}
		$suburb = strtoupper($suburb);
		
		return !empty($fad[$postcode]) && isset($fad[$postcode][$suburb])? $fad[$postcode][$suburb] : ['', '' ,''];
	}

	public static function rf2sort($rfcode, $origin = 'SYD')
	{
		$o = substr(strtoupper($origin), 0, 1);
		$frs = Yii::app()->cache->get('fastwaySorting');
		if (empty($frs)) {
			$frs = json_decode(file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'fastway_sorting.json'), true);
			Yii::app()->cache->set('fastwaySorting', $frs, 864000);
		}
		return !empty($frs[$rfcode]) && !empty($frs[$rfcode][$o])? $frs[$rfcode][$o] : '';
	}

	/**
	* get all openning manifests now
	* @return bool
	* @throws Exception
	*/
	public function getOpenedManifests()
	{
		// http://farmapi.fastway.org/v2/fastlabel/list-open-manifests?api_key=
		$action = 'fastlabel/list-open-manifests';
		$data = 'UserID=' . $this->userID;
		$data .= '&api_key=' . $this->apikey;
		$result = $this->request($action, 'POST', $data);
		if ($result) {
			return $result->result;
		}
		return false;
	}

	/**
	* close a specified manifest
	* @param $manifestId
	* @return bool
	* @throws Exception
	*/
	public function closeManifest($manifestId)
	{
		$action = 'fastlabel/closemanifest';
		$data = 'UserID=' . $this->userID;
		$data .= '&ManifestID=' . $manifestId;
		$data .= '&api_key=' . $this->apikey;
		$result = $this->request($action, 'POST', $data);
		if ($result) {
			return $result->result;
		}
		return false;
	}

	/**
	* list all available users
	* @return bool
	* @throws Exception
	*/
	public function getUsers()
	{
		// http://au.api.fastway.org/v2/fastlabel/listusers?api_key=c17c64585c67180170dd504b75ca44ad
		$action = 'fastlabel/listusers?api_key=' . $this->apikey;
		$result = $this->request($action);
		if ($result) {
			return $result->result;
		}
		return false;
	}

	public function getToken()
	{
		$r = $this->mfRequest(self::$token_url, 'POST', ['grant_type' => 'client_credentials', 'scope' => self::$scope, 'client_id' => $this->uid, 'client_secret' => $this->secret]);
		$this->token = $r->access_token;
		return $r->access_token;
	}

	public function addressValidation(&$addr){
		if(empty($this->token)) $this->getToken();
		$data = [
			'streetAddress' => $addr->address,
			'locality' => $addr->suburb,
			'stateOrProvince' => $addr->state,
			'postalCode' => $addr->postcode,
			'country' => 'AU',
		];
		return $this->mfRequest(self::$url.'/api/addresses/validate', 'POST', json_encode($data));
	}

	public function servicedBy(&$addr){
		if(empty($this->token)) $this->getToken();
		$pc = Postcode::model()->find('postcode = :pc AND country = :c', [':pc' => $addr->postcode, ':c' => 'AU']);
		$data = [
			'fromRF' => $this->origin,
			'locality' => $addr->suburb,
			'postalCode' => $addr->postcode,
			'lat' => empty($pc)? 0 : $pc->lat,
			'lng' => empty($pc)? 0 : $pc->lon,
			'country' => 'AU',
		];
		return $this->mfRequest(self::$url.'/api/addresses/serviced-by', 'POST', json_encode($data));
	}

	public function quoteConsignment(&$shipment)
	{
		$data = $this->_prepData($shipment);
		return $this->mfRequest(self::$url.'/api/consignments/quote', 'POST', json_encode($data));
	}

	public function newConsignment(&$shipment)
	{
		$data = $this->_prepData($shipment);
		return $this->mfRequest(self::$url.'/api/consignments', 'POST', json_encode($data));
	}

	public function _prepData(&$shipment){
		if(empty($this->token)) $this->getToken();
		$weight = $this->roundWeight($shipment->weight);
		$av = $this->addressValidation($shipment->cnee);
		if(!empty($av->errors[0]->suggestedValues)){
			$sa = json_decode($av->errors[0]->suggestedValues[0]);
			$ssa = trim($sa->StreetAddress);
			if(!empty($ssa)) $shipment->cnee->address = $ssa;
		}

		$ve = filter_var(trim($shipment->cnee->email), FILTER_VALIDATE_EMAIL);
		$pc = Postcode::model()->find('postcode = :pc AND country = :c', [':pc' => $shipment->cnee->postcode, ':c' => 'AU']);
		$data = [
			'pickupTypeId' => 3,
			'To' => [
				'ContactName' => $shipment->cnee->name,
				'PhoneNumber' => $shipment->cnee->tel,
				'Email' => empty($ve)? 'donotreply@toplogistics.com.au' : $ve,
				'Address' => [
					'StreetAddress' => $this->prepAddrLines($shipment->cnee->address),
					'Locality' => $shipment->cnee->suburb,
					'StateOrProvince' => $shipment->cnee->state,
					'PostalCode' => $shipment->cnee->postcode,
					'Country' => 'AU',
					'Lat' => empty($pc)? 0 : $pc->lat,
					'Lng' => empty($pc)? 0 : $pc->lon,
				],
			],
			'Items' => [],
		];

		$is_satchel = $weight <= 0.3;
		if($is_satchel){
			$data['Items'][0] = [
				'Quantity' => 1,
				'Reference' => $shipment->hbn,
				'PackageType' => 'S',
				'satchelSize' => '300gm',
			];
		}else{
			$data['Items'][0] = [
				'Quantity' => 1,
				'Reference' => $shipment->hbn,
				'PackageType' => 'P',
				'WeightDead' => $weight,
				'WeightCubic' => 0,
				'Length' => 1,
				'Width' => 1,
				'Height' => 1,
			];
		}
		return $data;
	}

	public function getLabel($id)
	{
		return $this->mfRequest(self::$url.'/api/consignments/'.$id.'/labels?pageSize=4x6');
	}

	public function mfRequest($url, $method = 'GET', $data = [])
	{
		$this->result = false;
		$c = new curl($url);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$hdr = [];
		if (!empty($this->token)) {
			$hdr[] = 'Authorization: bearer '.$this->token;
			$hdr[] = 'Content-Type: application/json';
		}
		if (is_array($data)) {
			$data = $c->asPostString($data);
		}
		if (!empty($data)) {
			$c->setopt(CURLOPT_POSTFIELDS, $data);
			$hdr[] = 'Content-Length: ' . strlen($data);
			$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		}

		if (!$c->exec()) {
			throw new Exception('cUrl Error: '.$c->err);
		}
		$this->rescode = $c->rcode;

		if ($this->debug && !empty($this->token)) {
			$this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".$data."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result."\n\n");
		}
		$this->result = json_decode($c->result);

		if (!empty($this->result->error)) {
			$this->err = $this->result->error;
			return false;
		}
		return $this->result;
	}

	public function ftpManifest($ss,$origin='syd'){
		require_once Yii::app()->basePath . '/vendor/autoload.php';

		$sftp = new phpseclib\Net\SFTP('sftp.fastway.org');
		$xml = $this->xmlManifest($ss,$origin);
		// Check SFTP Connection
		if ($sftp->login('292107', 'eGv0&4KS6*qfVPXOGwI7Qs')) {
			// $lists = $sftp->rawlist('/Manifests');
			$sftp->chdir('/Manifests');
			$man_no = date('YmdHis').basename(tempnam('',''));
			$mfn = 'TLA_Fastway_'.$man_no.'.xml';
			if($sftp->put($mfn, $xml)){
				file_put_contents($lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'fastway' . DIRECTORY_SEPARATOR.$mfn, $xml);

				//update tranship
				$trans = Yii::app()->db->beginTransaction();
				try{
					foreach($ss as $s){
						$endTrans = end($s->trans);
						if($endTrans->status == 10) $endTrans->status = 19;
						$endTrans->mdata['oid'] = $man_no;
						$endTrans->save();
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}

				//write manifest
				$trans = Yii::app()->db->beginTransaction();
				try{
					$mani = new Manifest;
					$mani->type = 90;
					$mani->fwd_id = 115;
					$mani->ref = $man_no;
					$mani->save();
					foreach($ss as $s){
						$mm = new ManiMap;
						$mm->mani_id = $mani->id;
						$mm->fid = $s->id;
						$mm->model = 'ImParcel';
						$mm->save();
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
				return true;
			}
		}
		return false;
	}

	public function prepAddrLines($addr, $split = false){
		$addr = str_ireplace(['&#xd', '&#xa', '&#39', ' ', '–', '"'], ["\n", "\n", "'", ' ', '-', ''], $addr);
		if(!$split) return $addr;
		$ls = preg_split('/[\n\r;]+/', trim($addr));
		if (sizeof($ls) == 1 && mb_strlen($addr) > 40) {
			$ls = AppHelper::mbstr_split($addr, 40);
		}
		return $ls;
	}

	public function xmlManifest($ss,$origin = 'syd'){
		$senderInfo = self::$api_accounts[$origin]['senderInfo'];
		$xml = new SimpleXMLElement('<Manifest/>');
		$array_to_xml = function($data, &$node) use (&$array_to_xml){
			foreach( $data as $key => $value ) {
				if( is_array($value) ) {
					if( is_numeric($key) ){
						$key = 'item'.$key; //dealing with <0/>..<n/> issues
					}
					$subnode = $node->addChild($key);
					$array_to_xml($value, $subnode);
				} else {
					$node->addChild("$key",htmlspecialchars("$value"));
				}
			 }
		};
		
		foreach($ss as $i=>$s){
			$addr = $this->prepAddrLines($s->cnee->address, true);
			$wt = $this->roundWeight($s->weight);

			//check if QX label has weight > 0.3kg
			if($wt > 0.3 && preg_match('/^QX\d+/', $s->ref)) $wt = 0.3;
		
			$ve = filter_var(trim($s->cnee->email), FILTER_VALIDATE_EMAIL);
			$data = ['Label' => [
				'PickupFranchiseCode' => strtoupper($origin),
				'LabelNumber' => $s->ref,
				'Reference' => $s->hbn,
				'Item' => [
					'Weight' => $wt,
					'Width' => 1,
					'Length' => 1,
					'Height' => 1,
				],
				'Sender' => $senderInfo,
				'Receiver' => [
					'ReceiverName' => mb_substr($s->cnee->name, 0, 40),
					'ReceiverCompany' => mb_substr($s->cnee->company, 0, 40),
					'ReceiverAddr1' => $addr[0],
					'ReceiverAddr2' => empty($addr[1])? '' : $addr[1],
					'ReceiverSuburb' => $s->cnee->suburb,
					'ReceiverPostcode' => $s->cnee->postcode,
					'ReceiverPhone' => empty($s->cnee->tel)? '0299257100' : $s->cnee->tel,
					'ReceiverEmail' => mb_substr(empty($ve)? 'donotreply@toplogistics.com.au' : $ve, 0, 40),
				],
			]];
			$array_to_xml($data, $xml);
		}
		return $xml->asXML();
	}

	//2X for parcel, QX for 300GM
	public static function genNumber($pfx = '2X')
	{
		return ConnoteRange::newNumber('Org', 115, $pfx);
	}

	/**
	* send non tracking event request to Fastway
	* @param $action
	* @param string $method
	* @param null $data
	* @return bool|mixed
	* @throws Exception
	*/
	protected function request($action, $method = 'GET', $data = null)
	{
		$this->result = false;
		$url = self::$api_url.$action;
		$c = new curl($url);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 600);
		if (is_array($data)) {
			$data = $c->asPostString($data);
		}
		if (!empty($data)) {
			$c->setopt(CURLOPT_POSTFIELDS, $data);
			$hdr = ['Content-Length: ' . strlen($data)];
			$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		}

		if (!$c->exec()) {
			throw new Exception('cUrl Error: '.$c->err);
		}
		$this->rescode = $c->rcode;

		if ($this->debug) {
			$this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".$data."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result."\n\n");
		}
		$this->result = json_decode($c->result);

		if (!empty($this->result->error)) {
			$this->err = $this->result->error;
			return false;
		}
		return $this->result;
	}
	
	protected function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'fastway' . DIRECTORY_SEPARATOR;
		$lf .= 'fastway_' . date('Y-m-d') . '.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

	//end of class
}
