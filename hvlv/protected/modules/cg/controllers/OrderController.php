<?php

class OrderController extends Controller{
	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax = array('list','make','checkOrgLimits');
    protected $skipAcl = ['supay'];

	protected $org, $type;


    /**
     * @param CAction $action
     * @return bool
     */
	public function beforeAction($action){

        if (!empty($_GET['redirect'])) {
            $this->layout = $_GET['redirect'];
            Yii::app()->user->setState('redirect', $_GET['redirect']);
        } else {
            if(!Yii::app()->request->isAjaxRequest) $this->layout = 'cg';
        }

		if(!Yii::app()->user->isGuest){
			$this->org = Org::model()->findByPk(Yii::app()->user->org);
            $this->type = 'sc';
		}
		return parent::beforeAction($action);
	}

    /**
     * @param $id
     */
    public function actionDeliverying($id){
        $resp = array('success' => 1,'msg' => '' );
        $order = CgOrder::model()->findByPk($id);
        if ( !empty($order) ) {
            //if ( $order->status == CgOrder::CGORDER_STATUS_PROCESSING )
            //{
            //    $order->status = CgOrder::CGORDER_STATUS_DELIVERING;
            //    $order->update('status');
           // }
            $order->delivery_by = !empty(Yii::app()->user) ? Yii::app()->user->id : 0;
            $order->update('delivery_by');
        }
        $resp['msg'] = 'Order has been taken successfully';
        echo json_encode($resp);
    }

    /**
     * @param $id
     */
    public function actionDetails($id) {
        $task = WmsTask::model()->findByPk($id);

        if (!empty($_GET['pay']) && $_GET['pay']) {
            $inv = Invoice::model()->with('lines')->find('lines.fid = :task_id', array(':task_id' => $id));
            $this->redirect($this->createUrl('order/finish', array('invoice' => $inv->no)));
        }

        if ($task) {
            $org = Org::model()->findByPk(Yii::app()->user->org);
            $discount = !empty($org->extra['rebate_apply']) ? (100-floatval($org->extra['rebate_rate']))/100 : 1;
            $products = [];
            $total = 0;
            foreach ($task->items as $item) {
                $stock = WmsStock::model()->findByPk($item->mdata['si']);
                if ($stock->prod->mdata['price']) {
                    $products[] = array('id' => $stock->prod->id, 'name' => $stock->prod->name, 'ean' => $stock->prod->ean, 'moq' => $stock->prod->mdata['moq'], 'qty' => $item->mdata['uq'] / $stock->prod->mdata['moq'], 'tqty' => $item->mdata['uq'], 'price' => round($stock->prod->mdata['price'] * $discount, 2), 'subt' => round($item->mdata['uq'] * $stock->prod->mdata['price'] * $discount, 2));
                } else {
                    if (preg_match('/热敏/', $stock->prod->name) && !empty($org->extra['thermal'])) {
                        $products[] = array('id' => $stock->prod->id, 'name' => $stock->prod->name, 'ean' => $stock->prod->ean, 'moq' => $stock->prod->mdata['bqtf'], 'qty' => $item->mdata['uq'], 'tqty' => $item->mdata['uq'] . ($task->mdata['thermal_free'] ? ' (免费配送' . $task->mdata['thermal_free'] . ')' : ''), 'price' => round($stock->prod->mdata['price2'] * $discount, 2), 'subt' => round($item->mdata['uq'] * $stock->prod->mdata['price2'] * $discount, 2) . ($task->mdata['thermal_free'] ? ' (-$ ' . round($task->mdata['thermal_free'] * $stock->prod->mdata['price2'] * $discount, 2) . ')' : ''));
                    } else if (preg_match('/缠绕膜/', $stock->prod->name) && !empty($org->extra['wrapper'])) {
                        $products[] = array('id' => $stock->prod->id, 'name' => $stock->prod->name, 'ean' => $stock->prod->ean, 'moq' => $stock->prod->mdata['bqtf'], 'qty' => $item->mdata['uq'], 'tqty' => $item->mdata['uq'] . ($task->mdata['wrapper_free'] ? ' (免费配送' . $task->mdata['wrapper_free'] . ')' : ''), 'price' => round($stock->prod->mdata['price2'] * $discount, 2), 'subt' => round($item->mdata['uq'] * $stock->prod->mdata['price2'] * $discount, 2) . ($task->mdata['wrapper_free'] ? ' (-$ ' . round($task->mdata['wrapper_free'] * $stock->prod->mdata['price2'] * $discount, 2) . ')' : ''));
                    } else if (preg_match('/透明胶带/', $stock->prod->name) && !empty($org->extra['tape'])) {
                        $products[] = array('id' => $stock->prod->id, 'name' => $stock->prod->name, 'ean' => $stock->prod->ean, 'moq' => $stock->prod->mdata['bqtf'], 'qty' => $item->mdata['uq'], 'tqty' => $item->mdata['uq'] . ($task->mdata['tape_free'] ? ' (免费配送' . $task->mdata['tape_free'] . ')' : ''), 'price' => round($stock->prod->mdata['price2'] * $discount, 2), 'subt' => round($item->mdata['uq'] * $stock->prod->mdata['price2'] * $discount, 2) . ($task->mdata['tape_free'] ? ' (-$ ' . round($task->mdata['tape_free'] * $stock->prod->mdata['price2'] * $discount, 2) . ')' : ''));
                    } else {
                        $products[] = array('id' => $stock->prod->id, 'name' => $stock->prod->name, 'ean' => $stock->prod->ean, 'moq' => $stock->prod->mdata['bqtf'], 'qty' => $item->mdata['uq'], 'tqty' => $item->mdata['uq'], 'price' => round($stock->prod->mdata['price2'] * $discount, 2), 'subt' => round($item->mdata['uq'] * $stock->prod->mdata['price2'] * $discount, 2));
                    }
                }
            }
            $delivery_task = WmsTask::model()->find('link_id = :link_id AND type = :type', array(
                ':link_id' => $task->id,
                ':type' => array_search('Delivery', WmsTask::$types)
            ));
            $org->address = $delivery_task->mdata['cnee']['address'];
            $org->suburb = $delivery_task->mdata['cnee']['suburb'];
            $org->state = $delivery_task->mdata['cnee']['state'];
            $org->postcode = $delivery_task->mdata['cnee']['postcode'];
            $org->country = $delivery_task->mdata['cnee']['country'];
            $org->phone = $delivery_task->mdata['cnee']['tel'];
            $org->email = $delivery_task->mdata['cnee']['email'];
            $this->render('details', array('products' => $products, 'model' => $org, 'status' => $task->status, 'prodt' => $task->mdata['prodt'], 'freight' => $task->mdata['freight'], 'total' => $task->mdata['total']));
        } else {
            echo json_encode(array('done' => false, 'msg' => 'order is not exist'));
            return;
        }
    }

