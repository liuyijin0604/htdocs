<?php

class CgoodsController extends Controller
{
    protected $nonAjax=array('updateOrgCgPriceLine','printOrders','getLatestOrders','print','printbatched');

	public function actionGoodsList()
	{
        $model = new ConsumableGoods();
        $model->unsetAttributes();
		$this->render('goods_list',['model' => $model]);
	}

    public function actionVendorList()
    {
        $model = new CgVendors('search');
        $model->unsetAttributes();
        $this->render('vendor_list',array(
            'model' => $model,
        ));
    }

    public function actionCreateVendor(){
        $model = new CgVendors();
        if ( isset($_POST['CgVendors'])) {
            $model->attributes = $_POST['CgVendors'];
            $model->save();
            $this->ajaxResult($model);
        }
        $this->render('create_vendor',array(
            'model'=>$model,
        ));
    }

    /**
     * update vendor information
     * @param $id
     */
    public function actionUpdateVendor($id){

        $model = CgVendors::model()->findByPk($id);
        if(isset($_POST['CgVendors'])){
            $model->attributes=$_POST['CgVendors'];
            $model->save();
            $this->ajaxResult($model);
        }
        $this->render('update_vendor',array(
            'model'=> $model,
        ));
    }

    /**
     * update product information
     * @param $id
     */
    public function actionUpdateCgoods($id){
        $model = ConsumableGoods::model()->findByPk($id);
        if(isset($_POST['ConsumableGoods'])){
            $model->attributes=$_POST['ConsumableGoods'];
            if ( $model->save() ) {
                // add inventory
                CgInventory::addInventory($model);
            }
            $this->ajaxResult($model);
        }
        $this->render('update_cgoods',array(
            'model'=> $model,
        ));
    }


    /**
     * create consumable goods
     */
    public function actionCreateConsumableGoods(){
        $model = new ConsumableGoods();
        if ( isset($_POST['ConsumableGoods'])) {
            $model->attributes = $_POST['ConsumableGoods'];

            if ( $model->save() ) {
                // add inventory
                CgInventory::addInventory($model);
            }
            $this->ajaxResult($model);
        }
        $this->render('create_cgoods',array(
            'model'=>$model,
        ));
    }

    /**
     * get all consumable goods inventory information
     */
    public function actionMngInventory(){
        $inventoryData = CgInventory::getInventoryData();
        $inventoryDataProvider = new CArrayDataProvider($inventoryData,array(
            'id' => 'cg_inventory_report',
            'pagination'=>array(
                'pageSize'=>'100',
            ),
        ));
        $this->render('inventory_list',array(
            'model'=> $inventoryDataProvider,
        ));
    }

    public function actionHistoryCgInventory($id){
        $model = new CgInventory('search');
        $model->unsetAttributes();
        $model->cd_id = $id;
        $this->render('inventory_history',array(
            'model'=> $model,
        ));
    }

    /**
     * @param $id
     */
    public function actionAddCgInventory($id){

        if ( isset($_POST['qty']) ) {
            $cg = ConsumableGoods::model();
            $note = isset($_POST['note']) ? $_POST['note'] : '';
            CgInventory::addInventoryQty($id,$_POST['qty'],$note);
            $this->ajaxResult($cg);
        }
        $cg = ConsumableGoods::model()->find($id);
        $data = array(
            'id' => $id,
            'name' => $cg->name,
            'op' => 'add'
        );
        $this->render('inventory_op',array(
            'data'=> $data,
        ));
    }

    /**
     * update orgnization related consumables price
     * @throws CDbException
     */
    public function actionUpdateOrgCgPriceLine(){
        if ( isset($_POST['OrgCgPrice']) ) {
            // update billing actual amount
            $model = OrgCgPrice::model()->findByPk($_POST['OrgCgPrice']['id']);
            if ( !empty($model) ) {
                $model->price = $_POST['OrgCgPrice']['price'];
                $model->update('price');
            }
            $this->ajaxResult($model);
        }
    }

