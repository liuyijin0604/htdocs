<?php

/**
 * curl --cacert "./UAT_thawte_bundleCA.cer" -H "Content-Type: text/xml" -vsS --trace "./TRACE.LOG" --output "./RESULT.LOG"
 * --user "pcaexpress_uat":"pcaexpressAc9b8Bqt" --data @"./test.xml " https://b2b-uat-farm.toll.com.au:7960/invoke/wm.tn/receive
 * Class TollAPI
 */
class TollAPI
{
	const TOLL_SLID = 'AWNL';
	const TOLL_SLID_OFFPEAK = 'AWUJ';

	/*
	const API_PASSWORD = 'pcaexpressAc9b8Bqt';
	const API_USERNAME = 'pcaexpress_uat';
	const API_SSL_CAFILE = 'UAT_thawte_bundleCA.cer';
	private static $url = 'https://b2b-uat-farm.toll.com.au:7960/invoke/wm.tn/receive'; //Production URL
	*/

	const API_PASSWORD = 'pcaexpressxdnr6cNn';
	const API_USERNAME = 'pcaexpress_prd';
	const API_SSL_CAFILE = 'PRD_thawte_bundleCA.cer';
	private static $url ='https://b2b-farm.toll.com.au:7960/invoke/wm.tn/receive'; //Production URL

	// define rate query interface const
	const API_RATE_QUERY_PASSWORD = 'WELCOME1';
	const API_RATE_QUERY_USERNAME = 'PCA_EXPRESS';
	const API_RATE_QUERY_SENDER = 'PCA_EXPRESS';
	const API_RATE_QUERY_ACCOUNTCODE = '209253';
	const API_RATE_QUERY_URL = 'https://online.toll.com.au/trackandtrace/b2b';

	// define toll tracking server credentials
	const API_SFTP_HOST = 'priority-ftp.toll.com.au';
	const API_SFTP_USERNAME = 'tppcaexp';
	const API_SFTP_PASSWORD = 'Tppc43Xp';
	const API_SFTP_FOLDER = 'prod/outbound';

	// define service id
	// Here is a table depicting some of the (many) CarrierServiceID values used by TPRI (Toll Priority)…
	// 11	Parcels Overnight
	// 12	Parcel OffPeak
	const TOLL_SERVICE_OVERNIGHT = '11';
	const TOLL_SERVICE_OFFPEAK = '12';

	public $result;
	public $rescode;
	public $err;
	public $is_test;

	/**
	 * @param bool $debug
	 * @param bool $test
	 */
	public function __construct($debug = false, $test = false)
	{
		$this->is_test = $test;
		$this->debug = $debug;
	}

	/**
	 * in limit postcode area, toll does not delivery the parcel
	 * @param $postcode
	 * @return bool
	 */
	public static function inLimitPostcode($postcode)
	{
		$limits = ['0800','0801','0804','0810','0811','0812','0813','0814','0815','0820','0821','0822','0828','0829','0830','0831','0832',
			'0834','0835','0836','0837','0838','0839','0840','0841','0845','0846','0847','0850','0852','0853','0854','0861','0862','0870','0872',
			'0880','0881','0885','0886','0906','0907','0909','2382','2469','2484','2648','2715','2717','2738','2739','2832','2833','2834','2835',
			'2836','2839','2840','2878','2879','2880','3723','4211','4213','4275','4285','4352','4355','4356','4357','4359','4361','4362','4370',
			'4371','4380','4385','4388','4390','4402','4403','4404','4405','4406','4408','4413','4415','4416','4419','4420','4423','4427','4428',
			'4474','4475','4479','4480','4481','4482','4486','4490','4492','4494','4498','4676','4816','4823','4825','4828','4829','4830','4871',
			'4873','4874','4875','4876','4877','4880','4881','4887','4890','4891','4892','4895','5220','5221','5222','5223','5356','5374','5440',
			'5558','5571','5573','5575','5605','5607','5655','5680','5690','5710','5722','5724','5731','5733','5734','6225','6230','6327','6346',
			'6390','6426','6427','6430','6438','6443','6507','6537','6646','6701','6705','6721','6725','6728','6740','6743','6753','6765','6770',
			'7255','7256'];
		return in_array($postcode, $limits);
	}

	/**
	 * sync tracking information to HVLV from toll tracking SFTP server
	 * will can called by cron
	 */
	public function syncTracking()
	{
		require_once Yii::app()->basePath . '/vendor/autoload.php';

		$sftp = new phpseclib\Net\SFTP(TollAPI::API_SFTP_HOST);

		// Check SFTP Connection
		if (!$sftp->login(TollAPI::API_SFTP_USERNAME, TollAPI::API_SFTP_PASSWORD)) {
			$this->log2file('Time: '.date('Y-m-d H:i:s')."\n [ERROR] login failed : " . $sftp->getSFTPLog()."  \n\n");
		} else {
			$lists = $sftp->rawlist(TollAPI::API_SFTP_FOLDER);

			// get all files
			$allFiles = [];
			foreach ($lists as $k =>  $item) {
				$type = $item['type'];
				if ($type == 1) {
					$allFiles[] = $k;
				}
			}

			$sftp->chdir(TollAPI::API_SFTP_FOLDER);
			foreach ($allFiles as $file) {
				$content = $sftp->get($file);

				$lines = explode("\n", $content);

				foreach ($lines as $line) {
					$items = explode('|', $line);
					if (!empty($items) && count($items) > 8) {
						$ref = $items[0];
						$scanTime = $items[3];
						$year = substr($scanTime, 4, 4);
						$month = substr($scanTime, 2, 2);
						$day = substr($scanTime, 0, 2);
						$time = substr($scanTime, 8);
						$scanTime = $year . '-' . $month . '-'  . $day . ' ' . $time;
						$scanEvent = $items[4];
						$desc = $items[8];

						$this->addTracking2Hvlv($ref, $scanTime, $scanEvent, $desc);
					}
				}

				// save file to local currently
				$this->saveTollTraceFile($content, $file);

				// delete this file
				$sftp->delete($file);
			}

			$sftp->disconnect();

			$this->log2file('Time: '.date('Y-m-d H:i:s')."\n All Done this time \n\n");
		}
	}

	/**
	 * save tracking information to HVLV
	 * @param $ref
	 * @param $scanTime
	 * @param $scanEvent
	 * @param $desc
	 */
	private function addTracking2Hvlv($ref, $scanTime, $scanEvent, $desc)
	{
		// get shipment
		$shipment = ImParcel::model()->find('ref = :ref', [':ref' => $ref]);
		if (!empty($shipment)) {

			// all done, just return;
			if ($shipment->status == 90) {
				return;
			}

			// in case shipment not in transit set as transit status
			if ($shipment->status < 70) {
				$shipment->status = 70;
				$shipment->update('status');
			}

			// in case deliveried successfully
			if ($scanEvent == 90) {
				$shipment->status = 90;
				$shipment->update('status');

				// update tranship status as finished
				$tranship = Tranship::model()->find('pid = :pid', [':pid' => $shipment->id]);
				$tranship->status = 99;
				$tranship->update('status');
			}

			$shipment->addTracking(70, $desc, '', $scanTime);
		}
	}

