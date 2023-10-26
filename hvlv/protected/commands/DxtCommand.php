<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/05/2016
 * Time: 4:38 PM
 */


/**
 * Cron to get orders from Dxt by their API
 * api test only please refer to :
 * http://supply.dshaitao.com/ElectricPort/defaultpost.aspx
 * Class DxtCommand
 */
class DxtCommand extends CConsoleCommand {

    const DXT_CLIENT_ID = 'PCAEPS';
    const DXT_CLIENT_PASSWORD = 'PCA20160510';
    const DXT_ORG_ID = 794; // just hardcode now

    const DXT_API_GETORDERS = 'http://supply.dshaitao.com/express/getOrder.aspx';
    const DXT_API_STOCKINBATCH = 'http://supply.dshaitao.com/express/stockInBatch.aspx';
    const DXT_API_STOCKOUT = 'http://supply.dshaitao.com/express/stockOutBatch.aspx';
    const DXT_API_FULLROUTE = 'http://supply.dshaitao.com/express/fullRoute.aspx';
    const DXT_API_IMPORTBL = 'http://supply.dshaitao.com/express/ImportBl.aspx';

    private $debug = true;

    public function run($args) {

        if(!empty($args[0]) && method_exists($this, $args[0])){
            $this->{$args[0]}();
        }
    }

    private function resendsome(){
        $some = array();

        $consoleid = 2966;

        $criteria = new CDbCriteria();
        $criteria->addInCondition('hbn', $some);
        $criteria->addCondition('consol_id = 2966');
        $shipments = Shipment::model()->findAll($criteria);
        foreach ( $shipments as $s ) {
         //   DxtPushQueue::addStockIn($s->id);
          //  DxtPushQueue::addStockOutByParcel($s);
           // DxtPushQueue::addRouteByParcel($s,'2016-08-27 19:00:00','2016-08-28 05:14:00');

           // $this->echo_debug('=== done for :' . $s->hbn );
        }

        $this->echo_debug('=== all done ===');

    }


    /**
     * sync flight information for DXT consol
     */
    private function syncconsol(){

        // if push to dxt in progress , don't sync flight information
        // because maybe route message in queue status will be wrong
        $pid = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'dxt_push.pid';
        if ( is_file($pid) && filectime($pid) > time() - 1800 ) return;

        $this->_updateExport();
    }

    /**
     * sync flight information for DXT consol
     */
    private function _updateExport(){
        //update flight info
        $rs = ExacConsol::model()->findAll("status IN (20,40,70) AND owner_id = " . self::DXT_ORG_ID);
        $this->echo_debug('=== Found '.sizeof($rs)." dxt export consol to update flight info. ===");


        // call flight tracking logic
        include(Yii::app()->basePath.DIRECTORY_SEPARATOR.'commands/trackingCommand.php');
        $tracking = new trackingCommand('dxt', null);

        foreach($rs as $r){

            if(empty($r->flight) || empty($r->etd) || $r->etd == '0000-00-00') continue;
            if(strtotime($r->etd) > time() || strtotime($r->etd) < strtotime('-15 day')) continue;

            $d = $tracking->flight($r->flight, $r->etd);
            if(!empty($d)){
                if($r->status < 70 && $d[7] > 0){ //dispatched
                    $r->status = 70;
                    $r->save();
                    foreach($r->shipments as $p){
                        $p->addTracking(38, '空运航班飞离始发港', $d[2], $d[4]);
                        $p->status = 60;
                        $p->save();
                    }

                    // put flight route information into waiting for sync queue
                    DxtPushQueue::addRoute($r->id,$d[4],'');

                }elseif($r->status == 70 && $d[7] == 2){ //arrival
                    $r->status = 80;
                    $r->save();
                    foreach($r->shipments as $p){
                        $p->addTracking(40, '空运航班抵达目的港', '', $d[5]);
                        $p->status = 70;
                        $p->save();
                    }

                    // put flight route information into waiting for sync queue
                    DxtPushQueue::addRoute($r->id,'',$d[5]);

                }
            }
        }
    }

