<?php

require_once 'protected\modules\REST\src\SplitDelivery\ModelSplitDelivery.php';

class ModelSplitDeliveryDFE extends ModelSplitDelivery{
    
    
    function funcDeliveryTaskToShipment(){
        $this->objTaskDelivery->toShipment();
        
        $strPostCode = $this->orm[Str::postcode];
        $strState = $this->orm[Str::state];
        $strSuburb = $this->orm[Str::suburb];
        
        $zoneMap = DfeZone::model()->find('postcode = :postcode AND state = :state AND postcodename = :suburb', [':postcode' =>$strPostCode ,':state' => strtoupper($strState),':suburb' => strtoupper($strSuburb)]);
        if($zoneMap !== null){
            $chargeCode = 'N1';
            $postcodename = '';
            
            $sortcode = $zoneMap->sortcode;
            $sortcodeArr = explode(' ', $sortcode);
            $tmp = strip_tags($sortcodeArr[0]);
            $temLen = mb_strlen($tmp);
            $head = mb_substr($tmp,0,2,'utf-8');
            $withoutHead = explode($head, $sortcodeArr[0]);
            unset($withoutHead[0]);
            $middle = join($head,$withoutHead);
            $tail = $sortcodeArr[1];
            // return ["head"=>$head,"middle"=>$middle,"tail"=>$tail];
            
            $this->orm->JsonData['DFE']['head']=$head;
            $this->orm->JsonData['DFE']['middle']=$middle;
            $this->orm->JsonData['DFE']['tail']=$tail;
            
            $this->orm->save();
        }
    }
}
