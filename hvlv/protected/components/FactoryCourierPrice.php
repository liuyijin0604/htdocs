<?php
class FactoryCourierPrice{
    const str_depot_brisbane = 'Brisbane';

    static function funcChargeCodePrice($numChargeCode){
        return new CChargeCodePrice($numChargeCode);
    }

    static function funcSurchargeTLD(){
        return new CCourierSurchargeTLD();
    }

    static function funcSurchargeTLDByDepot($strDepot){
        if($strDepot == self::str_depot_brisbane){
            return new CCourierSurchargeTLDBne();
        }
        return new CCourierSurchargeTLD();
    }

    static function funcSurchargeEizToll(){
        return new CCourierSurchargeEizToll();
    }

    static function funcListItem($listItem){
        $listCItem = [];
        foreach ($listItem as $item){
            $objC = new CItem($item->category,$item->weight,$item->length,$item->width,$item->height,$item->quantity);
            $listCItem[] = $objC;
        }
        return  $listCItem;
    }
    static function funcCItem($strCategory,$numWeightKg,$numLengthCm, $numWidthCm, $numHeightCm, $numQuantity){
        return new CItem($strCategory,$numWeightKg,$numLengthCm, $numWidthCm, $numHeightCm, $numQuantity);
    }

    static function funcUnloadingPrice($listCItem,$have_forklift){
        return new CUnloadingPrice($listCItem,$have_forklift);
    }
    static function funcUnloadingPriceBNE($listCItem,$have_forklift){
        return new CUnloadingPriceBNE($listCItem,$have_forklift);
    }
}

class CCourierSurcharge{
    protected $numLengthCm = null;
    protected $numWidthCm = null;
    protected $numHeightCm = null;
    protected $numWeightKg = null;

    public function getCourierName(){
        return 'please overwrite';
    }

    function funcSetItem($numLengthCm,$numWidthCm,$numHeightCm,$numWeightKg){
        $this->numLengthCm = $numLengthCm;
        $this->numWidthCm = $numWidthCm;
        $this->numHeightCm = $numHeightCm;
        $this->numWeightKg = $numWeightKg;
    }

    function funcOverSize(){
        throw new Exception('plaese over write');
    }
}

class CCourierSurchargeTLD extends CCourierSurcharge{
    protected $dicZone2MiniPrice = [
        1=>60,
        2=>60,
        3=>100,
        4=>135,
        999999999=>99999999,
    ];

    protected $numManualPriceMin = 45;
    protected $numManualPriceCBM = 20;

    function funcTailGate($numZone){
        $numPrice = 0;

        $numPriceMin = $this->dicZone2MiniPrice[$numZone];

        $numCBM =$this->numLengthCm*$this->numWidthCm*$this->numHeightCm/1000000;
        $numCBM250 = $this->numWeightKg/250;

        $numPriceCurrent = max( $numCBM,  $numCBM250)*15;

        $numPrice = max($numPriceCurrent,$numPriceMin);

        return $numPrice;
    }

    function funcManualUnloading(){
        $numCBMReal =$this->numLengthCm*$this->numWidthCm*$this->numHeightCm/1000000;
        $numCbm250 = $this->numWeightKg/250;
        $numCBM = max($numCBMReal,$numCbm250);
        $numPriceCurrent = $numCBM*$this->numManualPriceCBM;
        return max($numPriceCurrent,$this->numManualPriceMin);
    }

    function funcOverSize(){
        $listOversize = [ //cm , From<x<=To
			20 => [120, 200],
			60 => [200, 300],
            100 => [300, 500],
            160 => [500, 99999999999],
		];


        $numMax = max($this->numLengthCm,$this->numWidthCm,$this->numHeightCm);
        $numPrice = 0;
        foreach($listOversize as $numPriceCurrent => $listRange){
            if($numMax>$listRange[0] && $numMax<=$listRange[1]){
                $numPrice  = $numPriceCurrent;
            }
        }

        return  $numPrice;
    }


