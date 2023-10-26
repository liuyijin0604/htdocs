<?php

class ConsolWeightCheckController extends Controller{

	protected $nonAjax=array('ajaxGetAwbCustomer','getAttachs','export');

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model = new ExconsolCost('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['ExconsolCost']['billing_no'])) {
			$model->billing_no = $_GET['ExconsolCost']['billing_no'];
			unset($_GET['ExconsolCost']['billing_no']);
		}
		if (isset($_GET['ExconsolCost']['invoice_no'])) {
			$model->invoice_no = $_GET['ExconsolCost']['invoice_no'];
			unset($_GET['ExconsolCost']['invoice_no']);
		}
		if (isset($_GET['ExconsolCost']['consol_no'])) {
			$model->consol_no = $_GET['ExconsolCost']['consol_no'];
			unset($_GET['ExconsolCost']['consol_no']);
		}
		if (isset($_GET['ExconsolCost']['consol_poc'])) {
			$model->consol_poc = $_GET['ExconsolCost']['consol_poc'];
			unset($_GET['ExconsolCost']['consol_poc']);
		}
		if(isset($_GET['ExconsolCost'])) {
			$model->attributes = $_GET['ExconsolCost'];
		}
		$this->render('list',array(
			'model'=>$model,
		));
	}

	public function actionConsolList() {
		$model = new ConsolWeightCheckLine('search');
		$model->unsetAttributes();
		if (isset($_GET['ConsolWeightCheckLine'])) {
			$model->attributes = $_GET['ConsolWeightCheckLine'];
		}
		$this->render('consol_list', array(
			'model' => $model
		));
	}

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
	 * @param $id
	 * delete the specified console weight check
	 */
	public function actionDelete($id){
		$consol = ExconsolCost::model()->findByPk($id);
		if ( !empty($consol) ) {
			$consol->status = 9; // set as delete status
			$consol->update('status');
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreateCheck(){
		// check to see which button clicked
		if ( isset($_POST['yt0']) && $_POST['yt0'] == 'Import'){
			echo json_encode($this->importSourceData());
			Yii::app()->end();
		}

		/*if ( isset($_POST['yt1']) && $_POST['yt1'] == 'Calculate'){
			// do nothing currently
			$this->ajaxResult($model, array('id'));
		}*/

		if ( isset($_POST['yt1']) && $_POST['yt1'] == 'Save')
		{
			$this->saveWeightCheck($this->importSourceData());
			echo json_encode(array('done' => true, 'msg' => 'Save successfully'));
			Yii::app()->end();
		}

		$this->render('create_check');
	}

	/**
	 *
	 */
	private function importSourceData(){
		$resp = array('success' => 0 , 'msg' => '','data' => '','step' => 1);
		$check_file = empty($_FILES['cwc_file']) ? array() : $_FILES['cwc_file'];
		if ( empty($check_file['tmp_name']) || !is_uploaded_file( $check_file['tmp_name'] ) ) {
			$resp['msg'] = 'Invalid template file';
			echo json_encode($resp);
		} else {
			$xls = new oExcel;
			$xls->load($check_file['tmp_name']);
			$data = $xls->getAll();

			// remove title
			unset($data[1]);

			// load all data now
			$resultData = [];
			$err = [];

			foreach ( $data as $k => $line) {
				$awb = preg_replace('/[^\d]+/', '', $line[1]);
				if(empty($awb)) continue;

				//get consol
				$exc = ExcoConsol::model()->find("type = 25 AND awb = :awb OR awb = :awb2",[':awb' => $awb, ':awb2' => substr($awb,0,3).'-'.substr($awb,3)]);

				if(empty($exc)){
					$err[] = $awb.' not found';
				}else{
					$data = [
						'consol' =>[
							'id' => $exc->id,
							'no' => $exc->no,
							'awb' => $exc->awb,
							'poc' => ExChannel::getName($exc->poc),
							'etd' => $exc->etd,
							'awb_weight' => isset($exc->mdata['awb_check_wt']) ? $exc->mdata['awb_check_wt'] : 0,
							'qty' => $exc->totShipments(),
							'weight' => $exc->totWeight(),
						],
						'cost' => [
							'4' => round(PlLedger::getTotal('ExParcel', $exc->id, 'dc', 3) * $exc->exrate),
							'5' => round(PlLedger::getTotal('ExParcel', $exc->id, 'cr', 3) * $exc->exrate),
							'6' => round($exc->totShipments() * ExChannel::getDuty($exc->poc, $exc->etd)),
							'9' => 0,
						],
						'bill' => [
							'ref' => $line[8],
							'weight' => number_format(floatval($line[2]), 2, '.' , ''),
							'qty' => intval($line[3]),
							'4' => number_format(floatval($line[4]), 2, '.', ''),
							'5' => number_format(floatval($line[5]), 2, '.', ''),
							'6' => number_format(floatval($line[6]), 2, '.', ''),
							'9' => number_format(floatval($line[7]), 2, '.', ''),
						],
						'hist' => [],
						'dup' => [],
					];
					$rs = ExconsolCost::model()->findAll('consol_id = :cid AND status IN (1, 5, 9)', [':cid' => $exc->id]);
					$hh = [];
					foreach($rs as $r){
						$hh[$r->billing_id][$r->type] = $r->amount;
					}
					foreach($hh as $bid => $h){
						$b = Billing::model()->findByPk($bid);
						$h['ref'] = $b->billing_cref;
						if ($h['ref'] != $data['bill']['ref']) {
							$data['hist'][] = $h;
						} else {
							$data['dup'][] = $h;
						}
					}
					$resultData[] = $data;
				}
			}

			$resp['success'] = 1;
			$resp['data'] = $resultData;
			$resp['err'] = $err;

			return $resp;
		}
	}

	/**
	 * @param $model
	 */
	private function saveWeightCheck($resp = null){
		if ($resp) {
			foreach ($resp['data'] as $line) {
				$excol = ExcoConsol::model()->find('no = :no', array(':no' => $line['consol']['no']));

				// create billing
				$bill = Billing::model()->find('billing_cref = :billing_cref', array(':billing_cref' => $line['bill']['ref']));
				if (empty($bill)) {
					$bill = new Billing;
					$bill->org_id = $excol->owner_id;
					$bill->created = date('Y-m-d');
					$bill->date = date('Y-m-d');
					$bill->due = date('Y-m-d');
					$bill->type = BillingLine::BILLING_TYPE_EXPORT;
					$bill->dpmt = 20;
					$bill->status = 1;
					$bill->billing_cref = $line['bill']['ref'];
					$bill->billing_ref = $excol->no;
					$bill->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
					$bill->currency = 3;
					$bill->no = $bill->genNo();
					$bill->sync_xero = 0;
					$bill->save();
				}

				foreach ($line['bill'] as $index => $value) {
					if ($value == 0) continue;

					if ($index == 'qty') {
						$type = 1;
					} else if ($index == 'weight') {
						$type = 2;
					} else if (is_numeric($index)) {
						$type = $index;
					}

					// create exconsol cost
					$eccost = ExconsolCost::model()->find(
						'consol_id = :consol_id AND billing_id = :billing_id AND type = :type',
						array(
							':consol_id' => $excol->id,
							':billing_id' => $bill->id,
							':type' => $type
						)
					);
					// dup eccost
					if (!empty($eccost)) {
						if ($value == $eccost->amount) {
						} else {
							$eccost->amount = number_format($value, 2, '.' , '');
							$eccost->save();
						}
					} else {
						$eccost = new ExconsolCost;
						$eccost->consol_id = $excol->id;
						$eccost->billing_id = $bill->id;
						$eccost->type = $type;
						$eccost->status = 1;
						$eccost->amount = number_format($value, 2, '.' , '');
						$eccost->save();
					}

					if (is_numeric($index)) {
						// create billing line
						$bl = BillingLine::model()->find('billing_cref = :billing_cref AND item_code = :item_code AND billing_ref = :billing_ref', array(':billing_cref' => $line['bill']['ref'], ':item_code' => 'channel_' . strtolower(ExconsolCost::$types[$index]) . '_cost', ':billing_ref' => $excol->no));
						if (!empty($bl)) {
							if ($value == $bl->actual_amount) {
							} else {
								$bl->price = number_format($value, 2, '.' , '');
								$bl->actual_amount = number_format($value, 2, '.' , '');
								$bl->save();
							}
						} else {
							$bl = new BillingLine;
							$bl->billing_id = $bill->id;
							$bl->org_id = $bill->org_id;
							$bl->op_id = Yii::app()->user->id;
							$bl->link_id = $excol->id;
							$bl->created = date('Y-m-d');
							$bl->date = date('Y-m-d');
							$bl->due = date('Y-m-d');
							$bl->transaction_date = date('Y-m-d');
							$bl->type = BillingLine::BILLING_TYPE_EXPORT;
							$bl->dpmt = 20;
							$bl->gst = 'EXEMPTEXPENSES';
							$bl->status = 1;
							$bl->billing_cref = $line['bill']['ref'];
							$bl->billing_ref = $excol->no;
							$bl->awb = $line['consol']['awb'];
							$bl->desc = 'channel_' . strtolower(ExconsolCost::$types[$index]) . '_cost';
							$bl->qty = 1;
							$bl->price = number_format($value, 2, '.' , '');
							$bl->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
							$bl->currency = 3;
							$bl->no = $bill->no;
							$bl->actual_amount = number_format($value, 2, '.' , '');
							$bl->accrual_amount = $line['cost'][$index];
							$bl->gst_amount = 0;
							$bl->charge_code = 91002;
							$bl->weight = $line['consol']['awb_weight'];
							$bl->charge_weight = $line['bill']['weight'];
							$bl->item_code = 'channel_' . strtolower(ExconsolCost::$types[$index]) . '_cost';
							$bl->sync_xero = 0;
							$bl->save();
						}
					}
				}
			}
		}
	}

	public function actionConfirm($id) {
		$bill = Billing::model()->findByPk($id);

		if (!empty($bill)) {
			$eccosts = ExconsolCost::model()->findAll('billing_id = :billing_id', array(':billing_id' => $id));
			foreach ($eccosts as $eccost) {
				$eccost->status = array_search('Approved', ExconsolCost::$states);
				$eccost->save();
			}
		}

		$this->ajaxResult($bill);
	}

	/**
	 * @param $id
	 * @throws CHttpException
	 */
	public function actionNotes($id){
		$model = $this->loadModel($id);
		if(!empty($_POST['notes'])){
			$log = Log::add($model, 6, array('notes' => $_POST['notes']));
			$this->ajaxResult($log);
		}
	}

	/**
	 * @param $id
	 */
	public function actionAttach($id){
		$model = $this->loadModel($id);
		$this->render('attach_form',array(
			'model'=>$model,
		));
	}

	public function actionGetAttachs(){
		$id = $_GET['id'];
		// get latest uploaded zone map file
		$attachements = FileRepo::model()->findAll('fid = :oid and type = 88 order by id desc', [':oid' => $id]);

		$r = new stdClass();
		$r->success = 1;
		$index = 1;
		$r->data = '';
		foreach ( $attachements as $attachement ) {
			$r->data .= '<li>' . $index++ . '. <a target="_blank" href="' . $attachement->getUrl() . '" >' . $attachement->name . '</a></li>';
		}

		echo json_encode($r);

	}

	public function actionAjaxSaveAttach(){

		$cwcId = $_POST['ConsolWeightCheck']['id'];
		$model = $this->loadModel($cwcId);

	  //  $resp = array('success' => 1,'msg' => 'file attached successfully');
		$template_file = empty($_FILES['cwc_attach']) ? array() : $_FILES['cwc_attach'];
		if ( empty($template_file['tmp_name']) || !is_uploaded_file( $template_file['tmp_name'] ) ) {
		   // $resp['msg'] = 'Invalid file';
		  // echo json_encode($resp);
			$model->setError('Invalid File');
			$this->ajaxResult($model,array('id'));
		  //  return;
		} else {

			//save file
			FileRepo::storeFile($template_file['tmp_name'], $template_file['name'], 88,$cwcId);

		}

		$this->ajaxResult($model,array('id'));

		//echo json_encode($resp);
	}
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id){
		$model = $this->loadModel($id);
		if ( isset($_POST['yt1'])  || isset($_POST['yt2'])  ) {

			if ( isset($_POST['yt2'] ) && $_POST['yt2'] == 'Sync Xero' ) {

				// get date , due ,and invoice ref
				$date = $_POST['ConsolWeightCheck']['date'];
				$due = $_POST['ConsolWeightCheck']['due'];
				$invoice_ref = $_POST['ConsolWeightCheck']['invoice_ref'];

				if (empty($date) || empty($due) || empty($invoice_ref)) {
					$model->addError('id','Date, Due and Invoice Reference are mandatory!');
					$this->ajaxResult($model);
				} else {

					$model->date = $date;
					$model->due = $due;
					$model->invoice_ref = $invoice_ref;
					$model->updateMeta();

					if (!$this->syncBilling2Xero($model)) {
						$model->addError('id','Unknown Error!');
						$this->ajaxResult($model);
					} else {
						$this->ajaxResult($model, ['id'], 'Sync with xero Successfully');
					}
				}
			} else if ( isset($_POST['yt1']) &&  $_POST['yt1'] == 'Update') {
				// update all recalculate data
				$exchangeRate = isset($_POST['ConsolWeightCheck']['exchange_rate']) ? $_POST['ConsolWeightCheck']['exchange_rate'] : 0;

				if ( $exchangeRate != $model->exchange_rate && $exchangeRate > 0 ) {
					// re-calculate for each lines
					foreach ( $model->lines as $line ) {
						$line->clearance_cost = round($line->clearance_cost / $exchangeRate,2);
						$line->delivery_cost = round($line->delivery_cost / $exchangeRate,2);
						$line->duty = round($line->duty / $exchangeRate,2);
						$line->others = round($line->others / $exchangeRate,2);
						$line->update(['clearance_cost','delivery_cost','duty','others']);
					}
				}
				$model->attributes = $_POST['ConsolWeightCheck'];
				$model->channel_qty = intval(str_replace(',', '', $model->channel_qty));
				$model->channel_weight = floatval(str_replace(',', '', $model->channel_weight));
				$model->awb_qty = intval(str_replace(',', '', $model->awb_qty));
				$model->channel_cost = floatval(str_replace(',', '', $model->channel_cost));
				$model->unit_cost_kg = floatval(str_replace(',', '', $model->unit_cost_kg));
				$model->invoice_revenue = floatval(str_replace(',', '', $model->invoice_revenue));
				$model->invoice_weight = floatval(str_replace(',', '', $model->invoice_weight));
				$model->charge_rate_kg = floatval(str_replace(',', '', $model->charge_rate_kg));
				$model->mdata['exchange_rate'] = $exchangeRate;
				$model->save();
				$errors = $model->getErrors();

				if ( empty($errors) ) {
					// save related cost to report system
					$this->saveCost($model);
				}
			}
			$this->ajaxResult($model);

		}

		if(isset($_GET['tab'])){
			Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);
			$this->render('tab_'.$_GET['tab'], array('model'=>$model));
		}else{
			$this->render('update',array('model'=>$model));
		}

	}

	public function actionDetail($id, $type) {
		if ($type == 'billing') {
			$bill = Billing::model()->findByPk($id);
			$consols = ExconsolCost::model()->findAll(array('select' => 'distinct consol_id', 'condition' => 'billing_id = :billing_id', 'params' => array(':billing_id' => $id)));
			$data = array();
			foreach ($consols as $consol_id) {
				// consol
				$consol = ExcoConsol::model()->findByPk($consol_id->consol_id);
				$data[$consol->id]['consol'] = array(
					'id' => $consol->id,
					'no' => $consol->no,
					'awb' => $consol->awb,
					'poc' => ExChannel::getName($consol->poc),
					'etd' => $consol->etd,
					'awb_weight' => isset($consol->mdata['awb_check_wt']) ? $consol->mdata['awb_check_wt'] : 0,
					'qty' => $consol->totShipments(),
					'weight' => $consol->totWeight()
				);
				// accrual cost
				$data[$consol->id]['cost'] = array(
					'4' => round(PlLedger::getTotal('ExParcel', $consol->id, 'dc', 3) * $consol->exrate),
					'5' => round(PlLedger::getTotal('ExParcel', $consol->id, 'cr', 3) * $consol->exrate),
					'6' => round($consol->totShipments() * ExChannel::getDuty($consol->poc, $consol->etd)),
					'9' => 0,
				);
				// exconsol cost bill
				$data[$consol->id]['bill']['id'] = $bill->id;
				$eccosts = ExconsolCost::model()->findAll('consol_id = :consol_id AND billing_id = :billing_id', array(':consol_id' => $consol->id, ':billing_id' => $bill->id));
				foreach ($eccosts as $eccost) {
					$data[$consol->id]['bill'][$eccost->type] = $eccost->amount;
				}
				foreach (ExconsolCost::$types as $index => $type) {
					if (!isset($data[$consol->id]['bill'][$index])) {
						$data[$consol->id]['bill'][$index] = 0;
					}
				}
				// history
				if (!empty($_GET['others'])) {
					$hists = ExconsolCost::model()->findAll('consol_id = :consol_id AND billing_id != :billing_id', array(':consol_id' => $consol->id, ':billing_id' => $bill->id));
					if (!empty($hists)) {
						foreach ($hists as $hist) {
							$data[$consol->id]['hists'][$hist->billing_id][$hist->type] = $hist->amount;
						}
						foreach ($data[$consol->id]['hists'] as $bill_id => $item) {
							$hist_bill = Billing::model()->findByPk($bill_id);
							$data[$consol->id]['hists'][$bill_id]['no'] = $hist_bill->no;
							$data[$consol->id]['hists'][$bill_id]['cref'] = $hist_bill->billing_cref;
							foreach (ExconsolCost::$types as $index => $type) {
								if (!isset($data[$consol->id]['hists'][$bill_id][$index])) {
									$data[$consol->id]['hists'][$bill_id][$index] = 0;
								}
							}
						}
					}
				}
			}
			$this->render('detail', array('bill' => $bill, 'data' => $data, 'others' => !empty($_GET['others'])));
		} else if ($type == 'consol') {
			$exconsol = ExcoConsol::model()->findByPk($id);
			$bills = ExconsolCost::model()->findAll(array('select' => 'distinct billing_id', 'condition' => 'consol_id = :consol_id', 'params' => array(':consol_id' => $id)));
			$data = array();
			foreach ($bills as $bill_id) {
				// bill
				$bill = Billing::model()->findByPk($bill_id->billing_id);
				$data[$bill->id]['bill'] = $bill;
				// consols
				$consols = ExconsolCost::model()->findAll(array('select' => 'distinct consol_id', 'condition' => 'billing_id = :billing_id AND consol_id = :consol_id', 'params' => array(':billing_id' => $bill->id, ':consol_id' => $exconsol->id)));
				if (!empty($_GET['others'])) {
					$consols = array_merge($consols, ExconsolCost::model()->findAll(array('select' => 'distinct consol_id', 'condition' => 'billing_id = :billing_id AND consol_id != :consol_id', 'params' => array(':billing_id' => $bill->id, ':consol_id' => $exconsol->id))));
				}
				foreach ($consols as $consol_id) {
					// consol
					$consol = ExcoConsol::model()->findByPk($consol_id->consol_id);
					$data[$bill->id]['consols'][$consol->id]['consol'] = array(
						'id' => $consol->id,
						'no' => $consol->no,
						'awb' => $consol->awb,
						'poc' => ExChannel::getName($consol->poc),
						'etd' => $consol->etd,
						'awb_weight' => isset($consol->mdata['awb_check_wt']) ? $consol->mdata['awb_check_wt'] : 0,
						'qty' => $consol->totShipments(),
						'weight' => $consol->totWeight()
					);
					// accrual cost
					$data[$bill->id]['consols'][$consol->id]['cost'] = array(
						'4' => round(PlLedger::getTotal('ExParcel', $consol->id, 'dc', 3) * $consol->exrate),
						'5' => round(PlLedger::getTotal('ExParcel', $consol->id, 'cr', 3) * $consol->exrate),
						'6' => round($consol->totShipments() * ExChannel::getDuty($consol->poc, $consol->etd)),
						'9' => 0,
					);
					// exconsol cost bill
					$data[$bill->id]['consols'][$consol->id]['bill']['id'] = $bill->id;
					$eccosts = ExconsolCost::model()->findAll('consol_id = :consol_id AND billing_id = :billing_id', array(':consol_id' => $consol->id, ':billing_id' => $bill->id));
					foreach ($eccosts as $eccost) {
						$data[$bill->id]['consols'][$consol->id]['bill'][$eccost->type] = $eccost->amount;
					}
					foreach (ExconsolCost::$types as $index => $type) {
						if (!isset($data[$bill->id]['consols'][$consol->id]['bill'][$index])) {
							$data[$bill->id]['consols'][$consol->id]['bill'][$index] = 0;
						}
					}
				}
			}
			$this->render('detail', array('exconsol' => $exconsol, 'data' => $data, 'others' => !empty($_GET['others'])));
		}
	}

	public function actionExport($id, $type) {
		$xls = new oExcel;
		$i = 1;

		if ($type == 'bill') {
			$bill = Billing::model()->findByPk($id);
			$consols = ExconsolCost::model()->findAll(array('select' => 'distinct consol_id', 'condition' => 'billing_id = :billing_id', 'params' => array(':billing_id' => $id)));
			$data = array();
			foreach ($consols as $consol_id) {
				// consol
				$consol = ExcoConsol::model()->findByPk($consol_id->consol_id);
				$data[$consol->id]['consol'] = array(
					'id' => $consol->id,
					'no' => $consol->no,
					'awb' => $consol->awb,
					'poc' => ExChannel::getName($consol->poc),
					'etd' => $consol->etd,
					'awb_weight' => isset($consol->mdata['awb_check_wt']) ? $consol->mdata['awb_check_wt'] : 0,
					'qty' => $consol->totShipments(),
					'weight' => $consol->totWeight()
				);
				// accrual cost
				$data[$consol->id]['cost'] = array(
					'4' => round(PlLedger::getTotal('ExParcel', $consol->id, 'dc', 3) * $consol->exrate),
					'5' => round(PlLedger::getTotal('ExParcel', $consol->id, 'cr', 3) * $consol->exrate),
					'6' => round($consol->totShipments() * ExChannel::getDuty($consol->poc, $consol->etd)),
					'9' => 0,
				);
				// exconsol cost bill
				$data[$consol->id]['bill']['id'] = $bill->id;
				$eccosts = ExconsolCost::model()->findAll('consol_id = :consol_id AND billing_id = :billing_id', array(':consol_id' => $consol->id, ':billing_id' => $bill->id));
				foreach ($eccosts as $eccost) {
					$data[$consol->id]['bill'][$eccost->type] = $eccost->amount;
				}
				foreach (ExconsolCost::$types as $index => $type) {
					if (!isset($data[$consol->id]['bill'][$index])) {
						$data[$consol->id]['bill'][$index] = 0;
					}
				}
				// history
				$hists = ExconsolCost::model()->findAll('consol_id = :consol_id AND billing_id != :billing_id', array(':consol_id' => $consol->id, ':billing_id' => $bill->id));
				if (!empty($hists)) {
					foreach ($hists as $hist) {
						$data[$consol->id]['hists'][$hist->billing_id][$hist->type] = $hist->amount;
					}
					foreach ($data[$consol->id]['hists'] as $bill_id => $item) {
						$hist_bill = Billing::model()->findByPk($bill_id);
						$data[$consol->id]['hists'][$bill_id]['no'] = $hist_bill->no;
						$data[$consol->id]['hists'][$bill_id]['cref'] = $hist_bill->billing_cref;
						foreach (ExconsolCost::$types as $index => $type) {
							if (!isset($data[$consol->id]['hists'][$bill_id][$index])) {
								$data[$consol->id]['hists'][$bill_id][$index] = 0;
							}
						}
					}
				}
			}

			$xls->setColWidth(array(16,12,12,10,16,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8));
			$xls->setFont('A1:Z2', array('bold' => true));
			$xls->addRow($i++, array('Consol #', 'AWB', 'ETD', 'Channel', 'Invoice #', '', 'Wt./Awb Wt.', '', '', 'Pks', '', '', 'Clearance', '', '', 'Delivery', '', '', 'Duty', '', '', 'Others', '', '', 'Total', ''));
			$xls->addRow($i++, array('', '', '', '', '', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff'));

			$tot = array('wt_accr' => 0, 'wt_awb' => 0, 'wt_act' => 0, 'wt_diff' => 0, 'pks_accr' => 0, 'pks_act' => 0, 'pks_diff' => 0, 'clear_accr' => 0, 'clear_act' => 0, 'clear_diff' => 0, 'delivery_accr' => 0, 'delivery_act' => 0, 'delivery_diff' => 0, 'duty_accr' => 0, 'duty_act' => 0, 'duty_diff' => 0, 'others_accr' => 0, 'others_act' => 0, 'others_diff' => 0, 'total_accr' => 0, 'total_act' => 0, 'total_diff' => 0);
			foreach ($data as $id => $consol) {
				$subtot = array('wt_accr' => 0, 'wt_awb' => 0, 'wt_act' => 0, 'wt_diff' => 0, 'pks_accr' => 0, 'pks_act' => 0, 'pks_diff' => 0, 'clear_accr' => 0, 'clear_act' => 0, 'clear_diff' => 0, 'delivery_accr' => 0, 'delivery_act' => 0, 'delivery_diff' => 0, 'duty_accr' => 0, 'duty_act' => 0, 'duty_diff' => 0, 'others_accr' => 0, 'others_act' => 0, 'others_diff' => 0, 'total_accr' => 0, 'total_act' => 0, 'total_diff' => 0);

				// self
				$xls->addRow($i++, array($consol['consol']['no'], $consol['consol']['awb'], $consol['consol']['etd'], ExChannel::getName($consol['consol']['poc']), $bill->billing_cref . ' / ' . $bill->no, floatval($consol['consol']['weight']) . '/' . floatval($consol['consol']['awb_weight']), floatval($consol['bill'][2]), (floatval($consol['consol']['awb_weight']) - floatval($consol['bill'][2])), intval($consol['consol']['qty']), intval($consol['bill'][1]), (intval($consol['consol']['qty']) - intval($consol['bill'][1])), floatval($consol['cost'][4]), floatval($consol['bill'][4]), (floatval($consol['cost'][4]) - floatval($consol['bill'][4])), floatval($consol['cost'][5]), floatval($consol['bill'][5]), (floatval($consol['cost'][5]) - floatval($consol['bill'][5])), floatval($consol['cost'][6]), floatval($consol['bill'][6]), (floatval($consol['cost'][6]) - floatval($consol['bill'][6])), floatval($consol['cost'][9]), floatval($consol['bill'][9]), (floatval($consol['cost'][9]) - floatval($consol['bill'][9])), (floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9])), (floatval($consol['bill'][4]) + floatval($consol['bill'][5]) + floatval($consol['bill'][6]) + floatval($consol['bill'][9])), (floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9]) - floatval($consol['bill'][4]) - floatval($consol['bill'][5]) - floatval($consol['bill'][6]) - floatval($consol['bill'][9]))));

				$subtot['wt_accr'] += floatval($consol['consol']['weight']);
				$subtot['wt_awb'] += floatval($consol['consol']['awb_weight']);
				$subtot['wt_act'] += floatval($consol['bill'][2]);
				$subtot['wt_diff'] += floatval($consol['consol']['awb_weight']) - floatval($consol['bill'][2]);
				$subtot['pks_accr'] += intval($consol['consol']['qty']);
				$subtot['pks_act'] += intval($consol['bill'][1]);
				$subtot['pks_diff'] += intval($consol['consol']['qty']) - intval($consol['bill'][1]);
				$subtot['clear_accr'] += floatval($consol['cost'][4]);
				$subtot['clear_act'] += floatval($consol['bill'][4]);
				$subtot['clear_diff'] += floatval($consol['cost'][4]) - floatval($consol['bill'][4]);
				$subtot['delivery_accr'] += floatval($consol['cost'][5]);
				$subtot['delivery_act'] += floatval($consol['bill'][5]);
				$subtot['delivery_diff'] += floatval($consol['cost'][5]) - floatval($consol['bill'][5]);
				$subtot['duty_accr'] += floatval($consol['cost'][6]);
				$subtot['duty_act'] += floatval($consol['bill'][6]);
				$subtot['duty_diff'] += floatval($consol['cost'][6]) - floatval($consol['bill'][6]);
				$subtot['others_accr'] += floatval($consol['cost'][9]);
				$subtot['others_act'] += floatval($consol['bill'][9]);
				$subtot['others_diff'] += floatval($consol['cost'][9]) - floatval($consol['bill'][9]);
				$subtot['total_accr'] += floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9]);
				$subtot['total_act'] += floatval($consol['bill'][9]) + floatval($consol['bill'][5]) + floatval($consol['bill'][6]) + floatval($consol['bill'][9]);
				$subtot['total_diff'] += floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9]) - floatval($consol['bill'][9]) - floatval($consol['bill'][5]) - floatval($consol['bill'][6]) - floatval($consol['bill'][9]);

				// hists
				if (!empty($consol['hists'])) {
					foreach ($consol['hists'] as $bill_id => $hist) {
						$xls->addRow($i++, array('', '', '', '', $hist['cref'] . ' / ' . $hist['no'], '-', '-', '-', '-', '-', '-', '-', ($hist[4] ? floatval($hist[4]) : '-'), ($hist[4] ? floatval(-$hist[4]) : '-'), '-', ($hist[5] ? floatval($hist[5]) : '-'), ($hist[5] ? floatval(-$hist[5]) : '-'), '-', ($hist[6] ? floatval($hist[6]) : '-'), ($hist[6] ? floatval(-$hist[6]) : '-'), '-', ($hist[9] ? floatval($hist[9]) : '-'), ($hist[9] ? floatval(-$hist[9]) : '-'), '-', (($hist[4] + $hist[5] + $hist[6] + $hist[9]) ? floatval($hist[4] + $hist[5] + $hist[6] + $hist[9]) : '-'), (($hist[4] + $hist[5] + $hist[6] + $hist[9]) ? floatval(-$hist[4] - $hist[5] - $hist[6] - $hist[9]) : '-')));

						$subtot['clear_act'] += floatval($hist[4]);
						$subtot['clear_diff'] -= floatval($hist[4]);
						$subtot['delivery_act'] += floatval($hist[5]);
						$subtot['delivery_diff'] -= floatval($hist[5]);
						$subtot['duty_act'] += floatval($hist[6]);
						$subtot['duty_diff'] -= floatval($hist[6]);
						$subtot['others_act'] += floatval($hist[9]);
						$subtot['others_diff'] -= floatval($hist[9]);
						$subtot['total_act'] += floatval($hist[4]) + floatval($hist[5]) + floatval($hist[6]) + floatval($hist[9]);
						$subtot['total_diff'] -= floatval($hist[4]) + floatval($hist[5]) + floatval($hist[6]) + floatval($hist[9]);
					}
					$xls->addRow($i++, array('', '', '', '', 'Subtotal', $subtot['wt_accr'] . '/' . $subtot['wt_awb'], $subtot['wt_act'], $subtot['wt_diff'], $subtot['pks_accr'], $subtot['pks_act'], $subtot['pks_diff'], $subtot['clear_accr'], $subtot['clear_act'], $subtot['clear_diff'], $subtot['delivery_accr'], $subtot['delivery_act'], $subtot['delivery_diff'], $subtot['duty_accr'], $subtot['duty_act'], $subtot['duty_diff'], $subtot['others_accr'], $subtot['others_act'], $subtot['others_diff'], $subtot['total_accr'], $subtot['total_act'], $subtot['total_diff']));
				}

				$tot['wt_accr'] += $subtot['wt_accr'];
				$tot['wt_awb'] += $subtot['wt_awb'];
				$tot['wt_act'] += $subtot['wt_act'];
				$tot['wt_diff'] += $subtot['wt_diff'];
				$tot['pks_accr'] += $subtot['pks_accr'];
				$tot['pks_act'] += $subtot['pks_act'];
				$tot['pks_diff'] += $subtot['pks_diff'];
				$tot['clear_accr'] += $subtot['clear_accr'];
				$tot['clear_act'] += $subtot['clear_act'];
				$tot['clear_diff'] += $subtot['clear_diff'];
				$tot['delivery_accr'] += $subtot['delivery_accr'];
				$tot['delivery_act'] += $subtot['delivery_act'];
				$tot['delivery_diff'] += $subtot['delivery_diff'];
				$tot['duty_accr'] += $subtot['duty_accr'];
				$tot['duty_act'] += $subtot['duty_act'];
				$tot['duty_diff'] += $subtot['duty_diff'];
				$tot['others_accr'] += $subtot['others_accr'];
				$tot['others_act'] += $subtot['others_act'];
				$tot['others_diff'] += $subtot['others_diff'];
				$tot['total_accr'] += $subtot['total_accr'];
				$tot['total_act'] += $subtot['total_act'];
				$tot['total_diff'] += $subtot['total_diff'];
			}

			// total
			$xls->addRow($i++, array('', '', '', '', 'Total', $tot['wt_accr'] . '/' . $tot['wt_awb'], $tot['wt_act'], $tot['wt_diff'], $tot['pks_accr'], $tot['pks_act'], $tot['pks_diff'], $tot['clear_accr'], $tot['clear_act'], $tot['clear_diff'], $tot['delivery_accr'], $tot['delivery_act'], $tot['delivery_diff'], $tot['duty_accr'], $tot['duty_act'], $tot['duty_diff'], $tot['others_accr'], $tot['others_act'], $tot['others_diff'], $tot['total_accr'], $tot['total_act'], $tot['total_diff']));

			$xls->output('consol_weight_check_export_'.$bill->no.'_'.time().'.xlsx');
		} else if ($type == 'exconsol') {
			$exconsol = ExcoConsol::model()->findByPk($id);$bills = ExconsolCost::model()->findAll(array('select' => 'distinct billing_id', 'condition' => 'consol_id = :consol_id', 'params' => array(':consol_id' => $id)));
			$data = array();
			foreach ($bills as $bill_id) {
				// bill
				$bill = Billing::model()->findByPk($bill_id->billing_id);
				$data[$bill->id]['bill'] = $bill;
				// consols
				$consols = ExconsolCost::model()->findAll(array('select' => 'distinct consol_id', 'condition' => 'billing_id = :billing_id AND consol_id = :consol_id', 'params' => array(':billing_id' => $bill->id, ':consol_id' => $exconsol->id)));
				$consols = array_merge($consols, ExconsolCost::model()->findAll(array('select' => 'distinct consol_id', 'condition' => 'billing_id = :billing_id AND consol_id != :consol_id', 'params' => array(':billing_id' => $bill->id, ':consol_id' => $exconsol->id))));
				foreach ($consols as $consol_id) {
					// consol
					$consol = ExcoConsol::model()->findByPk($consol_id->consol_id);
					$data[$bill->id]['consols'][$consol->id]['consol'] = array(
						'id' => $consol->id,
						'no' => $consol->no,
						'awb' => $consol->awb,
						'poc' => ExChannel::getName($consol->poc),
						'etd' => $consol->etd,
						'awb_weight' => isset($consol->mdata['awb_check_wt']) ? $consol->mdata['awb_check_wt'] : 0,
						'qty' => $consol->totShipments(),
						'weight' => $consol->totWeight()
					);
					// accrual cost
					$data[$bill->id]['consols'][$consol->id]['cost'] = array(
						'4' => round(PlLedger::getTotal('ExParcel', $consol->id, 'dc', 3) * $consol->exrate),
						'5' => round(PlLedger::getTotal('ExParcel', $consol->id, 'cr', 3) * $consol->exrate),
						'6' => round($consol->totShipments() * ExChannel::getDuty($consol->poc, $consol->etd)),
						'9' => 0,
					);
					// exconsol cost bill
					$data[$bill->id]['consols'][$consol->id]['bill']['id'] = $bill->id;
					$eccosts = ExconsolCost::model()->findAll('consol_id = :consol_id AND billing_id = :billing_id', array(':consol_id' => $consol->id, ':billing_id' => $bill->id));
					foreach ($eccosts as $eccost) {
						$data[$bill->id]['consols'][$consol->id]['bill'][$eccost->type] = $eccost->amount;
					}
					foreach (ExconsolCost::$types as $index => $type) {
						if (!isset($data[$bill->id]['consols'][$consol->id]['bill'][$index])) {
							$data[$bill->id]['consols'][$consol->id]['bill'][$index] = 0;
						}
					}
				}
			}

			$xls->setColWidth(array(16,16,12,12,10,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8));
			$xls->setFont('A1:Z2', array('bold' => true));
			$xls->addRow($i++, array('Invoice #', 'Consol #', 'AWB', 'ETD', 'Channel', '', 'Wt./Awb Wt.', '', '', 'Pks', '', '', 'Clearance', '', '', 'Delivery', '', '', 'Duty', '', '', 'Others', '', '', 'Total', ''));
			$xls->addRow($i++, array('', '', '', '', '', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff', 'ACCR', 'ACT', 'Diff'));

			$tot = array('wt_accr' => 0, 'wt_awb' => 0, 'wt_act' => 0, 'wt_diff' => 0, 'pks_accr' => 0, 'pks_act' => 0, 'pks_diff' => 0, 'clear_accr' => 0, 'clear_act' => 0, 'clear_diff' => 0, 'delivery_accr' => 0, 'delivery_act' => 0, 'delivery_diff' => 0, 'duty_accr' => 0, 'duty_act' => 0, 'duty_diff' => 0, 'others_accr' => 0, 'others_act' => 0, 'others_diff' => 0, 'total_accr' => 0, 'total_act' => 0, 'total_diff' => 0);
			$k = 0;
			foreach ($data as $bill_id => $bill) {
				$k ++;
				$subtot = array('wt_accr' => 0, 'wt_awb' => 0, 'wt_act' => 0, 'wt_diff' => 0, 'pks_accr' => 0, 'pks_act' => 0, 'pks_diff' => 0, 'clear_accr' => 0, 'clear_act' => 0, 'clear_diff' => 0, 'delivery_accr' => 0, 'delivery_act' => 0, 'delivery_diff' => 0, 'duty_accr' => 0, 'duty_act' => 0, 'duty_diff' => 0, 'others_accr' => 0, 'others_act' => 0, 'others_diff' => 0, 'total_accr' => 0, 'total_act' => 0, 'total_diff' => 0);
				foreach ($bill['consols'] as $id => $consol) {
					// self
					$xls->addRow($i++, array((($consol['consol']['no'] == $exconsol->no) ? $bill['bill']->billing_cref . ' / ' . $bill['bill']->no : '-'), $consol['consol']['no'], $consol['consol']['awb'], $consol['consol']['etd'], ExChannel::getName($consol['consol']['poc']), floatval($consol['consol']['weight']) . '/' . floatval($consol['consol']['awb_weight']), floatval($consol['bill'][2]), (floatval($consol['consol']['awb_weight']) - floatval($consol['bill'][2])), intval($consol['consol']['qty']), intval($consol['bill'][1]), (intval($consol['consol']['qty']) - intval($consol['bill'][1])), floatval($consol['cost'][4]), floatval($consol['bill'][4]), (floatval($consol['cost'][4]) - floatval($consol['bill'][4])), floatval($consol['cost'][5]), floatval($consol['bill'][5]), (floatval($consol['cost'][5]) - floatval($consol['bill'][5])), floatval($consol['cost'][6]), floatval($consol['bill'][6]), (floatval($consol['cost'][6]) - floatval($consol['bill'][6])), floatval($consol['cost'][9]), floatval($consol['bill'][9]), (floatval($consol['cost'][9]) - floatval($consol['bill'][9])), (floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9])), (floatval($consol['bill'][4]) + floatval($consol['bill'][5]) + floatval($consol['bill'][6]) + floatval($consol['bill'][9])), (floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9]) - floatval($consol['bill'][4]) - floatval($consol['bill'][5]) - floatval($consol['bill'][6]) - floatval($consol['bill'][9]))));

					$subtot['wt_accr'] += ($k == 1) ? floatval($consol['consol']['weight']) : 0;
					$subtot['wt_awb'] += ($k == 1) ? floatval($consol['consol']['awb_weight']) : 0;
					$subtot['wt_act'] += floatval($consol['bill'][2]);
					$subtot['wt_diff'] += ($k == 1) ? floatval($consol['consol']['awb_weight']) - floatval($consol['bill'][2]) : floatval(-$consol['bill'][2]);
					$subtot['pks_accr'] += ($k == 1) ? intval($consol['consol']['qty']) : 0;
					$subtot['pks_act'] += intval($consol['bill'][1]);
					$subtot['pks_diff'] += ($k == 1) ? intval($consol['consol']['qty']) - intval($consol['bill'][1]) : intval(-$consol['bill'][1]);
					$subtot['clear_accr'] += ($k == 1) ? floatval($consol['cost'][4]) : 0;
					$subtot['clear_act'] += floatval($consol['bill'][4]);
					$subtot['clear_diff'] += ($k == 1) ? floatval($consol['cost'][4]) - floatval($consol['bill'][4]) : intval(-$consol['bill'][4]);
					$subtot['delivery_accr'] += ($k == 1) ? floatval($consol['cost'][5]) : 0;
					$subtot['delivery_act'] += floatval($consol['bill'][5]);
					$subtot['delivery_diff'] += ($k == 1) ? floatval($consol['cost'][5]) - floatval($consol['bill'][5]) : floatval(-$consol['bill'][5]);
					$subtot['duty_accr'] += ($k == 1) ? floatval($consol['cost'][6]) : 0;
					$subtot['duty_act'] += floatval($consol['bill'][6]);
					$subtot['duty_diff'] += ($k == 1) ? floatval($consol['cost'][6]) - floatval($consol['bill'][6]) : floatval(-$consol['bill'][6]);
					$subtot['others_accr'] += ($k == 1) ? floatval($consol['cost'][9]) : 0;
					$subtot['others_act'] += floatval($consol['bill'][9]);
					$subtot['others_diff'] += ($k == 1) ? floatval($consol['cost'][9]) - floatval($consol['bill'][9]) : floatval(-$consol['bill'][9]);
					$subtot['total_accr'] += ($k == 1) ? (floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9])) : 0;
					$subtot['total_act'] += floatval($consol['bill'][4]) + floatval($consol['bill'][5]) + floatval($consol['bill'][6]) + floatval($consol['bill'][9]);
					$subtot['total_diff'] += ($k == 1) ? (floatval($consol['cost'][4]) + floatval($consol['cost'][5]) + floatval($consol['cost'][6]) + floatval($consol['cost'][9]) - floatval($consol['bill'][4]) - floatval($consol['bill'][5]) - floatval($consol['bill'][6]) - floatval($consol['bill'][9])) : (floatval(-$consol['bill'][4]) + floatval(-$consol['bill'][5]) + floatval(-$consol['bill'][6]) + floatval(-$consol['bill'][9]));
				}

				// other
				$xls->addRow($i++, array('', '', '', '', 'Subtotal', $subtot['wt_accr'] . '/' . $subtot['wt_awb'], $subtot['wt_act'], $subtot['wt_diff'], $subtot['pks_accr'], $subtot['pks_act'], $subtot['pks_diff'], $subtot['clear_accr'], $subtot['clear_act'], $subtot['clear_diff'], $subtot['delivery_accr'], $subtot['delivery_act'], $subtot['delivery_diff'], $subtot['duty_accr'], $subtot['duty_act'], $subtot['duty_diff'], $subtot['others_accr'], $subtot['others_act'], $subtot['others_diff'], $subtot['total_accr'], $subtot['total_act'], $subtot['total_diff']));

				$tot['wt_accr'] += $subtot['wt_accr'];
				$tot['wt_awb'] += $subtot['wt_awb'];
				$tot['wt_act'] += $subtot['wt_act'];
				$tot['wt_diff'] += $subtot['wt_diff'];
				$tot['pks_accr'] += $subtot['pks_accr'];
				$tot['pks_act'] += $subtot['pks_act'];
				$tot['pks_diff'] += $subtot['pks_diff'];
				$tot['clear_accr'] += $subtot['clear_accr'];
				$tot['clear_act'] += $subtot['clear_act'];
				$tot['clear_diff'] += $subtot['clear_diff'];
				$tot['delivery_accr'] += $subtot['delivery_accr'];
				$tot['delivery_act'] += $subtot['delivery_act'];
				$tot['delivery_diff'] += $subtot['delivery_diff'];
				$tot['duty_accr'] += $subtot['duty_accr'];
				$tot['duty_act'] += $subtot['duty_act'];
				$tot['duty_diff'] += $subtot['duty_diff'];
				$tot['others_accr'] += $subtot['others_accr'];
				$tot['others_act'] += $subtot['others_act'];
				$tot['others_diff'] += $subtot['others_diff'];
				$tot['total_accr'] += $subtot['total_accr'];
				$tot['total_act'] += $subtot['total_act'];
				$tot['total_diff'] += $subtot['total_diff'];
			}

			// total
			$xls->addRow($i++, array('', '', '', '', 'Total', $tot['wt_accr'] . '/' . $tot['wt_awb'], $tot['wt_act'], $tot['wt_diff'], $tot['pks_accr'], $tot['pks_act'], $tot['pks_diff'], $tot['clear_accr'], $tot['clear_act'], $tot['clear_diff'], $tot['delivery_accr'], $tot['delivery_act'], $tot['delivery_diff'], $tot['duty_accr'], $tot['duty_act'], $tot['duty_diff'], $tot['others_accr'], $tot['others_act'], $tot['others_diff'], $tot['total_accr'], $tot['total_act'], $tot['total_diff']));

			$xls->output('consol_weight_check_export_'.$exconsol->no.'_'.time().'.xlsx');
		}
	}

	private function saveCost($cost){
		$channel = $cost->channel;
		$org = Org::model()->find('code = :code',[':code' => $channel]);
		$oid = empty($org) ? 0 : $org->id;

		foreach ( $cost->lines as $line ) {
			$consoleNo = $line->consol_id;
			$clearCost = $line->clearance_cost;
			$deliveryCost = $line->delivery_cost;
			$duty = $line->duty;
			$others = $line->others;
			$exConsol = ExcoConsol::model()->find('no = :no' ,[':no' => $consoleNo] ) ;

			if ( !empty($exConsol) ) {
				if ( $clearCost > 0 ) {

					// save as billing
					$billing = BillingLine::model()->find('org_id = :oid AND billing_ref = :bref AND type = 2 AND item_code = :icode',[
						':oid' => $oid , ':icode' => 'channel_clear_cost',':bref' => $exConsol->no
					]);
					if ( empty($billing) ) {
						$billing = new BillingLine();
						$billing->org_id = $oid;
						$billing->billing_ref = $exConsol->no;
						$billing->item_code = 'channel_clear_cost';
						$billing->link_id = $exConsol->id;

						$billing->currency = 3;
						$billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
						$billing->dpmt = Invoice::DPMT_EXPORT;
						$billing->type = BillingLine::BILLING_TYPE_EXPORT;
						$billing->desc = 'channel_clear_cost';
						$billing->gst = 'EXEMPTEXPENSES';
						$billing->price = 0;
						$billing->qty = 1;

						// get related supplier cost gl code
						$billing->charge_code = '91002';

						$billing->op_id =  isset(Yii::app()->user) ? Yii::app()->user->id : 0;

					}
					// get related supplier cost gl code
					$billing->charge_code = '91002';
					$billing->actual_amount = $clearCost;
					$billing->save();

					/*PlLedger::add([
						'fid' => $exConsol->id,
						'model' => 'ExcoConsol',
						'org_id' => $oid,
						'dpt_id' => Org::PCAE_DEPARTMENT_SYDNEY,
						'dpmt' => Invoice::DPMT_EXPORT,
						'lid' => 0,
						'grp1' => $exConsol->id,
						'grp2' => 'clear_cost',
						'gl' => 3, // for EXPORT SMALL PARCEL - PORT & TERMINAL COST
						'date' => $cost->created,
						'amt' => $clearCost,
						'actual_amt' => $clearCost,
						'gst' => 0,
						'acc' => 1,
					], true);*/
				}

				if ( $deliveryCost > 0 ) {

					// save as billing
					$billing = BillingLine::model()->find('org_id = :oid AND billing_ref = :bref AND type = 2 AND item_code = :icode',[
						':oid' => $oid , ':icode' => 'channel_delivery_cost',':bref' => $exConsol->no
					]);
					if ( empty($billing) ) {
						$billing = new BillingLine();
						$billing->org_id = $oid;
						$billing->billing_ref = $exConsol->no;
						$billing->item_code = 'channel_delivery_cost';
						$billing->link_id = $exConsol->id;

						$billing->currency = 3;
						$billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
						$billing->dpmt = Invoice::DPMT_EXPORT;
						$billing->type = BillingLine::BILLING_TYPE_EXPORT;
						$billing->desc = 'channel_delivery_cost';
						$billing->gst = 'EXEMPTEXPENSES';
						$billing->price = 0;
						$billing->qty = 1;

						// get related supplier cost gl code
						$billing->charge_code = '91002';

						$billing->op_id =  isset(Yii::app()->user) ? Yii::app()->user->id : 0;

					}
					// get related supplier cost gl code
					$billing->charge_code = '91002';
					$billing->actual_amount = $deliveryCost;
					$billing->save();

					/*PlLedger::add([
						'fid' => $exConsol->id,
						'model' => 'ExcoConsol',
						'org_id' => $oid,
						'dpt_id' => Org::PCAE_DEPARTMENT_SYDNEY,
						'dpmt' => Invoice::DPMT_EXPORT,
						'lid' => 0,
						'grp1' => $exConsol->id,
						'grp2' => 'delivery_cost',
						'gl' => 3, // for EXPORT SMALL PARCEL - PORT & TERMINAL COST
						'date' =>  $cost->created,
						'amt' => $deliveryCost,
						'actual_amt' => $deliveryCost,
						'gst' => 0,
						'acc' => 1,
					], true);*/
				}

				if ( $duty > 0 ) {

					// save as billing
					$billing = BillingLine::model()->find('org_id = :oid AND billing_ref = :bref AND type = 2 AND item_code = :icode',[
						':oid' => $oid , ':icode' => 'channel_duty_cost',':bref' => $exConsol->no
					]);
					if ( empty($billing) ) {
						$billing = new BillingLine();
						$billing->org_id = $oid;
						$billing->billing_ref = $exConsol->no;
						$billing->item_code = 'channel_duty_cost';
						$billing->link_id = $exConsol->id;

						$billing->currency = 3;
						$billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
						$billing->dpmt = Invoice::DPMT_EXPORT;
						$billing->type = BillingLine::BILLING_TYPE_EXPORT;
						$billing->desc = 'channel_duty_cost';
						$billing->gst = 'EXEMPTEXPENSES';
						$billing->price = 0;
						$billing->qty = 1;

						// get related supplier cost gl code
						$billing->charge_code = '91002';

						$billing->op_id =  isset(Yii::app()->user) ? Yii::app()->user->id : 0;

					}
					// get related supplier cost gl code
					$billing->charge_code = '91002';
					$billing->actual_amount = $duty;
					$billing->save();

					/*PlLedger::add([
						'fid' => $exConsol->id,
						'model' => 'ExcoConsol',
						'org_id' => $oid,
						'dpt_id' => Org::PCAE_DEPARTMENT_SYDNEY,
						'dpmt' => Invoice::DPMT_EXPORT,
						'lid' => 0,
						'grp1' => $exConsol->id,
						'grp2' => 'duty_cost',
						'gl' => 3, // for EXPORT SMALL PARCEL - PORT & TERMINAL COST
						'date' =>  $cost->created,
						'amt' => $duty,
						'actual_amt' => $duty,
						'gst' => 0,
						'acc' => 1,
					], true);*/
				}

				if ( $others > 0 ) {

					// save as billing
					$billing = BillingLine::model()->find('org_id = :oid AND billing_ref = :bref AND type = 2 AND item_code = :icode',[
						':oid' => $oid , ':icode' => 'channel_others_cost',':bref' => $exConsol->no
					]);
					if ( empty($billing) ) {
						$billing = new BillingLine();
						$billing->org_id = $oid;
						$billing->billing_ref = $exConsol->no;
						$billing->item_code = 'channel_others_cost';
						$billing->link_id = $exConsol->id;

						$billing->currency = 3;
						$billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
						$billing->dpmt = Invoice::DPMT_EXPORT;
						$billing->type = BillingLine::BILLING_TYPE_EXPORT;
						$billing->desc = 'channel_others_cost';
						$billing->gst = 'EXEMPTEXPENSES';
						$billing->price = 0;
						$billing->qty = 1;

						// get related supplier cost gl code
						$billing->charge_code = '91002';

						$billing->op_id =  isset(Yii::app()->user) ? Yii::app()->user->id : 0;

					}
					// get related supplier cost gl code
					$billing->charge_code = '91002';
					$billing->actual_amount = $others;
					$billing->save();

					/*PlLedger::add([
						'fid' => $exConsol->id,
						'model' => 'ExcoConsol',
						'org_id' => $oid,
						'dpt_id' => Org::PCAE_DEPARTMENT_SYDNEY,
						'dpmt' => Invoice::DPMT_EXPORT,
						'lid' => 0,
						'grp1' => $exConsol->id,
						'grp2' => 'others_cost',
						'gl' => 3, // for EXPORT SMALL PARCEL - PORT & TERMINAL COST
						'date' =>  $cost->created,
						'amt' => $others,
						'actual_amt' => $others,
						'gst' => 0,
						'acc' => 1,
					], true);*/
				}

			}
		}
	}
	/**
	 * @param $model
	 */
	private function syncBilling2Xero($model){

		// currently do not sync with xero
		return;

		$owner_id = empty($_POST['ConsolWeightCheck']['owner_id']) ? 0 : $_POST['ConsolWeightCheck']['owner_id'] ;
		$org = Org::model()->findByPk($owner_id);
		if (empty($org) ) return false;

		// get all lines and group by invoice ref
		$lines = array();
		$costLines = ConsolWeightCheckLine::model()->findAll('parent_id = :pid' , [':pid' => $model->id]);
		foreach ( $costLines as $line ) {
			if ( !isset($lines[$line->invoice_ref]) ) {
				$lines[$line->invoice_ref] = array();
			}
			$lines[$line->invoice_ref][] = $line;
		}

		$billingIndex = 1;
		$syncXero = true;
		foreach ( $lines as $k => $billingLines ) {
			$xdata = new stdClass();
			// get invoice number
			$xdata->no =  $model->invoice_ref;  //'CC'.$model->id . sprintf('%03d',$billingIndex++);
			$xdata->currency = 'RMB'; // default as AUD currently
			$xdata->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;

			$xdata->orgName = $org->name;
			$xdata->orgId = $org->id;

			// get invoice data and due
			$xdata->date = $model->date;  //date('Y-m-d');
			$xdata->due = $model->due; //date('Y-m-d',strtotime('+7 days'));
			$xdata->gstType = 1; // Exclusive in Xero

			$costs = array();
			foreach ( $billingLines as $cost ) {
				$amount = floatval($cost->clearance_cost) + floatval($cost->delivery_cost) + floatval($cost->duty) + floatval($cost->others);
				$amount = number_format($amount,2,'.','');
				$costs[] = array('qty' => 1,
					'description' => 'Consol#' . $cost->consol_id . ' awb#' . $cost->awb_no . ' inv#' . $cost->invoice_ref . ' awbWeight#' . $cost->awb_weight,
					'code' => '91002', // EXPORT SMALL PARCEL - PORT & TERMINAL COST
					'amount' =>  $amount,
					'taxType' => 'EXEMPTEXPENSES', // GST Free Expense
				);
			}

			// save to xero now
			if ( !empty($costs) ) {
				$xdata->lines = $costs;
				if ( !Invoice::saveBill2Xero($xdata) )  $syncXero = false;
			}
		}

		// sync to xero done successfully
		if ( $syncXero ) {
			$model->mdata['sync_xero'] = 1;
			$model->updateMeta();
			return true;
		}

		return false;

	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model = ConsolWeightCheck::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

}
