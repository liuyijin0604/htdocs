<?php
class CargoProcessJobService extends Service
{
    public static function getCostofCJob($c_JobId)
    {
        $numTotalCost = 0;

        if (!empty($c_JobId)) {
            $modelCJob = CargoProcessJob::model()->findByPk($c_JobId);

            if (!empty($modelCJob)) {
                $modelDriverOrgId = $modelCJob->driver->id;
                if (!empty($modelDriverOrgId)) {
                    $orgRateId = self::getOrgRateByKPCourierOrgId($modelDriverOrgId);
                    if (empty($orgRateId)) {
                        return 0;
                    }
                }

                $modelCJobRelations = $modelCJob->job_relations;
                if (!empty($modelCJobRelations)) {
                    foreach ($modelCJobRelations as $modelCJobRelation) {
                        $numTotalCost += self::getCargoProcessCostInCJobByCargoProcessIdAndOrgrateId($modelCJobRelation->cargo_process->id, $orgRateId);
                    }
                }
            }
        }
        return $numTotalCost;
    }

    public static function getCargoProcessCostInCJobByCargoProcessIdAndOrgrateId($cargoProcessId, $orgRateId)
    {
        $orgRate = OrgRate::model()->findByPk($orgRateId);
        if (empty($orgRate)) {
            return 0;
        }
        $modelCargoProcess = CargoProcess::model()->findByPk($cargoProcessId);
        $modelCargoProcessShipment = $modelCargoProcess->shipment;
        $cargoProcessJob = $modelCargoProcess->job_relation->job;
        if (!empty($modelCargoProcessShipment)) {
            $postcode = $modelCargoProcessShipment->cnee->postcode;
            $weight = $modelCargoProcessShipment->chargeWeight();
            //$packs = $modelCargoProcessShipment->pkg;
            $suburb = $modelCargoProcessShipment->cnee->suburb;
            $cbm = $modelCargoProcessShipment->getTotalCBM();

            //is org tick compare cbm weight
            //$numWeight = max($weight, $cbm * 250); 
            if(@$orgRate->org->extra['is_cbm_weight']==1){
                //$numWeight = max($weight, $cbm * 250); 
                /**
                 * Updated by Alex 2023-07-21
                 * disable cbm weight
                 * IT00572674
                 */
                $numWeight =  $weight;
            }else{
                $numWeight =  $weight;
            }

            $numInterstateCost = 0;
            if($modelCargoProcess->was_interstate==1){
                $numInterstateCost = !empty($modelCargoProcess->mdata['inputcostinterstate'])? $modelCargoProcess->mdata['inputcostinterstate']:0;
            }else{
                $numInterstateCost = 0;
            }
            
            //fba & b2b use pallet org rate;
            $numPallets=$numTotalPallets=0;
            if ($modelCargoProcess->type == 2 or $modelCargoProcess->type == 4) {
                if(!empty($modelCargoProcess->job_relation->job)){
                    //input cjob cost
                    $numCjobCost = 0;
                    // Cjob total palltes skip same one;
                    $numTotalPallets = max($modelCargoProcess->job_relation->job->getTotalPltNo(),0);
                    if(!empty($modelCargoProcess->job_relation->job->mdata['total_job_input_cost'])){
                        $numCjobCost = $modelCargoProcess->job_relation->job->mdata['total_job_input_cost'];
                    } else {
                        $numCjobCost = ImcoConsol::getCourierCostPrice($orgRate, $postcode, $numTotalPallets, 1, "", $suburb, false, '');
                    }
                    $jobWeight = $cargoProcessJob->getJobWeight();
                    if ($jobWeight == 0) {
                        return 0;
                    } else {
                        return round(($numCjobCost / $jobWeight) *  $modelCargoProcessShipment->weight + $numInterstateCost, 2);
                    }
                    /* // num pallets of cargo;
                    $numPallets = !empty($modelCargoProcess->mdata['job_pallet_no'])? $modelCargoProcess->mdata['job_pallet_no']:@$modelCargoProcess->shipment->mdata['amzon_pallet'];
                    if(empty($numPallets)){
                        $numPallets = @$modelCargoProcessShipment->getRackNumber()[1];
                    }
                    // Cjob total palltes not skip same one;
                    $numTotalPalletsNot = max($modelCargoProcess->job_relation->job->getTotalPltsNotSkip(),0);
                    $modelCargoProcess->mdata['costjobid'] = $modelCargoProcess->job_relation->job->id;
                    $modelCargoProcess->mdata['costplt'] = $numTotalPallets."/".$numTotalPalletsNot."/".$numPallets;
                    $modelCargoProcess->save();
                    if(($numPallets>0 && $numTotalPallets>0 && $numTotalPalletsNot>0 && $numTotalPalletsNot != 0) || ($numCjobCost>0 && $numTotalPallets>0 && $numTotalPalletsNot>0 && $numTotalPalletsNot != 0&&$numPallets>0)){
                        $cost = ImcoConsol::getCourierCostPrice($orgRate, $postcode, max($numTotalPallets,0), 1, "", $suburb, false, '');
                        if(!empty($numCjobCost) && is_numeric(floatval($numCjobCost))){
                            $cost = $numCjobCost; // instead of syscost;
                        }
                        $cost = ($cost/$numTotalPalletsNot)*$numPallets;
                        return $cost + $numInterstateCost;
                    }else{
                        return 0;
                    } */
                }else{
                    return 0;
                }
            }else{
                if(!is_numeric($numWeight)){
                    $numWeight = 0;
                }
                $modelCargoProcess->mdata['costjobid'] = $modelCargoProcess->job_relation->job->id;
                $modelCargoProcess->mdata['costweight'] = $numWeight;
                $modelCargoProcess->save();
                $cost = ImcoConsol::getCourierCostPrice($orgRate, $postcode,  $numWeight, 1, "", $suburb, false, '', $modelCargoProcessShipment);
                return $cost + $numInterstateCost;
            }
        }
        return 0;
    }

