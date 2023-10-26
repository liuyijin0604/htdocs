<?php
$strClassHideSummary='';
$strClassHideFlow='';
$strClassHideSchedule='';
$strClassShowError ='hidden';
$strClassHideDetails='';
$mobileProgressClassDispatch = 'step';
$mobileProgressClassArrival = 'step';
$mobileProgressClassClear = 'step';
$mobileProgressClassTransit = 'step';
$mobileProgressClassDone = 'step';
$strClassHideGpsTracking = 'none';
if(empty($model))
{
    $strClassShowError ='';
    $strClassHideDetails='hidden';
}
if($model->ot_id==10){
    $strClassHideSummary='hidden';
    $strClassHideFlow='hidden';
    $strClassHideSchedule='hidden';
    $strClassHideDetails='';
}
if(!isset($model->tracking[Tracking::TYPE_DISPATCHED])){
    $strClassHideSummary='hidden';
    $strClassHideFlow='hidden';
    $strClassHideSchedule='hidden';
    $strClassHideDetails='';
}
if (isset($model->tracking[Tracking::TYPE_DISPATCHED])) {
    $mobileProgressClassDispatch = 'step active';
}
if (isset($model->tracking[Tracking::TYPE_ARRIVAL])) {
    $mobileProgressClassArrival = 'step active';
}
if (isset($model->tracking[Tracking::TYPE_CLEARED]) && isset($model->tracking[Tracking::TYPE_DECONSOLIDATION])) {
    $mobileProgressClassClear = 'step active';
}
if (isset($model->tracking[Tracking::TYPE_HAND_OVER])) {
    $mobileProgressClassTransit = 'step active';
}
if (isset($model->tracking[Tracking::TYPE_DELIVERED])) {
    $mobileProgressClassDone = 'step active';
}
$strPhone = "";
$driverID = $model->driverID;
if(!empty($driverID)){
    if(!empty(CargoProcessVehicle::$arrayDriversPhone[$driverID])){
        $strPhone = "   Driver's Phone: ".CargoProcessVehicle::$arrayDriversPhone[$driverID];
    }
}

$Postcode = ImParcel::model()->findByPk($model->id)->cnee->postcode;

if (!empty(ImParcel::model()->findByPk($model->id)->cargo_process)) {
    if (!empty(ImParcel::model()->findByPk($model->id)->cargo_process->job_relation)) {
        if (!empty(ImParcel::model()->findByPk($model->id)->cargo_process->job_relation->job->driver_id)) {
            $intDriverId = ImParcel::model()->findByPk($model->id)->cargo_process->job_relation->job->driver_id; //this one is org id
            if (@$model->listTrackingRecord[0]->activity == 'Shipment despatched to courier') {
                $driverLocation = DriverTracking::model()->findByAttributes(['driver_id' => $intDriverId, 'type' => DriverTracking::TRACKING_REALTIME]);
            } elseif (@$model->listTrackingRecord[0]->activity == 'Parcels Successfully Delivered') {
                $driverLocation = DriverTracking::model()->findByAttributes(['driver_id' => $intDriverId, 'type' => DriverTracking::TRACKING_DELIVERED, 'cargo_process_id' => ImParcel::model()->findByPk($model->id)->cargo_process->id]);
            }
        }
    }
}
/**
 * Only display tracking map when having driver's GPS information in system
 * and the newest status of tracking is 'Shipment despatched to courier'.
 */

if (!empty($driverLocation) && (@$model->listTrackingRecord[0]->activity == 'Shipment despatched to courier' || @$model->listTrackingRecord[0]->activity == 'Parcels Successfully Delivered')) {
    $strClassHideGpsTracking = 'block';
}
?>

<html>