    /**
     * push all message which are in dxt_push_queue table to DXT on schedule time
     */
    private function push2dxt(){

        $this->echo_debug('push to dxt in progress ...');

        // if previous still running do nothing
        $pid = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'dxt_push.pid';
        if ( is_file($pid) && filectime($pid) > time() - 1800 ) return false;
        file_put_contents($pid, '1');

        $findCriteria = new CDbCriteria();
        $findCriteria->offset = 0;
        $findCriteria->limit = 100;
        $findCriteria->order = 'id ASC';
        $findCriteria->addCondition('status = 0');
        $all_msgs = DxtPushQueue::model()->findAll($findCriteria);

        $msgs_by_type = array();
        foreach ( $all_msgs as $msg ) {
            $msgs_by_type[$msg->type][] = $msg;
        }

        foreach ( $msgs_by_type as $k => $msgs ) {
            // message type:
            // 1 -  stock in message
            // 2 - stock out message
            // 3 - route message
            switch ( $k ) {
                case 1:
                    $this->pushStockinMsgs($msgs);
                    break;
                case 2:
                    $this->pushStockoutMsgs($msgs);
                    break;
                case 3:
                   $this->pushRouteMsgs($msgs);
                    break;
            }
        }

        // delete temporary file , for next time running again
        unlink($pid);

        $this->echo_debug('push to dxt done');

    }

    /**
     * @param $msgs
     */
    private function pushStockinMsgs($msgs){

        // makeup all contents
        // push message template
        $msg_tpl_item = '<message><stockInList>[DATA]</stockInList></message>';

        $dxt_data = '';
        foreach ( $msgs as $msg ) {
            $data = json_decode( $msg['message'] );
            $dxt_data .= '<stockIn><epsID>' . $data->epsID . '</epsID>';
            $dxt_data .= '<inDate>' . $data->inDate . '</inDate>';
            $dxt_data .= '<gwet>' . $data->gwet . '</gwet>';
            $dxt_data .= '<vlm>' . $data->vlm . '</vlm></stockIn>';
        }

        $dxt_data = str_replace('[DATA]',$dxt_data,$msg_tpl_item);

        $sign = $this->getSign($dxt_data);
        $data_string = 'clientID='.self::DXT_CLIENT_ID.'&data_content='.$dxt_data.'&data_digest='.$sign;

        $this->echo_debug('stock in data : ' . $data_string);

        $rt = $this->postUrlData(self::DXT_API_STOCKINBATCH,$data_string);

       // $this->log($rt);

        $this->echo_debug($rt);


        // test data only
       // $rt = '<message><resultList><result><epsID>1005403</epsID><success>TRUE</success><rtuCode>0</rtuCode><note>出库完成</note></result><result><epsID>1005401</epsID><success>FALSE</success><rtuCode>-100</rtuCode><note>无此快递</note></result></resultList></message>';
        $results =  $this->parseXml($rt);
        $esp_ok_list = array();
        if ( isset($results->resultList->result ) ) {
            foreach ($results->resultList->result as $result) {
                $epsID = (string)$result->epsID;
                $code = (int)$result->rtuCode;

                // 0	成功
                // -1	系统异常
                // -100	无此快递
                // -110	快递已入库
                if ($code != -1 && $code <= 0) {
                    // in these cases,we don't need to push again
                    $esp_ok_list[] = $epsID;
                }
            }
        } else {
            $this->echo_debug('pushStockinMsgs unknown error');
            $this->log('pushStockinMsgs unknown error : ' . $rt);
        }

        // update push result
        // if ok set status to 1 avoid re-push again
        if ( count($esp_ok_list) > 0 ) {
            $sql_in_str = implode(',', $esp_ok_list);
            $sql = 'UPDATE dxt_push_queue SET status = 1 WHERE ref in (' . $sql_in_str . ')';
            Yii::app()->db->createCommand($sql)->execute();
        }

    }

