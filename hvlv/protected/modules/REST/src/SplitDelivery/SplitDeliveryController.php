<?php

require_once 'protected\modules\REST\src\RestController.php';
require_once 'protected\modules\REST\src\SplitDelivery\FactorySplitDelivery.php';



class SplitDeliveryController extends RestController
{
    
	protected $nonAjax=['scanprint',];
	
    
    public function actionImport()
    {
        $objData = json_decode($_POST['data']);
        foreach ($objData as $i=>$item){
            FactorySplitDelivery::StrategyByCourierId($item);
        }
        
        $objResponce = new stdClass;
        $objResponce->isSuccess = true;
        // $objResponce->data = $objRecordsArray;
        $result = json_encode($objResponce);
        echo $result;
    }
    
    public function actionRecords()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $strTimeFrom = $_GET['startTime'];
                $strTimeTo = $_GET['endTime'];
                
                $objOrmSplitDelivery = new OrmSplitDelivery;
                // $objAllRecords = $objOrmSplitDelivery->findAll();
                $objAllRecords = $objOrmSplitDelivery->funcGetRecordsByTime($strTimeFrom ,$strTimeTo);
                
                $objResponce = new stdClass;
                $objRecordsArray = array();
                foreach($objAllRecords as $i =>$objRecord){
                    $objModelSplitDelivery = new ModelSplitDelivery;
                    $objModelSplitDelivery->setORM($objRecord);
                    $obj = $objModelSplitDelivery->funcOrm2StdClass();
                    
                    $objRecordsArray[] = $obj;
                }
                $objResponce->isSuccess = true;
                $objResponce->data = $objRecordsArray;
                $result = json_encode($objResponce);
                echo $result;
            break;
            case 'POST':
            break;
            
        }
    }
    
    public function actionScanPrint(){
        $strSubNumber = $_GET[Str::sub_number];
        
        $objModalSplitDelivery = FactorySplitDelivery::StrategyByCourierIdForSubNumber($strSubNumber);
        
        $objModalSplitDelivery->funcSetPrintTimeNow();
        $objModalSplitDelivery->funcSetTaskComplete();
        
        $objModalSplitDelivery->funcScanPrint();
    }
    
    
    public function actionGetOrgList(){
        $objResponce = new stdClass;
        $objRecordsArray = array();
        
        // $listAllOrg = Org::model()->findAll();
        $listAllOrg = Org::model()->findAll('type=:type',[':type'=>Org::CLIENTTYPE]);
        foreach($listAllOrg as $objOrg ){
            $obj = new stdClass;
            $obj->id = $objOrg->id;
            $obj->name = $objOrg->name;
            
            $objRecordsArray[] = $obj;
        }
        $objResponce->isSuccess = true;
        $objResponce->data = $objRecordsArray;
        $result = json_encode($objResponce);
        echo $result;
    }
    
    // public function actionA(){
    //     echo 'a';
    // }
    
    
    
    
    
}