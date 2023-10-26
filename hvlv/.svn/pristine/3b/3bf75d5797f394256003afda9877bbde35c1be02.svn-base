<?php
class TonyAPI
{

	// only elekzon now, probably need table to store more api info
	//public $store_domain = 'https://tyky.hn-enjoy.com';
	public $store_domain = 'https://app.teexpress.com.au';
	//public $store_domain = 'https://www.test.com';
	public $api_key = 'c8b70c46d613ccd101254e87032fd186';
	public $api_secret = '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';
	public $org_id = 1;

	// test api info
	// public $store_domain = 'https://pcaexpress.myshopify.com';
	// public $api_key = 'ded91eb1d28b189c5011c8375a9a3367';
	// public $api_secret = '4ed59305708f484a5eb7a8d575f2cf05';
	// public $org_id = 114;

	public $api_version = '/api/v1/615eba8ea1455';
	public $api_get = '/api/v1/612d907c1c2a5';

	public function __construct($config = array())
	{
		if (!empty($config['store_domain'])) {
			$this->store_domain = $config['store_domain'];
		}

		if (!empty($config['api_key'])) {
			$this->api_key = $config['api_key'];
		}

		if (!empty($config['api_secret'])) {
			$this->api_secret = $config['api_secret'];
		}

		if (!empty($config['org_id'])) {
			$this->org_id = $config['org_id'];
		}
	}

	public function getRequestUrl($endpoint, $params = array())
	{
		if ($params) {
			$endpoint .= '?';
			foreach ($params as $k => $v) {
				$endpoint .= $k . '=' . $v . '&';
			}
		}

		//$store_url = explode('//', $this->store_domain)[0] . '//' . $this->api_key . ':' . $this->api_secret . '@' . explode('//', $this->store_domain)[1];
		$store_url = $this->store_domain;
		return $store_url . $endpoint;
	}

	public function httpPost($endpoint, $params = array())
	{
		sleep(1);
		// Set the curl parameters
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl($endpoint));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$header = array();
		$header[] = 'Content-Type:application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$httpResponse = curl_exec($ch);
		curl_close($ch);

		if (!$httpResponse) {
			return ['done' => false];
		}

		// Extract the response details
		$httpParsedResponseAr = json_decode($httpResponse, true);

		if (sizeof($httpParsedResponseAr) == 0) {
			return ['done' => false];
		}

