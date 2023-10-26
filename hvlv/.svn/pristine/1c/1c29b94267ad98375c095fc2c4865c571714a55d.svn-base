<?php

class LedgerController extends Controller
{
	protected $nonAjax = ['delete'];
	protected $skipAcl = [];

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){
		$model=new Ledger;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Ledger']))
		{
			$model->attributes=$_POST['Ledger'];
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id){
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Ledger']))
		{
			$model->attributes=$_POST['Ledger'];
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * default cost for Import Or Export setting manager
	 */
	public function actionDefault(){
		$this->render('default');
	}

	/**
	 * create new ledger
	 */
	public function actionGrid(){
		if(isset($_POST['Ledger'])){
			if ( empty($_POST['Ledger']['from_id'])  ) {
				$error = 'Supplier is mandatory';
			} else if ( empty($_POST['Ledger']['chgcode'])  ) {
				$error = 'Charge code is mandatory';
			} else if ( empty($_POST['Ledger']['accrual_amount'])  ) {
				$error = 'Accrual amount is mandatory';
			}

			$model = new Ledger();
			if ( empty($error) ) {
				if ( !empty($_POST['Ledger']['id'])) {
					$model = Ledger::model()->findByPk($_POST['Ledger']['id']);
					if ( empty($model) )  $model = new Ledger();
				}

				$model->attributes = $_POST['Ledger'];
				$model->type = $_GET['type'];
				$model->model = $_GET['model'];
				$model->fid = $_GET['fid'];
				$model->save();
				$errors = $model->getErrors();

				// save import consol cost to our Billing DB
				if ( empty($errors) && ( $model->model === 'ImcoConsol' || $model->model === 'DmawbConsol' )) {
					if (!empty($errors)) $model->addErrors($errors);
				}
			} else {
				$model->addError('id',$error);
			}
			$this->ajaxResult($model);
		}
	}

	/**
	 *  types linked GL Code pls find below
	•	Export Documentation Fee - GST FREE (2030.00.30)
	•	Export Security Fee – GST (2020.00.30)
	•	EDF Surcharge (Missing FWB) (2020.00.30)
	•	Air Freight (2010.00.30)
	 */
	public function actionExGrid(){
		if(isset($_POST['ExLedger'])){
			if(empty($_POST['ExLedger']['id'])){
				$model = new ExLedger;
				$model->created = date('Y-m-d h:i:s');
			}else{
				$model = ExLedger::model()->findByPk($_POST['ExLedger']['id']);
			}
			$model->attributes=$_POST['ExLedger'];
			$model->type = $_GET['type'];
			$model->model = $_GET['model'];
			$model->fid = $_GET['fid'];

			// calculate total
			$gst = $_POST['ExLedger']['gst'];
			$amount = floatval($_POST['ExLedger']['amount']);
			$total = $amount;

			// in case GST on Expenses or GST on Capital
			if ( $gst == 'INPUT' || $gst == 'CAPEXINPUT') {
				$total += ($total * 10) / 100;
			}
			$total = round($total,2);
			$model->total = $total;

			$model->save();

			$errors = $model->getErrors();
			if ( empty($errors) ) {
				// save to our billing DB

				if ( !empty($rtErrors) ) {
					$model->addError('id','Failed to save to billing module');
				}
			}
			$this->ajaxResult($model);
		}
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new Ledger('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Ledger']))
			$model->attributes=$_GET['Ledger'];

		$this->render('list',array(
			'model'=>$model,
		));
	}

	/**
	 *
	 */
	public function actionInput(){
		$model['gllist'] = new Ledger();
		$model['data'] = array();
		$model['date'] = date('Y-m-d');
		$this->render('input',['model' => $model]);
	}

	/**
	 * search all waiting input cost items based on specified conditions
	 */
	public function actionAjaxSearchInput(){
		$chargeCode = $_POST['Ledger']['chgcode'];
		$type = $_POST['type'];
		$allLedgers = Ledger::model()->findAll('chgcode = :code AND model = :mod AND status = 1',[':code' => $chargeCode,':mod' => $type]);
		$model['gllist'] = new Ledger();

		$allData = array();
		foreach ( $allLedgers as $ledger ) {
			if ( $ledger->model == 'ImcoConsol' || $ledger->model == 'ExcoConsol') {
				$console = Consol::model()->findByPk($ledger->fid);
				$chargeCodeInfo = Chargecode::model()->find('status = 1 AND code = :code' ,[':code' => $chargeCode] );
				$chargCodeName = '';
				if ( !empty($chargeCodeInfo) ) $chargeCodeName = $chargeCodeInfo->name;
				$allData[] = array(
					'id' => $ledger->id,
					'code' => $ledger->chgcode . ':' . $chargeCodeName,
					'currency' => $ledger->currency,
					'cost' => $ledger->accrual_amount,
					'console' => $console->no,
					'console_id' => $console->id,
					'console_model' => $ledger->model
				);
			}
		}
		$model['data'] = $allData;
		$model['date'] = date('Y-m-d');
		$this->renderPartial('_ledger_input_search',array('model' => $model),false, true);
	}

	/**
	 * @throws CException
	 */
	public function actionAjaxInputCommit(){
		$date = $_POST['date'];
		if ( empty($date) ) $date = date('Y-m-d');
		$invoice = $_POST['invoice'];

		$resp = array('msg' => 'Input successfully!');
		// update all ledger real cost amout and related information
		$items = $_POST['items'];
		if ( !empty($items) && is_array( $items) ) {
			foreach ( $items as $id => $data ){
				$cost = floatval($data['cost']);
				if ( $cost <= 0.00 ) continue; // only update valid cost row
				$ledger = Ledger::model()->findByPk($id);
				$ledger->date = $date;
				$ledger->no = $invoice;
				$ledger->notes = $data['note'];
				$ledger->total = $cost;
				$ledger->currency = $data['currency'];
				$ledger->exchange_rate = $data['rate'];
				$ledger->status = 6; // set as posted status
				$ledger->save();
			}
		} else {
			$resp['msg'] = 'Sorry no more data to be updated';
		}
		echo json_encode($resp);
	}


	/**
	 *
	 */
	public function actionApprove(){
		$model['gllist'] = new Ledger();
		$model['data'] = array();
		$model['date'] = date('Y-m-d');
		$model['supplier'] = '';
		$this->render('approve',['model' => $model]);
	}

	/**
	 * search all pending input cost items based on specified conditions
	 */
	public function actionAjaxSearchApprove(){
		$chargeCode = $_POST['Ledger']['chgcode'];
		$invoice = trim($_POST['sinvoice']);
		$allLedgers = Ledger::model()->findAll('(no = :invoice OR chgcode = :code ) AND status = 6',[':code' => $chargeCode,':invoice' => $invoice]);
		$model['gllist'] = new Ledger();

		$allData = array();
		foreach ( $allLedgers as $ledger ) {
			if ( $ledger->model == 'ImcoConsol' || $ledger->model == 'ExcoConsol') {
				$console = Consol::model()->findByPk($ledger->fid);
				$chargeCodeInfo = Chargecode::model()->find('status = 1 AND code = :code' ,[':code' => $ledger->chgcode] );
				$chargCodeName = '';
				if ( !empty($chargeCodeInfo) ) $chargeCodeName = $chargeCodeInfo->name;
				$allData[] = array(
					'id' => $ledger->id,
					'code' => $ledger->chgcode . ':' . $chargeCodeName,
					'currency' => $ledger->currency,
					'cost' => $ledger->accrual_amount,
					'total' => $ledger->total,
					'exchange_rate' => $ledger->exchange_rate,
					'notes' => $ledger->notes,
					'invoice' => $ledger->no,
					'console' => $console->no,
					'console_id' => $console->id,
					'console_model' => $ledger->model
				);
			}
		}
		$model['data'] = $allData;
		$model['date'] = date('Y-m-d');
		$model['supplier'] = '';
		$this->renderPartial('_ledger_approve_search',array('model' => $model),false, true);
	}

	/**
	 * @throws CException
	 */
	public function actionAjaxApproveCommit(){
		$date = $_POST['date'];
		if ( empty($date) ) $date = date('Y-m-d');
		$invoice = $_POST['invoice'];

		$resp = array('msg' => 'Approved successfully!');

		// get supplier id
		$supplierId = $_POST['Ledger']['supplierId'];
		$supplier = Org::model()->findByPk($supplierId);
		if (  empty($supplier) ) {
			$resp['msg'] = 'Please set an valid supplier';
			echo json_encode($resp);
			return;
		}

		// check invoice
		if ( empty($invoice) ) {
			$resp['msg'] = 'Please set invoice';
			echo json_encode($resp);
			return;
		}

		// update all ledger real cost amout and related information
		$items = $_POST['items'];
		if ( !empty($items) && is_array( $items) ) {

			/**
			 * sync and save local bill to xero
			 * @param $data
			 * {
			 *   no -- invoice number unique id
			 *   currency -- default 1 AUD
			 *   orgName
			 *   orgId
			 *   date , due, gstType ( 1-Exclusive,2-Inclusive,others - NoTax)
			 *   lines(qty,description,amount,code,taxType)
			 * }
			 */
			$xdata = new stdClass();
			$xdata->no = $invoice;
			$xdata->currency = 1;

			// get bill org
			$xdata->orgName = $supplier->name;
			$xdata->orgId = $supplierId;

			$xdata->date = date('Y-m-d');
			$xdata->due = date('Y-m-d',strtotime('+7 days'));
			$xdata->gstType = 2;

			$lines = array();
			foreach ( $items as $id => $data ){
				$cost = floatval($data['cost']);
				if ( $cost <= 0.00 ) continue; // only update valid cost row
				$ledger = Ledger::model()->findByPk($id);
				$chargeCodeInfo = Chargecode::model()->find('status = 1 AND code = :code' ,[':code' => $ledger->chgcode] );
				$chargCodeName = '';
				if ( !empty($chargeCodeInfo) ) $chargeCodeName = $chargeCodeInfo->name;

				$ledger->date = $date;
				$ledger->no = $invoice;
				$ledger->notes = $data['note'];
				$ledger->total = $cost;
				$ledger->currency = $data['currency'];
				$ledger->exchange_rate = $data['rate'];
				$ledger->status = 9; // set as approved status
				$ledger->save();

				$lines[] = array('qty' => 1,
					'description' => $chargeCodeName,
					'code' => $ledger->chgcode,
					'amount' => $ledger->total,
					'taxType' => 'BASEXCLUDED'); // taxType should be mod TODO soon
			}

			// save to xero as well
			if ( !empty($lines) ) {
				$xdata->lines = $lines;
				Invoice::saveBill2Xero($xdata);
			}
		} else {
			$resp['msg'] = 'Sorry no more data to be updated';
		}
		echo json_encode($resp);
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=Ledger::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='ledger-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
