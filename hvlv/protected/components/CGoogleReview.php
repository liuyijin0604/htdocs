<?php
class CGoogleReview{

    public function funcCreateListRecord($strTimeFrom,$strTimeTo,$numLimitHours){
        
        //$strBaseSql = 'select * from(select id_log,time_done,lid,shipment_id,started,hbn,ref,cnor_id,cnee_id,pkg,weight,cnee_name,cnee_email,cnor_city,cnor_state,dt as time_arrived from (select id_log,time_done,lid,shipment_id,started,hbn,ref,cnor_id,cnee_id,pkg,weight,cnee_name,cnee_email,city as cnor_city,state as cnor_state from(select id_log,time_done,lid,shipment_id,started,hbn,ref,cnor_id,cnee_id,pkg,weight,name as cnee_name,email as cnee_email from(select  id as id_log,time_done,lid,shipment_id,started,hbn,ref,cnor_id,cnee_id,pkg,weight from(select id as id_log,time_done,lid,shipment_id,started from (select id as id_log,MIN(time) as time_done,lid from log where meta like \'{"status":"Cargo Process Done"%\' and time >"'.$strTimeFrom.'" and time <= "'.$strTimeTo.'" GROUP BY lid ) t_log JOIN cargo_process on  t_log.lid = cargo_process.id) t_log_cp JOIN shipment on  t_log_cp.shipment_id = shipment.id)t_log_cp_shipment join addr on t_log_cp_shipment.cnee_id = addr.id) t_log_cp_shipment_cnee join addr on t_log_cp_shipment_cnee.cnor_id = addr.id)t_log_cp_shipment_cnee_cnor join tracking on t_log_cp_shipment_cnee_cnor.shipment_id = tracking.pid and tracking.type=40)t_log_cp_shipment_cnee_cnor_tracking';
        $strBaseSql = 'SELECT * FROM
        (SELECT id_log, time_done, lid, shipment_id, started, hbn, ref, cnor_id, cnee_id, pkg, weight, cnee_name, cnee_email, cnee_tel, cnor_city, cnor_state, dt AS time_arrived FROM
        (SELECT id_log, time_done, lid, shipment_id, started, hbn, ref, cnor_id, cnee_id, pkg, weight, cnee_name, cnee_email, cnee_tel, city AS cnor_city, state AS cnor_state FROM
        (SELECT id_log, time_done, lid, shipment_id, started, hbn, ref, cnor_id, cnee_id, pkg, weight, name AS cnee_name, email AS cnee_email, tel AS cnee_tel FROM
        (SELECT id_log, time_done, lid, shipment_id, started, hbn, ref, cnor_id, cnee_id, pkg, weight FROM
        (SELECT id_log, time_done, lid, shipment_id, started FROM
        (SELECT id AS id_log, MIN(time) AS time_done, lid FROM
        log
        WHERE meta LIKE \'{"status":"Cargo Process Done"%\' AND time > "'.$strTimeFrom.'" AND time <= "'.$strTimeTo.'"
        GROUP BY lid) t_log
        JOIN cargo_process ON t_log.lid = cargo_process.id) t_log_cp
        JOIN shipment ON t_log_cp.shipment_id = shipment.id) t_log_cp_shipment
        JOIN addr ON t_log_cp_shipment.cnee_id = addr.id) t_log_cp_shipment_cnee
        JOIN addr ON t_log_cp_shipment_cnee.cnor_id = addr.id) t_log_cp_shipment_cnee_cnor
        JOIN tracking ON t_log_cp_shipment_cnee_cnor.shipment_id = tracking.pid AND tracking.type = 40) t_log_cp_shipment_cnee_cnor_tracking;';
        
        $listRows =Yii::app()->db->createCommand($strBaseSql)->queryAll();
        $listRecord = [];
        foreach($listRows as $row){
            $numTimeStart = strtotime($row['started']);
            $numTimeDone = strtotime($row['time_done']);

            /* if($numTimeDone - $numTimeStart > $numLimitHours*60*60){
                continue;
            }

			if(empty( $row['cnee_email'])){
				continue;
			} */

			if(strpos($row['ref'],'PICKUP')!==false){
				continue;
			}


            $objRecord = new stdClass;
            $objRecord->hbn = $row['hbn'];
            $objRecord->ref = $row['ref'];
            $objRecord->time_start = $row['started'];
            $objRecord->time_done = $row['time_done'];
            $objRecord->cnee_name = $row['cnee_name'];
            $objRecord->cnee_email = $row['cnee_email'];
            $objRecord->cnee_tel = $row['cnee_tel'];
            $objRecord->cnor_city = $row['cnor_city'];
            $objRecord->cnor_state = $row['cnor_state'];
            $objRecord->pkg = $row['pkg'];
            $objRecord->weight = $row['weight'];
            $objRecord->time_arrived = $row['time_arrived'];
            $objRecord->shipment_id = $row['shipment_id'];
            $objRecord->cargo_process_id = $row['lid'];
            $listRecord[] = $objRecord;
        }

        return $listRecord;
    }