    public function funcPostcode2ZoneLevel($numChargeCode,$numPostCode){
        $dicZoneName2ZoneLevel = [
            'N1'=>1,
            'N2'=>2,
            'N3'=>3,
            'N4'=>4,
            'Q1'=>1,
            'Q2'=>2,
            'Q3'=>3,
            'V1'=>1,
            'V2'=>2,
            'V3'=>3,
        ];

        $numZoneLevel = null;
        $chargecodeInfo = ImportChargeCode::model()->find('chargecode = :cid', [':cid' => $numChargeCode]);
        if($chargecodeInfo != null){
            $objZoneMap = ZoneMap::model()->find('chargecode_id = :cid AND pc_lo <= :pc AND pc_hi >= :pc', [':cid' => $chargecodeInfo['id'], ':pc' => $numPostCode]);
            if($objZoneMap == null){
                return 999999999;
            }
            $strZone = $objZoneMap->z1;
            if(isset($dicZoneName2ZoneLevel[$strZone])){
                $numZoneLevel = $dicZoneName2ZoneLevel[$strZone];
            }
        }

        return $numZoneLevel;
    }
}

class CCourierSurchargeTLDBne extends CCourierSurchargeTLD{
    protected $dicZone2MiniPrice = [
        1=>150,
        2=>150,
        3=>260,
        4=>260,
        999999999=> 999999999,
    ];

    protected $numManualPriceMin = 125;
    protected $numManualPriceCBM = 30;
}


class CCourierSurchargeEizToll extends CCourierSurcharge{

    function funcOverSize(){
        $numMaxSurcharge = 0;

        $listlengthLimit = [//cm
			14=>[120,180],
			75=>[180,300],
			105=>[300,400],
			450=>[400,999999999],
		];
		$listDeadWeightLimit = [//KG
			14=>[30,35],
			75=>[35,85],
			105=>[85,9999],
		];
		$listCubicWeightLimit = [
			14=>[30,35],
			75=>[35,85],
			105=>[85,9999],
		];

        $numMax = max($this->numLengthCm, $this->numWidthCm, $this->numHeightCm);

        $numlengthSurcharge = 0;

        foreach ($listlengthLimit as $numPrice => $listRange) {
            $numFrom = $listRange[0];
            $numTo = $listRange[1];
            if ($numMax >= $numFrom && $numMax < $numTo) {
                $numlengthSurcharge = $numPrice;
            }
        }


        $numDeadWeightSurcharge = 0;
        $numDeadWeight =  $this->numWeightKg;
        foreach ($listDeadWeightLimit as $numPrice => $listRange) {
            $numFrom = $listRange[0];
            $numTo = $listRange[1];
            if ($numDeadWeight >= $numFrom && $numDeadWeight < $numTo) {
                $numDeadWeightSurcharge = $numPrice;
            }
        }

        $numCubicWeightSurcharge = 0;
        $numCubicWeight =  $this->numLengthCm * $this->numWidthCm * $this->numHeightCm * 0.00025; // cm m
        foreach ($listCubicWeightLimit as $numPrice => $listRange) {
            $numFrom = $listRange[0];
            $numTo = $listRange[1];
            if ($numCubicWeight >= $numFrom && $numCubicWeight < $numTo) {
                $numCubicWeightSurcharge = $numPrice;
            }
        }

        $numMaxSurcharge =  max($numlengthSurcharge, $numDeadWeightSurcharge, $numCubicWeightSurcharge);


        return  $numMaxSurcharge;
    }

}

class CChargeCodePrice{
    static $str_NA = 0;

    private $objImportChargeCode = null;

    function __construct($numChargeCode)
    {
        $this->objImportChargeCode =  ImportChargeCode::model()->find('chargecode = :cid', [':cid' => $numChargeCode]); 
    }

    public function funcIsCalculateCbm(){
        if(!isset($this->objImportChargeCode->mdata['calculate_cbm'])){
            return false;
        }
        return $this->objImportChargeCode->mdata['calculate_cbm'];
    }

    public function funcPrice ($numPostCode,$numWeightOrCbm){
        $strPrice =  self::$str_NA;
        $chargecodeInfo = $this->objImportChargeCode;
        if($chargecodeInfo != null){
            $objZoneMap = ZoneMap::model()->find('chargecode_id = :cid AND pc_lo <= :pc AND pc_hi >= :pc', [':cid' => $chargecodeInfo['id'], ':pc' => $numPostCode]);
            if($objZoneMap != null){
                $strZone = $objZoneMap->z1;
                $zoneRate = ZoneRate::model()->find('chargecode_id = :cid AND weight_lo <=:w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0', [':cid' => $chargecodeInfo['id'], ':w' => $numWeightOrCbm, ':z' => $strZone]);
                $strPriceRate = $zoneRate['base'] + $zoneRate['item'] + $zoneRate['perkg'] * $numWeightOrCbm;
                if($strPriceRate>0){
                    $strPrice = max($strPriceRate, $zoneRate->minimum);
                }
            }
        }
        return empty($strPrice)?0:$strPrice;
    }

