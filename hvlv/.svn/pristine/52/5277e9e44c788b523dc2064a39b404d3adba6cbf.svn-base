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
body{ font-family: "Microsoft YaHei", Verdana, Geneva, sans-serif; font-size: 22px; text-rendering: optimize-speed;  width: 700px; background: #fff; transform: scale(0.6);transform-origin: 0 0; }
.barcode{ height:55px; overflow: hidden;}
small{ font-size: 16px;}
hr { border: none; border-bottom: 1px solid #000;}
td.brt { border-right: 1px solid #000;}
td.blt { border-left: 1px solid #000;}
td.btp { border-top: 1px solid #000;}
.p10 { padding: 10px;}
</style>
</head>
<body width="700">
<div style="border: 1px #000 solid;margin-bottom:35px;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="40%" align="center" valign="middle"><p style="padding-bottom:10px;"><b style="font-size:1.6em;">快递包裹</b></p>天津东丽包裹局收寄</td>
<td align="center" class="p10" valign="top" width="60%"><div class="barcode"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128B');
	echo $bc->getBarcodeSVGcode(2, 55);
	?></div><?=$p->ref;?></td>
<td>&nbsp;</td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="40" height="150" class="brt" align="center" valign="center" style="font-size: 1.2em;"><b>收<br /><br />件</b></td>
<td valign="top" style="font-size: 1.2em;">
<div class="p10">
<span style="font-size:1.2em"><?=$p->cnee->name, '</span> &nbsp; ', $p->cnee->tel;?><br />
<?=$p->cnee->getCnFullAddress();?>
</div></td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td width="60%" valign="top" class="brt p10">详细内容:
<div style="overflow: hidden; height: 175px;">
<?php
$gd = [];
foreach($p->eitems['g'] as $i => $g){
    $gd[] = $g.'x'.$p->eitems['q'][$i];
}
echo implode(' ', $gd);
?></div>
<td valign="top" class="p10"><b style="font-size:1.2em">收件人/代收人：</b></td>
</tr>
<tr><td valign="top" class="brt p10">
重量: <?=$p->shipWeight();?>kg<br />
订单号：<?=$p->hbn;?>
</td>
<td valign="top" class="p10">
签收时间：<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;年 &nbsp;&nbsp;&nbsp;月 &nbsp;&nbsp;&nbsp;日 &nbsp;&nbsp;&nbsp;时
</td>
</tr>
</table>
</div>

<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="60%" align="center" valign="bottom" class="p10"><div class="barcode"><?php
	echo $bc->getBarcodeSVGcode(2, 55);?></div><?=$p->ref;?></td>
<td align="center" valign="bottom" class="p10" style="font-size:24px;font-weight:bold"><img src="<?=$burl;?>/images/cnpost_logo.png" width="220" /><br />快递包裹</td>
</tr>
</table>

<div style="border: 1px #000 solid;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="40" height="100" class="brt" align="center" valign="center"><b>收<br />件</b></td>
<td valign="top">
<div class="p10">
<?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?><br />
<?=$p->cnee->getCnFullAddress();?>
</div>
</td>
</tr>
<tr>
<td width="40" height="100" class="brt btp" align="center" valign="center"><b>寄<br />件</b></td>
<td valign="top" class="p10 btp">
<?=$p->cnor->name;?>, <?=$p->cnor->fullAddress()?></td>
<td valign="top" align="center" class="blt btp" width="160" rowspan="3"><img src="<?=$burl;?>/images/cnpost_qr.png" width="160" /></td>
</tr>
<tr>
<td valign="top" class="p10 btp" colspan="2">订单号：<?=$p->hbn;?><br />网址：www.11185.cn &nbsp;&nbsp; 客服电话：11185</td>
</tr>
</table>
<script type="text/javascript">
window.print();
</script>
</body>
</html>