    // show order history
    public function actionHistory() {
        if( Yii::app()->user->isGuest){
            echo json_encode(array(
                'done' => false,
                'msg' => 'Please login firstly!'
            ));
            return;
        }

        $tasks = WmsTask::model()->findAll(array(
            'condition' => 'type = :type AND is_request = 1 AND meta like :org_id AND (status in (20, 30, 99) OR (status in (10) AND bwf&32 = 32))',
            'params' => array(
                ':type' => array_search('CG Delivery', WmsTask::$types),
                ':org_id' => '%"org":"' . (!empty($_POST['id']) ? $_POST['id'] : Yii::app()->user->org) . '"%'
            ),
            'order' => 'schd_time DESC'
        ));
        $temp_tasks = array();
        foreach ($tasks as $task) {
            if ($task->mdata['org'] == Yii::app()->user->org) {
                $temp_tasks[] = $task;
            }
        }
        $this->render('history', array('tasks' => $temp_tasks));
    }

    public function actionOwnerSuggest(){
        $this->suggest(array(15,30,50,60,65,70));
    }

    private function suggest($grp=''){
        if(empty($grp)){
            $rs = Org::model()->findAll(array(
                'condition' => 'status = 1 AND (name LIKE :n OR code LIKE :n OR id = :tn)',
                'params'=> array(':n' => '%'.$_GET['term'].'%', ':tn' => $_GET['term']),
                'order' => 'name',
                'limit' => 20,
            ));
        }elseif(is_array($grp)){
            $rs = Org::model()->findAll(array(
                'condition' => 'status = 1 AND type IN ('.implode(',', $grp).') AND (name LIKE :n OR code LIKE :n OR id = :tn)',
                'params' => array(':n' => '%'.$_GET['term'].'%', ':tn' => $_GET['term']),
                'order' => 'name',
                'limit' => 20,
            ));
        }else{
            $rs = Org::model()->findAll(array(
                'condition' => 'status = 1 AND type = :grp AND (name LIKE :n OR code LIKE :n OR id = :tn)',
                'params' => array(':n' => '%'.$_GET['term'].'%', ':grp' => $grp, ':tn' => $_GET['term']),
                'order' => 'name',
                'limit' => 20,
            ));
        }
        $a = array();

        foreach($rs as $r){
            $a[] = array(
                'value' => $r->id,
                'label' => $r->code.':'.$r->name,
            );
        }
        echo json_encode($a);
    }

    /**
     *
     */
    public function actionViewcorders(){
        if (Yii::app()->user->isGuest) {
            echo json_enocde(array('done' => false, 'msg' => 'Please login firstly'));
            return;
        }

        $tasks = WmsTask::model()->with('job')->findAll('t.type = :type and is_request = 1 and t.status = :status', array(
            ':type' => array_search('CG Delivery', WmsTask::$types),
            ':status' => array_search('WIP', WmsTask::$states)
        ));
        $this->render('corders', array('tasks' => $tasks));
    }

    /**
     *
     */
    public function actionConfirmDelivery(){
        $resp = array('done' => true ,'data' => '', 'msg' => 'Successfully');
        $no = isset($_POST['bc']) ? trim($_POST['bc']) : '';
        $task = WmsTask::model()->findByPk((int)substr($no,1));
        if ( empty($task) ) {
            $resp['done'] = false;
            $resp['msg'] = 'Order : ' . $no . ' Not Found';
        } else {
            $task->status = array_search('Completed', WmsTask::$states);
            if ( empty($task->mdata['delivery']) ){
                $task->mdata['delivery'] = Yii::app()->user->id;
            }
            $task->compl_time = date('Y-m-d h:i:s');
            $task->save();

            $resp['data'] = '<p>Order: ' . $no .' has been confirmed successfully<br>';
            $resp['data'] .= '</p>';
        }
        echo json_encode($resp);
    }