    /**
     * @param $id
     */
    public function actionShowDetails($id){
        $model = new CgOrderLine();
        $model->unsetAttributes();
        $model->cd_order_id = $id;
        $this->render('cg_order_detail',['model' => $model]);
    }

    /**
     *
     */
    public function actionOutturn(){
        $model = new CgOrder('search');
        $model->unsetAttributes();
        if (isset($_GET['CgOrder'])) {
            $model->attributes = $_GET['CgOrder'];
        }
        $this->render('cg_orders',['model' => $model]);
    }

    public function actionPrintOrdersReq()
    {
        $orders = CgOrder::model()->findAll('status = :nstatus',[':nstatus' => CgOrder::CGORDER_STATUS_NEW]);
        foreach ($orders as $order ) {
            $order->status = CgOrder::CGORDER_STATUS_PROCESSING;
            $order->update('status');
            $order->createInvoice(); // create related invoice as others type
        }
        $resp = array(
            'success' => 1 ,
            'url' => $this->createUrl('cgoods/printOrders'));
        echo json_encode($resp);
    }

    /**
     * update order
     */
    public function actionUpdateOrder($id){
        $model = CgOrder::model()->findByPk($id);

        if ( isset($_POST['CgOrder']) ) {
            $model->attributes = $_POST['CgOrder'];
            $model->save();
            $this->ajaxResult($model);
        }

        $this->render('update_order',['model' => $model]);
    }

    public function actionGetLatestOrders(){
        $resp = array('success' => false , 'data' => 'Unknown error');
        $allInfo = CgOrder::getNewsOrdersStatic();
        $data = '<span>Orders : ' . $allInfo['total'] . '</span><br/>';
        // calculate all items count
        foreach ( $allInfo['details'] as $cg ) {
            $data .= '<span>' .$cg['name'] . ' : ' . $cg['total'] . '</span><br/>';
        }
        if ( !empty($allInfo['details']) ) {
            $data .= '<br/>'. CHtml::button('Print Order',['id' => 'print-order-btn']);
        }
        $resp['data'] = $data;
        $resp['success'] = true;
        echo json_encode($resp);
    }

    public function actionPrintOrders()
    {
        $f1 = tempnam(Yii::app()->basePath."/runtime", "cgd1");
        $invPdfs = array();
        $orders = CgOrder::model()->findAll('status = :nstatus',[':nstatus' => CgOrder::CGORDER_STATUS_PROCESSING]);
        oPDF::renderPDF('cg_orders', ['orders' => $orders],2,$f1);
        $invPdfs[] = $f1;
        $invoices = array();
        foreach ($orders as $order ) {
            $order->status = CgOrder::CGORDER_STATUS_DELIVERING;
            $order->update('status');
            if ($order->amount > 0) {
                $inv = Invoice::model()->find('cg_order_id = :oid', [':oid' => $order->id]);
                if (!empty($inv)) {
                    $invoices[] = $inv;
                }
            }
        }
        // output invoice

        $findex = 2;
        foreach ( $invoices as $inv ) {
            $f2 = tempnam(Yii::app()->basePath."/runtime", "cdg".$findex++);
            oPDF::renderPDF('invoice', array('inv'=>$inv),2,$f2);
            $invPdfs[] = $f2;
        }

        oPDF::mergePDF($invPdfs, 1, true, 'cgd_inv_all'.date('Y-m-d') .'.pdf');

    }


    /**
     *
     */
    public function actionSaveCgSetting(){
        $org = Org::model()->findByPk($_POST['oid']);
        if ( !empty($org) ) {
            $org->extra['cg_sell_model'] = $_POST['stype'];
            $org->updateMeta();
        }
        echo json_encode(array('success' => 1));
    }

    // public function actionReports(){

