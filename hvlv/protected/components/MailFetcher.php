<?php
class MailFetcher
{
	private $_pop;
	private $_3plpop;
	private $_incoConsol;
	private $_manifest;
	private $_huntertracking;
	private $_pop_history;
	private $_3plpop_history;
	private $_incoConsol_history;
	private $_manifest_history;
	private $_huntertracking_history;
	const SERVER = "mail.toplogistics.com.au"; //"mail.pcaexpress.com.au";//                $server=;

	function __construct()
	{
		if (!extension_loaded('imap')) {
			if (!dl('imap.so')) {
				die("Fetcher Error: This class requires imap extension to run!<br/>");
			}
		}
		//110 for pop3
		//INBOX.history
		//Open stream connection to the POP server
		$this->_pop = imap_open("{" . self::SERVER . ":143/imap/notls/novalidate-cert}INBOX", "imports.auto@toplogistics.com.au", '3g%c2a#76ejiQu22') or die("Connection Error: " . imap_last_error() . "<br/>");
		$this->_3plpop = imap_open("{" . self::SERVER . ":143/imap/notls/novalidate-cert}INBOX", "3pl@toplogistics.com.au", '3g%c2a#76ejiQu22') or die("Connection Error: " . imap_last_error() . "<br/>");
		//$this->_incoConsol = imap_open("{" . self::SERVER . ":143/imap/notls/novalidate-cert}INBOX", "nero@toplogistics.com.au", ',zAJ#NcR_cWK') or die("Connection Error: " . imap_last_error() . "<br/>");
		//$this->_incoConsol = imap_open("{" . self::SERVER . ":143/imap/notls/novalidate-cert}INBOX", "nerotest@toplogistics.com.au", '7,Z#3o-V-2NU') or die("Connection Error: " . imap_last_error() . "<br/>");
		$this->_incoConsol = imap_open("{" . self::SERVER . ":143/imap/notls/novalidate-cert}INBOX", "prealert@toplogistics.com.au", '=6qnC&PAbDK]') or die("Connection Error: " . imap_last_error() . "<br/>");
		$this->_manifest = imap_open("{" . self::SERVER . ":143/imap/notls/novalidate-cert}INBOX", "manifestupload@toplogistics.com.au", 'EUXOryb&I~P9') or die("Connection Error: " . imap_last_error() . "<br/>");
		//$this->_huntertracking = imap_open("{" . self::SERVER . ":143/imap/notls/novalidate-cert}INBOX", "nerotest@toplogistics.com.au", '7,Z#3o-V-2NU') or die("Connection Error: " . imap_last_error() . "<br/>");
		$this->_huntertracking = imap_open("{" . self::SERVER . ":143/imap/notls/novalidate-cert}INBOX", "autohuntertracking@toplogistics.com.au", '}vaiyxirw+aK') or die("Connection Error: " . imap_last_error() . "<br/>");
		//$this->_pop_history=imap_open("{"."mail.pcaexpress.com.au".":143/imap/tls/novalidate-cert}INBOX.history", "importstest@toplogistics.com.au", ",zAJ#NcR_cWK") or die ("Connection Error: " . imap_last_error()."<br/>");
		if (@imap_num_msg($this->_pop) == 0) {
			$errors = imap_errors();
			imap_close($this->_pop);
			//echo ("No Message found!\n");
			$this->_pop = false;
		}

		if (@imap_num_msg($this->_3plpop) == 0) {
			$errors = imap_errors();
			imap_close($this->_3plpop);
			//echo ("No Message found!\n");
			$this->_3plpop = false;
		}
	}

	public function fetchHunterTracking()
	{
		$mypath = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'huntertracking' . DIRECTORY_SEPARATOR;
		if (empty($this->_huntertracking)) return [];
		foreach (imap_sort($this->_huntertracking, SORTARRIVAL, 0) as $mid) {
			try {
				$attachments = [];
				$attachments = self::getAllattrachmentsInEmail($this->_huntertracking, $mid);
				//$result = 1;
				$result = imap_mail_move($this->_huntertracking, $mid, 'INBOX.history1');
				if ($result) {
					foreach ($attachments as $attachment) {
						if ($attachment['is_attachment'] == true) {
							if (preg_match('/TOP-F&L/i', $attachment['filename'])) {
								if (preg_match('/|.csv/i', $attachment['filename'])) {
									$savePath = $mypath . date('Y-m-dH-i-s') . $attachment['name'];
									if (file_put_contents($savePath, $attachment['attachment'])) {
										$xls = new oExcel('CSV');
										$xls->load($savePath);
										$data = $xls->getAll();
										if (strpos('Consign NumberDateSender NameReceiver NameReceiver SuburbTimestampStatusQtyWeightGeneral NoteCubicRemarks', implode('', $data[1])) === 0) {
											$error = self::addFLHunterTracking($data);
										}
									}
								}
							}
							if(preg_match('/ConsignmentStatusUpdate/i', $attachment['filename'])){
								if (preg_match('/|.csv/i', $attachment['filename'])) {
									$savePath = $mypath . date('Y-m-dH-i-s') . $attachment['name'];
									if (file_put_contents($savePath, $attachment['attachment'])) {
										$xls = new oExcel('CSV');
										$xls->load($savePath);
										$data = $xls->getAll();
										if (strpos('ConsignmentNumberUniqueIdSenderReferenceStatusDateTimeStatusStatusTag', implode('', $data[1])) === 0) {
											$error = self::addFLHunterTrackingSingle($data);
										}
									}
								}
							}
						}
					}
				}
			} catch (Exception $ex) {
				continue;
			}
		}
		$this->finishHunterTracking();
	}

	public static function addFLHunterTrackingSingle($data){
		unset($data[1]);
		$last_shipmentno = "";
		foreach ($data as $i => $d) {
			if (empty($d[4])) {
				continue;
			}
			if ($last_shipmentno != $d[1]) {
				if (!empty($d[1])) {
					$last_shipmentno = $d[1];
					$arrShipment[$d[1]] = [
						'ref' => trim($d[1]),
						'trackings' => []
					];
				}
			}
			if (empty($d[4])) {
				$err[] = "line " . $i . " Timestamp is empty!";
				continue;
			}
			if (empty($d[5])) {
				$err[] = "line " . $i . " Status is empty!";
				continue;
			}
			$dateTimetamp = date('Y-m-d',strtotime(str_replace('/', '-', substr($d[4],0,10)))).substr($d[4],10,6).":00";
			$strStatus = trim($d[5]);
			$arrShipment[$last_shipmentno]['trackings'][] = [
				'timetamp' => $dateTimetamp,
				'status' => $strStatus
			];
		}
		foreach ($arrShipment as $a) {
			$objImparcel = ImParcel::model()->find('ref = :ref and status!=100',[':ref'=>$a['ref']]);
			if(!empty($objImparcel)){
				foreach($a['trackings'] as $trackinfo){
					$objImparcel->addActTrackingLimited(106, $trackinfo['status'],'', $trackinfo['timetamp']);
				}
			}
		}
	}

