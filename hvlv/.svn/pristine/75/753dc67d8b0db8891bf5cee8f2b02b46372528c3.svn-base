<?php

class BankController extends Controller
{

	protected $nonAjax = array('AjaxImportStatements', 'undoreconcile', 'reconcile', 'ajaxSearchTransaction', 'ajaxStatementReconcile', 'ajaxStatementReconcile2');

	public function actionUndoreconcile($id)
	{
		$bankStatement = BankStatement::model()->findByPk($id);
		if (!empty($bankStatement)) {

			if ($bankStatement->reconciled == 1) {
				// undo all reconcile adjustment if existing
				$adjustMent = BankReconcileAdjustment::model()->find('bank_transaction_id = :id', [':id' => $id]);
				if (!empty($adjustMent)) {
					$adjustMent->delete();
				}

				// undo all related payment
				if ($bankStatement->credits > 0) {
					$payments = Payment::model()->findAll('bank_transaction_id = :id', [':id' => $id]);
					foreach ($payments as $payment) {
						$payment->status = Payment::PAYMENT_STATUS_DELETED;
						$payment->bank_transaction_id = 0;
						$payment->save();
						foreach ($payment->invoices as $inv) {
							$inv->checkPaid();
							$inv->save();
						}
					}
				} else if ($bankStatement->debits > 0) {
					$payments = PaymentBilling::model()->findAll('bank_transaction_id = :id', [':id' => $id]);
					foreach ($payments as $payment) {
						$payment->status = PaymentBilling::PAYMENT_STATUS_DELETED;
						$payment->bank_transaction_id = 0;
						$payment->save();
						foreach ($payment->billings as $billing) {
							$billing->checkPaid();
							$billing->save();
						}
					}

					$payments = Payment::model()->findAll('refund = :refund', [':refund' => $bankStatement->debits]);
					foreach ($payments as $payment) {
						if (!empty($payment->mdata['refund_bank_transaction_id']) && $payment->mdata['refund_bank_transaction_id'] == $bankStatement->id) {
							$payment->refund = 0;
							unset($payment->mdata['refund_bank_transaction_id']);
							$payment->save();
							$payment->updateAta();
						}
					}
				}

				$bankStatement->reconciled = 0;
				$bankStatement->update('reconciled');

			} else {
				$bankStatement->addError('id', 'BankStatemnt : ' . $id . ' not reconciled yet');
			}

			$this->ajaxResult($bankStatement, ['balance' => BankStatement::model()->getBalance(), 'reconciled' => BankStatement::model()->getRecAmount(), 'unreconciled' => BankStatement::model()->getUnrecAmount()]);

		} else {
			$bankStatement = new BankStatement();
			$bankStatement->addError('id', 'BankStatemnt : ' . $id . ' not existing');
			$this->ajaxResult($bankStatement);
		}
	}

	public function actionOverview()
	{
		if (isset($_GET['tab'])) {
			Acl::hasAccess($this->CaName . '/' . $_GET['tab'], true);
			$model = BankStatement::model();
			if (isset($_GET['BankStatement'])) {
				$model->attributes = $_GET['BankStatement'];
			}
			$this->render('tab_' . $_GET['tab'], array('model' => $model));
		} else {
			$this->render('overview', ['balance' => BankStatement::model()->getBalance(), 'reconciled' => BankStatement::model()->getRecAmount(), 'unreconciled' => BankStatement::model()->getUnrecAmount(), 'opening' => BankStatement::model()->getOpening()]);
		}

	}

	public function actionImportStatement()
	{
		$this->render('import_new_statements');
	}