<head>
    <!-- <script src="https://www.google.com/recaptcha/api.js?render=<?= RecaptchaAPI::RECAPTCHA_V3_SITE_KEY ?>"></script> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .done {
            display: inline-block;
            border-radius: 5px;
            /* background: rgb(100, 110, 118); */
            background: green;
            color: white;
            height: 32px;
            padding: 4px;
        }

        .willDo {
            display: inline-block;
            border-radius: 5px;
            border: 1px solid rgb(100, 110, 118);
            color: rgb(100, 110, 118);
            height: 32px;
            padding: 4px;
        }

        .btnGo {
            width: 100px;
        }

        .inputTracking {
            width: 400px;
        }

        .divTracking {
            margin-left: auto;
            margin-right: auto;
            width: 510px;
        }

        .spanArrowRight::before {
            display: inline-block;
            background-color: rgb(14, 34, 80);
            width: 60px;
            height: 10px;
            content: '--------';
            color: white;
            margin-left: 2px;
            vertical-align: middle;

            line-height: 7px;
        }

        .spanArrowRight::after {
            display: inline-block;
            border-left: 10px solid rgb(14, 34, 80);
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            margin-bottom: -5px;
            width: 0;
            height: 0;
            content: ' '
        }


        .grayLine::before {
            display: inline-block;
            color: rgb(100, 110, 118);
            content: '....';
            width: 60px;
            height: 10px;
            margin-left: 2px;
            /* vertical-align: middle; */
            line-height: 5px;
            border-radius: 5px;
            font-size: 60px;
        }

        .greenLine-small {
            display: inline-block;
            background-color: green;
            width: 16px;
            height: 5px;
            margin-left: 2px;
            /* vertical-align: middle; */
            border-radius: 5px;
        }

        .grayLine-small::before {
            display: inline-block;
            color: rgb(100, 110, 118);
            content: '....';
            width: 16px;
            height: 5px;
            margin-left: 2px;
            /* vertical-align: middle; */
            line-height: 5px;
            border-radius: 5px;
            font-size: 16px;
        }

        .spanSummary {
            display: inline-block;
            border: 1px solid black;
            height: 21px;
        }

        .divSummary {
            display: inline-block;
            border: 1px solid black;
            height: 21px;
        }

        .divLayoutSummary {
            display: inline-block;
            height: 100px;
            vertical-align: top
        }

        .divLayoutSummary-small {
            display: inline-block;
            height: 42px;
            vertical-align: top
        }


        table {
            text-align: center;
            border-collapse: collapse;
        }

        table,
        table tr th,
        table tr td {
            border: 1px solid rgb(14, 34, 80);
            ;
        }

        .divDetails {
            border: 1px solid black;
            padding: 0px;
        }

        .divDetailsHeader {
            margin: 0px;
            background: rgb(100, 110, 118);
            color: white;
        }

        .hidden {
            display: none
        }

        .btnClose {
            margin: 20px auto 20px auto;

            border-radius: 5px;
            border: 1px solid rgb(100, 110, 118);
            color: rgb(100, 110, 118);
            height: 40px;
            width: 150px;
            padding: 4px;
            content: "";
        }

        .greenLine {
            display: inline-block;
            background-color: green;
            width: 60px;
            height: 10px;
            margin-left: 2px;
            /* vertical-align: middle; */
            border-radius: 5px;
            margin-top: 50px;
        }

        .greenLineRotate
        {
            margin-top: 30px;
            margin-bottom: 15px;
            transform:rotate(-30deg);
        }

        .greenLineRotate2
        {
            margin-top: 15px;
            margin-bottom: 30px;
            transform:rotate(30deg);
        }

        .greenLineRotate3
        {
            margin-top: 30px;
            margin-bottom: 15px;
            transform:rotate(30deg);
        }

        .greenLineRotate4
        {
            margin-top: 15px;
            margin-bottom: 30px;
            transform:rotate(-30deg);
        }

        .grayLineRotate
        {
           margin-top: 7px;
           margin-left:-10px;
           margin-bottom: 10px;
           transform:rotate(-30deg);
        }
        .grayLineRotate2
        {
           margin-top: 10px;
           margin-left:9px;
           margin-bottom: 7px;
           transform:rotate(30deg);
        }
        .grayLineRotate3
        {
           margin-top: 10px;
           margin-bottom: 10px;
           transform:rotate(30deg);
        }
        .grayLineRotate4
        {
           margin-top: 10px;
           margin-left:-20px;
           margin-bottom: 10px;
           transform:rotate(-30deg);
        }


        .icon {
            border-radius: 12px;
            width: 100px;
        }

        .icon-container
        {
            height: 15em;
        }
        @media (max-width: 9999px){
            .greenLine {
                display: inline-block;
                background-color: green;
                width: 90px;
                height: 10px;
                margin-left: 2px;
                /* vertical-align: middle; */
                border-radius: 5px;
                margin-top: 80px;
            }
            .grayLine::before
            {
                width: 90px;
                content:"......";
                margin-top: 40px;
            }
            .greenLineRotate
            {
                margin-top: 45px;
            }
            .greenLineRotate2
            {
                margin-top: 50px;
            }

            .greenLineRotate3
            {
                margin-top: 45px;
            }
            .greenLineRotate4
            {
                margin-top: 50px;
            }

            .grayLineRotate
            {
               margin-top: -10px;
               margin-left:-20px;
               margin-right:20px;
            }
            .grayLineRotate2
            {
               margin-top: -10px;
               margin-right:-10px;
               margin-left:20px;
            }
            .grayLineRotate3
            {
               margin-top: -10px;
               margin-left: 20px;
               margin-right:-20px;
            }
            .grayLineRotate4
            {
               margin-top: -10px;
               margin-left:-20px;
               margin-right:20px;
            }

            .icon {
                border-radius: 12px;
                width: 140px;
            }

            .icon_mi_to {
                margin-top: -60px;
            }
            .icon_mi_bo
            {
                margin-top: 20px;
            }

             .icon-container
            {
                height: 20em;
                margin-top: 2em;
            }
        }

        @media (max-width: 1500px){
            .icon-small {
                border-radius: 6px;
                width: 90px;
            }
            .icon {
                border-radius: 12px;
                width: 120px;
            }
             .icon-container
            {
                height: 15em;
            }
        }
        
        @media (max-width: 1200px){
            .greenLine {
                width: 90px;
                margin-top: 60px;
            }
            .grayLine::before
            {
                width: 90px;
                content:".....";
                margin-top: 40px;
            }
            .greenLineRotate
            {
                margin-top: 25px;
            }
            .greenLineRotate2
            {
                margin-top: 30px;
            }

            .greenLineRotate3
            {
                margin-top: 25px;
            }
            .greenLineRotate4
            {
                margin-top: 30px;
            }

            .grayLineRotate
            {
               margin-top: -30px;
               margin-left:-20px;
               margin-right:20px;
            }
            .grayLineRotate2
            {
               margin-top: -30px;
               margin-right:-10px;
               margin-left:20px;
            }
            .grayLineRotate3
            {
               margin-top: -30px;
               margin-left: 20px;
               margin-right:-20px;
            }
            .grayLineRotate4
            {
               margin-top: -30px;
               margin-left:-20px;
               margin-right:20px;
            }

            .icon {
                border-radius: 12px;
                width: 140px;
            }

            .icon_mi_to {
                margin-top: -60px;
            }
            .icon_mi_bo
            {
                margin-top: 20px;
            }

            .icon-small {
                border-radius: 6px;
                width: 90px;
            }
            .icon {
                border-radius: 12px;
                width: 100px;
            }
             .icon-container
            {
                height: 12em;
                margin-top: 1em;
            }
        }

        @media (max-width: 999px){
            .greenLine-small{
                margin-top: 80px;
                width:40px;
                margin-left: 10px;
                margin-right: 10px;
            }
            .greenLine-small-rotate
            {
               margin-top: 65px;
               margin-bottom: 10px;
               margin-left:    10px;
               margin-right: 10px;
               transform:rotate(-30deg);
            }
            .greenLine-small-rotate2
            {
                margin-top: 30px;
                margin-bottom: 30px;
                margin-left: 10px;
                margin-right: 10px;
                transform:rotate(30deg);
            }

            .greenLine-small-rotate3
            {
              margin-top: 65px;
              margin-bottom: 10px;
              margin-left: -10px;
              margin-right: 8px;
              transform:rotate(30deg);
            }
            .greenLine-small-rotate4
            {
                margin-top: 30px;
                margin-bottom: 30px;
                margin-left: -10px;
                transform:rotate(-30deg);
            }
            .grayLine-small::before
            {
                margin-top: 80px;
                width:40px;
                margin-left: 10px;
                margin-right: 10px;
                content:"......";
            }
            .grayLine-small-rotate
            {
               margin-top: -20px;
               -webkit-transform: rotate(-30deg) translateX(-119%) translateY(-13%);
                -ms-transform:  rotate(-30deg) translateX(-119%) translateY(-13%);
                 -o-transform:  rotate(-30deg) translateX(-119%) translateY(-13%);
                    transform:  rotate(-30deg) translateX(-119%) translateY(-13%);
                   transform:  rotate(-30deg) translateX(-119%) translateY(-13%);
               width:30px;
            }
            .grayLine-small-rotate2
            {
                margin-top: -60px;
                margin-left:10px;
                margin-right:10px;
                -webkit-transform: rotate(30deg) translateX(30%) translateY(1%);
                -ms-transform: rotate(30deg) translateX(30%) translateY(1%);
                 -o-transform: rotate(30deg) translateX(30%) translateY(1%);
                    transform: rotate(30deg) translateX(30%) translateY(1%);
                   transform:  rotate(30deg) translateX(30%) translateY(1%);
                width:30px;
            }

            .grayLine-small-rotate3
            {
              margin-top: -20px;
              margin-bottom: 10px;
              margin-left: 0px;
              margin-right: -20px;
              transform:rotate(30deg);
            }
            .grayLine-small-rotate4
            {
                margin-top: -60px;
                margin-left: -50px;
                margin-right: 20px;
                transform:rotate(-30deg);
            }




            .grayLine::before
            {
                width: 90px;
                content:".....";
                margin-top: 40px;
            }
            .greenLineRotate
            {
                margin-top: 25px;
            }
            .greenLineRotate2
            {
                margin-top: 30px;
            }

            .greenLineRotate3
            {
                margin-top: 25px;
            }
            .greenLineRotate4
            {
                margin-top: 30px;
            }

            .grayLineRotate
            {
               margin-top: -30px;
               margin-left:-20px;
               margin-right:20px;
            }
            .grayLineRotate2
            {
               margin-top: -30px;
               margin-right:-10px;
               margin-left:20px;
            }
            .grayLineRotate3
            {
               margin-top: -30px;
               margin-left: 20px;
               margin-right:-20px;
            }
            .grayLineRotate4
            {
               margin-top: -30px;
               margin-left:-20px;
               margin-right:20px;
            }

            .icon {
                border-radius: 12px;
                width: 80px;
            }

            .icon_mi_to {
                margin-top: -60px;
            }
            .icon_mi_bo
            {
                margin-top: 20px;
            }

            .icon-small {
                border-radius: 6px;
                width: 80px;
            }
            .icon {
                border-radius: 12px;
                width: 100px;
            }
             .icon-container
            {
                height: 12em;
                margin-top: 1em;
            }
        }

        @media (max-width: 900px){
            .icon {
                border-radius: 6px;
                width: 90px;
            }
            .icon-small {
                border-radius: 6px;
                width: 90px;
            }
             .icon-container
            {
                height: 12em;
            }
        }

        @media (max-width: 800px){
            .icon {
                border-radius: 6px;
                width: 90px;
            }
            .icon-small {
                border-radius: 6px;
                width: 90px;
            }
            .icon-container
            {
                height: 12em;
            }
        }

        @media (max-width: 700px){
            .icon {
                border-radius: 6px;
                width: 60px;
            }
            .icon-small {
                border-radius: 6px;
                width: 60px;
            }
            .icon-container
            {
                height: 12em;
            }
        }

        @media (max-width: 600px){
           .greenLine-small{
                margin-top: 60px;
                width:20px;
                margin-left: 2px;
                margin-right: 2px;
            }

            .greenLine-small-rotate
            {
               margin-top: 45px;
               margin-bottom: 10px;
               margin-left:    10px;
               margin-right: 10px;
               transform:rotate(-30deg);
            }
            .greenLine-small-rotate2
            {
                margin-top: 20px;
                margin-bottom: 30px;
                margin-left: 10px;
                margin-right: 10px;
                transform:rotate(30deg);
            }

            .greenLine-small-rotate3
            {
              margin-top: 45px;
              margin-bottom: 10px;
              margin-left: -10px;
              margin-right: 8px;
              transform:rotate(30deg);
            }
            .greenLine-small-rotate4
            {
                margin-top: 30px;
                margin-bottom: 30px;
                margin-left: -10px;
                transform:rotate(-30deg);
            }
            .grayLine-small::before
            {
                margin-top: 60px;
                width:40px;
                margin-left: 2px;
                margin-right: 2px;
                content:"......";
            }
            .grayLine-small-rotate
            {
               margin-top: -20px;
               margin-left:-10px;
               margin-right:-10px;
               -webkit-transform: rotate(-30deg) translateX(-91%) translateY(-18%);
                -ms-transform:  rotate(-30deg) translateX(-91%) translateY(-18%);
                 -o-transform:  rotate(-30deg) translateX(-91%) translateY(-18%);
                    transform:  rotate(-30deg) translateX(-91%) translateY(-18%);
                   transform: rotate(-30deg) translateX(-91%) translateY(-18%);
               width:30px;
            }
            .grayLine-small-rotate2
            {
                margin-top: -60px;
               margin-left:-10px;
               margin-right:-10px;
                -webkit-transform: rotate(30deg) translateX(55%) translateY(-1%);
                -ms-transform: rotate(30deg) translateX(55%) translateY(-1%);
                 -o-transform: rotate(30deg) translateX(55%) translateY(-1%);
                    transform: rotate(30deg) translateX(55%) translateY(-1%);
                   transform:  rotate(30deg) translateX(55%) translateY(-1%);
                width:30px;
            }

            .grayLine-small-rotate3
            {
              margin-top: -20px;
              margin-bottom: 10px;
              margin-left: 0px;
              margin-right: -20px;
            -webkit-transform: rotate(30deg) translateX(-7%) translateY(7%);
                -ms-transform: rotate(30deg) translateX(-7%) translateY(7%);
                 -o-transform: rotate(30deg) translateX(-7%) translateY(7%);
                    transform: rotate(30deg) translateX(-7%) translateY(7%);
                   transform:  rotate(30deg) translateX(-7%) translateY(7%);
            }
            .grayLine-small-rotate4
            {
                margin-top: -60px;
                margin-left: -50px;
                margin-right: 20px;
                -webkit-transform: rotate(-30deg) translateX(9%) translateY(5%);
                -ms-transform:  rotate(-30deg) translateX(9%) translateY(5%);
                 -o-transform:  rotate(-30deg) translateX(9%) translateY(5%);
                    transform:  rotate(-30deg) translateX(9%) translateY(5%);
                   transform:   rotate(-30deg) translateX(9%) translateY(5%);
            }
            .grayLine-small-end
            {
                margin-left: -20px;
            }




            .grayLine::before
            {
                width: 90px;
                content:".....";
                margin-top: 40px;
            }
            .greenLineRotate
            {
                margin-top: 25px;
            }
            .greenLineRotate2
            {
                margin-top: 30px;
            }

            .greenLineRotate3
            {
                margin-top: 25px;
            }
            .greenLineRotate4
            {
                margin-top: 30px;
            }

            .grayLineRotate
            {
               margin-top: -30px;
               margin-left:-20px;
               margin-right:20px;
            }
            .grayLineRotate2
            {
               margin-top: -30px;
               margin-right:-10px;
               margin-left:20px;
            }
            .grayLineRotate3
            {
               margin-top: -30px;
               margin-left: 20px;
               margin-right:-20px;
            }
            .grayLineRotate4
            {
               margin-top: -30px;
               margin-left:-20px;
               margin-right:20px;
            }


           
            .icon-small {
                border-radius: 6px;
                width: 50px;
            }
            .icon {
                border-radius: 12px;
                width: 50px;
            }
             .icon-container
            {
                height: 12em;
                margin-top: 1em;
            }
        }

        @media (max-width: 500px){
            .greenLine-small{
                margin-top: 60px;
                width:15px;
                margin-left: 2px;
                margin-right: 2px;
            }

            .grayLine-small::before
            {
                margin-top: 40px;
                width:15px;
                margin-left: 2px;
                margin-right: 2px;
                content:".....";
            }

            .greenLine-small-rotate
            {
               margin-top: 35px;
               margin-bottom: 10px;
               margin-left:    0px;
               margin-right: 0px;
               -webkit-transform: rotate(-30deg) translateX(-25%) translateY(133%);
                -ms-transform:  rotate(-30deg) translateX(-25%) translateY(133%);
                 -o-transform:  rotate(-30deg) translateX(-25%) translateY(133%);
                    transform:  rotate(-30deg) translateX(-25%) translateY(133%);
                   transform:   rotate(-30deg) translateX(-25%) translateY(133%);
            }
            .greenLine-small-rotate2
            {
                margin-top: 20px;
                margin-bottom: 30px;
                margin-left: 10px;
                margin-right: 10px;
                -webkit-transform: rotate(30deg) translateX(-24%) translateY(-62%);
                -ms-transform:  rotate(30deg) translateX(-24%) translateY(-62%);
                 -o-transform:  rotate(30deg) translateX(-24%) translateY(-62%);
                    transform:  rotate(30deg) translateX(-24%) translateY(-62%);
                   transform:   rotate(30deg) translateX(-24%) translateY(-62%);
            }

            .greenLine-small-rotate3
            {
              margin-top: 35px;
              margin-bottom: 10px;
              margin-left: -10px;
              margin-right: 8px;
              -webkit-transform: rotate(30deg) translateX(7%) translateY(135%);
              -ms-transform:  rotate(30deg) translateX(7%) translateY(135%);
               -o-transform:  rotate(30deg) translateX(7%) translateY(135%);
                  transform:  rotate(30deg) translateX(7%) translateY(135%);
                 transform:   rotate(30deg) translateX(7%) translateY(135%);
            }
            .greenLine-small-rotate4
            {
                margin-top: 20px;
                margin-bottom: 30px;
                margin-left: -10px;
                -webkit-transform: rotate(-30deg) translateX(-31%) translateY(-23%);
              -ms-transform:  rotate(-30deg) translateX(-31%) translateY(-23%);
               -o-transform:  rotate(-30deg) translateX(-31%) translateY(-23%);
                  transform:  rotate(-30deg) translateX(-31%) translateY(-23%);
                 transform:   rotate(-30deg) translateX(-31%) translateY(-23%);
            }

            .grayLine-small::before
            {
                margin-top: 55px;
                width:20px;
                margin-left: 2px;
                margin-right: 2px;
                content:".....";
            }
            .grayLine-small-end
            {
                margin-left: -16px;
            }
            .grayLine-small-rotate
            {
               margin-top: -20px;
                margin-left:-20px;
               -webkit-transform: rotate(-30deg) translateX(-46%) translateY(-2%);
                -ms-transform:  rotate(-30deg) translateX(-46%) translateY(-2%);
                 -o-transform:  rotate(-30deg) translateX(-46%) translateY(-2%);
                    transform:  rotate(-30deg) translateX(-46%) translateY(-2%);
                   transform:  rotate(-30deg) translateX(-46%) translateY(-2%);
               width:30px;
            }
            .grayLine-small-rotate2
            {
                margin-top: -60px;
                margin-left:-20px;
                margin-right:10px;
                -webkit-transform: rotate(30deg) translateX(89%) translateY(-9%);
                -ms-transform: rotate(30deg) translateX(89%) translateY(-9%);
                 -o-transform: rotate(30deg) translateX(89%) translateY(-9%);
                    transform: rotate(30deg) translateX(89%) translateY(-9%);
                   transform:  rotate(30deg) translateX(89%) translateY(-9%);
                width:30px;
            }

            .grayLine-small-rotate3
            {
              margin-top: -20px;
              margin-bottom: 10px;
              margin-left: 0px;
              margin-right: -20px;
            -webkit-transform: rotate(30deg) translateX(-9%) translateY(13%);
                -ms-transform: rotate(30deg) translateX(-9%) translateY(13%);
                 -o-transform: rotate(30deg) translateX(-9%) translateY(13%);
                    transform: rotate(30deg) translateX(-9%) translateY(13%);
                   transform:  rotate(30deg) translateX(-9%) translateY(13%);
            }
            .grayLine-small-rotate4
            {
                margin-top: -60px;
                margin-left: -20px;
                margin-right: 20px;
                -webkit-transform: rotate(-30deg) translateX(-33%) translateY(-11%);
                -ms-transform:  rotate(-30deg) translateX(-33%) translateY(-11%);
                 -o-transform:  rotate(-30deg) translateX(-33%) translateY(-11%);
                    transform:  rotate(-30deg) translateX(-33%) translateY(-11%);
                   transform:   rotate(-30deg) translateX(-33%) translateY(-11%);
            }


            .icon-small {
                border-radius: 6px;
                width: 43px;
            }
            .icon-container
            {
                height: 7em;
            }
        }


        @media (max-width: 400px){
            .greenLine-small{
                margin-top: 60px;
                width:10px;
                margin-left: 2px;
                margin-right: 2px;
            }

            .grayLine-small::before
            {
                margin-top: 50px;
                width:10px;
                margin-left: 2px;
                margin-right: 2px;
                content:"....";
            }
             .greenLine-small-rotate
            {
               margin-top: 35px;
               margin-bottom: 10px;
               margin-left:    0px;
               margin-right: 0px;
               -webkit-transform: rotate(-30deg) translateX(-48%) translateY(-1%);
                -ms-transform:  rotate(-30deg) translateX(-48%) translateY(-1%);
                 -o-transform:  rotate(-30deg) translateX(-48%) translateY(-1%);
                    transform:  rotate(-30deg) translateX(-48%) translateY(-1%);
                   transform:   rotate(-30deg) translateX(-48%) translateY(-1%);
            }
            .greenLine-small-rotate2
            {
                margin-top: 20px;
                margin-bottom: 30px;
                margin-left: 10px;
                margin-right: 10px;
                -webkit-transform: rotate(30deg) translateX(110%) translateY(14%);
                -ms-transform:  rotate(30deg) translateX(110%) translateY(14%);
                 -o-transform:  rotate(30deg) translateX(110%) translateY(14%);
                    transform:  rotate(30deg) translateX(110%) translateY(14%);
                   transform:   rotate(30deg) translateX(110%) translateY(14%);
            }

            .greenLine-small-rotate3
            {
              margin-top: 35px;
              margin-bottom: 10px;
              margin-left: -10px;
              margin-right: 8px;
              -webkit-transform: rotate(30deg) translateX(-19%) translateY(12%);
              -ms-transform:  rotate(30deg) translateX(-19%) translateY(12%);
               -o-transform:  rotate(30deg) translateX(-19%) translateY(12%);
                  transform:  rotate(30deg) translateX(-19%) translateY(12%);
                 transform:   rotate(30deg) translateX(-19%) translateY(12%);
            }
            .greenLine-small-rotate4
            {
                margin-top: 20px;
                margin-bottom: 30px;
                margin-left: -10px;
                -webkit-transform: rotate(-30deg) translateX(-127%) translateY(17%);
              -ms-transform:  rotate(-30deg) translateX(-127%) translateY(17%);
               -o-transform:  rotate(-30deg) translateX(-127%) translateY(17%);
                  transform:  rotate(-30deg) translateX(-127%) translateY(17%);
                 transform:   rotate(-30deg) translateX(-127%) translateY(17%);
            }

            .icon-small {
                border-radius: 6px;
                width: 40px;
            }
            .icon-container
            {
                height: 6em;
            }
        }


        .icon_description {
            position: relative;
            left: 4px;
        }

        #indicative {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #indicative td,
        #indicative th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #indicative tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #indicative tr:hover {
            background-color: #ddd;
        }

        #indicative th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: center;
            background-color: #04AA6D;
            color: white;
        }

        .track {
            position: relative;
            background-color: #ddd;
            height: 7px;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            margin-bottom: 60px;
            margin-top: 50px;
        }

        .track .step {
            -webkit-box-flex: 1;
            -ms-flex-positive: 1;
            flex-grow: 1;
            width: 25%;
            margin-top: -18px;
            text-align: center;
            position: relative;
        }

        .track .step.active:before {
            background: #04AA6D;
        }

        .track .step::before {
            height: 7px;
            position: absolute;
            content: "";
            width: 100%;
            left: 0;
            top: 18px;
        }

        .track .step.active .mobile-icon {
            background: #04AA6D;
            color: #fff;
        }

        .track .mobile-icon {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 40px;
            position: relative;
            border-radius: 100%;
            margin-top: 6px;
            background: #ddd;
            padding-top: 6px;
        }

        .track .step.active .text {
            font-weight: 400;
            font-size: small;
            color: #000;
        }

        .track .text {
            display: block;
            margin-top: 7px;
            font-size: small;
        }
    </style>
