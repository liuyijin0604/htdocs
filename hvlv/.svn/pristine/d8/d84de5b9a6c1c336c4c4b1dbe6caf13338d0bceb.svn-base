<?php
class CargoProcessService extends Service
{
    const MIN_STORAGE_FEE_PER_DAY = 5.00;
    const STORAGE_RATE = 5.00;      // $5.00 per CBM per day
    public static function getSumCargoProcessCreateTodayPeriod($depot)
    {
        $result = [];
        $totalCbm = $totalWeight = $totalPkgs = $total = 0;
        $fbaTotalCbm = $fbaTotalWeight = $fbaTotalPkgs = $fbatotal = 0;
        $btbTotalCbm = $btbTotalWeight = $btbTotalPkgs = $btbtotal = 0;
        $norTotalCbm = $norTotalWeight = $norTotalPkgs = $nortotal = 0;
        $perTotalCbm = $perTotalWeight = $perTotalPkgs = $pertotal = 0;
        $toDay = date("Y-m-d");
        $yesterDay = date("Y-m-d", strtotime("-1 day"));
        $models = CargoProcess::model()->findAllBySql('select * from cargo_process where dpt_id = '.$depot.' and status < 97 and type in (1,2,4) and started >= "' . $yesterDay . ' 17:00:00" and started <= "' . $toDay . ' 16:59:59"');
        if (!empty($models)) {
            $result['pkgtotals'] = 0;
            $result['cbmtotals'] = 0;
            $result['weighttotals'] = 0;
            foreach ($models as $model) {
                if ($model->shipment->status != 100) {
                    $total++;
                    $perTotalPkgs = $model->getPackages();
                    $result['pkgs'][] = $perTotalPkgs;
                    $totalPkgs += $perTotalPkgs;

                    $perTotalCbm = number_format($model->getTotalCBM(), 3, '.', '');
                    $result['cbm'][] = $perTotalCbm;
                    $totalCbm += $perTotalCbm;

                    $perTotalWeight = number_format($model->getWeight(), 3, '.', '');
                    $result['weight'][] = $perTotalWeight;
                    $totalWeight += $perTotalWeight;

                    if ($model->type == 1) {
                        $norTotalPkgs += $perTotalPkgs;
                        $norTotalCbm += $perTotalCbm;
                        $norTotalWeight += $perTotalWeight;
                        $nortotal++;
                    } elseif ($model->type == 2) {
                        $fbaTotalPkgs += $perTotalPkgs;
                        $fbaTotalCbm += $perTotalCbm;
                        $fbaTotalWeight += $perTotalWeight;
                        $fbatotal++;
                    } elseif ($model->type == 4) {
                        $btbTotalPkgs += $perTotalPkgs;
                        $btbTotalCbm += $perTotalCbm;
                        $btbTotalWeight += $perTotalWeight;
                        $btbtotal++;
                    }
                }
            }
            $result['total'] = $total;
            $result['pkgtotals'] = $totalPkgs;
            $result['cbmtotals'] = number_format($totalCbm, 3, '.', '');
            $result['weighttotals'] = number_format($totalWeight, 3, '.', '');

            $result['fbatotal'] = $fbatotal;
            $result['fbapkgtotals'] = $fbaTotalPkgs;
            $result['fbacbmtotals'] = number_format($fbaTotalCbm, 3, '.', '');
            $result['fbaweighttotals'] = number_format($fbaTotalWeight, 3, '.', '');

            $result['btbtotal'] = $btbtotal;
            $result['btbpkgtotals'] = $btbTotalPkgs;
            $result['btbcbmtotals'] = number_format($btbTotalCbm, 3, '.', '');
            $result['btbweighttotals'] = number_format($btbTotalWeight, 3, '.', '');

            $result['nortotal'] = $nortotal;
            $result['norpkgtotals'] = $norTotalPkgs;
            $result['norcbmtotals'] = number_format($norTotalCbm, 3, '.', '');
            $result['norweighttotals'] = number_format($norTotalWeight, 3, '.', '');
        }
        return $result;
    }