	public function actionAjaxImportStatements()
	{
		$resp = array('success' => 1, 'msg' => 'import successfully');

		$template_file = empty($_FILES['banksfile']) ? array() : $_FILES['banksfile'];
		if (empty($template_file['tmp_name']) || !is_uploaded_file($template_file['tmp_name'])) {
			$resp['msg'] = 'Invalid template file';
			echo json_encode($resp);
			return;

		} else {
			$xls = new oExcel;
			$xls->load($template_file['tmp_name']);
			$data = $xls->getAll();

			// remove first row and last two rows
			// $rowNums = count($data);
			// unset($data[1]);
			// unset($data[$rowNums]);
			// unset($data[$rowNums-1]);

			// order statements by latest order
			// $data = array_reverse($data);
			// valide  account number and currency
			foreach ($data as $rowNum => $row) {
				if (!is_numeric(str_replace(",", "", $row[6])) && !is_numeric(str_replace(",", "", $row[7]))) {
					unset($data[$rowNum]);
					continue;
				}

				$accountNumber = trim($row[2]);
				$bankAccount = BankAccount::model()->find('account_number = :no', [':no' => $accountNumber]);
				if (empty($bankAccount)) {
					$bankAccount = new BankAccount;
					$bankAccount->name = $row[1];
					$bankAccount->code = $row[2];
					$bankAccount->created = date('Y-m-d H:i:s');
					$bankAccount->account_number = $row[2];
					$bankAccount->save();
				}

				$currency = strtoupper(trim($row[3]));
				$currencyId = Invoice::getCurrencyId($currency);
				if ($currencyId < 0) {
					$resp['msg'] = ($resp['msg'] == 'import successfully' ? '' : $resp['msg'] . '<br>') . 'Currency : ' . $currency . ' Not Found';
					unset($data[$rowNum]);
				}

				$date = trim($row[4]);
				if (!preg_match('/\d{4}\-\d{2}\-\d{2}/', $date)) {
					$date = oExcel::toDate($date);
				}
				$desc = trim($row[5]);
				$debits = $row[6];
				$credits = $row[7];
				$balance = (!is_numeric($row[8]) && !empty($row[8])) ? $row[7] : 0;
				$customer = (!is_numeric($row[8]) && !empty($row[8])) ? 0 : $row[8];
				$hash = md5($date . $desc . $debits . $credits . $balance . $customer);
				$check = BankStatement::model()->find('hash = :hash', [':hash' => $hash]);
				if (!empty($check)) {
					$resp['msg'] = ($resp['msg'] == 'import successfully' ? '' : $resp['msg'] . '<br>') . 'Statement : ' . $desc . ' has been imported already';
					unset($data[$rowNum]);
				}
			}

			// save all saved successfully
			$successIds = array();
			$allErrors = array();
			foreach ($data as $row) {
				$accountNumber = trim($row[2]);

				// try to find relate bank account id
				$bankAccount = BankAccount::model()->find('account_number = :no', [':no' => $accountNumber]);
				$currency = strtoupper(trim($row[3]));
				$currencyId = Invoice::getCurrencyId($currency);

				$date = trim($row[4]);
				if (!preg_match('/\d{4}\-\d{2}\-\d{2}/', $date)) {
					$date = oExcel::toDate($date);
				}
				$desc = trim($row[5]);
				$debits = $row[6];
				$credits = $row[7];
				$balance = (!is_numeric($row[8]) && !empty($row[8])) ? $row[7] : 0;
				$customer = (!is_numeric($row[8]) && !empty($row[8])) ? 0 : $row[8];
				$hash = md5($date . $desc . $debits . $credits . $balance . $customer);

				// if (strlen($date) < 8) {
				// 	$date = '0' . $date;
				// }

				// $date = substr($date, 4) . '-' . substr($date, 2, 2) . '-' . substr($date, 0, 2);
				$desc = trim($row[5]);
				$debits = floatval(str_replace(",", "", $row[6]));
				$credits = floatval(str_replace(",", "", $row[7]));
				$balance = floatval(str_replace(",", "", $balance));
				$customer = intval($row[8]);

				$bankm = new BankStatement();
				$bankm->created = $date;
				$bankm->credits = $credits;
				$bankm->debits = $debits;
				$bankm->balance = $balance;
				$bankm->org_id = $customer;
				$bankm->currency = $currencyId;
				$bankm->bank_account_id = $bankAccount->id;
				$bankm->hash = $hash;
				$bankm->desc = $desc;
				if ($balance != 0) {
					// $bankm->reconciled = 1;
					// $bankm->credits = 0;
					// $bankm->debits = 0;
					$bankm->balance = 0;
					$bankm->org_id = 0;
				} else {
					$bankm->reconciled = 0;
				}
				$bankm->save();
				$errors = $bankm->getErrors();

				if (empty($errors)) {
					$successIds[] = $bankm->id;
				} else {
					$oneError = 'faild to save : ' . $desc . ' Reason: ';
					foreach ($errors as $k => $error) {
						$oneError .= json_encode($error) . ' ';
					}
					$allErrors[] = $oneError;
				}
			}

			// in case error happened , delete all
			if (!empty($allErrors)) {
				$crd = new CDbCriteria();
				$crd->addInCondition('id', $successIds);
				BankStatement::model()->deleteAll($crd);
				$resp['msg'] = '';
				foreach ($allErrors as $error) {
					$resp['msg'] .= $error . '<br/>';
				}
				$resp['success'] = 0;
			} else {
				// save uploaded file
				// with time as fid field value
				FileRepo::storeFile($template_file['tmp_name'], $template_file['name'], 91, time());
			}
		}

		$resp['balance'] = BankStatement::model()->getBalance();
		$resp['reconciled'] = BankStatement::model()->getRecAmount();
		$resp['unreconciled'] = BankStatement::model()->getUnrecAmount();
		echo json_encode($resp);
	}