    public function funcCreateListRecordByShipmentId($listId){

        $strSql = 'id in(';
        foreach($listId as $i=>$numId){
            $strSql .= $numId;
            if($i != sizeof($listId)-1){
                $strSql.=',';
            }
        }
        $strSql.=')';

        $listShipment = ImParcel::model()->findAll($strSql);
        
        $listRecord = [];
        foreach($listShipment as $objShipment){
            $objCnee = $objShipment->cnee;
            $objCnor = $objShipment->cnor;



            $objRecord = new stdClass;
            $objRecord->hbn = $objShipment->hbn;
            $objRecord->ref = $objShipment->ref;

            $objRecord->cnee_name = $objCnee->name;
            $objRecord->cnee_email = $objCnee->email;
            $objRecord->cnee_tel = $objCnee->tel;
            $objRecord->cnor_city = $objCnor->city;
            $objRecord->cnor_state = $objCnor->state;
            $objRecord->pkg = $objShipment->pkg;
            $objRecord->weight = $objShipment->weight;
            $listRecord[] = $objRecord;
        }

        return $listRecord;
    }

    public function funcSendEmial($listRecord){
        $objEmailService = new EmailService;

        foreach($listRecord as $objRecord){
            if(empty($objRecord->cnee_email)){
                continue;
            }
            
            $strSubject = 'RE: Shipment NO. '.$objRecord->ref.' from '.$objRecord->cnor_state.'  China';
            $strHtml='';
            // $strHtml .= 'Dear '.$objRecord->cnee_name.'，<br/><br/>';
            // $strHtml .= 'You have recently received delivery of a shipment from '.$objRecord->cnor_state.' of China.<br/><br/>';
            // $strHtml .= '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspShipment No. : '.$objRecord->ref.'<br/>';
            // $strHtml .= '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspNo. of Cartons : '.$objRecord->pkg.' pcs<br/>';
            // $strHtml .= '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGross Weight : '.$objRecord->weight.' kg<br/><br/>';
            // // $strDateArrived = substr($objRecord->time_arrived,0,10);
            // // $strDateDone = substr($objRecord->time_done,0,10);
            // // $numDays = (strtotime($strDateDone) - strtotime($strDateArrived))/86400;
            // // $strHtml .= '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspNo. of days from shipment arrived to Australia port : '.$numDays.' days<br/><br/>';
            // $strHtml .= 'Top Logistics Australia is the Australia (TLA) local shipping agent responsible to unload the container, sort the cargo, clear the customs, and arrange the delivery to you.<br/><br/>'; 
            // $strHtml .= 'We hope you have found our services to be helpful, and it would be highly appreciated if you could help us by spreading a good word online.<br/><br/>';
            // $strHtml .= 'Please do this by clicking on the link below, and leave a positive review on google.<br/><br/>';
            // $strHtml .= 'Leave your review here: <a href="https://g.page/top-logistics-australia/review?rc"><u>Google Review TLA</u></a><br/><br/>';
            // $strHtml .= 'If you have any issue or feedback, please feel free to email us back. <br/><br/>';
            // $strHtml .= 'Wishing you have a great day. <br/><br/>';
            // $strHtml .= 'TLA Customer Service <br/><br/>';
            $strHtml .= 'Hi '. $objRecord->cnee_name.',<br/><br/>';
            $strHtml .= 'As we truly value you as a customer, we are thrilled to know that you are happy with our service. Please take a moment to click the link below and give us a positive feedback on Google Review. Your support will help us go a long way in providing excellent delivery service.<br/>';
            $strHtml .= '[ <a href="https://ims.toplogistics.com.au/customerService/review"><u>https://ims.toplogistics.com.au/customerService/review</u></a> ]<br/><br/>';
            $strHtml .= 'TLA Customer Service';

            $objEmailService->funcSendGoogleReviewEmail($objRecord->cnee_email,$strSubject,$strHtml) ;

        }
    }



    public function funcSendSms($listRecord){

        $objEmailService = new EmailService;

        foreach($listRecord as $objRecord){
            if(empty($objRecord->cnee_tel)){
                continue;
            }

            $strMessage = 'Hi '. $objRecord->cnee_name.',

As we truly value you as a customer, we are thrilled to know that you are happy with our service.
Please take a moment to click the link below and give us a positive feedback on Google Review. Your support will help us go a long way in providing excellent delivery service.
[ https://g.page/top-logistics-australia/review?rc ]

TLA Customer Service
';

            $objEmailService->funcSendGoogleReviewSms($objRecord->cnee_tel,$strMessage);
            
        }

    }



}


