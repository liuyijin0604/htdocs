<?php

class EdiJobController extends Controller
{
	protected $nonAjax=['ajaxGetAwbCustomer','getAttachs','ajaxCreateInvoice','export','ajaxUpdateJobCost','mainJobLink', 'ajaxAutofill', 'securityDec'];

	/**
	 * Lists and search.
	 */
	public function actionList()
	{
		$model = new EdiJob('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['EdiJob'])) {
			$model->attributes = $_GET['EdiJob'];
		}
		$this->render('list', [
			'model'=>$model,
		]);
	}
	public function actionMissingAccrualFor3PL()
	{
		$model = new EdiJob('search');
		$userId = 0;
		if (isset($_GET['uid'])) {
			$userId = $_GET['uid'];
		}

		$criteria = new CDbCriteria;
		//$criteria->condition =  "select count(job_line.id) from job left join job_line on job.id = job_line.job_id where job.dpmt = 40 and job.created <= '". date('Y-m-d',strtotime('-30 days')) ."' and job_line.cost_amount = 0";
		$criteria->join = 'left join job_line on t.id = job_line.job_id';
		$criteria->condition =  "t.dpmt = 40 and t.created >= '2017-07-01' and job_line.cost_amount = 0";
		if ($userId > 0) {
			$criteria->condition .=  " and t.user_id = " . $userId;
		}
		$modeldp =  new CActiveDataProvider($model, [
			'criteria'=>$criteria,
			'sort'=>[
				'defaultOrder'=>'t.id DESC',
			],
			'pagination'=>[
				'pageSize' => 30,
			]
		]);

		$this->render('missing_3pl_accrual_list', ['modeldp' => $modeldp,'model' => $model]);
	}

	public function actionMissingAccrualForFreight()
	{
		$model = new EdiJob('search');

		$userId = 0;
		if (isset($_GET['uid'])) {
			$userId = $_GET['uid'];
		}

		$criteria = new CDbCriteria;
		// $criteria->condition =  "select count(job_line.id) from job left join job_line on job.id = job_line.job_id where job.dpmt = 30 and job.created <= '". date('Y-m-d',strtotime('-30 days')) ."' and job_line.cost_amount = 0";
		$criteria->join = 'left join job_line on t.id = job_line.job_id';
		$criteria->condition =  "t.dpmt = 30 and t.created >= '2017-07-01' and job_line.cost_amount = 0";

		if ($userId > 0) {
			$criteria->condition .=  " and t.user_id = " . $userId;
		}

		$modeldp =  new CActiveDataProvider($model, [
			'criteria'=>$criteria,
			'sort'=>[
				'defaultOrder'=>'t.id DESC',
			],
			'pagination'=>[
				'pageSize' => 30,
			]
		]);

		$this->render('missing_freight_accrual_list', ['modeldp' => $modeldp,'model' => $model]);
	}

	/**
	 * show all basic edi templates
	 */
	public function actionMngEdiBasicTemplate()
	{
		$model = new EdiJobBasicTemplate();
		$this->render('edi_basic_template_list', ['model' => $model]);
	}

	/**
	 * create basic edi template
	 */
	public function actionCreateEdiBasicTemplate($id)
	{
		$model = EdiJobBasicTemplate::model()->findByPk($id);
		if (empty($model)) {
			$model = new EdiJobBasicTemplate();
		}

		if (isset($_POST['JobLine'])) {
			// save EDI job template
			$errors = $this->saveEdiBasicJobTemplate($id);
			if (!$errors && !empty($errors)) {
				$model->addErrors($errors);
			}
			$this->ajaxResult($model);
		}

		$this->render('edi_basic_template', ['model' => $model]);
	}

	public function actionAjaxAddEdiTemplate()
	{
		$job = EdiJob::model()->findByPk($_POST['jid']);
		$resp = array('success' => 0);
		$tid = $_POST['tid'];
		$template = EdiJobTemplate::model()->find('id = :oid',[':oid' => $tid] ) ;
		if ( !empty($template) && !empty($template->meta) ) {
			$data = json_decode($template->meta);

			foreach ( $data->invoice as $invline ) {
				$il = new JobLine;
				$il->job_id = $_POST['jid'];
				$il->ccode = $invline->ccode;
				$il->desc = $invline->desc;
				$il->qty = $invline->qty;
				$il->rate = $invline->rate;
				$il->inv_gst = $invline->inv_gst;
				$il->invoice_date = $job->created;
				$il->invoice_due_date = $job->due;
				$il->save();
			}

			foreach ($data->cost as $costline) {
				$il = new BillingLine;
				$il->item_code = $costline->item_code;
				$il->desc = $costline->desc;
				$il->qty = $costline->qty;
				$il->price = $costline->price;
				$il->gst = $costline->gst;
				$il->org_id = $costline->org_id;
				$errMsg = BillingLine::saveExportAirFreightBillingByBilling($job, $il);
			}

			if (empty($errMsg)) {
				$resp['success'] = 1;
			} else {
				$resp['success'] = 0;
				$resp['errMsg'] = $errMsg;
			}
		}
		echo json_encode($resp);
	}

	public function actionBatchXrayJob(){
		if(!empty($_POST)){
			$r = ['error' => []];
			//validate
			if(empty($_POST['org_id'])){
				$r['error'][] = 'Please select customer';
			}elseif(empty($_POST['job'])){
				$r['error'][] = 'Please enter job lines';
			}else{
				foreach($_POST['job']['mawb'] as $i => $a){
					if(!preg_match('/^\d{3}-?\d{8}$/', trim($a))){
						$r['error'][] = [$i, 'mawb', 'Invalid MAWB number format'];
					}elseif(empty($_POST['inv']) && EdiJob::model()->count('owner_id = :oid AND awb = :awb', [':oid' => $_POST['org_id'], ':awb' => $a]) > 0){
						$r['error'][] = [$i, 'mawb', 'Job already exists'];
					}
					if(empty($_POST['job']['pol'][$i]) || !in_array($_POST['job']['pol'][$i],  explode(',', Yii::app()->params['settings']['pods']['value']))){
						$r['error'][] = [$i, 'pol', 'Not found'];
					}
					if(empty($_POST['job']['pod'][$i]) || !in_array($_POST['job']['pod'][$i],  explode(',', Yii::app()->params['settings']['pols']['value']))){
						$r['error'][] = [$i, 'pod', 'Not found'];
					}
					if(intval($_POST['job']['qty'][$i]) < 1){
						$r['error'][] = [$i, 'qty', 'Invalid number'];
					}
					if(!preg_match('/^[\dA-z]{1,2}\d{1,4}$/', trim($_POST['job']['flight'][$i]))){
						$r['error'][] = [$i, 'flight', 'Invalid flight no'];
					}
					if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($_POST['job']['etd'][$i]))){
						$r['error'][] = [$i, 'etd', 'Invalid date'];
					}
				}
			}

