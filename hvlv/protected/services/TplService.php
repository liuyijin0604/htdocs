<?php
class TplService extends Service
{
    public function getTplNewTask($depot)
    {
        [$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
        $result = $invNo = [];
        $result['totalcount'] = 0;
        $result['totalinvcount'] = 0;
        $result['totalinvno'] = 0;
        $result['totalcostcount'] = 0;
        // $toDay = date("Y-m-d", strtotime("-1 day"));
        // $yesterDay = date("Y-m-d", strtotime("-2 day"));
        $toDay = date("Y-m-d");
        $yesterDay = date("Y-m-d", strtotime("-1 day"));
        $numInv = $numCost = $wmsCost = $wmsSurcharge = 0;
        $models = WmsTask::model()->findAllBySql('select * from wms_task where link_id = 0 and dpt_id = ' . $depot . ' and status !=40 and status != 10 and schd_time >= "' . $yesterDay . ' 10:00:00" and schd_time <= "' . $toDay . ' 09:59:59"');
        if (!empty($models)) {
            $result['totalcount'] = count($models); //number
        }
        $modelInvs = Invoice::model()->findAllBySql('select * from invoice where dpt_id = ' . $depot . ' and status != 10 and status != 8 and dpmt = 40 and posted >= "' . $yesterDay . ' 10:00:00" and posted <= "' . $toDay . ' 09:59:59"');
        if (!empty($modelInvs)) {
            foreach ($modelInvs as $modelInv) {
                if (preg_match('/-/i', $modelInv->no)) {
                    continue;
                }
                //inv for task
                $numInv += $modelInv->total - $modelInv->gst;
                $invNo[] = $modelInv->no;
                if (preg_match('/WM/i', $modelInv->no)) {
                    $objInvoiceLines = $modelInv->lines;
                    if (!empty($objInvoiceLines)) {
                        foreach ($objInvoiceLines as $objInvoiceLine) {
                            $wmsTask = WmsTask::model()->findByPk($objInvoiceLine->fid);
                            if ($objInvoiceLine->det == "Delivery") {
                                if (!empty($wmsTask)) {
                                    $shipment = ImParcel::model()->findByPk($wmsTask->mdata['shipment_id']);
                                }
                                $floatWeight = empty($shipment->weight) ? 0 : $shipment->weight;
                                $strPackage = $wmsTask->mainTask->packTask->mdata['pkg'];
                                $wmsLength = $wmsWidth = $wmsHeight = $wmsPkgs = 0;
                                if (!empty($strPackage)) {
                                    foreach (json_decode($strPackage, true) as $pkg) {
                                        if (!empty($pkg['w']) || !empty($pkg['h']) || !empty($pkg['d'])) {
                                            $wmsLength += $pkg['w'] / 100;
                                            $wmsWidth += $pkg['h'] / 100;
                                            $wmsHeight += $pkg['d'] / 100;
                                            $wmsPkgs++;
                                        }
                                    }
                                }
                                $wmsCourierId = $wmsTask->mdata['courier'];
                                $wmsPostcode = $wmsTask->mdata['cnee']['postcode'];
                                $wmsSuburb = $wmsTask->mdata['cnee']['suburb'];
                                $orgRate = $wmsTask->chooseOrgRate($wmsCourierId, $wmsTask->dpt_id);
                                if ($wmsTask->mdata['courier'] == '3702' || $wmsTask->mdata['courier'] == '3701') {
                                    $wmsCost = round(ImcoConsol::getCourierTestCostPrice($orgRate, $wmsPostcode, $floatWeight, $wmsPkgs, false, $wmsSuburb)['price']);
                                } else {
                                    $wmsCost = round(ImcoConsol::getCourierCostPrice($orgRate, $wmsPostcode, $floatWeight, $wmsPkgs, "", $wmsSuburb, false, '', $shipment), 2);
                                }
                                $wmsSurcharge = round($wmsTask->deliveryTaskSurCharge($wmsCourierId, $wmsLength, $wmsWidth, $wmsHeight, $floatWeight), 2);
                                //cost for task
                                if(($wmsCost + $wmsSurcharge)>9999){
                                    $numCost += 0;
                                    $taskNoDelivery[$objInvoiceLine->fid]= 0;
                                }else{
                                    $numCost += $wmsCost + $wmsSurcharge;
                                    $taskNoDelivery[$objInvoiceLine->fid]= $wmsCost+$wmsSurcharge;
                                }
                                //stroage cost
                            }elseif($objInvoiceLine->det == "Warehouse Storage"){
                                if(!empty($objInvoiceLine->mdata['regular'][0])){
                                    $numStorage = is_int($objInvoiceLine->mdata['regular'][0])? $objInvoiceLine->mdata['regular'][0] * 3.68 : 0;
                                    $numCost += $numStorage;
                                }
                                if(!empty($objInvoiceLine->mdata['oversize'][0])){
                                    $numStorage = is_int($objInvoiceLine->mdata['oversize'][0])? $objInvoiceLine->mdata['oversize'][0] * 7.36 : 0;
                                    $numCost += $numStorage;
                                }
                                $taskNoStorage[$objInvoiceLine->fid] = $numStorage;
                            }
                        }
                    }
                }
            }
            //inv
            $result['totalinvcount'] = $numInv;
            $result['totalinvno'] = $invNo;
            //cost
            $result['totalcostcount'] = $numCost;
            $result['totaltaskdeliverycost']= $taskNoDelivery;
            $result['totaltaskstoragecost']=$taskNoStorage;
        }
        Yii::app()->name = $app_name;
        return $result;
    }

    public function getTplLeftTask($depot)
    {
        $result = [];
        $toDay = date("Y-m-d");
        $result['totalcount'] = 0;
        $models = WmsTask::model()->findAllBySql('select * from wms_task where link_id = 0 and dpt_id = ' . $depot . ' and status !=40 and status != 10 and  status < 99 and schd_time <= "' . $toDay . ' 09:59:59"');
        if (!empty($models)) {
            $result['totalcount'] = count($models); //number
        }
        return $result;
    }

    public function getTplDoneTodayTask($depot)
    {
        $result = [];
        $result['totalcount'] = 0;
        $toDay = date("Y-m-d");
        $yesterDay = date("Y-m-d", strtotime("-1 day"));
        $models = WmsTask::model()->findAllBySql('select * from wms_task where link_id = 0 and dpt_id = ' . $depot . ' and status = 99 and schd_time >= "' . $yesterDay . ' 10:00:00" and schd_time <= "' . $toDay . ' 09:59:59"');
        if (!empty($models)) {
            $result['totalcount'] = count($models); //number
        }
        return $result;
    }

    public static function getTPLDailyReport($depot){
        $result = [];
        $todayNew = self::getTplNewTask($depot);
        $todayDone = self::getTplDoneTodayTask($depot);
        $todayLeft = self::getTplLeftTask($depot);
        $result['todayNew'] = $todayNew;
        $result['todayDone'] = $todayDone;
        $result['todayLeft'] = $todayLeft;
        return $result;
    }
}
