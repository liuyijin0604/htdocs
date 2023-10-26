<?php

class ImportPaymentController extends PController{
    
    public function actionSeaStorage(){
       $shipment = ImParcel::model()->find('hbn=:hbn', array(':hbn' => $_GET['hbn']));
        if (!empty($shipment)&&(!empty($shipment->consol->bwf))&&(($shipment->consol->bwf&2)>0)) {
            $hash = isset($_GET['h']) ? $_GET['h'] : '';
            if (HashVerify::verify($hash, $shipment->id)) {
                $this->render('sea_parcel', array('model' => $shipment));
            } else {
                throw new CHttpException(400, "Visit Limit!");
            }
        } else {
            throw new CHttpException(400, "Parcel Not Found!");
        }
    }
    public function actionCreateUrl(){
           $shipment = ImParcel::model()->find('hbn=:hbn', array(':hbn' => $_GET['hbn']));
           $url=$shipment->getSeaHashUrl();
           echo $url;
    }
} 
