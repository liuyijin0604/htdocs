<?php
class Factory4pxOpenPlatform{
    public static function funcSandbox(){
        $strAppKey = 'af681e45-d03d-4ebf-95ed-bb60d36fc714';
        $strAppSecret = 'b764495e-ab7e-4b6b-9cf4-fc82c6bd4f51';
        $strUrl = 'http://open.sandbox.4px.com/router/api/service';
        
        return new C4pxOpenPlatform($strAppKey,$strAppSecret,$strUrl);
    }

    public static function funcCreate(){
        $strAppKey = '9c31c9d8-56b7-45c8-83a3-e43a9f273c59';
        $strAppSecret = '386a04b9-72dc-42c5-a248-9011696d0316';
        $strUrl = 'http://open.eu.4px.com/router/api/service';
        
        return new C4pxOpenPlatform($strAppKey,$strAppSecret,$strUrl);
    }
}

class C4pxOpenPlatform{
    private $strAppKey = null;
    private $strAppSecret = null;
    private $strUrl = null;

    private $isSuccess = null;
    private $strRespond = null;

    public function __construct($strAppKey , $strAppSecret,$strUrl){
        $this->strAppKey = $strAppKey;
        $this->strAppSecret = $strAppSecret;
        $this->strUrl = $strUrl;
    }

    public function getIsSuccess(){
        return $this->isSuccess == true;
    }

    public function getStrRespond(){
        return $this->strRespond;
    }


    private function funcRequest($strUrl,$strBody){
        $opts = ['http' => [
			'method' => 'POST',
			'header'=>[
                "Content-type: application/json",
                'Content-Length: ' . strlen($strBody),
            ],
			'content' => $strBody]
        ];
        $context = stream_context_create($opts);

        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context)
        {
            $this->isSuccess = false;
            $this->strRespond = $err_msg;
        }, E_WARNING);

        $strResponse = file_get_contents($strUrl, false, $context);
        if($strResponse !== false){
            $this->strRespond = $strResponse;
            $objResponse = json_decode($strResponse);
            $this->isSuccess = $objResponse->result;
        }
    }

    private function funcSign($listHeader,$strBody){
        $str = '';
        ksort($listHeader);
        foreach($listHeader as $k=>$v){
            if($k == 'sign'){
                continue;
            }
            if($k == 'app_secret' ){
                continue;
            }
			$str .= $k.$v;
        }

        $str.= $strBody;
        $str.= $this->strAppSecret;

        return MD5($str);
    }

    public function funcTrOrderTrackingGet($strDeliveryOrderNo){
        $objBody = new stdClass;
        $objBody->deliveryOrderNo =  $strDeliveryOrderNo;

        $strBody = json_encode($objBody);

        $listQuery = [
            'app_key'=> $this->strAppKey,
            'format'=>'json',
            'method'=> 'tr.order.tracking.get',
            'timestamp'=>time(),
            'v'=>'1.0',
        ];

        $strSign = $this->funcSign($listQuery,$strBody);
        $listQuery['sign'] = $strSign;

        $strUrl = $this->strUrl.'?';

        $i=1;
        foreach($listQuery as $k=>$v){
            $strUrl.= $k.'='.$v;
            if($i != sizeof($listQuery)){
                $strUrl.='&';
            }
            $i++;
        }

        $this->funcRequest($strUrl,$strBody);

    }

    public function funcComTrackTrackCreateByTrackNo($strFpxTrackNo,$strOccurDate,$strCode,$strAreaDescription,$strCreatePerson){
        $objBody = new stdClass;
        $objBody->fpxTrackNo = $strFpxTrackNo;
        $objBody->trk_occurdate =  $strOccurDate;//date('Y-m-d H:i:s');
        $objBody->tk_code =  $strCode;
        $objBody->trk_areadescription =  $strAreaDescription;
        $objBody->trk_createperson = $strCreatePerson;

        $strBody = json_encode($objBody);

        $listQuery = [
            'app_key'=> $this->strAppKey,
            'format'=>'json',
            'method'=> 'com.track.track.createByTrackNo',
            'timestamp'=>time(),
            'v'=>'1.0',
        ];

        $strSign = $this->funcSign($listQuery,$strBody);
        $listQuery['sign'] = $strSign;

        $strUrl = $this->strUrl.'?';

        $i=1;
        foreach($listQuery as $k=>$v){
            $strUrl.= $k.'='.$v;
            if($i != sizeof($listQuery)){
                $strUrl.='&';
            }
            $i++;
        }

        $this->funcRequest($strUrl,$strBody);
    }

    public function funcHandover2Courier($strFpxTrackNo,$strOccurDate){
        $this->funcComTrackTrackCreateByTrackNo($strFpxTrackNo,$strOccurDate,'FPX_D_STPP','澳洲仓','TLA');
    }

    public function funcClearance($strFpxTrackNo,$strOccurDate){
        $this->funcComTrackTrackCreateByTrackNo($strFpxTrackNo,$strOccurDate,'FPX_I_CPC','澳洲仓','TLA');
    }

    public function funcDeconsolidation ($strFpxTrackNo,$strOccurDate){
        $this->funcComTrackTrackCreateByTrackNo($strFpxTrackNo,$strOccurDate,'FPX_D_AAD','澳洲仓','TLA');
    }

    public function funcHeld($strFpxTrackNo,$strOccurDate){ 
        $this->funcComTrackTrackCreateByTrackNo($strFpxTrackNo,$strOccurDate,'FPX_I_HC','澳洲仓','TLA');
    }

    
}


