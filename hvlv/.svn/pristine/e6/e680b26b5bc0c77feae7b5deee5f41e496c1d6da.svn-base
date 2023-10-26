<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
if (preg_match('/(AMQ|333UF|33EVJ|33EVH|33A8Y)\d{7}/', $label->ref)) {
	$service_id=50;
} elseif (preg_match('/(33FJV|33FKB)\d{7}/', $label->ref)) {
	$service_id='03';
} else {
	$service_id=51;
}
$aid = $label->ref.sprintf('%02s', $pkg_sn).'00093'.$service_id.'0';
$aid .= AusPostAPI::aidChkDgt($aid);
?>
<div style="font-family: Helvetica, Verdana, Geneva, sans-serif; font-size: 20px;">
<div style="text-align:center; padding: 10px;"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/eparcel_header.svg" width="600" /></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-bottom: 10px" >
	<tr><td valign="top" height="180">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="180" style="padding:0"><b style="font-size:1.2em;">DELIVER TO</b></td>
			<td valign="top" style="font-size:1.1em;" style="padding:0"><b>PHONE:</b> <?=$label->cnee->tel;?></td>
		</tr>
		</table><br/>
		<p style="font-size:1.4em"><?=ucwords(strtolower($label->cnee->name));?>
					 <?php if (!empty($label->cnee->company) && $label->cnee->company != $label->cnee->name && !preg_match('/' . $label->cnee->name . '/i', $label->cnee->company)) {
	echo ", ".substr(ucwords(strtolower($label->cnee->company)), 0, 33);
}?><br />
	<?=$label->cnee->address;?><br/><br/>
	<?=strtoupper($label->cnee->suburb.' '.$label->cnee->state.' '.$label->cnee->postcode);?>
	</td>
	<td width="160" valign="top" height="180" align="right">
		<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td width="160" valign="top" height="120">
		 <?php if (empty($label->mdata['receipted'])):?>
			<img src="data:image/svg+xml;base64,<?php
			$dm = new TCPDF2DBarcode(chr(232).'019931265099999891'.$aid.chr(29).'420'.$label->cnee->postcode.chr(29).'8008'.date('ymdHis'), 'DATAMATRIX');
			echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(8, 8, 'black')));
			?>" width="110" style="position:absolute;"/>
			<?php endif; ?>
			</td>
		</tr>
		<?php if (!in_array($label->agent_id, [1206])):?>
		<tr><td><b style="font-size: 1.4em; border: 3px solid black; padding:0 20px;margin-left: 2px;"><?php
				$state='NSW';
				if (preg_match("/^(33EVH|33FKB|33PE9|33G7L)\d{7}/", $label->ref)) {
				 	$state="VIC";
				} elseif (preg_match("/^(33EVJ|33PET)\d{7}/", $label->ref)) {
					$state="QLD";
				} elseif (preg_match("/^(33PEH)\d{7}/", $label->ref)) {
					$state = 'WA';
				}
				echo $state;
			?></b></td></tr>
			<?php endif;?>
		</table>
		</td>
	</tr>
</table>
<hr />

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:5px 0;">
	<tr>
		<td valign="top" width="550" height="60"><b>DELIVERY INSTRUCTIONS</b></td>
		<td align="right" valign="top"><b style="font-size:1.4em;"><?=round($label->weight/$label->pkg, 2);?>kg</b></td>
	</tr>
</table>
<hr />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="400" valign="top" style="border-right: 1px solid; padding-top: 10px; padding-bottom: 10px"><b>SIGNATURE ON DELIVERY</b></td>
		<td align="right" valign="top" style="padding: 10px 0"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="50">CON NO</td>
		<td align="center" valign="top"><?=$label->ref;?></td>
	</tr>
	<tr>
		<td width="50"><b>PARCEL</b></td>
		<td align="center" valign="top"><?=$pkg_sn.'/'.$label->pkg;?></td>
	</tr>
</table></td>
	</tr>
