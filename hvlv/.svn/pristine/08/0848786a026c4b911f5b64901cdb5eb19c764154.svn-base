<?php

class ErpProductRouteController extends Controller
{
    protected $nonAjax = array('revert','ajaxWhReport');

	public function actionList()
	{
        // test only
       // $test = Manifest::model()->find('id = 9022');
       // $sig = $test['mdata']['sig'];
       // file_put_contents(Yii::app()->basePath . '/runtime/test2.png',base64_decode($sig));

        $model = new ErpProductRoutes('search');

        $model->unsetAttributes();  // clear any default values

        if(isset($_GET['ErpProductRoutes']))
            $model->attributes=$_GET['ErpProductRoutes'];

        $this->render('list',array(
            'model' => $model,
        ));

	}

    public function actionReports()
    {

        // get first warehouse reports
        // we will report all consume goods statistic information for the first warehouse
        $warehouse_list = ErpProductRoutes::warehouseList();
        $first_warehouse_id = 0;
        foreach ( $warehouse_list as $k => $warehouse ) {
            $first_warehouse_id = $k;
            break;
        }
        $end_time = NULL;
        if ( isset($_POST['wh_to_date'] ) ) {
            $end_time = $_POST['wh_to_date'];
        }
        $allGoods = $this->getWarehouseConsumeReport($first_warehouse_id,$end_time);

        $wh_data_provider = new CArrayDataProvider($allGoods,array(
           'id' => 'warehouse_report'
        ));

        $model['whdata_list'] = new ErpProductRoutes();
        $model['whdata_provider'] = $wh_data_provider;
        $model['end_time'] = date('Y-m-d');
        $model['whid'] = $first_warehouse_id;


        // for driver report data model
        $driver_list = ErpProductRoutes::driverList();
        $first_driver_id = 0;
        foreach ( $driver_list as $k => $driver ) {
            $first_driver_id = $k;
            break;
        }
        $allGoods = $this->getDriverConsumeReport($first_driver_id,$end_time);
        $model['driverdata_provider'] = new CArrayDataProvider($allGoods,array(
            'id' => 'driver_report'
        ));
        $model['did'] = $first_driver_id;
        $model['driver_end_time'] = date('Y-m-d');;


        // for agent report data model
        $allGoods = $this->getAgentConsumeReport(0,NULL);
        $model['agentdata_provider'] = new CArrayDataProvider($allGoods,array(
            'id' => 'agent_report'
        ));
        $model['agent_end_time'] = date('Y-m-d');;

        $this->render('reports', array(
                'model' => $model,
        ));

    }

    /**
     * return warehouse report data based on warehouse id and end of time
     */
    public function actionAjaxWhReport(){

        $warehouse_id = 0;
        if ( !empty($_POST['warehouse_id']) ){
            $warehouse_id = $_POST['warehouse_id'];
        }

        $end_time = NULL;
        if ( isset($_POST['wh_to_date'] ) ) {
            $end_time = $_POST['wh_to_date'];
        }
        $allGoods = $this->getWarehouseConsumeReport($warehouse_id,$end_time);

        $wh_data_provider = new CArrayDataProvider($allGoods,array(
            'id' => 'warehouse_report',
            'pagination'=>array(
                'pageSize'=>'30',
            ),
        ));

        $model['whdata_list'] = new ErpProductRoutes();
        $model['whdata_provider'] = $wh_data_provider;
        $model['end_time'] = $end_time;
        $model['whid'] = $warehouse_id;

        $this->renderPartial('_wh_reports',array('model' => $model),false, true);
    }

    /**
     * return driver report data based on driver id and end of time
     */
    public function actionAjaxDriverReport(){

        $driver_id = 0;
        if ( !empty($_POST['did']) ){
            $driver_id = $_POST['did'];
        }

        $end_time = NULL;
        if ( isset($_POST['driver_to_date'] ) ) {
            $end_time = $_POST['driver_to_date'];
        }
        $allGoods = $this->getDriverConsumeReport($driver_id,$end_time);

        $driver_data_provider = new CArrayDataProvider($allGoods,array(
            'id' => 'driver_report',
            'pagination'=>array(
                'pageSize'=>'30',
            ),
        ));

        $model['driver_end_time'] = $end_time;
        $model['did'] = $driver_id;
        $model['driverdata_provider'] = $driver_data_provider;

        $this->renderPartial('_driver_reports',array('model' => $model),false, true);
    }


