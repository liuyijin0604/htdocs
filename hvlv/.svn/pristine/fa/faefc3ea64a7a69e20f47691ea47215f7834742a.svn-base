<?php 
class ChargecodeService extends Service
{
	public function checkChargecode($shipment)
	{
		$chargeCode = $shipment->mdata["chargecode"];
		
			$chargeCodeInfo = ImportChargeCode::model()->find('chargecode = :ccode', [':ccode' => $chargeCode]);
			if(empty($chargeCodeInfo)){

				return '[60005] - ' . 'Invalid charge code : ' . $chargeCode;
			}

			if($chargeCodeInfo->status != 1){
				return '[40001] - Chargecode : ' . $chargeCode . ' has been disabled';
			}
			//adding dvalue check for eslink,chargecode 3600;
			if (in_array($chargeCode, [3600])) {
				if (($shipment->currency == 1 && $shipment->dvalue >= 1000)||($shipment->currency !=1 && $shipment->dvalue >= 1000 * Currency::getExrate()[0])) {
					return '[90005] - Shipment '.$d->cust_ref.' value is Over AUS $1000';
				}
			}

			$chargeCodeSetUp = $chargeCodeInfo->hasChargeSetUp($shipment);
			if(!$chargeCodeSetUp['status']){
				return '[90010] - Shipment '.$chargeCodeSetUp['msg'];
			}
		return "";

	}

	public function saveChgsurcharge($chargecode)
	{
		$modelChargeCode = ImportChargeCode::model()->find(["condition"=>'chargecode=:chargecode and status = 1', "params"=>[':chargecode' => $chargecode],"order"=>"created desc"]);
		
		if(!empty($_POST)&&!empty($modelChargeCode))
		{
			$id = $modelChargeCode->id;
			$modelChargeCode->mdata['surcharge_criteria'] = $_POST['surcharge_criteria'];
			$modelChargeCode->update(['meta']);

			$date = date("Y-m-d H:i:s");
			
			$importsFlexibleSurcharge= new ImportsFlexibleSurcharge();
			$vc = $importsFlexibleSurcharge->find(["condition"=>'chargecode_id=:chargecode_id and status =:status', "params"=>[':status' => ImportsFlexibleSurcharge::INACTIVE,':chargecode_id' => $id],"order"=>"version desc"]);
			if(!empty($vc))
			{
				$version = $vc->version+1;
			}else
			{
				$version = 1;
			}

			foreach ($_POST['mdata'] as $key => $typeData) {
				if(in_array($key,ImportsFlexibleSurcharge::$courierTypeArr))
				{
					foreach ($typeData as $code => $data) {
						$importsFlexibleSurcharge = ImportsFlexibleSurcharge::model()->find("code = :code and chargecode_id=:chargecode_id and courier_type=:type and status =:status",[":code"=>$code,":chargecode_id"=>$id,":type"=>ImportsFlexibleSurcharge::$courierType[$key],":status"=>ImportsFlexibleSurcharge::ACTIVE]);

						$archiveImportsFlexibleSurcharge = new ImportsFlexibleSurcharge();

						if(empty($importsFlexibleSurcharge))
						{
							$importsFlexibleSurcharge = new ImportsFlexibleSurcharge();
						}else
						{
							$archiveImportsFlexibleSurcharge->setAttributes($importsFlexibleSurcharge->getAttributes());
							$archiveImportsFlexibleSurcharge->id = null;
							$archiveImportsFlexibleSurcharge->status = ImportsFlexibleSurcharge::INACTIVE;
							$archiveImportsFlexibleSurcharge->version = $version;
							$archiveImportsFlexibleSurcharge->p_percent = $importsFlexibleSurcharge->p_percent;
							$archiveImportsFlexibleSurcharge->start = $importsFlexibleSurcharge->start;
							$archiveImportsFlexibleSurcharge->save();
						}

						$importsFlexibleSurcharge->type = $data['type'];
						$importsFlexibleSurcharge->code = $code;
						$importsFlexibleSurcharge->courier_type = ImportsFlexibleSurcharge::$courierType[$key];
						$importsFlexibleSurcharge->created = $date;
						$importsFlexibleSurcharge->status = ImportsFlexibleSurcharge::ACTIVE;
						$importsFlexibleSurcharge->chargecode_id = $id;

						if(!isset($data['kgdwf']))
						{
							$modelChargeCode->mdata[$key][$data['type']] = $data['p_percent'];
							$importsFlexibleSurcharge->p_percent = $data['p_percent'];
							$importsFlexibleSurcharge->start = $_POST['start'];
						}else
						{
							$importsFlexibleSurcharge->kgdwf = $data['kgdwf'];
							$importsFlexibleSurcharge->kgdwt = $data['kgdwt'];
							$importsFlexibleSurcharge->kgcwf = $data['kgcwf'];
							$importsFlexibleSurcharge->kgcwt = $data['kgcwt'];
							$importsFlexibleSurcharge->meterf = $data['meterf'];
							$importsFlexibleSurcharge->metert = $data['metert'];
							$importsFlexibleSurcharge->length = $data['length'];
							$importsFlexibleSurcharge->width =  $data['width'];
							$importsFlexibleSurcharge->height =  $data['height'];
							$importsFlexibleSurcharge->cbm =  $data['cbm'];
							$importsFlexibleSurcharge->p_piece =  $data['p_piece'];
							$importsFlexibleSurcharge->p_shipment =  $data['p_shipment'];
							$importsFlexibleSurcharge->p_percent =  $data['p_percent'];
							$importsFlexibleSurcharge->start = $_POST['start'];
						}

						$importsFlexibleSurcharge->save();

					}
					
				}

				
			}
			$modelChargeCode->note .= $_POST['note'];
			$modelChargeCode->save();
			// $this->ajaxResult($modelChargeCode);
			return false;
		}
	}