    /**
     * @param $msgs
     */
    private function pushRouteMsgs($msgs){
        // makeup all contents
        // push message template
        $msg_tpl_item = '<message><routeList>[DATA]</routeList></message>';

        $dxt_data = '';
        foreach ( $msgs as $msg ) {
            $data = json_decode( $msg['message'] );
            $dxt_data .= '<route><epsID>' . $data->epsID . '</epsID>';
            $dxt_data .= '<departure>' . $data->departure . '</departure>';
            $dxt_data .= '<arrive>' . $data->arrive . '</arrive></route>';
        }

        $dxt_data = str_replace('[DATA]',$dxt_data,$msg_tpl_item);

        $sign = $this->getSign($dxt_data);
        $data_string = 'clientID='.self::DXT_CLIENT_ID.'&data_content='.$dxt_data.'&data_digest='.$sign;

        $this->echo_debug('full route data : ' . $data_string);
        $this->log($data_string);

        $rt = $this->postUrlData(self::DXT_API_FULLROUTE,$data_string);

        // $this->log($rt);

        $this->echo_debug($rt);

        $this->log('route message return from DXT : ' . $rt);

        $results =  $this->parseXml($rt);
        $esp_ok_list = array();
        foreach ( $results->resultList->result as $result) {
            $epsID = (string)$result->epsID;
            $code = (int)$result->rtuCode;

            // 0	成功
            // -1	系统异常
            // -100	无此快递
            // -110	快递已入库
            if ( $code != -1 && $code <= 0 ) {
                // in these cases,we don't need to push again
                $esp_ok_list[] = $epsID;
            }
        }

        // update push result
        // if ok set status to 1 avoid re-push again
        if ( count($esp_ok_list) > 0 ) {
            $sql_in_str = implode(',', $esp_ok_list);
            $sql = 'UPDATE dxt_push_queue SET status = 1 WHERE ref in (' . $sql_in_str . ')';
            Yii::app()->db->createCommand($sql)->execute();
        }
    }

    /**
     * @param $msgs
     */
    private function pushStockoutMsgs($msgs){
        // makeup all contents
        // push message template
        $msg_tpl_item = '<message><stockOutList>[DATA]</stockOutList></message>';

        $dxt_data = '';
        foreach ( $msgs as $msg ) {
            $data = json_decode( $msg['message'] );
            $dxt_data .= '<stockOut><epsID>' . $data->epsID . '</epsID>';
            $dxt_data .= '<vessel>' . $data->vessel . '</vessel>';
            $dxt_data .= '<voy>' . $data->voy . '</voy>';
            $dxt_data .= '<ETD>' . $data->ETD . '</ETD>';
            $dxt_data .= '<blNo>' . $data->blNo . '</blNo>';
            $dxt_data .= '<portLoad>' . $data->portLoad . '</portLoad>';
            $dxt_data .= '<portDis>' . $data->portDis . '</portDis>';
            $dxt_data .= '<outDate>' . $data->outDate . '</outDate>';
            $dxt_data .= '<carrier>PCA</carrier>';
            $dxt_data .= '<mailNo></mailNo></stockOut>';
        }

        $dxt_data = str_replace('[DATA]',$dxt_data,$msg_tpl_item);

        $sign = $this->getSign($dxt_data);
        $data_string = 'clientID='.self::DXT_CLIENT_ID.'&data_content='.$dxt_data.'&data_digest='.$sign;

        $this->echo_debug('stock out data : ' . $data_string);

        $rt = $this->postUrlData(self::DXT_API_STOCKOUT,$data_string);

        // $this->log($rt);

        $this->echo_debug($rt);


        // test data only
        // $rt = '<message><resultList><result><epsID>1005403</epsID><success>TRUE</success><rtuCode>0</rtuCode><note>出库完成</note></result><result><epsID>1005401</epsID><success>FALSE</success><rtuCode>-100</rtuCode><note>无此快递</note></result></resultList></message>';
        $results =  $this->parseXml($rt);
        $esp_ok_list = array();
        foreach ( $results->resultList->result as $result) {
            $epsID = (string)$result->epsID;
            $code = (int)$result->rtuCode;

            // 0	成功
            // -1	系统异常
            // -100	无此快递
            // -110	快递已入库
            if ( $code != -1 && $code <= 0 ) {
                // in these cases,we don't need to push again
                $esp_ok_list[] = $epsID;
            }
        }

        // update push result
        // if ok set status to 1 avoid re-push again
        if ( count($esp_ok_list) > 0 ) {
            $sql_in_str = implode(',', $esp_ok_list);
            $sql = 'UPDATE dxt_push_queue SET status = 1 WHERE ref in (' . $sql_in_str . ')';
            Yii::app()->db->createCommand($sql)->execute();
        }
    }