	public function actionReconcile()
	{
		$bankStatement = BankStatement::model()->findByPk($_GET['fid']);

		$results = array();
		if (!empty($bankStatement)) {
			if ($bankStatement->credits > 0) {
				// try to match all credits
				// including invoices, receive money
				if (!empty($bankStatement->org->name)) {
					$name = $bankStatement->org->name;
				} else {
					$name = explode(' ', trim(preg_replace('/(WITHDRAWAL ONLINE)|(\d+)|(PYMT)/', '', $bankStatement->desc)))[0];
				}
				$this->searchInvoices($name, 0, $results);

				if (empty($results)) {
					$this->searchInvoices('', 0, $results);
				}

				// get all overpayment as well
				// $this->searchPayments(@$bankStatement->org->name, 0, $results);

			} else {
				// try to match all spent
				if (!empty($bankStatement->org->name)) {
					$name = $bankStatement->org->name;
				} else {
					$name = explode(' ', trim(preg_replace('/(WITHDRAWAL ONLINE)|(\d+)|(PYMT)/', '', $bankStatement->desc)))[0];
				}
				$this->searchGeneralCosts($name, 0, $results);

				if (empty($results)) {
					$this->searchGeneralCosts('', 0, $results);
				}

				// $this->searchPayments2(@$bankStatement->org->name, 0, $results);

				// get all billing payment
				// $this->searchBillingPayments(@$bankStatement->org->name, 0, $results);
			}
		}

		if (!empty($bankStatement)) {
			$this->render('bankstatement_reconcile', ['model' => $bankStatement, 'invoices' => $results]);
		}
	}

	/**
	 * show create a new overpayment UI
	 */
	public function actionCreateOverpayment()
	{
		$bankStatement = BankStatement::model()->findByPk($_GET['fid']);
		if ($bankStatement->credits > 0) {
			$this->redirect($this->createUrl('payment/create', [
				'tabid' => $_GET['tabid'],
				'bs_date' => $bankStatement->created,
				'bs_amount' => $bankStatement->credits,
			]));
		} else if ($bankStatement->debits > 0) {
			$this->redirect($this->createUrl('paymentBilling/create', [
				'tabid' => $_GET['tabid'],
				'bs_date' => $bankStatement->created,
				'bs_amount' => $bankStatement->debits,
			]));
		}
		// if ( isset($_POST['Payment']) ) {
		//     $payment = new Payment();
		//     $payment->attributes = $_POST['Payment'];
		//     $payment->type = Payment::PAYMENT_TYPE_EFT;
		//     $payment->ref = 'Overpayment: ' . $payment->ref;
		//     $payment->ata = $payment->amount;
		//     $payment->status = Payment::PAYMENT_STATUS_POSTED;
		//     $payment->bank = 10; // as westbank
		//     $payment->currency = 1; // as AUD
		//     $payment->bank_transaction_id = 0;
		//     $payment->save();
		//     $this->ajaxResult($payment);

		// } else {
		//     $orgId = isset($_GET['oid']) ? $_GET['oid'] : 0;
		//     //   $leftMoney = isset($_GET['lfm']) ? $_GET['lfm'] : 0;
		//     $payment = new Payment();
		//     $payment->org_id = $orgId;
		//     $payment->date = date('Y-m-d');
		//     $this->render('new_overpayment', ['model' => $payment]);
		// }
	}

	/**
	 * try to search for all general cost by name or amount
	 * @param $name
	 * @param $amount
	 * @param $result
	 */
	private function searchGeneralCosts($name, $amount, &$result, $page = 0)
	{
		$sql = 'SELECT i.*,g.name FROM billing AS i LEFT JOIN org as g ON g.id = i.org_id WHERE i.status in (3,5,7)'; // ignore cancelled billing

		if ($amount > 0 && empty($name)) {
			$sql .= ' AND i.total = ' . $amount;
		} else if (!empty($name) && $amount == 0) {
			$sql .= ' AND (g.name like "%' . $name . '%" OR i.billing_cref like "%' . $name . '%") AND i.total > 0';
		} else if (!empty($name) && $amount > 0) {
			$sql .= ' AND (g.name like "%' . $name . '%" OR i.billing_cref like "%' . $name . '%" OR i.total = ' . $amount . ')';
		}

		$sql .= ' ORDER BY i.date ASC LIMIT 20 OFFSET ' . ($page * 20);

		$c = Yii::app()->db->createCommand($sql);
		$costs = $c->queryAll();
		foreach ($costs as $cost) {
			$custName = isset($cost['name']) ? $cost['name'] : '';
			$index = 'c' . $cost['id'];
			$billing = Billing::model()->findByPk($cost['id']);
			$result[$index] = array(
				'index' => $index,
				'id' => $cost['id'],
				'model' => 'Billing',
				'no' => $cost['billing_cref'],
				'date' => !empty($cost['date']) ? $cost['date'] : $billing->lines[0]->date,
				'cust' => $custName,
				'ref' => $cost['no'],
				'status' => Billing::$states[$cost['status']],
				'amt' => $billing->getBalance(),
				'split' => 0,
			);
		}
	}