		//$this->log2file(json_encode($httpParsedResponseAr));
		return array_merge($httpParsedResponseAr, ['done' => true]);
	}

	public function httpGet($endpoint, $params = array())
	{
		sleep(1);
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
		$httpParsedResponseAr = json_decode($httpResponse, true);

		if (sizeof($httpParsedResponseAr) == 0) {
			return ['done' => false];
		}

		$this->log2file(json_encode($httpParsedResponseAr));
		return array_merge($httpParsedResponseAr, ['done' => true]);
	}

	public function getOrder($hbn)
	{
		$endpoint = $this->api_get;
		$params['order_no'] = $hbn;
		return $this->httpPost($endpoint, $params);
	}

	public function fulfillmentCargoProcess($hbn)
	{
		try {
			$job = ImParcel::model()->find('hbn =:hbn and status<90', [':hbn' => $hbn])->cargo_process;
			if (!empty($job)) {
				$arrOrder = $this->getOrder($hbn);
				if ($arrOrder['code'] == 1) {
					$customerName = $arrOrder['data'][0]['sign_user_name'];
					$img = $arrOrder['data'][0]['sign_user_img'];
					$pdf_path = $arrOrder['data'][0]['pdf_path'];
					if (!empty($img)) {
						$base64_img = self::imgtobase64($img);
						if (!empty($base64_img)) {
							//$job = CargoProcess::model()->findByPk(34);
							$cargos = $job->getCargos();
							foreach ($cargos as $key => $cargo) {
								$cargo->mdata['sig'] =  str_replace("\r\n", "", $base64_img);
								$cargo->mdata['sprint'] = $customerName;
								$cargo->mdata['sdate'] = date('Y-m-d');
								$cargo->mdata['driverDeliveryStatus'] = CargoProcess::DELIVERIED;
								$cargo->mdata['tonyPdf_Path'] = $pdf_path;
								$cargo->save();

								$cargo->log("cneeSignFBAFile");
								$shipmentModel = $cargo->shipment;
								if (!empty($cargo->mdata['caref'])) {
									$shipmentModel->ref = $cargo->mdata['caref'];
									$shipmentModel->weight = round(($cargo->shipment->weight * $cargo->mdata['left_packages']) / $cargo->shipment->pkg, 2);
									$shipmentModel->pkg = $cargo->mdata['left_packages'];
									$shipmentModel->mdata['total_cbm'] = $shipmentModel->cbm * $cargo->mdata['left_packages'];
								}

								$filename = $shipmentModel->id . "pod";
								$image = ['sprint' => $cargo->mdata['sprint'], 'sig' => $cargo->mdata['sig'], 'sdate' => $cargo->mdata['sdate']];
								$pdfPath = "";
								/* combine the signature file to the template to be pdfs*/
								if ($cargo->type == CargoProcess::FBA_CARGO || $cargo->type == CargoProcess::B2B_CARGO) {
									$pdfPath = oPDF::renderPDF('cargo_receipt', ['model' => $shipmentModel, 'image' => $image], 2, $filename . '.pdf');
								} else {
									$pdfPath = oPDF::renderPDF('cargo_receipt_1', ['model' => $shipmentModel, 'image' => $image], 2, $filename . '.pdf');
								}

								/*upload pdfs to every shipment*/
								$uploadType = 25;
								$pphash = FileRepo::uploadHash($shipmentModel, $uploadType);
								$filesize = filesize($pdfPath);
								$date = date('Y-m-d H:i:s');
								$filename = $filename . ".pdf";
								$name = "pod.pdf";
								$fileHash = hash_file('crc32b', $pdfPath) . hash('crc32b', $filesize);
								$finfo = finfo_open(FILEINFO_MIME_TYPE);
								$mime = finfo_file($finfo, $pdfPath);
								CargoProcess::updateUploadSingleFile($filesize, $date, $fileHash, $finfo, $mime, $pdfPath, $name, $pphash, true, false, $cargo->id);
								$cargo->processDone();
							}
						}
					}
				}
			}
		} catch (Exception $ex) {
			return false;
		}
	}

	public function imgtobase64($img = '', $imgHtmlCode = true)
	{
		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false,
			),
		);
		$base64 = "";
		//$imageInfo = getimagesize($img);
		//$base64 = 'data:image/png;base64,' . chunk_split(base64_encode(file_get_contents($img, false, stream_context_create($arrContextOptions))));
		try {
			$base64 = 'data:image/png;base64,' . @chunk_split(base64_encode(file_get_contents($img, false, stream_context_create($arrContextOptions))));
			//return 'data:image/png;base64,' . chunk_split(base64_encode(file_get_contents($img, false, stream_context_create($arrContextOptions))));
		} catch (Exception $e) {
			$base64 = "";
		}
		//echo $base64;
		return $base64;
		//return 'data:' . $imageInfo['mime'] . ';base64,' . chunk_split(base64_encode(file_get_contents($img)));
	}

	public function postOrder($cargoProcessJobId)
	{
		//$endpoint = '/admin' . $this->api_version . '/orders/' . $id . '/fulfillments.json';
		$endpoint = $this->api_version;
		$modelCargoProcessJob = CargoProcessJob::model()->findByPk($cargoProcessJobId);
		$modelCargoProcessRelations = $modelCargoProcessJob->job_relations;
		if ($modelCargoProcessJob->dpt_id == 106) {
			$strDepotPhone = "0290668200";
			$strArea = "Bankstown Aerodrome";
			$strAddress = "1/233 Milperra Rd";
			$strPostcode = "2200";
			$strEmail = "cartage@toplogistics.com.au";
			$customerId = "54";
		} elseif ($modelCargoProcessJob->dpt_id == 218) {
			$strDepotPhone = "";
			$strArea = "Sunshine";
			$strAddress = "3b/8 Judge St";
			$strPostcode = "3020";
			$strEmail = "cartage@vic.toplogistics.com.au";
			$customerId = "17";
		}

		$params = [];
		$order = [];
		if (!empty($modelCargoProcessRelations)) {
			foreach ($modelCargoProcessRelations as $modelCargoProcessRelation) {
				$modelCargoProcess = $modelCargoProcessRelation->cargo_process;

				if (!empty(json_decode($modelCargoProcess->shipment->items))) {
					$goodName = "";//implode(",", json_decode($modelCargoProcess->shipment->items)->g);
				} else {
					$goodName = "";
				}

				$numCBM = $modelCargoProcess->getTotalCBM();

				//for dim
				$measurement = "";
				$strmeasure = "";
				if(!empty($modelCargoProcess->shipment->packages)){
					$objmeasurement = json_decode($modelCargoProcess->shipment->packages);
					if(!empty($objmeasurement)){
						foreach( $objmeasurement as $key => $item)
						{
							$width = $item->width / 100;
							$length = $item->length / 100;
							$height = $item->height / 100;
							$strmeasure = $width."*".$length."*".$height;
							$measurement .= $strmeasure.";";
						}
					}
				}
				
				$order[] = array(
					"order_no" => $modelCargoProcess->shipment->hbn,
					"goods_name" => $goodName,
					"goods_weight" => $modelCargoProcess->getWeight(),
					"goods_num" => $modelCargoProcess->getPackages(),
					"user_name" => "TLA",
					"from_mobile" => $strDepotPhone,
					"from_area" => $strArea,
					"from_address" => $strAddress,
					"from_post_code" => $strPostcode,
					"get_code" => "",
					"send_code" => "",
					"to_user_name" => $modelCargoProcess->shipment->cnee->name,
					"to_mobile" => $modelCargoProcess->shipment->cnee->tel,
					"to_area" => $modelCargoProcess->shipment->cnee->suburb,
					"to_address" => $modelCargoProcess->shipment->cnee->address,
					"to_post_code" => $modelCargoProcess->shipment->cnee->postcode,
					"customer_id" => $customerId,
					"email" => $strEmail,
					"bar_code" => $this->genBarcode($modelCargoProcess->shipment->ref, $modelCargoProcess->getPackages()),
					"volume" => $numCBM,
					"measurement" =>$measurement,
				);
			}

			$params['order'] = $order;

			return $this->httpPost($endpoint, $params);
		} else {
			return false;
		}
	}

	public function genBarcode($ref, $pkgs)
	{
		$result = "";

		if ($pkgs == 1) {
			$result = $ref;
		} else {
			for ($i = 1; $i <= $pkgs; $i++) {
				if ($i != $pkgs) {
					$result .= $ref . "-" . $i . ",";
				} else {
					$result .= $ref . "-" . $i;
				}
			}
		}
		return $result;
	}

	protected function log2file($m)
	{
		$lf = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR  . 'tony' . DIRECTORY_SEPARATOR;
		$lf .= 'tony_' . date('Y-m-d H') . '.log';
		return file_put_contents($lf, date('Y-m-d H:i:s') . ' ' . $m . "\n", FILE_APPEND);
	}
}
