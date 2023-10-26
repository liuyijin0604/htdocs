<?php
class CustomsProcessService extends Service
{
    public static function getCustomProcessKPIReport($startdate, $enddate)
    {
        $start_date = $startdate;
        $end_date = $enddate;
        $provide = [];
        $orgShipmentProcesses = [];

        $sql = 'select lid , min(l.time) as time , JSON_EXTRACT(meta, "$.status") as recordtype 
        from log l 
        where l.model = "ShipmentProcess" 
        and (JSON_EXTRACT(l.meta, "$.status") = "Customs Done" 
        or JSON_EXTRACT(l.meta, "$.status") = "Documents Received") 
        and l.time > "' . $start_date . ' 00:00:00" and l.time < "' . $end_date . ' 23:59:59" 
        group by l.lid,l.meta order by time';

        $rs = Yii::app()->db->createCommand($sql)->queryAll();
        $i = 0;
        foreach ($rs as $r) {
            $orgShipmentProcesses[$i] = $r;
            $i++;
        }

        while (strtotime($start_date) <= strtotime($end_date)) {

            $hv_close_total = $aqis_close_total = $empp_close_total = 0;
            $hv_open_total = $aqis_open_total = $empp_open_total = 0;

            foreach ($orgShipmentProcesses as $key => $r) {
                $daysdiff = ceil((time() - strtotime(date("y-m-d", strtotime($r['time'])))) / 86400);
                $startdaysdiff = ceil((time() - strtotime($start_date)) / 86400);

                if ($daysdiff == $startdaysdiff) {
                    //$sql = 'SELECT p.* FROM `shipment_process` p where p.id =' . $r['lid'];
                    //$sql = 'SELECT p.type,s.hbn,s.ref FROM `shipment_process` p left join shipment s on p.pid = s.id where s.status!=100 and p.id =' . $r['lid'];
                    $sql = 'SELECT p.type,s.hbn,s.ref,min(l.time) as time 
                    FROM `shipment_process` p 
                    left join shipment s on p.pid = s.id 
                    left join log l on p.id = l.lid 
                    where l.model="ShipmentProcess" 
                    and JSON_EXTRACT(l.meta, "$.status") = "Documents Received" 
                    and p.id =' . $r['lid'] . '
                    group by l.lid,l.meta';

                    $rshipmentprocess = Yii::app()->db->createCommand($sql)->queryAll()[0];

                    if (!empty($rshipmentprocess)) {
                        if (($rshipmentprocess['type'] & ShipmentProcess::TYPE_HV) > 0) {
                            if ($r['recordtype'] == '"Customs Done"') {
                                $hv_close_total++;
                            } else if ($r['recordtype'] == '"Documents Received"') {
                                $hv_open_total++;
                            }
                        }
                        if (($rshipmentprocess['type'] & ShipmentProcess::TYPE_EMPP) > 0) {
                            if ($r['recordtype'] == '"Customs Done"') {
                                $empp_close_total++;
                            } else if ($r['recordtype'] == '"Documents Received"') {
                                $empp_open_total++;
                            }
                        }
                        if (($rshipmentprocess['type'] & ShipmentProcess::TYPE_AQIS) > 0) {
                            if ($r['recordtype'] == '"Customs Done"') {
                                $aqis_close_total++;
                            } else if ($r['recordtype'] == '"Documents Received"') {
                                $aqis_open_total++;
                            }
                        }
                    }
                    unset($orgShipmentProcesses[$key]);
                }
            }
            $provide[$start_date] = [$start_date, $hv_open_total, $hv_close_total, $empp_open_total, $empp_close_total, $aqis_open_total, $aqis_close_total];
            $start_date = date("Y-m-d", strtotime("+1 days", strtotime($start_date)));
        }
        return $provide;
    }

