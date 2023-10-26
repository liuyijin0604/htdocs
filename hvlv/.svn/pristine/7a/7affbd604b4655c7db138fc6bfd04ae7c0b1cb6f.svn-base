<?php
class AukAPI
{
    public $url = 'http://52.62.103.208/';

    public $testUrl = "http://52.62.103.208/"; 

    public $workUrl, $token,$defaultPlatform = 1,$defaultWarhouse = 1,$defaultChoiceId;
    public $_logger;
    public $counterSet,$idCounter;
    private $log,$fromInfo;

    function __construct($test = "TEST",$orgRateId = ImportChargeCode::AUK_SYD_ID)
    {
        $this->orgRate = OrgRate::model()->findByPk($orgRateId);
        $type = $this->orgRate->mdata['facility'];
        $this->log = new APILog('AUK',$type);

        if($test == "TEST"){
            $this->getInfoStaticTest();
        }else{
            $this->getInfo();
        }

       $this->fromInfo=$this->log->fromInfo;

    }

    public static function getAPIInstance($courierId)
    {
        return new AukAPI(false,$courierId);
    }


    public function getInfo(){
        $this->token = "ItHQD7fUM2a55RLw1IfAVw==";
        $this->defaultChoiceId = 3;
        $this->workUrl = $this->url.'api/';

    }
    public function getInfoStaticTest(){
        $this->token = "Sk0-yCNJi5kiCdYHoH2zXw=="; // this also just a testing not working
        $this->workUrl = $this->testUrl.'api/';
        $this->defaultChoiceId = 3; // for most of ph customer the choice id is 3
    }

    /**
     * @param Model_Ord_ParcelpoolObj $pObj
     * @return mixed
     */
    public function createShipment($pObj){
        $pObj->hbn = $pObj->hbn;
        $arrResult = array();
        $arrResult = $this->importOrder($pObj);
        $orderID = "";
        if($arrResult['request_success']){
            sleep(1);
            $orderID =   $pObj->hbn;
            if(!empty($arrResult['request_result'][$orderID]['success'])){
                if($arrResult['request_result'][$orderID]['success']){
                    $idPoolRemote = $arrResult['request_result'][$orderID]['id_pool'];
                    $allocateResult = $this->allocateOrder($idPoolRemote); // quick one
                    sleep(1);
                    $orderStatusResult = $this->checkOrderStatus($idPoolRemote);
                    if($orderStatusResult['request_success']&&$orderStatusResult['request_result']['status']==3)
                    {
                        $labelResult = $this->printLabel($idPoolRemote);
                        if(!empty($labelResult['success']))
                        {
                            return $labelResult;
                        }else
                        {
                            return array("success" => false,"error" => "order status is invalid");
                        }
                        return $labelResult;
                    }else
                    {
                        return array("success" => false,"error" => "order status is invalid");
                    }
                }
            }else{
                return array("success" => false,"error" => implode(",",$arrResult['request_result'][$orderID]['error']));
            }
        }else{
            return array("success" => false,"error" => var_export($arrResult,true));
        }

    }


