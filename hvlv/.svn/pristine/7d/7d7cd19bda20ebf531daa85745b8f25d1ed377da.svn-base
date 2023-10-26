<?php
//opcache_invalidate(__FILE__);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$aid = $label->ref.sprintf('%02s', $pkg_sn).'00060'.'02'.'0';
$aid .= AusPostAPI::aidChkDgt($aid);
?>
<div style="font-family: Helvetica, Verdana, Geneva, sans-serif; font-size: 20px;">
<div style="text-align:center; padding: 15px;"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/eparcel_header.svg" width="600" /></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" height="200">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="200" style="padding:0"><b style="font-size:1.2em;">DELIVER TO</b></td>
		<td valign="top" style="font-size:1.1em;" style="padding:0"><b>PHONE:</b> <?=$label->cnee->tel;?></td>
	</tr>
</table>
<p style="font-size:1.4em"><?=$label->cnee->name;?><br />
<?=$label->cnee->fullAddress();?>
		</td>
		<td width="160" valign="top" align="right">
		<?php
		//$dm = new TCPDF2DBarcode(chr(232).'019931265099999891'.$aid.chr(29).'420'.$label->cnee->postcode.chr(29).'8008'.date('ymdHis'), 'DATAMATRIX');
		//echo $dm->getBarcodeSVG(4, 4, 'black');
		?>
		</td>
	</tr>
</table>
<hr />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" width="550" height="100"><b>DELIVERY INSTRUCTIONS</b>
		<p><small>IF PREMISES UNATTENDED, PLEASE LEAVE AT LOCAL POST OFFICE WITH CARD NOTIFYING CUSTOMER. DO NOT LEAVE AT FRONT DOOR.</small></p></td>
		<td align="right" valign="top"><b style="font-size:1.4em;"><?=round($label->weight/$label->pkg,2);?>kg</b></td>
	</tr>
</table>
<hr />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="400" valign="top" style="border-right: 1px solid;"><b>SIGNATURE ON DELIVERY REQUIRED</b></td>
		<td align="right" valign="top" style="padding:0"><table width="100%" border="0" cellspacing="0" cellpadding="0">
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
<?php
//2014 format AID
//$aid = $label->ref.sprintf('%02s', $pkg_sn).'02'.'2';
$aid = $label->ref.sprintf('%02s', $pkg_sn).'50'.'2';
$aid .= AusPostAPI::aidChkDgt($aid).'0'.sprintf('%04s', $label->cnee->postcode);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><td><p align="center" style="font-size:1.25em;">AP Article Id: <?=$aid;?></p></td></tr>
	<tr><td><?php
	//$bc = new TCPDFBarcode(chr(241).'019931265099999891'.$aid, 'C128');
	//echo $bc->getBarcodeSVG(2, 130, 'black');
	?><img height="125px" src="data:image/svg+xml;base64,<?php
$bc = new TCPDFBarcode('99700160'.$aid, 'C128');
echo base64_encode($bc->getBarcodeSVGcode(2, 100, 'black'));
?>" width="95%" style="margin:0 15px;" /></td></tr>
	<tr><td><p align="center" style="font-size:1.25em;">AP Article Id: <?=$aid;?></p></td></tr>
</table>
<hr />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="240" height="240" rowspan="2" style="border-right: 1px solid; font-size: 0.95em;" valign="top"><b>SENDER</b><br />
		<?php
		$oc = OrgContact::model()->find('org_id = :id AND status = 1 AND func & 8 > 1', [':id' => $label->agent_id]);
		if(empty($oc) || $default_rts):
		?>
		<?=$default_rts? 'PCA Express' : $label->cnor->name;?><br />
		6C The Crescent<br />
		Kingsgrove NSW 2208
		<?php else:
			echo $oc->name, '<br />',
			$oc->address, '<br />',
			$oc->suburb, ', ', $oc->state, ' ', $oc->postcode;
		endif; ?>
		</td>
		<td valign="top" style="font-size: 16px;">
		<b>Aviation Security and Dangerous Goods Declaration</b>
<p>The sender acknowledges that this article may be carried by
air and will be subject to aviation security and clearing
procedures; and the sender declares that the article does not
contain any dangerous or prohibited goods, explosive or
incendiary devices. A false declaration is a criminal offence.</p>
		</td>
	</tr>
	<tr>
		<td valign="top" style="border-top: 1px solid;">
	<?=$label->hbn;?><br />
	Ref: <?=$label->cref;?>
	<?php if(in_array($label->agent_id, [650]) && !empty($label->cref)){
		$bc = new TCPDFBarcode($label->cref, 'C128');
		echo '<p style="padding:5px 0">';
		$bc->getBarcodeSVG(2, 30, 'black');
		echo '</p>';
	}
	echo '<br>Internal use only';
            $bc = new TCPDFBarcode($label->ref.($label->pkg > 1? '-'.$pkg_sn : ''), 'C128');
	echo '<p style="padding:5px 0">', $bc->getBarcodeSVG(2, 40, 'black'), '</p>';
	//}
            ?>
		</td>
	</tr>
</table>
</div>