	private function searchBillingPayments($name, $amount, &$result, $page = 0)
	{
		$sql = 'SELECT p.*, g.name FROM payment_b AS p LEFT JOIN org as g ON g.id = p.org_id WHERE p.status < 9';

		if ($amount > 0 && empty($name)) {
			$sql .= ' AND p.amount = ' . $amount;
		} else if (!empty($name) && $amount == 0) {
			$sql .= ' AND g.name like "%' . $name . '%" AND p.amount > 0';
		} else if (!empty($name) && $amount > 0) {
			$sql .= ' AND (g.name like "%' . $name . '%" OR p.amount = ' . $amount . ')';
		}

		$sql .= ' AND p.bank_transaction_id = 0 ORDER BY p.transaction_date ASC LIMIT 20 OFFSET ' . ($page * 20);

		$c = Yii::app()->db->createCommand($sql);
		$payments = $c->queryAll();
		foreach ($payments as $payment) {
			$custName = isset($payment['name']) ? $payment['name'] : '';
			$index = 'p' . $payment['id'];
			$result[$index] = array(
				'index' => $index,
				'id' => $payment['id'],
				'model' => 'PaymentB',
				'no' => PaymentBilling::model()->findByPk($payment['id'])->billingnos,
				'date' => $payment['date'],
				'cust' => $custName,
				'ref' => $payment['ref'],
				'status' => PaymentBilling::$states[$payment['status']],
				'amt' => round($payment['amount'], 2),
				'split' => 0,
			);
		}
	}

	/**
	 * search related invoices
	 * @param $name
	 * @param $amount
	 * @param $result
	 *     return all results into this parameter
	 */
	private function searchInvoices($name, $amount, &$result, $page = 0)
	{
		$sql = 'SELECT i.*,g.name FROM invoice AS i LEFT JOIN org as g ON g.id = i.to_id WHERE i.status in (2,3,7) '; // not paid invoices only

		if ($amount > 0 && empty($name)) {
			$sql .= ' AND i.total = ' . $amount;
		} else if (!empty($name) && $amount == 0) {
			$sql .= ' AND ( i.ref like "%' . $name . '%" OR g.name like "%' . $name . '%" OR i.no like "%' . $name . '%") AND i.total > 0';
		} else if (!empty($name) && $amount > 0) {
			$sql .= ' AND ( i.ref like "%' . $name . '%" OR g.name like "%' . $name . '%" OR i.no like "%' . $name . '%" OR i.total = ' . $amount . ')';
		}

		$sql .= ' ORDER BY i.due ASC LIMIT 20 OFFSET ' . ($page * 20);

		$c = Yii::app()->db->createCommand($sql);
		$invoices = $c->queryAll();
		foreach ($invoices as $inv) {
			$custName = isset($inv['name']) ? $inv['name'] : '';
			$index = 'i' . $inv['id'];
			$invoice = Invoice::model()->findByPk($inv['id']);
			$result[$index] = array(
				'index' => $index,
				'id' => $inv['id'],
				'model' => 'Invoice',
				'no' => $inv['no'],
				'date' => $inv['date'],
				'cust' => $custName,
				'ref' => $inv['ref'],
				'status' => Invoice::$states[$inv['status']],
				'amt' => $invoice->getBalance(),
				'split' => 0,
			);
		}
	}

	/**
	 * @param $name
	 * @param $amount
	 * @param $result
	 *   -- return all result to this parameters
	 */
	private function searchPayments($name, $amount, &$result, $page = 0)
	{
		$sql = 'SELECT p.*,g.name FROM payment AS p LEFT JOIN org as g ON g.id = p.org_id WHERE p.status < 9 AND p.no is not NULL AND p.date >= "2017-07-01"';

		if ($amount > 0 && empty($name)) {
			$sql .= ' AND p.amount = ' . $amount;
		} else if (!empty($name) && $amount == 0) {
			$sql .= ' AND (g.name like "%' . $name . '%" OR p.no like "%' . $name . '%") AND p.amount > 0';
		} else if (!empty($name) && $amount > 0) {
			$sql .= ' AND (g.name like "%' . $name . '%" OR p.no like "%' . $name . '%" OR p.amount = ' . $amount . ')';
		}

		$sql .= ' AND p.bank_transaction_id = 0 ORDER BY p.transaction_date ASC LIMIT 20 OFFSET ' . ($page * 20);

		$c = Yii::app()->db->createCommand($sql);
		$payments = $c->queryAll();
		foreach ($payments as $payment) {
			$custName = isset($payment['name']) ? $payment['name'] : '';
			$index = 'p' . $payment['id'];
			$result[$index] = array(
				'index' => $index,
				'id' => $payment['id'],
				'model' => 'Payment',
				'no' => $payment['no'],
				'date' => $payment['date'],
				'cust' => $custName,
				'ref' => $payment['ref'],
				'status' => Payment::$states[$payment['status']],
				'amt' => round($payment['amount'], 2),
				'split' => 0,
			);
		}
	}

