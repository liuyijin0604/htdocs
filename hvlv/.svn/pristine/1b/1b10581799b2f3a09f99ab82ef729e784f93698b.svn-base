<?php


require_once 'protected\modules\REST\src\SplitDelivery\ModelSplitDeliveryDFE.php';
require_once 'protected\modules\REST\src\SplitDelivery\ModelSplitDelivery.php';

class FactorySplitDelivery{
    
    static function StrategyByCourierId($objSdtClass){
        $strCoutierId = $objSdtClass->courier_id;
        switch ($strCoutierId){
            case Org::ORGID_COURIER_DFE_TOP :
                $instance = new ModelSplitDeliveryDFE;
                $instance->CreateFromStdClass($objSdtClass);
                return $instance;
            break;
            default:
                $instance = new ModelSplitDelivery;
                $instance->CreateFromStdClass($objSdtClass);
                return $instance;
            break;
        }
        
    }
    
    static function StrategyByCourierIdForSubNumber($strSubNumber){
        $result = OrmSplitDelivery::funcFindBySubNumber($strSubNumber);
        // if(count($result) !==1 ){
        //     throw new Exception (count($result).' records of this subnumber');
        // }
        
        if(count($result) ===0 ){
            echo '0 records of this subnumber';
            Yii::app()->end();
        }
        
        $objORM = $result[0];
        $strCoutierId = $objORM[Str::courier_id];
        switch ($strCoutierId){
            case Org::ORGID_COURIER_DFE_TOP :
                $instance = new ModelSplitDeliveryDFE;
                $instance->CreateFromORM($objORM);
                return $instance;
            break;
            default:
                $instance = new ModelSplitDelivery;
                $instance->CreateFromORM($objORM);
                return $instance;
            break;
        }
        
    }
    
}