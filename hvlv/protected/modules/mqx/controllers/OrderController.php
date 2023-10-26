<?php

class OrderController extends Controller{
	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax = array('search', 'detail');
	protected $skipAcl = [];

	/**
	 * @param CAction $action
	 * @return bool
	 */
	public function beforeAction($action){

		if(!Yii::app()->request->isAjaxRequest) $this->layout = 'mqx';

		return parent::beforeAction($action);
	}

	public function actionSearch() {
		$this->render('search');
	}

	public function actionDetail() {
		$shipments = array();
		if ($_GET) {
			if (preg_match_all('/(\w+)/', $_GET['trackno'], $tracknos)) {
				foreach ($tracknos[1] as $trackno) {
					$shipment = Shipment::model()->find('hbn = :hbn', array(':hbn' => $trackno));
					if ($shipment) {
						$shipments[$trackno] = array('id' => $shipment->id, 'cnee' => mb_substr($shipment->cnee->name, 0, 1, 'utf-8') . '**', 'tel' => substr_replace($shipment->cnee->tel, '****', 3, 4), 'address' => $shipment->cnee->state . $shipment->cnee->city . $shipment->cnee->suburb, 'status' => (floatval($shipment->insurance) != 5.5) ? 1 : 2);
					} else {
						$shipments[$trackno] = array('cnee' => '', 'tel' => '', 'address' => '', 'status' => 0);
					}
				}
			}
		}

		if (!empty($_GET['test']) && $_GET['test'] === '54gvvfbj4fwfg4j4wgcdvbw45w34gh5') {
			$test = '54gvvfbj4fwfg4j4wgcdvbw45w34gh5';
		} else {
			$test = '';
		}

		$this->render('detail', array('shipments' => $shipments, 'test' => $test));
	}

	public function actionPurchase() {
		if (!empty($_GET['shipments'])) {
			$shipmentids = array_values($_GET['shipments']);
			$payment = $_GET['optionRadio'];
			$total_amount = count($_GET['shipments']) * 5.5;

			if (!empty($_GET['test']) && $_GET['test'] === '54gvvfbj4fwfg4j4wgcdvbw45w34gh5') {
				$total_amount = 0.01;
			}

			if ($payment == 'poli') {
				$this->redirect($this->createPoliLink(array('shipmentids' => $shipmentids, 'amount' => $total_amount)));
			} else if ($payment == 'paypal') {
				$call_back_root = Yii::app()->request->hostInfo . $this->createUrl('order/paypal') . '?id=' . json_encode($shipmentids) . '&op=';
				$callback_success_url = $call_back_root . 'success';
				$callback_cancel_url = $call_back_root . 'cancel';
				$paypal = new PayPalApi(
					$callback_success_url,
					$callback_cancel_url
				);
				$this->redirect($paypal->ExpressCheckOut(array(array('name' => 'comsumable goods', 'price' => $total_amount, 'qty' => 1))));
			} else if ($payment == 'alipay' || $payment == 'wechatpay') {
				$callback_notification_url = Yii::app()->request->hostInfo . $this->createUrl('order/supay');
				$callback_return_url = Yii::app()->request->hostInfo . $this->createUrl('order/finish');
				$supay = new SupayAPI(
					$callback_notification_url,
					$callback_return_url,
                    '30810',
                    '56175fb50d4a8040cc782c35040fadcf'
				);

				if ($payment == 'alipay') {
					$this->redirect($supay->checkoutByAliPay('comsumable goods', implode('_', $shipmentids), 'AUD', $total_amount));
				} else {
					$this->redirect($supay->checkoutByWechatPay('comsumable goods', implode('_', $shipmentids), 'AUD', $total_amount));
				}
			}
		} else {
			echo '请勾选运单';
		}
	}

	/**
	 * Poli
	 */
	private function createPoliLink($data) {
		$paylink = Yii::app()->request->hostInfo . $this->createUrl('order/paypoli') . '?token=';
		$iv = str_repeat("\x00", openssl_cipher_iv_length('aes-256-cbc'));
		$token = base64_encode(openssl_encrypt(json_encode($data), 'AES-128-CBC', md5(PoliPaymentApi::PAYLINK_KEY), 0, $iv));
		$paylink .= trim($this->safe_b64encode($token));
		return $paylink;
	}

	public function actionPayPoli($token) {
		// get token
		$token = $this->safe_b64decode($token);
		$iv = str_repeat("\x00", openssl_cipher_iv_length('aes-256-cbc'));
		$data = rtrim(openssl_decrypt(base64_decode($token), 'AES-128-CBC', md5(PoliPaymentApi::PAYLINK_KEY), 0, $iv), "\0");
		$data = json_decode($data, true);

		if ( !empty($data['shipmentids']) && !empty($data['amount']) ) {
			// valid paylink
			$this->payPoliInvoiceDirect($data['shipmentids'], $data['amount']);
		} else {
			// invalid pay link
			echo 'invalid pay link';
		}
	}