	private function searchPayments2($name, $amount, &$result, $page = 0)
	{
		$sql = 'SELECT p.*,g.name FROM payment AS p LEFT JOIN org as g ON g.id = p.org_id WHERE p.status < 9 AND p.no is not NULL AND p.date >= "2017-07-01"';

		if ($amount > 0 && empty($name)) {
			$sql .= ' AND p.ata = ' . $amount;
		} else if (!empty($name) && $amount == 0) {
			$sql .= ' AND (g.name like "%' . $name . '%" OR p.no like "%' . $name . '%") AND p.ata > 0';
		} else if (!empty($name) && $amount > 0) {
			$sql .= ' AND (g.name like "%' . $name . '%" OR p.no like "%' . $name . '%" OR p.ata = ' . $amount . ')';
		}

		$sql .= ' AND p.refund = 0 ORDER BY p.transaction_date ASC LIMIT 20 OFFSET ' . ($page * 20);

		$c = Yii::app()->db->createCommand($sql);
		$payments = $c->queryAll();
		foreach ($payments as $payment) {
			$custName = isset($payment['name']) ? $payment['name'] : '';
			$index = 'p' . $payment['id'];
			$result[$index] = array(
				'index' => $index,
				'id' => $payment['id'],
				'model' => 'Payment',
				'no' => $payment['no'],
				'date' => $payment['date'],
				'cust' => $custName,
				'ref' => $payment['ref'],
				'status' => Payment::$states[$payment['status']],
				'amt' => round($payment['ata'], 2),
				'split' => 0,
			);
		}
	}

	/**
	 * ajax call
	 * search transactions
	 */
	public function actionAjaxSearchTransaction()
	{
		$bankStatement = BankStatement::model()->findByPk($_GET['fid']);

		$searchContent = trim($_POST['sname']);
		if (is_numeric($searchContent)) {
			$searchName = $searchContent;
			$searchAmount = floatval($searchContent);
			$searchAmount = round($searchAmount, 2);
		} else {
			$searchName = $searchContent;
			$searchAmount = 0;
		}

		$results = array();
		// step 1
		// search related invoices
		// $this->searchInvoices($searchName,$searchAmount,$results);

		// step 2
		// search related overpayments
		if ($bankStatement->credits > 0) {
			$this->searchInvoices($searchName, $searchAmount, $results, @$_GET['page']);
			if (!empty($_POST['payment']) && $_POST['payment'] == 'true') {
				$this->searchPayments($searchName, $searchAmount, $results, @$_GET['page']);
			}

			if (empty($results)) {
				$this->searchInvoices('', $searchAmount, $results, @$_GET['page']);
				if (!empty($_POST['payment']) && $_POST['payment'] == 'true') {
					$this->searchPayments('', $searchAmount, $results, @$_GET['page']);
				}
			}
		} else if ($bankStatement->debits > 0) {
			// $this->searchBillingPayments($searchName, $searchAmount, $results);
			$this->searchGeneralCosts($searchName, $searchAmount, $results, @$_GET['page']);
			if (!empty($_POST['payment']) && $_POST['payment'] == 'true') {
				$this->searchPayments2($searchName, $searchAmount, $results, @$_GET['page']);
			}

			if (empty($results)) {
				$this->searchGeneralCosts('', $searchAmount, $results, @$_GET['page']);
				if (!empty($_POST['payment']) && $_POST['payment'] == 'true') {
					$this->searchPayments2('', $searchAmount, $results, @$_GET['page']);
				}
			}
		}

		$resp = array('success' => 1, 'data' => $results);

		echo json_encode($resp);
	}

