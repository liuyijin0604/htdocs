<?php
class rayCommand extends CConsoleCommand {
	private $db, $args, $tmp,$parent;
	private $debug = false;

	public function run($args) {
		$this->db = Yii::app()->getDb();
		$this->args = $args;
		$this->tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;

		foreach($this->args as $ag){
			if($ag == '-d') $this->debug = true;
		}
		if(!empty($args[0]) && method_exists($this, $args[0])){
			$this->{$args[0]}();
		}
	}

	public function testTntBne()
	{
		$model = TntAPI::getTntInterface('bne');
		$result = $model->genTNTConsignmentNo();
		print_r($model);
		print_r($result);
	}

	public function testSearchFakEmail()
	{
		$no = "C19071608SZX";
		$consol = Consol::model()->find('no = :no',[':no' => $no]);
		$imparcelService = new ImParcelService();
		$iEmail = $imparcelService->searchFakEamilAdr($consol);
		print_r($iEmail);
	}

	public function testFakShipments()
	{
		$imparcelService = new ImParcelService();
		$imparcelService->sendFakEmailDaily();
		print_r('successsss');
		// print_r(array_column($shipments, 'status'));
		// echo count($shipments);
	}

	public function testImportConsol()
	{
		print_r('successsss');
		
		$xls = new oExcel;
		$xls->load('C:\xampp\htdocs\hvlv_branch\ims\imco_consol_import_template_217-92198223.xlsx');
		$data = $xls->getAll();
		if (strpos('WarehouseAWB NoAirlineFlight No.POLPODETDETAAWB weightChargable Wt.B/L pcsShipperShipper addressConsigneeConsignee addressPMC(1)/AKE(2)/Loose(3)Consignment NumbersBag',implode('', $data[1])) != 0) {
						$err[] = "template wrong";
					}
		if (!empty($err)) {
			$resp['msg'] = implode(';', $err);
			$resp['done'] = false;
			echo json_encode($resp);
			return;
		}
		unset($data[1]);
		$last_consolno = 0;

		foreach ($data as $i => $d) {
			if (empty($d[17])) {
				continue;
			}

			if ($last_consolno != 1) {
				if (empty($d[5])) {
					$d[5] = null;    // POL is empty 
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
				if(!empty(trim($d[1]))){
					if(!preg_match('/106|218|530|811|529/i',trim($d[1]))){
						$depot = self::getDepotId(trim($d[1]));
					}else{
						$depot = trim($d[1]);
					}
				}
				$tasks[1] = array(
				'dpt_id' => $depot,
				'service' => 10,//10 air 20 sea
				'awb' => trim($d[2]),
				'airline' => trim($d[3]),
				'flight' => trim($d[4]),
				'pol' => trim($d[5]),
				'pod' => trim($d[6]),
				'etd' => oExcel::toDate($d[7]),
				'eta' => oExcel::toDate($d[8]),
				'ignore'=> "",
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
			if(preg_match('/[\t ]+/', trim($d[17]))){
				$ts = preg_split('/[\t ]+/', trim($d[17]));
				$h = trim($ts[0]);
			}

			//bagTag
			$bagTag = trim($d[18]);

			$p = ImParcel::model()->find('(hbn = :h OR ref = :h) AND (cbwf&:cbwf)=0', [':h' => $h,':cbwf'=> ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
			if (empty($p)) {
				$err[] = "line " . $i . " Consignment Numbers ".trim($d[17])." not found!";
				continue;
			}
			
			$tasks[1]['consignment'][] = array(
				'ref' => $h,
				'bagTag' => $bagTag,//bagtag
			);
		
		}
		if (!empty($err)) {
			$resp['msg'] = implode('<br />', $err);
			$resp['done'] = false;
			echo json_encode($resp);
			return;
		}
		foreach ($tasks as $temp_task) {

			$model = new ImcoConsol;
			$errors=[];
			if (empty($temp_task)) {
				continue;
			}else{
				$selected_services=[];
				$ignore_depot = empty($temp_task['ignore'])?0:1;

				foreach ($temp_task['consignment'] as $temp_label){
					$h = $temp_label['ref'];
					if(preg_match('/[\t ]+/',$temp_label['ref'])){
						$ts = preg_split('/[\t ]+/', $temp_label['ref']);
						$h = trim($ts[0]);
					}
					$p = ImParcel::model()->find('(hbn = :h OR ref = :h) AND (cbwf&:cbwf)=0', [':h' => $h,':cbwf'=> ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
					$model->checkImparcel($p,$errors, $selected_services,$h,$_POST['ImcoConsol']['dpt_id'],$ignore_depot);
				}

				if (sizeof($selected_services)>=1) {
					asort($selected_services);
					$selected_services= array_reverse($selected_services, true);
					$the_service=key($selected_services)-(key($selected_services)&32);

				}
				foreach ($temp_task['consignment'] as $temp_label){
					$h = $temp_label['ref'];
					if(preg_match('/[\t ]+/',$temp_label['ref'])){
						$ts = preg_split('/[\t ]+/', $temp_label['ref']);
						$h = trim($ts[0]);
					}

					$p = ImParcel::model()->find('(hbn = :h OR ref = :h)', [':h' => $h]);
					$model->checkServiceErrors($p,$the_service,$errors,$h);
				}

				if (!empty($errors)) {
					$model->addError('shipment', implode(';', $errors));
					$this->ajaxResult($model);
				}

				$model->dpt_id=$temp_task['dpt_id'];
				$model->service=$temp_task['service'];
				$model->awb=$temp_task['awb'];
				$model->airline=$temp_task['airline'];
				$model->flight=$temp_task['flight'];
				$model->pol=$temp_task['pol'];
				$model->pod=$temp_task['pod'];
				$model->etd=$temp_task['etd'];
				$model->eta=$temp_task['eta'];
				$model->awb=$temp_task['awb'];
				$model->mdata['awb_wt'] = $temp_task['mdata[awb_wt]'];
				$model->mdata['cgb_wt'] = $temp_task['mdata[cgb_wt]'];
				$model->mdata['b&l_pcs'] = $temp_task['mdata[b&l_pcs]'];
				$model->mdata['cnor'] = $temp_task['mdata[cnor]'];
				$model->mdata['cnor_addr'] = $temp_task['mdata[cnor_addr]'];
				$model->mdata['cnee'] = $temp_task['mdata[cnee]'];
				$model->mdata['cnee_addr'] = $temp_task['mdata[cnee_addr]'];
				$model->mdata['air_type'] = $temp_task['mdata[air_type]'];
				$model->status = 10;
				$model->save();

				$transaction=Yii::app()->db->beginTransaction();
				try {
					foreach ($temp_task['consignment'] as $temp_label) {
						$h = $temp_label['ref'];
						if (!empty($temp_label['ref'])) {
							$ts = preg_split('/[\t ]+/', $temp_label['ref']);
							$h = trim($ts[0]);
							$wt = trim($ts[1]);
						}
						$p = ImParcel::model()->find('(hbn = :h OR ref = :h) AND cbwf&:cbwf=0', [':h' => $h,':cbwf'=> ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
						if(!empty($model->id))
						{
							$model->putImparcelIntoConsol($p,@$wt);
						}


						if(!empty($temp_label['bagTag'])){
							$bag = $temp_label['bagTag'];
							if(!empty($bag) && strlen($bag)<=45){
								$objBag = ImportBag::model()->find('bag_tag = :bag_tag',[':bag_tag'=>$bag]);
								if(empty($objBag)){
									$objBag = new ImportBag();
									$objBag->user_id= 1;
									$objBag->bag_tag = $bag;
									$objBag->created = date("Y-m-d H:i:s");
									$objBag->save();
								}

								//bagtag
								$bagTag = ImportBagTag::model()->find('shipment_id = :shipment_id',[':shipment_id'=>$p->id]);
								if(empty($bagTag)){
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
					
				}
				catch(Exception $ex){
					$transaction->rollback();
					throw $ex;
				}
			}
		}
		echo json_encode($resp);
	}

	public function testDeshipmentSearch(){
		$criteria = new CDbCriteria();
		$criteria->addCondition('( t.connote_no like "F3X%" or t.connote_no like "33XHE%" or t.connote_no like "33XJM%" or t.connote_no like "33G7K%" or t.connote_no like "3CK20%" or t.connote_no like "33YVW%" or t.connote_no like "33YVQ%" or (t.connote_no like "34HWK%") or (t.connote_no like "34VJ%" ) or (t.connote_no like "34QJ%") or ((t.connote_no like "33MHJ%" or t.connote_no like "WQF%" or t.connote_no like "WQE%" or t.connote_no like "WQC%") )  ) and t.scan_time  > "2022-03-01" and t.connote_no not like "34WMP%"');
		$gapsShipments = GatepassShipment::model()->findAll($criteria);
		if(!empty($gapsShipments)){
			foreach ($gapsShipments as $key => $gapsShipment) {
				echo ($gapsShipment->id);
			}
		}
		else{
			echo "not found";
		}
		
	}

	public function inputShipmentHbnTosDe()
	{
		$shipmentHbnStr = $this->prompt('HBN: ');
		$criteria = new CDbCriteria();
		// $shipmentHbnStr = "'33D98315914501000935001','33D98315321401000935000','33D98314616301000935001','33D98315906801000935003','33D98315905401000935006','33D98314781901000935006','33D98314761901000935002','33D98314781501000935008','33D98315906601000935009','33D98314782401000935000'";
		// $shipmentHbnStr = "'TMN1474027968','TMN1474028042','TSN1474027884'";
		$criteria->addCondition(" hbn in ({$shipmentHbnStr}) ");
		$shipments = Shipment::model()->findAll($criteria);
		// $shipments = Shipment::model()->findAll("hbn in (".$shipmentHbnStr.")");
		if(!empty($shipments)){
			foreach ($shipments as $key => $shipment) {
				$shipment->status = 98;
				if($shipment->update('status')){
					$hbnStr .= " ".$shipment->hbn;
				}
			}
			echo $hbnStr."has been done";
		}
		else{
			echo "Can not find";
		}
	}

	public function changeShipmentStatusDe()
	{
		$hbnStr = "";
		$criteria = new CDbCriteria();
		$shipmentHbnStr = "'33D98315914501000935001','33D98315321401000935000','33D98314616301000935001','33D98315906801000935003','33D98315905401000935006','33D98314781901000935006','33D98314761901000935002','33D98314781501000935008','33D98315906601000935009','33D98314782401000935000'";
		// $shipmentHbnStr = "'TMN1474027968','TMN1474028042','TSN1474027884'";
		$criteria->addCondition(" hbn in ({$shipmentHbnStr}) ");
		$shipments = Shipment::model()->findAll($criteria);
		// $shipments = Shipment::model()->findAll("hbn in (".$shipmentHbnStr.")");
		if(!empty($shipments)){
			foreach ($shipments as $key => $shipment) {
				$shipment->status = 98;
				if($shipment->update('status')){
					$hbnStr .= " ".$shipment->hbn;
				}
			}
			echo $hbnStr."has been done";
		}
		else{
			echo "Can not find";
		}
	}

	public function copyOrgFlexibleRate()
	{
		$fromOrgId = 4063;
		$toOrgId = 4597;

		$fromOrgFlexibleRate= new OrgFlexibleRate();
		$fromOrgFlexibleRates = $fromOrgFlexibleRate->findAll('org_id = :orgId and fid=0 and model=""', array(':orgId' => $fromOrgId));
		foreach ($fromOrgFlexibleRates as $key => $value) {
			$orgFlexibleRate = new OrgFlexibleRate();
			$orgFlexibleRate->item =    $value->item;
			$orgFlexibleRate->amount =  $value->amount;
			$orgFlexibleRate->unit =    $value->unit;
			$orgFlexibleRate->minimum = $value->minimum;
			$orgFlexibleRate->branch =  $value->branch;
			$orgFlexibleRate->fid = $value->fid;
			$orgFlexibleRate->model = $value->model;
			$orgFlexibleRate->cargo =   $value->cargo;
			$orgFlexibleRate->service = $value->service;
			$orgFlexibleRate->org_id =  $toOrgId;
			$orgFlexibleRate->user_id=  $value->user_id;
			$orgFlexibleRate->create =  $value->create;
			$orgFlexibleRate->rebate =  $value->rebate;
			$orgFlexibleRate->criteria = $value->criteria;
			$orgFlexibleRate->start_date = $value->start_date;
			$orgFlexibleRate->save();
		}

		$fromOrgFlexibleRateBk= new OrgFlexibleRateBk();
		$fromOrgFlexibleRateBks = $fromOrgFlexibleRateBk->findAll('org_id = :orgId and fid=0 and model=""', array(':orgId' => $fromOrgId));
		foreach ($fromOrgFlexibleRateBks as $key => $value) {
			$orgFlexibleRateBk = new OrgFlexibleRateBk();
			$orgFlexibleRateBk->item =    $value->item;
			$orgFlexibleRateBk->amount =  $value->amount;
			$orgFlexibleRateBk->unit =    $value->unit;
			$orgFlexibleRateBk->minimum = $value->minimum;
			$orgFlexibleRateBk->branch =  $value->branch;
			$orgFlexibleRateBk->cargo =   $value->cargo;
			$orgFlexibleRateBk->service = $value->service;
			$orgFlexibleRateBk->org_id =  $toOrgId;
			$orgFlexibleRateBk->user_id=  $value->user_id;
			$orgFlexibleRateBk->create =  $value->create;
			$orgFlexibleRateBk->rebate =  $value->rebate;
			$orgFlexibleRateBk->version = $version;
			$orgFlexibleRateBk->create_user = User::currentUserID();
			$orgFlexibleRateBk->start_date = $value->start_date;
			$orgFlexibleRateBk->criteria = $value->criteria;
			$orgFlexibleRateBk->save();
		}
	}

	public function changeChargecode()
	{
		$hbnStr = "";
		$criteria = new CDbCriteria();
		$shipmentHbnStr = "'8214935820','10240080465','10239982631'";
		// $shipmentHbnStr = "'TMN1474027968','TMN1474028042','TSN1474027884'";		
		$criteria->addCondition("t.	status != ".ImParcel::STATE_CANCELLED);
		$criteria->addCondition(" hbn in ({$shipmentHbnStr}) ");
		$shipments = Shipment::model()->findAll($criteria);
		// $shipments = Shipment::model()->findAll("hbn in (".$shipmentHbnStr.")");
		if(!empty($shipments)){
			foreach ($shipments as $key => $shipment) {
				$shipment->mdata['chargecode'] = 7546;
				if($shipment->update('meta')){
					$hbnStr .= " ".$shipment->hbn;
				}
			}
			echo $hbnStr." has been done";
		}
		else{
			echo "Can not find";
		}
	}

	public function changeDepot()
	{
		$hbnStr = "";
		$criteria = new CDbCriteria();
		$shipmentHbnStr = "'8214935820','10240080465','10239982631'";
		// $shipmentHbnStr = "'TMN1474027968','TMN1474028042','TSN1474027884'";		
		$criteria->addCondition("t.	status != ".ImParcel::STATE_CANCELLED);
		$criteria->addCondition(" hbn in ({$shipmentHbnStr}) ");
		$shipments = Shipment::model()->findAll($criteria);
		// $shipments = Shipment::model()->findAll("hbn in (".$shipmentHbnStr.")");
		if(!empty($shipments)){
			foreach ($shipments as $key => $shipment) {
				$shipment->ddpt_id = 218;
				if($shipment->update('ddpt_id')){
					$hbnStr .= " ".$shipment->hbn;
				}
			}
			echo $hbnStr." has been done";
		}
		else{
			echo "Can not find";
		}
	}

	public function sendInvoiceDaily()
	{
		$tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		if (!file_exists($tempDirectory)) {
			mkdir($tempDirectory);
		}
		
		//exclude air/sea
		$invoices = Invoice::model()->findAll(
			'dpmt in (10,40,50) AND status = :status',
		[':status' => Invoice::INVOICE_STATUS_PENDING]
		);//,40,36,37,35,39IM28634
		foreach ($invoices as $invoice) {
			Yii::app()->name = 'TLA';
			$invoice->getTotal();
			$invoice->save();
			// 3pl cust set statement feq
			// and pcaw set not send invoice
			if ($invoice->dpmt == 40 && !empty($invoice->cust->extra['3pl_statement_feq']) && (empty($invoice->cust->extra['invoice-feq']) || $invoice->cust->extra['invoice-feq'] == 'no')) {
				$invoice->status = Invoice::INVOICE_STATUS_POSTED;
				$invoice->posted = date('Y-m-d');
				$invoice->update(['status', 'posted']);
				$invoice->closeInvoice();
				$this->_sendInvoice($invoice, $tempDirectory);
				continue;
			}

			$this->_sendInvoice($invoice, $tempDirectory);
			$oldInvoiceNo = Invoice::checkNewInvoiceNo($invoice->no);
			$oldInvoice = Invoice::model()->find('no = :no', [':no' => trim($oldInvoiceNo)]);
			if (!empty($oldInvoice) && $oldInvoice->to_id != $invoice->to_id) {
				$this->_sendInvoice($invoice, $tempDirectory, false, true);
			}
		}
		AppHelper::unlinkRecursive($tempDirectory);
	}

	private function _sendInvoice($invoice, $tempDirectory, $includeOld = true, $showBalance = false)
	{
		$fileName = $tempDirectory.DIRECTORY_SEPARATOR.'Invoice_'.$invoice->no.'.pdf';
		$excelFile = $invoice->exportExcelInvoice($tempDirectory.DIRECTORY_SEPARATOR,false);
		$fileName2 = '';
		// echo $invoice->no;
		if ($invoice->type != 60 && $invoice->type != 104) {
			oPDF::renderPDF('invoice', ['inv'=>$invoice], 2, $fileName);
		} else {
			if ($showBalance) $_GET['bal'] = true;
			$f1 = tempnam(Yii::app()->basePath."/runtime", "ivp");
			$f2 = tempnam(Yii::app()->basePath."/runtime", "ivp");
			oPDF::renderPDF('invoice', ['inv'=>$invoice], 2, $f1);
			oPDF::renderPDF('invoice_detail', ['inv'=>$invoice], 2, $f2);
			oPDF::mergePDF([$f1, $f2], 2, true, $fileName);
		}
		$emailLog = new Emailog();
		$emailLog->type = Emailog::INVOICE;
		$emailLog->fid = $invoice->id;
		$emailLog->dt = date('Y-m-d H:i:s');
		$emailLog->to_id = $invoice->to_id;
		$emailLog->status = 10;

		$oldInvoiceNo = Invoice::checkNewInvoiceNo($invoice->no);
		// echo $oldInvoiceNo."\n";
		if (!empty($oldInvoiceNo) && $includeOld) {
			$emailLog->isNewInvoice = false;
			$fileName2=$tempDirectory.DIRECTORY_SEPARATOR.'Old_Invoice_'.$oldInvoiceNo.'.pdf';
			$oldInvoice=Invoice::model()->find('no=:no', [":no"=>trim($oldInvoiceNo)]);
			if ($oldInvoice->type != 60 && $oldInvoice->type != 104) {
				oPDF::renderPDF('invoice', ['inv'=>$oldInvoice], 2, $fileName2);
			} else {
				$f1 = tempnam(Yii::app()->basePath."/runtime", "ivp");
				$f2 = tempnam(Yii::app()->basePath."/runtime", "ivp");
				oPDF::renderPDF('invoice', ['inv'=>$oldInvoice], 2, $f1);
				oPDF::renderPDF('invoice_detail', ['inv'=>$oldInvoice], 2, $f2);
				oPDF::mergePDF([$f1, $f2], 2, true, $fileName2);
			}
			$emailLog->prepTemplate();
			$addingNotice="<p><b>This Invoice is to replace the old Invoice:".$oldInvoiceNo."</b></p>";
			$emailLog->tpl->assignThese([
				// 'OLD_INVOICE' => $addingNotice,
				'OLD_INVOICE' => '',
			]);
		} else {
			$emailLog->prepTemplate();
		}
		$emailLog->subject = $emailLog->tpl->subject;
		$emailLog->body = $emailLog->tpl->getContent();
		if ($emailLog->InvoiceSend($fileName, $fileName2, $excelFile) && $includeOld) {
			$invoice->status = Invoice::INVOICE_STATUS_POSTED;
			$invoice->posted = date('Y-m-d');
			$invoice->update(['status', 'posted']);
			$invoice->closeInvoice();
			$this->log2file('Invoice '.$invoice->no.'send!', 'invoice_sending');
		} else {
			$this->log2file('Invoice '.$invoice->no.'fail!', 'invoice_sending');
		}
	}

	public function postEdInvoice(){
		$invoices = Invoice::model()->findAll(
			'dpmt = 30 AND type = 50 AND status = :status AND date < DATE_SUB(CURDATE(), INTERVAL 1 WEEK)',
		[':status' => Invoice::INVOICE_STATUS_PENDING]
		);

		foreach ($invoices as $invoice) {
			$invoice->status = Invoice::INVOICE_STATUS_POSTED;
			$invoice->posted = date('Y-m-d');
			$invoice->closeInvoice(false);
			$invoice->save();
		}
	}

	public function sendFakEmail()
    {
        // [$consols,$shipments] = $this->getFakShipmentsSendingReport();
        $toEmail = 'ray.tang@toplogistics.com.au';
        $container_no = "CMAU6199403";
		$consol = Consol::model()->find('container_no = :container_no',[':container_no' => $container_no]);
        $cc = 'frank.liu@toplogistics.com.au'; 

        print_r($consol->container_no.'/n');
        // $toEmail = $this->searchFakEamilAdr($consol);
        // print_r($this->searchFakEamilAdr($consol).'/n');
        $htmlStr = '';
        $shipments = [];
        foreach ($consol->shipments as $key => $shipment) {
            if($shipment->status==ImParcel::DELIVERED || $shipment->status==ImParcel::STATE_SUB_UBM){
                continue;
            }
            else if(!empty($shipment->gatepass_shipment->parent_id) && !empty($shipment->cargo_process)){
                continue;
            }
            else{
                $shipments[]=$shipment;
            }
        }
        if(isset($shipments)){
            $htmlStr .= $this->printFakEmailContent($shipments);   
        }
        
        $subject = 'Container: '.$consol->container_no.'  cargo left over, storage start: '.$consol->etd;
        $emailService = new EmailService();
        $emailService->sendNormalEmail($toEmail,$subject,$htmlStr,$cc);                 
    }



	public static function getDepotId($code){
		switch($code){
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

	public function testChatgptVaildAddress()
	{
		// ChatgptAPI::requestResidentialAddress35API("68 Nuwarra Rd, Moorebank NSW 2170 ");
		ChatgptAPI::requestResidentialAddress35API("2/16 Muller road Boondall QLD 4034 ");
		// ChatgptAPI::requestResidentialAddress35API("613/1B Lemon Tree Avenue Melrose Park NSW 2114 ");
		// ChatgptAPI::requestResidentialAddress35API("E302/548-568 Canterbury rd Campsie NSW 2194 ");
		// ChatgptAPI::requestResidentialAddress35API("20 Bluegum Drive CAMIRA QLD 4300 ");
		// ChatgptAPI::requestResidentialAddress35API("9 Cowells Lane Ermington NSW 2115 ");
		// ChatgptAPI::requestResidentialAddress35API("7 Logan St Adelaide Adelaide SA 5000 ");
		// ChatgptAPI::requestResidentialAddress35API("38/8 Henry Kendall St Franklin NSW 2913 ");
		// ChatgptAPI::requestResidentialAddress35API("2 Donnelly crt Kealba VIC 3021 ");
		// ChatgptAPI::requestResidentialAddress35API("3/36 Brunei Cres, HEIDELBERG WEST VIC 3081 ");
		// ChatgptAPI::requestResidentialAddress35API("3/36 Brunei Cres, HEIDELBERG WEST VIC 3081 ");
		// ChatgptAPI::requestResidentialAddress35API("15A Wade Street, JOONDANNA TAS 6060 ");
		// ChatgptAPI::requestResidentialAddress35API("5 Klem Road Ardross TAS 6153 ");
		// ChatgptAPI::requestResidentialAddress35API("59 Albion st UMINA BEACH NSW 2257 ");
		// ChatgptAPI::requestResidentialAddress35API("19 Mavista Ave Glen Waverley VIC 3150 ");
		// ChatgptAPI::requestResidentialAddress35API("16 Bright St Marrickville NSW 2204 ");
		// ChatgptAPI::requestResidentialAddress35API("14 Garnaut Avenue Pooraka SA 5095 ");
		// ChatgptAPI::requestResidentialAddress35API("52/35 Balmoral St Waitara NSW 2077 ");
		// ChatgptAPI::requestResidentialAddress35API("22 Raimonde Rd Eastwood NSW 2122 ");
		// ChatgptAPI::requestResidentialAddress35API("72 Golden Bear Drive ARUNDEL QLD 4214 ");
		// ChatgptAPI::requestResidentialAddress35API("33 Rose Street SEFTON NSW 2162 ");

		return;
	}

	public function testChatgptCheckShipment()
	{
		$hbnStr = "";
		$criteria = new CDbCriteria();
		$shipmentHbnStr = "'33G7K374460201000935106'";
		$criteria->addCondition(" hbn in ({$shipmentHbnStr}) ");
		$shipment = Shipment::model()->find($criteria);
		// ChatgptAPI::checkShipmentResidAddreAPI($shipment,true);
		$answer = ChatgptAPI::checkShipmentResidAddreAPI($shipment);
		if (strpos($answer, "esidential") !== false) 
        { 
            print_r('residential!!!!!!!!!!');
        }
        else{
        	print_r("not residential!!!!!!!!!!");
        }
	}

	public function testChatgptCorrectAddress()
	{
		ChatgptAPI::validateShipmentAddreAPI("33 Rosa Street SEFTON NSW 2162 ", true);
	}

	public function testValidShipmentAddress()
	{
		// print_r(ChatgptAPI::getCorrectAddress("33 Rosecsfad Road SEFTON NSW 2162 ", false));
		print_r(ChatgptAPI::getCorrectAddress("33 Rosecsfad Road SEFTON NSW 2162 ", false));
	}

	public function testAuPostValid()
	{
		$apa= new AusPostAPI('syd', true, false);
		print_r($apa->validateAddress2("SEFTON", "NSW", "2163"));
	}

	public function testGoogleMapCorrectAddr()
	{
		print_r(GoogleMapApi::correctAddress("33 Rose St", "SEFTON", "NSW", "2162"));
	}

	public function testGoogleMapCorrectAddr1()
	{
		print_r(GoogleMapApi::correctAddress("33 Rose St", "SEFTON", "nsw", "2162", 'Australia', false, true));
	}

	public function getResidentialAddress()
	{
		$addresses = AddrResidential::model()->findAll('id<62');
		foreach ($addresses as $key => $address) {
			print_r($address->address." ".$address->suburb." ".$address->postcode."  ||  ");
		}
	}

	public function testFakEmailDaily123()
    {
    	$imparcelSevice = new ImParcelService();
        $emailArray = $imparcelSevice->getFakShipmentsSendingReport();
        $toEmail1 = 'ray.tang@toplogistics.com.au';
        // $toEmail1 = '';
        $cc = 'ray.tang@toplogistics.com.au'; 
        // $cc = 'ray.tang@toplogistics.com.au'; 
        // $cc = 'frank.liu@toplogistics.com.au'; 
        $htmlStr = '';
        $subject = ''; 
        $emailAddrs = [];
        $emailCop = '';
        foreach ($emailArray as $key => $email) {
             if($email->address != $emailCop){
                $emailCop=$email->address;
                $emailAddrs[]=$email->address;
             }
         } 
         $emailService = new EmailService();         
         foreach ($emailAddrs as $key => $email) {
            $htmlStr = "";
            $subject = 'Your Storage Information: '.date("Y-m-d");
            foreach ($emailArray as $key => $value) {
                if($email == $value->address){
                    // $toEmail1 = $email;                    
                    if(!empty($value->shipments)){
                        $htmlStr.= $imparcelSevice->printFakEmailContent($value->shipments, $value->consol);   
                    }
                    else{
                        $htmlStr.="Container No: ".$value->consol->container_no;
                    }

                }
                else{
                    break;
                }
                unset($emailArray[$key]);
            } 
            $emailService->sendNormalEmail($toEmail1,$subject,$htmlStr,$cc);          
            // $emailService->sendNormalEmail('ray.tang@toplogistics.com.au',$subject,$htmlStr,$cc);                      
                    
        }
        print_r('success');        
    }

    public function testSumConnotes(){
    	$emailService = new EmailService();
    	$emailService->sendConnoteRangeNotice();
    	print_r("Done");
    }

    public function testSystemSettingBrownways(){
    	$BrownwaysRates=SystemSetting::getBrownwaysInvoiceRates();
    	print_r($BrownwaysRates);
    }

    public function changeInvoiceStatus()
	{
		$noStr = "";
		$criteria = new CDbCriteria();
		$invoiceNumPP = "'IM705131','CA703904-1','IM712113','IM723943-1','IM722668-1','IM852670','OT855958','IM883465','IM883894','IM884743','WD886303','IM892909','IM894349','IM915136','IM917920','IM825973','IM827155','IM834076','IM836701','IM852223','IM852667','IM852679','WD857464','OT859285','WD886189','IM752563','IM752569','IM752614','IM752656','IM752638','IM752989','DT752971','IM752983','DT752968','IM815452-1','RT801319','WD818014','WD827938','WD848569'";
		// $invoiceNumPP = "'RT24777','OT24668'";
		$criteria->addCondition(" no in ({$invoiceNumPP}) ");
		$ppInvoices = Invoice::model()->findAll($criteria);
		// $shipments = Shipment::model()->findAll("hbn in (".$shipmentHbnStr.")");
		if(!empty($ppInvoices)){
			foreach ($ppInvoices as $key => $invoice) {
				$invoice->status = Invoice::INVOICE_STATUS_PARTIALLY_PAID;
				if($invoice->update('status')){
					$noStr .= " ".$invoice->no;
				}
			}
		}

		$criteria1 = new CDbCriteria();
		$invoiceNumOD = "'OT709627','IM727414','CA945763','CA945733','WD946861','IM947692'";
		// $invoiceNumOD = "'RT24777','OT24668'";
		$criteria1->addCondition(" no in ({$invoiceNumOD}) ");
		$odInvoices = Invoice::model()->findAll($criteria1);
		// $shipments = Shipment::model()->findAll("hbn in (".$shipmentHbnStr.")");
		if(!empty($odInvoices)){
			foreach ($odInvoices as $key => $invoice) {
				$invoice->status = Invoice::INVOICE_STATUS_OVERDUE;
				if($invoice->update('status')){
					$noStr .= " ".$invoice->no;
				}
			}
		}

		if(!empty($noStr)){
			echo $noStr."has been done";
		}
		else{
			echo "Can not find";
		}
	}

	public function deletePayInvById()
	{
		$payIdStr = $this->prompt('pay_id: ');
		$invIdStr = $this->prompt('inv_id: ');
		$criteria = new CDbCriteria();
		$criteria->addCondition("t.pay_id = ".$payIdStr);
		$criteria->addCondition("t.inv_id = ".$invIdStr);
		$payInv = PayInv::model()->find($criteria);
		if (!empty($payInv)){
			print_r($payInv->amount);
			$payInv->delete();
		}
		else{
			print_r("Can not find");
		}

		// $criteria->addCondition(" pay_id in ({$payIdsStr}) ");
		// $payInvs = Pay_inv::model()->findAll($criteria);
		// if(!empty($payInvs)){
		// 	foreach ($payInvs as $key => $payInv) {
		// 		$shipment->status = 98;
		// 		if($shipment->update('status')){
		// 			$hbnStr .= " ".$shipment->hbn;
		// 		}
		// 	}
		// 	echo $hbnStr."has been done";
		// }
		// else{
		// 	echo "Can not find";
		// }
	}

	public function insertPayInv()
	{
		$payId = $this->prompt('pay_id: ');
		$invId = $this->prompt('inv_id: ');
		$amount = $this->prompt('amount: ');
		$exrate = $this->prompt('exrate: ');
		$syncXero = $this->prompt('sync_xero: ');
		$payInv = new PayInv();
		$payInv->pay_id = $payId;
		$payInv->inv_id = $invId;
		$payInv->amount = $amount;
		$payInv->exrate = $exrate;
		$payInv->sync_xero = $syncXero;
		$payInv->transaction_date = date("Y-m-d");

		if ($payInv->save()){
			print_r("Done");
		}
		else{
			print_r("Not Done");
		}
	}

	public function changeImportShipmentRelationsLog(){
		$consolNoStr = $this->prompt('Consol No: ');
		$criteria = new CDbCriteria();
		$consol = Consol::model()->find('no = :no',[':no' => $consolNoStr]);

		// $criteria->addCondition(" no in ({$consolNoStr}) ");
		// $consols = Shipment::model()->findAll($criteria);

		$shipmentIds = array_column($consol->shipments, 'id');
		$shipmentIdStr = implode(',', $shipmentIds);
		$criteria = new CDbCriteria();
		$criteria->addCondition(" cid in ({$shipmentIdStr}) ");
		$importsShipmentRelations = ImportsShipmentRelations::model()->findAll($criteria);
		foreach ($importsShipmentRelations as $key => $relation) {
			$relation->is_changed_log = 1;
			$relation->save();
			print_r($relation->id."||");
		}
		print_r("Done");


	}

	public function testCheckCustomMail(){
		$importsMails = ImportsMail::model()->findAll('no in ("EM6337782","EM6252843")');
		$importsMailService = new ImportsMailService();
		foreach ($importsMails as $key => $mail) {
			$importsMailService->checkCustomMail($mail);
		}
		
	}

	public function manuallyCheckCustomMail(){
		$emailNo = $this->prompt('Email No: ');
		$criteria = new CDbCriteria();
		$criteria->addCondition(" no in ({$emailNo}) ");
		// $importsMails = ImportsMail::model()->findAll('no in ("EM6337782","EM6252843")');
		$importsMails = ImportsMail::model()->findAll($criteria);
		$importsMailService = new ImportsMailService();
		foreach ($importsMails as $key => $mail) {
			$importsMailService->checkCustomMail($mail);
		}
		echo "done";
	}

	public function checkCustomMailxToday(){
		$startDate = $this->prompt('From Date: ');
		$importsMails = ImportsMail::model()->findAll(" (from_email like :from_email or from_email like :from_email1 or from_email like :from_email2) and status = 10 and Date(create_time) >= :start_date and Date(create_time) <= :today",[":from_email"=>"DoNotReply@awe.gov.au", ":from_email1"=>"AutoEntry@agriculture.gov.au", ":from_email2"=>"DoNotReply@agriculture.gov.au", ":start_date" => $startDate, ":today" => date('Y-m-d')]);
		$importsMailService = new ImportsMailService();
		foreach ($importsMails as $key => $mail) {
			$importsMailService->checkCustomMail($mail);
			echo $mail->no."||";
		}
		echo "done";
		
	}

	public function sendCustomhtmlEmail()
	{
		$doNotMovereportCache = ReportCache::model()->find('create_time = :today and type=:type and status=1',[':today'=>date('Y-m-d',strtotime("-1 days"))." 00:00:00",":type"=>ReportCache::CustomAttHtmlMailTotalByDaily]);
		$str1 = "";
		if (!empty($doNotMovereportCache)) {
			$total = $doNotMovereportCache->mdata['total'];
			$dnmShipments = $doNotMovereportCache->mdata['shipments'];

			foreach ($dnmShipments as $key => $shipment)
			{
				$str1.="<tr>";
				$str1.="<td>&nbsp;&nbsp;&nbsp;".$shipment['hbn']."&nbsp;&nbsp;&nbsp;</td>";
				$str1.="<td>&nbsp;&nbsp;&nbsp;".$shipment['can']."&nbsp;&nbsp;&nbsp;</td>";
				$str1.="<td>&nbsp;&nbsp;&nbsp;".date('H:i:s ',strtotime($shipment['time']))."&nbsp;&nbsp;&nbsp;</td>";
				$str1.="</tr>";
			}

			$body = "<h1>".date('Y-m-d')." Item Record Shipments Total:".$total."</h1>";

			$emailService = new EmailService();
			$toEmail = 'ray.tang@toplogistics.com.au';
			$ccEmail = "weijt133@gmail.com";//"importscs@toplogistics.com.au;michelle@toplogistics.com.au;frank.liu@toplogistics.com.au;david.ren@toplogistics.com.au;";
			$body .= "<h1>Record Detail</h1><table><tr><th>HBN</th><th>Memo</th><th>Record Time</th></tr>".$str1."</table>";
			$subject = date("Y-m-d")." Item Record Report";
			$emailService->sendNormalEmail($toEmail,$subject,$body,$ccEmail);
			echo "Done";
		}		
	}

	public function testCheckCustomMailxToday(){
		// $importsMails = ImportsMail::model()->findAll(" (from_email like :from_email or from_email like :from_email1) and status = 10",[":from_email"=>"DoNotReply@awe.gov.au", ":from_email1"=>"AutoEntry@agriculture.gov.au" ]);
		$startDate = $this->prompt('From Date: ');
		$importsMails = ImportsMail::model()->findAll(" from_email like :from_email1 and status = 10 and Date(create_time) >= :start_date and Date(create_time) <= :today",[":from_email1"=>"AutoEntry@agriculture.gov.au", ":today" => date('Y-m-d'), ":start_date" => $startDate]);
		$importsMailService = new ImportsMailService();
		print_r(array_column($importsMails,'no'));
		foreach ($importsMails as $key => $mail) {
			$importsMailService->checkCustomMail($mail);
			echo $mail->no."||";
		}
		echo "done";
		
	}

	public function sendCustompdfEmail()
	{
		$pdfTypes = [ReportCache::DoNotMoveSumMailTotalByDaily,ReportCache::ReleasedDirectionMailTotalByDaily];
		$titles = ["Do Not Move", "Released Direction"];
		$i = 0;

		foreach ($pdfTypes as $key => $type) {
			$doNotMovereportCache = ReportCache::model()->find('create_time = :today and type=:type and status=1',[':today'=>"2023-09-08 00:00:00",":type"=>$type]);
			$str1 = "";
			if (!empty($doNotMovereportCache)) {
				$total = $doNotMovereportCache->mdata['total'];
				$dnmShipments = $doNotMovereportCache->mdata['shipments'];

				foreach ($dnmShipments as $key => $shipment)
				{
					$str1.="<tr>";
					$str1.="<td>&nbsp;&nbsp;&nbsp;".$shipment['hbn']."&nbsp;&nbsp;&nbsp;</td>";
					$str1.="<td>&nbsp;&nbsp;&nbsp;".$shipment['can']."&nbsp;&nbsp;&nbsp;</td>";
					$str1.="<td>&nbsp;&nbsp;&nbsp;".date('H:i:s ',strtotime($shipment['time']))."&nbsp;&nbsp;&nbsp;</td>";
					$str1.="</tr>";
				}

				$body = "<h1>".date('Y-m-d')." ".$titles[$i]." Total:".$total."</h1>";

				$emailService = new EmailService();
				$toEmail = 'ray.tang@toplogistics.com.au';
				$ccEmail = "weijt133@gmail.com";//"importscs@toplogistics.com.au;michelle@toplogistics.com.au;frank.liu@toplogistics.com.au;david.ren@toplogistics.com.au;";
				$body .= "<h1>Record Detail</h1><table><tr><th>HBN</th><th>Memo</th><th>Record Time</th></tr>".$str1."</table>";
				$subject = date("Y-m-d")." ".$titles[$i++]." Report";
				$emailService->sendNormalEmail($toEmail,$subject,$body,$ccEmail);
				echo "Done";
			}
		}
				
	}

	public function putOnRlsNumber($model){
		if (strpos($model->from_email, "AutoEntry@agriculture.gov.au") !== false) {
			if (!empty($model->attachments)) {
				$assignToNoOne = 0;
				$transaction = Yii::app()->db->beginTransaction();
				try {
					foreach ($model->attachments as $at) {
						if($at->mime != "text/html") continue;
						$d = Yii::app()->params['fileRepoPath'].DIRECTORY_SEPARATOR.substr($at->hash,0,2);
				        if(!is_dir($d)) mkdir($d);
				        try
				        {
				            move_uploaded_file($at->name, $d.DIRECTORY_SEPARATOR.$at->hash);
				        }catch(Exception $e)
				        {
				        	continue;
				        }

		            	$keyword = "/Released/i";
		            	$frPath = $at->getFile();
		            	$result = file_get_contents($frPath); 
		            	$pattern1 = '/\*BAB(\w+)\*/';
		            	// $pattern1 = '/Entry:\s*([A-Z0-9]+)/';
		            	$pattern2 = '/HAWB:(\w+)/';
		            	$pattern3 = '/HBOL:(\w+)/';

		            	preg_match('/<span\s+style="font-size:16pt">(.*?)<\/span>/i', $result, $title1);
		            	preg_match('/<span\s+style="font-size:12pt">(.*?)<\/span>/i', $result, $title2);
		            	print_r($title1);
		            	print_r($title2);

	                	// $keyword = "/{$pattern}/i";
	                	if (preg_match($keyword, @$title1[1], $key1) || preg_match($keyword, @$title2[1], $key2)) {
	                		$keys = empty($key1)?$key2:$key1;
	                	}
	                	else{
	                		continue;
	                	}
	                	if(preg_match($pattern1,$result,$e) && (preg_match($pattern2,$result,$m2) || preg_match($pattern3,$result,$m3)))
		                {
		                	$m = empty($m2)?$m3:$m2;
		                	$shipmentModel = ImParcel::model()->find(["condition"=>"hbn = :hbn","params"=>[":hbn"=>$m[1]],"order"=>" id desc"]);
		                	// $pphash = FileRepo::uploadHash($shipmentModel, 19);		

		                	if (empty($shipmentModel)) continue;   		
                	
							if (strpos($shipmentModel->can, "rls") === false) {
							   $shipmentModel->can = "rls".$shipmentModel->can;
							}							
							if (!$shipmentModel->update(['can'])) {
								echo $shipmentModel->hbn."not saved";								
							}
		                }                       
			           
					}
					$transaction->commit();
				} catch (Exception $ex) {
					print_r($ex);
					$transaction->rollback();
				}
			}

        }
	}

	public function memoAddRls(){
		// $importsMails = ImportsMail::model()->findAll(" (from_email like :from_email or from_email like :from_email1) and status = 10",[":from_email"=>"DoNotReply@awe.gov.au", ":from_email1"=>"AutoEntry@agriculture.gov.au" ]);
		$startDate = $this->prompt('From Date: ');
		$importsMails = ImportsMail::model()->findAll(" from_email like :from_email1 and status = 10 and Date(create_time) >= :start_date and Date(create_time) <= :today",[":from_email1"=>"AutoEntry@agriculture.gov.au", ":today" => date('Y-m-d'), ":start_date" => $startDate]);
		$importsMailService = new ImportsMailService();
		print_r(array_column($importsMails,'no'));
		foreach ($importsMails as $key => $mail) {
			$this->putOnRlsNumber($mail);
			echo $mail->no."||";
		}
		echo "done";
		
	}

	public function testChatGPT()
	{
		// $question = $this->prompt('Your Question: ');
		$question = "请你用英文，生成一篇写给澳洲海关清关的电子邮件给我可以吗？";
		$respond = ChatgptAPI::generateEssayToolAPI($question, false);
		print_r($respond);
		// do {
		// 	$respond = null;
		//   	$question = $this->prompt('Your Question: ');
		// 	$respond = ChatgptAPI::generateEssayToolAPI($question, false);
		// 	if (isset($respond)) {
		// 		print_r($respond);
		// 	}
		// } while (!empty($respond));

	}

	public function inactiveCust6MonthNoInv2(){
		$criteria = new CDbCriteria;
		// $criteria->limit = 5;
		$criteria->addCondition("status != 0"); // Add a condition
		$criteria->addCondition("type = 30");
		// $criteria->order = "id DESC"; // Order the results by create_time in descending order
		$today = date("Y-m-d");
		$sixMonthAgo = date("Y-m-d",strtotime("-6 month"));

		$custOrgs = Org::model()->findAll($criteria);
		foreach ($custOrgs as $key1 => $custOrg) {
			print_r("[ ".$custOrg->id.": ");
			$inact = true;
			if (!empty($custOrg->invs)) {
				print_r("Inovice: ");
				foreach ($custOrg->invs as $key2 => $inv) {
					$invPosted = date($inv->posted);
					if ($invPosted > $sixMonthAgo) {
						$inact = false;
						print_r($inv->id.": ".$invPosted.", ");
					}					
				}
			}

			if ($inact) {
				print_r($custOrg->id." Inactive");
				$custOrg->status = 0;
				$custOrg->extra['inactive_reasion']="IT00802282";
				$custOrg->update();
			}
			print_r(" ] ");
		}
	}

	public function inactiveUser60DayNoLogin(){
		$criteria = new CDbCriteria;
		$criteria->with = 'actionLogs';
		$criteria->addCondition('t.active = 1');
		$criteria->addCondition('t.type NOT IN (0,60,70,230,240,72,100)');
		$criteria->addCondition('actionLogs.type = 1');
		// $criteria->addCondition('actionLogs.model = "User"');
		$criteria->order = 'actionLogs.time DESC'; // 使用order属性排序logs.time

		$users = User::model()->findAll($criteria);

		$criteria->addCondition('DATEDIFF(NOW(), actionLogs.time) <= 60');
		$activeUsers = User::model()->findAll($criteria);
		$activeUsersIds = array_column($activeUsers,"id");

		print_r(count($users));
		print_r(array_column($users,"id"));
		print_r($activeUsersIds);

		foreach ($users as $key => $user) {
			if(!in_array($user->id ,$activeUsersIds))
			{
				print_r("inactive: ".$user->id."|| ");
				$user->active = 0;
				$user->extra['inactive_reasion']="NT00802198";
				$user->update();
			}
		}
	}

	public function inactiveUser60DayNoLogin2(){
		$sql = '
		    SELECT *
			FROM user u
			WHERE u.active = 1
			  AND EXISTS (
			    SELECT 1
			    FROM log l1
			    WHERE l1.lid = u.id
			      AND l1.type = 1
			      AND l1.model = "User"
			  )
			  AND NOT EXISTS (
			    SELECT 1
			    FROM log l2
			    WHERE l2.lid = u.id
			      AND l2.type = 1
			      AND l2.model = "User"
			      AND DATEDIFF(NOW(), l2.time) <= 60
			  );
		';

		$users = User::model()->findAllBySql($sql);
		print_r(count($users));
		print_r(array_column($users,"id"));

		foreach ($users as $key => $user) {
			print_r("inactive: ".$user->id.": ".count($user->logs)."  ");
			$user->active = 0;
			$user->extra['inactive_reasion']="NT00802198";
			$user->update();
		}
	}

	
}
