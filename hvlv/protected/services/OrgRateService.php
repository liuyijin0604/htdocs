<?php 
class OrgRateService extends Service
{
public function getImcoConsolOrgFlexibleRateItems($consolId,$shipments,$orgId,$cgb_wt,$awb_wt,$tare_wt)
	{
		$items = [];
		$orgFlexibleRates = OrgFlexibleRate::model()->findAll('fid = :fid and model ="Consol" and org_id = :orgId',[':fid'=>$consolId,':orgId'=>$orgId]);
		if(empty($orgFlexibleRates))
		{
			$orgFlexibleRates = OrgFlexibleRate::model()->findAll('org_id = :orgId and fid = 0',[':orgId'=>$orgId]);
		}
		$imcoConsol = Consol::model()->findByPk($consolId);
		if(!empty($imcoConsol->mdata['not_apply_org_flexible_rate'])||(!empty($imcoConsol->mdata['owner_id'])&&$orgId!=$imcoConsol->mdata['owner_id']))
		{
			return [];
		}

		// 由于未知原因,会出现OrgFlexibleRate复制的时候产生重复，所以在这里要过滤掉重复的rate
    	foreach ($orgFlexibleRates as $key1 => $r1) {
    		foreach ($orgFlexibleRates as $key2 => $r2) {
    			if($r1->id!=$r2->id&&$r1->org_id==$r2->org_id&&$r1->item==$r2->item&&$r1->unit==$r2->unit&&$r1->branch==$r2->branch&&$r1->cargo==$r2->cargo&&$r1->service==$r2->service)
    			{
    				if($r1->amount==$r2->amount||$r1->amount<0.001&&$r2->amount>0.001)
    				{
    					unset($orgFlexibleRates[$key1]);
    				}
    			}
    		}
    	}
		
		foreach ($orgFlexibleRates as $key => $orgFlexibleRate)
		{
			$des = "";
			$itemKey = $orgFlexibleRate->item.$orgFlexibleRate->unit.$orgFlexibleRate->branch.$orgFlexibleRate->cargo.$orgFlexibleRate->service;
			if((!empty($imcoConsol->service)&&in_array($imcoConsol->service,explode(',', $orgFlexibleRate->service)))&&(!empty($imcoConsol->dpt_id)&&in_array($imcoConsol->dpt_id,explode(',', $orgFlexibleRate->branch))))// get current service rates
			{								
				$number =0;
				$amount = 0;
				$pcs = 0;
				$weight = $cgb_wt;
				switch ($orgFlexibleRate->unit) {
					case 'bl':
						$number = 1;
						break;
					case 'kg':
						$number =$cgb_wt;
						break;
					case 'kg_awb':
						$number =$awb_wt;
						break;
					case 'bl_overweight':
						$number = 1;
						$weight = $cgb_wt+$tare_wt;
						$des .= "({$weight} KG)";
						break;
					case 'shipment':
						$number = count($shipments);
						$pcs = count($shipments);
						break;
					case 'piece':
						foreach ($shipments as $i => $shipment) {
							$number += $shipment->pkg;
							$pcs += $shipment->pkg;
						}
						break;
					case 'pallet':
						$number = $imcoConsol->mdata['cargo_receipt_pallets'];
						break;
					case 'cage':
						# code...
						break;
					case 'AQIS':
						$aqisDetails = $imcoConsol->totAQISShipments(0,$orgId,true);
						$number = count($aqisDetails);
						$pcs = $number;
						if($number>0)
						{
							foreach ($aqisDetails as $key => $de) {
								$des.="<p>".$de->ref."</p>";
							}
							$weight = 0;
						}
						break;
					case 'container':
						$number = 1;
						break;
					default:
						# code...
						break;
				}

				if(preg_match("/(.*)\((\d{1,10})\,(\d{1,10})\)/",$orgFlexibleRate->item,$wm))
				{
					$orgFlexibleRate->item = $wm[1];
					if($weight<$wm[2]||$weight>$wm[3])
					{
						continue;
					}
				}

				if(empty($imcoConsol->mdata['air_type'])&&in_array(ImcoConsol::AIRCONSOL,explode(',', $orgFlexibleRate->service))&&$imcoConsol->service==ImcoConsol::AIRCONSOL)
				{
					continue;
				}

				if(empty($imcoConsol->mdata['sea_type'])&&in_array(ImcoConsol::SEACONSOL,explode(',', $orgFlexibleRate->service))&&$imcoConsol->service==ImcoConsol::SEACONSOL)
				{
					continue;
				}


				if(!empty($imcoConsol->mdata['air_type'])&&in_array(ImcoConsol::AIRCONSOL,explode(',', $orgFlexibleRate->service))&&$imcoConsol->service==ImcoConsol::AIRCONSOL)
				{
					
					$selectAll = false;
					if(in_array('loose',explode(',', $orgFlexibleRate->cargo))&&in_array('AKE',explode(',', $orgFlexibleRate->cargo))&&in_array('PMC',explode(',', $orgFlexibleRate->cargo))&&in_array('DQF',explode(',', $orgFlexibleRate->cargo)))
					{
						$selectAll = true;
					}

					if(!empty($imcoConsol->mdata['air_type'])&&$imcoConsol->mdata['air_type']==ImcoConsol::AIR_TYPE_LOOSE&&!in_array('loose',explode(',', $orgFlexibleRate->cargo))&&$selectAll===false)
					{
						$number = 0;
						$pcs = 0;
					}elseif(!empty($imcoConsol->mdata['air_type'])&&$imcoConsol->mdata['air_type']==ImcoConsol::AIR_TYPE_PMC&&!in_array('PMC',explode(',', $orgFlexibleRate->cargo))&&$selectAll===false)
					{
						$number = 0;
						$pcs = 0;
					}elseif(!empty($imcoConsol->mdata['air_type'])&&$imcoConsol->mdata['air_type']==ImcoConsol::AIR_TYPE_AKE&&!in_array('AKE',explode(',', $orgFlexibleRate->cargo))&&$selectAll===false)
					{
						$number = 0;
						$pcs = 0;
					}elseif(!empty($imcoConsol->mdata['air_type'])&&$imcoConsol->mdata['air_type']==ImcoConsol::AIR_TYPE_DQF&&!in_array('DQF',explode(',', $orgFlexibleRate->cargo))&&$selectAll===false)
					{
						$number = 0;
						$pcs = 0;
					}

					if(empty($imcoConsol->mdata['air_type'])&&$selectAll===false)
					{
						$number = 0;
						$pcs = 0;
					}

					// if(!empty($imcoConsol->mdata['air_type'])&&$imcoConsol->mdata['air_type']==ImcoConsol::AIR_TYPE_PMC&&in_array('PMC',explode(',', $orgFlexibleRate->cargo))&&$selectAll===false&&!in_array($orgFlexibleRate->unit, ['bl','piece','kg','kg_awb','pallet','AQIS']))
					// {
					// 	$number = empty($imcoConsol->mdata['b&l_pcs'])?0:$imcoConsol->mdata['b&l_pcs'];
					// 	$pcs = empty($imcoConsol->mdata['b&l_pcs'])?0:$imcoConsol->mdata['b&l_pcs'];
					// }elseif(!empty($imcoConsol->mdata['air_type'])&&$imcoConsol->mdata['air_type']==ImcoConsol::AIR_TYPE_AKE&&in_array('AKE',explode(',', $orgFlexibleRate->cargo))&&$selectAll===false&&!in_array($orgFlexibleRate->unit, ['bl','piece','kg','kg_awb','pallet','AQIS']))
					// {
					// 	$number = empty($imcoConsol->mdata['b&l_pcs'])?0:$imcoConsol->mdata['b&l_pcs'];
					// 	$pcs = empty($imcoConsol->mdata['b&l_pcs'])?0:$imcoConsol->mdata['b&l_pcs'];
					// }
				}elseif(!empty($imcoConsol->mdata['sea_type'])&&in_array(ImcoConsol::SEACONSOL,explode(',', $orgFlexibleRate->service))&&$imcoConsol->service==ImcoConsol::SEACONSOL)
				{
					$selectAll = false;
					if(in_array("20'",explode(',', $orgFlexibleRate->cargo))&&in_array("40'",explode(',', $orgFlexibleRate->cargo)))
					{
						$selectAll = true;
					}

					if(!empty($imcoConsol->mdata['sea_type'])&&$imcoConsol->mdata['sea_type']==1&&in_array("20'",explode(',', $orgFlexibleRate->cargo)))
					{

					}elseif(!empty($imcoConsol->mdata['sea_type'])&&in_array($imcoConsol->mdata['sea_type'], [2,3,4])&&in_array("40'",explode(',', $orgFlexibleRate->cargo)))
					{

					}else
					{
						$number = 0;
						$pcs = 0;
					}

					if(empty($imcoConsol->mdata['sea_type'])&&$selectAll===false)
					{
						$number = 0;
						$pcs = 0;
					}

					if($number>0&&$orgFlexibleRate->unit=='container')
					{
						$allCargoArray = ['<600','<800','<1000','<1200','<99999999'];
						$myCargoArray = explode(',', $orgFlexibleRate->cargo);
						$checkCargoArray = false;
						$checkCargoArrayCount = 0;
						foreach ($allCargoArray as $key => $allCargoObj) {
							if(in_array($allCargoObj,explode(',', $orgFlexibleRate->cargo)))
							{
								$checkCargoArray = true;
								$checkCargoArrayCount+=1;
							}
						}


						if($checkCargoArray)
						{
							$number = 1;
							
							$thisPcs = $imcoConsol->mdata["b&l_pcs"];
							
							if($checkCargoArrayCount==count($allCargoArray))
							{
								if($thisPcs>99999999)
								{
									$number = 0;
								}
							}else
							{
								if(in_array('<600',explode(',', $orgFlexibleRate->cargo)))
								{
									if($thisPcs>600)
									{
										$number = 0;
									}
								}

								if(in_array('<800',explode(',', $orgFlexibleRate->cargo)))
								{
									if($thisPcs<800&&$thisPcs>=600)
									{
										$number = 1;
									}


									if($thisPcs>800||$thisPcs<600)
									{
										$number = 0;
									}

									if($thisPcs<=600)
									{
										if(in_array('<600',explode(',', $orgFlexibleRate->cargo)))
										{
											$number = 1;
										}
									}
								}

								if(in_array('<1000',explode(',', $orgFlexibleRate->cargo)))
								{

									if($thisPcs<1000&&$thisPcs>=800)
									{
										$number = 1;
									}


									if($thisPcs>1000||$thisPcs<800)
									{
										$number = 0;
									}

									

									if($thisPcs<=800&&empty($number))
									{
										if($thisPcs<=600)
										{
											if(in_array('<600',explode(',', $orgFlexibleRate->cargo)))
											{
												$number = 1;
											}
										}else
										{
											if(in_array('<800',explode(',', $orgFlexibleRate->cargo)))
											{
												$number = 1;
											}
										}
									}

								}

								if(in_array('<1200',explode(',', $orgFlexibleRate->cargo)))
								{
									if($thisPcs<1200&&$thisPcs>=1000)
									{
										$number = 1;
									}


									if($thisPcs>1200||$thisPcs<1000)
									{
										$number = 0;
									}

									if($thisPcs<=600&&empty($number))
									{
										if(in_array('<600',explode(',', $orgFlexibleRate->cargo)))
										{
											$number = 1;
										}
									}


									if($thisPcs<=1000&&empty($number))
									{
										if($thisPcs<=800&&empty($number))
										{
											if($thisPcs<=600)
											{
												if(in_array('<600',explode(',', $orgFlexibleRate->cargo)))
												{
													$number = 1;
												}
											}else
											{
												if(in_array('<800',explode(',', $orgFlexibleRate->cargo)))
												{
													$number = 1;
												}
											}
										}else
										{
											if(in_array('<1000',explode(',', $orgFlexibleRate->cargo)))
											{
												$number = 1;
											}
										}
									}

									if($thisPcs>1200)
									{
										if(in_array('<99999999',explode(',', $orgFlexibleRate->cargo)))
										{
											if($thisPcs>99999999)
											{
												$number = 0;
											}else
											{
												$number = 1;
											}
										}
									}
								}

								if(in_array('<99999999',explode(',', $orgFlexibleRate->cargo)))
								{
									if($thisPcs<99999999&&$thisPcs>=1200)
									{
										$number = 1;
									}
									
									if($thisPcs>99999999||$thisPcs<1200)
									{
										$number = 0;
									}
								}

							}

							$pcs = $thisPcs;
						}
					}else
					{
						$orgFlexibleRate->cargo = str_replace(',<600', '',$orgFlexibleRate->cargo);
						$orgFlexibleRate->cargo = str_replace(',<800', '',$orgFlexibleRate->cargo);
						$orgFlexibleRate->cargo = str_replace(',<1000', '',$orgFlexibleRate->cargo);
						$orgFlexibleRate->cargo = str_replace(',<1200', '',$orgFlexibleRate->cargo);
						$orgFlexibleRate->cargo = str_replace(',<99999999', '',$orgFlexibleRate->cargo);
					}


					
				}

				if($orgFlexibleRate->unit=='piece')
				{
					if(preg_match("/lenf|widf|heif/",$orgFlexibleRate->cargo))
					{
						$cargoConditions = explode(',',$orgFlexibleRate->cargo);
						$lengthLimit = 0;
						$widthLimit = 0;
						$heightLimit = 0;
						$lengthLimit2 = 0;
						$widthLimit2 = 0;
						$heightLimit2 = 0;
						$shipmentRefs = [];
						foreach ($cargoConditions as $ckey => $cargoCondition)
						{
							if(preg_match("/lenf/",$cargoCondition))
							{
								$lengthLimit = intval(str_replace('lenf','',$cargoCondition));
							}elseif(preg_match("/widf/",$cargoCondition))
							{
								$widthLimit = intval(str_replace('widf','',$cargoCondition));
							}elseif(preg_match("/heif/",$cargoCondition))
							{
								$heightLimit = intval(str_replace('heif','',$cargoCondition));
							}elseif(preg_match("/lent/",$cargoCondition))
							{
								$lengthLimit2 = intval(str_replace('lent','',$cargoCondition));
							}elseif(preg_match("/widt/",$cargoCondition))
							{
								$widthLimit2 = intval(str_replace('widt','',$cargoCondition));
							}elseif(preg_match("/heit/",$cargoCondition))
							{
								$heightLimit2 = intval(str_replace('heit','',$cargoCondition));
							}

						}
						if($lengthLimit>0||$widthLimit>0||$heightLimit>0||$lengthLimit2>0||$widthLimit2>0||$heightLimit2>0)
						{
							$number = 0;
							$pcs = 0;
							foreach ($shipments as $skey => $shipment)
							{
								$spcs = 0;
								foreach ($shipment->packs as $key => $pack)
								{
									$thisPackDim = [$pack['length'],$pack['width']];
									rsort($thisPackDim);
									$length = $thisPackDim[0];
									$width = $thisPackDim[1];
									$height = $pack['height'];
									if($lengthLimit>0.001)
									{
										if(($length>=$lengthLimit&&$length<=$lengthLimit2&&$lengthLimit>0.001)||($width>=$widthLimit&&$width<=$widthLimit2&&$widthLimit>0.001)||($height>=$heightLimit&&$height<=$heightLimit2&&$heightLimit>0.001))
										{
											$number += 1;
											$pcs+=1;
											$spcs+=1;
											$shipmentRefs[$shipment->id] = $shipment->ref."*".$spcs;
										}
									}
								}
							}
							if($number>0)
							{
								$weight = 0;
								$des = "({$number}*{$orgFlexibleRate->amount})</br>".join('</br>',$shipmentRefs);
							}
						}
					}
				}


				if(empty($number))
				{
					continue;
				}

				$amount = number_format(max($orgFlexibleRate->amount*$number,$orgFlexibleRate->minimum), 2, '.', '');
				if($amount<0.001) continue;
				
				if($selectAll)
				{
					$orgFlexibleRate->cargo = "";
				}
				$pcs = trim($pcs);
				$weight = trim($weight);
				$pcs = empty($pcs)?0:$pcs;
				$weight = empty($weight)?0:$weight;
				$orgFlexibleRate->cargo=str_replace(',lenf0,lent0,widf0,widt0,heif0,heit0','',$orgFlexibleRate->cargo);
				$items[] = [$orgFlexibleRate->item, $orgFlexibleRate->item.' '.$orgFlexibleRate->cargo.' Fee'.$des, $pcs, $weight, 0, $amount, 0, $orgFlexibleRate->amount, 0,'rebate'=>$orgFlexibleRate->rebate];
			}
		}

		$cartageKey = -1;
		$fuelKey = -1;
		foreach ($items as $key => $item) {
			if($item[0]=="Cartage")
			{
				$cartageKey = $key;
			}

			if($item[0]=="Fuel")
			{
				$fuelKey = $key;
			}
		}

		if($cartageKey>=0)
		{
			if($fuelKey>=0)
			{
				if($items[$fuelKey][7]>1)
				{

				}else if($items[$fuelKey][7]>0 and $items[$fuelKey][7]<1)
				{
					$items[$fuelKey][5] = number_format($items[$cartageKey][5]*$items[$fuelKey][7],2,'.','');
				}
			}else
			{

				$fuelPercentage = ImportsSystemFuel::getThisMonthFuel($imcoConsol->eta,ImportsSystemFuel::CARTAGE_TYPE);
				$amount = number_format($items[$cartageKey][5]*$fuelPercentage,2,'.','');
				$items[] = ['Fuel','Cartage Fuel Surcharge('.$items[$cartageKey][5].'*'.$fuelPercentage.")", 0, 0, 0, $amount, 0, $amount, 0,'rebate'=>$items[$cartageKey]['rebate']];
			}
		}

		return $items;
	}