    public static function getCustomProcessOpenRecordsDetail($startdate, $enddate)
    {
        $start_date = $startdate;
        $end_date = $enddate;
        $provide = [];
        $orgShipmentProcesses = [];

        $sql = 'select lid , min(l.time) as time , JSON_EXTRACT(meta, "$.status") as recordtype 
        from log l 
        where l.model = "ShipmentProcess" 
        and (JSON_EXTRACT(l.meta, "$.status") = "Customs Done" 
        or JSON_EXTRACT(l.meta, "$.status") = "Documents Received") 
        and l.time > "' . $start_date . ' 00:00:00" and l.time < "' . $end_date . ' 23:59:59" 
        group by l.lid,l.meta order by time';

        $rs = Yii::app()->db->createCommand($sql)->queryAll();
        $i = 0;
        foreach ($rs as $r) {
            $orgShipmentProcesses[$i] = $r;
            $i++;
        }

        while (strtotime($start_date) <= strtotime($end_date)) {

            foreach ($orgShipmentProcesses as $key => $r) {
                $hv_close_total = $aqis_close_total = $empp_close_total = 'False';
                $hv_open_total = $aqis_open_total = $empp_open_total = 'False';

                $daysdiff = ceil((time() - strtotime(date("y-m-d", strtotime($r['time'])))) / 86400);
                $startdaysdiff = ceil((time() - strtotime($start_date)) / 86400);

                if ($daysdiff == $startdaysdiff) {
                    $sql = 'SELECT p.type,s.hbn,s.ref,min(l.time) as time 
                    FROM `shipment_process` p 
                    left join shipment s on p.pid = s.id 
                    left join log l on p.id = l.lid 
                    where l.model="ShipmentProcess" 
                    and JSON_EXTRACT(l.meta, "$.status") = "Documents Received" 
                    and p.id =' . $r['lid'] . '
                    group by l.lid,l.meta';

                    $rshipmentprocess = Yii::app()->db->createCommand($sql)->queryAll()[0];

                    if (!empty($rshipmentprocess)) {
                        if (($rshipmentprocess['type'] & ShipmentProcess::TYPE_HV) > 0) {
                            if ($r['recordtype'] == '"Customs Done"') {
                                $hv_close_total = 'True';
                            } else if ($r['recordtype'] == '"Documents Received"') {
                                $hv_open_total = 'True';
                            }
                        }
                        if (($rshipmentprocess['type'] & ShipmentProcess::TYPE_EMPP) > 0) {
                            if ($r['recordtype'] == '"Customs Done"') {
                                $empp_close_total = 'True';
                            } else if ($r['recordtype'] == '"Documents Received"') {
                                $empp_open_total = 'True';
                            }
                        }
                        if (($rshipmentprocess['type'] & ShipmentProcess::TYPE_AQIS) > 0) {
                            if ($r['recordtype'] == '"Customs Done"') {
                                $aqis_close_total = 'True';
                            } else if ($r['recordtype'] == '"Documents Received"') {
                                $aqis_open_total = 'True';
                            }
                        }
                        $provide[] = [$start_date, $rshipmentprocess['hbn'], $rshipmentprocess['ref'], $hv_open_total, $hv_close_total, $empp_open_total, $empp_close_total, $aqis_open_total, $aqis_close_total];
                    }
                    unset($orgShipmentProcesses[$key]);
                }
            }
            //$provide[$start_date] = [$start_date, $hv_open_total,$hv_close_total,$empp_open_total, $empp_close_total,$aqis_open_total, $aqis_close_total];
            $start_date = date("Y-m-d", strtotime("+1 days", strtotime($start_date)));
        }
        return $provide;
    }

    public static function getCustomProcessKPIPeriodZeroRecord()
    {
        $havingStr = " having min(to_days(l.time)) = to_days(DATE_SUB(curdate(),INTERVAL 0 DAY))";
        return self::funcGetCustomProcessKPIPeriodRecord($havingStr,'0');
    }
        

    public static function getCustomProcessKPIPeriodOneRecord()
    {
        $havingStr = " having min(to_days(l.time)) < to_days(DATE_SUB(curdate(),INTERVAL 0 DAY)) and min(to_days(l.time)) >= to_days(DATE_SUB(curdate(),INTERVAL 5 DAY))";
        return self::funcGetCustomProcessKPIPeriodRecord($havingStr,'0-3');
    }

    public static function getCustomProcessKPIPeriodTwoRecord()
    {
        $havingStr = " having min(to_days(l.time)) < to_days(DATE_SUB(curdate(),INTERVAL 5 DAY)) and min(to_days(l.time)) >= to_days(DATE_SUB(curdate(),INTERVAL 10 DAY))";
        return self::funcGetCustomProcessKPIPeriodRecord($havingStr,'3-5');
    }

    public static function getCustomProcessKPIPeriodThreeRecord()
    {
        $havingStr = " having min(to_days(l.time)) < to_days(DATE_SUB(curdate(),INTERVAL 10 DAY)) and min(to_days(l.time)) >= to_days(DATE_SUB(curdate(),INTERVAL 15 DAY))";
        return self::funcGetCustomProcessKPIPeriodRecord($havingStr,'5-7');
    }