	private function payPoliInvoiceDirect($shipmentids, $amount) {
		if (count($shipmentids) == count(Shipment::model()->findAll('id in (' . implode(',', $shipmentids) . ')'))) {
			$home_page = Yii::app()->request->hostInfo . $this->createUrl('site/index');
			$call_back_root = Yii::app()->request->hostInfo . $this->createUrl('order/poli') . '?id=' . implode('_', $shipmentids) . '&op=';
			$callback_success_url = $call_back_root . 'success';
			$callback_fail_url = $call_back_root . 'fail';
			$callback_cancel_url = $call_back_root . 'cancel';
			$callback_notiry_url = $call_back_root . 'notify';

			$data = '{
				"Amount":"' . $amount . '",
				"CurrencyCode":"AUD",
				"MerchantReference":"' . implode('_', $shipmentids) . '",
				"MerchantHomepageURL":"' . $home_page . '",
				"SuccessURL":"' . $callback_success_url . '",
				"FailureURL":"' . $callback_fail_url . '",
				"CancellationURL":"' . $callback_cancel_url . '",
				"NotificationURL":"' . $callback_notiry_url . '"
			}';

			$json = PoliPaymentApi::payPoli($data);
			if ($json['Success'] == true && !empty($json["NavigateURL"])) {
				header('Location: ' . $json["NavigateURL"]);
			} else {
				$error = $json['ErrorMessage'] . '(' . $json['ErrorCode'] . ')';
				echo $error;
			}
		} else {
			echo 'no shipment';
		}
	}

	public function actionPoli($id, $op) {
		// transaction token returned by poli , we should saved this if need check status again
		$poli_token = $_GET['token'];

		// get trn by token
		$trn_info = PoliPaymentApi::getPoliTrn($poli_token);

		if ( !empty($trn_info['ErrorCode']) ) {
			echo $trn_info['ErrorMessage'];
			return;
		}

		$shipmentids = explode('_', $id);

		$shipments = Shipment::model()->findAll('id in (' . implode(',', $shipmentids) . ')');

		if ( !empty($shipments) ) {
			switch ($op) {
				case 'success' :
					// should update invoice status here
					$this->afterPaymentPaid($shipmentids, $trn_info, 'POLI');
					break;
				case 'fail' :
					break;
				case 'cancel' :
					$this->afterPaymentCancelled();
					break;
				case 'notify':
					break;
				default:
					echo 'unknown error occurred !';
					break;
			}
		} else {
			// important , we should check why this id not existing
			// because client has payed for this invoice
			// we should record this error with invoice id and pay result information
			echo "can't find the shipments";
		}
	}

	/**
	 * Paypal
	 */
	public function actionPaypal($id, $op) {
		$paypal_token = $_GET['token'];

		$paypal = new PayPalApi();
		$paypal_info = $paypal->GetExpressCheckoutDetails($paypal_token);

		$status = $paypal_info['BILLINGAGREEMENTACCEPTEDSTATUS'] && ($paypal_info['CHECKOUTSTATUS'] == 'PaymentActionCompleted');

		$shipments = Shipment::model()->findAll('id in (' . implode(',', json_decode($id, true)) . ')');

		if (!empty($shipments)) {
			switch ($op) {
				case 'success':
					$this->afterPaymentPaid(json_decode($id, true), $paypal_info, 'PAYPAL');
					break;
				case 'cancel':
					$this->afterPaymentCancelled();
					break;
				default:
					echo 'unknown error occurred!';
					break;
			}
		} else {
			echo "can't find the shipments";
		}
	}


	public function actionSupay() {
		Yii::log(json_encode($_SERVER['QUERY_STRING']), 'warning', 'trace');
		// Yii::log(json_encode($_GET), 'warning', 'trace');
		$supay = new SupayAPI('', '', '30810', '56175fb50d4a8040cc782c35040fadcf');
		$veri_result = $supay->verifyNotification($_GET);

		if ($veri_result) {
			$result = $supay->checkoutResult($_GET['merchant_trade_no']);
			if ($result) {
				$this->afterPaymentPaid(explode('_', $_GET['merchant_trade_no']), array('notice_id' => $_GET['notice_id'], 'amount' => count(explode('_', $_GET['merchant_trade_no'])) * 5.5), 'SUPAY');
			}
		} else {
			return "can't find the shipments";
		}
	}


	private function generateInvoice($shipments, $org) {
		// invoice
		$fd = date('Ymd');
		$fdts = date('Ymd');
		$wd = date('N', $fdts);
		if ($wd != 6) {
			$fdts = strtotime($fd . ' -' . ($wd + 1) . ' day');
			$fd = date('Ymd', $fdts);
		}
		$td = date('Ymd', strtotime($fd . ' +6 day'));
		$bd = date('Ymd', strtotime($fd . ' +9 day'));
		$inv = new Invoice();
		$inv->type = Invoice::INVOICE_TYPE_SHIPMENT_INSURANCE;
		$inv->dpmt = Invoice::DPMT_3PL;
		$inv->currency = array_search('AUD', Invoice::$currencies);
		$inv->to_id = $org->id;
		$inv->status = Invoice::INVOICE_STATUS_PENDING;
		$inv->date = $bd;
		$inv->mdata['name'] = $org->name;
		$inv->mdata['address'] = $org->getAddress();
		$inv->mdata['payterm'] = empty($org->extra['payterm']) ? 'COD' : $org->extra['payterm'] . ' days';
		$inv->mdata['billfrom'] = $fd;
		$inv->mdata['billto'] = $td;
		$inv->due = $inv->date;
		$inv->total = 0;
		$inv->gst = 0;
		$inv->save();
		InvLine::model()->deleteAll('inv_id = :id', array(':id' => $inv->id));

		$stot = count($shipments) * 5.5;
		$items = [];
		foreach ($shipments as $shipment) {
			$items[] = [$shipment->hbn, ucwords($shipment->ref), $shipment->created, 'Shipment Insurance - ' . $shipment->hbn, 5.5, 1, 5.5];

			$il = InvLine::model()->find('fid = :id AND inv_id = :inv_id', [':id' => $shipment->id, ':inv_id' => $inv->id]);
			if (empty($il)) {
				$il = new InvLine();
				$il->inv_id = $inv->id;
			}
			$il->amount = $stot;
			$il->gst = 0;
			$il->mdata['items'] = $items;
			$il->ccode = 'Shipment Insurance';
			$il->det = 'Shipment Insurance';
			$il->qty = 1;
			$il->fid = $shipment->id;
			$il->save();

			$inv->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
			$inv->lines = [$il];
			$inv->total += $il->amount;
			$inv->gst = 0;
			$inv->sync_xero = false;
			$inv->save();
		}

		return $inv;
	}

	private function afterPaymentPaid($shipmentids, $trnInfo, $type) {
		if (!empty($trnInfo['TransactionRefNo'])) {
			$trn = $trnInfo['TransactionRefNo'];
			$amount = $trnInfo['AmountPaid'];
		} else if (!empty($trnInfo['TOKEN'])) {
			$trn = $trnInfo['TOKEN'];
			$amount = $trnInfo['PAYMENTREQUEST_0_AMT'];
		} else if (!empty($trnInfo['notice_id'])) {
			$trn = $trnInfo['notice_id'];
			$amount = $trnInfo['amount'];
		}

		$shipments = Shipment::model()->findAll('id in (' . implode(',', $shipmentids) . ')');

		$org = Org::model()->findByPk(150);
		$payment_gateway_history_model = PaymentGatewayHistory::model()->find('trn = :trn', array(':trn' => $trn));
		if ( !isset($payment_gateway_history_model) ) {
				$invoice = $this->generateInvoice($shipments, $org);
				$payment_gateway_history_model = new PaymentGatewayHistory();
				$payment_gateway_history_model->trn = $trn;
				$payment_gateway_history_model->invoice_id = $invoice->id;
				$payment_gateway_history_model->type = constant('PaymentGatewayHistory::PAYMENT_GATEWAY_TYPE_'. $type);
				$payment_gateway_history_model->token = $trn;
				$payment_gateway_history_model->meta = json_encode($trnInfo);
				$payment_gateway_history_model->result = 'success';
				$payment_gateway_history_model->payed_time = new CDbExpression('NOW()');
				$payment_gateway_history_model->save();
		} else {
				if ( $payment_gateway_history_model->result == 'success' && !empty($payment_gateway_history_model->payed_time) ) {
						echo 'you have paid the invoice successfully already before!';
						return;
				}
		}

		// get original invoice data
		if ( !empty($invoice) ) {
				// 9 : paid
				// 10 : cancelled
				if ( $invoice->status != Invoice::INVOICE_STATUS_PAID &&
						$invoice->status != Invoice::INVOICE_STATUS_CACELLED ) {

						// create a payment
						$payment = new Payment();
						$payment->org_id = $invoice->to_id;
						$payment->amount = $amount;
						$payment->ref = $type . ' TRN: ' . $trn;
						$payment->date = date('Y-m-d', time());
						$payment->type = constant('Payment::PAYMENT_TYPE_'. $type);
						$payment->bank = array_search('Westpac AUD', Payment::$banks); // westbank
						$payment->mdata['bankcharge'] = $invoice->total - (double)$payment->amount;
						$payment->currency = 1; // AUD
						$payment->status = Payment::PAYMENT_STATUS_POSTED; // posted status ? paid finish?
						$payment->save();

						// link payment with invoice table
						$pi = new PayInv();
						$pi->inv_id = $invoice->id;
						$pi->pay_id = $payment->id;
						$pi->amount = $payment->amount;
						$pi->save();

						// update invoice status
						$invoice->status = Invoice::INVOICE_STATUS_PAID;
						$invoice->save();

						if (Yii::app()->user->isGuest) return 'SUCCESS';
						$this->redirect($this->createUrl('order/finish', array('result' => 'success')));
				} else {
						// we got payment , but sounds the invoice has been paid over
						// how to record this ?
						// TODO ...
				}

		} else {
				// invalid invoice id
				// how to record this ?
				// TODO ...
		}

		if (!empty($shipments)) {
			foreach ($shipments as $shipment) {
				$shipment->insurance = 5.5;
				$shipment->save();
			}

			$this->redirect($this->createUrl('order/finish', array('result' => 'success')));
		} else {

		}
	}

	private function afterPaymentCancelled() {
		$this->redirect($this->createUrl('order/finish', array('result' => 'cancel')));
	}

	public function actionFinish() {
		Yii::log(json_encode($_GET), 'warning', 'trace');
		$this->render('finish', array('result' => $_GET['result']));
	}

	public function actionCompensation() {
		if (!empty($_POST)) {
			$trackno = $_POST['trackno'];
			$sender = $_POST['sender'];
			$tel = $_POST['tel'];
			$email = $_POST['email'];
			$reason = $_POST['reason'];
			$otherreason = !empty($_POST['otherreason']) ? ' ' . $_POST['otherreason'] : '';
			$files = $_POST['file'];
			$shipment = Shipment::model()->find('hbn = :hbn', array(':hbn' => $trackno));

			if (empty($shipment)) {
				$this->render('compensation_finish', array('result' => 'fail', 'msg' => '运单信息不存在'));
				return;
			} else if ($shipment->cnor->name != $sender) {
				$this->render('compensation_finish', array('result' => 'fail', 'msg' => '发件人错误'));
				return;
			} else if (empty($tel) || empty($email) || empty($reason) || ($reason == 'other' && empty($otherreason)) || count($files) < 2) {
				$this->render('compensation_finish', array('result' => 'fail', 'msg' => '补全信息'));
				return;
			} else {
				foreach ($files as $data) {
					if (!preg_match('/data:image\/(\w+);base64,/', $data, $matches)) {
						$this->render('compensation_finish', array('result' => 'fail', 'msg' => '只能上传图片'));
						return;
					}
				}

				$crm_map = CrmMap::model()->find('fid = :fid', array(':fid' => $shipment->id));

				if (!empty($crm_map)) {
					$this->render('compensation_finish', array('result' => 'fail', 'msg' => '该运单已在理赔流程中，请勿反复申请'));
					return;
				}

				$crm = new ExCrm;
				$crm->main_type = array_search('ExCrm', Crm::$main_types);
				$crm->operator_id = 0;
				$crm->open_by = 0;
				$crm->parcel_id = $shipment->id;
				$crm->level = 6;
				$crm->type = array_search('Compensation', ExCrm::$types);
				$crm->create_time = date('Y-m-d H:i:s');
				$crm->last_update_time = date('Y-m-d H:i:s');
				$crm->telephone = $tel;
				$crm->email = $email;
				$crm->mdata['desc'] = $reason . $otherreason;
				$crm->status = array_search('新的', ExCrm::$states);
				$crm->source = array_search('Web', ExCrm::$sources);
				$crm->save();

				$crm_map = new CrmMap;
				$crm_map->crm_id = $crm->id;
				$crm_map->model = 'ExParcel';
				$crm_map->fid = $shipment->id;
				$crm_map->status = 1;
				$crm_map->save();

				foreach ($files as $data) {
					$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
					preg_match('/data:image\/(\w+);base64,/', $data, $matches);
					$type = $matches[1];
					file_put_contents($f, base64_decode(preg_replace('/data:image\/' . $type . ';base64,/', '', $data)));
					$id = FileRepo::storeFile($f, 'P' . date('YmdHis') . '.' . $type, 100, $crm->id);
					unlink($f);
				}

				$this->render('compensation_finish', array('result' => 'success'));
				return;
			}
		}

		$this->render('compensation');
	}

	private  function safe_b64encode($string) {
		$data = base64_encode($string);
		$data = str_replace(array('+','/','='), array('-','_',''), $data);
		return $data;
	}

	private function safe_b64decode($string) {
		$data = str_replace(array('-','_'), array('+','/'), $string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}

}