    public static function getSumCargoProcessDoneTodayPeriod($depot)
    {
        $result = [];
        $totalCbm = $totalWeight = $totalPkgs = $total =  0;
        $fbaTotalCbm = $fbaTotalWeight = $fbaTotalPkgs = $fbatotal = 0;
        $btbTotalCbm = $btbTotalWeight = $btbTotalPkgs = $btbtotal = 0;
        $norTotalCbm = $norTotalWeight = $norTotalPkgs = $nortotal = 0;
        $perTotalCbm = $perTotalWeight = $perTotalPkgs = $pertotal = 0;
        $toDay = date("Y-m-d");
        $yesterDay = date("Y-m-d", strtotime("-1 day"));
        $models = CargoProcess::model()->findAllBySql('select * from cargo_process where dpt_id = '.$depot.' and status = 100 and type in (1,2,4) and comp_date >= "' . $yesterDay . ' 17:00:00" and comp_date <= "' . $toDay . ' 16:59:59"');
        if (!empty($models)) {
            $result['pkgtotals'] = 0;
            $result['cbmtotals'] = 0;
            $result['weighttotals'] = 0;
            foreach ($models as $model) {
                if ($model->shipment->status != 100) {
                    $total++;
                    $perTotalPkgs = $model->getPackages();
                    $result['pkgs'][] = $perTotalPkgs;
                    $totalPkgs += $perTotalPkgs;

                    $perTotalCbm = number_format($model->getTotalCBM(), 3, '.', '');
                    $result['cbm'][] = $perTotalCbm;
                    $totalCbm += $perTotalCbm;

                    $perTotalWeight = number_format($model->getWeight(), 3, '.', '');
                    $result['weight'][] = $perTotalWeight;
                    $totalWeight += $perTotalWeight;

                    if ($model->type == 1) {
                        $norTotalPkgs += $perTotalPkgs;
                        $norTotalCbm += $perTotalCbm;
                        $norTotalWeight += $perTotalWeight;
                        $nortotal++;
                    } elseif ($model->type == 2) {
                        $fbaTotalPkgs += $perTotalPkgs;
                        $fbaTotalCbm += $perTotalCbm;
                        $fbaTotalWeight += $perTotalWeight;
                        $fbatotal++;
                    } elseif ($model->type == 4) {
                        $btbTotalPkgs += $perTotalPkgs;
                        $btbTotalCbm += $perTotalCbm;
                        $btbTotalWeight += $perTotalWeight;
                        $btbtotal++;
                    }
                }
            }
            $result['total'] = $total;
            $result['pkgtotals'] = $totalPkgs;
            $result['cbmtotals'] = number_format($totalCbm, 3, '.', '');
            $result['weighttotals'] = number_format($totalWeight, 3, '.', '');

            $result['fbatotal'] = $fbatotal;
            $result['fbapkgtotals'] = $fbaTotalPkgs;
            $result['fbacbmtotals'] = number_format($fbaTotalCbm, 3, '.', '');
            $result['fbaweighttotals'] = number_format($fbaTotalWeight, 3, '.', '');

            $result['btbtotal'] = $btbtotal;
            $result['btbpkgtotals'] = $btbTotalPkgs;
            $result['btbcbmtotals'] = number_format($btbTotalCbm, 3, '.', '');
            $result['btbweighttotals'] = number_format($btbTotalWeight, 3, '.', '');

            $result['nortotal'] = $nortotal;
            $result['norpkgtotals'] = $norTotalPkgs;
            $result['norcbmtotals'] = number_format($norTotalCbm, 3, '.', '');
            $result['norweighttotals'] = number_format($norTotalWeight, 3, '.', '');
        }
        return $result;
    }