			if(empty($r['error'])){
				foreach($_POST['job']['mawb'] as $i => $a){
					$model = null;
					if(!empty($_POST['inv'])){
						$model = EdiJob::model()->find('awb = :awb', [':awb' => $a]);
					}

					if(empty($model)){
						$model = new EdiJob('create');
						$no = 'JB'.date('ymd', strtotime($model->created));
						$s = EdiJob::model()->count('no LIKE :n', [':n' => $no.'%']) + 1;
						$model->owner_id = $_POST['org_id'];
						$model->dpt_id = 106;
						$model->no = $no.sprintf('%02d', $s).strtoupper(substr($model->depot->name, 0, 3));
						$model->awb = $a;
						$model->dpmt = 30;
						$model->created = date('Y-m-d');
						$model->due = date('Y-m-d');
						$model->currency = 1;
						$model->inv_type = 0;
						$model->status = 10;
						$model->mdata['secDecOpt'] = [
							'screening' => 'XRY',
							'received_from' => '',
							'content' => ['15','35'],
							'content_other' => '',
							'exemption' => '',
							'info' => 10,
							];
					}
					$model->mdata['plt'] = $_POST['job']['qty'][$i];
					$model->save();

					//consol
					$awbModel = EdiAwbConsol::model()->find('awb = :awb AND carrier_id = :id', [':awb' => $a, ':id' => $model->id]);
					if(empty($awbModel)){
						$awbModel = new EdiAwbConsol('create');
						$awbModel->owner_id = $_POST['org_id'];
						$awbModel->awb = $a;
						$awbModel->dpt_id = 106;
						$awbModel->carrier_id = $model->id;
						$awbModel->mdata['goods'] = 'g1';
					}
					$awbModel->pol = $_POST['job']['pol'][$i];
					$awbModel->pod = $_POST['job']['pod'][$i];
					$awbModel->airline = substr($_POST['job']['flight'][$i], 0, 2);
					$awbModel->flight = $_POST['job']['flight'][$i];
					$awbModel->etd = $_POST['job']['etd'][$i];
					$awbModel->save();

					// save invoice details
					$il = JobLine::model()->find('ccode = :ccode AND job_id = :id', [':ccode' => 'GL85', ':id' => $model->id]);
					if(empty($il)){
						$il = new JobLine;
						$il->job_id = $model->id;
						$il->ccode = 'GL85';
						$il->inv_gst = 'EXEMPTEXPORT';
						$il->invoice_date = $model->created;
						$il->invoice_due_date = $model->due;
						$il->desc = 'X Ray '.$a;
					}
					$il->qty = empty($_POST['job']['weight'][$i])? 1 : floatval($_POST['job']['weight'][$i]);
					$il->rate = 0.12;
					$il->save();

					// save cost details
					$bl = BillingLine::model()->find('billing_ref = :no AND item_code = :code', [':no' => $model->no, ':code' => 'GL25']);
					if(empty($bl)){
						$bl = new BillingLine();
						$bl->billing_ref = $model->no;
						$bl->item_code = 'GL25';
						$bl->desc = '';
						$bl->qty = 1;
						$bl->price = 0;
						$bl->type = 3;
						$bl->gst = 'EXEMPTEXPENSES';
						$bl->org_id = 1233;
						$bl->save();
					}

					//cartage
					$cart = Cartage::model()->find('type = 30 AND job_id = :id AND ref = :awb', [':id' => $model->id, ':awb' => $a]);
					if(empty($cart)){
						$cart = new Cartage('create');
						$cart->type = 30;
						$cart->op_id = Yii::app()->user->id;
						$cart->status = 10; //new
						$cart->from_id = 106;
						$cart->from_addr = '6C The Crescent, Kingsgrove';
						$cart->to_id = $_POST['org_id'];
						$org = Org::model()->findByPk($_POST['org_id']);
						$cart->to_addr = $org->getAddress(true, false);
						$cart->to_contact = $org->phone;
						$cart->job_id = $model->id;
						$cart->org_id = $_POST['org_id'];
						$cart->ref = $a;
					}
					$cart->scd_time = $_POST['job']['etd'][$i];
					$cart->plt = $_POST['job']['qty'][$i];
					$cart->mdata['proc_type'] = 2;
					$cart->save();

					if(!empty($_POST['inv']) && $il->qty > 1 && empty($model->invoice)){
						ob_start();
						$_POST['jid'] = $model->id;
						$this->actionAjaxCreateInvoice();
						ob_end_clean();
					}
				}
			}
			echo json_encode($r);
			return;
		}
		$this->render('batch_xray');
	}

	/**
	 * save EDI basic job template
	 */
	private function saveEdiBasicJobTemplate($id)
	{
		$name = $_POST['EdiJobBasicTemplate']['name'];
		$valid_from = date('Y-m-d');// $_POST['EdiJobTemplate']['valid_from'];
		$valid_to = '2020-12-31';//$_POST['EdiJobTemplate']['valid_to'];

		$invoiceLines = [];
		if (is_array($_POST['JobLine']['ccode'])) {
			foreach ($_POST['JobLine']['ccode'] as $i => $a) {
				$line = [
					'ccode' => $a,
					'desc' => $_POST['JobLine']['desc'][$i],
					'qty' => empty($_POST['JobLine']['qty'][$i]) ? 1 : $_POST['JobLine']['qty'][$i],
					'rate' => empty($_POST['JobLine']['rate'][$i]) ? 0 : $_POST['JobLine']['rate'][$i],
					'inv_gst' => empty($_POST['JobLine']['inv_gst'][$i]) ? 0 : $_POST['JobLine']['inv_gst'][$i],
				];
				$invoiceLines[] = $line;
			}
		}
		$costLines = [];
		if (is_array($_POST['BillingLine']['item_code'])) {
			foreach ($_POST['BillingLine']['item_code'] as $i => $a) {
				$line = [
					'item_code' => $a,
					'desc' => $_POST['BillingLine']['desc'][$i],
					'org_id' => empty($_POST['BillingLine']['org_id'][$i]) ? 0 : $_POST['BillingLine']['org_id'][$i],
					'qty' => empty($_POST['BillingLine']['qty'][$i]) ? 1 : $_POST['BillingLine']['qty'][$i],
					'price' => empty($_POST['BillingLine']['price'][$i]) ? 0 : $_POST['BillingLine']['price'][$i],
					'gst' => empty($_POST['BillingLine']['gst'][$i]) ? 0 : $_POST['BillingLine']['gst'][$i],
				];
				$costLines[] = $line;
			}
		}

		if ($id > 0) {
			$jobBasiccTemplate = EdiJobBasicTemplate::model()->findByPk($id);
		}
		if (empty($jobBasiccTemplate)) {
			$jobBasiccTemplate = EdiJobBasicTemplate::model()->find('name = :name', [':name' => $name]);
		} else {
			$jobBasiccTemplate->name = $name;
		}
		$existingInvoiceLines = [];
		$existingCostLines = [];
		if (empty($jobBasiccTemplate)) {
			$jobBasiccTemplate = new EdiJobBasicTemplate();
			$jobBasiccTemplate->user_id = Yii::app()->user->id;
			$jobBasiccTemplate->owner_id = Yii::app()->user->id;
			$jobBasiccTemplate->created = date('Y-m-d H:i:s');
			$jobBasiccTemplate->status = 1; // as active
			$jobBasiccTemplate->name = $name;
		} else {
			$metaData = json_decode($jobBasiccTemplate->meta);
			$existingInvoiceLines = $metaData->invoice;
			// delete some one if existing
			$dels = $_POST['invoice_dels'];
			if (!empty($dels)) {
				$dels = explode('-', $dels);
				foreach ($dels as $del) {
					unset($existingInvoiceLines[$del-1]);
				}
			}
			$existingCostLines = $metaData->cost;
			// delete some one if existing
			$dels = $_POST['cost_dels'];
			if (!empty($dels)) {
				$dels = explode('-', $dels);
				foreach ($dels as $del) {
					unset($existingCostLines[$del-1]);
				}
			}
		}

		$jobBasiccTemplate->valid_from = $valid_from;
		$jobBasiccTemplate->valid_to = $valid_to;
		if (!empty($existingCostLines)) {
			$costLines = array_merge($costLines, $existingCostLines);
		}
		if (!empty($existingInvoiceLines)) {
			$invoiceLines = array_merge($invoiceLines, $existingInvoiceLines);
		}
		$jobBasiccTemplate->meta = json_encode(['invoice' => $invoiceLines,'cost' => $costLines]);
		$rt = $jobBasiccTemplate->save();

		$errors = $jobBasiccTemplate->getErrors();
		if (empty($errors)) {
			return true;
		} else {
			return $errors;
		}
	}

	/**
	 * @param $id
	 */
	public function actionEdiBasicInvoiceLinesGrid($id)
	{
		if (isset($_POST['JobLine'])) {
			$id = $_POST['JobLine']['id'];
			$tId = floor($id / 100);
			$lineId = $id % 100 - 1;

			$ediTemplate = EdiJobBasicTemplate::model()->find('id = :oid', [':oid' => $tId]);
			$newLine = [
				'ccode' => $_POST['JobLine']['ccode'],
				'desc' => $_POST['JobLine']['desc'],
				'qty' => empty($_POST['JobLine']['qty']) ? 1 : $_POST['JobLine']['qty'],
				'rate' => empty($_POST['JobLine']['rate']) ? 0 : $_POST['JobLine']['rate'],
				'inv_gst' => empty($_POST['JobLine']['inv_gst']) ? 0 : $_POST['JobLine']['inv_gst'],
			];

			$metaData = json_decode($ediTemplate->meta);
			$lines = $metaData->invoice;
			foreach ($lines as $k => $line) {
				if ($k == $lineId) {
					$lines[$k] = $newLine;
					break;
				}
			}
			$newMetaData['invoice'] = $lines;
			$newMetaData['cost'] = $metaData->cost;
			$ediTemplate->meta = json_encode($newMetaData);
			$ediTemplate->save();

			$this->ajaxResult($ediTemplate);
		}
	}

	/**
	 * @param $id
	 */
	public function actionEdiBasicCostLinesGrid($id)
	{
		if (isset($_POST['BillingLine'])) {
			$id = $_POST['BillingLine']['id'];
			$tId = floor($id / 100);
			$lineId = $id % 100 - 1;

			$ediTemplate = EdiJobBasicTemplate::model()->find('id = :oid', [':oid' => $tId]);
			$newLine = [
				'item_code' => $_POST['BillingLine']['item_code'],
				'desc' => $_POST['BillingLine']['desc'],
				'qty' => empty($_POST['BillingLine']['qty']) ? 1 : $_POST['BillingLine']['qty'],
				'price' => empty($_POST['BillingLine']['price']) ? 0 : $_POST['BillingLine']['price'],
				'gst' => empty($_POST['BillingLine']['gst']) ? 0 : $_POST['BillingLine']['gst'],
				'org_id' => $_POST['BillingLine']['org_id'],
			];

			$metaData = json_decode($ediTemplate->meta);
			$lines = $metaData->cost;
			foreach ($lines as $k => $line) {
				if ($k == $lineId) {
					$lines[$k] = $newLine;
					break;
				}
			}
			$newMetaData['cost'] = $lines;
			$newMetaData['invoice'] = $metaData->invoice;
			;
			$ediTemplate->meta = json_encode($newMetaData);
			$ediTemplate->save();

			$this->ajaxResult($ediTemplate);
		}
	}


	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view', [
			'model'=>$this->loadModel($id),
		]);
	}

	public function actionMngChargeTypes()
	{
		$this->render('charge_types');
	}

	public function actionChargeTypesGrid()
	{
		if (isset($_POST['ChargeItemType'])) {
			if (empty($_POST['ChargeItemType']['id'])) {
				$model = new ChargeItemType();
			} else {
				$model = ChargeItemType::model()->findByPk($_POST['ChargeItemType']['id']);
			}

			$newChargeCode = $_POST['ChargeItemType']['charge_code'];
			$existing = Chargecode::model()->find('status = 1 AND code = :code', [':code' => $newChargeCode]);
			if (empty($existing)) {
				$model->addError('id', 'ChargeCode :  ' . $newChargeCode . ' not existing');
			} else {
				$model->attributes = $_POST['ChargeItemType'];
				$model->save();

				// update code if not existing
				if (empty($model->code)) {
					$model->code = 'GL'. $model->id;
					$model->update('code');
				}
			}
			$this->ajaxResult($model);
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreateJob()
	{
		$model = new EdiJob('create');
		$awbModel = new EdiAwbConsol('create');
		if (!empty($_GET['org'])) {
			$model->owner_id = $_GET['org'];
			$awbModel->owner_id = $_GET['org'];
		}
		$isJobCreated = false;
		if (isset($_POST['EdiJob'])) {
			if (empty($_POST['JobLine']['ccode']) || !is_array($_POST['JobLine']['ccode'])) {
				$model->addError('id', 'Please input at least one invoice line');
				$errors = $model->getErrors();
			} elseif (empty($_POST['BillingLine']['item_code'])) {
				$model->addError('id', 'Please input at least one cost line');
				$errors = $model->getErrors();
			} else {
				$selectedAwbId = 0;
				if (isset($_POST['EdiAwbConsol']['awb'])) {
					$selectedAwbId = $_POST['EdiAwbConsol']['awb'];
				}

				// save new job
				$model->attributes = $_POST['EdiJob'];
				$model->dpt_id = $_POST['EdiAwbConsol']['dpt_id'];
				$model->owner_id = $_POST['EdiAwbConsol']['owner_id'];
				$model->awb = $_POST['EdiAwbConsol']['awb'];
				if (!empty($selectedAwbId)) {
					$ediAwbModel = EdiAwbConsol::model()->findByPk($selectedAwbId);
					if (empty($ediAwbModel)) {
						$another = EdiJob::model()->find('awb = :awb', [':awb' => $_POST['EdiAwbConsol']['awb']]);
						if (!empty($another)) {
							echo json_encode(['done' => false, 'msg' => 'AWB exists']);
							Yii::app()->end();
						}
						$ediAwbModel = EdiAwbConsol::model()->find('awb = :awb', [':awb' => $_POST['EdiAwbConsol']['awb']]);
						if (empty($ediAwbModel)) {
							$ediAwbModel = new EdiAwbConsol('create');
						}
					}
					$ediAwbModel->attributes = $_POST['EdiAwbConsol'];
					$ediAwbModel->mdata['leg2'] = [
						'airline' => $_POST['airline'],
						'flight' => $_POST['flight'],
						'etd' => $_POST['etd2'],
						'eta' => $_POST['eta2'],
						'atd' => $_POST['atd2'],
						'ata' => $_POST['ata2'],
					];
					$ediAwbModel->save();
				}
				$model->status = 10; // as new job
				if (!empty($_POST['jbv_gw'])) {
					$model->mdata['jbv_gw'] = $_POST['jbv_gw'];
				}

				// create edi job number
				$no = 'JB'.date('ymd', strtotime($model->created));
				$s = EdiJob::model()->count('no LIKE :n', [':n' => $no.'%']) + 1;
				$no = $no.sprintf('%02d', $s).strtoupper(substr($model->depot->name, 0, 3));
				$model->no = $no;

				$model->save();

				// wmstask
				if (!empty($_GET['wmstask'])) {
					$wmsedi = WmsEdi::model()->find('task_id = :task_id AND job_id = :job_id', array(':task_id' => $_GET['wmstask'], ':job_id' => $model->id));
					if (empty($wmsedi)) {
						$wmsedi = new WmsEdi;
						$wmsedi->task_id = $_GET['wmstask'];
						$wmsedi->job_id = $model->id;
						$wmsedi->save();
					}
				}

				// cnlorder
				if (!empty($_GET['cnlorder'])) {
					$cnlorder = CnlOrder::model()->findByPk($_GET['cnlorder']);
					if (!empty($cnlorder)) {
						$cnlorder->mdata['edi_job'] = $model->id;
						$cnlorder->update('meta');

						$model->mdata['cnl_order'] = $cnlorder->id;
						$model->update('meta');
					}
				}

				// save each job line
				$errors = $model->getErrors();
				$airFreightItemExisting = false;

				if (empty($errors)) { // only in case create job ok
					$isJobCreated = true;
					// save invoice details
					foreach ($_POST['JobLine']['ccode'] as $i => $a) {
						// in case client set Air Freight Item
						if ($a == 'GL1') {
							$airFreightItemExisting = true;
						}
						$il = new JobLine;
						$il->job_id = $model->id;
						$il->ccode = $a ;
						$il->desc = $_POST['JobLine']['desc'][$i];
						$il->qty = empty($_POST['JobLine']['qty'][$i]) ? 1 : $_POST['JobLine']['qty'][$i];
						$il->rate = empty($_POST['JobLine']['rate'][$i]) ? 0 : $_POST['JobLine']['rate'][$i];
						$il->inv_gst = empty($_POST['JobLine']['inv_gst'][$i]) ? 0 : $_POST['JobLine']['inv_gst'][$i];
						$il->invoice_date = $model->created;
						$il->invoice_due_date = $model->due;
						$il->save();
						$errs = $il->getErrors();
						if (!empty($errs)) {
							$model->addErrors($errs);
							$errors = $model->getErrors();
						}
					}

					// save cost details
					if (!empty($_POST['BillingLine']['item_code'])) {
						foreach ($_POST['BillingLine']['item_code'] as $i => $a) {
							$il = new BillingLine();
							$il->item_code = $a;
							$il->desc = $_POST['BillingLine']['desc'][$i];
							$il->qty = empty($_POST['BillingLine']['qty'][$i]) ? 1 : $_POST['BillingLine']['qty'][$i];
							$il->price = empty($_POST['BillingLine']['price'][$i]) ? 0 : $_POST['BillingLine']['price'][$i];
							$il->gst = empty($_POST['BillingLine']['gst'][$i]) ? 0 : $_POST['BillingLine']['gst'][$i];
							$il->org_id = empty($_POST['BillingLine']['org_id'][$i]) ? 0 : $_POST['BillingLine']['org_id'][$i];

							// from business point of view , in some case supplier id is empty
							//if ( empty($il->org_id) ) {
							//    $model->addError('id', 'Cost Supplier must not be empty');
							//    $errors = $model->getErrors();
							//} else
							{
								// save to our Billing DB
								BillingLine::saveExportAirFreightBillingByBilling($model, $il);
							}
						}
					}
				}
			}

			if (!empty($errors)) {
				// delete just created job
				if ($isJobCreated) {
					BillingLine::model()->deleteAll('billing_ref = :bno', [':bno' => $model->no]);
					JobLine::model()->deleteAll('job_id = :jid', [':jid' => $model->id]);
					$model->delete();
				}
			} else {
				// in case AWB set , and Air Freight set
				// we link awb with the EdiJob
				// by set carrier_id in EdiAwbConsole
				if ($selectedAwbId > 0 && $airFreightItemExisting) {
					$awbModel = EdiAwbConsol::model()->findByPk($selectedAwbId);
					if (!empty($awbModel)) {
						$awbModel->carrier_id = $model->id;
						$awbModel->update('carrier_id');
					}
				}
			}
			$this->ajaxResult($model, ['id']);
		}

		// create job dummy data
		$model->created = date('Y-m-d');
		$model->due = date('Y-m-d');

		// get all available EDI AWB
		$awbs = EdiAwbConsol::model()->findAll('carrier_id = 0');
		$awbList = ['0' => 'Select AWB'];
		foreach ($awbs as $awb) {
			$awbList[$awb->id] = $awb->awb;
		}

		// set a default cost template , if no any cost template set, we use this default one

		if (!empty($_GET['cnlorder'])) {
			$cnlorder = CnlOrder::model()->findByPk($_GET['cnlorder']);

			$pol = $cnlorder->pol;
			$pol = strlen($pol) == 5 ? substr($pol, 2) : $pol;
			$pol = Unloco::model()->find('port = :port', [':port' => $pol]);
			$awbModel->pol = $pol->country . $pol->port;
			$pod = $cnlorder->pod;
			$pod = strlen($pod) == 5 ? substr($pod, 2) : $pod;
			$pod = Unloco::model()->find('port = :port', [':port' => $pod]);
			$awbModel->pod = $pod->country . $pod->port;

			$awbModel->awb = $cnlorder->orderCode;
		}

		$this->render('create_job', [
			'model' => $model,'awbs' => $awbList,'awbModel' => $awbModel
		]);
	}

	public function actionLinkJob()
	{
		if (empty($_POST)) {
			$this->render('link_job');
		} else {
			$job = EdiJob::model()->find('awb = :t OR no = :t', [':t' => $_POST['job']]);
			if  (empty($job)) {
				echo json_encode(['done' => false, 'msg' => 'Job does not exist']);
				Yii::app()->end();
			}

			if (!empty($_GET['wmstask'])) {
				$wmsedi = WmsEdi::model()->find('task_id = :task_id AND job_id = :job_id', array(':task_id' => $_GET['wmstask'], ':job_id' => $job->id));
				if (empty($wmsedi)) {
					$wmsedi = new WmsEdi;
					$wmsedi->task_id = $_GET['wmstask'];
					$wmsedi->job_id = $job->id;
					$wmsedi->save();
				}
			} else if (!empty($_GET['cnlorder'])) {
				$cnlorder = CnlOrder::model()->findByPk($_GET['cnlorder']);
				if (!empty($cnlorder)) {
					$cnlorder->mdata['edi_job'] = $job->id;
					$cnlorder->update('meta');
					$job->mdata['cnl_order'] = $cnlorder->id;
					$job->update('meta');
				}
			}
			$this->ajaxResult($job);
		}
	}

	/**
	 * export all current search edi jobs
	 */
	public function actionExport()
	{
		switch ($_GET['type']) {
			case 'search': {
				$model = new EdiJob('search');
				$model->unsetAttributes();
				if (!empty($_GET['EdiJob'])) {
					$model->attributes=$_GET['EdiJob'];
				}
				$dp = $model->search(false);
				$xls = new oExcel;
				$mfn = 'edijobs_detail_'.date('Y-m-d');
				$i = 1;
				$xls->addRow($i++, ['No.', 'Awb', 'Owner', 'Status', 'Dpot', 'Created', 'Due Date', 'Invoice No.','Accrual Cost','Actual Cost','Revenue','Profit']);
				$xls->setFont('A1:K1', ['bold' => true]);

				foreach ($dp->data as $il) {
					$cst = $il->totCost();
					$xls->addRow($i++, [
						$il->no, $il->awb,
						empty($il->owner)? "" : $il->owner->name,
						$il->getStatus(),
						empty($il->depot)? "" : $il->depot->name,
						$il->created,$il->due,
						$il->getInvoicesString(),
						$cst[0],
						$cst[1],
						($il->currency == 1) ? $il->totRevenue() : $il->totRevenue()[0] . 'USD ~ ' . $il->totRevenue()[1] . 'AUD',
						$il->totProfit()
					]);
				}
				$xls->output($mfn.'.xlsx');
			}
			break;
			case 'abm': {
				$jobs = EdiJob::model()->findAll(['condition' => 'owner_id IN (' . implode(',', Org::$displayJobRef) . ')', 'order' => 'id DESC']);
				$xls = new oExcel;
				$i = 1;
				$xls->addRow($i++, ['Job No.', 'Customer Name', 'POL', 'POD', 'Ref No.', 'PLT No.', 'Weight', 'Invoice Amount', 'AWB', 'ETD', 'ETA', 'ATD', 'ATA', 'HC40', 'HC20', 'GP40', 'GP20', '换板数', 'Flight No.']);
				foreach ($jobs as $job) {
					$list = AppHelper::setting2List('pods');
					$pol = $list[$job->awbconsol->pol];
					$list = AppHelper::setting2List('pols');
					$pod = $list[$job->awbconsol->pod];
					$inv_amount = 0;
					if (!empty($job->invoice)) {
						foreach ($job->invoice as $invoice) {
							if ($invoice->isRevert()) continue;
							$inv_amount = $invoice->total;
						}
					}
					$xls->addRow($i++, [$job->no, @$job->owner->name, $pol, $pod, @$job->mdata['ref'], @$job->mdata['plt'], @$job->mdata['jbv_gw'], $inv_amount, $job->awbconsol->awb, $job->awbconsol->etd, $job->awbconsol->eta, $job->awbconsol->atd, $job->awbconsol->ata, @$job->mdata['hc40'], @$job->mdata['hc20'], @$job->mdata['gp40'], @$job->mdata['gp20'], @$job->mdata['plt_change'], @$job->awbconsol->flight]);
				}
				$xls->output('abm_report.xlsx');
			}
			break;
		}
	}

	public function actionSecurityDec($id){
		$model = $this->loadModel($id);
		if(!empty($_POST)){
			foreach($_POST['option'] as $k => $v){
				$model->mdata['secDecOpt'][$k] = $v;
			}
			if(isset($_POST['yt1']) && (empty($model->mdata['secDecOpt']['ts']) || !empty($_POST['override_time']))) $model->mdata['secDecOpt']['ts'] = time();
			$model->save();
			if(isset($_POST['yt1'])){
				oPDF::renderPDF('security_dec', ['model' => $model]);
			}else{
				$this->ajaxResult($model);
			}
		}
		$this->render('security_dec', [
			'model'=>$model,
		]);
	}

	/**
	 * @param $id
	 */
	public function actionAttach($id)
	{
		$model = $this->loadModel($id);
		$this->render('attach_form', [
			'model'=>$model,
		]);
	}

	public function actionGetAttachs()
	{
		$id = $_GET['id'];
		// get latest uploaded zone map file
		$attachements = FileRepo::model()->findAll('fid = :oid and type = 89 order by id desc', [':oid' => $id]);

		$r = new stdClass();
		$r->success = 1;
		$index = 1;
		$r->data = '';
		foreach ($attachements as $attachement) {
			$r->data .= '<li>' . $index++ . '. <a target="_blank" href="' . $attachement->getUrl() . '" >' . $attachement->name . '</a></li>';
		}

		echo json_encode($r);
	}

	public function actionAjaxSaveAttach()
	{
		$ediId = $_POST['EdiJob']['id'];
		$model = $this->loadModel($ediId);

		//  $resp = array('success' => 1,'msg' => 'file attached successfully');
		$template_file = empty($_FILES['edi_attach']) ? [] : $_FILES['edi_attach'];
		if (empty($template_file['tmp_name']) || !is_uploaded_file($template_file['tmp_name'])) {
			// $resp['msg'] = 'Invalid file';
			// echo json_encode($resp);
			$model->setError('Invalid File');
			$this->ajaxResult($model, ['id']);
		//  return;
		} else {

			//save file
			FileRepo::storeFile($template_file['tmp_name'], $template_file['name'], 89, $ediId);
		}

		$this->ajaxResult($model, ['id']);

		//echo json_encode($resp);
	}
	/**
	 * show billing input shortcut page
	 */
	public function actionBillingInput()
	{
		$model = new EdiJob();

		if (isset($_POST['JobLine'])) {
			$r = new stdClass();
			$r->color = '#0c0'; // #c00 for wrong ,  $r->color = '#0c0'; for correct
			$r->stop = 0;
			$r->msg = 'All Done Successfully';

			$errors = [];
			foreach ($_POST['JobLine']['ccode'] as $i => $a) {
				$glcode =  $a;
				$awbId = $_POST['JobLine']['awb_no'][$i];
				$cost = empty($_POST['JobLine']['cost_amount'][$i]) ? 0 : $_POST['JobLine']['cost_amount'][$i];

				// based on awbNo , insert cost to related EDI job line
				$ediAwbConsole = EdiAwbConsol::model()->findByPk($awbId);
				if (!empty($ediAwbConsole)) {
					$jobId = $ediAwbConsole->carrier_id;
					if ($jobId > 0) {
						$jobLine = JobLine::model()->find('job_id = :jid AND ccode = :ccode', [':jid' => $jobId,':ccode' => $glcode]);
						if (!empty($jobLine)) {
							$jobLine->cost_amount = $cost;
							$jobLine->update('cost_amount');

							// update job status to waiting for confirm again
							$job = EdiJob::model()->findByPk($jobId);
							if (!empty($job)) {
								$job->status = 19;
								$job->update('status');
							}
						} else {
							$errors[] = 'AWB : ' . $ediAwbConsole->awb . ' related item : ' . $glcode  . ' Not Found';
						}
					} else {
						$errors[] = 'AWB : ' . $ediAwbConsole->awb . ' related Job Not Found';
					}
				} else {
					$errors[] = 'AWB ID : ' . $awbId . ' Not Found';
				}
			}
			if (!empty($errors)) {
				$r->color = '#c00';
				$r->msg = implode('<br>', $errors);
			}
			echo json_encode($r);
			Yii::app()->end();
		}
		$this->render('billing_input', ['model' => $model]);
	}

	/**
	 * get all related Awb numbers based on supplier id
	 * logic as below:
	 * if awb number set in EDI job
	 * AWB console's carrier_id will be job ID
	 * but supplier id is linked with JobLine
	 * so we need to get job id from JobLine
	 */
	public function actionAjaxGetSupplierAwbs()
	{
		$sid = $_POST['sid'];

		// get related job line in order to get job id
		$awbs = [];
		$resp = ['success' => 0 , 'data' => []];
		if ($sid > 0) {
			$joblines = JobLine::model()->findAll('supplier_id = :sid', [':sid' => $sid]);

			// loop all related job ID
			// try to get related AWB number
			foreach ($joblines as $line) {
				$jobId = $line->job_id;
				$awb = EdiAwbConsol::model()->find('carrier_id = :cid', [':cid' => $jobId]);
				if (!empty($awb)) {
					$awbs[$awb->id] = [
						'id' => $awb->id,
						'awb' => $awb->awb
					];
				}
			}
			// save as array
			$aryAwbs = [];
			foreach ($awbs as $awb) {
				$aryAwbs[] = $awb;
			}
			$resp['data'] =  $aryAwbs;
			$resp['success'] =  1;
		}
		echo json_encode($resp);
	}

	/**
	 * get EDI AWB customer information(name and ID) by sepecified ID
	 */
	public function actionAjaxGetAwbCustomer()
	{
		$awb = $_POST['awb'];
		$awbModel = EdiAwbConsol::model()->findByPk($awb);
		$resp = ['success' => 0 , 'data' => []];

		if (!empty($awbModel)) {
			$data = [
				'id' => $awbModel->owner->id,
				'name' => $awbModel->owner->name
			];
			$resp['data'] =  $data;
			$resp['success'] =  1;
		}
		echo json_encode($resp);
	}

	/**
	 * @param $id
	 * @throws CHttpException
	 */
	public function actionNotes($id)
	{
		$model = $this->loadModel($id);
		if (!empty($_POST['notes'])) {
			$log = Log::add($model, 6, ['notes' => $_POST['notes']]);
			$this->ajaxResult($log);
		}
	}

	/**
	 * @param $id
	 */
	public function actionAwbInvoiceLinesGrid($id)
	{
		// do nothing currenlty
		if (isset($_POST['JobLine'])) {
			$model = new JobLine;
			$this->ajaxResult($model);
		}
	}

	/**
	 * @param $id
	 */
	public function actionLinesInvoiceGrid($id)
	{
		if (isset($_POST['JobLine'])) {
			if (empty($_POST['JobLine']['id'])) {//create
				$model = new JobLine;
				$model->job_id = $id;
				$model->invline_id = 0;
			} else {//update
				$model = JobLine::model()->findByPk($_POST['JobLine']['id']);
				if (empty($model)) {
					$model = new JobLine;
					$model->addError('id', 'Related job line not found');
					$this->ajaxResult($model);
				}
			}
			// check invoice status
			if (!empty($model)) {
				if (!$this->isJobCanbeChanged($model->job_id)) {
					$model->addError('id', 'Invoice has been fully paid, You can not change them anymore!');
					$this->ajaxResult($model);
				}
			}

			$model->attributes = $_POST['JobLine'];
			$model->save();

			// in case no error happened
			/**
			 * cause double create invoice | credit invoice and create new
			**/
			$errors = $model->getErrors();
			if (empty($errors)) {
				$invoice = Invoice::model()->find('job_id = :jid AND status = :status', [':jid' => $model->job_id, ':status' => Invoice::INVOICE_STATUS_PENDING]);
				if (!empty($invoice)) {
					$this->createInvoiceByJob($model->job_id);
				}
			}
			$this->ajaxResult($model);
		}
	}

	/**
	 * @param $id
	 */
	public function actionLinesCostGrid($id)
	{
		if (isset($_POST['BillingLine'])) {
			if (empty($_POST['BillingLine']['id'])) {//create
				$model = new BillingLine();
				$job = EdiJob::model()->findByPk($id);
				$model->billing_ref = $job->no;
				$model->type = BillingLine::BILLING_TYPE_AIR_SEA;
			} else {//update
				$model = BillingLine::model()->findByPk($_POST['BillingLine']['id']);
				$job = EdiJob::model()->find('no = :no', [':no' => $model->billing_ref]);
			}
			unset($_POST['BillingLine']['id']);
			$model->attributes = $_POST['BillingLine'];
			// update with ledger as well
			if ($model->save()) {
				BillingLine::saveExportAirFreightBillingByBilling($job, $model);
				// confirm
				if (!in_array($model->org_id, [964, 1133]) || ($model->org_id == 964 && empty($model->mdata['aline']))) {
					if (empty($model->billing_cref)) {
						$model->status = 1;
						$model->billing_id = 0;
						/**
						 * 2020-05-27
						 * if empty billing_cref and accrual_amount
						 */
						if ($model->accrual_amount == 0) {
							$model->actual_amount = 0;
							$model->gst_amount = 0;
						}
						$model->update('status', 'billing_id', 'actual_amount', 'gst_amount');
					} else {
						if (empty($model->actual_amount) || $model->actual_amount == 0) {
							$model->actual_amount = $model->accrual_amount;
							$model->gst_amount = $model->getGSTValue();
							$model->update('actual_amount', 'gst_amount');
						}

						if (empty($model->billing_id)) {
							$model->status = 2;
							$model->update('status');
							Billing::linkLine($model);
						} else if ($model->billing->billing_cref != $model->billing_cref) {
							if ($model->billing->status >= 3) {
								$model->billing_cref = $model->billing->billing_cref;
								$model->update('billing_cref');
								$model->addError('id', 'bill ' . $model->billing->billing_cref . ' has been posted');
							} else {
								$model->billing_id = 0;
								$model->status = 2;
								$model->update('billing_id', 'status');
								Billing::linkLine($model);
							}
						}
					}
				}

				// check for billing matched or not this time after modification
				BillingLine::checkMatchedEdiJobRealCost($job);
			}
			$this->ajaxResult($model);
		}
	}


	/**
	 * based on invoice status to check if job can be changed or not
	 * @param $jobId
	 * @return bool
	 */
	private function isJobCanbeChanged($jobId)
	{
		$beChanged = true;
		$invoice = Invoice::model()->find('job_id = :jid AND status != :status AND status != :dstatus', [':jid' => $jobId,':status' => Invoice::INVOICE_STATUS_PAID,
			':dstatus' => Invoice::INVOICE_STATUS_CACELLED]);
		if (empty($invoice)) {
			// try to find if all related invoice has been paid fully
			$paidInvoice = Invoice::model()->find('job_id = :jid AND status = :status', [':jid' => $jobId,':status' => Invoice::INVOICE_STATUS_PAID]);
			if (!empty($paidInvoice)) {
				$beChanged = false;
			}
		}
		return $beChanged;
	}

	/**
	 * delete job invoice line
	 * @param $id
	 */
	public function actionDeleteInvoiceGridLine($id)
	{
		$jobLine = JobLine::model()->findByPk($id);

		if ($this->isJobCanbeChanged($jobLine->job_id)) {
			$job = Job::model()->findByPk($jobLine->job_id);
			Log::add($job, 5, array_merge(['status' => $job->getStatus()], ['note' => 'delete one line']));
			$jobLine->delete();
			$invoice = Invoice::model()->find('job_id = :jid AND status = :status', [':jid' => $jobLine->job_id, ':status' => Invoice::INVOICE_STATUS_PENDING]);
			if (!empty($invoice)) {
				$this->createInvoiceByJob($jobLine->job_id);
			}
		} else {
			$jobLine->addError('id', 'You can not change any more!');
		}
		$this->ajaxResult($jobLine);
	}

	/**
	 * delete job cost line
	 * @param $id
	 */
	public function actionDeleteCostGridLine($id)
	{
		$line = BillingLine::model()->findByPk($id);
		if (!empty($line)) {
			// only can delete not posted billing
			if ($line->status < 3) {
				// in case is frozen , we can't delet it again
				$plLedger = PlLedger::model()->find('fid = :fid AND model = :model', [':fid' => $line->id, ':model' => 'BillingLine']);
				if (!empty($plLedger)) {
					$plLedger->delete();
				}
				// $line->delete();

				if ($line->accrual_amount > 0) {
					$line->actual_amount = 0;
					$line->billing_cref = '';
					$line->billing_id = 0;
					$line->status = 1;
					$line->update('actual_amount', 'billing_cref', 'billing_id', 'status');
				} else {
					$line->status = 11;
					$line->update('status');
				}
			}
		}

		$this->ajaxResult($line);
	}


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if (Yii::app()->request->isPostRequest) {
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax'])) {
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : ['admin']);
			}
		} else {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	public function actionUpdateByNo($no)
	{
		$consol = EdiJob::model()->find('no = :no', [':no' => $no]);
		if (!empty($consol)) {
			$this->actionUpdate($consol->id);
		} else {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		if (!empty($_POST)) {
			if (isset($_POST['EdiAwbConsol'])) {
				// check to see if same awb existing or not
				$awb =  $_POST['EdiAwbConsol']['awb'];
				$awbModel = EdiAwbConsol::model()->find('awb = :awb', [':awb' => $awb]);
				if (empty($awbModel)) {
					$awbModel = new EdiAwbConsol();
				}

				$awbModel->attributes = $_POST['EdiAwbConsol'];

				// save leg one data into meta
				$awbModel->mdata['leg2'] = [
					'airline' => $_POST['airline'],
					'flight' => $_POST['flight'],
					'etd' => $_POST['etd2'],
					'eta' => $_POST['eta2'],
				];
				$awbModel->mdata['goods'] = isset($_POST['selected_goods']) ? $_POST['selected_goods'] : '';

				if (!empty($_POST['notes'])) {
					$awbModel->mdata['custom_log_note'] = $_POST['notes'];
				}

				$awbModel->save();

				$model->dpt_id = $_POST['EdiAwbConsol']['dpt_id'];
				$model->owner_id = $_POST['EdiAwbConsol']['owner_id'];
				$model->awb = $awb;
				$model->update('awb');
			}

			$this->ajaxResult($model);
		}

		// in case from fixing cost line
		if (isset($_GET['afid'])) {
			Yii::app()->session['afid'] = $_GET['afid'];
		}

		if (isset($_GET['tab'])) {
			$awbList = null;
			$aflines = null;
			if ($_GET['tab'] == 'overview') {
				// get all available EDI AWB
				$awbs = EdiAwbConsol::model()->findAll('id > 0');
				$awbList = ['0' => 'Select AWB'];
				foreach ($awbs as $awb) {
					$awbList[$awb->id] = $awb->awb;
				}

				if (isset(Yii::app()->session['afid'])) {
					$aflines = new AFInvoiceReconciliationLine();
					$aflines->unsetAttributes();
					$aflines->invoice_id = Yii::app()->session['afid'];
					unset(Yii::app()->session['afid']);
				}
			}
			Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);
			$this->render('tab_'.$_GET['tab'], ['model'=>$model,'awbs' => $awbList,'aflines' => $aflines]);
		} else {
			if (!empty($model->wmstasks)) {
				$weight = 0;
				foreach ($model->wmstasks as $wmstask) {
					if ($wmstask->type == 3010) {
						foreach ($wmstask->items as $item) {
							$plt = WmsLocation::model()->findByPk($item->mdata['pli']);
							$weight += floatval(@$plt->extra['weight']);
						}
					}
				}
				// if (!empty($model->mdata['jbv_gw']) && $model->mdata['jbv_gw'] != $weight) {
				// 	$model->mdata['jbv_gw'] = $weight;
				// 	$model->update('meta');
				// }
			}
			$this->render('update', ['model'=>$model]);
		}
	}

	/**
	 * update job related accrual cost and invoice based on job air freight qutoes and weight
	 */
	public function actionAjaxUpdateJobCost()
	{
		$resp = ['success' => 1,'ecode' => 0 ,'msg' => 'Success'];
		$jobId = $_POST['jid'];
		$job = EdiJob::model()->findByPk($jobId);
		if (empty($job)) {
			$resp['success']  = 0 ;
			$resp['msg'] = 'Related job not found (job id : ' . $jobId . ')';
		}

		// update job related invoice weight currently only for
		// Air Freight(GL1) and Security Fee(GL11) items
		$weight = round(floatval($_POST['w']), 3);
		foreach ($job->lines as $line) {
			if ($line->ccode == 'GL1' || $line->ccode == 'GL11') {
				$line->qty = $weight;
				$line->update('qty');
			}
		}


		// update job related cost accrual amount only for
		// Air Freight(GL1) and Security Fee(GL11) items
		// based on job id get Edi job related consol
		$awbModel = EdiAwbConsol::model()->find('awb = :awb', [':awb' => $job->awb]);
		if (!empty($awbModel)) {
			// get air freight price based job POL , POD and air frieght line
			// $pol = $awbModel->pol;
			$pod = $awbModel->pod;
			$flight = $awbModel->flight;

			// remove country code from $pod
			// because in route and quote table we only can found quote by simple AIR freight code
			$pod = substr($pod, 2);
			// get route id
			$routeId = 0;
			$route = Route::model()->find('departure = :d AND code = :code AND flight_no = :fno', [':d' => 'Sydney',':code' => $pod,':fno' => $flight]);
			if (!empty($route)) {
				$routeId = $route->id;
			}
			$quote = Quotes::model()->find('route_id = :rid AND uld_type = :uld AND wt_lo <= :weight AND wt_hi > :weight', [':rid' => $routeId,':uld' => 'pallet',':weight' => $weight]);
			$pkgRate = !empty($quote) ? $quote->pkg : 0;
			if ($pkgRate <= 0) {
				$resp['success']  = 0;
				$resp['ecode'] = 3; // invoke client to update related air line quote
				$resp['msg'] = 'Related air freight cost rate does not set yet! Please set now';
			} else {
				$billings = BillingLine::model()->findAll('billing_ref = :jno', [':jno' => $job->no]);
				foreach ($billings as $billing) {
					if ($billing->item_code == 'GL1' || $billing->item_code == 'GL11') {
						$billing->qty = round($weight, 2);
						// for air freight
						if ($billing->item_code == 'GL1') {
							$billing->price = round($pkgRate, 2); // update new price with latest quote from air company
						}
						$billing->accrual_amount = round($weight * $billing->price, 2);
						$billing->update(['qty','price','accrual_amount']);
					}
				}
			}
		}

		echo json_encode($resp);
	}

	/**
	 * update job related air freight line related quote
	 */
	public function actionAjaxUpdateJobCostRate()
	{
		$resp = ['success' => 1,'msg' => 'Updated successfully!'];
		$jobId = $_POST['jid'];
		$job = EdiJob::model()->findByPk($jobId);
		if (empty($job)) {
			$resp['success']  = 0 ;
			$resp['msg'] = 'Related job not found (job id : ' . $jobId . ')';
		}

		$weight = round(floatval($_POST['w']), 3);
		$awbModel = EdiAwbConsol::model()->find('awb = :awb', [':awb' => $job->awb]);
		if (!empty($awbModel)) {
			$pod = $awbModel->pod;
			$flight = $awbModel->flight;
			$pod = substr($pod, 2);
			// get route id
			$routeId = 0;
			$route = Route::model()->find('departure = :d AND code = :code AND flight_no = :fno', [':d' => 'Sydney',':code' => $pod,':fno' => $flight]);
			if (!empty($route)) {
				$routeId = $route->id;
			}
			$quote = Quotes::model()->find('route_id = :rid AND uld_type = :uld AND wt_lo <= :weight AND wt_hi > :weight', [':rid' => $routeId,':uld' => 'pallet',':weight' => $weight]);
			$pkgRate =  round(floatval($_POST['r']), 2);
			if (!empty($quote)) {
				$quote->pkg = $pkgRate;
				$quote->update('pkg');

				// update invoice lines
				foreach ($job->lines as $line) {
					if ($line->ccode == 'GL1' || $line->ccode == 'GL11') {
						$line->qty = $weight;
						$line->update('qty');
					}
				}

				// update cost lines
				$billings = BillingLine::model()->findAll('billing_ref = :jno', [':jno' => $job->no]);
				foreach ($billings as $billing) {
					if ($billing->item_code == 'GL1' || $billing->item_code == 'GL11') {
						$billing->qty = round($weight, 2);
						// for air freight
						if ($billing->item_code == 'GL1') {
							$billing->price = round($pkgRate, 2); // update new price with latest quote from air company
						}
						$billing->accrual_amount = round($weight * $billing->price, 2);
						$billing->update(['qty','price','accrual_amount']);
					}
				}
			} else {
				$resp['success']  = 0 ;
				$resp['msg'] = 'Air line route not found, please go to Tools -> Airline Check add the new route';
			}
		}

		echo json_encode($resp);
	}


	public function actionAjaxCreateInvoice()
	{
		$resp = ['success' => 1, 'msg' => 'Success'];
		$jobId = $_POST['jid'];

		// pre check
		$job = EdiJob::model()->findByPk($jobId);
		if (!empty($_POST['created'])) {
			$job->created = $_POST['created'];
		}
		if (!empty($_POST['due'])) {
			$job->due = $_POST['due'];
		}
		if (!empty($_POST['currency'])) {
			$job->currency = $_POST['currency'];
		}
		if (!empty($_POST['inv_type'])) {
			$job->inv_type = $_POST['inv_type'];
		}
		if (!empty($_POST['ref2'])) {
			$job->mdata['ref2'] = $_POST['ref2'];
		}
		$job->update(['created', 'due', 'currency', 'inv_type', 'meta']);
		$rev = [];
		foreach ($job->lines as $line) {
			if (empty($rev[$line->ccode])) {
				$rev[$line->ccode] = 0;
			}
			$rev[$line->ccode] += $line->rate * $line->qty;
		}
		$billinglines = BillingLine::model()->findAll('billing_ref = :billing_ref', [':billing_ref' => $job->no]);
		$cost = [];
		foreach ($billinglines as $line) {
			if (empty($cost[$line->item_code])) {
				$cost[$line->item_code] = 0;
			}
			$cost[$line->item_code] += $line->price * $line->qty;
		}

		$job->mdata['rev_err'] = [];
		$exrate = Currency::getExrate($job->created, $job->currency)[0];
		$exrate = !empty($exrate) ? $exrate : 1;
		foreach ($rev as $k => $v) {
			if (!empty($cost[$k]) && ($v/$exrate) > 1.5 * $cost[$k] && $cost[$k] >= 0.01) {
				if ($resp['success']) {
					$resp['success'] = 0;
					$resp['msg'] = [];
				}
				$resp['msg'][] = 'Revenue of ' . EdiJob::getChargeItemTypes()[$k] . ' is higher than 1.5 * cost';
				$job->mdata['rev_err'][] = 'Revenue of ' . EdiJob::getChargeItemTypes()[$k] . ' is higher than 1.5 * cost';
			}
		}
		if ((array_sum($rev) / $exrate) > 1.5 * array_sum($cost) && array_sum($cost) >= 0.01) {
			if ($resp['success']) {
				$resp['success'] = 0;
				$resp['msg'] = [];
			}
			$resp['msg'][] = 'Revenue is higher than 1.5 * cost';
			$job->mdata['rev_err'][] = 'Revenue is higher than 1.5 * cost';
		}
		$job->update('meta');

		if (!empty($job->invoice) && !empty($job->mdata['ref2'])) {
			foreach ($job->invoice as $invoice) {
				if ($invoice->isRevert()) continue;
				$invoice->mdata['ref2'] = $job->mdata['ref2'];
				$invoice->update('meta');
			}
		}

		if ($resp['success'] == 0 && $_POST['confirm'] == 0) {
			echo json_encode($resp);
			return;
		} else {
			$resp['success'] = 1;
		}

		$rt = $this->createInvoiceByJob($jobId);
		if (!empty($rt)) {
			$resp['success'] = 0;
			$resp['msg'] = $rt;
		}
		echo json_encode($resp);
	}

	private function createInvoiceByJob($jobId)
	{
		$job = EdiJob::model()->findByPk($jobId);
		$errors = '';
		if (!empty($job)) {
			$oldPaymentLines = [];
			// create invoice
			// in case existing already just update it
			if (!empty($_POST['created'])) {
				$job->created = $_POST['created'];
			}
			if (!empty($_POST['due'])) {
				$job->due = $_POST['due'];
			}
			if (!empty($_POST['currency'])) {
				$job->currency = $_POST['currency'];
			}
			$job->update(['created', 'due', 'currency']);
			 
			$invoices = Invoice::model()->findAll(['order'=>'id DESC','condition'=>'job_id = :jid AND status not in (6,7,8,9,10)','params'=>[':jid' => $job->id]]);
			if (is_array($invoices)) {
				if (sizeof($invoices)>1) {
					return 'Invoice has error[901]';
				}
			}
			$invoice = Invoice::model()->find(['order'=>'id DESC','condition'=>'job_id = :jid AND status not in (6,7,8,9,10)','params'=>[':jid' => $job->id]]);
			if (empty($invoice)) {
				// try to find if all related invoice has been paid fully
				$paidInvoice = Invoice::model()->find('job_id = :jid AND status in (6,9)', [':jid' => $job->id,]);
				if (!empty($paidInvoice)) {
					return 'Invoice has been fully paid';
				}

				$invoice = new Invoice('create');
				$invoice->type = Invoice::INVOICE_TYPE_EDI_INVOICE;
				if (preg_match('/-/', $invoice->no)) {
					$invoice->status = Invoice::INVOICE_STATUS_POSTED;
				} else {
					$invoice->status = Invoice::INVOICE_STATUS_PENDING;
				}
				$invoice->dpt_id = $job->dpt_id;
				if($job->owner->by > 1){
					$invoice->to_id = $job->owner->by;
					// find ancestor
					if ($job->owner->owner->by > 1) $invoice->to_id = $job->owner->owner->by;
					$invoice->mdata['suborg'] = $job->owner_id;
				}else{
					$invoice->to_id = $job->owner_id;
				}
				
				$invoice->job_id = $job->id;
				if (empty($job->created) || $job->created == '0000-00-00') {
					$invoice->date = date('Y-m-d');
				} else {
					// $invoice->date = date('Y-m-d');
					// $invoice->date = $job->created;
					// Lip: make invoice date relate to inv_type, for comparing hvlv and xero financial reports
					if (preg_match('/import/i', $job->inv_type) && !empty($job->consol->eta)) {
						$invoice->date = $job->consol->eta;
					} else if (preg_match('/export/i', $job->inv_type) && !empty($job->consol->etd)) {
						$invoice->date = $job->consol->etd;
					} else {
						$invoice->date = $job->created;
					}
				}
				if (empty($job->due) || $job->due == '0000-00-00') {
					$invoice->due = date('Y-m-d');
				} else {
					// $invoice->due = date('Y-m-d');
					$invoice->due = $job->due;
				}
				$invoice->posted = $invoice->date;
				$invoice->dpmt = $job->dpmt;
				$invoice->currency = $job->currency;

				$oldInvoice = Invoice::model()->find(['order' => 'id DESC', 'condition' => 'job_id = :jid AND status in (6,7,8,9,10)', 'params' => [':jid' => $job->id]]);
				if (!empty($oldInvoice)) {
					$invoice->no = Invoice::genNewInvoiceNo($oldInvoice->no);
				}

				$invoice->save();
				if ($invoice->getErrors()) {
					if (isset($invoice->getErrors()['date'])) {
						$invoice->date = date('Y-m-d');
						$invoice->due = date('Y-m-d');
						$invoice->posted = date('Y-m-d');
						$invoice->save();
					}
				}
			} else {

				// in case invoice has been closed, we can't update old invoice again
				// we need create a new one
				if ($invoice->isInvoiceClosed()) {
					$oldPaymentLines = $invoice->createCreditForMe();

					// create a new invoice the for this month
					$invRef = 'Original Invoice : ' . $invoice->no;
					$oldInvoiceNo = $invoice->no;
					$invoice = new Invoice('create');
					$invoice->ref = $invRef;
					$invoice->no = Invoice::genNewInvoiceNo($oldInvoiceNo);     //$oldInvoiceNo . '-1'; // flag as revert used only
					$invoice->type = Invoice::INVOICE_TYPE_EDI_INVOICE;
					// $invoice->status = Invoice::INVOICE_STATUS_PENDING; // as posted
					if (preg_match('/-/', $invoice->no)) {
						$invoice->status = Invoice::INVOICE_STATUS_POSTED;
					} else {
						$invoice->status = Invoice::INVOICE_STATUS_PENDING;
					}
					$invoice->dpt_id = $job->dpt_id;
					if($job->owner->by > 1){
						$invoice->to_id = $job->owner->by;
						// find ancestor
						if ($job->owner->owner->by > 1) $invoice->to_id = $job->owner->owner->by;
						$invoice->mdata['suborg'] = $job->owner_id;
					}else{
						$invoice->to_id = $job->owner_id;
					}
					$invoice->job_id = $job->id;
					if (empty($job->created) || $job->created == '0000-00-00') {
						$invoice->date = date('Y-m-d');
					} else {
						$invoice->date = $job->created;
					}
					if (empty($job->due) || $job->due == '0000-00-00') {
						$invoice->due = date('Y-m-d');
					} else {
						$invoice->due = $job->due;
					}
					$invoice->dpmt = $job->dpmt;
					$invoice->posted = $invoice->date;
					$invoice->currency = $job->currency;
					$invoice->save();
					if ($invoice->getErrors()) {
						if (isset($invoice->getErrors()['date'])) {
							$invoice->date = date('Y-m-d');
							$invoice->due = date('Y-m-d');
							$invoice->posted = date('Y-m-d');
							$invoice->save();
						}
					}
				} else {
					// delete all old invoice lines
					InvLine::model()->deleteAll('inv_id = :invid', [':invid' => $invoice->id]);
				}
			}

			// create invoice lines
			foreach ($job->lines as $line) {
				$il = new InvLine;
				$il->inv_id = $invoice->id;
				$il->ccode = $line->ccode;
				$il->det = $line->desc;
				$il->amount = $line->rate;
				$il->qty = $line->qty;
				$il->tax = $line->inv_gst;
				$il->model = 'JobLine';
				$il->fid = $line->id;

				if ($il->hasGstByTaxValue(Invoice::INVOICE_TAX_TYPE_EXCLUSIVE)) {
					$il->gst = $il->amount * 10 / 100;
					$il->amount += $il->gst;
				}
				$il->save();
				$err = $il->getErrors();
				if (empty($err)) {
					$line->invline_id = $il->id;
					$line->update('invline_id');
				} else {
					foreach ($err as $k  => $e) {
						$errors .= implode(' ', $e);
					}
				}
			}

			// update invoice data
			$invoice->dpt_id = $job->dpt_id;
			$invoice->to_id = $job->owner->by > 1? $job->owner->by : $job->owner_id;
			// find ancestor
			if ($job->owner->owner->by > 1) $invoice->to_id = $job->owner->owner->by;
			$invoice->job_id = $job->id;
			$invoice->currency = $job->currency;
			$invoice->save();
			// if (empty($job->created) || $job->created == '0000-00-00') {
			// 	$invoice->date = date('Y-m-d');
			// } else {
			// 	$invoice->date = $job->created;
			// }
			// if (empty($job->due) || $job->due == '0000-00-00') {
			// 	$invoice->due = date('Y-m-d');
			// } else {
			// 	$invoice->due = $job->due;
			// }

			// save meta data for invoice
			$invoice->refresh();
			$invoice->getTotal();
			$invoice->checkPaid(); // cause ED invoice become posted or overdue directly

			// eva ED invoice keep pending
			if ((!empty($job->owner->extra['sp_id']) && $job->owner->extra['sp_id'] == 305) || (!empty($job->owner->extra['op_id']) && $job->owner->extra['op_id'] == 305)) {
				$invoice->status = Invoice::INVOICE_STATUS_PENDING;
			}

			if (in_array($job->owner->id, [Org::ORGID_AIRSEA_WGAU, Org::ORGID_AIRSEA_HOUPU]) && !empty($job->owner->owner)) {
				$owner = $job->owner->owner;
			} else {
				$owner = $job->owner;
			}
			$invoice->mdata['name'] = $owner->name;
			$invoice->mdata['address'] = $owner->getAddress();
			$invoice->mdata['payterm'] = empty($owner->extra['payterm']) ? 'COD' : $owner->extra['payterm'] . ' days';

			// need to sync with xero again
			$invoice->sync_xero = 0;
			if ($invoice->total == 0) {
				$invoice->status = 10;
			}
			$invoice->save();
			$err = $invoice->getErrors();
			foreach ($err as $k  => $e) {
				$errors .= implode(' ', $e);
			}

			if (!empty($oldPaymentLines)) {
				$invoice->applyPayments($oldPaymentLines);
			}
		}
		if (!empty($errors)) {
			return 'Failed to create/update invoice : ' . $errors;
		}
		return '';
	}

	/**
	 * create an invoice for selected lines for the JOB
	 * @param $lines
	 */
	private function createInvoiceForJob(&$job, &$lines)
	{
		// create invoice
		// in case existing already just update it
		$model = Invoice::model()->find('job_id = :jid', [':jid' => $job->id]);
		if (empty($model)) {
			$model = new Invoice('create');
			$model->type = Invoice::INVOICE_TYPE_EDI_INVOICE;
			$model->status = 2; // as posted
			$model->dpt_id = $job->dpt_id;
			if($job->owner->by > 1){
				$model->to_id = $job->owner->by;
				// find ancestor
				if ($job->owner->owner->by > 1) $model->to_id = $job->owner->owner->by;
				$model->mdata['suborg'] = $job->owner_id;
			}else{
				$model->to_id = $job->owner_id;
			}
			$model->job_id = $job->id;
			if (empty($job->created) || $job->created == '0000-00-00') {
				$model->date = date('Y-m-d');
			} else {
				$model->date = $job->created;
			}
			if (empty($job->due) || $job->due == '0000-00-00') {
				$model->due = date('Y-m-d');
			} else {
				$model->due = $job->due;
			}

			$model->currency = $job->currency;
			$model->save();
		}

		// create invoice lines
		foreach ($lines as $line) {
			$il = new InvLine;
			$il->inv_id = $model->id;
			$il->ccode = $line->ccode;
			$il->det = $line->desc;
			$il->amount = $line->rate;
			$il->qty = $line->qty;
			$il->tax = $line->inv_gst;

			if ($il->hasGstByTaxValue(Invoice::INVOICE_TAX_TYPE_EXCLUSIVE)) {
				$il->gst = $il->amount * 10 / 100;
				$il->amount += $il->gst;
			}

			$il->model = 'JobLine';
			$il->fid = $line->id;

			$il->save();
			if (!empty($il)) {
				$line->invline_id = $il->id;
				$line->update('invline_id');
			}
		}

		// update invoice data
		$model->dpt_id = $job->dpt_id;
		$model->to_id = $job->owner->by > 1? $job->owner->by : $job->owner_id;
		// find ancestor
		if ($job->owner->owner->by > 1) $model->to_id = $job->owner->owner->by;
		$model->job_id = $job->id;
		$model->date = $job->created;
		$model->due = $job->due;
		$model->currency = $job->currency;

		// save meta data for invoice
		$model->getTotal();
		$model->mdata['name'] = $job->owner->name;
		$model->mdata['address'] = $job->owner->getAddress();
		$model->mdata['payterm'] = empty($job->owner->extra['payterm']) ? 'COD' : $job->owner->extra['payterm'] . ' days';

		// need to sync with xero again
		$model->sync_xero = 0;
		$model->save();
	}

	public function actionMainJobLink()
	{
		if (!empty($_POST)) {
			$model = $this->loadModel($_POST['id']);
			$mainJob = EdiJob::model()->find('no = :no', [':no' => trim($_POST['main_job_no'])]);
			if (empty($mainJob)) {
				echo json_encode(['success' => false, 'msg' => 'Main Job No does not exist']);
				Yii::app()->end();
			} else {
				if (!empty($model->mdata['main'])) {
					foreach ($model->mdata['main'] as $main) {
						if ($main['id'] == $mainJob->id) {
							echo json_encode(['success' => false, 'msg' => 'Already linked']);
							Yii::app()->end();
						}
					}
				}
				$mainJob->mdata['sub'][] = ['id' => $model->id, 'no' => $model->no, 'note' => $_POST['main_job_note']];
				$mainJob->update('meta');
				$model->mdata['main'][] = ['id' => $mainJob->id, 'no' => $mainJob->no, 'note' => $_POST['main_job_note']];
				$model->update('meta');
				echo json_encode(['success' => true]);
				Yii::app()->end();
			}
		}
	}

	public function actionSwitchBillingLine()
	{
		if (!empty($_POST['selected']) && count($_POST['selected']) == 2) {
			$line1 = BillingLine::model()->findByPk($_POST['selected'][0]);
			$line2 = BillingLine::model()->findByPk($_POST['selected'][1]);

			$temp = $line1->actual_amount;
			$line1->actual_amount = $line2->actual_amount;
			$line2->actual_amount = $temp;

			$temp = $line1->gst_amount;
			$line1->gst_amount = $line2->gst_amount;
			$line2->gst_amount = $temp;

			$temp = $line1->billing_cref;
			$line1->billing_cref = $line2->billing_cref;
			$line2->billing_cref = $temp;

			$temp = $line1->desc;
			$line1->desc = $line2->desc;
			$line2->desc = $temp;

			$temp = $line1->item_code;
			$line1->item_code = $line2->item_code;
			$line2->item_code = $temp;

			$temp = $line1->charge_code;
			$line1->charge_code = $line2->charge_code;
			$line2->charge_code = $temp;

			$temp = $line1->mdata;
			$line1->mdata = $line2->mdata;
			$line2->mdata = $temp;

			$line1->update('actual_amount', 'gst_amount', 'billing_cref', 'desc', 'item_code', 'charge_code', 'meta');
			$line2->update('actual_amount', 'gst_amount', 'billing_cref', 'desc', 'item_code', 'charge_code', 'meta');

			echo json_encode(['success' => true]);
			Yii::app()->end();
		} else {
			echo json_encode(['success' => false, 'msg' => 'please select 2 lines']);
			Yii::app()->end();
		}
	}

	public function actionRateSuggest()
	{
		$job = EdiJob::model()->findByPk($_GET['jid']);
		$_GET['org'] = $job->owner_id;

		$type = $_GET['type'];
		$org = $_GET['org'];

		$condition = 'ccode = :ccode AND job.owner_id = :org_id';
		$params = [':ccode' => $type, ':org_id' => $org];

		if (!empty($_GET['jid'])) {
			$condition .= ' AND job.id != :id';
			$params[':id'] = $_GET['jid'];
		}
		$jobline = JobLine::model()->with('job')->find(['condition' => $condition, 'params' => $params, 'order' => 't.id desc']);

		echo json_encode(['rate' => @$jobline->rate]);
		Yii::app()->end();
	}

	public function actionAjaxAutofill()
	{
		$resp = array('data' => new stdClass, 'success' => 1);
		$airline = @$_POST['airline'];
		$flight = @$_POST['flight'];
		$wmstask = @$_POST['wmstask'];
		$weight = floatval(@$_POST['weight']);
		$pod = @$_POST['pod'];
		$pol = @$_POST['pol'];
		$org_id = @$_POST['org_id'];

		if (empty($flight)) {
			$resp['msg'] = 'Please input flight no';
		}

		// add air freight cost
		if (!empty($flight)) {
			if (!empty($wmstask)) {
				if ($wmstask->type == 3010) {
					foreach ($wmstask->items as $item) {
						$plt = WmsLocation::model()->findByPk($item->mdata['pli']);
						$weight += floatval(@$plt->extra['weight']);
					}
				}
			}

			if ($weight > 0) {
				$quote = Quotes::model()->with('route')->find('route.flight_no LIKE :flight_no AND route.code = :code AND wt_lo <= :weight AND (wt_hi > :weight OR wt_hi IS NULL)', [':flight_no' => '% ' . $flight . ' %', ':weight' => $weight, ':code' => substr($pod, 2)]);
				if (empty($quote)) {
					$resp['msg'] = 'Cost - This flight do not have appropriate rate';
				}
			} else {
				$resp['msg'] = 'Plts in this WmsTask do not have weight';
			}

			if (!empty($quote)) {
				$cost = new stdClass;
				$cost->desc = 'Air Freight';
				$cost->gst = 'EXEMPTEXPENSES';
				$cost->item_code = 'GL1';
				$cost->org_id = 964;
				$cost->price = $quote->pkg;
				$cost->qty = $weight;
				$cost->supplier_name = '';
				$org = Org::model()->findByPk($cost->org_id);
				if (!empty($org)) {
					$cost->supplier_name = $org->name;
				}
				$resp['data']->cost[] = $cost;
			}
		}

		// add export security fee cost
		if (!empty($flight)) {
			$route = Route::model()->find('flight_no LIKE :flight_no', [':flight_no' => '% ' . $flight . ' %']);
			if (empty($route) || empty($route->airline)) {
				$resp['msg'] = 'Cost - This flight is not exist';
			} else {
				if (empty($route->airline->terminal->extra['terminal_rate'])) {
					$resp['msg'] = 'Cost - Terminal rate has not been set up';
				} else {
					if ($weight == 0) {
						$resp['msg'] = 'Weight is 0';
					} else {
						$cost = new stdClass;
						$cost->desc = 'Export Security Fee';
						$cost->gst = 'EXEMPTEXPENSES';
						$cost->item_code = 'GL11';
						$cost->org_id = 964;
						$cost->price = $route->airline->terminal->extra['terminal_rate'];
						$cost->qty = $weight;
						$cost->supplier_name = '';
						$org = Org::model()->findByPk($cost->org_id);
						if (!empty($org)) {
							$cost->supplier_name = $org->name;
						}
						$resp['data']->cost[] = $cost;
					}
				}
			}
		}

		$org = Org::model()->findByPk($org_id);
		// add air freight revenue
		if (!empty($org->extra['af_rates'])) {
			foreach ($org->extra['af_rates'] as $rate) {
				if ($rate['pol'] == $pol && $rate['pod'] == $pod) {
					foreach ($rate['range'] as $k => $range) {
						$next_range = @$rate['range'][$k+1];
						if ($weight >= $range && (empty($next_range) || $next_range > $weight) && preg_match('/' . $airline . '/i', $rate['airline'])) {
							$invoice = new stdClass;
							$invoice->ccode = 'GL1';
							$invoice->desc = 'Air Freight';
							$invoice->inv_gst = 'EXEMPTEXPORT';
							$invoice->qty = $weight;
							$invoice->rate = $rate['rate'][$k];
							$resp['data']->invoice[] = $invoice;
							break 2;
						}
					}
				}
			}
		}

		// add export security fee revenue
		if (!empty($org->extra['esf_rate'])) {
			$invoice = new stdClass;
			$invoice->ccode = 'GL11';
			$invoice->desc = 'Export Security Fee';
			$invoice->inv_gst = 'EXEMPTEXPORT';
			$invoice->qty = $weight;
			$invoice->rate = $org->extra['esf_rate'];
			$resp['data']->invoice[] = $invoice;
		}

		// add xray fee revenue
		if (!empty($org->extra['xray_rate'])) {
			$invoice = new stdClass;
			$invoice->ccode = 'GL85';
			$invoice->desc = 'Air - Enhanced Air Cargo Examination Fee - Primary Level Screening';
			$invoice->inv_gst = 'EXEMPTEXPORT';
			$invoice->qty = $weight;
			$invoice->rate = $org->extra['xray_rate'];
			$resp['data']->invoice[] = $invoice;
		}

		if (empty($resp['data']->cost)) {
			$resp['success'] = 0;
		}

		echo json_encode($resp);
	}

	public function actionAjaxAutofill2()
	{
		$resp = array('success' => 0);
		$job = EdiJob::model()->findByPk($_GET['jid']);
		if (!empty($_POST['weight']) && $job->mdata['jbv_gw'] != $_POST['weight']) {
			$job->mdata['jbv_gw'] = $_POST['weight'];
			$job->update('meta');
		}
		$weight = floatval(@$job->mdata['jbv_gw']);
		$awbModel = EdiAwbConsol::model()->find('awb = :awb', [':awb' => $job->awb]);
		$flight = @$_POST['flight'];

		if (empty($flight)) {
			$resp['msg'] = 'Please input flight no';
		}

		// add air freight cost
		if (!empty($flight)) {
			if (!empty($wmstasks)) {
				foreach ($wmstasks as $wmstask) {
					if ($wmstask->type == 3010) {
						foreach ($wmstask->items as $item) {
							$plt = WmsLocation::model()->findByPk($item->mdata['pli']);
							$weight += floatval(@$plt->extra['weight']);
						}
					}
				}
			}

			if ($weight > 0) {
				$quote = Quotes::model()->with('route')->find('route.flight_no LIKE :flight_no AND route.code = :code AND wt_lo <= :weight AND (wt_hi > :weight OR wt_hi IS NULL)', [':flight_no' => '% ' . $flight . ' %', ':weight' => $weight, ':code' => substr($awbModel->pod, 2)]);
				if (empty($quote)) {
					$resp['msg'] = 'Cost - This flight and destination do not have appropriate rate';
				}
			} else {
				$resp['msg'] = 'Plts in this WmsTask do not have weight';
			}

			if (!empty($quote)) {
				$il = BillingLine::model()->find('item_code = "GL1" AND billing_ref = :ref AND status != 11', [':ref' => $job->no]);
				if (empty($il)) {
					$il = new BillingLine;
					$il->item_code = 'GL1';
					$il->desc = 'Air Freight';
					$il->qty = $weight;
					$il->gst = 'EXEMPTEXPENSES';
					$il->org_id = 964;
					BillingLine::saveExportAirFreightBillingByBilling($job, $il);
				}
				$il->price = $quote->pkg;
				$il->qty = $weight;
				$il->accrual_amount = $il->qty * $il->price;
				$il->save();
			}
		}

		// add export security fee cost
		if (!empty($flight)) {
			$route = Route::model()->find('flight_no LIKE :flight_no', [':flight_no' => '% ' . $flight . ' %']);
			if (empty($route) || empty($route->airline)) {
				$resp['msg'] = 'Cost - This flight is not exist';
			} else {
				if (empty($route->airline->terminal->extra['terminal_rate'])) {
					$resp['msg'] = 'Cost - Terminal rate has not been set up';
				} else {
					$il = BillingLine::model()->find('item_code = "GL11" AND billing_ref = :ref AND status != 11', [':ref' => $job->no]);
					if (empty($il)) {
						$il = new BillingLine;
						$il->item_code = 'GL11';
						$il->desc = 'Export Security Fee';
						$il->gst = 'EXEMPTEXPENSES';
						$il->org_id = 964;
						BillingLine::saveExportAirFreightBillingByBilling($job, $il);
					}
					$il->price = $route->airline->terminal->extra['terminal_rate'];
					$il->qty = $weight;
					$il->accrual_amount = $il->qty * $il->price;
					$il->save();
				}
			}
		}

		// add air freight revenue
		if (!empty($job->owner->extra['af_rates'])) {
			foreach ($job->owner->extra['af_rates'] as $rate) {
				if ($rate['pol'] == $job->awbconsol->pol && $rate['pod'] == $job->awbconsol->pod) {
					foreach ($rate['range'] as $k => $range) {
						$next_range = @$rate['range'][$k+1];
						if ($weight >= $range && (empty($next_range) || $next_range > $weight) && preg_match('/' . $job->awbconsol->airline . '/i', $rate['airline'])) {
							$il = JobLine::model()->find('job_id = :job_id AND ccode = "GL1"', [':job_id' => $job->id]);
							if (empty($il)) {
								$il = new JobLine;
								$il->job_id = $job->id;
								$il->ccode = 'GL1';
								$il->desc = 'Air Freight';
								$il->inv_gst = 'EXEMPTEXPORT';
							}
							$il->qty = $weight;
							$il->rate = $rate['rate'][$k];
							$il->save();
							break;
						}
					}
				}
			}
		}

		// add export security fee revenue
		if (!empty($job->owner->extra['esf_rate'])) {
			$il = JobLine::model()->find('job_id = :job_id AND ccode = "GL11"', [':job_id' => $job->id]);
			if (empty($il)) {
				$il = new JobLine;
				$il->job_id = $job->id;
				$il->ccode = 'GL11';
				$il->desc = 'Export Security Fee';
				$il->inv_gst = 'EXEMPTEXPORT';
			}
			$il->qty = $weight;
			$il->rate = $job->owner->extra['esf_rate'];
			$il->save();
		}

		// add xray fee revenue
		if (!empty($job->owner->extra['xray_rate'])) {
			$il = JobLine::model()->find('job_id = :job_id AND ccode = "GL85"', [':job_id' => $job->id]);
			if (empty($il)) {
				$il = new JobLine;
				$il->job_id = $job->id;
				$il->ccode = 'GL85';
				$il->desc = 'Air - Enhanced Air Cargo Examination Fee - Primary Level Screening';
				$il->inv_gst = 'EXEMPTEXPORT';
			}
			$il->qty = $weight;
			$il->rate = $job->owner->extra['xray_rate'];
			$il->save();
		}

		if (empty($resp['msg'])) {
			$resp['success'] = 1;
		}

		echo json_encode($resp);
	}

	public function actionTerminalRate()
	{
		$orgs = Org::model()->findAll('name LIKE "export%"');
		if (empty($_POST)) {
			$this->render('terminal_rate', ['orgs' => $orgs]);
		} else {
			if (!empty($_POST['rates'])) {
				foreach ($_POST['rates'] as $oid => $rate) {
					$org = Org::model()->findByPk($oid);
					if ($rate != $org->extra['terminal_rate']) {
						$org->extra['terminal_rate'] = $rate;
						$org->sync_xero = false;
						$org->update('meta');
					}
				}
			}
			echo json_encode(['done' => true, 'msg' => 'Save successfully']);
			yii::app()->end();
		}
	}

	public function actionInvoiceRate($id)
	{
		$model = Org::model()->findByPk($id);
		if (empty($_POST)) {
			$list = AppHelper::setting2List('pols');
			ksort($list);
			$pods = preg_replace('/\v/', '', CHtml::dropDownList('pod[]', '', $list, array('prompt' => 'All', 'style' => 'width: 100%')));
			$list = AppHelper::setting2List('pods');
			ksort($list);
			$pols = preg_replace('/\v/', '', CHtml::dropDownList('pol[]', '', $list, array('prompt' => 'All', 'style' => 'width: 100%')));
			$this->render('invoice_rate', ['rates' => @$model->extra['af_rates'], 'esf_rate' => @$model->extra['esf_rate'], 'xray_rate' => @$model->extra['xray_rate'], 'pods' => $pods, 'pols' => $pols]);
		} else {
			if (!empty($_POST['pol'])) {
				$model->extra['af_rates'] = [];
				foreach ($_POST['pol'] as $k => $pol) {
					$item['pol'] = $pol;
					$item['pod'] = @$_POST['pod'][$k];
					$item['airline'] = @$_POST['airline'][$k];
					if (!empty($_POST['range'][$k])) {
						asort($_POST['range'][$k]);
					}
					$item['range'] = @array_values($_POST['range'][$k]);
					if (!empty($_POST['rate'][$k])) {
						arsort($_POST['rate'][$k]);
					}
					$item['rate'] = @array_values($_POST['rate'][$k]);
					if (!empty($item['range'])) {
						foreach ($item['range'] as $i => $v) {
							if (empty($v)) {
								unset($item['range'][$i]);
							}
						}
					}
					if (!empty($item['rate'])) {
						foreach ($item['rate'] as $i => $v) {
							if (empty($v)) {
								unset($item['rate'][$i]);
							}
						}
					}
					$item['range'] = @array_values($item['range']);
					$item['rate'] = @array_values($item['rate']);
					$model->extra['af_rates'][] = $item;
				}
			}
			if (!empty($_POST['esf'])) {
				$model->extra['esf_rate'] = $_POST['esf'];
			}
			if (!empty($_POST['xray'])) {
				$model->extra['xray_rate'] = $_POST['xray'];
			}
			$model->sync_xero = false;
			$model->update('meta');
			echo json_encode(['done' => true, 'msg' => 'Save successfully']);
			yii::app()->end();
		}
	}

	public function actionDeliveryRecord()
	{
		$this->render('delivery_record');
	}

	public function actionDeliveryRecordFiles($id)
	{
		$record = DeliveryRecord::model()->findByPk($id);
		$this->render('delivery_record_files', ['model' => $record]);
	}

	public function actionDeliveryUpdate($id)
	{
		$record = DeliveryRecord::model()->findByPk($id);
		if (empty($_POST)) {
			$this->render('delivery_update', ['model' => $record]);
		} else {
			$record->task = $_POST['DeliveryRecord']['task'];
			$record->save();
			$this->ajaxResult($record);
		}
	}

	public function actionAjaxPlt($jid)
	{
		$model = $this->loadModel($jid);
		$model->mdata['plt'] = @$_POST['plt'];
		$model->mdata['ref'] = @$_POST['ref'];
		$model->mdata['jbv_gw'] = @$_POST['jbv_gw'];
		$model->mdata['ref2'] = @$_POST['ref2'];
		$model->mdata['hc40'] = @$_POST['hc40'];
		$model->mdata['hc20'] = @$_POST['hc20'];
		$model->mdata['gp40'] = @$_POST['gp40'];
		$model->mdata['gp20'] = @$_POST['gp20'];
		$model->mdata['plt_change'] = @$_POST['plt_change'];
		$model->save();

		if (!empty($model->invoice) && !empty($model->mdata['ref2'])) {
			foreach ($model->invoice as $invoice) {
				if ($invoice->isRevert()) continue;
				$invoice->mdata['ref2'] = $model->mdata['ref2'];
				$invoice->update('meta');
			}
		}

		$this->ajaxResult($model);
	}

	public function actionCheckAwb()
	{
		if (!empty($_GET['awb'])) {
			$condition = 'awb = :awb';
			$params = [':awb' => $_GET['awb']];
			if (!empty($_GET['id'])) {
				$condition .= ' AND id != :id';
				$params[':id'] = $_GET['id'];
			}
			$consol = EdiJob::model()->find($condition, $params);
			if (!empty($consol)) {
				echo json_encode(['done' => false, 'msg' => 'AWB exist']);
				Yii::app()->end();
			} else {
				echo json_encode(['done' => true]);
				Yii::app()->end();
			}
		}

		echo json_encode(['done' => true]);
		Yii::app()->end();
	}

	public function actionCalendar()
	{
		$this->render('calendar');
	}

	public function actionRenderCalendar()
	{
		if ($_GET['type'] == 'month') {
			$this->renderPartial('_calendar_month');
		} else if ($_GET['type'] == 'week') {
			$this->renderPartial('_calendar_week');
		}
	}

	public function actionCreateCalendarItem()
	{
		$model = new CalendarItem;
		$model->date = $_GET['date'];
		if (!empty($_GET['time'])) $model->time = $_GET['time'];
		if (empty($_POST)) {
			$this->renderPartial('create_calendar_item', ['model' => $model]);
		} else {
			$model->attributes = $_POST['CalendarItem'];
			if (!empty($_POST['no'])) {
				$job = EdiJob::model()->find('no = :no', [':no' => $_POST['no']]);
				if (!empty($job)) {
					$model->model = 'EdiJob';
					$model->fid = $job->id;
				}
				$task = WmsTask::model()->find('id = :id', [':id' => ltrim($_POST['no'], 'T')]);
				if (!empty($task)) {
					$model->model = 'WmsTask';
					$model->fid = $task->id;
				}
				if (empty($model->model)) {
					$model->fid = $_POST['no'];
				}
			}
			$model->save();

			$this->ajaxResult($model);
		}
	}

	public function actionUpdateCalendarItem($id)
	{
		$model = CalendarItem::model()->findByPk($id);
		if (empty($_POST)) {
			$this->renderPartial('update_calendar_item', ['model' => $model]);
		} else {
			$model->attributes = $_POST['CalendarItem'];
			if (!empty($_POST['no'])) {
				$job = EdiJob::model()->find('no = :no', [':no' => $_POST['no']]);
				if (!empty($job)) {
					$model->model = 'EdiJob';
					$model->fid = $job->id;
				}
				$task = WmsTask::model()->find('id = :id', [':id' => ltrim($_POST['no'], 'T')]);
				if (!empty($task)) {
					$model->model = 'WmsTask';
					$model->fid = $task->id;
				}
				if (empty($model->model)) {
					$model->fid = $_POST['no'];
				}
			}
			$model->save();

			$this->ajaxResult($model);
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = EdiJob::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
}