    public static function getDriverOrgRateIdByCDriverId($cargo_type, $id, $isHistory, $hasForklift)
    {
        switch ($cargo_type) {
            case 1:
                if ($isHistory == 1) {
                    return self::getOrgRateByKPCourierOrgIdHistory($id);
                } else {
                    return self::getOrgRateByHasForklif($id, $hasForklift);
                }
                // return ()? self::getOrgRateByKPCourierOrgIdHistory($id) : self::getOrgRateByHasForklif($id,$hasForklift);
            case 2:
                return self::getOrgRateByKPCourierOrgIdFBA($id);
            case 4:
                return self::getOrgRateByKPCourierOrgIdB2B($id);
        }
    }

    public static function getOrgRateByHasForklif($id, $hasForklift)
    {
        if ($hasForklift) {
            return self::getOrgRateByKPCourierOrgIdHasFork($id); //hasforklift
        } else {
            return self::getOrgRateByKPCourierOrgId($id); //noforklift
        }
    }

    //noforklift
    public static function getOrgRateByKPCourierOrgId($id)
    {
        $result = CargoProcess::$cargoSystemCostRateNoFork[$id];
        if (!empty($result)) {
            return $result;
        }
        return 0;
    }

    //hasforklift
    public static function getOrgRateByKPCourierOrgIdHasFork($id)
    {
        $result = CargoProcess::$cargoSystemCostRateHasFork[$id];
        if (!empty($result)) {
            return $result;
        }
        return 0;
    }

    //history
    public static function getOrgRateByKPCourierOrgIdHistory($id)
    {
        $result = CargoProcess::$cargoSystemCostHistoryRate[$id];
        if (!empty($result)) {
            return $result;
        }
        return 0;
    }

    //FBA
    public static function getOrgRateByKPCourierOrgIdFBA($id)
    {
        $result = CargoProcess::$cargoSystemCostFBA[$id];
        if (!empty($result)) {
            return $result;
        }
        return 0;
    }

    //B2B
    public static function getOrgRateByKPCourierOrgIdB2B($id)
    {
        $result = CargoProcess::$cargoSystemCostB2B[$id];
        if (!empty($result)) {
            return $result;
        }
        return 0;
    }