    public static function getCustomProcessKPIPeriodFourRecord()
    {
        $havingStr = " having min(to_days(l.time)) < to_days(DATE_SUB(curdate(),INTERVAL 15 DAY))";
        return self::funcGetCustomProcessKPIPeriodRecord($havingStr,'>7');
    }

    public static function funcGetCustomProcessKPIPeriodRecord($havingStr,$days)
    {
        $provide = [];
        $empp_Total = [];
        $aqis_Total = [];
        $hv_Total = [];

        $sql = 'SELECT p.type,p.status,s.hbn,s.ref,c.eta,min(l.time) as time,JSON_EXTRACT(l.meta, "$.status") as recordtype 
        FROM `shipment_process` p 
        left join shipment s on p.pid = s.id 
        left join consol c on s.consol_id = c.id
        left join log l on p.id = l.lid 
        where l.model="ShipmentProcess" 
        and p.status not in (1,2,3,24,200,210)
        and JSON_EXTRACT(l.meta, "$.status") = "Documents Received"
        and c.id is not null
        group by l.lid
        '. $havingStr;

        $rs = Yii::app()->db->createCommand($sql)->queryAll();

        if (!empty($rs)) {
            foreach ($rs as $r) {
                if (($r['type'] & ShipmentProcess::TYPE_HV) > 0) {
                    $hv_Total[]=$r['hbn'];
                }
                if (($r['type'] & ShipmentProcess::TYPE_EMPP) > 0) {
                    $empp_Total[]=$r['hbn'];
                }
                if (($r['type'] & ShipmentProcess::TYPE_AQIS) > 0) {
                    $aqis_Total[]=$r['hbn'];
                }
            }
        }
        $provide[] = ['DAY' => ''.$days, 'EMPP' => count($empp_Total), 'AQIS' => count($aqis_Total), 'HV' => count($hv_Total), 'TOTAL' => count($empp_Total) + count($aqis_Total) + count($hv_Total),'EMPP_data'=>$empp_Total,'AQIS_data'=>$aqis_Total,'HV_data'=>$hv_Total];
        return $provide;
    }

    public static function convertCustomKPIArr($eta)
    {
        $empp = ['EMPP'];
        $aqis = ['AQIS'];
        $hv = ['HV'];
        $total = ['SUB TOTAL'];
        $provide = [];

        foreach ($eta as $r) {
            if(empty($r[0])) continue;
            $empp[] = [$r[0]['EMPP'],$r[0]['EMPP_data']];
            $aqis[] = [$r[0]['AQIS'],$r[0]['AQIS_data']];
            $hv[] = [$r[0]['HV'],$r[0]['HV_data']];
            $total[] = [$r[0]['TOTAL'],[]];
        }

        $provide[] = $empp;
        $provide[] = $aqis;
        $provide[] = $hv;
        $provide[] = $total;

        return $provide;
    }

    public static function getCountTotalCustomProcessDoneCurrentDay()
    {

        $proivde = ['EMPPClose' => 0, "AQISClose" => 0, 'HVClose' => 0,'EMPPOpen'=>0,'AQISOpen'=>0,'HVOpen'=>0];
        $provide['EMPPClose'] = self::getEMPPTotalCurrentDay();
        $provide['HVClose'] = self::getHVTotalCurrentDay();
        $provide['AQISClose'] = self::getAQISTotalCurrentDay();
        $provide['EMPPOpen']=self::getEMPPOpenTotalCurrentDay();
        $provide['HVOpen']=self::getHVOpenTotalCurrentDay();
        $provide['AQISOpen']=self::getAQISOpenTotalCurrentDay();

        return $provide;
    }

    public static function getEMPPTotalCurrentDay()
    {
        $provide = 0;
        $sql = "select count(*) as EMPP from shipment s 
        left join shipment_process p on s.id = p.pid 
        where s.status !=100 
        and (s.bwf & 512) >0 
        and p.status = 24 
        and p.date like CONCAT('%',DATE_SUB(curdate(),INTERVAL 0 DAY),'%')";

        $rs = Yii::app()->db->createCommand($sql)->queryAll();

        if (!empty($rs)) {
            $provide = $rs[0]['EMPP'];
        }
        return $provide;
    }