    /**
     * pull all orders from Dxt server
     */
    private function syncOrders(){

        // if previous still running do nothing
        $pid = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'dxt.pid';
        if ( is_file($pid) && filectime($pid) > time() - 1800 ) return false;
        file_put_contents($pid, '1');

        // begin to sync all orders now
        $this->getOrders();

        // delete temporary file , for next time running again
        unlink($pid);
    }

    /**
     * get all orders from DXT server by their API
     */
    private function getOrders(){

        $last_batch_id = 0;

        // get last time orders sync batch list ID
        // if not existing yet, just start from zero
        $batch_ids = DxtSync::model()->find('id > 0');
        if ( isset($batch_ids) ) {
            $last_batch_id = $batch_ids->batch_id;
        }

        $this->echo_debug('get orders in progress ...');
        // based on last batch list id to get new orders now
        $resp = $this->postOrdersCommand($last_batch_id);

        if (!empty($resp)) {
            $this->log('get orders response data : ' . $resp);
            $results = $this->parseXml($resp);
            if (isset($results)) {
                // check to see if we have got some orders
                $msgType = $results->head->msgType->__toString();
                if (strtolower($msgType) !== 'express') {
                    // not correct
                    $this->log('get orders failed :' . $resp);

                } else {

                    $this->log('get orders successfully');

                    // get batch new id
                    if (!isset($results->body->batchList->batchInfo->opID)) {
                        $this->echo_debug('DXT server return wrong message');
                        return;
                    }

                    $newBatchId = $results->body->batchList->batchInfo->opID->__toString();
                    $this->updateSyncBatchId($newBatchId);
                    $orderCount = (int)$results->body->batchList->batchInfo->bCount;
                    if ($orderCount <= 0) {
                        $this->echo_debug('DXT server return zero order');
                        return;
                    }

                    $this->log('new batch ID : ' . $newBatchId);
                    $this->echo_debug('new batch ID : ' . $newBatchId);
                    $this->echo_debug('new batch order count : ' . $orderCount);

                    // loop all orders and push into our DB
                    $obj_orderinfo = $results->body->ordeInforList->orderInfo;
                    foreach ($obj_orderinfo as $order) {
                        $this->pushDxtOrder2Pca($order);
                    }

                    $this->log('save orders done');
                    $this->echo_debug('save orders done');

                }
            }
        }
    }

    /**
     * move all shipment without weight to another new air freight console
     */
    public static function wrapNoweightShipments($consoleId){
        $p = ExacConsol::model()->findByPk($consoleId);
        if ( $p->owner_id != DxtCommand::DXT_ORG_ID ) return;

        // get all parcels without weight
        $parcels = ExAfs::model()->findAll(array(
            'condition' => 'consol_id = :cid AND weight <= 0',
            'params' => array(
                ':cid' => $consoleId
            )
        ));

        if ( isset($parcels) && count($parcels) > 0 ) {
            // create a new console and move all without weight to this new one
            // create new console for DXT
            $newDxt = new ExacConsol();
            $newDxt->owner_id = self::DXT_ORG_ID;
            $newDxt->type = 20; // ExacConsol
            $newDxt->status = 10; // new one
            $newDxt->save();
            $newConsolID = $newDxt->id;

            // get all in parcel id
            $all_ids = array();
            foreach ( $parcels as $parcel ) {
                $all_ids[] = $parcel->id;
            }
            $crt = new CDbCriteria();
            $crt->addInCondition('id',$all_ids);
            ExAfs::model()->updateAll(array('consol_id' => $newConsolID),$crt);
        }
    }

