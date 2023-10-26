<?php
class D2zAPI
{
    const TEST_TOKEN='test5AdbzO5OEeOpvgAVXUFE0A';
    const TEST_KEY='79db9e5OEeOpvgAVXUFWSD';
    const TEST_URL='http://qa.etowertech.com';
    const PRODUCTION_KEY="Sb_vjQKjroDqR4SU3pFdIA";
    const PRODUCTION_TOKEN="pclEn1hnY7ejLxJRylmiLS";
    const PRODUCTION_URL="https://au.etowertech.com";
    public $result;
    public $err;
    public $is_test;
    public $debug;
    public $couier_id;
     
    public static $melcodes=["V0","V1","GL","BR","V2","V3","T0","T1","S0"];
    public static $bricodes=["Q0","Q1","GC","SC","IP","Q2","Q3","Q4","Q5"];
    const  SYD_FACILITY="SYD2";
    const  MEL_FACILITY="MEL3";
    const  BRI_FACILITY="BNE";
    public function __construct($debug = false, $test = false, $courier_id='')
    {
        $this->is_test = $test;
        $this->debug = $debug;
        $this->couier_id=$courier_id;
    }
        
        
    public function findTheFacility($postcode)
    {
        $zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>1426 ,':zoneid' => 1,':code' => $postcode]);
        $chargeCode = 'N1';
        if ($this->couier_id==243) {
            return self::SYD_FACILITY;
        }
        if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
            $chargeCode = $zoneMap['z1'];
        }
        if (in_array($chargeCode, self::$melcodes)) {
            return self::MEL_FACILITY;
        } elseif (in_array($chargeCode, self::$bricodes)) {
            return self::BRI_FACILITY;
        }
        return self::SYD_FACILITY;
    }
     
     
    private function build_headers($method, $path, $acceptType='application/json')
    {
        $walltech_date=date(DATE_RSS);
        $auth = $method."\n".$walltech_date."\n".$path;
        $hash=base64_encode(hash_hmac('sha1', $auth, $this->is_test?self::TEST_KEY:self::PRODUCTION_KEY, true));
        //  echo $walltech_date."<br>".$auth."<br>".$hash."<br>";
        return [ 'Content-Type: application/json',
            'Accept: '.$acceptType,
            'X-WallTech-Date: '.$walltech_date,
            'Authorization: WallTech '.($this->is_test?self::TEST_TOKEN:self::PRODUCTION_TOKEN).':'.$hash
        ];
    }
    
    public function getBreakWeight($weight)
    {
        if ($weight <= 0.500) {
            return $weight;
        } elseif ($weight <= 1.0) {
            return $this->doTheWeight($weight, 0.5, 1.0, 0.4, 0.5);
        } elseif ($weight <= 2) {
            return $this->doTheWeight($weight, 1, 2, 0.8, 1.0);
        } elseif ($weight <= 3) {
            return $this->doTheWeight($weight, 2, 3, 1.8, 2.0);
        } elseif ($weight <= 4) {
            return $this->doTheWeight($weight, 3, 4, 2.5, 3.0);
        } elseif ($weight <= 5) {
            return $this->doTheWeight($weight, 4, 5, 3.5, 4.0);
        } elseif ($weight <= 7) {
            return $this->doTheWeight($weight, 5, 7, 4.5, 5.0);
        } elseif ($weight <= 10) {
            return round($weight * rand(80, 100) / 100, 3);
        } else {
            return round($weight, 3);
        }
    }
    public function doTheWeight($w, $x1, $x2, $y1, $y2)
    {
        return round(($y1-abs($y1-$y2)/abs($x1-$x2)*$x1)+abs($y1-$y2)/abs($x1-$x2)*$w, 3);
    }
     
    /**
     * create shipment Order;
     * @param  $ss is an Array of shipment
     */
    public function createShipment($ss)
    {
        $data=[];
        foreach ($ss as $shipment) {
            $data[]= $this->prepareData($shipment);
        }
        $result=$this->request('/services/integration/shipper/orders', 'POST', $data);//pdflabel
        return $result;
    }
     
    public function manifest($ss)
    {
        $data=[];
        foreach ($ss as $s) {
            $data[]=$s->mdata['d2z_shipment_orderid'];
        }
        $result=$this->request('/services/integration/shipper/manifests', 'POST', $data);//pdflabel
        return $result;
    }
     
    public function getLabelInfo($ss)
    {
        $data=[];
        foreach ($ss as $s) {
            $data[]=$s->hbn;
        }
        $result=$this->request('/services/integration/shipper/labelSpecs', 'POST', $data);//pdflabel
    }
    
    public function getD2zCost($shipment)
    {
        $price = 0;
        $zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>1426 ,':zoneid' => 1,':code' => $shipment->postcode]);
        $chargeCode = 'N1';
        if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
            $chargeCode = $zoneMap['z1'];
        }
        $weight=$shipment->weight;
        $zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $shipment->weight, ':rateid' => 137]);
        if (!empty($zrs)) {
            // get maximum one
            foreach ($zrs as $zr) {
                $temp = $zr['base'] + $zr['item'];
                if ($zr['nkg'] > 0) {
                    $wl = $weight- ($zr['base'] > 0 ? $zr['nkg'] : 0);
                    $temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
                } else {
                    $temp +=  $weight * $zr['perkg'];
                }
                if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
                    $temp = $zr['minimum'];
                }
                $price = max($price, $temp);
            }
        }
        return $price;
    }
    public function getD2zCountryCost($shipment)
    {
        $price = 0;
        $zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>1426 ,':zoneid' => 2,':code' => $shipment->postcode]);
        $chargeCode = 'N1';
        if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
            $chargeCode = $zoneMap['z1'];
        }
        $weight=$shipment->weight;
        $zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $shipment->weight, ':rateid' => ImportChargeCode::D2Z_COUNTRY_ID]);
        if (!empty($zrs)) {
            // get maximum one
            foreach ($zrs as $zr) {
                $temp = $zr['base'] + $zr['item'];
                if ($zr['nkg'] > 0) {
                    $wl = $weight- ($zr['base'] > 0 ? $zr['nkg'] : 0);
                    $temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
                } else {
                    $temp +=  $weight * $zr['perkg'];
                }
                if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
                    $temp = $zr['minimum'];
                }
                $price = max($price, $temp);
            }
        }
        return $price;
    }


    public function prepareData($shipment)
    {
        $predata=new stdClass();
        $predata->referenceNo=$shipment->hbn;
        $predata->recipientName=substr($shipment->cnee->name, 0, 35);
        $predata->recipientCompany=substr($shipment->cnee->company, 0, 49);
        $predata->email= substr($shipment->cnee->email, 0, 50);
        $predata->phone=substr($shipment->cnee->tel, 0, 20);
        $predata->addressLine1= substr($shipment->cnee->address, 0, 40);
        if (!empty(substr($shipment->cnee->address, 40, 100))) {
            $predata->addressLine2= substr($shipment->cnee->address, 40, 100);
        }
        $predata->city=$shipment->cnee->suburb;
        $predata->state=$shipment->cnee->state;
        $predata->postcode=$shipment->cnee->postcode;
        $predata->country='AU';
        $predata->weight= $this->getBreakWeight($shipment->weight);
        $shipment->mdata['manifest_weight']=$predata->weight;
        $shipment->updateMeta();
        //       $predata->volume=$shipment->cbm*($shipment->pkg);
        $predata->invoiceValue=$shipment->dvalue;
        $predata->invoiceCurrency='USD';
        $predata->description=substr($shipment->getGoods(), 0, 50);
        $predata->serviceCode="UBI.AU2AU.AUPOST";
        $predata->facility= $this->findTheFacility($shipment->postcode);
        $predata->shipperName=$shipment->cnor->name;
        $predata->shipperAddressLine1=substr($shipment->cnor->address, 0, 80);
        $predata->shipperCity=substr($shipment->cnor->city, 0, 80);
        $predata->shipperState=substr($shipment->cnor->state, 0, 80);
        $predata->shipperPostcode=substr($shipment->cnor->postcode, 0, 80);
        $predata->shipperCountry=$shipment->cnor->country;
        $predata->shipperPhone=substr($shipment->cnor->tel, 0, 20);
        return $predata;
    }
    public function request($action, $method='POST', $data=null)
    {
        $this->result=false;
        
        $url= ($this->is_test?self::TEST_URL:self::PRODUCTION_URL).$action;
        $c=new curl($url);
        $c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
        $c->setopt(CURLOPT_RETURNTRANSFER, true);
        $c->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $c->setopt(CURLOPT_TIMEOUT, 600);
        $hdr = $this->build_headers($method, $url);
        if (!empty($data)) {
            $data = AppHelper::safeJsonEncode($data);
            $c->setopt(CURLOPT_POSTFIELDS, $data);
        }
        $c->setopt(CURLOPT_HTTPHEADER, $hdr);
         
        if (!$c->exec()) {
            throw new Exception('cUrl Error: '.$c->err);
        }
        $this->rescode=$c->rcode;
        $this->result = json_decode($c->result);
        $this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".urldecode($data)."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result.PHP_EOL.PHP_EOL);
        return $this->result;
    }
     
    protected function log2file($m, $file='ELabel')
    {
        $lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'd2z' . DIRECTORY_SEPARATOR;
        $lf .= 'd2z_api_'.$file. date('Y-m-d') . '.log';
        return file_put_contents($lf, $m, FILE_APPEND);
    }
}