    public static function getHVTotalCurrentDay()
    {
        $provide = 0;
        $sql = "select count(*) as HV from shipment s 
        left join shipment_process p on s.id = p.pid 
        where s.status !=100 
        and (s.bwf & 4) >0 
        and p.status = 24 
        and p.date like CONCAT('%',DATE_SUB(curdate(),INTERVAL 0 DAY),'%')";

        $rs = Yii::app()->db->createCommand($sql)->queryAll();

        if (!empty($rs)) {
            $provide = $rs[0]['HV'];
        }
        return $provide;
    }

    public static function getAQISTotalCurrentDay()
    {
        $provide = 0;
        $sql = "select count(*) as AQIS from shipment s 
        left join shipment_process p on s.id = p.pid 
        where s.status !=100 
        and (s.bwf & 32) >0 
        and p.status = 24 
        and p.date like CONCAT('%',DATE_SUB(curdate(),INTERVAL 0 DAY),'%')";

        $rs = Yii::app()->db->createCommand($sql)->queryAll();

        if (!empty($rs)) {
            $provide = $rs[0]['AQIS'];
        }
        return $provide;
    }

    public static function getHVOpenTotalCurrentDay()
    {
        $provide = 0;
        $sql = 'SELECT s.bwf,p.type,p.status,s.hbn,s.ref,min(l.time) as time,JSON_EXTRACT(l.meta, "$.status") as recordtype 
        FROM `shipment_process` p 
        left join shipment s on p.pid = s.id 
        left join log l on p.id = l.lid 
        where l.model="ShipmentProcess" 
        and p.status not in (24)
        and JSON_EXTRACT(l.meta, "$.status") = "Documents Received"
        and (s.bwf & 4)>0
        group by l.lid,l.meta
        having min(to_days(l.time)) = to_days(DATE_SUB(curdate(),INTERVAL 0 DAY))';

        $rs = Yii::app()->db->createCommand($sql)->queryAll();

        if (!empty($rs)) {
            $provide = count($rs);
        }
        return $provide;
    }

    public static function getEMPPOpenTotalCurrentDay()
    {
        $provide = 0;
        $sql = 'SELECT s.bwf,p.type,p.status,s.hbn,s.ref,min(l.time) as time,JSON_EXTRACT(l.meta, "$.status") as recordtype 
        FROM `shipment_process` p 
        left join shipment s on p.pid = s.id 
        left join log l on p.id = l.lid 
        where l.model="ShipmentProcess" 
        and p.status not in (24)
        and JSON_EXTRACT(l.meta, "$.status") = "Documents Received"
        and (s.bwf & 512)>0
        group by l.lid,l.meta
        having min(to_days(l.time)) = to_days(DATE_SUB(curdate(),INTERVAL 0 DAY))';

        $rs = Yii::app()->db->createCommand($sql)->queryAll();

        if (!empty($rs)) {
            $provide = count($rs);
        }
        return $provide;
    }

    public static function getAQISOpenTotalCurrentDay()
    {
        $provide = 0;
        $sql = 'SELECT s.bwf,p.type,p.status,s.hbn,s.ref,min(l.time) as time,JSON_EXTRACT(l.meta, "$.status") as recordtype 
        FROM `shipment_process` p 
        left join shipment s on p.pid = s.id 
        left join log l on p.id = l.lid 
        where l.model="ShipmentProcess" 
        and p.status not in (24)
        and JSON_EXTRACT(l.meta, "$.status") = "Documents Received"
        and (s.bwf & 32)>0
        group by l.lid,l.meta
        having min(to_days(l.time)) = to_days(DATE_SUB(curdate(),INTERVAL 0 DAY))';

        $rs = Yii::app()->db->createCommand($sql)->queryAll();

        if (!empty($rs)) {
            $provide = count($rs);
        }
        return $provide;
    }

    public static function getPercentage($divisor,$dividend){
        if($divisor==0 || $dividend==0){
            return 0;
        }else {
            return round($divisor / $dividend * 100, 2);
        }
    }


    // this function is for generating the AQIS File before sending email
    public function generateAQISFile($shipmentId,$type,$postData)
    {
        $p= ImParcel::model()->findByPk($shipmentId);
        switch($p->process->status)
        {
            case ShipmentProcess::STATE_WAITING_FOR_INSPECTION:
            case ShipmentProcess::STATE_WAITING_FOR_DISPOSAL:
            case ShipmentProcess::STATE_DISPOSAL_COMPLETE:
                return $this->funcGenerateAQISFile($p,$type,$postData);
            break;
            

            default:

            break;
        }

         return $this->getFailResult("status error");
    }

