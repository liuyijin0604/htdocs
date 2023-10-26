<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 102 --page-height 152 -T 3 -R 3 -B 3 -L 3 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />
<title>PCA Express Label</title>
<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
$burl = Yii::app()->request->hostInfo.Yii::app()->baseUrl;
?>
<style type="text/css">
@media print {  
	@page {
		size: 100mm 150mm;
		margin: 5mm;
	}
	body{ transform: none; width: 100mm; height: 150mm; }
}
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: "Microsoft YaHei", Verdana, Geneva, sans-serif; font-size: 21px; text-rendering: optimize-speed;  width: 700px; background: #fff; transform: scale(0.6);transform-origin: 0 0; }
.barcode{ height:50px; overflow: hidden;}
small{ font-size: 16px;}
hr { border: none; border-bottom: 1px dashed #000;}
td.brt { border-right: 1px dashed #000;}
td.btp { border-top: 1px dashed #000;}
td.p10 { padding: 10px;}
</style>
</head>
<body width="700">
<div style="border: 1px #000 dashed;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="40%" class="p10"><img src="<?=$burl;?>/images/yse_logo.png" width="160" height="100" /></td>
<td align="center" class="p10" valign="top"><div class="barcode" style="margin-top: 20px;"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128');
	echo $bc->getBarcodeSVGcode(3, 60);
	?></div><?=$p->ref;?></td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td width="60%" valign="top" height="110" class="p10 brt">收件： <?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?><br />
<?=$p->cnee->getCnFullAddress();?></td>
<td align="center" class="p10"><span style="font-size: 60px; font-weight: bold;"><?php $fjm = jjFJM::model()->getCode($p->cnee); echo $fjm['FJBM'];?></span><br /><span style="font-size:1.2em"><?=($fjm['SFMC'] == $fjm['CSMC']? $fjm['SFMC'] : $fjm['SFMC'].$fjm['CSMC']);?></span></td>
</tr>
<tr><td height="90" valign="top" colspan="2" class="btp p10" style="padding-top:10px;font-size: 1.2em;">寄件： <?=$p->cnor->name;?><br />
<?=$p->cnor->fullAddress()?>
</td></tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="40%" height="60" valign="top" class="p10 brt">签收人：<br /></td>
<td valign="top" class="p10">
签收时间： <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;年 &nbsp;&nbsp;&nbsp;&nbsp;月 &nbsp;&nbsp;&nbsp;&nbsp;日 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;时</td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
	<td width="65%" valign="top" class="brt p10" height="150">
	配货信息：<br/> 
<?php
$gd = [];
foreach($p->eitems['g'] as $i => $g){
    $gd[] = $g.'x'.$p->eitems['q'][$i];
}
echo implode(' ', $gd);?></td>
<td valign="top" class="p10">件数: 1 <br /><br /> 重量: <?=$p->shipWeight();?>kg<br /><br />日期: <?=date('Y-m-d')?></td>
</tr>
</table>
<hr />
<div style="padding-top:10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="90%" align="center" class="p10" height="70"><div class="barcode"><?php
	echo $bc->getBarcodeSVGcode(3, 50);?></div><?=$p->ref;?></td>
</tr>
</table>
</div>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" width="40%" class="brt p10" height="100">寄件： <?=$p->cnor->name;?><br />
<?=$p->cnor->fullAddress()?></td>
<td valign="top" class="p10">收件： <?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?><br />
<?=$p->cnee->getCnFullAddress();?></td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" height="40" class="p10 brt"><span style="font-size:0.8em">退货地址: </span>晋江市内坑镇晋江陆地港国际快件中心3楼310, 13328559878</td>
</tr>
</table>
</div>
<table width="100%" cellspacing="0" cellpadding="0" style="padding: 5px 10px">
<tr>
<td width="50%">关联单号: <?=$p->hbn;?></td>
<td align="right">&nbsp;</td>
</tr>
</table>
<script type="text/javascript">
window.print();
</script>
</body>
</html>