	public function calculateCheaperCourierCost($shipment,$couriersObj)
	{
		$country = "AU";
		$o = new stdClass;
		$o->status = false;
		$o->error = [];
		$o->msg = '';
		$courier = 0;
		$valid_dim_weight = function($dim, $org_rate, $weight){
			$temp = array_values($dim);
			sort($temp);
			return $org_rate->zone_id == 2? ($temp[0] <= 0.5 && $temp[1] <= 13 && $temp[2] <= 24 && $weight <= 0.25) : ($temp[0] <= 2 && $temp[1] <= 26 && $temp[2] <= 36 && $weight <= 0.50);
		};

		$poboxerror = false;
		if(Addr::checkIsPoBox(@$shipment->cnee->address)){
			foreach ($couriersObj as $key => $courier){
				$thisCourier = OrgRate::model()->findByPk($courier);
				if(!in_array($thisCourier->code, [ImportChargeCode::IMILE_SYD,ImportChargeCode::SYDNEY_AUPOST_ID_CODE,ImportChargeCode::AUPOST_EXPRESS_CODE,ImportChargeCode::MELBOUNE_AUPOST_ID_CODE, ImportChargeCode::BRISBANE_AUPOST_ID_CODE, ImportChargeCode::PERTH_AUPOST_ID_CODE,ImportChargeCode::LETTER_BIG_CODE, ImportChargeCode::LETTER_SMALL_CODE, ImportChargeCode::PRIORITY_LETTER_BIG_CODE, ImportChargeCode::PRIORITY_LETTER_SMALL_CODE, ImportChargeCode::UBI_SYD_CODE, ImportChargeCode::UBI_MEL_CODE, ImportChargeCode::UBI_BNE_CODE, ImportChargeCode::UBI_PER_CODE, ImportChargeCode::UBI_ADL_CODE,ImportChargeCode::PICKUP_CODE,ImportChargeCode::SYDNEY_GV_AUPOST_ID_CODE,ImportChargeCode::MELBOURNE_GV_AUPOST_ID_CODE, ImportChargeCode::BRISBANE_GV_AUPOST_ID_CODE, ImportChargeCode::PERTH_GV_AUPOST_ID_CODE])){ // , ImportChargeCode::STARTRACK_SYDNEY, ImportChargeCode::STARTRACK_MEL		
					unset($couriersObj[$key]);
					$poboxerror = true;
				}
			}
		}

		if(empty($couriersObj)){
			$o->error[] = $poboxerror? '[60001] - Po Box not supported' : '[60004] - ' . 'Charge code : ' . $chargeCode . ' has no courier set';
			return $o;
		}

		$selectedOrgRates = [];
		$selectedOrg = [];
		$isFoundTLD = false;
		$isFoundCourier = false;
		$otherCourierDelivery = false;
		$isMixTLDCourier = false;
		$weightCheckMsg = [];
		$pe = null;
		$peDepotId = 0;
		if(!empty($shipment->mdata['pe_number']))
		{
			$pe = PriceEnquiry::model()->find("code = :code",[':code'=>$shipment->mdata['pe_number']]);
			if(empty($pe))
			{
				$o->error[] = '[90400] - PE number is not existed in our system.';
				return $o;
			}else
			{
				$peDepotId = PriceEnquiry::listDepotId[$pe->depot];
			}
		}
		
		foreach ($couriersObj as $courier){
			$orgRate = OrgRate::model()->findByPk($courier);
			if(empty($orgRate) || empty($orgRate->org_id)) continue;
			$selectedOrg[$orgRate->org_id] = $orgRate->org_id;//save for checking whether it is mix TLD and courier


			if(!empty($shipment->mdata['is_dg'])&&empty($orgRate->mdata['can_dg'])) continue;
			if($shipment->pkg > 1 && in_array($courier, [101, 110, 111])) continue; //eparcel single only
			if(!empty($orgRate->mdata['business_type']))//B2B or B2C
			{
				if($orgRate->mdata['business_type']==OrgRate::B2C_TYPE&&!empty($shipment->mdata['is_b2b']))
				{
					$weightCheckMsg[] = $shipment->hbn . '- shipment is B2B ';
					continue;
				}else if($orgRate->mdata['business_type']==OrgRate::B2B_TYPE&&empty($shipment->mdata['is_b2b']))
				{
					$weightCheckMsg[] = $shipment->hbn . '- shipment is B2C ';
					continue;
				}
			}


			$maxWeight = isset($orgRate->org->extra['maxwt'])? $orgRate->org->extra['maxwt'] : 0;
			$maxDim = isset($orgRate->org->extra['maxdim'])? $orgRate->org->extra['maxdim'] : 0; // by CM for any maximum of D, H , W
			$maxDim = !empty($orgRate->mdata['maxdim'])? $orgRate->mdata['maxdim'] : $maxDim; // by CM for any maximum of D, H , W
			$maxWeight = !empty($orgRate->mdata['maxwt'])? $orgRate->mdata['maxwt'] : $maxWeight;
			$limitCBM = isset($orgRate->org->extra['limit_cbm'])? $orgRate->org->extra['limit_cbm'] : 0;
			$limitCBM = !empty($orgRate->mdata['limit_cbm'])? $orgRate->mdata['limit_cbm'] : $limitCBM;

			$limitPCS = isset($orgRate->mdata['limit_pcs'])? $orgRate->mdata['limit_pcs'] : 0;


			$units = $shipment->pkg <= 0 ? 1 : $shipment->pkg;
			$chargeWeight = $shipment->courierWeight();

			if(!empty($orgRate->mdata['plt_pricing']))
			{
				$chargeWeight = empty($shipment->mdata['amzon_pallet'])?0:$shipment->mdata['amzon_pallet'];
			}

			if(!empty($limitPCS)&&$units>$limitPCS)
			{
				$weightCheckMsg[] = $shipment->cref . '- max pieces over limit';
				continue;
			}

			if(empty($shipment->packs))//if empty packages data, just compare the whole shipment with limit
			{
				if(!empty($limitCBM))
				{
					if(!empty($maxWeight)&&$shipment->weight>=$maxWeight)
					{
						$weightCheckMsg[] = $shipment->cref . '- max weight over limit';
						continue;
					}

					if($shipment->getTotalCBM()>$limitCBM)
					{
						$weightCheckMsg[] = $shipment->cref.' with '.$shipment->getTotalCBM().' CBM is over '.$limitCBM.' CBM';
						continue;
					}
				}
			}else
			{
				if(!empty($shipment->packs) && $maxWeight > 0)
				{
					foreach ($shipment->packs as $i=>$pack){
						$cbm = $pack['length']*$pack['width']*$pack['height']/1000000;
						if($cbm>$limitCBM&&$limitCBM>0)
						{
							$weightCheckMsg[] = $shipment->hbn.'- packs index:'.($i+1). ' volume '.number_format($cbm,4,'.','').' cbm over '.$limitCBM.' CBM limit';
							continue 2;
						}

						if($pack['weight']>$maxWeight&&$maxWeight>0){
							$isWeightValid=false;
							$weightCheckMsg[] = $shipment->hbn.'- packs index:'.($i+1). ' weight '.$pack['weight'].'Kg over limit';
							continue 2;
						}

					}
				}
			}



			if($shipment->insurance>0)
			{
				if(!in_array($orgRate->code, [ImportChargeCode::SYDNEY_AUPOST_ID_CODE,ImportChargeCode::AUPOST_EXPRESS_CODE,ImportChargeCode::MELBOUNE_AUPOST_ID_CODE, ImportChargeCode::BRISBANE_AUPOST_ID_CODE, ImportChargeCode::PERTH_AUPOST_ID_CODE]))
				{
					$weightCheckMsg[] = $shipment->cref.'can\'t have insurance for courier '.$orgRate->org_id;
					continue;
				}
			}

			//check letter
			if($orgRate->org_id == Org::ORGID_COURIER_AUSLETTER){
				if($shipment->pkg > 1){
					$weightCheckMsg[] = $shipment->hbn . '- letter only support 1 Pack !';
					continue;
				}

				if(!empty($shipment->mdata['dim']['w']) && !empty($shipment->mdata['dim']['h']) && !empty($shipment->mdata['dim']['d'])){
					$letter_dim=[
						$shipment->mdata['dim']['w'], 
						$shipment->mdata['dim']['h'],
						$shipment->mdata['dim']['d'],
					];
				} elseif(isset($shipment->packs[0])){
					//we can check the packs[0] for width, length,height;
					$letter_dim = [
						$shipment->packs[0]['width'],
						$shipment->packs[0]['length'],
						$shipment->packs[0]['height'],
					];
				} else {
					$weightCheckMsg[] = 'Letter Service Require h d w';
					continue;
				}

				if(!$valid_dim_weight($letter_dim, $orgRate, $shipment->weight)){
					$weightCheckMsg[] = $shipment->hbn.'-letter dim over size';
					continue;
				}
			}
			if($maxDim > 0){
				foreach(['w', 'h', 'd'] as $k){
					${$k} = isset($shipment->mdata['dim'][$k])? $shipment->mdata['dim'][$k] : 0;
				}
				$sMaxDim = max($w, $h, $d);
				if($sMaxDim > $maxDim){
					$weightCheckMsg[] = '[60001] - ' . $shipment->hbn . '- max dimension over  ' . $orgRate->org->name . ' limit';
					continue;
				}
			}
			if(!empty($shipment->packs) && $maxDim > 0){
			
				foreach ($shipment->packs as $pack){
					if(max($pack['width'], $pack['height'], $pack['length']) > $maxDim){
						$weightCheckMsg[] = $shipment->hbn.'-dim over '. $orgRate->org_id . '(' . $orgRate->org->name . ')' . ' max Requirement:'.$maxDim.'cm | ';
						continue;
					}
				}
			}

			if(empty($shipment->packs)) {
				if(!empty($orgRate->mdata['dimension_requiring']))//&&$p->agent_id!=Org::ORGID_CLIENT_AOCHEN)
				{
					$weightCheckMsg[] = $shipment->hbn . ' courier '.$orgRate->org_id." is dimension requiring";
					continue;
				}
			}

			if((!empty($p->mdata['is_fba'])||TntAPI::isMatchAmazon($shipment->cnee->postcode,$shipment->cnee->suburb,$shipment->cnee->address))&&(preg_match('/tnt|border|hunter/i', $orgRate->code)||(in_array($orgRate->org_id, [Org::ORGID_COURIER_AUPOST,Org::ORGID_COURIER_FASTWAY])&&$shipment->agent_id==Org::ORGID_CLIENT_AUSTWAY)))
			{
				$weightCheckMsg[] = '[60001] - ' . $orgRate->org_id . ' can\'t service for FBA parcels';
				continue;
			}

			// 2022-09-27 check oversize and overweight for Toll
			if(true)
			{
				if(preg_match('/'.SystemSetting::getTLDChargecode('courier_oversize_overweight_code').'/i',$orgRate->code)&&!in_array($shipment->agent_id,SystemSetting::getTLDChargecode('courierIgnoreAgentIds')))
				{
					$surcharge=$shipment->getChargeByChargecode(SystemSetting::getTLDChargecode('checkNormalChargecode'),false,null,true,false,true);
					if(!empty($surcharge['MHCharge']))
					{
						$weightCheckMsg[] = '[99999] - ' . $orgRate->org_id . 'oversize/overweight no courier found';
						continue;
					}
				}
			}

			$selectedOrgRates[] = $orgRate;
		}


		// if(count($selectedOrg)>1)
		// {
		// 	$isFoundNotToll = false;
		// 	foreach ($selectedOrg as $key => $value) {
		// 		if($value==Org::ORGID_COURIER_TLA) $isFoundTLD = true;
		// 		if($value!=Org::ORGID_COURIER_TLA) $isFoundCourier = true;
		// 	}

		// 	if($isFoundTLD&&$isFoundCourier)
		// 	{
		// 		$isMixTLDCourier = true;
		// 	}

		// 	$tolls = [];
		// 	foreach ($selectedOrgRates as $key => $orgRate) {
		// 		if(preg_match('/TOLL/i', $orgRate->code))
		// 		{
		// 			$tolls[] = $orgRate;
		// 			unset($selectedOrgRates[$key]);
		// 		}else
		// 		{
		// 			$isFoundNotToll = true;//2022-11-23 when WA MIX chargecode, don't use TOLL
		// 		}
		// 	}

		// 	if($isFoundNotToll&&preg_match('/6\d{3}/',$shipment->cnee->postcode))
		// 	{

		// 	}else
		// 	{
		// 		if(!empty($tolls))
		// 		{
		// 			$selectedOrgRates = array_merge($selectedOrgRates,$tolls);
		// 		}
		// 	}
		// }

		if(empty($selectedOrgRates)){
			$o->error[] = '[60001] - ' . $shipment->hbn . ' failed : ' . implode(', ', $weightCheckMsg);
			return $o;
		}
		$cheapOrgRate = null;
		//cost check

		$minCost = 999999999; // in order to get minimum one
		$minDistance = 999999;// in order to get the solution with shortest distance
		$cheapOrgRate = 0;
		$getCheapOrgRateError = '';
		foreach ($selectedOrgRates as $orgrate){
			// if(!in_array($orgrate->org_id, [Org::ORGID_COURIER_AUPOST,Org::ORGID_COURIER_FASTWAY]))
			// {
			// 	$orgrate->priority = 2;

			// 	if(in_array($orgrate->org_id, [Org::ORGID_COURIER_TNT,Org::ORGID_COURIER_TNT_TOP]))
			// 	{
			// 		$orgrate->priority = 1;
			// 	}
			// }
			//if pe is not empty, filter all orgrates are not in the same pedepotid or that are not the TLD service
			if(!empty($pe))
			{
				if($orgrate->org_id!=Org::ORGID_COURIER_TLA&&$orgrate->org_id!=Org::ORGID_COURIER_TOLL_IPEC)
				{
					continue;
				}

				if(!empty($orgrate->mdata['ddpt_id']))
				{
					if($orgrate->mdata['ddpt_id']!=$peDepotId)
					{
						continue;
					}
				}

				// now this orgrate can be used directly
				$cheapOrgRate = $orgrate;
				break;
			}


			if($country!='AU'&&$orgrate->code!=ImportChargeCode::AUPOST_INTERNATIONAL_CODE) continue;// the international country only use auspost
			if($orgrate->org_id == Org::ORGID_COURIER_PICKUP){ //pickup override
				$cheapOrgRate = $orgrate;
				break;
			}
			// if(empty($chargeCodeInfo->mdata['cm_address'])||$p->weight>=5||(!empty($chargeCodeInfo->mdata['cm_address'])&&$orgrate->org_id!=Org::ORGID_COURIER_AUPOST&&count($selectedOrgRates)>1))// this is for the chargecode with mixed couriers
			// {
			$chargeCodeInfo = ImportChargeCode::model()->find('chargecode = :ccode', [':ccode' => '0001']);
			$rt = ChooseShipment::courierCanDelivery($orgrate->org_id, $shipment,true,$orgrate->id,$chargeCodeInfo->id);
			if(!$rt->success){
				$getCheapOrgRateError .= implode(', ', $rt->error);
				continue;
			}
			// }
			$cost = ImcoConsol::getCourierCostPrice($orgrate, $shipment->cnee->postcode, $chargeWeight, $units, true, $shipment->cnee->suburb,false,$country,$shipment);
			/* aupost metro ratio hack
			if($orgrate->org_id == 115 && $cost > 0 && in_array($shipment->agent_id, [3113])){
				$z = Yii::app()->db->createCommand("SELECT id FROM `zone_map` WHERE org_id = 101 AND chargecode_id = 0 AND zone_id = 3 AND pc_lo <= :pc AND pc_hi >= :pc AND z1 REGEXP '[A-Z]{1}(0|1)' LIMIT 1")->bindValues([':pc' => $shipment->cnee->postcode])->queryScalar();
				if(!empty($z)) $cost += 1;
			}*/
			// only for austway

			if(!empty($cheapOrgRate)&&$cost>0&&$cost!=99999&&$cost!=88888)
			{
				if($shipment->agent_id==Org::ORGID_CLIENT_AUSTWAY)
				{
					if(in_array($cheapOrgRate->code,ImportChargeCode::EIZ_ALLIED_CODES)&&in_array($orgrate->code,ImportChargeCode::EIZ_TOLL_CODES)&&$cheapOrgRate->mdata['ddpt_id']==$orgrate->mdata['ddpt_id'])
					{
						if($shipment->getSingleCubeWeight()<30||$shipment->pkg>3)
						{
							$cheapOrgRate = $orgrate;
						}
						continue;
					}

					if(in_array($cheapOrgRate->code,ImportChargeCode::EIZ_TOLL_CODES)&&in_array($orgrate->code,ImportChargeCode::EIZ_ALLIED_CODES)&&$cheapOrgRate->mdata['ddpt_id']==$orgrate->mdata['ddpt_id'])
					{
						if($shipment->getSingleCubeWeight()>=30&&$shipment->pkg<=3)
						{
							$cheapOrgRate = $orgrate;
						}
						continue;
					}

				}

				//for priority selecting
				// if($cheapOrgRate->priority==2&&$orgrate->priority==1)
				// {
				// 	$cheapOrgRate = $orgrate;
				// 	continue;
				// }
				// if($cheapOrgRate->priority==1&&$orgrate->priority==2)
				// {
				// 	continue;
				// }



			}
			
			if($cost <= 0||$cost==99999){
				$getCheapOrgRateError .= $orgrate->org_id . ' cost not set for postcode : ' . $shipment->cnee->postcode . ' weight :' . $shipment->weight.";";
			}elseif($cost==88888){
				$getCheapOrgRateError .= $orgrate->org_id . ' cost remote checking error, please try again';
			}elseif($cost < $minCost){
				if(!empty($cheapOrgRate)&&$cheapOrgRate->isOrgRateMyToll()&&$orgrate->isOrgRateUbiToll())// for checking my toll and ubi toll compare when mytoll cost is lower than ubitoll cost
				{
					$calCost = $minCost-$cost;
					if(($calCost/$minCost)>0.05)
					{
						$minCost = $cost;
						$cheapOrgRate = $orgrate;
						$distance = GoogleMapAPI::getDistance($shipment,@$orgrate->mdata['ddpt_id']);
						if(!empty($distance)&&($distance < $minDistance))
						{
							$minDistance = $distance;
						}
					}
				}else
				{
					$minCost = $cost;
					$cheapOrgRate = $orgrate;
					$distance = GoogleMapAPI::getDistance($shipment,@$orgrate->mdata['ddpt_id']);
					if(!empty($distance)&&($distance < $minDistance))
					{
						$minDistance = $distance;
					}
				}

			}elseif($cost == $minCost){
				if(!empty($cheapOrgRate)&&$orgrate->isOrgRateMyToll()&&$cheapOrgRate->isOrgRateUbiToll())// for checking my toll and ubi toll compare
				{
					$minCost = $cost;
					$cheapOrgRate = $orgrate;
					$distance = GoogleMapAPI::getDistance($shipment,@$orgrate->mdata['ddpt_id']);
					if(!empty($distance)&&($distance < $minDistance))
					{
						$minDistance = $distance;
					}
				}else
				{
					$distance = GoogleMapAPI::getDistance($shipment,@$orgrate->mdata['ddpt_id']);
					if(!empty($distance)&&($distance < $minDistance))
					{
						$minCost = $cost;
						$minDistance = $distance;
						$cheapOrgRate = $orgrate;
					}
				}
			}

			if($cost>$minCost&&$cost!=99999&&$cost!=88888)
			{
				if($orgrate->org_id!=Org::ORGID_COURIER_TLA)
				{
					$otherCourierDelivery =true;
				}

				if(!empty($cheapOrgRate)&&$orgrate->isOrgRateMyToll()&&$cheapOrgRate->isOrgRateUbiToll())// for checking my toll and ubi toll compare when mytoll cost is larger than ubitoll cost
				{
					$calCost = $cost-$minCost;
					if(($calCost/$minCost)<0.05)
					{
						$minCost = $cost;
						$cheapOrgRate = $orgrate;
						$distance = GoogleMapAPI::getDistance($shipment,@$orgrate->mdata['ddpt_id']);
						if(!empty($distance)&&($distance < $minDistance))
						{
							$minDistance = $distance;
						}
					}
				}
			}

		}

		if(!empty($cheapOrgRate)){
			if(Org::ORGID_COURIER_TOLL == $cheapOrgRate->org_id && empty($shipment->cnee->company)){
				$o->error[] = '[60001] - ' . $shipment->hbn . '- Please set consignee company name for Toll';
			}

			if(in_array($cheapOrgRate->org_id, [Org::ORGID_COURIER_TOLL, Org::ORGID_COURIER_STARTRACK]) && floatval($shipment->cbm) <= 0){ //dim/cubic check
				if(isset($shipment->mdata['dim'])){
					$shipment->cbm = floatval($shipment->mdata['dim']['w']) * floatval($shipment->mdata['dim']['h']) * floatval($shipment->mdata['dim']['d']);
				}

				if($shipment->cbm <= 0){
					$o->error[] = '[60006] - Dimensions or cubic is missing';
				}
			}

			if($isMixTLDCourier)
			{
				if($cheapOrgRate->org_id==Org::ORGID_COURIER_TLA&&!$otherCourierDelivery)
				{
					$o->error[] = '[60003] - ' . $shipment->hbn . ' failed : shipment is not in TLD delivery area';
				}
			}

		}else{
			$o->error[] = '[60003] - ' . $shipment->hbn . ' failed : ' . $getCheapOrgRateError;
		}

		if(empty($o->error)){
			$o->status = true;
			$o->courier = $cheapOrgRate;
			$o->minCost = $minCost;
		}

		return $o;
	}