    /**
     *
     */
    public function actionMakedelivery(){

        if ( isset($_POST['barcode']) ) {
            $resp = array('done' => true ,'data' => '', 'msg' => 'Successfully');
            // based on order no to get order information
            $no = trim($_POST['barcode']);
            $task = WmsTask::model()->findByPk((int)substr($no,1));
            if ( empty($task) ) {
                $resp['done'] = false;
                $resp['msg'] = 'Order : ' . $no . ' Not Found';
            } else {
                if ($task->status == array_search('Completed', WmsTask::$states)) {
                    $resp['done'] = false;
                    $resp['msg'] = 'Order : ' . $no . ' is completed';
                } else {
                    $org = Org::model()->findByPk($task->mdata['org']);
                    $resp['data'] = '<p>Customer: ' . $org->id . ' - ' . $org->name . '<br>';
                    $resp['data'] .= 'Details:<br>';
                    foreach ($task->getItemsArray() as $item) {
                        $stock = WmsStock::model()->findByPk($item['si']);
                        $resp['data'] .= $stock->prod->name . ": " . $item['uq'] . "个<br>";
                    }
                    $resp['data'] .= '</p>';
                }
            }
            echo json_encode($resp);
            return;
        }
        $this->render('make_delivery');
    }


    public function actionCheckOrgLimits(){
        $resp = array('success' => 0 , 'data' => '','msg' => 'unknown error!','oidx' => 0);
        $org = Org::model()->findByPk($_POST['oid']);
        if ( empty($org) ) {
            $resp['msg'] = 'Org : ' . $_POST['oid'] . ' not existing';
        } else {
            $order = CgOrder::model()->find(
                [
                    'condition' => 'client_id = ' . $org->id . ' AND status = ' . CgOrder::CGORDER_STATUS_DELIVERING,
                ]
            );
            if (!empty($order)) {
                $data = '<p>';
                $data .= '<span style="font-size: 25px;color:#ff0000">Sorry your previous order as below is in delivering , you can not make a new order currently!</span> <br/><br/>';
                $data .= ' <span>Created: ' . $order->added_datetime .'</span> <br/>';
                $data .= '<span>Delivery: ' . $order->dispatch_date .'</span> <br/>';
                $data .= '<span>Amount: ' .  $order->amount . '</span> <br/>';
                $data .= '<span>Status: ' . $order->getStatus() .'</span> <br/><br/>';
                $odata = CgOrderLine::model()->findAll('cd_order_id = :oid', [':oid' => $order->id]);
                foreach ($odata as $line) {
                    $cgf = ConsumableGoods::model()->findByPk($line->cg_id);
                    $data .=  '<span>' . $cgf->name . ' : ' . $line->qty . ' </span><br/>';
                }
                $data .= '</p>';
                $resp['data'] = $data;

                $resp['success'] = 2;
            } else {
                $order = CgOrder::model()->find(
                    [
                        'condition' => 'client_id = ' . $org->id . ' AND status = ' . CgOrder::CGORDER_STATUS_NEW,
                    ]
                );
                if (!empty($order)) {
                    $data = '<p>';
                    $data .= '<span style="font-size: 25px;color:#ff0000">You have an order as below,the order will be updated</span> <br/><br/>';
                    $data .= ' <span>Created: ' . $order->added_datetime .'</span> <br/>';
                    $data .= '<span>Delivery: ' . $order->dispatch_date .'</span> <br/>';
                    $data .= '<span>Amount: ' .  $order->amount . '</span> <br/>';
                    $data .= '<span>Status: ' . $order->getStatus() .'</span> <br/><br/>';
                    $odata = CgOrderLine::model()->findAll('cd_order_id = :oid', [':oid' => $order->id]);
                    foreach ($odata as $line) {
                        $cgf = ConsumableGoods::model()->findByPk($line->cg_id);
                        $data .=  '<span>' . $cgf->name . ' : ' . $line->qty . ' </span><br/>';
                    }
                    $data .= '</p>';
                    $resp['data'] = $data;

                    $limits = ConsumableGoods::getOrgCgLimitations($org->id);
                    // get limitations
                    $limitsTips = '';
                    if ( !empty($limits) ) {
                        if ( $limits['box'] > 0 )
                        {
                            $limitsTips = '纸箱总共限购：' . $limits['box'] . '个';
                        }
                        if ( $limits['tape'] > 0 ) {
                            $limitsTips .= '<br>';
                            $limitsTips .= '胶带限购：' . $limits['tape'] . '卷';
                        }

                        if ( $limits['box'] == 0 ) {
                            $limitsTips .= '您已超过订购限额';
                        }

                    }

                    if ( !empty($limitsTips ) ) {
                        $resp['data'] .=  '<span style="color:#008000">'.$limitsTips.'</span><br>';
                    }

                    $resp['oidx'] = $order->id;
                    $resp['success'] = 3;
                } else {
                    $resp['success'] = 1;
                    $limits = ConsumableGoods::getOrgCgLimitations($org->id);
                    // get limitations
                    $limitsTips = '';
                    if ( !empty($limits) ) {
                        if ( $limits['box'] > 0 ) {
                            $limitsTips = '纸箱总共限购：' . $limits['box'] . '个';
                        }
                        if ( $limits['tape'] > 0 ) {
                            $limitsTips .= '<br>';
                            $limitsTips .= '胶带限购：' . $limits['tape'] . '卷';
                        }
                        if ( $limits['box'] == 0 ) {
                            $limitsTips .= '您已超过订购限额';
                        }
                    }

                    if ( !empty($limitsTips ) ) {
                        $resp['data'] =  '<span style="color:#008000">'.$limitsTips.'</span><br>';
                    }
                }
            }
        }

        echo json_encode($resp);
    }

