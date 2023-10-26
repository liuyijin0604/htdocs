<?php

class JobController extends Controller
{

	protected $nonAjax = array('exloclist', 'download', 'palletRpt', 'pltMark', 'downloadCost');

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view', array(
			'model' => $this->loadModel($id),
		));
	}


	public function actionGlobalJobList()
	{
		$jobs = new CargoProcess('search');
		$jobs->type = CargoProcess::NORMAL_CARGO;
		if (!empty($_GET['CargoProcess'])) {
			$jobs->setAttributes($_GET['CargoProcess']);
			$jobs->setDeliveryBookingTime($_GET['CargoProcess']['deliveryBookingTime']);
		}
		$this->render('list', ["model" => $jobs, 'type' => 'global']);
	}
	public function actionMyList()
	{
		$jobs = new CargoProcess('search');
		$jobs->driver_id = User::getCurrentUser()->org_id;
		$jobs->type = CargoProcess::NORMAL_CARGO;
		$jobs->deliveriedStatus = CargoProcess::UNDELIVERIED;
		if (!empty($_GET['CargoProcess'])) {
			$jobs->setAttributes($_GET['CargoProcess']);
			if (isset($_GET['CargoProcess']['deliveriedStatus'])) {
				$jobs->deliveriedStatus = $_GET['CargoProcess']['deliveriedStatus'];
			}
			$jobs->setDeliveryBookingTime($_GET['CargoProcess']['deliveryBookingTime']);
		}
		$this->render('list', ["model" => $jobs, 'type' => 'my']);
	}

	public function actionJobDetails()
	{
		$jobs = new CargoProcess('search');
		$jobs->driver_id = User::getCurrentUser()->org_id;
		$jobs->type = CargoProcess::NORMAL_CARGO;
		$jobs->jobId = $_GET['id'];
		$jobs->isSearchJob = 1;
		if (!empty($_GET['CargoProcess'])) {
			$jobs->setAttributes($_GET['CargoProcess']);
			if (isset($_GET['CargoProcess']['deliveriedStatus'])) {
				$jobs->deliveriedStatus = $_GET['CargoProcess']['deliveriedStatus'];
			}
			$jobs->setDeliveryBookingTime($_GET['CargoProcess']['deliveryBookingTime']);
		}
		$this->render('job_cargos_list', ["model" => $jobs, 'type' => 'my', "cjobID" => $_GET['id']]);
	}

	public function actionCargoJobList()
	{
		$jobs = new CargoProcessJob('search');
		$jobs->driver_id = User::getCurrentUser()->org_id;
		$jobs->type = CargoProcessJob::NORMAL_JOB;
		$jobs->status = '<' . CargoProcessJob::PROCESSDONE;
		$jobs->created = date("Y-m-d");

		if (!empty($_GET['CargoProcessJob'])) {
			$jobs->setAttributes($_GET['CargoProcessJob']);
			if (isset($_GET['CargoProcessJob']['time'])) {
				$jobs->mdata['time'] = $_GET['CargoProcessJob']['time'];
			}
			if (isset($_GET['CargoProcessJob']['status'])) {
				$jobs->status = $_GET['CargoProcessJob']['status'];
			}
			if (isset($_GET['CargoProcessJob']['created'])) {
				$jobs->created = $_GET['CargoProcessJob']['created'];
			}
		}
		$this->render('cargo_job_list', ["model" => $jobs]);
	}

	public function actionAllJobList()
	{
		$jobs = new CargoProcessJob('search');
		$jobs->driver_id = User::getCurrentUser()->org_id;

		$jobs->status = '<' . CargoProcessJob::PROCESSDONE;
		$jobs->created = date("Y-m-d");

		if (!empty($_GET['CargoProcessJob'])) {
			$jobs->setAttributes($_GET['CargoProcessJob']);
			if (isset($_GET['CargoProcessJob']['time'])) {
				$jobs->mdata['time'] = $_GET['CargoProcessJob']['time'];
			}
			if (isset($_GET['CargoProcessJob']['status'])) {
				$jobs->status = $_GET['CargoProcessJob']['status'];
			}
			if(isset($_GET['CargoProcessJob']['StartDate'])){ 
				$jobs->strdate = $_GET['CargoProcessJob']['StartDate'];
			}
			if(isset($_GET['CargoProcessJob']['EndDate'])){
				$jobs->enddate = $_GET['CargoProcessJob']['EndDate'];
			}
			if(isset($_GET['CargoProcessJob']['created'])) 
			{
				$jobs->created = $_GET['CargoProcessJob']['created'];
			}
			if(isset($_GET['w'])){
				if($_GET['w']==1){
					$jobs->created = date('Y-m-d');
				}elseif($_GET['w']==2){
					$jobs->created = date('Y-m-d',strtotime("1 day"));
				}
			}
		}
		$this->render('cargo_job_list', ["model" => $jobs]);
	}

	public function actionDeliveryedCargo()
	{
		$jobRelations = new CargoProcessJobRelations('search');
		$jobRelations->driverId = User::getCurrentUser()->org_id;

		if (!empty($_GET['week'])) {
			$jobRelations->week = $_GET['week'];
		} else {
			$jobRelations->week = 0;
		}

		$this->render('cargo_invoice_list', ["model" => $jobRelations, 'week' => $_GET['week']]);
	}



	public function actionFbaList()
	{
		$jobs = new CargoProcess('search');
		$jobs->driver_id = User::getCurrentUser()->org_id;
		$jobs->type = CargoProcess::FBA_CARGO;
		$jobs->deliveriedStatus = CargoProcess::UNDELIVERIED;
		if (!empty($_GET['CargoProcess'])) {
			$jobs->setAttributes($_GET['CargoProcess']);
			if (isset($_GET['CargoProcess']['deliveriedStatus'])) {
				$jobs->deliveriedStatus = $_GET['CargoProcess']['deliveriedStatus'];
			}
			$jobs->setDeliveryBookingTime($_GET['CargoProcess']['deliveryBookingTime']);
		}
		$this->render('list', ["model" => $jobs, 'type' => 'fba']);
	}

	public function actionB2BList()
	{
		$jobs = new CargoProcess('search');
		$jobs->driver_id = User::getCurrentUser()->org_id;
		$jobs->type = CargoProcess::B2B_CARGO;
		$jobs->deliveriedStatus = CargoProcess::UNDELIVERIED;
		if (!empty($_GET['CargoProcess'])) {
			$jobs->setAttributes($_GET['CargoProcess']);
			if (isset($_GET['CargoProcess']['deliveriedStatus'])) {
				$jobs->deliveriedStatus = $_GET['CargoProcess']['deliveriedStatus'];
			}
			$jobs->setDeliveryBookingTime($_GET['CargoProcess']['deliveryBookingTime']);
		}
		$this->render('list', ["model" => $jobs, 'type' => 'b2b']);
	}

	public function actionAccept($id)
	{
		$model = $this->loadModel($id);
		if ($model->driver_id > 0) {
			$model->addError('Job is accepted', 'Job is accepted');
			$this->ajaxResult($model);
		}

		$model->driver_id = User::getCurrentUser()->org_id;
		$model->status = CargoProcess::WAITINGAGENTDELIVERY;
		$model->save();
		$model->getCRuleInvoice();

		$this->ajaxResult($model);
	}

	public function actionSignJobPage($id)
	{
		if (!empty($_GET['id']) && empty($id)) {
			$id = $_GET['id'];
		}
		$jobModel = CargoProcessJob::model()->findByPk($id);
		$cargoRelationsModels = $jobModel->job_relations;
		foreach ($cargoRelationsModels as $cargoRelationsModel) {
			$job = $cargoRelationsModel->cargo_process;
			if (!empty($job)) {
				if (!empty($_FILES['CargoProcess'])) {
					$errors = [];
					$photos = empty($_FILES['CargoProcess']['tmp_name']['photos']) ? [] : $_FILES['CargoProcess']['tmp_name']['photos'];
					foreach ($photos as $key => $photo) {
						if (empty($photo)) {
							$errors[] = 'images not exists';
						} else {
							$name = $_FILES['CargoProcess']['name']['photos'][$key];
							if (!is_uploaded_file($photo)) $errors[] = $name . 'images not exists';
							$hash = FileRepo::uploadHash($job->shipment, FileRepo::CARGOPROCESSFILETYPE);
							$filesize = filesize($photo);
							$date = date('Y-m-d H:i:s');
							$fileHash = hash_file('crc32b', $photo) . hash('crc32b', $filesize);
							$finfo = finfo_open(FILEINFO_MIME_TYPE);
							$mime = finfo_file($finfo, $photo);
							$fr = CargoProcess::updateUploadSingleFile($filesize, $date, $fileHash, $finfo, $mime, $photo, $name, $hash, false, false, false, false, FileRepo::PENDING,true);
						}
					}
				}
			}
		}

		if (!empty($cargoRelationsModels)) {

			foreach ($cargoRelationsModels as $cargoRelationsModel) {
				$cargoModel = CargoProcess::model()->findByPk($cargoRelationsModel->cargo_process_id);
				if (!empty($_POST['CargoProcess']) && !empty($_POST['CargoProcess']['sig'] && !empty($cargoModel))) {
					$cargos = [$cargoModel];
					foreach ($cargos as $key => $cargo) {
						$cargo->mdata['sig'] = $_POST['CargoProcess']["sig"];
						$cargo->mdata['sprint'] = $_POST['CargoProcess']["sprint"];
						$cargo->mdata['sdate'] = date('Y-m-d');
						$cargo->mdata['driverDeliveryStatus'] = CargoProcess::DELIVERIED;
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
			if (!empty($_POST['CargoProcess']) && !empty($_POST['CargoProcess']['sig'])) {
				$jobModel->mdata["sig"] = $_POST['CargoProcess']["sig"];
				$jobModel->mdata['sprint'] = $_POST['CargoProcess']["sprint"];
				$jobModel->mdata['sdate'] = date('Y-m-d');
				$jobModel->save();
				$this->ajaxResult($jobModel);
			}
		}

		$this->render('signJobPage', ["job" => $jobModel]);
	}

	public function actionSignPage($id)
	{
		$job = CargoProcess::model()->findByPk($id);
		if (!empty($_FILES['CargoProcess'])) {
			$errors = [];
			$photos = empty($_FILES['CargoProcess']['tmp_name']['photos']) ? [] : $_FILES['CargoProcess']['tmp_name']['photos'];
			foreach ($photos as $key => $photo) {
				if (empty($photo)) {
					$errors[] = 'images not exists';
				} else {
					$name = $_FILES['CargoProcess']['name']['photos'][$key];
					if (!is_uploaded_file($photo)) $errors[] = $name . 'images not exists';
					$hash = FileRepo::uploadHash($job->shipment, FileRepo::CARGOPROCESSFILETYPE);
					$filesize = filesize($photo);
					$date = date('Y-m-d H:i:s');
					$fileHash = hash_file('crc32b', $photo) . hash('crc32b', $filesize);
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mime = finfo_file($finfo, $photo);
					$fr = CargoProcess::updateUploadSingleFile($filesize, $date, $fileHash, $finfo, $mime, $photo, $name, $hash);
				}
			}
		}
		if (!empty($_POST['CargoProcess']) && !empty($_POST['CargoProcess']['signNotes'])) {
			$job = CargoProcess::model()->findByPk($id);
			$job->mdata['signNotes'] = $_POST['CargoProcess']["signNotes"];
			$job->note = $job->note . " " . $_POST['CargoProcess']["signNotes"];
			$job->save();
		}

		if (!empty($_POST['CargoProcess']) && !empty($_POST['CargoProcess']['sig'])) {
			$cargos = $job->getCargos();
			foreach ($cargos as $key => $cargo) {
				$cargo->mdata['sig'] = $_POST['CargoProcess']["sig"];
				$cargo->mdata['sprint'] = $_POST['CargoProcess']["sprint"];
				$cargo->mdata['sdate'] = date('Y-m-d');
				$cargo->mdata['driverDeliveryStatus'] = CargoProcess::DELIVERIED;
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

				if (!empty($_POST['longitudesig']) && !empty($_POST['latitudesig'])) {
					$model = new DriverTracking();
					$model->driver_id = User::getCurrentUser()->org_id;
					$model->tracking_time = date('Y-m-d H:i:s');
					$model->longitude = $_POST['longitudesig'];
					$model->latitude = $_POST['latitudesig'];
					$model->type = 2;
					$model->cargo_process_id = $cargo->id;
					$model->save();
				}
			}
		}

		$this->render('signPage', ["job" => $job]);
	}

	public function actionSignErrorPage($id)
	{
		$job = CargoProcess::model()->findByPk($id);

		if (!empty($_FILES['CargoProcess'])) {
			$errors = [];
			$photos = empty($_FILES['CargoProcess']['tmp_name']['photos']) ? [] : $_FILES['CargoProcess']['tmp_name']['photos'];
			foreach ($photos as $key => $photo) {
				if (empty($photo)) {
					$errors[] = 'images not exists';
				} else {
					$name = $_FILES['CargoProcess']['name']['photos'][$key];
					if (!is_uploaded_file($photo)) $errors[] = $name . 'images not exists';
					$hash = FileRepo::uploadHash($job->shipment, FileRepo::CARGOPROCESSFILETYPE);
					$filesize = filesize($photo);
					$date = date('Y-m-d H:i:s');
					$fileHash = hash_file('crc32b', $photo) . hash('crc32b', $filesize);
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mime = finfo_file($finfo, $photo);
					$fr = CargoProcess::updateUploadSingleFile($filesize, $date, $fileHash, $finfo, $mime, $photo, $name, $hash);
				}
			}
		}

		if (!empty($_POST['CargoProcess']) && !empty($_POST['CargoProcess']['sig'])) {
			$cargos = $job->getCargos();
			foreach ($cargos as $key => $cargo) {
				$cargo->mdata['sigerror'] = $_POST['CargoProcess']["sig"];
				$cargo->mdata['sprinterror'] = $_POST['CargoProcess']["sprinterror"];
				$cargo->mdata['sdateerror'] = date('Y-m-d');
				$cargo->mdata['driverDeliveryStatus'] = CargoProcess::ERROR;
				$cargo->mdata['CargoProcessErrorType'] = CargoProcess::$errorTypeList[$_POST['CargoProcess']["CargoProcessErrorType"]];
				$cargo->save();

				$cargo->log("whsignReturn");
				$shipmentModel = $cargo->shipment;

				$filename = $shipmentModel->id . "returnpod";
				$image = ['sprint' => $cargo->mdata['sprinterror'], 'sig' => $cargo->mdata['sigerror'], 'sdate' => $cargo->mdata['sdateerror']];
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
				$name = "returnpod.pdf";
				$fileHash = hash_file('crc32b', $pdfPath) . hash('crc32b', $filesize);
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime = finfo_file($finfo, $pdfPath);
				CargoProcess::updateUploadSingleFile($filesize, $date, $fileHash, $finfo, $mime, $pdfPath, $name, $pphash, true, false, $cargo->id);
				$cargo->processReturn();
			}
		}

		$this->render('signErrorPage', ["job" => $job]);
	}

	public function actionViewCargoReceipt($id)
	{
		$model = $this->loadModel($id);
		$fileType = FileRepo::IMPORTPODFILE;
		$file = FileRepo::model()->find("type = :type and fid = :fid order by id desc", [":type" => $fileType, ":fid" => $model->shipment_id]);
		$file->download();
	}

	public function actionTonyAssignDriver()
	{
		$assignDriver = $_POST['driver_id'];
		$arrayCargoProcessID = explode(',', $_POST['id']);
		$successfulInfo = [];
		$errorInfo = [];

		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach ($arrayCargoProcessID as $cargoprocessId) {
				$model = CargoProcess::model()->findByPk($cargoprocessId);
				$model->mdata['tony_assign_driver'] = CargoProcess::$tonySydDriver[$assignDriver];
				if ($model->save()) {
					$successfulInfo = "<br /> Ref: " . $model->shipment->ref . " assigned successful!";
				} else {
					$errorInfo = "<br /> Error! Please contact relevant personnel.";
				}
			}

			if (empty($errorInfo)) {
				$transaction->commit();
				echo json_encode([
					'isSuccess' => true,
					'info' => $successfulInfo,
				]);
				return;
			} else {
				$transaction->rollback();
				echo json_encode([
					'isSuccess' => false,
					'info' => $errorInfo,
				]);
				return;
			}
		} catch (Exception $ex) {
			$transaction->rollback();
			throw $ex;
		}
	}

	public function actionErrorCargoProcess()
	{
		$cargoProcessId = $_POST['id'];
		$cargoProcessErrorId = $_POST['error_id'];

		$modelCargoProcess = CargoProcess::model()->findByPk($cargoProcessId);
		if (!empty($modelCargoProcess)) {
			$modelCargoProcess->mdata['CargoProcessErrorType'] = $cargoProcessErrorId;
			$modelCargoProcess->status = 99; //failed delivery
			$modelCargoProcess->note = $modelCargoProcess->note . "\n" . CargoProcess::$errorDeliveryList[$cargoProcessErrorId];
			if ($cargoProcessErrorId == 1) {
				$modelCargoProcess->sub_type = 16384;
			} elseif ($cargoProcessErrorId == 2) {
				$modelCargoProcess->sub_type = 32768;
			} elseif ($cargoProcessErrorId == 3) {
				$modelCargoProcess->sub_type = 65536;
			} elseif ($cargoProcessErrorId == 4) {
				$modelCargoProcess->sub_type = 131072;
			} elseif ($cargoProcessErrorId == 5) {
				$modelCargoProcess->sub_type = 262144;
			} elseif ($cargoProcessErrorId == 6) {
				$modelCargoProcess->sub_type = 524288;
			}

			$modelCargoProcess->save();

			echo json_encode([
				'isSuccess' => true,
				'info' => 'Successful！',
			]);
			return;
		} else {
			echo json_encode([
				'isSuccess' => false,
				'info' => 'Error, CargoProcess is not exist.',
			]);
			return;
		}
	}

	public function actionExport()
	{
		$reportService = new ReportService();
		$data = $reportService->getCargoProcessDetailByCJobID($_GET['id']);
		$header = ['Connote', 'Deliver', 'Name', 'Tel', 'Cbm', 'Weight', 'Distance', 'Pkg', 'Note', 'Driver'];
		$filename = 'C_Job_report_' . time() . '.xlsx';
		$reportService->exportData($data, $header, $filename);
	}

	public function actionExportInvoice()
	{
		$reportService = new ReportService();
		$xls = $reportService->getCargoProcessJobRelationInvoiceDetail($_POST['week']);
		$tempfile = Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR . 'CargoProcess Report-' . User::getCurrentUser()->org_id . '.xlsx';
		$xls->output($tempfile, $xls->format, false);
		$dlUrl =  $this->createUrl('job/downloadTmcResult');
		$resp = '<span>File is ready , Please <a href="' . $dlUrl . '" target="_blank">click me</a> to download now<span>';
		echo $resp;
	}

	public function actionGetBiddingList()
	{
		$objBidding = new CargoProcessBidding('search');
		$objBidding->dpt_id = User::getCurrentUser()->dpt_id;
		$objBidding->status = 10;
		if(!empty($_GET["mybidding"])){
			$objBidding->driver_id = User::getCurrentUser()->org_id;
		}
		if (isset($_GET["CargoProcessBidding"])) {
			$objBidding->attributes = $_GET["CargoProcessBidding"];
			$objBidding->deliverydate = $_GET["CargoProcessBidding"]['deliverydate'];
		}
		$this->render('cargo_bidding_list', [
			'model' => $objBidding
		]);
	}

	public function actionGetMyBiddingList()
	{
		$objBidding = new CargoProcessBidding('search');
		$objBidding->driver_id = User::getCurrentUser()->org_id;
		$this->render('cargo_bidding_list', [
			'model' => $objBidding
		]);
	}

	public function actionGetBiddingDetail()
	{
		$objBidding = CargoProcessBidding::model()->findByPk($_GET['id']);
		if (!empty($_GET['id'])) {
			$objCargoProcessBiddingDetail = CargoProcessBiddingDetail::model()->find('bid_id=:bid_id and driver_id = :driver_id', [':bid_id' => $_GET['id'], ':driver_id' => User::getCurrentUser()->org_id]);
			if (!empty($objCargoProcessBiddingDetail)) {
				if (!empty($_POST['CargoProcessBiddingDetail'])) {
					$objCargoProcessBiddingDetail->setAttributes($_POST['CargoProcessBiddingDetail']);
					$objCargoProcessBiddingDetail->driver_id = User::getCurrentUser()->org_id;
					$objCargoProcessBiddingDetail->status = 1;
					$objCargoProcessBiddingDetail->createtime = date('Y-m-d');
					$objCargoProcessBiddingDetail->creater = User::getCurrentUser()->id;
					$objCargoProcessBiddingDetail->save();
					$this->ajaxResult($objCargoProcessBiddingDetail);
				}else{
					$this->render('cargo_bidding_detail', ['model' => $objBidding->shipment, 'objBidding' => $objBidding, 'objBiddingDetail' => $objCargoProcessBiddingDetail]);
				}
			} else {
				$objNewCargoProcessBiddingDetail = new CargoProcessBiddingDetail;
				if (!empty($_POST['CargoProcessBiddingDetail'])) {
					$objNewCargoProcessBiddingDetail->setAttributes($_POST['CargoProcessBiddingDetail']);
					$objNewCargoProcessBiddingDetail->driver_id = User::getCurrentUser()->org_id;
					$objNewCargoProcessBiddingDetail->status = 1;
					$objNewCargoProcessBiddingDetail->createtime = date('Y-m-d');
					$objNewCargoProcessBiddingDetail->creater = User::getCurrentUser()->id;
					$objNewCargoProcessBiddingDetail->save();
					$this->ajaxResult($objNewCargoProcessBiddingDetail);
				}else{
					$this->render('cargo_bidding_detail', ['model' => $objBidding->shipment, 'objBidding' => $objBidding, 'objBiddingDetail' => $objNewCargoProcessBiddingDetail]);
				}
			}
		}
	}

	public function actionDownloadTmcResult()
	{
		$xls = new oExcel;
		$mfn = 'CargoProcess Report-' . User::getCurrentUser()->org_id;
		$tempfile = Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR . $mfn . '.xlsx';
		$xls->load($tempfile);
		$xls->output($mfn . '.xlsx');
	}

	public function actionExportCost()
	{
		if (empty($_POST)) {
			$this->render('export_cost');
		} else {
			$response = new stdClass;
			//$org = Org::model()->findByPk(Yii::app()->user->org);
			$response->dateFrom = date('Y-m-d H:i:s', strtotime($_POST['from_date']));
			$response->dateTo = date('Y-m-d H:i:s', strtotime($_POST['to_date'] . '+1 day'));
			$response->costType = $_POST['cost_type'];
			/* if (!empty($_POST['cost_type']) && $_POST['cost_type'] == 'B2C') {
				$cargoProcessJobType = $_POST['cost_type'];
				$sql = 'SELECT * FROM cargo_process_job WHERE `type` IN (' . CargoProcessJob::NORMAL_JOB . ', ' . CargoProcessJob::PICKUP_JOB . ', ' . CargoProcessJob::PALLET_JOB . ') AND `status` = ' . CargoProcessJob::PROCESSDONE . ' AND `user_id` = ' . $org->id . ' AND `comp_date` >= "' . $dateFrom . '" AND `comp_date` <= "' . $dateTo . '"';
				$jobs = CargoProcessJob::model()->findAllBySql($sql);
				$data = [];
				$fileName=Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'cargo_cost' . DIRECTORY_SEPARATOR. 'cargo_cost_' . $org->name . '_' . $dateFrom . '-' . $dateTo . '.xlsx';
				$xls = new oExcel;
				$i = 1;
				$xls->addRow($i++, ['Consignment No', 'Description', 'Freight fee', 'EXTRA service', 'EXTRA FEE', 'Total', 'Gst']);
				$xls->output($fileName, null, false);
				$response->fileName = $fileName;
			} */
			
			echo json_encode($response);
		}
	}

	public function actionDownloadCost()
	{
		if (!empty($_GET)) {
			$costType = $_GET['costType'];
			$dateFrom = $_GET['dateFrom'];
			$dateTo = $_GET['dateTo'];
			$org = Org::model()->findByPk(Yii::app()->user->org);
			if (!empty($costType) && $costType == 'B2C') {
				$sql = 'SELECT * FROM cargo_process_job WHERE `type`= ' . CargoProcessJob::NORMAL_JOB . ' AND `status` = ' . CargoProcessJob::PROCESSDONE . ' AND `driver_id` = ' . $org->id . ' AND `comp_date` >= "' . $dateFrom . '" AND `comp_date` < "' . $dateTo . '"';
				$jobs = CargoProcessJob::model()->findAllBySql($sql);
				$data = [];
				foreach ($jobs as $job) {
					$key = date('d/m/Y', strtotime($job->comp_date));
					$jobRelations = CargoProcessJobRelations::model()->findAllByAttributes(['job_id' => $job->id, 'active' => 1]);
					if (!empty($jobRelations)) {
						foreach ($jobRelations as $jobRelation) {
							$cargoProcess = CargoProcess::model()->findByPk($jobRelation->cargo_process_id);
							if (!empty($cargoProcess)) {
								$shipment = ImParcel::model()->findByPk($cargoProcess->shipment_id);
								if (!empty($shipment)) {
									$temp = new stdClass;
									$temp->hbn = $shipment->hbn;
									$temp->description = '';
									if (!empty($cargoProcess->mdata['systemCost'])) {
										$temp->freightFee = $cargoProcess->getSystemBaseCost();
									} else {
										$cost = $cargoProcess->getCargoProcessFeeReCal();
										$temp->freightFee = $cargoProcess->getSystemBaseCost();
									}
									$temp->freightFee = number_format(floatval($temp->freightFee), 2, '.', '');
									$temp->extraService = '';
									$temp->extraFee = '';
									$temp->total = $temp->freightFee;
									$temp->gst = number_format(floatval($temp->total * 0.1), 2, '.', '');
									if(array_key_exists($key, $data)) {
										array_push($data[$key], $temp);
									} else {
										$data[$key] = array($temp);
									}
								}
							}
						}
					}
				}
				if (!is_dir(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'cargo_cost')) {
					mkdir(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'cargo_cost');
				}
				$fileName=Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'cargo_cost' . DIRECTORY_SEPARATOR. 'cargo_cost_B2C_' . $org->name . '_' . date('Y-m-d', strtotime($dateFrom)) . '-' . date('Y-m-d', strtotime($dateTo . '-1 day')) . '.xlsx';
				$xls = new oExcel;
				$i = 1;
				$xls->addRow($i++, ['Supplier ID', $org->id]);
				$xls->addRow($i++, ['Consignment No', 'Description', 'Freight fee', 'EXTRA service', 'EXTRA FEE', 'Total', 'Gst']);
				$subTotalBase = 0;
				$subTotalAll = 0;
				$subTotalGst = 0;
				foreach ($data as $key => $values) {
					$xls->addRow($i++, [$key]);
					foreach ($values as $value) {
						$xls->addRow($i++, [$value->hbn, $value->description, $value->freightFee, $value->extraService, $value->extraFee, $value->total, $value->gst]);
						$subTotalBase += $value->freightFee;
						$subTotalAll += $value->total;
						$subTotalGst += $value->gst;
					}
					$xls->addRow($i++, []);
				}
				//$xls->addRow($i++, []);
				$xls->addRow($i++, ['SUBTOTAL', '', $subTotalBase, '', '', $subTotalAll, $subTotalGst]);
				$xls->addRow($i++, ['TOTAL', '', '', '', '', '', $subTotalAll+$subTotalGst]);
				$xls->output($fileName, null, true);
				unlink($fileName);
			} elseif (!empty($costType) && $costType == 'B2B') {
				$sql = 'SELECT * FROM cargo_process_job WHERE `type` IN (' . CargoProcessJob::B2B_JOB . ', ' . CargoProcessJob::FBA_JOB . ') AND `status` = ' . CargoProcess::PROCESSDONE . ' AND `driver_id` = ' . $org->id . ' AND `comp_date` >= "' . $dateFrom . '" AND `comp_date` < "' . $dateTo . '"';
				$jobs = CargoProcessJob::model()->findAllBySql($sql);
				$data = [];
				foreach ($jobs as $job) {
					$temp = new stdClass;
					$temp->job_no = $job->job_no;
					$temp->isa = '';
					$temp->pallet_number = $job->getTotalPltNo();
					$temp->pallet_rate = 0;
					$temp->freightFee = 0;
					$key = date('d/m/Y', strtotime($job->comp_date));
					$jobRelations = CargoProcessJobRelations::model()->findAllByAttributes(['job_id' => $job->id, 'active' => 1]);
					if (!empty($jobRelations)) {
						foreach ($jobRelations as $jobRelation) {
							if (empty($jobRelation->active)) {
								continue;
							}
							$cargoProcess = CargoProcess::model()->findByPk($jobRelation->cargo_process_id);
							if (!empty($cargoProcess)) {
								$shipment = ImParcel::model()->findByPk($cargoProcess->shipment_id);
								if (!empty($shipment)) {
									//$amazonInfo = AmazonInfo::model()->findByAttributes(['fid' => $job->id, 'model'=>'CargoProcessJob']);
									$temp->isa = !empty($cargoProcess->mdata['booking_ref']) ? $cargoProcess->mdata['booking_ref'] : '';
									$palletRate = 0;
									if (!empty($cargoProcess->mdata['orgRateId'])) {
										$costZone = OrgRateService::getShipmentCostZone($cargoProcess->mdata['orgRateId'], $shipment);
										if (isset($costZone['cost zone'])) {
											$zoneRate = ZoneRate::model()->findByAttributes(array('rate_id' => $cargoProcess->mdata['orgRateId'], 'zone' => $costZone['cost zone']));
											if (!empty($zoneRate)) {
												$palletRate = $zoneRate->perkg;
											}
										}
									}
									$temp->pallet_rate = $palletRate;
									if (!empty($cargoProcess->mdata['systemCost'])) {
										$temp->freightFee += $cargoProcess->getSystemBaseCost();
									} else {
										$cost = $cargoProcess->getCargoProcessFeeReCal();
										$temp->freightFee += $cargoProcess->getSystemBaseCost();
									}
								}
							}
						}
					}
					$temp->other_item = '';
					$temp->extra_fee = '';
					$temp->freightFee = number_format(floatval($temp->freightFee), 2, '.', '');
					$temp->total = $temp->freightFee;
					$temp->gst = number_format(floatval($temp->total * 0.1), 2, '.', '');
					if(array_key_exists($key, $data)) {
						array_push($data[$key], $temp);
					} else {
						$data[$key] = array($temp);
					}
				}
				if (!is_dir(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'cargo_cost')) {
					mkdir(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'cargo_cost');
				}
				$fileName=Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'cargo_cost' . DIRECTORY_SEPARATOR. 'cargo_cost_B2B_' . $org->name . '_' . date('Y-m-d', strtotime($dateFrom)) . '-' . date('Y-m-d', strtotime($dateTo . '-1 day')) . '.xlsx';
				$xls = new oExcel;
				$i = 1;
				$xls->addRow($i++, ['Supplier ID', $org->id]);
				$xls->addRow($i++, ['DATE', 'C-JOB', 'ISA', 'PALLET NUMBER', 'PALLET RATES', 'FREIGHT FEE', 'OTHER ITEM', 'EXTRA FEE', 'TOTAL', 'GST']);
				$subTotalBase = 0;
				$subTotalAll = 0;
				$subTotalGst = 0;
				foreach ($data as $key => $values) {
					$xls->addRow($i++, [$key]);
					foreach ($values as $value) {
						$xls->addRow($i++, ['', $value->job_no, $value->isa, $value->pallet_number, $value->pallet_rate, $value->freightFee, $value->other_item, $value->extra_fee, $value->total, $value->gst]);
						$subTotalBase += $value->freightFee;
						$subTotalAll += $value->total;
						$subTotalGst += $value->gst;
					}
					$xls->addRow($i++, []);
				}
				$xls->addRow($i++, ['SUBTOTAL', '', '', '', '', $subTotalBase, '', '', $subTotalAll, $subTotalGst]);
				$xls->addRow($i++, ['Fuel Levy %']);
				$xls->addRow($i++, ['TOTAL', '', '', '', '', '', '', '', '', $subTotalAll+$subTotalGst]);
				$xls->output($fileName, null, true);
				unlink($fileName);
			}
		}	
	}
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = CargoProcess::model()->findByPk($id);
		if ($model === null)
			throw new CHttpException(404, 'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'ex-consol-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