	/**
	 * get xml TollRateRequest data
	 * The name of the request is TollRateRequest. It consists of the Header and the RateRequest sections. The Header section holds information
	 * relating to the type of document and the identities of the sending and receiving entities of the document.
	 * The RateRequest section holds information relating to the Rate Enquiry criteria.
	 * @param $shipment
	 */
	public function getXmlRateQuery(&$shipment)
	{
		$itemCount = $shipment->pkg > 0 ? $shipment->pkg : 1 ;
		$itemWeight = $shipment->weight / $itemCount;
		$width = 0;
		$length = 0;
		$height = 0;

		if (isset($shipment->mdata['dim'])) {
			if (isset($shipment->mdata['dim']['w'])) {
				$width = floatval($shipment->mdata['dim']['w']);
			}
			if (isset($shipment->mdata['dim']['h'])) {
				$height = floatval($shipment->mdata['dim']['h']);
			}
			if (isset($shipment->mdata['dim']['d'])) {
				$length = floatval($shipment->mdata['dim']['d']);
			}
		}

		if ($width <= 0 || $length <= 0 || $height <= 0) {
			$this->log2file('Time: '.date('Y-m-d H:i:s')."\n [ERROR] " . $shipment->hbn ." no dim \n\n");
			return '';
		}

		// make up header xml data elements
		$tollSLID = self::getTollSLID($shipment);
		$header = [
			'Sender' => self::API_RATE_QUERY_SENDER,
			'Receiver' => 'Toll',
			'DocumentType' => 'TollRateRequest',
			'DocumentID' => 'CI_'.uniqid($tollSLID) . '-' . $tollSLID  ,
			'DateTimeStamp' => date("Y-m-d\Th:i:s+hi")
		];

		$rateRequest = [
			'CarrierID' => 'PRIO',
			'AccountDetail' => [
				'AccountCode' => self::API_RATE_QUERY_ACCOUNTCODE,
				// 'SubAccountID' => ''
			],

			// 6C The Crescent<br/>
			// KINGSGROVE NSW 2208<br/>
			// 02-99257111
			'SenderLocality' => [
				'Suburb' => 'KINGSGROVE',
				'State' => 'NSW',
				'Postcode' => '2208',
				'Country' => 'AU'
			],

			'ReceiverLocality' => [
				'Suburb' => $shipment->cnee->suburb,
				'State' => $shipment->cnee->state,
				'Postcode' => $shipment->cnee->postcode,
				'Country' => 'AU'
			],

			'ServiceDetail' => [
				'DespatchDate' => date('Y-m-d') ,

				'ServiceID' => '02', // Overnight
				'ProductID' => '02', // Parcels

				// 'ServiceID' => 'BC', // Overnight
				// 'ProductID' => '7Y', // Consumer Delivery – Signature Required
				//  'ServiceMode' => '',
				// 'TollExtraServiceRequired' => '',
				// 'TollExtraServiceValue' => '',
			],

			'Items' => [
				'NumberOfItems' => $shipment->pkg,
				// 'Pallets' => '',
				// 'MiscQty' => '',
				// 'CommodityCode' => '',
				'Weight' => number_format($itemWeight, 2, '.', ''),
				'Length' => $length,
				'Width' => $width ,
				'Height' => $height,
				// 'Qty' => '',
				// 'CubicMetres' => ''
			]
		];

		$alldata = [
			'Header' => $header,
			'RateRequest' => $rateRequest
		];

		$rt = $this->getXmlData($alldata, 'TollRateRequest');


		return $rt;
	}

	/**
	 * A Toll Exchange Standard XML Consignment Notes (TESXML) file is a sequence of exactly one header tag, and one or more repeated Consignment tags.

	TAG	Description	Restrictions
	<Header>	Specifies routing details of the message.	Mandatory.  Appears once in the message.
	<Consignment>	Payload, a repeatable collection of consignment(s) related information.	Mandatory.  At least one Consignment Note must appear in the transmission.
	 * @param $shipments
	 */
	public function getXmlConsignments(&$shipments)
	{


		//$tollSLID = self::getTollSLID($shipment);

		// map all HVLV shipment to Toll TESXML structure
		// make up header xml data elements
		$header = [
			'Sender' => self::TOLL_SLID,
			'Receiver' => 'Toll',
			'DocumentType' => 'ConNote_1_3_Add',
			'DocumentID' => 'CI_'.uniqid(self::TOLL_SLID) . '-' . self::TOLL_SLID  ,
			'DateTimeStamp' => gmdate("Y-m-d\TH:i:s\Z")
		];

		$consignments = [];
		foreach ($shipments as $shipment) {

			// make up PUD data
			$pud = [
				'DespatchDate' => date('Y-m-d'),
				//    'DeliveryDate' => '',
				//    'CloseTime' => '',
				//   'ReadyTime' => '',
				//   'SaturdayDelivery' => '',
				//   'SpecialInstructions' => '',

			];


			// make up sender data
			// jerry
			// 6C The Crescent
			// KINGSGROVE NSW 2019
			$sender = [
				'PartyName' => $shipment->cnor->name,
				'PartyID' => 'PCAE',
				'AccountCode' => $shipment->cnor->id,
				//  'Notes' => '',
				'Address' =>  [
					//  'CustLocationCode' => '',
					//  'CarrierLocationCode' => '',
					//  'LocationDescription' => '',
					'AddressLine1' => '6C The Crescent',
					//  'AddressLine2' => '',
					//  'AddressLine3' => '',
					'Suburb' => 'KINGSGROVE',
					'State' => 'NSW',
					'Country' => 'AU',
					'PostCode' => '2208',
					// 'Phone' => '',
					// 'Fax' => '',
				],
				'Contact' => [
					'ContactName' => 'Jerry',
					//  'EmailAddress' => '',
					'Phone' => [
						'AreaCode' => '02',
						'Number' => '99257111',
						'CountryCode' => '61'
					]
				],
			];


			/*
			$sender = array(
				'PartyName' => $shipment->cnor->name,
				'PartyID' => 'PCAE',
				'AccountCode' => $shipment->cnor->id,
				//  'Notes' => '',
				'Address' => $this->getAddressData($shipment->cnor),
				'Contact' => array(
					'ContactName' => $shipment->cnor->name,
					//  'EmailAddress' => '',
					'Phone' => $this->getPhoneData($shipment->cnor),
					// 'Fax' => '',
				),
			);*/


			// make up receiver data
			$receiver = [
				'PartyName' => $shipment->cnee->name,
				'PartyID' => 'PCAE',
				'AccountCode' => $shipment->cnee->id,
				//  'Notes' => '',
				'Address' => $this->getAddressData($shipment->cnee),
				'Contact' => [
					'ContactName' => $shipment->cnee->name,
					//  'EmailAddress' => '',
					'Phone' => $this->getPhoneData($shipment->cnee),
					// 'Fax' => '',
				],
			];


			// Here is a table depicting some of the (many) CarrierServiceID values used by TPRI (Toll Priority)…
			// 11	Parcels Overnight
			// 12	Parcel OffPeak
			$carrierServiceID =  $shipment->getTollServiceType();

			$vol = $shipment->cbm;
			if (!empty($shipment->mdata['dim'])) {
				$realVol = (floatval($shipment->mdata['dim']['w']) * floatval($shipment->mdata['dim']['d']) * floatval($shipment->mdata['dim']['h'])) / 1000000;
				if ($vol == 0.0 || $vol > $realVol) {
					$vol = $realVol;
				}
			}

			
			
			$consignment_each= [
				'CustConNoteNbr' => $shipment->hbn,
				'TollConNoteNbr' => $shipment->ref,
				'CustConNoteRef' => $shipment->cref,
				'ConNoteCreationDate' => $shipment->created,
				'CarrierID' => 'PRI',
				'CarrierServiceID' => $carrierServiceID,
				// 'CarrierProductCode' => '',
				'Payer' => 'S',
				'SourceSystemID' => 'MTS',
				'TotalVolume' =>  $vol,
				'TotalWeight' => $shipment->weight,
				//  'ConnoteBarcode' => $barcode,
				'PUD' => $pud,
				'Sender' => $sender,
				'Receiver' => $receiver,
				//       'ThirdParty' => '',
				'Items_parent' => $this->getItemsData($shipment),
				//    'Pallets' => '',
				//    'AllowAlternateDeliveryFlag' => '',
				//     'AlternativeDeliveryDetails' => '',
				//    'AdditionalReferences' => '',
				//   'InsuranceDetails' => '',
				//   'Temperatures' => '',
				//   'Flags' => ''
			];
			if ($shipment->insurance>0) {
				$insuranceDetail=[
					'InsuranceAmount'=>$shipment->insurance,
					'InsuranceCurrency'=>'AUD'
				];
				$consignment_each['InsuranceDetails']=$insuranceDetail;
			}
			$shipment->mdata['manifest_weight']=$shipment->weight;
			$shipment->updateMeta();
				
			$consignments[]=$consignment_each;
		}
		

		$alldata = [
			'Header' => $header,
			'Consignment_parent' => $consignments
		];

		$rt = $this->getXmlData($alldata);

		// we need to replace <Items><Item></Item> </Items>
		// with only <Items> </Items>
		// because Toll TESXML format for <Items> is not regular
		$rt = strtr(
			$rt,
			[ '<Items_parent>' => '',
				'</Items_parent>' => '',
				'<Consignment_parent>' => '',
				'</Consignment_parent>' => '',
				'<TollConsignments>' => '<TollConsignments xmlns="http://online.toll.com.au/XMLSchema/TollConsignmentEntry_1_3">']
		);

		return $rt;
	}