	public function actionReconcileView()
	{
		$bankStatement = BankStatement::model()->findByPk($_GET['fid']);

		$details = array();
		if (!empty($bankStatement)) {

			// get all related payments
			if ($bankStatement->credits > 0) {
				$payments = Payment::model()->findAll('bank_transaction_id = :id', [':id' => $bankStatement->id]);
				foreach ($payments as $index => $payment) {
					$custName = !empty($payment->cust) ? $payment->cust->name : '';
					foreach ($payment->pays as $pay) {
						$details[$index][] = array(
							'date' => $pay->invoice->date,
							'cust' => $custName,
							'no' => $pay->invoice->no,
							'received' => $pay->amount,
							'spent' => 0,
							'view' => '<a class="jqm_link" href="' . $this->createUrl('invoice/update', ['id' => $pay->invoice->id]) . '">View</a>',
						);
					}
				}
			} else if ($bankStatement->debits > 0) {
				$payments = PaymentBilling::model()->findAll('bank_transaction_id = :id', [':id' => $bankStatement->id]);
				foreach ($payments as $index => $payment) {
					$custName = !empty($payment->cust) ? $payment->cust->name : '';
					foreach ($payment->pays as $pay) {
						$details[$index][] = array(
							'date' => $pay->billing->date,
							'cust' => $custName,
							'no' => $pay->billing->billing_cref,
							'received' => 0,
							'spent' => $pay->amount,
							'view' => '<a class="jqm_link" href="' . $this->createUrl('billing/update', ['id' => $pay->billing->id]) . '">View</a>',
						);
					}
				}
				$offset = count($payments);

				$payments = Payment::model()->findAll('meta like "%refund_bank_transaction_id%"');
				foreach ($payments as $index => $payment) {
					$custName = !empty($payment->cust) ? $payment->cust->name : '';
					$details[$offset + $index][] = array(
						'date' => $payment->date,
						'cust' => $custName,
						'no' => $payment->no,
						'received' => 0,
						'spent' => $payment->refund,
						'view' => '<a class="jqm_link" href="' . $this->createUrl('payment/update', ['id' => $payment->id]) . '">View</a>',
					);
				}
			}

			// get all related adjustments
			$adjustMent = BankReconcileAdjustment::model()->find("bank_transaction_id = :id", [':id' => $bankStatement->id]);
			if (!empty($adjustMent)) {
				$received = 0;
				$spent = 0;
				if ($adjustMent->amount < 0) {
					$spent = abs($adjustMent->amount);
				} else {
					$received = $adjustMent->amount;
				}
				$details[] = array(
					'date' => $adjustMent->created,
					'cust' => 'Reconciliation adjustment',
					'received' => $received,
					'spent' => $spent,
					'view' => '',
				);
			}
		}

		$this->render('bankstatement_review', ['model' => $bankStatement, 'details' => $details]);

	}