    /**
     * return agent report data based on driver id and end of time
     */
    public function actionAjaxAgentReport(){

        $agent_id = 0;
        if ( !empty($_POST['ErpProductRoutes']['to_id']) ){
            $agent_id = $_POST['ErpProductRoutes']['to_id'];
        }

        $end_time = NULL;
        if ( isset($_POST['agent_to_date'] ) ) {
            $end_time = $_POST['agent_to_date'];
        }
        $allGoods = $this->getAgentConsumeReport($agent_id,$end_time);

        $agent_data_provider = new CArrayDataProvider($allGoods,array(
            'id' => 'agent_report',
            'pagination'=>array(
                'pageSize'=>'30',
            ),
        ));

        $model['agent_end_time'] = $end_time;
        $model['to_id'] = $agent_id;
        $model['agentdata_provider'] = $agent_data_provider;
        $model['whdata_list'] = new ErpProductRoutes();
        $model['whdata_list']->setAttribute('to_id',$agent_id);

        $this->renderPartial('_agent_reports',array('model' => $model),false, true);
    }

    /**
     * get all agent products consumed statistic
     * @param $agentId
     * @param null $from
     * @param null $to
     * @return array
     */
    private function getAgentConsumeReport($agentId,$endTime = NULL){

        $endTime .= ' 23:59:59';
        // get all consume goods belongs to the driver
        if ( empty($endTime) ) {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,b.name as product_name ,SUM(quantity) AS amt FROM erp_product_routes AS a LEFT JOIN erp_products as b ON a.product_id = b.id WHERE to_id = :did AND type = 2 GROUP BY product_id',
                array(':did' => $agentId));
        } else {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,b.name as product_name ,SUM(quantity) AS amt FROM erp_product_routes AS a LEFT JOIN erp_products as b ON a.product_id = b.id WHERE to_id = :did AND type = 2 AND a.added_time <= :endTime GROUP BY product_id',
                array(':did' => $agentId,':endTime' => $endTime));
        }
        $all_goods = array();
        foreach ( $goods as $good ) {
            $all_goods[$good->product_id] = array(
                'id' => $good->product_id,
                'name' => $good->product_name,
                'total' => $good->amt,
                'left' => 0,
                'consumed' => $good->amt,
                'unknown' => 0
            );
        }

        return $all_goods;
    }

    /**
     * get all driver products consumed statistic
     * @param $driverId
     * @param null $from
     * @param null $to
     * @return array
     */
    private function getDriverConsumeReport($driverId,$endTime = NULL){

        // get all consume goods belongs to the driver
        $endTime .= ' 23:59:59';

        if ( empty($endTime) ) {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,b.name as product_name ,SUM(quantity) AS amt FROM erp_product_routes AS a LEFT JOIN erp_products as b ON a.product_id = b.id WHERE to_id = :did AND type = 1 GROUP BY product_id',
                array(':did' => $driverId));
        } else {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,b.name as product_name ,SUM(quantity) AS amt FROM erp_product_routes AS a LEFT JOIN erp_products as b ON a.product_id = b.id WHERE to_id = :did AND type = 1 AND a.added_time <= :endTime GROUP BY product_id',
                array(':did' => $driverId,':endTime' => $endTime));
        }
        $all_goods = array();
        foreach ( $goods as $good ) {
            $all_goods[$good->product_id] = array(
                'id' => $good->product_id,
                'name' => $good->product_name,
                'total' => $good->amt,
                'left' => $good->amt,
                'consumed' => 0,
                'unknown' => 0
            );
        }

        // get all output data - received by agent
        if ( empty($endTime) ) {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,SUM(quantity) AS amt FROM erp_product_routes  WHERE from_id = :did AND type = 2 GROUP BY product_id',
                array(':did' => $driverId));
        } else {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,SUM(quantity) AS amt FROM erp_product_routes  WHERE from_id = :did AND type = 2 AND added_time <= :endTime GROUP BY product_id',
                array(':did' => $driverId, ':endTime' => $endTime));
        }
        foreach ( $goods as $good ) {
            if ( isset( $all_goods[$good->product_id]) ) {
                $all_goods[$good->product_id]['consumed'] = $good->amt;
                $all_goods[$good->product_id]['left'] = $all_goods[$good->product_id]['left'] - $good->amt;
            }
        }

        // returned by driver
        if ( empty($endTime) ) {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,SUM(quantity) AS amt FROM erp_product_routes  WHERE from_id = :did AND type = 3 GROUP BY product_id',
                array(':did' => $driverId));
        } else {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,SUM(quantity) AS amt FROM erp_product_routes  WHERE from_id = :did AND type = 3 AND added_time <= :endTime GROUP BY product_id',
                array(':did' => $driverId, ':endTime' => $endTime));
        }
        foreach ( $goods as $good ) {
            if ( isset( $all_goods[$good->product_id]) ) {
                $all_goods[$good->product_id]['total'] -= $good->amt;
                $all_goods[$good->product_id]['left'] = $all_goods[$good->product_id]['left'] - $good->amt;
            }
        }

        return $all_goods;
    }


    /**
     * get all warehouse products consumed statistic
     * @param $warehouseId
     * @param null $from
     * @param null $to
     * @return array
     */
    private function getWarehouseConsumeReport($warehouseId,$endTime = NULL){

        $endTime .= ' 23:59:59';
        // get all consume goods belongs to the warehouse
        $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,b.name as product_name ,SUM(quantity) AS amt FROM erp_product_routes AS a LEFT JOIN erp_products as b ON a.product_id = b.id WHERE to_id = :did AND (type = 0 OR type = 3) GROUP BY product_id',
                array(':did' => $warehouseId));

        $all_goods = array();
        foreach ( $goods as $good ) {
            $all_goods[$good->product_id] = array(
                'id' => $good->product_id,
                'name' => $good->product_name,
                'total' => $good->amt,
                'left' => $good->amt,
                'consumed' => 0,
                'unknown' => 0
            );
        }

        // get all output data
        if ( empty($endTime) ) {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,SUM(quantity) AS amt FROM erp_product_routes  WHERE warehouse_id = :did AND (type = 2 OR type = 5) GROUP BY product_id',
                array(':did' => $warehouseId));
        } else {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,SUM(quantity) AS amt FROM erp_product_routes  WHERE warehouse_id = :did AND (type = 2 OR type = 5)  AND added_time <= :endTime GROUP BY product_id',
                array(':did' => $warehouseId, ':endTime' => $endTime));
        }
        foreach ( $goods as $good ) {
            if ( isset( $all_goods[$good->product_id]) ) {
                $all_goods[$good->product_id]['consumed'] = $good->amt;
                $all_goods[$good->product_id]['left'] = $all_goods[$good->product_id]['left'] - $good->amt;
            }
        }

        // unknown data - scrap or disappear
        if ( empty($endTime) ) {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,SUM(quantity) AS amt FROM erp_product_routes  WHERE warehouse_id = :did AND type = 4 GROUP BY product_id',
                array(':did' => $warehouseId));
        } else {
            $goods = ErpProductRoutes::model()->findAllBySql('SELECT product_id,SUM(quantity) AS amt FROM erp_product_routes  WHERE warehouse_id = :did AND type = 4 AND added_time <= :endTime GROUP BY product_id',
                array(':did' => $warehouseId, ':endTime' => $endTime));
        }
        foreach ( $goods as $good ) {
            if ( isset( $all_goods[$good->product_id]) ) {
                $all_goods[$good->product_id]['unknown'] = $good->amt;
                $all_goods[$good->product_id]['left'] = $all_goods[$good->product_id]['left'] - $good->amt;
            }
        }

        return $all_goods;
    }

    /**
     * input products
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionInput(){

        $model = new ErpProductRoutes();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if ( isset($_POST['ErpProductRoutes'])) {

            // input products means from vendor to warehouse
            $model->attributes = $_POST['ErpProductRoutes'];
            $model->setAttribute('type',0);

            // set vendor id based on product id
            $vendor_id = 0;
            $product = ErpProducts::model()->find('id = :id',array(':id' => $model->product_id));
            if ( !empty($product) ) {
                $vendor_id = $product->vendor_id;
            }
            $model->setAttribute('from_id',$vendor_id);

            // in case input product , to_id means warehouse id
            $model->setAttribute('to_id',$model->warehouse_id);

            $model->save();
            $this->ajaxResult($model);
        }

        $this->render('input',array(
            'model'=>$model,
        ));

    }

    /**
     * scrap or disappear products
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionVirtual(){

        $model = new ErpProductRoutes();

        if ( isset($_POST['ErpProductRoutes'])) {

            // product maybe disappear or scrap
            $model->attributes = $_POST['ErpProductRoutes'];
            $model->setAttribute('type',4);

            // no source and destination
            $model->setAttribute('from_id',0);
            $model->setAttribute('to_id',0);

            $model->save();
            $this->ajaxResult($model);
        }

        $this->render('virtual',array(
            'model'=>$model,
        ));

    }

    /**
     *   update product current inventory in special warehouse
     */
    public function actionUpdateInventory(){
        // based on warehouse and product id to get inventory
        $product_id = 0;
        $warehouse_id = 0;

        // get all input product quantity in warehouse
        if ( isset($_POST['ErpProductRoutes']['product_id']) && ( $_POST['ErpProductRoutes']['product_id'] > 0 )  ){
            $product_id = $_POST['ErpProductRoutes']['product_id'];
        }
        if ( isset($_POST['ErpProductRoutes']['warehouse_id']) && ( $_POST['ErpProductRoutes']['warehouse_id'] > 0 )  ){
            $warehouse_id =  $_POST['ErpProductRoutes']['warehouse_id'];
        }

        // invalid input just return zero
        if ( $product_id <= 0 || $warehouse_id <= 0) {
            echo '0';
            return;
        }

        echo  $this->getProductInventory($warehouse_id,$product_id);

    }

    /**
     * get all product inventories based on warehouse
     */
    public function actionRefreshInventory(){
        $warehouseId = $_POST['whid'];
        $allProducts = ErpProductRoutes::productList();
        $productInventories = array();
        foreach ($allProducts as $k => $v) {
            $productInventories[$k] = $this->getProductInventory($warehouseId,$k);
        }
        $result = new stdClass();
        $result->success = 1;
        $result->inventories = $productInventories;
        $result->products = $allProducts;
        $result->warehouseId = $warehouseId;
        echo json_encode($result);
    }

    private function getProductInventory($warehouseId,$productId){
        if ( $warehouseId <= 0 || $productId <= 0 ) return 0;

        $params_array[':pid'] = $productId;
        $params_array[':whid'] = $warehouseId;

        // get all input product quantity (including returned quantity)
        $sum_model = ErpProductRoutes::model()->find(array(
            'select' => 'SUM(quantity) as amt',
            'condition' => 'to_id = :whid AND product_id = :pid AND ( type = 0 OR type = 3  )',
            'params' => $params_array
        ));
        $inventory_amount = $sum_model->amt;

        // get output product quantity to driver
        $sum_model = ErpProductRoutes::model()->find(array(
            'select' => 'SUM(quantity) as amt',
            'condition' => 'from_id = :whid AND product_id = :pid AND type = 1',
            'params' => $params_array
        ));
        $inventory_amount -= $sum_model->amt;

        // get disappear or scrap quantity
        $sum_model = ErpProductRoutes::model()->find(array(
            'select' => 'SUM(quantity) as amt',
            'condition' => 'warehouse_id = :whid AND product_id = :pid AND type = 4',
            'params' => $params_array
        ));
        $inventory_amount -= $sum_model->amt;

        // get internal used product
        $sum_model = ErpProductRoutes::model()->find(array(
            'select' => 'SUM(quantity) as amt',
            'condition' => 'from_id = :whid AND product_id = :pid AND type = 5',
            'params' => $params_array
        ));

        $inventory_amount -= $sum_model->amt;

        return $inventory_amount;
    }
    /**
     * return suggest from driver name
     */
    public function actionDriverSuggest(){
        $this->driverSuggest(array(100));
    }

    /**
     * return suggest from name
     */
    public function actionFromSuggest(){
        $this->suggest(array(60,65));
    }

    private function driverSuggest($grp=''){
        if(empty($grp)){
            $rs = User::model()->findAll(array(
                'condition' => 'active = 1 AND (fname LIKE :n OR lname LIKE :n)',
                'params'=> array(':n' => '%'.$_GET['term'].'%'),
                'order' => 'fname',
                'limit' => 20,
            ));
        }elseif(is_array($grp)){
            $rs = User::model()->findAll(array(
                'condition' => 'active = 1 AND type IN ('.implode(',', $grp).') AND (fname LIKE :n OR lname LIKE :n)',
                'params' => array(':n' => '%'.$_GET['term'].'%'),
                'order' => 'fname',
                'limit' => 20,
            ));
        }else{
            $rs = User::model()->findAll(array(
                'condition' => 'active = 1 AND type = :grp AND (fname LIKE :n OR lname LIKE :n)',
                'params' => array(':n' => '%'.$_GET['term'].'%', ':grp' => $grp),
                'order' => 'fname',
                'limit' => 20,
            ));
        }
        $a = array();

        foreach($rs as $r){
            $a[] = array(
                'value' => $r->id,
                'label' => $r->fname . ' ' . $r->lname,
            );
        }
        echo json_encode($a);
    }

    /**
     * return suggest to name
     */
    public function actionToSuggest(){
        $this->suggest(array(60,65));
    }

    private function suggest($grp=''){
        if(empty($grp)){
            $rs = Org::model()->findAll(array(
                'condition' => 'status = 1 AND (name LIKE :n OR code LIKE :n)',
                'params'=> array(':n' => '%'.$_GET['term'].'%'),
                'order' => 'name',
                'limit' => 20,
            ));
        }elseif(is_array($grp)){
            $rs = Org::model()->findAll(array(
                'condition' => 'status = 1 AND type IN ('.implode(',', $grp).') AND (name LIKE :n OR code LIKE :n)',
                'params' => array(':n' => '%'.$_GET['term'].'%'),
                'order' => 'name',
                'limit' => 20,
            ));
        }else{
            $rs = Org::model()->findAll(array(
                'condition' => 'status = 1 AND type = :grp AND (name LIKE :n OR code LIKE :n)',
                'params' => array(':n' => '%'.$_GET['term'].'%', ':grp' => $grp),
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
     * update product routes information
     * @param $id
     */
    public function actionUpdate($id){

        $model = $this->loadModel($id);

        if(isset($_POST['ErpProductRoutes'])){
            $model->attributes=$_POST['ErpProductRoutes'];
            $model->save();
            $this->ajaxResult($model);
        }

        // normal update view
        $view_name = 'update';
        if ( $model->type == 0 ) {
            // product input update view (from vendor to warehouse)
            $view_name = 'input_update';
        } elseif ( $model->type == 1) {
            // product output update view ( from warehouse to driver)
            $view_name = 'output_wd_update';
        } elseif ( $model->type == 2 ) {
            // product output update view ( from driver to agent)
            $view_name = 'output_da_update';
        } elseif ( $model->type == 3 ) {
            // product return update view (from driver to warehouse)
            $view_name = 'return_update';
        } elseif ( $model->type == 4 ) {
            // product virtual update view ( record normal virtual status for example , item disappear or scrap)
            $view_name = 'virtual_update';
        } elseif ( $model->type == 6 ) {
            // product out from wareshoue to agent directly
            $view_name = 'output_wa_update';
        }

        $this->render('update',array(
            'model'=> $model,
            'view_name' => $view_name
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id){
        $model = ErpProductRoutes::model()->findByPk($id);
        if ( $model === null )
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * output products
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionOutput(){

        $model = new ErpProductRoutes();

        $productInventory = array();
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if ( isset($_POST['ErpProductRoutes'])) {

            if ( isset($_POST['prod']) ) {

                foreach ( $_POST['prod'] as $k => $v ) {
                    if ( $_POST['qty'][$k] > 0 ) {
                        $newRoute = new ErpProductRoutes();
                        // save all products belong to the driver
                        $newRoute->attributes = $_POST['ErpProductRoutes'];

                        // in case product output from warehouse to driver
                        $newRoute->setAttribute('type', 1);
                        $newRoute->setAttribute('from_id', $newRoute->warehouse_id);
                        $newRoute->setAttribute('product_id', $v);
                        $newRoute->setAttribute('quantity', $_POST['qty'][$k]);

                        $newRoute->save();
                    }
                }
            }

            $this->ajaxResult($model);
        } else {
            // set default selected warehouse
            $allWarehouses = ErpProductRoutes::warehouseList();
            $def_warehouse_id = 0;
            foreach ( $allWarehouses as $k => $v ) {
                $model->setAttribute('warehouse_id',$k);
                $def_warehouse_id = $k;
                break;
            }

            // get all products inventory information
            $allProducts = ErpProductRoutes::productList();
            $def_product_id = 0;
            foreach ($allProducts as $k => $product) {
                if ( $def_product_id == 0 ) {
                    $def_product_id = $k;
                    $model->setAttribute('product_id',$k);
                }
                $productInventory[$k] = $this->getProductInventory($def_warehouse_id,$k);
            }

        }

        $this->render('output',array(
            'model'=>$model,
            'inventories' => $productInventory
        ));

    }

    /**
     * output products to agent directly
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionOutputAgent(){

        $model = new ErpProductRoutes();

        $productInventory = array();
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if ( isset($_POST['ErpProductRoutes'])) {

            if ( isset($_POST['prod']) ) {

                foreach ( $_POST['prod'] as $k => $v ) {
                    if ( $_POST['qty'][$k] > 0 ) {
                        $newRoute = new ErpProductRoutes();
                        // save all products belong to the driver
                        $newRoute->attributes = $_POST['ErpProductRoutes'];

                        // in case product output from warehouse to agent directly
                        $newRoute->setAttribute('type', 6);
                        $newRoute->setAttribute('from_id', $newRoute->warehouse_id);
                        $newRoute->setAttribute('product_id', $v);
                        $newRoute->setAttribute('quantity', $_POST['qty'][$k]);

                        $newRoute->save();
                    }
                }
            }

            $this->ajaxResult($model);
        } else {
            // set default selected warehouse
            $allWarehouses = ErpProductRoutes::warehouseList();
            $def_warehouse_id = 0;
            foreach ( $allWarehouses as $k => $v ) {
                $model->setAttribute('warehouse_id',$k);
                $def_warehouse_id = $k;
                break;
            }

            // get all products inventory information
            $allProducts = ErpProductRoutes::productList();
            $def_product_id = 0;
            foreach ($allProducts as $k => $product) {
                if ( $def_product_id == 0 ) {
                    $def_product_id = $k;
                    $model->setAttribute('product_id',$k);
                }
                $productInventory[$k] = $this->getProductInventory($def_warehouse_id,$k);
            }

        }

        $this->render('output_agent',array(
            'model'=>$model,
            'inventories' => $productInventory
        ));

    }

    /**
     * internal used product form
     */
    public function actionOutputInside(){

        $model = new ErpProductRoutes();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if ( isset($_POST['ErpProductRoutes'])) {
            $model->attributes = $_POST['ErpProductRoutes'];

            // in case product output from warehouse to driver
            $model->setAttribute('type',5);
            $model->setAttribute('from_id',$model->warehouse_id);
            $model->setAttribute('to_id',0);

            $model->save();
            $this->ajaxResult($model);
        }

        $this->render('output_inside',array(
            'model'=>$model,
        ));

    }



    /**
     * return products
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionReturn(){

        $model = new ErpProductRoutes();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if ( isset($_POST['ErpProductRoutes'])) {
            $model->attributes = $_POST['ErpProductRoutes'];

            // in case product returned from driver to warehouse
            $model->setAttribute('type',3);

            $model->setAttribute('from_id',$model->to_id);
            $model->setAttribute('to_id',$model->warehouse_id);

            $model->save();
            $this->ajaxResult($model);
        }

        $this->render('return',array(
            'model'=>$model,
        ));

    }


    /**
     * return one product route
     * @param $id
     * @throws CHttpException
     */
    public function actionRevert($id){
        $model = $this->loadModel($id);
        if ( !empty($model) ) {
            $model->status = 1; // not editable again
            $model->save();

            // new product route
            $newData = new ErpProductRoutes();
            $newData->attributes = $model->attributes;
            $newData->from_id = $model->to_id;
            $newData->to_id = $model->from_id;
            if ( $model->type == 1  ) $newData->type = 3; // product returned
            if ( $model->type == 3  ) $newData->type = 1; // product returned

            $newData->save();

            $this->ajaxResult($model, array(), 'Product route was reverted successfully!');
        }

    }

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}