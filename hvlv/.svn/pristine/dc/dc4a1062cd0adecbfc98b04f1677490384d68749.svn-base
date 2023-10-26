<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$service_id=51;
if(!empty($label->mdata['article_id']))
{
	$aid = $label->mdata['article_id'];
}else
{
	$aid = $label->ref.sprintf('%02s', $pkg_sn).'00093'.$service_id.'0';
	$aid .= AusPostAPI::aidChkDgt($aid);
}
?>
<div style="font-family: Helvetica, Verdana, Geneva, sans-serif; font-size: 20px;">
<div style="text-align:center; padding: 10px;"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/eparcel_header.svg" width="600" /></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom: 3px solid; padding-bottom: 10px">
	<tr>
		<td width="180" style="padding:0; padding-left:10px"><b style="font-size:1.2em;">DELIVER TO</b></td>
		<td valign="top" style="font-size:1.2em; padding:0"><b>PHONE:</b> <?=$label->cnee->tel;?></td>
	</tr>
	<tr>
		<td valign="top" height="100">
		<p style="font-size:1.3em"><?=ucwords(strtolower($label->cnee->name));?>
			<?php if (!empty($label->cnee->company) && $label->cnee->company != $label->cnee->name && !preg_match('/' . $label->cnee->name . '/i', $label->cnee->company)) {
				echo ", ".substr(ucwords(strtolower($label->cnee->company)), 0, 33);
		}?><br />
		<?=join("<br/>",explode(",",$label->cnee->address));?><br/>
		<?=strtoupper($label->cnee->suburb.' '.$label->cnee->state.' '.$label->cnee->postcode);?>
		</td>
		<td width="160" valign="top" height="180">
			<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
			<tr><td valign="top" height="120" align="right" width="100%" style="padding-right: 20px;">
			<?php if (empty($label->mdata['receipted'])):?>
				<img src="data:image/svg+xml;base64,<?php
				$dm = new TCPDF2DBarcode(chr(232).'019931265099999891'.$aid.chr(29).'420'.$label->cnee->postcode.chr(29).'8008'.date('ymd').'120000', 'DATAMATRIX');
				echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(8, 8, 'black')));
				?>" width="140" />
				<?php endif; ?>
				</td>
			</tr>
			<?php if (!in_array($label->agent_id, [1206])):?>
			<tr><td align="right"><b style="font-size: 1.8em; border: 3px solid black; padding:0 40px;margin-left: 2px;"><?php
					if(!empty($label->mdata['facility']))
					{
						if(!empty($label->mdata['sort_code']))
						{
							$state=$label->mdata['sort_code'];
						}else
						{
							$state=explode('WW',$label->mdata['facility'])[0];
						}
					}else
					{
						$state='NSW';
						if (preg_match("/^(33EVH|33FKB|33PE9|SJU|33A93|33G7L)\d{7}/", $label->ref)) {
						 	$state="VIC";
						} elseif (preg_match("/^(33EVJ|33PET|349PU)\d{7}/", $label->ref)) {
							$state="QLD";
						} elseif (preg_match("/^(33PEH|349PV)\d{7}/", $label->ref)) {
							$state = 'WA';
						}
					}
					echo $state;
				?></b></td></tr>
				<?php endif;?>
			</table>
		</td>
	</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-top: 10px; padding-bottom: 10px;margin-top:0px; border-bottom: 3px solid">
	<tr style="padding-bottom: 0; margin-bottom: 0">
		<td style="padding-bottom: 0; margin-bottom: 0"><b>DESCRIPTION</b></td>
		<td align="right" valign="middle" rowspan="2" style="padding-top: 0; margin-top: 0; padding-right: 20px"><b style="font-size:1.4em;"><?=round($label->weight/$label->pkg, 2);?>kg</b></td>
	</tr>
	<tr style="padding-top: 0; margin-top: 0">
		<td valign="top" width="550" style="padding-top: 0; margin-top: 0"><b>DELIVERY INSTRUCTIONS</b></td>
	</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr style="padding-bottom: 0; margin-bottom: 0">
		<td width="400" valign="top" style="padding-bottom: 0; margin-bottom: 0"><b>SIGNATURE ON DELIVERY REQUIRED</b></td>
		<td style="padding-bottom: 0; margin-bottom: 0">CON NO <?=$label->ref;?></td>
	</tr>
	<tr style="padding-top: 0; margin-top: 0">
		<td style="padding-top: 0; margin-top: 0"></td>
		<td style="padding-top: 0; margin-top: 0"><b>PARCEL</b> <?=$pkg_sn.'/'.$label->pkg;?></td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: -20px;">
	<tr><td>
		<p align="center" style="font-size:1.25em;"><b>AP Article Id: <?=$aid;?></b></p>
		<div style="width:620px; margin:0 auto;"><?php
	$bc = new TCPDFBarcode(chr(241).'019931265099999891'.$aid, 'C128');
	echo '<p style="padding:5px 0;width:100%;"><img src="data:image/png;base64,'.base64_encode($bc->getBarcodePngData(2, 165)).'" /></p>';
	?></div>
		<p align="center" style="font-size:1.25em;"><b>AP Article Id: <?=$aid;?></b></p>
	</td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="280" style="width:280px;border-right: 3px solid; font-size: 0.8em; padding: 0 10px" valign="top"><b>Return Address (If undeliverable)</b><br />
		<?php
		$returnAddress = $label->getReturnAddress(false,$default_rts,true);
		if(empty($label->withoutSender))
		{
			echo $returnAddress["name"]."<br/>";
			echo $returnAddress["fullAddress"];
		}
		?>
		<div style="min-height:150px">
		Ref: <span style="font-size: 1.3em;"><?=$label->cref;?></span>
		<?php if (!empty($label->mdata['cust_ref1'])) {
			echo '<br/>Ref1: '.$label->mdata['cust_ref1'];
		}?>
		<p style="padding: 10px 0 0 5px;">
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
		?></p>
		</div>

		</td>
		<td valign="top" style="font-size: 16px; padding: 0">
			<p style="padding: 10px; font-size: 12px;">Aviation Security and Dangerous Goods Declaration: The sender acknowledges that this article may be carried by air and will be subject to aviation security and clearing procedures; and the sender declares that the article does not contain any dangerous or prohibited goods, explosive or incendiary devices. A false declaration is a criminal offence.</p>

			<p style="width:90%;border-bottom: 3px solid; padding-bottom: 20px; text-align: center;padding-left: 20px;">
				
			</p>
			<p style="padding-left: 5px; padding-top: 5px">Order No: <span style="font-size:1.2em"><?=$label->hbn?></span></p>
			<p style="padding-left: 5px; padding-top: 5px">SKU No: <span style="font-size:1.2em"></span></p>
		</td>
	</tr>
</table>