    public function funGetRemoteCost($postcode,$suburb,$weight){
        $remoteCostRate = RemoteChargeRate::model()->find("chargecode_id = :rateId and (postcode =:postcode or CONCAT('0',postcode)=:postcode) and upper(suburb) = :suburb and (perkg+base)>0",[":rateId"=>$this->objImportChargeCode->id,":postcode"=>$postcode,":suburb"=>strtoupper($suburb)]);
        if(!empty($remoteCostRate)){
            $remotrSurcharge = $weight*$remoteCostRate->perkg+$remoteCostRate->base;
        }
        return empty($remotrSurcharge)?0:$remotrSurcharge;
    }
}



class CItem{
    private $strCategory = null;
    private $numWeightKg = null;
    private $numLengthCm = null;
    private $numWidthCm = null;
    private $numHeightCm = null;
    private $numQuantity = null;
    
    public function __construct($strCategory,$numWeightKg,$numLengthCm, $numWidthCm, $numHeightCm, $numQuantity)
    {
        $this->strCategory = $strCategory;
        $this->numWeightKg = $numWeightKg;
        $this->numLengthCm = $numLengthCm;
        $this->numWidthCm = $numWidthCm;
        $this->numHeightCm = $numHeightCm;
        $this->numQuantity = $numQuantity;
    }

    public function funcWeight(){
        $numWeightKg250 = $this->numLengthCm *$this->numWidthCm*$this->numHeightCm/1000000 * 250;
        return max($this->numWeightKg, $numWeightKg250 );
    }

    public function funcTotalWeight(){
        return $this->funcWeight()*$this->numQuantity;
    }

    public function funcTotalWeightReal(){
        return $this->numWeightKg*$this->numQuantity;
    }

    public function funcCBM(){
        $numCBMReal = $this->numLengthCm *$this->numWidthCm*$this->numHeightCm/1000000;
        $numCBM250 = $this->numWeightKg/250;
        return max($numCBMReal,$numCBM250);
    }

    public function funcTotalCBM(){
        return $this->funcCBM() *$this->numQuantity;
    }

    public function getNumLengthCm(){
        return  $this->numLengthCm;
    }

    public function getNumWidthCm(){
        return  $this->numWidthCm;
    }

    public function getNumHeightCm(){
        return  $this->numHeightCm;
    }

    public function getNumWeightKg(){
        return $this->numWeightKg;
    }
}

class CUnloadingPrice{
    protected $listUnloadingItem = [];
    protected $haveForklift = false;

    protected $numZone = 99999999;
    protected $dicZone2MiniPrice = [
        1=>60,
        2=>60,
        3=>100,
        4=>135,
        99999999=> 99999999,
    ];
    protected $numPricePerCbm = 30;

    const enum_umloading_level_1 = 1;
    const enum_umloading_level_2 = 2;
    const enum_umloading_level_3 = 3;
    const enum_umloading_level_error = -1;

    public function __construct($listUnloadingItem,$haveForklift)
    {
        $this->listUnloadingItem = $listUnloadingItem;
        $this->haveForklift = $haveForklift;
    }