	/**
	 * bank statements reconcile logic progress
	 */
	public function actionAjaxStatementReconcile()
	{
		$resp = array('success' => 1, 'msg' => '', 'balance' => 0);

		$bankStatementId = isset($_POST['bsid']) ? $_POST['bsid'] : 0;
		// $adjustAmount = isset($_POST['adj_amt']) ? $_POST['adj_amt'] : 0;
		$selectedTransactions = isset($_POST['sts']) ? $_POST['sts'] : array();

		// try to get related bank statement
		$errors = array();
		$errMsg = '';
		$bankStatement = BankStatement::model()->findByPk($bankStatementId);
		$credits = $bankStatement->credits;
		$debits = $bankStatement->debits;
		if (!empty($bankStatement)) {

			if ($bankStatement->reconciled == 1) {
				$errMsg = 'Bank Statement : ' . $bankStatementId . ' has been reconciled already';
			} else {
				// in case adjustment existing , create it
				// if ($adjustAmount != 0) {
				//     $bankAdjustment = new BankReconcileAdjustment();
				//     $bankAdjustment->amount = $adjustAmount;
				//     $bankAdjustment->created = date('Y-m-d');
				//     $bankAdjustment->bank_transaction_id = $bankStatementId;
				//     $bankAdjustment->save();
				//     $errors = $bankAdjustment->getErrors();
				// }

				if (empty($errors)) {
					$clientsInvoices = array();
					foreach ($selectedTransactions as $id => $transaction) {
						// in case transaction is payment already just link them
						if ($transaction['model'] == 'Payment') {
							$p = Payment::model()->findByPk($transaction['id']);
							if (!empty($p)) {
								if ($p->bank_transaction_id == 0 && $credits > 0) {
									$p->bank_transaction_id = $bankStatementId;
									$p->update('bank_transaction_id');
								} else if ($p->refund == 0 && $debits > 0) {
									$p->refund = $transaction['split'] > 0 ? $transaction['split'] : $transaction['amt'];
									$p->mdata['refund_bank_transaction_id'] = $bankStatementId;
									$p->update('refund', 'meta');
									$p->updateAta();
								}
							}
						} else if ($transaction['model'] == 'Invoice') {
							// link payments with bank statments
							// split invoice related payment by customer
							$invoice = Invoice::model()->findByPk($transaction['id']);
							if (!empty($invoice)) {
								if (!isset($clientsInvoices[$invoice->to_id])) {
									$clientsInvoices[$invoice->to_id] = array('invs' => array(), 'total' => 0);
								}
								$clientsInvoices[$invoice->to_id]['invs'][] = $transaction;
								$clientsInvoices[$invoice->to_id]['total'] += $transaction['split'] > 0 ? $transaction['split'] : $transaction['amt'];
							}
						} else if ($transaction['model'] == 'PaymentB') {
							$p = PaymentBilling::model()->findByPk($transaction['id']);
							if (!empty($p) && $p->bank_transaction_id == 0) {
								$p->bank_transaction_id = $bankStatementId;
								$p->update('bank_transaction_id');
							}
						} else if ($transaction['model'] == 'Billing') {
							$billing = Billing::model()->findByPk($transaction['id']);
							if (!empty($billing)) {
								if (!isset($clientsBillings[$billing->org_id])) {
									$clientsBillings[$billing->org_id] = array('billings' => array(), 'total' => 0);
								}
								$clientsBillings[$billing->org_id]['billings'][] = $transaction;
								$clientsBillings[$billing->org_id]['total'] += $transaction['split'] > 0 ? $transaction['split'] : $transaction['amt'];
							}
						}
					}

					// create all payments and link with related invoice
					// and link with bank statement as well
					if (!empty($clientsInvoices)) {
						foreach ($clientsInvoices as $id => $invoices) {
							// create payment for the client
							$total = $invoices['total'];
							$p = new Payment;
							$p->org_id = $id;
							$p->amount = $total;
							// $p->ata = 0; // all amount should be allocated
							$p->date = $bankStatement->created;
							$p->bank = 10; // default as westbank
							$p->currency = 1; // default as AUD
							$p->type = Payment::PAYMENT_TYPE_EFT; // default as pay by EFT
							$p->status = Payment::PAYMENT_STATUS_POSTED;
							$p->bank_transaction_id = $bankStatementId;
							$p->mdata['diff'] = $_POST['diff'];
							$p->save();
							$credits -= $total;

							$errors = $p->getErrors();
							if (empty($errors)) {
								// once payment saved successfully
								// allocate to all related invoices now
								foreach ($invoices['invs'] as $invoice) {
									$pi = new PayInv();
									$pi->inv_id = $invoice['id'];
									$pi->pay_id = $p->id;
									$pi->amount = $invoice['split'] > 0 ? $invoice['split'] : $invoice['amt'];
									$pi->exrate = 1;
									$pi->save();

									// try to update invoice status
									$inv = Invoice::model()->findByPk($invoice['id']);
									$inv->checkPaid();
									$inv->save();
								}
							}
						}
						$p->amount += $credits;
						if ($p->save()) {
							$p->updateAta();
						} else {
							$errors = $p->getErrors();
						}
					}

					// create all billing payments and link with related billings
					if (!empty($clientsBillings)) {
						foreach ($clientsBillings as $id => $billings) {
							$total = $billings['total'];
							$p = new PaymentBilling;
							$p->org_id = $id;
							$p->amount = $total;
							$p->date = $bankStatement->created;
							$p->bank = 10;
							$p->currency = 1;
							$p->type = PaymentBilling::PAYMENT_TYPE_EFT;
							$p->status = PaymentBilling::PAYMENT_STATUS_POSTED;
							$p->bank_transaction_id = $bankStatementId;
							$p->save();
							$debits -= $total;

							$errors = $p->getErrors();
							if (empty($errors)) {
								foreach ($billings['billings'] as $billing) {
									$pb = new PayBill;
									$pb->bill_id = $billing['id'];
									$pb->pay_id = $p->id;
									$pb->amount = $billing['split'] > 0 ? $billing['split'] : $billing['amt'];
									$pb->save();

									$bill = Billing::model()->findByPk($billing['id']);
									$bill->checkPaid();
									$bill->save();
								}
							}
						}
						$p->amount += $debits;
						if ($p->save()) {
							$p->updateAta();
						} else {
							$errors = $p->getErrors();
						}
					}

					if (empty($errors)) {
						$bankStatement->reconciled = 1;
						$bankStatement->reconciled_date = date('Y-m-d');
						$bankStatement->update('reconciled', 'reconciled_date');
					} else {
						foreach ($errors as $k => $err) {
							$errMsg .= $err[0] . '<br>';
						}
					}
				}
			}
		} else {
			$errMsg = 'Bank Statment : ' . $bankStatementId . ' not found';
		}

		if (!empty($errMsg)) {
			$resp['success'] = 0;
			$resp['msg'] = '<span style="color:red;"> ' . $errMsg . '</span>';
		}
		$resp['balance'] = BankStatement::model()->getBalance();
		$resp['reconciled'] = BankStatement::model()->getRecAmount();
		$resp['unreconciled'] = BankStatement::model()->getUnrecAmount();
		echo json_encode($resp);
	}

	public function actionLog()
	{
		$model = BankStatement::model()->findByPk($_GET['fid']);
		$this->render('log', array('model' => $model));
	}

