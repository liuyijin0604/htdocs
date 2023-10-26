<?php 
class ZwStorageService extends Service
{
	public function getZWStorage($importZwStorage)
	{
		if(!empty($importZwStorage->putcode)) return $importZwStorage->putcode;
		$api = new HvlvJavaAPI($importZwStorage->store_code);
		$importZwStorage->refresh();
		$data = [];
		$data["tlano"] = $importZwStorage->tlano;
		$data["hubInCode"] = $importZwStorage->channel_code;
		$data["pkg"] = $importZwStorage->goods_count;
        $data["weight"] = $importZwStorage->weight;
        $data["remark"] = $importZwStorage->remark;
        
        $data["consignee_name"]= "TLA";
        $data["consignee_tel"]= Org::IM_COMPANY_PHONE;
        $data["consignee_address"]= $api->fromInfo['address'];
        $data["consignee_country"]= "AU";
        $data["consignee_state"] = $api->fromInfo['state'];
        $data["consignee_suburb"] = $api->fromInfo['suburb'];
        $data["consignee_postcode"] = $api->fromInfo['postcode'];

        $items = [];
        foreach ($importZwStorage->eitems["g"] as $key => $value) {
        	$item = [];
        	$item["g_zh"] = isset($importZwStorage->eitems["g_zh"])?$importZwStorage->eitems["g_zh"][$key]:"";
        	$item["g"] = isset($importZwStorage->eitems["g"])?$importZwStorage->eitems["g"][$key]:"";
        	$item["q"] = isset($importZwStorage->eitems["q"])?$importZwStorage->eitems["q"][$key]:"";
        	$item["v"] = isset($importZwStorage->eitems["v"])?$importZwStorage->eitems["v"][$key]:"";
        	$item["hs"] = isset($importZwStorage->eitems["hs"])?$importZwStorage->eitems["hs"][$key]:"";
        	$item["sku"] = isset($importZwStorage->eitems["sku"])?$importZwStorage->eitems["sku"][$key]:"";
        	$items[] = $item;
        }
        $data["items"] = $items;
        $result = $api->orderAochenStorage($data);
        if($result['code']=="60000")
        {
        	if(!empty($result['data']))
        	{
        		$resultData = json_decode($result['data'],true);
        		$resultData = $resultData['body'];
        		$importZwStorage->mdata['aochenData'] = $resultData;
        		if(!empty($resultData['oddNo']))
        		{
        			$importZwStorage->putcode = $resultData['oddNo'];
        			$importZwStorage->status = ImportZwStorage::STATE_PUSHED;
        		}
        		$importZwStorage->update(["status","putcode",'meta']);
        	}
        }
        return $importZwStorage->putcode;
	}

    public function checkZwStorageRealData($importZwStorage)
    {
        if(!empty($importZwStorage->mdata['aochenRealData'])) return $importZwStorage;
        $api = new HvlvJavaAPI($importZwStorage->store_code);
        $data = [];
        $data = ["putcode"=>$importZwStorage->putcode];
        $result = $api->checkAochenOrder($data);
        if(!empty($result['code'])&&$result['code']=="60000")
        {
            if(!empty($result['data']))
            {
                $resultData = json_decode($result['data'],true);
                if(!empty($resultData['body']))
                {
                    $resultData = $resultData['body'];
                    $importZwStorage->mdata['aochenRealData'] = $resultData;
                    $importZwStorage->status = ImportZwStorage::STATE_RECEIVED; 
                    $importZwStorage->update(['status','meta']);
                }
            }
        }
        return $importZwStorage;
    }
	
}
?>