    //     // get total consumables inventory data
    //     $inventoryData = CgInventory::getInventoryData();

    //     // statistic consumables consume trend based on the average of recent three months data
    //     $consumeDataByMonth = CgInventory::getInventoryDataByMonth(true);

    //     // get average consumable recent years
    //     $cgData = array();
    //     foreach ( $consumeDataByMonth as $mon ) {
    //         if ( !isset($cgData[$mon->cgid]) ) {
    //             $cgData[$mon->cgid]= abs($mon->total);
    //         } else {
    //             $cgData[$mon->cgid] += abs($mon->total);
    //         }
    //     }
    //     foreach ( $cgData as $id => $data ) {
    //         $cgData[$id] = floor($data / 12); // average consume based on weeks
    //     }
    //     foreach ( $inventoryData as $id => $data ) {
    //         $data->avg = isset($cgData[$data->id]) ? $cgData[$data->id] : 0;
    //         if ( $data->avg > 0 ) {
    //             $leftweeks = floor($data->total / $data->avg);
    //             if ( $leftweeks > 3 ) {
    //                 $data->alert = '<span style="color:#008000">' . $leftweeks . ' weeks left </span>';
    //             } else {
    //                 $data->alert = '<span style="color:#ff0000">' . $leftweeks . ' weeks left </span>';
    //             }
    //         } else {
    //             $data->alert = '<span style="color:#008000">GOOD</span>';
    //         }

    //         $inventoryData[$id] = $data;
    //     }


    //     $dpInventory = new CArrayDataProvider($inventoryData,array(
    //         'id' => 'inventory_report',
    //         'pagination'=>array(
    //             'pageSize'=>'100',
    //         ),
    //     ));

    //     $dpConsumeTrend = new CArrayDataProvider($consumeDataByMonth,array(
    //         'id' => 'trends_report',
    //         'pagination'=>array(
    //             'pageSize'=>'100',
    //         ),
    //     ));

    //     $this->render('cg_reports',['trend' => $dpConsumeTrend,'inventory' => $dpInventory]);
    // }