    public static function getChargeCodeByCargoProcessId($id)
    {
        //$orgId = CargoProcess::model()->findByPk($id)->shipment->agent_id;
        $cargoModel = CargoProcess::model()->findByPk($id);
        $shipment = $cargoModel->shipment;
        $numChargeCode = $shipment->mdata['chargecode'];

        if (!empty($numChargeCode)) {
            //default revenue chargecode or org revenue chargecode
            return $numChargeCode;
            //return empty(CargoProcess::$cargoRevenueList[$orgId]) ? 0 : CargoProcess::$cargoRevenueList[$orgId];
        } else {
            if ($shipment->ot_id == 10) {
                $dpt_id = $cargoModel->dpt_id;
                $quote = WmsOrgQuote::model()->find(['condition' => 'org_id = :org_id AND status = 1 AND (JSON_VALUE(meta, "$.whole_sale") IS NULL OR JSON_VALUE(meta, "$.whole_sale") = 0)', 'params' => [':org_id' => $shipment->agent_id], 'order' => 'id DESC']);
                if ($dpt_id == Org::TLA_DEPARTMENT_SYDNEY) {
                    $numCourierChargeCode = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_TLACARGO];
                } else if ($dpt_id == Org::TLA_DEPARTMENT_MELBOURNE) {
                    $numCourierChargeCode = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_TLACARGO];
                } else if ($dpt_id == Org::TLA_DEPARTMENT_BRISBANE) {
                    $numCourierChargeCode = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_BNE_CHARGECODE_TLACARGO];
                }
                if (!empty($numCourierChargeCode)) {
                    return $numCourierChargeCode;
                }
            }
            return 0;
        }
    }

    public static function getRevenueByCargoProcessIdAndChargeCodeId($cargoProcessId, $chargeCodeId)
    {
        $cc = ImportChargeCode::model()->find('chargecode = :chargecode', [':chargecode' => $chargeCodeId]);
        if (!empty($cc)) {
            $modelCargoProcessShipment = CargoProcess::model()->findByPk($cargoProcessId)->shipment;
            if (!empty($modelCargoProcessShipment)) {
                $postcode = $modelCargoProcessShipment->cnee->postcode;
                $weight = $modelCargoProcessShipment->weight;
                //$packs = $modelCargoProcessShipment->pkg;
                $suburb = $modelCargoProcessShipment->cnee->suburb;
                $cbm = $modelCargoProcessShipment->getTotalCBM();
                return self::getChargeByChargecode($cargoProcessId, $chargeCodeId);
                //return self::getChargeByChargecode(max($weight, $cbm * 250), $postcode, $chargeCodeId,$cbm);
            }
        }
        return 0;
    }

    public static function getChargeByChargecode($cargoProcessId, $chargeCodeId)
    {
        $total = 0;
        $arrTotal = [];
        $modelCargoProcessShipment = CargoProcess::model()->findByPk($cargoProcessId)->shipment;
        if (!empty($modelCargoProcessShipment)) {
            $numRevenue = $modelCargoProcessShipment->getChargeByChargecode($chargeCodeId);
            $numsurcharge = $modelCargoProcessShipment->getChargeByChargecode($chargeCodeId, false, null, true, false, true);
            $total = $numRevenue + $numsurcharge['amount'];
            $arrTotal[] = $numRevenue;
            $arrTotal[] = $numsurcharge;
            $arrTotal[] = $total;
            return $arrTotal;
        }
        return $total;
    }

    public static function getDriverScheduleByWeek($driver_id, $week)
    {
        $provide = [];

        if (is_int(intval($week))) {
            $dateStart = date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600));
            $dataEnd = date('Y-m-d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600));
            $dateStart = date("Y-m-d", strtotime((intval($week) * 7) . " day", strtotime($dateStart)));
            $dataEnd =  date("Y-m-d", strtotime((intval($week) * 7) . " day", strtotime($dataEnd)));
        }

        $strMonday = $dateStart;
        $strTuesday = date("Y-m-d", strtotime("+1 day", strtotime($dateStart)));
        $strWednesday = date("Y-m-d", strtotime("+2 day", strtotime($dateStart)));
        $strThursday = date("Y-m-d", strtotime("+3 day", strtotime($dateStart)));
        $strFriday = date("Y-m-d", strtotime("+4 day", strtotime($dateStart)));
        $strSaturday = date("Y-m-d", strtotime("+5 day", strtotime($dateStart)));
        $strSunday = date("Y-m-d", strtotime("+6 day", strtotime($dateStart)));

        $models = CargoProcessJob::model()->findAllBySql('select * from cargo_process_job where driver_id = ' . $driver_id . ' and created >= "' . $dateStart . ' 00:00:00" and created <= "' . $dataEnd . ' 23:59:59"');
        if (!empty($models)) {

            foreach ($models as $model) {
                if (empty($model->job_relations)) {
                    continue;
                }
                $strDescription = "";
                if($model->type==1){
                    $strDescription = " CBM:".$model->getTotalCBMOfJob();
                }elseif($model->type==2||$model->type==4){
                    $strDescription = " PLT:".$model->getTotalPltNo();
                }
                $weekDays = date("w", strtotime($model->created));

                switch ($weekDays) {
                    case 0:
                        $strSunday = $strSunday . '<br/>' . $model->job_name.$strDescription;
                        break;
                    case 1:
                        $strMonday = $strMonday . '<br/>' . $model->job_name.$strDescription;
                        break;
                    case 2:
                        $strTuesday = $strTuesday . '<br/>' . $model->job_name.$strDescription;
                        break;
                    case 3:
                        $strWednesday = $strWednesday . '<br/>' . $model->job_name.$strDescription;
                        break;
                    case 4:
                        $strThursday = $strThursday . '<br/>' . $model->job_name.$strDescription;
                        break;
                    case 5:
                        $strFriday = $strFriday . '<br/>' . $model->job_name.$strDescription;
                        break;
                    case 6:
                        $strSaturday = $strSaturday . '<br/>' . $model->job_name.$strDescription;
                        break;
                }
            }
        }
        $provide[] = ['id' => 0, 'monday' => $strMonday, 'tuesday' => $strTuesday, 'wednesday' => $strWednesday, 'thursday' => $strThursday, 'friday' => $strFriday, 'saturday' => $strSaturday, 'sunday' => $strSunday];
        foreach ($provide as $key => $value) {
            $provide[$key]['monday'] = $value['monday'];
            $provide[$key]['tuesday'] = $value['tuesday'];
            $provide[$key]['wednesday'] =  $value['wednesday'];
            $provide[$key]['thursday'] = $value['thursday'];
            $provide[$key]['friday'] =  $value['friday'];
            $provide[$key]['saturday'] =  $value['saturday'];
            $provide[$key]['strSunday'] = $value['strSunday'];
        }
        return $provide;
    }
}
