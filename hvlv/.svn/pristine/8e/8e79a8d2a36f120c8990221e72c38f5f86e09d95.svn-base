<?php
class RtsController extends Controller
{
	protected $nonAjax = ['labels', 'gatepass', 'rtclabels', 'rtcgatepass', 'discard', 'returnOrg','export','downloadCheckRtsStatus'];

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionScan()
	{
		if (isset($_POST['barcode'])) {
			$barcode = trim($_POST['barcode']);
			$r = new StdClass;
			$r->msg = 'Not Found';
			$r->color = '#c00';
			$r->sound = 'not_found';
			$r->stop = 0;
			$sn = 0;
			$courierId = 0;
			$changed_label = false;

			// in case aupost barcode
			$p = ShipmentScan::getShipmentByBarcode($barcode, $sn, $courierId, false, 0, $changed_label,false);
			if (!empty($p)) {
				if ($p->status != 80) {
					if($p->status<60)
					{	
						$r->msg = 'Error Parcel Status:'.$p->getStatus();
						$r->color = '#c00';
						echo json_encode($r);
						Yii::app()->end();
					}
					$r->msg = 'push to RTS queue successfully!';
					$r->color = '#0c0';
					$p->status = 80; // set as returned shipment
					$p->mdata['rts_scan_date'] = date('Y-m-d');
					$p->cbwf = $p->cbwf | 256;
					$p->save();

					// add tracking informationn for returned
					$p->addTracking(95, 'Shipment returned to TLA Logistics');
					// create RTS first process fee now
					// $this->createRTSScanInvoice($p);
					ShipmentScan::genShipmentScan($p, 4, $sn, 106);
				} else {
					// location 
					$strLocation = $p->mdata['location'];
					$strGroundLabel =  WmsLocation::model()->find('code=:code',[':code'=>$p->mdata['location']])->extra['ground_label'];
					if($strGroundLabel != null){
						$strLocation = $strLocation.'['.$strGroundLabel.']';
					}
					ShipmentScan::genShipmentScan($p, 4, $sn, 106);
					$r->msg = 'Shipment has been in RTS status . location: '.$strLocation;
					$r->color = '#c00';
				}
			}
			$r->hbn = $hbn;
			if (empty($p) || empty($p->consol_id)) {
				echo json_encode($r);
				Yii::app()->end();
			}

			echo json_encode($r);
			Yii::app()->end();
		}

		$filtersForm = new FiltersForm;
		if (isset($_GET['FiltersForm'])) {
			$filtersForm->filters = $_GET['FiltersForm'];
		}
		$data = ImParcel::model()->findAll('status=80');
		$provide = [];

		foreach ($data as $d) {
			//ground label
			$strLocation = @$d->mdata['location'];
			$strGroundLabel =  @WmsLocation::model()->find('code=:code',[':code'=>$d->mdata['location']])->extra['ground_label'];
			if($strGroundLabel != null){
				$strLocation = $strLocation.'['.$strGroundLabel.']';
			}
			//hbn   ref agent-name   cnee-name  postcode  weight  mdata["rts_scan_date"]; note
			$provide[] = ['id' => $d->id, 'hbn' => $d->hbn, 'ref' => $d->ref, 'agent_name' => empty($d->agent) ? '' : $d->agent->name,
				'cnee_name' => empty($d->cnee) ? '' : $d->cnee->name, 'pkg' => $d->pkg,'rts_packages'=>$d->getRtsPackages(), 'postcode' => $d->postcode, 'weight' => $d->weight,
				'received' => empty($d->mdata['rts_scan_date']) ? "" : $d->mdata['rts_scan_date'], 'note' => $d->note , 'location' => $strLocation,'department'=>$d->is3PL()?"3PL":"Import",'rts_warehouse'=>@$d->mdata['rts_warehouse']];
		}
		$filteredData = $filtersForm->filter($provide);
		$dataprovider = new CArrayDataProvider($filteredData);
		$sort = new CSort();
		$sort->attributes = [
			'hbn' => [
				'asc' => 'hbn DESC',
				'desc' => 'hbn ASC',
			],
			'ref' => [
				'asc' => 'ref DESC',
				'desc' => 'ref ASC',
			],
			'rts_warehouse' => [
				'asc' => 'rts_warehouse DESC',
				'desc' => 'rts_warehouse ASC',
			],
			'agent_name' => [
				'asc' => 'agent_name DESC',
				'desc' => 'agent_name ASC',
			],
			'cnee_name' => [
				'asc' => 'cnee_name DESC',
				'desc' => 'cnee_name ASC',
			],
			'pkg' => [
				'asc' => 'pkg ASC',
				'desc' => 'pkg DESC',
			],
			'postcode' => [
				'asc' => 'postcode DESC',
				'desc' => 'postcode ASC',
			],
			'weight' => [
				'asc' => 'weight DESC',
				'desc' => 'weight ASC',
			],
			'received' => [
				'asc' => 'received DESC',
				'desc' => 'received ASC',
			],

		];
		$sort->defaultOrder = "id DESC";
		$dataprovider->sort = $sort;
		$dataprovider->pagination = ['pageSize' => 30];

		$model = ImParcel::model();
		$model->setAttribute('status', 80);
		if (isset($_GET['ImParcel'])) {
			$model->attributes = $_GET['ImParcel'];
		}
		$this->render('scan', ['model' => $model, 'dataProvider' => [$dataprovider, $filtersForm]]);
	}

	/**
	 * create RTS resend fee invoice
	 * @param $parcel
	 * @param $amount
	 */
	private function createRTSResendInvoice(&$parcel, $amount)
	{
		$oParcel = ImParcel::model()->findByPk($parcel->getRTSOrgShipNoId());
		if(!empty($oParcel->consol_id))
		{
			$oConsol = $oParcel->consol;
			$oConsol->isTLA();
		}
		// check to see if related pending invoice existing
		$invLines = InvLine::model()->findAll('model = :model AND fid = :fid', [':model' => 'ImParcel', 'fid' => $parcel->id]);
		$invoice = null;
		if (!empty($invLines)) {
			// try to check related invoice belongs to RTS fee or not
			foreach ($invLines as $invLine) {
				$invoiceRts = Invoice::model()->findByPk($invLine->inv_id);
				if (!empty($invoiceRts) && $invoiceRts->type == Invoice::INVOICE_TYPE_RTS_RESEND_FEE) {
					// delete all old invoice line
					InvLine::model()->deleteAll('inv_id = :invid', [':invid' => $invoiceRts->id]);
					$invoice = $invoiceRts;
				}
			}
		}
		if (empty($invoice)) {
			// not existing yet , just create a new one
			$invoice = new Invoice();
			$invoice->type = Invoice::INVOICE_TYPE_RTS_RESEND_FEE; // for RTS fee invoice
			$invoice->to_id = $parcel->agent_id;
			$invoice->man_id = 0; // in case manifest id means nothing
			$invoice->dpt_id = $parcel->ddpt_id;
			$invoice->dpmt = Invoice::DPMT_IMPORT;

			// if department not set , we set as Sydney warehouse
			if (empty($invoice->dpt_id)) {
				$invoice->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
			}
			$invoice->status = Invoice::INVOICE_STATUS_PENDING;
			$invoice->date = date('Y-m-d'); // here just dummy date , once accounting change to posted , should create new date for the invoice

			// get invoice currency
			$invoiceCurrency = 1; // default as AUD
			$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $parcel->agent_id]);
			if (!empty($orgRate)) {
				$invoiceCurrency = $orgRate['currency'];
			}
			$invoice->currency = $invoiceCurrency;

			// set invoice name , address and pay terms information
			$owner = $parcel->agent;
			$invoice->mdata['name'] = $owner->name;
			$invoice->mdata['address'] = $owner->getAddress();
			$invoice->mdata['payterm'] = empty($owner->extra['payterm']) ? '2 days' : $owner->extra['payterm'] . ' days';

