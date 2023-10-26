<?php
//opcache_invalidate(__FILE__);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$barcodes =  $label->getParcelBarcode(null);
$articleId= $barcodes[$pkg_sn-1];
$sortcode = DFEAPI::getSortcode($label);
$qrcodeData = 'D2'.substr($label->ref, 0,5).'1'.$label->ref.$articleId.str_pad($label->cnee->name,20,' ').str_pad($label->cnee->address,60,' ').str_pad($label->cnee->suburb,20,' ').str_pad($label->cnee->state,3,' ').str_pad($label->cnee->postcode,4,' ').str_pad("",15,' ');
$qrcodeData .= empty($label->mdata['is_dg'])? 'N   ': 'Y   ';
$qrcodeData .= str_pad($label->pkg."",4," ").str_pad($label->weight."",5," ").str_pad($label->getTotalCBM(),6," ").str_pad("",3," ").str_pad("",5," ").str_pad("",6," ").str_pad(round($label->weight/$label->pkg,3),5," ").str_pad(round($label->cbm,3),6," ").str_pad('Item',8," ");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: -10px;">
	<tr>
		<td colspan="2">
			<p style="width:100%">DIRECT FREIGHT EXPRESS</p>
			<div style="width:100%;display:block;background-color:#071B6D;height:7px;">
	&nbsp;
</div>
<div style="width:100%;display:block;background-color:#F48132;margin-top:2px;height:7px;border-radius:0px 0px 12px 12px;">
	&nbsp;
</div>
<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/dfe_logo.jpg" width="120" style="margin-top: -2.0em;margin-left:22em;" />
		</td>
	</tr>
</table>
<div style="margin: 0 20px;font-weight:bold;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="35%" valign="top" style="padding: 35px 0 0 0">
				<div style="position: absolute; font-size:3.5em; center; -webkit-transform:rotate(-90deg);margin-top: 30px; margin-left: -40px; "><?=$sortcode['tail']?></div>
				<div style="margin-left: 100px;">
				<p style="font-size: 6em; line-height: 0.8em;"><?=$sortcode['head']?></p>
				<p style="font-size: 3em;"><?=$sortcode['middle']?></p>
				</div>
			</td>
			<td width="65%" align="right" valign="top" style="padding: 0">
				<img src="data:image/svg+xml;base64,<?php
				$dm = new TCPDF2DBarcode($qrcodeData, 'QRCODE,L');
				echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(12, 12, 'black')));
				?>" width="200" height='200' style="margin-left:-40px;padding: 16px;" />
			</td>

		</tr>
	</table>
</div>
<div style="margin: 0 30px; border: 2px #000 solid; border-left: none; border-right: none; font-size: 16px; padding: 10px; ">
	<p style="font-size:1.4em;"><?=ucwords(strtolower($label->cnee->name)).(empty($label->cnee->company) || $label->cnee->name == $label->cnee->company? '' : ', '.substr(ucwords(strtolower($label->cnee->company)), 0, 33)); ?><br/>
	</p>
	<p style="font-size:1.4em; word-break:break-all; word-wrap:break-word;" ><?= $label->cnee->address;?></p>
	<p style="font-size:1.5em;"><b><?=$label->cnee->suburb.' '.$label->cnee->state.' '.$label->cnee->postcode;?></b></p>
</div>
<div style="margin:0 30px; font-size: 14px;">
<?php if(!empty($label->mdata['is_dg'])):?>
<div style="position:absolute; left: 40px; margin-top: 10px; font-size:1.2em;padding: 2px 10px;line-height:2em;font-weight: bold; border:1px #000 solid;">Yes DG</div>
<?php endif;?>
<p style="font-size:1.5em;padding: 10px;text-align: right;">
	Ref: <?=preg_replace('/(\d{4})$/', '<b style="font-size:1.2em;">\\1</b>', $label->hbn);?><br />
	C/NOTE: <?=preg_replace('/(\d{4})$/', '<b style="font-size:1.2em;">\\1</b>', $label->ref);?>
</p>
</div>

<div style="margin: 0 30px; border:1px #000 solid;font-size: 16px;">
	<div style="padding: 10px;">
		<p>SPECIAL INSTRUCTIONS</p>
		<p>Please ring receiver before delivery</p>
		<hr style="margin: 10px 0;" />
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr >
				<td width="65%" valign="top" style="padding: 0 20px 0 0;">
					<p style="word-break:break-all; word-wrap:break-word;text-align: left;min-height: 1.1em">TO: <?=ucwords(strtolower($label->cnee->name)).(empty($label->cnee->company) || $label->cnee->name == $label->cnee->company? '' : ', '.substr(ucwords(strtolower($label->cnee->company)), 0, 33)); ?></p>
					<p style="word-break:break-all; word-wrap:break-word;text-align: left;min-height: 1.1em;"><?=$label->cnee->address ?></p>
					<p style ="min-height: 1.1em;"><?=$label->cnee->suburb." ".$label->cnee->state." ".$label->cnee->postcode?></p><br />
					<p>Sender Ref: <?=$label->cref?></p>
				</td>
				<td width="35%" valign="top" style="padding: 0">
					<p>Date: <?=date('d/m/Y',strtotime(@$label->mdata['connote_date']))?></p>
					<p>Qty: <b><?=$label->pkg?></b></p>
					<p>Kgs: <?=$label->weight?></p>
					<p>DIM: <?=!empty($label->mdata['dim'])?join(' ',$label->mdata['dim']):"0 0 0"?></p>
					<p>M3: <?=sprintf('%0.3f', $label->getTotalCBM());?></p>
				</td>
			</tr>
		</table>
		<hr style="margin: 10px 0;" />
		<p>Received in Good Order</p>
		<p style="display: inline-block;line-height:3em;">Signed: </p><p style="border-bottom: 1px #000 solid;width:430px;display: inline-block;"></p><br />
		<p style="display: inline-block;line-height:2em;">Print Name: </p><p style="border-bottom: 1px #000 solid;width:155px;display: inline-block;"></p> &nbsp; <p style="display: inline-block;">Date: </p><p style="border-bottom: 1px #000 solid;width:60px;display: inline-block;"></p>/<p style="border-bottom: 1px #000 solid;width:60px;display: inline-block;"></p>/<p style="border-bottom: 1px #000 solid;width:60px;display: inline-block;"></p>
	</div>
	<div style="margin-top: 10px;text-align: center;">
	<?php
		$bc = new TCPDFBarcode($articleId, 'C128');
		$bc->getBarcodeSVG(3, 140, 'black');
	?>
	<p style="font-size: 1.7em;"><?=preg_replace('/^(\d{6})(\d+)(\d{7})$/', '\\1<b style="font-size:1.2em;">\\2</b>\\3', $articleId)?></b></p>
	</div>
</div>