    public function funcLevel(){
        $numLevel = self::enum_umloading_level_error;

        $numMaxWeightSingle = 0;
        $numMaxCm = 0;
        $numTotalWeightAll = 0;
        foreach($this->listUnloadingItem as $objItem){
            $numTotalWeightAll += $objItem->funcTotalWeightReal();
            if($numMaxWeightSingle <  $objItem->getNumWeightKg()){
                $numMaxWeightSingle = $objItem->getNumWeightKg();
            }
            if($numMaxCm <  $objItem->getNumLengthCm()){
                $numMaxCm = $objItem->getNumLengthCm();
            }
            if($numMaxCm <  $objItem->getNumWidthCm()){
                $numMaxCm = $objItem->getNumWidthCm();
            }
            if($numMaxCm <  $objItem->getNumHeightCm()){
                $numMaxCm = $objItem->getNumHeightCm();
            }
        }

        if($numMaxWeightSingle < 25 &&  $numMaxCm<100 && $numTotalWeightAll<250 ){
            $numLevel = self::enum_umloading_level_1;
        }
        else if($numMaxWeightSingle > 750 ||  $numMaxCm>200 || $numTotalWeightAll>8000){
            $numLevel = self::enum_umloading_level_error;
        }
        else{
            $numLevel = self::enum_umloading_level_2;
        }

        // if(($numMaxWeightSingle>=25&&$numMaxWeightSingle<250)||
        // ($numMaxCm>=100&&$numMaxCm<150)||
        // ($numTotalWeightAll>=250&&$numTotalWeightAll<750)){
        //     $numLevel = self::enum_umloading_level_2;
        // }

        // if(($numMaxWeightSingle>=25&&$numMaxWeightSingle<750)||
        // ($numMaxCm>=100&&$numMaxCm<200)||
        // ($numTotalWeightAll>=750&&$numTotalWeightAll<8000)){
        //     $numLevel = self::enum_umloading_level_3;
        // }

        return $numLevel;
    }

    public function funcPostcode2ZoneLevel($numChargeCode,$numPostCode){
        $dicZoneName2ZoneLevel = [
            'N1'=>1,
            'N2'=>2,
            'N3'=>3,
            'N4'=>4,
            'Q1'=>1,
            'Q2'=>2,
            'Q3'=>3,
            'V1'=>1,
            'V2'=>2,
            'V3'=>3,
        ];

        $numZoneLevel = null;
        $chargecodeInfo = ImportChargeCode::model()->find('chargecode = :cid', [':cid' => $numChargeCode]);
        if($chargecodeInfo != null){
            $objZoneMap = ZoneMap::model()->find('chargecode_id = :cid AND pc_lo <= :pc AND pc_hi >= :pc', [':cid' => $chargecodeInfo['id'], ':pc' => $numPostCode]);
            if($objZoneMap == null){
                return 999999999;
            }
            $strZone = $objZoneMap->z1;
            if(isset($dicZoneName2ZoneLevel[$strZone])){
                $numZoneLevel = $dicZoneName2ZoneLevel[$strZone];
            }
        }

        // return $numZoneLevel;
        $this->numZone = $numZoneLevel;
    }

    // public function funcPrice(){
    //     if($this->haveForklift){
    //         return 0;
    //     }

    //     $numLevel = $this->funcLevel();
    //     if($numLevel == self::enum_umloading_level_1){
    //         return 0;
    //     }
    //     if($numLevel == self::enum_umloading_level_2 || $numLevel == self::enum_umloading_level_3){
    //         $numTotalCBMAll = 0;
    //         foreach($this->listUnloadingItem as $objItem){
    //             $numTotalCBMAll += $objItem->funcTotalCBM();
    //         }
    //         $numPriceMin = 45;
    //         $price = 20*$numTotalCBMAll;
    //         return max($numPriceMin ,$price);
    //     }
    //     if($numLevel == self::enum_umloading_level_error){
    //         return 99999999;
    //     }
    // }

    public function funcPrice(){
        if($this->haveForklift){
            return 0;
        }

        $numLevel = $this->funcLevel();
        if($numLevel == self::enum_umloading_level_1){
            return 0;
        }
        if($numLevel == self::enum_umloading_level_2 || $numLevel == self::enum_umloading_level_3){
            $numTotalCBMAll = 0;
            foreach($this->listUnloadingItem as $objItem){
                $numTotalCBMAll += $objItem->funcTotalCBM();
            }

            $numPriceMin = $this->funcPriceMin();
            $price =  $this->funcPricePerCBM($numTotalCBMAll);

            return max($numPriceMin ,$price);
        }
        if($numLevel == self::enum_umloading_level_error){
            return 99999999;
        }
    }

    public function funcPricePerCBM($numTotalCBMAll){
        return $this->numPricePerCbm*$numTotalCBMAll;
    }

    public function funcPriceMin(){
        return $this->dicZone2MiniPrice[$this->numZone];;
    }
}


class CUnloadingPriceBNE extends CUnloadingPrice {
    protected $dicZone2MiniPrice = [
        1=>150,
        2=>150,
        3=>260,
        4=>260,
        99999999=> 99999999,
    ];
    protected $numPricePerCbm = 30;

}