	public function changeAuPostSurchargeMonthly(){
		$html = file_get_contents('https://auspost.com.au/business/shipping/check-postage-costs/fuel-surcharge');
		$pattern = '/<th scope="row">(.*?)<\/th>\s*<td><p><b>Surcharge<\/b><\/p>\s*<p>(.*?)<\/p>/s';

        if(preg_match_all($pattern, $html, $matches, PREG_SET_ORDER))
        {
   //      	foreach ($matches as $match) {
			//     $month = trim($match[1]);
			//     $surcharge = trim($match[2]);
			//     $surchargeDecimal = floatval($surcharge) / 100.00;
			//     echo "$month: $surchargeDecimal\n";
			//     $monthStr = strtoupper(substr($month, 0, 3));
			//     $noteStr .= " ".$monthStr." ".$surcharge;
			// }			
			$month = trim($matches[0][1]);			
			$surcharge = trim($matches[0][2]);
			$surchargeDecimal = floatval($surcharge) / 100.00;
			$monthStr = strtoupper(substr($month, 0, 3));
			$noteStr = " ".$monthStr." ".$surcharge;
        }        

		if (!empty($surchargeDecimal)) {
			$chargecodesArr = SystemSetting::getAuPostSurImportChargecode();
			$nextMonth = date('Y-m-d', strtotime('first day of next month'));
			$courierPost = Array(
			    "start" => $nextMonth,
			    "surcharge_criteria" => 1,
			    "surcharge_select" => 2,
			    "mdata" => Array(
			        "truck_delivery" => Array(
			            "fuel_surcharge" => Array(
			                "p_percent" => 0,
			                "type" => "fuel_surcharge"
			            ),
			        ),
			        "courier_surcharge" => Array(
			            "fuel_surcharge" => Array(
			                "p_percent" => $surchargeDecimal,
			                "type" => "fuel_surcharge"
			            ),
			        )
			    ),
			    "note" => $noteStr,
			    "yt0" => "Save"
			);

			foreach ($chargecodesArr as $key => $chargecode) {
				$_POST = $courierPost;
				$this->saveChgsurcharge((int)$chargecode);
			}
		}
		else{
			print_r("Can not update");
		}
		
	}

	public static function getCharecodeZoneRate($cid,$weightOrCbmOrPlt,$zone,$ccodeStartDate)
	{
		$zoneRates = ZoneRate::model()->findAll('chargecode_id = :cid AND weight_lo <=:w AND weight_hi >= :w AND zone = :z AND minimum+item+perkg > 0 AND start_date<=:start_date', [':cid' => $cid, ':w' => $weightOrCbmOrPlt, ':z' => $zone, ':start_date' => $ccodeStartDate]);

		if(empty($zoneRates))// get zone rate from history version
		{
			$history = ZoneRateBk::model()->find(["condition"=>'chargecode_id = :cid AND start_date<=:start_date', "params"=>[':cid' => $cid, ':start_date' => $ccodeStartDate],"order"=>"version DESC"]);
			if(!empty($history))
			{
				$zoneRates = ZoneRateBk::model()->findAll(["condition"=>'chargecode_id = :cid AND weight_lo <= :w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0 AND start_date<=:start_date AND version =:version', "params"=>[':cid' => $cid, ':w' => $weightOrCbmOrPlt, ':z' => $zone, ':start_date' => $ccodeStartDate,":version"=>$history->version]]);
			}
		}
		return $zoneRates;
	}

}
?>