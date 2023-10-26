<?php
class jrfCommand extends CConsoleCommand
{
	private $db;
	private $args;
	private $tmp;

	public function run($args)
	{
		$this->db = Yii::app()->getDb();
		$this->args = $args;
		$this->tmp = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
		if (!empty($args[0]) && method_exists($this, $args[0])) {
			$this->{$args[0]}();
		}
	}

	public function log($m)
	{
		$lf = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'jrf_cmd.log';
		@file_put_contents($lf, date('Y-m-d H:i:s') . ' ' . $m . "\n", FILE_APPEND);
	}

	public function _loadXlsData($f)
	{
		if (!is_file($f)) {
			die($f . " not exist\n");
		}

		return oExcel::getAllData($f, false, true);
	}

	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha' => array(
				'class' => 'CCaptchaAction',
				'backColor' => 0xEBF0FA,
				'maxLength' => 4,
				'minLength' => 4,
			),
		);
	}

	public function user_login()
	{
		$ca = Yii::app()->createController('cg/site')[0]->createAction("captcha");
		$model = new LoginForm;
		$model->attributes = array('username' => 'driver@gmail.com', 'password' => 'password');
		$model->vcc = $ca->getVerifyCode();
		return $model->validate();
	}

	public function shipment_weight()
	{
		$task = WmsTask::model()->find('link_id = :link_id AND type = :type', array(
			':link_id' => 328,
			':type' => array_search('Delivery', WmsTask::$types),
		));
		$criteria = new CDbCriteria();
		$criteria->compare('id', $task->mdata['shipment_id']);
		$shipments = Shipment::model()->findAll($criteria);
		$weight = 0;
		foreach ($shipments as $shipment) {
			$weight += $shipment->weight;
		}
		echo $weight;
		return;
	}

	public function org_creditBalance()
	{
		$org = Org::model()->findByPk(839);
		echo $org->getCurrentCreditOfLimit();
		return;
	}

	public function org_orgHaveCredit()
	{
		$orgs = Org::model()->findAll();
		foreach ($orgs as $org) {
			if ($org->getCurrentCreditOfLimit()) {
				echo $org->id;
			}
		}
		return;
	}

	public function jjfjm_genYseFjm()
	{
		$address = "福建泉州";
		$sql = "select * from FJMB where (('" . $address . "' like concat(SF,'%',CS,'%',DQ,'%')) or ('" . $address . "' like concat(SF,'%',CS,'%')) or ('" . $address . "' like concat(SF,'%',DQ,'%')) or ('" . $address . "' like concat(CS,'%',DQ,'%')) or ('" . $address . "' like concat(CS,'%')) or ('" . $address . "' like concat(DQ2,'%'))) order by (length('" . $address . "') - length(replace('" . $address . "',SF,''))-length(replace('" . $address . "',CS,''))) desc, (length('" . $address . "')-length(replace('" . $address . "',DQ,''))-length(replace('" . $address . "',DQ2,''))) desc limit 0,1";
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		echo $rs[0]["FJBM"];
	}

	// public function file_match() {
	//     $excel_name = 'C:\Users\FC\Desktop\PCA-CHEN第1批20180323(1).xlsx';
	//     $xls = new oExcel;
	//     $xls->load($excel_name);
	//     $data = $xls->getAll();
	//     unset($data[1]);

	//     $pdfs = [];
	//     $total_pdfs = [];
	//     $errors = [];
	//     foreach ($data as $k => $line) {
	//         $pdf1 = 'C:\Users\FC\Desktop\VANGEN\vangen\\' . $line[2] . '.pdf';
	//         $pdf2 = 'C:\Users\FC\Desktop\VANGEN\yuantong\pdf\\' . $line[4] . '.pdf';
	//         if (is_file($pdf1) && is_file($pdf2)) {
	//             $pdfs[] = $pdf1;
	//             $pdfs[] = $pdf2;
	//         } else {
	//             echo $k;
	//             $errors[] = floor($k / 50) * 50 - 1;
	//         }
	//         if (($k % 50 == 49 || $k == count($data) + 1)) {
	//             if (!in_array((floor($k / 50) * 50 - 1), $errors)) {
	//                 $total = 'C:\Users\FC\Desktop\VANGEN' . ceil(($k+1) / 50) . '.pdf';
	//                 oPDF::mergePDF($pdfs, 1, true, $total);
	//                 $pdfs = [];
	//                 $total_pdfs[] = $total;
	//             } else {
	//                 $pdfs = [];
	//             }
	//         }
	//     }
	//     return;
	// }

	// public function file_combine() {
	//     $pdfs = [];
	//     for ($i = 11; $i <= 12; $i++) {
	//         $pdfs[] = 'C:\Users\FC\Desktop\pdfs\\' . 'VANGEN' . $i . '.pdf';
	//     }

	//     $total = 'C:\Users\FC\Desktop\\' . 'VANGEN6.pdf';
	//     oPDF::mergePDF($pdfs, 1, true, $total);
	//     return;
	// }

	// public function wmstask_autocomplete() {
	//     if (empty($this->args[1])) {
	//         return;
	//     }

	//     $tasks = WmsTask::model()->with('job')->findAll('t.is_request = 1 AND t.type = :type AND job.no = :job_no AND t.status = :status', array(':type' => array_search('Pick Carton', WmsTask::$types), ':job_no' => $this->args[1], ':status' => array_search('Scheduled', WmsTask::$states)));

	//     foreach ($tasks as $task) {
	//         $ac_task = $task->actionTask;
	//         foreach ($task->items as $item) {
	//             $stock = WmsStock::model()->findByPk($item->mdata['si']);
	//             $sls = $stock->locs;
	//             $qty = $item->mdata['uq'];
	//             foreach ($sls as $sl) {
	//                 if ($qty) {
	//                     $itm = new WmsTaskItem;
	//                     $itm->task_id = $ac_task->id;
	//                     $itm->mdata = array(
	//                         'si' => $item->mdata['si'],
	//                         'sn' => $item->mdata['sn'],
	//                         'uq' => number_format(($qty > $sl->qty ? $sl->qty : $qty), 0),
	//                         'pli' => $sl->loc->id,
	//                         'pl' => $sl->loc->code
	//                     );
	//                     $itm->save();
	//                     $qty -= $qty > $sl->qty ? $sl->qty : $qty;
	//                 }
	//             }
	//         }
	//         $ac_task->compl_time = date('Y-m-d H:i:s');
	//         $ac_task->save();

	//         $pack_task = WmsTask::model()->find('link_id = :link_id AND type = :type', array(':link_id' => $task->id, 'type' => array_search('Pack Order', WmsTask::$types)));
	//         $meta = json_decode($task->meta, true);
	//         if ($meta && array_key_exists("pkg", $meta)) {
	//             $pkg = json_decode($meta['pkg'], true);
	//         } else {
	//             $pkg = array();
	//         }

	//         $weight = "7.4";
	//         $material = "是";
	//         $parcelId = "P" . sprintf("%06d", $task->id) . sprintf("%03d", count($pkg) + 1);
	//         $pkg[] = array("wt" => $weight, "w" => "", "h" => "", "d" => "", "nt" => $material . " " . date("Y-m-d H:i:s", time()) . " " . $parcelId);
	//         $meta["pkg"] = json_encode($pkg);
	//         $pack_task->mdata = $meta;
	//         $pack_task->save();

	//         $task->status = array_search('Completed', WmsTask::$states);
	//         $task->save();
	//     }
	//     return;
	// }

	public function cg_result()
	{
		$supay = new SupayAPI;
		echo $supay->checkoutResult($this->args[1]);
	}

	// public function cg_finish() {
	//     $task_id = $this->args[1];
	//     $supay = new SupayAPI;
	//     $task = WmsTask::model()->findByPk($task_id);
	//     if ($supay->checkoutResult($task->id)) {
	//         $this->afterPaymentPaid($task->id, array('notice_id' => $task->id, 'amount' => $task->mdata['total']), 'SUPAY');
	//     } else {
	//         $this->afterPaymentCancelled($task->id);
	//     }
	// }

	// private function generateInvoice($task, $org) {
	//     // invoice
	//     $fd = date('Ymd');
	//     $fdts = date('Ymd');
	//     $wd = date('N', $fdts);
	//     if ($wd != 6) {
	//         $fdts = strtotime($fd . ' -' . ($wd + 1) . ' day');
	//         $fd = date('Ymd', $fdts);
	//     }
	//     $td = date('Ymd', strtotime($fd . ' +6 day'));
	//     $bd = date('Ymd', strtotime($fd . ' +9 day'));
	//     $inv = new Invoice();
	//     $inv->type = Invoice::INVOICE_TYPE_CG_DELIVERY;
	//     $inv->dpmt = Invoice::DPMT_3PL;
	//     $inv->currency = array_search('AUD', Invoice::$currencies);
	//     $inv->to_id = $org->id;
	//     $inv->status = Invoice::INVOICE_STATUS_PENDING;
	//     $inv->date = $bd;
	//     $inv->mdata['name'] = $org->name;
	//     $inv->mdata['address'] = $org->getAddress();
	//     $inv->mdata['payterm'] = empty($org->extra['payterm']) ? 'COD' : $org->extra['payterm'] . ' days';
	//     $inv->mdata['billfrom'] = $fd;
	//     $inv->mdata['billto'] = $td;
	//     $inv->due = $inv->date;
	//     $inv->total = 0;
	//     $inv->gst = 0;
	//     $inv->save();
	//     InvLine::model()->deleteAll('inv_id = :id', array(':id' => $inv->id));

	//     $stot = $task->mdata['total'];
	//     $items = [];
	//     foreach ($task->items as $item) {
	//         $stock = WmsStock::model()->findByPk($item->mdata['si']);
	//         $items[] = [$task->getNo(), ucwords($task->ref), $task->schd_time, 'CG Delivery - ' . $item->mdata['sn'], $stock->prod->mdata['price'], $item->mdata['uq'], $item->mdata['uq'] * $stock->prod->mdata['price']];
	//     }
	//     $items[] = [$task->getNo(), ucwords($task->ref), $task->schd_time, 'CG Delivery - Freight fee', $task->mdata['freight'], 1, $task->mdata['freight']];

	//     $il = InvLine::model()->find('fid = :id', [':id' => $task->id]);
	//     if (empty($il)) {
	//         $il = new InvLine();
	//         $il->inv_id = $inv->id;
	//     }
	//     $il->amount = $stot;
	//     $il->gst = 0;
	//     $il->mdata['items'] = $items;
	//     $il->ccode = $task->type;
	//     $il->det = $task->getType();
	//     $il->qty = 1;
	//     $il->fid = $task->id;
	//     $il->save();

	//     $inv->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
	//     $inv->lines = [$il];
	//     $inv->total += $il->amount;
	//     $inv->gst = 0;
	//     $inv->sync_xero = false;
	//     $inv->save();

	//     return $inv;
	// }

	// private function afterPaymentPaid($task_id, $trnInfo, $type) {
	//     if (!empty($trnInfo['TransactionRefNo'])) {
	//         $trn = $trnInfo['TransactionRefNo'];
	//         $amount = $trnInfo['AmountPaid'];
	//     } else if (!empty($trnInfo['TOKEN'])) {
	//         $trn = $trnInfo['TOKEN'];
	//         $amount = $trnInfo['PAYMENTREQUEST_0_AMT'];
	//     } else if (!empty($trnInfo['notice_id'])) {
	//         $trn = $trnInfo['notice_id'];
	//         $amount = $trnInfo['amount'];
	//     }

	//     $task = WmsTask::model()->findByPk($task_id);
	//     $org = Org::model()->findByPk($task->job->org_id);
	//     $payment_gateway_history_model = PaymentGatewayHistory::model()->find('trn = :trn', array(':trn' => $trn));
	//     if ( !isset($payment_gateway_history_model) ) {
	//         $invoice = $this->generateInvoice($task, $org);
	//         $payment_gateway_history_model = new PaymentGatewayHistory();
	//         $payment_gateway_history_model->trn = $trn;
	//         $payment_gateway_history_model->invoice_id = $invoice->id;
	//         $payment_gateway_history_model->type = constant('PaymentGatewayHistory::PAYMENT_GATEWAY_TYPE_'. $type);
	//         $payment_gateway_history_model->token = $trn;
	//         $payment_gateway_history_model->meta = json_encode($trnInfo);
	//         $payment_gateway_history_model->result = 'success';
	//         $payment_gateway_history_model->payed_time = new CDbExpression('NOW()');;
	//         $payment_gateway_history_model->save();
	//     } else {
	//         if ( $payment_gateway_history_model->result == 'success' && !empty($payment_gateway_history_model->payed_time) ) {
	//             echo 'you have paid the invoice successfully already before!';
	//             return;
	//         }
	//     }

	//     // get original invoice data
	//     if ( !empty($invoice) ) {
	//         // 9 : paid
	//         // 10 : cancelled
	//         if ( $invoice->status != Invoice::INVOICE_STATUS_PAID &&
	//             $invoice->status != Invoice::INVOICE_STATUS_CACELLED ) {

	//             // create a payment
	//             $payment = new Payment();
	//             $payment->org_id = $invoice->to_id;
	//             $payment->amount = $amount;
	//             $payment->ref = $type . ' TRN: ' . $trn;
	//             $payment->date = date('Y-m-d', time());
	//             $payment->type = constant('Payment::PAYMENT_TYPE_'. $type);
	//             $payment->bank = array_search('Westpac AUD', Payment::$banks); // westbank
	//             $payment->mdata['bankcharge'] = $invoice->total - (double)$payment->amount;
	//             $payment->currency = 1; // AUD
	//             $payment->status = Payment::PAYMENT_STATUS_POSTED; // posted status ? paid finish?
	//             $payment->save();

	//             // link payment with invoice table
	//             $pi = new PayInv();
	//             $pi->inv_id = $invoice->id;
	//             $pi->pay_id = $payment->id;
	//             $pi->amount = $payment->amount;
	//             $pi->save();

	//             // update invoice status
	//             $invoice->status = Invoice::INVOICE_STATUS_PAID;
	//             $invoice->save();

	//             // update task
	//             $task->status = array_search('Scheduled', WmsTask::$states);
	//             $task->save();
	//             foreach ($task->items as $item) {
	//                 $item->toStock();
	//             }
	//         } else {
	//             // we got payment , but sounds the invoice has been paid over
	//             // how to record this ?
	//             // TODO ...
	//         }

	//     } else {
	//         // invalid invoice id
	//         // how to record this ?
	//         // TODO ...
	//     }
	// }

	// private function afterPaymentCancelled($task_id) {
	//     $task = WmsTask::model()->findByPk($task_id);
	//     $task->status = array_search('Cancelled', WmsTask::$states);
	//     $task->save();

	//     $task->job->status = array_search('Cancelled', WmsJob::$states);
	//     $task->job->save();
	// }

	// private function org_thermal() {
	//     $shipments = Shipment::model()->findAll(array('select' => array('distinct agent_id', 'type'), 'condition' => 'hbn like \'%EAU%\' or \'%DAU%\''));

	//     foreach ($shipments as $shipment) {
	//         echo $shipment->agent->id;
	//         if ($shipment->agent->id) {
	//             $org = Org::model()->findByPk($shipment->agent->id);
	//             $org->extra['thermal'] = true;
	//             $org->sync_xero = false;
	//             $org->save();
	//         }
	//     }
	// }

	// private function wmstaskitem_toStock() {
	//     $task_id = $this->args[1];
	//     $task = WmsTask::model()->findByPk($task_id);
	//     $ac_task = $task->actionTask;
	//     foreach ($ac_task->items as $item) {
	//         $item->toStock();
	//     }
	// }

	// private function wmstask_wip() {
	//     $org_id = $this->args[1];

	//     $pickup_tasks = WmsTask::model()->with('job', 'mainTask')->findAll(
	//         'mainTask.status = :status AND t.type in (2110, 2120) AND job.org_id = :org_id',
	//         array(
	//             ':status' => array_search('WIP', WmsTask::$states),
	//             ':org_id' => $org_id,
	//         )
	//     );

	//     foreach ($pickup_tasks as $pickup_task) {
	//         $log = Log::model()->find(array(
	//             'condition' => 'model = :model AND meta like :meta AND lid = :lid',
	//             'order' => 'id DESC',
	//             'params' => array(
	//                 ':model' => 'WmsTask',
	//                 ':meta' => '%WIP%',
	//                 ':lid' => $pickup_task->mainTask->id
	//             )
	//         ));

	//         if ($log) {
	//             $pickup_task->mainTask->compl_time = $log->time;
	//             $pickup_task->mainTask->status = 99;
	//             $pickup_task->mainTask->save();

	//             echo $pickup_task->mainTask->id . ' ';
	//         }
	//     }
	// }

	// private function cg_task_update() {
	//     $orders = WmsTask::model()->findAll('type = 5010 and is_request = 1 order by id');
	//     foreach ($orders as $order) {
	//         $order->ref .= ' ' . $order->mdata['org'];
	//         $order->save();
	//     }
	// }

	private function cg_org_limit()
	{
		$id = $this->prompt('Org ID: ');
		echo 'got org ID  : ' . $id . PHP_EOL;
		$org = Org::model()->findByPk($id);
		echo json_encode($org->cgLimit());
	}

	private function wechat_sort()
	{
		$token = '54gvvfbj4fwfg4j4wgcdvbw45w34gh5';
		$timestamp = '1524705845';
		$nonce = '3870172442';
		$signature = '23f80151da98f56ede56524a73ba32d021e61632';

		$array = array($token, $timestamp, $nonce);
		sort($array, SORT_STRING);
		$str = implode($array);
		$str = sha1($str);
		echo $str == $signature;

		$array = array(null, $token, $timestamp, $nonce);
		sort($array, SORT_STRING);
		$str = implode($array);
		$str = sha1($str);
		echo $str == $signature;
	}

	// private function crm_create() {
	//     $crm_msg = new CrmMsg;
	//     $crm_msg->wechat_id = 1;
	//     $crm_msg->wechat_msg_id = 1;
	//     $crm_msg->status = 1;
	//     $crm_msg->create_time = date('Y-m-d H:i:s');
	//     $crm_msg->update_time = $crm_msg->create_time;
	//     $crm_msg->mdata['msgs'][] = array('wechat_id' => $crm_msg->wechat_id, 'msg' => 'test', 'time' => date('Y-m-d H:i:s'));
	//     $crm_msg->save();
	// }

	// private function crmmsg_pic() {
	//     $weObj = new WechatAPI();
	//     $weObj->checkAuth();
	//     $file = $weObj->getMedia('8GicxmAWfdkm26Sr_fXo-9JyfWBDlGP3nmdWap6j3GQP5kuBvl4B7wM_xOJuNowl');

	//     // get type
	//     $bin = substr($file,0,2);
	//     $strInfo = @unpack("C2chars", $bin);
	//     $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
	//     switch ($typeCode) {
	//         case 255216:
	//             $fileType = 'jpg';
	//             break;
	//         case 7173:
	//             $fileType = 'gif';
	//             break;
	//         case 6677:
	//             $fileType = 'bmp';
	//             break;
	//         case 13780:
	//             $fileType = 'png';
	//             break;
	//     }
	//     echo $fileType;

	//     // save pic
	//     $f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
	//     file_put_contents($f, $file);
	//     $id = FileRepo::storeFile($f, 'P' . date('YmdHis') . '.' . $fileType, 101, 18);
	//     $file = FileRepo::model()->findByPk($id);
	//     echo $file->getUrl();
	// }

	// private function wechat_menu() {
	//     $weObj = new WechatAPI();
	//     $weObj->checkAuth();

	//     $newmenu = array(
	//         'button' => array(
	//             0 => array(
	//                 'name' => '快捷入口',
	//                 'sub_button' => array(
	//                     0 => array('type' => 'view', 'name' => '运单查询', 'url' => 'https://au.pca168.com/tracking.html'),
	//                     1 => array('type' => 'view', 'name' => '证件上传', 'url' => 'https://au.pca168.com/upload-id.html'),
	//                     2 => array('type' => 'view', 'name' => '在线查询', 'url' => 'https://www.pcaexpress.com.au/zh/%e5%9c%a8%e7%ba%bf%e5%92%a8%e8%af%a2/')
	//                 )
	//             ),
	//             1 => array(
	//                 'name' => '客服电话',
	//                 'sub_button' => array(
	//                     0 => array('type' => 'click', 'name' => '澳洲客服电话', 'key' => 'AU_PHONE'),
	//                     1 => array('type' => 'click', 'name' => '中国客服电话', 'key' => 'CN_PHONE')
	//                 )
	//             )
	//         )
	//     );
	//     $result = $weObj->createMenu($newmenu);
	//     echo $result;
	// }

	// private function thread_test() {
	//     exec('yiic jrf function1');
	//     exec('yiic jrf function2');
	// }

	// private function function1() {
	//     $file = fopen('file1.txt', 'w');
	//     while (true) {
	//         fwrite($file, 1);
	//         sleep(2);
	//     }
	// }

	// private function function2() {
	//     $file = fopen('file2.txt', 'w');
	//     while (true) {
	//         fwrite($file, 2);
	//         sleep(2);
	//     }
	// }

	// private function wechat_msg_id() {
	//     $crm_msg_lines = CrmMsgLine::model()->findAll();
	//     foreach ($crm_msg_lines as $crm_msg_line) {
	//         if (!empty($crm_msg_line->mdata['msgid'])) {
	//             $crm_msg_line->wechat_msg_id = $crm_msg_line->mdata['msgid'];
	//             $crm_msg_line->save();
	//         }
	//     }
	// }

	// private function wechat_msg_id() {
	//     $crm_msg_lines = CrmMsgLine::model()->findAll();
	//     foreach ($crm_msg_lines as $crm_msg_line) {
	//         if (!empty($crm_msg_line->mdata['msgid'])) {
	//             unset($crm_msg_line->mdata['msgid']);
	//             $crm_msg_line->save();
	//         }
	//     }
	// }

	// private function wechat_msg() {
	//     $crm_msg_lines = CrmMsgLine::model()->findAll('crm_msg_id = 11');
	//     foreach ($crm_msg_lines as $crm_msg_line) {
	//         if (!empty($crm_msg_line->mdata['wechat'])) {
	//             $crm_msg = CrmMsg::model()->find('wechat_id = :wechat_id AND status != 100', array(':wechat_id' => $crm_msg_line->mdata['wechat']));
	//             $crm_msg_line->crm_msg_id = $crm_msg->id;
	//             $crm_msg_line->save();
	//         }
	//     }
	// }

	// private function wechat_text() {
	//     $crm_msg_lines = CrmMsgLine::model()->findAll();

	//     $i = 0;
	//     foreach ($crm_msg_lines as $crm_msg_line) {
	//         if (!empty($crm_msg_line->mdata['text']) && preg_match('/(EAU\d+|DAU\d+|PE\d+)/i', $crm_msg_line->mdata['text'], $matches)) {
	//             $no = $matches[1];
	//             $shipment = Shipment::model()->find('hbn = :hbn', array(':hbn' => $no));
	//             echo json_encode($shipment->trackingInfo());
	//             break;
	//         }
	//     }
	// }

	// private function wms_location_fix() {
	//     $locations = WmsLocation::model()->findAll('type = 30');
	//     foreach ($locations as $location) {
	//         $dup_location = WmsLocation::model()->find('code = :code AND type = 50', array(':code' => $location->code));
	//         if ($dup_location) {
	//             $dup_location->type = $location->type;
	//             $dup_location->pid = $location->pid;
	//             $dup_location->wt = $location->wt;
	//             $dup_location->save();
	//             $location->status = 0;
	//             $location->save();
	//         }
	//     }
	// }

	private function org_rebate()
	{
		$sell_rates = SellRate::model()->findAll('type = 25 AND vfrom = "2018-05-15" AND item = 7.5');
		foreach ($sell_rates as $sell_rate) {
			$org = Org::model()->findByPk($sell_rate->org_id);
			$org->extra['rebate_apply'] = true;
			$org->sync_xero = 0;
			$org->save();
		}
	}

	private function wms_location_fix()
	{
		$locs = WmsLocation::model()->findAll('(code like "%ES%" OR code like "%GS%") AND status = 1');

		foreach ($locs as $loc) {
			$sl = WmsStockLocation::model()->with('loc')->find('loc.code = :p', [':p' => $loc->code]);
			if ($sl->stock->org_id == Org::ORGID_3PL_COBAYER) {
				$dup_loc = WmsLocation::model()->find('code = :code AND status = 0', array(':code' => $loc->code));
				if ($dup_loc) {
					$dup_loc->status = 1;
					$dup_loc->save();

					$loc->name .= '-dup';
					$loc->code .= '-dup';
					$loc->type = 50;
					$loc->pid = 5;
					$loc->wt = 0;
					$loc->save();
				}
			}
		}
	}

	public function exconsol_accrual_fix()
	{
		$afhis = AFInvoiceReconciliationHistory::model()->findAll('op_id = :op_id', array(':op_id' => 596));
		foreach ($afhis as $history) {
			if (in_array($history_id, [27, 28])) {
				$afs = AFInvoiceReconciliation::model()->findAll('history_id = :history_id', array(':history_id' => $history->id));
			} else {
				$afs = AFInvoiceReconciliation::model()->findAll('history_id = :history_id AND matched_result > 1', array(':history_id' => $history->id));
			}
			foreach ($afs as $af) {
				$awb = $af->awb;
				$dashAwb = substr($awb, 0, 3) . '-' . substr($awb, 3);
				$exco = ExcoConsol::model()->find('awb = :awb', array(':awb' => $dashAwb));
				if ($exco) {
					$bls = BillingLine::model()->findAll('billing_ref = :billing_ref', array(':billing_ref' => $exco->no));
					$totWeight = !empty($exco->mdata['awb_check_wt']) ? $exco->mdata['awb_check_wt'] : $exco->totWeight();
					$gl1 = 0;
					$gl5 = 51;
					$gl11 = $totWeight * 0.135;
					foreach ($bls as $bl) {
						if ($bl->item_code == 'GL11') {
							yii::log($bl->id . ' ' . $bl->accrual_amount, 'warning');
							$bl->accrual_amount = $totWeight * 0.135;
							yii::log($bl->id . ' ' . $bl->accrual_amount, 'warning');
							$bl->save();
						} else if ($bl->item_code == 'GL1') {
							yii::log($bl->id . ' ' . $bl->accrual_amount, 'warning');
							$bl->accrual_amount = $totWeight * Yii::app()->params['settings']['af_' . $exco->pol . '_' . $exco->pod]['value'] - $gl5 - $gl11 - 0.1 * $totWeight;
							yii::log($totWeight . ' ' . Yii::app()->params['settings']['af_' . $exco->pol . '_' . $exco->pod]['value'], 'warning');
							yii::log($bl->id . ' ' . $bl->accrual_amount, 'warning');
							$bl->save();
						}
					}
				}
			}
		}
	}

	public function wechat_pic_test()
	{
		$weObj = new WechatAPI();
		$weObj->checkAuth();
		echo json_encode($weObj->checkAuth());
		$file = $weObj->getMedia('BJXWQG_K2ocKZGe8wGrj6bZ_Ml9VdEx7mIfIJsPdkzLRlf2psfeMHPEQ00SGeH52');
		echo $file;
	}

	public function wechat_pic_fix()
	{
		$files = FileRepo::model()->findAll('name like "%." AND fid != 0');
		foreach ($files as $file) {
			$crm_msg_line = CrmMsgLine::model()->findByPk($file->fid);

			if (!empty($crm_msg_line->mdata['mediaid'])) {
				$weObj = new WechatAPI();
				$weObj->checkAuth();
				$file = $weObj->getMedia($crm_msg_line->mdata['mediaid']);

				// get type
				$bin = substr($file, 0, 2);
				$strInfo = @unpack("C2chars", $bin);
				$typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
				switch ($typeCode) {
					case 255216:
						$fileType = 'jpg';
						break;
					case 7173:
						$fileType = 'gif';
						break;
					case 6677:
						$fileType = 'bmp';
						break;
					case 13780:
						$fileType = 'png';
						break;
				}

				// save pic
				$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
				file_put_contents($f, $file);
				$id = FileRepo::storeFile($f, 'P' . date('YmdHis') . '.' . $fileType, 101, $crm_msg_line->id);
				$file = FileRepo::model()->findByPk($id);
				unlink($f);
			}
		}
	}

	public function wechat_pic_fix1()
	{
		$files = FileRepo::model()->findAll('name like "%." AND fid > 12847');
		foreach ($files as $file) {
			$crm_msg_line = CrmMsgLine::model()->findByPk($file->fid);
			yii::log($file->fid, 'warning');
			$dup_file = FileRepo::model()->find('fid = :fid AND size > 0 AND type = 101', array(':fid' => $file->fid));
			$crm_msg_line->mdata['pic'] = substr($dup_file->getUrl(), 5, strlen($dup_file->getUrl()));
			$crm_msg_line->save();
		}
	}

	public function sendInvoiceTermDue()
	{
		$sql = 'SELECT to_id FROM `invoice` where status in(2,3,7) AND dpmt in(10,30,40) AND date>"2018-01-29" group by to_id';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		$orgIds = [];
		foreach ($rs as $r) {
			$orgIds[] = $r['to_id'];
		}
		$orgIds = [1388];
		if (!empty($orgIds)) {
			foreach ($orgIds as $oid) {
				$inv_no = [];
				$term = '';
				$org = Org::model()->find('id=:oid', array(':oid' => $oid));
				if (!empty($org->extra['credit_payment_method'])) {
					continue;
				}

				$a = $org->getCurrentCreditOfLimit();
				$limit = floor($org->extra['creditlimit']);
				$term = empty($org->extra['creditterms']) ? 0 : intval($org->extra['creditterms']);
				if (empty($term)) {
					continue;
				}

				for ($i = ($term > 3 ? 3 : $term); $i >= 0; $i--) {
					if ($term - $i >= 0) {
						$invoices = Invoice::model()->findAll('status in(2,3,7) AND dpmt in(10,30,40) AND date=DATE_SUB(CURDATE(), INTERVAL :day DAY) AND to_id=:oid AND date>"2018-01-29"', array(':day' => $term - $i, ':oid' => $oid));
						$inv_no[$i] = $invoices;
					}
				}
				$invoices = Invoice::model()->findAll('status in(2,3,7) AND dpmt in(10,30,40) AND date<DATE_SUB(CURDATE(), INTERVAL :day DAY) AND to_id=:oid AND date>"2018-01-29"', array(':day' => $term, ':oid' => $oid));
				$inv_no['OverDue'] = $invoices;
				$term = '<table class="chart"><tr><th>Day To OverDue</th><th>Invoices</th><th>Amount-AUD</th></tr>';
				$main_total = 0;
				foreach ($inv_no as $index => $invs) {
					$total = 0;
					$inv_term = '';
					foreach ($invs as $k => $inv) {
						$inv_term .= $inv->no . '(' . $inv->getBalance() . ');';
						if ($k % 4 == 0) {
							$inv_term .= '<br/>';
						}

						$total += $inv->getBalance();
					}
					$main_total += $total;
					$term .= '<tr><td>' . $index . '</td><td>' . $inv_term . '</td><td>' . $total . '</td></tr>';
				}

				$term .= '<tfoot><tr><th colspan="2" align="right">Total:</th><th>' . $main_total . '</th></tr></tfoot></table>';

				if ($main_total > 0) {
					echo $org->id . ' - ' . $a . ' - ' . $limit . ' - ' . $term;
					// $this->sendCreditLimit($org, $a, $limit, $term);
				}
			}
		}
	}

	public function exp_chargeweight()
	{
		$hbn = $this->prompt('Shipment HBN: ');
		echo 'got shipment HBN  : ' . $hbn . PHP_EOL;
		$ep = ExParcel::model()->find('hbn = :hbn', array(':hbn' => $hbn));

		echo $ep->chargeWeight() . PHP_EOL;
		echo $ep->goodsType() . PHP_EOL;
		echo $ep->getAgentRate() . PHP_EOL;
	}

	public function test1()
	{
		echo intval(date('W'));
	}

	public function wechat_forevermedia_count()
	{
		$weObj = new WechatAPI();
		$weObj->checkAuth();

		$result = $weObj->getForeverCount();
		echo json_encode($result);
	}

	public function wms_pobox()
	{
		$delivery_task = WmsTask::model()->find('type = 2120 AND link_id = :link_id', array(':link_id' => 18042));

		if (Addr::checkIsPoBox($delivery_task->mdata['cnee']['address'])) {
			$delivery_task->mdata['courier'] = 101;
			$delivery_task->save();
		}
	}

	public function exp_change()
	{
		$hbn = $this->prompt('Shipment HBN: ');
		echo 'got shipment HBN  : ' . $hbn . PHP_EOL;
		$ep = ExParcel::model()->find('hbn = :hbn', array(':hbn' => $hbn));

		$rev = (!empty($ep->mdata['accr_tariff']) ? $ep->mdata['accr_tariff'] : 0) > 0;
		echo 'rev: ' . $rev . PHP_EOL;

		$tq = empty($ep->eitems['q']) ? 1 : array_sum($ep->eitems['q']);
		if (!$rev || $tq <= 1) {
			echo 'return1' . PHP_EOL;
		}

		if (empty($ep->eitems['pid'])) {
			$ep->eitems['pid'] = [];
		}

		echo json_encode($ep->eitems) . PHP_EOL;
		foreach ($ep->eitems['pid'] as $gi => $pid) {
			$rep = true;
			if (preg_match('/1段|一段|2段|二段/', $ep->eitems['g'][$gi])) {
				$ep->eitems['g'][$gi] = preg_replace(['/1段|2段/', '/一段|二段/'], ['3段', '三段'], $ep->eitems['g'][$gi]);
				$pd = ExProdb::model()->find('name_zh = :n', [':n' => $ep->eitems['g'][$gi]]);
				if (!empty($pd)) {
					$pid = $pd->id;
				}

			} else {
				$rep = false;
			}

			if (empty($pid)) {
				continue;
			}

			if ($rep) {
				if (empty($pd)) {
					$pd = ExProdb::model()->findByPk($pid);
				}

				$ep->eitems['pid'][$gi] = $pid;
				$ep->eitems['g'][$gi] = $pd->name_zh;
				$ep->eitems['b'][$gi] = $pd->brand;
				$ep->eitems['m'][$gi] = $pd->model;
				$ep->eitems['u'][$gi] = $pd->unit;
				$ep->eitems['hs'][$gi] = $pd->hs;
				$ep->eitems['w'][$gi] = $pd->weight * $ep->eitems['q'][$gi];
				$ep->eitems['v'][$gi] = empty($pd->mdata['price_CNJJI']) ? $pd->price : $pd->mdata['price_CNJJI'];
			}
		}
		echo json_encode($ep->eitems) . PHP_EOL;
	}

	public function exp_change2()
	{
		$mode = $this->prompt('mode: ');
		$date = $this->prompt('date: ');

		if ($mode == 1) {
			$consols = ExcoConsol::model()->findAll('created > "' . $date . '" AND type = 25');
			$agents = [];
			$count = 0;
			foreach ($consols as $consol) {
				foreach ($consol->shipments as $shipment) {
					if (!empty($shipment->value) && !empty($shipment->eitems['v']) && array_sum($shipment->eitems['v']) && (number_format(floatval($shipment->value), 2) != number_format(floatval(array_sum($shipment->eitems['v'])), 2)) && (in_array('三段', $shipment->eitems['m']) || in_array('四段', $shipment->eitems['m']))) {
						$count++;
					}
				}
			}
			echo $count . PHP_EOL;
		} else if ($mode == 2) {
			$consols = ExcoConsol::model()->findAll('created > "' . $date . '" AND type = 25');
			$agents = [];
			$count = 0;
			foreach ($consols as $consol) {
				foreach ($consol->shipments as $shipment) {
					if (!empty($shipment->value) && !empty($shipment->eitems['v']) && array_sum($shipment->eitems['v']) && (number_format(floatval($shipment->value), 2) != number_format(floatval(array_sum($shipment->eitems['v'])), 2)) && (in_array('三段', $shipment->eitems['m']) || in_array('四段', $shipment->eitems['m']))) {
						echo $shipment->hbn . PHP_EOL;
					}
				}
			}
		} else if ($mode == 3) {
			$consols = ExcoConsol::model()->findAll('created > "' . $date . '" AND type = 25');
			$agents = [];
			$count = 0;
			foreach ($consols as $consol) {
				foreach ($consol->shipments as $shipment) {
					if (!empty($shipment->value) && !empty($shipment->eitems['v']) && array_sum($shipment->eitems['v']) && (number_format(floatval($shipment->value), 2) != number_format(floatval(array_sum($shipment->eitems['v'])), 2)) && (in_array('三段', $shipment->eitems['m']) || in_array('四段', $shipment->eitems['m']))) {
						if (empty($agents[$shipment->agent_id])) {
							echo $shipment->agent_id . PHP_EOL;
							$agents[$shipment->agent_id] = $shipment->agent_id;
						}
					}
				}
			}
		}
	}

	public function priority_check_import()
	{
		$start_id = $this->prompt('start hid: ');
		$end_id = $this->prompt('end hid: ');

		$afs = AFInvoiceReconciliation::model()->findAll('(history_id between :sid and :eid) and matched_result = 1', array(':sid' => $start_id, ':eid' => $end_id));
		foreach ($afs as $af) {
			$bls = BillingLine::model()->count('billing_cref = :billing_cref and sync_xero = 1', array(':billing_cref' => $af->invoice_no));
			if ($bls) {
				if ($bls != count($af->lines)) {
					echo $af->invoice_no . ' b:' . $bls . ' a:' . count($af->lines) . PHP_EOL;
				}

			} else {
				$b = Billing::model()->find('billing_cref = :billing_cref and sync_xero = 1', array(':billing_cref' => $af->invoice_no));
				if (empty($b)) {
					echo $af->invoice_no . ' a:' . count($af->lines) . PHP_EOL;
				} else {
					if (count($b->lines) != count($af->lines)) {
						echo $af->invoice_no . ' b:' . count($b->lines) . ' a:' . count($af->lines) . PHP_EOL;
					}

				}
			}
		}
	}

	public function invoice_check_paid()
	{
		$inv_id = $this->prompt('invoice id: ');

		$inv = Invoice::model()->findByPk($inv_id);
		echo $inv->paid() . PHP_EOL;
		echo $inv->getCredit() . PHP_EOL;
		echo $inv->realPaid() . PHP_EOL;
	}

	public function fix_consol_accrual()
	{
		$history_id = $this->prompt('history id:');

		$afh = AFInvoiceReconciliationHistory::model()->findByPk($history_id);
		foreach ($afh->af as $af) {
			if ($af->model == 'ExcoConsol') {
				$exco = ExcoConsol::model()->findByPk($af->fid);
				if ($exco->pod == 'CNJMN') {
					$exco->pod = 'CNCAN';
					$exco->update(['pod']);
				}
				$exco->addBillingAccrual();
				$exco->fixBillingAccrual();
			}
		}
	}

	public function invoice_status()
	{
		$invoices = Invoice::model()->findAll('status in (2,3,6,7,8,9) AND date >= :date', [':date' => $this->prompt('Date: ')]);
		foreach ($invoices as $invoice) {
			$status1 = $invoice->getStatus();
			$invoice->checkPaid();
			$status2 = $invoice->getStatus();
			if ($status1 != $status2 && $invoice->total > 0) {
				echo $invoice->id . ' ' . $status1 . ' ' . $status2 . PHP_EOL;
			}
		}
	}

	public function invoice_fix()
	{
		$invoices = Invoice::model()->findAll('status in (2,3,6,7,8,9) AND date >= :date', [':date' => $this->prompt('Date: ')]);
		foreach ($invoices as $invoice) {
			$status1 = $invoice->getStatus();
			$invoice->checkPaid();
			$status2 = $invoice->getStatus();
			if ($status1 != $status2 && $invoice->total > 0) {
				echo $invoice->id . ' ' . $status1 . ' ' . $status2 . PHP_EOL;
				$invoice->update(['status']);
			}
		}
	}

	// public function invoice_sale_person() {
	//     $invoices = Invoice::model()->findAll();

	//     foreach ($invoices as $invoice) {
	//         $log = Log::getLast($invoice);
	//         if ($log) {
	//             $invoice->sp_id = $log->user_id;
	//             $invoice->update('sp_id');
	//         }
	//     }
	// }

	public function create_storage()
	{
		for ($i = 1; $i <= 18; $i++) {
			for ($j = 1; $j <= 4; $j++) {
				$storage = Storage::model()->find('name = :name', array(':name' => 'I-' . sprintf('%02d', $i) . '-' . $j));
				if (empty($storage)) {
					$storage = new Storage;
					$storage->name = 'I-' . sprintf('%02d', $i) . '-' . $j;
					$storage->code = $storage->name;
					$storage->type = 50;
					$storage->wid = 106;
					$storage->status = 1;
					$storage->save();
				}
			}
		}
	}

	public function org_sp_op()
	{
		$orgs = Org::model()->findAll();
		foreach ($orgs as $org) {
			if (!empty($org->extra['sp_id'])) {
				echo $org->id . '  ' . $org->extra['sp_id'] . PHP_EOL;
				$org->extra['op_id'] = $org->extra['sp_id'];
				$org->sync_xero = false;
				$org->update(['meta']);
			}
		}
	}

	public function create_location()
	{
		$locs = ['B2','C1'];
		foreach ($locs as $loc) {
			for ($i = 1; $i <= 16; $i++) {
				for ($j = 1; $j <= 8; $j++) {
					$storage = WmsLocation::model()->find('name = :name', array(':name' => $loc . '-' . sprintf('%02d', $i) . '-' . 'U' . $j));
					if (empty($storage)) {
						$storage = new WmsLocation;
						$storage->name = $loc . '-' . sprintf('%02d', $i) . '-' . 'U' . $j;
						$storage->code = '203' . $loc . sprintf('%02d', $i) . 'U' . $j;
						$storage->type = 20;
						$storage->wid = 106;
						$storage->status = 1;
						$storage->save();
					}

					$storage = WmsLocation::model()->find('name = :name', array(':name' => $loc . '-' . sprintf('%02d', $i) . '-' . 'D' . $j));
					if (empty($storage)) {
						$storage = new WmsLocation;
						$storage->name = $loc . '-' . sprintf('%02d', $i) . '-' . 'D' . $j;
						$storage->code = '203' . $loc . sprintf('%02d', $i) . 'D' . $j;
						$storage->type = 20;
						$storage->wid = 106;
						$storage->status = 1;
						$storage->save();
					}
				}
			}
		}
	}

	public function reconciliation_fix()
	{
		$recons = Reconciliation::model()->findAll();

		foreach ($recons as $recon) {
			$recon->manifest_no = $recon->lines[0]->getInvoiceNoColumnData();
			$recon->update('manifest_no');
		}
	}

	public function wms_org()
	{
		$orgs = Org::model()->findAll();
		foreach ($orgs as $org) {
			if (!empty($org->extra['wms_invoice'])) {
				echo $org->id . ' ' . $org->name . ' ' . $org->extra['wms_invoice'] . PHP_EOL;
			}
		}
	}

	// public function pay_inv_fix() {
	//     $pis = PayInv::model()->with(['payment', 'invoice'])->findAll('invoice.currency != payment.currency AND t.transaction_date > "2018-07-01" AND t.transaction_date < "2018-09-10"');
	//     foreach ($pis as $pi) {
	//         $result = $pi->amount;
	//         if (!empty($pi->payment->mdata['rate'])) {
	//             if ($pi->invoice->currency == 1) {
	//                 $pi->amount /= $pi->payment->mdata['rate'];
	//                 $pi->amount = number_format(floor($pi->amount * 100) / 100, 2);
	//                 $pi->exrate = 1 / $pi->payment->mdata['rate'];
	//             } else if ($pi->payment->currency == 1) {
	//                 $pi->amount *= $pi->payment->mdata['rate'];
	//                 $pi->amount = number_format(floor($pi->amount * 100) / 100, 2);
	//                 $pi->exrate = $pi->payment->mdata['rate'];
	//             }
	//             $pi->save();
	//         }
	//     }
	// }

	public function wms_billing_shipment_recon()
	{
		$delivery_tasks = WmsTask::model()->findAll('t.type = 2120');
		foreach ($delivery_tasks as $delivery_task) {
			$bill = WmsBilling::model()->find('task_id = :tid', array(':tid' => $delivery_task->mainTask->id));
			if (empty($bill)) {
				$bill = new WmsBilling;
				$bill->status = 1; // Auto
				$bill->task_id = $delivery_task->mainTask->id;
				$bill->code = 'PDP';
				$bill->desc = '';
				$bill->date = date('Y-m-d');
			}
			$bill->price = 0;
			$bill->qty = 1;
			$bill->mdata['reconLine'] = [];

			if (!empty($delivery_task->mdata['shipment_id'])) {
				if (!is_array($delivery_task->mdata['shipment_id'])) {
					$shipment = Shipment::model()->findByPk($delivery_task->mdata['shipment_id']);
					if (empty($shipment)) {
						continue;
					}

					$reconLine = ReconciliationLine::model()->find('shipment_no = :no', array(':no' => $shipment->ref));
					if (!empty($reconLine)) {
						$bill->price += $reconLine->value;
						$bill->mdata['reconLine'][] = $reconLine->id;
					}
				} else {
					foreach ($delivery_task->mdata['shipment_id'] as $id) {
						$shipment = Shipment::model()->findByPk($id);
						if (empty($shipment)) {
							continue;
						}

						$reconLine = ReconciliationLine::model()->find('shipment_no = :no', array(':no' => $shipment->ref));
						if (!empty($reconLine)) {
							$bill->price += $reconLine->value;
							$bill->mdata['reconLine'][] = $reconLine->id;
						}
					}
				}
				$bill->save();
			}
		}
	}

	public function delivery_task()
	{
		$delivery_tasks = WmsTask::model()->findAll('t.type = 2120');
		foreach ($delivery_tasks as $delivery_task) {
			if (!empty($delivery_task->mdata['shipment_id'])) {
				if (!is_array($delivery_task->mdata['shipment_id'])) {
					$delivery_task->mdata['shipment_id'] = [$delivery_task->mdata['shipment_id']];
					$delivery_task->update('meta');
				}
			}
		}
	}

	public function invoice_split_manifest()
	{
		$invoices = Reconciliation::model()->findAll('invoice_date >= "2018-07-01" AND client_type = 1');

		foreach ($invoices as $invoice) {
			$items = [];
			foreach ($invoice->lines as $line) {
				if (empty($items[$line->getInvoiceNoColumnData()])) {
					$items[$line->getInvoiceNoColumnData()] = [];
				}
				$items[$line->getInvoiceNoColumnData()][] = $line;
			}

			foreach ($items as $k => $lines) {
				if ($k == $invoice->manifest_no) {
					continue;
				}

				$new_inv = new Reconciliation;
				$new_inv->client_type = $invoice->client_type;
				$new_inv->invoice_no = $invoice->invoice_no;
				$new_inv->invoice_date = $invoice->invoice_date;
				$new_inv->manifest_no = $k;
				$new_inv->flag = $invoice->flag;
				$new_inv->save();
				foreach ($lines as $line) {
					$line->parent_id = $new_inv->id;
					$line->update('parent_id');
				}
				$new_inv->invoice_total = $new_inv->getInvoiceTotal();
				$new_inv->my_total = $new_inv->getMyTotal();
				$new_inv->update('invoice_total', 'my_total');
			}

			$invoice->invoice_total = $invoice->getInvoiceTotal();
			$invoice->my_total = $invoice->getMyTotal();
			$invoice->update('invoice_total', 'my_total');
		}
	}

	public function af_date_fix()
	{
		$lines = AFInvoiceReconciliationLine::model()->findAll();
		foreach ($lines as $line) {
			if (preg_match('/(43\d{3})/', $line->desc, $m)) {
				$line->desc = str_replace($m[1], ' ' . date('Y-m-d', strtotime(oExcel::toDate($m[1]))), $line->desc);
				// echo $line->desc . PHP_EOL;
				$line->update('desc');
				// echo $m[1] . PHP_EOL;
				// echo date('Y-m-d', strtotime(oExcel::toDate($m[1]))) . PHP_EOL;
			}
		}
	}

	public function invoice_payment()
	{
		$invoices = Invoice::model()->findAll('status in (6,8,9) AND id > 30000');
		foreach ($invoices as $invoice) {
			$total = 0;
			foreach ($invoice->payments as $payinv) {
				if ($payinv->payment->status == 6) {
					$total += $payinv->amount;
				}
			}
			if (number_format($total, 2, '.', '') != number_format($invoice->total, 2, '.', '')) {
				echo $invoice->no . ' ' . $total . ' ' . $invoice->total . PHP_EOL;
			}
		}
	}

	public function shipment_fix()
	{
		$shipments = Shipment::model()->findAll('meta like "%import_billing_id_first%"');
		foreach ($shipments as $shipment) {
			$shipment->mdata['import_billing_id'] = $shipment->mdata['import_billing_id_first'];
			unset($shipment->mdata['import_billing_id_first']);
			$shipment->updateMeta();
		}
	}

	public function recon_shipment()
	{
		$consol = Consol::model()->findByPk(18064);
		$consol->updateWmsDeliveryCost(115, 3503);
	}

	public function edi_template()
	{
		$basicTemplates = EdiJobBasicTemplate::model()->findAll();
		foreach ($basicTemplates as $temp) {
			$metaData = json_decode($temp->meta);
			foreach ($metaData->cost as $index => $cost) {
				if ($cost->org_id == 954) {
					$metaData->cost[$index]->org_id = 964;
				}
			}
			$temp->meta = json_encode($metaData);
			$temp->update('meta');
		}

		$orgTemplates = EdiJobTemplate::model()->findAll();
		foreach ($orgTemplates as $temp) {
			$metaData = json_decode($temp->meta);
			foreach ($metaData->cost as $index => $cost) {
				if ($cost->org_id == 954) {
					$metaData->cost[$index]->org_id = 964;
				}
			}
			$temp->meta = json_encode($metaData);
			$temp->update('meta');
		}
	}

	public function d2z_fix()
	{
		$invoices = ['INV-1171', 'INV-1172', 'INV-1183'];

		foreach ($invoices as $invoice) {
			$recon = Reconciliation::model()->find('invoice_no = :no', array(':no' => $invoice));
			if (empty($recon)) {
				continue;
			}

			foreach ($recon->lines as $line) {
				$shipment = Shipment::model()->find('ref = :ref', array(':ref' => $line->shipment_no));
				if (!empty($shipment)) {
					unset($shipment->mdata['import_billing_id']);
					$shipment->update('meta');
				}
			}
		}
	}

	public function invoice_revert()
	{
		$invoices = Invoice::model()->findAll('status = 8');
		foreach ($invoices as $invoice) {
			// echo $invoice->id . PHP_EOL;
			$no = explode('-', $invoice->no)[0];
			$latest = Invoice::model()->find(array('order' => 'id desc', 'condition' => 'no like :no', 'params' => array(':no' => '%' . $no . '%')));
			if ($latest->status == 8) {
				echo $latest->no . PHP_EOL;
			}
		}
	}

	public function wms_invoice_check()
	{
		$org_ids = [];
		$sql = "SELECT org_id FROM `wms_stock` where qty>0 OR updated>=DATE_SUB(NOW(), INTERVAL 32 DAY) group by org_id";
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		if (!empty($rs)) {
			foreach ($rs as $r) {
				$org_ids[] = $r['org_id'];
			}

			foreach ($org_ids as $oid) {
				$org = Org::model()->findByPk($oid);
				if (!empty($org) && !empty($org->extra['wms_invoice'])) {
					$wmsOrgQuote = WmsOrgQuote::model()->find("org_id=:org_id AND status=1", [':org_id' => $oid]);
					//if not exist OrgQuote, we need to find their parent org quotes, through by_id
					if (empty($wmsOrgQuote) && $org->by > 1) {
						$wmsOrgQuote = WmsOrgQuote::model()->find("org_id=:org_id AND status=1", [':org_id' => $org->by]);
					}
					// if (empty($wmsOrgQuote)) {
					//     echo '100001-WMS ORG Rate Not set UP for' . $org->id . PHP_EOL;
					//     continue;
					// }
					$start_date = '2018-10-06';
					$end_date = '2018-10-12';
					$fd = $start_date;
					$ttl = $end_date;
					$errors = [];
					$aid = $oid;
					$owner = Org::model()->findByPk($aid);
					// fd start from saturday
					$fdts = strtotime($fd);
					$wd = date('N', $fdts);
					if ($wd != 6) {
						$fdts = strtotime($fd . ' -' . ($wd + 1) . ' day');
						$fd = date('Y-m-d', $fdts);
					}
					// generate invoice
					$stot = 0;
					while (strtotime($fd) < strtotime($ttl)) {
						$td = date('Y-m-d', strtotime($fd . ' +7 day'));
						$bd = date('Y-m-d', strtotime($fd . ' +9 day'));
						echo $td . PHP_EOL;
						echo $bd . PHP_EOL;
						if ($td > '2018-10-13') {
							echo 'break' . PHP_EOL;
							break;
						}

						$fd = date('Y-m-d', strtotime($fd . ' +7 day'));
					}
				}
			}
		}
	}

	public function prod_pack()
	{
		$tasks = WmsTask::model()->with('job')->findAll('job.org_id = 1247 AND t.status = 99 AND t.is_request = 1');
		foreach ($tasks as $task) {
			if (count($task->items) === 1) {
				if (empty($task->packTask->mdata['pkg'])) {
					continue;
				}
				$pkg = json_decode($task->packTask->mdata['pkg'], true);
				foreach ($task->items as $item) {
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
					$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = 10 AND qty = 1 AND weight != 0', array(':prod_id' => $stock->prod_id));

					if (!empty($pack)) {
						if (number_format($pkg[0]['wt'], 3) >= $pack->weight) {
							$pack->weight = number_format($pkg[0]['wt'], 3);
							$pack->update('weight');
						} else {
							continue;
						}
					} else {
						$pack = new WmsProdPack;
						$pack->prod_id = $stock->prod_id;
						$pack->type = 10;
						$pack->qty = 1;
						$pack->weight = number_format($pkg[0]['wt'], 3);
						$pack->save();
					}
				}
			}
		}
	}

	public function update_consol_realcost()
	{
		$consol_id = $this->prompt('consol id: ');
		$consol = Consol::model()->findByPk($consol_id);
		$consol->updateCourierRealCost(Org::ORGID_COURIER_D2Z, "33A8Y\d{7}|ZK62\d{6}");
	}

	public function delivery()
	{
		$task_id = $this->prompt('task id: ');
		$task = WmsTask::model()->findByPk($task_id);
		$task = $task->deliveryTask;
		$criteria = new CDbCriteria();
		$criteria->compare('id', $task->mdata['shipment_id']);
		$shipments = Shipment::model()->findAll($criteria);
		$weight1 = 0;
		foreach ($shipments as $shipment) {
			$weight1 += $shipment->weight;
		}
		$weight = $weight1;
		$quote_type = 0;
		if (preg_match('/[\x{4e00}-\x{9fa5}]+/u', $task->mdata['cnee']['state'])) {
			$stot = max(1, $weight) * 6.5 + 1;
			$items = [];
			$items[] = [$task->getNo(), ucwords($task->mainTask->ref), empty($task->mainTask->compl_time) ? $task->compl_time : $task->mainTask->compl_time, $task->getType() . ' - ' . $weight . ' kg', $stot, 1, 1 * $stot];
		} else {
			// $chargecode = ImportChargeCode::model()->find('org_id = :org_id', [':org_id' => $task->job->org_id]);
			// $chargecode = $chargecode ? $chargecode : ImportChargeCode::model()->find('org_id = 114');
			$chargecode = ImportChargeCode::model()->find('org_id = 114');
			if (!empty($chargecode)) {
				$stot = $task->getChargeByChargecode($weight, $task->mdata['cnee']['postcode'], $chargecode->chargecode);
				$items = [];
				$items[] = [$task->getNo(), ucwords($task->mainTask->ref), empty($task->mainTask->compl_time) ? $task->compl_time : $task->mainTask->compl_time, $task->getType() . ' - ' . $weight . ' kg', $stot, 1, 1 * $stot];
			}
		}
		echo $weight . ' ' . $stot;
	}

	public function org_product_expiry_date()
	{
		$org_id = $this->prompt('org id: ');
		$org = Org::model()->findByPk($org_id);
		if (!empty($org)) {
			$stocks = WmsStock::model()->findAll('org_id = :org_id', [':org_id' => $org->id]);
			foreach ($stocks as $stock) {
				if (empty($stock->prod)) {
					continue;
				}
				$pack = WmsProdOrg::model()->find('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $stock->prod->id, ':org_id' => $org->id]);
				if (empty($pack)) {
					$pack = new WmsProdOrg;
					$pack->prod_id = $stock->prod->id;
					$pack->org_id = $org->id;
				}
				$pack->sku = $stock->prod->ean;
				$pack->mdata['exp_acc'] = 'Date';
				$pack->save();
				echo $pack->prod_id . PHP_EOL;
			}
		}
	}

	public function org_stock_expiry_date()
	{
		$org_id = $this->prompt('org id: ');
		$org = Org::model()->findByPk($org_id);
		if (!empty($org)) {
			$stocks = WmsStock::model()->findAll('org_id = :org_id', [':org_id' => $org->id]);
			foreach ($stocks as $stock) {
				if (empty($stock->prod)) {
					continue;
				}

				$first_ledger = WmsStockLedger::model()->find(['condition' => 'stock_id = :stock_id', 'params' => [':stock_id' => $stock->id], 'order' => 'id ASC']);
				$stock->expiry = $first_ledger->taskItem->mdata['ex'];
				$stock->update('expiry');
				echo $stock->id . PHP_EOL;
			}
		}
	}

	public function prod_name()
	{
		$prods = WmsProd::model()->findAll();
		foreach ($prods as $prod) {
			if (preg_match('/\s\s/', $prod->name)) {
				while (preg_match('/\s\s/', $prod->name)) {
					$prod->name = preg_replace('/\s\s/', ' ', $prod->name);
				}
				$prod->update('name');
			}
		}
	}

	public function prod_package()
	{
		$sql = 'select count(*), prod_id, type from wms_prod_pack group by prod_id, type having count(*) > 1';
		$prods = Yii::app()->db->createCommand($sql)->queryAll();

		foreach ($prods as $prod) {
			echo $prod['prod_id'] . ' ' . WmsProdPack::$types[$prod['type']] . PHP_EOL;
		}
	}

	public function fyn_billing_desc_fix()
	{
		$afs = AFInvoiceReconciliation::model()->findAll('history_id in (237, 240, 243, 258)');
		foreach ($afs as $af) {
			$billinglines = BillingLine::model()->findAll('billing_cref = :no', [':no' => $af->invoice_no]);
			foreach ($billinglines as $line) {
				if (!empty($af->mdata['shipno']) && !preg_match('/' . $af->mdata['shipno'] . '/', $line->desc)) {
					$line->desc = $af->mdata['shipno'] . ' ' . $line->desc;
					$line->update('desc');
				}
			}
		}
	}

	public function wms_task_complete()
	{
		$org_id = $this->prompt('org id: ');
		$tasks = WmsTask::model()->with('job')->findAll('t.is_request = 1 AND t.status = 30 AND job.org_id = :org_id', [':org_id' => $org_id]);
		foreach ($tasks as $task) {
			$task->status = 99;
			$log = Log::model()->find(['condition' => 'model = "WmsTask" AND lid = :id', 'params' => [':id' => $task->id], 'order' => 'id DESC']);
			$task->compl_time = $log->time;
			$task->update('status', 'compl_time');
			echo $task->id . PHP_EOL;
		}
	}

	public function invoice_shipment_process()
	{
		$invoices = Invoice::model()->findAll('status in (6,9) AND type in (41,45)');
		foreach ($invoices as $invoice) {
			$num = Invoice::model()->count('pid = :pid AND type in (41,45) AND status in (2,3,7)', [':pid' => $invoice->pid]);
			if ($num == 0) {
				$sp = ShipmentProcess::model()->find('pid = :pid', [':pid' => $invoice->pid]);
				if (!empty($sp) && $sp->status < ShipmentProcess::CONFIRM_PAYMENT) {
					echo $invoice->no . ' confirm from ' . $sp->getStatus() . PHP_EOL;
					$sp->changeStatus(ShipmentProcess::CONFIRM_PAYMENT);
					ShipmentProcess::sendOpNotice($sp->shipment);
				}
			} else {
				echo $invoice->no . ' has other' . PHP_EOL;
			}
		}
	}

	public function chargecode_replace()
	{
		// $map = [
		//     '91010' => '91002',
		//     '91011' => '91005',
		//     '91012' => '91006',
		//     '91013' => '91010',
		//     '91014' => '91011',
		//     '91015' => '91004',
		//     '91016' => '91003',
		//     '91017' => '91999',
		//     '91100' => '91012',
		//     '91101' => '91013',
		//     '91114' => '91014',
		//     '91600' => '91030',
		//     '91650' => '91031',
		//     '91700' => '91032',
		//     '91750' => '91033',
		//     '91755' => '91034',
		//     '91900' => '91021',
		//     '91905' => '91022',
		//     '91910' => '91023',
		// ];

		foreach ($map as $old => $new) {
			$sql = 'UPDATE chargecode SET code = "' . $new . '" WHERE code = "' . $old . '"';
			Yii::app()->db->createCommand($sql)->execute();

			$sql = 'UPDATE charge_item_type SET cost_code = "' . $new . '" WHERE cost_code = "' . $old . '"';
			Yii::app()->db->createCommand($sql)->execute();

			$sql = 'UPDATE billing_line SET charge_code = "' . $new . '" WHERE charge_code = "' . $old . '"';
			Yii::app()->db->createCommand($sql)->execute();
		}
	}

	public function location_label()
	{
		$lines = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
		foreach ($lines as $line) {
			for ($i = 1; $i <= 80; $i++) {
				$storage = WmsLocation::model()->find('name = :name', array(':name' => 'GS-' . $line . '-' . sprintf('%03d', $i)));
				if (empty($storage)) {
					$storage = new WmsLocation;
					$storage->name = 'GS-' . $line . '-' . sprintf('%03d', $i);
					$storage->code = $storage->name;
					$storage->type = 20;
					$storage->wid = 106;
					$storage->status = 1;
					$storage->save();
				}
			}
		}
	}

	public function empty_stock()
	{
		$task_id = $this->prompt('task id: ');
		$task = WmsTask::model()->findByPk($task_id);
		if (!empty($task)) {
			$stocks = WmsStock::model()->findAll('org_id = :org_id', [':org_id' => $task->job->org_id]);

			$items = [];
			foreach ($stocks as $stock) {
				if ($stock->qty <= 0) {
					continue;
				}
				$items[] = [
					'si' => $stock->id,
					'sn' => $stock->prod->name,
					'pq' => '',
					'cq' => '',
					'uq' => $stock->qty,
					'pli' => '',
					'pl' => '',
					'nt' => '',
				];
			}
			$task->new_items = $items;
			$task->save();
		}
	}

	public function fake_stock()
	{
		$task_id = $this->prompt('task id: ');
		$task = WmsTask::model()->findByPk($task_id);
		if (!empty($task)) {
			foreach ($task->items as $item) {
				$ledger = $item->stockLedgers[0];
				$ledger->qty_in = $item->mdata['uq'];
				$ledger->update('qty_in');
				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				$stock->qty = $item->mdata['uq'];
				$stock->update('qty');
			}
		}
	}

	public function billing_invoice_check()
	{
		$models = BillingInvoice::model()->findAll();
		foreach ($models as $model) {
			$model->checkStatus();
		}
	}

	public function send_startrack()
	{
		$consol_id = $this->prompt('consol id: ');
		$con = ImcoConsol::model()->findByPk($consol_id);
		if (!empty($con)) {
			$ss_syd = [];
			$ss_mel = [];
			foreach ($con->shipments as $s) {
				// when create startrack label, we have set one tranship record
				if (preg_match('/^(7RFZ|4XHZ)\d{8}/', $s->ref) && !empty($s->trans) && count($s->trans) == 1 && ($s->cbwf & ImParcel::CBWF_DIRECT_CONSOL_BASE) == 0) {
					// only for startrack shipment and Not sent yet
					// only for we received the shipment which means has been scanned in our warehouse
					// and status is cleared
					$s->status = ImParcel::STATE_LOCAL_ARRIVAL;
					$s->update('status');
					$s->cnee->checkPostcode();
					if (!isset($s->trans[0]->mdata['oid'])) {
						// if upload manifest before, we don't send again
						if (preg_match('/7RFZ\d{8}/i', $s->ref)) {
							$ss_syd[] = $s;
						} elseif (preg_match('/4XHZ\d{8}/i', $s->ref)) {
							$ss_mel[] = $s;
						}
					}
				}
			}

			echo 'syd: ' . count($ss_syd);
			echo 'mel: ' . count($ss_mel);

			$err = [];
			$consolIds = [];
			if (!empty($ss_syd)) {
				$sydAPI = new StarTrackAPI('syd', true);
				$err = array_merge($err, $this->doStartrackManifest($sydAPI, $ss_syd, $consolIds, $con));
			}
			if (!empty($ss_mel)) {
				$melAPI = new StarTrackAPI('mel', true);
				$err = array_merge($err, $this->doStartrackManifest($melAPI, $ss_mel, $consolIds, $con));
			}
			if (!empty($consolIds)) {
				// ImcoConsol::updateImportConsoleBilling($consolIds);
			}
		}
	}

	public function doStartrackManifest($api, $ss, &$consolIds, $cn)
	{
		$err = [];
		$r = $api->createOrderFromShipments($ss, $cn->no);
		if (!empty($r->order)) {
			$cn->mdata['startracksent'] = 1;
			$cn->save();
			$oid = $r->order->order_id;
			// because aupost returned shipment is not the same order with our sending order
			// so here we need to order by HBN again
			$auPostShipments = [];
			foreach ($r->order->shipments as $aushipment) {
				$auPostShipments[$aushipment->shipment_reference] = $aushipment;
			}
			$transaction = Yii::app()->db->beginTransaction();
			try {
				foreach ($ss as $i => $s) {
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
					if ($s->consol_id > 0) {
						array_push($consolIds, $s->consol_id);
					}
				}
				$consolIds = array_unique($consolIds);
				$transaction->commit();
			} catch (Exception $ex) {
				$transaction->rollback();
				Log::log2file("Startrack Manifest=>" . $cn->no . "=>" . $ex->getMessage(), "transaction_err_log", "transaction");
				throw $ex;
			}
		} else {
			foreach ($api->err as $e) {
				$msg = $e->message;
				if (!empty($e->field) && preg_match('/shipments\[(\d+)\]/', $e->field, $m)) {
					$msg .= ': ' . $ss[$m[1]]->hbn;
				}
				$err[] = $msg;
			}
		}
		return $err;
	}

	public function clear_fake_stock()
	{
		$stocks = WmsStock::model()->findAll('org_id = ' . Org::ORGID_3PL_COBAYER);
		foreach ($stocks as $stock) {
			//stock location
			$sql = 'SELECT location_id as lid, SUM(qty_in) - SUM(qty_out) as qty FROM wms_stock_ledger WHERE stock_id = ' . $stock->id . ' GROUP BY location_id';
			$rs = Yii::app()->db->createCommand($sql)->queryAll();
			$slids = [0];
			foreach ($rs as $r) {
				$sl = WmsStockLocation::model()->find('location_id = :lid AND stock_id = :sid', [':lid' => $r['lid'], ':sid' => $stock->id]);
				if (empty($sl)) {
					$sl = new WmsStockLocation;
					$sl->location_id = $r['lid'];
					$sl->stock_id = $stock->id;
				}
				if ($sl->qty != floatval($r['qty'])) {
					$old_qty = empty($sl->qty) ? 0 : $sl->qty;
				} else {
					$old_qty = null;
				}
				$sl->qty = $r['qty'];
				$sl->save();
				if (!empty($old_qty)) {
					echo 'wsl: ' . $sl->id . ' ' . $sl->loc->id . ' old: ' . $old_qty . ' new: ' . $sl->qty . PHP_EOL;
				}
				$slids[] = $sl->id;
			}
			$rs = WmsStockLocation::model()->findAll('stock_id = :sid AND t.id NOT IN (' . implode(',', $slids) . ')', [':sid' => $stock->id]);
			foreach ($rs as $r) {
				$r->qty = 0;
				$r->save();
				echo 'wsl: ' . $r->id . ' ' . $r->loc->id . ' empty' . PHP_EOL;
			}

			//reserve qty
			$sql = 'SELECT SUM(qty) as qty FROM wms_stock_location sl INNER JOIN wms_location l on sl.location_id = l.id WHERE (sl.location_id = 4 OR l.pid = 4) AND stock_id = ' . $stock->id;
			if ($stock->qty_res != floatval(Yii::app()->db->createCommand($sql)->queryScalar())) {
				$old_qty = $stock->qty_res;
			} else {
				$old_qty = null;
			}
			$stock->qty_res = Yii::app()->db->createCommand($sql)->queryScalar();
			if (!empty($old_qty)) {
				echo 'ws: ' . $stock->id . ' oldr: ' . $old_qty . ' newr: ' . $stock->qty_res . PHP_EOL;
			}

			//stock qty
			$sql = 'SELECT SUM(qty) as qty FROM wms_stock_location sl INNER JOIN wms_location l on sl.location_id = l.id WHERE qty > 0 AND sl.location_id > 99 AND l.pid NOT IN (2,3,4,5,6) AND stock_id = ' . $stock->id; //AND l.pid > 99
			if ($stock->qty != floatval(Yii::app()->db->createCommand($sql)->queryScalar())) {
				$old_qty = $stock->qty;
			} else {
				$old_qty = null;
			}
			$stock->qty = Yii::app()->db->createCommand($sql)->queryScalar();
			if (!empty($old_qty)) {
				echo 'ws: ' . $stock->id . ' old: ' . $old_qty . ' new: ' . $stock->qty . PHP_EOL;
			}

			$stock->save();
		}
	}

	public function edi_job_template_rate()
	{
		$orgTemplates = EdiJobTemplate::model()->findAll();
		foreach ($orgTemplates as $temp) {
			$metaData = json_decode($temp->meta);
			foreach ($metaData->invoice as $index => $invoice) {
				if ($invoice->rate == 0) {
					$metaData->invoice[$index]->rate = 1;
				}
			}
			$temp->meta = json_encode($metaData);
			$temp->update('meta');
		}
	}

	public function billingline_3pl()
	{
		$lines = BillingLine::model()->findAll('billing_ref like "%3PL%"');
		foreach ($lines as $line) {
			$consol = Consol::model()->find('no = :no', [':no' => $line->billing_ref]);
			$recModel = Reconciliation::model()->find('invoice_no = :invno AND client_type = 2', [':invno' => $line->billing_cref]);
			if (!empty($consol) && !empty($recModel)) {
				$consol->updateWmsDeliveryCost(Org::ORGID_COURIER_STARTRACK, $recModel->id);
			}
		}
	}

	public function wmstask_out()
	{
		$tasks = WmsTask::model()->findAll('meta like "%outtaskid%"');
		foreach ($tasks as $task) {
			if (!is_array($task->mdata['outtaskid'])) {
				$id = $task->mdata['outtaskid'];
				$task->mdata['outtaskid'] = [];
				$task->mdata['outtaskid'][] = $id;
				$task->update('meta');
			}
		}
	}

	public function prod_oversize()
	{
		$packs = WmsProdPack::model()->findAll();
		foreach ($packs as $pack) {
			if ((!empty($pack->dims['w']) && $pack->dims['w'] >= 130) || (!empty($pack->dims['d']) && $pack->dims['d'] >= 130) || (!empty($pack->dims['h']) && $pack->dims['h'] >= 170)) {
				$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND qty > 0', [':prod_id' => $pack->prod_id]);
				if (!empty($stocks)) {
					foreach ($stocks as $stock) {
						echo $stock->prod->name . '  ' . $pack->getType() . '  ' . $stock->qty . PHP_EOL;
					}
				} else {
					echo $pack->prod->name . '  nostock' . PHP_EOL;
				}
			}
		}
	}

	public function wmstask_exp_recover()
	{
		$task_id = $this->prompt('task id: ');
		$task = WmsTask::model()->findByPk($task_id);

		foreach ($task->actionTask->items as $item) {
			if (empty($item->mdata['ex'])) {
				$sl = WmsStockLedger::model()->find('ti_id = :ti_id', [':ti_id' => $item->id]);
				if (!empty($sl->stock)) {
					$item->mdata['ex'] = @$sl->stock->expiry;
					$item->mdata['bn'] = @$sl->stock->batch;
					$item->update('meta');
					echo $item->id . ' ' . $item->mdata['gn'] . ' ' . $item->mdata['ex'] . ' ' . $item->mdata['bn'] . PHP_EOL;
				}
			}
		}
	}

	public function invoice_name_addr()
	{
		$to_id = $this->prompt('org id: ');
		$invs = Invoice::model()->findAll('to_id = :to_id AND dpmt = 10 AND status IN (2,3,7)', [':to_id' => $to_id]);
		foreach ($invs as $inv) {
			$inv->mdata['name'] = $inv->cust->name;
			$inv->mdata['address'] = $inv->cust->getAddress();
			$inv->update('meta');
		}
	}

	public function choose_shipment()
	{
		$shipment = Shipment::model()->find('hbn = "ECN1474002369"');
		$chargeCode = 5283;

		$resp = new stdClass;
		$resp->status = 1;
		$resp->msg = '';

		$chargeCodeInfo = ImportChargeCode::model()->find('chargecode = :ccode', [':ccode' => $chargeCode]);
		$chargeCodeSetUp = $chargeCodeInfo->hasChargeSetUp($shipment);
		if (!$chargeCodeSetUp['status']) {
			$resp->msg = '[90010] - Shipment ' . $chargeCodeSetUp['msg'];
			$resp->status = 0;
			return $resp;
		}
		if (!empty($chargeCodeInfo)) {
			// get related charge code information
			// get courier list
			$couriers = $chargeCodeInfo->couriersObj;
			$selectedOrgRates = array();

			if (!empty($couriers)) {

				// for mixed aupost and fastway service, if the address is pobox | parcel locker, we need to ignore using fastway.
				if (isset($shipment->cnee->address) && Addr::checkIsPoBox($shipment->cnee->address)) {
					if (in_array(52, $couriers)) {
						$key = array_search(52, $couriers);
						unset($couriers[$key]);
					}
				}
				$left_couriers = $couriers;

				while (!empty($left_couriers)) {
					$resp->status = 1;
					$resp->msg = '';
					$selectedOrgRates = [];

					// set cheap price courier
					foreach ($left_couriers as $courier) {
						// check which courier is cheap
						// step 1
						// get Org ID base on OrgRate ID
						$orgRate = OrgRate::model()->findByPk($courier);
						if (empty($orgRate)) {
							continue;
						}

						$orgId = $orgRate->org_id;

						//eparcel single only
						if (!empty($chargeCodeInfo->mdata['eparcel_single']) && $shipment->pkg > 1 && in_array($courier, [101, 110, 111])) {
							continue;
						}

						// check courier's minimum conditions
						// check max weight  and max dimension
						$org = Org::model()->findByPk($orgId);
						if (empty($org)) {
							continue;
						}

						$maxWeight = 0; // by KG
						if (isset($org->extra['maxwt'])) {
							$maxWeight = $org->extra['maxwt'];
						}
						$maxDim = 0; // by CM for any maximum of D, H , W
						if (isset($org->extra['maxdim'])) {
							$maxDim = $org->extra['maxdim'];
						}
						$units = $shipment->pkg <= 0 ? 1 : $shipment->pkg;

						$chargeWeight = $shipment->weight;
						if ($shipment->type == 10) {
							if (empty($chargeCodeInfo->charge_wt)) {
								$chargeWeight = $shipment->chargeWeight();
							}
						}
						if ($maxWeight > 0 && number_format($chargeWeight / $units, 2) > number_format($maxWeight, 2)) {
							// over the courier's max weight
							continue;
						}

						if ($maxDim > 0) {
							$w = 0;
							if (isset($shipment->mdata['dim']['w'])) {
								$w = $shipment->mdata['dim']['w'];
							}

							$h = 0;
							if (isset($shipment->mdata['dim']['h'])) {
								$h = $shipment->mdata['dim']['h'];
							}

							$d = 0;
							if (isset($shipment->mdata['dim']['d'])) {
								$d = $shipment->mdata['dim']['d'];
							}

							$sMaxDim = max($w, $h, $d);
							if ($sMaxDim > $maxDim) {
								// over the courier's max dimension
								continue;
							}
						}
						$selectedOrgRates[] = $orgRate;
					}
					// check courier minimum conditions
					// finally set the cheapest courier
					$imcoConsole = new ImcoConsol();
					$minCost = 99999; // in order to get minimum one
					$cheapOrgRate = null;
					$getCheapOrgRateError = '';
					foreach ($selectedOrgRates as $orgrate) {
						if ($orgrate->org_id == 199) {
							$cheapOrgRate = $orgrate;
							break;
						}
						$rt = ChooseShipment::courierCanDelivery($orgrate->org_id, $shipment);
						if (!$rt->success) {
							$getCheapOrgRateError .= implode(', ', $rt->error) . ' | ';
							continue;
						}

						// get cost based on org rate
						$cost = $imcoConsole->getCourierCostPrice($orgrate, $shipment->cnee->postcode, $chargeWeight, $units, true, $shipment->cnee->suburb);
						echo $cost . '---' . $orgrate->id . PHP_EOL;
						if ($cost > 0 && $cost < $minCost) {
							$minCost = $cost;
							$cheapOrgRate = $orgrate;
						}
						if ($cost == 0) {
							$getCheapOrgRateError .= $orgrate->org_id . '(' . $orgrate->org->name . ')' . ' cost not set for postcode : ' . $shipment->cnee->postcode . ' weight :' . $shipment->weight . ' | ';
						}
					}

					if (sizeof($left_couriers) > 1) {
						if (($key = array_search($cheapOrgRate->id, $left_couriers)) !== false) {
							unset($left_couriers[$key]);
							continue;
						}
					}
				}
			}
		}
	}

	public function fix_3pl_courier()
	{
		$recModel = Reconciliation::model()->findByPk($this->prompt('Rec ID:'));
		$consolids = [];

		if (!empty($recModel)) {
			foreach ($recModel->lines as $line) {
				$shipment = Shipment::model()->find('ref = :ref', [':ref' => $line->shipment_no]);
				if (!empty($shipment)) {
					$consolids[] = $shipment->consol_id;
				}
			}
		}

		foreach ($consolids as $cid) {
			$consol = Consol::model()->findByPk($cid);
			if (!empty($consol)) {
				if ($consol->owner_id == 114) {
					$consol->updateWmsDeliveryCost(Org::ORGID_COURIER_STARTRACK, $recModel->id);
				}
			}
		}
	}

	public function check_unscan()
	{
		$status = $this->prompt('Status: ');
		$created = $this->prompt('Created: ');
		$shipments = ImParcel::model()->findAll('created >= :created AND status = :status', [':status' => $status, ':created' => $created]);
		foreach ($shipments as $s) {
			if ($s->scan_no != $s->pkg - $s->scanCount() && $s->consol_id != 0) {
				echo $s->ref . ' ' . $s->scan_no . ' ' . ($s->pkg - $s->scanCount()) . PHP_EOL;
			}
		}
	}

	public function check_chargecode()
	{
		$chargecode = ImportChargeCode::model()->find('chargecode = "0931"');
		$shipment = ImParcel::model()->find('ref = "7RFZ50012306"');
		$result = $chargecode->hasChargeSetUp($shipment);
		echo json_encode($result);
	}

	// 大货架标激活
	public function copy_location()
	{
		$locs = WmsLocation::model()->findAll('name like "G1%"');
		foreach ($locs as $loc) {
			$new_loc = new WmsLocation;
			$new_loc->attributes = $loc->attributes;
			$new_loc->name = str_replace('G1', 'G2', $new_loc->name);
			$new_loc->code = str_replace('G1', 'G2', $new_loc->code);
			$new_loc->save();
		}
	}

	public function hunter_cost()
	{
		$imconsol = new ImcoConsol;
		$shipment = ImParcel::model()->find('hbn = :hbn', [':hbn' => $this->prompt('Shipment: ')]);
		$org_rate = OrgRate::model()->find('org_id = :org_id', [':org_id' => 140]);
		echo $imconsol->getHunterCostPrice($org_rate, $shipment->cnee->suburb, $shipment->cnee->postcode, $shipment->chargeWeight(), $shipment->pkg);
	}

	public function scan_record_fix()
	{
		$shipments = ImParcel::model()->findAll('(ref LIKE "DQ%" OR ref LIKE "UDW%") AND scan != ""');
		foreach ($shipments as $shipment) {
			$shipment->scan_no = $shipment->pkg - $shipment->scanCount();
			$shipment->update('scan_no');
		}
	}

	public function ausletter_fix()
	{
		$shipment = ImParcel::model()->find('hbn = :ref OR ref = :ref', [':ref' => $this->prompt('Shipment: ')]);
		$type = ImParcel::$cbwfs;  //['small letter','Large Letters up to 125g','Large Letters 125g-250g','Large Letters 250g-500g'];
		$dim = [];
		$dim[] = floatval($shipment->mdata['dim']['h']);
		$dim[] = floatval($shipment->mdata['dim']['w']);
		$dim[] = floatval($shipment->mdata['dim']['d']);
		asort($dim);
		$tmp = [];
		$typeTemp = 0;
		foreach ($dim as $value) {
			$tmp[] = $value;
		}
		$weight = $shipment->weight;
		$shipment->ref = 'LET' . sprintf('%07s', substr($shipment->id, -7));
		// later we may need to consider more on the width, length, as h<=0.5 don't mean it's a small letter.
		if ($tmp[0] <= 0.5 && $tmp[1] <= 13 && $tmp[2] <= 24 && ($shipment->getLetterService() != 1)) {
			if ($weight <= 0.250) {
				$shipment->note = $type[1];
				$typeTemp = 1;
			} else {
				$shipment->note = $type[8];
				$typeTemp = 8;
			}
		} else {
			if ($weight <= 0.125) {
				$shipment->note = $type[2];
				$typeTemp = 2;
			} else if ($weight <= 0.250) {
				$shipment->note = $type[4];
				$typeTemp = 4;
			} else {
				$shipment->note = $type[8];
				$typeTemp = 8;
			}
		}
		$shipment->mdata['letter_aupost'] = $typeTemp;
		$shipment->cbwf = $shipment->cbwf | $typeTemp;
		$shipment->update(['ref','note','cbwf']);
		$shipment->updateMeta();
	}

	public function heldreason_fix()
	{
		$shipments = ImParcel::model()->findAll('consol_id = 31255');
		foreach ($shipments as $shipment) {
			$shipment->heldReason();
			$shipment->update('bwf');
		}
	}

	public function eps_fix()
	{
		//invoice
		$consol = ImcoConsol::model()->findByPk($this->prompt('Consol id: '));
		$owner = Org::model()->findByPk(1206);
		// in case invoice existing , we just update
		$inv = Invoice::model()->find('consol_id = :cid', [':cid' => $consol->id]);

		// in case invoice has been frozen, we can't change it again
		$invoiceNo = '';
		$oldPaymentLines = '';
		if (!empty($inv)) {
			if ($inv->isInvoiceClosed()) {
				$inv->createCreditForMe();
				$oldPaymentLines = $inv->payments;
				$invoiceNo = $inv->no;
				$inv = null;
			}
		}
		if (empty($inv)) {
			$inv = new Invoice;
			$inv->type = 10;
			$inv->dpmt = Invoice::DPMT_IMPORT;
			$inv->to_id = 1206; // for EPS client
			$inv->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY; // default set Sydney as warehouse
			$inv->ref = 'ck1-ep' . date('Ymd', strtotime($consol->created));
			$inv->currency = 1;
			if (!empty($invoiceNo)) {
				$inv->no = Invoice::genNewInvoiceNo($invoiceNo);
			}               // $invoiceNo . '-1';
			$inv->consol_id = $consol->id;
			$inv->status = Invoice::INVOICE_STATUS_PENDING;
		}
		$inv->date = date('Y-m-d', strtotime($consol->created));
		$inv->due = $inv->date;
		$inv->save();
		if ($inv->getErrors()) {
			$inv->date = date('Y-m-d');
			$inv->save();
		}
		$items = [];
		$tot = 0;
		$ss = $consol->shipments;
		$chargeCode = '';
		if ($consol->owner_id == 1206) {
			$chargeCode = 8271;
		}
		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach ($ss as $i => $p) {
				$pc = trim($p->cnee->postcode);
			
				// currently we hardcode here , we always use charge code 5813
				// so we create invoice by charge code now
				$amt = $p->getChargeByChargecode($chargeCode, true);
				$p->mdata['charge_client_amount']= number_format($amt, 4, '.', '');
				$p->mdata['charge_client_weight']= number_format($p->weight, 2, '.', '');
				$p->updateMeta();
				$zoneMap = ZoneMap::model()->find('chargecode_id = 15 AND zone_id = 1 AND pc_lo <= :p AND pc_hi >= :p', [':p' => $pc]);
				$zoneCode = 'N1';
				if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
					$zoneCode = $zoneMap['z1'];
				}
				if (!empty($p->tempChargeweight)) {
					$p->weight=$p->tempChargeweight;
				}  //to record the break weight
				$items[] = [$p->ref, $p->getDesc().'    ' . $zoneCode, $p->pkg, $p->weight, $p->cbm, $amt,$p->cnee->postcode];
				$tot += $amt;

				// in case parcel with insurance
				// we add insurance value to total value
				if ($p->insurance > 0) {
					$insuranceRatio = 1;
					if (isset($owner->extra['insurance_invoice_ratio'])) {
						$insuranceRatio = floatval($owner->extra['insurance_invoice_ratio']);
					}
					$invInsurance = round($p->insurance * ($insuranceRatio / 100), 2);
					$p->mdata['insurance_charge_client']=$invInsurance;
					$p->updateMeta();
					$items[] = [$p->ref, 'Insurance Fee',0, 0, 0, $invInsurance];
					$tot += $invInsurance;
				}
			}
			$transaction->commit();
		} catch (Exception $ex) {
			$transaction->rollback();
			Log::log2file("Manifest(1206) create Invoice".$con->no."=>".$ex->getMessage(), "transaction_err_log", "transaction");
			throw $ex;
		}

		$inv->refresh();
		$il = new InvLine;
		$il->inv_id = $inv->id;
		$il->ccode = 'EPA';
		$il->mdata['items'] = $items;
		$il->det = $consol->no;
		$il->fid = $consol->id;
		$il->model = 'ImcoConsol'; // invoice connected with console directly
		$il->amount = number_format(round(round($tot * 1000)/1000,3), 3, '.', '');
		$il->qty = 1;
		$il->save();
		$inv->refresh();
		$inv->getTotal();
		$inv->consol_id = $consol->id;

		$inv->mdata['name'] = $owner->name;
		$inv->mdata['address'] = $owner->getAddress();
		$inv->mdata['payterm'] = empty($owner->extra['payterm'])? 'COD' : $owner->extra['payterm'].' days';

		if (!empty($oldPaymentList)) {
			$inv->applyPayments($oldPaymentList);
			$inv->getTotal();
			$inv->checkPaid();
		}

		$inv->save();

		// update console related aupost courier cost
		ImcoConsol::updateImportConsoleBilling([$consol->id]);

		// check to see if something wrong when create invoice or invoice line
		$errorsInv = $inv->getErrors();
		if (!empty($errorsInv)) {
			echo 'Failed to create invoice : ' . json_encode($errorsInv) . PHP_EOL;
		}
		$errorsInvLine = $il->getErrors();
		if (!empty($errorsInvLine)) {
			echo 'Failed to create invoice : ' . json_encode($errorsInvLine) . PHP_EOL;
		}
		$error = implode(PHP_EOL, array_merge($errorsInv, $errorsInvLine));
		if (!empty($error)) {
			echo 'Failed to create invoice : ' . $error . PHP_EOL;
		} else {
			echo 'Invoice ' . $inv->no . ' issued' . PHP_EOL;
		}
	}

	public function get_user_main()
	{
		$users = User::model()->findAll();
		foreach ($users as $user) {
			$orgs = Org::model()->findAll('id = :oid OR `by` = :oid', [':oid' => $user->org_id]);
			$jobs = WmsJob::model()->with('customer')->findAll('customer.id = :oid OR customer.`by` = :oid', [':oid' => $user->org_id]);

			if (sizeof($orgs) > 1 && sizeof($jobs) > 0 && $user->type && $user->org_id != 1) {
				echo $user->id . ' ' . $user->email . ' ' . sizeof($orgs) . ' ' . sizeof($jobs) . PHP_EOL;
			}
		}
	}

	public function check_scantime()
	{
		$shipments = Shipment::model()->findAll('consol_id = :consol_id', [':consol_id' => $this->prompt('Consol id: ')]);
		foreach ($shipments as $shipment) {
			if (empty($shipment->getScanTime())) {
				echo $shipment->ref . PHP_EOL;
			}
		}
	}

	public function dw_dup()
	{
		$invoices = Invoice::model()->findAll('to_id = 1308 AND status NOT IN (8, 10) AND type = 70');

		$shipments = [];
		foreach ($invoices as $invoice) {
			foreach ($invoice->lines as $line) {
				$task = WmsTask::model()->findByPk($line->fid);
				foreach ($task->mdata['shipment_id'] as $id) {
					if (empty($shipments[$id])) {
						$shipments[$id] = [];
					}
					$shipments[$id][] = $invoice->no;
				}
			}
		}

		foreach ($shipments as $id => $invoices) {
			if (sizeof($invoices) > 1) {
				$shipment = Shipment::model()->findByPk($id);
				echo $shipment->ref . ' :' . implode(' ', $invoices) . PHP_EOL;
			}
		}
	}

	public function rts_fix()
	{
		$invoices = Invoice::model()->findAll('type = 37');
		foreach ($invoices as $invoice) {
			foreach ($invoice->lines as $line) {
				$shipment = Shipment::model()->find('hbn = :hbn', [':hbn' => $line->mdata['items'][0][0]]);
				$line->mdata['items'][0][0] = $shipment->note;
				$line->save();
			}
		}
	}

	public function custom_fix()
	{
		$shipments = Shipment::model()->with('consol')->findAll('consol.no IN ("DW19042903MEL", "DW19042902SYD")');
		foreach ($shipments as $shipment) {
			foreach ($shipment->tracks as $track) {
				if ($track->type == 55) {
					echo $shipment->ref . PHP_EOL;
					$track->mdata = $shipment->mdata;
					$track->activity = 'Customs held' . $shipment->getHeldReasons();
					$track->save();
					echo $track->id . ' ' . json_encode($track->getErrors()) . PHP_EOL;
				}
			}
		}
	}

	public function stock_update_fix()
	{
		$stocks = WmsStock::model()->findAll('org_id = 2601');
		foreach ($stocks as $stock) {
			$ledger = WmsStockLedger::model()->with('stock')->find(['condition' => 'stock.org_id = 1084 AND stock.prod_id = :prod_id AND t.qty_in = :qty', 'params' => [':prod_id' => $stock->prod_id, ':qty' => $stock->qty], 'order' => 't.id ASC']);
			if (!empty($ledger)) {
				$stock->updated = $ledger->ts;
				$stock->no_updated = true;
				$stock->update('updated');
			}
		}
	}

	public function broker_fix()
	{
		$billings = BillingInvoice::model()->with('af')->findAll('af.history_id = 1005');
		foreach ($billings as $billing) {
			$billing->checkStatus();
		}
	}

	public function ot_mhf_fix()
	{
		$lines = BillingLine::model()->findAll('`desc` like "%manual%" AND meta like "%ot_inv%" AND actual_amount > 10');
		foreach ($lines as $line) {
			$inv = Invoice::model()->findByPk($line->mdata['ot_inv']);
			echo $line->billing_ref . ' ' . $line->actual_amount . ' ' . $inv->no . ' ' . $inv->getStatus() . PHP_EOL;
		}
	}

	public function cleared_log_fix()
	{
		$shipments = Shipment::model()->findAll('ref in ("AMQ5214399","AMQ5229213","AMQ5227182","AMQ5227178","AMQ5226575","BD0006781777","DKC000001829","AMQ5223966","AMQ5223725","AMQ5223546","AMQ5223543","AMQ5223529","AMQ5223499","AMQ5223456","AMQ5223200","AMQ5221162","AMQ5221160","AMQ5221159","AMQ5221148","AMQ5221105","AMQ5221082","AMQ5221081","AMQ5221074","AMQ5221043","AMQ5221039","AMQ5220973","AMQ5220841","AMQ5208489","AMQ5205797","AMQ5205784","AMQ5205772","AMQ5205758","AMQ5205756","AMQ5203533","AMQ5203531","AMQ5203525","AMQ5203510","AMQ5203506","AMQ5203500","AMQ5203497","AMQ5203495","AMQ5203489","AMQ5203471","AMQ5203468","AMQ5203465","AMQ5203462","AMQ5203459","AMQ5203404","AMQ5203396","AMQ5203395","AMQ5203394","AMQ5203393","AMQ5203392","AMQ5203387","AMQ5203375","AMQ5203354","AMQ5200244","AMQ5198643","AMQ5198126","AMQ5198088","AMQ5223822","AMQ5223781","AMQ5203527","AMQ5203494","AMQ5203476","DKC000002006","DKC000002004","AMQ5217348","7RFZ50012800","DKC000001889","7RFZ50012705","7RFZ50012766","BD0006737500","ML0000676208")');

		foreach ($shipments as $shipment) {
			if ($shipment->status != 60) {
				$temp_status = $shipment->status;

				$shipment->status = 60;
				$shipment->update('status');

				$shipment->status = $temp_status;
				$shipment->update('status');
			} else {
				$shipment->status = 60;
				$shipment->update('status');
			}
		}
	}

	public function fix_scan_no()
	{
		$shipments = ImParcel::model()->findAll(['condition' => 'scan_no != 0', 'order' => 'id desc']);
		foreach ($shipments as $shipment) {
			if ($shipment->pkg - $shipment->scanCount() != $shipment->scan_no) {
				$shipment->scan_no = $shipment->pkg - $shipment->scanCount();
				$shipment->update('scan_no');
			}
		}
	}

	private function selectServiceByChargeCode($chargeCode, &$shipment)
	{
		$resp = new stdClass;
		$resp->status = 1;
		$resp->msg = '';

		$chargeCodeInfo = ImportChargeCode::model()->find('chargecode = :ccode', [':ccode' => $chargeCode]);
		if (!empty($chargeCodeInfo)) {
			// get related charge code information
			// get courier list
			$couriers = $chargeCodeInfo->couriersObj;
			$selectedOrgRates = [];

			if (!empty($couriers)) {			
				// fastway not support po box,
				if (isset($shipment->cnee->address)&&Addr::checkIsPoBox($shipment->cnee->address)) {
					if (in_array(52, $couriers)) {
						$key= array_search(52, $couriers);
						unset($couriers[$key]);
					}
							   
					if ((in_array(38, $couriers))) { //startrack
						$key= array_search(38, $couriers);
						unset($couriers[$key]);
						if (empty($couriers)) {
							$resp->status = 0;
							$resp->msg = '[60001] - Po Box not supported by Startrack' ;
						}
					}
				}
														
												 
				//for fastway and eparcel mixed service
				if ((in_array(52, $couriers)&&in_array(101, $couriers))&& preg_match('/wa/i', $shipment->cnee->state)) {
					$key= array_search(52, $couriers);
					unset($couriers[$key]);
				}
							   
				$left_couriers=$couriers;
							
				while (!empty($left_couriers)) {
					$resp->status = 1;
					$resp->msg = '';
					$selectedOrgRates=[];
			
					// set cheap price courier
								$validError='';//record the error during validation
				foreach ($left_couriers as $courier) {
					// check which courier is cheap
					// step 1
					// get Org ID base on OrgRate ID
					$orgRate = OrgRate::model()->findByPk($courier);
					if (empty($orgRate)) {
						continue;
					}
					$orgId = $orgRate->org_id;

					// check courier's minimum conditions
					// check max weight  and max dimension
					$org = Org::model()->findByPk($orgId);
					if (empty($org)) {
						continue;
					}
					$maxWeight = 0; // by KG
					if (isset($org->extra['maxwt'])) {
						$maxWeight = $org->extra['maxwt'];
					}
					$maxDim = 0; // by CM for any maximum of D, H , W
					if (isset($org->extra['maxdim'])) {
						$maxDim = $org->extra['maxdim'];
					}
					$units = $shipment->pkg <= 0 ? 1 : $shipment->pkg;
									   
					
					$chargeWeight=$shipment->weight;
					if ($shipment->type==10) {
						if (empty($chargeCodeInfo->charge_wt)) {
							$chargeWeight=$shipment->chargeWeight();
						}
					}
										
					//old mode weight valid
					if ($maxWeight > 0 && $chargeWeight / $units > $maxWeight) {
						// over the courier's max weight
						$validError.=$shipment->hbn . '- maxWeight over  '. $orgRate->org->name. ' limit |';
						continue;
					}
					//old mode weight valid
					//new mode weight valid
					$isWeightValid=true;
					if (!empty($shipment->packs)&&$maxWeight>0) {
						foreach ($shipment->packs as $i=>$pack) {
							if ($pack['weight']>$maxWeight) {
								$isWeightValid=false;
								$validError.=$shipment->hbn.'- packs index:'.($i+1). ' weight '.$pack['weight'].'Kg over '.$orgRate->org->name. ' limit |';
							}
						}
						if (!$isWeightValid) {
							continue;
						}
					}
					//new mode weight valid
							  
					//**for letter validation  according to the org, w, h, d  weight
					if ($orgRate->org_id==Org::ORGID_COURIER_AUSLETTER) {
						//for letter we only support  one packs
						if ($shipment->pkg>1) {
							$validError.=$shipment->hbn . '- letter only support 1 Pack !';
							continue;
						}
											
						//support only mode
						if (!empty($shipment->mdata['dim']['w'])&&!empty($shipment->mdata['dim']['h'])&&!empty($shipment->mdata['dim']['d'])) {
							$letter_dim=[];
							$letter_dim[]=$shipment->mdata['dim']['w'];
							$letter_dim[]=$shipment->mdata['dim']['h'];
							$letter_dim[]=$shipment->mdata['dim']['d'];
							if (!$this->valid_dim_weight($letter_dim, $orgRate, $shipment->weight)) {
								$validError.=$shipment->hbn.'-letter dim over requirement |';
								continue;
							}
						} elseif (isset($shipment->packs[0])) {
							//we can check the packs[0] for width, length,height;
							$letter_dim=[];
							$letter_dim[]=$shipment->packs[0]['width'];
							$letter_dim[]=$shipment->packs[0]['length'];
							$letter_dim[]=$shipment->packs[0]['height'];
							if (!$this->valid_dim_weight($letter_dim, $orgRate, $shipment->weight)) {
								$validError.=$shipment->hbn.'-letter dim over requirement |';
								continue;
							}
						} else {
							$validError.='Letter Service Require h d w ';
							continue;
						}
					}
						
					if ($maxDim > 0) {
						//old mode----------
						$w = 0;
						if (isset($shipment->mdata['dim']['w'])) {
							$w =  $shipment->mdata['dim']['w'];
						}
						$h = 0;
						if (isset($shipment->mdata['dim']['h'])) {
							$h =  $shipment->mdata['dim']['h'];
						}
						$d = 0;
						if (isset($shipment->mdata['dim']['d'])) {
							$d =  $shipment->mdata['dim']['d'];
						}
						$sMaxDim = max($w, $h, $d);
						if ($sMaxDim > $maxDim) {
							// over the courier's max dimension
							continue;
						}
						//old mode-----
						//new mode-----
						if (!empty($shipment->packs)) {
							$isDimValid=true;
							foreach ($shipment->packs as $pack) {
								if (max($pack['width'], $pack['height'], $pack['length'])>$maxDim) {
									$isDimValid=false;
									$validError .= $shipment->hbn.'-dim over '. $orgRate->org_id . '(' . $orgRate->org->name . ')' . ' max Requirement:'.$maxDim.'cm | ';
								}
							}
							if (!$isDimValid) {
								continue;
							}
						}
						//new mode-----
					}
					$selectedOrgRates[] = $orgRate;
				}
								
					if (empty($selectedOrgRates)&&!empty($validError)) {
						$resp->status = 0;
						$resp->msg = '[60001] - ' . $shipment->hbn . ' failed : ' .  $validError ;
						return $resp;
					}

					// check courier minimum conditions

					// finally set the cheapest courier
					$imcoConsole = new ImcoConsol();
					$minCost = 99999; // in order to get minimum one
					$cheapOrgRate = null;
					$getCheapOrgRateError = '';
					foreach ($selectedOrgRates as $orgrate) {
						if($orgrate->org_id == Org::ORGID_COURIER_PICKUP){
							$cheapOrgRate = $orgrate;
							break;
						}
						$rt = ChooseShipment::courierCanDelivery($orgrate->org_id, $shipment);
						if (!$rt->success) {
							$getCheapOrgRateError .= implode(', ', $rt->error) . ' | ';
							continue;
						}

						// get cost based on org rate
						$cost = $imcoConsole->getCourierCostPrice($orgrate, $shipment->cnee->postcode, $chargeWeight, ($shipment->pkg > 0 ? $shipment->pkg : 1), true, $shipment->cnee->suburb);
						//adding a new logic when mixed fastway and eparcel, fastway cost  /1.2 for chargecode(3600/6315/6085/4063);
						if ($orgrate->org_id==115&&(!empty($shipment->mdata['chargecode'])&&in_array($shipment->mdata['chargecode'], [3600,6315,6085,4063]))) {
							$cost=$cost/1.2;
						}
						if ($cost > 0 &&  $cost < $minCost) {
							$minCost = $cost;
							$cheapOrgRate = $orgrate;
						}
						if ($cost == 0) {
							$getCheapOrgRateError .= $orgrate->org_id . '(' . $orgrate->org->name . ')' . ' cost not set for postcode : ' . $shipment->cnee->postcode . ' weight :' . $chargeWeight . ' | ';
						}
					}

					// eventually we got the cheap courier
					// eventually we got the cheap courier
					if (!empty($cheapOrgRate)) {

					// in case toll selected , client must provide the company name for consignee
						$beforeValidation = true;
						if (Org::ORGID_COURIER_TOLL == $cheapOrgRate->org_id) {
							if (!isset($shipment->cnee->company) || empty($shipment->cnee->company)) {
								$resp->status = 0;
								$resp->msg = '[60001] - ' . $shipment->hbn . '- Please set consignee company name for Toll' ;
								$beforeValidation = false;
							}
						}

						// for startrack and toll we need to check dim based on the following logic
						// 如果客人api推上来的是STARTRACK或者TOLL的服务， 则三边或者总体积两者
						// 一定要有一个填全，两者都填的情况下，系统比较取较小的那个提交给快递。若三边或
						// 者总体积均未填写数据， 则一样报错并不要生成shipment，此规则eparcel服务或者
						// fastway服务不要使用。

						if (Org::ORGID_COURIER_TOLL == $cheapOrgRate->org_id  ||
						Org::ORGID_COURIER_STARTRACK == $cheapOrgRate->org_id) {
							$dim = floatval($shipment->cbm);
							$validDim = true;
							if ($dim <= 0.0) {
								if (!isset($shipment->mdata['dim'])) {
									$validDim = false;
								} else {
									$cbm = floatval($shipment->mdata['dim']['w']) * floatval($shipment->mdata['dim']['h']) * floatval($shipment->mdata['dim']['d']);
									$shipment->cbm= number_format($cbm/1000000, 6, '.', '');
									$shipment->update(['cbm']);
									if ($cbm <= 0.0) {
										$validDim = false;
									}
								}
							}
							if (!$validDim) {
								$resp->status = 0;
								$resp->msg = '[60006] - Dimensions or Cube is missing' ;
								$beforeValidation = false;
							}
						}
						if ($beforeValidation) {
							// create cheap courier's label
											  
							if (!$shipment->createCourierLabel($cheapOrgRate)) {
								if (sizeof($left_couriers)>1) {
									if (($key= array_search($cheapOrgRate->id, $left_couriers))!==false) {
										unset($left_couriers[$key]);
										continue;
									}
								}
				 
								$resp->status = 0;
								$resp->msg = '[60002] - ' .$shipment->hbn . '- Failed to create Label for : ' .($cheapOrgRate->org_id!=1426?$cheapOrgRate->org_id . '(' . $cheapOrgRate->org->name . ')':"Courier");
							} else {
								//$resp->msg = $shipment->hbn  . ' - Tranship with : ' . $cheapOrgRate->org->name ;
							}
						}
						break;
					} else {
						$resp->status = 0;
						$resp->msg = '[60003] - ' . $shipment->hbn . ' failed : ' .  $getCheapOrgRateError ;
						break;
					}
				}
			} else { // in case no any courier , some thing wrong
				$resp->status = 0;
				$resp->msg = '[60004] - ' . 'Charge code : ' . $chargeCode . ' no any courier set';
			}
		} else {
			$resp->status = 0;
			$resp->msg = '[60005] - ' .'Invalid charge code : ' . $chargeCode;
		}
			  
		return $resp;
	}

	public function shipment_api()
	{
		$chargecode = '8704';
		$shipment = Shipment::model()->find('hbn = "ECN1478005802"');
		echo json_encode($this->selectServiceByChargeCode($chargecode, $shipment));
	}

	public function  startrack_manifest()
	{
		$s = ImParcel::model()->find('ref = :ref', [':ref' => $this->prompt('ref: ')]);
		if (!empty($s)) {
			if (preg_match('/7RFZ/', $s->ref)) {
				$api = StarTrackAPI::getSydneyInterface(true);
			} else if (preg_match('/4XHZ/', $s->ref)) {
				$api = StarTrackAPI::getMelbourneInterface(true);
			}
			$r = $api->createOrderFromShipments([$s], $s->ref);
			if (!empty($r->order)) {
				$oid = $r->order->order_id;
				// because aupost returned shipment is not the same order with our sending order
				// so here we need to order by HBN again
				$auPostShipments = [];
				foreach ($r->order->shipments as $aushipment) {
					$auPostShipments[$aushipment->shipment_reference] = $aushipment;
				}

				$aushipment = $auPostShipments[$s->hbn];
				// check to see if tranship existing
				// in case existing , just update it
				$ts = Tranship::model()->find('pid = :pid AND org_id = :oid AND status = 19', [':pid' => $s->id, ':oid' => Org::ORGID_COURIER_STARTRACK]);
				if (empty($ts)) {
					$ts = new Tranship;
					$ts->pid = $s->id;
					$ts->org_id = Org::ORGID_COURIER_STARTRACK;  // for startrack post office
					$ts->man_id = $s->man_id;
					$ts->type = 80;  // shipment transfer to a different delivery courier
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
			}
		}
	}

	public function delivery_cost()
	{
		$task = WmsTask::model()->find('link_id = :id AND type = 2120', [':id' => $this->prompt('id: ')]);
		$criteria = new CDbCriteria();
		$criteria->compare('id', $task->mdata['shipment_id']);
		$shipments = Shipment::model()->findAll($criteria);
		$stot = 0;
		$weight = 0;
		$aid = $task->job->org_id;
		if (in_array($aid, [Org::ORGID_3PL_COBAYER, 2025])) {
			$cc = ImportChargeCode::model()->find('org_id = :org_id', [':org_id' => $aid]);
		} else {
			$cc = ImportChargeCode::model()->find('org_id = 114');
		}
		// $chargecode = ImportChargeCode::model()->find('org_id = :org_id', [':org_id' => $aid]);
		// $chargecode = $chargecode ? $chargecode : ImportChargeCode::model()->find('org_id = 114');
		if (!empty($cc)) {
			$items = [];
			foreach ($shipments as $shipment) {
				$chargecode = $cc->chargecode;
				if (!empty($shipment->mdata['chargecode'])) {
					$chargecode = $shipment->mdata['chargecode'];
				}
				if ($task->mdata['courier'] == 858) {
					// startrack
					$chargecode = ImportChargeCode::STARTRACK_TNT_3PL;
				}
				if (in_array($aid, [1308])) {
					if (preg_match('/7RFZ/', $shipment->ref)) {
						$chargeweight = isset($shipment->mdata['charge_client_weight']) ? $shipment->mdata['charge_client_weight'] : (isset($shipment->mdata['chargecode']) ? $shipment->chargeWeight() : $shipment->weight);
						$cost = $task->getChargeByChargecode($chargeweight, $shipment->postcode, $chargecode);
					} else {
						$cost = $task->getChargeByChargecode($shipment->weight, $shipment->postcode, $chargecode);
					}
				} else {
					if ($task->mdata['shipment_courier_id'] == 101) {
						$cost = $task->getChargeByChargecode($shipment->weight, $shipment->postcode, $chargecode, true);
					} else {
						$cost = $task->getChargeByChargecode($shipment->weight, $shipment->postcode, $chargecode);
					}
				}
				echo 'chargecode ' . $chargecode . ' cost ' . $cost . ' weight ' . $shipment->weight . PHP_EOL;
				$stot += $cost;
				$weight += $shipment->weight;
			}
		}
		echo 'total ' . $stot . ' weight ' . $weight . PHP_EOL;
	}

	public function delivery_cost2($id)
	{
		$task = WmsTask::model()->findByPk($id);
		if (empty($task) || preg_match('/[\x{4e00}-\x{9fa5}]+/u', $task->mdata['cnee']['state'])) {
			return 0;
		}
		$criteria = new CDbCriteria();
		$criteria->compare('id', $task->mdata['shipment_id']);
		$shipments = Shipment::model()->findAll($criteria);
		$stot = 0;
		$weight = 0;
		$aid = $task->job->org_id;
		if (in_array($aid, [Org::ORGID_3PL_COBAYER, 2025])) {
			$cc = ImportChargeCode::model()->find('org_id = :org_id', [':org_id' => $aid]);
		} else {
			$cc = ImportChargeCode::model()->find('org_id = 114');
		}
		if (!empty($cc)) {
			$items = [];
			foreach ($shipments as $shipment) {
				$chargecode = $cc->chargecode;
				if (!empty($shipment->mdata['chargecode'])) {
					$chargecode = $shipment->mdata['chargecode'];
				}
				if ($task->mdata['courier'] == 858) {
					// startrack
					$chargecode = ImportChargeCode::STARTRACK_TNT_3PL;
				}
				if (in_array($aid, [1308])) {
					if (preg_match('/7RFZ/', $shipment->ref)) {
						$chargeweight = isset($shipment->mdata['charge_client_weight']) ? $shipment->mdata['charge_client_weight'] : (isset($shipment->mdata['chargecode']) ? $shipment->chargeWeight() : $shipment->weight);
						$cost = $task->getChargeByChargecode($chargeweight, $shipment->postcode, $chargecode);
					} else {
						$cost = $task->getChargeByChargecode($shipment->weight, $shipment->postcode, $chargecode);
					}
				} else {
					if ($task->mdata['shipment_courier_id'] == 101) {
						$cost = $task->getChargeByChargecode($shipment->weight, $shipment->postcode, $chargecode, true);
					} else {
						$cost = $task->getChargeByChargecode($shipment->weight, $shipment->postcode, $chargecode);
					}
				}
				$stot += $cost;
				$weight += $shipment->weight;
			}
		}
		return $stot;
	}

	public function delivery_cost_compare()
	{
		for ($i = 1; $i < 10; $i++) {
			$file = fopen('delivery_cost_compare' . $i . '.txt', 'w');
			$lines = InvLine::model()->findAll('model = "WmsInvoiceLine" AND det = "Delivery" AND fid >= ' . ($i * 10000 - 10000) . ' AND fid < ' . ($i * 10000));
			foreach ($lines as $line) {
				if ((round($this->delivery_cost2($line->fid) * 100) / 100) == 0 || (round($line->amount / 1.1 * 100) / 100) == (round($this->delivery_cost2($line->fid) * 100) / 100)) {
					continue;
				}
				fwrite($file, $line->mdata['items'][0][0] . ' ' . (round($line->amount / 1.1 * 100) / 100) . ' ' . (round($this->delivery_cost2($line->fid) * 100) / 100) . "\n");
			}
			fclose($file);
		}
	}

	public function check_invoice_org()
	{
		$invoices = Invoice::model()->findAll('date >= "2019-05-01"');
		foreach ($invoices as $invoice) {
			if (!empty($invoice->job) && $invoice->to_id != $invoice->job->owner_id) {
				echo $invoice->no . PHP_EOL;
			}
		}
	}

	public function plt_note()
	{
		$locations = WmsLocation::model()->findAll('meta like "%note%"');
		foreach ($locations as $location) {
			$location->notes = @$location->extra['note'];
			$location->save();
		}
	}

	public function reconcile_invoice()
	{
		$xero = new XeroAPI;
		$results = $xero->get('Accounting\Invoice', ['Type' => 'ACCREC', 'ModifiedAfter' => date('Y-m-d', strtotime(date('Y-m-d') . ' - 1 day'))]);

		foreach ($results as $result) {
			// get xero inv and hvlv inv
			$xero_inv = $xero->getByID('Accounting\Invoice', $result['InvoiceID']);
			$inv = Invoice::model()->find('no = :no', [':no' => $xero_inv['InvoiceNumber']]);
			if (empty($inv)) {
				continue;
			}

			// check allocate payment
			if (!empty($xero_inv['Payments'])) {
				foreach ($xero_inv['Payments'] as $item) {
					// get xero pay and hvlv pay
					$xero_payment = $xero->getByID('Accounting\BatchPayment', $item['PaymentID']);
					if (empty($xero_payment)) {
						$xero_payment = $xero->getByID('Accounting\Payment', $item['PaymentID']);
					} else {
						$xero_payment['PaymentID'] = $xero_payment['BatchPaymentID'];
					}
					$payment = Payment::model()->find('xero_id = :xero_id', [':xero_id' => $xero_payment['PaymentID']]);

					// get xero account and hvlv account
					$xero_account = $xero->getByID('Accounting\Account', $item['Account']['AccountID']);
					$account = BankAccount::model()->find('xero_id = :xero_id', [':xero_id' => $xero_account['AccountID']]);

					if (empty($payment)) {
						$payment = new Payment;
						$payment->org_id = $inv->to_id;
						$payment->date = $xero_payment['Date']->format('Y-m-d');
						$payment->transaction_date = $xero_payment['Date']->format('Y-m-d');
						$payment->status = Payment::PAYMENT_STATUS_POSTED;
						$payment->dpmt = $inv->dpmt;
						$payment->bank = $account->id;
						$payment->currency = array_search($xero_account['CurrencyCode'], Invoice::$currencies);
						$payment->amount = $xero_payment['Amount'];
						$payment->ref = $xero_payment['Reference'];
						$payment->ata = 0;
						$payment->type = Payment::PAYMENT_TYPE_EFT;
						$payment->xero_id = $xero_payment['PaymentID'];
						$payment->save();
					}

					$pay_inv = PayInv::model()->find('inv_id = :inv_id AND pay_id = :pay_id', [':inv_id' => $inv->id, ':pay_id' => $payment->id]);
					if (empty($pay_inv)) {
						$pay_inv = new PayInv;
						$pay_inv->inv_id = $inv->id;
						$pay_inv->pay_id = $payment->id;
						$pay_inv->amount = $item['Amount'];
						$pay_inv->transaction_date = $item['Date']->format('Y-m-d');
						$pay_inv->exrate = $item['CurrencyRate'];
						$pay_inv->save();

						$payment->updateAta();
					}
				}
			}

			// check allocate credit note
			if (!empty($xero_inv['CreditNotes'])) {
				foreach ($xero_inv['CreditNotes'] as $item) {
					// get xero credit and hvlv credit
					$xero_credit = $xero->getByID('Accounting\CreditNote', $item['CreditNoteID']);
					$credit = Payment::model()->find('no = :no', [':no' => $item['CreditNoteNumber']]);

					if (!empty($credit)) {
						if (empty($credit->xero_id) || $credit != $item['CreditNoteID']) {
							$credit->xero_id = $item['CreditNoteID'];
							$credit->update('xero_id');
						}
					} else if (empty($credit)) {
						continue;
					}

					$pay_inv = PayInv::model()->find('inv_id = :inv_id AND pay_id = :pay_id', [':inv_id' => $inv->id, ':pay_id' => $credit->id]);
					if (empty($pay_inv)) {
						$pay_inv = new PayInv;
						$pay_inv->inv_id = $inv->id;
						$pay_inv->pay_id = $credit->id;
						$pay_inv->amount = $item['Total'];
						$pay_inv->transaction_date = date('Y-m-d');
						$pay_inv->exrate = 1;
						$pay_inv->save();

						$credit->updateAta();
					}
				}
			}

			$inv->checkPaid();
			$inv->save();
		}
	}

	public function get_charge_pro()
	{
		$shipment = Shipment::model()->find('ref = :ref', [':ref' => $this->prompt('Shipment ID: ')]);
		if (!empty($shipment)) {
			$amt = $shipment->getRtsEstiInvoice();
			echo json_encode($amt) . PHP_EOL;
		}
	}

	public function austway_report()
	{
		$xls = new oExcel;
		$xls->setTitle('2019 Jan');
		$i = 1;
		$xls->setColWidth([15, 15, 15]);
		$xls->addRow($i++, ['Date', 'No', 'Total']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 41 AND status NOT IN (8,10) AND date >= "2019-01-01" AND date < "2019-02-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			$xls->addRow($i++, [$inv->date, $inv->no, $inv->total]);
		}

		$xls->createSheet('2019 Feb');
		$xls->goSheet(1);
		$i = 1;
		$xls->setColWidth([15, 15, 15]);
		$xls->addRow($i++, ['Date', 'No', 'Total']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 41 AND status NOT IN (8,10) AND date >= "2019-02-01" AND date < "2019-03-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			$xls->addRow($i++, [$inv->date, $inv->no, $inv->total]);
		}

		$xls->createSheet('2019 Mar');
		$xls->goSheet(2);
		$i = 1;
		$xls->setColWidth([15, 15, 15]);
		$xls->addRow($i++, ['Date', 'No', 'Total']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 41 AND status NOT IN (8,10) AND date >= "2019-03-01" AND date < "2019-04-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			$xls->addRow($i++, [$inv->date, $inv->no, $inv->total]);
		}

		$xls->createSheet('2019 Apr');
		$xls->goSheet(3);
		$i = 1;
		$xls->setColWidth([15, 15, 15]);
		$xls->addRow($i++, ['Date', 'No', 'Total']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 41 AND status NOT IN (8,10) AND date >= "2019-04-01" AND date < "2019-05-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			$xls->addRow($i++, [$inv->date, $inv->no, $inv->total]);
		}

		$xls->createSheet('2019 May');
		$xls->goSheet(4);
		$i = 1;
		$xls->setColWidth([15, 15, 15]);
		$xls->addRow($i++, ['Date', 'No', 'Total']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 41 AND status NOT IN (8,10) AND date >= "2019-05-01" AND date < "2019-06-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			$xls->addRow($i++, [$inv->date, $inv->no, $inv->total]);
		}

		$xls->createSheet('2019 Jun');
		$xls->goSheet(5);
		$i = 1;
		$xls->setColWidth([15, 15, 15]);
		$xls->addRow($i++, ['Date', 'No', 'Total']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 41 AND status NOT IN (8,10) AND date >= "2019-06-01" AND date < "2019-07-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			$xls->addRow($i++, [$inv->date, $inv->no, $inv->total]);
		}

		$xls->goSheet(0);
		$xls->output('austway_report1.xlsx', null, false);

		$xls = new oExcel;
		$xls->setTitle('2019 Jan');
		$i = 1;
		$xls->setColWidth([15, 15, 15, 15]);
		$xls->addRow($i++, ['Ref', 'Date', 'Invoice', 'Amount']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 10 AND status NOT IN (8,10) AND date >= "2019-01-01" AND date < "2019-02-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			foreach ($inv->lines as $line) {
				foreach ($line->mdata['items'] as $item) {
					if (!preg_match('/ECN/', $item[0])) {
						continue;
					}
					$shipment = Shipment::model()->find('ref = :ref', [':ref' => $item[0]]);
					$xls->addRow($i++, [$item[0], $shipment->created, $inv->no, $item[5]]);
				}
			}
		}

		$xls->createSheet('2019 Feb');
		$xls->goSheet(1);
		$i = 1;
		$xls->setColWidth([15, 15, 15, 15]);
		$xls->addRow($i++, ['Ref', 'Date', 'Invoice', 'Amount']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 10 AND status NOT IN (8,10) AND date >= "2019-02-01" AND date < "2019-03-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			foreach ($inv->lines as $line) {
				foreach ($line->mdata['items'] as $item) {
					if (!preg_match('/ECN/', $item[0])) {
						continue;
					}
					$shipment = Shipment::model()->find('ref = :ref', [':ref' => $item[0]]);
					$xls->addRow($i++, [$item[0], $shipment->created, $inv->no, $item[5]]);
				}
			}
		}

		$xls->createSheet('2019 Mar');
		$xls->goSheet(2);
		$i = 1;
		$xls->setColWidth([15, 15, 15, 15]);
		$xls->addRow($i++, ['Ref', 'Date', 'Invoice', 'Amount']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 10 AND status NOT IN (8,10) AND date >= "2019-03-01" AND date < "2019-04-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			foreach ($inv->lines as $line) {
				foreach ($line->mdata['items'] as $item) {
					if (!preg_match('/ECN/', $item[0])) {
						continue;
					}
					$shipment = Shipment::model()->find('ref = :ref', [':ref' => $item[0]]);
					$xls->addRow($i++, [$item[0], $shipment->created, $inv->no, $item[5]]);
				}
			}
		}

		$xls->createSheet('2019 Apr');
		$xls->goSheet(3);
		$i = 1;
		$xls->setColWidth([15, 15, 15, 15]);
		$xls->addRow($i++, ['Ref', 'Date', 'Invoice', 'Amount']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 10 AND status NOT IN (8,10) AND date >= "2019-04-01" AND date < "2019-05-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			foreach ($inv->lines as $line) {
				foreach ($line->mdata['items'] as $item) {
					if (!preg_match('/ECN/', $item[0])) {
						continue;
					}
					$shipment = Shipment::model()->find('ref = :ref', [':ref' => $item[0]]);
					$xls->addRow($i++, [$item[0], $shipment->created, $inv->no, $item[5]]);
				}
			}
		}

		$xls->createSheet('2019 May');
		$xls->goSheet(4);
		$i = 1;
		$xls->setColWidth([15, 15, 15, 15]);
		$xls->addRow($i++, ['Ref', 'Date', 'Invoice', 'Amount']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 10 AND status NOT IN (8,10) AND date >= "2019-05-01" AND date < "2019-06-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			foreach ($inv->lines as $line) {
				foreach ($line->mdata['items'] as $item) {
					if (!preg_match('/ECN/', $item[0])) {
						continue;
					}
					$shipment = Shipment::model()->find('ref = :ref', [':ref' => $item[0]]);
					$xls->addRow($i++, [$item[0], $shipment->created, $inv->no, $item[5]]);
				}
			}
		}

		$xls->createSheet('2019 Jun');
		$xls->goSheet(5);
		$i = 1;
		$xls->setColWidth([15, 15, 15, 15]);
		$xls->addRow($i++, ['Ref', 'Date', 'Invoice', 'Amount']);
		$invs = Invoice::model()->findAll(['condition' => 'to_id = 1474 AND type = 10 AND status NOT IN (8,10) AND date >= "2019-06-01" AND date < "2019-07-01"', 'order' => 'id ASC']);
		foreach ($invs as $inv) {
			foreach ($inv->lines as $line) {
				foreach ($line->mdata['items'] as $item) {
					if (!preg_match('/ECN/', $item[0])) {
						continue;
					}
					$shipment = Shipment::model()->find('ref = :ref', [':ref' => $item[0]]);
					$xls->addRow($i++, [$item[0], $shipment->created, $inv->no, $item[5]]);
				}
			}
		}

		$xls->goSheet(0);
		$xls->output('austway_report2.xlsx', null, false);
	}

	public function container_load_plt_fix()
	{
		$names = ["PLT1901000218","PLT1901000219","PLT1901000220","PLT1901000221","PLT1901000222","PLT1901000223","PLT1901000224","PLT1901000225","PLT1901000226","PLT1901000227","PLT1901000229","PLT1901000230","PLT1901000231","PLT1901000232","PLT1901000234","PLT1901000235","PLT1901000236","PLT1901000237","PLT1901000238","PLT1901000239","PLT1901000240","PLT1901000241","PLT1901000242","PLT1901000247","PLT1901000248","PLT1901000249","PLT1901000250","PLT1901000285","PLT1901000286","PLT1901000287","PLT1901000288","PLT1901000289","PLT1901000290","PLT1901000291","PLT1901000292","PLT1901000293","PLT1901000294","PLT1901000295","PLT1901000296","PLT1901000297"];
		$locations = WmsLocation::model()->findAll('name IN ("' . implode('","', $names) . '")');
		foreach ($locations as $location) {
			$item = WmsTaskItem::model()->find('task_id = 60270 AND meta LIKE "%' . $location->name . '%"');
			if (empty($item)) {
				$item = new WmsTaskItem;
				$item->task_id = 60270;
				$item->ts = '2019-01-23 11:51:00';
				$item->mdata = ['nt' => '', 'pq' => '1', 'pli' => $location->id, 'pl' => $location->name];
				$item->save();
			} else {
				$item->toStock();
			}
		}
	}

	public function complete_old_task()
	{
		$model = new WmsTask;
		$ec = new CDbCriteria;
		$ec->with = ['job.customer', 'job.customer.owner'];
		$ec->addCondition('(JSON_VALUE(customer.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(customer.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')) AND t.status != 99');
		$data = $model->search(false, 0, $ec)->getData();
		foreach ($data as $task) {
			if (!empty($task->job->customer->extra['wms_invoice']) || !empty($task->job->customer->owner->extra['wms_invoice']) || (!empty($task->createlog) && strtotime($task->createlog->time) < strtotime('2019-04-01'))) {
				continue;
			}
			$task->compl_time = '2019-03-31 23:59:59';
			$task->status = 99;
			$task->update('compl_time', 'status');
			$this->log('Complete task ' . $task->getNo() . ' created on ' . @$task->createlog->time . ' for org ' . $task->job->customer->id . ' owner ' . $task->job->customer->owner->id);
		}
	}

	public function retrieve_old_task()
	{
		$model = new WmsTask;
		$ec = new CDbCriteria;
		$ec->with = ['job.customer', 'job.customer.owner'];
		$ec->addCondition('(JSON_VALUE(customer.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(customer.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')) AND t.status = 99');
		$data = $model->search(false, 0, $ec)->getData();
		foreach ($data as $task) {
			if (!empty($task->lastlog) && $task->compl_time == '2019-03-31 23:59:59') {
				$logs = Log::model()->findAll(['condition' => 'model = "WmsTask" AND lid = :lid', 'params' => [':lid' => $task->id], 'order' => 'id DESC']);
				foreach ($logs as $log) {
					$status = array_search($log->extra['status'], WmsTask::$states);
					if (!empty($status) && $status != 99) {
						$task->status = $status;
						$task->compl_time = '2019-04-30 23:59:59';
						$task->update('status', 'compl_time');
						break;
					}
				}
			}
		}
	}

	public function fix_simplein_ledger()
	{
		$prod = WmsProd::model()->find('name = "MISC PALLET"');
		$items = WmsTaskItem::model()->findAll('task_id in (100810, 99940, 90924, 91287)');
		foreach ($items as $item) {
			if (empty($item->mdata['gi']) && empty($item->mdata['gn'])) {
				$item->mdata['gi'] = $prod->id;
				$item->mdata['gn'] = $prod->name;
				$item->save();
			}
		}
	}

	public function delete_fastway_cache()
	{
		Yii::app()->cache->delete('fastwayArea');
	}

	public function sync_invoice_by_id()
	{
		$no = $this->prompt('Invoice ID OR NO.: ');
		$invoice = Invoice::model()->find('(id = :n OR no = :n) AND status != 10', [':n' => $no]);
		if (!empty($invoice)) {
			$invoice->saveInvoice2xero();
		} else {
			[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
			$invoice = Invoice::model()->find('(id = :n OR no = :n) AND status != 10', [':n' => $no]);
			if (!empty($invoice)) {
				$invoice->saveInvoice2xero();
			} else {
				echo 'invalid no' . PHP_EOL;
			}
		}
	}

	public function sync_invoice_by_query()
	{
		$query = $this->prompt('Query: ');
		if (empty($query)) return;
		$invoices = Invoice::model()->findAll($query . ' AND status != 10');
		if (!empty($invoices)) {
			foreach ($invoices as $invoice) {
				$invoice->saveInvoice2xero();
			}
		} else {
			[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
			$invoices = Invoice::model()->findAll($query . ' AND status != 10');
			if (!empty($invoices)) {
				foreach ($invoices as $invoice) {
					$invoice->saveInvoice2xero();
				}
			} else {
				echo 'invalid no' . PHP_EOL;
			}
		}
	}

	public function get_inv_total()
	{
		$invoice = Invoice::model()->findByPk($this->prompt('Invoice ID: '));
		$invoice->getTotal();
		echo $invoice->total . PHP_EOL;
		echo $invoice->gst . PHP_EOL;
	}

	public function check_invoice_total_gst()
	{
		$xero = new XeroAPI;
		$xero_invs = $xero->get('Accounting\Invoice', ['Type' => "ACCREC"]);
		foreach ($xero_invs as $xero_inv) {
			$invoice = Invoice::model()->find(['condition' => 'no LIKE :no AND sync_xero = 1 AND date >= "2018-07-01" AND status != 10', 'params' => [':no' => '%' . $xero_inv['InvoiceNumber'] . '%'], 'order' => 'id DESC']);
			if (!empty($invoice) && ($xero_inv['Total'] != $invoice->total || $xero_inv['TotalTax'] != $invoice->gst) && !in_array($xero_inv['Status'], ['PAID', 'VOIDED'])) {
				if (abs($xero_inv['Total'] - $invoice->total) < 0.1 && abs($xero_inv['TotalTax'] - $invoice->gst) < 0.1) {
					continue;
				}
				$this->log($invoice->no . ' ' . $xero_inv['Status'] . "\n" . 'HVLV total = ' . $invoice->total . '; gst = ' . $invoice->gst . ' XERO total = ' . $xero_inv['Total'] . '; gst = ' . $xero_inv['TotalTax']);
			}
		}
	}

	public function sync_partial_invoice()
	{
		$invoices = Invoice::model()->findAll('sync_xero = 0 AND no IN ("ED63879-1","OT67497","OT67722","RT69782","ED71782","RT72001","OT72529","ED71788-1","WM73426","OT75727","WM76882","OT77506","WM77878","WM77890","WM77911","ED76528-1","ED79324","OT80365","WM81207","WM82143","WM83094","IM83199","OT83418","IM81786-1","IM81780-3","IM82623-1","IM84099","ED84030-1","OT84258","IM84519","IM79558-1","IM79207-2","IM79555-2","IM79564-1","IM79570-1","WM85202","DI85588","ED86155","IM86203","ED81705-2","ED87178","OT87547","FW87982","IM88141","IM88147","IM88387","IM88651","IM86875-1")');
		foreach ($invoices as $invoice) {
			$xdata = new stdClass;
			$xdata->no = $invoice->no;
			$xdata->currency = $invoice->currency;
			$xdata->cust = $invoice->cust;
			$xdata->date = $invoice->date;
			$xdata->due = $invoice->due;
			$xdata->gst = 1;
			$xdata->dpt_id = $invoice->dpt_id;
			$costs = [];

			// xero account
			if ($invoice->dpmt == 10) {
				$code = 83000;
			} else if ($invoice->dpmt == 20) {
				$code = 81000;
			} else if ($invoice->dpmt == 30) {
				$code = 82000;
			} else if ($invoice->dpmt == 40) {
				if ($invoice->type == 60) {
					$code = 85000;
				} else {
					$code = 85050;
				}
			}

			$costs = [[
				'qty' => 1,
				'det' => '',
				'ccode' => $code,
				'taxType' => $invoice->lines[0]->tax,
				'dept' => BillingLine::$xero_segments[$invoice->dpmt / 10],
				'amount' => $invoice->getBalance(),
			]];

			if (!empty($costs)) {
				$xdata->lines = array_values($costs);

				// invoice type in Xero
				// ACCPAY	A bill – commonly known as a Accounts Payable or supplier invoice
				// ACCREC	A sales invoice – commonly known as an Accounts Receivable or customer invoice
				$invoice = new XeInvoice('ACCREC');

				$invoice->status = 'AUTHORISED'; // approved , waiting for pay
				$invoice->invoiceNumber = explode('-', $xdata->no)[0];

				$curIndex = $xdata->currency;
				$curCode = 'AUD';
				if (isset(Invoice::$currencies[$curIndex])) {
					$curCode = Invoice::$currencies[$curIndex];
				}
				$invoice->currencyCode = $curCode;

				$contact = new XeContact();
				$contact->name = $xdata->cust->name;
				$contact->accountNumber = 'PORG-' . $xdata->cust->id;
				$invoice->contact = $contact;
				$invoice->date = $xdata->date;
				$invoice->dueDate = $xdata->due;

				// Exclusive - exclude GST
				// Inclusive - include GST
				// NoTax
				if ($xdata->gst > 0) {
					$invoice->lineAmountTypes = 'Inclusive';
				} else {
					$invoice->lineAmountTypes = 'NoTax';
				}

				// get all items
				foreach ($xdata->lines as $line) {
					$itemData = array();
					$itemData['quantity'] = $line['qty'];
					$itemData['accountCode'] = $line['ccode'];
					$itemData['description'] = '【' . $line['ccode'] . '】 ' . $line['det'];
					$itemData['unitAmount'] = $line['amount'];
					$itemData['taxType'] = $line['taxType'];
					$invoice->addLineItem($itemData);
				}

				try {
					$rt = $invoice->save();
					if ($rt && $invoice->statusAttributeString == 'OK') {
						// set sync to Xero successful flag
						$invoice = Invoice::model()->find('no = :no', [':no' => $xdata->no]);
						if (!empty($invoice)) {
							$invoice->sync_xero = 1;
							$invoice->update('sync_xero');
						}
					} else {
						// log error message
						Yii::app()->xero->log('failed to save invoice to xero for : ' . $xdata->no, Xero::LOG_LEVEL_ERR);
					}
				} catch (Exception $mye) {
					$msg = 'failed to save invoice - ' . $mye->getMessage();
					$msg .= PHP_EOL;
					$msg .= 'Invoice Data : ' . json_encode($invoice, JSON_PRETTY_PRINT);
					Yii::app()->xero->log($msg, Xero::LOG_LEVEL_ERR);
				}
			}
		}
	}

	public function star_fuel()
	{
		$invoice_no = '1014833519052';
		$d['code'] = 'Fuel Surcharge';
		$d['charge'] = '15.3';
		$billings = BillingLine::model()->findAll('billing_cref = :cref AND billing_ref like "T%"', [':cref' => $invoice_no]);
		foreach ($billings as $line) {
			// one task has multiple parcels cant detect dup
			$billing = new BillingLine;
			$billing->mdata['from_rec'] = 0;
			$billing->status = 1;
			$billing->link_id = 0;
			$billing->org_id = $line->org_id;
			$billing->charge_code = Consol::AU_LOCAL_DELIVERY_COST_GL_CODE;
			$billing->billing_cref = $invoice_no;
			$billing->currency = 1;
			$billing->weight = 0;
			$billing->charge_weight = 0;
			$billing->billing_ref = $line->billing_ref;
			$billing->type = BillingLine::BILLING_TYPE_3PL;
			$billing->dpmt = Invoice::DPMT_3PL;
			$billing->awb = $line->awb;
			$billing->dpt_id = $line->dpt_id;
			$billing->date = $line->date;
			$billing->created = $line->created;
			$billing->transaction_date = $line->transaction_date;
			$billing->due = $line->due;
			$billing->actual_amount = number_format(round($line->actual_amount * $d['charge'] / 100, 2), 2, '.', '');
			$billing->gst = 'INPUT';
			$billing->gst_amount = $billing->getGSTValue();
			$billing->desc = $d['code'] . ' @' . $d['charge'] . '%';
			$billing->accrual_amount = number_format(round($line->actual_amount * $d['charge'] / 100, 2), 2, '.', '');
			$billing->save();
			Billing::linkLine($billing, false);
		}
	}

	public function resend_acr()
	{
		$shipments = ImParcel::model()->findAll('consol_id = ' . $this->prompt('Consol ID: '));

		foreach ($shipments as $shipment) {

		$resp = ['success' => 0];

		$p = $shipment;
		if (in_array($p->status, [50, 55, 58, 60])) {
			$cn = $p->consol;
			if (in_array($p->agent_id, SystemSetting::getNameValidationCustomers())) {
				$tempGoods = $p->getGoods();	// inorder to use reference as parameter
				$goods = WordReplace::replace($tempGoods, $p->agent_id, $p)[0];
			} else {
				$goods = $p->getGoods();
			}

			$err = [];
			if (empty($p->weight)) {
				$err[] = 'Shipment ' . $p->hbn . ' has no weight.';
			}
			if (empty($p->dvalue)) {
				$err[] = 'Shipment ' . $p->hbn . ' has no value.';
			}
			if (empty($p->pkg)) {
				$err[] = 'Shipment ' . $p->hbn . ' has number of packs.';
			}
			if (empty($goods)) {
				$err[] = 'Shipment has no goods description.';
			}

			$cnor_name = $p->cnor->name;
			if (!preg_match('/\w/', $cnor_name)) $cnor_name = $p->agent->name;
			$cnor_name = Edimsg::strEscape($cnor_name);
							
			if(empty($cnor_name)){
				$err[] = 'Shipment '.$p->hbn.' cnor name problem';
			}

			$cnor_addr = $p->cnor->fullAddress(['suburb', 'city', 'state', 'postcode']);
			if (!preg_match('/\w/', $cnor_addr)) {
				$cnor_addr = $p->agent->getAddress(true, false, false);
			}
			$cnor_addr = trim($cnor_addr, " .,");
			$cnor_addr = Edimsg::strEscape($cnor_addr);
							
			if(empty($cnor_addr)){
				$err[] = 'Shipment '.$p->hbn.' cnor address problem';
			}

			if (!empty($p->receiver)&&!empty($p->receiver->address)&&!empty($p->receiver->name)&&!empty($p->receiver->postcode)) {
				$cnee_name=$p->receiver->name;
				$cnee_addr=$p->receiver->fullAddress(['suburb', 'state', 'postcode']);
			} else {
				$cnee_name=$p->cnee->name;
				$cnee_addr=$p->cnee->fullAddress(['suburb', 'state', 'postcode']);
			}

			$cnee_name = Edimsg::strEscape($cnee_name);
			$cnee_addr = Edimsg::strEscape($cnee_addr);

			if(empty($cnee_name) || !preg_match('/[A-z]+/', $cnee_name)){
				$err[] = 'Shipment '.$p->hbn.' cnee name problem';
			}
			if(empty($cnee_addr) || !preg_match('/[A-z]+/', $cnee_addr)){
				$err[] = 'Shipment '.$p->hbn.' cnee address problem';
			}

			if(!empty($err)){
				$resp['msg'] = implode('<br />', $err);
				echo json_encode($resp);
				return;
			}

			if ($cn->service == 20) {//sea cargo

				$os = Edimsg::model()->findAll(['condition' => "status = 50 AND type = 'SEACR-S' AND fid = :id", 'params' => [':id' => $p->id], 'order' => 't.id DESC']);
				$omid = null;
				if (!empty($os)) {
					foreach ($os as $o) {
						if (preg_match('/BGM\+933:::SEACR\+([^\+:]+):\d+\+9\'/', $o->msg, $m)) {
							$omid = $m[1];
							break;
						}
					}
				}
				
				if(Edimsg::model()->count('fid = :id AND type ="SEACR-S" AND status IN (20, 30, 40)', [':id' => $p->id]) > 0){
					$resp['msg']='There is an SCR message waiting for response from ICS, please try again later.';
					echo json_encode($resp);
					return;
				}

				$canSac = $p->canSAC();
				$obl=$cn->awb;// /required ocean bill landing
				$hbl=$cn->mdata['house_bill'];//house bill landing;// parent house bill landing
			//A document signed and delivered by the Master of a Ship to the consignor. A document of title and a receipt for goods.
				$voyage_number=$cn->flight ;// like 023s
				$vessel=$cn->airline; //IMO  like 9235103
				$container_number=trim($cn->mdata['container_no']);//container no  like OOLU9551594
				$container_size= DmawbConsol::getContainerCode($cn->mdata['sea_type']);
				$cargo_type=$cn->mdata['cargo_type'];
				if (empty($cargo_type)) $cargo_type="LCL";
				$volume=$p->cbm>0?$p->cbm*$p->pkg:$p->weight/250;
				if ($volume<0.01) $volume=0.01;

				$em = new Edimsg;
				$em->dt = date('Y-m-d H:i:s');
				$em->type = 'SEACR-S';
				$em->sender = Yii::app()->params['ics']['testing']? Yii::app()->params['ics']['test_site'] : Yii::app()->params['ics']['prod_site'];
				$em->receiver = Yii::app()->params['ics']['customs_id'];
				$em->status = 19;
				$em->fid = $p->id;
				$em->save();
				$em->mid = sprintf('%06s', substr($em->id, -6));
				$vn = Edimsg::model()->count('type = :t AND fid = :fid', [':t' => 'SEACR-S', ':fid' => $p->id]) + 1;
				$msg = "UNH+".$em->mid."+CUSCAR:D:99B:UN'
BGM+933:::SEACR+" . (empty($omid)?  $p->hbn.'-'.$vn : $omid). ":" . $vn . "+".(empty($omid)? 9 : 4)."'
RFF+PQ:PO'\n";
				if(!empty($hbl)) $msg .= "RFF+BM:".$hbl."'\n";
				//use word replace service;
				$msg .= "RFF+BH:".$p->hbn."'
RFF+MB:".$obl."'
NAD+AH+".Yii::app()->params['ics']['abn']."::95'
NAD+VW+".Yii::app()->params['ics']['abn']."::95'
NAD+CN++".Edimsg::strSegment($cnee_name, 35, 2).":".Edimsg::strSegment($cnee_addr, 35, 3)."'
NAD+CZ++".Edimsg::strSegment($cnor_name, 35, 2).":".Edimsg::strSegment($cnor_addr, 35, 3)."'
TDT+20+".$voyage_number."++11++++".$vessel."::11'
LOC+8+".$cn->pod."::6'
LOC+12+".$cn->pod."::6'
LOC+27+".substr($cn->pol, 0, 2)."::5'
LOC+76+".$cn->pol."::6'
LOC+73+".$cn->pol."::6'
CNI++:::".(empty($omid)? 'I' : 'A')."'
RFF+AAQ:".$container_number."'
GID+1'
RFF+ACC:".$container_size."'
GID+1'
RFF+ZZZ:1'
".($canSac ? "GIS+SAC:109:95'\n" : "")."GID+1'
PAC+".$p->pkg."'
PAC+++".$cargo_type.":67:95'
PAC+++GENN:121:95'
PAC+++PK:185:95'
FTX+AAA+++".Edimsg::strSegment(str_replace('/', ',', trim($goods, '/')), 512, 5)."'
MEA+AAE+AAL+KG:".sprintf("%.2f", $p->weight)."'
MEA+AAE+ABJ+CU:".sprintf("%.2f", $volume)."'
MEA+AAE+G+KG:".sprintf("%.2f", $p->weight)."'
";
				$mc = substr_count($msg, "\n")+1;
				$msg .= "UNT+".$mc."+".$em->mid."'";
				$em->msg = $msg;
				if (Yii::app()->params['ics']['testing']) {
					$em->mdata['test'] = 1;
				}
			}else{

			// get related console information
			$cn = $p->consol;
			$hwb = empty($cn->mdata['house_bill'])? $cn->no : $cn->mdata['house_bill'];
			$airline = substr(trim($cn->airline), 0, 2);
			$flight = substr(trim($cn->flight), 2);
			//$flight = preg_replace('/[^\d]+/', '', $cn->flight);
			$awb = preg_replace('/[^\d]+/', '', $cn->awb);


			// only value < 750USD can SAC
			// or 1000 AUD can be SAC (Self Assessed Clearance) only
			if (!empty($_GET['config_check'])) {
				if (!empty($_GET['can_sac'])) {
					$canSac=true;
				} else {
					$canSac=false;
				}
			} else {
				$canSac = $p->canSAC();
			}
			$os = Edimsg::model()->findAll(['condition' => "status = 50 AND type = 'AIRCR-S' AND fid = :id", 'params' => [':id' => $p->id], 'order' => 't.id DESC']);
			$omid = null;
			if (!empty($os)) {
				foreach ($os as $o) {
					if (preg_match('/MWB:'.$awb.'/', $o->msg) && preg_match('/BGM\+933:::AIRCR\+([^\+:]+):\d+\+9\'/', $o->msg, $m)) {
						$omid = $m[1];
						break;
					}
				}
			}
			
			if(Edimsg::model()->count('fid = :id AND type ="AIRCR-S" AND status IN (20, 30, 40)', [':id' => $p->id]) > 0){
				$resp['msg']='There is an ACR message waiting for response from ICS, please try again later.';
				echo json_encode($resp);
				return;
			}

			/*if (empty($omid)) {
				$resp['msg']='Original MSG not found for '.$p->hbn;
				echo json_encode($resp);
				return;
			}*/

			$em = new Edimsg;
			$em->dt = date('Y-m-d H:i:s');
			$em->type = 'AIRCR-S';
			$em->sender = Yii::app()->params['ics']['testing'] ? Yii::app()->params['ics']['test_site'] : Yii::app()->params['ics']['prod_site'];
			$em->receiver = Yii::app()->params['ics']['customs_id'];
			$em->status = 19;
			$em->fid = $p->id;
			$em->save();
			$em->mid = sprintf('%06s', substr($em->id, -6));
			$vn = Edimsg::model()->count('type = :t AND fid = :fid', [':t' => 'AIRCR-S', ':fid' => $p->id]) + 1;
			$msg = "UNH+" . $em->mid . "+CUSCAR:D:99B:UN'
BGM+933:::AIRCR+" . (empty($omid)?  $p->hbn.'-'.$vn : $omid). ":" . $vn . "+".(empty($omid)? 9 : 4)."'
RFF+PQ:PO'
RFF+AWB:" . $hwb . "'\n";
			$msg .= "RFF+HWB:" . $p->hbn . "'
RFF+MWB:" . $awb . "'
NAD+CN++" . Edimsg::strSegment($cnee_name, 35, 2) . ":" . Edimsg::strSegment($cnee_addr, 35, 3) . "'
NAD+CZ++" . Edimsg::strSegment($cnor_name, 35, 2) . ":" . Edimsg::strSegment($cnor_addr, 35, 3) . "'
NAD+VW+" . Yii::app()->params['ics']['abn'] . "::95'
TDT+20+" . $flight . "++6+" . $airline . "::3'
LOC+8+" . $cn->pod . "::6'
LOC+76+" . $cn->pol . "::6'
LOC+12+" . $cn->pod . "::6'
LOC+91+" . $cn->pol . "::6'
DTM+178:" . date("Ymd", strtotime($cn->eta)) . ":102'
CNI+1'
RFF+UCN:" . $p->hbn . "'
MOA+44:" . sprintf("%.2f", $p->getDvalue()) . ":USD'
" . ($canSac ? "GIS+SAC:109:95'\n" : "") . "GID+1'
PAC+" . $p->pkg . "'
FTX+AAA+++" . Edimsg::strSegment(str_replace('/', ',', trim($goods, '/')), 512, 5) . "'
MEA+AAE+G+KG:" . sprintf("%.2f", $p->weight) . "'
";
			$mc = substr_count($msg, "\n") + 1;
			$msg .= "UNT+" . $mc . "+" . $em->mid . "'";
			$em->msg = $msg;
			if (Yii::app()->params['ics']['testing']) {
				$em->mdata['test'] = 1;
			}
		}
			//echo $msg;
			$em->status = 20;
			$em->save();
			$p->save();

			$resp['success'] = 1;
		} else {
			$resp['success']   = 0;
			$resp['msg'] = 'The shipment status is wrong : only for HELD and REPORTED status';
		}
		}
	}

	public function fix_currency()
	{
		$url = 'https://www.ccf.border.gov.au/reference/production/main/';

		if (!$c = @file_get_contents($url)) {
			return false;
		}

		if (preg_match('/(XCHGRATE-P1-EDMAIN-1907270114\.txt)/', $c, $m)) {
			preg_match_all('/(CNY) \d+ \d+\s+([\d\.]+)\s+(\d{8})/', file_get_contents($url . $m[1]), $rs);
			if (!empty($rs[1])) {
				$c2k = array_flip(Currency::$currency_type);
				foreach ($rs[1] as $k => $v) {
					$date = date('Y-m-d', strtotime($rs[3][$k]));
					if ($date <= '2018-11-07') {
						continue;
					}
					if (isset($c2k[$v])) {
						if (Currency::model()->count('date=:date AND type=:t', [':date' => $date, ':t' => $c2k[$v]]) == 0) {
							$model = new Currency;
							$model->currency = $rs[2][$k];
							$model->type = $c2k[$v];
							$model->date = $date;
							$model->save();
						}
					}
				}
			}
		}
	}

	public function edijob_no_yyinv()
	{
		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth([15,15,15,15]);
		$xls->addRow($i++, ['Job No', 'AWB', 'Created', 'ETD']);
		$jobs = EdiJob::model()->findAll('created > "2019-04-01"');
		foreach ($jobs as $job) {
			$bls = BillingLine::model()->findAll('billing_ref = :ref AND org_id IN (954,964) AND billing_cref != "" AND accrual_amount > 0 AND status != 11 AND item_code NOT IN ("GL52", "GL53")', [':ref' => $job->no]);
			if (!empty($bls)) {
				continue;
			}

			$bls = BillingLine::model()->findAll('billing_ref = :ref AND org_id IN (954,964) AND billing_cref = "" AND accrual_amount > 0 AND status != 11', [':ref' => $job->no]);
			if (empty($bls)) {
				continue;
			}

			$xls->addRow($i++, [$job->no, $job->awb, $job->created, @$job->awbconsol->etd]);
		}

		$xls->output('JOB ACCRUAL-NO-INV.xlsx', null, false);
	}

	public function link_credit_note()
	{
		$xero = new XeroAPI;

		$crns = Payment::model()->findAll('type = 5 AND date >= "2019-01-01" AND xero_id = "" AND bank = 91 AND ata != 0');
		foreach ($crns as $crn) {
			sleep(1);
			$xero_credits = $xero->get('Accounting\CreditNote', ['CreditNoteNumber' => $crn->no]);
			if (empty($xero_credits)) {
				continue;
			}
			foreach ($xero_credits as $xero_credit) {
				if ($xero_credit['CreditNoteNumber'] == $crn->no) {
					$crn->xero_id = $xero_credit['CreditNoteID'];
					$crn->save();
				}
			}
		}
	}

	public function invoice_batch()
	{
		$xero = new XeroAPI;
		$results = $xero->get('Accounting\Invoice', ['Type' => 'ACCREC', 'ModifiedAfter' => date('Y-m-d 00:00:00')]);

		foreach ($results as $result) {
			sleep(3);
			// get xero inv and hvlv inv
			$xero_inv = $xero->getByID('Accounting\Invoice', $result['InvoiceID']);
			$inv = Invoice::model()->find(['condition' => 'no LIKE :no AND status IN (2,3,7)', 'params' => [':no' => '%' . $xero_inv['InvoiceNumber'] . '%'], 'order' => 'id DESC']);
			if (empty($inv)) {
				continue;
			}

			// check allocate payment
			if (!empty($xero_inv['Payments'])) {
				foreach ($xero_inv['Payments'] as $item) {
					// get xero pay and hvlv pay
					$xero_payment = $xero->getByID('Accounting\BatchPayment', $item['PaymentID']);
					if (empty($xero_payment)) {
						$xero_payment = $xero->getByID('Accounting\Payment', $item['PaymentID']);
						$time = $xero_payment['UpdatedDateUTC'];
					} else {
						$xero_payment['PaymentID'] = $xero_payment['BatchPaymentID'];
						foreach ($xero_payment['Payments'] as $pay) {
							if ($pay['Invoice']['InvoiceID'] == $result['InvoiceID']) {
								$pay = $xero->getByID('Accounting\Payment', $pay['PaymentID']);
								$time = $pay['UpdatedDateUTC'];
							}
						}
					}
				}
			}
		}
	}

	public function sync_creditnote_by_id()
	{
		$no = $this->prompt('Credit Note ID OR NO.: ');
		$creditnote = Payment::model()->find('id = :n OR no = :n', [':n' => $no]);
		if (!empty($creditnote)) {
			$creditnote->saveCreditNote2Xero();
		} else {
			[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
			$creditnote = Payment::model()->find('id = :n OR no = :n', [':n' => $no]);
			if (!empty($creditnote)) {
				$creditnote->saveCreditNote2Xero();
			} else {
				echo 'invalid no' . PHP_EOL;
			}
		}
	}

	public function check_badatong()
	{
		$file = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'badatong.xlsx';
		$xls = new oExcel;
		$xls->load($file);
		$sheetsArray = $xls->xls->getAllSheets();

		$result = new oExcel;
		$i = 1;
		$invs = [];
		$pays = [];
		$no_pays = [];
		$no_invoices = [];
		$records = [];
		foreach ($sheetsArray as $n => $sheet) {
			$xls->goSheet($n);
			$data = $xls->getAll();
			foreach ($data as $line) {
				if (!empty($line[1]) && !empty($line[9]) && !preg_match('/Total/i', $line[6]) && !preg_match('/Total/i', $line[5])) {
					$no = $line[1];
					$amount = floatval($line[9]);

					$invs[$no][] = $amount;
					$inv = Invoice::model()->find('no = :no', [':no' => $no]);
					if (empty($inv)) {
						$records[$no][$amount] = [$amount, '', 'not found invoice'];
						continue;
					}

					$pay_inv = PayInv::model()->find('inv_id = :inv_id AND amount = :amount', [':inv_id' => $inv->id, ':amount' => $amount]);
					if (empty($pay_inv)) {
						$records[$no][$amount] = [$amount, '', 'not found pay_inv'];
						continue;
					}
					$records[$no][$pay_inv->payment->no . $amount] = [$amount, $pay_inv->payment->no, 'match'];
					$pays[$pay_inv->payment->no] = $pay_inv->payment;
				}
			}
		}

		foreach ($pays as $pay) {
			foreach ($pay->pays as $pay_inv) {
				if (empty($invs[$pay_inv->invoice->no])) {
					$records[$pay_inv->invoice->no][$pay->no . $pay_inv->amount] = [$pay_inv->amount, $pay->no, 'only hvlv'];
				} else {
					foreach ($invs[$pay_inv->invoice->no] as $amount) {
						if ($pay_inv->amount == $amount) {
							continue 2;
						}
					}
					$records[$pay_inv->invoice->no][$pay->no . $pay_inv->amount] = [$pay_inv->amount, $pay->no, 'not match'];
				}
			}
		}

		foreach ($records as $no => $inv_recs) {
			foreach ($inv_recs as $rec) {
				$result->addRow($i++, array_merge([$no], $rec));
			}
		}

		$result->output('badatong_output.xlsx', null, false);
	}

	public function check_badatong_2()
	{
		$file = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'badatong.xlsx';
		$xls = new oExcel;
		$xls->load($file);
		$sheetsArray = $xls->xls->getAllSheets();

		$result = new oExcel;
		$i = 1;
		$invs = [];
		$min = 999999;
		$max = 0;
		foreach ($sheetsArray as $n => $sheet) {
			$xls->goSheet($n);
			$data = $xls->getAll();
			foreach ($data as $line) {
				if (!empty($line[1]) && !empty($line[9]) && !preg_match('/Total/i', $line[6]) && !preg_match('/Total/i', $line[5])) {
					$no = $line[1];
					$amount = floatval($line[9]);

					$invs[$no][$amount] = $amount;
					$invoices = Invoice::model()->findAll('no LIKE :no AND status NOT IN (8,10)', [':no' => explode('-', $no)[0]]);
					foreach ($invoices as $invoice) {
						if ($invoice->id < $min) {
							$min = $invoice->id;
						}
						if ($invoice->id > $max) {
							$max = $invoice->id;
						}
					}
				}
			}
		}

		$result->addRow($i++, ['INV NO', 'HVLV EFT', 'HVLV CRN', 'HVLV TOTAL', 'BADATONG', 'DIFF']);

		$invoices = Invoice::model()->findAll(['condition' => 'id >= :min AND id <= :max AND to_id = 1316 AND status NOT IN (8,10)', 'params' => [':min' => $min, ':max' => $max], 'order' => 'no ASC']);
		foreach ($invoices as $invoice) {
			$pay_invs = PayInv::model()->with('payment')->findAll('payment.status != 9 AND payment.bank != 91 AND date >= "2019-01-01" AND inv_id = :inv_id', [':inv_id' => $invoice->id]);
			$hvlv_eft = 0;
			$hvlv_crn = 0;
			foreach ($pay_invs as $pay_inv) {
				if ($pay_inv->payment->type != 5) {
					$hvlv_eft += $pay_inv->amount;
				} else {
					$hvlv_crn += $pay_inv->amount;
				}
			}

			$bada_eft = 0;
			if (!empty($invs[$invoice->no])) {
				$bada_eft = array_sum($invs[$invoice->no]);
			}

			$result->addRow($i++, [$invoice->no, $hvlv_eft, $hvlv_crn, $hvlv_eft + $hvlv_crn, $bada_eft, $hvlv_eft + $hvlv_crn - $bada_eft]);
		}

		$result->output('badatong_output.xlsx', null, false);
	}

	public function test_xero()
	{
		$xero = new XeroAPI;
		$results = $xero->get('Accounting\Invoice', ['Type' => 'ACCREC', 'ModifiedAfter' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 12 day'))]);

		foreach ($results as $result) {
			// get xero inv and hvlv inv
			$inv = Invoice::model()->find(['condition' => 'no LIKE :no AND status IN (2,3,7)', 'params' => [':no' => '%' . $result['InvoiceNumber'] . '%'], 'order' => 'id DESC']);
			if (empty($inv) || $result['AmountPaid'] == 0) {
				continue;
			}
			$xero_inv = $xero->getByID('Accounting\Invoice', $result['InvoiceID']);
			sleep(1);

			// check allocate payment
			if (!empty($xero_inv['Payments'])) {
				foreach ($xero_inv['Payments'] as $item) {
					// get xero pay and hvlv pay
					$xero_payment = $xero->getByID('Accounting\BatchPayment', $item['PaymentID']);
					if (empty($xero_payment)) {
						$xero_payment = $xero->getByID('Accounting\Payment', $item['PaymentID']);
						$time = $xero_payment['UpdatedDateUTC'];
					} else {
						$xero_payment['PaymentID'] = $xero_payment['BatchPaymentID'];
						foreach ($xero_payment['Payments'] as $pay) {
							if ($pay['Invoice']['InvoiceID'] == $result['InvoiceID']) {
								$pay = $xero->getByID('Accounting\Payment', $pay['PaymentID']);
								$time = $pay['UpdatedDateUTC'];
							}
						}
					}

					// check old payment
					if ($result['InvoiceNumber'] == 'ED70465') {
						echo strtotime($time->format('Y-m-d H:i:s')) . PHP_EOL;
						echo strtotime(date('Y-m-d H:i:s') . ' - 12 day') . PHP_EOL;
					}
					if (empty($time) || strtotime($time->format('Y-m-d H:i:s')) < strtotime(date('Y-m-d H:i:s') . ' - 12 day')) {
						continue;
					}
				}
			}
		}
	}

	public function reconcile_invoice_single()
	{
		$xero = new XeroAPI;
		$results = $xero->get('Accounting\Invoice', ['Type' => 'ACCREC', 'InvoiceNumber' => 'ED70465']);

		foreach ($results as $result) {
			// get xero inv and hvlv inv
			$inv = Invoice::model()->find(['condition' => 'no LIKE :no AND status IN (2,3,7)', 'params' => [':no' => '%' . $result['InvoiceNumber'] . '%'], 'order' => 'id DESC']);
			if (empty($inv) || $result['AmountPaid'] == 0) {
				continue;
			}
			$xero_inv = $xero->getByID('Accounting\Invoice', $result['InvoiceID']);
			sleep(1);

			// check allocate payment
			if (!empty($xero_inv['Payments'])) {
				foreach ($xero_inv['Payments'] as $item) {
					// get xero pay and hvlv pay
					$xero_payment = $xero->getByID('Accounting\BatchPayment', $item['PaymentID']);
					if (empty($xero_payment)) {
						$xero_payment = $xero->getByID('Accounting\Payment', $item['PaymentID']);
						$time = $xero_payment['UpdatedDateUTC'];
					} else {
						$xero_payment['PaymentID'] = $xero_payment['BatchPaymentID'];
						foreach ($xero_payment['Payments'] as $pay) {
							if ($pay['Invoice']['InvoiceID'] == $result['InvoiceID']) {
								$pay = $xero->getByID('Accounting\Payment', $pay['PaymentID']);
								$time = $pay['UpdatedDateUTC'];
							}
						}
					}

					// check old payment
					if (empty($time) || strtotime($time->format('Y-m-d H:i:s')) < strtotime(date('Y-m-d H:i:s') . ' - 12 day')) {
						continue;
					}

					$payment = Payment::model()->find('xero_id = :xero_id AND status != 9', [':xero_id' => $xero_payment['PaymentID']]);

					// get xero account and hvlv account
					$xero_account = $xero->getByID('Accounting\Account', $item['Account']['AccountID']);
					$account = BankAccount::model()->find('xero_id = :xero_id', [':xero_id' => $xero_account['AccountID']]);

					// further check pay_inv
					if (empty($payment)) {
						$pay_inv = PayInv::model()->with('payment')->find('t.inv_id = :inv_id AND t.amount = :amount AND payment.status != 9', [':inv_id' => $inv->id, ':amount' => $item['amount']]);
						if (!empty($pay_inv)) {
							$payment = $pay_inv->payment;
							$payment->xero_id = $xero_payment['PaymentID'];
							$payment->save();

							$msg = 'Sync payment with xero for invoice ' . $inv->no . ' ' . $payment->no;
							$tmp = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'xero' . DIRECTORY_SEPARATOR;
							file_put_contents($tmp . 'xero_sync.log', date('Y-m-d H:i:s') . ' ' . $msg . "\n", FILE_APPEND);
						}
					}

					if (empty($payment)) {
						$payment = new Payment;
						$payment->org_id = $inv->to_id;
						$payment->date = $xero_payment['Date']->format('Y-m-d');
						$payment->transaction_date = $xero_payment['Date']->format('Y-m-d');
						$payment->status = Payment::PAYMENT_STATUS_POSTED;
						$payment->dpmt = $inv->dpmt;
						$payment->bank = $account->id;
						$payment->currency = array_search($xero_account['CurrencyCode'], Invoice::$currencies);
						$payment->amount = $xero_payment['Amount'];
						$payment->ref = !empty($xero_payment['BatchPaymentID']) ? '' : $xero_payment['Reference'];
						$payment->ata = 0;
						$payment->type = Payment::PAYMENT_TYPE_EFT;
						$payment->xero_id = $xero_payment['PaymentID'];
						$payment->save();

						if ($payment->getErrors()) {
							$payment->date = date('Y-m-d');
							$payment->save();
						}

						$msg = 'New payment created for invoice ' . $inv->no . ' ' . $payment->no;
						$tmp = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'xero' . DIRECTORY_SEPARATOR;
						file_put_contents($tmp . 'xero_sync.log', date('Y-m-d H:i:s') . ' ' . $msg . "\n", FILE_APPEND);
					}

					$pay_inv = PayInv::model()->with('payment')->find('t.inv_id = :inv_id AND t.pay_id = :pay_id AND payment.status != 9', [':inv_id' => $inv->id, ':pay_id' => $payment->id]);
					if (empty($pay_inv)) {
						$pay_inv = new PayInv;
						$pay_inv->inv_id = $inv->id;
						$pay_inv->pay_id = $payment->id;
						$pay_inv->amount = $item['Amount'];
						$pay_inv->transaction_date = $item['Date']->format('Y-m-d');
						$pay_inv->exrate = $item['CurrencyRate'];
						$pay_inv->save();

						$payment->updateAta();

						$msg = 'New pay_inv created for invoice ' . $inv->no . ' ' . $payment->no;
						$tmp = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'xero' . DIRECTORY_SEPARATOR;
						file_put_contents($tmp . 'xero_sync.log', date('Y-m-d H:i:s') . ' ' . $msg . "\n", FILE_APPEND);
					}
				}
			}

			// check allocate credit note
			if (!empty($xero_inv['CreditNotes'])) {
				foreach ($xero_inv['CreditNotes'] as $item) {
					// get xero credit and hvlv credit
					$xero_credit = $xero->getByID('Accounting\CreditNote', $item['CreditNoteID']);
					$credit = Payment::model()->find('no = :no', [':no' => $item['CreditNoteNumber']]);

					if (!empty($credit)) {
						if (empty($credit->xero_id) || $credit != $item['CreditNoteID']) {
							$credit->xero_id = $item['CreditNoteID'];
							$credit->update('xero_id');
						}
					} else if (empty($credit)) {
						continue;
					}

					$pay_inv = PayInv::model()->find('inv_id = :inv_id AND pay_id = :pay_id', [':inv_id' => $inv->id, ':pay_id' => $credit->id]);
					if (empty($pay_inv)) {
						$pay_inv = new PayInv;
						$pay_inv->inv_id = $inv->id;
						$pay_inv->pay_id = $credit->id;
						$pay_inv->amount = $item['Total'];
						$pay_inv->transaction_date = date('Y-m-d');
						$pay_inv->exrate = 1;
						$pay_inv->save();

						$credit->updateAta();
					}
				}
			}

			$inv->checkPaid();
			$inv->save();
			sleep(2);
		}
	}

	public function check_ar()
	{
		$file = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'checkar.xlsx';
		$xls = new oExcel;
		$xls->load($file);
		$data = $xls->getAll();

		$newxls = new oExcel;
		$i = 1;
		$newxls->addRow($i++, ['No', 'Ref', 'Balance', 'HVLV Balance', 'HVLV Status']);

		unset($data[1]);
		foreach ($data as $line) {
			$no = $line[1];
			$ref = $line[2] ? $line[2] : $line[1];
			$amount = $line[3];

			$invoice = Invoice::model()->find(['condition' => 'no LIKE :no OR no LIKE :ref', 'params' => [':no' => '%' . explode('-', $no)[0] . '%', ':ref' => '%' . explode('-', $ref)[0] . '%'], 'order' => 'id DESC']);
			$credit = Payment::model()->find('no = :no OR no = :ref', [':no' => $no, ':ref' => $ref]);
			if (!empty($invoice)) {
				$diff = floatval($amount) - floatval($invoice->getBalance());
				$diff = $diff != 0 ? $diff : '';
				$newxls->addRow($i++, [$no, $ref, $amount, $invoice->getBalance(), $invoice->getStatus(), '', $diff]);
			} else if (!empty($credit)) {
				$diff = $amount + $credit->ata;
				$diff = $diff != 0 ? $diff : '';
				$newxls->addRow($i++, [$no, $ref, $amount, -$credit->ata, $credit->getStatus(), '', $diff]);
			} else {
				$newxls->addRow($i++, [$no, $ref, $amount, '', '', '', $amount]);
			}
		}

		$newxls->output('checkar_output.xlsx', null, false);
	}

	public function fix_void_invoice()
	{
		$no = $this->prompt('Invoice ID OR NO.: ');
		$new = $this->prompt('Sync AS: ');
		$inv = Invoice::model()->find(['condition' => 'no = "' . $no . '"', 'order' => 'id DESC']);

		$xdata = new stdClass;
		$xdata->no = $inv->no;
		$xdata->currency = $inv->currency;
		$xdata->cust = $inv->cust;
		$xdata->date = $inv->date;
		$xdata->due = $inv->due;
		$xdata->gst = 1;
		$xdata->dpt_id = $inv->dpt_id;
		$costs = [];

		// xero account
		if ($inv->dpmt == 10) {
			$code = 83000;
		} else if ($inv->dpmt == 20) {
			$code = 81000;
		} else if ($inv->dpmt == 30) {
			$code = 82000;
		} else if ($inv->dpmt == 40) {
			if ($inv->type == 60) {
				$code = 85000;
			} else {
				$code = 85050;
			}
		}

		foreach ($inv->lines as $line) {
			if (empty($costs[$line->tax . $code . $inv->dpmt])) {
				$costs[$line->tax . $code . $inv->dpmt] = array(
					'qty' => 1,
					'det' => '',
					'ccode' => $code,
					'taxType' => $line->tax,
					'dept' => BillingLine::$xero_segments[$inv->dpmt / 10],
					'amount' => 0,
				);
			}

			$costs[$line->tax . $code . $inv->dpmt]['amount'] += $line->amount * ($line->qty == 0 ? 1 : $line->qty);
		}

		if (!empty($costs)) {
			$xdata->lines = array_values($costs);

			// invoice type in Xero
			// ACCPAY	A bill – commonly known as a Accounts Payable or supplier invoice
			// ACCREC	A sales invoice – commonly known as an Accounts Receivable or customer invoice
			$invoice = new XeInvoice('ACCREC');

			$invoice->status = 'AUTHORISED'; // approved , waiting for pay
			$invoice->invoiceNumber = $new;

			$curIndex = $xdata->currency;
			$curCode = 'AUD';
			if (isset(Invoice::$currencies[$curIndex])) {
				$curCode = Invoice::$currencies[$curIndex];
			}
			$invoice->currencyCode = $curCode;

			$contact = new XeContact();
			$contact->name = $xdata->cust->name;
			$contact->accountNumber = 'PORG-' . $xdata->cust->id;
			$invoice->contact = $contact;
			$invoice->date = $xdata->date;
			$invoice->dueDate = $xdata->due;

			// Exclusive - exclude GST
			// Inclusive - include GST
			// NoTax
			if ($xdata->gst > 0) {
				$invoice->lineAmountTypes = 'Inclusive';
			} else {
				$invoice->lineAmountTypes = 'NoTax';
			}

			// get all items
			foreach ($xdata->lines as $line) {
				$itemData = array();
				$itemData['quantity'] = $line['qty'];
				$itemData['accountCode'] = $line['ccode'];
				$itemData['description'] = '【' . $line['ccode'] . '】 ' . $line['det'];
				$itemData['unitAmount'] = $line['amount'];
				$itemData['taxType'] = $line['taxType'];

				$tracking = Invoice::getTrackingInfo($xdata, isset($line['dept']) ? $line['dept'] : '');
				$itemData['trackingName'] = $tracking['name'];
				$itemData['trackingValue'] = $tracking['value'];

				$invoice->addLineItem($itemData);
			}

			try {
				$rt = $invoice->save();
				if ($rt && $invoice->statusAttributeString == 'OK') {
					// set sync to Xero successful flag
					$invoice = Invoice::model()->find(['condition' => 'no LIKE :no AND status IN (2,3,7)', 'params' => [':no' => '%' . $xdata->no . '%'], 'order' => 'id DESC']);
					if (!empty($invoice)) {
						$invoice->sync_xero = 1;
						$invoice->update('sync_xero');
					}
				} else {
					// log error message
					Yii::app()->xero->log('failed to save invoice to xero for : ' . $xdata->no, Xero::LOG_LEVEL_ERR);
				}
			} catch (Exception $mye) {
				$msg = 'failed to save invoice - ' . $mye->getMessage();
				$msg .= PHP_EOL;
				$msg .= 'Invoice Data : ' . json_encode($invoice, JSON_PRETTY_PRINT);
				Yii::app()->xero->log($msg, Xero::LOG_LEVEL_ERR);
			}
		}
	}

	public function fix_void_creditnote()
	{
		$no = $this->prompt('CreditNote ID OR NO.: ');
		$new = $this->prompt('Sync AS: ');
		$payment = Payment::model()->find(['condition' => 'no LIKE "%' . $no . '%"', 'order' => 'id DESC']);

		$xdata = new stdClass;
		$xdata->no = $payment->no;
		$xdata->currency = $payment->currency;
		$xdata->cust = $payment->cust;
		$xdata->date = $payment->date;
		$xdata->due = $payment->date;
		$xdata->gst = 1;
		$xdata->dpt_id = 106;

		// xero account
		if ($payment->dpmt == 10) {
			$code = 83000;
		} else if ($payment->dpmt == 20) {
			$code = 81000;
		} else if ($payment->dpmt == 30) {
			$code = 82000;
		} else if ($payment->dpmt == 40) {
			if ($payment->type == 60) {
				$code = 85000;
			} else {
				$code = 85050;
			}
		}

		if (!empty($payment->amount)) {
			$costs = [['qty' => 1, 'det' => $payment->ref, 'ccode' => $code, 'taxType' => 'OUTPUT', 'dept' => BillingLine::$xero_segments[$payment->dpmt / 10], 'amount' => $payment->amount]];

			$xdata->lines = array_values($costs);

			$creditNote = new XeCreditNote();

			$creditNote->type = 'ACCRECCREDIT';
			$creditNote->status = 'AUTHORISED'; // approved , waiting for pay
			$creditNote->creditNoteNumber = $new;

			$curIndex = $xdata->currency;
			$curCode = 'AUD';
			if (isset(Invoice::$currencies[$curIndex])) {
				$curCode = Invoice::$currencies[$curIndex];
			}
			$creditNote->currencyCode = $curCode;

			$contact = new XeContact();
			$contact->name = $xdata->cust->name;
			$contact->accountNumber = 'PORG-' . $xdata->cust->id;
			$creditNote->contact = $contact;
			$creditNote->date = $xdata->date;

			// Exclusive - exclude GST
			// Inclusive - include GST
			// NoTax
			if ($xdata->gst > 0) {
				$creditNote->lineAmountTypes = 'Exclusive';
			} else {
				$creditNote->lineAmountTypes = 'NoTax';
			}

			// get all items
			foreach ($xdata->lines as $line) {
				$itemData = array();
				$itemData['quantity'] = $line['qty'];
				$itemData['accountCode'] = $line['ccode'];
				$itemData['description'] = '【' . $line['ccode'] . '】 ' . $line['det'];
				$itemData['unitAmount'] = $line['amount'];

				$tracking = Invoice::getTrackingInfo($xdata, isset($line['dept']) ? $line['dept'] : '');
				$itemData['trackingName'] = $tracking['name'];
				$itemData['trackingValue'] = $tracking['value'];

				$creditNote->addLineItem($itemData);
			}

			try {
				$rt = $creditNote->save();
				if ($rt && $creditNote->statusAttributeString == 'OK') {
					// set sync to Xero successful flag
					$model = Payment::model()->find('no = :no', [':no' => $xdata->no]);
					if (!empty($model)) {
						$model->xero_id = $creditNote->creditNoteID;
						$model->update('xero_id');
					}
				} else {
					// log error message
					Yii::app()->xero->log('failed to save invoice to xero for : ' . $xdata->no, Xero::LOG_LEVEL_ERR);
				}
			} catch (Exception $mye) {
				$msg = 'failed to save invoice - ' . $mye->getMessage();
				$msg .= PHP_EOL;
				$msg .= 'Invoice Data : ' . json_encode($creditNote, JSON_PRETTY_PRINT);
				Yii::app()->xero->log($msg, Xero::LOG_LEVEL_ERR);
			}
		}
	}

	public function check_startrack()
	{
		$nos = ["7RFZ50013986","7RFZ50013988","7RFZ50013977","7RFZ50014029","7RFZ50014050","7RFZ50014067","7RFZ50013987","7RFZ50013995","7RFZ50013862","7RFZ50014022","7RFZ50014042","7RFZ50014052","7RFZ50013732","7RFZ50014086","7RFZ50014102","7RFZ50014104","7RFZ50014116","7RFZ50013967","7RFZ50014011","7RFZ50013978","7RFZ50013959","7RFZ50013920","7RFZ50013960","7RFZ50013980","7RFZ50013989","7RFZ50013843","7RFZ50014009","7RFZ50014113","7RFZ50013884","7RFZ50013909","7RFZ50013915","7RFZ50013939","7RFZ50013941","7RFZ50013950","7RFZ50013952","7RFZ50013953","7RFZ50013956","7RFZ50013965","7RFZ50013966","7RFZ50013976","7RFZ50013979","7RFZ50013984","7RFZ50013985","7RFZ50013992","7RFZ50014006","7RFZ50013970","7RFZ50013971","7RFZ50013990","7RFZ50014002","7RFZ50014005","7RFZ50014089","7RFZ50014035","7RFZ50014040","7RFZ50014044","7RFZ50013750","7RFZ50014003","7RFZ50014032","7RFZ50014033","7RFZ50014061","7RFZ50014062","7RFZ50014064","7RFZ50014065","7RFZ50014068","7RFZ50014072","7RFZ50014073","7RFZ50014083","7RFZ50014091","7RFZ50014093","7RFZ50014096","7RFZ50014098","7RFZ50014099","7RFZ50014107","7RFZ50014111","7RFZ50014138","7RFZ50014077","7RFZ50014078","7RFZ50014079","7RFZ50014080","7RFZ50014081","7RFZ50014097","7RFZ50014127","7RFZ50013958","7RFZ50013881","7RFZ50014041","7RFZ50013913","7RFZ50013938","7RFZ50013940","7RFZ50013942","7RFZ50013961","7RFZ50013964","7RFZ50013972","7RFZ50013975","7RFZ50013981","7RFZ50013991","7RFZ50014076","7RFZ50013927","7RFZ50014012","7RFZ50014018","7RFZ50014030","7RFZ50014039","7RFZ50014048","7RFZ50014013","7RFZ50014027","7RFZ50014028","7RFZ50014049","7RFZ50014092","7RFZ50014108","7RFZ50013857","7RFZ50013859","7RFZ50014132","7RFZ50014051","7RFZ50014082","7RFZ50014101","7RFZ50014060","7RFZ50014053","7RFZ50014055","7RFZ50013957","7RFZ50014090","7RFZ50014074","7RFZ50014109","7RFZ50014133","7RFZ50014134","7RFZ50014069","7RFZ50014070","7RFZ50014071","7RFZ50014105","7RFZ50013968","7RFZ50014056","7RFZ50013954","7RFZ50013973","7RFZ50014046","7RFZ50013901","7RFZ50014135","7RFZ50014100","7RFZ50014019"];
		foreach ($nos as $no) {
			$shipment = ImParcel::model()->find('ref = :ref', [':ref' => $no]);
			if (empty($shipment)) {
				echo $no . PHP_EOL;
			}
		}
	}

	public function fix_afyy_create()
	{
		$afhs = AFInvoiceReconciliationHistory::model()->with('af')->findAll('af.supplier_id IN (954,964)');
		foreach ($afhs as $afh) {
			$file = FileRepo::model()->findByPk($afh->attached_file_id);
			$afh->created = $file->date;
			$afh->save();
		}
	}

	public function fix_yy_billing()
	{
		$lines = BillingLine::model()->findAll('status = 3 AND sync_xero = 1 AND billing_id = 0 AND org_id in (964,954)');
		foreach ($lines as $line) {
			Billing::linkLine($line, false, $line->status);
		}
	}

	public function get_xero_invoice()
	{
		$xero = new XeroAPI;
		$results = $xero->get('Accounting\Invoice', ['Type' => 'ACCREC', 'InvoiceNumber' => $this->prompt('Invoice: ')]);
		foreach ($results as $result) {
			echo json_encode($result);
		}
	}

	public function get_xero_creditnote()
	{
		$xero = new XeroAPI;
		$xero_credit = $xero->getByID('Accounting\CreditNote', $this->prompt('CRN-XERO: '));
		echo json_encode($xero_credit);
	}

	public function fix_broker_billing_gst()
	{
		$afs = AFInvoiceReconciliation::model()->findAll('supplier_id = 1828');
		foreach ($afs as $af) {
			$af->updateAmount();
		}
	}

	public function check_crn_sync()
	{
		$xero = new XeroAPI;
		$results = $xero->get('Accounting\Invoice', ['Type' => 'ACCREC', 'ModifiedAfter' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 10 day'))]);
		foreach ($results as $result) {
			// get xero inv and hvlv inv
			$result['InvoiceNumber'] = explode('-', $result['InvoiceNumber'])[0];
			$inv = Invoice::model()->with('payments', 'payments.payment')->find(['condition' => 't.no LIKE :no AND t.status IN (6,8) AND payments.sync_xero = 0 AND payment.type = 5 AND payment.bank = 91 AND payment.status = 6', 'params' => [':no' => '%' . $result['InvoiceNumber'] . '%'], 'order' => 't.id DESC']);
			if (empty($inv) || ($result['AmountCredited'] == 0)) {
				continue;
			}
			$xero_inv = $xero->getByID('Accounting\Invoice', $result['InvoiceID']);
			sleep(1);

			// check allocate credit note
			if (!empty($xero_inv['CreditNotes'])) {
				foreach ($xero_inv['CreditNotes'] as $item) {
					// get xero credit and hvlv credit
					$xero_credit = $xero->getByID('Accounting\CreditNote', $item['CreditNoteID']);
					$count ++;
					$credit = Payment::model()->find('no = :no', [':no' => explode('-', $item['CreditNoteNumber'])[0]]);

					if (!empty($credit)) {
						if (empty($credit->xero_id) || $credit != $item['CreditNoteID']) {
							$credit->xero_id = $item['CreditNoteID'];
							$credit->update('xero_id');
						}
					} else if (empty($credit)) {
						continue;
					}

					$pay_inv = PayInv::model()->find('inv_id = :inv_id AND pay_id = :pay_id', [':inv_id' => $inv->id, ':pay_id' => $credit->id]);
					if (!empty($pay_inv)) {
						$pay_inv->sync_xero = 1;
						$pay_inv->save();
					}
				}
			}
		}
	}

	public function round_number($num)
	{
		return round($num * 100) / 100;
	}

	public function getXeroBillings()
	{
		$results = $this->getXeroBillingsWithLines();
		$xero_billings = [];
		foreach ($results as $result) {
			if ($result['Status'] == 'VOIDED') continue;
			$no = $result['InvoiceNumber'];
			if (substr($no, strlen($no) - 2) == '-1' || substr($no, strlen($no) - 2) == '-2') {
				$no = substr($no, 0, strlen($no) - 2);
			}
			$no = strtolower(trim($no));
			$name = strtolower(trim($result['Contact']['Name']));
			if (empty($xero_billings[$no][$name])) {
				$xero_billings[$no][$name] = ['no' => $no, 'subtotal' => 0, 'gst' => 0, 'date' => explode(' ', json_decode(json_encode($result['Date']), true)['date'])[0], 'account_code' => []];
			}
			$xero_billings[$no][$name]['subtotal'] += $result['Subtotal'];
			$xero_billings[$no][$name]['gst'] += $result['TotalTax'];
			foreach ($result['LineItems'] as $item) {
				$xero_billings[$no][$name]['account_code'][] = $item['AccountCode'];
			}
		}

		$lines = BillingLine::model()->with('billing')->findAll('t.status NOT IN (1,11)');
		$hvlv_billings = [];
		foreach ($lines as $line) {
			$no = $line->billing_cref;
			if (substr($no, strlen($no) - 2) == '-1' || substr($no, strlen($no) - 2) == '-2') {
				$no = substr($no, 0, strlen($no) - 2);
			}
			$no = strtolower(trim($no));
			$name = strtolower(trim($line->cust->name));
			if (empty($hvlv_billings[$no][$name])) {
				$hvlv_billings[$no][$name] = ['no' => $no, 'subtotal' => 0, 'gst' => 0, 'date' => @$line->billing->date, 'line_date' => [], 'charge_code' => []];
			}
			$hvlv_billings[$no][$name]['line_date'][] = $line->date;
			if ($line->type != 6) {
				$hvlv_billings[$no][$name]['subtotal'] += $line->actual_amount;
				$hvlv_billings[$no][$name]['gst'] += $line->gst_amount;
			} else {
				$hvlv_billings[$no][$name]['subtotal'] += $this->round_number($line->actual_amount - $line->gst_amount);
				$hvlv_billings[$no][$name]['gst'] += $line->gst_amount;
			}
			$hvlv_billings[$no][$name]['charge_code'][] = $line->charge_code;
		}

		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['XERO INVOICE NO', 'XERO AMOUNT', 'XERO GST', 'XERO DATE', 'XERO ACCOUNT CODE', 'HVLV INVOICE NO', 'HVLV AMOUNT', 'HVLV GST', 'HVLV DATE', 'HVLV LINE DATE', 'HVLV CHARGE CODE', 'AMOUNT DIFF', 'DATE DIFF', 'DATE DIFF 2', 'ORG']);
		foreach ($xero_billings as $no => $orgs) {
			foreach ($orgs as $name => $xero) {
				if (!empty($hvlv_billings[$no][$name])) {
					$hvlv = $hvlv_billings[$no][$name];

					if ($this->round_number($xero['subtotal'] + $xero['gst'] - $hvlv['subtotal'] - $hvlv['gst']) == 0 && ($xero['date'] == $hvlv['date'] ? 'Equal' : 'Not') == 'Equal' && ($hvlv['date'] == implode(',', array_unique($hvlv['line_date'])) ? 'Equal' : 'Not') == 'Equal') continue;

					$xls->addRow($i++, [$xero['no'], $xero['subtotal'], $xero['gst'], $xero['date'], implode(',', array_unique($xero['account_code'])), $hvlv['no'], $hvlv['subtotal'], $hvlv['gst'], $hvlv['date'], implode(',', array_unique($hvlv['line_date'])), implode(',', array_unique($hvlv['charge_code'])), $this->round_number($xero['subtotal'] + $xero['gst'] - $hvlv['subtotal'] - $hvlv['gst']), $xero['date'] == $hvlv['date'] ? 'Equal' : 'Not', $hvlv['date'] == implode(',', array_unique($hvlv['line_date'])) ? 'Equal' : 'Not', $name]);
				} else {
					$xls->addRow($i++, [$xero['no'], $xero['subtotal'], $xero['gst'], $xero['date'], implode(',', array_unique($xero['account_code'])), '', '', '', '', '', '', $this->round_number($xero['subtotal'] + $xero['gst']), 'Not', 'Not', $name]);
				}
			}

			if (!empty($hvlv_billings[$no])) {
				foreach ($hvlv_billings[$no] as $name => $hvlv) {
					if (empty($xero_billings[$no][$name])) {
						$xls->addRow($i++, ['', '', '', '', '', $hvlv['no'], $hvlv['subtotal'], $hvlv['gst'], $hvlv['date'], implode(',', array_unique($hvlv['line_date'])), implode(',', array_unique($hvlv['charge_code'])), -$this->round_number($hvlv['subtotal'] + $hvlv['gst']), 'Not', 'Not', $name]);
					}
				}
			}
		}

		foreach ($hvlv_billings as $no => $orgs) {
			if (empty($xero_billings[$no])) {
				foreach ($orgs as $name => $hvlv) {
					if (strtotime($hvlv['date']) < strtotime(date('2018-07-01'))) continue;
					$xls->addRow($i++, ['', '', '', '', '', $hvlv['no'], $hvlv['subtotal'], $hvlv['gst'], $hvlv['date'], implode(',', array_unique($hvlv['line_date'])), implode(',', array_unique($hvlv['charge_code'])), -$this->round_number($hvlv['subtotal'] + $hvlv['gst']), 'Not', 'Not', $name]);
				}
			}
		}

		$xls->output('billing diff.xlsx', null, false);
	}

	public function fix_broker_billing_subtotal()
	{
		$afs = AFInvoiceReconciliation::model()->findAll('supplier_id = 1828 AND history_id = 1516');
		foreach ($afs as $af) {
			$af->updateAmount();
			if ($af->invoice) {
				$af->invoice->billing_subtotal = $af->subtotal;
				$af->invoice->billing_gst = $af->gst;
				$af->invoice->nolog = true;
				$af->invoice->save();
			}
		}
	}

	public function getPCALocation()
	{
		$custs = WmsAPI::model()->findAll('type = :type', [':type' => WmsAPI::WMS_API_TYPE_SHOPIFY]);
		foreach ($custs as $cust) {
			$config = array(
				'store_domain' => $cust->domain,
				'api_key' => $cust->api_key,
				'api_secret' => $cust->api_secret,
				'org_id' => $cust->org_id,
			);
			$shopify = new ShopifyAPI($config);
			$l = $shopify->getPCALocation();
			echo json_encode($l);
		}
	}

	public function fix_putaway_record()
	{
		$rs = ImParcel::model()->findAll('JSON_VALUE(meta, "$.location") IS NOT NULL AND JSON_VALUE(meta, "$.rts_scan_date") IS NULL');
		foreach ($rs as $shipment) {
			$flag = false;
			foreach ($shipment->logs as $log) {
				if (preg_match('/' . date('Y-m-d') . '/', $log->time) && $log->user_id == 568) {
					if (in_array($shipment->ref, ["AMQ5148861","AMQ5163089","AMQ5224682","ML0000771562","RC0001204736","RC0001210791","ML0000794275","RC0001241567","33G7K9242470","RC0001264249","RC0001268405","RC0001291111","33G7K9267162","33G7K9267795","33G7K9268030","33G7K9269492","RC0001301310","33G7K9272326","RC0001307774","RC0001309946","RC0001315405","RC0001321688","33G7K9282375","33G7K9284181","RC0001329272","33G7K9287388","RC0001340020","33G7K9289202","33G7K9291678","33G7K9292169","RC0001351157","ML0000839013","33G7K9294585","AMQ5330895","RC0001371641","33G7K9295949","RC0001386912","33G7K9298386","RC0001389453","RC0001390954","ML0000847585","33G7K9298792","ML0000848939","RC0001403412","RC0001403696","ML0000849921","33G7K9300105","RC0001406730","RC0001408336","33G7K9300669","33G7K9300726","33G7K9301438","RC0001412734","RC0001415637","RC0001415972","RC0001417714","33G7K9303312","RC0001418248","RC0001420072","RC0001420625","RC0001421549","RC0001422707","33G7K9304073","RC0001423115","RC0001424165","RC0001424192","RC0001426631","RC0001427989","RC0001428742","BD0007519264","RC0001429198","RC0001429283","RC0001432094","RC0001432308","33G7K9305864","RC0001435111","BD0007531464","33G7K9306678","RC0001437300","RC0001440045","BD0007543210","33G7K9308577","BD0007560579","BD0007560669","RC0001453672","33G7K9314155","33G7K9315070","RC0001465411","RC0001466192","RC0001466644","RC0001466664","7RFZ50015371","33G7K9317696","BD0007603096","33G7K9318757","33G7K9319454","BD0007610117","BD0007610762","33G7K9320245","AMQ5344291","BD0007682077"])) {
						$flag = true;
					} else {
						echo $shipment->ref . ' ' . $shipment->getStatus() . ' ' . $log->time . PHP_EOL;
					}
					break;
				}
			}

			if ($flag) {
				$shipment->status = 80;
				$shipment->mdata['rts_scan_date'] = date('Y-m-d');
				$shipment->cbwf = $shipment->cbwf | 256;
				$shipment->update('status', 'meta', 'cbwf');

				$shipment_scan=new ShipmentScan;
				$shipment_scan->pid=$shipment->id;
				$shipment_scan->user_id=568;
				$shipment_scan->type=4;
				$shipment_scan->weight=round($shipment->weight/$shipment->pkg, 2);
				$shipment_scan->pno=1;
				$shipment_scan->warehouse=106;
				$shipment_scan->pkg=1;
				$shipment_scan->scan_time=date('Y-m-d H:i:s');
				$shipment_scan->save();
			}
		}
	}

	public function fix_fix_putaway_record()
	{
		$rs = ImParcel::model()->findAll('status = 80 AND JSON_VALUE(meta, "$.location") IS NOT NULL');
		foreach ($rs as $shipment) {
			if (!empty($shipment->getTranshipImparcel())) {
				$shipment->status = 70;
				unset($shipment->mdata['rts_scan_date']);
				$shipment->cbwf &= ~256;
				$shipment->update('status', 'meta', 'cbwf');
			}
		}
	}

	public function fix_allocate_chargecode()
	{
		$consols = ImcoConsol::model()->findAll('no IN ("C19091103SHA", "C19090930SHA")');
		foreach ($consols as $consol) {
			foreach ($consol->shipments as $shipment) {
				if (empty($shipment->mdata['chargecode'])) {
					$other = ImParcel::model()->find('hbn = :hbn AND status = 100', [':hbn' => $shipment->hbn]);
					if (empty($other)) {
						echo $shipment->hbn . ' empty other' . PHP_EOL;
						continue;
					}
					if (empty($other->mdata['chargecode'])) {
						echo $shipment->hbn . ' empty other chargecode' . PHP_EOL;
						continue;
					}
					$shipment->mdata['chargecode'] = $other->mdata['chargecode'];
					$shipment->update('meta');
					echo $shipment->hbn . ' done' . PHP_EOL;
				}
			}
		}
	}

	public function delete_rts_invoice()
	{
		$invs = Invoice::model()->findAll('type = 36 AND (date = "2019-10-12" OR date = "2019-10-11") AND status != 10');
		echo count($invs) . PHP_EOL;
		foreach ($invs as $inv) {
			$inv->revoke();
			sleep(1);
		}
	}

	public function fix_st()
	{
		$file = $this->prompt('File: ');
		$file = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'startrack' . DIRECTORY_SEPARATOR . $file;

		try {
			$data = @file($file);
			if (empty($data)) throw new Exception('file not exist');

			foreach ($data as $line) {
				if (!preg_match('/response:/i', $line)) continue;

				$info = json_decode(substr($line, 10), true);
				if (!empty($info['labels'])) {
					$shipment_id = $info['labels'][0]['shipment_ids'][0];
					$request_id = $info['labels'][0]['request_id'];

					$tr = Tranship::model()->find('JSON_VALUE(meta, "$.ss_shipment_id") = :n', [':n' => $shipment_id]);
					if (!empty($tr)) {
						$tr->mdata['ss_lbl_request_id'] = $request_id;
						$tr->update('meta');

						$p = $tr->shipment;
						if (!empty($p)) {
							$p->mdata['ss_lbl_request_id'] = $request_id;
							$p->update('meta');
						}
					}
				}
			}

			echo 'successfully' . PHP_EOL;
		} catch (Exception $ex) {
			echo 'Caught exception: ',  $ex->getMessage(), "\n";
		}
	}

	public function getCostByRef()
	{
		$p = ImParcel::model()->find('ref = :ref', [':ref' => $this->prompt('REF: ')]);
		$orgRate = OrgRate::model()->find('type = 40 and org_id = :oid', [':oid' => $p->agent_id]);
		echo json_encode($p->getChargePro($p->agent_id, $orgRate));
	}

	public function check_broker_cancel()
	{
		$afs = AFInvoiceReconciliation::model()->findAll('model LIKE "dmawb%"');
		foreach ($afs as $af) {
			$consol = Consol::model()->findByPk($af->fid);
			if ($consol->status == 100) echo $af->invoice_no . PHP_EOL;
		}
	}

	public function manual_credit()
	{
		$crns = Payment::model()->findAll('type = 5 AND bank = 91 AND status = 6 AND xero_id = "" AND date >= "2019-07-01"');
		foreach ($crns as $crn) {
			$xero = new XeroAPI;
			$results = $xero->get('Accounting\CreditNote', ['Type' => 'ACCRECCREDIT', 'CreditNoteNumber' => $crn->no]);
			if (empty($results) || $results[0]['Status'] != 'AUTHORISED') {
				$results = $xero->get('Accounting\CreditNote', ['Type' => 'ACCRECCREDIT', 'CreditNoteNumber' => $crn->no . '-1']);
			}

			if (!empty($results)) {
				$crn->xero_id = $results[0]['CreditNoteID'];
				$crn->update('xero_id');
			}
		}
	}

	public function check_creditnote_gst()
	{
		$crns = Payment::model()->findAll('type = 5 AND status = 6 AND xero_id != "" AND date >= "2019-01-01"');
		$xls = new oExcel;
		$i = 1;
		$xls->mergeCells('A1:G1');
		$xls->mergeCells('I1:L1');
		$xls->addRow($i++, ['HVLV', '', 'XERO']);
		$xls->addRow($i++, ['NO', 'GROSS', 'NET', 'GST', 'STATUS', 'DATE', 'INV', '', 'NO', 'GROSS', 'NET', 'GST']);
		foreach ($crns as $crn) {
			if ($crn->line_gst == 0) continue;

			$xero = new XeroAPI;
			$xero_credit = $xero->getByID('Accounting\CreditNote', $crn->no);
			if (empty($xero_credit) || !in_array($xero_credit['Status'], ['AUTHORISED', 'PAID'])) {
				$xero_credit = $xero->getByID('Accounting\CreditNote', $crn->no . '-1');
			}

			if (!empty($xero_credit)) {
				if (!empty($crn->invoices)) {
					$invs = '';
					foreach ($crn->invoices as $k => $inv) {
						$invs .= $inv->no . ' ' . $inv->date . '
';
					}
					$xls->addRow($i++, [$crn->no, $crn->amount, $crn->amount - $crn->line_gst, $crn->line_gst, 'USED', $crn->date, $invs, '', $xero_credit['CreditNoteNumber'], $xero_credit['Total'], $xero_credit['SubTotal'], $xero_credit['TotalTax']]);
				} else {
					$xls->addRow($i++, [$crn->no, $crn->amount, $crn->amount - $crn->line_gst, $crn->line_gst, 'NOT USED', $crn->date, '', '', $xero_credit['CreditNoteNumber'], $xero_credit['Total'], $xero_credit['SubTotal'], $xero_credit['TotalTax']]);
				}
			}
		}
		$xls->output('crn_gst.xlsx', null, false);
	}

	public function edi_job_create()
	{
		$jobs = EdiJob::model()->findAll();
		foreach ($jobs as $job) {
			if (!empty($job->logs) && $job->logs[0]->type == 3) {
				$job->mdata['create'] = $job->logs[0]->user_id;
				$job->nolog = true;
				$job->update('meta');
				if (!empty($job->getErrors())) echo json_encode($job->getErrors()) . PHP_EOL;
			}
		}
	}

	public function check_wmsst()
	{
		$aid = $this->prompt('Org: ');
		$td = $this->prompt('Date: ');
		$rs = WmsStock::model()->findAll('org_id = :oid', [':oid' => $aid]);
		$quotes = WmsOrgQuote::model()->find("org_id = :org_id AND status = 1", [':org_id' => $aid]);
		$owner = Org::model()->findByPk($aid);
		if (empty($quotes) && $owner->by != 1) {
			$quotes = WmsOrgQuote::model()->find('org_id = :org_id and status = 1', [':org_id' => $owner->by]);
		}
		$freewk = empty($quotes->mdata[WmsOrgQuote::QUOTE_STORAGE_FREE_WEEK]) ? 0 : $quotes->mdata[WmsOrgQuote::QUOTE_STORAGE_FREE_WEEK];
		$locs = [];
		$stot = 0;
		foreach ($rs as $r) {
			$q = $r->chargeUnit($td, $freewk, false, $locs);
			if ($q[0] == 0) continue;
			if ($q[1] == 0) continue;
			echo $r->prod->name . ' ' . $r->id . ' ' . json_encode($q) . PHP_EOL;
			$stot += $q[1];
		}
		echo $stot . PHP_EOL;
	}

	public function fix_sku()
	{
		$prods = WmsProd::model()->findAll('brand = "2EROS"');
		foreach ($prods as $prod) {
			$prod_org = WmsProdOrg::model()->find('org_id = 2939 AND prod_id = :pid', [':pid' => $prod->id]);
			if (empty($prod_org)) {
				$prod_org = new WmsProdOrg;
				$prod_org->prod_id = $prod->id;
				$prod_org->org_id = 2939;
				$prod_org->sku = $prod->model;
				$prod_org->save();
			}
		}
	}

	public function wms_70()
	{
		$invs = Invoice::model()->findAll('type = 70 AND sync_xero = 1 AND status IN (2,3)');
		foreach ($invs as $inv) {
			$xero = new XeroAPI;
			$xero_inv = $xero->getByID('Accounting\Invoice', explode('-', $inv->no)[0]);
			if ($inv->total != $xero_inv['Total']) echo $inv->no . PHP_EOL;
		}
	}

	public function fix_d2z_reship_mani()
	{
		$no = $this->prompt('NO: ');
		if (empty($no)) return;
		$sst = ImParcel::model()->findAll('ref IN ("' . $no . '") OR hbn IN ("' . $no . '")');

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
						$ts->save();
					}
					$transaction->commit();
				} catch (Exception $ex) {
					$transaction->rollback();
					throw $ex;
				}
			} else {
				// return $result;
			}
		}
		$sst = ImParcel::model()->findAll('ref IN ("' . $no . '") OR hbn IN ("' . $no . '")');
		echo $sst[0]->getStatus();
	}

	public function fastway_recon()
	{
		$action = $this->prompt('Update: ');
		$chargeCodeID = $this->prompt('Charge Code ID: ');
		$recs = Reconciliation::model()->findAll('client_type = 0 AND invoice_no = :no', [':no' => $this->prompt('Invoice No: ')]);
		foreach ($recs as $rec) {
			echo $rec->invoice_no . ' start' . PHP_EOL;
			foreach ($rec->lines as $line) {
				$shipment = ImParcel::model()->find('ref = :ref', [':ref' => $line->shipment_no]);
				$ourRated = $this->getCourierCostByShipment($line->shipment_no, $chargeCodeID, $line->weight);
				if ($line->my_value != $ourRated['price']) {
					if ($action == 'Y') {
						$line->my_value = $ourRated['price'];
						$line->update('my_value');
					}
				}
			}
			echo $rec->invoice_no . ' end' . PHP_EOL;
		}
	}

	public function get_fw_cost()
	{
		$shipment = ImParcel::model()->find('ref = :ref', [':ref' => $this->prompt('Ref: ')]);
		$rl = ReconciliationLine::model()->find('shipment_no = :sno', [':sno' => $shipment->ref]);
		$ourRated = $this->getCourierCostByShipment($shipment->ref, ImportChargeCode::FASTWAY_ID, $rl->weight);
		echo json_encode($ourRated) . PHP_EOL;
		$action = $this->prompt('Overwrite: ');
		if ($action == 'Y') {
			$rl->my_value = $ourRated['price'];
			$rl->update('my_value');
		}
	}

	/**
	 * @param $shipmentRef
	 * @return int
	 */
	private function getCourierCostByShipment($shipmentRef, $org_rate_id, $chargeWeight = null,$isHunter = false)
	{
		$orgRate = OrgRate::model()->findByPk($org_rate_id);
		$price = 0;
		$consolId = 0;
		$shipment = Shipment::model()->find('ref = :ref OR hbn = :ref', [':ref' => $shipmentRef]);
		if (!empty($shipment)) {
			$consolId = empty($shipment->consol_id)?0:$shipment->consol_id;
			if (empty($chargeWeight)) {
				$weight = $shipment->weight;
			} else {
				$weight = $chargeWeight;
			}
			$zoneMap = ZoneMap::model()->find(
				'org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code',
				[':oid' => $orgRate->org_id, ':zoneid' => $orgRate->zone_id, ':code' => $shipment->cnee->postcode]
			);

			$chargeCode = 'N1';
			if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
				$chargeCode = $zoneMap['z1'];
			}

			if($isHunter)//hunter use hunter_postcode_mapping
			{
				$chargeCode = HunterPortcode::model()->find('suburb = :suburb AND postcode = :postcode',
				[':suburb' => strtoupper(trim($shipment->cnee->suburb)), ':postcode' => $shipment->cnee->postcode]
				);
				if($chargeCode!=null)
				{
					$chargeCode = $chargeCode['zonecode'];
				}
			}
			// base charge code to get zone rate
			$zrs = ZoneRate::model()->findAll(
				"zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ",
				[':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate->id]
			);
			if (!empty($zrs)) {
				foreach ($zrs as $zr) {
					$temp = $zr['base'] + ($zr['item'] * $shipment->pkg);
					if ($zr['nkg'] > 0) {
						$wl = $weight - ($zr['base'] > 0 ? $zr['nkg'] : 0);
						$temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
					} else {
						$temp += $weight * $zr['perkg'];
					}
					if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
						$temp = $zr['minimum'];
					}
					$price = max($price, $temp);
				}
			}
		}
		if ($org_rate_id == ImportChargeCode::GLOBAVEND_POSTAGE_ID) {
			$price *= 1.021 * 1.03; 
		}
		return ['price' => $price, 'consol_id' => $consolId];
	}

	public function fix_easyship_item_pi()
	{
		$tasks = WmsTask::model()->findAll('meta LIKE "%errs%"');
		foreach ($tasks as $task) {
			if (empty($task->mdata['errs'])) continue;
			foreach ($task->mdata['errs'] as $k => $err) {
				$prod = WmsProd::model()->find('ean = :ean', [':ean' => explode(' ', $err['message'])[0]]);
				foreach ($task->items as $item) {
					$item->refresh();
					if (empty($item->mdata['pi']) && $item->mdata['sn'] == $prod->name) {
						$item->mdata['pi'] = $prod->id;
						$item->save();
					}
				}
			}
		}
	}

	public function extract_easyship_log()
	{
		$url = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'easyship.log';
		$content = file_get_contents($url);
		$lines = explode("\n", $content);
		$orders = [];
		foreach ($lines as $line) {
			if ($line == '') continue;
			$data = substr($line, 20);
			if ($data == '') continue;
			$data = json_decode($data, true);
			if (empty($data['orders'])) continue;
			foreach ($data['orders'] as $order) {
				$orders[] = $order;
			}

			$data['api_hook'] = 'ufl';
			$data['api_meta'] = ['customer' => $data['customer']];
		}

		$out = ['orders' => []];
		$errs = [];

		$odr_job_type_map = function($t) {
			return in_array($t, [20, 30]) ? 10 : 30;
		};

		$odr_att_type_map = function($t) {
			$rt = 84;
			switch($t) {
				case 10:
					$rt = 85;
				break;
				case 20:
					$rt = 86;
				break;
				case 30:
					$rt = 87;
				break;
			}
			return $rt;
		};
		
		$mapFileType = function($t){
			switch($t){
				case 'ShippingLabel':
					$r = 10;
				break;
				case 'CommercialInvoice':
					$r = 20;
				break;
				case 'PackingSlip':
					$r = 30;
				break;
				default:
					$r = 90;
				break;
			}
			return $r;
		};

		$user = User::model()->findByPk(1250);

		foreach ($orders as $odr) {
			$od = [
				'no' => $odr['orderNo'],
				'type' => 10,
				'confirmed' => true,
				'items' => [],
				'to' => [
					'name' => @$odr['orderConsigneeName'],
					'company' => @$odr['orderConsigneeCompany'],
					'state' => @$odr['orderConsigneeState'],
					'suburb' => @$odr['orderConsigneeCity'],
					'postcode' => @$odr['orderConsigneePostalCode'],
					'address' => @$odr['orderConsigneeAddr1'].(empty($odr['orderConsigneeAddr2'])? '' : ' '.$odr['orderConsigneeAddr2']),
					'phone' => @$odr['orderConsigneePhone'],
					'email' => @$odr['orderConsigneeEmail'],
				],
				'attachments' => [],
			];
			$this->_mapFields($odr, $od, ['courierName' => 'freight_co', 'courierBillNo' => 'connote_no', 'remarkforDelivery' => 'shipping_note', 'orderTotalAmt' => 'total']);

			//items
			foreach($odr['parts'] as $p){
				$this->_mapFields($p, $od['items'][], ['partNo' => 'ean', 'vendorCode' => 'sku', 'vendorName' => 'name', 'partQty' => 'qty', 'PartUnitPrice' => 'price']);
			}

			//attachments
			if(!empty($odr['orderFiles'])){
				foreach($odr['orderFiles'] as $f){
					$od['attachments'][] = [
						'type' => $mapFileType(@$f['usage']),
						'name' => empty($f['fileName'])? @$f['usage'].'.'.@$f['format'] : $f['fileName'],
						'content' => @$f['baseContent'],
					];
				}
			}

			$odr = $od;

			if (empty($odr['no'])) {
				$errors[] = ['no' => '', 'code' => 110, 'message' => 'Missing Order No'];
				continue;
			}

			//pick/create job
			$job = WmsJob::model()->find('org_id = :org_id AND type = :t AND date(`created`) = :day', [':org_id' => $user->org_id, ':t' => $odr_job_type_map($odr['type']), ':day' => date('Y-m-d')]);

			if (empty($job)) {
				$job = new WmsJob;
				$job->org_id = $user->org_id;
				$job->type = $odr_job_type_map($odr['type']);
				$job->status = 10;
				$job->ref = '3PL_' . date('Y-m-d');
				$job->save();
			}

			//group item
			$items = [];
			$not_found = [];
			foreach($odr['items'] as $itm){
				//find product
				if(!empty($itm['ean'])){
					$prod = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $itm['ean']]);
					if (empty($itm['sku'])) $itm['sku'] = $itm['ean'];
					if(empty($prod) && !empty($itm['sku'])){ // try sku
						$wpo = WmsProdOrg::model()->with('prod')->find('prod.status = 1 AND t.org_id = :org_id AND (t.sku = :sku OR t.sku = :ean)', [':org_id' => $user->org_id, ':sku' => $itm['sku'], ':ean' => $itm['ean']]);
						if(!empty($wpo)){
							$prod = $wpo->prod;
						}
					}
					if(empty($prod)){
						$errs[] = ['no' => $odr['no'], 'code' => 201, 'message' => 'Product not found EAN '.$itm['ean'] . (!empty($itm['sku']) ? ' / SKU ' . $itm['sku'] : '')];
						$temp_err[] = ['no' => $odr['no'], 'code' => 201, 'message' => 'Product not found EAN '.$itm['ean'] . (!empty($itm['sku']) ? ' / SKU ' . $itm['sku'] : '')];
						$not_found[$itm['ean']] += intval($itm['qty']);
					}else{
						if(!isset($items[$prod->id])) $items[$prod->id] = 0;
						$items[$prod->id] += intval($itm['qty']);
					}
				}
			}

			//check stock
			$new_items = [];
			foreach ($items as $pid => $qty) {
				$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $pid, ':org_id' => $user->org_id]]);
				if ($stock->qty - $stock->qty_res < $qty) {
					$prod = WmsProd::model()->findByPk($pid);
					$errs[] = ['no' => $odr['no'], 'code' => 203, 'message' => $prod->ean . ' not enough stock ('.$qty.' < '.($stock->qty - $stock->qty_res).')'];
					$temp_err[] = ['no' => $odr['no'], 'code' => 203, 'message' => $prod->ean . ' not enough stock ('.$qty.' < '.($stock->qty - $stock->qty_res).')'];
					$new_items[] = [
						'si' => '',
						'sn' => $prod->name,
						'pq' => '',
						'cq' => '',
						'uq' => $qty,
						'pli' => '',
						'pl' => '',
						'nt' => '',
						'pi' => $prod->id,
					];
				} else {
					$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id AND qty - qty_res > 0', [':prod_id' => $pid, ':org_id' => $user->org_id]);
					foreach ($stocks as $stock) {
						if ($qty <= 0) {
							break;
						}
						$uq = min($qty, $stock->availQty());
						$new_items[] = [
							'si' => $stock->id,
							'sn' => $stock->prod->name,
							'pq' => '',
							'cq' => '',
							'uq' => $uq,
							'pli' => '',
							'pl' => '',
							'nt' => '',
							'pi' => $stock->prod->id,
						];
						$qty -= $uq;
					}
				}
			}
			foreach ($not_found as $ean => $qty) {
				$new_items[] = [
					'si' => '',
					'sn' => $ean,
					'pq' => '',
					'cq' => '',
					'uq' => $qty,
					'pli' => '',
					'pl' => '',
					'nt' => '',
					'pi' => '',
				];
			}

			$create = true;
			if (!empty($errs)) {
				// easyship if stock not enough still create, but do not occupy stock
				foreach ($new_items as $k => $item) {
					$new_items[$k]['si'] = '';
				}
			}

			if ($create) {
				//create main task
				$task = WmsTask::model()->with('job')->find('t.ref = :ref AND job.org_id = :org_id AND t.status < 100', [':org_id' => $user->org_id, ':ref' => $odr['no']]);
				if (empty($task)) {
					$task = new WmsTask;
					$task->job_id = $job->id;
					$task->type = 3030;
					$task->is_request = 1;
					$task->op_id = 0;
					$task->status = empty($errs) ? 20 : 10;
					$task->ref = $odr['no'];
					$task->new_items = $new_items;
					if(!empty($data['api_hook'])){
						$task->mdata['api_hook'] = $data['api_hook'];
						if(!empty($data['api_meta'])){
							$task->mdata['api_meta'] = $data['api_meta'];
						}
					}
					$task->mdata['errs'] = $errs;
					$task->save();

					$task->pickupTask->type = 2120;
					$delivery_task = $task->pickupTask;
					$delivery_task->ref = $odr['connote_no'];
					$delivery_task->mdata['cnee'] = $odr['to'];
					$delivery_task->save();
					
					//attachments
					foreach ($odr['attachments'] as $att) {
						FileRepo::storeBase64File($att['content'], $att['name'], $odr_att_type_map($att['type']), $task->id);
					}

					$out['orders'][] = ['no' => $odr['no'], 'id' => $task->id, 'status' => 20];
				} else {
					$errs[] = ['no' => $odr['no'], 'code' => 101, 'message' => 'Order already exists'];
				}
			}
		}

		echo json_encode(@$errors) . PHP_EOL;
		echo json_encode(@$errs) . PHP_EOL;
	}

	protected function _mapFields(&$o, &$t, $map){
		foreach($map as $k => $mk){
			if(isset($o[$k])){
				$t[$mk] = $o[$k];
			}
		}
	}

	public function clear_easyship_errs()
	{
		$tasks = WmsTask::model()->findAll('meta LIKE "%errs%"');
		foreach ($tasks as $task) {
			$errs = [];
			if (!empty($task->mdata['errs'])) {
				foreach ($task->mdata['errs'] as $err) {
					if ($err['no'] == $task->ref) {
						$errs[] = $err;
					}
				}
			}
			$task->mdata['errs'] = $errs;
			$task->update('meta');
		}
	}

	public function fix_easyship_status()
	{
		$tasks = WmsTask::model()->findAll('ref LIKE "%esau%" AND id > 150913 AND is_request = 1');
		$org_id = 2939;
		foreach ($tasks as $task) {
			$flag = true;
			foreach ($task->items as $item) {
				$pid = $item->mdata['pi'];
				$stock = WmsStock::model()->find(['condition' => 'prod_id = :prod_id AND org_id = :org_id AND qty - qty_res > :qty', 'params' => [':prod_id' => $pid, ':org_id' => 2939, ':qty' => $item->mdata['uq']]]);
				if (empty($stock)) {
					$flag = false;
					break;
				}
			}

			if ($flag) {
				foreach ($task->items as $item) {
					$pid = $item->mdata['pi'];
					$stock = WmsStock::model()->find('prod_id = :prod_id AND org_id = :org_id AND qty - qty_res > :qty', [':prod_id' => $pid, ':org_id' => 2939, ':qty' => $item->mdata['uq']]);
					$item->mdata['si'] = $stock->id;
					$item->update('meta');
				}

				$task->status = 20;
				$task->mdata['errs'] = [];
				$task->update('status', 'meta');
			}
		}
	}

	public function fix_aupost_recon()
	{
		$lines = ReconciliationLine::model()->findAll('id IN (4261769,4261406,4260818,4259753,4258226,4256552,4256525,4256519,4255541,4252526,4252130,4251395,4251095,4251059,4250720,4249007,4248179,4247378,4246487,4246292,4246259,4245947,4245704,4245515,4243955,4241744,4241183,4239239,4237886,4236971,4236599,4236464,4234280,4231913,4231628,4229303,3840064,3840010,3837700,3837202,3835915,3835549,3834829,3834751,3833632,3833305,3833197,3833182,3832570,3831382,3831277,3830905,3830830,3829762,3829315,3829183,3828940,3827398,3827032,3825814,3824110,3823879,3823219,3822568,3821212,3819310,3819226,3818404,3816211,3815341,3815308,3815302,3814675,3814621,3814141,3814018,3813268,3812314,3812131,3812128,3812023,3811606,3810409,3809707,3806419,3806356,3803797,3798538,3797629,3794848,3794464,3792064,3791524,3791098,3789571,3789532,3788725,3788665,3788554,3788410,3788215,3787768,3787765,3787291,3787006,3786889,3786247,3784942,3783115,3782098,3780580,3778813,3778702,3776389,3775105,3774820,3774601,3772243,3772168,3770446,3768919,3768550,3767527,3767512,3766789,3766774,3766489,3764368,3763744,3762283,3760489,3759985,3759838,3758044,3757660,3756712,3756364,3756310,3755305,3754840,3754177,3753253,3752395,3748150,3748066,3746938,3744748,3743908,3743809,3743143,3742183,3738193,3738163,3738124,3737722,3737713,3736720,3736579,3735856,3729436,3727408,3727333,3726643,3726112,3725179,3724144,3723820)');
		foreach ($lines as $line) {
			$amq = $line->shipment_no;
			$org_rate_id = ImportChargeCode::SYDNEY_AUPOST_ID;
			if (preg_match("/(AMQ|333UF)\d{7}/", $amq)) {
				$org_rate_id = ImportChargeCode::SYDNEY_AUPOST_ID;
			} elseif (preg_match("/33EVH\d{7}/i", $amq)) {
				$org_rate_id = ImportChargeCode::MELBOUNE_AUPOST_ID;
			} elseif (preg_match("/33EVJ\d{7}/i", $amq)) {
				$org_rate_id = ImportChargeCode::BRISBANE_AUPOST_ID;
			} elseif (preg_match("/33A8Y\d{7}/i", $amq)) {
				$org_rate_id = ImportChargeCode::D2Z_COUNTRY_ID;
			}
			$ourRated = $this->getCourierCostByShipment($amq, $org_rate_id);
			$manifest_weight = $cust_check_weight = 0;
			$shipment = ImParcel::model()->find('ref = :ref', [':ref' => $amq]);
			$ourRate['consol_id'] = $shipment->consol_id;
			$our_charge_weight = floatval(isset($shipment->mdata['charge_client_weight']) ? $shipment->mdata['charge_client_weight'] : $shipment->chargeWeight());
			if (!empty($shipment)) {
				$manifest_weight = isset($shipment->mdata['manifest_weight']) ? floatval($shipment->mdata['manifest_weight']) : 0;
				$cust_check_weight = $shipment->weight;
				// save actual cost in shipment
				$manifest_weight = isset($shipment->mdata['manifest_weight']) ? floatval($shipment->mdata['manifest_weight']) : 0;
				$cust_check_weight = $shipment->weight;
				// save actual cost in shipment
				$shipment->mdata['actual_delivery_cost'] = number_format($value, 2, '.', '');
				$shipment->updateMeta();
				$ourcharge = $shipment->getCouiercost();
				$consoleId = $shipment->consol_id;
				$data = [$amq, $invoice_date, $postcode, $weight, $value, $ourRated, $deadweight, $ourcharge, $manifest_weight, $cust_check_weight, $our_charge_weight, $article_no];

				// $recData->parent_id = $recModel->id;
				$line->invoice_no = $shipment->consol->no; // for aupost we use invoice_no field to save console number
				// $recData->postcode = $line[2];
				// $recData->shipment_no = $line[0];
				// $recData->weight = number_format($line[3], 2, '.', '');
				// $recData->value = number_format($line[4], 2, '.', '');
				// $recData->cdeadwt = number_format($line[6], 2, '.', '');
				$line->my_value = number_format($data[5]['price'], 2, '.', '');
				$line->my_charge = number_format($data[7], 2, '.', '');
				$line->manifest_weight = number_format($data[8], 2, '.', '');
				$line->cust_check_weight = number_format($data[9], 2, '.', '');
				$line->our_charge_weight = number_format($data[10], 2, '.', '');
				$line->consol_id = $data[5]['consol_id'];
				// $recData->mdata['article_no'] = $line[11];
				$line->save();
			}
		}
	}

	public function reprocess_edimsg()
	{
		Yii::import('application.libs.edifact.ediParser');
		$rs = Edimsg::model()->findAll('id IN (20564149,20564146,20564143,20564140,20564134)');
		$transaction=Yii::app()->db->beginTransaction();
		try{
		foreach ($rs as $r) {
			$ep = new ediParser($r->msg);
			$r->mdata['ackreq'] = $ep->getExchValue('UNB', 90);
			$r->mdata['mrn'] = $ep->getExchValue('UNH', 10);
			$mtyp = $ep->getExchValue('UNH', 20, 0);
			$r->type = $mtyp == 'CUSRES'? $ep->getEdiValue('BGM', 10, 3) : $mtyp;
			switch ($r->type) {
				case 'CARST':
					$isSea=false;
					if (preg_match('/DTM\+9:(\d{14})/', $r->msg, $dtm)) {
						$r->dt = date('Y-m-d H:i:s', strtotime($dtm[1]));
					}
					if (preg_match("/RFF\+MWB:([^:\+']+)[:\+']+/", $r->msg, $m)) {
						$mwb = $m[1];
					}
					if (preg_match("/RFF\+MB:([^:\+']+)[:\+']+/", $r->msg, $m)) {
						$mwb = $m[1];
						$isSea=true;
					}
					if (preg_match("/RFF\+(HWB|BH):([^:\+']+)[:\+']+/", $r->msg, $m)) {
						$hbn = $m[2];
						$p = ImParcel::model()->find('hbn = :n AND cbwf&16384=0', [':n' => $hbn]);
						if (empty($p)) {
							$c = Consol::model()->find('type IN(15,70) AND no = :n', [':n' => $hbn]);
							if (!empty($c)) {
								$r->fid=$c->id;
								$awb = preg_replace('/[^\d]+/', '', $c->awb);
								if (strpos($r->msg, 'CONSOLIDATED STATUS:DCLALLOWED') > 0) {
									$c->mdata['dclallowed'] = 1;
									$ms = Edimsg::model()->findAll('status = 18 AND fid = :c', [':c' => $c->id]);
									foreach ($ms as $m) {
										$m->status = 20 ; // push to sending queue
										$m->save();
									}
									if (!empty($ms)) {
										$c->mdata['ubmsent'] = 1;
									}
								} elseif ($awb==$mwb && $r->isLatest() && in_array($c->type, [70])) {
									$c = DmawbConsol::model()->findByPk($c->id);
									$shipment=$c->linkShipment(false);
									preg_match('/CONSOLIDATED STATUS:(HELD|CLEAR)/', $r->msg, $m);
									if ($m[1] == 'CLEAR') {
										$c->status = 60;
										$shipment->status = 60;
									} else {
										$c->status = 50;
										$shipment->status = 55;
										if (preg_match_all("/FTX\+AHN\+\+\+([\s\S]*?)'/i", $r->msg, $mreasons)) {
											$shipment->mdata['held_reason'] = implode("\n", $mreasons[1]);
											$shipment->heldReason();
										}
									}
									$shipment->save();
								}
								$c->save();
							}
						} else {
							$r->fid = $p->id;
							if (!$isSea) {
								$awb = preg_replace('/[^\d]+/', '', $p->consol->awb);
								$hwb = empty($p->consol->mdata['house_bill'])? '' : preg_replace('/[^\d]+/', '', $p->consol->mdata['house_bill']);
							} else {
								$awb = trim($p->consol->awb);
								$hwb = empty($p->consol->mdata['house_bill'])? '' : trim($p->consol->mdata['house_bill']);
							}
							if (in_array($mwb, [$awb, $hwb]) && $r->isLatest()) {
								preg_match('/CONSOLIDATED STATUS:(HELD|CLEAR)/', $r->msg, $m);
								if ($m[1] == 'CLEAR') {
									$tempStatus = 60;
									if (!$p->canSAC(true)) {
										$tempStatus = 57;
									} elseif ($p->hasCustomProcess() && empty($p->mdata['screen_period_exceed'])) {  //means the shipment go through our custom process
										$tempStatus = (($p->bwf & 512 > 0) && ($p->bwf & 128 == 0)) && $p->status != 58 ? 57 : 58;
									}
									$p->status = $tempStatus;
								} else {
									$p->status = 55;
									// get exactly held reason
									if (preg_match_all("/FTX\+AHN\+\+\+([\s\S]*?)'/i", $r->msg, $mreasons)) {
										$p->mdata['held_reason'] = implode("\n", $mreasons[1]);
										$p->heldReason();
									}
								}
								$p->save();
							}
						}
					}else{
						$c = Consol::model()->find('type IN(15,70) AND REGEXP_REPLACE(awb, "[^0-9]+", "") = :n', [':n' => $mwb]);
						if (!empty($c)) $r->fid=$c->id;
					}
					//preg_match('/RFF\+ABO:([^:\-]+):/', $r->msg, $m);
					if(!empty($r->fid)) $r->status = 19;
					$r->save();
				break;
			}
		}
			$transaction->commit();
		} catch (Exception $ex){
			$transaction->rollback();
			throw  $ex;
		}
	}

	public function fix_aupost_my_charge()
	{
		$lines = ReconciliationLine::model()->with('parent')->findAll('parent.client_type = :type AND t.my_charge / t.my_value >= 20', [':type' => Reconciliation::AUPOST_TYPE]);
		foreach ($lines as $line) {
			$shipment = ImParcel::model()->find('ref = :ref', [':ref' => $line->shipment_no]);
			$ourcharge = $shipment->getCouiercost();
			$line->my_charge = $ourcharge;
			$line->update('my_charge');
		}
	}

	public function fix_aupost_actual_cost()
	{
		$no = $this->prompt('No: ');
		$sql = "select * from (select shipment_no, sum(value) as total_a, parent_id from reconciliation_line where parent_id in (select id from reconciliation where invoice_no = '" . $no . "' and client_type = 1) group by shipment_no) a join (SELECT ref, json_value(meta, '$.actual_delivery_cost') as total_b FROM `shipment` WHERE consol_id in (select id from consol where no = '" . $no . "') and ref like '%amq%') b on a.shipment_no = b.ref where total_a != total_b";
		$rs = Yii::app()->db->createCommand($sql)->queryAll();

		foreach ($rs as $r) {
			$shipment = ImParcel::model()->find('ref = :ref', [':ref' => $r['ref']]);
			$temp = $r['total_a'] - $r['total_b'];
			$shipment->mdata['actual_delivery_cost'] = $r['total_a'];
			$shipment->nolog = true;
			$shipment->update('meta');
		}

		$bls = BillingLine::model()->findAll('org_id = 101 AND billing_ref = :ref AND actual_amount > 0', [':ref' => $no]);
		foreach ($bls as $bl) {
			$sql = "(SELECT sum(json_value(meta, '$.actual_delivery_cost')) AS total FROM `shipment` WHERE json_value(meta, '$.import_billing_id') = " . $bl->id . ")";
			$r = Yii::app()->db->createCommand($sql)->queryScalar();
			$bl->actual_amount = number_format($r, 2, '.', '');
			$bl->gst_amount = $bl->getGSTValue();
			$bl->update('actual_amount', 'gst_amount');
		}
	}

	public function fix_aupost_recon_my_value()
	{
		$sql = "select id from reconciliation where id in (select parent_id from `reconciliation_line` WHERE parent_id in (select id from reconciliation where client_type = 1 and invoice_date >= '2019-01-01') and my_value = 0)";
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rs as $r) {
			$r = Reconciliation::model()->findByPk($r['id']);
		}
	}

	public function fix_aupost_billing_line()
	{
		$sql = "select * from billing_line where meta like '%rts_manifest_no%' and actual_amount > 0";
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rs as $r) {
			$bl = BillingLine::model()->findByPk($r['id']);
			$bl->mdata = [];

			$ss = ImParcel::model()->findAll('JSON_VALUE(meta, "$.import_billing_id") = ' . $bl->id);
			$theCost = 0;
			foreach ($ss as $p) {
				foreach ($p->trans as $ts) {
					if ($ts->type == 80) {
						$theCost += $ts->cost;
					}
				}
			}
			$bl->accrual_amount = number_format($theCost, 2, '.', '');
			$bl->save();
		}
	}

	public function easyship_manual_ship()
	{
		$tasks = WmsTask::model()->with('deliveryTask', 'job')->findAll('JSON_VALUE(t.meta, "$.api_comp") = 1 AND JSON_VALUE(t.meta, "$.api_ship") IS NULL AND (deliveryTask.ref LIKE "P%" OR deliveryTask.ref LIKE "S%") AND job.org_id IN (2393, 2939)');
		foreach ($tasks as $task) {
			// echo $task->id . PHP_EOL;
			$task->easyshipScanOut();
		}
	}

	public function easyship_manual_ship_by_id()
	{
		$task = WmsTask::model()->findByPk($this->prompt('ID: '));
		$task->easyshipScanOut();
	}

	// public function split_recon_billing()
	// {
	// 	$transaction = Yii::app()->db->beginTransaction();
	// 	try {
	// 		$recs = Reconciliation::model()->findAll('invoice_date >= "2019-06-01" AND client_type = 1 AND invoice_no not like "3pl%"');
	// 		foreach ($recs as $rec) {
	// 			$sql = "SELECT SUM(actual_amount) FROM billing_line WHERE id IN (select distinct json_value(meta, '$.import_billing_id') AS id FROM shipment WHERE ref IN (SELECT shipment_no FROM `reconciliation_line` WHERE parent_id = :pid))";
	// 			$total = Yii::app()->db->createCommand($sql)->bindValues([':pid' => $rec->id])->queryScalar();
	// 			if ($total == $rec->invoice_total) continue;

	// 			$sql = "SELECT distinct json_value(meta, '$.import_billing_id') AS id FROM shipment WHERE ref IN (SELECT shipment_no FROM `reconciliation_line` WHERE parent_id = :pid)";
	// 			$rs = Yii::app()->db->createCommand($sql)->bindValues([':pid' => $rec->id])->queryAll();
	// 			foreach ($rs as $r) {
	// 				$bl = BillingLine::model()->findByPk($r['id']);

	// 				if (empty($bl)) continue;

	// 				$sql = "SELECT SUM(json_value(meta, '$.actual_delivery_cost')) FROM `shipment` WHERE ref IN (SELECT shipment_no FROM reconciliation_line WHERE parent_id = :pid AND json_value(meta, '$.import_billing_id') = :bid)";
	// 				$total = Yii::app()->db->createCommand($sql)->bindValues([':pid' => $rec->id, ':bid' => $r['id']])->queryScalar();

	// 				$new_bl = new BillingLine;
	// 				$new_bl->billing_id = $bl->billing_id;
	// 				$new_bl->org_id = $bl->org_id;
	// 				$new_bl->op_id = $bl->op_id;
	// 				$new_bl->link_id = $bl->link_id;
	// 				$new_bl->to_id = $bl->to_id;
	// 				$new_bl->created = $bl->created;
	// 				$new_bl->date = $bl->date;
	// 				$new_bl->due = $bl->due;
	// 				$new_bl->transaction_date = $bl->transaction_date;
	// 				$new_bl->type = $bl->type;
	// 				$new_bl->dpmt = $bl->dpmt;
	// 				$new_bl->gst = $bl->gst;
	// 				$new_bl->status = $bl->status;
	// 				$new_bl->billing_cref = $bl->billing_cref;
	// 				$new_bl->billing_ref = $bl->billing_ref;
	// 				$new_bl->awb = $bl->awb;
	// 				$new_bl->dpt_id = $bl->dpt_id;
	// 				$new_bl->currency = $bl->currency;
	// 				$new_bl->charge_code = $bl->charge_code;
	// 				$new_bl->desc = $bl->desc;
	// 				$new_bl->qty = $bl->qty;
	// 				$new_bl->item_code = $bl->item_code;
	// 				$new_bl->price = $bl->price;
	// 				$new_bl->weight = $bl->weight;
	// 				$new_bl->charge_weight = $bl->charge_weight;
	// 				$new_bl->sync_xero = $bl->sync_xero;
	// 				$new_bl->actual_amount = number_format($total, 2, '.', '');
	// 				$new_bl->gst_amount = $new_bl->getGSTValue();
	// 				$new_bl->save();
	// 				if (!empty($new_bl->getErrors())) echo json_encode($new_bl->getErrors()) . PHP_EOL;

	// 				$bl->actual_amount = number_format($bl->actual_amount - $new_bl->actual_amount, 2, '.', '');
	// 				$bl->gst_amount = $bl->getGSTValue();
	// 				$bl->save();

	// 				$ss = ImParcel::model()->findAll('ref IN (SELECT shipment_no FROM reconciliation_line WHERE parent_id = :pid)', [':pid' => $rec->id]);
	// 				foreach ($ss as $s) {
	// 					$s->mdata['import_billing_id'] = $new_bl->id;
	// 					$s->nolog = true;
	// 					$s->update('meta');
	// 				}
	// 			}
	// 		}
	// 		$transaction->commit();
	// 	} catch (Exception $ex) {
	// 		$transaction->rollback();
	// 		throw $ex;
	// 	}
	// }

	// public function fix_shipment_billing()
	// {
	// 	$bls = BillingLine::model()->findAll('id >= 290965 AND id <= 291406');
	// 	foreach ($bls as $bl) {
	// 		$sql = "SELECT SUM(json_value(meta, '$.actual_delivery_cost')) FROM `shipment` WHERE json_value(meta, '$.import_billing_id') = :bid";
	// 		$total = Yii::app()->db->createCommand($sql)->bindValues([':bid' => $bl->id])->queryScalar();

	// 		$bl->actual_amount = number_format($total, 2, '.', '');
	// 		$bl->gst_amount = $bl->getGSTValue();
	// 		$bl->save();
	// 	}
	// }

	public function fix_shipment_billing2()
	{
		$bls = BillingLine::model()->findAll('id >= 290965 AND id <= 291406');
		foreach ($bls as $bl) {
			$obls = BillingLine::model()->findAll('org_id = 101 AND billing_ref = :ref AND actual_amount > 0', [':ref' => $bl->billing_ref]);
			foreach ($obls as $obl) {
				$sql = "SELECT SUM(json_value(meta, '$.actual_delivery_cost')) FROM `shipment` WHERE json_value(meta, '$.import_billing_id') = :bid";
				$total = Yii::app()->db->createCommand($sql)->bindValues([':bid' => $obl->id])->queryScalar();

				$obl->actual_amount = number_format(floatval($total), 2, '.', '');
				$obl->gst_amount = $obl->getGSTValue();
				$obl->accrual_amount = 0;
				$obl->save();
			}
		}
	}

	public function fix_rts_email()
	{
		$rs = ImParcel::model()->findAll('(cbwf&256)>0');
		foreach ($rs as $r) {
			$emailog = Emailog::model()->find('body LIKE :ref AND type = 71', [':ref' => '%' . $r->hbn . '%']);
			if (!empty($emailog)) {
				$r->cbwf=$r->cbwf&256^$r->cbwf;
				$r->update('cbwf');
			}
		}
	}

	public function d2z_refresh_rate()
	{
		$rls = ReconciliationLine::model()->findAll('invoice_no = :invoice_no', [':invoice_no' => 'INV-2208']);
		foreach ($rls as $rl) {
			$d2ztype = $rl->shipment->mdata['d2ztype'];
			$org_rate_id = @ImportChargeCode::$d2z_services["{$d2ztype}"];
			if ($org_rate_id == 522) $org_rate_id = 657;
			$ourRate = Shipment::getCourierCostByShipment($rl->shipment_no, $org_rate_id, $rl->weight);
			$rl->my_value = number_format($ourRate['price'], 2, '.', '');
			$rl->update('my_value');
		}
	}

	public function fix_prod_sku()
	{
		$prods = WmsProd::model()->findAll('brand = "SUPAWEAR" AND id NOT IN (SELECT prod_id FROM wms_prod_org)');
		foreach ($prods as $prod) {
			$org = new WmsProdOrg;
			$org->prod_id = $prod->id;
			$org->org_id = 2939;
			$org->sku = $prod->model;
			$org->save();
		}
	}

	public function fix_fastway_consol()
	{
		$consol = Consol::model()->findByPk(47158);
		$items = [];
		foreach ($consol->shipments as $p) {
			$desc = $p->getDesc();
			if (empty($desc)) $desc = 'null';
			else $desc = str_replace('"', '', json_encode($desc));
			$amt = $p->mdata['charge_client_amount'];
			$items[] = [$p->ref, $desc, $p->pkg, $p->weight, $p->cbm, $amt, $p->cnee->postcode];
			$tot += $amt;
		}
		$inv = Invoice::model()->findByPk(116860);
		$il = new InvLine;
		$il->inv_id = $inv->id;
		$il->ccode = 'EPA';
		$il->mdata['items'] = $items;
		$il->det = $consol->no;
		$il->fid = $consol->id;
		$il->model = 'ImcoConsol'; // invoice connected with console directly
		$il->amount = round($tot * 1000) / 1000;
		$il->qty = 1;
		$il->save();
		$inv->getTotal();
	}

	public function fill_sendle_info()
	{
		$tasks = WmsTask::model()->findAll('type = 2120 AND JSON_QUERY(meta, "$.cnee") IS NOT NULL AND JSON_QUERY(meta, "$.shipment_id") IS NOT NULL AND id > 100000 AND JSON_VALUE(meta, "$.cnee.country") NOT IN ("AU", "CN")');
		echo count($tasks) . PHP_EOL;
		foreach ($tasks as $task) {
			foreach ($task->mdata['shipment_id'] as $sid) {
				$p = Shipment::model()->findByPk($sid);
				if (!empty($p->trans)) continue;
				$sendle = new SendleAPI();
				$result = $sendle->createInternationalOrder($p);
				echo json_encode($result) . PHP_EOL;
				echo $task->id . ' ' . $p->ref . PHP_EOL;
			}
		}
	}

	public function fix_pallet_out_ledger_miss()
	{
		$wsls = WmsStockLedger::model()->findAll('id IN (446308,446311,446314,446317,446320,446323,446326,446329,446332,446335,446338,446341,446344,446347,449143,449146,449149,449152,449155,449158,449161,449164,449167,449170,449173,449176,449179,449182,449185,449188,450640,477904,477907,477910,477913,477916,477919,477922,477925,477928,481735,481738,481741,481744,481747,481750,481753,481756,481759,481762,481765,481768,481771,481774,481870,481873,481879,481882,481885,481888,481891,481894,481897,481900,481903,481906,481909,481912,481915,481918,481921,481924,481927,481930,481933,481936,481939,481942,481945,481948)');
		foreach ($wsls as $wsl) {
			$ti = WmsTaskItem::model()->with('task')->find('task.type IN (2030,3010) AND JSON_VALUE(t.meta, "$.pli") = :n', [':n' => $wsl->loc->id]);
			// if (!empty($ti)) echo $ti->id . ' ' . $wsl->id . PHP_EOL;
			if (empty($ti->stockLedgers)) {
				$wsl_out = new WmsStockLedger;
				$wsl_out->ti_id = $ti->id;
				$wsl_out->task_id = 0;
				$wsl_out->stock_id = $wsl->stock_id;
				$wsl_out->location_id = $wsl->location_id;
				$wsl_out->l2_id = 5;
				$wsl_out->qty_in = 0;
				$wsl_out->qty_out = $wsl->qty_in;
				$wsl_out->ts = $ti->ts;
				$wsl_out->save();
			}
		}
	}

	public function shopify_ninja()
	{
		$cust = WmsAPI::model()->find('org_id = :org_id', [':org_id' => 1985]);
		$config = array(
			'store_domain' => $cust->domain,
			'api_key' => $cust->api_key,
			'api_secret' => $cust->api_secret,
			'org_id' => $cust->org_id,
		);
		$shopify = new ShopifyAPI($config);
		$shopify_order_id = $shopify->getOrderByRef(7387);
		echo $shopify_order_id . PHP_EOL;
	}

	public function extract_easyship_fail_send()
	{
		$url = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'ufl_api.log';
		$content = file_get_contents($url);
		$lines = explode("\n", $content);
		foreach ($lines as $k => $line) {
			if (preg_match('/Not Found/', $line)) {
				$info = json_decode(substr($lines[$k-1], 28), true);
				$order = $info['orderNo'];

				$info = json_decode(substr($lines[$k-1], 28), true);
				$status = $info['status'];

				if (!empty($order) && !empty($status)) {
					$task = WmsTask::model()->find('ref = :ref', [':ref' => $order]);
					if ($status == 'RS') {
						$api = new UflAPI;
						$r = $api->outboundUpdate($task->mdata['api_meta']['customer'], $task->ref, 'RS');
						if (!empty($r['Response']) && preg_match('/success/', $r['Response']['result'])) {
							$task->mdata['api_comp'] = 1;
							$task->updateMeta();
						}
					} else if ($status == 'C') {
						$api = new UflAPI;
						$r = $api->outboundUpdate($task->mdata['api_meta']['customer'], $task->ref, 'C');
						if (!empty($r['Response']) && preg_match('/success/', $r['Response']['result'])) {
							$task->mdata['api_ship'] = 1;
							$task->updateMeta();
						}
					} else if ($status == 'X') {
						$api = new UflAPI;
						$r = $api->outboundUpdate($task->mdata['api_meta']['customer'], $task->ref, 'X');
						if (!empty($r['Response']) && preg_match('/success/', $r['Response']['result'])) {
							$task->mdata['api_cancel'] = 1;
							$task->updateMeta();
						}
					}
				}

				echo $order . ' ' . $status . PHP_EOL;
			}
		}
	}

	protected function log2file($m, $file="direct_err_")
	{//for direct courier err
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'direct' . DIRECTORY_SEPARATOR;
		$lf .= $file. date('Y-m-d') . '.log';
		return file_put_contents($lf, date('Y-m-d H:i:s').' '.$m."\n", FILE_APPEND);
	}

	public function displayCache()
	{
		$cache = Yii::app()->cache->get('sorting_queue');
		echo json_encode($cache) . PHP_EOL;
	}

	public function deleteCache()
	{
		Yii::app()->cache->delete('sorting_queue');
	}

	public function quickCompleteInTask()
	{
		$task = WmsTask::model()->findByPk(190729);
		$plts = ['PLT1912000195', 'PLT1912000196', 'PLT1912000197', 'PLT1912000198'];
		foreach ($task->items as $k => $item) {
			$acItem = new WmsTaskItem;
			$acItem->task_id = $task->actionTask->id;
			$acItem->op_id = 0;
			$acItem->mdata = array(
				'cq' => '',
				'uq' => $item->mdata['uq'],
				'ex' => '',
				'bn' => $item->mdata['bn'],
				'nt' => '',
				'pl' => $plts[$k % 4],
				'gi' => $item->mdata['gi'],
				'gn' => $item->mdata['gn'],
			);
			$acItem->save();
		}
	}

	public function uflCheckOrderInventory()
	{
		$tasks = WmsTask::model()->with('job')->findAll('t.type IN (3020,3030) AND t.is_request = 1 AND job.org_id IN (' . implode(',', Org::$easyships) . ') AND t.status = 10 AND t.meta LIKE "%product not found%"');
		foreach ($tasks as $task) {
			foreach ($task->mdata['errs'] as $err) {
				if (!preg_match('/Product not found/i', $err['message'])) continue;

				preg_match('/EAN\s([\w*\d*\-*]*)\s\\//i', $err['message'], $matches);
				if (empty($matches)) {
					echo $task->getNo() . ' no ean ' . $err['message'] . PHP_EOL;
					continue;
				}

				$ean = $matches[1];
				$prod = WmsProd::model()->with('orgs')->find('orgs.sku = :sku', [':sku' => $ean]);
				if (empty($prod)) {
					echo $task->getNo() . ' ean not exist ' . $err['message'] . PHP_EOL;
					continue;
				}

				$flag = true;
				foreach ($task->items as $item) {
					if (!empty($item->mdata['si'])) {
						$stock = WmsStock::model()->findByPk($item->mdata['si']);
						if ($stock->prod->id == $prod->id) $flag = false;
					} else if (!empty($item->mdata['pi'])) {
						if ($item->mdata['pi'] == $prod->id) $flag = false;
					} else {
						if ($item->mdata['sn'] == $ean) $flag = false;
					}
				}

				if ($flag) {
					echo $task->getNo() . ' fail to create item ' . $err['message'] . PHP_EOL;
				} else {
					$prod = WmsProd::model()->find('ean = :ean', [':ean' => $item->mdata['sn']]);
					if (empty($prod)) {
						$prod = WmsProd::model()->with('orgs')->find('orgs.sku = :sku', [':sku' => $item->mdata['sn']]);
					}
					if (empty($prod)) {
						continue;
					}

					$item->mdata['pi'] = $prod->id;
					$item->mdata['sn'] = $prod->name;
					$item->update('meta');
					echo $task->getNo() . ' created item ' . $err['message'] . PHP_EOL;
				}
			}
		}
	}

	public function updateInvLine()
	{
		$ils = InvLine::model()->findAll('inv_id = :inv_id', [':inv_id' => 120460]);
		foreach ($ils as $il) {
			$il->mdata['items'] = array_values($il->mdata['items']);
			$il->update('meta');
		}
	}

	public function fillin_payterm()
	{
		// $xls = new oExcel;
		// $i = 1;
		$orgs = Org::model()->findAll();
		$count = 0;
		foreach ($orgs as $org) {
			if (!empty($org->extra['creditterms']) && $org->extra['creditterms'] > 1 && empty($org->extra['payterm'])) {
				$org->extra['payterm'] = $org->extra['creditterms'];
				$org->update('meta');
				// $xls->addRow($i++, [$org->name, $org->extra['creditterms'], $org->extra['payterm']]);
			}
		}

		// $xls->output('payterm.xls', null, false);
	}

	public function updateOTINV()
	{
		$recs = Reconciliation::model()->findAll();
		foreach ($recs as $rec) {
			$rec->getOTinvStatus();
		}
	}

	public function sendinvoice()
	{
		$tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		if (!file_exists($tempDirectory)) {
			mkdir($tempDirectory);
		}
		
		//exclude air/sea
		$invoices = Invoice::model()->findAll('id IN (72316,72319,72322,72325,72328,72331,72334,113119,113821,113824,114277,114283,114565,114700,115540,115543,115891,117181,117586,117907,118183,118189,118213,119392,119680,119929,120142,120457,120685,122467,122728,122731,123049,123175,123310)');
		foreach ($invoices as $invoice) {
			$fileName = $tempDirectory.DIRECTORY_SEPARATOR.'Invoice_'.$invoice->no.'.pdf';
			$excelFile = $invoice->exportExcelInvoice($tempDirectory.DIRECTORY_SEPARATOR,false);
			$fileName2='';
//			echo $invoice->no;
			if ($invoice->type != 60) {
				oPDF::renderPDF('invoice', ['inv'=>$invoice], 2, $fileName);
			} else {
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

			$oldInvoiceNo= Invoice::checkNewInvoiceNo($invoice->no);
			//                                  echo $oldInvoiceNo."\n";
			if (!empty($oldInvoiceNo)) {
				$emailLog->isNewInvoice=false;
				$fileName2=$tempDirectory.DIRECTORY_SEPARATOR.'Old_Invoice_'.$oldInvoiceNo.'.pdf';
				$oldInvoice=Invoice::model()->find('no=:no', [":no"=>trim($oldInvoiceNo)]);
				if ($oldInvoice->type != 60) {
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
			if ($emailLog->InvoiceSend($fileName, $fileName2, $excelFile)) {
				// $invoice->status = Invoice::INVOICE_STATUS_POSTED;
				// $invoice->posted = date('Y-m-d');
				// $invoice->update(['status', 'posted']);
				// $invoice->closeInvoice();
				$this->log2file('Invoice '.$invoice->no.'send!', 'invoice_sending');
			}
		}
		AppHelper::unlinkRecursive($tempDirectory);
	}

	public function fastway_cost()
	{
		$date = '2019-06-01';
		$transaction = Yii::app()->db->beginTransaction();
		while ($date != date('Y-m-d')) {
			$url = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'fastway' . DIRECTORY_SEPARATOR . 'fastway_' . $date . '.log';
			if (file_exists($url)) {
				echo $date . PHP_EOL;
				$content = file_get_contents($url);
				$lines = explode("\n", $content);
				foreach ($lines as $line) {
					if (!preg_match('/TotalCostExGST/i', $line)) continue;

					$data = substr($line, 10);
					$data = json_decode($data, true);
					$ref = $data['result']['Items'][0]['labels'][0]['labelNumber'];

					$tranship = Tranship::model()->find('connote = :ref AND cost = 0', [':ref' => $ref]);
					if (empty($tranship)) continue;

					$tranship->cost = $data['result']['TotalCostExGST'];
					$tranship->update('cost');

					$shipment = $tranship->shipment;
					if (empty($shipment->mdata['import_billing_id'])) continue;

					$bl = BillingLine::model()->findByPk($shipment->mdata['import_billing_id']);
					if (empty($bl) || $bl->org_id != 115) continue;

					$bl->accrual_amount += $tranship->cost;
					$bl->update('accrual_amount');
				}
			}

			$date = date('Y-m-d', strtotime($date . ' + 1 day'));
		}

		$transaction->commit();
	}

	public function fix_ledger()
	{
		$tasks = WmsTask::model()->findAll('id IN (190297,191926,191950,191890,193339,193354,193366,193393,193402,193426,193438,193450,193453,193489,193510,193543,193570,194812,194848,194893,194917,194974,195019,195034,195115,195253,194806)');
		foreach ($tasks as $task) {
			foreach ($task->items as $item) {
				$item->toStock();
			}
		}
	}

	public function link_cancel_ship()
	{
		$tasks = WmsTask::model()->findAll('type = 2120 AND is_request = 0');
		foreach ($tasks as $task) {
			if (empty($task->mainTask)) continue;

			$condition = 'cref = :cref';
			$params = [':cref' => $task->mainTask->getNo()];
			if (!empty($task->mdata['shipment_id'])) {
				$condition .= ' AND id NOT IN (' . implode(',', $task->mdata['shipment_id']) . ')';
			}

			$cancel = [];
			$rs = Shipment::model()->findAll($condition, $params);
			foreach ($rs as $r) {
				$cancel[] = $r->id;
			}

			if (!empty($cancel)) {
				$task->mdata['cancel_shipment_id'] = $cancel;
				$task->update('meta');
			}
		}
	}

	public function item_no_ledger()
	{
		$id = $this->prompt('ID: ');
		if (empty($id)) return;
		$items = WmsTaskItem::model()->findAll('task_id = :task_id AND del = 0', [':task_id' => $id]);
		foreach ($items as $item) {
			$item->toStock();
		}
	}

	public function extract_easyship_response()
	{
		$url = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'ufl_api.log';
		$content = file_get_contents($url);
		$lines = explode("\n", $content);
		foreach ($lines as $k => $line) {
			if (preg_match('/{\"result\":\"success\",\"code\":\"200\"}/', $line)) {
				$info = json_decode(substr($lines[$k-1], 28), true);
				$order = $info['orderNo'];

				$info = json_decode(substr($lines[$k-1], 28), true);
				$status = $info['status'];

				if (!empty($order) && !empty($status)) {
					$task = WmsTask::model()->find('ref = :ref', [':ref' => $order]);
					unset($task->mdata['api_com']);
					if ($status == 'RS') {
						$task->mdata['api_comp'] = 1;
						$task->updateMeta();
					} else if ($status == 'C') {
						$task->mdata['api_ship'] = 1;
						$task->updateMeta();
					} else if ($status == 'X') {
						$task->mdata['api_cancel'] = 1;
						$task->updateMeta();
					}
				}

				echo $order . ' ' . $status . PHP_EOL;
			}
		}
	}

	public function uflResendComp()
	{
		$task = WmsTask::model()->find('ref = :ref', [':ref' => $this->prompt('Ref: ')]);
		if (!empty($task) && !empty($task->mdata['api_hook']) && !empty($task->mdata['api_comp'])) {
			$api = new UflAPI;
			if (!empty($task->mdata['sourceLinkId'])) {
				$data = [
					'completedDate' => date('Y-m-d H:i:s'),
					'completedTimeZone' => date('GMT+8'),
					'sourceLinkId' => $task->mdata['sourceLinkId'],
					'orderSPT' => $task->mdata['orderSPT'],
				];
				if (!empty($task->deliveryTask->mdata['shipment_id'])) {
					$vcourier = $task->_getCourierNameAndRef();
					$data['courierBillNo'] = implode(', ', $vcourier[0]);
					$data['courierName'] = $vcourier[1];
				}
			} else {
				$data = [];
			}
			$r = $api->outboundUpdate($task->mdata['api_meta']['customer'], $task->ref, 'RS', $data);
		}
	}

	public function uflResendShip()
	{
		$task = WmsTask::model()->find('ref = :ref', [':ref' => $this->prompt('Ref: ')]);
		if (!empty($task) && !empty($task->mdata['api_hook']) && !empty($task->mdata['api_ship'])) {
			$api = new UflAPI;
			if (!empty($task->mdata['sourceLinkId'])) {
				$data = [
					'completedDate' => date('Y-m-d H:i:s'),
					'completedTimeZone' => date('GMT+8'),
					'sourceLinkId' => $task->mdata['sourceLinkId'],
					'orderSPT' => $task->mdata['orderSPT'],
				];
				if (!empty($task->deliveryTask->mdata['shipment_id'])) {
					$vcourier = $task->_getCourierNameAndRef();
					$data['courierBillNo'] = implode(', ', $vcourier[0]);
					$data['courierName'] = $vcourier[1];
				}
			} else {
				$data = [];
			}
			$r = $api->outboundUpdate($task->mdata['api_meta']['customer'], $task->ref, 'C', $data);
		}
	}

	public function wms_ledger_fix_by_task()
	{
		$task = WmsTask::model()->findByPk($this->prompt('ID: '));
		if (!empty($task->items)) {
			foreach ($task->items as $item) {
				if (!empty($item->stockLedgers)) {
					if (count($item->stockLedgers) == 1 && $item->stockLedgers[0]->qty_in != $item->mdata['uq']) {
						$item->stockLedgers[0]->qty_in = $item->mdata['uq'];
						$item->stockLedgers[0]->update('meta');
						WmsStock::countAll($item->mdata['si']);
					}
				} else {
					$sl = new WmsStockLedger;
					$sl->ti_id = $item->id;
					$sl->qty_in = $item->mdata['uq'];
					$sl->stock_id = $item->mdata['si'];
					$sl->location_id = 4;
					$sl->ts = $item->ts;
					$sl->save();
					WmsStock::countAll($item->mdata['si']);
				}
			}
		}
	}

	public function wms_task_dpmt()
	{
		$tasks = WmsTask::model()->findAll('is_request = 1');
		foreach ($tasks as $task) {
			if (empty($task->mdata['dpmt'])) {
				$task->noAfterSave = true;
				if (!empty($task->job->customer->extra['sp_id']) && in_array($task->job->customer->extra['sp_id'], WmsTask::$op)) {
					$task->mdata['dpmt'] = '3PL';
					$task->updateMeta();
				} else if (!empty($task->job->customer->extra['op_id']) && in_array($task->job->customer->extra['op_id'], WmsTask::$op)) {
					$task->mdata['dpmt'] = '3PL';
					$task->updateMeta();
				} else if (!empty($task->job->customer->extra['sp_id']) && in_array($task->job->customer->extra['sp_id'], EdiJob::$op)) {
					$task->mdata['dpmt'] = '大货';
					$task->updateMeta();
				} else if (!empty($task->job->customer->extra['op_id']) && in_array($task->job->customer->extra['op_id'], EdiJob::$op)) {
					$task->mdata['dpmt'] = '大货';
					$task->updateMeta();
				}
			}
		}
	}

	public function ninja_shark_address()
	{
		$url = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'wmsapi' . DIRECTORY_SEPARATOR . 'wms_api_2020-01-03.log';
		$content = file_get_contents($url);
		$lines = explode("\n", $content);
		$orders = [];
		foreach ($lines as $line) {
			if ($line == '') continue;
			$data = substr($line, 20);
			if ($data == '') continue;
			$data = json_decode($data, true);
			if (empty($data['orders'])) continue;
			foreach ($data['orders'] as $order) {
				if (empty($order['to'])) continue;
				$task = WmsTask::model()->find('ref = :ref', [':ref' => $order['no']]);
				if (empty($task) || $task->status != 40) continue;
				$task->deliveryTask->mdata['cnee'] = $order['to'];
				$task->deliveryTask->mdata['cnee']['tel'] = $order['to']['phone'];
				$task->deliveryTask->update('meta');

				if (!empty($task->actionTask->items)) {
					$task->status = 30;
					$task->save();
				} else {
					$task->status = 20;
					$task->save();
				}
			}
		}
	}

	public function aupost_fix_eparcel()
	{
		$sql = 'select sum(json_value(meta, "$.actual_delivery_cost")) as amount, json_value(meta, "$.import_billing_id") as id from shipment where ref in (select shipment_no from reconciliation_line where parent_id in (SELECT id FROM `reconciliation` WHERE client_type = 1 and manifest_no in (select shipment_no from reconciliation_line where invoice_no = "' . $this->prompt('Invoice No: ') . '" and postcode = "eParcel"))) group by json_value(meta, "$.import_billing_id")';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rs as $r) {
			$bl = BillingLine::model()->findByPk($r['id']);
			$r['amount'] = number_format(round($r['amount'] * 100) / 100, 2, '.', '');
			if ($bl->actual_amount != $r['amount']) {
				echo $bl->id . ' ' . $bl->actual_amount . ' ' . $r['amount'] . PHP_EOL;
				$bl->actual_amount = $r['amount'];
				$bl->gst_amount = $bl->getGSTValue();
				$bl->update('actual_amount', 'gst_amount');
			}
		}
	}

	public function pallet_out_update()
	{
		$task = WmsTask::model()->findByPk($this->prompt('TASK ID: '));
		foreach ($task->actionTask->items as $item) {
			$item->toStock();
		}
	}

	public function wms_item_fix_by_task()
	{
		$task = WmsTask::model()->findByPk($this->prompt('ID: '));
		if (!empty($task->actionTask->items)) {
			foreach ($task->actionTask->items as $item) {
				$res_item = WmsTaskItem::model()->find('task_id = :tid AND JSON_VALUE(meta, "$.si") = :sid AND JSON_VALUE(meta, "$.uq") = :uq', [':tid' => $task->id, ':sid' => $item->mdata['si'], ':uq' => $item->mdata['uq']]);
				if (!empty($res_item) && empty($res_item->mdata['acti_id'])) {
					$res_item->mdata['acti_id'] = $item->id;
					$res_item->update('meta');
				}
				$res_item = WmsTaskItem::model()->find('task_id = :tid AND JSON_VALUE(meta, "$.si") = :sid AND JSON_VALUE(meta, "$.uq") = :uq AND JSON_VALUE(meta, "$.acti_id") = :acti_id', [':tid' => $task->id, ':sid' => $item->mdata['si'], ':uq' => $item->mdata['uq'], ':acti_id' => $item->id]);
				if (empty($res_item)) {
					$res_item = new WmsTaskItem;
					$res_item->task_id = $task->id;
					$res_item->mdata = array(
						'si' => $item->mdata['si'],
						'sn' => $item->mdata['sn'],
						'pq' => '',
						'cq' => '',
						'uq' => $item->mdata['uq'],
						'pli' => '',
						'pl' => '',
						'nt' => '',
						'acti_id' => $item->id,
					);
					$res_item->ts = $item->ts;
					$res_item->save();
				} else if (!empty($res_item->stockLedgers[0]) && count($res_item->stockLedgers) == 1 && $res_item->stockLedgers[0]->qty_in != $res_item->mdata['uq']) {
					$res_item->stockLedgers[0]->qty_in = $res_item->mdata['uq'];
					$res_item->stockLedgers[0]->update('meta');
					WmsStock::countAll($item->mdata['si']);
				}
			}
		}
	}

	public function exchange2batch()
	{
		$wtbs = WmsTaskBatch::model()->findAll();
		foreach ($wtbs as $wtb) {
			if (!empty($wtb->batch_id)) continue;

			$batch = WmsBatch::model()->find('date = :date AND type = :type AND no = :no', [':date' => $wtb->date, ':type' => $wtb->type, ':no' => date('ymd', strtotime($wtb->date)) . ' - ' . sprintf('%02d', $wtb->batch) . ' - ' . WmsBatch::$types[$wtb->type]]);
			if (empty($batch)) {
				$batch = new WmsBatch;
				$batch->date = $wtb->date;
				$batch->type = $wtb->type;
				$batch->status = 10;
				$batch->org_id = $wtb->org_id;
				$batch->save();
			}

			if ($batch->org_id != $wtb->org_id && (empty($batch->mdata['org_id']) || $batch->mdata['org_id'] != $wtb->org_id)) {
				$batch->mdata['org_id'] = $wtb->org_id;
				$batch->update('meta');
			}

			$wtb->batch_id = $batch->id;
			$wtb->update('batch_id');
		}
	}

	public function courierDpmt()
	{
		$billings = Billing::model()->findAll('org_id IN (' . implode(',', Org::$couriers) . ') AND status != 11');
		foreach ($billings as $billing) {
			$dpmts = [];
			foreach ($billing->lines as $line) {
				$dpmts[] = $line->dpmt;
			}

			$dpmts = array_unique($dpmts);

			$billing->mdata['dpmts'] = $dpmts;
			$billing->update('meta');
		}
	}

	public function calRecLine()
	{
		$no = $this->prompt('No: ');
		$line = ReconciliationLine::model()->find('shipment_no = :no', [':no' => $no]);
		if (!empty($line)) {
			if (preg_match('/7RFZ\d{8}/', $no)) {
				$org_rate_id = ImportChargeCode::STARTRACK_SYDNEY;
			} else if (preg_match('/4XHZ\d{8}/', $no)) {
				$org_rate_id = ImportChargeCode::STARTRACK_MEL;
			}
			if (!empty($org_rate_id)) {
				$ourRate = Shipment::getCourierCostByShipment($line->shipment_no, $org_rate_id, $line->weight);
				echo json_encode($ourRate) . PHP_EOL;
			}
		}
	}

	public function fix_st_recline()
	{
		$lines = ReconciliationLine::model()->with('parent')->findAll('parent.id >= 23638 AND parent.client_type = 2 AND my_value = 0');
		foreach ($lines as $line) {
			if (preg_match('/7RFZ\d{8}/', $line->shipment_no)) {
				$org_rate_id = ImportChargeCode::STARTRACK_SYDNEY;
			} else if (preg_match('/4XHZ\d{8}/', $line->shipment_no)) {
				$org_rate_id = ImportChargeCode::STARTRACK_MEL;
			}
			if (!empty($org_rate_id)) {
				$ourRate = Shipment::getCourierCostByShipment($line->shipment_no, $org_rate_id, $line->weight);
				if (!empty($ourRate)) {
					$line->my_value = $ourRate['price'];
					$line->update('my_value');
				}
			}
		}
	}

	public function fix_billing_total()
	{
		$billings = Billing::model()->findAll('status != 11 AND total = 0');
		foreach ($billings as $billing) {
			$billing->calTotal();
		}
	}

	public function syncCin7Prod()
	{
		$custs = WmsAPI::model()->findAll('type = :type AND status = :status', [':type' => WmsAPI::WMS_API_TYPE_CIN7, ':status' => 1]);
		foreach ($custs as $cust) {
			$config = array(
				'user' => $cust->api_key,
				'key' => $cust->api_secret,
				'org_id' => $cust->org_id,
			);
			$cin7 = new Cin7API($config);
			$this->_syncCin7Prod($cin7);
		}
	}

	public function _syncCin7Prod($cin7)
	{
		$r = $cin7->getProducts();

		$transaction = Yii::app()->db->beginTransaction();
		try {
			if ($r['done'] == true && empty($r['items']['message'])) {
				foreach ($r['items'] as $item) {
					if ($item['orderType'] == 'Kit') {
						yii::log(json_encode($item), 'warning');
						$option = $cin7->getProductOptions($item['productOptions'][0]['id']);
						yii::log(json_encode($option), 'warning');
					}
				}
			}
		} catch (Exception $ex) {
			$transaction->rollback();
			$this->log('Sync CIN7 product fail');
			throw $ex;
		}
	}

	public function fixLetterShipment()
	{
		$tasks = WmsTask::model()->with('job', 'mainTask')->findAll('job.org_id = 3058 AND mainTask.status != 100 AND t.type = 2120 AND JSON_QUERY(t.meta, "$.shipment_id") IS NULL');
		foreach ($tasks as $dt) {
			$task = $dt->mainTask;
			$printed = 0;
			foreach ($task->items as $item) {
				$stk = WmsStock::model()->findByPk($item->mdata['si']);

				for ($i = 1; $i <= $item->mdata['uq']; $i++) {
					$ar = new Addr;
					$ar->name = $task->job->customer->name . ((!empty($task->job->customer->extra['sp_id']) && $task->job->customer->extra['sp_id'] == 305) ? ' - 3PL' : '');
					$ar->tel = $task->job->customer->phone;
					$ar->country = 'Australia';
					$ar->save();

					$ae = new Addr;
					$ae->setAttributes($dt->mdata['cnee']);
					$ae->save();

					$p = new ImParcel;
					$p->setAttributes(array(
						'agent_id' => Org::ORGID_3PL_XCSOURCE,
						'odpt_id' => 106,
						'cnor_id' => $ar->id,
						'cnee_id' => $ae->id,
						'status' => 10,
						'state' => $ae->state,
						'postcode' => $ae->postcode,
						'pkg' => 1,
						'currency' => 1,
						'ref' => $task->getNo() . '-' . ($printed+1),
						'cref' => $task->ref,
						'hbn' => $task->getNo() . '-' . ($printed+1),
					));
					$p->mdata['show_sku'] = true;
					$p->eitems = ['sku' => ['<span style="font-size:1.2em">'.$item->mdata['sn'].' '.$stk->prod->ean.' x 1</span>']];
					$p->save();

					$dt->mdata['shipment_id'][] = $p->id;

					$printed ++;
				}
			}
			$dt->update('meta');
		}
	}

	public function checkFWinvoice()
	{
		$p = Shipment::model()->find('ref = :ref', [':ref' => $this->prompt('Ref: ')]);
		$chargeCode = '';
		if ($p->agent_id == 1206) {
			$chargeCode = 8271;
		}
		echo $p->getChargeByChargecode($chargeCode, true) . PHP_EOL;
	}

	public function fix_short_release()
	{
		$items = WmsTaskItem::model()->findAll('id IN (573230,573233,573236,573239,573242,573245,573248,573251,573254,573257,573260,573263,573266,573269,573272)');
		foreach ($items as $item) {
			$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id AND qty - qty_res > 0', [':prod_id' => $item->mdata['pi'], ':org_id' => $item->task->job->org_id]);
			$qty = $item->mdata['uq'];
			foreach ($stocks as $stock) {
				if ($qty <= 0) {
					break;
				}
				$uq = min($qty, $stock->availQty());

				$new_item = new WmsTaskItem;
				$new_item->task_id = $item->task_id;
				$new_item->op_id = $item->op_id;
				$new_item->ts = $item->ts;
				$new_item->mdata = [
					'si' => $stock->id,
					'sn' => $stock->prod->name,
					'pq' => '',
					'cq' => '',
					'uq' => $uq,
					'pli' => '',
					'pl' => '',
					'nt' => '',
					'pi' => $stock->prod->id,
				];
				$new_item->save();
				$new_item->toStock();
				$qty -= $uq;
			}
		}
	}

	public function fixUBI()
	{
		$consols = Consol::model()->findAll('id IN (53780,54101,53585,53777,54104,53690,53588,53786,53900,53783,53894,53582)');
		foreach ($consols as $consol) {
			$consol->updateCourierRealCost(Org::ORGID_COURIER_UBI, "33A8Y\d{7}|ZK6\d{7}|33PET\d{7}|33PEN\d{7}|33PEH\d{7}|SJU\d{7}|33G7K\d{7}|33G7L\d{7}|33A93\d{7}|33G7P\d{7}|33G7M\d{7}");
		}
	}

	public function testABM()
	{
		$task = WmsTask::model()->findByPk($this->prompt('ID: '));

		echo json_encode($task->sendABM());
	}

	public function dw_vertex()
	{
		$invs = Invoice::model()->findAll('to_id = 1308 and type = 70 and status != 10');
		$paid = 0;
		$credited = 0;
		$total = 0;
		foreach ($invs as $inv) {
			foreach ($inv->payments as $pay) {
				if ($pay->payment->status == 9) continue;
				if ($pay->payment->bank == 90 && $pay->payment->type == 5) continue 2;
			}
			foreach ($inv->lines as $line) {
				foreach ($line->mdata['items'] as $item) {
					if (empty($shipments[$item[3]][strval($item[6])])) {
						$shipments[$item[3]][strval($item[6])] = 0;
					}
					$shipments[$item[3]][strval($item[6])] ++;
				}
			}

			foreach ($inv->payments as $pay) {
				if ($pay->payment->status == 9) continue;
				if ($pay->payment->type == 5) {
					$credited += $pay->amount;
				} else {
					$paid += $pay->amount;
				}
			}

			$total += $inv->total;
		}

		$xls = new oExcel;
		$xls->setTitle('Normal');
		$i = 1;
		$xls->addRow($i++, ['Ref', 'Amount']);
		$normal = 0;
		foreach ($shipments as $k => $shipment) {
			foreach ($shipment as $amount => $qty) {
				$xls->addRow($i++, [$k, $amount]);
				break;
			}
			$normal += $amount;
		}
		$xls->addRow($i++, []);
		$xls->addRow($i++, ['Total: ', round($normal * 100) / 100]);
		$xls->addRow($i++, ['Total (Incl GST): ', round($normal * 1.1 * 100) / 100]);

		$xls->createSheet();
		$xls->goSheet(1);
		$xls->setTitle('OverCharge');
		$i = 1;
		$xls->addRow($i++, ['Ref', 'Qty', 'Amount', 'Subtotal']);
		$over = 0;
		foreach ($shipments as $k => $shipment) {
			$first = true;
			foreach ($shipment as $amount => $qty) {
				if ($first) {
					$first = false;
					if ($qty == 1) continue;
					$xls->addRow($i++, [$k, ($qty - 1), $amount, ($qty - 1) * $amount, ($qty - 1) * $amount]);
					$over += ($qty - 1) * $amount;
				} else {
					$xls->addRow($i++, [$k, $qty, $amount, $qty * $amount, $qty * $amount]);
					$over += $qty * $amount;
				}
			}
		}
		$xls->addRow($i++, []);
		$xls->addRow($i++, ['', '', '', 'Total: ', round($over * 100) / 100]);
		$xls->addRow($i++, ['', '', '', 'Total (Incl GST): ', round($over * 1.1 * 100) / 100]);

		$xls->createSheet();
		$xls->goSheet(2);
		$xls->setTitle('Invoice');
		$i = 1;
		$xls->addRow($i++, ['Invoice no', 'Total', 'Paid', 'Credited']);
		foreach ($invs as $inv) {
			foreach ($inv->payments as $pay) {
				if ($pay->payment->status == 9) continue;
				if ($pay->payment->bank == 90 && $pay->payment->type == 5) continue 2;
			}

			$temp_paid = 0;
			$temp_credited = 0;
			foreach ($inv->payments as $pay) {
				if ($pay->payment->status == 9) continue;
				if ($pay->payment->type == 5) {
					$temp_credited += $pay->amount;
				} else {
					$temp_paid += $pay->amount;
				}
			}
			$xls->addRow($i++, [$inv->no, $inv->total, $temp_paid, $temp_credited]);
		}
		$payments = Payment::model()->findAll('org_id = 1308 AND type = 5 AND bank = 91 AND ata > 0 AND status != 9');
		$unalloc_crn = 0;
		foreach ($payments as $payment) {
			$unalloc_crn += $payment->ata;
		}

		$xls->addRow($i++, []);
		$xls->addRow($i++, ['Total: ', $total, $paid, $credited]);
		$xls->addRow($i++, ['Unallocated Credit Note: ', '', '', $unalloc_crn]);
		$xls->addRow($i++, ['Invoice Due', $total - $paid - $credited - $unalloc_crn]);
		$xls->output('Dw Vertex Invoice Issue.xlsx', null, false);
	}

	public function fixTNT()
	{
		$reclines = ReconciliationLine::model()->findAll('parent_id IN (30107,30113)');
		foreach ($reclines as $line) {
			if ($line->mdata['tnt_type'] == 'Shipment') {
				$org_rate_id = ImportChargeCode::TNT_SYDNEY_ID;
				if (preg_match('/PCD\d{9}/', $line->shipment_no)) {
					$org_rate_id = ImportChargeCode::TNT_MELBOURNE_ID;
				} else if (preg_match('/BPC\d{9}/', $line->shipment_no)) {
					$org_rate_id = ImportChargeCode::TNT_BRISBANE_ID;
				}
				$ourRate = Shipment::getCourierCostByShipment($line->shipment_no, $org_rate_id, $line->weight);
				$line->my_value = $ourRate['price'];
			} else {
				$line->my_value = $line->value;
			}
			$line->update('my_value');
		}
	}

	public function fixstockallout()
	{
		$task = WmsTask::model()->findByPk($this->prompt('ID: '));
		foreach ($task->actionTask->items as $item) {
			$item->delete();
		}
	}

	public function transferLF()
	{
		// $locs = WmsLocation::model()->with('parent')->findAll('t.type = 60 AND parent.name LIKE "D2-%-D%"');
		// foreach ($locs as $loc) {
		// 	$new = WmsLocation::model()->find('name = :name', [':name' => 'B2' . substr($loc->parent->name, 2)]);
		// 	echo $loc->name . ' ' . $loc->parent->name . ' ' . $new->name . PHP_EOL;
		// 	$loc->pid = $new->id;
		// 	$loc->update('pid');
		// }

		// $locs = WmsLocation::model()->with('parent')->findAll('t.type = 60 AND parent.name LIKE "E2-%-D%"');
		// foreach ($locs as $loc) {
		// 	$new = WmsLocation::model()->find('name = :name', [':name' => 'C1' . substr($loc->parent->name, 2)]);
		// 	echo $loc->name . ' ' . $loc->parent->name . ' ' . $new->name . PHP_EOL;
		// 	$loc->pid = $new->id;
		// 	$loc->update('pid');
		// }


		// for ($i = 1; $i <= 13; $i++) {
		// 	$locs = WmsLocation::model()->with('parent')->findAll('t.type = 60 AND parent.name LIKE "D2-' . sprintf("%02d", $i) . '-U%"');
		// 	foreach ($locs as $loc) {
		// 		$new = WmsLocation::model()->find('name = :name', [':name' => 'B2' . substr($loc->parent->name, 2)]);
		// 		echo $loc->name . ' ' . $loc->parent->name . ' ' . $new->name . PHP_EOL;
		// 		$loc->pid = $new->id;
		// 		$loc->update('pid');
		// 	}
		// }
		// for ($i = 15; $i <= 16; $i++) {
		// 	$locs = WmsLocation::model()->with('parent')->findAll('t.type = 60 AND parent.name LIKE "D2-' . sprintf("%02d", $i) . '-U%"');
		// 	foreach ($locs as $loc) {
		// 		$new = WmsLocation::model()->find('name = :name', [':name' => 'B2-' . ($i-1) . substr($loc->parent->name, 5)]);
		// 		echo $loc->name . ' ' . $loc->parent->name . ' ' . $new->name . PHP_EOL;
		// 		$loc->pid = $new->id;
		// 		$loc->update('pid');
		// 	}
		// }
		// for ($i = 9; $i <= 14; $i++) {
		// 	$locs = WmsLocation::model()->with('parent')->findAll('t.type = 60 AND parent.name LIKE "E2-' . sprintf("%02d", $i) . '-U%"');
		// 	foreach ($locs as $loc) {
		// 		$new = WmsLocation::model()->find('name = :name', [':name' => 'C1-' . sprintf("%02d", $i) . '-U%']);
		// 		echo $loc->name . ' ' . $loc->parent->name . ' ' . $new->name . PHP_EOL;
		// 		$loc->pid = $new->id;
		// 		$loc->update('pid');
		// 	}
		// }
	}

	public function getAupostCost()
	{
		$date = '2020-03-08';
		while ($date != date('Y-m-d')) {
			$date = date('Y-m-d', strtotime($date . ' + 1 day'));
			$url = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'auspost' . DIRECTORY_SEPARATOR . 'auspost_api_' . $date . '.log';
			if (!file_exists($url)) continue;
			echo $date . PHP_EOL;
			$content = file_get_contents($url);
			$lines = explode("\n", $content);
			foreach ($lines as $line) {
				if (!preg_match('/total_cost/i', $line)) continue;

				$data = substr($line, 10);
				$data = json_decode($data, true);
				foreach ($data['order']['shipments'] as $s) {
					$ts = Tranship::model()->find('status != 99 AND cost = 0 AND JSON_VALUE(meta, "$.sid") = :sid', [':sid' => $s['shipment_id']]);
					if (empty($ts)) continue;

					$costValue = floatval($s['shipment_summary']['total_cost'] - $s['shipment_summary']['total_gst']);
					$ts->cost = round($costValue, 2);
					$ts->save();

					if (empty($ts->shipment->consol)) continue;
					if (!empty($ts->shipment->mdata['import_billing_id'])) {
						$bl = BillingLine::model()->findByPk($ts->shipment->mdata['import_billing_id']);
						$bl->accrual_amount += $ts->cost;
						$bl->save();
					} else {
						$bl = BillingLine::model()->find('billing_ref = :ref AND org_id = 101', [':ref' => $ts->shipment->consol->no]);
						if (!empty($bl)) {
							$bl->accrual_amount += $ts->cost;
							$bl->save();
						}
					}
				}
			}
		}
	}

	public function shipment_scan()
	{
		$lines = ReconciliationLine::model()->findAll('parent_id = 30861');
		foreach ($lines as $line) {
			$shipment = $line->shipment;
			$type = 4;
			$sn = 1;
			$tempWareHouse = 106;

			$shipment_scan = new ShipmentScan;
			$shipment_scan->pid = $shipment->id;
			$shipment_scan->user_id = 73;
			$shipment_scan->type = $type;
			$shipment_scan->weight = round($shipment->weight/$shipment->pkg, 2);
			$shipment_scan->pno = $sn;
			$shipment_scan->warehouse = $tempWareHouse;
			$shipment_scan->pkg = 1;
			$shipment_scan->scan_time = date('Y-m-d H:i:s');
			$shipment_scan->save();
		}
	}

	public function shopify_dup()
	{
		$prods = WmsProdMap::model()->findAll('id IN (7,4,61,28,31,34,46,49,52,55,58,22,25,10,13,16,19,37,40,43,64,67,247,244,220,229,232,241,223,226,238,235,249,265,271,268,300363,796,799,871,793,781,784,769,772,775,856,859,862,865,868,832,835,820,823,826,829,847,850,853,1027,1030,1018,1021,1024,838,841,844,808,817,778,886,895,898,901,919,787,790,802,805,910,913,904,907,448,469,478,481,484,916,874,877,880,883,502,283,754,1061,1064,493,760,763,475,499,457,460,463,466,925,928,931,1535,970,973,976,979,982,985,934,937,940,946,949,952,955,958,961,964,967,1006,1009,1012,1015,988,991,1000,1003,1082,547,550,553,556,559,562,511,514,517,523,526,529,532,535,538,541,544,589,592,595,598,571,574,565,568,583,586,508,994,997,577,580,451,454,892,1085,811,814,1088,1076,1079,1067,1070,1073,173331,173334,372867,372870,372873,1091,60899,85049,85052)');
		foreach ($prods as $prod) {
			$dups = WmsProdMap::model()->findAll('fid = :fid AND prod_id = :pid AND id != :id', [':fid' => $prod->fid, ':pid' => $prod->prod_id, ':id' => $prod->id]);
			foreach ($dups as $dup) {
				$dup->delete();
			}
		}
	}

	public function fixUBIDup()
	{
		$lines = BillingLine::model()->findAll('org_id = :org_id', [':org_id' => Org::ORGID_COURIER_UBI]);
		foreach ($lines as $line) {
			$dups = BillingLine::model()->findAll('billing_ref = :ref AND billing_ref = billing_cref AND org_id = :org AND actual_amount = 0', [':ref' => $line->billing_ref, ':org' => Org::ORGID_COURIER_UBI]);
			foreach ($dups as $dup) {
				$dup->status = 11;
				$dup->mdata['ubi_acr'] = true;
				$dup->update('status', 'meta');
			}

			$dups = BillingLine::model()->findAll('billing_ref = :ref AND billing_cref = :cref AND org_id = :org AND `desc` NOT LIKE "%rts%"', [':ref' => $line->billing_ref, ':cref' => $line->billing_cref, ':org' => Org::ORGID_COURIER_UBI]);
			if (sizeof($dups) > 1) {
				foreach ($dups as $k => $dup) {
					if ($k == 0) continue;
					$dup->status = 11;
					$dup->mdata['ubi_dup'] = true;
					$dup->update('status', 'meta');
				}
			}
		}
	}

	public function createBL()
	{
		$bl = new BillingLine;
		$bl->org_id = $this->prompt('Org ID: ');
		$bl->type = $this->prompt('Type: ');
		$bl->save();
		echo $bl->id . PHP_EOL;
	}

	public function copyBL()
	{
		$bl = BillingLine::model()->findByPk($this->prompt('ID: '));
		BillingLine::copy($bl);
	}

	public function checkOT()
	{
		$ils = InvLine::model()->findAll("id IN (693891,693882,693876,693873,693864,693867,693861,693849,693852,693855,693837,693840,693834,693831,693819,693807,693813,693816,688812,688800,688803,688809,688791,688785,688779,688776,688773,688767,688764,688752)");
		foreach ($ils as $il) {
			$shipment = ImParcel::model()->find('ref = :ref', [':ref' => substr($il->det, 0, 12)]);
			if (empty($shipment->mdata['charge_client_amount'])) {
				echo $shipment->no . PHP_EOL;
				continue;
			}
			$il->amount = floatval(@$shipment->mdata['charge_client_amount']) * 0.5;
			if (!empty($agent->extra['incl_gst'])) {
				$il->tax = 'OUTPUT';
				$il->gst = $il->amount * 10 / 100;
				$il->amount += $il->gst;
			} else {
				$il->tax = 'EXEMPTOUTPUT';
				$il->gst = 0;
			}
			$il->save();

			$invoice = Invoice::model()->findByPk($il->inv_id);
			$invoice->getTotal();
			$invoice->update('total');
		}
	}

	public function fix_aupost_mani()
	{
		$sql = 'select a.value, s.ref, s.id, json_value(s.meta, "$.actual_delivery_cost") from (select sum(rl.value) as value, shipment_id from `reconciliation_line` rl where rl.parent_id in (select id from reconciliation where client_type = 1 and invoice_date >= "2019-07-01") group by rl.shipment_no) a join shipment s on s.id = a.shipment_id where json_value(s.meta, "$.actual_delivery_cost") != a.value';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rs as $r) {
			$line = Shipment::model()->findByPk($r['id']);
			$line->mdata['actual_delivery_cost'] = $r['value'];
			$line->save();
		}


		$sql = 'select a.amount, a.bid, bl.actual_amount from (select sum(json_value(meta, "$.actual_delivery_cost")) as amount, json_value(meta, "$.import_billing_id") as bid from shipment where id in (select shipment_id from `reconciliation_line` rl where rl.parent_id in (select id from reconciliation where client_type = 1 and invoice_date >= "2019-07-01")) group by json_value(meta, "$.import_billing_id")) a join billing_line bl on a.bid = bl.id where bl.actual_amount != a.amount';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rs as $r) {
			$line = BillingLine::model()->findByPk($r['bid']);
			$line->actual_amount = number_format($r['amount'], 2, '.', '');
			$line->gst_amount = $line->getGSTValue();
			$line->save();
		}
	}

	public function deleteCacheByName()
	{
		Yii::app()->cache->delete($this->prompt('Delete: '));
	}

	public function getCacheByName()
	{
		echo json_encode(Yii::app()->cache->get($this->prompt('Get: ')));
	}

	public function checkAndFixUBI()
	{
		$rls = ReconciliationLine::model()->with('parent')->findAll('parent.client_type IN (12,13)');
		$sum = [];
		foreach ($rls as $rl) {
			if (empty($rl->consol_id)) continue;
			if (empty($sum[$rl->invoice_no][$rl->consol->no])) $sum[$rl->invoice_no][$rl->consol->no] = ['actual' => 0, 'accrual' => 0];
			$sum[$rl->invoice_no][$rl->consol->no]['actual'] += $rl->value;
			$sum[$rl->invoice_no][$rl->consol->no]['accrual'] += $rl->my_value;

			if ($rl->parent->client_type != Reconciliation::UBI_TYPE) continue;
			if ($rl->shipment->mdata['actual_delivery_cost'] != $rl->value) {
				$rl->shipment->mdata['actual_delivery_cost'] = $rl->value;
				$rl->shipment->updateMeta();
			}
		}

		foreach ($sum as $invoice_no => $consols) {
			foreach ($consols as $consol_no => $value) {
				$bl = BillingLine::model()->find('billing_cref = :invoice_no AND billing_ref = :consol_no AND org_id = :oid AND status != 11', [':invoice_no' => $invoice_no, ':consol_no' => $consol_no, ':oid' => Org::ORGID_COURIER_UBI]);

				if (!empty($bl) && ($bl->actual_amount - $value['actual'] >= 0.1 || $value['actual'] - $bl->actual_amount >= 0.1 || $bl->accrual_amount - $value['accrual'] >= 0.1 || $value['accrual'] - $bl->accrual_amount >= 0.1)) {

					$bl->accrual_amount = $value['accrual'];
					$bl->actual_amount = $value['actual'];
					$bl->gst = 'INPUT';
					$bl->gst_amount = $bl->getGSTValue();
					$bl->update('accrual_amount', 'actual_amount', 'gst', 'gst_amount');

					echo 'fix ' . $invoice_no . ' ' . $consol_no . PHP_EOL;

				} else if (empty($bl)) {

					echo 'fix ' . $invoice_no . ' ' . $consol_no . PHP_EOL;

					$invoice = Reconciliation::model()->find('invoice_no = :no', [':no' => $invoice_no]);
					$consol = Consol::model()->find('no = :no', [':no' => $consol_no]);
					if (empty($invoice) || empty($consol)) continue;

					$newBilling = new BillingLine();
					$newBilling->mdata['from_rec'] = 1;
					$newBilling->status = 1;
					$newBilling->link_id = 0;
					$newBilling->org_id = Org::ORGID_COURIER_UBI;
					$newBilling->charge_code = Consol::AU_LOCAL_DELIVERY_COST_GL_CODE;
					$newBilling->billing_cref = $invoice->invoice_no;
					$newBilling->currency = 1;
					$newBilling->weight = 0;
					$newBilling->charge_weight = 0;

					$newBilling->billing_ref = $consol->no;
					$newBilling->awb = $consol->awb;
					$newBilling->dpt_id = empty($consol->dpt_id) ? Org::PCAE_DEPARTMENT_SYDNEY : $consol->dpt_id;
					$newBilling->date = $invoice->invoice_date;
					$newBilling->created = date('Y-m-d');
					$newBilling->transaction_date = date('Y-m-d');
					$newBilling->due = date('Y-m-d');
					$newBilling->type = BillingLine::BILLING_TYPE_IMPORT;
					$newBilling->dpmt = Invoice::DPMT_IMPORT;
					$newBilling->actual_amount = number_format($value['actual'], 2, '.', '');
					$newBilling->gst = 'INPUT';
					$newBilling->accrual_amount = number_format($value['accrual'], 2, '.', '');
					$newBilling->save();
				}
			}
		}
	}

	public function checkAndFixAupost1()
	{
		$sql = 'select s.id as sid, s.ref as sref, json_value(s.meta, "$.actual_delivery_cost") as scost, sum(rl.value) as rlcost from shipment s join reconciliation_line rl on s.id = rl.shipment_id where parent_id in (select id from reconciliation where client_type = 1) and s.created >= "2019-07-01" group by s.id having scost != rlcost';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();

		foreach ($rs as $r) {
			$shipment = Shipment::model()->findByPk($r['sid']);
			if (empty($shipment)) continue;

			$shipment->mdata['actual_delivery_cost'] = $r['rlcost'];
			$shipment->updateMeta();
		}

		$sql = 'select id, (select consol_id from shipment where id = shipment_id) as new_consol_id from `reconciliation_line` where consol_id != (select consol_id from shipment where id = shipment_id)';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();

		foreach ($rs as $r) {
			$recline = ReconciliationLine::model()->find('id = :id', [':id' => $r['id']]);
			if (empty($recline)) continue;

			$recline->consol_id = $r['new_consol_id'];
			$recline->update('consol_id');
		}
	}


	public function checkAndFixAupost2()
	{
		$sql = 'select s.id as sid, s.ref as sref, json_value(s.meta, "$.actual_delivery_cost") as scost, rl.id as rlid, rl.invoice_no as consol, sum(rl.value) as rlcost, rl.my_value as rlaccr, if(json_value(s.meta,"$.import_billing_id_aupost"), json_value(s.meta,"$.import_billing_id_aupost"), json_value(s.meta,"$.import_billing_id")) as blid from shipment s join reconciliation_line rl on s.id = rl.shipment_id where parent_id in (select id from reconciliation where client_type = 1) and s.created >= "2019-07-01" and s.meta like "%import_billing_id%" group by s.ref';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();

		$bls = [];
		foreach ($rs as $r) {
			if (empty($bls[$r['blid']])) {
				$bls[$r['blid']] = ['accrual' => 0, 'actual' => 0, 'count' => 0];
			}
			$bls[$r['blid']]['accrual'] += $r['rlaccr'];
			$bls[$r['blid']]['actual'] += $r['rlcost'];
			$bls[$r['blid']]['count'] += 1;
		}

		foreach ($bls as $id => $value) {
			$line = BillingLine::model()->findByPk($id);
			if (empty($line)) echo $id . ' not found' . PHP_EOL;

			if (abs($line->actual_amount - $value['actual']) >= 0.1 || abs($line->accrual_amount - $value['accrual']) >= 0.1 || $line->desc != 'total shipment: ' . $value['count']) {

				$line->accrual_amount = $value['accrual'];
				$line->actual_amount = $value['actual'];
				$line->gst = 'INPUT';
				$line->gst_amount = $line->getGSTValue();
				$line->desc = 'total shipment: ' . $value['count'];
				$line->update('accrual_amount', 'actual_amount', 'gst', 'gst_amount', 'desc');

				echo 'fix ' . $line->id . PHP_EOL;
			}
		}
	}

	public function checkAndFixFastway2()
	{
		$sql = 'select s.id as sid, s.ref as sref, json_value(s.meta, "$.actual_delivery_cost") as scost, rl.id as rlid, rl.invoice_no as consol, sum(rl.value) as rlcost, rl.my_value as rlaccr, json_value(s.meta,"$.import_billing_id") as blid from shipment s join reconciliation_line rl on s.id = rl.shipment_id where parent_id in (select id from reconciliation where client_type = 0) and s.created >= "2019-01-01" and s.meta like "%import_billing_id%" group by s.ref';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();

		$bls = [];
		foreach ($rs as $r) {
			if (empty($bls[$r['blid']])) {
				$bls[$r['blid']] = ['accrual' => 0, 'actual' => 0, 'count' => 0];
			}
			$bls[$r['blid']]['accrual'] += $r['rlaccr'];
			$bls[$r['blid']]['actual'] += $r['rlcost'];
			$bls[$r['blid']]['count'] += 1;
		}

		foreach ($bls as $id => $value) {
			$line = BillingLine::model()->findByPk($id);
			if (empty($line)) {
				echo $id . ' not found' . PHP_EOL;
				continue;
			}

			if (($line->actual_amount - $value['actual'] >= 0.1 || $value['actual'] - $line->actual_amount >= 0.1 || $line->accrual_amount - $value['accrual'] >= 0.1 || $value['accrual'] - $line->accrual_amount >= 0.1)) {

				$line->accrual_amount = $value['accrual'];
				$line->actual_amount = $value['actual'];
				$line->gst = 'INPUT';
				$line->gst_amount = $line->getGSTValue();
				$line->desc = 'total shipment: ' . $value['count'];
				$line->update('accrual_amount', 'actual_amount', 'gst', 'gst_amount', 'desc');

				echo 'fix ' . $line->id . PHP_EOL;
			}

			if ($line->billing_id == 0) {
				Billing::linkLine($line);
			}
		}
	}

	public function getShopifyOrder()
	{
		$cust = WmsAPI::model()->find('org_id = :org_id', [':org_id' => $this->prompt('Org ID: ')]);
		$config = array(
			'store_domain' => $cust->domain,
			'api_key' => $cust->api_key,
			'api_secret' => $cust->api_secret,
			'org_id' => $cust->org_id,
		);
		$shopify = new ShopifyAPI($config);
		$shopify_order = $shopify->getOrder($this->prompt('Shopify ID: '));
		echo json_encode($shopify_order) . PHP_EOL;
	}

	public function test_return_manifest()
	{
		$apa = new AusPostAPI('test', true, true);
		$task = WmsTask::model()->findByPk($this->prompt('Task ID: '));
		$rs = [];
		foreach ($task->deliveryTask->mdata['return_shipment_id'] as $rsi) {
			$r = Shipment::model()->findByPk($rsi);
			$rs[] = $r;
		}
		$r = $apa->createOrderIncludingShipments($rs, 'test', AusPostAPI::CHARGE_CODE_POD);
	}

	public function haigou()
	{
		$out = new oExcel;
		$i = 1;

		$file = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'haigou.xlsx';
		$xls = new oExcel;
		$xls->load($file);
		$data = $xls->getAll();

		foreach ($data as $line) {
			$count = 0;

			$prod_barcode = $line[2];
			$prod_name = $line[3];
			$prod_po = $line[9];
			$prod_qty = $line[7];

			$prod = WmsProd::model()->find('(name = :name OR ean = :ean) AND status = 1', [':name' => $line[3], ':ean' => $line[2]]);
			if (empty($prod)) {
				$out->addRow($i++, [$line[2], $line[3], $line[7], $line[9]]);
				continue;
			}
			$pos = explode(',', $line[9]);

			$tasks = [];
			foreach ($pos as $po) {
				$ti = WmsTaskItem::model()->find('JSON_VALUE(meta, "$.gi") = :gi AND meta like :po AND del = 0', [':gi' => $prod->id, ':po' => '%' . $po . '%']);
				$tasks[] = $ti->task_id;
			}
			$tasks = array_unique($tasks);

			foreach ($tasks as $task) {
				$atis = WmsTaskItem::model()->with('task.mainTask')->findAll('mainTask.id = :id AND JSON_VALUE(t.meta, "$.gi") = :gi AND del = 0', [':id' => $task, ':gi' => $prod->id]);
				$uq = 0;
				$pls = [];
				foreach ($atis as $ati) {
					$uq += $ati->mdata['uq'];
					$pls[] = $ati->mdata['pl'];
				}
				$pls = array_unique($pls);
				$out_ids = [];
				foreach ($pls as $pl) {
					$location = WmsLocation::model()->find('name = :pl', [':pl' => $pl]);
					$outLedger = WmsStockLedger::model()->with('stock.prod')->find('location_id = :pli AND qty_out > 0 AND prod.id = :pid', [':pli' => $location->id, ':pid' => $prod->id]);
					if (empty($out_ids[$outLedger->taskItem->task->link_id])) {
						$out_ids[$outLedger->taskItem->task->link_id] = 0;
					}
					$out_ids[$outLedger->taskItem->task->link_id] += $outLedger->qty_out;
				}

				$out_tasks = [];
				foreach ($out_ids as $out_id => $qty) {
					$temp = WmsTask::model()->findByPk($out_id);
					$out_tasks[] = $temp->getNo() . '/' . @$temp->mdata['ctn_no'] . ' => ' . $qty;
				}

				$find_pos = WmsTaskItem::model()->findAll('JSON_VALUE(t.meta, "$.gi") = :gi AND task_id = :main', [':main' => $task, ':gi' => $prod->id]);
				$pos = [];
				foreach ($find_pos as $po) {
					$pos[] = $po->mdata['nt'];
				}

				if ($count == 0) {
					if (empty($uq)) {
						$task = '';
						$uq = '';
					}
					$out->addRow($i++, array_merge([$line[1], $line[2], $line[3], $line[4], $line[5], $line[6], $line[7], $line[8], $line[9], $line[10], $line[11]], ['', 'T'.$task, $uq, implode(',', array_unique($pos)), implode(', ', $out_tasks)]));
				} else {
					$out->addRow($i++, array_merge(['', '', '', '', '', '', '', '', '', '', ''], ['', 'T'.$task, $uq, implode(',', array_unique($pos)), implode(', ', $out_tasks)]));
				}

				$count++;
			}
		}

		$out->output('output.xlsx', null, false);
	}

	public function aupost()
	{
		$out = new oExcel;
		$i = 1;
		$out->addRow($i++, ['Reference', 'Cust', 'Dpmt', 'Actual Weight', 'Manifest Weight', 'Client Weight', 'Claim Pkg', 'Total Pkg', 'Charge (Actual)', 'Charge (Manifest)', 'Charge (Client)']);

		$file = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'AUPOST.xlsx';
		$xls = new oExcel;
		$xls->load($file);
		$data = $xls->getAll();
		unset($data[1]);
		unset($data[2]);

		foreach ($data as $line) {
			$ref = $line[1];
			$act = $line[3];
			$mani = $line[5];
			$client = $line[6];
			$pkg = $line[4];

			$p = ImParcel::model()->find('ref = :ref', [':ref' => $ref]);
			$orgRate = OrgRate::model()->find('type = 40 and org_id = :oid', [':oid' => $p->agent_id]);

			if ($p->pkg > $pkg) {
				$p->weight = $act / $pkg * $p->pkg;
				$act = $act . ' / ' . number_format($act / $pkg * $p->pkg, 2, '.', '');
			} else {
				$p->weight = $act;
			}
			$act_value = $p->getChargePro($p->agent, $orgRate, false);
			$p->weight = $mani;
			$mani_value = $p->getChargePro($p->agent, $orgRate, false);
			$p->weight = $client;
			$client_value = $p->getChargePro($p->agent, $orgRate, false);

			if ($act_value == 0 && $mani_value == 0 && $client_value == 0) {
				$dpmt = '3PL';
			} else {
				$dpmt = 'Import';
			}

			// if ($dpmt == '3PL') {
			// 	$quote = WmsOrgQuote::model()->find('org_id = :org_id and status = 1 AND (JSON_VALUE(meta, "$.whole_sale") IS NULL OR JSON_VALUE(meta, "$.whole_sale") = 0)', [':org_id' => $p->agent_id]);
			// 	if (empty($quote) && $p->agent->by != 1) {
			// 		$quote = WmsOrgQuote::model()->find('org_id = :org_id and status = 1', [':org_id' => $p->agent->by]);
			// 	}
			// 	if (!empty($quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE])) {
			// 		$cc = ImportChargeCode::model()->find('chargecode = :chargecode AND status = 1', [':chargecode' => $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE]]);
			// 		$chargecode = $cc->chargecode;
			// 		$task = new WmsTask;
			// 		$act_value = $task->getChargeByChargecode($act, $p->postcode, $chargecode, true);
			// 		$mani_value = $task->getChargeByChargecode($mani, $p->postcode, $chargecode, true);
			// 		$client_value = $task->getChargeByChargecode($client, $p->postcode, $chargecode, true);
			// 	}
			// }

			$out->addRow($i++, [$ref, $p->agent->name, $dpmt, $act, $mani, $client, $pkg, $p->pkg, $act_value, $mani_value, $client_value]);
		}

		$out->output('aupost_output.xlsx', false, null);
	}

	public function aupost2()
	{
		$from = $this->prompt('From: ');
		$to = $this->prompt('To: ');
		echo $from . ' ~ ' . $to . PHP_EOL;
		$shipments = [];
		$rls = ReconciliationLine::model()->with('parent', 'shipment')->findAll('parent.client_type = 1 AND shipment.created >= :from AND shipment.created <= :to', [':from' => $from, ':to' => $to]);
		foreach ($rls as $rl) {
			if (empty($shipments[$rl->shipment_id])) {
				$shipments[$rl->shipment_id] = ['act' => 0, 'mani' => $rl->shipment->mdata['manifest_weight'], 'client' => $rl->shipment->weight, 'pkg' => 0];
			}
			$shipments[$rl->shipment_id]['act'] += $rl->cdeadwt;
			$shipments[$rl->shipment_id]['pkg'] += 1;
		}

		$out = new oExcel;
		$i = 1;
		$out->addRow($i++, ['Reference', 'Dpmt', 'Actual Weight', 'Manifest Weight', 'Client Weight', 'Pkg', 'Total Pkg', 'Charge (Actual)', 'Charge (Manifest)', 'Charge (Client)']);
		$rates = [];
		foreach ($shipments as $id => $shipment) {
			$p = ImParcel::model()->findByPk($id);
			if (!empty($p->consol) && preg_match('/3PL/', $p->consol->no)) {
				continue;
			}

			$ref = $p->ref;
			$act = $shipment['act'];
			$mani = $shipment['mani'];
			$client = $shipment['client'];
			$pkg = $shipment['pkg'];
			echo $p->ref . ' ' . $act . ' ' . $mani . ' ' . $client . PHP_EOL;

			if (!empty($rates[$p->agent_id])) {
				$orgRate = $rates[$p->agent_id];
			} else {
				$orgRate = OrgRate::model()->find('type = 40 and org_id = :oid', [':oid' => $p->agent_id]);
				$rates[$p->agent_id] = $orgRate;
			}

			if ($p->pkg > $pkg) {
				$p->weight = $act / $pkg * $p->pkg;
				$act = $act . ' / ' . number_format($act / $pkg * $p->pkg, 2, '.', '');
			} else {
				$p->weight = $act;
			}
			$act_value = $p->getChargePro($p->agent, $orgRate, false);
			$p->weight = $mani;
			$mani_value = $p->getChargePro($p->agent, $orgRate, false);
			$p->weight = $client;
			$client_value = $p->getChargePro($p->agent, $orgRate, false);

			$out->addRow($i++, [$ref, $dpmt, $act, $mani, $client, $pkg, $p->pkg, $act_value, $mani_value, $client_value, $p->created]);
		}

		$out->output($from . $to . '.xlsx');
	}

	public function test()
	{
		$p = Shipment::model()->find('ref = :ref', [':ref' => '333UF0175206']);
		$orgRate = OrgRate::model()->find('type = 40 and org_id = :oid', [':oid' => $p->agent_id]);
		echo $p->getChargePro($p->agent, $orgRate, false);
	}

	public function aupost_manifest()
	{
		$apa = new AusPostAPI('syd');
		$s = ImParcel::model()->find('ref = :ref', [':ref' => $this->prompt('Ref: ')]);
		$ss = [$s];
		$r = $apa->createOrderIncludingShipments($ss, time(), AusPostAPI::CHARGE_CODE_POD);

		if (!empty($r->order)) {
			$oid = $r->order->order_id;
			// because aupost returned shipment is not the same order with our sending order
			// so here we need to order by HBN again
			$auPostShipments = [];
			foreach ($r->order->shipments as $aushipment) {
				$auPostShipments[$shipment_created? $aushipment->shipment_id : $aushipment->shipment_reference] = $aushipment;
			}

			foreach ($ss as $i => $s) {
				$ts = new Tranship;
				$ts->pid = $s->id;
				$ts->org_id = 101;  // for Australia post office
				$ts->man_id = $s->man_id;
				$ts->type = 80;  // shipment transfer to a different delivery courier
				$ts->status = 19; // in finally moving status
				$ts->connote = $s->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->mdata['oid'] = $oid;
				$aushipment = $auPostShipments[$s->hbn];
				$ts->mdata['sid'] = $aushipment->shipment_id;

				$costValue = floatval($aushipment->shipment_summary->total_cost - $aushipment->shipment_summary->total_gst);
				$ts->cost = round($costValue, 2);
				$ts->save();
				if (!empty($s->mdata['direct_courier'])&&$s->mdata['direct_courier']==2) {
					$s->cbwf=($s->cbwf^32)&$s->cbwf;
					$s->cbwf=$s->cbwf|64;
					$s->mdata['direct_courier']=1;
					$s->updateMeta();
					$s->update(['cbwf']);
				}
			}
		}

		echo json_encode($r);
	}

	public function batch_aupost_manifest()
	{
		$apa = new AusPostAPI('syd');
		$ss = ImParcel::model()->findAll('ref IN ("AMQ5420214","AMQ5420542","AMQ5418342","AMQ5419610","AMQ5419825","AMQ5419409","AMQ5419830","AMQ5418671","AMQ5420204","AMQ5418117","AMQ5418922","AMQ5419182","AMQ5418149","AMQ5418172","AMQ5418140","AMQ5418137","AMQ5418129","AMQ5418127","AMQ5418133","AMQ5418134","AMQ5419209","AMQ5418135","AMQ5418148","AMQ5419205","AMQ5418179","AMQ5415353","AMQ5419287","AMQ5415351","AMQ5419845","AMQ5419849","AMQ5418082","AMQ5419208","AMQ5418132","AMQ5419199","AMQ5419211","AMQ5419177","AMQ5419203","AMQ5419210","AMQ5420461","AMQ5418142","AMQ5419254","AMQ5419194","AMQ5419533","AMQ5417656","AMQ5417600","AMQ5418942","AMQ5417596","AMQ5419190","AMQ5419201","AMQ5419204","AMQ5419489","AMQ5419786","AMQ5419198","AMQ5419582","AMQ5419187","AMQ5416339","AMQ5418954","AMQ5419502","AMQ5417840","AMQ5419945","AMQ5420217","AMQ5419207","AMQ5419188","AMQ5418041","AMQ5419450","AMQ5419163","AMQ5418950","AMQ5420218","AMQ5419202","AMQ5419427","AMQ5419186","AMQ5419196","AMQ5419197","AMQ5419218","AMQ5420222","AMQ5417665","AMQ5420213","AMQ5417666","AMQ5418131","AMQ5418128","AMQ5418146","AMQ5415393","AMQ5415349","AMQ5419180","AMQ5419181","AMQ5419214","AMQ5419189","AMQ5419184","AMQ5418077","AMQ5420224","AMQ5420319","AMQ5420690","AMQ5417838","AMQ5419195","AMQ5417847","AMQ5418930","AMQ5418955","AMQ5418957","AMQ5419213","AMQ5419175","AMQ5419192","AMQ5419200","AMQ5419176","AMQ5419174","AMQ5419206","AMQ5418962","AMQ5420230","AMQ5420688","AMQ5418071","AMQ5419504","AMQ5420874","AMQ5420716","AMQ5420929","AMQ5418147","AMQ5418139","AMQ5415434","AMQ5420317","AMQ5420274","AMQ5419295","AMQ5417662","AMQ5417827","AMQ5415708","AMQ5420904","AMQ5418951","AMQ5418927","AMQ5419164","AMQ5415706","AMQ5418932","AMQ5417649","AMQ5418940","AMQ5419909","AMQ5418948","AMQ5419183","AMQ5419191","AMQ5419289","AMQ5419178","AMQ5419193","AMQ5419575","AMQ5419154","AMQ5421309","AMQ5421281","AMQ5421140","AMQ5421119","AMQ5421107","AMQ5421098","AMQ5421094","AMQ5421054","AMQ5420442","AMQ5420294","AMQ5419640","AMQ5419639","AMQ5419626","AMQ5419624","AMQ5419229","AMQ5419227","AMQ5419136","AMQ5419135","AMQ5419130","AMQ5418733","AMQ5418702","AMQ5418652","AMQ5418649","AMQ5418647","AMQ5418623","AMQ5418612","AMQ5418581","AMQ5418553","AMQ5418514","AMQ5418513","AMQ5418483","AMQ5418482","AMQ5418476","AMQ5418458","AMQ5418442","AMQ5418417","AMQ5418400","AMQ5418397","AMQ5418395","AMQ5418393","AMQ5418385","AMQ5418382","AMQ5418374","AMQ5418373","AMQ5418372","AMQ5418371","AMQ5418369","AMQ5418360","AMQ5418359","AMQ5418356","AMQ5418351","AMQ5418349","AMQ5418347","AMQ5418346","AMQ5418345","AMQ5418340","AMQ5418339","AMQ5418338","AMQ5418334","AMQ5418314","AMQ5418312","AMQ5418300","AMQ5418299","AMQ5418298","AMQ5418277","AMQ5418269","AMQ5418262","AMQ5418258","AMQ5418252","AMQ5418200","AMQ5418181","AMQ5418159","AMQ5418153","AMQ5418152","AMQ5418118","AMQ5418084","AMQ5418047","AMQ5417792","AMQ5417709","AMQ5417695","AMQ5417683","AMQ5417678","AMQ5417592","AMQ5417591","AMQ5417590","AMQ5417036","AMQ5417024","AMQ5417019","AMQ5417018","AMQ5417014","AMQ5417012","AMQ5417007","AMQ5416992","AMQ5416987","AMQ5416963","AMQ5416910","AMQ5416907","AMQ5416905","AMQ5416904","AMQ5416903","AMQ5416902","AMQ5416898","AMQ5416897","AMQ5416896","AMQ5416895","AMQ5416894","AMQ5416893","AMQ5416892","AMQ5416890","AMQ5416889","AMQ5416887","AMQ5416885","AMQ5416881","AMQ5416880","AMQ5416878","AMQ5416877","AMQ5416876","AMQ5416865","AMQ5416783","AMQ5416334","AMQ5416310","AMQ5416256","AMQ5416254","AMQ5416158","AMQ5416153","AMQ5416137","AMQ5416136","AMQ5416133","AMQ5416105","AMQ5416091","AMQ5416051","AMQ5416044","AMQ5416036","AMQ5416017","AMQ5416015","AMQ5415998","AMQ5415995","AMQ5415990","AMQ5415956","AMQ5415950","AMQ5415946","AMQ5415911","AMQ5415842","AMQ5415841","AMQ5415833","AMQ5415829","AMQ5415820","AMQ5415806","AMQ5415802","AMQ5415799","AMQ5415796","AMQ5415777","AMQ5415752","AMQ5415732","AMQ5415665","AMQ5415654","AMQ5415624","AMQ5415580","AMQ5415529","AMQ5415527","AMQ5415487","AMQ5414998","AMQ5414997","AMQ5414995","AMQ5414777","AMQ5414776","AMQ5414775","AMQ5414774","AMQ5414773","AMQ5414772","AMQ5414756","AMQ5414749","AMQ5414184","AMQ5414105","AMQ5414095","AMQ5413908","AMQ5413409","AMQ5413394","AMQ5413334","AMQ5413255","AMQ5413248","AMQ5413201","AMQ5413141","AMQ5413137","AMQ5413120","AMQ5413114","AMQ5412915","AMQ5412911","AMQ5412907","AMQ5412676","AMQ5412654","AMQ5412039","AMQ5411942","AMQ5411216","AMQ5411210","AMQ5410721","AMQ5410427","AMQ5410426","AMQ5409323","33EVH0009135","AMQ5434761")');
		$r = $apa->createOrderIncludingShipments($ss, time(), AusPostAPI::CHARGE_CODE_POD);

		if (!empty($r->order)) {
			$oid = $r->order->order_id;
			// because aupost returned shipment is not the same order with our sending order
			// so here we need to order by HBN again
			$auPostShipments = [];
			foreach ($r->order->shipments as $aushipment) {
				$auPostShipments[$shipment_created? $aushipment->shipment_id : $aushipment->shipment_reference] = $aushipment;
			}

			foreach ($ss as $i => $s) {
				$ts = new Tranship;
				$ts->pid = $s->id;
				$ts->org_id = 101;  // for Australia post office
				$ts->man_id = $s->man_id;
				$ts->type = 80;  // shipment transfer to a different delivery courier
				$ts->status = 19; // in finally moving status
				$ts->connote = $s->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->mdata['oid'] = $oid;
				$aushipment = $auPostShipments[$s->hbn];
				$ts->mdata['sid'] = $aushipment->shipment_id;

				$costValue = floatval($aushipment->shipment_summary->total_cost - $aushipment->shipment_summary->total_gst);
				$ts->cost = round($costValue, 2);
				$ts->save();
				if (!empty($s->mdata['direct_courier'])&&$s->mdata['direct_courier']==2) {
					$s->cbwf=($s->cbwf^32)&$s->cbwf;
					$s->cbwf=$s->cbwf|64;
					$s->mdata['direct_courier']=1;
					$s->updateMeta();
					$s->update(['cbwf']);
				}
			}
		}

		echo json_encode($r);
	}

	public function rts_set()
	{
		$tasks = WmsTask::model()->findAll('bwf & 128 > 0');
		foreach ($tasks as $task) {
			if (!empty($task->mdata['return_option_task'])) {
				$rot = WmsTask::model()->findByPk($task->mdata['return_option_task']);
				if ($rot->status >= 99) {
					$task->mdata['return_status'] = 99;
				} else {
					$task->mdata['return_status'] = 30;
				}
			} else if (!empty($task->mdata['return_check_task'])) {
				$roc = WmsTask::model()->findByPk($task->mdata['return_check_task']);
				if ($roc->status >= 99) {
					$task->mdata['return_status'] = 99;
				} else {
					$task->mdata['return_status'] = 30;
				}
			} else {
				$task->mdata['return_status'] = 20;
			}
			$task->updateMeta();
		}
	}

	public function syncShopifyByRef()
	{
		$custs = WmsAPI::model()->findAll('type = :type AND status = :status AND org_id = :org_id', [':type' => WmsAPI::WMS_API_TYPE_SHOPIFY, ':status' => 1, ':org_id' => $this->prompt('ORG ID: ')]);
		foreach ($custs as $cust) {
			$config = array(
				'store_domain' => $cust->domain,
				'api_key' => $cust->api_key,
				'api_secret' => $cust->api_secret,
				'org_id' => $cust->org_id,
			);
			$shopify = new ShopifyAPI($config);
			$this->_syncShopifyOrder($shopify);
		}
	}

	public function _syncShopifyOrder($shopify)
	{
		$r = $shopify->countOrders('open');

		$transaction = Yii::app()->db->beginTransaction();
		try {
			if ($r['done'] == true) {
				while ($r['count'] > 0) {
					$page = ceil($r['count'] / 250);
					$r['count'] -= 250;

					// Get orders
					$orders = $shopify->getOrders($page, 'open', 'any');
					if ($orders['done'] == true) {
						$this->_processShopifyOrder($orders, $shopify);
					}
				}
				$transaction->commit();
			}
		} catch (Exception $ex) {
			$transaction->rollback();
			$this->log('Sync Shopify order fail');
			throw $ex;
		}
	}

	public function _processShopifyOrder($orders, $shopify)
	{
		$ref = $this->prompt('Ref: ');

		$errors = [];
		$orders['orders'] = array_reverse($orders['orders']);
		foreach ($orders['orders'] as $order) {
			if ($order['id'] <= 971160191076) { // elekzon #1042
				continue;
			}

			if (!preg_match('/' . $ref . '/', $order['name'])) continue;

			if ($order['fulfillment_status'] == 'fulfilled') {
				continue;
			}

			$task_map = WmsTaskMap::model()->find('fid = :fid AND platform = "shopify"', [':fid' => $order['id']]);
			if (empty($task_map)) {
				// Get job
				$job = WmsJob::model()->find(['condition' => 'org_id = :org_id AND type = 30 AND week(`created`, 1) = :week', 'params' => [':org_id' => $shopify->org_id, ':week' => intval(date('W'))], 'order' => 'id desc']);
				if (empty($job)) {
					$job = new WmsJob;
					$job->org_id = $shopify->org_id;
					$job->type = 30;
					$job->status = 10;
					$job->ref = '3PL_' . date('Y-m-d');
					$job->save();
				}
				if ($job->getErrors()) {
					foreach ($job->getErrors() as $error) {
						$this->shopifyLog($error[0]);
						$errors[] = $error[0];
					}
				}

				// Create pick&pack task
				$task_errors = [];
				$task = new WmsTask;
				$task->job_id = $job->id;
				$task->type = 3030;
				$task->is_request = 1;
				$task->op_id = 0;
				$task->status = 20;
				if ($order['financial_status'] == 'pending') {
					$task->status = 10;
				}
				$task->ref = $order['name'] . ' ' . @$order['shipping_address']['first_name'] . ' ' . @$order['shipping_address']['last_name'] . ' ' . @$order['shipping_address']['zip'];
				$prods = [];
				$backorder = false;
				$not_exist = [];
				foreach ($order['line_items'] as $item) {
					$prod = null;
					// Get prod
					$prod_map = WmsProdMap::model()->with('prod')->find('t.fid = :fid AND t.platform = "shopify" AND prod.status = 1', [':fid' => $item['variant_id']]);
					if (!empty($prod_map)) {
						$prod = $prod_map->prod;
					} else if (!empty($item['barcode'])) {
						$prod = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $item['barcode']]);
					} else if (!empty($item['sku'])) {
						$prod = WmsProd::model()->with('orgs')->find('orgs.sku = :sku AND orgs.org_id = :org_id AND status = 1', [':sku' => $item['sku'], ':org_id' => $shopify->org_id]);
						// 2020-04-22
						if (empty($prod)) {
							$prod = WmsProd::model()->with('orgs')->find('orgs.sku = :sku AND orgs.org_id = :org_id AND status = 1', [':sku' => str_replace('_', ' ', $item['sku']), ':org_id' => $shopify->org_id]);
						}
					}

					// 2020-06-22
					// if ($prod->ean == 'LIP004') {
					// 	$prod = WmsProd::model()->find('ean = "LIP004X2"');
					// }

					// 2020-04-22
					if (empty($prod) && !empty($item['barcode'])) {
						$pack = WmsProdPack::model()->with('prod')->find('barcode = :barcode AND prod.status = 1', [':barcode' => $item['barcode']]);
						if (!empty($pack)) {
							$prod = $pack->prod;
							$item['quantity'] *= $pack->qty;
						}
					}
					if (empty($prod) && !empty($item['sku'])) {
						$pack = WmsProdPack::model()->with('prod')->find('barcode = :barcode AND prod.status = 1', [':barcode' => $item['sku']]);
						if (!empty($pack)) {
							$prod = $pack->prod;
							$item['quantity'] *= $pack->qty;
						}
					}

					if (empty($prod)) {
						$task_errors[] = $item['name'] . ' ' . $item['variant_id'] . ' does not exist in PCAE';
						if (!isset($not_exist[$item['name']])) $not_exist[$item['name']] = 0;
						$not_exist[$item['name']] += $item['quantity'];
						continue;
					}

					// special product filter
					if (in_array($item['name'], ['FREE POSTAGE'])) {
						continue;
					}

					// if (empty($prods[$prod->id])) $prods[$prod->id] = 0;
					// $prods[$prod->id] += $item['quantity'];
					// accept pack/kit sku
					if (empty($prods[$prod->id])) $prods[$prod->id] = 0;
					if ($prod->type != WmsProd::WMS_PROD_KIT) {
						$prods[$prod->id] += $item['quantity'];
					} else if ($prod->type == WmsProd::WMS_PROD_KIT) {
						// don't record kit, cause backorder
						// $prods[$prod->id] += $item['quantity'];
						foreach ($prod->items as $prod_item) {
							if (!isset($prods[$prod_item->item->id])) $prods[$prod_item->item->id] = 0;
							$prods[$prod_item->item->id] += intval($item['quantity']) * $prod_item->qty;
						}
					}
				}
				$items = [];
				foreach ($prods as $prod_id => $qty) {
					$temp_qty = $qty;
					// Get stocks
					$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id AND qty - qty_res > 0', [':prod_id' => $prod_id, ':org_id' => $shopify->org_id]);
					foreach ($stocks as $stock) {
						if ($qty > 0) {
							$items[] = [
								'si' => $stock->id,
								'sn' => $stock->prod->name,
								'pq' => '',
								'cq' => '',
								'uq' => min($qty, $stock->availQty()),
								'pli' => '',
								'pl' => '',
								'nt' => '',
							];
							$qty -= min($qty, $stock->availQty());
						} else {
							break;
						}
					}

					$prod = WmsProd::model()->findByPk($prod_id);
					if ($qty > 0) {
						$task_errors[] = $prod->name . ' in ' . $order['name'] . ' quantity is less than ' . $temp_qty . ', only ' . (intval($temp_qty) - intval($qty)) . '.';

						// 2020-06-22 still record item
						$stock = WmsStock::model()->find('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $prod_id, ':org_id' => $shopify->org_id]);
						$items[] = [
							'si' => $stock->id,
							'sn' => $stock->prod->name,
							'pq' => '',
							'cq' => '',
							'uq' => $qty,
							'pli' => '',
							'pl' => '',
							'nt' => '',
						];
						$backorder = true;
					}

					if (!empty($not_exist)) {
						foreach ($not_exist as $name => $qty) {
							$items[] = [
								'si' => '',
								'sn' => $name,
								'pq' => '',
								'cq' => '',
								'uq' => $qty,
								'pli' => '',
								'pl' => '',
								'nt' => '',
							];
						}
					}
				}

				if ($task->getErrors()) {
					foreach ($task->getErrors() as $error) {
						$this->shopifyLog($error[0]);
						$errors[] = $error[0];
					}
				} else {
					if (!empty($task_errors)) {
						foreach ($task_errors as $error) {
							$this->shopifyLog($error);
							$errors[] = $error;
						}
						if ($backorder) {
							$task->status = 10;
						} else {
							$task->status = 40;
						}
					}
					$task->mdata['note'] = implode('; ', $task_errors);
					$task->new_items = $items;
					$task->save();

					// Delivery
					$task->pickupTask->type = 2120;
					$delivery_task = $task->pickupTask;
					$delivery_task->mdata['cnee']['company'] = @$order['shipping_address']['company'];
					$delivery_task->mdata['cnee']['name'] = @$order['shipping_address']['first_name'] . ' ' . @$order['shipping_address']['last_name'];
					$delivery_task->mdata['cnee']['tel'] = @$order['shipping_address']['phone'];
					$delivery_task->mdata['cnee']['address'] = @$order['shipping_address']['address1'] . ' ' . @$order['shipping_address']['address2'];
					$delivery_task->mdata['cnee']['city'] = @$order['shipping_address']['city'];
					$delivery_task->mdata['cnee']['suburb'] = @$order['shipping_address']['city'];
					$delivery_task->mdata['cnee']['state'] = Addr::checkAuState(@$order['shipping_address']['province']);
					$delivery_task->mdata['cnee']['postcode'] = @$order['shipping_address']['zip'];
					$delivery_task->mdata['cnee']['country'] = @$order['shipping_address']['country_code'];
					$delivery_task->mdata['cnee']['email'] = $order['email'];
					// elekzon default tnt
					if ($shopify->org_id == Org::ORGID_3PL_ELEKZON) {
						// $delivery_task->mdata['courier'] = Org::ORGID_COURIER_TNT;
						if (!empty($order['shipping_lines']) && preg_match('/Standard Shipping|Australia Free Shipping/i', $order['shipping_lines'][0]['code'])) {
							$delivery_task->mdata['shopify_standard'] = true;
						}
					}
					$delivery_task->save();

					// Create task map
					$task_map = new WmsTaskMap;
					$task_map->fid = $order['id'];
					$task_map->platform = 'shopify';
					$task_map->task_id = $task->id;
					$task_map->save();
				}
			} else {
				if (!empty($order['cancelled_at']) || (!empty($order['financial_status']) && $order['financial_status'] == 'voided')) {
					$task_map->task->status = 100;
					$task_map->task->save();
				}
			}
		}

		if ($shopify->org_id == Org::ORGID_3PL_ELEKZON && !empty($errors)) {
			$emailLog = new Emailog();
			$emailLog->type = Emailog::SHOPIFY_SYNC_REPORT;
			$emailLog->fid = 0;
			$emailLog->dt = date('Y-m-d H:i:s');
			$emailLog->to_id = $shopify->org_id;
			$emailLog->status = 10;
			$emailLog->prepTemplate();
			$emailLog->tpl->assignThese([
				'DETAIL' => implode('<br />', $errors),
			]);
			$emailLog->subject = $emailLog->tpl->subject;
			$emailLog->body = $emailLog->tpl->getContent();
			$emailLog->mdata['to'] = 'gero@toplogistics.com.au';
			if (!empty($emailLog->to_id)) {
				$org = Org::model()->findByPk($emailLog->to_id);
				if (!empty($org->email)) {
					$emailLog->mdata['to'] .= ';' . $org->email;
				}
				if ($emailLog->to_id == Org::ORGID_3PL_ELEKZON) {
					$emailLog->mdata['cc'] = 'jason@elekzon.com.au';
				}
			}
			$emailLog->sendShopifySyncReport();
		}
	}

	public function shopifyLog($m)
	{
		$lf = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'shopify' . DIRECTORY_SEPARATOR;
		$lf .= 'shopify_' . date('Y-m-d H') . '.log';
		return file_put_contents($lf, date('Y-m-d H:i:s') . ' ' . $m . "\n", FILE_APPEND);
	}

	public function completeTask()
	{
		$tasks = WmsTask::model()->findAll('job_id in (select id from wms_job where org_id in (886,888,889)) AND status = 30');
		echo count($tasks) . PHP_EOL;
		foreach ($tasks as $task) {
			$task->status = 99;
			$task->update('status');
		}
	}

	public function small_location_label()
	{
		$lines = ['AA', 'AB'];
		foreach ($lines as $line) {
			for ($f = 1; $f <= 3; $f++) {
				for ($c = 1; $c <= 5; $c++) {
					for ($d = 1; $d <= 8; $d++) {
						$storage = WmsLocation::model()->find('name = :name', array(':name' => $line . sprintf('%02d', $f) . '-' . $c . '-' . $d));
						if (empty($storage)) {
							$storage = new WmsLocation;
							$storage->name = $line . sprintf('%02d', $f) . '-' . $c . '-' . $d;
							$storage->code = $storage->name;
						}
						$storage->type = 30;
						$storage->wid = 106;
						$storage->status = 1;
						$storage->save();
					}
				}
			}
		}
	}

	public function getXeroBillingsWithLines($from = '', $to = '')
	{
		if (empty($from)) $from = '2019-07-01';
		if (empty($to)) $to = date('Y-m-d');
		$xero = new XeroAPI;
		$i = 1;
		$results = [];
		while (true) {
			$temp = $xero->get('Accounting\Invoice', ['Type' => 'ACCPAY', 'FromDate' => $from . ' 00:00:00', 'ToDate' => $to . ' 23:59:59'], $i++);
			if (count($temp) == 0) break;
			$results = array_merge($results, array_values((array)($temp)));
		}

		return $results;
	}

	public function alsisLabel()
	{
		$tasks = WmsTask::model()->with('job')->findAll('t.status IN (10,20) AND job.org_id = 2872 AND t.is_request = 1 AND t.type = 3030');
		$count = 0;
		echo count($tasks) . PHP_EOL;
		foreach ($tasks as $task) {
			if (sizeof($task->items) != 1) {
				echo $task->getNo() . PHP_EOL;
				continue;
			}
			foreach ($task->items as $item) {
				if ($item->mdata['sn'] != 'LIMITED LAUNCH OFFER! LIPMD® + FREE Serum Gift Results guaranteed! Default Title' || !preg_match('/is less than 1/i', $task->mdata['note'])) {
					echo $task->getNo() . PHP_EOL;
					continue 2;
				}
				$count ++;
			}

			$task->status = 20;
			$task->save();

			foreach ($task->items as $item) {
				$item->mdata['uq'] = 1;
				$item->save();
				$item->toStock();
			}
		}

		echo $count . PHP_EOL;
	}

	public function alsisLabel2()
	{
		$tasks = WmsTask::model()->with('mainTask', 'job')->findAll('mainTask.status IN (10,20,40) AND job.org_id = 2872 AND mainTask.is_request = 1 AND t.type = 2120');
		foreach ($tasks as $task) {
			if (empty($task->mdata['cnee']['tel']) && !empty($task->job->customer->extra['3pl_cnee_tel'])) {
				$task->mdata['cnee']['tel'] = $task->job->customer->extra['3pl_cnee_tel'];
				$task->save();
			}
			if (!preg_match('/address is incomplete/i', $task->mainTask->mdata['note']) && !preg_match('/address is incorrect/i', $task->mainTask->mdata['note'])) {
				$task->mainTask->status = 20;
				$task->mainTask->save();
			}
		}
	}

	public function alsisLabel3()
	{
		$tasks = WmsTask::model()->with('job')->findAll('t.status IN (10,20,40) AND job.org_id = 2872 AND t.is_request = 1 AND t.type = 3030');
		foreach ($tasks as $task) {
			if (!empty($task->deliveryTask) && (!empty($task->deliveryTask->mdata['shipment_id']) || !empty($task->deliveryTask->mdata['shipment_courier_id']) || !empty($task->deliveryTask->mdata['courier']))) {
				unset($task->deliveryTask->mdata['shipment_id']);
				unset($task->deliveryTask->mdata['shipment_courier_id']);
				unset($task->deliveryTask->mdata['courier']);
				$task->deliveryTask->save();
			}
		}
	}

	public function alsislabel4()
	{
		$query = $this->prompt('Query: ');
		$qty = $this->prompt('Qty: ');
		$tasks = WmsTask::model()->findAll($query);
		echo count($tasks) . PHP_EOL;
		if ($this->prompt('Process ? Y/N') == 'Y') {
			foreach ($tasks as $task) {
				$ac_item = WmsTaskItem::model()->find('task_id = :task_id AND del = 0', [':task_id' => $task->actionTask->id]);
				if (empty($ac_item)) {
					$stock = WmsStock::model()->findByPk(42246);
					$loc = $stock->locs[0];
					if (!empty($stock) && !empty($loc)) {
						$ac_item = new WmsTaskItem;
						$ac_item->task_id = $task->actionTask->id;
						$ac_item->mdata = array(
							'si' => $stock->id,
							'sn' => $stock->prod->name,
							'uq' => $qty,
							'pli' => $loc->loc->id,
							'pl' => $loc->loc->code,
						);
						$ac_item->save();
					}
				}
				$task->status = 99;
				$task->save();
			}
		}
	}

	public function alsislabel5()
	{
		$query = $this->prompt('Query: ');
		$tasks = WmsTask::model()->findAll($query);
		echo count($tasks) . PHP_EOL;
		if ($this->prompt('Process ? Y/N') == 'Y') {
			foreach ($tasks as $task) {
				$ac_item = WmsTaskItem::model()->find('task_id = :task_id AND del = 0', [':task_id' => $task->actionTask->id]);
				if (empty($ac_item)) {
					$stock = WmsStock::model()->findByPk(21880);
					$loc = $stock->locs[0];
					if (!empty($stock) && !empty($loc)) {
						$ac_item = new WmsTaskItem;
						$ac_item->task_id = $task->actionTask->id;
						$ac_item->mdata = array(
							'si' => $stock->id,
							'sn' => $stock->prod->name,
							'uq' => 1,
							'pli' => $loc->loc->id,
							'pl' => $loc->loc->code,
						);
						$ac_item->save();
					}
				}
				$task->status = 99;
				$task->save();
			}
		}
	}

	private function testStockIn($stock, $date)
	{
		$stock->refresh();

		$loc = new WmsLocation;
		$loc->pid = 1;
		$loc->wid = 106;
		$loc->type = 50;
		$loc->status = 1;
		$loc->pid = 0;
		$loc->save();
		$loc->name = 'testHSY' . (WmsLocation::model()->count('name LIKE "testHSY%"') + 1);
		$loc->code = 'testHSY' . (WmsLocation::model()->count('name LIKE "testHSY%"') + 1);
		$loc->save();
		if ($loc->getErrors()) {
			echo json_encode($loc->getErrors()) . PHP_EOL;
		}

		$ledger = new WmsStockLedger;
		$ledger->ti_id = 1;
		$ledger->stock_id = $stock->id;
		$ledger->location_id = $loc->id;
		$ledger->ts = date('Y-m-d 00:00:01', strtotime($date . ' - ' . rand(1, 90) . ' days'));
		$ledger->qty_in = 1;
		$ledger->save();
		if ($ledger->getErrors()) {
			echo json_encode($ledger->getErrors()) . PHP_EOL;
		}

		$sl = new WmsStockLocation;
		$sl->stock_id = $stock->id;
		$sl->location_id = $loc->id;
		$sl->qty = 1;
		$sl->save();
		if ($sl->getErrors()) {
			echo json_encode($sl->getErrors()) . PHP_EOL;
		}
	}

	private function testStockOut($stock, $date)
	{
		$stock->refresh();

		for ($i = 0; $i < count($stock->locs); $i++) {
			$inLedger = WmsStockLedger::model()->find('stock_id = :stock_id AND location_id = :location_id AND qty_in > 0', [':stock_id' => $stock->id, ':location_id' => $stock->locs[$i]->location_id]);

			$gap = rand(0, floor((strtotime($date) - strtotime($inLedger->ts)) / 86400));

			$ledger = WmsStockLedger::model()->find('stock_id = :stock_id AND location_id = :location_id AND qty_out > 0', [':stock_id' => $stock->id, ':location_id' => $stock->locs[$i]->location_id]);
			if (empty($ledger)) {
				$ledger = new WmsStockLedger;
				$ledger->ti_id = 1;
				$ledger->stock_id = $stock->id;
				$ledger->location_id = $stock->locs[$i]->location_id;
				$ledger->ts = date('Y-m-d 23:59:59', strtotime($inLedger->ts . ' + ' . $gap . ' days'));
				$ledger->qty_out = 1;
				$ledger->save();
			}
			if ($ledger->getErrors()) {
				echo json_encode($ledger->getErrors()) . PHP_EOL;
			}
		}
	}

	public function exportLedger($stock, $date)
	{
		echo '  LOC ID |       IN DATE       |       OUT DATE      |      STAY' . PHP_EOL;
		$ledgers = WmsStockLedger::model()->findAll(['condition' => 'stock_id = :stock_id', 'params' => [':stock_id' => $stock->id], 'order' => 'ts']);
		$locs = [];
		foreach ($ledgers as $ledger) {
			$locs[$ledger->location_id][$ledger->qty_in > 0 ? 'in' : 'out'] = $ledger->ts;
		}

		foreach ($locs as $id => $trans) {
			if (!empty($trans['out'])) {
				$out = $trans['out'];
			} else {
				$out = date('Y-m-d 23:59:59', strtotime($date));
			}
			$stay = (strtotime($out) - strtotime($trans['in'])) / 86400;
			echo '  ' . $id . ' | ' . $trans['in'] . ' | ' . $out . ' |       ' . $stay . PHP_EOL;
		}
		echo PHP_EOL;
	}

	public function exportTest($stock, $free_tests, $charge_date, $function)
	{
		echo '        ';
		$date = '2020-02-22';
		while (strtotime($date) <= strtotime($charge_date)) {
			echo $date . ' | ';
			$date = date('Y-m-d', strtotime($date . ' + 7 days'));
		}
		echo PHP_EOL;

		echo '        ';
		$date = '2020-02-28';
		while (strtotime($date) <= strtotime($charge_date)) {
			echo $date . ' | ';
			$date = date('Y-m-d', strtotime($date . ' + 7 days'));
		}
		echo PHP_EOL;

		$date = '2020-02-28';
		foreach ($free_tests as $free_day) {
			echo str_pad('Free ' . $free_day, 12);
			$temp = $date;
			while (strtotime($temp) <= strtotime($charge_date)) {
				$locs = [];
				echo str_pad($stock->{$function}($temp, $free_day, false, $locs, true)[0], 3) . '    |     ';
				$temp = date('Y-m-d', strtotime($temp . ' + 7 days'));
			}
			echo PHP_EOL;
		}
	}

	public function exportTestEach($stock, $free_tests, $charge_date, $function)
	{
		$ledgers = WmsStockLedger::model()->findAll(['condition' => 'stock_id = :stock_id', 'params' => [':stock_id' => $stock->id], 'order' => 'ts']);
		$locs = [];
		foreach ($ledgers as $ledger) {
			$locs[$ledger->location_id][$ledger->qty_in > 0 ? 'in' : 'out'] = $ledger->ts;
		}

		foreach ($locs as $id => $trans) {
			if (!empty($trans['out'])) {
				$out = $trans['out'];
			} else {
				$out = date('Y-m-d 23:59:59', strtotime($charge_date));
			}
			$stay = (strtotime($out) - strtotime($trans['in'])) / 86400;
			echo PHP_EOL . '   合生元   ' . $id . ' | ' . $trans['in'] . ' | ' . $out . ' | ' . $stay . PHP_EOL . PHP_EOL;
			echo '                             ';
			$date = '2020-02-22';
			while (strtotime($date) <= strtotime($charge_date)) {
				echo $date . ' | ';
				$date = date('Y-m-d', strtotime($date . ' + 7 days'));
			}
			echo PHP_EOL;

			echo '                             ';
			$date = '2020-02-28';
			while (strtotime($date) <= strtotime($charge_date)) {
				echo $date . ' | ';
				$date = date('Y-m-d', strtotime($date . ' + 7 days'));
			}
			echo PHP_EOL;

			$date = '2020-02-28';
			foreach ($free_tests as $free_day) {
				echo str_pad('Free ' . $free_day, 7) . ' ' . date('Y-m-d H:i:s', strtotime($trans['in'] . ' + ' . $free_day . ' days')) . '     ';
				$temp = $date;
				while (strtotime($temp) <= strtotime($charge_date)) {
					$locs = [];
					$stock->{$function}($temp, $free_day, false, $locs);
					echo (in_array($id, $locs) ? 'Yes' : '   ') . '     |    ';
					$temp = date('Y-m-d', strtotime($temp . ' + 7 days'));
				}
				echo PHP_EOL;
			}
			echo PHP_EOL;
		}
	}

	public function exportTestEach2($stock, $free_tests, &$total, &$correct, $charge_date, $function)
	{
		$ledgers = WmsStockLedger::model()->findAll(['condition' => 'stock_id = :stock_id', 'params' => [':stock_id' => $stock->id], 'order' => 'ts']);
		$locs = [];
		foreach ($ledgers as $ledger) {
			$locs[$ledger->location_id][$ledger->qty_in > 0 ? 'in' : 'out'] = $ledger->ts;
		}

		foreach ($locs as $id => $trans) {
			echo str_pad('IN', 22) . str_pad('OUT', 22) . str_pad('DAYS', 7) . str_pad('FREE', 10) . str_pad('COUNT', 7) . str_pad('CHARGE WK', 10) . PHP_EOL;
			if (!empty($trans['out'])) {
				$out = $trans['out'];
			} else {
				$out = date('Y-m-d 23:59:59', strtotime($charge_date));
			}

			$date = '2020-02-28';
			foreach ($free_tests as $free_day) {
				$stay = (strtotime($out) - strtotime($trans['in'])) / 86400;
				echo $trans['in'] . ' | ' . $out . ' | ' . str_pad(number_format($stay, 1), 4) . ' | ';
				echo str_pad('Free ' . $free_day, 7) . ' | ' . str_pad(max(number_format(0, 1), number_format($stay - $free_day, 1)), 4) . ' | ';
				$charges = 0;
				$temp = $date;
				while (strtotime($temp) <= strtotime($charge_date)) {
					$locs = [];
					$stock->{$function}($temp, $free_day, false, $locs);
					$charges = in_array($id, $locs) ? $charges + 1: $charges;
					$temp = date('Y-m-d', strtotime($temp . ' + 7 days'));
				}
				echo str_pad($charges, 2);
				if ($charges == ceil(($stay - $free_day > 0 ? $stay - $free_day : 0) / 7)) {
					$correct ++;
				} else {
					echo '*';
				}
				echo PHP_EOL;
				$total ++;
			}
			echo str_pad('', 20, '-') . PHP_EOL;
		}
	}

	public function testStorage()
	{
		$sql = 'DELETE FROM wms_location WHERE NAME LIKE "testHSY%"';
		Yii::app()->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM wms_stock_ledger WHERE stock_id = (SELECT id FROM wms_stock WHERE prod_id = 1 AND org_id = 1)';
		Yii::app()->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM wms_stock_location WHERE stock_id = (SELECT id FROM wms_stock WHERE prod_id = 1 AND org_id = 1)';
		Yii::app()->db->createCommand($sql)->execute();

		$stock = WmsStock::model()->find('prod_id = 1 AND org_id = 1');
		if (empty($stock)) {
			$stock = new WmsStock;
			$stock->org_id = 1;
			$stock->prod_id = 1;
			$stock->qty = 0;
			$stock->save();
		}
		if ($stock->getErrors()) {
			echo json_encode($stock->getErrors()) . PHP_EOL;
		}

		for ($i = 0; $i < 50; $i++) {
			$this->testStockIn($stock, '2020-05-22');
		}
		$this->testStockOut($stock, '2020-05-22');

		// $this->exportLedger($stock, '2020-05-29');

		// $this->exportTest($stock, '2020-05-29', 'chargeUnit');

		$stock->refresh();
		// $free_tests = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14];
		// $this->exportTestEach($stock, $free_tests, '2020-05-29', 'chargeUnit');
		$total = $correct = 0;
		$free_tests = [0,3,5,7,12,12,14,19,22,25];
		$this->exportTestEach2($stock, $free_tests, $total, $correct, '2020-05-29', 'chargeUnit');
		echo 'Total: ' . $total . ', correct: ' . $correct . ', rate: ' . ($correct / $total * 100) . '%' . PHP_EOL;

		$sql = 'DELETE FROM wms_location WHERE NAME LIKE "testHSY%"';
		Yii::app()->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM wms_stock_ledger WHERE stock_id = (SELECT id FROM wms_stock WHERE prod_id = 1 AND org_id = 1)';
		Yii::app()->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM wms_stock_location WHERE stock_id = (SELECT id FROM wms_stock WHERE prod_id = 1 AND org_id = 1)';
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function transfer_sort_record()
	{
		$tasks = WmsTask::model()->with('job', 'items')->findAll('job.org_id IN (1816, 1985, 2872, 2939, 3058) AND t.is_request = 1 AND t.status != 100 AND JSON_VALUE(items.meta, "$.sort_qty") >= 0');
		foreach ($tasks as $task) {
			$prods = [];
			foreach ($task->items as $item) {
				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				if (empty($stock)) continue;
				if (empty($prods[$stock->prod_id]['qty'])) $prods[$stock->prod_id]['qty'] = 0;
				$prods[$stock->prod_id]['qty'] += intval(@$item->mdata['uq']);
				if (empty($prods[$stock->prod_id]['sort_qty'])) $prods[$stock->prod_id]['sort_qty'] = 0;
				$prods[$stock->prod_id]['sort_qty'] += intval(@$item->mdata['sort_qty']);
			}

			foreach ($prods as $prod_id => $values) {
				$wts = WmsTaskSort::model()->find('task_id = :task_id AND prod_id = :prod_id', [':task_id' => $task->id, ':prod_id' => $prod_id]);
				if (empty($wts)) {
					$wts = new WmsTaskSort;
					$wts->task_id = $task->id;
					$wts->prod_id = $prod_id;
				}
				$wts->qty = $values['qty'];
				$wts->sort_qty = $values['sort_qty'];
				$wts->save();
				if (!empty($wts->getErrors())) echo json_encode($wts->getErrors()) . PHP_EOL;
			}
		}
	}

	public function transfer_sort_record_2()
	{
		$tasks = WmsTask::model()->with('job', 'items')->findAll('job.org_id IN (1816, 1985, 2872, 2939, 3058) AND t.is_request = 0 AND t.status != 100 AND JSON_VALUE(items.meta, "$.sort_qty") >= 0');
		foreach ($tasks as $task) {
			$prods = [];
			foreach ($task->items as $item) {
				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				if (empty($stock)) continue;
				if (empty($prods[$stock->prod_id]['qty'])) $prods[$stock->prod_id]['qty'] = 0;
				$prods[$stock->prod_id]['qty'] += intval(@$item->mdata['uq']);
				if (empty($prods[$stock->prod_id]['sort_qty'])) $prods[$stock->prod_id]['sort_qty'] = 0;
				$prods[$stock->prod_id]['sort_qty'] += intval(@$item->mdata['sort_qty']);
			}

			foreach ($prods as $prod_id => $values) {
				$wts = WmsTaskSort::model()->find('task_id = :task_id AND prod_id = :prod_id', [':task_id' => $task->mainTask->id, ':prod_id' => $prod_id]);
				if (empty($wts)) {
					$wts = new WmsTaskSort;
					$wts->task_id = $task->mainTask->id;
					$wts->prod_id = $prod_id;
				}
				$wts->qty = $values['qty'];
				$wts->sort_qty = $values['sort_qty'];
				$wts->save();
				if (!empty($wts->getErrors())) echo json_encode($wts->getErrors()) . PHP_EOL;
			}
		}
	}

	public function st_fuel_supplement()
	{
		$fuel_rates = [
			'20033' => 15.3, '20034' => 15.3, '20035' => 15.3,
			'20036' => 15.5, '20037' => 15.5, '20038' => 15.5, '20039' => 15.5, '20040' => 15.5, '20041' => 15.5, '20042' => 15.5, '20043' => 15.5, '20044' => 15.5,
		];

		foreach ($fuel_rates as $no => $rate) {
			$lines = BillingLine::model()->findAll('org_id IN (858, 1888) AND status != 11 AND billing_cref LIKE :no AND flag = 0 AND billing_id != 0 AND `desc` != "admin fee"', [':no' => '%' . $no . '%']);
			foreach ($lines as $line) {
				$fuel_line = BillingLine::model()->find('JSON_VALUE(meta, "$.fuel_for") = :id', [':id' => $line->id]);
				if (empty($fuel_line)) {
					$fuel_line = new BillingLine;
					$fuel_line->billing_id = $line->billing_id;
					$fuel_line->org_id = $line->org_id;
					$fuel_line->op_id = $line->op_id;
					$fuel_line->link_id = $line->link_id;
					$fuel_line->to_id = $line->to_id;
					$fuel_line->created = $line->created;
					$fuel_line->date = $line->date;
					$fuel_line->due = $line->due;
					$fuel_line->transaction_date = $line->transaction_date;
					$fuel_line->type = $line->type;
					$fuel_line->dpmt = $line->dpmt;
					$fuel_line->gst = $line->gst;
					$fuel_line->status = $line->status;
					$fuel_line->billing_cref = $line->billing_cref;
					$fuel_line->billing_ref = $line->billing_ref;
					$fuel_line->awb = $line->awb;
					$fuel_line->desc = 'Fuel Surcharge ' . $rate . '%';
					$fuel_line->qty = $line->qty;
					$fuel_line->price = $line->price;
					$fuel_line->dpt_id = $line->dpt_id;
					$fuel_line->currency = $line->currency;
					$fuel_line->charge_code = $line->charge_code;
					$fuel_line->weight = $line->weight;
					$fuel_line->charge_weight = $line->charge_weight;
					$fuel_line->item_code = $line->item_code;
					$fuel_line->sync_xero = $line->sync_xero;
					$fuel_line->flag = $line->flag;
					$fuel_line->mdata['fuel_for'] = $line->id;
				}
				$fuel_line->actual_amount = number_format($line->actual_amount * $rate / 100, 2, '.', '');
				$fuel_line->accrual_amount = $fuel_line->actual_amount;
				$fuel_line->gst_amount = $fuel_line->getGSTValue();
				$fuel_line->save();
				if (!empty($fuel_line->getErrors())) echo json_encode($fuel_line->getErrors()) . PHP_EOL;

				Billing::linkLine($fuel_line);
			}
		}
	}

	public function checkTNTother()
	{
		$model = Reconciliation::model()->findByPk($this->prompt('ID: '));
		$model->tntOther();
	}

	public function brokerCredit()
	{
		$bis = BillingInvoice::model()->with('invoice')->findAll('invoice.status = 8');
		foreach ($bis as $bi) {
			$inv = Invoice::model()->find(['condition' => 'no LIKE :no', 'params' => [':no' => '%' . $bi->invoice->no . '%'], 'order' => 'id DESC']);
			if (!empty($inv) && $bi->inv_id != $inv->id) {
				$bi->inv_id = $inv->id;
				$bi->checkInvoice();
				$bi->save();
			}
		}
	}

	public function autoCompleteWmsTask()
	{
		$tasks = WmsTask::model()->findAll('status = 30 AND link_id = 0 AND job_id = :job_id', [':job_id' => $this->prompt('ID: ')]);
		foreach ($tasks as $task) {
			if (empty($task->actionTask->items)) {
				continue;
			}

			$task->status = 99;
			$task->compl_time = date('Y-m-d H:i:s', strtotime($task->lastlog->time) + 3600);
			if (strtotime($task->createlog->time) < strtotime('2019-07-01 00:00:00')) {
				$task->bwf |= 4;
			}
			$task->bwf |= 2;
			$task->save();
		}
	}

	public function groupAupostLine()
	{
		$consol = $this->prompt('Consol No: ');

		if (!empty($consol)) {
			$bls = BillingLine::model()->findAll('org_id = 101 AND billing_ref = :no AND billing_cref = billing_ref AND actual_amount > 0 AND status != 11', [':no' => $consol]);
			echo count($bls) . PHP_EOL;

			$data = [];
			foreach ($bls as $bl) {
				$shipments = Shipment::model()->with('consol')->findAll('consol.no = :no AND JSON_VALUE(t.meta, "$.import_billing_id") = :bid', [':no' => $consol, ':bid' => $bl->id]);
				foreach ($shipments as $shipment) {
					$recLine = ReconciliationLine::model()->with('parent')->find('shipment_id = :sid AND client_type = 1', [':sid' => $shipment->id]);
					if (!empty($recLine)) {
						$data[$recLine->invoice_no]['shipment'][] = $shipment;
						$data[$recLine->invoice_no]['billing'][] = $bl;
						if (empty($data[$recLine->invoice_no]['accrual'])) $data[$recLine->invoice_no]['accrual'] = 0;
						$data[$recLine->invoice_no]['accrual'] += $recLine->my_value;
						if (empty($data[$recLine->invoice_no]['actual'])) $data[$recLine->invoice_no]['actual'] = 0;
						$data[$recLine->invoice_no]['actual'] += $recLine->value;
					}
				}
			}

			foreach ($data as $ap => $v) {
				foreach ($v['billing'] as $bl) {
					$bl->status = 11;
					$bl->update('status');
				}

				$new_line = BillingLine::copy($bl);
				$new_line->accrual_amount = $v['accrual'];
				$new_line->actual_amount = $v['actual'];
				$new_line->gst_amount = $new_line->getGSTValue();
				$new_line->desc = 'total shipment: ' . count($v['shipment']);
				$new_line->mdata['aupost_manifest_no'] = $ap;
				$new_line->status = 1;
				$new_line->save();

				foreach ($v['shipment'] as $shipment) {
					$shipment->mdata['import_billing_id'] = $new_line->id;
					$shipment->updateMeta();
				}
			}
		}
	}

	public function fix_ninjashark()
	{
		$tasks = [];
		$invs = Invoice::model()->findAll('id IN (160869)');
		foreach ($invs as $inv) {
			foreach ($inv->lines as $line) {
				if ($line->det == 'Delivery') $tasks[] = $line->fid;
			}
		}

		$quote = WmsOrgQuote::model()->find('org_id = :org_id and status = 1', [':org_id' => 1985]);

		foreach ($tasks as $task_id) {
			$old = InvLine::model()->find('det = "Delivery" AND fid = :fid', [':fid' => $task_id]);

			$task = WmsTask::model()->findByPk($task_id);
			$wt = 0;
			foreach ($task->mainTask->items as $item) {
				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				$wt += $stock->prod->weight * $item->mdata['uq'];
			}
			$wt += 0.8;

			$criteria = new CDbCriteria();
			$criteria->compare('id', $task->mdata['shipment_id']);
			$shipments = Shipment::model()->findAll($criteria);

			if ($task->mdata['cnee']['country'] != 'AU') {
				$total = 0;
				foreach ($shipments as $shipment) {
					if (!empty($quote->mdata[WmsOrgQuote::QUOTE_INTERNATIONAL_CHARGECODE])) {
						$chargecode = $quote->mdata[WmsOrgQuote::QUOTE_INTERNATIONAL_CHARGECODE];
						$total += $task->getChargeByChargecodeInternational($wt, 'AU', $chargecode);
					} else if (!empty($shipment->trans[sizeof($shipment->trans) - 1]->cost) && !empty($quote->mdata[WmsOrgQuote::QUOTE_SENDLE_CHARGE])) {
						$total += $shipment->trans[sizeof($shipment->trans) - 1]->cost + $quote->mdata[WmsOrgQuote::QUOTE_SENDLE_CHARGE];
					}
				}

				echo $task->mainTask->getNo() . ' ' . str_replace('kg', '', substr($old->mdata['items'][0][3], 11)) . ' ' . $wt . ' ' . number_format($old->amount - $old->gst, 2, '.', '')  . ' ' . $total . PHP_EOL;
			} else {
				if (!empty($quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE])) {
					$chargecode = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE];
				}
				if (in_array($task->mdata['courier'], [Org::ORGID_COURIER_STARTRACK, Org::ORGID_COURIER_TNT])) {
					$chargecode = ImportChargeCode::STARTRACK_TNT_3PL;
				}

				$new = $task->getChargeByChargecode($wt, $task->mdata['cnee']['postcode'], $chargecode);

				echo $task->mainTask->getNo() . ' ' . count(json_decode($task->mainTask->packTask->mdata['pkg'])) . ' ' . str_replace('kg', '', substr($old->mdata['items'][0][3], 11)) . ' ' . $wt . ' ' . number_format($old->amount - $old->gst, 2, '.', '')  . ' ' . $new . PHP_EOL;
			}
		}
	}

	public function recordInvoiceAmountDiff()
	{
		$id = $this->prompt('ID: ');
		if (empty($id)) return;
		$recModel = Reconciliation::model()->findByPk($id);
		$recModel->recordInvoiceAmountDiff();
	}

	public function checkChargeCode()
	{
		$iccs = ImportChargeCode::model()->findAll('status = 1');
		$errors = [];
		foreach ($iccs as $icc) {
			$attachements = FileRepo::model()->findAll('fid = :oid and type = 55 order by id desc', [':oid' => $icc->id]);

			foreach ($attachements as $attachement) {
				$xls = new oExcel;
				$xls->load('filerepo' . DIRECTORY_SEPARATOR . substr($attachement->hash, 0, 2) . DIRECTORY_SEPARATOR . $attachement->hash);
				$data = $xls->getAll();
				unset($data[1]);
				foreach ($data as $row) {
					if (!preg_match('/([0-9|\,|\-|\s]*)/', $row[3], $matches) || $matches[1] != $row[3]) {
						$errors[$icc->chargecode . ' ' . $attachement->name] = 1;
					}
				}
			}
		}
		echo json_encode(array_keys($errors)) . PHP_EOL;
	}

	public function alsis_backorder()
	{
		$tasks = WmsTask::model()->with('job')->findAll(['condition' => 'job.org_id = 2872 AND t.is_request = 1 AND t.status = 10', 'order' => 't.id ASC']);
		echo count($tasks) . PHP_EOL;
		foreach ($tasks as $task) {

		}

		$cust = WmsAPI::model()->find('type = :type AND status = :status AND org_id = :org_id', [':type' => WmsAPI::WMS_API_TYPE_SHOPIFY, ':status' => 1, ':org_id' => 2872]);
		$config = array(
			'store_domain' => $cust->domain,
			'api_key' => $cust->api_key,
			'api_secret' => $cust->api_secret,
			'org_id' => $cust->org_id,
		);
		$shopify = new ShopifyAPI($config);

		$tasks = WmsTask::model()->with('job')->findAll(['condition' => 'job.org_id = 2872 AND t.is_request = 1 AND t.status = 10', 'order' => 't.id ASC']);
		foreach ($tasks as $task) {
			$map = WmsTaskMap::model()->find('task_id = :task_id', [':task_id' => $task->id]);
			if (empty($map)) {
				echo $task->ref . ' not found map' . PHP_EOL;
				continue;
			}

			$order = $shopify->getOrder($map->fid);
			if (empty($order['order'])) {
				echo $task->ref . ' not found order' . PHP_EOL;
				continue;
			}

			$order = $order['order'];

			foreach ($task->items as $item) {
				$item->del = 1;
				$item->save();
			}

			$prods = [];
			$backorder = false;
			$not_exist = [];
			foreach ($order['line_items'] as $item) {
				$prod = null;
				// Get prod
				$prod_map = WmsProdMap::model()->with('prod')->find('t.fid = :fid AND t.platform = "shopify" AND prod.status = 1', [':fid' => $item['variant_id']]);
				if (!empty($prod_map)) {
					$prod = $prod_map->prod;
				} else if (!empty($item['barcode'])) {
					$prod = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $item['barcode']]);
				} else if (!empty($item['sku'])) {
					$prod = WmsProd::model()->with('orgs')->find('orgs.sku = :sku AND orgs.org_id = :org_id AND status = 1', [':sku' => $item['sku'], ':org_id' => $shopify->org_id]);
					// 2020-04-22
					if (empty($prod)) {
						$prod = WmsProd::model()->with('orgs')->find('orgs.sku = :sku AND orgs.org_id = :org_id AND status = 1', [':sku' => str_replace('_', ' ', $item['sku']), ':org_id' => $shopify->org_id]);
					}
				}

				// 2020-06-22
				// if ($prod->ean == 'LIP004') {
				// 	$prod = WmsProd::model()->find('ean = "LIP004X2"');
				// }

				// 2020-04-22
				if (empty($prod) && !empty($item['barcode'])) {
					$pack = WmsProdPack::model()->with('prod')->find('barcode = :barcode AND prod.status = 1', [':barcode' => $item['barcode']]);
					if (!empty($pack)) {
						$prod = $pack->prod;
						$item['quantity'] *= $pack->qty;
					}
				}
				if (empty($prod) && !empty($item['sku'])) {
					$pack = WmsProdPack::model()->with('prod')->find('barcode = :barcode AND prod.status = 1', [':barcode' => $item['sku']]);
					if (!empty($pack)) {
						$prod = $pack->prod;
						$item['quantity'] *= $pack->qty;
					}
				}

				if (empty($prod)) {
					$task_errors[] = $item['name'] . ' ' . $item['variant_id'] . ' does not exist in PCAE';
					if (!isset($not_exist[$item['name']])) $not_exist[$item['name']] = 0;
					$not_exist[$item['name']] += $item['quantity'];
					continue;
				}

				// special product filter
				if (in_array($item['name'], ['FREE POSTAGE'])) {
					continue;
				}

				// if (empty($prods[$prod->id])) $prods[$prod->id] = 0;
				// $prods[$prod->id] += $item['quantity'];
				// accept pack/kit sku
				if (empty($prods[$prod->id])) $prods[$prod->id] = 0;
				if ($prod->type != WmsProd::WMS_PROD_KIT) {
					$prods[$prod->id] += $item['quantity'];
				} else if ($prod->type == WmsProd::WMS_PROD_KIT) {
					// don't record kit, cause backorder
					// $prods[$prod->id] += $item['quantity'];
					foreach ($prod->items as $prod_item) {
						if (!isset($prods[$prod_item->item->id])) $prods[$prod_item->item->id] = 0;
						$prods[$prod_item->item->id] += intval($item['quantity']) * $prod_item->qty;
					}
				}
			}
			$items = [];
			foreach ($prods as $prod_id => $qty) {
				$temp_qty = $qty;
				// Get stocks
				$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id AND qty - qty_res > 0', [':prod_id' => $prod_id, ':org_id' => $shopify->org_id]);
				foreach ($stocks as $stock) {
					if ($qty > 0) {
						$items[] = [
							'si' => $stock->id,
							'sn' => $stock->prod->name,
							'pq' => '',
							'cq' => '',
							'uq' => min($qty, $stock->availQty()),
							'pli' => '',
							'pl' => '',
							'nt' => '',
						];
						$qty -= min($qty, $stock->availQty());
					} else {
						break;
					}
				}

				$prod = WmsProd::model()->findByPk($prod_id);
				if ($qty > 0) {
					$task_errors[] = $prod->name . ' in ' . $order['name'] . ' quantity is less than ' . $temp_qty . ', only ' . (intval($temp_qty) - intval($qty)) . '.';

					// 2020-06-22 still record item
					$stock = WmsStock::model()->find('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $prod_id, ':org_id' => $shopify->org_id]);
					$items[] = [
						'si' => $stock->id,
						'sn' => $stock->prod->name,
						'pq' => '',
						'cq' => '',
						'uq' => $qty,
						'pli' => '',
						'pl' => '',
						'nt' => '',
					];
					$backorder = true;
				}

				if (!empty($not_exist)) {
					foreach ($not_exist as $name => $qty) {
						$items[] = [
							'si' => '',
							'sn' => $name,
							'pq' => '',
							'cq' => '',
							'uq' => $qty,
							'pli' => '',
							'pl' => '',
							'nt' => '',
						];
					}
				}
			}

			$task->refresh();
			$task->new_items = $items;
			$task->mdata['updated'] = true;
			$task->save();
			echo $task->id . PHP_EOL;
		}
	}

	public function alsis_backorder2()
	{
		WmsTask::alsisBackOrder();
	}

	public function updateLetterAccrual()
	{
		$consols = ElmsConsol::model()->findAll('created >= "2019-07-01"');
		foreach ($consols as $consol) {
			$orgRate_small = OrgRate::model()->find("org_id=:org_id AND name = 'Small Letter'", [':org_id'=>Org::ORGID_COURIER_AUSLETTER]);
			$orgRate_big = OrgRate::model()->find("org_id=:org_id AND name = 'Big Letter'", [':org_id'=>Org::ORGID_COURIER_AUSLETTER]);
			$ratesmall250 = ImcoConsol::getCourierCostPrice($orgRate_small, 2000, 0.249);
			$ratelarge125 = ImcoConsol::getCourierCostPrice($orgRate_big, 2000, 0.120);
			$ratelarge250 = ImcoConsol::getCourierCostPrice($orgRate_big, 2000, 0.249);
			$ratelarge500 = ImcoConsol::getCourierCostPrice($orgRate_big, 2000, 0.499);

			$small250 = InvLine::model()->with('invoice')->find('invoice.consol_id = :cid AND invoice.type = 40 AND det = "Small Letters"', [':cid' => $consol->id]);
			$small250 = intval(@$small250->qty);
			$large125 = InvLine::model()->with('invoice')->find('invoice.consol_id = :cid AND invoice.type = 40 AND det = "Large Letters up to 125g"', [':cid' => $consol->id]);
			$large125 = intval(@$large125->qty);
			$large250 = InvLine::model()->with('invoice')->find('invoice.consol_id = :cid AND invoice.type = 40 AND det = "Large Letters 125-250g"', [':cid' => $consol->id]);
			$large250 = intval(@$large250->qty);
			$large500 = InvLine::model()->with('invoice')->find('invoice.consol_id = :cid AND invoice.type = 40 AND det = "Large Letters 250-500g"', [':cid' => $consol->id]);
			$large500 = intval(@$large500->qty);

			$totalCost = $small250*$ratesmall250+$large125*$ratelarge125+$large250*$ratelarge250+$large500*$ratelarge500;

			$vcost = round($totalCost, 2);

			$billing = BillingLine::model()->find('billing_ref = :cref AND org_id = :oid AND charge_code = :ccode', [':cref' => $consol->no, ':oid' => Org::ORGID_COURIER_AUPOST, ':ccode' => Consol::AU_LOCAL_DELIVERY_COST_GL_CODE]);
			if (empty($billing)) {
				$billing = new BillingLine();
				$billing->type = BillingLine::BILLING_TYPE_IMPORT;
				$billing->status = 1; // initial pending status
				$billing->link_id = 0;
				$billing->org_id = Org::ORGID_COURIER_AUPOST;
				$billing->charge_code = Consol::AU_LOCAL_DELIVERY_COST_GL_CODE;
				$billing->billing_cref = $consol->no;
				$billing->currency = 1; // AUD default
				$billing->weight = 0;
				$billing->charge_weight =  0;

				// normally billing reference will be awb no , if not set we set job ID as reference
				$billing->billing_ref = $consol->no;
				$billing->awb = '';
				$billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
				$billing->date = $consol->created;
				$billing->created = $consol->created;
				$billing->transaction_date = $billing->date;
				$billing->due = $billing->date;
				$billing->type = BillingLine::BILLING_TYPE_IMPORT; // for import type
				$billing->dpmt = Invoice::DPMT_IMPORT;
				$billing->actual_amount = 0;
				$billing->gst = 'EXEMPTEXPENSES';
			}
			echo $consol->id . ' ' . $billing->accrual_amount . ' ' . $vcost . PHP_EOL;
			$billing->accrual_amount = $vcost;
			$billing->save();
		}
	}

	public function updateAcceptTotal()
	{
		$rs = Reconciliation::model()->findAll('client_type = 11');
		foreach ($rs as $r) {
			foreach ($r->lines as $line) {
				if ($line->postcode != 'eParcel') continue;

				$recModels = Reconciliation::model()->findAll('client_type = :type AND manifest_no = :manifest_no', [':type' => Reconciliation::AUPOST_TYPE, ':manifest_no' => $line->shipment_no]);

				$line->my_value = 0;
				$line->mdata['claim_value'] = 0;
				foreach ($recModels as $model) {
					$line->my_value += $model->getAcceptTotal();
					$line->mdata['claim_value'] += $model->invoice_total;
				}
				$line->my_value = number_format($line->my_value, 2, '.', '');
				$line->mdata['claim_value'] = number_format($line->mdata['claim_value'], 2, '.', '');
				$line->update('my_value', 'meta');
			}
		}
	}

	public function checkAndFixAupost3()
	{
		$sql = 'SELECT distinct parent_id FROM `reconciliation_line` WHERE parent_id in (select id from reconciliation where client_type = 1 and invoice_date >= "2020-01-01") and value > 1.05 * my_value';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rs as $r) {
			$rec = Reconciliation::model()->findByPk($r['parent_id']);
			foreach ($rec->lines as $line) {
				$org_rate_id = ImportChargeCode::SYDNEY_AUPOST_ID;
				if (preg_match("/(AMQ|333UF)\d{7}/", $line->shipment_no)) {
					$org_rate_id = ImportChargeCode::SYDNEY_AUPOST_ID;
				} elseif (preg_match("/33EVH\d{7}/i", $line->shipment_no)) {
					$org_rate_id = ImportChargeCode::MELBOUNE_AUPOST_ID;
				} elseif (preg_match("/33EVJ\d{7}/i", $line->shipment_no)) {
					$org_rate_id = ImportChargeCode::BRISBANE_AUPOST_ID;
				} elseif (preg_match("/33A8Y\d{7}/i", $line->shipment_no)) {
					$org_rate_id = ImportChargeCode::D2Z_COUNTRY_ID;
				}

				$ourRated = Shipment::getCourierCostByShipment($line->shipment_no, $org_rate_id, $line->weight);
				$line->my_value = number_format($ourRated['price'] / $line->shipment->pkg, 2, '.', '');
				$line->update('my_value');
			}
		}
	}

	public function checkAndFixAupost4()
	{
		$rec = Reconciliation::model()->findByPk($this->prompt('ID: '));
		foreach ($rec->lines as $line) {
			$org_rate_id = ImportChargeCode::SYDNEY_AUPOST_ID;
			if (preg_match("/(AMQ|333UF)\d{7}/", $line->shipment_no)) {
				$org_rate_id = ImportChargeCode::SYDNEY_AUPOST_ID;
			} elseif (preg_match("/33EVH\d{7}/i", $line->shipment_no)) {
				$org_rate_id = ImportChargeCode::MELBOUNE_AUPOST_ID;
			} elseif (preg_match("/33EVJ\d{7}/i", $line->shipment_no)) {
				$org_rate_id = ImportChargeCode::BRISBANE_AUPOST_ID;
			} elseif (preg_match("/33A8Y\d{7}/i", $line->shipment_no)) {
				$org_rate_id = ImportChargeCode::D2Z_COUNTRY_ID;
			}

			$ourRated = Shipment::getCourierCostByShipment($line->shipment_no, $org_rate_id, $line->weight);
			$line->my_value = number_format($ourRated['price'] / $line->shipment->pkg, 2, '.', '');
			$line->update('my_value');
		}
	}

	public function createLocation()
	{
		foreach (['A-1-1-A','A-1-1-B','A-1-2-A','A-1-2-B','A-1-3-A','A-1-3-B','A-1-4-A','A-1-4-B','A-1-5-A','A-1-5-B','A-1-6-A','A-1-6-B','A-2-1-A','A-2-1-B','A-2-2-A','A-2-2-B','A-2-3-A','A-2-3-B','A-2-4-A','A-2-4-B','A-2-5-A','A-2-5-B','A-2-6-A','A-2-6-B','A-3-1-A','A-3-1-B','A-3-2-A','A-3-2-B','A-3-3-A','A-3-3-B','A-3-4-A','A-3-4-B','A-3-5-A','A-3-5-B','A-3-6-A','A-3-6-B','A-4-1-A','A-4-1-B','A-4-2-A','A-4-2-B','A-4-3-A','A-4-3-B','A-4-4-A','A-4-4-B','A-4-5-A','A-4-5-B','A-4-6-A','A-4-6-B','A-5-1-A','A-5-1-B','A-5-2-A','A-5-2-B','A-5-3-A','A-5-3-B','A-5-4-A','A-5-4-B','A-5-5-A','A-5-5-B','A-5-6-A','A-5-6-B','A-6-1-A','A-6-1-B','A-6-2-A','A-6-2-B','A-6-3-A','A-6-3-B','A-6-4-A','A-6-4-B','A-6-5-A','A-6-5-B','A-6-6-A','A-6-6-B','S-01-1-A','S-01-1-B','S-01-2-A','S-01-2-B','S-01-3-A','S-01-3-B','S-01-4-A','S-01-4-B','S-01-5-A','S-01-5-B','S-02-1-A','S-02-1-B','S-02-2-A','S-02-2-B','S-02-3-A','S-02-3-B','S-02-4-A','S-02-4-B','S-02-5-A','S-02-5-B','S-03-1-A','S-03-1-B','S-03-2-A','S-03-2-B','S-03-3-A','S-03-3-B','S-03-4-A','S-03-4-B','S-03-5-A','S-03-5-B','S-04-1-A','S-04-1-B','S-04-2-A','S-04-2-B','S-04-3-A','S-04-3-B','S-04-4-A','S-04-4-B','S-04-5-A','S-04-5-B','S-05-1-A','S-05-1-B','S-05-2-A','S-05-2-B','S-05-3-A','S-05-3-B','S-05-4-A','S-05-4-B','S-05-5-A','S-05-5-B','S-06-1-A','S-06-1-B','S-06-2-A','S-06-2-B','S-06-3-A','S-06-3-B','S-06-4-A','S-06-4-B','S-06-5-A','S-06-5-B','S-07-1-A','S-07-1-B','S-07-2-A','S-07-2-B','S-07-3-A','S-07-3-B','S-07-4-A','S-07-4-B','S-07-5-A','S-07-5-B','S-08-1-A','S-08-1-B','S-08-2-A','S-08-2-B','S-08-3-A','S-08-3-B','S-08-4-A','S-08-4-B','S-08-5-A','S-08-5-B','S-09-1-A','S-09-1-B','S-09-2-A','S-09-2-B','S-09-3-A','S-09-3-B','S-09-4-A','S-09-4-B','S-09-5-A','S-09-5-B','S-10-1-A','S-10-1-B','S-10-2-A','S-10-2-B','S-10-3-A','S-10-3-B','S-10-4-A','S-10-4-B','S-10-5-A','S-10-5-B','S-11-1-A','S-11-1-B','S-11-2-A','S-11-2-B','S-11-3-A','S-11-3-B','S-11-4-A','S-11-4-B','S-11-5-A','S-11-5-B','T-01-1-A','T-01-1-B','T-01-2-A','T-01-2-B','T-01-3-A','T-01-3-B','T-01-4-A','T-01-4-B','T-01-5-A','T-01-5-B','T-02-1-A','T-02-1-B','T-02-2-A','T-02-2-B','T-02-3-A','T-02-3-B','T-02-4-A','T-02-4-B','T-02-5-A','T-02-5-B','T-03-1-A','T-03-1-B','T-03-2-A','T-03-2-B','T-03-3-A','T-03-3-B','T-03-4-A','T-03-4-B','T-03-5-A','T-03-5-B','T-04-1-A','T-04-1-B','T-04-2-A','T-04-2-B','T-04-3-A','T-04-3-B','T-04-4-A','T-04-4-B','T-04-5-A','T-04-5-B','T-05-1-A','T-05-1-B','T-05-2-A','T-05-2-B','T-05-3-A','T-05-3-B','T-05-4-A','T-05-4-B','T-05-5-A','T-05-5-B','T-06-1-A','T-06-1-B','T-06-2-A','T-06-2-B','T-06-3-A','T-06-3-B','T-06-4-A','T-06-4-B','T-06-5-A','T-06-5-B','T-07-1-A','T-07-1-B','T-07-2-A','T-07-2-B','T-07-3-A','T-07-3-B','T-07-4-A','T-07-4-B','T-07-5-A','T-07-5-B','T-08-1-A','T-08-1-B','T-08-2-A','T-08-2-B','T-08-3-A','T-08-3-B','T-08-4-A','T-08-4-B','T-08-5-A','T-08-5-B','T-09-1-A','T-09-1-B','T-09-2-A','T-09-2-B','T-09-3-A','T-09-3-B','T-09-4-A','T-09-4-B','T-09-5-A','T-09-5-B','T-10-1-A','T-10-1-B','T-10-2-A','T-10-2-B','T-10-3-A','T-10-3-B','T-10-4-A','T-10-4-B','T-10-5-A','T-10-5-B','T-11-1-A','T-11-1-B','T-11-2-A','T-11-2-B','T-11-3-A','T-11-3-B','T-11-4-A','T-11-4-B','T-11-5-A','T-11-5-B','U-01-1-A','U-01-1-B','U-01-2-A','U-01-2-B','U-01-3-A','U-01-3-B','U-01-4-A','U-01-4-B','U-01-5-A','U-01-5-B','U-02-1-A','U-02-1-B','U-02-2-A','U-02-2-B','U-02-3-A','U-02-3-B','U-02-4-A','U-02-4-B','U-02-5-A','U-02-5-B','U-03-1-A','U-03-1-B','U-03-2-A','U-03-2-B','U-03-3-A','U-03-3-B','U-03-4-A','U-03-4-B','U-03-5-A','U-03-5-B','U-04-1-A','U-04-1-B','U-04-2-A','U-04-2-B','U-04-3-A','U-04-3-B','U-04-4-A','U-04-4-B','U-04-5-A','U-04-5-B','U-05-1-A','U-05-1-B','U-05-2-A','U-05-2-B','U-05-3-A','U-05-3-B','U-05-4-A','U-05-4-B','U-05-5-A','U-05-5-B','U-06-1-A','U-06-1-B','U-06-2-A','U-06-2-B','U-06-3-A','U-06-3-B','U-06-4-A','U-06-4-B','U-06-5-A','U-06-5-B','U-07-1-A','U-07-1-B','U-07-2-A','U-07-2-B','U-07-3-A','U-07-3-B','U-07-4-A','U-07-4-B','U-07-5-A','U-07-5-B','U-08-1-A','U-08-1-B','U-08-2-A','U-08-2-B','U-08-3-A','U-08-3-B','U-08-4-A','U-08-4-B','U-08-5-A','U-08-5-B','U-09-1-A','U-09-1-B','U-09-2-A','U-09-2-B','U-09-3-A','U-09-3-B','U-09-4-A','U-09-4-B','U-09-5-A','U-09-5-B','U-10-1-A','U-10-1-B','U-10-2-A','U-10-2-B','U-10-3-A','U-10-3-B','U-10-4-A','U-10-4-B','U-10-5-A','U-10-5-B','U-11-1-A','U-11-1-B','U-11-2-A','U-11-2-B','U-11-3-A','U-11-3-B','U-11-4-A','U-11-4-B','U-11-5-A','U-11-5-B','V-01-1-A','V-01-1-B','V-01-2-A','V-01-2-B','V-01-3-A','V-01-3-B','V-01-4-A','V-01-4-B','V-01-5-A','V-01-5-B','V-02-1-A','V-02-1-B','V-02-2-A','V-02-2-B','V-02-3-A','V-02-3-B','V-02-4-A','V-02-4-B','V-02-5-A','V-02-5-B','V-03-1-A','V-03-1-B','V-03-2-A','V-03-2-B','V-03-3-A','V-03-3-B','V-03-4-A','V-03-4-B','V-03-5-A','V-03-5-B','V-04-1-A','V-04-1-B','V-04-2-A','V-04-2-B','V-04-3-A','V-04-3-B','V-04-4-A','V-04-4-B','V-04-5-A','V-04-5-B','V-05-1-A','V-05-1-B','V-05-2-A','V-05-2-B','V-05-3-A','V-05-3-B','V-05-4-A','V-05-4-B','V-05-5-A','V-05-5-B','V-06-1-A','V-06-1-B','V-06-2-A','V-06-2-B','V-06-3-A','V-06-3-B','V-06-4-A','V-06-4-B','V-06-5-A','V-06-5-B','V-07-1-A','V-07-1-B','V-07-2-A','V-07-2-B','V-07-3-A','V-07-3-B','V-07-4-A','V-07-4-B','V-07-5-A','V-07-5-B','V-08-1-A','V-08-1-B','V-08-2-A','V-08-2-B','V-08-3-A','V-08-3-B','V-08-4-A','V-08-4-B','V-08-5-A','V-08-5-B','V-09-1-A','V-09-1-B','V-09-2-A','V-09-2-B','V-09-3-A','V-09-3-B','V-09-4-A','V-09-4-B','V-09-5-A','V-09-5-B','V-10-1-A','V-10-1-B','V-10-2-A','V-10-2-B','V-10-3-A','V-10-3-B','V-10-4-A','V-10-4-B','V-10-5-A','V-10-5-B','V-11-1-A','V-11-1-B','V-11-2-A','V-11-2-B','V-11-3-A','V-11-3-B','V-11-4-A','V-11-4-B','V-11-5-A','V-11-5-B','W-01-1-A','W-01-1-B','W-01-2-A','W-01-2-B','W-01-3-A','W-01-3-B','W-01-4-A','W-01-4-B','W-01-5-A','W-01-5-B','W-02-1-A','W-02-1-B','W-02-2-A','W-02-2-B','W-02-3-A','W-02-3-B','W-02-4-A','W-02-4-B','W-02-5-A','W-02-5-B','W-03-1-A','W-03-1-B','W-03-2-A','W-03-2-B','W-03-3-A','W-03-3-B','W-03-4-A','W-03-4-B','W-03-5-A','W-03-5-B','W-04-1-A','W-04-1-B','W-04-2-A','W-04-2-B','W-04-3-A','W-04-3-B','W-04-4-A','W-04-4-B','W-04-5-A','W-04-5-B','W-05-1-A','W-05-1-B','W-05-2-A','W-05-2-B','W-05-3-A','W-05-3-B','W-05-4-A','W-05-4-B','W-05-5-A','W-05-5-B','W-06-1-A','W-06-1-B','W-06-2-A','W-06-2-B','W-06-3-A','W-06-3-B','W-06-4-A','W-06-4-B','W-06-5-A','W-06-5-B','W-07-1-A','W-07-1-B','W-07-2-A','W-07-2-B','W-07-3-A','W-07-3-B','W-07-4-A','W-07-4-B','W-07-5-A','W-07-5-B','W-08-1-A','W-08-1-B','W-08-2-A','W-08-2-B','W-08-3-A','W-08-3-B','W-08-4-A','W-08-4-B','W-08-5-A','W-08-5-B','W-09-1-A','W-09-1-B','W-09-2-A','W-09-2-B','W-09-3-A','W-09-3-B','W-09-4-A','W-09-4-B','W-09-5-A','W-09-5-B','W-10-1-A','W-10-1-B','W-10-2-A','W-10-2-B','W-10-3-A','W-10-3-B','W-10-4-A','W-10-4-B','W-10-5-A','W-10-5-B','W-11-1-A','W-11-1-B','W-11-2-A','W-11-2-B','W-11-3-A','W-11-3-B','W-11-4-A','W-11-4-B','W-11-5-A','W-11-5-B','X-01-1-A','X-01-1-B','X-01-2-A','X-01-2-B','X-01-3-A','X-01-3-B','X-01-4-A','X-01-4-B','X-01-5-A','X-01-5-B','X-02-1-A','X-02-1-B','X-02-2-A','X-02-2-B','X-02-3-A','X-02-3-B','X-02-4-A','X-02-4-B','X-02-5-A','X-02-5-B','X-03-1-A','X-03-1-B','X-03-2-A','X-03-2-B','X-03-3-A','X-03-3-B','X-03-4-A','X-03-4-B','X-03-5-A','X-03-5-B','X-04-1-A','X-04-1-B','X-04-2-A','X-04-2-B','X-04-3-A','X-04-3-B','X-04-4-A','X-04-4-B','X-04-5-A','X-04-5-B','X-05-1-A','X-05-1-B','X-05-2-A','X-05-2-B','X-05-3-A','X-05-3-B','X-05-4-A','X-05-4-B','X-05-5-A','X-05-5-B','X-06-1-A','X-06-1-B','X-06-2-A','X-06-2-B','X-06-3-A','X-06-3-B','X-06-4-A','X-06-4-B','X-06-5-A','X-06-5-B','X-07-1-A','X-07-1-B','X-07-2-A','X-07-2-B','X-07-3-A','X-07-3-B','X-07-4-A','X-07-4-B','X-07-5-A','X-07-5-B','X-08-1-A','X-08-1-B','X-08-2-A','X-08-2-B','X-08-3-A','X-08-3-B','X-08-4-A','X-08-4-B','X-08-5-A','X-08-5-B','X-09-1-A','X-09-1-B','X-09-2-A','X-09-2-B','X-09-3-A','X-09-3-B','X-09-4-A','X-09-4-B','X-09-5-A','X-09-5-B','X-10-1-A','X-10-1-B','X-10-2-A','X-10-2-B','X-10-3-A','X-10-3-B','X-10-4-A','X-10-4-B','X-10-5-A','X-10-5-B','X-11-1-A','X-11-1-B','X-11-2-A','X-11-2-B','X-11-3-A','X-11-3-B','X-11-4-A','X-11-4-B','X-11-5-A','X-11-5-B','Y-01-1-A','Y-01-1-B','Y-01-2-A','Y-01-2-B','Y-01-3-A','Y-01-3-B','Y-01-4-A','Y-01-4-B','Y-01-5-A','Y-01-5-B','Y-02-1-A','Y-02-1-B','Y-02-2-A','Y-02-2-B','Y-02-3-A','Y-02-3-B','Y-02-4-A','Y-02-4-B','Y-02-5-A','Y-02-5-B','Y-03-1-A','Y-03-1-B','Y-03-2-A','Y-03-2-B','Y-03-3-A','Y-03-3-B','Y-03-4-A','Y-03-4-B','Y-03-5-A','Y-03-5-B','Y-04-1-A','Y-04-1-B','Y-04-2-A','Y-04-2-B','Y-04-3-A','Y-04-3-B','Y-04-4-A','Y-04-4-B','Y-04-5-A','Y-04-5-B','Y-05-1-A','Y-05-1-B','Y-05-2-A','Y-05-2-B','Y-05-3-A','Y-05-3-B','Y-05-4-A','Y-05-4-B','Y-05-5-A','Y-05-5-B','Y-06-1-A','Y-06-1-B','Y-06-2-A','Y-06-2-B','Y-06-3-A','Y-06-3-B','Y-06-4-A','Y-06-4-B','Y-06-5-A','Y-06-5-B','Y-07-1-A','Y-07-1-B','Y-07-2-A','Y-07-2-B','Y-07-3-A','Y-07-3-B','Y-07-4-A','Y-07-4-B','Y-07-5-A','Y-07-5-B','Y-08-1-A','Y-08-1-B','Y-08-2-A','Y-08-2-B','Y-08-3-A','Y-08-3-B','Y-08-4-A','Y-08-4-B','Y-08-5-A','Y-08-5-B','Y-09-1-A','Y-09-1-B','Y-09-2-A','Y-09-2-B','Y-09-3-A','Y-09-3-B','Y-09-4-A','Y-09-4-B','Y-09-5-A','Y-09-5-B','Y-10-1-A','Y-10-1-B','Y-10-2-A','Y-10-2-B','Y-10-3-A','Y-10-3-B','Y-10-4-A','Y-10-4-B','Y-10-5-A','Y-10-5-B','Y-11-3-A','Y-11-3-B','Y-11-4-A','Y-11-4-B','Y-11-5-A','Y-11-5-B','Z-01-1-A','Z-01-1-B','Z-01-3-A','Z-01-3-B','Z-01-4-A','Z-01-4-B','Z-01-5-A','Z-01-5-B','Z-01-6-A','Z-01-6-B','Z-02-1-A','Z-02-1-B','Z-02-3-A','Z-02-3-B','Z-02-4-A','Z-02-4-B','Z-02-5-A','Z-02-5-B','Z-02-6-A','Z-02-6-B','Z-03-4-A','Z-03-4-B','Z-03-4-C','Z-03-5-A','Z-03-5-B','Z-03-5-C','Z-03-6-A','Z-03-6-B','Z-03-6-C','Z-04-1-A','Z-04-1-B','Z-04-3-A','Z-04-3-B','Z-04-4-A','Z-04-4-B','Z-04-5-A','Z-04-5-B','Z-04-6-A','Z-04-6-B','Z-05-1-A','Z-05-1-B','Z-05-3-A','Z-05-3-B','Z-05-4-A','Z-05-4-B','Z-05-5-A','Z-05-5-B','Z-05-6-A','Z-05-6-B','Z-06-1-A','Z-06-1-B','Z-06-3-A','Z-06-3-B','Z-06-4-A','Z-06-4-B','Z-06-5-A','Z-06-5-B','Z-06-6-A','Z-06-6-B','Z-07-1-A','Z-07-1-B','Z-07-3-A','Z-07-3-B','Z-07-4-A','Z-07-4-B','Z-07-5-A','Z-07-5-B','Z-07-6-A','Z-07-6-B','Z-08-1-A','Z-08-1-B','Z-08-3-A','Z-08-3-B','Z-08-4-A','Z-08-4-B','Z-08-5-A','Z-08-5-B','Z-08-6-A','Z-08-6-B','Z-09-1-A','Z-09-1-B','Z-09-3-A','Z-09-3-B','Z-09-4-A','Z-09-4-B','Z-09-5-A','Z-09-5-B','Z-09-6-A','Z-09-6-B','Z-10-1-A','Z-10-1-B','Z-10-3-A','Z-10-3-B','Z-10-4-A','Z-10-4-B','Z-10-5-A','Z-10-5-B','Z-10-6-A','Z-10-6-B','Z-11-1-A','Z-11-1-B','Z-11-3-A','Z-11-3-B','Z-11-4-A','Z-11-4-B','Z-11-5-A','Z-11-5-B','Z-11-6-A','Z-11-6-B'] as $item) {
			$storage = new WmsLocation;
			$storage->name = $item;
			$storage->code = $item;
			$storage->type = 20;
			$storage->wid = 218;
			$storage->status = 1;
			$storage->save();
		}
	}

	public function checkAndFixTnt1()
	{
		$sql = 'select s.id as sid, s.ref as sref, json_value(s.meta, "$.actual_delivery_cost") as scost, sum(rl.value) as rlcost from shipment s join reconciliation_line rl on s.id = rl.shipment_id where parent_id in (select id from reconciliation where client_type = 4) and s.created >= "2019-07-01" group by s.id having scost != rlcost';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();

		foreach ($rs as $r) {
			$shipment = Shipment::model()->findByPk($r['sid']);
			if (empty($shipment)) continue;

			$shipment->mdata['actual_delivery_cost'] = $r['rlcost'];
			$shipment->updateMeta();
		}
	}

	public function checkAndFixTnt2()
	{
		$sql = 'select s.id as sid, s.ref as sref, json_value(s.meta, "$.actual_delivery_cost") as scost, rl.id as rlid, rl.invoice_no as consol, sum(rl.value) as rlcost, rl.my_value as rlaccr, json_value(s.meta,"$.import_billing_id") as blid from shipment s join reconciliation_line rl on s.id = rl.shipment_id where parent_id in (select id from reconciliation where client_type = 4) and s.created >= "2019-07-01" and s.meta like "%import_billing_id%" group by s.ref';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();

		$bls = [];
		foreach ($rs as $r) {
			if (empty($bls[$r['blid']])) {
				$bls[$r['blid']] = ['accrual' => 0, 'actual' => 0, 'count' => 0];
			}
			$bls[$r['blid']]['accrual'] += $r['rlaccr'];
			$bls[$r['blid']]['actual'] += $r['rlcost'];
			$bls[$r['blid']]['count'] += 1;
		}

		foreach ($bls as $id => $value) {
			$line = BillingLine::model()->findByPk($id);
			if (empty($line)) echo $id . ' not found' . PHP_EOL;

			if (abs($line->actual_amount - $value['actual']) >= 0.1 || abs($line->accrual_amount - $value['accrual']) >= 0.1 || $line->desc != 'total shipment: ' . $value['count']) {

				$line->accrual_amount = $value['accrual'];
				$line->actual_amount = $value['actual'];
				$line->gst = 'INPUT';
				$line->gst_amount = $line->getGSTValue();
				$line->desc = 'total shipment: ' . $value['count'];
				$line->update('accrual_amount', 'actual_amount', 'gst', 'gst_amount', 'desc');

				echo 'fix ' . $line->id . PHP_EOL;
			}
		}
	}

	public function fastway_refresh()
	{
		$rs = Reconciliation::model()->findAll('invoice_no IN ("1685056")');
		foreach ($rs as $r) {
			$consols = [];
			foreach ($r->lines as $line) {
				if (empty($line->shipment_id)) {
					$shipment = Shipment::model()->find('ref = :ref OR can = :ref', [':ref' => $line->shipment_no]);
				} else {
					$shipment = $line->shipment;
				}

				if (empty($shipment)) continue;

				if (empty($line->consol_id)) {
					$line->consol_id = $shipment->consol_id;
				}

				$ourRated = Shipment::getCourierCostByShipment($line->shipment_no, ImportChargeCode::FASTWAY_ID_OLD, $line->weight);
				$line->shipment_id = $shipment->id;
				$line->my_value = number_format($ourRated['price'], 2, '.', '');
				$line->update('my_value', 'consol_id', 'shipment_id');

				$consols[$line->consol_id] = $line->consol_id;
			}

			foreach ($consols as $consol) {
				if ($consol == 0) continue;
				$consol = Consol::model()->findByPk($consol);
				if ($consol->owner_id != 114) {
					$consol->updateFastWayRealCost($r->id);
				} else {
					$consol->updateWmsDeliveryCost(Org::ORGID_COURIER_FASTWAY, $r->id);
				}
			}
		}
	}

	public function alsisLabel6()
	{
		$query = $this->prompt('Query: ');
		$tasks = WmsTask::model()->findAll($query);
		$out = $this->prompt('Out ? Y/N');
		echo count($tasks) . PHP_EOL;
		if ($this->prompt('Process ? Y/N') == 'Y') {
			foreach ($tasks as $task) {
				if ($out == 'Y') {
					foreach ($task->items as $item) {
						$stock = WmsStock::model()->findByPk($item->mdata['si']);
						$loc = $stock->locs[0];
						if (!empty($stock) && !empty($loc)) {
							$ac_item = new WmsTaskItem;
							$ac_item->task_id = $task->actionTask->id;
							$ac_item->mdata = array(
								'si' => $stock->id,
								'sn' => $stock->prod->name,
								'uq' => $item->mdata['uq'],
								'pli' => $loc->loc->id,
								'pl' => $loc->loc->code,
							);
							$ac_item->save();
						}
					}
				}
				$task->status = 99;
				$task->save();
			}
		}
	}

	public function changeLabel()
	{
		$sql = $this->prompt('Query: ');
		if (empty($sql)) return;
		$consols = Consol::model()->findAll($sql);
		$count = 0;
		foreach ($consols as $consol) {
			foreach ($consol->shipments as $r) {
				if (!preg_match('/7RFZ|DKC/', $r->ref)) continue;

				$count++;
				$l = new ChangeShipmentLabel;
				$l->pid = $r->id;
				$l->pref = $r->ref;
				$l->phbn = $r->hbn;

				$courier = OrgRate::model()->findByPk(ImportChargeCode::SYDNEY_AUPOST_ID);

				$r->can = $r->ref;
				$r->update('can');
				$r->ref = '';
				$r->createCourierLabel($courier);

				$l->newref = $r->ref;
				$l->save();
			}
		}
		echo $count;
	}

	public function cobayer_delivery()
	{
		$tasks = WmsTask::model()->findAll('id IN (372752,378876,404625,421437,424932,454134,467934,484389,484401) AND type = 2120');
		foreach ($tasks as $task) {
			$task->mdata['courier'] = Org::ORGID_COURIER_PCA;
			$task->save();
			$task->toShipment();
		}
	}

	public function billing_paid_json()
	{
		$from = $this->prompt('From: ');
		$to = $this->prompt('To: ');
		$results = $this->getXeroBillingsWithLines($from, $to);
		$xero_billings = [];
		foreach ($results as $result) {
			if ($result['Status'] == 'VOIDED') continue;
			$no = $result['InvoiceNumber'];
			// if (substr($no, strlen($no) - 2) == '-1' || substr($no, strlen($no) - 2) == '-2') {
			// 	$no = substr($no, 0, strlen($no) - 2);
			// }
			$no = strtolower(trim($no));
			$name = strtolower(trim($result['Contact']['Name']));
			if (empty($xero_billings[$no][$name])) {
				$xero_billings[$no][$name] = ['amount' => 0, 'paid' => 0, 'credit' => 0, 'status' => $result['Status'], 'payments' => [], 'credits' => []];
			}
			$xero_billings[$no][$name]['amount'] += $result['Subtotal'];
			$xero_billings[$no][$name]['amount'] += $result['TotalTax'];
			$xero_billings[$no][$name]['paid'] += $result['AmountPaid'];
			$xero_billings[$no][$name]['credit'] += $result['AmountCredited'];
			if (!empty($result['Payments'])) {
				foreach ($result['Payments'] as $payment) {
					$xero_billings[$no][$name]['payments'][] = ['id' => $payment['PaymentID'], 'amount' => $payment['Amount'], 'date' => $payment['Date']];
				}
			}
			if (!empty($result['CreditNotes'])) {
				foreach ($result['CreditNotes'] as $payment) {
					$xero_billings[$no][$name]['credits'][] = ['id' => $payment['CreditNoteID'], 'amount' => $payment['Total'], 'date' => $payment['Date'], 'no' => $payment['CreditNoteNumber']];
				}
			}
		}
		file_put_contents('xero_billing.json', json_encode($xero_billings));
	}

	public function billing_paid()
	{
		if (!is_file('xero_billing.json')) {
			return;
		} else {
			$xero_billings = json_decode(file_get_contents('xero_billing.json'), true);
		}

		$xls = new oExcel;
		$i = 1;
		foreach ($xero_billings as $no => $org_billings) {
			foreach ($org_billings as $org => $org_billing) {
				$hvlv_billings = Billing::model()->with('cust')->findAll('t.status != 11 AND t.billing_cref = :no AND cust.name = :name', [':no' => $no, ':name' => $org]);
				if (empty($hvlv_billings)) {
					if (substr($no, strlen($no) - 2) == '-1' || substr($no, strlen($no) - 2) == '-2') $no = substr($no, 0, strlen($no) - 2);
					$hvlv_billings = Billing::model()->with('cust')->findAll('t.status != 11 AND t.billing_cref LIKE :no AND cust.name = :name', [':no' => $no . '%', ':name' => $org]);
				}
				if (sizeof($hvlv_billings) > 1) {
					$xls->addRow($i++, [$no, $org, $org_billing['status'], $org_billing['amount'], 'more than 1']);
					continue;
				} else if (sizeof($hvlv_billings) == 0) {
					$xls->addRow($i++, [$no, $org, $org_billing['status'], $org_billing['amount'], 'not found']);
					continue;
				}

				$hvlv_billing = $hvlv_billings[0];
				$hvlv_billing->total = number_format($hvlv_billing->total, 2, '.', '');
				$org_billing['amount'] = number_format($org_billing['amount'], 2, '.', '');

				if (strtotime($hvlv_billing->date) < strtotime('2019-07-01')) {
					$hvlv_billing->status = Billing::BILLING_STATUS_PAID;
					$hvlv_billing->update('status');
					continue;
				}

				foreach ($org_billing['payments'] as $payment) {
					$pb = PaymentBilling::model()->find('xero_id = :xero_id', [':xero_id' => $payment['id']]);
					if (empty($pb)) {
						$pb = new PaymentBilling;
						$pb->org_id = $hvlv_billing->org_id;
						$pb->date = date('Y-m-d', strtotime($payment['date']['date']));
						$pb->transaction_date = date('Y-m-d', strtotime($payment['date']['date']));
						$pb->type = PaymentBilling::PAYMENT_TYPE_EFT;
						$pb->status = PaymentBilling::PAYMENT_STATUS_POSTED;
						$pb->currency = $hvlv_billing->currency;
						$pb->amount = 0;
						$pb->xero_id = $payment['id'];
						$pb->bank = 10;
						$pb->save();
					}

					if (empty($pb->getErrors())) {
						$pbill = PayBill::model()->find('pay_id = :pay_id AND bill_id = :bill_id', [':pay_id' => $pb->id, ':bill_id' => $hvlv_billing->id]);
						if (empty($pbill)) {
							$pbill = new PayBill;
							$pbill->pay_id = $pb->id;
							$pbill->bill_id = $hvlv_billing->id;
							$pbill->amount = number_format($payment['amount'], 2, '.', '');
							$pbill->transaction_date = date('Y-m-d', strtotime($payment['date']['date']));
							$pbill->save();
							if (!empty($pbill->getErrors())) {
								echo $hvlv_billing->billing_cref . ' ' . json_encode($pbill->getErrors()) . PHP_EOL;
							}
						}
					} else {
						echo $hvlv_billing->billing_cref . ' ' . json_encode($pb->getErrors()) . PHP_EOL;
					}

					$pb->updateAmount();

					if ($org_billing['paid'] > 0 && ($hvlv_billing->total - $org_billing['paid'] < 0.1 || abs($hvlv_billing->total / $org_billing['paid'] - 1) < 0.001)) {
						$hvlv_billing->status = Billing::BILLING_STATUS_PAID;
						$hvlv_billing->update('status');
					} else if ($org_billing['credit'] > 0 && ($hvlv_billing->total - $org_billing['credit'] < 0.1 || abs($hvlv_billing->total / $org_billing['credit'] - 1) < 0.001)) {
						$hvlv_billing->status = Billing::BILLING_STATUS_FULLY_CREDITED;
						$hvlv_billing->update('status');
					} else if ($hvlv_billing->total - $org_billing['paid'] - $org_billing['credit'] < 0.1 || abs($hvlv_billing->total / ($org_billing['paid'] + $org_billing['credit']) - 1) < 0.001) {
						$hvlv_billing->status = Billing::BILLING_STATUS_PAID_PARTLY_CREDIT;
						$hvlv_billing->update('status');
					} else if ($org_billing['paid'] + $org_billing['credit'] > 0) {
						$hvlv_billing->status = Billing::BILLING_STATUS_PARTIALLY_PAID;
						$hvlv_billing->update('status');
					} else {
						$hvlv_billing->status = Billing::BILLING_STATUS_POSTED;
						$hvlv_billing->update('status');
					}
				}

				foreach ($org_billing['credits'] as $credit) {
					$pb = PaymentBilling::model()->find('xero_id = :xero_id', [':xero_id' => $credit['id']]);
					if (empty($pb)) {
						$pb = new PaymentBilling;
						$pb->org_id = $hvlv_billing->org_id;
						$pb->date = date('Y-m-d', strtotime($credit['date']['date']));
						$pb->transaction_date = date('Y-m-d', strtotime($credit['date']['date']));
						$pb->type = PaymentBilling::PAYMENT_TYPE_CREDIT_NOTE;
						$pb->status = PaymentBilling::PAYMENT_STATUS_POSTED;
						$pb->currency = $hvlv_billing->currency;
						$pb->amount = 0;
						$pb->xero_id = $credit['id'];
						$pb->bank = 10;
						$pb->save();
					}

					if (!empty($credit['no'])) {
						$cb = Billing::model()->find('billing_cref = :no AND org_id = :oid', [':no' => $credit['no'], ':oid' => $hvlv_billing->org_id]);
						if (!empty($cb)) {
							$cb->status = Billing::BILLING_STATUS_PAID;
							$cb->update('status');
						}
					}

					if (empty($pb->getErrors())) {
						$pbill = PayBill::model()->find('pay_id = :pay_id AND bill_id = :bill_id', [':pay_id' => $pb->id, ':bill_id' => $hvlv_billing->id]);
						if (empty($pbill)) {
							$pbill = new PayBill;
							$pbill->pay_id = $pb->id;
							$pbill->bill_id = $hvlv_billing->id;
							$pbill->amount = number_format($credit['amount'], 2, '.', '');
							$pbill->transaction_date = date('Y-m-d', strtotime($credit['date']['date']));
							$pbill->save();
							if (!empty($pbill->getErrors())) {
								echo $hvlv_billing->billing_cref . ' ' . json_encode($pbill->getErrors()) . PHP_EOL;
							}
						}
					} else {
						echo $hvlv_billing->billing_cref . ' ' . json_encode($pb->getErrors()) . PHP_EOL;
					}

					$pb->updateAmount();

					if ($org_billing['paid'] > 0 && ($hvlv_billing->total - $org_billing['paid'] < 0.1 || abs($hvlv_billing->total / $org_billing['paid'] - 1) < 0.001)) {
						$hvlv_billing->status = Billing::BILLING_STATUS_PAID;
						$hvlv_billing->update('status');
					} else if ($org_billing['credit'] > 0 && ($hvlv_billing->total - $org_billing['credit'] < 0.1 || abs($hvlv_billing->total / $org_billing['credit'] - 1) < 0.001)) {
						$hvlv_billing->status = Billing::BILLING_STATUS_FULLY_CREDITED;
						$hvlv_billing->update('status');
					} else if ($hvlv_billing->total - $org_billing['paid'] - $org_billing['credit'] < 0.1 || abs($hvlv_billing->total / ($org_billing['paid'] + $org_billing['credit']) - 1) < 0.001) {
						$hvlv_billing->status = Billing::BILLING_STATUS_PAID_PARTLY_CREDIT;
						$hvlv_billing->update('status');
					} else if ($org_billing['paid'] + $org_billing['credit'] > 0) {
						$hvlv_billing->status = Billing::BILLING_STATUS_PARTIALLY_PAID;
						$hvlv_billing->update('status');
					} else {
						$hvlv_billing->status = Billing::BILLING_STATUS_POSTED;
						$hvlv_billing->update('status');
					}
				}

				if (abs($hvlv_billing->total - $org_billing['amount']) > 0.1 && abs(abs($hvlv_billing->total - $org_billing['amount']) / $org_billing['amount']) > 0.001) {
					$xls->addRow($i++, [$no, $org, $org_billing['status'], $org_billing['amount'], $hvlv_billing->total]);
				}
			}
		}

		$xls->output('billing paid.xlsx', null, false);
	}

	public function fix_payment_billing()
	{
		$pbs = PaymentBilling::model()->findAll('no = "" OR amount = 0');
		foreach ($pbs as $pb) {
			$pb->genNo();
			$pb->updateAmount();
			$pb->save();
		}
	}

	public function check_billing_payment_status()
	{
		$hvlv_billing = Billing::model()->findByPk($this->prompt('ID: '));
		$xero_billings = json_decode(file_get_contents('xero_billing.json'), true);
		if (!empty($xero_billings[strtolower($hvlv_billing->billing_cref)][strtolower($hvlv_billing->cust->name)])) {
			$org_billing = $xero_billings[strtolower($hvlv_billing->billing_cref)][strtolower($hvlv_billing->cust->name)];
			foreach ($org_billing['payments'] as $payment) {
				$pb = PaymentBilling::model()->find('xero_id = :xero_id', [':xero_id' => $payment['id']]);
				if (empty($pb)) {
					$pb = new PaymentBilling;
					$pb->org_id = $hvlv_billing->org_id;
					$pb->date = date('Y-m-d', strtotime($payment['date']['date']));
					$pb->transaction_date = date('Y-m-d', strtotime($payment['date']['date']));
					$pb->type = PaymentBilling::PAYMENT_TYPE_EFT;
					$pb->status = PaymentBilling::PAYMENT_STATUS_POSTED;
					$pb->currency = $hvlv_billing->currency;
					$pb->amount = 0;
					$pb->xero_id = $payment['id'];
					$pb->bank = 10;
					$pb->save();
				}

				if (empty($pb->getErrors())) {
					$pbill = PayBill::model()->find('pay_id = :pay_id AND bill_id = :bill_id', [':pay_id' => $pb->id, ':bill_id' => $hvlv_billing->id]);
					if (empty($pbill)) {
						$pbill = new PayBill;
						$pbill->pay_id = $pb->id;
						$pbill->bill_id = $hvlv_billing->id;
						$pbill->amount = number_format($payment['amount'], 2, '.', '');
						$pbill->transaction_date = date('Y-m-d', strtotime($payment['date']['date']));
						$pbill->save();
						if (!empty($pbill->getErrors())) {
							echo $hvlv_billing->billing_cref . ' ' . json_encode($pbill->getErrors()) . PHP_EOL;
						}
					}
				} else {
					echo $hvlv_billing->billing_cref . ' ' . json_encode($pb->getErrors()) . PHP_EOL;
				}

				$pb->updateAmount();

				if ($org_billing['paid'] > 0 && ($hvlv_billing->total - $org_billing['paid'] < 0.1 || abs($hvlv_billing->total / $org_billing['paid'] - 1) < 0.001)) {
					$hvlv_billing->status = Billing::BILLING_STATUS_PAID;
					$hvlv_billing->update('status');
				} else if ($org_billing['credit'] > 0 && ($hvlv_billing->total - $org_billing['credit'] < 0.1 || abs($hvlv_billing->total / $org_billing['credit'] - 1) < 0.001)) {
					$hvlv_billing->status = Billing::BILLING_STATUS_FULLY_CREDITED;
					$hvlv_billing->update('status');
				} else if ($hvlv_billing->total - $org_billing['paid'] - $org_billing['credit'] < 0.1 || abs($hvlv_billing->total / ($org_billing['paid'] + $org_billing['credit']) - 1) < 0.001) {
					$hvlv_billing->status = Billing::BILLING_STATUS_PAID_PARTLY_CREDIT;
					$hvlv_billing->update('status');
				} else if ($org_billing['paid'] + $org_billing['credit'] > 0) {
					$hvlv_billing->status = Billing::BILLING_STATUS_PARTIALLY_PAID;
					$hvlv_billing->update('status');
				} else {
					$hvlv_billing->status = Billing::BILLING_STATUS_POSTED;
					$hvlv_billing->update('status');
				}
			}

			foreach ($org_billing['credits'] as $credit) {
				$pb = PaymentBilling::model()->find('xero_id = :xero_id', [':xero_id' => $credit['id']]);
				if (empty($pb)) {
					$pb = new PaymentBilling;
					$pb->org_id = $hvlv_billing->org_id;
					$pb->date = date('Y-m-d', strtotime($credit['date']['date']));
					$pb->transaction_date = date('Y-m-d', strtotime($credit['date']['date']));
					$pb->type = PaymentBilling::PAYMENT_TYPE_CREDIT_NOTE;
					$pb->status = PaymentBilling::PAYMENT_STATUS_POSTED;
					$pb->currency = $hvlv_billing->currency;
					$pb->amount = 0;
					$pb->xero_id = $credit['id'];
					$pb->bank = 10;
					$pb->save();
				}

				if (empty($pb->getErrors())) {
					$pbill = PayBill::model()->find('pay_id = :pay_id AND bill_id = :bill_id', [':pay_id' => $pb->id, ':bill_id' => $hvlv_billing->id]);
					if (empty($pbill)) {
						$pbill = new PayBill;
						$pbill->pay_id = $pb->id;
						$pbill->bill_id = $hvlv_billing->id;
						$pbill->amount = number_format($credit['amount'], 2, '.', '');
						$pbill->transaction_date = date('Y-m-d', strtotime($credit['date']['date']));
						$pbill->save();
						if (!empty($pbill->getErrors())) {
							echo $hvlv_billing->billing_cref . ' ' . json_encode($pbill->getErrors()) . PHP_EOL;
						}
					}
				} else {
					echo $hvlv_billing->billing_cref . ' ' . json_encode($pb->getErrors()) . PHP_EOL;
				}

				$pb->updateAmount();

				if ($org_billing['paid'] > 0 && ($hvlv_billing->total - $org_billing['paid'] < 0.1 || abs($hvlv_billing->total / $org_billing['paid'] - 1) < 0.001)) {
					$hvlv_billing->status = Billing::BILLING_STATUS_PAID;
					$hvlv_billing->update('status');
				} else if ($org_billing['credit'] > 0 && ($hvlv_billing->total - $org_billing['credit'] < 0.1 || abs($hvlv_billing->total / $org_billing['credit'] - 1) < 0.001)) {
					$hvlv_billing->status = Billing::BILLING_STATUS_FULLY_CREDITED;
					$hvlv_billing->update('status');
				} else if ($hvlv_billing->total - $org_billing['paid'] - $org_billing['credit'] < 0.1 || abs($hvlv_billing->total / ($org_billing['paid'] + $org_billing['credit']) - 1) < 0.001) {
					$hvlv_billing->status = Billing::BILLING_STATUS_PAID_PARTLY_CREDIT;
					$hvlv_billing->update('status');
				} else if ($org_billing['paid'] + $org_billing['credit'] > 0) {
					$hvlv_billing->status = Billing::BILLING_STATUS_PARTIALLY_PAID;
					$hvlv_billing->update('status');
				} else {
					$hvlv_billing->status = Billing::BILLING_STATUS_POSTED;
					$hvlv_billing->update('status');
				}
			}
		}
	}

	public function ccode()
	{
		$maps = ZoneMapIntl::model()->findAll();
		foreach ($maps as $map) {
			$map->save();
		}
	}

	public function return_manifest()
	{
		$sst = Shipment::model()->findAll('id IN (12866867,13233450,13236396,13729665,13736538,14510055,14510067,14510085,15322158,15391560,15435750,15436350,15436413,15451554,15451596)');
		$cn = time();

		$apa = new AusPostAPI('syd');
		$max_ppg = 1000;
		$pgs = ceil(count($sst) / $max_ppg);
		for($pg = 0; $pg < $pgs; $pg++){
			$ss = array_slice($sst, $pg * $max_ppg, $max_ppg);
			$transaction = Yii::app()->db->beginTransaction();
			try {
				$r = $apa->createOrderFromShipments($ss, $cn . '-return' . ($pgs > 1 ? '-' . $pg : ''));
				if (!empty($r->order)) {
					$oid = $r->order->order_id;
					$mani = Manifest::model()->find('type = 90 and ref = :ref',[':ref'=>$oid]);
					if(empty($mani))
					{	
						$mani = new Manifest();
						$mani->fwd_id=101;
						$mani->type = 90;
						$mani->created = date('Y-m-d H:i:s');
						$mani->ref = $oid;
					}else
					{
						$mani->created = date('Y-m-d H:i:s');
					}
					$mani->save();

					// because aupost returned shipment is not the same order with our sending order
					// so here we need to order by HBN again
					$auPostShipments = [];
					foreach ($r->order->shipments as $aushipment) {
						$auPostShipments[$shipment_created? $aushipment->shipment_id : $aushipment->shipment_reference] = $aushipment;
					}

					foreach ($ss as $i => $s) {
						$maniMap = ManiMap::model()->find("mani_id = :mani_id and model = 'ImParcel' and fid = :fid",[":mani_id"=>$mani->id,":fid"=>$s->id]);
						if(empty($maniMap))
						{
							$maniMap = new ManiMap();
						}

						$maniMap->mani_id = $mani->id;
						$maniMap->model = 'ImParcel';
						$maniMap->fid = $s->id;
						$maniMap->status = 10;
						$maniMap->mdata['mani_time'] = date('Y-m-d H:i:s');
						$maniMap->save();


						$ts = new Tranship;
						$ts->pid = $s->id;
						$ts->org_id = 101;  // for Australia post office
						$ts->man_id = $s->man_id;
						$ts->type = 80;  // shipment transfer to a different delivery courier
						$ts->status = 19; // in finally moving status
						$ts->connote = $s->ref;
						$ts->time = date('Y-m-d H:i:s');
						$ts->mdata['oid'] = $oid;
						if($shipment_created){
							$aushipment = $auPostShipments[$s->mdata['ap_sid']];
						}else{
							$aushipment = $auPostShipments[$s->hbn];
						}
						$ts->mdata['sid'] = $aushipment->shipment_id;

						$costValue = floatval($aushipment->shipment_summary->total_cost - $aushipment->shipment_summary->total_gst);
						$ts->cost = round($costValue, 2);
						$ts->save();
						if (!empty($s->mdata['direct_courier'])&&$s->mdata['direct_courier']==2) {
							$s->cbwf=($s->cbwf^32)&$s->cbwf;
							$s->cbwf=$s->cbwf|64;
							$s->mdata['direct_courier']=1;
							$s->updateMeta();
							$s->update(['cbwf']);
						}
					}
				} else {
					foreach ($apa->err as $e) {
						$msg = $e->message;
						if (!empty($e->field) && preg_match('/shipments\[(\d+)\]/', $e->field, $m)) {
							$msg .= ': '.$ss[$m[1]]->hbn;
						}
						$err[] = $msg;
					}
				}
				$transaction->commit();
			} catch (Exception $ex) {
				$transaction->rollback();
				Log::log2file("Aupost Return Manifest".$cn->no."=>".$ex->getMessage(), "transaction_err_log", "transaction");
				throw $ex;
			}
		}
	}

	public function tranship()
	{
		$sst = Shipment::model()->findAll('id IN (12866867,13233450,13236396,13729665,13736538,14510055,14510067,14510085,15322158,15391560,15435750,15436350,15436413,15451554,15451596)');
		$ids = [];
		foreach ($sst as $ss) {
			$apa = new AusPostAPI('syd');
			$r = $apa->getShipments($ss->mdata['ap_sid']);

			if (!empty($r->shipments)) {
				$mani = Manifest::model()->find('type = 90 and ref = :ref', [':ref' => $r->shipments[0]->order_id]);
				if (empty($mani)) {
					$mani = new Manifest();
					$mani->fwd_id = 101;
					$mani->type = 90;
					$mani->created = date('Y-m-d H:i:s');
					$mani->ref = $r->shipments[0]->order_id;
					$mani->save();
				}

				$maniMap = ManiMap::model()->find("mani_id = :mani_id and model = 'ImParcel' and fid = :fid", [":mani_id" => $mani->id, ":fid" => $ss->id]);
				if (empty($maniMap)) {
					$maniMap = new ManiMap();
				}
				$maniMap->mani_id = $mani->id;
				$maniMap->model = 'ImParcel';
				$maniMap->fid = $ss->id;
				$maniMap->status = 10;
				$maniMap->mdata['mani_time'] = date('Y-m-d H:i:s');
				$maniMap->save();

				$ts = new Tranship;
				$ts->pid = $ss->id;
				$ts->org_id = 101;  // for Australia post office
				$ts->man_id = $mani->id;
				$ts->type = 80;  // shipment transfer to a different delivery courier
				$ts->status = 19; // in finally moving status
				$ts->connote = $ss->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->mdata['oid'] = $r->shipments[0]->order_id;
				$ts->mdata['sid'] = $r->shipments[0]->shipment_id;
				$ts->save();
			}
		}
	}

	public function test_ufl()
	{
		$fd = '2020-07-18';
		$td = '2020-07-25';

		$max_qty = 0;
		$temp_fd = $fd;
		while ($temp_fd <= $td) {
			$stock_qty = 0;

			$stock = new WmsStock('search');
			$stock->unsetAttributes();
			$stock->org_id = 2939;
			$rs = $stock->search(false);
			foreach ($rs->data as $r) {
				$ledgers = WmsStockLedger::model()->findAll('ts >= :todate AND stock_id = :sid AND location_id > 10', array(':todate' => date('Y-m-d', strtotime($temp_fd . ' + 1 day')), ':sid' => $r->id));
				foreach ($ledgers as $ledger) {
					if ($ledger->qty_in > 0) {
						$r->qty -= $ledger->qty_in;
					} else if ($ledger->qty_out > 0) {
						$r->qty += $ledger->qty_out;
					}
				}

				$stock_qty += $r->qty;
			}

			$max_qty = $max_qty < $stock_qty ? $stock_qty : $max_qty;

			echo $temp_fd . ' ' . $max_qty . PHP_EOL;

			$temp_fd = date('Y-m-d', strtotime($temp_fd . ' + 1 day'));
		}
	}

	public function get_xero_oauth2_authUrl()
	{
		$token = $this->prompt('Token System Key: ');
		if (empty($token)) return;

		include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR.'vendor/autoload.php');

		$xero = new XeroAPI($token, true);
		$provider = $xero->provider;

		$authUrl = $provider->getAuthorizationUrl([
			'scope' => 'offline_access accounting.transactions'
		]);
		echo $authUrl . PHP_EOL;		
	}

	public function get_xero_oauth2_token()
	{
		$token = $this->prompt('Token System Key: ');
		if (empty($token)) return;

		include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR.'vendor/autoload.php');

		$xero = new XeroAPI($token, true);
		$provider = $xero->provider;

		$token = $provider->getAccessToken('authorization_code', [
			'code' => $this->prompt('auth code:')
		]);
		echo json_encode($token) . PHP_EOL;
	}

	public function get_xero_oauth2_tenant()
	{
		$token = $this->prompt('Token System Key: ');
		if (empty($token)) return;

		include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR.'vendor/autoload.php');

		$xero = new XeroAPI($token);
		$provider = $xero->provider;

		$setting = SystemSetting::model()->find('t.key = :key', [':key' => $token]);
		$accessToken = new League\OAuth2\Client\Token\AccessToken($setting->mdata['token']);
		echo json_encode($accessToken) . PHP_EOL;

		$tenants = $provider->getTenants($accessToken);
		echo json_encode($tenants) . PHP_EOL . PHP_EOL . PHP_EOL;
	}

	public function test_get_invoice()
	{
		$xero = new XeroAPI;
		echo json_encode($xero->get('Accounting\Invoice', ['Type' => 'ACCPAY', 'FromDate' => '2020-07-01 00:00:00', 'ToDate' => date('Y-m-d H:i:s')])) . PHP_EOL;
	}

	public function fix()
	{
		$bs = Billing::model()->findAll('JSON_VALUE(meta, "$.from_supplier_invoice") > 1000 AND status = 2');
		foreach ($bs as $billing) {
			$billings = [];
			$billings[$billing->billing_cref] = $billing->lines;
			Yii::import('application.controllers.BillingController');
			$bc = new BillingController('default');
			$bc->postXero($billings, true);
		}
	}

	public function get_billing_xero()
	{
		$xero = new XeroAPI;
		$i = 1;
		$results = [];
		while (true) {
			$temp = $xero->get('Accounting\Invoice', ['Type' => 'ACCPAY', 'FromDate' => '2018-07-01 00:00:00'], $i++);
			if (count($temp) == 0) break;
			$results = array_merge($results, array_values((array)($temp)));
		}
		file_put_contents('xero_billing.json', json_encode($results));
	}

	public function sync_billing_xero_id()
	{
		if (!is_file('xero_billing.json')) {
			return;
		} else {
			$xero_billings = json_decode(file_get_contents('xero_billing.json'), true);
		}

		foreach ($xero_billings as $result) {
			$no = $result['InvoiceNumber'];
			$billing = Billing::model()->with('cust')->find('t.status != 11 AND t.billing_cref = :no AND cust.name = :name', [':no' => $no, ':name' => $result['Contact']['Name']]);
			if (empty($billing)) {
				$billing = Billing::model()->with('cust')->find('t.status != 11 AND t.billing_cref LIKE :no AND cust.name = :name', [':no' => '%' . explode('-', $no)[0] . '%', ':name' => $result['Contact']['Name']]);
			}
			if (empty($billing)) continue;
			else $count++;

			$billing->xero_id = $result['InvoiceID'];
			$billing->nolog = true;
			$billing->save();
		}
	}

	public function get_invoice_xero()
	{
		$xero = new XeroAPI;
		$i = 1;
		$results = [];
		while (true) {
			$temp = $xero->get('Accounting\Invoice', ['Type' => 'ACCREC', 'FromDate' => '2018-07-01 00:00:00'], $i++);
			if (count($temp) == 0) break;
			$results = array_merge($results, array_values((array)($temp)));
		}
		file_put_contents('xero_invoice.json', json_encode($results));
	}

	public function sync_invoice_xero_id()
	{
		if (!is_file('xero_invoice.json')) {
			return;
		} else {
			$xero_invoices = json_decode(file_get_contents('xero_invoice.json'), true);
		}

		foreach ($xero_invoices as $result) {
			$no = $result['InvoiceNumber'];
			$invoice = Invoice::model()->with('cust')->find(['condition' => 't.status != 10 AND t.no = :no AND cust.name = :name AND xero_id = ""', 'params' => [':no' => $no, ':name' => $result['Contact']['Name']], 'order' => 't.id DESC']);
			if (empty($invoice)) {
				$invoice = Invoice::model()->with('cust')->find(['condition' => 't.status != 10 AND t.no LIKE :no AND cust.name = :name AND xero_id = ""', 'params' => [':no' => '%' . explode('-', $no)[0] . '%', ':name' => $result['Contact']['Name']], 'order' => 't.id DESC']);
			}
			if (empty($invoice)) continue;

			$invoice->xero_id = $result['InvoiceID'];
			$invoice->nolog = true;
			$invoice->save();
		}
	}

	public function resend_invoice()
	{
		$id = $this->prompt('ID: ');
		if (empty($id)) return;

		if ($id > 500000) [$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];

		$tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		if (!file_exists($tempDirectory)) {
			mkdir($tempDirectory);
		}

		$invoice = Invoice::model()->findByPk($id);
		if (empty($invoice)) return;

		$this->_sendInvoice($invoice, $tempDirectory);
	}

	private function _sendInvoice($invoice, $tempDirectory, $includeOld = true, $showBalance = false)
	{
		$fileName = $tempDirectory.DIRECTORY_SEPARATOR.'Invoice_'.$invoice->no.'.pdf';
		$excelFile = $invoice->exportExcelInvoice($tempDirectory.DIRECTORY_SEPARATOR,false);
		$fileName2 = '';
		// echo $invoice->no;
		if ($invoice->type != 60) {
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
			if ($oldInvoice->type != 60) {
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

	public function resend_custinv()
	{
		[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
		$invoice = Invoice::model()->findByPk($this->prompt('ID: '));
		if (empty($invoice)) return;

		$process = ShipmentProcess::model()->find('pid = :pid', [':pid' => $invoice->pid]);

		$model = new Emailog();
		$model->fid = $process->pid;
		$shipment = Shipment::model()->findByPk($process->pid);
		if (($shipment->bwf & ImParcel::CUSTOM_DDP) > 0) {
			$org = Org::model()->findByPk($shipment->agent_id);
			if (!empty($org->extra['outturn_email'])) {
				$email = $org->extra['outturn_email'];
			}
			$model->type = Emailog::CUSTOM_INVOICE_AGENT;
		} elseif (($shipment->bwf & ImParcel::CUSTOM_DDU) > 0) {
			$email = isset($shipment->cnee->email) ? $shipment->cnee->email : '';
			$model->type = Emailog::CUST_INVOICE_INFORM;
		}
		$model->prepTemplate();
		$model->subject = $model->tpl->subject;
		$model->body = $model->tpl->getContent();

		$model->mdata['to'] = $email;

		$model->mdata['from'] = 'imports@toplogistics.com.au';
		$model->mdata['fromName'] = 'PCA imports';
		$model->status = 10;
		$model->save();

		$files = [];
		$tempDirectory = Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR . 'zip' . time();
		if (!file_exists($tempDirectory)) {
			mkdir($tempDirectory);
		}
		$invs = Invoice::model()->findAll('dpmt=10 AND type IN(41,45) AND pid=:pid AND status not in (10,9,8,7) ORDER BY id DESC', [':pid' => $model->fid]);
		if (empty($invs)) {
			return;
		}
		if (empty($model->mdata['to'])) {
			return;
		}
		foreach ($invs as $invoice) {
			$fileName = $tempDirectory . DIRECTORY_SEPARATOR . 'Invoice_' . $invoice->no . '.pdf';
			oPDF::renderPDF('invoice', ['inv' => $invoice], 2, $fileName);
			$files[] = [$fileName, 'Invoice_' . $invoice->no . '.pdf'];
		}
		$o = $model->sendEmail($files);
		AppHelper::unlinkRecursive($tempDirectory);
	}

	public function resend_lodge()
	{
		$con = Consol::model()->findByPk($this->prompt('ID: '));
		Yii::import('application.controllers.ImcoConsolController');
		$cc = new ImcoConsolController('default');
		$cc->actionAupost($con->id);
		$cc->actionStartrack($con->id, true);
		$cc->actionTntManifest($con->id);
		$cc->actionFastway($con->id);
		$cc->actionAupostInt($con->id);
	}

	public function fix_fastway_manifest()
	{
		$id = $this->prompt('ID: ');
		if ($id > 500000) [$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
		$inv = Invoice::model()->findByPk($id);
		$consol = $inv->consol;

		$tot = 0;
		$items = [];
		$chargeCode = '';
		if ($consol->owner_id == 1206) {
			$chargeCode = 8271;
		}
		$owner = Org::model()->findByPk($consol->owner_id);
		foreach ($consol->shipments as $i => $p) {
			$amt = $p->getChargeByChargecode($chargeCode, true);  // fastway with special charge code
			$p->mdata['charge_client_amount']= number_format($amt, 4, '.', '');
			$p->mdata['charge_client_weight']= number_format($p->weight, 2, '.', '');
			$p->updateMeta();
			if (!empty($p->tempChargeweight)) {
				$p->weight=$p->tempChargeweight;
			}
			$desc = $p->getDesc();
			if (empty($desc)) $desc = 'null';
			else $desc = str_replace('"', '', json_encode($desc));
			$items[] = [$p->ref, $desc, $p->pkg, $p->weight, $p->cbm, $amt,$p->cnee->postcode];
			$tot += $amt;
		}

		$il = new InvLine;
		$il->inv_id = $inv->id;
		$il->ccode = 'EPA';
		$il->mdata['items'] = $items;
		$il->det = $consol->no;
		$il->fid = $consol->id;
		$il->model = 'ImcoConsol'; // invoice connected with console directly
		$il->amount = number_format(round(round($tot * 1000)/1000,3), 3, '.', '');
		$il->qty = 1;
		$il->gst = 0;
		$il->det = '';
		$il->tax = '';
		$il->save();
		Log::log2file("1206 Manifest Creating Invoice".$consol->no."=>".json_encode($il), "transaction_err_log", "transaction");
		Log::log2file("1206 Manifest Creating Invoice".$consol->no."=>".json_encode($il->getErrors()).json_encode($inv->getErrors()), "transaction_err_log", "transaction");

		$inv->refresh();
		$inv->getTotal();

		$inv->mdata['name'] = $owner->name;
		$inv->mdata['address'] = $owner->getAddress();
		$inv->mdata['payterm'] = empty($owner->extra['payterm'])? 'COD' : $owner->extra['payterm'].' days';
		$inv->no = 'FW'.$inv->id;
		$inv->save();

		// update console's fastway cost billing
		ImcoConsol::updateImportConsoleBilling([$consol->id]);

		echo 'Invoice '.$inv->no." issued<br />";
	}

	public function genWmsInvoice()
	{
		$min = $this->prompt('ID: ');
		$org_ids = [];
		$sql = "SELECT org_id FROM `wms_stock` where qty>0 OR updated>=DATE_SUB(NOW(), INTERVAL 32 DAY) group by org_id";
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		if (!empty($rs)) {
			foreach ($rs as $r) {
				if ($r['org_id'] < $min) continue;
				$org_ids[] = $r['org_id'];
			}
			$org_ids[] = 1308;

			foreach ($org_ids as $oid) {
				$org = Org::model()->findByPk($oid);
				if (!empty($org) && !empty($org->extra['wms_invoice'])) {
					$wmsOrgQuote = WmsOrgQuote::model()->find("org_id=:org_id AND status=1", [':org_id'=>$oid]);
					//if not exist OrgQuote, we need to find their parent org quotes, through by_id
					if (empty($wmsOrgQuote)&&$org->by>1) {
						$wmsOrgQuote = WmsOrgQuote::model()->find("org_id=:org_id AND status=1", [':org_id'=>$org->by]);
					}
					if (empty($wmsOrgQuote)) {
						//we need to make an error log;
						$this->log2file('100001-WMS ORG Rate Not set UP for'.$org->id, 'wms_err');
						continue;
					}

					//to here we already get the rate, so we need to get the start date
					$start_date = date('Y-m-d', strtotime('-7 day'));
					$end_date = date('Y-m-d', strtotime('-1 day'));
					// if (!empty($org->extra['wms_from_date'])) {
					// 	$start_date = $org->extra['wms_from_date'];
					// }
					$errors = WmsInvoice::genWmsService($org, $start_date, $end_date);
					if (!empty($errors)) {
						$this->log2file('100004-'.implode(";", $errors)." org_id: ".$org->id, 'wms_err');
						// if (!in_array($org->id, Org::$airsea_heshengyuan)) {
						// 	throw new Exception('wms service invoice - '.implode(";", $errors)." org_id: ".$org->id);
						// }
					} else {
						//update the wms_from_date
						$org->extra['wms_from_date']=$end_date;
						$org->updateMeta();
					}
				}
			}
		}
	}

	public function billingCheckPaid()
	{
		$billing = Billing::model()->findByPk($this->prompt('ID: '));
		$billing->checkPaid();
		$billing->update('status');
	}

	public function revertPick()
	{
		$id = $this->prompt('ID: ');
		if (empty($id)) return;

		$task = WmsTask::model()->findByPk($id);
		foreach ($task->actionTask->items as $itm) {
			$itm->revertPick();
		}
	}

	public function billing_status()
	{
		$billings = Billing::model()->findAll('status in (6,7,8,9) AND date >= :date', [':date' => $this->prompt('Date: ')]);
		foreach ($billings as $billing) {
			$status1 = $billing->getStatus();
			$billing->checkPaid();
			$status2 = $billing->getStatus();
			if ($status1 != $status2 && $billing->total > 0) {
				echo $billing->id . ' ' . $status1 . ' ' . $status2 . PHP_EOL;
			}
		}
	}

	public function billing_fix()
	{
		$billings = Billing::model()->findAll('status in (6,7,8,9) AND date >= :date', [':date' => $this->prompt('Date: ')]);
		foreach ($billings as $billing) {
			$status1 = $billing->getStatus();
			$billing->checkPaid();
			$status2 = $billing->getStatus();
			if ($status1 != $status2 && $billing->total > 0) {
				echo $billing->id . ' ' . $status1 . ' ' . $status2 . PHP_EOL;
				$billing->update(['status']);
			}
		}
	}

	public function alsis_tasks()
	{
		$tasks = WmsTask::model()->findAll('id IN (534026,534038,534062,534074,534086,534098)');

		foreach ($tasks as $task) {
			if (!empty($task->items)) continue;

			$item = new WmsTaskItem;
			$item->task_id = $task->id;
			$item->mdata = [
				'si' => 42246,
				'sn' => 'EXTREME LIP PLUMPING SERUM Default Title (Bat: 062019)',
				'pq' => '',
				'cq' => '',
				'uq' => 2,
				'pli' => '',
				'pl' => '',
				'nt' => '',
			];
			$item->save();
		}
	}

	public function invoices()
	{
		$invoices = Invoice::model()->findAll("no IN ('OT174738','OT174744','OT174642','OT174657','OT174672','OT174684','OT174687','OT174690','OT174693','OT174696','OT174708','OT174714','OT174717','OT174723','OT174732','OT174735','CA171291-1','ED174312','ED174324','ED173814','ED174309','ED174318','ED174339','ED173811','ED174258','ED174315','ED174327','ED174330','ED174333','ED174336','ED174342','ED174345','ED175344','ED175347','ED175356','ED173835','ED175353','ED174081-1','ED174162-1','WM174486','WM174426','ED173655','WM174453','WM174384','WM174555','WM174519','WM174510','WM174546','WD173592','WD173598','IM173766','WD173802','LO173769','WD173805','WD173874','WD173886','WD173868','WD173871','WD173877','CA173892','CA173889','CA173895','WD174111','WD174144','WD174093','WD174141','WD174096','WD174105','WD174108','WD174114','WD174138','WD174147','WD174156','WD174159','WD174294','WD174291','OT174963','IM175107','IM175098','IM175134','IM175140','OT175068','IM175113','IM175119','IM175128','IM175086','IM175092','IM175110','IM175116','OT175122','IM175125','IM175131-1','IM175137','ST175380','WM174498','IM173763','IM173772','IM173775','WD173883','IM174987','IM175398','OT175422','DI175416','DI175419','WM174393','WM174462','ED175410','DI173961','WD173880','WD174126','WM174420','WM174480','WM174567','ED173616-1','ED173619-1','WM174387','WM174483','WM174408','WM174471','WM174477','WM174414','WM174504','WM174516','IM174225','OT174210','IM174222','OT174219','OT174231','OT174234','RT174084','OT174204','OT174207','IM174228','ED174747','ED174750','ED174753','ED174762','ED174756','ED174759','ED174765','ED174771','ED174768','ED174774','ED174777','IM174213','RT174012','WM174489','WM174561','WM174417','WM174468','OT175065','ED174183','ED174165','ED174168','ED174171','ED174174','ED174252','ED174864','ED174972','ED174885','ED174867','CA173934','CA174015','CA174960','CA175362','CA175377','ED173913','ED174267','OT175104','OT175095','OT175209','CRN72740','ED173718','CRN72372','IM173856','RT173778','OT174177','IM174216','OT174978','IM174981','CA170199-1','IM173955','CA172005-1','CA175203','IM175218','WD173595','WD174132','WD174135','WD174102','WD174129','WD174150','WD174153','IM174975','RT175080','RT175212','OT174666','RT173760','RT173757','RT173808','RT173901','RT173919','RT174033','RT175050','RT175407','OT175389','WM174522','ED173988','ED173967','ED173982','ED173973','ED173985','ED173970','ED173979','OT173844','OT173838','OT173847','OT173841','WM174432','WM174492','DI174624','DI174630','DI174621','DI174627','ED174270','WM174465','WM174396','ED175413','WM174501','WM174537','IM174189','IM174984','OT175200','OT175197','WD173865','WD174099','WD174117','WD174123','IM174198','WD174087','WD174090','WD174120','IM174192','IM174195','IM174201','IM175029','IM175008','IM175011-1','IM175017','IM175041','IM174993','IM175014','IM175023','IM175032','IM175191','IM175185','IM175182','IM175179','RT175206','OT175395','CRN72782','IM174990','IM175071','IM175074','CA173799','CA173952','CA174036','CA174039','CA174042','CA174243','CA174246','CA174240','RT174321','CA174255','IM175026','IM175053','IM175038','IM175056','IM175047','IM175059','CA175146','OT175083','ST175269','ST175245','ST175263','ST175299','ST175329','ST175341','ST175224','ST175305','ST175314','ST175227','ST175236','ST175248','ST175266','ST175272','ST175275','ST175281','ST175287','ST175290','ST175302','ST175308','ST175311','ST175320','ST175338','ST175221','ST175230','ST175233','ST175239','ST175242','ST175251','ST175254','ST175257','ST175260','ST175278','ST175284','ST175293','ST175296','ST175317','ST175323','ST175326','ST175332','ST175335','OT175392')");
		foreach ($invoices as $invoice) {
			$invoice->saveInvoice2xero();
		}
	}

	public function testDelivery()
	{
		$task = WmsTask::model()->findByPk($this->prompt('ID: '));
		$criteria = new CDbCriteria();
		$criteria->compare('id', $task->mdata['shipment_id']);
		$shipments = Shipment::model()->findAll($criteria);
		$quote_type = 0;
		$stot = 0;
		$weight = [];
		if (preg_match('/[\x{4e00}-\x{9fa5}]+/u', $task->mdata['cnee']['state']) && in_array($aid, [Org::ORGID_3PL_SUNNYA, Org::ORGID_3PL_COBAYER])) {
			foreach ($shipments as $shipment) {
				// for cobayer
				if (!empty($quote->mdata[WmsOrgQuote::QUOTE_MISC_DELIVERY_CHINA])) {
					$china = $quote->mdata[WmsOrgQuote::QUOTE_MISC_DELIVERY_CHINA];
				} else {
					$china = 5;
				}
				// sunnya milk 4.5, other 6
				if (in_array($aid, [Org::ORGID_3PL_SUNNYA])) {
					$stock = WmsStock::model()->findByPk($task->mainTask->items[0]->mdata['si']);
					if (preg_match('/milk/i', $stock->prod->name)) {
						$china = 4.5;
					} else {
						$china = 6;
					}
				}
				$stot += max($shipment->weight, 1) * $china;
				$weight[] = $shipment->weight;
			}
			$items = [];
			// sunnya not split display
			if (in_array($aid, [Org::ORGID_3PL_SUNNYA])) {
				$items[] = [$task->getNo(), ucwords($task->mainTask->ref), empty($task->mainTask->compl_time) ? $task->compl_time : $task->mainTask->compl_time, $task->getType() . ' - ' . array_sum($weight) . 'kg', $stot, 1, 1 * $stot];
			} else {
				$items[] = [$task->getNo(), ucwords($task->mainTask->ref), empty($task->mainTask->compl_time) ? $task->compl_time : $task->mainTask->compl_time, $task->getType() . ' - ' . implode('kg, ', $weight) . 'kg', $stot, 1, 1 * $stot];
			}
		} else {
			if (!empty($quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE])) {
				$cc = ImportChargeCode::model()->find('chargecode = :chargecode AND status = 1', [':chargecode' => $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE]]);
			} else if (!in_array($aid, [Org::ORGID_3PL_LIFESTYLE])) {
				$cc = ImportChargeCode::model()->find('org_id = 114 AND status = 1');
			}
			if (empty($cc)) {
				$errors[] = "10003 - Neither a valid import chargecode for this org or a standard import chargecode is available";
			}
			$items = [];
			foreach ($shipments as $shipment) {
				$chargecode = $cc->chargecode;

				// startrack and tnt chargecode
				if (in_array($task->mdata['courier'], [Org::ORGID_COURIER_STARTRACK, Org::ORGID_COURIER_TNT])) {
					$chargecode = ImportChargeCode::STARTRACK_TNT_3PL;
				}

				// express and exclude startrack & tnt
				$express = false;
				if (!empty($task->mainTask->mdata['note']) && preg_match('/express/i', $task->mainTask->mdata['note'])) {
					$express = true;
				}

				foreach ($task->mainTask->items as $item) {
					if (!empty($item->mdata['nt']) && preg_match('/express/i', $item->mdata['nt'])) {
						$express = true;
					}
				}

				if ($express && !in_array($task->mdata['courier'], [Org::ORGID_COURIER_STARTRACK, Org::ORGID_COURIER_TNT])) {
					$chargecode = $quote->mdata[WmsOrgQuote::QUOTE_EXPRESS_CHARGECODE];
				}

				if (in_array($aid, [Org::ORGID_3PL_DWVERTEX])) {
					// dw-vertex use chargecode in shipment meta
					if (!empty($shipment->mdata['chargecode'])) {
						$chargecode = $shipment->mdata['chargecode'];
					}
					if (preg_match('/7RFZ/', $shipment->ref)) {
						$chargeweight = isset($shipment->mdata['charge_client_weight']) ? $shipment->mdata['charge_client_weight'] : $shipment->chargeWeight();
						$cost = $task->getChargeByChargecode($chargeweight, $shipment->postcode, $chargecode);
					} else {
						$cost = $task->getChargeByChargecode($shipment->weight, $shipment->postcode, $chargecode);
					}
				// } else if (in_array($aid, [Org::ORGID_3PL_COBAYER])) {
					// cobayer for all parcel charge by total weight
					// $cost = $task->getChargeByChargecode($shipment->weight, $shipment->postcode, $chargecode);
				} else if ($task->mdata['cnee']['country'] != 'AU') {
					// international, first read chargecode, then read sendle cost
					if (!empty($quote->mdata[WmsOrgQuote::QUOTE_INTERNATIONAL_CHARGECODE]) && $task->mdata['courier'] != Org::ORGID_COURIER_SENDLE) {
						$chargecode = $quote->mdata[WmsOrgQuote::QUOTE_INTERNATIONAL_CHARGECODE];
						$cost = $task->getChargeByChargecodeInternational($shipment->weight, $shipment->cnee->country, $chargecode);
					} else if (!empty($shipment->trans[sizeof($shipment->trans) - 1]->cost) && !empty($quote->mdata[WmsOrgQuote::QUOTE_SENDLE_CHARGE])) {
						$cost = $shipment->trans[sizeof($shipment->trans) - 1]->cost + $quote->mdata[WmsOrgQuote::QUOTE_SENDLE_CHARGE];
					} else {
						$errors[] = "10003 - Please set up a valid International Charge Rate for Delivery in the Rate Management " . $task->mainTask->id;
						continue;
					}
				} else if (preg_match('/' . $task->getNo() . '/i', $shipment->ref)) {
					// letter
					if (!empty($quote->mdata[WmsOrgQuote::QUOTE_LETTER_CHARGECODE])) {
						$chargecode = $quote->mdata[WmsOrgQuote::QUOTE_LETTER_CHARGECODE];
						$cost = $task->getChargeByChargecode($shipment->weight, $shipment->postcode, $chargecode);
					} else {
						$errors[] = "10003 - Please set up a valid Letter Charge Rate for Delivery in the Rate Management " . $task->mainTask->id;
						continue;
					}
				} else {
					// aupost charge by weight of each pkg, other charge by total weight
					if ($task->mdata['shipment_courier_id'] == 101) {
						$cost = $task->getChargeByChargecode($shipment->weight, $shipment->postcode, $chargecode, true);
					} else {
						$cost = $task->getChargeByChargecode($shipment->weight, $shipment->postcode, $chargecode);
					}
				}
				if ($cost) {
					$stot += $cost;
					$weight[] = $shipment->weight;
					if (in_array($aid, [Org::ORGID_3PL_DWVERTEX])) {
						$items[] = [$task->getNo(), ucwords($task->mainTask->ref), $shipment->created, $shipment->ref . ' ' . $shipment->weight . 'kg', $cost, 1, 1 * $cost];
					}
				} else {
					$errors[] = "10003 - Courier Chargecode do not cover postcode for " . $task->mainTask->id;
					continue;
				}
			}
			if (!in_array($aid, [Org::ORGID_3PL_DWVERTEX])) {
				$items[] = [$task->getNo(), ucwords($task->mainTask->ref), empty($task->mainTask->compl_time) ? $task->compl_time : $task->mainTask->compl_time, $task->getType() . ' - ' . implode('kg, ', $weight) . 'kg', $stot, 1, 1 * $stot];
			}
		}

		echo json_encode($items) . PHP_EOL;
		echo json_encode($errors) . PHP_EOL;
	}

	public function updateOrg()
	{
		$xls = new oExcel;
		$xls->load(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'xero_org.xlsx');
		$data = $xls->getAll();
		foreach ($data as $k => $line) {
			$org = Org::model()->find('name = :name', [':name' => $line[3]]);
			if (empty($org)) {
				echo $k . PHP_EOL;
				continue;
			}

			if ($org->abn != $line[1]) {
				Log::add($org, Log::LOG_TYPE_UPDATE, array_merge(['notes' => 'Update ABN from ' . $org->abn . ' to ' . $line[1]]));
				$org->abn = $line[1];
				$org->nolog = true;
				$org->save();
				sleep(1);
			}

			if ($org->phone != $line[2]) {
				Log::add($org, Log::LOG_TYPE_UPDATE, array_merge(['notes' => 'Update phone from ' . $org->phone . ' to ' . $line[2]]));
				$org->phone = $line[2];
				$org->nolog = true;
				$org->save();
				sleep(1);
			}

			if ($org->address != $line[4]) {
				Log::add($org, Log::LOG_TYPE_UPDATE, array_merge(['notes' => 'Update address from ' . $org->address . ' to ' . $line[4]]));
				$org->address = $line[4];
				$org->nolog = true;
				$org->save();
				sleep(1);
			}

			if ($org->suburb != $line[5]) {
				Log::add($org, Log::LOG_TYPE_UPDATE, array_merge(['notes' => 'Update suburb from ' . $org->suburb . ' to ' . $line[5]]));
				$org->suburb = $line[5];
				$org->nolog = true;
				$org->save();
				sleep(1);
			}

			if ($org->state != $line[6]) {
				Log::add($org, Log::LOG_TYPE_UPDATE, array_merge(['notes' => 'Update state from ' . $org->state . ' to ' . $line[6]]));
				$org->state = $line[6];
				$org->nolog = true;
				$org->save();
				sleep(1);
			}

			if ($org->postcode != $line[7]) {
				Log::add($org, Log::LOG_TYPE_UPDATE, array_merge(['notes' => 'Update postcode from ' . $org->postcode . ' to ' . $line[7]]));
				$org->postcode = $line[7];
				$org->nolog = true;
				$org->save();
				sleep(1);
			}
		}
	}

	public function fixAlsis()
	{
		$cust = WmsAPI::model()->findByPk(6);
		$config = array(
			'store_domain' => $cust->domain,
			'api_key' => $cust->api_key,
			'api_secret' => $cust->api_secret,
			'org_id' => $cust->org_id,
		);
		$tasks = WmsTask::model()->findAll('id IN (547418,547460,547472,547508,547520,547532,547544,547580,547628,547676,547892,547904,548018,548150,548246,548270,548474,548522,548582,548606,548906,548990,549014,549050,549110,549134,549194,549206,549242,549266,549278,549302,549326,549374,549386,549398,549422,549518,549542,549566,549626,549638,549662,550046,550070,550082,550094,550106,550130,550142,550154,550262,550274,550286,550298,550310,550322,550346,550370,550598,550610,550622,550646,550670,550706,550718)');
		foreach ($tasks as $task) {
			$map = WmsTaskMap::model()->find('task_id = :task_id', [':task_id' => $task->id]);
			$shopify = new ShopifyAPI($config);
			$shopify_order = $shopify->getOrder($map->fid);

			$shipments = Shipment::model()->findAll('cref = :task_id', [':task_id' => $task->getNo()]);
			foreach ($shipments as $shipment) {
				if ($shipment->consol_id != 0) continue;
				$task->deliveryTask->mdata['shipment_id'] = [$shipment->id];
				$task->deliveryTask->update('meta');

				if (!empty($shopify_order['order']['fulfillments'][0]['id'])) {
					// $result = $shopify->openFulfillment($map->fid, $shopify_order['order']['fulfillments'][0]['id']);
					// echo json_encode($result) . PHP_EOL;
					// $result = $shopify->updateFulfillment($shopify_order['order']['fulfillments'][0]['id'], $shipment->ref . ',' . 'https://www.fastway.com.au/tools/track/?l=' . $shipment->ref, ['https://www.fastway.com.au/tools/track/?l=' . $shipment->ref]);
					// echo json_encode($result) . PHP_EOL;
					// $result = $shopify->successFulfillment($map->fid, $shopify_order['order']['fulfillments'][0]['id']);
					// echo json_encode($result) . PHP_EOL;
					$result = $shopify->postFulfillment($map->fid, $shipment->ref . ',' . 'https://www.fastway.com.au/tools/track/?l=' . $shipment->ref, ['https://www.fastway.com.au/tools/track/?l=' . $shipment->ref]);
					echo json_encode($result) . PHP_EOL;
				}
			}
		}
	}

	public function bio()
	{
		$tasks = WmsTask::model()->with('job')->findAll('t.status = 20 AND t.is_request = 1 AND job.org_id = 1816');
		foreach ($tasks as $task) {
			foreach ($task->items as $item) {
				$item->toStock();
			}
		}
	}

	public function check_ninja_bio_weight()
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['Customer', 'Task No', 'est Weight', 'package Weight', 'invoice Weight']);

		$tasks = array_merge(
			WmsTask::model()->with('job')->findAll('t.is_request = 1 AND t.type IN (3020,3030) AND t.status = 99 AND job.org_id = 1816 AND t.id >= 505446 AND t.bwf = 0'),
			WmsTask::model()->with('job')->findAll('t.is_request = 1 AND t.type IN (3020,3030) AND t.status = 99 AND job.org_id = 1985 AND t.id >= 512643 AND t.bwf = 0')
		);

		foreach ($tasks as $task) {
			$eWeight = 0;
			foreach ($task->items as $item) {
				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				$eWeight += $stock->prod->weight / 1000 * intval($item->mdata['uq']);
			}

			$pWeight = 0;
			$pkgs = 0;
			if (!empty($task->packTask->mdata['pkg'])) {
				foreach (json_decode($task->packTask->mdata['pkg'], true) as $pk) {
					$pWeight += $pk['wt'];
					$pkgs += 1;
				}
			}

			$dWeight = 0;
			if (!empty($task->deliveryTask->mdata['shipment_id'])) {
				foreach ($task->deliveryTask->mdata['shipment_id'] as $sid) {
					$shipment = Shipment::model()->findByPk($sid);
					$dWeight += $shipment->weight;
				}
			}

			$xls->addRow($i++, [$task->job->customer->name, $task->getNo(), $eWeight, $pWeight, $dWeight, $pkgs]);
		}

		$xls->output('ninja&biophysics.xlsx', null, false);
	}

	public function deleteTLA3pl()
	{
		[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
		$invoices = Invoice::model()->findAll('id > 500000 AND no like "WM%"');
		foreach ($invoices as $invoice) {
			echo $invoice->no . PHP_EOL;
			$invoice->revoke();
		}
	}

	public function alsis_clear()
	{
		$tasks = WmsTask::model()->findAll('is_request = 1 and job_id in (select id from wms_job where org_id = 2872) and type in (3020,3030) and (status in (20,30,40) or compl_time >= "2020-09-08 00:00:00")');
		echo sizeof($tasks) . PHP_EOL;
		foreach ($tasks as $task) {
			foreach ($task->actionTask->items as $item) {
				$item->delete();
			}
		}
	}

	public function update_consol()
	{
		$from = $this->prompt('From: ');
		if (empty($from)) {
			return;
		}

		$to = $this->prompt('To: ');
		if (empty($to)) {
			return;
		}

		$cond = 'type IN (15,70,80) AND created >= :from AND created < :to';
		$params = [':from' => $from, ':to' => $to];

		$id = $this->prompt('ID: ');
		if ($id) {
			$cond .= ' AND id = :id';
			$params[':id'] = $id;
		}

		$consols = Consol::model()->findAll($cond, $params);
		foreach ($consols as $consol) {
			echo $consol->no . ' ' . memory_get_usage() . PHP_EOL;
			foreach ([Org::ORGID_COURIER_AUPOST, Org::ORGID_COURIER_FASTWAY, Org::ORGID_COURIER_STARTRACK, Org::ORGID_COURIER_TNT] as $crid) {
				$lines = BillingLine::model()->findAll('org_id = :crid AND status != 11 AND `desc` LIKE "%total shipment%" AND billing_ref = :ref', [':ref' => $consol->no, ':crid' => $crid]);
				foreach ($lines as $line) {
					$sql = 'SELECT id FROM shipment WHERE consol_id = :cid AND JSON_VALUE(meta, "$.import_billing_id") = :bid';
					$rs = Yii::app()->db->createCommand($sql)->bindValues([':cid' => $consol->id, ':bid' => $line->id])->queryAll();
					if (empty($rs)) {
						$line->status = 11;
						$line->mdata['courier_dup'] = true;
						$line->update('status', 'meta');
					}
				}

				$lines = BillingLine::model()->findAll('org_id = :crid AND status != 11 AND `desc` LIKE "%left shipment%" AND actual_amount = 0 AND accrual_amount > 0 AND billing_ref = :ref', [':ref' => $consol->no, ':crid' => $crid]);
				foreach ($lines as $k => $line) {
					if ($k == 0) continue;
					$line->status = 11;
					$line->mdata['courier_left_dup'] = true;
					$line->update('status', 'meta');
				}

				$left = ['no' => 0, 'amount' => 0];
				foreach ($consol->shipments as $p) {
					$flag = false;
					foreach ($p->trans as $tran) {
						if ($tran->org_id == $crid) {
							$flag = true;
						}
					}
					if (!$flag) continue;

					if (isset($p->mdata['import_billing_id_aupost'])) {
						$billingline = BillingLine::model()->findByPk($p->mdata['import_billing_id_aupost']);
						if (!empty($billingline) && $billingline->status != 11 && $billingline->actual_amount != 0 && $billingline->org_id == $crid) {
							continue;
						}
					} else if (isset($p->mdata['import_billing_id'])) {
						$billingline = BillingLine::model()->findByPk($p->mdata['import_billing_id']);
						if (!empty($billingline) && $billingline->status != 11 && $billingline->actual_amount != 0 && $billingline->org_id == $crid) {
							continue;
						}
					}

					$left['no'] += 1;

					foreach ($p->trans as $ts) {
						if ($ts->org_id == $crid) {
							$left['amount'] += $ts->cost;
							break;
						}
					}
				}
				if (number_format($left['amount'], 2, '.', '') == 0) continue;
				
				$newBilling = BillingLine::model()->find('org_id = :crid AND status != 11 AND `desc` LIKE "%left shipment%" AND actual_amount = 0 AND accrual_amount > 0 AND billing_ref = :ref', [':ref' => $consol->no, ':crid' => $crid]);
				if (empty($newBilling)) $newBilling = new BillingLine;
				$newBilling->status = 1; // initial pending status
				$newBilling->link_id = 0;
				$newBilling->org_id = $crid;
				$newBilling->charge_code = Consol::AU_LOCAL_DELIVERY_COST_GL_CODE;
				$newBilling->billing_cref = $consol->no;
				$newBilling->currency = 1; // AUD default
				$newBilling->weight = 0;
				$newBilling->charge_weight = 0;
				$newBilling->billing_ref = $consol->no;
				$newBilling->awb = $consol->awb;
				$newBilling->dpt_id = empty($consol->dpt_id) ? Org::PCAE_DEPARTMENT_SYDNEY : $consol->dpt_id;
				$newBilling->date = date('Y-m-d');
				$newBilling->created = date('Y-m-d');
				$newBilling->transaction_date = date('Y-m-d');
				$newBilling->due = date('Y-m-d');
				$newBilling->type = BillingLine::BILLING_TYPE_IMPORT; // for import type
				$newBilling->dpmt = Invoice::DPMT_IMPORT;
				$newBilling->actual_amount = 0;
				$newBilling->gst = 'INPUT';
				$newBilling->desc = 'left shipment ' . $left['no'];
				$newBilling->accrual_amount = number_format($left['amount'], 2, '.', '');
				$newBilling->mdata['jrf_update_consol'] = true;
				$newBilling->save();
			}

			unset($lines);
			unset($consol->shipments);
			unset($consol);
		}
	}

	public function update_tranship()
	{
		$ors = [];

		$or = OrgRate::model()->find('code = "FWSYD2020"');
		if (!empty($or)) $ors['FWSYD2020'] = $or;
		$or = OrgRate::model()->find('code = "FWSYD"');
		if (!empty($or)) $ors['FWSYD'] = $or;

		$or = OrgRate::model()->find('name = "eParcel ex GST 2019"');
		if (!empty($or)) $ors['eParcel ex GST 2019'] = $or;

		$or = OrgRate::model()->find('name = "StarTrack Road"');
		if (!empty($or)) $ors['StarTrack Road'] = $or;
		$or = OrgRate::model()->find('name = "StarTrack Mel"');
		if (!empty($or)) $ors['StarTrack Mel'] = $or;

		$or = OrgRate::model()->find('name = "TNT"');
		if (!empty($or)) $ors['TNT'] = $or;
		$or = OrgRate::model()->find('name = "TNT Melbourne"');
		if (!empty($or)) $ors['TNT Melbourne'] = $or;
		$or = OrgRate::model()->find('name = "TNT Brisbane"');
		if (!empty($or)) $ors['TNT Brisbane'] = $or;

		$org_id = $this->prompt('Org ID: ');
		$no = $this->prompt('No: ');

		if (empty($org_id) || $org_id == Org::ORGID_COURIER_FASTWAY) {
			$ts = Tranship::model()->findAll('org_id = :org_id AND cost = 0 AND time >= "2019-01-01"', [':org_id' => Org::ORGID_COURIER_FASTWAY]);
			foreach ($ts as $t) {
				if (!empty($no) && ($t->id % 5 != $no)) continue;
				if (strtotime($t->time) >= strtotime('2020-07-01')) $rate_id = $ors['FWSYD2020']->id;
				else $rate_id = $ors['FWSYD']->id;
				if (empty($t->shipment)) {
					$sql = 'select weight from shipment_archive WHERE hbn = :n OR ref = :n';
					$weight = Yii::app()->db->createCommand($sql)->bindValues([':n' => $t->connote])->queryScalar();
				} else {
					$weight = $t->shipment->weight;
				}
				$t->cost = Shipment::getCourierCostByShipment($t->connote, $rate_id, $weight)['price'];
				$t->update('cost');
			}
		}

		if (empty($org_id) || $org_id == Org::ORGID_COURIER_AUPOST) {
			$ts = Tranship::model()->findAll('org_id = :org_id AND cost = 0 AND man_id != 0 AND time >= "2019-01-01"', [':org_id' => Org::ORGID_COURIER_AUPOST]);
			foreach ($ts as $t) {
				if (!empty($no) && ($t->id % 5 != $no)) continue;
				$rate_id = $ors['eParcel ex GST 2019']->id;
				if (empty($t->shipment)) {
					$sql = 'select weight from shipment_archive WHERE hbn = :n OR ref = :n';
					$weight = Yii::app()->db->createCommand($sql)->bindValues([':n' => $t->connote])->queryScalar();
				} else {
					$weight = $t->shipment->weight;
				}
				$t->cost = Shipment::getCourierCostByShipment($t->connote, $rate_id, $weight)['price'];
				$t->update('cost');
			}
		}

		if (empty($org_id) || $org_id == Org::ORGID_COURIER_STARTRACK) {
			$ts = Tranship::model()->findAll('org_id = :org_id AND cost = 0 AND man_id != 0 AND time >= "2019-01-01"', [':org_id' => Org::ORGID_COURIER_STARTRACK]);
			foreach ($ts as $t) {
				if (!empty($no) && ($t->id % 5 != $no)) continue;
				if (preg_match('/7RFZ/', $t->connote)) $rate_id = $ors['StarTrack Road']->id;
				else $rate_id = $ors['StarTrack Mel']->id;
				if (empty($t->shipment)) {
					$sql = 'select weight from shipment_archive WHERE hbn = :n OR ref = :n';
					$weight = Yii::app()->db->createCommand($sql)->bindValues([':n' => $t->connote])->queryScalar();
				} else {
					$weight = $t->shipment->weight;
				}
				$t->cost = Shipment::getCourierCostByShipment($t->connote, $rate_id, $weight)['price'];
				$t->update('cost');
			}
		}

		if (empty($org_id) || $org_id == Org::ORGID_COURIER_TNT) {
			$ts = Tranship::model()->findAll('org_id = :org_id AND cost = 0 AND man_id != 0 AND time >= "2019-01-01"', [':org_id' => Org::ORGID_COURIER_TNT]);
			foreach ($ts as $t) {
				if (!empty($no) && ($t->id % 5 != $no)) continue;
				if (preg_match('/DKC/', $t->connote)) $rate_id = $ors['TNT']->id;
				else if (preg_match('/PCD/', $t->connote)) $rate_id = $ors['TNT Melbourne']->id;
				else $rate_id = $ors['TNT Brisbane']->id;
				if (empty($t->shipment)) {
					$sql = 'select weight from shipment_archive WHERE hbn = :n OR ref = :n';
					$weight = Yii::app()->db->createCommand($sql)->bindValues([':n' => $t->connote])->queryScalar();
				} else {
					$weight = $t->shipment->weight;
				}
				$t->cost = Shipment::getCourierCostByShipment($t->connote, $rate_id, $weight)['price'];
				$t->update('cost');
			}
		}
	}

	public function reread_easyship()
	{
		$date = date('Y-m-d');
		$errors = [];
		for ($i = 0; $i < 2; $i++) {
			$url = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'wmsapi' . DIRECTORY_SEPARATOR . 'wms_api_' . date('Y-m-d', strtotime($date . ' - ' . $i . ' days')) . '.log';
			if (!is_file($url)) continue;

			$content = file_get_contents($url);
			$lines = explode("\n", $content);
			$orders = [];
			foreach ($lines as $line) {
				if ($line == '') continue;
				$data = substr($line, 20);
				if ($data == '') continue;
				$data = json_decode($data, true);
				if (empty($data['orders'])) continue;
				if (empty($data['customer'])) continue;
				foreach ($data['orders'] as $order) {
					$orders[] = $order;
				}
			}

			$out = ['orders' => []];

			$odr_job_type_map = function ($t) {
				if ($t == 20) return 10;
				else return 20;
				// return in_array($t, [20, 30]) ? 10 : 30;
			};

			$odr_att_type_map = function ($t) {
				$rt = 84;
				switch ($t) {
					case 10:
						$rt = 85;
						break;
					case 20:
						$rt = 86;
						break;
					case 30:
						$rt = 87;
						break;
				}
				return $rt;
			};
			
			$mapFileType = function ($t) {
				switch ($t) {
					case 'ShippingLabel':
						$r = 10;
						break;
					case 'CommercialInvoice':
						$r = 20;
						break;
					case 'PackingSlip':
						$r = 30;
						break;
					default:
						$r = 90;
						break;
				}
				return $r;
			};

			$user = User::model()->findByPk(1250);

			foreach ($orders as $odr) {
				$temp_err = [];
				$od = [
					'no' => $odr['orderNo'],
					'type' => 10,
					'confirmed' => true,
					'items' => [],
					'to' => [
						'name' => @$odr['orderConsigneeName'],
						'company' => @$odr['orderConsigneeCompany'],
						'state' => @$odr['orderConsigneeState'],
						'suburb' => @$odr['orderConsigneeCity'],
						'postcode' => @$odr['orderConsigneePostalCode'],
						'address' => @$odr['orderConsigneeAddr1'] . (empty($odr['orderConsigneeAddr2']) ? '' : ' ' . $odr['orderConsigneeAddr2']),
						'phone' => @$odr['orderConsigneePhone'],
						'email' => @$odr['orderConsigneeEmail'],
						'country' => @$odr['orderConsigneeCountry'],
					],
					'attachments' => [],
				];

				if (!empty($odr['sourceLinkId'])) {
					$od['sourceLinkId'] = $odr['sourceLinkId'];
					if (!empty($odr['orderSPT'])) {
						$od['orderSPT'] = $odr['orderSPT'];
					}
				}

				$this->_mapFields($odr, $od, ['courierName' => 'freight_co', 'courierBillNo' => 'connote_no', 'remarkforDelivery' => 'shipping_note', 'orderTotalAmt' => 'total']);

				//items
				foreach ($odr['parts'] as $p) {
					$this->_mapFields($p, $od['items'][], ['partNo' => 'ean', 'vendorCode' => 'sku', 'vendorName' => 'name', 'partQty' => 'qty', 'PartUnitPrice' => 'price']);
				}

				//attachments
				if (!empty($odr['orderFiles'])) {
					foreach ($odr['orderFiles'] as $f) {
						$od['attachments'][] = [
							'type' => $mapFileType(@$f['usage']),
							'name' => empty($f['fileName']) ? @$f['usage'] . '.' . @$f['format'] : $f['fileName'],
							'content' => @$f['baseContent'],
						];
					}
				}

				$odr = $od;

				if (empty($odr['no'])) {
					$errors[] = ['no' => '', 'code' => 110, 'message' => 'Missing Order No'];
					continue;
				}

				//pick/create job
				$job = WmsJob::model()->find('org_id = :org_id AND type = :t AND date(`created`) = :day', [':org_id' => $user->org_id, ':t' => $odr_job_type_map($odr['type']), ':day' => date('Y-m-d')]);

				if (empty($job)) {
					$job = new WmsJob;
					$job->org_id = $user->org_id;
					$job->type = $odr_job_type_map($odr['type']);
					$job->status = 10;
					$job->ref = '3PL_' . date('Y-m-d');
					$job->save();
				}

				//group item
				//group item
				$items = [];
				$not_found = [];
				foreach ($odr['items'] as $itm) {
					$prod = null;
					// find product
					if (empty($itm['ean']) && empty($itm['sku'])) {
						$errors[] = ['no' => '', 'code' => 110, 'message' => 'Order EAN / SKU info incomplete'];
						continue;
					}

					// find ean first
					if (!empty($itm['ean'])) {
						$prod = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $itm['ean']]);
					}
					// then find sku
					if (!empty($itm['sku'])) {
						$wpo = WmsProdOrg::model()->with('prod')->find('prod.status = 1 AND t.org_id = :org_id AND (t.sku = :sku)', [':org_id' => $user->org_id, ':sku' => $itm['sku']]);
						if (!empty($wpo)) {
							$prod = $wpo->prod;
						}
					}

					// life style barcode and sku is mixed up, need to check stock
					if ($user->org_id == Org::ORGID_3PL_LIFESTYLE) {
						$have_stock = false;
						$temp_prod = null;
						if (!empty($itm['sku'])) {
							// find sku in sku
							$wpo = WmsProdOrg::model()->with('prod')->find('prod.status = 1 AND t.org_id = :org_id AND (t.sku = :sku)', [':org_id' => $user->org_id, ':sku' => $itm['sku']]);
							if (!empty($wpo->prod)) {
								$prod = $wpo->prod;
								$temp_prod = $prod;
								$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $prod->id, ':org_id' => $user->org_id]]);
								if ($stock->qty - $stock->qty_res > $itm['qty']) {
									$have_stock = true;
								}
							}
							// find sku in ean
							if ($have_stock == false || empty($prod)) {
								$prod = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $itm['sku']]);
								if (!empty($prod)) {
									$temp_prod = $prod;
									$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $prod->id, ':org_id' => $user->org_id]]);
									if ($stock->qty - $stock->qty_res > $itm['qty']) {
										$have_stock = true;
									}
								}
							}
						}
						if ($have_stock == false && !empty($itm['ean'])) {
							// find ean in ean
							$prod = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $itm['ean']]);
							if (!empty($prod)) {
								$temp_prod = $prod;
								$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $prod->id, ':org_id' => $user->org_id]]);
								if ($stock->qty - $stock->qty_res > $itm['qty']) {
									$have_stock = true;
								}
							}
							// find ean in sku
							if ($have_stock == false || empty($prod)) {
								$wpo = WmsProdOrg::model()->with('prod')->find('prod.status = 1 AND t.org_id = :org_id AND (t.sku = :sku)', [':org_id' => $user->org_id, ':sku' => $itm['ean']]);
								if (!empty($wpo->prod)) {
									$prod = $wpo->prod;
									$temp_prod = $prod;
									$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $prod->id, ':org_id' => $user->org_id]]);
									if ($stock->qty - $stock->qty_res > $itm['qty']) {
										$have_stock = true;
									}
								}
							}
						}
						if (empty($prod)) {
							$prod = $temp_prod;
						}
					}

					if (empty($itm['ean'])) {
						$itm['ean'] = $itm['sku'];
					}
					if (empty($prod)) {
						$errors[] = ['no' => $odr['no'], 'code' => 201, 'message' => 'Product not found EAN ' . $itm['ean'] . (!empty($itm['sku']) ? ' / SKU ' . $itm['sku'] : '')];
						$temp_err[] = ['no' => $odr['no'], 'code' => 201, 'message' => 'Product not found EAN ' . $itm['ean'] . (!empty($itm['sku']) ? ' / SKU ' . $itm['sku'] : '')];
						if (empty($not_found[$itm['ean']])) $not_found[$itm['ean']] = 0;
						$not_found[$itm['ean']] += intval($itm['qty']);
					} else {
						if (!isset($items[$prod->id])) {
							$items[$prod->id] = 0;
						}

						if ($prod->type != WmsProd::WMS_PROD_KIT) {
							$items[$prod->id] += intval($itm['qty']);
						} else if ($prod->type == WmsProd::WMS_PROD_KIT) {
							foreach ($prod->items as $item) {
								if (empty($items[$item->item->id])) $items[$item->item->id] = 0;
								$items[$item->item->id] += intval($itm['qty']) * $item->qty;
							}
						}
					}
				}

				//check stock
				$new_items = [];
				foreach ($items as $pid => $qty) {
					$stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $pid, ':org_id' => $user->org_id]]);
					if ($stock->qty - $stock->qty_res < $qty) {
						$prod = WmsProd::model()->findByPk($pid);
						$errors[] = ['no' => $odr['no'], 'code' => 203, 'message' => $prod->ean . ' not enough stock (' . $qty . ' < ' . ($stock->qty - $stock->qty_res) . ')'];
						$temp_err[] = ['no' => $odr['no'], 'code' => 203, 'message' => $prod->ean . ' not enough stock (' . $qty . ' < ' . ($stock->qty - $stock->qty_res) . ')'];
						$new_items[] = [
							'si' => '',
							'sn' => $prod->name,
							'pq' => '',
							'cq' => '',
							'uq' => $qty,
							'pli' => '',
							'pl' => '',
							'nt' => '',
							'pi' => $prod->id,
						];
					} else {
						$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id AND qty - qty_res > 0', [':prod_id' => $pid, ':org_id' => $user->org_id]);
						foreach ($stocks as $stock) {
							if ($qty <= 0) {
								break;
							}
							$uq = min($qty, $stock->availQty());
							$new_items[] = [
								'si' => $stock->id,
								'sn' => $stock->prod->name,
								'pq' => '',
								'cq' => '',
								'uq' => $uq,
								'pli' => '',
								'pl' => '',
								'nt' => '',
								'pi' => $stock->prod->id,
							];
							$qty -= $uq;
						}
					}
				}
				foreach ($not_found as $ean => $qty) {
					$new_items[] = [
						'si' => '',
						'sn' => $ean,
						'pq' => '',
						'cq' => '',
						'uq' => $qty,
						'pli' => '',
						'pl' => '',
						'nt' => '',
						'pi' => '',
					];
				}

				$create = true;
				if (!empty($temp_err)) {
					// easyship if stock not enough still create, but do not occupy stock
					foreach ($new_items as $k => $item) {
						$new_items[$k]['si'] = '';
					}
				}

				if (empty($odr['to']['country'])) {
					$odr['to']['country'] = 'AU';
				} else if ($odr['to']['country'] == '中国') {
					$odr['to']['country'] = 'CN';
				}

				$odr['to']['phone'] = trim($odr['to']['phone']);

				if ($create) {
					//create main task
					$task = WmsTask::model()->with('job')->find('t.ref = :ref AND job.org_id = :org_id AND t.status < 100', [':org_id' => $user->org_id, ':ref' => $odr['no']]);
					if (empty($task)) {
						$task = new WmsTask;
						$task->job_id = $job->id;
						$task->type = WmsTask::TYPE_PICK_UNIT;
						$task->is_request = 1;
						$task->op_id = 0;
						$task->status = empty($temp_err) ? 20 : 10;
						$task->ref = $odr['no'];
						$task->new_items = $new_items;
						$data['api_hook'] = 'ufl';
						$data['api_meta'] = ['customer' => $data['customer']];
						if (!empty($data['api_hook'])) {
							$task->mdata['api_hook'] = $data['api_hook'];
							if (!empty($data['api_meta'])) {
								$task->mdata['api_meta'] = $data['api_meta'];
							}
						}
						$task->mdata['errs'] = $temp_err;
						$task->mdata['reread'] = true;
						$task->save();

						$task->pickupTask->type = WmsTask::TYPE_DELIVERY;
						$delivery_task = $task->pickupTask;
						$delivery_task->ref = @$odr['connote_no'];
						$delivery_task->mdata['cnee'] = $odr['to'];
						$delivery_task->save();
						
						//attachments
						if (!empty($odr['attachments'])) {
							foreach ($odr['attachments'] as $att) {
								FileRepo::storeBase64File($att['content'], $att['name'], $odr_att_type_map($att['type']), $task->id);
							}
						}

						$out['orders'][] = ['no' => $odr['no'], 'id' => $task->id, 'status' => 20];
					} else {
						$errors[] = ['no' => $odr['no'], 'code' => 101, 'message' => 'Order already exists'];
						$temp_err[] = ['no' => $odr['no'], 'code' => 101, 'message' => 'Order already exists'];
					}
				}
			}

			if (!empty($errors)) $this->log(json_encode($errors));
		}
	}

	public function SendManualCreditNoteDailyTLA()
	{
		[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
		$tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		if (!file_exists($tempDirectory)) {
			mkdir($tempDirectory);
		}
		$creditNotes=Payment::model()->findAll('bank=91 AND type=5 AND status=6 AND flag&1>0');
		foreach ($creditNotes as $credit) {
			$fileName=$tempDirectory.DIRECTORY_SEPARATOR.'CreditNOte_'.$credit->no.'.pdf';

			oPDF::renderPDF('credit_note', ['credit' => $credit], 2, $fileName);
			$emailLog = new Emailog();
			$emailLog->type = 60;
			$emailLog->fid = $credit->id;
			$emailLog->dt = date('Y-m-d H:i:s');
			$emailLog->to_id = $credit->org_id;
			$emailLog->status = 10;
			$emailLog->prepTemplate();
			$emailLog->subject = $emailLog->tpl->subject;
			$emailLog->body = $emailLog->tpl->getContent();
			if ($emailLog->InvoiceSend($fileName)) {
				$credit->flag=($credit->flag&1)^$credit->flag;
				$credit->flag=$credit->flag|2;
				$credit->update('flag');
				// $this->log2file('Credit '.$credit->no.'send!', 'Credit_sending');
			}
		}
		AppHelper::unlinkRecursive($tempDirectory);
		Yii::app()->name = $app_name;
	}

}