    /**
     * show all awb related console scan statistic information
     */
    public function actionMake() {
        if( Yii::app()->user->isGuest){
            $this->redirect($this->createUrl('site/index'));
        }

        if (!empty(Yii::app()->user->incomplete)) {
            $this->redirect($this->createUrl('site/index'));
        }

        $org = Org::model()->findByPk(Yii::app()->user->org);
        
        $products = WmsProd::model()->findAll('type = 20 AND status = 1');

        $discount = !empty($org->extra['rebate_apply']) ? (100-floatval($org->extra['rebate_rate']))/100 : 1;

        if (!isset($_POST['Org'])) {
            $this->render('make', array_merge($org->cgLimit(), array('products' => $this->objects_sort($products, 'mdata-order'), 'model' => $org, 'freight_free_above' => Yii::app()->params['settings']['freight_free_above']['value'], 'freight_charge_fee' => Yii::app()->params['settings']['freight_charge_fee']['value'], 'discount' => $discount)));
        } else {
            if (empty($_POST['Org']) || empty($_POST['Org']['name']) || empty($_POST['Org']['address']) || empty($_POST['Org']['suburb']) || empty($_POST['Org']['state']) || empty($_POST['Org']['postcode']) || empty($_POST['Org']['phone'])) {
                $this->redirect($this->createUrl('order/finish', array('result' => 'fail', 'msg' => 'Please complete delivery information')));
                return;
            }

            $qtys = $_POST['qty'];

            if (array_sum($qtys) == 0) {
                $this->redirect($this->createUrl('order/finish', array('result' => 'fail', 'msg' => 'Product quantities in total is 0')));
                return;
            }

            // create job
            $job = new WmsJob();
            $job->org_id = Yii::app()->user->org;
            $job->type = array_search('CG Delivery', WmsJob::$types);
            $job->status = array_search('New', WmsJob::$states);
            $job->save();

            // create task
            $task = new WmsTask();
            $task->job_id = $job->id;
            $task->status = array_search('New', WmsTask::$states);
            $task->is_request = 1;
            $task->type = array_search('CG Delivery', WmsTask::$types);
            $task->schd_time = date('YmdHis');
            $amount = 0;
            $boxq = 0;
        	$limits = $org->cgLimit();
            foreach ($qtys as $id => $qty) {
                if ($qty) {
                    $product = WmsProd::model()->findByPk($id);
                    if (preg_match('/PCAE 手写面单/', $product->name) && $qty <= 50) {
                        $this->redirect($this->createUrl('order/finish', array('result' => 'fail', 'msg' => '手写面单订购数仅为: ' . $qty . '张，请填写完整数量')));
                        return;
                    }
                    $stocks = WmsStock::model()->findAll('prod_id = :prod_id', array(':prod_id' => $id));
                    if ($product->mdata['moq']) {
                        $qty = $qty * $product->mdata['moq'];
                    }
                    foreach ($stocks as $stock) {
                        if ($qty) {
                            $task->new_items[] = array(
                                'si' => $stock->id,
                                'sn' => $stock->prod->name,
                                'pq' => '',
                                'cq' => '',
                                'uq' => ($qty > ($stock->qty - $stock->qty_res) ? ($stock->qty - $stock->qty_res) : $qty),
                                'pli' => '',
                                'pl' => '',
                                'nt' => ''
                            );
                            $qty -= $qty > ($stock->qty - $stock->qty_res) ? ($stock->qty - $stock->qty_res) : $qty;
                        } else {
                            break;
                        }
                    }
                    if ($qty) {
                        $job->delete();
                        $this->redirect($this->createUrl('order/finish', array('result' => 'fail', 'msg' => $product->name . ' is out of stock, you can only order ' . (($product->mdata['moq'] ? $qtys[$id] * $product->mdata['moq'] : $qtys[$id]) - $qty))));
                        return;
                    }

                    if (preg_match('/纸箱/', $product->name)) {
                        $boxq += floatval($qtys[$id]) * floatval($product->mdata['moq']);
                    }
                    if (!empty($product->mdata['price'])) {
                        $amount += floatval($qtys[$id]) * floatval($product->mdata['moq']) * round(floatval($product->mdata['price']) * $discount, 2);
                    } else {
                        if (preg_match('/热敏/', $product->name) && !empty($org->extra['thermal'])) {
                            $thermal_free = min(floor(($boxq + $limits['thermal_credit']) / $product->mdata['bqtf']) - $limits['thermal_limit'], floatval($qtys[$id]) * floatval($product->mdata['moq']));
                            $thermal_free = max($thermal_free, 0);
                            $amount -= $thermal_free * round(floatval($product->mdata['price2']) * $discount, 2);
                        } else if (preg_match('/缠绕膜/', $product->name) && !empty($org->extra['wrapper'])) {
                            $wrapper_free = min(floor(($boxq + $limits['thermal_credit']) / $product->mdata['bqtf']) - $limits['wrapper_limit'], floatval($qtys[$id]) * floatval($product->mdata['moq']));
                            $wrapper_free = max($wrapper_free, 0);
                            $amount -= $wrapper_free * round(floatval($product->mdata['price2']) * $discount, 2);
                        } else if (preg_match('/透明胶带/', $product->name) && !empty($org->extra['tape'])) {
                            $tape_free = min(floor(($boxq + $limits['tape_credit']) / $product->mdata['bqtf']) - $limits['tape_limit'], floatval($qtys[$id]) * floatval($product->mdata['moq']));
                            $tape_free = max($tape_free, 0);
                            $amount -= $tape_free * round(floatval($product->mdata['price2']) * $discount, 2);
                        }
                        $amount += floatval($qtys[$id]) * floatval($product->mdata['moq']) * round(floatval($product->mdata['price2']) * $discount, 2);
                    }
                }
            }

            $freight = ($amount >= Yii::app()->params['settings']['freight_free_above']['value'] || $amount == 0) ? 0 : Yii::app()->params['settings']['freight_charge_fee']['value'];

            if ($amount == 0 && empty($qtys)) {
                $job->delete();
                $this->redirect($this->createUrl('order/finish', array('result' => 'fail', 'msg' => 'Product quantities in total is 0')));
                return;
            }

            $flag = 0;
            if (!empty($org->extra['consum_postpay']) && $_POST['optionRadio'] == 'credit') {
                $previous_tasks = WmsTask::model()->findAll('ref = :name AND type = 5010 AND status in (20,30)', array(':name' => $org->name . ' ' . $org->id));
                $invoices = array();
                foreach ($previous_tasks as $previous_task) {
                    $invoice = InvLine::model()->find('fid = :task_id AND det = :det', array(':task_id' => $previous_task->id, ':det' => 'CG Delivery'))->invoice;
                    if ($previous_task->mdata['org'] == $org->id && $invoice->status == Invoice::INVOICE_STATUS_POSTED) {
                        $flag ++;
                        $invoices[] = $invoice->no;
                        if ($flag >= 2) {
                            $job->delete();
                            $this->redirect($this->createUrl('order/finish', array('result' => 'fail', 'msg' => 'Previous orders ' . implode(',', $invoices) . ' paid by Credit have not been finished, please wait them to be completed.')));
                            return;
                        }
                    }
                }
            } else if ($_POST['optionRadio'] == 'trans') {
                $previous_tasks = WmsTask::model()->findAll('ref = :name AND type = 5010 AND status in (10) AND bwf & 32 = 32', array(':name' => $org->name . ' ' . $org->id));
                foreach ($previous_tasks as $previous_task) {
                    $invoice = InvLine::model()->find('fid = :task_id AND det = :det', array(':task_id' => $previous_task->id, ':det' => 'CG Delivery'))->invoice;
                    $job->delete();
                    $this->redirect($this->createUrl('order/finish', array('result' => 'fail', 'msg' => 'Previous order ' . $invoice->no . ' paid by 转账 has not been finished, please wait them to be completed.')));
                    return;
                }
            }

            $task->mdata = array('prodt' => round($amount, 2), 'freight' => $freight, 'total' => round($amount + $freight, 2), 'org' => $org->id, 'thermal_free' => !empty($thermal_free) ? $thermal_free : 0, 'wrapper_free' => !empty($wrapper_free) ? $wrapper_free : 0, 'tape_free' => !empty($tape_free) ? $tape_free : 0);
            $task->ref = $org->name . ' ' . $org->id;
            $task->save();

            // delivery task
            $delivery_task = WmsTask::model()->find('link_id = :link_id and type = :type', array(':link_id' => $task->id, ':type' => array_search('Pickup', WmsTask::$types)));
            $delivery_task->type = array_search('Delivery', WmsTask::$types);
            $delivery_task->mdata['courier'] = "114";
            $cnee = $_POST['Org'];
            $delivery_task->mdata['cnee']['company'] = $cnee['name'];
            $delivery_task->mdata['cnee']['name'] = $cnee['name'];
            $delivery_task->mdata['cnee']['tel'] = $cnee['phone'];
            $delivery_task->mdata['cnee']['address'] = $cnee['address'];
            $delivery_task->mdata['cnee']['city'] = $cnee['suburb'];
            $delivery_task->mdata['cnee']['suburb'] = $cnee['suburb'];
            $delivery_task->mdata['cnee']['state'] = $cnee['state'];
            $delivery_task->mdata['cnee']['postcode'] = $cnee['postcode'];
            $delivery_task->mdata['cnee']['country'] = 'AU';
            $delivery_task->mdata['cnee']['email'] = $cnee['email'];
            $delivery_task->save();

            $total_amount = $amount + $freight;
            if (!empty($_GET['test']) && $_GET['test'] === '54gvvfbj4fwfg4j4wgcdvbw45w34gh5') {
                $total_amount = 0.01;
            }

            if (!empty($org->extra['consum_postpay']) && $_POST['optionRadio'] == 'credit') {
                $this->afterPayedByCredit($task->id);
            } else if ($_POST['optionRadio'] == 'trans') {
                $this->afterPayedByTrans($task->id);
            } else if ($_POST['optionRadio'] == 'poli') {
                $this->redirect($this->createPoliLink(array('task_id' => $task->id, 'amount' => $total_amount)));
            } else if ($_POST['optionRadio'] == 'paypal') {
                $call_back_root = Yii::app()->request->hostInfo . $this->createUrl('order/paypal') . '?id=' . $task->id . '&op=';
                $callback_success_url = $call_back_root . 'success';
                $callback_cancel_url = $call_back_root . 'cancel';
                $paypal = new PayPalApi(
                    $callback_success_url,
                    $callback_cancel_url
                );
                $this->redirect($paypal->ExpressCheckOut(array(array('name' => 'comsumable goods', 'price' => $total_amount, 'qty' => 1))));
            } else if ($_POST['optionRadio'] == 'alipay' || $_POST['optionRadio'] == 'wechatpay') {
                $callback_notification_url = Yii::app()->request->hostInfo . $this->createUrl('order/supay');
                $callback_return_url = Yii::app()->request->hostInfo . $this->createUrl('order/finish');
                $supay = new SupayAPI(
                    $callback_notification_url,
                    $callback_return_url,
                    '30810',
                    '56175fb50d4a8040cc782c35040fadcf'
                );

                if ($_POST['optionRadio'] == 'alipay') {
                    $this->redirect($supay->checkoutByAliPay('comsumable goods', $task->id, 'AUD', $total_amount));
                } else {
                    $this->redirect($supay->checkoutByWechatPay('comsumable goods', $task->id, 'AUD', $total_amount));
                }
            }
        }
    }