	public static function addFLHunterTracking($data)
	{
		unset($data[1]);
		$last_shipmentno = "";

		foreach ($data as $i => $d) {
			if (empty($d[6])) {
				continue;
			}
			if ($last_shipmentno != $d[1]) {
				if (!empty($d[1])) {
					$last_shipmentno = $d[1];
					$arrShipment[$d[1]] = [
						'ref' => trim($d[1]),
						'trackings' => []
					];
				}
			}
			if (empty($d[6])) {
				$err[] = "line " . $i . " Timestamp is empty!";
				continue;
			}
			if (empty($d[7])) {
				$err[] = "line " . $i . " Status is empty!";
				continue;
			}

			$dateTimetamp = date('Y-m-d',strtotime(str_replace('/', '-', substr($d[6],0,10)))).substr($d[6],10,6).":00";
			$strStatus = trim($d[7]);
			$arrShipment[$last_shipmentno]['trackings'][] = [
				'timetamp' => $dateTimetamp,
				'status' => $strStatus
			];
		}

		foreach ($arrShipment as $a) {
			$objImparcel = ImParcel::model()->find('ref = :ref and status!=100',[':ref'=>$a['ref']]);
			if(!empty($objImparcel)){
				foreach($a['trackings'] as $trackinfo){
					$objImparcel->addActTrackingLimited(106, $trackinfo['status'],'', $trackinfo['timetamp']);
				}
			}
		}
	}



	public function fetchAttrachmentIncoConsolNew()
	{
		$mypath = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'incoConsol' . DIRECTORY_SEPARATOR;
		if (empty($this->_incoConsol)) return [];
		if(empty(imap_sort($this->_incoConsol, SORTARRIVAL, 0))){
			return [];
		}
		foreach (imap_sort($this->_incoConsol, SORTARRIVAL, 0) as $mid) {
			try {
				
				$parser = new PhpMimeMailParser\Parser();
				$parser->addMiddleware(function ($mimePart, $next) {
					$part = $mimePart->getPart();
					foreach (['from', 'to', 'cc'] as $t) {
						if (isset($part['headers'][$t])) {
							$as = preg_split('/,\s*/', $part['headers'][$t]);
							$ads = [];
							foreach ($as as $a) {
								$ads[] = preg_replace('/(.*<[a-zA-Z0-9._%-]+@[a-zA-Z0-9.\-]+>).*/', "$1", $a);
							}
							$part['headers'][$t] = implode(',', $ads);
						}
					}

					//fix content ID
					if (isset($part['headers']['content-id'])) {
						$part['headers']['content-id'] = preg_replace('/^<+([^>]+)>+$/', '<$1>', $part['headers']['content-id']);
					}
					$mimePart->setPart($part);

					return $next($mimePart);
				});

				$body = imap_body($this->_incoConsol, $mid);
				$headers = imap_fetchheader($this->_incoConsol, $mid, FT_PREFETCHTEXT);
				if (empty($headers) || empty($body)) continue;
				$message = $headers . "\n" . $body;
				@$parser->setText($message);






				$header = imap_headerinfo($this->_incoConsol, $mid);
				$fromaddr = $header->from[0]->mailbox . "@" . $header->from[0]->host;
				$attachments = [];
				$attachments = self::getAllattrachmentsInEmail($this->_incoConsol, $mid);
				$result = imap_mail_move($this->_incoConsol, $mid, 'INBOX.history');
				if ($result) {
					foreach ($attachments as $attachment) {
						if ($attachment['is_attachment'] == true) {
							if (preg_match('/imco_consol_import_template|pre_alert_air/i', $attachment['filename'])) {
								$isAirImport = true;
								if (preg_match('/.tmp|.xlsx/i', $attachment['filename'])) {
									$savePath = $mypath . date('Y-m-dH-i-s') . $attachment['name'];
									if (file_put_contents($savePath, $attachment['attachment'])) {
										$xls = new oExcel;
										$xls->load($savePath);
										$data = $xls->getAll();
										if (strpos('WarehouseAWB NoAirlineFlight No.POLPODETDETAAWB weightChargable Wt.B/L pcsShipperShipper addressConsigneeConsignee addressPMC(1)/AKE(2)/Loose(3)Consignment NumbersBag', implode('', $data[1])) === 0) {
											$error = self::importAirIncoConsol($data);
											if (!empty($error)) {
												Emailog::sendEmailTo($fromaddr, json_encode($error), "Pre alert import Error-".trim($parser->getHeader('subject')));
											}
										} else {
											Emailog::sendEmailTo($fromaddr, "Template wrong", "Pre alert import Error-".trim($parser->getHeader('subject')));
										}
									}
								} else {
									if (preg_match('/.xls/i', $attachment['filename'])) {
										Emailog::sendEmailTo($fromaddr, "The file extension must be .xlsx", "Pre alert import Error-".trim($parser->getHeader('subject')));
									}
								}
							}
							if (preg_match('/pre_alert_sea/i', $attachment['filename'])) {
								$isSeaImport = true;
								if (preg_match('/.tmp|.xlsx/i', $attachment['filename'])) {
									$savePath = $mypath . date('Y-m-dH-i-s') . $attachment['name'];
									if (file_put_contents($savePath, $attachment['attachment'])) {
										$xls = new oExcel;
										$xls->load($savePath);
										$data = $xls->getAll();
										if (strpos('WarehouseOcean BillVessel id(IMO)VoyagePOLPODETDETAAWB weightChargable Wt.B/L pcsShipperShipper addressConsigneeConsignee AddressCBM. (M³)House Bill.Container TypeCargo TypeContainer NoSealVessel NameLoad PortCarrierConsignment NumbersBag', implode('', $data[1])) === 0) {
											$error = self::importSeaIncoConsol($data);
											if (!empty($error)) {
												Emailog::sendEmailTo($fromaddr, json_encode($error), "Pre alert import Error-".trim($parser->getHeader('subject')));
											}
										} else {
											Emailog::sendEmailTo($fromaddr, "Template wrong", "Pre alert import Error-".trim($parser->getHeader('subject')));
										}
									}
								} else {
									if (preg_match('/.xls/i', $attachment['filename'])) {
										Emailog::sendEmailTo($fromaddr, "The file extension must be .xlsx", "Pre alert import Error-".trim($parser->getHeader('subject')));
									}
								}
							}
						}
					}
					if ($isAirImport == false && $isSeaImport == false) {
						Emailog::sendEmailTo($fromaddr, "No corrected file finded", "Pre alert import Error-".trim($parser->getHeader('subject')));
					}
				}
			} catch (Exception $ex) {
				continue;
			}
		}
		$this->finishInco();
	}

