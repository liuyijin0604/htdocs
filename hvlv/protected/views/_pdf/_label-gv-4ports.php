<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$aid = $label->mdata['article_id'];
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
		<tr><td><b style="font-size: 1.6em; border: 3px solid black; padding: 5px 45px; display: inline-block; margin: 0 5px 0 0;"><?php
				$zoneMap = ZoneMap::model()->find('org_id = 101 AND zone_id = 6 AND pc_lo <= :code AND pc_hi >= :code', [':code' => $label->cnee->postcode]);
				if(empty($zoneMap)){
					$state=substr($label->cnee->postcode, 0, 1);
				}else{
					switch($zoneMap->z1){
						case 'BNEMETRO':
						case 'BNEREGIONAL':
							$state = 4;
						break;
						case 'PERMETRO':
						case 'PERREGIONAL':
							$state = 6;
						break;
						case 'SYDMETRO':
						case 'SYDREGIONAL':
							$state = 2;
						break;
						case 'VICMETRO':
						case 'VICREGIONAL':
							$state = 3;
						break;
					}
				}
				echo $state;
			?></b></td></tr>
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
		$name='GVPCA - '.$label->agent_id;
		$org=$label->agent;
		$warehouseAddress = "38/756 Burwood Highway, Ferntree Gully VIC 3156";

		echo $name, '<br />',$warehouseAddress;
		?>
		</p>
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
	<p>Ref:&nbsp;&nbsp;<?=$label->hbn?></p>
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