    /**
     * push Dxt order into PCA shipment
     * @param $order
     */
    public function pushDxtOrder2Pca($order){

        // order basic information
        $epsID = (string)$order->epsID;
        $orderID = (string)$order->orderID;
        $orderNo = (string)$order->orderNo;
        $scanCode = (string)$order->scanCode;
        $routeCode = (string)$order->routeCode;
        $orderDate = (string)$order->orderDate;
        $supplier = (string)$order->supplier;
        $opID = (string)$order->opID;
        $owner = '';
        if ( isset( $order->gdsOwner) ) {
            $owner = (string)$order->gdsOwner;
        }

        // get consignor information
        $cnor_name = (string)$order->consignor->name;
        $cnor_phone = (string)$order->consignor->phone;
        $cnor_province = (string)$order->consignor->province;
        $cnor_city = (string)$order->consignor->city;
        $cnor_district = (string)$order->consignor->district;
        $cnor_street = (string)$order->consignor->street;

        // get consigneer information
        $cnee_name = (string)$order->consignee->name;
        $cnee_phone = (string)$order->consignee->phone;
        $cnee_province = (string)$order->consignee->province;
        $cnee_city = (string)$order->consignee->city;
        $cnee_district = (string)$order->consignee->district;
        $cnee_street = (string)$order->consignee->street;

        // get sku information
        $sku_list = array();
        foreach ( $order->skuList->sku as $sku ) {
            $sku_list[] = array(
                'id' => (string)$sku->id,
                'name' => (string)$sku->name,
                'foreignName' => (string)$sku->foreignName,
                'barcode' => (string)$sku->barcode,
                'count' => (string)$sku->count,
                'price' => (string)$sku->price,
                'currencyUnit' => (string)$sku->currencyUnit
            );
        }

        // all ready to put into pca DB
        $isUpdate = false;
        $hbn = $scanCode;
        $s = empty($hbn)? null : ExAfs::model()->find('hbn = :h', [':h' => $hbn]);
        if ( empty($s) ) {
            $s = empty($hbn)? null : ExParcel::model()->find('hbn = :h', [':h' => $hbn]);
        }
        // get console ID
        $dxt_console_id = $this->getDxtFreeConsoleID();
        if(empty($s)){
            $this->echo_debug('add new order : ' . $hbn);
            $s = new ExAfs;
            $s->cnor = new Addr;
            $s->cnee = new Addr;
            $s->exm = 'EXLV';
            $s->status = 20;
            $s->weight = 0;
            $s->consol_id = $dxt_console_id;
        } else {
            $this->echo_debug( 'update for order : ' . $hbn );
            $s->type = 25; // convert to DXT parcel type
            $s->status = 20;
            if ( $s->consol_id == 0 ) { // maybe having console , we should not change it again
                $s->consol_id = $dxt_console_id;
            }
            if (!isset($s->cnor)) $s->cnor = new Addr;
            if (!isset($s->cnee) ) $s->cnee = new Addr;
            $isUpdate = true;
        }

        $s->agent_id = self::DXT_ORG_ID;
        $s->hbn = $scanCode;
        $s->cref = $orderNo;
        $s->ref = $epsID;   // DXT unique parcel ID
        $s->pkg = 1; // default in one package

        //cnor
        $s->cnor->name = $cnor_name;
        $s->cnor->tel = $cnor_phone;
        $s->cnor->address =  $cnor_street;
        $s->cnor->state = $cnor_province;
        $s->cnor->city = $cnor_city;
        $s->cnor->suburb = $cnor_district;
        $s->cnor->postcode = '';
        $s->cnor->save();
        $s->cnor_id = $s->cnor->id;

        //cnee
        $s->cnee->name = $cnee_name;
        $s->cnee->tel = $cnee_phone;
        $s->cnee->address =  $cnee_street;
        $s->cnee->state = $cnee_province;
        $s->cnee->city = $cnee_city;
        $s->cnee->suburb = $cnee_district;
        $s->cnee->postcode = '';
        if(empty($s->cnee->postcode)) $s->cnee->setCnAddr($s->cnee->address);
        $s->cnee->save();
        $s->cnee_id = $s->cnee->id;

        // sku
        // clear old data
        $s->eitems['g'] = array();
        $s->eitems['g_zh'] = array();
        $s->eitems['q'] = array();
        $s->eitems['hs'] = array();
        $s->eitems['v'] = array();
        foreach ( $sku_list as $sku ) {
            $s->eitems['g'][] = $sku['foreignName'];  // goods name
            $s->eitems['g_zh'][] = $sku['name']; // goods chinese name
            $s->eitems['q'][] = $sku['count']; // quantity
            $s->eitems['hs'][] = $sku['barcode']; // hs code
            $s->eitems['v'][] = $sku['price']; // goods value
        }

        if (!empty($owner) ) {
            $s->mdata['dxt_order_owner'] = $owner;
        }

        $s->save();

        // in case updated
        if ( $isUpdate ) {
            //  weight existing we need to push to waiting sync with DXT queue now
            if ( $s->weight > 0 ) {
                DxtPushQueue::addStockIn($s->id);
            }

            // in case consol has been confirmed
            // we should re-send stockout information to DXT
            //  ExacConsol status 20 : confirmed
            if ( $s->consol->status >= 20 ) {
                DxtPushQueue::addStockOutByParcel($s);
            }

            // we still need to check to see if we should send route information to DXT
            // in case order information arrived later
           if ( $s->status == 60 ) {
               // in case flight departure
               $departureTime = $s->getDepartueTime();
               if ( $departureTime ) {
                   DxtPushQueue::addRouteByParcel($s, $departureTime);
               }

           } else if ( $s->status == 70 ) {
               // in case flight arrived
               $departureTime = $s->getDepartueTime();
               if ( !$departureTime ) $departureTime = '';
               $arriveTime = $s->getArriveTime();
               if (!$arriveTime ) $arriveTime = '';
               DxtPushQueue::addRouteByParcel($s,$departureTime,$arriveTime);
           }
        }
    }