	public function actionNotes()
	{
		$model = BankStatement::model()->findByPk($_GET['fid']);
		Log::add($model, Log::LOG_TYPE_NOTES, ['notes' => $_POST['notes']]);
		$this->ajaxResult($model);
	}

	public function actionAjaxStatementReconcile2()
	{
		$resp = array('success' => 1, 'msg' => '');

		$bankStatementId = isset($_POST['bsid']) ? $_POST['bsid'] : 0;
		$bankStatement = BankStatement::model()->findByPk($bankStatementId);

		if (empty($_POST['org'])) {
			$resp['success'] = 0;
			$resp['msg'] = 'Please select org';
		}

		if ($resp['success'] && !empty($bankStatement) && $bankStatement->credits > 0) {
			$p = new Payment;
			$p->org_id = $_POST['org'];
			$p->amount = $bankStatement->credits;
			$p->ata = $p->amount;
			$p->date = $bankStatement->created;
			$p->bank = 10;
			$p->currency = 1;
			$p->type = Payment::PAYMENT_TYPE_EFT;
			$p->status = Payment::PAYMENT_STATUS_POSTED;
			$p->bank_transaction_id = $bankStatementId;
			$p->ref = $bankStatement->desc;

			$owner = Org::model()->findByPk($p->org_id);
			$p->mdata['name'] = $owner->name;
			$p->mdata['address'] = $owner->getAddress();
			$p->save();

			if (empty($p->getErrors()) && empty($p->credit_lines)) {
				$il = new CreditLine();
				$il->pid = $p->id;
				$il->description = $p->ref;
				$il->tax = 'EXEMPTOUTPUT';
				$il->rate = $p->amount;
				$il->qty = 1;
				$il->amount = $il->rate * $il->qty;
				$gst = 0;
				if (!$il->save()) {
					var_dump($il->getErrors());
				}
			}

			if (empty($p->getErrors())) {
				$bankStatement->reconciled = 1;
				$bankStatement->reconciled_date = date('Y-m-d');
				$bankStatement->update('reconciled', 'reconciled_date');
			} else {
				$resp['success'] = 0;
				foreach ($p->getErrors() as $err) {
					$resp['msg'] .= '<br />' . $err[0];
				}
			}
		}

		$resp['balance'] = BankStatement::model()->getBalance();
		$resp['reconciled'] = BankStatement::model()->getRecAmount();
		$resp['unreconciled'] = BankStatement::model()->getUnrecAmount();
		echo json_encode($resp);
	}

	public function actionEditGrid()
	{
		$bankStatementId = $_POST['BankStatement']['id'];
		$bankStatement = BankStatement::model()->findByPk($bankStatementId);
		$bankStatement->desc = $_POST['BankStatement']['desc'];
		$bankStatement->update('desc');
		$this->ajaxResult($bankStatement);
	}

	public function actionSyncXero()
	{
		if (empty($_GET['confirm'])) {
			$this->render('sync_xero');
		} else {
			$last = BankStatement::model()->find(array('order' => 'created desc'));

			$xero = new XeroAPI;
			// include FromDate
			$transactions = $xero->get('Accounting\BankTransaction', array('FromDate' => date('Y-m-d', strtotime($last->created . ' + 1 day'))));

			foreach ($transactions as $transaction) {
				$bank_account = BankAccount::getByXeroID($transaction['BankAccount']['AccountID']);

				$bank_statement = BankStatement::model()->find('xero_id = :id', array(':id' => $transaction['BankTransactionID']));
				if (empty($bank_statement)) {
					$bank_statement = new BankStatement;
					$bank_statement->bank_account_id = empty($bank_account) ? 0 : $bank_account->id;
					$bank_statement->currency = Invoice::getCurrencyId($transaction['CurrencyCode']);
					$bank_statement->created = $transaction['Date']->format('Y-m-d');
					$bank_statement->xero_id = $transaction['BankTransactionID'];
					$bank_statement->desc = $transaction['Contact']['Name'];
					if ($transaction['Type'] == 'SPEND') {
						$bank_statement->debits = $transaction['Total'];
						$bank_statement->credits = 0;
					} else if ($transaction['Type'] == 'RECEIVE') {
						$bank_statement->debits = 0;
						$bank_statement->credits = $transaction['Total'];
					}
					$bank_statement->balance = 0;
					$bank_statement->org_id = 0;
					$bank_statement->reconciled = 0;
					$bank_statement->hash = md5($bank_statement->created . $bank_statement->desc . $bank_statement->debits . $bank_statement->credits . $bank_statement->balance . $bank_statement->org_id);
					$bank_statement->save();
				}
			}

			$this->ajaxResult($last);
		}
	}

	public function actionDelete($id)
	{
		$model = BankStatement::model()->findByPk($id);
		$model->reconciled = 11;
		$model->update('reconciled');

		$this->ajaxResult($model);
	}

}