	public static function getShipmentCostZone($orgRate,$shipment)
	{
		$result = [];
		$or = OrgRate::model()->findByPk($orgRate);
		if(empty($or))
		{
			return ["cost zone"=>"n/a","weight"=>"n/a"];

		}
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$weight = $shipment->chargeWeight();
		$checkZoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND suburb!="" AND pc_hi!="" ', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id]);

		if(!empty($checkZoneMap))
		{
			$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code AND suburb=:suburb', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $shipment->cnee->postcode,':suburb' => $shipment->cnee->suburb]);
		}else
		{
			$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $shipment->cnee->postcode]);
		}

		$chargeCode = 'xxxxxxxxx';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}
		$zr = ZoneRate::model()->find(["condition"=>"zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ","params"=> [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate],"order"=>"id desc"]);
		if(empty($zr))
		{
			$result = ["cost zone"=>"","weight"=>""];
		}else
		{
			$result = ["cost zone"=>$zr->zone,"weight"=>$zr->weight_lo."-".$zr->weight_hi];
		}
		return $result;

	}

	public function shipmentRemotePercentageRate($shipment,$orgRateId = null)
	{
		$levyCost = 0;
		if(empty($orgRateId))
		{
			$orgRateId = @$shipment->mdata['org_rate_id'];
		}
		if(!empty($orgRateId))
		{
			$orgId= OrgRate::model()->findByPk($orgRateId)->org_id;
			$levyCostRate = RemoteChargeRate::model()->find("rate_id = :rateId and (postcode =:postcode or CONCAT('0',postcode)=:postcode) and upper(suburb) = :suburb and levy >0",[":rateId"=>$orgRateId,":postcode"=>$shipment->cnee->postcode,":suburb"=>strtoupper($shipment->cnee->suburb)]);
			if(empty($levyCostRate))
			{
				$levyCostRate = RemoteChargeRate::model()->find("rate_id = :rateId and postcode <=:postcode and postcode_to >=:postcode and levy >0",[":rateId"=>$orgRateId,":postcode"=>$shipment->cnee->postcode]);
			}

			if(empty($levyCostRate))
			{
				$chargecode = ImportChargeCode::model()->find("chargecode = :chargecode",[":chargecode"=>$shipment->mdata["chargecode"]]);
				if(!empty($chargecode))
				{
					$levyCostRate = RemoteChargeRate::model()->find("chargecode_id = :chargecode_id and chargecode_rate_id = :rateId and (postcode =:postcode or CONCAT('0',postcode)=:postcode)  and levy >0 and suburb='' ",[":chargecode_id"=>$chargecode->id,":rateId"=>$orgRateId,":postcode"=>$shipment->cnee->postcode]);
				}
			}
		}

		if(!empty($levyCostRate))
		{
			$levyCost =$levyCostRate->levy;
		}
		return $levyCost;
	}

	public static function getOrgRebatePercentWithRebateAmount($rebateAmount,$orgId)
	{
		$percent = 0;
		$orgFlexibleRates = OrgFlexibleRate::model()->findAll('org_id = :orgId and fid = 0 and unit = "rebateRate"',[':orgId'=>$orgId]);
		foreach ($orgFlexibleRates as $key => $orgFlexibleRate)
		{
			$period = explode(',', $orgFlexibleRate->item);
			if(count($period)!=2)
			{
				continue;
			}

			if(!is_numeric($period[0])||!is_numeric($period[1]))
			{
				continue;
			}

			if($rebateAmount>=$period[0]&&$rebateAmount<=$period[1])
			{
				$percent = $orgFlexibleRate->amount;
			}
		}
		return $percent;
	}

	public function getCourierSurchargeCost($shipment,$courier)
	{
		$hvlvJavaApiService = new HvlvJavaService();
		$alliedHomeSurcharge = AddressService::isShipmentResidential($shipment,$courier);
		if($alliedHomeSurcharge)
		{
			return $this->getShipmentCostSurcharge($shipment,$courier,["noRSD"=>true]);
		}else
		{
			return $this->getShipmentCostSurcharge($shipment,$courier,["noRSD"=>false]);
		}
	}

	public function getShipmentCostSurcharge($shipment,$courier,$params)
	{
		$hvlvJavaService = new HvlvJavaService();
		$result = [];
		$cbm = $shipment->myChargeCBM();
		$courierSurchargeInfo = @$courier->courier_surcharge;
		$chargeArr = [];
		$amount = 0;
		$codeArr = [];

		foreach ($courierSurchargeInfo as $key => $courierSurchargeObj)
		{
			$codeArr[$courierSurchargeObj->type] = $courierSurchargeObj->type;
		}
		
		foreach ($codeArr as $key => $code) {
			if(in_array($code,["OSC"]))
			{
				[$objArr,$codeArr,$chargeArr,$keyArr] = $this->calculateSingleOSCRule($shipment,$courier,$courierSurchargeInfo,$code);
			}elseif(in_array($code,["RSD"]))
			{
				if($params["noRSD"])
				{
					[$objArr,$codeArr,$chargeArr,$keyArr] = [[],[],[],[]];
					
				}else
				{
					[$objArr,$codeArr,$chargeArr,$keyArr] = $this->calculateSingleRule($shipment,$courier,$courierSurchargeInfo,$code);
				}
			}else
			{	
				[$objArr,$codeArr,$chargeArr,$keyArr] = $this->calculateSingleRule($shipment,$courier,$courierSurchargeInfo,$code);
			}

			if(!empty($chargeArr))
			{
				$lastCost = array_sum($chargeArr);
			}else
			{
				$lastCost = 0;
			}
			$amount+=$lastCost;
		}
		
		return $amount;
	}


	//calculate for single type
	public function calculateSingleRule($shipment,$courier,$courierSurchargeInfo,$pattern)
	{
		//for oversize fee
		$OSArr = [];
		$OSCodeArr = [];
		$OSChargeArr = [];
		$OSKeyArr = [];
		if(!empty($courierSurchargeInfo))
		{
			if(!empty($shipment->packs))
			{
				foreach ($shipment->packs as $key2 => $pack) {
					$OS = null;
					$OSCode = '';
					$OSKey = "";
					$lastOSCost = 0;

					// handling to one pack for MH
					$thisPackDim = [$pack['length'],$pack['width']];
					rsort($thisPackDim);
					$length = $thisPackDim[0]/100;
					$width = $thisPackDim[1]/100;
					$height = $pack['height']/100;
					foreach ($courierSurchargeInfo as $key => $value)
					{
						$weight = $pack['weight'];
						$cubeWeight = ($length*$width*$height)*250;

						$thisOS = null;
						$thisOSCode = "";
						$thisCharge = 0;
						$thisOSKey = "";
						if($value->type==$pattern)
						{
							if($value['p_shipment']>0)
							{
								$weight = $shipment->weight;
								$cubeWeight = ($shipment->cbm*$shipment->pkg)*250;
								if($weight>$value['kgdwf']&&$weight<$value['kgdwt']&&!empty($value['kgdwt']))
								{
									$thisOS = $value;
									$thisOSCode =  "Weight between ".$value['kgdwf']." to ".$value['kgdwt'];
									$thisOSKey = $key;
								}
								if($cubeWeight>$value['kgcwf']&&$cubeWeight<$value['kgcwt']&&!empty($value['kgcwt']))
								{
									$thisOS = $value;
									$thisOSCode =  "cube Weight between ".$value['kgcwf']." to ".$value['kgcwt'];
									$thisOSKey = $key;
								}
								if($value['metert']>0.001)
								{
									$diagonalLength = number_format(sqrt(pow($length,2)+pow($width,2)+pow($height,2)), 2, '.', '');
									if($diagonalLength>=$value['meterf']&&$diagonalLength<=$value['metert'])
									{
										$thisOS = $value;
										$thisOSCode = "Diagonal Length between ".$value['meterf']." to ".$value['metert'];
										$thisOSKey = $key;
									}

								}

								if($value['length']>0.001||$value['width']>0.001||$value['height']>0.001)
								{
									if(in_array(@$shipment->mdata['chargecode'],CargoProcess::$newRuleChargecode)){
										if(($length>=$value['length']&&$value['length']>0.001)&&($width>=$value['width']&&$value['width']>0.001)&&($height>=$value['height']&&$value['height']>0.001))
										{
											$thisOS = $value;
											$thisOSCode = "Limited Length ".$value['length']." Width ".$value['width']." Height ".$value['height'];
											$thisOSKey = $key;
										}
									}else{
										if(($length>=$value['length']&&$value['length']>0.001)||($width>=$value['width']&&$value['width']>0.001)||($height>=$value['height']&&$value['height']>0.001))
										{
											$thisOS = $value;
											$thisOSCode = "Limited Length ".$value['length']." Width ".$value['width']." Height ".$value['height'];
											$thisOSKey = $key;
										}
									}
								}
								
								if($value['cbm']>0.001)
								{
									if($cbm>$value['cbm'])
									{
										$thisOS = $value;
										$thisOSCode = "Over cbm ".$value['cbm'];
										$thisOSKey = $key;
									}
								}

							}else if($value['p_piece']>0)
							{
								if($weight>$value['kgdwf']&&$weight<$value['kgdwt']&&!empty($value['kgdwt']))
								{
									$thisOS = $value;
									$thisOSCode =  "weight between ".$value['kgdwf']." to ".$value['kgdwt'];
									$thisOSKey = $key;
								}
								if($cubeWeight>$value['kgcwf']&&$cubeWeight<$value['kgcwt']&&!empty($value['kgcwt']))
								{
									$thisOS = $value;
									$thisOSCode = "cube Weight between ".$value['kgcwf']." to ".$value['kgcwt'];
									$thisOSKey = $key;
								}
								if($value['metert']>0.001)
								{
									$diagonalLength = number_format(sqrt(pow($length,2)+pow($width,2)+pow($height,2)), 2, '.', '');
									if($diagonalLength>=$value['meterf']&&$diagonalLength<=$value['metert'])
									{
										$thisOS = $value;
										$thisOSCode = "Diagonal Length between ".$value['meterf']." to ".$value['metert'];
										$thisOSKey = $key;
									}

								}

								if(in_array(@$shipment->mdata['chargecode'],CargoProcess::$newRuleChargecode)){
									if($value['length']>0.001||$value['width']>0.001||$value['height']>0.001)
									{
										if(($length>=$value['length']&&$value['length']>0.001)&&($width>=$value['width']&&$value['width']>0.001)&&($height>=$value['height']&&$value['height']>0.001))
										{
											$thisOS = $value;
											$thisOSCode = "Limited Length ".$value['length']." Width ".$value['width']." Height ".$value['height'];
											$thisOSKey = $key;
										}
									}
								}else{
									if($value['length']>0.001||$value['width']>0.001||$value['height']>0.001)
									{
										if(($length>=$value['length']&&$value['length']>0.001)||($width>=$value['width']&&$value['width']>0.001)||($height>=$value['height']&&$value['height']>0.001))
										{
											$thisOS = $value;
											$thisOSCode = "Limited Length ".$value['length']." Width ".$value['width']." Height ".$value['height'];
											$thisOSKey = $key;
										}
									}
								}
								
								
								if($value['cbm']>0.001)
								{
									if($cbm>$value['cbm'])
									{
										$thisOS = $value;
										$thisOSCode = "Over cbm ".$value['cbm'];
										$thisOSKey = $key;
									}
								}

							}
						}
						if(!empty($thisOS))
						{
							$thisCharge = $thisOS['p_piece']+$thisOS['p_shipment'];
							if($thisCharge>$lastOSCost)
							{
								$OS = $thisOS;
								$OSCode = $thisOSCode;
								$lastOSCost = $thisCharge;
								$OSKey = $thisOSKey;
							}
						}
					}
					if(!empty($OS))
					{
						if($OS['p_piece']>0)
						{
							$OSArr[] = $OS;
							$OSCodeArr[] = $OSCode;
							$OSChargeArr[] = $lastOSCost;
							$OSKeyArr[] = $OSKey;
						}else if($OS['p_shipment']>0)
						{
							if(empty($OSArr)||(!empty($OSArr)&&$OS['p_shipment']>$OS['p_shipment']))
							{
								$OSArr = [$OS];
								$OSCodeArr = [$OSCode];
								$OSChargeArr = [$lastOSCost];
								$OSKeyArr = [$OSKey];
							}
						}
					}

				}
			}
		}

		return [$OSArr,$OSCodeArr,$OSChargeArr,$OSKeyArr];
	}

	public function calculateSingleOSCRule($shipment,$courier,$courierSurchargeInfo,$pattern)
	{
		//for oversize fee
		$OSArr = [];
		$OSCodeArr = [];
		$OSChargeArr = [];
		$OSKeyArr = [];
		if(!empty($courierSurchargeInfo))
		{
			if(!empty($shipment->packs))
			{
				foreach ($shipment->packs as $key2 => $pack) {
					$OS = null;
					$OSCode = '';
					$OSKey = "";
					$lastOSCost = 0;

					// handling to one pack for MH
					$thisPackDim = [$pack['length'],$pack['width']];
					rsort($thisPackDim);
					$length = $thisPackDim[0]/100;
					$width = $thisPackDim[1]/100;
					$height = $pack['height']/100;
					foreach ($courierSurchargeInfo as $key => $value)
					{
						$weight = $pack['weight'];
						$cubeWeight = ($length*$width*$height)*250;

						$thisOS = null;
						$thisOSCode = "";
						$thisCharge = 0;
						$thisOSKey = "";
						if($value->type==$pattern)
						{
							if($value['p_shipment']>0)
							{
								//$weight = $this->weight;
								$weight = $pack['weight'];
								//$cubeWeight = ($this->cbm*$this->pkg)*250;
								if($weight>$value['kgdwf']&&$weight<$value['kgdwt']&&!empty($value['kgdwt']))
								{
									if(!empty($value['length']))
									{
										if($length>=$value['length']&&$width>=$value['width']&&$height>=$value['height'])
										{
											$thisOS = $value;
											$thisOSCode = "Limited Length ".$value['length']." Width ".$value['width']." Height ".$value['height'];
											$thisOSKey = $key;
										}
									}
								}
							}else if($value['p_piece']>0)
							{
								if($weight>$value['kgdwf']&&$weight<$value['kgdwt']&&!empty($value['kgdwt']))
								{
									if(!empty($value['length']))
									{
										if($length>=$value['length']&&$width>=$value['width']&&$height>=$value['height'])
										{
											$thisOS = $value;
											$thisOSCode = "Limited Length ".$value['length']." Width ".$value['width']." Height ".$value['height'];
											$thisOSKey = $key;
										}
									}
								}
							}
						}
						if(!empty($thisOS))
						{
							$thisCharge = $thisOS['p_piece']+$thisOS['p_shipment'];
							if($thisCharge>$lastOSCost)
							{
								$OS = $thisOS;
								$OSCode = $thisOSCode;
								$lastOSCost = $thisCharge;
								$OSKey = $thisOSKey;
							}
						}
					}
					if(!empty($OS))
					{
						if($OS['p_piece']>0)
						{
							$OSArr[] = $OS;
							$OSCodeArr[] = $OSCode;
							$OSChargeArr[] = $lastOSCost;
							$OSKeyArr[] = $OSKey;
						}else if($OS['p_shipment']>0)
						{
							if(empty($OSArr)||(!empty($OSArr)&&$OS['p_shipment']>$OS['p_shipment']))
							{
								$OSArr = [$OS];
								$OSCodeArr = [$OSCode];
								$OSChargeArr = [$lastOSCost];
								$OSKeyArr = [$OSKey];
							}
						}
					}

				}
			}
		}

		return [$OSArr,$OSCodeArr,$OSChargeArr,$OSKeyArr];
	}
	
}
?>