    public static function getZone($shipment)
    {
        $or = OrgRate::model()->findByPk($shipment->mdata['org_rate_id']);
        $o = new stdClass();
        $o->code = 0;
        $o->msg="";
        $o->data = 0;

        $price = 0;
        $postcode = $shipment->cnee->postcode;
        $suburb = $shipment->cnee->suburb;
        $weight = $shipment->weight;
        $zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code ', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode]);

        $z1 = '';
        if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
            $z1 = $zoneMap['z1'];
        }
        return $z1;
    }

    public static function getPackages($shipment)
    {
        $consignmentDataArr = [];
        if(!empty($shipment->packs))
        {
            foreach ($shipment->packs as $key => $package) {
                $consignmentData = new stdClass();
                $consignmentData->qty = 1;
                $consignmentData->weight = number_format($package['weight'],2,'.','');
                $consignmentData->length = number_format($package['length'],0,'.','');
                $consignmentData->width = number_format($package['width'],0,'.','');
                $consignmentData->height = number_format($package['height'],0,'.','');
                $consignmentDataArr[] = $consignmentData;
            }
        }
        // }else
        // {
        //     if(!empty($shipment->cbm))
        //     {
        //         $thisCBM = (($shipment->weight/$shipment->pkg)/rand(251,280));
        //         $cbm = $thisCBM*1000000;
        //         $x = pow($cbm/(3*3*4), 1/3);
        //         for ($i=0; $i < $shipment->pkg; $i++) { 
        //             $consignmentData = new stdClass();
        //             $consignmentData->qty = 1;
        //             $consignmentData->weight = number_format($shipment->weight/$shipment->pkg);
        //             if(4*$x>=120)
        //             {
        //                 $x = pow($thisCBM/(3*3*3), 1/3);
        //                 $consignmentData->length = number_format(3*$x,0,'.','');
        //                 $consignmentData->width = number_format(3*$x,0,'.','');
        //                 $consignmentData->height = number_format(3*$x,0,'.','');
        //             }else
        //             {
        //                 $consignmentData->length = number_format(3*$x,0,'.','');
        //                 $consignmentData->width = number_format(3*$x,0,'.','');
        //                 $consignmentData->height = number_format(4*$x,0,'.','');
        //             }
        //             $consignmentDataArr[] = $consignmentData;
        //         }
        //     }
        // }
        return $consignmentDataArr;
    }



    /**
     * @param Model_Ord_ParcelpoolObj $pObj
     * @param null $senderID
     */
    public function importOrder($pObj,$senderID = null){

        $cnee = $pObj->cnee;
        $arrReceiverInfo = array(
            "name" =>$cnee->name,
            "company" => $cnee->company,
            "address_line1"=> substr($cnee->address, 0, 40),
            "address_line2"=> "",
            "address_suburb"=> $cnee->suburb,
            "address_city"=> $cnee->suburb,
            "address_state"=> $cnee->state,
            "address_postcode"=> $cnee->postcode,
            "address_country"=> $cnee->country,
            "phone"=>  $cnee->tel,
            "email" =>$cnee->email,
            "special_instruction" => ""
        );


        $name=$pObj->agent_id;
        $shipper = Org::model()->findByPk($this->orgRate->mdata['ddpt_id']);
        $org=$pObj->agent;
        if (!empty($org->extra['delivery_label_name'])) {
            $name=$org->extra['delivery_label_name'];
        }


        $senderInfo = array(
                    "sender_name" => $name,
                    "sender_company" => $name,
                    "sender_address_line1" => substr($this->fromInfo['address'], 0, 80),
                    "sender_address_line2" => "",
                    "sender_address_suburb" => substr($this->fromInfo['suburb'], 0, 80),
                    "sender_address_city"   => substr($this->fromInfo['suburb'], 0, 80),
                    "sender_address_state" => substr($this->fromInfo['state'], 0, 80),
                    "sender_address_postcode"=>$this->fromInfo['postcode'],
                    "sender_address_country"=> 'Australia',
                    "sender_phone"=>substr($shipper->phone, 0, 20),
                    "sender_email"=>"imports@toplogistics.com.au"
        );



        if (strlen($cnee->address) > 40) {
             $arrReceiverInfo["address_line2"]=substr($cnee->address, 40, 80);
        }

        $arrPlatformInfo = array(
            "order_reference_id" => $pObj->cref
        );
        $shippingMethod = "Standard";
        $arrShippingInfo = array(
            "shipping_method" => $shippingMethod
        );

        $arrItems = array();
        $arrItemIds = array();
        foreach ($pObj->eitems['g'] as $key => $g)
        {
            $item = array(
                "item_title" =>$g,
                "item_sku" =>$g,
                "item_qty" =>$pObj->eitems['q'][$key],
                "item_unit_price"=> $pObj->eitems['v'][$key]
            );
            $arrItems[] = $item;
            $arrItemIds[] = $key;
        }



        $arrPackage = array();
        $packages = $this->getPackages($pObj);
        if(empty($packages))
        {
            return ['request_success'=>false,'error'=>'empty packages info'];
        }
        foreach ($packages as $key => $package) {
            $package = array(
                "package_id" => $key,
                "width" =>  number_format($package->width,0,'.',''),
                "height" => number_format($package->height,0,'.',''),
                "depth" =>  number_format($package->length,0,'.',''),
                "weight" => number_format($package->weight,2,'.',''),
                "choice_carrier" => $this->defaultChoiceId // currently for toll is 3
            );
            if($key == 0){
                $package['package_items'] = $arrItemIds;
            }
            $arrPackage[] = $package;
        }

        $arrSingleOrder = array(
            "id_order" => $pObj->hbn,
            "id_platform" => $this->defaultPlatform,
            "id_warehouse" => $this->defaultWarhouse,
            "id_sender" => 0,
            "sender_info" => $senderInfo,
            "receiver_info" => $arrReceiverInfo,
            "platform_info" => $arrPlatformInfo,
            "shipping_info" => $arrShippingInfo,
            "order_items" => $arrItems,
            "order_packages" => $arrPackage
        );
        $arrOrders = array('orders' =>array($arrSingleOrder));
        $strOrder = json_encode($arrOrders);

        $url =  $this->workUrl."import-orders/count/1";
        //$orderImportResult = $this->sendRequest($verb,$strOrder);
        $orderImportResult = $this->request($strOrder,'POST',$url);
        $arrResult = json_decode(json_encode($orderImportResult),true);
        return $arrResult;
    }
    public function allocateOrder($idPool){
        $url = $this->workUrl."allocation-order/id_pool/".$idPool."/nocookie/allowNoCookie";
        $result = $this->request(null,'GET',$url);
        return $result;
    }

    public function checkOrderStatus($idPool){
        $url = $this->workUrl."order-status/id_pool/".$idPool;
        $result = $this->request(null,'GET',$url);
        return $result;
    }
    public function printLabel($idPool){
        $url = $this->workUrl."print-label/id_pool/".$idPool;
        $result = $this->request(null,'GET',$url);
        return $result;
    }

    public function getTollLabel($ref){
        $url = $this->workUrl."export/toll/".$ref.".pdf";
        $result = file_get_contents($url);
        return $result;
    }

    private static function getFileName()
    {
        return 'TLA_AUK_MANIFEST_'.date('YmdHis').'.xls';
    }

    public static function manifest($ss)
    {
        if(empty($ss)) return false;
        try 
        {
            $i = 1;
            $xls = new oExcel;
            $xls->addRow($i++, ['Connote', 'TrackingNumber', 'Weight']);
            foreach ($ss as $p) {
                $xls->addRow($i++, [$p->hbn, $p->ref,$p->weight]);
            }

            $filename = self::getFileName();
            $path = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'AUK' . DIRECTORY_SEPARATOR;
            $fullFilename = $path.$filename;
            $xls->output($fullFilename, null, false);
            $result = self::sendManifestEmail($fullFilename,$filename);
            $result['filename'] = $filename;
            return $result;
        } catch (Exception $ex) {
            throw $ex;
            return array("status"=>false);
        }
    }

    public static function sendManifestEmail($fullFilename,$filename)
    {
        $email = 'directinjection@auklogistics.com';
        $emailLog = new Emailog();
        $emailLog->to_id = Org::ORGID_COURIER_AUK;
        $emailLog->fid = 0;
        $emailLog->type= Emailog::DFE_API_TEMPLATE;
        $emailLog->dt = date('Y-m-d H:i:s');
        $emailLog->status = 10;
        $emailLog->prepTemplate();
        $emailLog->tpl->assignSubject('TITLE', $filename);
        // $emailLog->tpl->assignThese([
        //  'FILENAME'=>$filename,
        // ]);
        $emailLog->subject = $emailLog->tpl->subject;
        $emailLog->body = $emailLog->tpl->getContent();
        $emailLog->mdata['to']=$email;
        $emailLog->mdata['fromName']='TLA Imports';
        $emailLog->mdata['from']='imports@toplogistics.com.au';
        $emailLog->mdata['cc']='imports@toplogistics.com.au';
        $o=$emailLog->sendEmail([[$fullFilename,$filename]]);

        return $o;
    }

    /**
     * send non tracking event request to AUK toll
     * @param $action
     * @param string $method
     * @param null $data
     * @return bool|mixed
     * @throws Exception
     */
    protected function request($data = null, $method = 'POST',$url = "")
    {
        $this->result = false;
        $url =  $url;
        $c = new curl($url);
        $c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
        $c->setopt(CURLOPT_RETURNTRANSFER, true);
        $c->setopt(CURLOPT_SSL_VERIFYPEER, true);
        $c->setopt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $c->setopt(CURLOPT_TIMEOUT, 600);


        if (!empty($data)) {
            $c->setopt(CURLOPT_POSTFIELDS, $data);
            $hdr = ['Content-Length: ' . strlen($data),'Content-Type: application/json','Accept: application/json', 'Authorization: Bearer ' . $this->token];
            $c->setopt(CURLOPT_HTTPHEADER, $hdr);
        }else
        {
            $hdr = ['Content-Type: application/json','Accept: application/json', 'Authorization: Bearer ' . $this->token];
        }

        if (!$c->exec()) {
            print_r($c);
            $this->err = 'Network Error=>'.$c->err;
            return false;
        }

        $this->rescode=$c->rcode;
        $this->result = json_decode($c->result,true);
        if(!isset($this->result['request_success'])&&!isset($this->result['success']))
        {
            $this->log->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".json_encode($data)."\nResponse Code: ".$this->rescode."\nResponse: ".PHP_EOL.PHP_EOL);
        }else
        {
            $this->log->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".json_encode($data)."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result.PHP_EOL.PHP_EOL);
        }
        return $this->result;
    }
    
    public static function canDeliver($shipment,$orgRate)
    {
        $packages = self::getPackages($shipment);
        foreach ($packages as $key => $package) {
            if($package->height>=240||$package->length>=240||$package->width>=240)
            {
                return false;
            }
        }

        if($shipment->pkg>98)
        {
            return false;
        }
        $or = OrgRate::model()->findByPk($orgRate);
        $o = new stdClass();
        $o->code = 0;
        $o->msg="";
        $o->data = 0;

        $price = 0;
        $postcode = $shipment->cnee->postcode;
        $suburb = $shipment->cnee->suburb;
        $weight = $shipment->weight;

        $zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode]);
        $chargeCode = 'xxxxxxxxx';
        if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
            $chargeCode = $zoneMap['z1'];
        }
        $zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate]);
        if (!empty($zrs))
        {
            $remoteCostRate = RemoteChargeRate::model()->find("rate_id = :rateId and (postcode =:postcode or CONCAT('0',postcode)=:postcode) and upper(suburb) = :suburb and (perkg+base)>0",[":rateId"=>$or->id,":postcode"=>$postcode,":suburb"=>strtoupper($suburb)]);
            if(!empty($remoteCostRate))
            {
                return false;
            }
            return true;
        }else
        {
            return false;
        }
    }

    public static function getCost($orgRate, $suburb, $postcode, $weight)
    {
        $or = OrgRate::model()->findByPk($orgRate);
        $o = new stdClass();
        $o->code = 0;
        $o->msg="";
        $o->data = 0;

        $price = 0;
        $postcode = intval($postcode)+0;
        $zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode]);
        $zoneMap2 = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code AND suburb=:suburb', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode,':suburb' => $suburb]);
        if(!empty($zoneMap2))
        {
            $zoneMap = $zoneMap2;
        }
        $chargeCode = 'xxxxxxxxx';
        if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
            $chargeCode = $zoneMap['z1'];
        }
        $zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate]);
        if (!empty($zrs)) {
            // get maximum one
            foreach ($zrs as $zr) {
                $temp = $zr['base'] + $zr['item'];
                if ($zr['nkg'] > 0) {
                    $wl = $weight;
                    $temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
                } else {
                    $temp +=  $weight * $zr['perkg'];
                }
                if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
                    $temp = $zr['minimum'];
                }
                $price = max($price, $temp);
            }
            
        }else
        {
            $o->code = 1;
            $o->msg="no rate for this serivce";
            return $o;
        }

        $o->msg = "success";
        $o->data = $price;
        return $o;
    }

    public function sendRequest($verb, $body)
    {

        $this->_logger->debug("final POST Url:".$this->workUrl . $verb);
        $header = array('Content-type: application/json', 'Authorization: Bearer ' . $this->token);
        $this->_logger->debug("Header",$header);
        $this->_logger->debug("Post Body",array($body));
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->workUrl . $verb);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        $response = curl_exec($curl);
        $this->_logger->debug("Output",array($response));
        curl_close($curl);
        $this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".urldecode($data)."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result.PHP_EOL.PHP_EOL);
        return $response;
    }


    public function sendGetRequest($verb)
    {

        $this->_logger->debug("final GET Url:".$this->workUrl . $verb);
        $header = array('Content-type: application/json', 'Authorization: Bearer ' . $this->token);
        $this->_logger->debug("Header",$header);

        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $this->workUrl . $verb);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        $response = trim(curl_exec($ch));
        $this->_logger->debug("Output",array($response));
        curl_close($ch);
        $this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".urldecode($data)."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result.PHP_EOL.PHP_EOL);
        return $response;
    }
    
    public function getBreakWeight($weight)
    {
        // if ($weight <= 0.500) {
        //     return $weight;
        // } elseif ($weight <= 1.0) {
        //     return $this->doTheWeight($weight, 0.5, 1.0, 0.4, 0.5);
        // } elseif ($weight <= 2) {
        //     return $this->doTheWeight($weight, 1, 2, 0.8, 1.0);
        // } elseif ($weight <= 3) {
        //     return $this->doTheWeight($weight, 2, 3, 1.8, 2.0);
        // } elseif ($weight <= 4) {
        //     return $this->doTheWeight($weight, 3, 4, 2.5, 3.0);
        // } elseif ($weight <= 5) {
        //     return $this->doTheWeight($weight, 4, 5, 3.5, 4.0);
        // } elseif ($weight <= 7) {
        //     return $this->doTheWeight($weight, 5, 7, 4.5, 5.0);
        // } elseif ($weight <= 10) {
        //     return round($weight * rand(80, 100) / 100, 3);
        // } else {
        //     return round($weight, 3);
        // }
        return  round($weight * 100) / 100;
    }

    public function removeSpecialChars($s){
        return preg_replace(['/[^\d\w\'\"\,\;\.\/\(\):&# \-]+/', '/[\’]+/'], ['', '\''] , $s);
    }
    public function prepareLabelData($shipment,$isUpdate = false,$serviceCode,$consignmentId = 0,$consignmentData = false)
    {
        $predata=new stdClass();
        $predata->referenceNo=$shipment->id;
        if(empty($serviceCode))
        {
            $orgRateId= $shipment->mdata['org_rate_id'];
            $orgRate = OrgRate::model()->findByPk($orgRateId);
            $serviceCode = "IPEC";
        }

        $predata->recipientName=substr($this->removeSpecialChars($shipment->cnee->name), 0, 35);
        $predata->recipientCompany=substr($this->removeSpecialChars($shipment->cnee->company), 0, 49);
        $predata->email= substr($shipment->cnee->email, 0, 50);
        $predata->phone=substr($shipment->cnee->tel, 0, 20);
        $cnee_addr = $this->removeSpecialChars($shipment->cnee->address);
        $predata->addressLine1= substr($cnee_addr, 0, 40);
        if (strlen($cnee_addr) > 40) {
            $predata->addressLine2= substr($cnee_addr, 40, 100);
        }
        $predata->city=$shipment->cnee->suburb;
        $predata->state=$shipment->cnee->state;
        $predata->postcode=$shipment->cnee->postcode;
        $predata->country='AU';
        $predata->weight= $this->getBreakWeight($shipment->weight);
        if(!empty($consignmentId))
        {
            $predata->length = number_format($consignmentData->length,0,'.','');
            $predata->width =   number_format($consignmentData->width,0,'.','');
            $predata->height =  number_format($consignmentData->height,0,'.','');
            $predata->weight= $this->getBreakWeight($consignmentData->weight);
            $predata->consignmentId = $consignmentId;
        }
        // $predata->volume=$shipment->cbm*($shipment->pkg);
        $predata->invoiceValue=$shipment->dvalue;
        $predata->invoiceCurrency='AUD';
        $predata->description=substr($shipment->getGoods(), 0, 50);
        $predata->serviceCode=$serviceCode;

        $name=$shipment->agent_id;
        $shipper = Org::model()->findByPk($this->orgRate->mdata['ddpt_id']);
        $org=$shipment->agent;
        if (!empty($org->extra['delivery_label_name'])) {
            $name=$org->extra['delivery_label_name'];
        }
        if($shipper->suburb=='BANKSTOWN AERODROME')
        {
            $shipper->postcode = '2200';
        }

        $predata->shipperName=$name;
        $predata->shipperAddressLine1=substr($this->fromInfo['address'], 0, 80);
        $predata->shipperCity=substr($this->fromInfo['suburb'], 0, 80);
        $predata->shipperState=substr($this->fromInfo['state'], 0, 80);
        $predata->shipperPostcode=substr($this->fromInfo['postcode'], 0, 80);
        $predata->shipperCountry='Australia';
        $predata->shipperPhone= Org::IM_COMPANY_PHONE;
        $predata->returnName= $name;
        $predata->returnAddressLine1=$this->fromInfo['address'];
        $predata->returnCity=$this->fromInfo['suburb'];
        $predata->returnState = $this->fromInfo['state'];
        $predata->returnPostcode = $this->fromInfo['postcode'];
        $predata->returnCountry = 'Australia';


        return $predata;
    }

}