    public static function getSumCargoProcessLeft($depot)
    {
        $result = [];
        $totalCbm = $totalWeight = $totalPkgs = $total = 0;
        $fbaTotalCbm = $fbaTotalWeight = $fbaTotalPkgs = $fbatotal = 0;
        $btbTotalCbm = $btbTotalWeight = $btbTotalPkgs = $btbtotal = 0;
        $norTotalCbm = $norTotalWeight = $norTotalPkgs = $nortotal = 0;
        $perTotalCbm = $perTotalWeight = $perTotalPkgs = $pertotal = 0;
        //$models = CargoProcess::model()->findAllBySql('select * from cargo_process left join shipment on cargo_process.shipment_id = shipment.id where cargo_process.dpt_id = '.$depot.' and cargo_process.status < 100 and cargo_process.type in (1,2,4) and shipment.status !=100');
        $models = CargoProcess::model()->findAllBySql('select * from cargo_process where dpt_id = '.$depot.' and status < 97 and type in (1,2,4)');
        if (!empty($models)) {
            //$result['total'] = 0;
            $result['pkgtotals'] = 0;
            $result['cbmtotals'] = 0;
            $result['weighttotals'] = 0;
            foreach ($models as $model) {
                if ($model->shipment->status < 100 && !empty($model->shipment->status)) {
                    $total++;

                    $perTotalPkgs = $model->getPackages();
                    $result['pkgs'][] = $perTotalPkgs;
                    $totalPkgs += $perTotalPkgs;

                    $perTotalCbm = number_format($model->getTotalCBM(), 3, '.', '');
                    $result['cbm'][] = $perTotalCbm;
                    $totalCbm += $perTotalCbm;

                    $perTotalWeight = number_format($model->getWeight(), 3, '.', '');
                    $result['weight'][] = $perTotalWeight;
                    $totalWeight += $perTotalWeight;

                    if ($model->type == 1) {
                        $norTotalPkgs += $perTotalPkgs;
                        $norTotalCbm += $perTotalCbm;
                        $norTotalWeight += $perTotalWeight;
                        $nortotal++;
                    } elseif ($model->type == 2) {
                        $fbaTotalPkgs += $perTotalPkgs;
                        $fbaTotalCbm += $perTotalCbm;
                        $fbaTotalWeight += $perTotalWeight;
                        $fbatotal++;
                    } elseif ($model->type == 4) {
                        $btbTotalPkgs += $perTotalPkgs;
                        $btbTotalCbm += $perTotalCbm;
                        $btbTotalWeight += $perTotalWeight;
                        $btbtotal++;
                    }
                }
            }
            $result['total'] = $total;
            $result['pkgtotals'] = $totalPkgs;
            $result['cbmtotals'] = number_format($totalCbm, 3, '.', '');
            $result['weighttotals'] = number_format($totalWeight, 3, '.', '');

            $result['fbatotal'] = $fbatotal;
            $result['fbapkgtotals'] = $fbaTotalPkgs;
            $result['fbacbmtotals'] = number_format($fbaTotalCbm, 3, '.', '');
            $result['fbaweighttotals'] = number_format($fbaTotalWeight, 3, '.', '');

            $result['btbtotal'] = $btbtotal;
            $result['btbpkgtotals'] = $btbTotalPkgs;
            $result['btbcbmtotals'] = number_format($btbTotalCbm, 3, '.', '');
            $result['btbweighttotals'] = number_format($btbTotalWeight, 3, '.', '');

            $result['nortotal'] = $nortotal;
            $result['norpkgtotals'] = $norTotalPkgs;
            $result['norcbmtotals'] = number_format($norTotalCbm, 3, '.', '');
            $result['norweighttotals'] = number_format($norTotalWeight, 3, '.', '');
        }
        return $result;
    }

    public static function getYesterdayLeft($depot)
    {
        if($depot==106){
            $type=2;
        }elseif($depot==218){
            $type=3;
        }elseif($depot==530){
            $type=18;
        }
        $result = [];
        $yesterDay = date("Y-m-d", strtotime("-1 day"));
        $model = ReportCache::model()->findBySql('select * from report_cache where type = '.$type.' and status = 1 and create_time = "' . $yesterDay . '"');
        if (!empty($model->mdata['todayLeft'])) {
            $result = $model->mdata['todayLeft'];
        }
        return $result;
    }

    public static function getDeliveryDaysDiff($depot){
        $result = [];
        $numTotalCount = $numEffecticeCount = $numGoodDelivery = 0;
        $arrGooddelivery = $arrNotGooddelivery =  [];
        $beginDate = date('Y-m-01',strtotime(date("Y-m-d")));
        $models = CargoProcess::model()->findAllBySql('select * from cargo_process where dpt_id = '.$depot.' and status = 100 and type in (1) and comp_date >= "' . $beginDate . ' 00:00:00" and JSON_EXTRACT(meta, "$.approveuser") is null');
        if(!empty($models)){
            foreach($models as $model){
                $numTotalCount++;
                if($model->getDateDiff()<4){
                    $numEffecticeCount++;
                }
                if($model->isUploadPodOnTime()){
                    $numGoodDelivery++;
                    $arrGooddelivery[] = $model->ref;
                }else{
                    $arrNotGooddelivery[] = $model->ref;
                }
            }
            $result['diffb2ctotal'] = $numTotalCount;
            $result['diffb2ceffecttotal'] = $numEffecticeCount;
            $result['diffb2cgooddelivery'] = $numGoodDelivery;
            $result['arrGooddelivery'] = $arrGooddelivery;
            $result['arrNotGooddelivery'] = $arrNotGooddelivery;
        }
        return $result;
    }