    /**
     * push Dxt order into DXT's new console
     * in order to send this parcel firstly
     * once real order information arrived from DXT we send related information to DXT again
     * @param $data
     *     $data['hbn'] scan barcode
     *     $data['weight]
     */
    public function pushDxtOrder2Console($data){

        // order basic information
        $hbn = $data['hbn'];
        $weight = $data['weight'];
        if ( empty($hbn) ) return;
        $s = empty($hbn)? null : ExAfs::model()->find('hbn = :h', [':h' => $hbn]);
        if ( !empty($s) ) return; // shouldn't be existing

        // get console ID
        $dxt_console_id = $this->getDxtFreeConsoleID();
        $s = new ExAfs;
        $s->cnor = new Addr;
        $s->cnee = new Addr;
        $s->exm = 'EXLV';
        $s->status = 20;
        $s->weight = $weight;
        $s->consol_id = $dxt_console_id;
        $s->agent_id = self::DXT_ORG_ID;
        $s->hbn = $hbn;
        $s->cref = '';
        $s->ref = '';
        $s->pkg = 1; // default in one package
        $s->save();
    }

    public function pushImportBl(){
        $data = '<Consol>
<Status>1</Status>
<ConsolNo>AF16111186SYD</ConsolNo>
<AWB>784-29326485</AWB>
<Flight>CZ302</Flight>
<ETD>2016-11-28</ETD>
<POL>AUSYD</POL>
<POD>CNCAN</POD>
<Packs>1</Packs>
<Weight>2.9</Weight>
<ClearanceMode>BBC</ClearanceMode>
<ClearncePlace>CNCAN</ClearncePlace>
<CorpCode>VIP</CorpCode>
<Shipper>
    <Name>PCA Express</Name>
    <Code>PCA</Code>
    <Address>U 12, 1801 Botany Road, NSW 2019</Address>
    <Country>Australia</Country>
    <Tel>+61299257100</Tel>
</Shipper>
<Consignee>
    <Name>VIP Shop</Name>
    <Code>VIP</Code>
    <Address>1 Vip Road, Guangdong</Address>
    <Country>China</Country>
    <Tel>+86123456789</Tel>
</Consignee>
<Notify>
    <Name>VIP Shop</Name>
    <Code>VIP</Code>
    <Address>1 Vip Road, Guangdong</Address>
    <Country>China</Country>
    <Tel>+86123456789</Tel>
</Notify>
<Shipments>
    <Shipment>
        <OrderID>16112398755425</OrderID>
        <Connote>972576809981</Connote>
        <Ref>16112398755425</Ref>
        <PltNo>1</PltNo>
        <Weight>2.90</Weight>
    </Shipment>
</Shipments>
</Consol>';

        $sign = $this->getSign($data);
        $data_string = 'clientID='.self::DXT_CLIENT_ID.'&data_content='.$data.'&data_digest='.$sign;

        $this->echo_debug('stock out data : ' . $data_string);

        $rt = $this->postUrlData(self::DXT_API_IMPORTBL,$data_string);

        // $this->log($rt);

        $this->echo_debug($rt);
    }


