<?php
	class CK1API
	{
		const TEST_CLIENTID = 'MzAyMTc4NTctMTgwMi00NDE2LWEzNTQtYjljMTkyOTBiY2Q1';
		const TEST_CLIENTSECRET = 'ZjkwZTcyZGYtNTZlNC00YWYwLWE1MDEtNTE1NjAwYzNjMWUzLThhMjYwMTk4LTM2YjgtNGI1NS1iZGViLTJhOTFmNzEwMDg4YQ==';
        const PRODUCTION_CLIENTID = 'YjFlNDRjOTAtNmY4NS00YmFiLThmZDAtZWIxNDdjOWY4ODA1';
        const PRODUCTION_CLIENTSECRET = 'ZTk0MGFjNjctYzhiYi00NDVlLTliNDUtMWM1OWExNjAyZWU2LTc4NjA0ZTUwLTIxOTUtNDZlZS05NjU3LWQwYTE0YmIyYjM3OA==';
		const redirectUri = 'https://os.toplogistics.com.au';
		const refreshToken = 'Y2VhYmQ5YmMtODI1MS00Y2QyLWE0MjgtMmY4Mjg4ZWNlY2Iz';
        const PRODUCTION_refreshToken = 'ODMwYmZlMTMtN2VjMi00M2JlLTk5MmYtYzcyZGU5YTVlOWI4';
		public $is_test = false;
		public $result = false;
		public $token = "";
		const TEST_URL = 'https://openapi.ck1info.com';
		const PRODUCTION_URL = 'https://openapi.chukou1.cn';
		public function __construct()
		{
            if($this->is_test==true)
            {
                $this->token = "MjU3Zjc4ZGMtY2JhMy00OWE5LWI3YzEtNWY2MDkzYjY2YmM2";
            }else
            {
                $this->token = "MjU3Zjc4ZGMtY2JhMy00OWE5LWI3YzEtNWY2MDkzYjY2YmM2";
            }
		}

		public function prepareData($shipment)
		{
			$data = new stdClass();
			$data->Location = 'AU';
			$data->Package = [];
			$data->Remark = "remark";
			$data->SubmitLater = false;

			$package = new stdClass();
			$package->PackageId = $shipment->hbn;
			$package->ServiceCode = "CSA";
			$ShipToAddress = new stdClass();
			$ShipToAddress->Country = $shipment->cnee->country;
			$ShipToAddress->Province = $shipment->cnee->state;
			$ShipToAddress->City = $shipment->cnee->suburb;
			$ShipToAddress->Street1 = $shipment->cnee->address;
			$ShipToAddress->Postcode = $shipment->cnee->postcode;
			$ShipToAddress->Contact = $shipment->cnee->name;
			$ShipToAddress->Phone = $shipment->cnee->tel;
			$ShipToAddress->Email = $shipment->cnee->email;
			$package->ShipToAddress = $ShipToAddress;
			$skus = [];
			$package->weight = ceil($shipment->weight*100);
			if(!empty($shipment->mdata['dim'])&&!empty($shipment->mdata['dim']['w']))
			{
				$package->Width = $shipment->mdata['dim']['w'];
				$package->Height = $shipment->mdata['dim']['h'];
				$package->Length = $shipment->mdata['dim']['d'];
			}
			$itemsNum = count($shipment->eitems['g']);
			foreach ($shipment->eitems['g'] as $i => $itemg) {
				$sku = new stdClass();
				if(!empty($shipment->eitems['sku']))
				{
					$sku->Sku = $shipment->eitems['sku'][$i];
				}
				if(!empty($shipment->eitems['g_zh']))
				{
					  $sku->DeclareNameCn=$shipment->eitems['g_zh'][$i];
				}
				$sku->Quantity= $shipment->eitems['q'][$i];
				$sku->Weight= ceil((($package->weight/$itemsNum)/$sku->Quantity));
		        $sku->DeclareValue=$shipment->eitems['v'][$i];
		        $sku->DeclareNameEn=$shipment->eitems['g'][$i];
		        $sku->ProductName=$shipment->eitems['g'][$i];
		        $sku->Price=$shipment->eitems['v'][$i];
		        $skus[] = $sku;
			}
			$package->Skus = $skus;
			$data->Package = $package;
			return $data;
		}

    /**
     * create shipment Order;
     * @param  $ss is an Array of shipment
     */
    public function createShipment($shipment)
    {
        $finalResult = [];
        $data=$this->prepareData($shipment);
        $result=$this->request('/v1/directExpressOrders?sync=1', 'POST', $data);//pdflabel
        // if(in_array($result['rescode'],[200,201]))
        // {
        //     $isCreated = 'Creating';
        //     $count = 1;
        //     while($isCreated=='Creating'||$count>6)
        //     {
        //         $result = $this->getShipmentStatus($shipment->hbn);
        if(empty($result['msg']->Status))
        {
            $finalResult['rescode'] = 400;
            return $finalResult;
        }
        
         $isCreated = $result['msg']->Status;
         if(!empty($result['msg']->CreateFailedReason))
         {
             $finalResult['rescode'] = 400;
             $finalResult['msg'] = json_encode([$result['msg']->CreateFailedReason]);
         }else
         {
             $finalResult['rescode'] = 200;
             $finalResult['ref'] = @$result['msg']->TrackingNumber;
             $finalResult['Ck1PackageId'] = $result['msg']->Ck1PackageId;
         }
        //         sleep(5);
        //         $count++;
        //     }
        // }else
        // {
        //  $finalResult['rescode'] = 400;
        //  $finalResult['msg'] = json_encode([$result['msg']]);
        // }
        return $finalResult;
    }

    /**
     * create shipment Order;
     * @param  $ss is an Array of shipment
     */
    public function updateShipment($shipment)
    {
        $finalResult = [];
        $data=$this->prepareData($shipment);
        $result=$this->request('/v1/directExpressOrders', 'POST', $data);//pdflabel
        if(in_array($result['rescode'],[200,201]))
        {
            $finalResult['rescode'] = 200;
            $finalResult['msg'] = json_encode([$result['msg']]);
        }else
        {
            $finalResult['rescode'] = 400;
            $finalResult['msg'] = json_encode([$result['msg']]);
        }
        return $finalResult;
    }

    public function getShipmentStatus($hbn)
    {
        $data = new stdClass();
        $data->PackageId = $hbn;
        $result=$this->request("/v1/directExpressOrders/{$hbn}/status", 'GET', $data);//pdflabel
        return $result;
    }

    private function getFileName()
    {
        return 'TLA_MANIFEST_'.date('YmdHis').'.xls';
    }

    public function manifest($ss)
    {
        try 
        {
            $i = 1;
            $xls = new oExcel;
            $xls->addRow($i++, ['PackageId', 'TrackingNumber', 'Weight']);
            foreach ($ss as $p) {
                // $this->updateShipment($p);
                $xls->addRow($i++, [$p->hbn, $p->ref,$p->weight]);
            }

            $filename = $this->getFileName();
            $path = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'chukou1' . DIRECTORY_SEPARATOR;
            $fullFilename = $path.$filename;
            $xls->output($fullFilename, null, false);
            $result = $this->sendManifestEmail($fullFilename,$filename);
            $result['filename'] = $filename;
            return $result;
        } catch (Exception $ex) {
            throw $ex;
            return array("status"=>false);
        }
    }

    public function sendManifestEmail($fullFilename,$filename)
    {
        $email = 'yuru.chen@chukou1.com';
        $emailLog = new Emailog();
        $emailLog->to_id = Org::ORGID_COURIER_CHUKOU1;
        $emailLog->fid = 0;
        $emailLog->type= Emailog::DFE_API_TEMPLATE;
        $emailLog->dt = date('Y-m-d H:i:s');
        $emailLog->status = 10;
        $emailLog->prepTemplate();
        $emailLog->tpl->assignSubject('TITLE', $filename);
        // $emailLog->tpl->assignThese([
        //  'FILENAME'=>$filename,
        // ]);
        $emailLog->subject = $emailLog->tpl->subject;
        $emailLog->body = $emailLog->tpl->getContent();
        $emailLog->mdata['to']=$email;
        $emailLog->mdata['fromName']='TLA Imports';
        $emailLog->mdata['from']='imports@toplogistics.com.au';
        $emailLog->mdata['cc']='imports@toplogistics.com.au;chan-maint@chukou1.com;mingjia.chen@chukou1.com;jingwen.liang@chukou1.com';
        $o=$emailLog->sendEmail([[$fullFilename,$filename]]);

        return $o;
    }
     
    public function getLabel($ss,$out = 1)
    {   
        $data = new stdClass();
        $PackageIds = [];
        foreach ($ss as $key => $s) {
            $PackageIds[] = $s->hbn;
        }
        $data->PackageIds = $PackageIds;
        $data->PrintFormat = "ClassicLabel";
        $data->PrintContent = "Address";
        $data->IdType = "PackageId";
        $result=$this->request('/v1/directExpressOrders/label', 'POST', $data);//pdflabel
        $content = $result['msg']->Label;
        $pdf = base64_decode($content);
        if($out==1)
        {
            oPDF::output($pdf, 1, 'label.pdf');
        }else
        {
            return oPDF::output($pdf, 2,'');
        }
    }
    
    public function getCost($orgRateId,$shipment)
    {
        $orgRate = OrgRate::model()->findByPk($orgRateId);
        $price = 0;
        $zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$orgRate->org_id ,':zoneid' => $orgRate->zone_id,':code' => $shipment->postcode]);
        $chargeCode = 'N1';
        if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
            $chargeCode = $zoneMap['z1'];
        }
        $weight=$shipment->weight;
        $zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $shipment->weight, ':rateid' => $orgRateId]);
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
    
    public function getTrackingEvents($ref)
    {
        $finalResult = [];
        $result=$this->request("/v1/trackings/${ref}?lang=en", 'GET', null);//pdflabel
        return  $result;
    }


    public function request($action, $method='POST', $data=null)
    {
        $this->result=[];
        
        $url= ($this->is_test?self::TEST_URL:self::PRODUCTION_URL).$action;
        $c=new curl($url);
        $c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
        $c->setopt(CURLOPT_RETURNTRANSFER, true);
        $c->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $c->setopt(CURLOPT_TIMEOUT, 600);
        if(empty($data))
        {
          $hdr = $this->build_headers($method, $url,'application/json',null);
        }else
        {
          $hdr = $this->build_headers($method, $url,'application/json',AppHelper::safeJsonEncode($data)); 
        }
        if (!empty($data)) {
            $data = AppHelper::safeJsonEncode($data);
            $c->setopt(CURLOPT_POSTFIELDS, $data);
        }
        $c->setopt(CURLOPT_HTTPHEADER, $hdr);
        if (!$c->exec()) {
            throw new Exception('cUrl Error: '.$c->err);
        }
        $this->rescode=$c->rcode;
        $this->result['rescode'] = json_decode($this->rescode);
        $this->result['msg'] = json_decode($c->result);
        $this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".urldecode($data)."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result.PHP_EOL.PHP_EOL);
        return $this->result;
    }

    private function build_headers($method, $path, $acceptType='application/json',$data)
    {
        if(empty($data))
        {
             return [
            'Authorization:Bearer '.$this->token,
            'Content-Type:application/json; charset=utf-8',
            ];
        }
        //  echo $walltech_date."<br>".$auth."<br>".$hash."<br>";
        return [
            'Content-Length:'.strlen($data),
            'Authorization:Bearer '.$this->token,
            'Content-Type:application/json; charset=utf-8',
        ];
    }
     
    protected function log2file($m, $file='ELabel')
    {
        $lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'chukou1' . DIRECTORY_SEPARATOR;
        $lf .= 'chukou1_api_'.$file. date('Y-m-d') . '.log';
        return file_put_contents($lf, $m, FILE_APPEND);
    }
    }

?>