    public function funcGenerateAQISFile($p,$type,$postData)
    {
        $tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'cust';
        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory);
        }
        switch($type)
        {
            case "INS_REQUEST_FORM":
                $name = $p->ref.(empty($postData['mdata']['insf']['quarantine_entry_number'])?"":"_".$postData['mdata']['insf']['quarantine_entry_number']).'_inspection_request_form.pdf';
                $fileName = $tempDirectory.DIRECTORY_SEPARATOR.$name;
                $fileName2='';
                $f1 = tempnam(Yii::app()->basePath."/runtime", "ins");
                $f2 = tempnam(Yii::app()->basePath."/runtime", "ins");
                $p->process->mdata['insf'] = $postData['mdata']['insf'];
                $p->process->updateMeta();
                oPDF::renderPDF('request_inspection_form1', ['model'=>$p,'type'=>$type], 2, $f1);
                oPDF::renderPDF('request_inspection_form2', ['model'=>$p,'type'=>$type], 2, $f2);
                oPDF::mergePDF([$f1, $f2], 2, true, $fileName);
                $files = FileRepo::model()->findAll("fid = :fid and type =:type",[":type"=>FileRepo::AQIS_INSPECTION_FORM,":fid"=>$p->id]);
                foreach ($files as $key => $value) {
                    $value->status = FileRepo::DELETED;
                    $value->update(['status']);
                }

                FileRepo::storeFile($fileName, $name, FileRepo::AQIS_INSPECTION_FORM, $p->id);
                return $this->getSuccessResult();
            break;

            case "DIS_REQUEST_FORM":
                $name = $p->ref.'_disposal_request_form.pdf';
                $fileName = $tempDirectory.DIRECTORY_SEPARATOR.$name;
                $fileName2='';
                $f1 = tempnam(Yii::app()->basePath."/runtime", "ins");
                $f2 = tempnam(Yii::app()->basePath."/runtime", "ins");
                $p->process->mdata['disf'] = $postData['mdata']['disf'];
                $p->process->updateMeta();
                oPDF::renderPDF('request_disposal_form1', ['model'=>$p,'type'=>$type], 2, $f1);
                oPDF::renderPDF('request_disposal_form2', ['model'=>$p,'type'=>$type], 2, $f2);
                oPDF::mergePDF([$f1, $f2], 2, true, $fileName);
                $files = FileRepo::model()->findAll("fid = :fid and type =:type",[":type"=>FileRepo::AQIS_DISPOSAL_FORM,":fid"=>$p->id]);
                foreach ($files as $key => $value) {
                    $value->status = FileRepo::DELETED;
                    $value->update(['status']);
                }

                FileRepo::storeFile($fileName, $name, FileRepo::AQIS_DISPOSAL_FORM, $p->id);
                return $this->getSuccessResult();
            break;

            default:
            break;
        }
        //generateFile
        //set the file type
        return $this->getFailResult("Generate Failed");
    }

    // this function is for sending the AQIS Email depending the status of the shipment process and action type
    public function sendAQISEmail($shipmentId,$postData,$type,$action)
    {
        $p= ImParcel::model()->findByPk($shipmentId);
        $files = [];        

        switch($p->process->status)
        {
            case ShipmentProcess::STATE_AQIS_CUSTOM:
            case ShipmentProcess::STATE_AQIS_DONOT_MOVE:
                $tfiles = FileRepo::model()->findAll('type = 19 and fid = :fid and name like "%'.$p->hbn.' do not move%"',["fid" => $p->id]);
                foreach ($tfiles as $key => $value) {
                     $files[] = [$value->getFile(),$value->name];
                }
                break;

            case ShipmentProcess::STATE_CONFIRM_INS:
            case ShipmentProcess::STATE_CONFIRM_DIS:
            case ShipmentProcess::STATE_WAITING_FOR_INSPECTION:
            case ShipmentProcess::STATE_WAITING_FOR_DISPOSAL:
            case ShipmentProcess::STATE_DISPOSAL_COMPLETE:
            case ShipmentProcess::STATE_INS_NOT_OK:
                $tfiles = FileRepo::model()->findAll('status = 30 and fid = :fid ',["fid" => $p->id]);
                foreach ($tfiles as $key => $value) {
                     $files[] = [$value->getFile(),$value->name];
                }
            break;

            // case ShipmentProcess::STATE_DISPOSAL_COMPLETE:
            //     if($type=="send_inspection_booking"||$type=="send_disposal_booking")
            //     {
            //         $tfiles = FileRepo::model()->findAll("fid = :fid and type in (".FileRepo::AQIS_INSPECTION_FORM.",".FileRepo::AQIS_DISPOSAL_FORM.") and status =30",[":fid"=>$p->id]);
            //         foreach ($tfiles as $key => $value) {
            //              $files[] = [$value->getFile(),$value->name];
            //         }
            //     }
            // break;


            default:

            break;
        }

        return $this->sendEmail($shipmentId,$postData,$files,$action);
    }

    public function getAqisEmail($shipmentId,$type)
    {
        $p= ImParcel::model()->findByPk($shipmentId);
        $setting = $this->getInsRequestFormSetting($shipmentId)['directionLocation'];
        $files = [];
        $emailType = "";
        $arr = [];
        $arr['HBN'] = $p->hbn;
        $arr['REF'] = $p->ref;
        $arr['CREF'] = $p->ref;
        if(preg_match('/([A-Za-z0-9]+)/',$p->can,$m))
        {
            $arr['ENTRY'] = $m[1];
        }
        $arr['COMPANY'] = $setting[0];
        $arr['ADDRESS'] = $setting[1];
        $arr['SUBURB'] = $setting[2];
        $arr['WHERE'] = (empty($setting[3])?$setting[0]:$setting[3]);
        $email = '';
        $cc = [(!empty(Yii::app()->user)&&Yii::app()->user->grp == 72) ? Yii::app()->user->email : 'imports@toplogistics.com.au'];
        switch($p->process->status)
        {
            case ShipmentProcess::STATE_AQIS_CUSTOM:
            case ShipmentProcess::STATE_AQIS_DONOT_MOVE:
                $emailType = Emailog::AQIS_DONOT_MOVE;
                $arr['LINK'] = $p->getTheHashUrl(7);
                if (!empty($p->agent->extra['customs_email'])) {
                    foreach (preg_split('/[,;]+/i', trim($p->agent->extra['customs_email'])) as $ck =>$to) {
                        if($ck==0)
                        {
                            $email = $to;
                            break;
                        }
                    }
                }else
                {
                    $c = explode(';', $p->agent->extra['outturn_email']);
                    if(!empty($c))
                    {
                        $email = $c[0];
                        unset($c[0]);
                        $cc = array_merge($cc,$c);
                    }
                }
            break;
            case ShipmentProcess::STATE_CONFIRM_INS:
                if($type=="send_to_customs")
                {
                    $emailType = Emailog::INS_SEND_CUSTOMS;
                }elseif($type="request_for_invoice")
                {
                    $emailType = Emailog::INS_REQUEST_INV;
                }
            break;
            case ShipmentProcess::STATE_INS_INVOICE_PAID:
            case ShipmentProcess::STATE_DIS_INVOICE_PAID:
                if($type=="send_to_customs")
                {
                    $emailType = Emailog::INS_SEND_CUSTOMS;
                }elseif($type="request_for_invoice")
                {
                    $emailType = Emailog::INS_REQUEST_INV;
                }
            break;
            case ShipmentProcess::STATE_CONFIRM_DIS:
                $emailType = "";
                if($type=="send_to_customs")
                {
                    $emailType = Emailog::DIS_SEND_CUSTOMS;
                }elseif($type=="request_for_invoice")
                {
                    $emailType = Emailog::DIS_REQUEST_INV;
                }elseif($type=="send_disposal_booking")
                {
                    $emailType = Emailog::DIS_SEND_BOOKING;
                }

            break;
            case ShipmentProcess::STATE_WAITING_FOR_INSPECTION:
                if($type=="send_inspection_booking")
                {
                    $emailType = Emailog::INS_SEND_BOOKING;
                }
            break;
            case ShipmentProcess::STATE_WAITING_FOR_DISPOSAL:
                if($type=="send_disposal_booking")
                {
                    $emailType = Emailog::DIS_SEND_BOOKING2;
                }
            break;
            case ShipmentProcess::STATE_DISPOSAL_COMPLETE:
                if($type=="send_record_to_customs")
                {
                    $emailType = Emailog::DIS_SEND_RECORD;
                }
            break;
            case ShipmentProcess::STATE_INS_NOT_OK:
                $emailType = Emailog::INS_NOT_OK;
                $arr['LINK'] = $p->getTheHashUrl(7);
                if (!empty($p->agent->extra['customs_email'])) {
                    foreach (preg_split('/[,;]+/i', trim($p->agent->extra['customs_email'])) as $ck =>$to)
                    {
                        if($ck==0)
                        {
                            $email = $to;
                            break;
                        }
                    }
                }else
                {
                    $c = explode(';', $p->agent->extra['outturn_email']);
                    if(!empty($c))
                    {
                        $email = $c[0];
                        unset($c[0]);
                        $cc = array_merge($cc,$c);
                    }
                }
            break;


            default:

            break;
        }
         $cc = join(";",$cc);

        $model = new Emailog();
        $from = (!empty(Yii::app()->user)&&Yii::app()->user->grp == 72) ? Yii::app()->user->email : 'imports@toplogistics.com.au';
        // $cc = '';
        $type = "";
        $typeCN = "";
        $model->type = $emailType;
        

        $model->fid = 0;
        $model->prepTemplate();
        $model->tpl->assignSubject('HBN', $p->hbn);
        foreach ($arr as $key => $value) {
            $model->tpl->assignSubject($key, $value);
        }
        // $model->tpl->assignSubject('TYPE', $type);
        $model->tpl->assignThese(array_merge([
                'HBN' => $p->hbn
            ],$arr));
        $model->subject = $model->tpl->subject;
        $model->body = $model->tpl->getContent();
        $model->mdata['to'] = $email;
        $model->mdata['from'] = $from;
        $model->mdata['cc'] = $cc;
        return $model;
    }


    public function sendEmail($shipmentId,$postData,$files,$action)
    {
        $model = new Emailog();
        $email = '';
        $from = (!empty(Yii::app()->user)&&Yii::app()->user->grp == 72) ? Yii::app()->user->email : 'imports@toplogistics.com.au';
        $cc = (!empty(Yii::app()->user)&&Yii::app()->user->grp == 72) ? Yii::app()->user->email : 'imports@toplogistics.com.au';
        // $cc = '';
        $p = ImParcel::model()->findByPk($shipmentId);
        $type = "";
        $typeCN = "";
        $model->type = Emailog::SURPLUS;
        

        $model->fid = 0;
        if (isset($postData['Emailog'])) {
            if(empty($postData['extra']['to']))
            {
                return $this->getFailResult("Required To Email");
            }
            $model->attributes = $postData['Emailog'];
            foreach ($postData['extra'] as $k => $v) {
                $model->mdata[$k] = $v;
            }
            $model->status = 10;
            $model->save();
            $d = $model->sendEmail($files);
            $p->process->mdata['lastEmailId'] = $model->id;
            $p->process->custom_log_note = $action;
            $p->process->updateMeta();

            if ($d['status']) {
                if($p->process->status==ShipmentProcess::STATE_AQIS_CUSTOM)
                {
                    $p->process->status=ShipmentProcess::STATE_AQIS_DONOT_MOVE;
                    $p->process->mdata['donot_move_due_date'] = date('Y-m-d',strtotime('+3 day',strtotime(date('Y-m-d'))));
                    $p->process->update(['status','meta']);
                }

                if($p->process->status==ShipmentProcess::STATE_AQIS_DONOT_MOVE&&empty($p->process->mdata['donot_move_due_date']))
                {
                    $p->process->mdata['donot_move_due_date'] = date('Y-m-d',strtotime('+3 day',strtotime(date('Y-m-d'))));
                    $p->process->update(['meta']);
                }

                if(in_array($p->process->status,[ShipmentProcess::STATE_CONFIRM_DIS,ShipmentProcess::STATE_CONFIRM_INS]))
                {
                    $p->process->mdata['request_for_inv_date'] = date('Y-m-d');
                    $p->process->mdata['lastConfirmEmailId'] = $model->id;
                    $p->process->update(['meta']);
                }

                if($p->process->status==ShipmentProcess::STATE_INS_NOT_OK)
                {
                    $p->process->status=ShipmentProcess::STATE_CONFIRM_DIS;
                    $p->process->update(['status','meta']);
                }

                return $this->getSuccessResult();
            } else {
                return $this->getFailResult("Sent Email Failure");
            }
            AppHelper::unlinkRecursive($tempDirectory);
        }


       return $this->getSuccessResult();
    }

    public function getInsRequestFormSetting($id)
    {
        $p = ImParcel::model()->findByPk($id);
        return SystemSetting::getSetting('InsRequestFormSetting',$p->consol->dpt_id);
    }


    // this function is for pushing the shipment process to next status
    public function goNextAqisStatus($shipmentId,$status,$action,$postData)
    {
        $p = ImParcel::model()->findByPk($shipmentId);
        if($p->process->status!=$status)
        {
            return $this->getFailResult("Status Error");
        }
        $next = false;
        if(in_array($p->process->status,array_keys(ShipmentProcess::$insstates)))
        {
            foreach (ShipmentProcess::$insstates as $key => $value) {
                if($next)
                {
                    $p->process->status = $key;
                    break;
                }
                if($key==$p->process->status)
                {
                    $next = true;
                }
            }
        }

        if(in_array($p->process->status,array_keys(ShipmentProcess::$disstates)))
        {
            foreach (ShipmentProcess::$disstates as $key => $value) 
            {
                if($next)
                {
                    $p->process->status = $key;
                    break;
                }
                if($key==$p->process->status)
                {
                    $next = true;
                }
            }
        }
        if($next)
        {
            $p->process->custom_log_note = $action;
            if(!empty($postData['mdata']))
            {
                foreach ($postData['mdata'] as $key => $value) {
                   $p->process->mdata[$key] = $value;
                }
            }
            if($p->process->save())
            {
                return $this->getSuccessResult();
            }else
            {
                return $this->getFailResult(json_encode($p->process->getErrors()));
            }
        }else
        {
            return $this->getFailResult("Status Error");
        }
    }

    public function uploadAuCustomsEntryPrint($hash)
    {
        $fileName = "AUCustoms EntryPrint (Portrait)";
        $f = $_FILES['file'];
        $fr = new FileRepo;
        if(!is_uploaded_file($f['tmp_name'])) return false;
        $fr->name = $f['name'];
        $fr->size = filesize($f['tmp_name']);
        $fr->date = date('Y-m-d H:i:s');
        $fr->hash = hash_file('crc32b', $f['tmp_name']).hash('crc32b', $fr->size).rand(1000,5000);
        $sup = Yii::app()->session['uploads'][$hash];
        $fr->type = $sup[0];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $f['tmp_name']);
        $fr->mime = empty($mime)? 'application/octet-stream' : $mime;
        $fr->user_id = User::currentUserID();
        $d = Yii::app()->params['fileRepoPath'].DIRECTORY_SEPARATOR.substr($fr->hash,0,2);

        if(!is_dir($d)) mkdir($d);
        try
        {
            move_uploaded_file($f['tmp_name'], $d.DIRECTORY_SEPARATOR.$fr->hash);
        }catch(Exception $e)
        {

        }
        if(empty($sup[1])){
            $fr->fid = 0;
            $fr->save();
            if(!isset($sup[2])) $sup[2] = array();
            $sup[2][] = $fr->id;
            $su = Yii::app()->session['uploads'];
            $su[$hash] = $sup;
            Yii::app()->session['uploads'] = $su;
        }else{
            $fr->fid = $sup[1];
            $fr->save();
            if(!isset($sup[2])) $sup[2] = array();
            $sup[2][] = $fr->id;
        }        

        if (strpos($fr->name, $fileName)!==false) {         
            $pattern = '/TOTAL AMOUNT PAYABLE \*\*\*[\s\S]*(\d{1,100}\.+\d{0,100}) \*\*\*/i';
            $frPath = $d . DIRECTORY_SEPARATOR . $fr->hash;
            $f2 = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR, 'check');
            $result = oPDF::pdftotxt( $frPath,$f2);                  
            if(preg_match($pattern, $result,$m))
            {
                if(preg_match('/\d+\.+\d+/',$m[0],$h))
                {
                    // echo $h[0];
                    // $amount = floatval($h[0]);

                    $model = ImParcel::model()->findByPk($fr->fid);
                    if(!isset($model->mdata['custom_inv'])){                        
                        $model->mdata['custom_inv']['ccode'] = ['CUSTOMS DUTY/GST'];
                        $model->mdata['custom_inv']['amount'] = [$h[0]];
                        $model->mdata['custom_inv']['det'] = [];
                        $model->mdata['custom_inv']['qty'] = ['1'];
                        $model->mdata['custom_inv']['tax'] = ['EXEMPTOUTPUT'];                        
                    }
                    else{
                         $model->mdata['custom_inv']['ccode'][] = 'CUSTOMS DUTY/GST';
                         $model->mdata['custom_inv']['amount'][] = $h[0];
                         $model->mdata['custom_inv']['det'][] = "";
                         $model->mdata['custom_inv']['qty'][] = '1';
                         $model->mdata['custom_inv']['tax'][] = 'EXEMPTOUTPUT';
                    }
                    $model->updateMeta();
                }          
            }
        }
        echo 'DONE';      
    }
}
