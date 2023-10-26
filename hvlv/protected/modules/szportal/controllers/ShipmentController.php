<?php

class ShipmentController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax = ['list','printBagLabel'];
	protected $skipAcl = ['connote','print'];

	protected $org;
	protected $type;

	public function beforeAction($action)
	{
		if (!Yii::app()->user->isGuest) {
			$this->org = Org::model()->findByPk(Yii::app()->user->org);
			$this->type = 'sc';
		}
		return parent::beforeAction($action);
	}
	
	public function actionCheckin()
	{
		$model = ConsolScan::model()->findAll('status = 0');
		$this->render('list', ['consoles' => $model]);
	}

	/**
	 * show all awb related console scan statistic information
	 */
	public function actionProfile()
	{

	}

	/**
	 * process scan check in
	 */
	public function actionScan()
	{
		// $model = Consol::model()->findByPk();

		//update weight
		if(isset($_POST['shipment']))
		{
			$p = Shipment::model()->findByPk($_POST['shipment']['id']);
			$p->wtck = $_POST['shipment']['wtck'];
			if(!empty($p->wtck))
			{
				$p->cln[] = $p->wtck." by weighing";
				$p->weight = $p->wtck;
			}

			$p->mdata['pkgtck'] = @$_POST['shipment']['pkgtck'];
			$p->mdata['dimtck']['w'] = @$_POST['dimtck']['w'];
			$p->mdata['dimtck']['h'] = @$_POST['dimtck']['h'];
			$p->mdata['dimtck']['d'] = @$_POST['dimtck']['d'];
			$p->mdata['total_cbmtck'] = round((@$_POST['dimtck']['w']*@$_POST['dimtck']['h']*@$_POST['dimtck']['d']))/1000;
			if($p->save())
			{
				echo json_encode(["msg"=>"Success"]);
			}else
			{
				echo json_encode(["msg"=>"Update Faild"]);
			}
			Yii::app()->end();
		}





		//operation
		if (!empty($_POST)) {
			// get scan result
			if (isset($_POST['barcode'])) {

				$groupSound = function() use(&$r){
					$ss = [];
					foreach(['sound', 'amazon', 'area', 'label','ubi'] as $k){
						if(!empty($r->{$k})){
							$ss[] = $r->{$k};
						}
					}
					$r->sounds = $ss;
				};
				$courier_id='';
				$isHbn=false;
				$r = new StdClass;
				$r->color = 'red';
				$r->sound = 'not_found';
				$r->amazon='';
				$r->area='sydney';
				$r->stop = 0;
				$r->msg = 'NOT FOUND';
				$r->status = 'UNKNOWN';
				$r->nosound = 0;
				$r->found = 0;
				$r->id = 0;
				$r->label='';

				$status_suffix = '';

				$barcode = strtoupper(trim($_POST['barcode']));
				$r->msg = 'Not Found ' . $barcode;
				 
				$tollMatch1 = '/^T\d{6}' . TollAPI::TOLL_SLID . '\d{10}/';
				$tollMatch2 = '/^T\d{6}'. TollAPI::TOLL_SLID_OFFPEAK .'\d{10}/';
				$p = null;
				$sn = "";
				$p = ShipmentScan::getShipmentByBarcode($barcode, $sn, $courier_id);
				//to do 
				if(empty($p)) {
					if (preg_match('/-\d+$/', $barcode)) {
						list($hbn, $sn) = explode('-', $barcode);
					} else {
						$hbn = $barcode;
						$sn = 0;
					}
					$p = ImParcel::fromBarcode($hbn, $isHbn,true);
					if (!empty($p)) {
						$sn = @$p->sn;
					}
				}

				if (!empty($p)) 
				{
					if ($sn>0) {
						$isHbn=false;
					}
				}

				if (empty($p)) 
				{
					//$user=User::model()->findByPk(Yii::app()->user->id);
					if (!empty($_POST['sound'])) 
					{
						$r->sound.='_1';
					}
					$r->area='';
					//Log::log2file($barcode, "scan", "test");
					$groupSound();
					echo json_encode($r);
					Yii::app()->end();
				}
	
				/*the shipment_scan is used to record every user's scan rate.
				 *
				 */
				//aupost
				if($courier_id == Org::ORGID_COURIER_AUPOST)
				{
					if (in_array(substr($p->ref, 0, -7), ImParcel::$bri_milds)) 
					{//Brisbane;
						$r->area='brisbane';
					} elseif (in_array(substr($p->ref, 0, -7), ImParcel::$mel_milds)) 
					{//vic
						$r->area='melbourne';
					}
				}

				if ($_GET['op']=='checkin'||$_GET['op']=='tocheck'||$_GET['op']=='status'||$_GET['op']=='change') 
				{
					if ($_GET['op']=='checkin') 
					{
						$type=0;
					} elseif ($_GET['op']=='resort') 
					{
						$type=1;
					} elseif ($_GET['op']=='status') 
					{
						$type=2;
					} else {
						$type=3;
					}


					//need to fin the consol of the shipment


					// $transaction=Yii::app()->db->beginTransaction();
					// try {

					// 	// this to add other process flag to  the shipment.
					// 	// $shipment_scan= ShipmentScan::model()->find('user_id=:uid AND pid=:pid AND pno=:pno AND type=:type AND `scan_time` > DATE_SUB(NOW(), INTERVAL 1 HOUR)', [':uid'=>Yii::app()->user->id,':pid'=>$p->id,':pno'=>$sn,':type'=>$type]);
					// 	// if (empty($shipment_scan)) {
					// 	// 	ShipmentScan::genShipmentScan($p, $type, $sn, $tempWareHouse);
					// 	// }


					// 	$transaction->commit();
					// } catch (Exception $ex) {
					// 	$transaction->rollback();
					// }
				}

				$podMap = [];
				$pod_state_map = Yii::app()->params['settings']['pod_state_map'];
				if($p->ddpt_id==0)
				{
						$r->area='';
						//Log::log2file($barcode, "scan", "test");
						$groupSound();
						echo json_encode($r);
						Yii::app()->end();
				}

				$org = Org::model()->findByPk($p->ddpt_id);
				$shipmentState = strtoupper($org->state);
				$podMap = $pod_state_map[$shipmentState];



				if($_GET['op']=='checkin')
				{
					// get all confirmed consol
					$criteria = new CDbCriteria();
					$criteria->condition = "  meta like '%szportalCreate%' and DATE_SUB(CURDATE(), INTERVAL 60 DAY) <= date(created) and status = ".Consol::CONFIRM_STATUS." and meta not like '%\"scanComplete\":true%'  ";  
					$criteria->order = 'created';  

					$confirmedConsol = ImcoConsol::model()->findAll($criteria);
					$thisConsol = null;
					$consolFull = false;
					foreach ($confirmedConsol as $key => $imcoConsol) {
						//first to check whether this consol is full
						$maxWeight = @$imcoConsol->mdata["szpConsolMaxWeight"];
						$nowWeight = $imcoConsol->totWeight();
						if(!empty($maxWeight))
						{
							//second if it is full, pass it
							if(($nowWeight+$p->weight)>$maxWeight)
							{
								$consolFull = true;
								continue;
							}
						}

						// if it is available, check the parcel's state and find the pod and put it to the latest consol
						if($podMap["pod"]==$imcoConsol->pod)
						{
							$thisConsol = $imcoConsol;
							break;
						}
						//print_r($pod_state_map);
					}
					if(empty($thisConsol))
					{
						if (!empty($_POST['sound'])) {
							$r->sound.='_1';
						}

						if($consolFull)
						{
							$r->sound ="consolfull";
							$r->status = "UNKNOWN";
							$r->msg = "CONSOL FULL";
						}else
						{
							$r->msg = "NOT SUITABLE CONCOL";
						}
						$r->done=true;
						$r->area='';
						//Log::log2file($barcode, "scan", "test");
						$groupSound();
						echo json_encode($r);
						Yii::app()->end();

					}else
					{
						$errors = $thisConsol->putShipmentIntoConsol($p);
						if(count($errors)>0)
						{
							if(count($errors)>1&&isset($errors["normal"])||!isset($errors["normal"]))//if errors is not only include normal error
							{
								$r->sound ="not_found";
								$r->status = "UNKNOWN";
								$r->msg = join(",",$errors);
								$groupSound();
								echo json_encode($r);
								Yii::app()->end();
							}
						}
						$p->mdata['szpscan_consol_time'] = date("Y-m-d H:i:s");
						$p->save();
					}
				}

				if($_GET['op']=='tocheck')
				{
					if($p->isUbiHandle())
					{
						$r->ubi = "ubi";
					}
					$width = 0;
					$height = 0;
					$length = 0;
					$imService = new ImcoConsol();
					foreach ($p->packs as $key => $pack) {
						$width+=@$pack['width'];
						$height+=@$pack['height'];
						$length+=@$pack['length'];
					}
					if(!isset($p->mdata['szpscan_in_time']))
					{
						$p->mdata['szpscan_in_time'] = date("Y-m-d H:i:s");
						$p->mdata["pkgtck"] = @$p->pkg;
						$p->wtck = @$p->weight;
						$p->mdata["total_cbmtck"] = @$p->pkg*$p->cbm;
						$p->mdata['dimtck']['w'] = 0;
						$p->mdata['dimtck']['h'] = 0;
						$p->mdata['dimtck']['d'] = 0;
						$p->save();
					}

					$errors = $imService->putShipmentIntoConsol($p,true);

					$r->id = $p->id;
					$r->weight =  $p->weight;
					$r->hbn = $p->hbn;
					$r->wtck = $p->wtck;
					$r->pkg = $p->pkg;
					$r->pkgtck = $p->mdata["pkgtck"];
					$r->cbm =  @$p->pkg*$p->cbm;
					$r->dimw =  @$width;
					$r->dimh = 	@$height;
					$r->dimd =  @$length;
					$r->dimwtck = $p->mdata['dimtck']['w'];
					$r->dimhtck = $p->mdata['dimtck']['h'];
					$r->dimdtck = $p->mdata['dimtck']['d'];
					$r->warnings = $p->getSZPWarnings()." Errors: ".join($errors,",");

				}

				//print_r($errors)
				$r->id = $p->id;
				$r->color = 'green';
				if(isset($errors["normal"]))
				{
					$r->color = "blue";
				}
				$r->done = true;
				$r->found = 1;
				$r->sound = "found";
				$r->status = "KNOW";
				$r->msg = @$p->ref;
				$r->area=$podMap["city"];
				//Log::log2file($barcode, "scan", "test");
				$groupSound();
				echo json_encode($r);
				Yii::app()->end();

			}
		}

		$op = isset($_GET['op']) ? $_GET['op'] : '';
		$cid = isset($_GET['id']) ? $_GET['id'] : 0; // console id
		$parcel = new ImParcel('search');
		$parcel->unsetAttributes();
		$parcel->status = ImParcel::NEW_STATUS;

		if(isset($_GET['ImParcel'])){
			$parcel->attributes=$_GET['ImParcel'];
			return $this->render('scan', ['op' => $op,'cid' => $cid,'parcel'=>$parcel]);
		}

		$model = new ImcoConsol('search');
		$model->unsetAttributes();
		if(isset($_GET["ImcoConsol"]))
		{
			$model->setAttributes($_GET["ImcoConsol"]);
		}
		$model->szportalCreate = true;
		$model->status=Consol::CONFIRM_STATUS;


		$this->render('scan', ['op' => $op,'cid' => $cid,'model'=>$model,'parcel'=>$parcel]);

	}


	public function actionPrint()
	{
		$id = trim($_GET['id']);
		$p = ImParcel::model()->find('id = :id', [':id' => $id]);
		if (!empty($p)) {
			if (stripos($p->ref, 'AMQ') !== false) {
				//$this->printByZpl($p);
				$this->printByPrintNode($p);
			} else {
				$this->printByPrintNode($p);
			}
		}
	}

	/**
	 * with our intranet share printer to print label in order to print label instantaneous
	 */
	private function printByZpl($p)
	{
		$this->layout = false;

		$r = new stdClass();
		$r->success = 1;

		if (!empty($p)) {
			$aid =  $p->ref.sprintf('%02s', 1).'00093'.'02'.'0';
			$aid .= AusPostAPI::aidChkDgt($aid);
			$said = str_replace('AMQ', '>6AMQ>5', $aid);
			$model = [
				'cnee_name' => $p->cnee->name,
				'cnee_address' => $p->cnee->address,
				'cnee_state_postcode' => $p->cnee->suburb . ' ' . $p->cnee->state . ' ' . $p->cnee->postcode,
				'cnee_phone' => $p->cnee->tel,
				'weight' => $p->weight,
				'cnor_name' => $p->cnee->name,
				'cnor_address' => '6C The Crescent',
				'cnor_state_postcode' => 'KINGSGROVE NSW 2208',
				'parcel_ref' => $p->ref,
				'parcel_uni_no' => $p->hbn,
				'parcel_cref' => $p->cref,
				'parcel_index' => '1/1',
				'parcel_aupost_id' => $aid,
				'parcel_aupost_2dbarcode' => '_1019931265099999891'.$aid.'_1420'.$p->cnee->postcode.'_18008'.date('ymdHis'),
				'parcel_aupost_barcode' => '>;>8019931265099999891'. $said
			];
			$allData = $this->render('label_eparcel_template', ['model' => $model], true);

			$method = 'POST';

			$url = 'http://wbm.pcaexpress.com.au:800/print_zpl.php?printer=1002';
			//$url = 'http://DL360/print_zpl.php?printer=1001';

			$c = new curl($url);
			$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
			$c->setopt(CURLOPT_RETURNTRANSFER, true);
			$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
			$c->setopt(CURLOPT_TIMEOUT, 600);
			$hdr = [];

			$data = $c->asPostString(['zpl' => $allData]);
			$c->setopt(CURLOPT_POSTFIELDS, $data);
			$hdr[] = 'Content-Length: ' . strlen($data);
			$c->setopt(CURLOPT_HTTPHEADER, $hdr);
			if (!$c->exec()) {
				$r->success = 0;
				$r->msg = '<span style="color:darkred"> Print Faild :' . $c->err . '</span>';
			} else {
				if ($c->rcode != 200 && $c->rcode != 201 && $c->result !== 'Done') {
					$r->success = 0;
					$r->msg = '<span style="color:darkred"> Print Faild :' . $c->err . '</span>';
				}
			}
		} else {
			$r->success = 0;
			$r->msg = 'Shipment Not Found';
		}

		echo json_encode($r);
	}

	private function printByPrintNode($p)
	{
		spl_autoload_unregister(['YiiBase', 'autoload']); // Disable Yii autoloader
		Yii::import('application.libs.PrintNode.Loader', true);
		PrintNode_Loader::init();
		spl_autoload_register(['YiiBase', 'autoload']); // Re-enable Yii autoloader
		$credentials = new PrintNode_Credentials('PcaePrinting.41329', 'ef99369f8f9c5bbf3068c397ae085b225eb716d5');
		$request = new PrintNode_Request($credentials);
		$printJob = new PrintNode_PrintJob();
		$printJob->printer = $p->agent->extra['printer_id'];
		$printJob->source = 'HVLV WMS OP';

		//$printJob->contentType = 'pdf_base64';
		//$printJob->content = base64_encode(oPDF::renderPDF('label_A6', array('tpl' => '_label-eparcel', 'rs' => [$p]), 0, $p->ref.'.pdf'));

		$printJob->contentType = 'pdf_uri';
		$url = Yii::app()->request->hostInfo.$this->createUrl('shipment/connote').'?id='.$p->id;
		$printJob->content = $url;


		$printJob->title = $p->hbn;
		$response = $request->post($printJob);

		$r = new stdClass();
		$r->success = 1;
		if ($response->getStatusCode() != 200 && $response->getStatusCode() != 201) {
			$r->success = 0;
			$msg =  json_decode($response->getContent());
			if (isset($msg) && isset($msg->message)) {
				$r->msg = '<span style="color:darkred"> Print Faild :' . $msg->message . '</span>';
			} else {
				$r->msg = '<span style="color:darkred"> Print Faild : Unknown Error </span>';
			}
		}

		echo json_encode($r);
	}

	public function actionConnote()
	{
		$id = trim($_GET['id']);
		$model = ImParcel::model()->findByPk($id);
		if (empty($model)) {
			throw new CHttpException(404, 'Page not found!');
		}
		oPDF::renderPDF('label_A6', ['tpl' => '_label-ex', 'empty' => false, 'rs' => [$model], 'sn' => empty($_GET['sn'])? 0 : $_GET['sn']]);
	}

	public function actionFind()
	{
		if (empty($_POST)) {
			$this->render('find');
		} else {
			if (empty($_POST['shipment'])) {
				echo json_encode(['success' => false, 'msg' => 'shipment cannot be empty']);
				yii::app()->end();
			}

			$shipment = Shipment::model()->find('hbn = :ref OR ref = :ref', [':ref' => $this->_ref($_POST['shipment'])]);
			if (empty($shipment)) {
				echo json_encode(['success' => false, 'msg' => 'shipment is invalid']);
				yii::app()->end();
			}

			if (empty($shipment->mdata['location'])) {
				echo json_encode(['success' => false, 'msg' => 'not found']);
				yii::app()->end();
			} else {
				echo json_encode(['success' => true, 'msg' => $shipment->mdata['location']]);
				yii::app()->end();
			}
		}
	}

	public function actionStatus()
	{
		if (empty($_POST['shipment'])) {
			echo json_encode(['sound' => 'not_found']);
			yii::app()->end();
		} else {
			$shipment = Shipment::model()->find('hbn = :ref OR ref = :ref', [':ref' => $this->_ref($_POST['shipment'])]);
			if (empty($shipment)) {
				echo json_encode(['sound' => 'not_found']);
				yii::app()->end();
			}

			switch ($shipment->status) {
				case 80: {
					echo json_encode(['sound' => 'rts_received']);
					yii::app()->end();
				}
				break;
				case 85: {
					echo json_encode(['sound' => 'rts_done']);
					yii::app()->end();
				}
				break;
				case 86: {
					echo json_encode(['sound' => 'discard']);
					yii::app()->end();
				}
				break;
				default: {
					$shipment->status = 80;
					$shipment->mdata['rts_scan_date'] = date('Y-m-d');
					$shipment->cbwf = $p->cbwf|256;
					$shipment->update('status', 'meta', 'cbwf');
					echo json_encode(['sound' => 'rts_received']);
					yii::app()->end();
				}
			}
		}
	}

	public function actionPutaway()
	{
		if (empty($_POST)) {
			$this->render('putaway');
		} else {
			if (empty($_POST['shipment'])) {
				echo json_encode(['success' => false, 'msg' => 'shipment cannot be empty']);
				yii::app()->end();
			} else if (empty($_POST['location'])) {
				echo json_encode(['success' => false, 'msg' => 'location cannot be empty']);
				yii::app()->end();
			}
			$barcode = $_POST['shipment'];

			$tollMatch1 = '/^T\d{6}' . TollAPI::TOLL_SLID . '\d{10}/';
			$tollMatch2 = '/^T\d{6}'. TollAPI::TOLL_SLID_OFFPEAK .'\d{10}/';
			if (preg_match($tollMatch1, $barcode, $m) || preg_match($tollMatch2, $barcode, $m)) {  // for toll barcode
				// toll barcode format : 'T207711AWNL5450160016';
				$tollBarcode = $m[0];
				$ref = substr($tollBarcode, 7, 10);
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND ref = :r', [':r' => $ref]);
			} elseif (preg_match('/^(7RFZ|4XHZ)\d{8}'.StarTrackAPI::API_PRODUCT_ID.'\d{5}/', $barcode, $m)) {
				// for startrack barcode
				// startrack barcode : 7RFZ50000034EXP00001
				$ssBarcode = $m[0];
				$ref = substr($ssBarcode, 0, 12);
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $ref]);
			} elseif (preg_match('/^99700160AMQ\d{18}/', $barcode, $m)) {
				// for eparcel long barcode
				// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
				$postBarcode = $m[0];
				$ref = substr($postBarcode, 8, 10);
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND ref = :r', [':r' => $ref]);
			} elseif (preg_match('/^019931265099999891EBA\d{20}/', $barcode, $m)) {
				// our special agent , just for eparcel parcel reporting
				// barcode format : 019931265099999891EBA00752884701004440907
				$epBarcode = $m[0];
				$ref = substr($epBarcode, 18);
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND hbn = :r', [':r' => $ref]);
			} elseif (preg_match('/^0199312650999998912TD\d{18}/', $barcode, $m)) {
				// our special agent , just for eparcel parcel reporting
				// barcode format : 019931265099999891EBA00752884701004440907
				$epBarcode = $m[0];
				$ref = substr($epBarcode, 18);
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND (hbn = :r OR ref=:r)', [':r' => $ref]);
			} elseif (preg_match('/^019931265099999891((\w{3,5})\d{7})(\d{11})$/', $barcode, $m)) {
				// for eparcel long barcode
				// 019931265099999891AMQ328549401000935002
				// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
				$postBarcode = $m[0];
				// $ref = substr($postBarcode,18,10);
				$ref = $m[1];
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND (ref = :r OR hbn=:r OR ref = :r2 OR hbn = :r2) AND (cbwf&:cbwf)=0', [':r' => $ref, ':r2' => $m[1].$m[3], ':cbwf'=> ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
			} elseif (preg_match('/^(019931265099999891((\w{3,5})\d{7})\d{11})\d{23}$/', $barcode, $m)) {
				// for eparcel long barcode
				// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
				$postBarcode = $m[1];
				$ref = $m[2];
				$barcode=$postBarcode;
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND (ref = :r OR hbn=:r) AND cbwf&:cbwf=0', [':r' => $ref,':cbwf'=> ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
			} elseif (preg_match("/^610412\d{18}0\d{4}0$/", $barcode, $m)) {
				$prefix= TntAPI::decodePrefix(substr($m[0], 6, 6));
				$ref=$prefix.substr($m[0], 12, 9);
				$barcode = $ref.substr($m[0], 21, 3);
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $ref]);
			} elseif (preg_match('/^(CP[A-Z]{5}\d{7})(\d{3})$/', $barcode, $m)){ //couriers please
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} elseif (preg_match('/^(SCAU\d{8})(\d{3})(\d{3})$/', $barcode, $m)){ //saicheng
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} elseif (preg_match('/^(DQ\d{6}|DQ\d{5})(\d{3})(\d{7})$/', $barcode, $m)){ //saicheng
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} elseif (preg_match('/^(ZV\d{6})(\d{3})(\d{7})$/', $barcode, $m)){ //hunter express
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND (ref = :r OR cref = :r) AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} else if (preg_match('/^(ZK\d{8}|33A8Y\d{7})$/', $barcode, $m)) {
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} else if (preg_match('/^99700160UDW\d{18}/', $barcode, $m)) { //saicheng
				// for eparcel long barcode
				$postBarcode = $m[0];
				$ref = substr($postBarcode, 8, 10);
				$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND ref = :r', [':r' => $ref]);

				if (empty($p)) {
					$ref = substr($postBarcode, 8, 16);
					$p = ImParcel::model()->find('consol_id = 0 AND status < 100 AND ref = :r', [':r' => $ref]);
				}
			} else {
				$p = ImParcel::model()->find('hbn = :ref OR ref = :ref', [':ref' => $barcode]);
			}

			$shipment = $p;
			if (empty($shipment)) {
				echo json_encode(['success' => false, 'msg' => 'shipment is invalid']);
				yii::app()->end();
			}
			$location = WmsLocation::model()->find('name = :code OR code = :code', [':code' => $_POST['location']]);
			if (empty($location)) {
				echo json_encode(['success' => false, 'msg' => 'location is invalid']);
				yii::app()->end();
			}

			if (empty($shipment->mdata['location'])) {
				$shipment->mdata['location'] = $_POST['location'];
				unset($shipment->mdata['used_location']);
				$shipment->update('meta');

				echo json_encode(['success' => true, 'msg' => 'Put away ' . $_POST['shipment'] . ' in ' . $_POST['location'] . ' successfully']);
				yii::app()->end();
			} else {
				echo json_encode(['success' => false, 'msg' => 'Already in location ' . $shipment->mdata['location']]);
				yii::app()->end();
			}
		}
	}

	public function actionReship()
	{
		if (empty($_POST)) {
			$this->render('reship');
		} else {
			if (empty($_POST['shipment'])) {
				echo json_encode(['success' => false, 'msg' => 'shipment cannot be empty']);
				yii::app()->end();
			}
			$barcode = $_POST['shipment'];

			$tollMatch1 = '/^T\d{6}' . TollAPI::TOLL_SLID . '\d{10}/';
			$tollMatch2 = '/^T\d{6}'. TollAPI::TOLL_SLID_OFFPEAK .'\d{10}/';
			if (preg_match($tollMatch1, $barcode, $m) || preg_match($tollMatch2, $barcode, $m)) {  // for toll barcode
				// toll barcode format : 'T207711AWNL5450160016';
				$tollBarcode = $m[0];
				$ref = substr($tollBarcode, 7, 10);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r', [':r' => $ref]);
			} elseif (preg_match('/^(7RFZ|4XHZ)\d{8}'.StarTrackAPI::API_PRODUCT_ID.'\d{5}/', $barcode, $m)) {
				// for startrack barcode
				// startrack barcode : 7RFZ50000034EXP00001
				$ssBarcode = $m[0];
				$ref = substr($ssBarcode, 0, 12);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $ref]);
			} elseif (preg_match('/^99700160AMQ\d{18}/', $barcode, $m)) {
				// for eparcel long barcode
				// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
				$postBarcode = $m[0];
				$ref = substr($postBarcode, 8, 10);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r', [':r' => $ref]);
			} elseif (preg_match('/^019931265099999891EBA\d{20}/', $barcode, $m)) {
				// our special agent , just for eparcel parcel reporting
				// barcode format : 019931265099999891EBA00752884701004440907
				$epBarcode = $m[0];
				$ref = substr($epBarcode, 18);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND hbn = :r', [':r' => $ref]);
			} elseif (preg_match('/^0199312650999998912TD\d{18}/', $barcode, $m)) {
				// our special agent , just for eparcel parcel reporting
				// barcode format : 019931265099999891EBA00752884701004440907
				$epBarcode = $m[0];
				$ref = substr($epBarcode, 18);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND (hbn = :r OR ref=:r)', [':r' => $ref]);
			} elseif (preg_match('/^019931265099999891((\w{3,5})\d{7})(\d{11})$/', $barcode, $m)) {
				// for eparcel long barcode
				// 019931265099999891AMQ328549401000935002
				// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
				$postBarcode = $m[0];
				// $ref = substr($postBarcode,18,10);
				$ref = $m[1];
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND (ref = :r OR hbn=:r OR ref = :r2 OR hbn = :r2) AND (cbwf&:cbwf)=0', [':r' => $ref, ':r2' => $m[1].$m[3], ':cbwf'=> ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
			} elseif (preg_match('/^(019931265099999891((\w{3,5})\d{7})\d{11})\d{23}$/', $barcode, $m)) {
				// for eparcel long barcode
				// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
				$postBarcode = $m[1];
				$ref = $m[2];
				$barcode=$postBarcode;
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND (ref = :r OR hbn=:r) AND cbwf&:cbwf=0', [':r' => $ref,':cbwf'=> ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
			} elseif (preg_match("/^610412\d{18}0\d{4}0$/", $barcode, $m)) {
				$prefix= TntAPI::decodePrefix(substr($m[0], 6, 6));
				$ref=$prefix.substr($m[0], 12, 9);
				$barcode = $ref.substr($m[0], 21, 3);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $ref]);
			} elseif (preg_match('/^(CP[A-Z]{5}\d{7})(\d{3})$/', $barcode, $m)){ //couriers please
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} elseif (preg_match('/^(SCAU\d{8})(\d{3})(\d{3})$/', $barcode, $m)){ //saicheng
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} elseif (preg_match('/^(DQ\d{6}|DQ\d{5})(\d{3})(\d{7})$/', $barcode, $m)){ //saicheng
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} elseif (preg_match('/^(ZV\d{6})(\d{3})(\d{7})$/', $barcode, $m)){ //hunter express
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND (ref = :r OR cref = :r) AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} else if (preg_match('/^(ZK\d{8}|33A8Y\d{7})$/', $barcode, $m)) {
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} else if (preg_match('/^99700160UDW\d{18}/', $barcode, $m)) { //saicheng
				// for eparcel long barcode
				$postBarcode = $m[0];
				$ref = substr($postBarcode, 8, 10);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r', [':r' => $ref]);

				if (empty($p)) {
					$ref = substr($postBarcode, 8, 16);
					$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r', [':r' => $ref]);
				}
			} else {
				$p = ImParcel::model()->find('hbn = :ref OR ref = :ref', [':ref' => $barcode]);
			}

			$shipment = $p;
			if (empty($shipment)) {
				echo json_encode(['success' => false, 'msg' => 'shipment is invalid']);
				yii::app()->end();
			}

			if (!empty($shipment->mdata['location'])) {
				$shipment->mdata['used_location'] = $shipment->mdata['location'];
				unset($shipment->mdata['location']);
				$shipment->update('meta');

				echo json_encode(['success' => true, 'msg' => 'Reship ' . $_POST['shipment'] . ' from ' . $shipment->mdata['used_location'] . ' successfully']);
				yii::app()->end();
			} else {
				echo json_encode(['success' => true, 'msg' => 'Reship ' . $_POST['shipment'] . ' successfully']);
				yii::app()->end();
			}
		}
	}

	public function actionDiscard()
	{
		if (empty($_POST)) {
			$this->render('discard');
		} else {
			if (empty($_POST['shipment'])) {
				echo json_encode(['success' => false, 'msg' => 'shipment cannot be empty']);
				yii::app()->end();
			}

			$tollMatch1 = '/^T\d{6}' . TollAPI::TOLL_SLID . '\d{10}/';
			$tollMatch2 = '/^T\d{6}'. TollAPI::TOLL_SLID_OFFPEAK .'\d{10}/';
			if (preg_match($tollMatch1, $barcode, $m) || preg_match($tollMatch2, $barcode, $m)) {  // for toll barcode
				// toll barcode format : 'T207711AWNL5450160016';
				$tollBarcode = $m[0];
				$ref = substr($tollBarcode, 7, 10);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r', [':r' => $ref]);
			} elseif (preg_match('/^(7RFZ|4XHZ)\d{8}'.StarTrackAPI::API_PRODUCT_ID.'\d{5}/', $barcode, $m)) {
				// for startrack barcode
				// startrack barcode : 7RFZ50000034EXP00001
				$ssBarcode = $m[0];
				$ref = substr($ssBarcode, 0, 12);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $ref]);
			} elseif (preg_match('/^99700160AMQ\d{18}/', $barcode, $m)) {
				// for eparcel long barcode
				// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
				$postBarcode = $m[0];
				$ref = substr($postBarcode, 8, 10);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r', [':r' => $ref]);
			} elseif (preg_match('/^019931265099999891EBA\d{20}/', $barcode, $m)) {
				// our special agent , just for eparcel parcel reporting
				// barcode format : 019931265099999891EBA00752884701004440907
				$epBarcode = $m[0];
				$ref = substr($epBarcode, 18);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND hbn = :r', [':r' => $ref]);
			} elseif (preg_match('/^0199312650999998912TD\d{18}/', $barcode, $m)) {
				// our special agent , just for eparcel parcel reporting
				// barcode format : 019931265099999891EBA00752884701004440907
				$epBarcode = $m[0];
				$ref = substr($epBarcode, 18);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND (hbn = :r OR ref=:r)', [':r' => $ref]);
			} elseif (preg_match('/^019931265099999891((\w{3,5})\d{7})(\d{11})$/', $barcode, $m)) {
				// for eparcel long barcode
				// 019931265099999891AMQ328549401000935002
				// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
				$postBarcode = $m[0];
				// $ref = substr($postBarcode,18,10);
				$ref = $m[1];
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND (ref = :r OR hbn=:r OR ref = :r2 OR hbn = :r2) AND (cbwf&:cbwf)=0', [':r' => $ref, ':r2' => $m[1].$m[3], ':cbwf'=> ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
			} elseif (preg_match('/^(019931265099999891((\w{3,5})\d{7})\d{11})\d{23}$/', $barcode, $m)) {
				// for eparcel long barcode
				// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
				$postBarcode = $m[1];
				$ref = $m[2];
				$barcode=$postBarcode;
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND (ref = :r OR hbn=:r) AND cbwf&:cbwf=0', [':r' => $ref,':cbwf'=> ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
			} elseif (preg_match("/^610412\d{18}0\d{4}0$/", $barcode, $m)) {
				$prefix= TntAPI::decodePrefix(substr($m[0], 6, 6));
				$ref=$prefix.substr($m[0], 12, 9);
				$barcode = $ref.substr($m[0], 21, 3);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $ref]);
			} elseif (preg_match('/^(CP[A-Z]{5}\d{7})(\d{3})$/', $barcode, $m)){ //couriers please
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} elseif (preg_match('/^(SCAU\d{8})(\d{3})(\d{3})$/', $barcode, $m)){ //saicheng
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} elseif (preg_match('/^(DQ\d{6}|DQ\d{5})(\d{3})(\d{7})$/', $barcode, $m)){ //saicheng
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} elseif (preg_match('/^(ZV\d{6})(\d{3})(\d{7})$/', $barcode, $m)){ //hunter express
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND (ref = :r OR cref = :r) AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} else if (preg_match('/^(ZK\d{8}|33A8Y\d{7})$/', $barcode, $m)) {
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $m[1]]);
			} else if (preg_match('/^99700160UDW\d{18}/', $barcode, $m)) { //saicheng
				// for eparcel long barcode
				$postBarcode = $m[0];
				$ref = substr($postBarcode, 8, 10);
				$p = ImParcel::model()->find('consol_id > 0 AND status < 100 AND ref = :r', [':r' => $ref]);
			}

			$shipment = $p;
			if (empty($shipment) || !in_array($shipment->status, [80,81,82,83,84,85])) {
				echo json_encode(['success' => false, 'msg' => 'shipment is invalid']);
				yii::app()->end();
			}

			if (!empty($shipment->mdata['location'])) {
				$shipment->mdata['used_location'] = $shipment->mdata['location'];
				unset($shipment->mdata['location']);
				$shipment->status = 86;
				$shipment->update('meta', 'status');

				echo json_encode(['success' => true, 'msg' => 'Discard ' . $_POST['shipment'] . ' from ' . $shipment->mdata['used_location'] . ' successfully']);
				yii::app()->end();
			} else {
				$shipment->status = 86;
				$shipment->update('status');

				echo json_encode(['success' => true, 'msg' => 'Discard ' . $_POST['shipment'] . ' successfully']);
				yii::app()->end();
			}
		}
	}

	public function actionScanComplete()
	{
		$id = $_GET["id"];
		$model=ImcoConsol::model()->findByPk($id);
		if($model->status == 20){
			$model->mdata["scanComplete"]=true;
			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionBagging()
	{
		$model=Bag::model();
		$model->unsetAttributes();

		$this->render("bagging",["model"=>$model]);
	}

	public function actionPrintBagLabel()
	{
		$id = $_GET["id"];
		$model=Bag::model()->findByPk($id);
		oPDF::renderPDF('label_bag', ['bag' => $model], 1, $model->no . '.pdf');
	}


	private function _ref($s)
	{
		if (preg_match('/(AMQ\d{7}|333UF\d{7})/', $s, $matches)) {
			return $matches[1];
		} else {
			return $s;
		}
	}

}