    /**
     * get new consol ID for DXT if not existing free one, just create a free new one and return
     * @return int
     */
    private function getDxtFreeConsoleID(){

        $dxtConsol = ExacConsol::model()->find('owner_id = :dxtId AND status = 10',
            array(':dxtId' => self::DXT_ORG_ID) );
        if ( isset($dxtConsol) ) {
            $newConsolID = $dxtConsol->id;
        } else {
            // create new console for DXT
            $newDxt = new ExacConsol();
            $newDxt->owner_id = self::DXT_ORG_ID;
            $newDxt->type = 20; // ExacConsol
            $newDxt->status = 10; // new one
            $newDxt->save();
            $newConsolID = $newDxt->id;
        }
        return $newConsolID;
    }

    /**
     * update new sync batch ID
     * @param $batchId
     */
    private function updateSyncBatchId($batchId){
        $obj_batch_id = DxtSync::model()->findByPk('1');
        if ( !isset($obj_batch_id) ) {
            $model = new DxtSync();
            $model->setAttribute('id', 1);
            $model->setAttribute('batch_id', $batchId);
            $model->save();
        } else {
            $obj_batch_id->setAttribute('batch_id',$batchId);
            $obj_batch_id->save();
        }
    }

    /**
     * parse xml result to array
     * @param $data
     * @return SimpleXMLElement
     */
    private function parseXml($data){

        return simplexml_load_string($data);

    }

    /**
     * call getOrders API
     * @param $batchId
     * @return mixed
     */
    private function postOrdersCommand($batchId){
        $sign = $this->getSign($batchId);
        $data_string = 'clientID='.self::DXT_CLIENT_ID.'&data_opID='.$batchId.'&data_digest='.$sign;
        return $this->postUrlData(self::DXT_API_GETORDERS,$data_string);
    }

    private function postUrlData($url,$data){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        if ($result === FALSE) {
            echo 'cUrl error (# ' .curl_errno($ch) . '): '. htmlspecialchars(curl_error($ch)) .'<br>\n';
        }
        return $result;
    }

    /**
     * get sign field string
     * @param $batchId
     * @return string
     */
    private function getSign($data){
        $org = self::DXT_CLIENT_ID . $data . self::DXT_CLIENT_PASSWORD;
       // if ( $this->debug ) echo 'before crypt : '. $org . "\n";
       // if ( $this->debug ) echo 'after md5 : ' . base64_encode(md5($org,true)) . "\n";
       // if ( $this->debug ) echo 'afet urlencode : ' . urlencode(base64_encode(md5($org,true))) . "\n";
        return urlencode(base64_encode(md5($org,true)));
    }

    /**
     * @param $l
     */
    private function log($msg){
        $tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
        file_put_contents($tmp.'dxt_api.log', date('Y-m-d H:i:s') . ' ' . $msg ."\n", FILE_APPEND);
    }

    /**
     * echo debug information
     * @param $str
     */
    private function echo_debug($str){
        if ( $this->debug )  echo $str . "\n";
    }

}
