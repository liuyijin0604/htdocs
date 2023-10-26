<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);

$item_number= TntAPI::genItemUniqueNumber($label->ref, $pkg_sn);
$singleItemWeight= sprintf("%0.2f",$label->weight/$label->pkg);

$articleId="6104".$item_number."0".$label->cnee->postcode."0";
$articleBarcode = $articleId;
$collect_address = TntAPI::SYDNEY_ADDRESS;
$collect_suburb_state = TntAPI::SYDNEY_SUBURB.",".TntAPI::SYDNEY_STATE.",".TntAPI::SYDNEY_POSTCODE;
if(preg_match("/(PCD|LMA)\d{9}/i", $label->ref)){
	$collect_address = TntAPI::MEL_ADDRESS;
	$collect_suburb_state = TntAPI::MEL_SUBURB.",".TntAPI::MEL_STATE.",".TntAPI::MEL_POSTCODE;
}

if(preg_match("/BPC\d{9}/i", $label->ref)){
	$accountType =TntAPI::$api_accounts[TntAPI::SERVICE_BNE_TOP];
	$collect_address = $accountType['address'];
	$collect_suburb_state =  $accountType['suburb'].",".$accountType['state'].",".$accountType['postcode'];
}

if(preg_match("/LPC\d{9}/i", $label->ref)){
	$accountType =TntAPI::$api_accounts[TntAPI::SERVICE_PER_TOP];
	$collect_address = $accountType['address'];
	$collect_suburb_state =  $accountType['suburb'].",".$accountType['state'].",".$accountType['postcode'];
}
?>
<div>
	<div style="border-bottom:solid 2px #000000;width: 100%;">
		<div style="display: -webkit-inline-box; height: 110px;">
			<div style="width:250px"><span
					style="font-size:70px;text-align:center; display:inline-block;margin-bottom:-10px;margin-top: -10px; margin-left: 10px;"><b><?php echo @$label->mdata['tnt']['Postcode'] ?></b></span>
				<span style="font-size:20px;font-weight: bold;white-space:nowrap; text-align: center;margin-left: 10px;"><?=@$label->mdata['tnt']['Suburb']?></span>
			</div>
			
			<div style ="width:420px;">
				<div style="height:50px; font-weight:bold; margin-bottom: -5px; "><span style="font-size:33px;display: inline-block; width: 20%">via</span><span style="font-size:40px;display: inline-block;width: 30%"><?=@$label->mdata['tnt']['GatewayDepot']?></span><span style="font-size:35px;display: inline-block; width: 20%" >to</span><span style="font-size:40px; display: inline-block;width: 30%;text-align:right;"><?=@$label->mdata['tnt']['OnForwardingDepot'];?></span></div>
				<div style="height:60px; font-weight:bold;margin-bottom:-15px;"><span style="font-size:40px;display: inline-block; width: 100%;text-align:right"><?=$label->ref?></span></div>
				 <div style="height:20px;"><span style="font-size:18px;display: inline-block; width: 100%;text-align:right">Itm:<?=$item_number?></span></div>  
			</div>
		</div>
		<div style="font-size:25px;margin-top:2px;border-top: solid 2px black;border-bottom: solid 2px black;height:50px;overflow:hidden;">
			<span style="width: 60%;font-size: 34px; line-height: 50px; display: inline-block;"><b><span style="background: #000; color: #fff; padding: 0 8px; display: inline-block;">TNT</span> ROAD EXPRESS</b></span>
			<span style="width: 15%; display: inline-block;font-size:18px; text-align: right;line-height: 21px;height:50px;overflow: hidden;">Sort<br /> Bin:</span>
			<span style="width: 20%; font-weight: bold; font-size: 30px; display: inline-block;text-align: center; padding-right: 5px;"><?=@$label->mdata['tnt']['BinNumber']?></span>
		</div>
		<div style=" height:50px; padding-bottom:5px;">
			<span style="font-size:20px;display: inline-block; width:22%;"><?=date('d-m-Y',strtotime($label->created));?></span>
			<span style="display: inline-block;width: 28%;"><span style="font-size:35px;"> <?php echo $pkg_sn; ?> </span>OF <span style="font-size: 45px;"><?php echo $label->pkg; ?> </span> </span>
			<span style="display: inline-block;width: 32%; ">Item Wt. <?=$singleItemWeight?> Kg</span>
			<span style="display: inline-block;width:15%;font-size: 22px;font-weight:bold; text-align: right">Ex <?=@$label->mdata['tnt']['OriginDepot']?></span>
		</div>
		 <div style="font-size:25px;border-top: solid 2px black;padding-left: 2px; padding-bottom:2px; padding-top:2px;">
			<span style="width:95%;font-size: 22px; display: inline-block; text-align:center"><?=!empty($label->mdata['is_dg'])?'Contain Dangerous Goods':'Does Not Contain Dangerous Goods'?></span>
		</div>
		</div>
	   <div style="height:160px;border-bottom: solid 2px black;">
		   <div style="position:absolute;">To:</div><div style="padding-left: 40px; font-size: 26px;font-weight: bold;"><?=$label->cnee->name.(empty($label->cnee->company) || $label->cnee->name == $label->cnee->company? '' : ', '.substr(ucwords(strtolower($label->cnee->company)), 0, 33))?><br />
			<?=$label->cnee->address?><br />
			<?= strtoupper($label->cnee->suburb." ".$label->cnee->state." ".$label->cnee->postcode)?><?=empty($label->cnee->tel)? '' : ' &nbsp; &nbsp; Tel: '.$label->cnee->tel;?></div>
		</div>
		<div style="height:140px;padding-top:10px;">
			<div>
			<?php
				$name = $label->cnor->name;
				$org = $label->agent;
				if (!empty($org->extra['delivery_label_name'])) {
					$name = $org->extra['delivery_label_name'];
					$collect_address = '1/233 Milperra Rd';
					$collect_suburb_state = 'Bankstown Aerodrome NSW 2200';
				}
				if ($label->agent_id == 4372) {
					$collect_address = '3/47-53 Moxon Road';
					$collect_suburb_state = 'Punchbowl NSW 2196';
				}
			?>
			<?php if(empty($label->withoutSender)):?>
			<span><b>From:</b></span> <span style="font-size:20px;"><?=$name." , ".$collect_address.", ".$collect_suburb_state?></span>
			<?php endif;?>
			</div>
			<div><span><b>REF: </b></span><span style="font-size:40px;"><?=$label->hbn.($label->pkg >1? '-'.$pkg_sn : '');?></span></div>
			<div><span><b>Senders Ref: </b></span><span style="font-size:22px;"><?=$label->cref?></span></div>
			<div>
				<span><b>Special Instructions:</b><?=empty($label->mdata['sea_instruction'])? '' : ' '.$label->mdata['sea_instruction'];?></span>
			</div>
		</div>
		<div style="height:230px; border:2px solid black; margin-top: 20px;margin-left: 25px; margin-right: 25px;">
			<div style="display: inline-block;width:49%;height: 100%;border-right:2px solid black;padding-left: 5px;float: left;">
				<b>CN: <?=$label->ref?></b><br/>
				<span style="font-size:17px;"><b>Itm:<?=$item_number?></b><br/></span>
				<b><?php echo $pkg_sn; ?> &nbsp; OF &nbsp; <?php echo $label->pkg; ?></b><br/>
				<b>To:</b><br/>
			   <?=$label->cnee->name?><br>
			   <?=$label->cnee->address?><br/>
			   <?= strtoupper($label->cnee->suburb." ".$label->cnee->state)." ".$label->cnee->postcode?> 
			</div>
			<div style="height: 98%;display: inline-block; float: left; width: 49%;padding-left: 5px;">
					<b>Road Express</b><br/>  
					<b>Con Note Wt.: <?=$singleItemWeight?> Kg.</b><br/>
					<br/>
				<?php if(empty($label->withoutSender)):?>
					<b>FROM:</b><br/>
					<?=$name." , ".$collect_address."<br/>"?>
					<?=$collect_suburb_state?>
				<?php endif;?>
			</div>
	   </div>
	   </div>
	<div style="margin-top: 10px;text-align: center;width: 100%;">
		<p style="padding:5px;">
			<?php
			$bc = new TCPDFBarcode($articleBarcode, 'C128');
			$bc->getBarcodeSVG(3, 190, 'black');
			?></p>

		<p style="padding: 8px 0; font-size: 20px"><?= $articleId; ?></p>
	</div>

</div>