    public function actionReports() {
        $orders = WmsTask::model()->findAll('type = 5010 AND status in (20,30,99)');
        $products = array();
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if (empty($products[$item->mdata['si']])) {
                    $products[$item->mdata['si']] = array('name' => $item->mdata['sn'], 'uq' => $item->mdata['uq']);
                } else {
                    $products[$item->mdata['si']]['uq'] += $item->mdata['uq'];
                }
            }
        }

        $this->render('reports', array('orders' => $orders, 'products' => $products));
    }


    /**
     * return customer consumes in recent three months
     * @throws CException
     */
    public function actionInventoryByCustomer(){
        $org = Org::model()->findByPk($_POST['cid']);
        $htmlData = 'Unknown error occured';

        if ( !empty($org) ) {

            $consumeDataByMonth = array();
            $prev3Month = date('Y-m-d',strtotime('-3 month'));

            $sql = 'SELECT MONTH(a.added_datetime) AS mon, c.cg_id,s.name,sum(c.qty) as amt FROM cg_order as a right join cg_order_line as c on a.id = c.cd_order_id  ';
            $sql .= 'left join consumable_goods as s on s.id = c.cg_id ';
            $sql .= "where a.added_datetime >= '" . $prev3Month ."' and a.client_id = " . $org->id;
            $sql .= ' GROUP BY c.cg_id,mon ORDER BY mon DESC';
            $sumRows = Yii::app()->db->createCommand($sql)->queryAll();
            $index = 1;
            foreach ( $sumRows as $r ) {
                $line = new stdClass();
                $line->id = $index++;
                $line->month = $r['mon'];
                $line->cgid = $r['cg_id'];
                $line->total = $r['amt'];
                $line->name = $r['name'];
                $consumeDataByMonth[] = $line;
            }


            $dpConsumeTrend = new CArrayDataProvider($consumeDataByMonth, array(
                'id' => 'customer_trends_report',
                'pagination' => array(
                    'pageSize' => '100',
                ),
            ));
            $htmlData = $this->renderPartial('_inventory_bycustomer', array('cinventory' => $dpConsumeTrend,'org' => $org), true);
        }

        echo $htmlData;
    }

    /**
     *
     */
    public function actionRefreshOrgCgoods(){
        $org = Org::model()->findByPk($_GET['oid']);
        // insert all consumables for sepcified org
        if ( !empty($org) ) {
            $allConsumables = ConsumableGoods::model()->findAll();
            foreach ( $allConsumables  as $c ) {
                $orgCgPrice = OrgCgPrice::model()->find('org_id = :oid AND cg_id = :cid',[':oid' => $org->id,':cid' => $c->id]);
                if ( empty($orgCgPrice) ) {
                    $orgCgPrice = new OrgCgPrice();
                    $orgCgPrice->org_id = $org->id;
                    $orgCgPrice->cg_id = $c->id;
                    $orgCgPrice->price = 0;
                    $orgCgPrice->save();
                }
            }
        }

        echo json_encode(array('success' => 1));
    }

    /**
     * @param $id
     */
    public function actionMinusCgInventory($id){

        if ( isset($_POST['qty']) ) {
            $cg = ConsumableGoods::model();
            $note = isset($_POST['note']) ? $_POST['note'] : '';
            CgInventory::addInventoryQty($id,'-'.$_POST['qty'],$note);
            $this->ajaxResult($cg);
        }

        $cg = ConsumableGoods::model()->find($id);
        $data = array(
            'id' => $id,
            'name' => $cg->name,
            'op' => 'minus'
        );
        $this->render('inventory_op',array(
            'data'=> $data,
        ));
    }

    public function actionOrders() {
        $tasks = WmsTask::model()->findAll(array('condition' => 'bwf&32 = 32 AND status = :status AND type = :type', 'params' => array(
            ':status' => array_search('New', WmsTask::$states),
            ':type' => array_search('CG Delivery', WmsTask::$types)
        )));
        foreach ($tasks as $task) {
            $invoice = InvLine::model()->find('fid = :task_id', array(':task_id' => $task->id))->invoice;
            if ($invoice->status == Invoice::INVOICE_STATUS_PAID) {
                $task->status = array_search('Scheduled', WmsTask::$states);
                $task->save();
                foreach ($task->items as $item) {
                    $item->toStock();
                }
            }
        }

        $model = new WmsTask('search');
        $model->unsetAttributes();  // clear any default values
        $model->type = array_search('CG Delivery', WmsTask::$types);
        if (isset($_GET['WmsTask'])) {
            $model->attributes = $_GET['WmsTask'];
        }

        $this->render('order_list', array(
            'model' => $model,
        ));
    }

    public function actionCompleteTask($id) {
        $task = WmsTask::model()->findByPk($id);
        if (!empty($_GET['confirm'])) {
            $task->compl_time = date('Y-m-d h:i:s');
            $task->status = 99;
            $task->save();

            foreach ($task->job->tasks as $task) {
                if ($task->status != 99) return;
            }
            $task->job->status = 99;
            $task->job->save();
            $this->ajaxResult($task);
        } else {
            $this->render('complete', array('model' => $task));
        }
    }

    public function actionJobs() {
        $model = new WmsJob('search');
        $model->unsetAttributes();  // clear any default values
        $model->type = array_search('CG Delivery', WmsJob::$types);
        if (isset($_GET['WmsJob'])) {
            $model->attributes = $_GET['WmsJob'];
        }

        $this->render('job_list', array(
            'model' => $model,
        ));
    }

    public function actionProducts() {
        $model = new WmsProd('search');
        $model->unsetAttributes();  // clear any default values
        $model->type = array_search('Material', WmsProd::$types);
        if (isset($_GET['WmsProd'])) {
            $model->attributes = $_GET['WmsProd'];
        }

        $this->render('product_list', array(
            'model' => $model,
        ));
    }

    public function actionStocks() {
        $model = new WmsStock('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['WmsStock'])) {
            $model->attributes = $_GET['WmsStock'];
        }

        $this->render('stock_list', array(
            'model' => $model,
        ));
    }

    public function actionPrint() {
        $tasks = WmsTask::model()->findAll('type = :type AND status = :status', array(
            ':type' => array_search('CG Delivery', WmsTask::$types),
            ':status' => array_search('Scheduled', WmsTask::$states)
        ));

        if (!empty($tasks)) {
            $job = new WmsJob();
            $job->org_id = WmsStock::model()->findByPk($tasks[0]->items[0]->mdata['si'])->org_id;
            $job->type = array_search('CG Delivery', WmsJob::$types);
            $job->status = array_search('Processing', WmsJob::$states);
            $job->save();

            foreach ($tasks as $task) {
                $task->job->status = array_search('Cancelled', WmsJob::$states);
                $task->job->save();
                $ac_task = $task->actionTask;
                foreach ($task->items as $item) {
                    $stock = WmsStock::model()->findByPk($item->mdata['si']);
                    $sls = $stock->locs;
                    $qty = $item->mdata['uq'];
                    foreach ($sls as $sl) {
                        if ($qty) {
                            $itm = new WmsTaskItem;
                            $itm->task_id = $ac_task->id;
                            $itm->mdata = array(
                                'si' => $item->mdata['si'],
                                'sn' => $item->mdata['sn'],
                                'uq' => ($qty > $sl->qty ? $sl->qty : $qty),
                                'pli' => $sl->loc->id,
                                'pl' => $sl->loc->code
                            );
                            $itm->save();
                            $qty -= $qty > $sl->qty ? $sl->qty : $qty;
                        }
                    }
                }
                $ac_task->compl_time = date('Y-m-d H:i:s');
                $ac_task->save();

                $task->status = array_search('WIP', WmsTask::$states);
                $task->job_id = $job->id;
                $task->mdata['delivery_time'] = date('Y-m-d H:i:s');
                $task->save();
            }

            $f1 = tempnam(Yii::app()->basePath."/runtime", "cgd1");
            $invPdfs = array();
            oPDF::renderPDF('cg_orders', array('tasks' => $tasks), 2, $f1);
            $invPdfs[] = $f1;
            oPDF::mergePDF($invPdfs, 1, true, 'cgd_inv_all'.date('Y-m-d') .'.pdf');
        } else {
            echo 'no Scheduled task available now';
        }
    }

    public function actionPrintBatched() {
        $tasks = WmsTask::model()->findAll('job_id = :job_id', array(':job_id' => $_GET['id']));

        if (!empty($tasks)) {
            $f1 = tempnam(Yii::app()->basePath."/runtime", "cgd1");
            $invPdfs = array();
            oPDF::renderPDF('cg_orders', array('tasks' => $tasks), 2, $f1);
            $invPdfs[] = $f1;
            oPDF::mergePDF($invPdfs, 1, true, 'cgd_inv_all'.date('Y-m-d') .'.pdf');
        } else {
            echo 'no tasks available now';
        }
    }

    public function actionSettings() {
        if(!empty($_POST['Settings'])){
            $f = Yii::app()->basePath.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'settings.php';
            $set = Yii::app()->params['settings'];
            foreach ($set as $k => $v) {
                if ($k == 'freight_free_above' || $k == 'freight_charge_fee')
                $set[$k]['value'] = $_POST['Settings'][$k]['value'];
            }
            Yii::app()->params['settings'] = $set;
            $r = var_export(Yii::app()->params['settings'], true);
            file_put_contents($f, "<?php\nreturn ".$r.';');
            $r = new stdClass;
            $r->done = true;
            $r->msg = 'Settings Saved!';
            echo json_encode($r);
            Yii::app()->end();
        }
        $this->render('cg_settings');
    }

    public function actionEditStock() {
        if (empty($_POST)) {
            $prods = WmsProd::model()->findAll('type = 20 AND status = 1');
            $this->render('update_stock', array('prods' => $prods, 'type' => $_GET['type']));
        } else {
            $job = WmsJob::model()->find('org_id = 114 and type = 40');

            // create task
            if ($_GET['type'] == 'in') {
                $task = new WmsTask;
                $task->job_id = $job->id;
                $task->type = 1010;
                $task->is_request = 1;
                $task->op_id = Yii::app()->user->id;
                $task->status = 20;
                $items = [];
                foreach ($_POST['WmsProd'] as $id => $qty) {
                    if ($qty > 0) {
                        $prod = WmsProd::model()->findByPk($id);
                        $items[] = ['gi' => $prod->id, 'gn' => $prod->name, 'uq' => $qty];
                    }
                }
                $task->new_items = $items;

                if(empty($task->getErrors())) {
                    $task->save();
                    // do wma
                    foreach ($_POST['WmsProd'] as $id => $qty) {
                        if ($qty > 0) {
                            $prod = WmsProd::model()->findByPk($id);
                            $itm = new WmsTaskItem;
                            $itm->task_id = $task->actionTask->id;
                            $itm->mdata = ['gi' => $prod->id, 'gn' => $prod->name, 'uq' => $qty, 'pl' => 'PLT1803000238', 'cq' => '', 'ex' => '', 'bn' => '', 'nt' => ''];
                            $itm->save();
                        }
                    }
                }
            } else if ($_GET['type'] == 'out') {
                $task = new WmsTask;
                $task->job_id = $job->id;
                $task->type = 3030;
                $task->is_request = 1;
                $task->op_id = Yii::app()->user->id;
                $task->status = 20;
                $items = [];
                foreach ($_POST['WmsProd'] as $id => $qty) {
                    if ($qty > 0) {
                        $stocks = WmsStock::model()->with('prod')->findAll('prod.id = :id', array(':id' => $id));
                        foreach ($stocks as $stock) {
                            if ($qty) {
                                $item = array(
                                    'si' => $stock->id,
                                    'sn' => $stock->prod->name,
                                    'pq' => '',
                                    'cq' => '',
                                    'uq' => ($qty > $stock->availQty()) ? $stock->availQty() : $qty,
                                    'pli' => '',
                                    'pl' => '',
                                    'nt' => ''
                                );
                                $qty -= $item['uq'];
                                $items[] = $item;
                            } else {
                                break;
                            }
                        }
                    }
                }
                $task->new_items = $items;

                if(empty($task->getErrors())) {
                    $task->save();

                    $ac_task = $task->actionTask;
                    foreach ($task->items as $item) {
                        $stock = WmsStock::model()->findByPk($item->mdata['si']);
                        $sls = $stock->locs;
                        $qty = $item->mdata['uq'];
                        foreach ($sls as $sl) {
                            if ($qty) {
                                $itm = new WmsTaskItem;
                                $itm->task_id = $ac_task->id;
                                $itm->mdata = array(
                                    'si' => $item->mdata['si'],
                                    'sn' => $item->mdata['sn'],
                                    'uq' => ($qty > $sl->qty ? $sl->qty : $qty),
                                    'pli' => $sl->loc->id,
                                    'pl' => $sl->loc->code
                                );
                                $itm->save();
                                $qty -= $qty > $sl->qty ? $sl->qty : $qty;
                            }
                        }
                    }
                    $ac_task->save();
                    $task->compl_time = date('Y-m-d H:i:s');
                    $task->status = array_search('Completed', WmsTask::$states);
                    $task->save();
                }
            }

            $this->ajaxResult($job, ['id']);
        }
    }

}