    public function actionInvoice() {
        if( Yii::app()->user->isGuest || !empty(Yii::app()->user->incomplete)){
            $this->redirect($this->createUrl('site/index'));
        }

        if (!empty($_GET['id'])) {
            $task_id = $_GET['id'];
            $il = InvLine::model()->find('fid = :task_id', array(':task_id' => $_GET['id']));
            oPDF::renderPDF('invoice', array('inv'=>$il->invoice), 1, 'Invoice_'.$il->invoice->no.'.pdf');
        }
    }

    /**
     * Credit
     */
    private function afterPayedByCredit($task_id) {
        $task = WmsTask::model()->findByPk($task_id);
        $org = Org::model()->findByPk(Yii::app()->user->org);

        $invoice_model = $this->generateInvoice($task, $org);
        // get original invoice data
        if (!empty($invoice_model)) {
            // not actually paid, use credit
            $invoice_model->status = Invoice::INVOICE_STATUS_POSTED;
            $invoice_model->save();

            // update task
            $task = WmsTask::model()->findByPk($invoice_model->lines[0]->fid);
            $task->status = array_search('Scheduled', WmsTask::$states);
            $task->save();
            $boxq = 0;
            foreach ($task->items as $item) {
                $item->toStock();
                if (preg_match('/纸箱/', $item->mdata['sn'])) {
                    $boxq += $item->mdata['uq'];
                }
            }
            $thermal_prod = WmsProd::model()->find('name like "%热敏面单%"');
            $wrapper_prod = WmsProd::model()->find('name like "%缠绕膜%"');
            $tape_prod = WmsProd::model()->find('name like "%透明胶带%"');
            if (!empty($org->extra['thermal'])) {
                $org->extra['thermal_credit'] = $org->extra['thermal_credit'] + $boxq - $task->mdata['thermal_free'] * $thermal_prod->mdata['bqtf'];
                $org->sync_xero = false;
                $org->save();
            }
            if (!empty($org->extra['wrapper'])) {
                $org->extra['wrapper_credit'] = $org->extra['wrapper_credit'] + $boxq - $task->mdata['wrapper_free'] * $wrapper_prod->mdata['bqtf'];
                $org->sync_xero = false;
                $org->save();
            }
            if (!empty($org->extra['tape'])) {
                $org->extra['tape_credit'] = $org->extra['tape_credit'] + $boxq - $task->mdata['tape_free'] * $tape_prod->mdata['bqtf'];
                $org->sync_xero = false;
                $org->save();
            }

            $this->redirect($this->createUrl('order/finish', array('result' => 'success')));
        } else {
            // invalid invoice id
            // how to record this ?
            // TODO ...
        }
    }

