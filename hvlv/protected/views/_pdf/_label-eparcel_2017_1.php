<?php
//opcache_invalidate(__FILE__);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$aid = empty($label->mdata['receipted'])? $label->ref.sprintf('%02s', $pkg_sn).'00093'.'50'.'0' : '57'.$label->ref.'13';
$aid .= AusPostAPI::aidChkDgt($aid);
?>
<div style="font-family: Helvetica, Verdana, Geneva, sans-serif; font-size: 20px; position: relative;">
<div style="padding: 5px 0 0px 0;"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/parcelpost.svg" width="70%" /></div>

<table width="71%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" width="90%;">
		<b style="font-size:1.4em;">To:</b>
<div style="border: 4px solid #E5DBCC;margin-bottom:10px;">
<div style="height:180px; overflow:hidden">
<p style="font-size:1.4em;padding: 5px;"><?=ucwords(strtolower($label->cnee->name));?><br />
<?=nl2br($label->cnee->apAddress());?></p>
</div>
<hr style="border-top: 4px solid #E5DBCC;" />
<p style="font-size:1.4em;padding: 5px;">Phone: <?=$label->cnee->tel;?></p>
<hr style="border-top: 4px solid #E5DBCC;" />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" width="30%" style="border-right: 4px solid #E5DBCC;">
Dead weight<br />
<p align="center"><b style="font-size:1.5em;line-height:45px"><?=round($label->weight/$label->pkg, 2);?>kg</b></p>
</td>
		<td valign="top">Delivery features<br />
		<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/sign.svg" width="50" style="margin-top:5px;" />
		<div style="position:absolute; font-size:0.9em; margin-left:58px; margin-top:-42px;"><?=empty($label->mdata['receipted'])? 'Sign on' : 'Receipted';?><br />delivery</div>
		</td>
	</tr>
</table>
</div>

<div style="border: 4px solid #E5DBCC; margin-bottom:15px;margin-top:10px; height: 605px;">
<div style="height:165px;position:relative;left:230px;top:50px; overflow:hidden;">
	<p style="padding-top:11px"><b style="font-size:0.8em;">From:</b></p>
<p style="font-size:0.8em;padding: 5px;"><?php
		$oc = OrgContact::model()->find('org_id = :id AND status = 1 AND func & 8 > 1', [':id' => $label->agent_id]);
		if (empty($oc) || $default_rts):
		?>
		<?=$default_rts? 'PCA Express' : $label->cnor->name;?><br />
		6C The Crescent<br />
		Kingsgrove NSW 2208
		<?php else:
			echo $oc->name, '<br />',
			$oc->address, '<br />',
			strtoupper($oc->suburb. ' '. $oc->state. ' '. $oc->postcode);
				endif; ?><br>Buyer is Not to return in person</p>
</div>
<hr style="border-top: 4px solid #E5DBCC;" />
<div style="padding:5px;">
<b style="color:#D81E05;font-size:0.9em;">Aviation Security and Dangerous Goods Declaration</b><br />
<p style="line-height:18px;font-size:0.92em;">The sender acknowledges that this article may be carried by air and
will be subject to aviation security and clearing procedures; and the
sender declares that the article does not contain any dangerous or
prohibited goods, explosive or incendiary devices. A false declaration
is a criminal offence.</p>
</div>
<hr style="border-top: 4px solid #E5DBCC;" />

<hr style="border-top: 4px solid #E5DBCC;" />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" width="63%" style="border-right: 4px solid #E5DBCC;">CON NO: <?=empty($label->mdata['receipted'])? $label->ref : $aid;?></td>
		<td valign="top">PARCEL: <?=$pkg_sn.'/'.$label->pkg;?></td>
	</tr>
</table>
	<hr style="border-top: 4px solid #E5DBCC;" />
<div style="height:135px;overflow:hidden;"><p style="padding:5px;">
<?=$label->hbn;?><br />
<?php
	// show RTS tranship original parcel No.
	if (isset($label->mdata['rts_org_no'])) {
		echo $label->mdata['rts_org_no'] . '(RTS Original)<br/>';
	}
?>

	Ref: <?=$label->cref;?><?php if (in_array($label->agent_id, [650])) {
	$bc = new TCPDFBarcode($label->cref, 'C128');
	echo '<p style="padding:5px 0">';
	$bc->getBarcodeSVG(2, 40, 'black');
	echo '</p>';
}?>
</p>
<div style="position: absolute; bottom: 40px;">
	<p>Internal use only</p>
	<p style="padding:5px;">
		<?php
		$bc = new TCPDFBarcode(empty($label->mdata['receipted'])? $label->hbn.($label->pkg > 1? '-'.$pkg_sn : '') : '997001'.$aid, 'C128');
		$bc->getBarcodeSVG(3, 50, 'black');
		?></p>
	</div>
</div>
</div>
		</td>

	</tr>
</table>


<div style="position: absolute;left:70px; top:450px;">
	<table><tr>
	<td width="200" valign="top" align="right">

		<?php
		if (empty($label->mdata['receipted'])):
			?>
			<img src="data:image/svg+xml;base64,<?php
			$dm = new TCPDF2DBarcode(chr(232).'019931265099999891'.$aid.chr(29).'420'.$label->cnee->postcode.chr(29).'8008'.date('ymdHis'), 'DATAMATRIX');
			echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(8, 8, 'black')));
			?>" width="120" style="position:absolute;"/>
		<?php endif; ?>
		<!--<p style="padding-top: 150px;padding-right: 60px;"><b style="font-size:0.95em;">Postage Paid</b></p>-->
	</td></tr>
		</table>
</div>
<div style="position: absolute;left:530px; top:0px;">
	<table><tr>
	<td width="200" valign="top" align="right">

		<?php
		if (empty($label->mdata['receipted'])):
			?>
			<img src="data:image/svg+xml;base64,<?php
			$dm = new TCPDF2DBarcode(chr(232).'019931265099999891'.$aid.chr(29).'420'.$label->cnee->postcode.chr(29).'8008'.date('ymdHis'), 'DATAMATRIX');
			echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(8, 8, 'black')));
			?>" width="120" style="position:absolute;"/>
		<?php endif; ?>
		<!--<p style="padding-top: 150px;padding-right: 60px;"><b style="font-size:0.95em;">Postage Paid</b></p>-->
	</td></tr>
		</table>
</div>

<div style="-webkit-transform: rotate(90deg); text-align:center;position:absolute;left: 130px; top:545px; width:950px;height:150px;overflow:hidden;">
<?php
$bc = new TCPDFBarcode((empty($label->mdata['receipted'])? chr(241).'019931265099999891' : '997001').$aid, 'C128');
//$bc = new TCPDFBarcode((empty($label->mdata['receipted'])? chr(241).'0199312650999998912NM100001901000930201' : '997001').$aid, 'C128');
echo '<div class="bc_center">', $bc->getBarcodeHTML(3, 250, 'black'), '</div>';
?>
<!--img src="data:image/svg+xml;base64,<?php
//echo base64_encode(str_replace(chr(241), '', $bc->getBarcodeSVGcode(2, 105, 'black')));
?>" height="120" /-->
</div>
<p align="center" style="-webkit-transform: rotate(90deg);white-space: nowrap;padding-top:6px; position: absolute;left:310px;top:600px;font-size:1.1em">AP Article Id: <?=$aid;?></p>
</div>