	/**
	 * @param $shipment
	 * @return array
	 */
	private function getItemsData($shipment)
	{
		$items = [];
		$itemCount = $shipment->pkg > 0 ? $shipment->pkg : 1 ;
		$itemWeight = $shipment->weight / $itemCount;

		// foreach($shipment->eitems['g'] as $gi=>$g){
		for ($i = 1; $i <= $itemCount; $i++) {
			$barcode = 'T' . $shipment->cnee->postcode . '11' . $shipment->ref  . sprintf('%03d', $i);
			$barcode .=  self::getCheckDigital($barcode);

			$items[] = [
				//  'Description' => $g,
				'ItemBarcode' => $barcode,
				// 'Qty' => $shipment->eitems['q'][$gi],
				'Qty' => 1,
				// 'Pallets' => '',
				//  'MiscQty' => '',
				//  'CommodityCode' => '',
				'Weight' => number_format($itemWeight, 2, '.', ''),
				// 'Length' => '',
				//'Width' => '',
				// 'Height' => '',
				// 'CubicMetres' => '',
				'DangerousGoods' => 'N',
				//  'DangerousGoodsClass' => '',
				//  'DangerousGoodsSubRisk' => '',
				//  'DangerousGoodsPackGroup' => '',
				//  'DangerousGoodsUNCode' => '',
				'CustItemRef1' =>  $shipment->hbn.'-'.$i,
				//  'CustItemRef2' => '',
				//  'Description' => '',
				//  'Description' => '',
				//  'Description' => '',
			];
		}
		return $items;
	}

	/**
	 * @param $address
	 * @return array
	 */
	private function getAddressData($address)
	{
		return [
			//  'CustLocationCode' => '',
			//  'CarrierLocationCode' => '',
			//  'LocationDescription' => '',
			'AddressLine1' => $address->address,
			//  'AddressLine2' => '',
			//  'AddressLine3' => '',
			'Suburb' => $address->suburb,
			'State' => $address->state,
			'Country' => 'AU',
			'PostCode' => $address->postcode,
			// 'Phone' => '',
			// 'Fax' => '',
		];
	}

	/**
	 * @param $address
	 * @return array
	 */
	private function getPhoneData($address)
	{
		$countryCode = '';
		if (isset($address->country)) {
			$upperCountry = strtoupper(trim($address->country));
			if ($upperCountry == 'AU' || $upperCountry == 'AUSTRALIA') {
				$countryCode = '61';
			} elseif ($upperCountry == 'CN' || $upperCountry == 'CHINA') {
				$countryCode = '86';
			}
		}

		$phoneNumber = $address->tel;
		// remove all spances in the phone number
		$phoneNumber = str_replace([' ','+','-'], '', trim($phoneNumber));


		$phone = [
			// 'AreaCode' => '',
			'Number' => $phoneNumber
		];
		if (!empty($countryCode)) {
			$phone['CountryCode'] = $countryCode;
		}
		return $phone;
	}

	/**
	 * @param $data
	 * @return mixed
	 * @throws CException
	 */
	public function getXmlData(&$data, $resourceType = 'TollConsignments')
	{
		$xml = new SimpleXMLElement("<{$resourceType}></{$resourceType}>");
		$this->array_to_xml($data, $xml);
		$xmlData = $xml->asXML();

		/* just remove title : <?xml version="1.0" ?> */
		return str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>', $xmlData);
	}

	/**
	 * @param $data
	 * @param $xml_data
	 */
	private function array_to_xml($data, &$xml_data)
	{
		foreach ($data as $key => $value) {
			if (is_numeric($key)) {
				// $key = 'item'.$key; //dealing with <0/>..<n/> issues
				$key = $xml_data->getName();

				// from _parent from key name just get child key name
				$key = str_replace('_parent', '', $key);

				//$parent_div = $xml_data->xpath("parent::*");
				//$name = $parent_div->getName();
			}
			if (is_array($value)) {
				$subnode = $xml_data->addChild($key);
				$this->array_to_xml($value, $subnode);
			} else {
				$xml_data->addChild("$key", htmlspecialchars("$value"));
			}
		}
	}


	/**
	 * @return string
	 */
	public static function getTollSLID(&$shipment)
	{
		$tollSLID = self::TOLL_SLID;
		if ($shipment->getTollServiceType() == self::TOLL_SERVICE_OFFPEAK) {
			$tollSLID = self::TOLL_SLID_OFFPEAK;
		}
		return $tollSLID;
	}

	/**
	 * create Toll related connote number
	 * 3.1	Connote Number
	•	Each consignment must have a unique Connote Number.
	•	The Connote Number is 10 digits long.
	•	Alpha characters must be uppercase.
	•	The Connote Number structure is as follows:

	1	2	3	4	5	6	7	8	9	10
	SLID	Sequence #
	A	A	A	A	N	N	N	N	N	N

	Where:
	SLID	Customer identifier, assigned to the customer by Toll Priority, which must be used on all freight from that customer/site. Unique SLID per despatch site is required.
	Sequence #	Numeric sequence number in the range 000000 - 999999.
	A 		Alpha-Numeric (Alphas to always be in Upper case)
	N		Numeric

	 * @param $shipment
	 * @return string
	 */
	public static function getTollConnoteNumber(&$shipment)
	{
		$tollSLID = self::TOLL_SLID;
		if ($shipment->getTollServiceType() == self::TOLL_SERVICE_OFFPEAK) {
			$tollSLID = self::TOLL_SLID_OFFPEAK;
		}
		return $tollSLID .sprintf('%06s', substr($shipment->id, -6));
	}

	public function postShipments(&$data)
	{
		return $this->request($data);
	}

	public function getCost(&$shipment)
	{
		$cost = 0;
		$xmlData = $this->getXmlRateQuery($shipment);
		if (!empty($xmlData)) {
			$result = $this->rateRequest($xmlData);
			if (!$result) {
				$this->log2file('Time: '.date('Y-m-d H:i:s')."\n get cost error : ".$this->err."\n\n");
			} else {
				$resp = simplexml_load_string($result);
				if (isset($resp->RateResponse[0]->TotalChargeAmount)) {
					$cost = floatval((string)$resp->RateResponse[0]->TotalChargeAmount);
				}

				// minus gst if existing
				if (isset($resp->RateResponse[0]->GSTAmount)) {
					$cost -= floatval((string)$resp->RateResponse[0]->GSTAmount);
				}
			}
		}
		if ($cost < 0) {
			$cost = 0;
		}
		return $cost;
	}

	/**
	 * send rate query request to Toll
	 * @param $data
	 * @return bool
	 */
	public function rateRequest($data)
	{
		if (empty($data)) {
			return false;
		}
		$this->result = false;
		$url = self::API_RATE_QUERY_URL;
		$c = new curl($url);
		$c->setopt(CURLOPT_CUSTOMREQUEST, 'POST');
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		//$c->setopt(CURLOPT_SSL_VERIFYPEER, true);

		// set certificate file
		// $cafile = realpath(dirname(__DIR__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'UAT_thawte_bundleCA.cer');
		//$c->setopt(CURLOPT_CAINFO, $cafile);

		// set user name and password
		$userInfo = self::API_RATE_QUERY_USERNAME  . ':' . self::API_RATE_QUERY_PASSWORD;
		$c->setopt(CURLOPT_USERPWD, "$userInfo");
		$c->setopt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		$c->setopt(CURLOPT_TIMEOUT, 600);
		if (!empty($data)) {
			$c->setopt(CURLOPT_POSTFIELDS, $data);
			$hdr = ['Content-Length: ' . strlen($data),'Content-Type: text/xml'];
			$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		}

		if (!$c->exec()) {
			$this->err = 'Network Error: '.$c->err;
			return false;
		}

		$this->rescode = $c->rcode;
		$this->result = $c->result;
		if ($this->debug) {
			// $this->log2file('Time: '.date('Y-m-d H:i:s')."\nRaw Response: " . $c->getRawResponse() ."\n\n");
			$this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".$data."\nResponse Code: ".$this->rescode."\nResponse: ".$this->result."\n\n");
		}

		if ($this->rescode != 200) {
			$this->err = $this->result;
			return false;
		}
		return $this->result;
	}

	/**
	 * send non tracking event request to Fastway
	 * @param $action
	 * @param string $method
	 * @param null $data
	 * @return bool|mixed
	 * @throws Exception
	 */
	protected function request($data = null, $method = 'POST')
	{
		if (empty($data)) {
			return false;
		}
		$this->result = false;
		$url =  self::$url;
		$c = new curl($url);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, true);