	public static function getAllattrachmentsInEmail($inbox, $email_number)
	{
		$structure = imap_fetchstructure($inbox, $email_number);
		if (isset($structure->parts) && count($structure->parts)) {
			for ($i = 0; $i < count($structure->parts); $i++) {
				if (isset($structure->parts[$i]->parts) && !empty($structure->parts[$i]->parts) && count($structure->parts[$i]->parts) > 0) {
					for ($y = 0; $y < count($structure->parts[$i]->parts); $y++) {
						$attachments[$y] = array(
							'is_attachment' => false,
							'filename' => '',
							'name' => '',
							'attachment' => ''
						);
						if ($structure->parts[$i]->parts[$y]->ifdparameters == 1) {
							foreach ($structure->parts[$i]->parts[$y]->dparameters as $object) {
								if (strtolower($object->attribute) == 'filename') {
									$attachments[$y]['is_attachment'] = true;
									$attachments[$y]['filename'] = $object->value;
								}
							}
						}

						if ($structure->parts[$i]->parts[$y]->ifparameters) {
							foreach ($structure->parts[$i]->parts[$y]->parameters as $object) {
								if (strtolower($object->attribute) == 'name') {
									$attachments[$y]['is_attachment'] = true;
									$attachments[$y]['name'] = $object->value;
								}
							}
						}

						if ($attachments[$y]['is_attachment']) {
							$attachments[$y]['attachment'] = imap_fetchbody($inbox, $email_number, ($i + 1) . '.' . ($y + 1));
							if ($structure->parts[$i]->parts[$y]->encoding == 3) {
								$attachments[$y]['attachment'] = base64_decode($attachments[$y]['attachment']);
							} elseif ($structure->parts[$i]->parts[$y]->encoding == 4) {
								$attachments[$y]['attachment'] = quoted_printable_decode($attachments[$y]['attachment']);
							}
						}
					}
				} else {
					$attachments[$i] = array(
						'is_attachment' => false,
						'filename' => '',
						'name' => '',
						'attachment' => ''
					);
					if ($structure->parts[$i]->ifdparameters == 1) {
						foreach ($structure->parts[$i]->dparameters as $object) {
							if (strtolower($object->attribute) == 'filename') {
								$attachments[$i]['is_attachment'] = true;
								$attachments[$i]['filename'] = $object->value;
							}
						}
					}

					if ($structure->parts[$i]->ifparameters) {
						foreach ($structure->parts[$i]->parameters as $object) {
							if (strtolower($object->attribute) == 'name') {
								$attachments[$i]['is_attachment'] = true;
								$attachments[$i]['name'] = $object->value;
							}
						}
					}

					if ($attachments[$i]['is_attachment']) {
						$attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);
						if ($structure->parts[$i]->encoding == 3) {
							$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
						} elseif ($structure->parts[$i]->encoding == 4) {
							$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
						}
					}
				}
			}
		}
		return $attachments;
	}



	public function fetchAttrachmentIncoConsol()
	{
		if (empty($this->_incoConsol)) return [];
		require_once(Yii::app()->basePath . '/vendor/autoload.php');
		//require_once(Yii::app()->basePath . '/vendor/php-mime-mail-parser/php-mime-mail-parser/vendor/autoload.php');
		$id = 0;
		foreach (imap_sort($this->_incoConsol, SORTARRIVAL, 0) as $mid) {
			if ($id++ > 50) break;
			try {
				$parser = new PhpMimeMailParser\Parser();
				$parser->addMiddleware(function ($mimePart, $next) {
					$part = $mimePart->getPart();
					foreach (['from', 'to', 'cc'] as $t) {
						if (isset($part['headers'][$t])) {
							$as = preg_split('/,\s*/', $part['headers'][$t]);
							$ads = [];
							foreach ($as as $a) {
								$ads[] = preg_replace('/(.*<[a-zA-Z0-9._%-]+@[a-zA-Z0-9.\-]+>).*/', "$1", $a);
							}
							$part['headers'][$t] = implode(',', $ads);
						}
					}

					//fix content ID
					if (isset($part['headers']['content-id'])) {
						$part['headers']['content-id'] = preg_replace('/^<+([^>]+)>+$/', '<$1>', $part['headers']['content-id']);
					}
					$mimePart->setPart($part);

					return $next($mimePart);
				});
				$body = imap_body($this->_incoConsol, $mid);
				$headers = imap_fetchheader($this->_incoConsol, $mid, FT_PREFETCHTEXT);
				if (empty($headers) || empty($body)) continue;
				$message = $headers . "\n" . $body;
				@$parser->setText($message);



				$attachments = $parser->getAttachments();
				$isAirImport = false;
				$isSeaImport = false;
				$result = 1;
				if ($result) {
					foreach ($attachments as $attachment) {
						if (preg_match('/imco_consol_import_template|pre_alert_air/i', $attachment->getFilename())) {
							$isAirImport = true;
							$savePath = $attachment->save(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'incoConsol' . DIRECTORY_SEPARATOR);
							if (!empty($savePath)) {
								if (preg_match('/.tmp|.xlsx/i', $savePath)) {
									$xls = new oExcel;
									$xls->load($savePath);
									$data = $xls->getAll();
									//if (implode('', $data[1]) == "WarehouseAWB NoAirlineFlight No.POLPODETDETAAWB weightChargable Wt.B/L pcsShipperShipper addressConsigneeConsignee addressPMC(1)/AKE(2)/Loose(3)Consignment Numbers"){
									if (strpos('WarehouseAWB NoAirlineFlight No.POLPODETDETAAWB weightChargable Wt.B/L pcsShipperShipper addressConsigneeConsignee addressPMC(1)/AKE(2)/Loose(3)Consignment NumbersBag', implode('', $data[1])) === 0) {
										$error = self::importAirIncoConsol($data);
										if (!empty($error)) {
											Emailog::sendEmailTo($parser->getAddresses('from')[0]['address'], json_encode($error), "Pre alert import Error-".trim($parser->getHeader('subject')));
										}else{
											$result = imap_mail_move($this->_incoConsol, $mid, 'INBOX.history');
										}
									} else {
										Emailog::sendEmailTo($parser->getAddresses('from')[0]['address'], "Template wrong", "Pre alert import Error-".trim($parser->getHeader('subject')));
										continue;
									}
								}
							}
						}
						if (preg_match('/pre_alert_sea/i', $attachment->getFilename())) {
							$isSeaImport = true;
							$savePath = $attachment->save(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'incoConsol' . DIRECTORY_SEPARATOR);
							if (!empty($savePath)) {
								if (preg_match('/.tmp|.xlsx/i', $savePath)) {
									$xls = new oExcel;
									$xls->load($savePath);
									$data = $xls->getAll();
									//if (implode('', $data[1]) == "WarehouseOcean BillVessel id(IMO)VoyagePOLPODETDETAAWB weightChargable Wt.B/L pcsShipperShipper addressConsigneeConsignee AddressCBM. (M³)House Bill.Container TypeCargo TypeContainer NoSealVessel NameLoad PortCarrierConsignment Numbers"){
									if (strpos('WarehouseOcean BillVessel id(IMO)VoyagePOLPODETDETAAWB weightChargable Wt.B/L pcsShipperShipper addressConsigneeConsignee AddressCBM. (M³)House Bill.Container TypeCargo TypeContainer NoSealVessel NameLoad PortCarrierConsignment NumbersBag', implode('', $data[1])) === 0) {
										$error = self::importSeaIncoConsol($data);
										if (!empty($error)) {
											Emailog::sendEmailTo($parser->getAddresses('from')[0]['address'], json_encode($error), "Pre alert import Error".trim($parser->getHeader('subject')));
										}else{
											$result = imap_mail_move($this->_incoConsol, $mid, 'INBOX.history');
										}
									} else {
										Emailog::sendEmailTo($parser->getAddresses('from')[0]['address'], "Template wrong", "Pre alert import Error".trim($parser->getHeader('subject')));
										continue;
									}
								}
							}
						}
					}
				}
			} catch (Exception $ex) {
				continue;
			}
		}
		//$this->finishInco();
	}

	public static function importAirIncoConsol($data)
	{
		unset($data[1]);
		$last_consolno = 0;

		foreach ($data as $i => $d) {
			if (empty($d[17])) {
				continue;
			}

			if ($last_consolno != 1) {
				if (empty($d[5])) {
					$err[] = "Line " . $i . " POL is empty!";
					continue;
				} else if (empty($d[6])) {
					$err[] = "Line " . $i . " POD is empty!";
					continue;
				} else if (empty($d[8])) {
					$err[] = "Line " . $i . " ETA Time is empty!";
					continue;
				}
				$last_consolno = 1;
				$depot = 0;
				if (!empty(trim($d[1]))) {
					if (!preg_match('/106|218|530|811|529/i', trim($d[1]))) {
						$depot = self::getDepotId(trim($d[1]));
					} else {
						$depot = trim($d[1]);
					}
				}

				$tasks[1] = array(
					'dpt_id' => $depot,
					'service' => 10, //10 air 20 sea
					'awb' => trim($d[2]),
					'airline' => trim($d[3]),
					'flight' => trim($d[4]),
					'pol' => trim($d[5]),
					'pod' => trim($d[6]),
					'etd' => oExcel::toDate($d[7]),
					'eta' => oExcel::toDate($d[8]),
					'ignore' => "",
					'mdata[awb_wt]' => trim($d[9]),
					'mdata[cgb_wt]' => trim($d[10]),
					'mdata[b&l_pcs]' => trim($d[11]),
					'mdata[cnor]' => trim($d[12]),
					'mdata[cnor_addr]' => trim($d[13]),
					'mdata[cnee]' => trim($d[14]),
					'mdata[cnee_addr]' => trim($d[15]),
					'mdata[air_type]' => trim($d[16]),
					'consignment' => []
				);
			}
			if (empty($d[17])) {
				$err[] = "line " . $i . " Consignment Numbers is empty!";
				continue;
			}

			$h = trim($d[17]);
			if (!empty($h)) {
				$ns = preg_split('/[\s\t,;]+/', trim($h));
				foreach ($ns as $nskey => $nsValue) {
					$ns[$nskey] = trim($nsValue);
					if(empty($ns[$nskey]))
					{
						unset($ns[$nskey]);
					}
				}
				$h = $ns;
			}

			//bagTag
			$bagTag = "";
			if (!empty(trim($d[18]))) {
				$bagTag = trim($d[18]);
			}

			$ps = ImParcel::model()->findAll("(hbn in ('".join("','",$h)."') OR ref in ('".join("','",$h)."') )AND (cbwf&:cbwf)=0", [ ':cbwf' => ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
			if (empty($ps)) {
				$err[] = "line " . $i . " Consignment Numbers " . trim($d[17]) . " not found!";
				continue;
			}

			if(!empty($ps))
			{
				foreach ($ps as $pkey => $p)
				{
					$tasks[1]['consignment'][] = array(
						'ref' => $p->hbn,
						'bagTag' => $bagTag, //bagtag
					);
				}
			}
		}
		if (!empty($err)) {
			return $err;
		}
		foreach ($tasks as $temp_task) {

			$model = new ImcoConsol;
			$errors = [];
			if (empty($temp_task)) {
				continue;
			} else {
				$selected_services = [];
				$ignore_depot = empty($temp_task['ignore']) ? 0 : 1;

				foreach ($temp_task['consignment'] as $temp_label) {
					$h = $temp_label['ref'];
					if (preg_match('/[\t ]+/', $temp_label['ref'])) {
						$ts = preg_split('/[\t ]+/', $temp_label['ref']);
						$h = trim($ts[0]);
					}
					$p = ImParcel::model()->find('(hbn = :h OR ref = :h) AND (cbwf&:cbwf)=0', [':h' => $h, ':cbwf' => ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
					$model->checkImparcel($p, $errors, $selected_services, $h, $_POST['ImcoConsol']['dpt_id'], $ignore_depot);
				}

				if (sizeof($selected_services) >= 1) {
					asort($selected_services);
					$selected_services = array_reverse($selected_services, true);
					$the_service = key($selected_services) - (key($selected_services) & 32);
				}
				foreach ($temp_task['consignment'] as $temp_label) {
					$h = $temp_label['ref'];
					if (preg_match('/[\t ]+/', $temp_label['ref'])) {
						$ts = preg_split('/[\t ]+/', $temp_label['ref']);
						$h = trim($ts[0]);
					}

					$p = ImParcel::model()->find('(hbn = :h OR ref = :h)', [':h' => $h]);
					$model->checkServiceErrors($p, $the_service, $errors, $h);
				}

				if (!empty($errors)) {
					return $errors;
				}

				$model->dpt_id = $temp_task['dpt_id'];
				$model->service = $temp_task['service'];
				$model->awb = $temp_task['awb'];
				$model->airline = $temp_task['airline'];
				$model->flight = $temp_task['flight'];
				$model->pol = $temp_task['pol'];
				$model->pod = $temp_task['pod'];
				$model->etd = $temp_task['etd'];
				$model->eta = $temp_task['eta'];
				$model->awb = $temp_task['awb'];
				$model->mdata['awb_wt'] = $temp_task['mdata[awb_wt]'];
				$model->mdata['cgb_wt'] = $temp_task['mdata[cgb_wt]'];
				$model->mdata['b&l_pcs'] = $temp_task['mdata[b&l_pcs]'];
				$model->mdata['cnor'] = $temp_task['mdata[cnor]'];
				$model->mdata['cnor_addr'] = $temp_task['mdata[cnor_addr]'];
				$model->mdata['cnee'] = $temp_task['mdata[cnee]'];
				$model->mdata['cnee_addr'] = $temp_task['mdata[cnee_addr]'];
				$model->mdata['air_type'] = $temp_task['mdata[air_type]'];
				$model->mdata['prealert'] = 1;
				$model->status = 10;
				if ($model->save()) {
					$transaction = Yii::app()->db->beginTransaction();
					try {
						foreach ($temp_task['consignment'] as $temp_label) {
							$h = $temp_label['ref'];
							if (!empty($temp_label['ref'])) {
								$ts = preg_split('/[\t ]+/', $temp_label['ref']);
								$h = trim($ts[0]);
								$wt = trim($ts[1]);
							}
							$p = ImParcel::model()->find('(hbn = :h OR ref = :h) AND cbwf&:cbwf=0', [':h' => $h, ':cbwf' => ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
							if(!empty($model->id))
							{
								$model->putImparcelIntoConsol($p, @$wt);
							}

							if (!empty($temp_label['bagTag'])) {
								$bag = $temp_label['bagTag'];
								if (!empty($bag) && strlen($bag) <= 45) {
									$objBag = ImportBag::model()->find('bag_tag = :bag_tag', [':bag_tag' => $bag]);
									if (empty($objBag)) {
										$objBag = new ImportBag();
										$objBag->user_id = 1;
										$objBag->bag_tag = $bag;
										$objBag->created = date("Y-m-d H:i:s");
										$objBag->save();
									}

									//bagtag
									$bagTag = ImportBagTag::model()->find('shipment_id = :shipment_id', [':shipment_id' => $p->id]);
									if (empty($bagTag)) {
										$bagTag = new ImportBagTag();
									}
									$bagTag->bag_tag = $bag;
									$bagTag->import_bag_id = $objBag->id;
									$bagTag->shipment_id = $p->id;
									$bagTag->save();
								}
							}
						}
						$transaction->commit();
					} catch (Exception $ex) {
						$transaction->rollback();
						return $ex->getMessage();
					}
				} else {
					return "Consol not create successful,Please check";
				}
			}
		}
	}

	public static function importSeaIncoConsol($data)
	{
		unset($data[1]);
		$last_consolno = 0;

		foreach ($data as $i => $d) {
			if (empty($d[25])) {
				continue;
			}

			if ($last_consolno != 1) {
				if (empty($d[5])) {
					$err[] = "Line " . $i . " POL is empty!";
					continue;
				} else if (empty($d[6])) {
					$err[] = "Line " . $i . " POD is empty!";
					continue;
				} else if (empty($d[8])) {
					$err[] = "Line " . $i . " ETA Time is empty!";
					continue;
				} else if (empty($d[18])) {
					$err[] = "Line " . $i . " Container Type is empty!";
					continue;
				} else if (empty($d[20])) {
					$err[] = "Line " . $i . " Container No is empty!";
					continue;
				}
				$last_consolno = 1;
				$depot = 0;
				if (!empty(trim($d[1]))) {
					if (!preg_match('/106|218|530|811|529/i', trim($d[1]))) {
						$depot = self::getDepotId(trim($d[1]));
					} else {
						$depot = trim($d[1]);
					}
				}
				$seaType = 0;
				if (!empty(trim($d[18]))) {
					$seaType = self::getContainerTypeKey($d[18]);
				}

				$tasks[1] = array(
					'dpt_id' => $depot,
					'service' => 20, //10 air 20 sea
					'awb' => trim($d[2]),
					'airline' => trim($d[3]),
					'flight' => trim($d[4]),
					'pol' => trim($d[5]),
					'pod' => trim($d[6]),
					'etd' => oExcel::toDate($d[7]),
					'eta' => oExcel::toDate($d[8]),
					'ignore' => "",
					'mdata[awb_wt]' => trim($d[9]),
					'mdata[cgb_wt]' => trim($d[10]),
					'mdata[b&l_pcs]' => trim($d[11]),
					'mdata[cnor]' => trim($d[12]),
					'mdata[cnor_addr]' => trim($d[13]),
					'mdata[cnee]' => trim($d[14]),
					'mdata[cnee_addr]' => trim($d[15]),
					'mdata[cbm]' => trim($d[16]),
					'mdata[house_bill]' => trim($d[17]),
					'mdata[sea_type]' => $seaType,
					'mdata[cargo_type]' => 'LCL', //trim($d[19])
					'mdata[container_no]' => trim($d[20]),
					'mdata[sea_seal]' => trim($d[21]),
					'mdata[sea_vessel]' => trim($d[22]),
					'mdata[sea_load_port]' => trim($d[23]),
					'mdata[sea_carrier]' => trim($d[24]),
					'consignment' => []
				);
			}
			if (empty($d[25])) {
				$err[] = "line " . $i . " Consignment Numbers is empty!";
				continue;
			}

			$h = trim($d[25]);
			if (preg_match('/[\t ]+/', trim($d[25]))) {
				$ts = preg_split('/[\t ]+/', trim($d[25]));
				$h = trim($ts[0]);
			}

			//bagTag
			$bagTag = "";
			if (!empty(trim($d[26]))) {
				$bagTag = trim($d[26]);
			}

			$p = ImParcel::model()->find('(hbn = :h OR ref = :h) AND (cbwf&:cbwf)=0', [':h' => $h, ':cbwf' => ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
			if (empty($p)) {
				$err[] = "line " . $i . " Consignment Numbers " . trim($d[25]) . " not found!";
				continue;
			}
			$tasks[1]['consignment'][] = array(
				'ref' => $h,
				'bagTag' => $bagTag, //bagtag
			);
		}
		if (!empty($err)) {
			return $err;
		}
		foreach ($tasks as $temp_task) {

			$model = new ImcoConsol;
			$errors = [];
			if (empty($temp_task)) {
				continue;
			} else {
				$selected_services = [];
				$ignore_depot = empty($temp_task['ignore']) ? 0 : 1;

				foreach ($temp_task['consignment'] as $temp_label) {
					$h = $temp_label['ref'];
					if (preg_match('/[\t ]+/', $temp_label['ref'])) {
						$ts = preg_split('/[\t ]+/', $temp_label['ref']);
						$h = trim($ts[0]);
					}
					$p = ImParcel::model()->find('(hbn = :h OR ref = :h) AND (cbwf&:cbwf)=0', [':h' => $h, ':cbwf' => ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
					$model->checkImparcel($p, $errors, $selected_services, $h, $_POST['ImcoConsol']['dpt_id'], $ignore_depot);
				}

				if (sizeof($selected_services) >= 1) {
					asort($selected_services);
					$selected_services = array_reverse($selected_services, true);
					$the_service = key($selected_services) - (key($selected_services) & 32);
				}
				foreach ($temp_task['consignment'] as $temp_label) {
					$h = $temp_label['ref'];
					if (preg_match('/[\t ]+/', $temp_label['ref'])) {
						$ts = preg_split('/[\t ]+/', $temp_label['ref']);
						$h = trim($ts[0]);
					}

					$p = ImParcel::model()->find('(hbn = :h OR ref = :h)', [':h' => $h]);
					$model->checkServiceErrors($p, $the_service, $errors, $h);
				}

				if (!empty($errors)) {
					return $errors;
				}

				$model->dpt_id = $temp_task['dpt_id'];
				$model->service = $temp_task['service'];
				$model->awb = $temp_task['awb'];
				$model->airline = $temp_task['airline'];
				$model->flight = $temp_task['flight'];
				$model->pol = $temp_task['pol'];
				$model->pod = $temp_task['pod'];
				$model->etd = $temp_task['etd'];
				$model->eta = $temp_task['eta'];
				$model->awb = $temp_task['awb'];
				$model->mdata['awb_wt'] = $temp_task['mdata[awb_wt]'];
				$model->mdata['cgb_wt'] = $temp_task['mdata[cgb_wt]'];
				$model->mdata['b&l_pcs'] = $temp_task['mdata[b&l_pcs]'];
				$model->mdata['cnor'] = $temp_task['mdata[cnor]'];
				$model->mdata['cnor_addr'] = $temp_task['mdata[cnor_addr]'];
				$model->mdata['cnee'] = $temp_task['mdata[cnee]'];
				$model->mdata['cnee_addr'] = $temp_task['mdata[cnee_addr]'];
				$model->mdata['cbm'] = $temp_task['mdata[cbm]'];
				$model->mdata['house_bill'] = $temp_task['mdata[house_bill]'];
				$model->mdata['sea_type'] = $temp_task['mdata[sea_type]'];
				$model->mdata['cargo_type'] = $temp_task['mdata[cargo_type]'];
				$model->mdata['container_no'] = $temp_task['mdata[container_no]'];
				$model->mdata['sea_seal'] = $temp_task['mdata[sea_seal]'];
				$model->mdata['sea_vessel'] = $temp_task['mdata[sea_vessel]'];
				$model->mdata['sea_load_port'] = $temp_task['mdata[sea_load_port]'];
				$model->mdata['sea_carrier'] = $temp_task['mdata[sea_carrier]'];
				$model->status = 10;
				if ($model->save()) {
					$transaction = Yii::app()->db->beginTransaction();
					try {
						foreach ($temp_task['consignment'] as $temp_label) {
							$h = $temp_label['ref'];
							if (!empty($temp_label['ref'])) {
								$ts = preg_split('/[\t ]+/', $temp_label['ref']);
								$h = trim($ts[0]);
								$wt = trim($ts[1]);
							}
							$p = ImParcel::model()->find('(hbn = :h OR ref = :h) AND cbwf&:cbwf=0', [':h' => $h, ':cbwf' => ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
							if(!empty($model->id))
							{
								$model->putImparcelIntoConsol($p, @$wt);
							}

							if (!empty($temp_label['bagTag'])) {
								$bag = $temp_label['bagTag'];
								if (!empty($bag) && strlen($bag) <= 45) {
									$objBag = ImportBag::model()->find('bag_tag = :bag_tag', [':bag_tag' => $bag]);
									if (empty($objBag)) {
										$objBag = new ImportBag();
										$objBag->user_id = 1;
										$objBag->bag_tag = $bag;
										$objBag->created = date("Y-m-d H:i:s");
										$objBag->save();
									}

									//bagtag
									$bagTag = ImportBagTag::model()->find('shipment_id = :shipment_id', [':shipment_id' => $p->id]);
									if (empty($bagTag)) {
										$bagTag = new ImportBagTag();
									}
									$bagTag->bag_tag = $bag;
									$bagTag->import_bag_id = $objBag->id;
									$bagTag->shipment_id = $p->id;
									$bagTag->save();
								}
							}
						}
						$transaction->commit();
					} catch (Exception $ex) {
						$transaction->rollback();
						return $ex->getMessage();
					}
				} else {
					return "Consol not create successful,Please check";
				}
			}
		}
	}

	public static function getDepotId($code)
	{
		switch ($code) {
			case 'AUSYD':
				return 106;
			case 'AUMEL':
				return 218;
			case 'AUBNE':
				return 530;
			case 'AUPER':
				return 811;
			case 'AUADL':
				return 529;
		}
	}

	public static function getContainerTypeKey($value)
	{
		switch ($value) {
			case '20GP':
				return 1;
			case '40GP':
				return 2;
			case '40HQ':
				return 3;
			case '40HC':
				return 4;
		}
	}

	public function fetchAttrachmentManifest()
	{
		if (empty($this->_manifest)) return [];
		require_once(Yii::app()->basePath . '/vendor/autoload.php');
		//require_once(Yii::app()->basePath . '/vendor/php-mime-mail-parser/php-mime-mail-parser/vendor/autoload.php');
		$id = 0;
		foreach (imap_sort($this->_manifest, SORTARRIVAL, 0) as $mid) {
			if ($id++ > 50) break;
			try {
				$parser = new PhpMimeMailParser\Parser();
				$parser->addMiddleware(function ($mimePart, $next) {
					$part = $mimePart->getPart();
					foreach (['from', 'to', 'cc'] as $t) {
						if (isset($part['headers'][$t])) {
							$as = preg_split('/,\s*/', $part['headers'][$t]);
							$ads = [];
							foreach ($as as $a) {
								$ads[] = preg_replace('/(.*<[a-zA-Z0-9._%-]+@[a-zA-Z0-9.\-]+>).*/', "$1", $a);
							}
							$part['headers'][$t] = implode(',', $ads);
						}
					}

					//fix content ID
					if (isset($part['headers']['content-id'])) {
						$part['headers']['content-id'] = preg_replace('/^<+([^>]+)>+$/', '<$1>', $part['headers']['content-id']);
					}
					$mimePart->setPart($part);

					return $next($mimePart);
				});
				$body = imap_body($this->_manifest, $mid);
				$headers = imap_fetchheader($this->_manifest, $mid, FT_PREFETCHTEXT);
				if (empty($headers) || empty($body)) continue;
				$message = $headers . "\n" . $body;
				@$parser->setText($message);

				//sender
				$emailAddressFrom = $parser->getAddresses('from')[0]['address'];
				if (!empty($emailAddressFrom)) {
					$orgInfo = self::getManifestCustomerInfo($emailAddressFrom);
					if ($orgInfo != 0) {
						$attachments = $parser->getAttachments();
						$isImport = false;
						$tempfile = [];
						$result = imap_mail_move($this->_manifest, $mid, 'INBOX.history');
						if ($result) {
							foreach ($attachments as $attachment) {
								if (preg_match('/Automation Template-no-chargecode/i', $attachment->getFilename())) {
									$isImport = true;
									$tempfile['name'] = $attachment->getFilename();
									$savePath = $attachment->save(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'incoConsol' . DIRECTORY_SEPARATOR);
									if (!empty($savePath)) {
										if (preg_match('/.tmp|.xlsx/i', $savePath)) {
											$tempfile['tmp_name'] = $savePath;
											$error = self::importManifest($tempfile, $orgInfo);
											if (!empty($error)) {
												Emailog::sendEmailTo($parser->getAddresses('from')[0]['address'], json_encode($error), "Manifest Import Error");
											}
										}
									}
								}
							}
						}
					} else {
						$result = imap_mail_move($this->_manifest, $mid, 'INBOX.history');
						Emailog::sendEmailTo($parser->getAddresses('from')[0]['address'], "You do not have import permissions. Please contact the relevant staff", "Manifest Import Error");
					}
				}
			} catch (Exception $ex) {
				continue;
			}
		}
		$this->finishManifest();
	}

	public static function getManifestCustomerInfo($email)
	{
		$ext = explode('@', $email);
		if (!empty($ext)) {
			switch ($ext[1]) {
					// [orgid,currency]
				case '4px.com';
					return [2619, 1];
				case 'gotoubi.com';
					return [3025, 1];
				case 'sfmail.sf-express.com';
					return [3333, 1];
			}
		}
		return 0;
	}

	public static function importManifest($file, $orgInfo)
	{
		$trans = Yii::app()->db->beginTransaction();
		try {
			$model = new Manifest('upload');
			$model->fwd_id = $orgInfo[0];
			$model->type = 20;
			$model->dpt_id = 106;
			$model->imCover = false;
			$model->bagTagIdentify = false;
			$model->mdata['edi_currency'] = $orgInfo[1];
			$model->mdata['exchange_rate'] = empty(Currency::getExrate()[0]) ? 0.750 : Currency::getExrate()[0];
			$model->file = $file;
			$model->save();
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			return $ex->getMessage();
		}
	}

	public function fetchMsgFor($fwdfrom = false)
	{
		if (empty($this->_pop)) return [];
		require_once(Yii::app()->basePath . '/vendor/autoload.php');
		//require_once (Yii::app()->basePath.'/vendor/php-mime-mail-parser/php-mime-mail-parser/vendor/autoload.php');
		$id = 0;
		foreach (imap_sort($this->_pop, SORTARRIVAL, 0) as $mid) {
			if ($id++ > 50) break;
			try {
				$parser = new PhpMimeMailParser\Parser();
				$parser->addMiddleware(function ($mimePart, $next) {
					$part = $mimePart->getPart();
					foreach (['from', 'to', 'cc'] as $t) {
						if (isset($part['headers'][$t])) {
							$as = preg_split('/,\s*/', $part['headers'][$t]);
							$ads = [];
							foreach ($as as $a) {
								$ads[] = preg_replace('/(.*<[a-zA-Z0-9._%-]+@[a-zA-Z0-9.\-]+>).*/', "$1", $a);
							}
							$part['headers'][$t] = implode(',', $ads);
						}
					}

					//fix content ID
					if (isset($part['headers']['content-id'])) {
						$part['headers']['content-id'] = preg_replace('/^<+([^>]+)>+$/', '<$1>', $part['headers']['content-id']);
					}
					$mimePart->setPart($part);

					return $next($mimePart);
				});
				$body = imap_body($this->_pop, $mid);
				$headers = imap_fetchheader($this->_pop, $mid, FT_PREFETCHTEXT);
				if (empty($headers) || empty($body)) continue;
				$message = $headers . "\n" . $body;
				@$parser->setText($message);
				$result = ImportsMail::createTicket($parser, $message);
				if (!empty($result)) {
					imap_mail_move($this->_pop, $mid, 'INBOX.history');
				}
			} catch (Exception $ex) {
				if (json_encode($ex->getMessage()) != '{"from_name":["From Name cannot be blank."]}'&&!preg_match("/Bad message number/i",json_encode($ex->getMessage()))) {
					Emailog::sendEmailTo(Emailog::NOTIFY_OP_EMAIL, json_encode($ex->getMessage()), "Imports Mail Error");
				}
				continue;
			}
		}
		$this->finish();
	}

	public function fetchMsgFor3PL($fwdfrom = false)
	{
		if (empty($this->_3plpop)) return [];
		require_once(Yii::app()->basePath . '/vendor/autoload.php');
		$id = 0;
		foreach (imap_sort($this->_3plpop, SORTARRIVAL, 0) as $mid) {
			if ($id++ > 50) break;
			try {
				$parser = new PhpMimeMailParser\Parser();
				$parser->addMiddleware(function ($mimePart, $next) {
					$part = $mimePart->getPart();
					foreach (['from', 'to', 'cc'] as $t) {
						if (isset($part['headers'][$t])) {
							$as = preg_split('/,\s*/', $part['headers'][$t]);
							$ads = [];
							foreach ($as as $a) {
								$ads[] = preg_replace('/(.*<[a-zA-Z0-9._%-]+@[a-zA-Z0-9.\-]+>).*/', "$1", $a);
							}
							$part['headers'][$t] = implode(',', $ads);
						}
					}

					//fix content ID
					if (isset($part['headers']['content-id'])) {
						$part['headers']['content-id'] = preg_replace('/^<+([^>]+)>+$/', '<$1>', $part['headers']['content-id']);
					}
					$mimePart->setPart($part);

					return $next($mimePart);
				});
				$body = imap_body($this->_3plpop, $mid);
				$headers = imap_fetchheader($this->_3plpop, $mid, FT_PREFETCHTEXT);
				if (empty($headers) || empty($body)) continue;
				$message = $headers . "\n" . $body;
				@$parser->setText($message);
				$result = ImportsMail::createTicket($parser, $message, 1);
				if (!empty($result)) {
					imap_mail_move($this->_3plpop, $mid, 'INBOX.history');
				}
			} catch (Exception $ex) {
				if (json_encode($ex->getMessage()) != '{"from_name":["From Name cannot be blank."]}'&&!preg_match("/Bad message number/i",json_encode($ex->getMessage()))) {
					Emailog::sendEmailTo(Emailog::NOTIFY_OP_EMAIL, json_encode($ex->getMessage()), "Imports Mail Error");
				}
				continue;
			}
		}
		$this->finish3pl();
	}

	public function searchUid($model, $popHistory)
	{
		$criteria = '';
		if (!empty($model->mdata['search']['message_id'])) {
			$criteria .= 'TEXT "' . $model->mdata['search']['message_id'] . '" ';
		}
		if (!empty($model->mdata['search']['subject'])) {
			$criteria .= 'SUBJECT "' . $model->mdata['search']['subject'] . '" ';
		}
		if (!empty($model->mdata['search']['subject'])) {
			$criteria .= 'FROM "' . $model->mdata['search']['from'] . '"';
		}
		$result = imap_search($popHistory, $criteria, SE_UID);
		if (!empty($result)) {
			return max($result);
		} else {
			return 0;
		}
	}

	public function updateUid(&$popHistory)
	{
		$rs = ImportsMail::model()->findAll('flag=0');
		if (!empty($rs)) {
			$popHistory = imap_open("{" . self::SERVER . ":143/imap/notls/novalidate-cert}INBOX.history", "3pl.auto@toplogistics.com.au", "3g%c2a#76ejiQu22") or die("Connection Error: " . imap_last_error() . "<br/>");
			foreach ($rs as $r) {
				$uid = $this->searchUid($r, $popHistory);
				if (!empty($uid)) {
					$r->uid = $uid;
					$r->flag = 1;
					$r->update(['uid', 'flag']);
				} else {
					$r->flag = 2;
					$r->update(['flag']);
				}
			}
			imap_close($popHistory);
		}
	}

	public  function clearHistoryMailBox()
	{
		foreach (imap_sort($this->_pop_history, SORTARRIVAL, 0) as $mid) {
			imap_delete($this->_pop_history, $mid);
		}
		imap_expunge($this->_pop_history);
		imap_close($this->_pop_history);
		if (empty($this->_pop)) imap_close($this->_pop);

		foreach (imap_sort($this->_3plpop_history, SORTARRIVAL, 0) as $mid) {
			imap_delete($this->_3plpop_history, $mid);
		}
		imap_expunge($this->_3plpop_history);
		imap_close($this->_3plpop_history);
		if (empty($this->_3plpop)) imap_close($this->_3plpop);
	}

	//Delete used mails and end session
	public function finish()
	{
		if (empty($this->_pop)) return false;
		imap_expunge($this->_pop);
		imap_close($this->_pop);
		$this->updateUid($this->_pop_history);
		//      imap_close($this->_pop_history);
	}

	public function finish3pl()
	{
		if (empty($this->_3plpop)) return false;
		imap_expunge($this->_3plpop);
		imap_close($this->_3plpop);
		$this->updateUid($this->_3plpop_history);
		//      imap_close($this->_pop_history);
	}

	public function finishInco()
	{
		if (empty($this->_incoConsol)) return false;
		imap_expunge($this->_incoConsol);
		imap_close($this->_incoConsol);
		$this->updateUid($this->_incoConsol_history);
	}

	public function finishHunterTracking()
	{
		if (empty($this->_huntertracking)) return false;
		imap_expunge($this->_huntertracking);
		imap_close($this->_huntertracking);
		$this->updateUid($this->_huntertracking_history);
	}

	public function finishManifest()
	{
		if (empty($this->_manifest)) return false;
		imap_expunge($this->_manifest);
		imap_close($this->_manifest);
		$this->updateUid($this->_manifest_history);
	}
}
