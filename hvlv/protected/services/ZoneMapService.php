<?php 
class ZoneMapService extends Service
{
	public function transformingNewFastwayTemplate($data)
	{
		unset($data[1]);
		$result =['SYD'=>[],'NON-SYD'=>[]];
		foreach ($data as $key => $d) {
			if(strtoupper(trim($d[7]))=="METRO")
			{
				$result['SYD'][]=$d[3];
			}else
			{
				$result['NON-SYD'][]=$d[3];
			}
		}
		$result['SYD'] = array_unique($result['SYD']);
		$result['NON-SYD'] = array_unique($result['NON-SYD']);
		$xsl=new oExcel();
		$i=1;
		$xsl->addRow($i++, ['Code','Name','postcode list']);
		$cSYD = $this->combinePostcode(join(',',$result['SYD']));
		$nSYD = $this->combinePostcode(join(',',$result['NON-SYD']));
		$result['SYD'] = $cSYD;
		$result['NON-SYD'] = $nSYD;
		foreach ($result as $key => $value) {
			$xsl->addRow($i++, [$key,$key,$value]);
		}
		$tempfile = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'transformed_zoneMap.xlsx';
		$xsl->output($tempfile, null, false);
		return true;
	}

	public function combinePostcode($postcodeStr)
	{
		$postcodeArr = explode(',', $postcodeStr);
		foreach ($postcodeArr as $key => &$value) {
			$value = str_pad($value,4,0,STR_PAD_LEFT);
		}
		sort($postcodeArr);
		$result = [];
		$size = count($postcodeArr);
		$thisResult =[];
		if($size==1)
		{
			$result = $postcodeArr;
		}
		for ($key=0; $key <= ($size-2); $key++) { 

			if(($postcodeArr[$key]+1)!=$postcodeArr[$key+1])
			{
				$thisResult[] =  $postcodeArr[$key];
				if(count($thisResult)==1)
				{
					$result[] = $thisResult[0];
				}else
				{
					$result[] = $thisResult[0].'-'.$thisResult[count($thisResult)-1];
				}
				$thisResult =[];
			}else
			{
				$thisResult[] = $postcodeArr[$key];
				if($key==$size-2)
				{
					$thisResult[] =  $postcodeArr[$key+1];
				}
			}

			if($key==$size-2)
			{
				if(count($thisResult)==1)
				{
					$result[] = $thisResult[0];
				}else if(count($thisResult)>1)
				{
					$result[] = $thisResult[0].'-'.$thisResult[count($thisResult)-1];
				}

				if(($postcodeArr[$key]+1)!=$postcodeArr[$key+1])
				{
					$result[] =  $postcodeArr[$key+1];
				}
			}
		}
		return join(',',$result);
	}

	public static function checkOrgRateHasDeliveryArea($shipment,$orgRateId)
	{
		$or = OrgRate::model()->findByPk($orgRateId);
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$postcode = $shipment->cnee->postcode;
		$suburb = $shipment->cnee->suburb;
		$weight = $shipment->weight;

		$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode]);
		$chargeCode = 'xxxxxxxxx';
		if(!empty($zoneMap['z1']))
		{
			return true;
		}else
		{
			return false;
		}
	}
	public static function checkIsLongDistance($shipment)
	{
		$distance = $shipment->getDeliveryDistance();
		if(!empty($distance))
		{
			if($distance>60)
			{
				return 1;
			}else
			{
				return 0;
			}
		}else
		{
			return 2;
		}
	}


}
?>