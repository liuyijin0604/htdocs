<?php 
class PriceEnquiryService extends Service
{

	public static function checkPENumberCanUse($p,$courier)
	{
		$o = new stdClass();
		$o->code = 0;
		$o->msg = "";
		if(empty($p->mdata['pe_number']))
		{
			$o->code = 0;
			$o->msg = "require pe number";
			return $o;
		}else
		{
			if($courier->org_id!=Org::ORGID_COURIER_TLA&&$courier->org_id!=Org::ORGID_COURIER_PICKUP&&$courier->org_id!=Org::ORGID_COURIER_TOLL_IPEC)
			{
				$o->code =0;
				$o->msg = "Only Top Logistics Delivery(TLD) Service and Toll can use PE number.";
				return $o;
			}
			$pe = PriceEnquiry::model()->find("code = :code and org_id = :orgId and status = :status",[':code'=>$p->mdata['pe_number'],':status'=>PriceEnquiry::status_active,':orgId'=>$p->agent_id]);
			if(empty($pe))
			{
				$o->code =0;
				$o->msg = "PE number can not be used.";
				return $o;
			}
			if(trim($p->cnee->address)==trim($pe->address)&&abs($p->weight-$pe->weight)<1)
			{
				$objItems = $pe->mdata['listItem'];
				$packs = self::itemsToPacks($objItems);
				$checkPacks = $p->packs;
				foreach ($packs as $key => $pack) {
					foreach ($checkPacks as $key2 => $checkPack) {
						if(abs($pack['width']-$checkPack['width'])<1&&abs($pack['height']-$checkPack['height'])<1&&abs($pack['length']-$checkPack['length'])<1&&abs($pack['weight']-$checkPack['weight'])<1)
						{
							unset($packs[$key]);
							break;
						}
					}
				}
				if(!empty($packs))
				{
					$o->code = 0;
					$o->msg = "The info of PE number can not match this parcel";
					return $o;
				}
				$o->code=1;
			}else
			{
				$o->code = 0;
				$o->msg = "The info of PE number can not match this parcel";
			}
		}
		return $o;
	}

	public static function itemsToPacks($objItems)
	{
		$packs = [];
		foreach($objItems as $key=>$objItem)
		{
			$thisPack =[];
			for ($i=0; $i < $objItem['quantity']; $i++) 
			{ 
				$thisPack['length'] = $objItem['length'];
				$thisPack['width'] = $objItem['width'];
				$thisPack['height'] = $objItem['height'];
				$thisPack['weight'] =$objItem['weight'];
				$packs[] = $thisPack;
			}
		}
		return $packs;
	}

	public static function getPEPriceFromPE($peNumber)
	{
		$objPriceEnquiry = PriceEnquiry::model()->find('code = :code and status = :status',[":code"=>$peNumber,":status"=>PriceEnquiry::status_active]);
		if(!empty($objPriceEnquiry->price))
		{
			return [$objPriceEnquiry->price,0,0,0,0];
		}
		return [$objPriceEnquiry->mdata['base_TLD'],$objPriceEnquiry->mdata['oversize_TLD']+$objPriceEnquiry->mdata['manual_TLD']+$objPriceEnquiry->mdata['tailgate_TLD'],$objPriceEnquiry->mdata['oversize_TLD'],$objPriceEnquiry->mdata['manual_TLD'],$objPriceEnquiry->mdata['tailgate_TLD']];
	}
}
?>