		// set certificate file
		$cafile = realpath(dirname(__DIR__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'UAT_thawte_bundleCA.cer');
		$c->setopt(CURLOPT_CAINFO, $cafile);

		// set user name and password
		$userInfo = self::API_USERNAME  . ':' . self::API_PASSWORD;
		$c->setopt(CURLOPT_USERPWD, "$userInfo");
		$c->setopt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		$c->setopt(CURLOPT_TIMEOUT, 600);
		if (!empty($data)) {
			$c->setopt(CURLOPT_POSTFIELDS, $data);
			$hdr = ['Content-Length: ' . strlen($data),'Content-Type: text/xml'];
			$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		}

		if (!$c->exec()) {
			$this->err = 'Network Error: '.$c->err;
			return false;
		}

		$this->rescode = $c->rcode;

		if ($this->rescode == 100) {
			// Toll api some case will return 100 for ok or continue again
			$response = $c->result;
			$delimiter = "\r\n\r\n";

			do { //split header/body
				list($r_headers, $response) = explode($delimiter, $response, 2);
				$h_lines = explode("\r\n", $r_headers);
				$r_lines = array_shift($h_lines);
				if (preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@', $r_lines, $matches)) {
					$this->rcode = $matches[1];
				} else {
					$this->rcode = "Error";
					break;
				}
			} while ($this->rcode == "302");
			$this->rescode = $this->rcode;
			$this->result = $response;
		} else {
			$this->result = $c->result;
		}

		if ($this->debug) {
			// $this->log2file('Time: '.date('Y-m-d H:i:s')."\nRaw Response: " . $c->getRawResponse() ."\n\n");
			$this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".$data."\nResponse Code: ".$this->rescode."\nResponse: ".$this->result."\n\n");
		}

		if ($this->rescode != 200) {
			$this->err = $this->result;
			return false;
		}
		return $this->result;
	}

	/**
	 * @return mixed
	 */
	public function getError()
	{
		return $this->err;
	}

	public function getTracking($trackingNo)
	{
		$url = 'https://online.toll.com.au/trackandtrace/showConnotes.do?system=PRIO&connote=' . $trackingNo;
		$trackingContent = file_get_contents($url);

		if (!empty($trackingContent)) {
			// Our pattern for capturing all that is between <tr> and </tr>
			$pattern = '/<tr class="tatTrkInfoTblRow" [^>]*>(.*)<\/tr>/s';

			// If a match is found, store the results in $match
			if (preg_match($pattern, $trackingContent, $match)) {
				// Show the captured value
				return $match[1];
			}
		}
		return '';
	}
	/**
	 * create check digital number
	 * @param $itemNumber
	 */
	/**
	Below is an algorithm for calculating check digit:

	DECLARE an INT VARIABLE named LAST_DIGIT and SET the value as 0
	DECLARE an INT VARIABLE named CALC and SET the value as 0
	DECLARE an INT VARIABLE named CHECK_DIGIT and SET the value as 0
	DECLARE an INT VARIABLE named ACCUMULATION and SET the value as 0
	DECLARE an INT VARIABLE named MULTIPLIER and SET the value as 3
	DECLARE a STRING VARIABLE named CONNOTE and SET the value as "" (empty string)
	DECLARE an INT VARIABLE named TEMP and SET the value as 0

	GET the barcode value and SET it as the BARCODE value i.e. BARCODE = T320711ALDL044287001

	START LOOP
	FOR EACH CHARACTER of BARCODE, starting from the last/rightmost character, step backwards through each character i.e. 1 through to T

	IF the CHARACTER is NUMERIC
	SET TEMP as the result of the ASCII value of the CHARCTER, MINUS the ASCII value of ZERO (48) i.e. ASCII value of 1 is 49.  TEMP result is 49 – 48 = 1
	ELSE IF the CHARACRTER is ALPHA
	SET TEMP as the result of the following: ASCII value of the CHARCTER, MINUS the ASCII value of A (capital A, 65) i.e. ASCII value of T is 84.  TEMP result is 84 – 65 = 19
	with the result MODULUS 10 (similar to taking the remainder when divided by 10) i.e. Modulus 10 of 1 = 1.  Modulus 10 of 19 = 9.

	ACCUMLATION = ACCUMLATION + (TEMP * MULTIPLIER)

	IF MULTIPLIER = 3
	SET MULTIPLER = 1
	ELSE IF MULTIPLIER = 1
	SET MULTIPLIER = 3

	END LOOP

	GET the last/rightmost digit from ACCUMLATION and SET it as the LAST_DIGIT value
	SET CALC to the result of ASCII value of LAST_DIGIT MINUS the ASCII value of ZERO (48)
	SET CHECK_DIGIT to the result of 10 - CALCVAR

	IF the CHECK_DIGIT is 10
	SET CHECK_DIGIT to 0
	ELSE
	CHECK_DIGIT = CHECK_DIGIT

	Your check digit is now calculated, and is the value of CHECK_DIGIT.  i.e. The check digit of T320711ALDL044287001 = 6

	 */
	public static function getCheckDigital($barcode)
	{
		//Variable from psuedo code
		$ACCUMULATION = 0;
		$MULTIPLIER = 3;

		//store the length of the BARCODE for use in the loop
		$barcode_string_length = strlen($barcode);

		//for loop running for the number of characters in the string, i-- so we iterate from high to low, which is right to left for string index
		for ($i = $barcode_string_length; $i >=  1; $i--) {
			//select the digit to calculate at index (i - 1) as index starts at zero in java
			$working_digit = (int)ord($barcode[$i - 1]);

			//if working digit is a character, working_digit cast as int, giving the ASCII value
			if ($working_digit >= 65) {
				//working_digit cast as int giving the ASCII value minus ASCII value of "A" then modulus, stored in TEMP
				$TEMP = (($working_digit - ord('A')) % 10);
			}
			//if working digit is numeric working_digit cast as int, giving the ASCII value
			else {
				//working_digit cast as int giving the ASCII value minus ASCII value of "0" stored in TEMP
				$TEMP = ($working_digit) - ord('0');
			}

			//Accumulate the values, using multiplier
			$ACCUMULATION = $ACCUMULATION + ($TEMP * $MULTIPLIER);
			//Alternate multipler between 1 and 3
			if ($MULTIPLIER == 3) {
				$MULTIPLIER = 1;
			} else {
				$MULTIPLIER = 3;
			}
		}


		//Turn ACCUMULATION into a string as to obtain the length
		$myStringAccumulation = (string)$ACCUMULATION;

		//Obtain the length of ACCUMULATION (which is now myStringAccumulation)
		$ACCUMULATION_length = strlen($myStringAccumulation);

		//Take the last digit of ACCUMULATION (which is now myStringAccumulation)
		$LAST_DIGIT = ord($myStringAccumulation[$ACCUMULATION_length - 1]);

		//Take the last digit, which is already an ASCII value due to a CHAR being store in LAST_DIGIT which is an INT and subtract the ASCII value of "0"
		$CALC = $LAST_DIGIT - ord('0');

		//Perform basic operator to finalise the check digit
		$CHECK_DIGIT = 10 - $CALC;
		//Set the CHECK Digit to 0 if it is equal to 10
		if ($CHECK_DIGIT == 10) {
			$CHECK_DIGIT = 0;
		}

		return $CHECK_DIGIT;
	}

	protected function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR. 'tollapi' . DIRECTORY_SEPARATOR;
		$lf .= 'toll_api_' . date('Y-m-d') . '.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