    public static function getDeliveryDaysDiffB2B($depot){
        $result = [];
        $numTotalCount = $numEffecticeCount = $numGoodDelivery = 0;
        $arrGooddelivery = $arrNotGooddelivery =  [];
        $beginDate = date('Y-m-01',strtotime(date("Y-m-d")));
        $models = CargoProcess::model()->findAllBySql('select * from cargo_process where dpt_id = '.$depot.' and status = 100 and type in (4) and comp_date >= "' . $beginDate . ' 00:00:00" and JSON_EXTRACT(meta, "$.approveuser") is null');
        if(!empty($models)){
            foreach($models as $model){
                $numTotalCount++;
                if($model->getDateDiff()<6){
                    $numEffecticeCount++;
                }
                if($model->isUploadPodOnTime()){
                    $numGoodDelivery++;
                    $arrGooddelivery[] = $model->ref;
                }else{
                    $arrNotGooddelivery[] = $model->ref;
                }
            }
            $result['diffb2btotal'] = $numTotalCount;
            $result['diffb2beffecttotal'] = $numEffecticeCount;
            $result['diffb2bgooddelivery'] = $numGoodDelivery;
            $result['arrGooddelivery'] = $arrGooddelivery;
            $result['arrNotGooddelivery'] = $arrNotGooddelivery;
        }
        return $result;
    }

    public static function getDeliveryDaysDiffFBA($depot){
        $result = [];
        $numTotalCount = $numEffecticeCount = $numGoodDelivery = 0;
        $arrGooddelivery = $arrNotGooddelivery =  [];
        $beginDate = date('Y-m-01',strtotime(date("Y-m-d")));
        $models = CargoProcess::model()->findAllBySql('select * from cargo_process where dpt_id = '.$depot.' and status = 100 and type in (2) and comp_date >= "' . $beginDate . ' 00:00:00" and JSON_EXTRACT(meta, "$.approveuser") is null');
        if(!empty($models)){
            foreach($models as $model){
                $numTotalCount++;
                if($model->getDateDiff()<6){
                    $numEffecticeCount++;
                    
                }
                if($model->isUploadPodOnTime()){
                    $numGoodDelivery++;
                    $arrGooddelivery[] = $model->ref;
                }else{
                    $arrNotGooddelivery[] = $model->ref;
                }
            }
            $result['difffbatotal'] = $numTotalCount;
            $result['difffbaeffecttotal'] = $numEffecticeCount;
            $result['difffbagooddelivery'] = $numGoodDelivery;
            $result['arrGooddelivery'] = $arrGooddelivery;
            $result['arrNotGooddelivery'] = $arrNotGooddelivery;
        }
        return $result;
    }

    public static function getCargoPorcessDailyReport($depot)
    {
        $result = [];
        $yesterdayLeft = self::getYesterdayLeft($depot);
        $todayNew = self::getSumCargoProcessCreateTodayPeriod($depot);
        $todayClose = self::getSumCargoProcessDoneTodayPeriod($depot);
        $todayLeft = self::getSumCargoProcessLeft($depot);
        $effectiveToday = self::getDeliveryDaysDiff($depot);
        $effectiveTodayB2B = self::getDeliveryDaysDiffB2B($depot);
        $effectiveTodayFBA = self::getDeliveryDaysDiffFBA($depot);
        $result['yesterdayLeft'] = $yesterdayLeft;
        $result['todayNew'] = $todayNew;
        $result['todayClose'] = $todayClose;
        $result['todayLeft'] = $todayLeft;
        $result['effective'] = $effectiveToday;
        $result['effectiveb2b'] = $effectiveTodayB2B;
        $result['effectivefba'] = $effectiveTodayFBA;
        return $result;
    }