			// here just dummy date , once accounting change to posted , should create new date for the invoice
			$invoice->due = Invoice::calcDue($invoice->date, $invoice->mdata['payterm']);
		}

		$rtsFee = $amount;
		// check to see if we should including GST (default 10%)
		$gst = 0;
		if (isset($parcel->agent->extra['incl_gst']) && $parcel->agent->extra['incl_gst'] == 1) {
			$gst = round($rtsFee * 10 / 100, 2);
			$rtsFee += $gst;
			$invoice->gst = $gst;
		}

		$invoice->total = $rtsFee;
		$invoice->save();

		// create related invoice line and attached it to the invoice
		$il = new InvLine;
		$il->inv_id = $invoice->id;
		$il->amount = $rtsFee;
		$il->gst = $gst;
		$il->mdata['items'] = [[$parcel->note, 'Returned to Sender Reshipping Fee', $il->amount - $il->gst, $parcel->ref]];
		$il->model = 'ImParcel';
		$il->fid = $parcel->id;
		$il->qty = 1;
		$il->save();

		// add storage fee
		if (isset($parcel->mdata['rts_org_no'])) {
			$orgShipment = Shipment::model()->find('hbn = :hbn', [':hbn' => $parcel->mdata['rts_org_no']]);

			// set original shipment as all done status
			$orgShipment->status = 85;
			$orgShipment->update('status');

			// calculate RTS storage fee invoice
			if (!empty($orgShipment) && isset($orgShipment->mdata['rts_scan_date'])) {
				$receivedDate = $orgShipment->mdata['rts_scan_date'];
				$inTime = new DateTime($receivedDate, new DateTimeZone('Australia/Sydney'));
				$outTime = new DateTime('now', new DateTimeZone('Australia/Sydney'));
				$storageDays = $outTime->diff($inTime)->format("%a");
				$freeStorageDays = $this->getRtsStorageTerms($orgShipment->agent->id);
				$storageDays = $storageDays - $freeStorageDays;
				if ($storageDays > 0) {
					$storageCharge = $this->getRtsStorageRate($orgShipment->agent->id) * $orgShipment->weight * $storageDays;
					if ($storageCharge > 0) {
						$il = new InvLine;
						$il->inv_id = $invoice->id;
						$il->amount = $storageCharge;
						$il->mdata['items'] = [[$parcel->hbn, 'Returned to Sender Storage Fee', $il->amount]];
						$il->model = 'ImParcel';
						$il->fid = $parcel->id;
						$il->save();

						if (isset($parcel->agent->extra['incl_gst']) && $parcel->agent->extra['incl_gst'] == 1) {
							$gst = round($storageCharge * 10 / 100, 2);
							$storageCharge += $gst;
							$invoice->gst += $gst;
							$il->gst = $gst;
							$il->amount += $gst;
							$il->update(['amount', 'gst']);
						}

						$invoice->total += $storageCharge;
						$invoice->update(['total', 'gst']);
					}
				}
			}
		}
	}

	/**
	 * create RTS scan fee
	 * @param $parcel
	 */
	private function createRTSScanInvoice(&$parcel)
	{

		// check to see if RTS scan invoice with pending status existing or not
		// if existing , we just update it
		// otherwise create a new one with pending status
		// Normally only account change status from pending to posted
		// check to see if related pending invoice existing
		$invoice = Invoice::model()->find('to_id = :tid AND type = :itype AND status = 1', [':tid' => $parcel->agent_id, ':itype' => Invoice::INVOICE_TYPE_RTS_FEE]);
		if (empty($invoice)) {
			// not existing yet , just create a new one
			$invoice = new Invoice();
			$invoice->type = Invoice::INVOICE_TYPE_RTS_FEE; // for RTS scan fee invoice
			$invoice->to_id = $parcel->agent_id;
			$invoice->man_id = 0; // in case manifest id means nothing
			$invoice->dpt_id = $parcel->ddpt_id;
			$invoice->dpmt = Invoice::DPMT_IMPORT;

			// if department not set , we set as Sydney warehouse
			if (empty($invoice->dpt_id)) {
				$invoice->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
			}
			$invoice->status = Invoice::INVOICE_STATUS_PENDING; // set as pending status
			$invoice->date = date('Y-m-d'); // here just dummy date , once accounting change to posted , should create new date for the invoice

			// get invoice currency
			$invoiceCurrency = 1; // default as AUD
			$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $parcel->agent_id]);
			if (!empty($orgRate)) {
				$invoiceCurrency = $orgRate['currency'];
			}
			$invoice->currency = $invoiceCurrency;

			// set invoice name , address and pay terms information
			$owner = $parcel->agent;
			$invoice->mdata['name'] = $owner->name;
			$invoice->mdata['address'] = $owner->getAddress();
		}

		$couries = 'aus'; //default auspost
		if (isset($parcel->trans) && !empty($parcel->trans) && $parcel->trans[0]->org_id == Org::ORGID_COURIER_FASTWAY) {
			$couries = 'fastway';
		} elseif (isset($parcel->trans) && !empty($parcel->trans) && $parcel->trans[0]->org_id == Org::ORGID_COURIER_STARTRACK) {
			$couries = 'startrack';
		} elseif (preg_match('/LET\d{7}/i', $parcel->ref)) {
			$couries = 'letter';
		}
		// get RTS fee
		$rtsFee = $this->getRtsRate($parcel->agent_id, $couries, $parcel);

		// check to see if we should including GST (default 10%)
		$gst = 0;
		if (isset($parcel->agent->extra['incl_gst']) && $parcel->agent->extra['incl_gst'] == 1) {
			$gst = round($rtsFee * 10 / 100, 2);
			$rtsFee += $gst;
		}

		$invoice->total += $rtsFee;
		if ($gst > 0) {
			$invoice->gst += $gst;
		}
		$invoice->mdata['payterm'] = empty($owner->extra['payterm']) ? '2 days' : $owner->extra['payterm'] . ' days';
		// here just dummy date , once accounting change to posted , should create new date for the invoice
		$invoice->due = Invoice::calcDue($invoice->date, $invoice->mdata['payterm']);

		//if some couries resend not charge, we don't need create  invoice
		if ($invoice->total > 0) {
			$invoice->save();
			// create related invoice line and attached it to the invoice
			$il = new InvLine;
			$il->inv_id = $invoice->id;
			$il->amount = $rtsFee;
			if ($gst > 0) {
				$il->gst = $gst;
			}

			// save receiving date,shipment ref No. and customer ref No.
			$il->mdata['items'] = [[empty($parcel->cref) ? $parcel->hbn : $parcel->cref, $parcel->ref, date('Y-m-d'), 'Returned to Sender Receiving Fee', $il->amount]];
			$il->model = 'ImParcel';
			$il->fid = $parcel->id;
			if ($il->amount > 0) {
				$il->save();
			}
		}
	}

	/**
	 * get RTS fee for the specified agent
	 * @param $owner
	 * @return array|int
	 */
	private function getRtsRate($orgId, $courier = 'AUS', $p = null)
	{

		// get organizaton price rate
		$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $orgId]);
		if (empty($orgRate)) {
			return 0;
		}

		// get org rate details
		$rateInfo = json_decode($orgRate['meta']);
		$code = 'AU'; // RTS fee belongs to Australia local
		$rtsFee = 0;
		if ($courier == 'fastway') {
			if (isset($rateInfo->$code->rts_rate_fast)) {
				$rtsFee = floatval($rateInfo->$code->rts_rate_fast);
			}
		} elseif ($courier == 'startrack') {
			if (isset($rateInfo->$code->rts_rate_star)) {
				$rtsFee = floatval($rateInfo->$code->rts_rate_star) * $p->getRtsEstiInvoice() / 100;
			}
		} elseif ($courier == 'letter') {
//for letter currently without charge
			$rtsFee = 0;
		} else {
			if (isset($rateInfo->$code->rts_rate)) {
				$rtsFee = floatval($rateInfo->$code->rts_rate);
			}
		}
		return $rtsFee;
	}

	/**
	 * get RTS new shipment order fee
	 * @param $orgId
	 * @return float|int
	 */
	private function getRtsModRate($orgId)
	{

		// get organizaton price rate
		$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $orgId]);
		if (empty($orgRate)) {
			return 0;
		}

		// get org rate details
		$rateInfo = json_decode($orgRate['meta']);
		$code = 'AU'; // RTS fee belongs to Australia local
		$rtsModFee = 0;
		if (isset($rateInfo->$code->rts_modrate)) {
			$rtsModFee = floatval($rateInfo->$code->rts_modrate);
		}
		return $rtsModFee;
	}

	/**
	 * get RTS storage rate
	 * @param $orgId
	 * @return float|int
	 */
	private function getRtsStorageRate($orgId)
	{

		// get organizaton price rate
		$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $orgId]);
		if (empty($orgRate)) {
			return 0;
		}

		// get org rate details
		$rateInfo = json_decode($orgRate['meta']);
		$code = 'AU'; // RTS fee belongs to Australia local
		$rtsStrFee = 0;
		if (isset($rateInfo->$code->rts_strrate)) {
			$rtsStrFee = floatval($rateInfo->$code->rts_strrate);
		}
		return $rtsStrFee;
	}

	/**
	 * get RTS storage terms
	 * @param $orgId
	 * @return int
	 */
	private function getRtsStorageTerms($orgId)
	{

		// get organizaton price rate
		$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $orgId]);
		if (empty($orgRate)) {
			return 0;
		}

		// get org rate details
		$rateInfo = json_decode($orgRate['meta']);
		$code = 'AU'; // RTS fee belongs to Australia local
		$rtsStrTerms = 0;
		if (isset($rateInfo->$code->rts_strterms)) {
			$rtsStrTerms = intval($rateInfo->$code->rts_strterms);
		}
		return $rtsStrTerms;
	}

	/**
	 * Lists and search.
	 */
	public function actionList()
	{

		// show all returned shipments
		$model = ImParcel::model()->findAll('status = 80');
		if (isset($_GET['Outturn'])) {
			$model->attributes = $_GET['Outturn'];
		}

		$this->render('list', [
			'model' => $model,
		]);
	}

	/**
	 * list all shipments invoice in RTS list
	 * @param $id
	 */
	public function actionInvoices($id)
	{
		$model = RtsList::model()->findByPk($id);
		$this->render('invoices', [
			'model' => $model,
		]);
	}

	/**
	 * list all shipments invoice in RTS list
	 * @param $id
	 */
	public function actionRtcinvoices($id)
	{
		$model = RtsRtcList::model()->findByPk($id);
		$this->render('invoices', [
			'model' => $model,
		]);
	}

	public function actionRtc()
	{
		$model = RtsRtcList::model();
		$this->render('rtc', [
			'model' => $model,
		]);
	}

	/**
	 * @param $id
	 */
	public function actionCreateInvoice($id)
	{
		$model = ImParcel::model()->findByPk($id);

		if (isset($_POST['amount'])) {
			$amount = $_POST['amount'];
			if ($amount > 0) {
				$this->createRTSResendInvoice($model, $amount);
				$this->ajaxResult($model);
			} else {
				$this->ajaxResult($model, ['id'], 'Invoice Amount must be great than zero');
			}
		}

		$this->render('createInvoice', [
			'model' => $model,
		]);
	}

	public function actionExport()
	{
		$filtersForm = new FiltersForm;
		if (isset($_GET['FiltersForm'])) {
			$filtersForm->filters = $_GET['FiltersForm'];
		}
		$data = ImParcel::model()->findAll('status=80');
		$provide = [];

		foreach ($data as $d) {
			//hbn   ref agent-name   cnee-name  postcode  weight  mdata["rts_scan_date"]; note
			$provide[] = ['hbn' => $d->hbn, 'ref' => $d->ref, 'agent_name' => empty($d->agent) ? '' : $d->agent->name,
				'cnee_name' => empty($d->cnee) ? '' : $d->cnee->name, 'pkg' => $d->pkg, 'postcode' => $d->postcode, 'weight' => $d->weight,
				'received' => empty($d->mdata['rts_scan_date']) ? "" : $d->mdata['rts_scan_date'], 'note' => $d->note, 'location' => @$d->mdata['location'], 'used_location' => @$d->mdata['used_location'],'state'=>empty($d->cnee) ? '' : $d->cnee->state];
		}
		$xls = new oExcel;
		$i = 1;
		$keys = array_keys($provide[0]);
		// $xls->addRow($i++, ['WBN', 'Ref','Awb','Packages', 'Weight', 'Status', 'Agent', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Date', 'Delay','Extra Info']);
		$xls->addRow($i++, $keys);
		foreach ($provide as $key => $p) {
			$xls->addRow($i++, $p);
		}

		$xls->output('rts_export_'.time().'.xlsx');
	}
	/**
	 * List all reshipping shipments
	 */
	public function actionRrts()
	{
		$filtersForm = new FiltersForm;
		if (isset($_GET['FiltersForm'])) {
			$filtersForm->filters = $_GET['FiltersForm'];
		}
		$data = ImParcel::model()->findAll('status=80');
		$provide = [];

		foreach ($data as $d) {
			//ground label
			$strLocation = @$d->mdata['location'];
			$strGroundLabel =  WmsLocation::model()->find('code=:code',[':code'=>$d->mdata['location']])->extra['ground_label'];
			if($strGroundLabel != null){
				$strLocation = $strLocation.'['.$strGroundLabel.']';
			}
			//hbn   ref agent-name   cnee-name  postcode  weight  mdata["rts_scan_date"]; note
			$provide[] = ['id' => $d->id, 'hbn' => $d->hbn, 'ref' => $d->ref, 'agent_name' => empty($d->agent) ? '' : $d->agent->name,
				'cnee_name' => empty($d->cnee) ? '' : $d->cnee->name,'ddpt_id' => $d->ddepot->name, 'pkg' => $d->pkg, 'postcode' => $d->postcode, 'weight' => $d->weight,
				'received' => empty($d->mdata['rts_scan_date']) ? "" : $d->mdata['rts_scan_date'], 'note' => $d->note, 'location' => $strLocation, 'used_location' => @$d->mdata['used_location']];
		}
		$filteredData = $filtersForm->filter($provide);
		$dataprovider = new CArrayDataProvider($filteredData);
		$sort = new CSort();
		$sort->attributes = [
			'hbn' => [
				'asc' => 'hbn DESC',
				'desc' => 'hbn ASC',
			],
			'ref' => [
				'asc' => 'ref DESC',
				'desc' => 'ref ASC',
			],
			'agent_name' => [
				'asc' => 'agent_name DESC',
				'desc' => 'agent_name ASC',
			],
			'cnee_name' => [
				'asc' => 'cnee_name DESC',
				'desc' => 'cnee_name ASC',
			],
			'pkg' => [
				'asc' => 'pkg ASC',
				'desc' => 'pkg DESC',
			],
			'postcode' => [
				'asc' => 'postcode DESC',
				'desc' => 'postcode ASC',
			],
			'weight' => [
				'asc' => 'weight DESC',
				'desc' => 'weight ASC',
			],
			'received' => [
				'asc' => 'received DESC',
				'desc' => 'received ASC',
			],

		];
		$sort->defaultOrder = "id DESC";
		$dataprovider->sort = $sort;
		$dataprovider->pagination = ['pageSize' => 30];

		$model = ImParcel::model();
		$model->setAttribute('status', 80);
		if (isset($_GET['ImParcel'])) {
			$model->attributes = $_GET['ImParcel'];
		}
		$this->render('rrts', [
			'model' => $model,
			'dataProvider' => [$dataprovider, $filtersForm],
		]);
	}

	/**
	 * reshipping action logic
	 */
	public function actionReshipping()
	{
		$model = RtsList::model();
		$this->render('reshipping', [
			'model' => $model,
		]);
	}

	/**
	 * show all finished RTS shipment
	 */
	public function actionFinished()
	{
		$model = ImParcel::model();
		$model->setAttribute('status', 85);
		if (isset($_GET['ImParcel'])) {
			$model->attributes = $_GET['ImParcel'];
		}
		$this->render('finished', [
			'model' => $model,
		]);
	}

	/**
	 * process all RTS resend shipments now
	 * currently create manifest , send to AuPost
	 * create label for download
	 */
	public function actionAjaxResend()
	{
		$resp = [];
		$resp['msg'] = 'All Done Successfully';
		if (!isset($_POST['sids'])) {
			$resp['msg'] = 'Please select at least one shipment';
		} else {
			$rtsShipments = [];
			$shipmentIds = $_POST['sids'];
			$note = $_POST['note'];
			$dptid = $_POST['dptid'];
			$company = $_POST['company'];
			// based on mother shipment to get RTS new shipment
			foreach ($shipmentIds as $sid) {
				$shipment = Shipment::model()->findByPk($sid);
				if (!empty($shipment)) {
					$rtsShipment = Shipment::model()->find('hbn = :hbn', [':hbn' => $shipment->getRTSTranshipNo()]);
					if (!empty($rtsShipment)) {
						$rtsShipments[] = $rtsShipment;
					}
				}
			}

			if (!empty($rtsShipments)) {
				$rtsListId = $this->createRtsList($rtsShipments, $note, $company, $dptid);
				$rts_fastway = [];
				$rts_startrack = [];
				$rts_eParcels = [];
				$rts_tnt = [];
				$rts_d2z = [];
				$rts_dfe = [];
				$rts_dfe_top = [];
				$rts_eiz = [];
				$rts_ubi = [];
				$rts_my_toll = [];
				foreach ($rtsShipments as $i => $p) {
					if (empty($p->trans)) {
						if (preg_match('/^(AMQ|333UF|33EVH|33EVJ|34AWE)\d{7}$/i', $p->ref,$m)) {
							$rts_eParcels[$m[1]][] = $p;
						}
						continue;
					}
					$trans = $p->trans;
					$endTran = end($trans);
					switch ($endTran->org_id) {
						case Org::ORGID_COURIER_AUPOST:
							$rts_eParcels['AMQ'][] = $p;
							break;
						case Org::ORGID_COURIER_FASTWAY:
							$rts_fastway[] = $p;
							break;
						case Org::ORGID_COURIER_STARTRACK:
							$rts_startrack[] = $p;
							break;
						case Org::ORGID_COURIER_TNT_TOP:
							$rts_tnt[] = $p;
							break;
						case Org::ORGID_COURIER_D2Z:
							$rts_d2z[] = $p;
							break;
						case Org::ORGID_COURIER_DFE:
							$rts_dfe[] = $p;
							break;
						case Org::ORGID_COURIER_DFE_TOP:
							$rts_dfe_top[] = $p;
							break;
						case Org::ORGID_COURIER_ALLIED:
							$rts_eiz[] = $p;
							break;
						case Org::ORGID_COURIER_UBI:
							$orgRate = OrgRate::model()->findByPk($p->mdata['org_rate_id']);
							if(!preg_match('/TOLL/i', $orgRate->code)) break;
							$rts_ubi[] = $p;
							break;
						case Org::ORGID_COURIER_TOLL_IPEC:
							$rts_my_toll[] = $p;
							break;
					}
				}

				$markRtsDone = function ($s) {
					if (!isset($s->mdata['rts_org_no'])) {
						return;
					}

					$orgShipmentNo = $s->mdata['rts_org_no'];
					$orgShipment = Shipment::model()->find('hbn = :hbn', [':hbn' => $orgShipmentNo]);
					$orgShipment->status = 82; // set as reshipping done
					$orgShipment->mdata['rts_reshipping_time'] = date('Y-m-d'); // recored RTS reshipping date
					$orgShipment->update('status');
					$orgShipment->updateMeta();
				};

				if (!empty($rts_eParcels))
				{

					foreach ($rts_eParcels as $mlid => $rts_eParcel)
					{
						$acc = AusPostAPI::mlid2acc($mlid);
						if(empty($acc)) continue;
						$apa = new AusPostAPI($acc);
						$r = $apa->createOrderIncludingShipments($rts_eParcel, $rtsListId, AusPostAPI::CHARGE_CODE_POD);
						if (!empty($r->order)) {
							$oid = $r->order->order_id;
							foreach ($rts_eParcel as $i => $s) {
								$ts = new Tranship;
								$ts->pid = $s->id;
								$ts->org_id = 101; // for Australia post office
								$ts->man_id = $rtsListId;
								$ts->type = 80; // shipment transfer to a different delivery courier
								$ts->status = 19; // in finally moving status
								$ts->connote = $s->ref;
								$ts->time = date('Y-m-d H:i:s');
								$ts->mdata['oid'] = $oid;
								$ts->mdata['sid'] = $r->order->shipments[$i]->shipment_id;
								$ts->save();

								// change original shipment to RTS delivery status
								if (isset($s->mdata['rts_org_no'])) {
									$orgShipmentNo = $s->mdata['rts_org_no'];
									$orgShipment = Shipment::model()->find('hbn = :hbn', [':hbn' => $orgShipmentNo]);
									$orgShipment->status = 82; // set as reshipping done
									$orgShipment->mdata['rts_reshipping_time'] = date('Y-m-d'); // recored RTS reshipping date
									$orgShipment->update('status');
									$orgShipment->updateMeta();
								}
							}
						} else {
							$resp['msg'] = '';
							foreach ($apa->err as $e) {
								$msg = $e->message;
								if (!empty($e->field) && preg_match('/shipments\[(\d+)\]/', $e->field, $m)) {
									$msg .= ': ' . $rtsShipments[$m[1]]->hbn;
								}
								$resp['msg'] .= $msg . '<br/>';
							}
							// delete just create manifest
							Manifest::model()->deleteByPk($rtsListId);
						}
					}
					
				}
				// for fastway
				if (!empty($rts_fastway)) {
					foreach ($rts_fastway as $i => $s) {
						$markRtsDone($s);
					}
				}

				if (!empty($rts_startrack)) {
// we need to log the  startrack shipment
					$ssApi = new StarTrackAPI('syd', true);
					$r = $ssApi->createOrderFromShipments($rts_startrack, 'rts' . date('Ymd'));
					if (!empty($r->order)) {
						$oid = $r->order->order_id;

						// because aupost returned shipment is not the same order with our sending order
						// so here we need to order by HBN again
						$auPostShipments = [];
						foreach ($r->order->shipments as $aushipment) {
							$auPostShipments[$aushipment->shipment_reference] = $aushipment;
						}
						foreach ($rts_startrack as $i => $s) {
							$aushipment = $auPostShipments[$s->hbn];

							// check to see if tranship existing
							// in case existing , just update it
							$ts = Tranship::model()->find('pid = :pid AND org_id = :oid AND status = 19', [':pid' => $s->id, ':oid' => Org::ORGID_COURIER_STARTRACK]);
							if (empty($ts)) {
								$ts = new Tranship;
								$ts->pid = $s->id;
								$ts->org_id = Org::ORGID_COURIER_STARTRACK; // for startrack post office
								$ts->man_id = $s->man_id;
								$ts->type = 80; // shipment transfer to a different delivery courier
								$ts->status = 19; // in finally moving status
								$ts->connote = $s->ref;
								// currently we save cost with a single field

								$ts->mdata['cost'] = $aushipment->shipment_summary->total_cost;
								$costValue = floatval($aushipment->shipment_summary->total_cost - $aushipment->shipment_summary->total_gst);
								$ts->cost = round($costValue, 2);
							}
							$ts->time = date('Y-m-d H:i:s');
							$ts->mdata['oid'] = $oid;
							$ts->mdata['sid'] = $aushipment->shipment_id;
							$ts->save();
							$markRtsDone($s);
						}
					}
				}

				if (!empty($rts_tnt)) {
					$ss_syd = []; //manifest shipments;
					$ss_mel = [];
					$ss_bne = [];
					$ss_per = [];
					foreach ($rts_tnt as $s) {
						if (preg_match("/^TLS\d{9}$/i", $s->ref)) {
							$ss_syd[] = $s;
						} else if (preg_match("/LMA\d{9}/i", $s->ref)) {
							$ss_mel[] = $s;
						} else if (preg_match("/TBC\d{9}/i", $s->ref)) {
							$ss_bne[] = $s;
						}else if (preg_match("/LPC\d{9}/i", $s->ref)) {
							$ss_per[] = $s;
						}
					}

					if (!empty($ss_syd)) {
						$tnt = TntAPI::getTntInterface('syd_top');
						$tnt->preEt12File($ss_syd);
					}

					if (!empty($ss_mel)) {
						$tnt = TntAPI::getTntInterface('mel_top');
						$tnt->preEt12File($ss_mel);
					}

					if (!empty($ss_bne)) {
						$tnt = TntAPI::getTntInterface('bne_top');
						$tnt->preEt12File($ss_bne);
					}

					if (!empty($ss_per)) {
						$tnt = TntAPI::getTntInterface('per_top');
						$tnt->preEt12File($ss_per);
					}

					foreach ($rts_tnt as $s) {
						$markRtsDone($s);
					}
				}

				if (!empty($rts_d2z)) {
					$this->doD2zManifest($rts_d2z);
					foreach ($rts_d2z as $s) {
						$markRtsDone($s);
					}
				}

				if (!empty($rts_dfe)) {
					$this->doDfeManifest($rts_dfe);
					foreach ($rts_dfe as $s) {
						$markRtsDone($s);
					}
				}

				if (!empty($rts_dfe_top)) {
					$this->doDfeManifest($rts_dfe_top);
					foreach ($rts_dfe_top as $s) {
						$markRtsDone($s);
					}
				}

				if (!empty($rts_eiz)) {
					$this->doEizManifest($rts_eiz);
					foreach ($rts_eiz as $s) {
						$markRtsDone($s);
					}
				}

				if (!empty($rts_ubi)) {
					$this->doUbiManifest($rts_ubi);
					foreach ($rts_ubi as $s) {
						$markRtsDone($s);
					}
				}

				if (!empty($rts_my_toll)) {
					$this->doMyTollManifest($rts_my_toll);
					foreach ($rts_my_toll as $s) {
						$markRtsDone($s);
					}
				}
			}
		}
		echo json_encode($resp);
	}

	public function doD2zManifest($sst)
	{
		$d2z = new D2zShipAPI('auto');
		$max_ppg = 500;
		$pgs = ceil(count($sst) / $max_ppg);

		for($pg = 0; $pg < $pgs; $pg++){
			$shipments = array_slice($sst, $pg * $max_ppg, $max_ppg);
			// fix d2z use reserved connote number range
			foreach ($shipments as $shipment) {
				if ($shipment->trans[0]->status == 10) $needCreate[] = $shipment;
			}
			if (!empty($needCreate)) $result = $d2z->createConsignments($needCreate, true);
			$result = $d2z->allocate_shipments($shipments, 'RTS_' . date('YmdHis'));
			if (!empty($result['responseMessage']) && in_array($result['responseMessage'], ['Shipment allocation Successful', 'Shipment Allocated Successfully'])) {
				$transaction=Yii::app()->db->beginTransaction();
				try {
					foreach ($shipments as $s) {
						$ts = Tranship::model()->find('pid = :id', [':id' => $s->id]);
						$ts->mdata['oid'] = Org::ORGID_COURIER_D2Z;
						$ts->status = 11;
						$ts->save();
					}
					$transaction->commit();
				} catch (Exception $ex) {
					$transaction->rollback();
					throw $ex;
				}
			} else {
				return $result;
			}
		}
		return true;
	}

	public function doDfeManifest($sst)
	{
		$imConsolService = new ImConsolService();
		$errs = $imConsolService->doDfeManifest($sst);
	}


	public function doEizManifest($sst)
	{
		$imConsolService = new ImConsolService();
		$errs = $imConsolService->doEizManifest($sst);
	}

	public function doUbiManifest($sst)
	{
		$imConsolService = new ImConsolService();
		$errs = $imConsolService->doUbiTollManifest($sst);
	}

	public function doMyTollManifest($sst)
	{
		$imConsolService = new ImConsolService();
		$errs = $imConsolService->doMyTollManifest($sst);
	}

	/**
	 * send to fastway
	 *
	 */
	private function sent2fastway(&$shipment)
	{
		return $shipment->createFastwayLabel();
	}

	/**
	 * create startrack label
	 * @param $shipment
	 * @return bool
	 */
	private function createStartrackLabel(&$shipment)
	{
		$ss = new StarTrackAPI('syd', true);
		$result = $ss->createShipments($shipment);
		if ($result && isset($result->shipments) && is_array($result->shipments)) {
			$result = $result->shipments[0];
			$cost = $result->shipment_summary->total_cost;

			$shipment->mdata['ss_shipment_id'] = $result->shipment_id;
			foreach ($result->items as $item) {
				if (!empty($item->tracking_details->article_id)) {
					if (preg_match('/7RFZ\d{8}EXP00001/i', $item->tracking_details->article_id)) {
						$shipment->mdata['ss_shipment_items'][] = $item->item_id;
						break;
					}
				}
			}

			// startrack label format: 7RFZ50000002EXP00001, we only need 7RFZ50000002
			// EXP00001 is index
			$shipment->ref = substr($result->items[0]->tracking_details->article_id, 0, 12);
			$shipment->update('ref');

			// create label
			$respLabel = $ss->createLabels([$result->shipment_id]);
			$lblRequestId = '';
			if ($respLabel) {
				// get label  request ID
				// then we can print label based on request ID
				$lblRequestId = $respLabel->labels[0]->request_id;
				$shipment->mdata['ss_lbl_request_id'] = $lblRequestId;
				$shipment->updateMeta();

				// save tranship information for startrack courier
				$ts = new Tranship;
				$ts->pid = $shipment->id;
				$ts->org_id = Org::ORGID_COURIER_STARTRACK; // for StarTrack
				$ts->man_id = $shipment->man_id;
				$ts->type = 80; // shipment transfer to a different delivery courier
				$ts->status = 19; // in finally moving status
				$ts->connote = $shipment->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->mdata['ss_shipment_id'] = $result->shipment_id;
				$ts->mdata['ss_lbl_request_id'] = $lblRequestId;
				$ts->cost = number_format(round($cost, 2), 2, '.', '');
				if (!$ts->save()) {
					$this->logerr(json_encode($ts->getErrors()));
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * put RTS received shipment into RTC waiting pool
	 * @param $id
	 */
	public function actionRtcop($id)
	{
		$shipment = Shipment::model()->findByPk($id);
		if (!empty($shipment)) {
			$shipment->status = 83;
			$shipment->update('status');
		}
		$resp = ['msg' => 'All Done Successfully', 'status' => 0];
		echo json_encode($resp);
	}

	/**
	 * selected shipments returned RTS finished
	 * @throws CDbException
	 */
	public function actionAjaxReturned()
	{
		$resp = [];
		$resp['msg'] = 'All Done Successfully';
		if (!isset($_POST['sids'])) {
			$resp['msg'] = 'Please select at least one shipment';
		} else {
			$shipments = [];
			$shipmentIds = $_POST['sids'];
			$note = $_POST['note'];
			$dptid = $_POST['dptid'];
			$company = $_POST['company'];
			// based on mother shipment to get RTS new shipment
			foreach ($shipmentIds as $sid) {
				$shipment = Shipment::model()->findByPk($sid);
				$shipment->status = 84; // shipment RTS action done
				$shipment->update('status');
				$shipments[] = $shipment;
			}

			if (!empty($shipments)) {
				// create RTS list
				$this->createRtsRtcList($shipments, $note, $company, $dptid);
			}
		}
		echo json_encode($resp);
	}

	/**
	 * print all new RTS shipments labels
	 * @param $id
	 */
	public function actionLabels($id)
	{
		$rtsList = RtsList::model()->findByPk($id);
		$shipments = [];
		foreach ($rtsList->lines as $line) {
			$model = new $line->model;
			$shipment = $model->findByPk($line->fid);
			if (!empty($shipment)) {
				$shipments[] = $shipment;
			}
		}
		if (!empty($shipments)) {
			$imparcelService = new ImParcelService();
			$imparcelService->getImparcelLabel($shipments);
		}
		Yii::app()->end();
	}

	public function actionGatepass($id)
	{
		$m = RtsList::model()->findByPk($id);
		oPDF::renderPDF('rts_gatepass', ['m' => $m]);
	}

	public function actionImportRtc()
	{
		$this->render('import_rtc');
	}

	public function actionCheckRtsStatus()
	{
		$this->render('check_rts_status');
	}

	/**
	 * save rtc import
	 */
	public function actionAjaxImportRtc()
	{
		$resp = ['success' => 1, 'msg' => 'import successfully'];
		$template_file = empty($_FILES['rtc_form']) ? [] : $_FILES['rtc_form'];
		if (empty($template_file['tmp_name']) || !is_uploaded_file($template_file['tmp_name'])) {
			$resp['msg'] = 'Invalid template file';
			echo json_encode($resp);
			return;
		} else {
			$xls = new oExcel;
			$xls->load($template_file['tmp_name']);
			$data = $xls->getAll();
			$shipments = [];
			$note = empty($data[2][3]) ? '' : $data[2][3];
			$dptid = empty($data[2][4]) ? 106 : trim($data[2][4]);
			$company = empty($data[2][2]) ? '' : $data[2][2];
			$status = empty($data[2][5]) ? '' : $data[2][5];
			if (!in_array($status, [84, 86])) {
				$resp['msg'] = 'status must be 84 or 86';
				echo json_encode($resp);
				return;
			}
			unset($data[1]);
			foreach ($data as $d) {
				if (empty($d[1])) {
					continue;
				}
				$p = ImParcel::model()->find('ref=:ref', [':ref' => trim($d[1])]);
				if (!empty($p) && $p->status != 80) {
					$resp['msg'] = 'shipment ' . $p->ref . ' not in RTS received!';
					echo json_encode($resp);
					return;
				}
			}

			//80
			foreach ($data as $d) {
				if (empty($d[1])) {
					continue;
				}
				$p = ImParcel::model()->find('ref=:ref', [':ref' => trim($d[1])]);
				if (!empty($p)) {
					$p->status = $status;
					$p->update(['status']);
					$shipments[] = $p;
				}
			}

			if (!empty($shipments) && $status == 84) {
				// create RTS list
				$this->createRtsRtcList($shipments, $note, $company, $dptid);
			}
		}
		echo json_encode($resp);
	}

	/**
	 * check rts status
	 */
	public function actionAjaxCheckRtsStatus()
	{
		$resp = ['success' => 1, 'msg' => 'import successfully'];
		$template_file = empty($_FILES['rtc_form']) ? [] : $_FILES['rtc_form'];
		if (empty($template_file['tmp_name']) || !is_uploaded_file($template_file['tmp_name'])) {
			$resp['msg'] = 'Invalid template file';
			echo json_encode($resp);
			return;
		} else {
			$xlsExport = new oExcel;
			$xls = new oExcel;
			$xls->supported($template_file['name']);
			$xls->load($template_file['tmp_name']);
			$data = $xls->getAll();
			$shipments = [];
			$i = 1;
			$xlsExport->addRow($i++, ['hbn', 'ref', 'RTS status']);
			unset($data[1]);
			foreach ($data as $d) 
			{
				if (empty($d[1])) {
					continue;
				}
				$p = ImParcel::model()->find('hbn=:ref or ref=:ref', [':ref' => trim($d[1])]);
				if(empty($p))
				{
					$xlsExport->addRow($i++, [$d[1], $d[1], 'empty shipment']);
				}else
				{
					$logs = Log::model()->find(' JSON_VALUE(meta, "$.status") like :status and lid = :lid and model = :model ', [":status"=>"%".ImParcel::$states[ImParcel::RTS_RECEIVED]."%",":lid"=>$p->id,":model"=>"ImParcel"]);
					if(!empty($logs))
					{
						$xlsExport->addRow($i++, [$p->hbn, $p->ref, ImParcel::$states[ImParcel::RTS_RECEIVED]]);
					}else
					{
						$xlsExport->addRow($i++, [$p->hbn, $p->ref, 'not found RTS']);
					}
				}
				
			}
			$tempfile = Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR . 'check_rts_status.xlsx';
			$xlsExport->output($tempfile, null, false);
			$resp['msg'] = "<a href='".$this->createUrl('rts/downloadCheckRtsStatus')."' target='blank'>download File</p>";
			echo json_encode($resp);
		}
	}

	public function actionDownloadCheckRtsStatus()
	{
		$tempfile = Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR .  'check_rts_status.xlsx';
		header("Cache-Control: maxage=1");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=\"" . urldecode(basename($tempfile)) . '"');
		header("Content-Transfer-Encoding: binary");
		readfile($tempfile);
		unlink($tempfile);
		Yii::app()->end();
	}

	/**
	 * print all new RTS shipments labels
	 * @param $id
	 */
	public function actionRtclabels($id)
	{
		$rtsList = RtsRtcList::model()->findByPk($id);
		$shipments = [];
		foreach ($rtsList->lines as $line) {
			$model = new $line->model;
			$shipment = $model->findByPk($line->fid);
			if (!empty($shipment)) {
				$shipments[] = $shipment;
			}
		}
		if (!empty($shipments)) {
			oPDF::renderPDF('label_A6', ['tpl' => '_label-eparcel', 'empty' => false, 'rs' => $shipments]);
		}
		Yii::app()->end();
	}

	public function actionRtcgatepass($id)
	{
		$m = RtsRtcList::model()->findByPk($id);
		oPDF::renderPDF('rts_gatepass', ['m' => $m, 'rtc' => true]);
	}

	/**
	 * create RTS ready list all shipments in list will be ready for pickup again
	 * @param $shipments
	 * @param $note
	 * @return string
	 */
	private function createRtsList(&$shipments, $note, $company, $dptid)
	{

		// create new Rts shipments List
		$modelRtsList = new RtsList('create');
		$modelRtsList->status = 10;
		$modelRtsList->ref = $note;
		$modelRtsList->mdata['rts_company'] = $company;
		$modelRtsList->dpt_id = $dptid; // default as Sydney
		$modelRtsList->save();

		// save related all shipment
		foreach ($shipments as $shipment) {
			$attr = [
				'mani_id' => $modelRtsList->id,
				'fid' => $shipment->id,
				'model' => 'ImParcel',
			];
			$mm = new ManiMap;
			$mm->attributes = $attr;
			$mm->status = 10;
			$mm->save();
		}
		return $modelRtsList->id;
	}

	/**
	 * create RTS RTC ready list all shipments in list will be ready for return to customer directly
	 * @param $shipments
	 * @param $note
	 * @return string
	 */
	private function createRtsRtcList(&$shipments, $note, $company, $dptid)
	{

		// create new Rts RTC shipments List
		$modelRtsRtcList = new RtsRtcList('create');
		$modelRtsRtcList->status = 10;
		$modelRtsRtcList->ref = $note;
		$modelRtsRtcList->mdata['rts_company'] = $company;
		$modelRtsRtcList->dpt_id = $dptid; // default as Sydney
		$modelRtsRtcList->save();

		// save related all shipment
		foreach ($shipments as $shipment) {
			$attr = [
				'mani_id' => $modelRtsRtcList->id,
				'fid' => $shipment->id,
				'model' => 'ImParcel',
			];
			$mm = new ManiMap;
			$mm->attributes = $attr;
			$mm->status = 10;
			$mm->save();
		}
		return $modelRtsRtcList->id;
	}

	/**
	 * @param $id
	 *
	 */
	public function actionUpdate($id)
	{
		$model = ImParcel::model()->findByPk($id);
		$defaultSelect = "";
		if(!empty($model->mdata['d2ztype']))
		{
			$defaultSelect = "aus";
		}
		// we need to check to see if you have changed some thing
		if (isset($_POST['Cnee'])) {
			$consigneeInfo = $_POST['Cnee'];

			// only create a new one for consignee info changed
			if ($this->consigneeInfoChanged($model->cnee, $consigneeInfo) || (!empty($_POST['courier_rts']))) {
				$model->cnee->attributes = $consigneeInfo;
				$model->postcode = $consigneeInfo['postcode'];
				$model->state = $consigneeInfo['state'];
				$newShipment = $this->createNewShipment($model, $_POST['courier_rts']);

				$errs = $newShipment->getErrors();
				if (empty($errs)) {
					$model = ImParcel::model()->findByPk($id);
					$model->status = 81; // set original one as RTS in progressing
					$model->update('status');

					// generate invoice for create new shipment
					//$this->createRTSNewShipmentInvoice($newShipment);

					$this->ajaxResult($model, ['id'], 'New shipment(' . $newShipment->hbn . ') has been created successfully!');
				} else {
					$this->ajaxResult($newShipment);
				}
			} else {
				$this->ajaxResult($model, ['id'], 'No any changed found!');
			}
		}

		if (!empty($model)) {
			$this->render('update', [
				'model' => $model,
				'defaultSelect'=>$defaultSelect
			]);
		}
	}

	/**
	 * @param $id
	 */
	public function actionDisposal($id)
	{
		$model = ImParcel::model()->findByPk($id);
		$this->ajaxResult($model, ['id'], 'Disposal done!');
	}

	/**
	 * create new shipment invoice
	 * @param $shipment
	 */
	private function createRTSNewShipmentInvoice(&$parcel)
	{

		// create new shipment label invoice
		// check to see if related pending invoice existing
		$invLine = InvLine::model()->find('model = :model AND fid = :fid', [':model' => 'ImParcel', 'fid' => $parcel->id]);
		$invoice = null;
		if (!empty($invLine)) {
			// try to check related invoice belongs to RTS fee or not
			$invoice = Invoice::model()->findByPk($invLine->inv_id);
			if (!empty($invoice) && $invoice->type == Invoice::INVOICE_TYPE_RTS_FEE) {
				// delete all old invoice line
				InvLine::model()->deleteAll('inv_id = :invid', [':invid' => $invoice->id]);
			}
		}
		if (empty($invoice)) {
			// not existing yet , just create a new one
			$invoice = new Invoice();
			$invoice->type = Invoice::INVOICE_TYPE_RTS_FEE; // for RTS fee invoice
			$invoice->to_id = $parcel->agent_id;
			$invoice->man_id = 0; // in case manifest id means nothing
			$invoice->dpt_id = $parcel->ddpt_id;
			$invoice->dpmt = Invoice::DPMT_IMPORT;

			// if department not set , we set as Sydney warehouse
			if (empty($invoice->dpt_id)) {
				$invoice->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
			}
			$invoice->status = Invoice::INVOICE_STATUS_PENDING;
			$invoice->date = date('Y-m-d'); // here just dummy date , once accounting change to posted , should create new date for the invoice

			// get invoice currency
			$invoiceCurrency = 1; // default as AUD
			$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $parcel->agent_id]);
			if (!empty($orgRate)) {
				$invoiceCurrency = $orgRate['currency'];
			}
			$invoice->currency = $invoiceCurrency;

			// set invoice name , address and pay terms information
			$owner = $parcel->agent;
			$invoice->mdata['name'] = $owner->name;
			$invoice->mdata['address'] = $owner->getAddress();
			$invoice->mdata['payterm'] = empty($owner->extra['payterm']) ? '2 days' : $owner->extra['payterm'] . ' days';

			// here just dummy date , once accounting change to posted , should create new date for the invoice
			$invoice->due = Invoice::calcDue($invoice->date, $invoice->mdata['payterm']);
		}

		// get RTS fee
		$rtsFee = $this->getRtsModRate($parcel->agent_id);
		$invoice->total = $rtsFee;
		$invoice->save();

		// create related invoice line and attached it to the invoice
		$il = new InvLine;
		$il->inv_id = $invoice->id;
		$il->amount = $rtsFee;
		$il->mdata['items'] = [[$parcel->hbn, 'RTS Mod Fee', $rtsFee]];
		$il->model = 'ImParcel';
		$il->fid = $parcel->id;
		$il->save();
	}

	/**
	 * create new delivery invoice
	 * @param $shipment
	 */
	private function createRTSDeliveryInvoice(&$parcel)
	{
		// TODO soon
		// charged by another manifest already maybe
	}

	/**
	 * create a new shipment for returned item
	 * @param $orgShipment
	 * @return string
	 */
	private function createNewShipment(&$orgShipment, $cr = '')
	{
		$newShipment = new ImParcel('create');

		// copy basic attributes
		$newShipment->attributes = $orgShipment->attributes;
		$cnor = new Addr;
		$cnor->attributes = $orgShipment->cnor->attributes;
		$cnor->save();
		$newShipment->cnor_id = $cnor->id;
		$cnee = new Addr;
		$cnee->attributes = $orgShipment->cnee->attributes;

		$cnee->save();
		$newShipment->cnee_id = $cnee->id;
		$newShipment->eitems = $orgShipment->eitems;
		$newShipment->mdata = $orgShipment->mdata;
		unset($newShipment->mdata['direct_courier']);
		unset($newShipment->mdata['rts_scan_date']);
		unset($newShipment->mdata['ss_lbl_request_id']);

		unset($newShipment->mdata['import_billing_id']);
		unset($newShipment->mdata['old_import_billing_id']);
		unset($newShipment->mdata['location']);
		unset($newShipment->mdata['used_location']);
		unset($newShipment->mdata['eiz']);
		unset($newShipment->mdata['eiz_id']);
		$newShipment->hbn = ''; //  in order to create a new one in case
		$newShipment->ref = '';
		$newShipment->status = 25; // set as Received status
		$newShipment->consol_id = 0;
		$newShipment->created = date('Y-m-d');

		// save original RTS shipment No.
		$newShipment->mdata['rts_org_no'] = $orgShipment->hbn;
		$newShipment->note .= $orgShipment->ref;
		$newShipment->bwf = 0;

		if ($newShipment->save()) {
			$createOk = true;
		} else {
			$createOk = false;
			// var_dump($newShipment->getErrors());
			return $newShipment;
		}

		// currently default resend by Australia postoffice
		// if Aupost we need to create new ref, fastway need to get ref later when confirm to resend.so we just use previous ref for fastway at this stage.

		if (((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_FASTWAY) && empty($cr)) || $cr == 'fw') {
			if (!$this->sent2fastway($newShipment)) {
				//fastway need to create label if not success,means the address not accessed by fastway.
				$newShipment->delete();
				$newShipment->addError('id', 'Fastway can not delivery to this address');
				return $newShipment;
			}
		} elseif (((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_STARTRACK) && empty($cr)) || $cr == 'st-syd') {
			if (!$this->createStartrackLabel($newShipment)) {
				$newShipment->delete();
				$newShipment->addError('id', 'Startrack can not delivery to this address');
				return $newShipment;
			}
		} else if (((isset($orgShipment->trans) && !empty($orgShipmnet->trans) && $orgShipment->trans[0]->org_id == ORG::ORGID_COURIER_STARTRACK_MEL) && empty($cr)) || $cr == 'st-mel') {
			$newShipment->createStartrackLabel('mel');
		} elseif (((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_TNT_TOP) && empty($cr)) || $cr == 'tnt-syd-top') {
			$tnt = TntAPI::getTntInterface('syd_top');
			$result = $tnt->createOneShipment($newShipment);
			if ($result['status']) {
				$ts = new Tranship;
				$ts->pid = $newShipment->id;
				$ts->org_id = Org::ORGID_COURIER_TNT_TOP; // for tnt top
				$ts->man_id = $newShipment->man_id;
				$ts->type = 80; // shipment transfer to a different delivery courier
				$ts->status = 19; // in finally moving status
				$ts->connote = $newShipment->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->save();
			}else
			{
				$newShipment->delete();
				$orgShipment->status = ImParcel::RTS_RECEIVED;
				$orgShipmnet->save();
				$newShipment->addError('id', 'TNT create Label Error, Please try again later');
				return $newShipment;
			}
		} else if (((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_TNT_TOP) && empty($cr)) || $cr == 'tnt-mel-top') {
			$tnt = TntAPI::getTntInterface('mel_top');
			$result = $tnt->createOneShipment($newShipment);
			if ($result['status']) {
				$ts = new Tranship;
				$ts->pid = $newShipment->id;
				$ts->org_id = Org::ORGID_COURIER_TNT_TOP; // for StarTrack
				$ts->man_id = $newShipment->man_id;
				$ts->type = 80; // shipment transfer to a different delivery courier
				$ts->status = 19; // in finally moving status
				$ts->connote = $newShipment->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->save();
			}else
			{
				$newShipment->delete();
				$orgShipment->status = ImParcel::RTS_RECEIVED;
				$orgShipmnet->save();
				$newShipment->addError('id', 'TNT create Label Error, Please try again later');
				return $newShipment;
			}
		} else if (((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_TNT_TOP) && empty($cr)) || $cr == 'tnt-bne-top') {
			$tnt = TntAPI::getTntInterface('bne_top');
			$result = $tnt->createOneShipment($newShipment);
			if ($result['status']) {
				$ts = new Tranship;
				$ts->pid = $newShipment->id;
				$ts->org_id = Org::ORGID_COURIER_TNT_TOP; // for StarTrack
				$ts->man_id = $newShipment->man_id;
				$ts->type = 80; // shipment transfer to a different delivery courier
				$ts->status = 19; // in finally moving status
				$ts->connote = $newShipment->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->save();
			}else
			{
				$newShipment->delete();
				$orgShipment->status = ImParcel::RTS_RECEIVED;
				$orgShipmnet->save();
				$newShipment->addError('id', 'TNT create Label Error, Please try again later');
				return $newShipment;
			}
		}else if (((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_TNT_TOP) && empty($cr)) || $cr == 'tnt-per-top') {
			$tnt = TntAPI::getTntInterface('per_top');
			$result = $tnt->createOneShipment($newShipment);
			if ($result['status']) {
				$ts = new Tranship;
				$ts->pid = $newShipment->id;
				$ts->org_id = Org::ORGID_COURIER_TNT_TOP; // for StarTrack
				$ts->man_id = $newShipment->man_id;
				$ts->type = 80; // shipment transfer to a different delivery courier
				$ts->status = 19; // in finally moving status
				$ts->connote = $newShipment->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->save();
			}else
			{
				$newShipment->delete();
				$orgShipment->status = ImParcel::RTS_RECEIVED;
				$orgShipmnet->save();
				$newShipment->addError('id', 'TNT create Label Error, Please try again later');
				return $newShipment;
			}
		}else if (((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_HUNTER) && empty($cr)) || $cr == 'hunter') {
			$newShipment->createHunterLabel();
			// $hunter = new HunterAPI(false);
			// $result = $hunter->bookingJob($newShipment);
			// if (!empty($result['trackingNumber'])) {
			// 	$newShipment->ref = $result['trackingNumber'];
			// 	$newShipment->update('ref');

			// 	$ts = new Tranship;
			// 	$ts->pid = $newShipment->id;
			// 	$ts->org_id = Org::ORGID_COURIER_HUNTER;
			// 	$ts->man_id = $newShipment->man_id;
			// 	$ts->type = 80;
			// 	$ts->status = 19;
			// 	$ts->connote = $newShipment->ref;
			// 	$ts->time = date('Y-m-d H:i:s');
			// 	$ts->mdata['hunter_job_number'] = $result['jobNumber'];
			// 	$ts->mdata['oid'] = 140;
			// 	$ts->cost = number_format(round($result['fee'], 2), 2, '.', '');
			// 	$ts->save();
			// }
		} else if ((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_D2Z && empty($cr) && @$orgShipment->mdata['d2ztype'] == 'syd') || $cr == 'd2z-syd') {
			$newShipment->createD2zLabel('syd');
		} else if ((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_D2Z && empty($cr) && (@$orgShipment->mdata['d2ztype'] == 'mel'||@$orgShipment->mdata['d2ztype'] == 'local-mel')) || $cr == 'd2z-mel') {
			$newShipment->createD2zLabel('mel');
		} else if ((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_D2Z && empty($cr) && @$orgShipment->mdata['d2ztype'] == 'bri') || $cr == 'd2z-bri') {
			$newShipment->createD2zLabel('bri');
		} else if ((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_D2Z && empty($cr) && @$orgShipment->mdata['d2ztype'] == 'per') || $cr == 'd2z-per') {
			$newShipment->createD2zLabel('per');
		} else if ((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_D2Z && empty($cr) && @$orgShipment->mdata['d2ztype'] == 'local') || $cr == 'd2z-local') {
			$newShipment->createD2zLabel('local');
		} else if ((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_D2Z && empty($cr))) {
			$newShipment->createD2zLabel();
		}else if ($cr == 'eiz-toll-syd')
		{
			$newShipment->createEizLabel(ImportChargeCode::EIZ_TOLL_ID,'syd');
		}else if ($cr == 'eiz-toll-mel')
		{
			$newShipment->createEizLabel(ImportChargeCode::EIZ_TOLL_MEL_ID,'mel');
		}else if ($cr == 'eiz-toll-bne')
		{
			$newShipment->createEizLabel(ImportChargeCode::EIZ_TOLL_BNE_ID,'bne');
		}else if ($cr == 'ubi-toll-syd')
		{
			$orgRate = OrgRate::model()->find('code = :code',[":code"=>ImportChargeCode::UBI_TOLL_SYD_CODE]);
			$newShipment->mdata['etower_shipment_orderid'] = "";
			$newShipment->mdata['ubi_toll'] = "";
			$newShipment->mdata['barcode'] = "";
			$newShipment->save();
			$newShipment->createCourierLabel($orgRate);
		}else if ($cr == 'ubi-toll-mel')
		{
			$orgRate = OrgRate::model()->find('code = :code',[":code"=>ImportChargeCode::UBI_TOLL_MEL_CODE]);
			$newShipment->mdata['etower_shipment_orderid'] = "";
			$newShipment->mdata['ubi_toll'] = "";
			$newShipment->mdata['barcode'] = "";
			$newShipment->save();
			$newShipment->createCourierLabel($orgRate);
		}else if ($cr == 'my-toll-syd')
		{
			$orgRate = OrgRate::model()->findByPk(ImportChargeCode::MYTOLL_SYD_ID);
			$newShipment->createCourierLabel($orgRate);
		}else if ($cr == 'my-toll-mel')
		{
			$orgRate = OrgRate::model()->findByPk(ImportChargeCode::MYTOLL_MEL_ID);
			$newShipment->createCourierLabel($orgRate);
		}
		else if ($cr == 'aus-mel')
		{
			$newShipment->ref = ImParcel::genEparcelNo(Org::ORGID_COURIER_AUPOST,'33EVH');
			$newShipment->mdata['test_aupost_2017'] = 1;
			if (isset($newShipment->mdata['test_aupost_2014'])) {
				unset($newShipment->mdata['test_aupost_2014']);
			}
			$newShipment->update('ref');
			$newShipment->updateMeta();
		}else if ($cr == 'aus-per')
		{
			$newShipment->ref = ImParcel::genEparcelNo(Org::ORGID_COURIER_AUPOST,'34AWE');
			$newShipment->mdata['test_aupost_2017'] = 1;
			if (isset($newShipment->mdata['test_aupost_2014'])) {
				unset($newShipment->mdata['test_aupost_2014']);
			}
			$newShipment->update('ref');
			$newShipment->updateMeta();
		}else if ($cr == 'aus-bne')
		{
			$newShipment->ref = ImParcel::genEparcelNo(Org::ORGID_COURIER_AUPOST,'33EVJ');
			$newShipment->mdata['test_aupost_2017'] = 1;
			if (isset($newShipment->mdata['test_aupost_2014'])) {
				unset($newShipment->mdata['test_aupost_2014']);
			}
			$newShipment->update('ref');
			$newShipment->updateMeta();
		}else
		{
		// } else if ((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_DFE && empty($cr))) {
		// 	$newShipment->createDfeLabel(ImportChargeCode::DFE_ID,'syd');

		// } else if ($cr == 'dfe-syd')
		// {
		// 	$newShipment->createDfeLabel(ImportChargeCode::DFE_ID,'syd');
		// }else if ((isset($orgShipment->trans) && !empty($orgShipment->trans) && $orgShipment->trans[0]->org_id == Org::ORGID_COURIER_DFE_TOP && empty($cr))) {
		// 	$code = OrgRate::model()->findByPk($orgShipment->mdata['org_rate_id'])->code;
		// 	switch ($code) {
		// 		case ImportChargeCode::DFE_TOP_SYD_CODE:
		// 			$newShipment->createDfeLabel(ImportChargeCode::DFE_ID_TOP,'syd_top');
		// 			break;
		// 		case ImportChargeCode::DFE_TOP_MEL_CODE:
		// 			$newShipment->createDfeLabel(ImportChargeCode::DFE_MEL_ID_TOP,'mel_top');
		// 			break;
		// 		case ImportChargeCode::DFE_TOP_BNE_CODE:
		// 			$newShipment->createDfeLabel(ImportChargeCode::DFE_BNE_ID_TOP,'bne_top');
		// 			break;
		// 		default:
		// 			$newShipment->createDfeLabel(ImportChargeCode::DFE_ID_TOP,'syd_top');
		// 			break;
		// 	}
		// } else if ($cr == 'dfe-syd-top')
		// {
		// 	$newShipment->createDfeLabel(ImportChargeCode::DFE_ID_TOP,'syd_top');
		// }else if ($cr == 'dfe-mel-top')
		// {
		// 	$newShipment->createDfeLabel(ImportChargeCode::DFE_MEL_ID_TOP,'mel_top');
		// }else if ($cr == 'dfe-bne-top')
		// {
		// 	$newShipment->createDfeLabel(ImportChargeCode::DFE_BNE_ID_TOP,'bne_top');
		// } else {
			//others only default Aupost currently
			$newShipment->ref = ImParcel::genEparcelNo();
			$newShipment->mdata['test_aupost_2017'] = 1;
			if (isset($newShipment->mdata['test_aupost_2014'])) {
				unset($newShipment->mdata['test_aupost_2014']);
			}
			$newShipment->update('ref');
			$newShipment->updateMeta();
		}

		$orgShipment->note .= $newShipment->ref;
		$orgShipment->update(['note']);
		// add tracking information for new one
		$newShipment->addTracking(95, 'Shipment tranship from : ' . $orgShipment->hbn);

		// add new tranship
		// in order to link original one with new one
		$ts = new Tranship;
		$ts->pid = $orgShipment->id;
		$ts->org_id = 101; // default as Australia Post Office
		$ts->man_id = 0;
		$ts->type = 90; // RTS Delivery Courier
		$ts->status = 19; // Final moving
		$ts->connote = $newShipment->hbn;
		$ts->time = date('Y-m-d H:i:s');
		$ts->save();

		// add tracking information for original one
		$orgShipment->addTracking(95, 'Shipment tranship with : ' . $newShipment->hbn);

		return $newShipment;
	}

	/**
	 * check to see if the consignee information has been changed
	 * @param $org
	 * @param $new
	 * @return bool
	 */
	private function consigneeInfoChanged(&$org, &$new)
	{
		if ($org->name != $new['name']) {
			return true;
		}
		if ($org->address != $new['address']) {
			return true;
		}
		if ($org->suburb != $new['suburb']) {
			return true;
		}
		if ($org->state != $new['state']) {
			return true;
		}
		if ($org->postcode != $new['postcode']) {
			return true;
		}
		if ($org->country != $new['country']) {
			return true;
		}
		if ($org->tel != $new['tel']) {
			return true;
		}
		if ($org->email != $new['email']) {
			return true;
		}
		return false;
	}

	public function actionDiscard()
	{
		$exclude = [1206, 1427];
		$rs = ImParcel::model()->findAll('status = 80 AND agent_id NOT IN (' . implode(',', $exclude) . ')');

		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth([20, 20, 20, 20]);
		$xls->addRow($i++, ['hbn', 'ref', 'scan_date', 'location']);

		$endDate = new DateTime(date('Y-m-d'), new DateTimeZone('Australia/Sydney'));
		foreach ($rs as $p) {
			if (empty($p->mdata['rts_scan_date'])) {
				continue;
			}

			$startDate = new DateTime($p->mdata['rts_scan_date'], new DateTimeZone('Australia/Sydney'));
			if ($startDate->diff($endDate)->days > 30) {
				$xls->addRow($i++, [$p->hbn, $p->ref, $p->mdata['rts_scan_date'], @$p->mdata['location']]);
			}
		}

		$xls->output('discard_list_' . date('Ymd') . '.xlsx');
	}

	public function actionReturnOrg($org_id)
	{
		$rs = ImParcel::model()->findAll('status = 80 AND agent_id IN (' . $org_id . ')');

		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth([20, 20, 20, 20]);
		$xls->addRow($i++, ['hbn', 'ref', 'scan_date', 'location']);

		foreach ($rs as $p) {
			if (empty($p->mdata['rts_scan_date'])) {
				continue;
			}

			$xls->addRow($i++, [$p->hbn, $p->ref, $p->mdata['rts_scan_date'], @$p->mdata['location']]);
		}

		$xls->output('return_list_' . $org_id . '_' . date('Ymd') . '.xlsx');
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'import-rts-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