	/**
	 * save all toll trace files in local temporary
	 * @param $data
	 * @param $filename
	 * @return int
	 */
	protected function saveTollTraceFile($data, $filename)
	{
		$dir = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'tollTrack';
		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}
		$lf = $dir . DIRECTORY_SEPARATOR .$filename;
		return file_put_contents($lf, $data, FILE_APPEND);
	}


	/*
		private function getBinDataByShipment(&$shipment){
			$binData = '';
	
			// CONNOTE ID	Char	1	20	Mand	Consignment Note Number
			// (Left Justify, Trailing Space filled)
			// Refer to Connote & Item Numbering Requirement
			$hbn = str_pad($shipment->hbn,20," ", STR_PAD_LEFT);
			$binData = $hbn;
	
			// LINE ITEM NO	Num	21	2	Mand	Line Item (Sequence) Number
			// (Zero filled)
			$binData .= pack('C',0);
			$binData .= pack('C',0);
	
			// CONNOTE ENTERED CODE	Char	23	1	Opt	Connote Entered Code
			// This value is always ‘Y’.
			$binData .= 'Y';
	
			//ACTIVITY CODE	Char	24	2	Not Used	Activity Code
			$binData .= pack('C',0);
			$binData .= pack('C',0);
	
			// SEGMENT CODE	Char	26	2	Not Used	Segment Code
			$binData .= pack('C',0);
			$binData .= pack('C',0);
	
			//SERVICE CODE	Char	28	2	Mand	Service Code
			// (Left Justify, Trailing Space filled)
			// Refer to Toll Priority Service Codes and Names
			$binData .= 'PC'; // TODO mod soon
	
			// SYSTEM ID	Char	30	4	Mand	System Identifier
			// (Left Justify, Trailing Space filled)
			// Toll Priority will assign this value. The System ID is unique to a single despatch location.
			$binData .= self::TOLL_SLID;
	
			// DESPATCH DATE	Num	34	6	Mand	Despatch Date
			// Format: YYMMDD  (all digits)
			$binData .= date('ymd');
	
			// SPECL HANDLING	Char	40	1	Not Used	Special Handling Flag
			$binData .= pack('C',0);
	
			// STATUS FLAG	Char	41	1	Mand	Connote Record Status
			// Space Filled = Normal Active Record
			// ‘C’ = Cancelled Connote
			$binData .= ' ';
	
			// RECEIVER NAME	Char	42	40	Mand	Receiver Name
			//  (Left Justify, Trailing Space filled)
			// This is the company name or name of person who is receiving the goods. This field must not be all blank.
			$recvName = $shipment->cnee->name;
			if ( strlen($recvName) >= 40 ) {
				$recvName = substr($recvName,0,40);
			} else {
				$recvName = str_pad($recvName, 40, " ", STR_PAD_LEFT);
			}
			$binData .= $recvName;
	
			// RECEIVER STREET 1	Char	82	30	Mand	Receiver Address Line 1
			// (Left Justify, Trailing Space filled)
			// This field must NOT be all blank.
			$recvAddr = $shipment->cnee->address;
			if ( strlen($recvAddr) >= 30 ) {
				$recvAddr = substr($recvAddr,0,30);
			} else {
				$recvAddr = str_pad($recvAddr, 30, " ", STR_PAD_LEFT);
			}
			$binData .= $recvAddr;
	
			// RECEIVER STREET 2	Char	112	30	Opt	Receiver Address Line 2
			// (Left Justify, Trailing Space filled)
			$recvAddr2 = '';
			$recvAddr2 = str_pad($recvAddr2, 30, " ", STR_PAD_LEFT);
			$binData .= $recvAddr2;
	
	
			// RECEIVER TOWN	Char	142	30	Mand	Receiver Address Town Name
			// (Left Justify, Trailing Space filled)
			// This field must NOT be all blank.
			$recvSuburb = $shipment->cnee->suburb;
			if ( strlen($recvSuburb) >= 30 ) {
				$recvSuburb = substr($recvSuburb,0,30);
			} else {
				$recvSuburb = str_pad($recvSuburb, 30, " ", STR_PAD_LEFT);
			}
			$binData .= $recvSuburb;
	
			// RECEIVER POSTCODE	Num	172	6	Mand	Receiver Address Postcode
			// (Left Justify, Trailing Space filled)
			// This field must NOT be all blank.
			$postcode = '00' . $shipment->cnee->postcode;
			$binData .= $postcode;
	
			// DELIVERY EARLY DATE
			// Num	178	6	Not Used	Earliest Delivery Date
			// (Left Justify, Trailing Space filled)
			$binData .= '000000';
	
			// DELIVERY LATE DATE	Num	184	6	Not Used	Latest Delivery Date
			// (Left Justify, Trailing Space filled)
			$binData .= '000000';
	
			// DELIVERY EARLY TIME	Num	190	4	Not Used	Earliest Delivery Time
			// (Left Justify, Trailing Space filled)
			$binData .= '0000';
	
			// DELIVERY LATE TIME	Num	194	4	Not Used	Latest Delivery Time
			// (Left Justify, Trailing Space filled)
			$binData .= '0000';
	
			// DELIVERY SPECIAL INSTRUCTIONS	Char	198	90	Opt	Delivery Special Instructions
			// (Left Justify, Trailing Space filled)
			$recvSpec = '';
			$recvSpec = str_pad($recvSpec, 90, " ", STR_PAD_LEFT);
			$binData .= $recvSpec;
	
			// DELIVERY SPECIAL CODE	Char	288	1	Not Used	Delivery   Special Delivery Flag
			// (Left Justify, Trailing Space filled)
			$binData .= '0';
	
			// RECEIVER CONTACT NAME	Char	289	20	Opt	Receiver of the goods Contact Name
			// (Left Justify, Trailing Space filled)
			// Name of a person that can be contacted in regards to delivery.
			$recvContactName = $shipment->cnee->name;
			if ( strlen($recvContactName) >= 20 ) {
				$recvContactName = substr($recvContactName,0,20);
			} else {
				$recvContactName = str_pad($recvContactName, 20, " ", STR_PAD_LEFT);
			}
			$binData .= $recvContactName;
	
	
			// RECEIVER CONTACT PHONE	Char	309	11	Opt	Receiver Contact Telephone Number
			// (Left Justify, Trailing Space filled)
			// Format: Numeric. Country code (optional), area code then number. Spaces, ‘+’, ‘()’ etc must not be used for digit separation.
			// For example: 0396762233 – landline, 0411123456 – mobile. If expressed as an international number, sent as such - 61396762233 for landline or 61411123456 for mobile.
			// Phone number cannot exceed 11 digits.
			// Phone contact only. For SMS notification use field SMS NOTIFICATION NUMBER (pos.1218)
			$phone = $shipment->cnee->tel;
			if ( strlen($phone) >= 11 ) {
				$phone = substr($phone,0,11);
			} else {
				$phone = str_pad($phone, 11, " ", STR_PAD_LEFT);
			}
			$binData .= $phone;
	
			// RECEIVER CONT FAX	Char	320	11	Not Used	Receiver Contact Facsimile Number
			//  (Left Justify, Trailing Space filled)
			$recvFax = '';
			$recvFax = str_pad($recvFax, 11, " ", STR_PAD_LEFT);
			$binData .= $recvFax;
	
			// SENDER NAME	Char	331	30	Mand	Sender’s Name
			// (Left Justify, Trailing Space filled)
			// This field must NOT be blank.
			// This is the company name or name of person who is sending the goods.
			$sendName = $shipment->cnor->name;
			if ( strlen($sendName) >= 30 ) {
				$sendName = substr($sendName,0,30);
			} else {
				$sendName = str_pad($sendName, 30, " ", STR_PAD_LEFT);
			}
			$binData .= $sendName;
	
			// SENDER TOWN	Char	361	30	Mand	Sender’s Address Town Name
			// (Left Justify, Trailing Space filled)
			// This field must NOT be all blank.
			$sendSuburb = $shipment->cnor->suburb;
			if ( strlen($sendSuburb) >= 30 ) {
				$sendSuburb = substr($sendSuburb,0,30);
			} else {
				$sendSuburb = str_pad($sendSuburb, 30, " ", STR_PAD_LEFT);
			}
			$binData .= $sendSuburb;
	
			// SENDER POSTCODE	Num	391	6	Mand	Senders Address Postcode
			// (Left Justify, Trailing Space filled)
			// This field must NOT be all blank.
			$postcode = '00' . $shipment->cnor->postcode;
			$binData .= $postcode;
	
			// SENDER CONTACT NAME	Char	397	20	Opt	Senders Contact Name
			// (Left Justify, Trailing Space filled)
			// Name of a person that can be contacted in regards to despatch of the freight. Phone contact only.
			$sendContactName = $shipment->cnor->name;
			if ( strlen($sendContactName) >= 20 ) {
				$sendContactName = substr($sendContactName,0,20);
			} else {
				$sendContactName = str_pad($sendContactName, 20, " ", STR_PAD_LEFT);
			}
			$binData .= $sendContactName;
	
			// SENDER CONTACT PHONE	Char	417	11	Opt	Senders Contact Telephone Number
			//  (Left Justify, Trailing Space filled)
			// Format: Numeric. Country code (optional), area code then number. Spaces, ‘+’, ‘()’ etc must not be used for digit separation.
			// For example: 0396762233 – landline, 0411123456 – mobile. If expressed as an international number, sent as such - 61396762233 for landline or 61411123456 for mobile.
			//  Phone number cannot exceed 11 digits.
			//  Phone contact only.
			$phone = $shipment->cnor->tel;
			if ( strlen($phone) >= 11 ) {
				$phone = substr($phone,0,11);
			} else {
				$phone = str_pad($phone, 11, " ", STR_PAD_LEFT);
			}
			$binData .= $phone;
	
			// SENDER CONT FAX	Char	428	11	Not Used	Senders Contact Facsimile Number
			// (Left Justify, Trailing Space filled)
			$sendFax = '';
			$sendFax = str_pad($sendFax, 11, " ", STR_PAD_LEFT);
			$binData .= $sendFax;
	
	
			// CONNOTE REFERENCE	Char	439	15	Opt	Customers Reference at a connote Level
			// (Left Justify, Trailing Space filled)
			// Not used by Toll Priority.
			// Use the ‘Extended Connote References’ at position 920.
			$connoteRef = '';
			$connoteRef = str_pad($connoteRef, 15, " ", STR_PAD_LEFT);
			$binData .= $connoteRef;
	
			// RECV KEY	Char	454	15	Opt	Receivers Key
			// (Left Justify, Trailing Space filled)
			// Used by Toll Connect
			$recvKey = '';
			$recvKey = str_pad($recvKey, 15, " ", STR_PAD_LEFT);
			$binData .= $recvKey;
	
			// PALLET CHEP	Num	469	3	Opt	Number of CHEP type pallets
			// (Left Justify, Trailing Space filled)
			$palletChep = '';
			$palletChep = str_pad($palletChep, 3, " ", STR_PAD_LEFT);
			$binData .= $palletChep;
	
			// PALLET LOSCAM	Num	472	3	Not Used	Number of LOSCAM type pallets
			// (Left Justify, Trailing Space filled)
			$binData .= '000';
	
			// PALLET OTHER	Num	475	3	Not Used	Number of other type of pallets
			// (Left Justify, Trailing Space filled)
			$binData .= '000';
	
			// INSUR CLASS	Char	478	1	Not Used	Insurance Class Code
			// (Left Justify, Trailing Space filled)
			$binData .= '0';
	
			// INSUR COVER	Num	479	7	Opt	Toll Extra Service Amount
			// (Right Justify, Leading Zeros filled)
			// This represents the amount of Toll Extra Service required. The field must not contain any sign or decimal
			// place indicators. An amount of Zero is acceptable. This field is in Dollars & Cents. ie. ($$$$$cc) so 0015000 is $150.00
			$binData .= '0000000';
	
			// CHARGE CODE	Char	486	1	Mand	Charge Code (Who Pays)
			// Acceptable Values:
			// S = Sender to Pay
			// R = Receiver to Pay
			// T = 3rd Party
			$binData .= 'S';
	
			// APPLY BASIC CHARGE	Char	487	1	Not Used	Apply Basic Charge
			$binData .= '0';
	
			// CHARGE ACCOUNT	Char	488	10	Mand	Charge Account Number
			// (Left Justify, Trailing Space filled)
			// Mandatory and this field MUST contain a valid Toll Priority account number.
			$binData .= '0000000000'; // TODO mod soon
	
			// CHARGE BASIC	Num	498	5	Not Used	Charge:   Basic
			// (Left Justify, Trailing Space filled)
			$binData .= '00000';
	
			// CHARGE FREIGHT	Num	503	7	Not Used	Charge:   Freight
			// (Left Justify, Trailing Space filled)
			$binData .= '0000000';
	
			// CHARGE EXTRA	Num	510	7	Not Used	Charge:   Extras
			// (Left Justify, Trailing Space filled)
			$binData .= '0000000';
	
			// CHARGE OTHER	Num	517	7	Not Used	Charge:   Other
			// (Left Justify, Trailing Space filled)
			$binData .= '0000000';
	
			// CHARGE TAX	Num	524	6	Not Used	Charge:   Tax
			// (Left Justify, Trailing Space filled)
			$binData .= '000000';
	
			// CHARGE SURCHARGE	Num	530	5	Not Used	Charge:   Surcharge
			// (Left Justify, Trailing Space filled)
			$binData .= '00000';
	
			// CHARGE TOTAL	Num	535	9	Opt	Charge:   Total
			// (Left Justify, Trailing Space filled)
			// Used to express total monetary amount that customer will be charged. Also referred to as Hand Priced Connote value.
			// Must be blank if Position 663 is blank.
			$binData .= '000000000';
	
			// CHARGE INSURANCE PER CONNOTE	Num	544	9	Not Used	Charge - Insurance per Connote
			// (Left Justify, Trailing Space filled)
			$binData .= '000000000';
	
			// CHARGE INSURANCE ADDITIONAL 	Num	553	9	Not Used	Charge:   Insurance
			//  (Left Justify, Trailing Space filled)
			$binData .= '000000000';
	
			// ITEM IDENTIFIER
			// Char	562	20	Not Used	Item Identification
			// (Left Justify, Trailing Space filled)
			$itemIdentifier = '';
			$itemIdentifier = str_pad($itemIdentifier, 20, " ", STR_PAD_LEFT);
			$binData .= $itemIdentifier;
	
			// LINE UNIT COMMOD ITEMS COUNT	Num	582	5	Not Used	Unit commodity items count.
			// (Left Justify, Trailing Space filled)
			$binData .= '00000';
	
			// LINE UNIT COMMOD ITEMS CODE	Char	587	2	Not Used	Unit Commodity items code.
			// (Left Justify, Trailing Space filled)
			$binData .= '00';
	
			// NUMBER OF ITEMS	Num	589	5	Mand	Number of items
			// (Right Justify, Leading Zeros filled)
			// This value will always be ‘00001’ for Toll Priority, as each record in this file will represent a single item in the consignment.
			$pkg = sprintf('%d',$shipment->pkg);
			$len = 5 - sizeof($pkg);
			for ( $i = 0 ; $i < $len ; $i++ ) {
				$binData .= pack('C',0);
			}
			$binData .= $pkg;
	
			// LINE WEIGHT	Num	594	6	Mand	Weight of this item
			//  (Right Justify, Leading Zeros filled)
			// Format: All digits only. Minimum vlaue is 000001.
			// This field is considered to be in units of tenths of kilograms. eg. 003456 is 345.6 kilograms.
			$weight = floatval($shipment->weight);
			$floor = floor($weight);
			$fraction = $weight - $floor;
			if ( $fraction > 0.0 ) {
				$weight = sprintf('%d%d',$floor, floor($fraction * 10));
			} else {
				$weight = sprintf('%d',$floor);
			}
			$len = 5 - sizeof($weight);
			for ( $i = 0 ; $i < $len ; $i++ ) {
				$binData .= pack('C',0);
			}
			$binData .= $weight;
	
			// LINE COMMOD	Char	600	2	Opt	Commodity (Unit) Code
			// (Left Justify, Trailing Space filled)
			// Used by DX Mail customers only.
			$lineCommod = '';
			$lineCommod = str_pad($lineCommod, 2, " ", STR_PAD_LEFT);
			$binData .= $lineCommod;
	
			// PACKAGE TYPE	Char	602	1	Not Used	Package Type
			$binData .= '0';
	
			// DESCRIPTION OF GOODS	Char	603	20	Opt	Description of Goods
			// (Left Justify, Trailing Space filled)
			$goodsDesc = '';
			$goodsDesc = str_pad($goodsDesc, 20, " ", STR_PAD_LEFT);
			$binData .= $goodsDesc;
	
	
			// CUBIC LENGTH	Num	623	5	Mand	Length of goods (centimetres)
			// (Right Justify, Leading Zeros filled)
			// Format: Numeric Only – ie. 00400 = 400 centimetres
			// Refer section 4.5 “Services with Mandatory Cubic Requirements”
			$length = 0;
			if ( !empty($shipment->mdata['dim']['d']) ) $length = floatval($shipment->mdata['dim']['d']);
			$floor = floor($length);
			$fraction = $length - $floor;
			if ( $fraction > 0.0 ) {
				$length = sprintf('%d%d',$floor, floor($fraction * 10));
			} else {
				$length = sprintf('%d',$floor);
			}
			$len = 5 - sizeof($length);
			for ( $i = 0 ; $i < $len ; $i++ ) {
				$binData .= pack('C',0);
			}
			$binData .= $length;
	
			// CUBIC WIDTH	Num	628	5	Mand	Width of goods (centimetres)
			// (Right Justify, Leading Zeros filled)
			// Format: Numeric Only – ie. 00012 = 12 centimetres
			// Refer section 4.5 “Services with Mandatory Cubic Requirements”
			$width = 0;
			if ( !empty($shipment->mdata['dim']['w']) ) $width = floatval($shipment->mdata['dim']['w']);
			$floor = floor($width);
			$fraction = $width - $floor;
			if ( $fraction > 0.0 ) {
				$width = sprintf('%d%d',$floor, floor($fraction * 10));
			} else {
				$width = sprintf('%d',$floor);
			}
			$len = 5 - sizeof($width);
			for ( $i = 0 ; $i < $len ; $i++ ) {
				$binData .= pack('C',0);
			}
			$binData .= $width;
	
			// CUBIC HEIGHT	Num	633	5	Mand	Height of goods (centimetres)
			// (Right Justify, Leading Zeros filled)
			// Format: Numeric Only – ie. 00001 = 1 centimetre
			// Refer section 4.5 “Services with Mandatory Cubic Requirements”
			$height = 0;
			if ( !empty($shipment->mdata['dim']['h']) ) $height = floatval($shipment->mdata['dim']['h']);
			$floor = floor($height);
			$fraction = $height - $floor;
			if ( $fraction > 0.0 ) {
				$height = sprintf('%d%d',$floor, floor($fraction * 10));
			} else {
				$height = sprintf('%d',$floor);
			}
			$len = 5 - sizeof($height);
			for ( $i = 0 ; $i < $len ; $i++ ) {
				$binData .= pack('C',0);
			}
			$binData .= $height;
	
			// CUBIC QTY	Num	638	5	Mand	Cubic Quantity
			// (Right Justify, Leading Zeros filled)
			// This field may contain all zeros if the other cubic fields are also zero, otherwise it must contain a minimum value of 00001.
			// This field is an expansion of the cubic information and is used as a multiplier for the existing cubic fields.
			// It allows us to say that we want n lots of the given cubic. ie. 10 lots of (L x W x D).
			// Refer section 4.5 “Services with Mandatory Cubic Requirements”
			for ( $i = 0 ; $i < 4 ; $i++ ) {
				$binData .= pack('C',0);
			}
			$binData .= '1';
	
			// CUBIC VOLUME	Num	643	5	Mand	Volume of goods in cubic metres
			// (Right Justify, Leading Zeros filled)
			// Format: Numeric (all digits)
			// This field is to 3 decimal places, hence, 03500 = 3.5 cubic metres. If all four "cubic" fields
			// CUBIC LENGTH, CUBIC WIDTH, CUBIC DEPTH, CUBIC QTY > 0 then:  CUBIC VOLUME =
			//    Lgth x Width x Depth x Qty / 1000
			//    Example:
			//    CUBIC LENGTH   = 00165
			//    CUBIC WIDTH     = 00155
			//      CUBIC DEPTH     = 00125
			//        CUBIC QTY          = 00002
			//        CUBIC VOLUME  = 06394
			//        Line Volume is rounded from the resultant calculation.
			// Refer section 4.5 “Services with Mandatory Cubic Requirements”
			$cbm = $shipment->cbm;
			$floor = floor($cbm);
			$fraction = $cbm - $floor;
			if ( $fraction > 0.0 ) {
				$cbm = sprintf('%d%02d',$floor, floor($fraction * 10));
			} else {
				$cbm = sprintf('%d',$floor);
			}
			$len = 5 - sizeof($cbm);
			for ( $i = 0 ; $i < $len ; $i++ ) {
				$binData .= pack('C',0);
			}
			$binData .= $cbm;
	
	
			// SENDER REFERENCE	Char	648	15	Opt	Customer’s own Reference at Line Item Level
			// (Left Justify, Trailing Space filled)
			$senderRef = '';
			$senderRef = str_pad($senderRef, 15, " ", STR_PAD_LEFT);
			$binData .= $senderRef;
	
			// RATING CODE	Char	663	1	Opt	Rating Code
			// Flag to specify type of pricing.
			// ‘H’ (capital) if it is a Hand Price connote.
			//  Blank - System Calculated. Position 535 field is expected to have value if contains ‘H’.
			$binData .= ' ';
	
			// CONSOLIDATION FLAG	Char	664	1	Not Used	Consolidation Flag
			$binData .= '0';
	
			// SECURITY FLAG	Char	665	1	Not Used	Security Flag
			$binData .= '0';
	
			// REMOTE PRINTING FLAG	Char	666	1	Not Used	Remote Printing Flag
			$binData .= '0';
	
			// HANDPRICED INSURANCE	Char	667	1	Not Used	Hand Priced Insurance Flag
			$binData .= '0';
	
			// DIRECT DELIVERY	Char	668	1	Not Used	Direct Delivery Flag.
			$binData .= '0';
	
			// RETURNS COLLECTIONS	Char	669	1	Not Used	Returns/Collections Flag
			$binData .= '0';
	
			// SERVICE DESCRIPTION	Char	670	25	Mand	Service Name.
			// (Left Justify, Trailing Space filled)
			//  Refer to Toll Priority Service Codes and Names
			$serviceDesc = 'Parcels Overnight';
			$serviceDesc = str_pad($serviceDesc, 25, " ", STR_PAD_LEFT);
			$binData .= $serviceDesc;
	
			// SMS REQUIRED FLAG	Char	695	1	Opt	SMS Required Flag
			// Used for SMS notification sent to Consumer about impending delivery. Used in conjunction with field 1218.
			// ‘1’ for send SMS message. Field 1218 must have a valid phone number for SMS notification.
			// ‘0’ do not  send SMS message
			// Space - functionality not used.
			$binData .= '0';
	
			// EXTRA CHARGE CODES	Char	696	9	Not Used	Extra Charge Codes
			// (Left Justify, Trailing Space filled)
			$extraChargeCode = '';
			$extraChargeCode = str_pad($extraChargeCode, 9, " ", STR_PAD_LEFT);
			$binData .= $extraChargeCode;
	
			//EXTENDED DESCRIPTION	Char	705	1	Not Used	Extended Description Flag
			$binData .= '0';
	
			// SENDER ADDRESS 1	Char	706	30	Mand	Senders Address Line 1
			// (Left Justify, Trailing Space filled)
			// This field must NOT be all blank.
			$sendAddr = $shipment->cnor->address;
			if ( strlen($sendAddr) >= 30 ) {
				$sendAddr = substr($sendAddr,0,30);
			} else {
				$sendAddr = str_pad($sendAddr, 30, " ", STR_PAD_LEFT);
			}
			$binData .= $sendAddr;
	
			// SENDER ADDRESS 2	Char	736	30	Opt	Senders Address Line 2
			// (Left Justify, Trailing Space filled)
			$sendAddr2 = '';
			$sendAddr2 = str_pad($sendAddr2, 30, " ", STR_PAD_LEFT);
			$binData .= $sendAddr2;
	
			// NOTIFICATION EMAIL ADDRESS	Char	766	78	Opt	Notification Email Address
			// e.g. user@email.com.au
			// This is email address of Consumer, ie the end recipient of the goods.
			// Must be provided in B2C scenarios to enable consumer notifications.
			$notiEmail = '';
			$notiEmail = str_pad($notiEmail, 78, " ", STR_PAD_LEFT);
			$binData .= $notiEmail;
	
			// SENDER LOCATION	Char	844	6	Not Used	Senders Location Code
			// (Left Justify, Trailing Space filled)
			$sendLocation = '';
			$sendLocation = str_pad($sendLocation, 6, " ", STR_PAD_LEFT);
			$binData .= $sendLocation;
	
			// SENDER LOCATION EXT	Char	850	3	Not Used	Senders Location Extension Code
			// (Left Justify, Trailing Space filled)
			$sendLocationExt = '';
			$sendLocationExt = str_pad($sendLocationExt, 3, " ", STR_PAD_LEFT);
			$binData .= $sendLocationExt;
	
			// RECEIVER LOCATION	Char	853	6	Not Used	Receivers Location Code
			// (Left Justify, Trailing Space filled)
			$recvLocation = '';
			$recvLocation = str_pad($recvLocation, 6, " ", STR_PAD_LEFT);
			$binData .= $recvLocation;
	
			// RECEIVER LOCATION EXT	Char	859	3	Not Used	Receivers Location Extension Code
			// (Left Justify, Trailing Space filled)
			$recvLocationExt = '';
			$recvLocationExt = str_pad($recvLocationExt, 3, " ", STR_PAD_LEFT);
			$binData .= $recvLocationExt;
	
			// DANGER CLASS	Char	862	10	Not Used	Dangerous Goods Class
			// (Left Justify, Trailing Space filled)
			$dangerClass = '';
			$dangerClass = str_pad($dangerClass, 10, " ", STR_PAD_LEFT);
			$binData .= $dangerClass;
	
			// DG PACK GROUP	Char	872	10	Not Used	Dangerous Goods Pack Group
			// (Left Justify, Trailing Space filled)
			$dgPackGroup = '';
			$dgPackGroup = str_pad($dgPackGroup, 10, " ", STR_PAD_LEFT);
			$binData .= $dgPackGroup;
	
			// DG UN CODE	Char	882	6	Not Used	Dangerous Goods UN Code
			// (Left Justify, Trailing Space filled)
			$dgUnCode = '';
			$dgUnCode = str_pad($dgUnCode, 6, " ", STR_PAD_LEFT);
			$binData .= $dgUnCode;
	
			// ITEM NUMBER	Char	888	32	Mand	Item Number
			// (Left Justify, Trailing Space filled)
			// Refer to Connote & Item Numbering Requirement
			$itemNumber = $shipment->ref;
			$itemNumber = str_pad($itemNumber, 32, " ", STR_PAD_LEFT);
			$binData .= $itemNumber;
	
			// EXTENDED CONNOTE REFERENCE	Char	920	40	Opt	Extended Customers Reference at a connote Level
			// (Left Justify, Trailing Space filled)
			// Alphanumeric This is a reference at connote level.
			// Search by this reference is enabled in Toll Online.
			$extendedConnoteRef = '';
			$extendedConnoteRef = str_pad($extendedConnoteRef, 40, " ", STR_PAD_LEFT);
			$binData .= $extendedConnoteRef;
	
	
			// SENDER COUNTRY	Char	960	30	Mand	Sender Country
			// (Left Justify, Trailing Space filled)
			$sendCountry = 'Australia';
			$sendCountry = str_pad($sendCountry, 30, " ", STR_PAD_LEFT);
			$binData .= $sendCountry;
	
			// RECEIVER COUNTRY	Char	990	30	Mand	Receiver Country
			// (Left Justify, Trailing Space filled)
			$recvCountry = 'Australia';
			$recvCountry = str_pad($recvCountry, 30, " ", STR_PAD_LEFT);
			$binData .= $recvCountry;
	
			// DECLARED VALUE	Num	1020	20	Opt	Declared Consignment Value for Customs
			// (Right Justify, Leading Zeros filled)
			// This represents the Declared Consignment Value for Customs purposes.
			// The field must not contain any sign or decimal place indicators.
			// An amount of Zero is acceptable. This field is in Dollars & Cents. ie. ($$$$$cc) so 0000015000 is $150.00
			$declaredValue = '';
			$declaredValue = str_pad($declaredValue, 20, '0', STR_PAD_LEFT);
			$binData .= $declaredValue;
	
			// DECLARED CURRENCY CODE	Char	1040	10	Opt	Declared Consignment Value Currency Code
			// (Left Justify, Trailing Space filled)
			// The currency code for the declared value. For example, AUD = Australian Dollars, NZD = New Zealand Dollars.
			$declaredCur = 'AUD';
			$declaredCur = str_pad($declaredCur, 10, " ", STR_PAD_LEFT);
			$binData .= $declaredCur;
	
			// OTHER REFS	Char	1050	40	Opt	Other Refs – Sender’s Reference 2
			// (Left Justify, Trailing Space filled)
			$otherRefs = '';
			$otherRefs = str_pad($otherRefs, 40, " ", STR_PAD_LEFT);
			$binData .= $otherRefs;
	
			// Collection Port	Char	1090	3	Opt	Collection Port
			// (Left Justify, Trailing Space filled)
			// The airport code from which this consignment was sent from.
			$collection = '';
			$collection = str_pad($collection, 3, " ", STR_PAD_LEFT);
			$binData .= $collection;
	
			// Delivery Port	Char	1093	3	Opt	Delivery Depot
			// (Left Justify, Trailing Space filled)
			// The airport code that this consignment was sent to.
			$deliveryPort = '';
			$deliveryPort = str_pad($deliveryPort, 3, " ", STR_PAD_LEFT);
			$binData .= $deliveryPort;
	
			// Receipt Amount	Num	1096	9	Opt	Receipt Amount
			// (Right Justify, Leading Zeros filled)
			// This represents the total amount paid by the sender for all TAE POS consignments in a single manifest/receipt.
			// The field must not contain any sign or decimal place indicators. An amount of Zero is acceptable.
			// This field is in Dollars & Cents. ie. ($$$$$cc) so 000015000 is $150.00
			$binData .= '000000000';
	
			// Receipt Date	Num	1105	6	Opt	Receipt Date
			// Format: YYMMDD  (all digits)
			// This represents the date on which the sender paid for their TAE POS consignments.
			$binData .= date('ymd');
	
			// Payment Method	Char	1111	10	Opt	Payment Method
			// (Left Justify, Trailing Space filled)
			// The method in which the sender paid for their TAE POS consignments - Cash, Credit Card, Cheque, EFT.
			$payMethod = 'EFT';
			$payMethod = str_pad($payMethod, 10, " ", STR_PAD_LEFT);
			$binData .= $payMethod;
	
			// Payment Reference	Char	1121	20	Opt	Payment Reference
			// (Left Justify, Trailing Space filled)
			// The payment reference for a TAE POS manifest/receipt.
			$payRef = '';
			$payRef = str_pad($payRef, 20, " ", STR_PAD_LEFT);
			$binData .= $payRef;
	
	
			// External Freight Amount	Num	1141	9	Opt	Freight Value for TAE POS use only
			// (Right Justify, Leading Zeros filled)
			// This represents the Freight component of a TAE POS consignment.
			// The field must not contain any sign or decimal place indicators. An amount of Zero is acceptable.
			// This field is in Dollars & Cents. ie. ($$$$$cc) so 000015000 is $150.00
			$binData .= '000000000';
	
			// External Surcharge Amount	Num	1150	9	Opt	Surcharge Value for TAE POS use only (Right Justify, Leading Zeros filled)
			// This represents the Surcharge component of a TAE POS consignment. The field must not contain any sign or decimal place indicators.
			// An amount of Zero is acceptable. This field is in Dollars & Cents. ie. ($$$$$cc) so 000015000 is $150.00
			$binData .= '000000000';
	
			// External GST Amount	Num	1159	9	Opt	GST Value for TAE POS use only
			// (Right Justify, Leading Zeros filled)
			// This represents the GST component of a TAE POS consignment.
			// The field must not contain any sign or decimal place indicators. An amount of Zero is acceptable.
			// This field is in Dollars & Cents. ie. ($$$$$cc) so 000015000 is $150.00
			$binData .= '000000000';
	
			// SPARE	Char	1168	12	Not Used	Spare
			// (Left Justify, Trailing Space filled)
			$spare = '';
			$spare = str_pad($spare, 12, " ", STR_PAD_LEFT);
			$binData .= $spare;
	
			// HUD BATCH NUMBER	Char	1180	14	Not Used	Heads Up Data batch number
			// (Left Justify, Trailing Space filled)
			$hud = '';
			$hud = str_pad($hud, 14, " ", STR_PAD_LEFT);
			$binData .= $hud;
	
	
			// SENDER ID	Char	1194	3	Opt	Sender’s ID
			// (Left Justify, Trailing Space filled)
			$senderId = '';
			$senderId = str_pad($senderId, 3, " ", STR_PAD_LEFT);
			$binData .= $senderId;
	
			// SENDER ADDRESS STATE	Char	1197	3	Mand	Sender’s Address State Code
			// (Left Justify, Trailing Space filled) eg. VIC, NSW
			$binData .= $shipment->cnor->state;
	
	
			// RECEIVER ADDRESS STATE	Char	1200	3	Mand	Receiver’s Address State Code
			// (Left Justify, Trailing Space filled) eg. VIC, NSW
			$binData .= $shipment->cnee->state;
	
	
			// MANIFEST NUMBER	Char	1203	10	Mand	Manifest Number
			// (Right Aligned - Left Zero Filled) Each Manifest and corresponding Connote Data file should be sequential numbered.
			$binData .= '0000000001'; // TODO mod soon
	
			// RECORD ID	Num	1213	5	Not Used	Record ID with file
			// (Left Justify, Trailing Space filled)
			$binData .= '00000';
	
	
			// SMS NOTIFICATION NUMBER	Char	1218	28	Opt	SMS Notification Phone Number
			// Format – Numeric.
			// For Australian market - mobile number starting with 04
			// e.g. 0468123456.
			// Must be provided in B2C business scenarios.
			// This is phone number of Consumer, ie the end receiver of freight for SMS notification about impending delivery.
			$smsNumber = '';
			$smsNumber = str_pad($smsNumber, 28, " ", STR_PAD_LEFT);
			$binData .= $smsNumber;
	
			// CHECKSUM	Char	1246	3	Not Used	Checksum
			// (Left Justify, Trailing Space filled)
			$binData .= '000';
	
			// TERMINATOR	Char	1249	2	Mand	Record Terminator (CR & NL)
			// Each record is terminated by a Carriage Return and New-line (in that order). This field is mandatory and will always contain the hex values: (HEX: 0D0A)
			$binData .= '\r\n';
	
			return $binData;
		}
	
		/**
		 * get binary consignments data for toll SFTP API
		 * @param $shipments
		 */
	/*
	public function getBinConsignments(&$shipments){
		$binContent = '';
		foreach ( $shipments as $shipment ) {
			$binContent .= $this->getBinDataByShipment($shipment);
		}

		// add file terminator
		// 2.2	End Of File Record
		// The EOF record is a mandatory record containing a fixed message component.
		// This record is important as it allows Toll Group IT to readily determine that the whole of the anticipated file has been received.
		//  In the case of partial transmissions, the file will be rejected due to the absence of the EOF record.
		// EOF record contains:
		// Field	Type	Size	Mand.	Description
		// EOF ID	Char	5	Mand	Fixed Message – Always ‘%%EOF’
		// TERMINATOR	Char	2	Mand	Record Terminator (CR & NL)
		// Each record is terminated by a Carriage Return and New-line (in that order).
		// This field is mandatory and will always contain the hex values: (HEX: 0D0A)

		$binContent .= '%%EOF';
		$binContent .= '\r\n';

		return $binContent;
	}
	*/
//end of class
}