</table>
<hr />
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 10px 0">
	<tr><td>
		<p align="center" style="font-size:1.25em;">AP Article Id: <?=$aid;?></p>
		<div style="width:620px; margin:0 auto;"><?php
	$bc = new TCPDFBarcode(chr(241).'019931265099999891'.$aid, 'C128');
	echo '<p style="padding:5px 0"><img src="data:image/png;base64,'.base64_encode($bc->getBarcodePngData(2, 120)).'" /></p>';
	?></div>
	<p align="center" style="font-size:1.25em;">AP Article Id: <?=$aid;?></p>
	</td></tr>
</table>
<hr />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="270" height="130" rowspan="1" style="border-right: 1px solid; font-size: 0.95em; padding-top: 10px; padding-bottom: 10px" valign="top"><b>SENDER</b><br />
		<?php
		$oc = OrgContact::model()->find('org_id = :id AND status = 1 AND func & 8 > 1', [':id' => $label->agent_id]);
		$name=$default_rts? 'PCA Express' : $label->cnor->name;
		$org=$label->agent;
		if (!empty($org->extra['delivery_label_name'])) {
		 	$name=$org->extra['delivery_label_name'];
		}
		$sender_name = $name;
		$ssn1 = false;
		$warehouseAddress = "PO Box 75 <br />KINGSGROVE NSW 1480";
		if(!empty($label->mdata['chargecode']) && (strtotime($label->created) > strtotime('2019-08-01')) && in_array($label->mdata['chargecode'], include(Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'d2z_return_beverly_cc.php'))){
			$warehouseAddress = 'PO BOX 300<br />BEVERLY HILLS NSW 2209';
		}
		if (empty($oc) || $default_rts):
		?>
		<?=$name;?><br />
		<?=$warehouseAddress?>
		</p>
		<?php else:
			echo $oc->name, '<br />',
			$oc->address, '<br />',
			$oc->suburb, ', ', $oc->state, ' ', $oc->postcode;
		endif; ?>
		</td>
		<td valign="top" style="font-size: 16px; padding-top: 10px; padding-bottom: 10px">
		<b>Aviation Security and Dangerous Goods Declaration</b>
<p>The sender acknowledges that this article may be carried by
air and will be subject to aviation security and clearing
procedures; and the sender declares that the article does not
contain any dangerous or prohibited goods, explosive or
incendiary devices. A false declaration is a criminal offence.</p>
	</td>
	</tr>
	<tr>
	<td valign="top" height="60" style="border-top: 1px solid;font-size: 0.9em;">
	<p>Ref: <span style="font-size:1.3em"><?=$label->hbn?></span></p>
	<div style="width:220px; margin:0;"><?php
	$bc = new TCPDFBarcode($label->ref, 'C128');
	echo '<p style="padding:5px 0"><img src="data:image/png;base64,'.base64_encode($bc->getBarcodePngData(2, 40)).'" /></p>';
	?></div>
		</td>
		<td valign="top" height="60"  style="border-top: 1px solid;">
		<?=$ssn1? $sender_name.'<br />' : '';?>
		<?php if (!empty($label->mdata['chargecode'])&& in_array($label->mdata['chargecode'], [9270,3777,6001,8966,6124,4561,8991,1209,5186])):?>
			<div style="width: 300px; border: 3px solid black;padding:2px; margin-left: 5px;">
			<p style="font-size:30px;font-weight: bold">Road Transport Only</p>
			<p style=" width: 250px;margin:0 auto;font-weight: bold">Not to be moved by air</p>
			</div>
		<?php endif;?>
		<?php
			if (!empty($label->mdata['show_sku'])) {
				if (!empty($label->eitems['sku'][0])) {
					echo 'sku1: ' . mb_substr($label->eitems['sku'][0], 0, 25);
				}
				if (!empty($label->eitems['sku'][1])) {
					echo '<br/>sku2: ' . mb_substr($label->eitems['sku'][1], 0, 25);
				}
				if (!empty($label->eitems['sku'][2])) {
					echo '<br/>sku3: ' . mb_substr($label->eitems['sku'][2], 0, 25);
				}
			}
		?>
		</td>
	</tr>
</table>
<?php if (!empty($label->mdata['cust_ref2'])):?>
<div style="font-size:1.2em;padding-top:0.2em; font-weight: bold; text-align: center;"><?= strtoupper(substr($label->mdata['cust_ref2'], 0, 50))?></div>
<?php endif; ?>