    public static function getShipmentCargoProcessCost($model,$cargoProcessId = null)
    {
        $orgRateService = new OrgRateService();
        $cost1 = 0;
        $sysBaseCost = 0;
        //for cargoprocess cost and costzone
        $modelCargoPorcesses = CargoProcess::model()->findAll('shipment_id = :shipment_id and status !=:status',[':shipment_id'=>$model->id,':status'=>CargoProcess::DELETED]);
        $sysInterstateCost=$sysBaseCost=0;
        if(!empty($modelCargoPorcesses)){
            foreach($modelCargoPorcesses as $modelCargoPorcess){
                if(!empty($cargoProcessId)&&$modelCargoPorcess->id!=$cargoProcessId) continue;
                $numOTInvoice = $modelCargoPorcess->getRelatedOTFee();
                $cargoProcessFee = $modelCargoPorcess->getCargoProcessFeeReCal();
                $sysInterstateCost = $modelCargoPorcess->getSystemInterstateCost();
                $sysBaseCost = $modelCargoPorcess->getSystemBaseCost();
                if($cargoProcessFee!="n/a")
                {
                    $cost1+=$cargoProcessFee;
                }elseif($sysInterstateCost!="n/a"&&!empty($sysInterstateCost))
                {
                    $cost1+=$sysInterstateCost;
                }
                $numOrgRate = !empty($modelCargoPorcess->mdata['orgRateId'])? $modelCargoPorcess->mdata['orgRateId']:0;
                $costZone = $orgRateService->getShipmentCostZone($numOrgRate,$model);
            }
		    if(count($modelCargoPorcesses)==1&&$cost1<0.001)
        	{
            	return $sysBaseCost;
        	}
        }
		return $cost1;
    }

    public function calculateCargoProcessStorageFee($end, $shipment)
    {
        $oldInvoice = Invoice::model()->find('type = :type AND pid = :pid AND to_id = :aid AND status != :status', array(':type'=>Invoice::INVOICE_TYPE_CG_STORAGE, ':pid' => $shipment->id, ':aid' => $shipment->agent_id, ':status' => Invoice::INVOICE_STATUS_CACELLED));
        if (!empty($shipment->mdata['cg_storage_invoiced_to']) && !empty($oldInvoice)) {   // previously have cargo storage charged
            $storageStart = date('Y-m-d', strtotime($shipment->mdata['cg_storage_invoiced_to'] . '+1 day'));
        } else {
            $cargoProcess = CargoProcess::model()->find('shipment_id = :sid AND status != :status', array(':sid' => $shipment->id, ':status' => CargoProcess::DELETED));
            if (!empty($cargoProcess)) {
                $storageStart = date('Y-m-d', strtotime($cargoProcess->started . '+15 day'));
            } else if (!empty($shipment->mdata['scan_time'])) {
                $storageStart = date('Y-m-d', strtotime($shipment->mdata['scan_time'] . '+15 day'));
            } else if (!empty($shipment->consol->mdata['ContainerUnloadDate'])) {
                $storageStart = date('Y-m-d', strtotime($shipment->consol->mdata['ContainerUnloadDate'] . '+15 day'));
            } else {
                $storageStart = date('Y-m-d', strtotime('+15 day'));
            }
        }
        
        $storageDays = round((strtotime($end) - strtotime($storageStart)) / (60 * 60 * 24));
        $weekendDays = HolidayHelper::getWeekendDaysBetweenTwoDays($storageStart, $end, $shipment->consol->pod);
        $storageDays -= $weekendDays;
        if ($storageDays <= 0) {
            return array(0.00, 0, $storageStart, $end);
        } else {
            if (isset($shipment->mdata['delivery_booking_cancelled_by']) && $shipment->mdata['delivery_booking_cancelled_by'] == 'internal') {
                /**
                 * For bookings that was cancelled by internal staff, don't charge any more storage fee
                 */
                return array(0.00, $storageDays, $storageStart, $end);
            } else {
                if ($shipment->cbm * self::STORAGE_RATE < self::MIN_STORAGE_FEE_PER_DAY) {
                    return array(number_format((float)($storageDays * self::MIN_STORAGE_FEE_PER_DAY), 2, '.', ''), $storageDays, $storageStart, $end);
                } else {
                    return array(number_format((float)($storageDays * $shipment->cbm * self::STORAGE_RATE), 2, '.', ''), $storageDays, $storageStart, $end);
                }     
            }  
        }
    }

}