</head>

<body class="background">
    <div class="container-fluid">
        <div class="background">
            <!--         <div class="divTracking">
            <h1 style="font-size: 40;">TRACK YOUR  SHIPMENT</h1>
            <span><form method="post"><input name="tracking_number" class="inputTracking" type="text" placeholder="Enter tracking No. eg TCNxxx"> <input class="btnGo" value="go" type="submit"></form></span>
        </div> -->
            <?php

            ?>
            <div class="contianer-fluid">
                <div class="<?= $strClassShowError ?>">
                    <h2>We cannot find this parcel in our system. Please try another ref number.</h2>
                </div>
                <div class="<?= $strClassHideSummary ?> d-none d-lg-block">
                    <h3>Shipment Summary</h3>
                    <?php if (empty($model)) : ?>
                        <h5>Parcel is not existed;</h5>
                    <?php else : ?>
                        <span>
                            From:
                            <span class="willDo"><?= $model->from ?></span><span class="spanArrowRight"></span>
                            <span class="willDo"><?= $model->to ?></span></span>
                        Australia
                        </span>
                        <br /><br /><br />
                </div>
                <center class="<?= $strClassHideFlow ?> icon-container">
                    <div class="<?= $strClassHideFlow ?>">
                            <div class="container-fluid d-none d-lg-block">
                                <div class="divLayoutSummary">
                                    <!-- <div class="<?= !isset($model->tracking[Tracking::TYPE_DISPATCHED]) ? 'willDo' : 'Done' ?>" style="margin-top: 40px;">
                                        Left Origin
                                    </div> -->
                                    <?php
                                        if (isset($model->tracking[Tracking::TYPE_DISPATCHED])) {
                                            if ($model->consolType == 'sea') {
                                                echo '<img src="https://os.toplogistics.com.au/images/icons/Departure-Ship.png" class="icon" style="position: relative; top: 10px;" />';
                                            } else {
                                                echo '<img src="https://os.toplogistics.com.au/images/icons/Departure.png" class="icon" style="position: relative; top: 10px;" />';
                                            }
                                        } else {
                                            if ($model->consolType == 'sea') {
                                                echo '<img src="https://os.toplogistics.com.au/images/icons/Departure-Ship-grey.png" class="icon" style="position: relative; top: 10px;" />';
                                            } else {
                                                echo '<img src="https://os.toplogistics.com.au/images/icons/Departure-grey.png" class="icon" style="position: relative; top: 10px;" />';
                                            }
                                        }
                                    ?>
                                </div>
                                <div class="divLayoutSummary">
                                    <?= isset($model->tracking[Tracking::TYPE_ARRIVAL]) && strtotime($model->strConsolEta) < time() ? '<div class="greenLine"></div>' : '<div class="grayLine" ></div>' ?>
                                </div>
                                <div class="divLayoutSummary">
                                    <!-- <div class="<?= isset($model->tracking[Tracking::TYPE_ARRIVAL]) && strtotime($model->strConsolEta) < time() ? 'Done' : 'willDo' ?>" style="margin-top: 40px;">
                                        Arrived into Australia
                                    </div> -->
                                    <?php
                                        if (isset($model->tracking[Tracking::TYPE_ARRIVAL])) {
                                            if ($model->consolType == 'sea') {
                                                echo '<img src="https://os.toplogistics.com.au/images/icons/Arrival-Ship.png" class="icon" style="position: relative; top: 10px;" />';
                                            } else {
                                                echo '<img src="https://os.toplogistics.com.au/images/icons/Arrival.png" class="icon" style="position: relative; top: 10px;" />';
                                            }
                                        } else {
                                            if ($model->consolType == 'sea') {
                                                echo '<img src="https://os.toplogistics.com.au/images/icons/Arrival-Ship-grey.png" class="icon" style="position: relative; top: 10px;" />';
                                            } else {
                                                echo '<img src="https://os.toplogistics.com.au/images/icons/Arrival-grey.png" class="icon" style="position: relative; top: 10px;" />';
                                            }
                                        }
                                    ?>
                                </div>
                                
                                <div class="divLayoutSummary">
                                    <?=!isset($model->tracking[Tracking::TYPE_CLEARED]) ? '<div class="grayLine grayLineRotate"></div>' : '<div class="greenLine greenLineRotate" ></div>' ?>

                                    <br />
                                    <?=!isset($model->tracking[Tracking::TYPE_DECONSOLIDATION]) ? '<div class="grayLine grayLineRotate2"></div>' : '<div class="greenLine  greenLineRotate2"></div>' ?>

                                </div>

                                <div class="divLayoutSummary"">
                                   <!-- <div class=" <?= !isset($model->tracking[Tracking::TYPE_CLEARED]) ? 'willDo' : 'Done' ?>">
                                    Customs Clearance
                                </div> -->
                                <?php if (isset($model->tracking[Tracking::TYPE_CLEARED])) {
                                    echo '<img src="https://os.toplogistics.com.au/images/icons/Clearance.png" class="icon icon_mi_to" style="position: relative;" />';
                                } else {
                                    echo '<img src="https://os.toplogistics.com.au/images/icons/Clearance-grey.png" class="icon icon_mi_to" style="position: relative;" />';
                                }
                                ?>


                                <br />
                                <!-- <div class="<?= !isset($model->tracking[Tracking::TYPE_DECONSOLIDATION]) ? 'willDo' : 'Done' ?>" style="margin-top: 55px; width:145px">
                                    Unpacking
                                </div> -->
                                <?php if (isset($model->tracking[Tracking::TYPE_DECONSOLIDATION])) {
                                    echo '<img src="https://os.toplogistics.com.au/images/icons/Unpacking.png" class="icon icon_mi_bo" style="position: relative;" />';
                                } else {
                                    echo '<img src="https://os.toplogistics.com.au/images/icons/Unpacking-grey.png" class="icon icon_mi_bo" style="position: relative;" />';
                                }
                                ?>

                            </div>
                            <div class="divLayoutSummary">
                                <?php

                                if (!isset($model->tracking[Tracking::TYPE_HAND_OVER])) {
                                    echo '<div class="grayLine grayLineRotate3"></div>';
                                    echo '<br />';
                                    echo '<div class="grayLine grayLineRotate4"></div>';
                                } else {
                                    echo '<div class="greenLine greenLineRotate3" ></div>';
                                    echo '<br />';
                                    echo '<div class="greenLine greenLineRotate4" ></div>';
                                }
                                ?>
                            </div>
                            <div class="divLayoutSummary">
                                <!-- <div class="<?= !isset($model->tracking[Tracking::TYPE_HAND_OVER]) ? 'willDo' : 'Done' ?>" style="margin-top: 40px;">
                                    Send to Courier
                                </div> -->
                                <?php if (isset($model->tracking[Tracking::TYPE_HAND_OVER])) {
                                    echo '<img src="https://os.toplogistics.com.au/images/icons/In-transit.png" class="icon" style="position: relative; top: 10px;" />';
                                } else {
                                    echo '<img src="https://os.toplogistics.com.au/images/icons/In-transit-grey.png" class="icon" style="position: relative; top: 10px;" />';
                                }
                                ?>

                            </div>
                            <div class="divLayoutSummary">
                                <?= !isset($model->tracking[Tracking::TYPE_DELIVERED]) ? '<div class="grayLine"></div>' : '<div class="greenLine"></div>' ?>

                            </div>
                            <div class="divLayoutSummary">
                                <!-- <div class="<?= !isset($model->tracking[Tracking::TYPE_DELIVERED]) ? 'willDo' : 'Done' ?>" style="margin-top: 40px;">
                                        Courier Delivery
                                    </div> -->
                                <?php if (isset($model->tracking[Tracking::TYPE_DELIVERED])) {
                                    echo '<img src="https://os.toplogistics.com.au/images/icons/Delivered.png" class="icon" style="position: relative; top: 10px;" />';
                                } else {
                                    echo '<img src="https://os.toplogistics.com.au/images/icons/Delivered-grey.png" class="icon" style="position: relative; top: 10px;" />';
                                }
                                ?>

                            </div>
                    </div>
                    <div class="d-lg-none">
                        <div class="container-fluid">
                            <div class="divLayoutSummary-small">
                                <!-- <div class="<?= !isset($model->tracking[Tracking::TYPE_DISPATCHED]) ? 'willDo' : 'Done' ?>" style="margin-top: 40px;">
                                    Left Origin
                                </div> -->
                                <?php
                                if (isset($model->tracking[Tracking::TYPE_DISPATCHED])) {
                                    if ($model->consolType == 'sea') {
                                        echo '<img src="https://os.toplogistics.com.au/images/icons/Departure-Ship.png" class="icon-small" style="position: relative; top: 40px;" />';
                                    } else {
                                        echo '<img src="https://os.toplogistics.com.au/images/icons/Departure.png" class="icon-small" style="position: relative; top: 40px;" />';
                                    }
                                } else {
                                    if ($model->consolType == 'sea') {
                                        echo '<img src="https://os.toplogistics.com.au/images/icons/Departure-Ship-grey.png" class="icon-small" style="position: relative; top: 40px;" />';
                                    } else {
                                        echo '<img src="https://os.toplogistics.com.au/images/icons/Departure-grey.png" class="icon-small" style="position: relative; top: 40px;" />';
                                    }
                                }
                                ?>
                            </div>
                            <div class="divLayoutSummary-small">
                                <?= isset($model->tracking[Tracking::TYPE_ARRIVAL]) && strtotime($model->strConsolEta) < time() ? '<div class="greenLine-small" ></div>' : '<div class="grayLine-small"></div>' ?>
                            </div>
                            <div class="divLayoutSummary-small">
                                <!-- <div class="<?= isset($model->tracking[Tracking::TYPE_ARRIVAL]) && strtotime($model->strConsolEta) < time() ? 'Done' : 'willDo' ?>" style="margin-top: 40px;">
                                    Arrived into Australia
                                </div> -->
                                <?php
                                if (isset($model->tracking[Tracking::TYPE_ARRIVAL])) {
                                    if ($model->consolType == 'sea') {
                                        echo '<img src="https://os.toplogistics.com.au/images/icons/Arrival-Ship.png" class="icon-small" style="position: relative; top: 40px;" />';
                                    } else {
                                        echo '<img src="https://os.toplogistics.com.au/images/icons/Arrival.png" class="icon-small" style="position: relative; top: 40px;" />';
                                    }
                                } else {
                                    if ($model->consolType == 'sea') {
                                        echo '<img src="https://os.toplogistics.com.au/images/icons/Arrival-Ship-grey.png" class="icon-small" style="position: relative; top: 40px;" />';
                                    } else {
                                        echo '<img src="https://os.toplogistics.com.au/images/icons/Arrival-grey.png" class="icon-small" style="position: relative; top: 40px;" />';
                                    }
                                }
                                ?>
                            </div>

                            <div class="divLayoutSummary-small">

                                <?=!isset($model->tracking[Tracking::TYPE_CLEARED]) ? '<div class="grayLine-small grayLine-small-rotate"></div>' : '<div class="greenLine-small greenLine-small-rotate"></div>' ?>
                                <br/>
                                <?= !isset($model->tracking[Tracking::TYPE_DECONSOLIDATION]) ? '<div class="grayLine-small grayLine-small-rotate2"></div>' : '<div class="greenLine-small greenLine-small-rotate2"></div>' ?>

                            </div>

                            <div class="divLayoutSummary-small"">
                               <!-- <div class=" <?= !isset($model->tracking[Tracking::TYPE_CLEARED]) ? 'willDo' : 'Done' ?>">
                                Customs Clearance
                            </div> -->
                            <?php if (isset($model->tracking[Tracking::TYPE_CLEARED])) {
                                echo '<img src="https://os.toplogistics.com.au/images/icons/Clearance.png" class="icon-small" style="position: relative; top: 14px; left: -10px;" />';
                            } else {
                                echo '<img src="https://os.toplogistics.com.au/images/icons/Clearance-grey.png" class="icon-small" style="position: relative; top: 14px; left: -10px;" />';
                            }
                            ?>


                            <br />
                            <!-- <div class="<?= !isset($model->tracking[Tracking::TYPE_DECONSOLIDATION]) ? 'willDo' : 'Done' ?>" style="margin-top: 55px; width:145px">
                                Unpacking
                            </div> -->
                            <?php if (isset($model->tracking[Tracking::TYPE_DECONSOLIDATION])) {
                                echo '<img src="https://os.toplogistics.com.au/images/icons/Unpacking.png" class="icon-small" style="position: relative; top: 20px; left: -10px;" />';
                            } else {
                                echo '<img src="https://os.toplogistics.com.au/images/icons/Unpacking-grey.png" class="icon-small" style="position: relative; top: 20px; left: -10px;" />';
                            }
                            ?>

                        </div>
                        <div class="divLayoutSummary-small">
                            <?php
                            if (!isset($model->tracking[Tracking::TYPE_HAND_OVER])) {
                                echo '<div class="grayLine-small grayLine-small-rotate3"></div>';
                                echo '<br />';
                                echo '<div class="grayLine-small grayLine-small-rotate4"></div>';
                            } else {
                                echo '<div class="greenLine-small greenLine-small-rotate3"></div>';
                                echo '<br />';
                                echo '<div class="greenLine-small greenLine-small-rotate4"></div>';
                            }
                            ?>
                        </div>
                        <div class="divLayoutSummary-small">
                            <!-- <div class="<?= !isset($model->tracking[Tracking::TYPE_HAND_OVER]) ? 'willDo' : 'Done' ?>" style="margin-top: 40px;">
                                Send to Courier
                            </div> -->
                            <?php if (isset($model->tracking[Tracking::TYPE_HAND_OVER])) {
                                echo '<img src="https://os.toplogistics.com.au/images/icons/In-transit.png" class="icon-small" style="position: relative; top: 40px; left: -10px;" />';
                            } else {
                                echo '<img src="https://os.toplogistics.com.au/images/icons/In-transit-grey.png" class="icon-small" style="position: relative; top: 40px; left: -10px;" />';
                            }
                            ?>

                        </div>
                        <div class="divLayoutSummary-small">
                            <?= !isset($model->tracking[Tracking::TYPE_DELIVERED]) ? '<div class="grayLine-small grayLine-small-end"></div>' : '<div class="greenLine-small" style="margin-left:-0.2em;"></div>' ?>

                        </div>
                        <div class="divLayoutSummary-small">
                            <!-- <div class="<?= !isset($model->tracking[Tracking::TYPE_DELIVERED]) ? 'willDo' : 'Done' ?>" style="margin-top: 40px;">
                                    Courier Delivery
                                </div> -->
                            <?php if (isset($model->tracking[Tracking::TYPE_DELIVERED])) {
                                echo '<img src="https://os.toplogistics.com.au/images/icons/Delivered.png" class="icon-small" style="position: relative; top: 40px; left: -6px;" />';
                            } else {
                                echo '<img src="https://os.toplogistics.com.au/images/icons/Delivered-grey.png" class="icon-small" style="position: relative; top: 40px; left: -6px;" />';
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </center>
        </div>
    <?php endif; ?>
    </div>
    <?php if (!empty($model)) : ?>
        <div class="<?= $strClassHideSchedule ?>">
            <div class="container-fluid">
                <div class="track">
                    <div class="<?= $mobileProgressClassDispatch ?>"> <span class="mobile-icon"> <i class="bi bi-check-circle-fill"></i> </span> <span class="text">Departure</span> </div>
                    <div class="<?= $mobileProgressClassArrival ?>"> <span class="mobile-icon"> <i class="bi bi-pin-map-fill"></i> </span> <span class="text">Arrival</span> </div>
                    <div class="<?= $mobileProgressClassClear ?>"> <span class="mobile-icon"> <i class="bi bi-journal-check"></i> </span> <span class="text">Customs Clearance</span> </div>
                    <div class="<?= $mobileProgressClassTransit ?>"> <span class="mobile-icon"> <i class="bi bi-truck"></i> </span> <span class="text">In Transit</span> </div>
                    <div class="<?= $mobileProgressClassDone ?>"> <span class="mobile-icon"> <i class="bi bi-house-door-fill"></i> </span> <span class="text">Delivered</span> </div>
                </div>
            </div>
            <div class="container-fluid d-none d-lg-block">
                <h3 style="margin-bottom: 0px;">Indicative Delivery Schedule</h3>
                <p style="margin-top: 0px;">This is an indicative schedule, please refer to details below for most update status.</p>
                <table id="indicative">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?= !isset($model->tracking[Tracking::TYPE_DISPATCHED]) ? 'Approx ' : '' ?>Departure</th>
                            <th><?= !isset($model->tracking[Tracking::TYPE_ARRIVAL]) ? 'Approx ' : '' ?>Arrival to Australia</th>
                            <th><?= !isset($model->tracking[Tracking::TYPE_DECONSOLIDATION]) ? 'Approx ' : '' ?>Unpacking</th>
                            <th><?= !isset($model->tracking[Tracking::TYPE_HAND_OVER]) ? 'Approx ' : '' ?>Send to Courier</th>
                            <th><?= !isset($model->tracking[Tracking::TYPE_DELIVERED]) ? 'Approx ' : '' ?>Delivery Complete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            if ($model->consolType == 'sea') {
                                echo '<td>SEA FREIGHT</td>';
                                $listETA = $model->listETASea;
                            } else {
                                echo '<td>AIR FREIGHT</td>';
                                $listETA = $model->listETAAir;
                            }
                            foreach ($listETA as $objETA) {
                                echo '<td>';
                                if (is_array($objETA)) {
                                    echo $objETA[0] . ' to ' . $objETA[1];
                                } else {
                                    echo $objETA;
                                }
                                echo '</td>';
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br />
        <div class="container-fluid d-none d-lg-block">
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <h5>Most Recent Status:</h5>
                </div>
                <div class="card-body">
                    <h6 style="color:#04AA6D;"><?php echo @$model->listTrackingRecord[0]->activity; ?></h6>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Details (Reference Number: <?= $model->ref ?>)</h5>
                </div>
                <div class="card-body">
                    <?php
                    $i = 0;
                    $previousDate = '01-01-0000';
                    $trackingRecord = $model->listTrackingRecord;

                    // Sort tracking record by time ascending order
                    usort($trackingRecord, function ($a, $b) {
                        return strtotime($a->date) - strtotime($b->date);
                    });

                    foreach ($trackingRecord as $i => $objTracking) {
                        // Split timestamp into date and time, add an attribute 'weekDay'
                        $timeStamp = $objTracking->date;
                        $objTracking->date = date('d-m-Y', strtotime($timeStamp));
                        $objTracking->time = date('H:i', strtotime($timeStamp));
                        $objTracking->weekDay = date('D', strtotime($timeStamp));
                        if ($objTracking->time == '00:00') {
                            $objTracking->time = 'N/A';
                        }

                        if (!empty($objTracking)) {
                            $activity = str_replace(';O T H E R', '',$objTracking->activity);
                            if ($objTracking->date == $previousDate) {
                                echo '<p class="card-text">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&ensp;' . $objTracking->time . '&emsp;&ensp;' . $activity;
                                if ($objTracking->type == Tracking::TYPE_HAND_OVER) {
                                    echo ' ' . $strPhone;
                                    foreach ($model->trans as $ts) {
                                        echo ' [ ' . $ts->infoLink . ' ] ';
                                    }
                                    echo '</p>';
                                } else {
                                    echo '</p>';
                                }
                            } else {
                                echo '<hr />';
                                echo '<p class="card-text"><span style="font-weight: bold;">' . $objTracking->date . '&emsp;' . $objTracking->weekDay . '</span></p>';
                                echo '<p class="card-text">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&ensp;' . $objTracking->time . '&emsp;&ensp;' . $activity;
                                if ($objTracking->type == Tracking::TYPE_HAND_OVER) {
                                    echo ' ' . $strPhone;
                                    foreach ($model->trans as $ts) {
                                        echo ' [ ' . $ts->infoLink . ' ] ';
                                    }
                                    echo '</p>';
                                } else {
                                    echo '</p>';
                                }
                            }
                            $previousDate = $objTracking->date;
                        }
                        $i++;
                    }
                    ?>
                    <hr />
                    <p class="card-text">Website to track</p>
                    <?php
                    foreach ($model->trans as $ts) {
                        echo $ts->infoLink . '<br />';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="d-lg-none">
            <div class="container-fluid">
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Most recent status:</h5>
                    </div>
                    <div class="card-body">    
                        <p class="card-text" style="color:#04AA6D"><?php echo @$model->listTrackingRecord[0]->activity; ?></p>
                        <p class="card-text"><?php echo @$model->listTrackingRecord[0]->date . "  " .  @$model->listTrackingRecord[0]->time; ?></p>
                    </div>
                </div>
                <br />
                <div class="card">
                    <div class="card-header">
                        <h6>Tracking history</h6>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Details</h6>
                        <hr />
                        <?php
                        foreach ($trackingRecord as $trackingItem) {
                            $activity = str_replace(';O T H E R', '',$trackingItem->activity);
                            echo "<p class=\"card-text\">" . $activity . "</p>";
                            echo "<p class=\"card-text\">" . $trackingItem->date . "  " . $trackingItem->weekDay . " " . $trackingItem->time . "</p>";
                            echo "<hr />";
                        }
                        ?>
                        <p class="card-text">Website to track</p>
                        <?php
                        foreach ($model->trans as $ts) {
                            echo $ts->infoLink . '<br />';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <div class="container-fluid" style="display: <?php echo $strClassHideGpsTracking; ?>;">
            <div class="card">
                <div class="card-header">
                    <h6>Shipment location at <?php echo date('d-m-Y H:i:s', strtotime(@$driverLocation->tracking_time)); ?></h6>
                </div>
                <div class="card-body">
                    <iframe style="width: 100%; height: 30vh;" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.it/maps?q=<?php echo @$driverLocation->latitude . ',' . @$driverLocation->longitude; ?>&output=embed"></iframe>
                </div>
                <div class="card-footer text-muted">
                    <span style="color:red">* The pinpoint showing on this map is an approximate location. The actual delivery address is subject to the consignee address.</span>
                </div>
            </div>
        </div>
        <br />
        <?php if(!$model->noEnqury):?>
        <div class="container-fluid">
            <p><a href="https://ims.toplogistics.com.au/customerService/cneeShipmentQuery.app?ref=<?= $model->ref ?>" style="linkColor:#cd2653;vlinkColor:#cd2653;color:#cd2653;font-size: 1.2em" target="_blank">I want to enquire about this shipment:</a></p>
            <!-- <h5>If you require further assistance please send email to <a href="javascript:void(0)"><?= Org::IM_CS_EMAIL ?></a></h5> -->
        </div>
        <?php endif;?>
        </div>
    <?php endif; ?>
    </div>
    </div>
</body>
<script type="text/javascript">
    function postcodeVeryfi() {
        var postcode;
        var systempostcode = <?php echo $Postcode ?>;
        postcode = prompt('Enter Your Postcode.')
        if (systempostcode == postcode) {
            return true;
        } else {
            return false;
        }
        return false;
    }

    function funcMoreDetails() {
        obj = document.getElementById('more_details');
        obj.setAttribute('class', '');
    }

    function funcLessDetails() {
        obj = document.getElementById('more_details');
        obj.setAttribute('class', 'hidden');
    }
</script>

<!-- Start of LiveChat (www.livechat.com) code -->
<script>
    window.__lc = window.__lc || {};
    window.__lc.license = 15009726;
    ;(function(n,t,c){function i(n){return e._h?e._h.apply(null,n):e._q.push(n)}var e={_q:[],_h:null,_v:"2.0",on:function(){i(["on",c.call(arguments)])},once:function(){i(["once",c.call(arguments)])},off:function(){i(["off",c.call(arguments)])},get:function(){if(!e._h)throw new Error("[LiveChatWidget] You can't use getters before load.");return i(["get",c.call(arguments)])},call:function(){i(["call",c.call(arguments)])},init:function(){var n=t.createElement("script");n.async=!0,n.type="text/javascript",n.src="https://cdn.livechatinc.com/tracking.js",t.head.appendChild(n)}};!n.__lc.asyncInit&&e.init(),n.LiveChatWidget=n.LiveChatWidget||e}(window,document,[].slice))
</script>
<noscript><a href="https://www.livechat.com/chat-with/15009726/" rel="nofollow">Chat with us</a>, powered by <a href="https://www.livechat.com/?welcome" rel="noopener nofollow" target="_blank">LiveChat</a></noscript>
<!-- End of LiveChat code -->

</html>