    /**
     * Trans
     */
    private function afterPayedByTrans($task_id) {
        $task = WmsTask::model()->findByPk($task_id);
        $task->bwf |= 32;
        $task->save();
        $org = Org::model()->findByPk(Yii::app()->user->org);

        $invoice_model = $this->generateInvoice($task, $org);
        $invoice_model->status = Invoice::INVOICE_STATUS_POSTED;
        $invoice_model->save();
        $invoice = Invoice::model()->findByPk($invoice_model->id);
        $this->redirect($this->createUrl('order/finish', array('invoice' => $invoice->no)));
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

        if ( !empty($data['task_id']) && !empty($data['amount']) ) {
            // valid paylink
            $this->payPoliInvoiceDirect($data['task_id'], $data['amount']);
        } else {
            // invalid pay link
            echo 'invalid pay link';
        }
    }

    private function payPoliInvoiceDirect($task_id, $amount) {
        $task = WmsTask::model()->findByPk($task_id);

        if ( !empty($task) && $task->status == array_search('New', WmsTask::$states) ) {
            $home_page = Yii::app()->request->hostInfo . $this->createUrl('site/index');
            $call_back_root = Yii::app()->request->hostInfo . $this->createUrl('order/poli') . '?id=' . $task_id . '&op=';
            $callback_success_url = $call_back_root . 'success';
            $callback_fail_url = $call_back_root . 'fail';
            $callback_cancel_url = $call_back_root . 'cancel';
            $callback_notiry_url = $call_back_root . 'notify';

            $data = '{
              "Amount":"' . $amount . '",
              "CurrencyCode":"AUD",
              "MerchantReference":"' . $task->getNo() . '",
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
            // 9 : invoice has been paid
            if ( !empty($task) && ($task->status == array_search('WIP', WmsTask::$states) || $task->status == array_search('Completed', WmsTask::$states)) ) {
                echo 'invoice has been paid';
            } else {
                echo 'invoice does not exist ';
            }
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

        $task = WmsTask::model()->findByPk($id);

        if ( !empty($task) ) {
            switch ($op) {
                case 'success' :
                    // should update invoice status here
                    $this->afterPaymentPaid($id, $trn_info, 'POLI');
                    break;
                case 'fail' :
                    break;
                case 'cancel' :
                    $this->afterPaymentCancelled($id);
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
            echo "can't find the invoice";
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

        $task = WmsTask::model()->findByPk($id);

        if (!empty($task)) {
            switch ($op) {
                case 'success':
                    $this->afterPaymentPaid($id, $paypal_info, 'PAYPAL');
                    break;
                case 'cancel':
                    $this->afterPaymentCancelled($id);
                    break;
                default:
                    echo 'unknown error occurred!';
                    break;
            }
        } else {
            echo "can't find the invoice";
        }
    }


    public function actionSupay() {
        Yii::log(json_encode($_SERVER['QUERY_STRING']), 'warning', 'trace');
        // Yii::log(json_encode($_GET), 'warning', 'trace');
        $supay = new SupayAPI('', '', '30810', '56175fb50d4a8040cc782c35040fadcf');
        $veri_result = $supay->verifyNotification($_GET);

        $task = WmsTask::model()->findByPk($_GET['merchant_trade_no']);
        if (!empty($task) && $veri_result) {
            $result = $supay->checkoutResult($task->id);
            if ($result) {
                $this->afterPaymentPaid($task->id, array('notice_id' => $_GET['notice_id'], 'amount' => $task->mdata['total']), 'SUPAY');
            } else {
                $this->afterPaymentCancelled($task->id);
            }
        } else {
            return "can't find the invoice";
        }
    }


    private function generateInvoice($task, $org) {
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
        $il = InvLine::model()->find('fid = :task_id', array(':task_id' => $task->id));
        if (empty($il)) {
        	$inv = new Invoice();
        } else {
        	$inv = $il->invoice;
        }
        $inv->type = Invoice::INVOICE_TYPE_CG_DELIVERY;
        $inv->dpmt = Invoice::DPMT_3PL;
        $inv->currency = array_search('AUD', Invoice::$currencies);
        $inv->to_id = $org->id;
        $inv->cg_order_id = $task->id;
        $inv->status = Invoice::INVOICE_STATUS_PENDING;
        $inv->mdata['name'] = $org->name;
        $inv->mdata['address'] = $org->getAddress();
        $inv->mdata['payterm'] = empty($org->extra['payterm']) ? 'COD' : $org->extra['payterm'] . ' days';
        $inv->mdata['billfrom'] = $fd;
        $inv->mdata['billto'] = $td;
        $inv->total = 0;
        $inv->gst = 0;
        $inv->save();
        InvLine::model()->deleteAll('inv_id = :id', array(':id' => $inv->id));

        $discount = !empty($org->extra['rebate_apply']) ? (100-floatval($org->extra['rebate_rate']))/100 : 1;
        $stot = $task->mdata['total'];
        $items = [];
        foreach ($task->items as $item) {
            $stock = WmsStock::model()->findByPk($item->mdata['si']);
            if (preg_match('/热敏/', $item->mdata['sn'])) {
                $free = max($task->mdata['thermal_free'], 0);
                if ($item->mdata['uq'] - $free) {
                    $items[] = [$task->getNo(), ucwords($task->ref), $task->schd_time, 'CG Delivery - ' . $item->mdata['sn'], round($stock->prod->mdata['price2'] * $discount, 2), ($item->mdata['uq'] - $free), round(($item->mdata['uq'] - $free) * round($stock->prod->mdata['price2'] * $discount, 2), 2)];
                }
                if ($org->extra['thermal'] && $free) {
                    $items[] = [$task->getNo(), ucwords($task->ref), $task->schd_time, 'CG Delivery - ' . $item->mdata['sn'] . ' - 免费配送', round(0, 2), $free, round(0, 2)];
                }
            } else if (preg_match('/缠绕膜/', $item->mdata['sn'])) {
                $free = max($task->mdata['wrapper_free'], 0);
                if ($item->mdata['uq'] - $free) {
                    $items[] = [$task->getNo(), ucwords($task->ref), $task->schd_time, 'CG Delivery - ' . $item->mdata['sn'], round($stock->prod->mdata['price2'] * $discount, 2), ($item->mdata['uq'] - $free), round(($item->mdata['uq'] - $free) * round($stock->prod->mdata['price2'] * $discount, 2), 2)];
                }
                if ($org->extra['wrapper'] && $free) {
                    $items[] = [$task->getNo(), ucwords($task->ref), $task->schd_time, 'CG Delivery - ' . $item->mdata['sn'] . ' - 免费配送', round(0, 2), $free, round(0, 2)];
                }
            } else if (preg_match('/透明胶带/', $item->mdata['sn'])) {
                $free = max($task->mdata['tape_free'], 0);
                if ($item->mdata['uq'] - $free) {
                    $items[] = [$task->getNo(), ucwords($task->ref), $task->schd_time, 'CG Delivery - ' . $item->mdata['sn'], round($stock->prod->mdata['price2'] * $discount, 2), ($item->mdata['uq'] - $free), round(($item->mdata['uq'] - $free) * round($stock->prod->mdata['price2'] * $discount, 2), 2)];
                }
                if ($org->extra['tape'] && $free) {
                    $items[] = [$task->getNo(), ucwords($task->ref), $task->schd_time, 'CG Delivery - ' . $item->mdata['sn'] . ' - 免费配送', round(0, 2), $free, round(0, 2)];
                }
            }else {
                $items[] = [$task->getNo(), ucwords($task->ref), $task->schd_time, 'CG Delivery - ' . $item->mdata['sn'], round($stock->prod->mdata['price'] * $discount, 2), $item->mdata['uq'], round($item->mdata['uq'] * round($stock->prod->mdata['price'] * $discount, 2), 2)];
            }
        }
        if ($task->mdata['freight']) {
            $items[] = [$task->getNo(), ucwords($task->ref), $task->schd_time, 'CG Delivery - Freight fee', $task->mdata['freight'], 1, $task->mdata['freight']];
        }

        $il = InvLine::model()->find('fid = :id AND inv_id = :inv_id', [':id' => $task->id, ':inv_id' => $inv->id]);
        if (empty($il)) {
            $il = new InvLine();
            $il->inv_id = $inv->id;
        }
        $il->amount = $stot;
        $il->gst = 0;
        $il->mdata['items'] = $items;
        $il->ccode = $task->type;
        $il->det = $task->getType();
        $il->qty = 1;
        $il->fid = $task->id;
        $il->save();

        $inv->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
        $inv->lines = [$il];
        $inv->total += $il->amount;
        $inv->gst = 0;
        $inv->sync_xero = false;
        $inv->save();

        return $inv;
    }

    private function afterPaymentPaid($task_id, $trnInfo, $type) {
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

        $task = WmsTask::model()->findByPk($task_id);
        $org = Org::model()->findByPk($task->job->org_id);

        // change url certificate
        $invoice = Invoice::model()->find('cg_order_id = :task_id', array(':task_id' => $task->id));
        if (!empty($invoice)) {
            $payment_gateway_history_model = PaymentGatewayHistory::model()->find('invoice_id = :invoice_id', array(':invoice_id' => $invoice->id));
            $payment_gateway_history_model->trn = $trn;
            $payment_gateway_history_model->token = $trn;
            $payment_gateway_history_model->meta = json_encode($trnInfo);
            $payment_gateway_history_model->save();

            $pay_inv = PayInv::model()->find('inv_id = :inv_id', array(':inv_id' => $invoice->id));
            $pay_inv->payment->ref = 'SUPAY TRN: ' . $trn;
            $pay_inv->payment->save();
            return;
        }

        $payment_gateway_history_model = PaymentGatewayHistory::model()->find('trn = :trn', array(':trn' => $trn));
        if ( !isset($payment_gateway_history_model) ) {
            $invoice = $this->generateInvoice($task, $org);
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

                // update task
                $task->status = array_search('Scheduled', WmsTask::$states);
                $task->save();
                $boxq = 0;
                foreach ($task->items as $item) {
                    $item->toStock();
                    if (preg_match('/纸箱/', $item->mdata['sn'])) {
                        $boxq += $item->mdata['uq'];
                    }
                }
                $thermal_prod = WmsProd::model()->find('name like "%热敏面单%"');
                $wrapper_prod = WmsProd::model()->find('name like "%缠绕膜%"');
                $tape_prod = WmsProd::model()->find('name like "%透明胶带%"');
                if (!empty($org->extra['thermal'])) {
                    $org->extra['thermal_credit'] = $org->extra['thermal_credit'] + $boxq - $task->mdata['thermal_free'] * $thermal_prod->mdata['bqtf'];
                    $org->sync_xero = false;
                    $org->save();
                }
                if (!empty($org->extra['wrapper'])) {
                    $org->extra['wrapper_credit'] = $org->extra['wrapper_credit'] + $boxq - $task->mdata['wrapper_free'] * $wrapper_prod->mdata['bqtf'];
                    $org->sync_xero = false;
                    $org->save();
                }
                if (!empty($org->extra['tape'])) {
                    $org->extra['tape_credit'] = $org->extra['tape_credit'] + $boxq - $task->mdata['tape_free'] * $tape_prod->mdata['bqtf'];
                    $org->sync_xero = false;
                    $org->save();
                }

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
    }

    private function afterPaymentCancelled($task_id) {
        $task = WmsTask::model()->findByPk($task_id);
        $task->status = array_search('Cancelled', WmsTask::$states);
        $task->save();

        $task->job->status = array_search('Cancelled', WmsJob::$states);
        $task->job->save();

        $this->redirect($this->createUrl('order/finish', array('result' => 'cancel')));
    }

    public function actionFinish() {
        Yii::log(json_encode($_GET), 'warning', 'trace');
        if (!empty($_GET['result'])) {
            $this->render('finish', array('result' => $_GET['result'], 'msg' => (!empty($_GET['msg']) ? $_GET['msg'] : '')));
        } else if (!empty($_GET['invoice'])) {
            $this->render('finish', array('invoice' => $_GET['invoice']));
        }
    }


    private function multi_array_sort($arr, $shortKey, $short = SORT_ASC, $shortType = SORT_REGULAR) {
        foreach ($arr as $key => $data) {
            $name[$key] = $data[$shortKey];
        }
        array_multisort($name, $shortType, $short, $arr);
        return $arr;
    }

    private function objects_sort($objs, $keys) {
        $result_objs = [];
        $keys = explode('-', $keys);
        foreach ($objs as $obj) {
            $index = $obj;
            for ($i = 0; $i < count($keys); $i ++) {
                if ($i < count($keys) - 1) {
                    $index = $index[$keys[$i]];
                }
                else {
                    $index = $index[$keys[$i]];
                    $result_objs[$index] = $obj;
                }
            }
        }
        ksort($result_objs);
        return $result_objs;
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