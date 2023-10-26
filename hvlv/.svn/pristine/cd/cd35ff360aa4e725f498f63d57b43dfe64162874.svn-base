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
@font-face {
	font-family: 'code39';
	src: url('<?=$burl?>/css/fonts/code39.eot');
	src: url('<?=$burl?>/css/fonts/code39.eot?#iefix') format('embedded-opentype'),
		 url('<?=$burl?>/css/fonts/code39.woff2') format('woff2'),
		 url('<?=$burl?>/css/fonts/code39.woff') format('woff'),
		 url('<?=$burl?>/css/fonts/code39.ttf') format('truetype'),
		 url('<?=$burl?>/css/fonts/code39.svg#code39') format('svg');
	font-weight: normal;
	font-style: normal;
}
@media print {
	@page {
		size: 100mm 150mm;
		margin: 5mm;
	}
	body{ transform: none; width: 100mm; height: 150mm; }
}
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: "Microsoft YaHei", Verdana, Geneva, sans-serif; font-size: 22px; text-rendering: optimize-speed; width: 700px; background: #fff; transform: scale(0.6);transform-origin: 0 0;}
table, p{ font-size: 21px; }
p.logo { text-align: center; padding-bottom: 30px; }
.barcode{ text-align: center; height:80px; overflow: hidden;}
.connote{ font-size: 28px; }
h4 { font-size: 26px; }
small { font-size: 16px; }
hr { border: none; border-bottom: 1px solid #000; }
.cnee {font-size: 1.2em;}
</style>
</head>
<body width="700">
<div style="border: 1px #000 solid;">
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="250"><img src="<?=$burl?>/images/yh_logo.jpg" height="80" /></td>
<td align="center" valign="middle"><?php
$bc = new TCPDFBarcode($p->ref, 'C128');
echo $bc->getBarcodeSVGcode(2.5, 40, 'black');
?><br />
<?=$p->ref;?></td>
</tr>
</table>
</div>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="40%">收件人：<span class="cnee"><?=$p->cnee->name;?></span></td>
<td width="40%">电话：<span class="cnee"><?=$p->cnee->tel;?></span></td>
<td>国家：<span class="cnee">中国</span></td>
</tr>
<tr>
<td valign="top" colspan="3" style="padding-top:10px;"><div class="cnee" style="overflow: hidden; height: 60px;">
地址：<?=$p->cnee->getCnFullAddress();?>
</div></td>
</tr>
<tr>
<td colspan="3" style="padding-top:10px;">邮编：<?=$p->cnee->postcode;?></td>
</tr>
</table>
</div>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="40%">发件人： <?=$p->cnor->name;?></td>
<td width="40%">电话： <?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?></td>
<td>国家：澳洲</td>
</tr>
<tr>
<td valign="top" colspan="3" style="padding-top:10px;"><div style="overflow: hidden; height: 55px;">地址： <?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?></div></td>
</tr>
</table>
</div>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="40%">价值：<?=sprintf('%.02f', round($p->getDvalue()));?></td>
<td width="40%">重量：<?=$p->shipWeight();?>kg</td><td>件数：1</td>
</tr>
</table>
</div>
<hr class="dashed" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding:10px;">内件信息：
<div style="overflow: hidden; height: 100px;">
<?php
$gd = [];
foreach($p->eitems['g'] as $i => $g){
    $qty = $p->eitems['q'][$i];
    $gd[] = $g.'*'.sprintf('%1d', $qty);
}
echo implode('; ', $gd);
$whd = $p->genWHD();
?>
</div>
</td>
<td valign="top" width="30%" style="padding:10px;line-height: 30px;border-left:1px solid #000;" rowspan="2">
<p>日期：<?=date('Y-m-d');?><br/>
原寄地: 悉尼<br />
目的地: <?=$p->cnee->state;?><br />
签收人：</p>
</td>
</tr>
<tr><td align="center" style="padding:10px; font-size: 1.5em;">已验视</td></tr>
</table>
<hr class="dashed" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="250" style="padding:10px;"><img src="<?=$burl?>/images/yh_logo.jpg" height="70" /></td>
<td valign="top" style="padding:10px; margin:0;" valign="top"><div style="text-align:center;"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128');
	echo $bc->getBarcodeSVGcode(2.5, 50);
	?><br /><?=$p->ref;?></div></td>
</tr>
</table>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%">收件人：<?=$p->cnee->name;?></td>
<td>电话：<?=$p->cnee->tel;?></td>
</tr>
<tr>
<td valign="top" colspan="2" style="padding-top:10px;"><div style="overflow: hidden; height: 50px;">
地址：<?=$p->cnee->getCnFullAddress();?>
</div></td>
</tr>
</table>
</div>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%">发件人：<?=$p->cnor->name;?></td>
<td>电话：<?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?></td>
</tr>
<tr>
<td valign="top" colspan="3" style="padding-top:10px;"><div style="overflow: hidden; height: 25px;">地址： <?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?></div></td>
</tr>
</table>
</div>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" style="padding: 5px 0;">关联单号：<b><?=$p->hbn;?></b></td>
<td><?php
	$bc = new TCPDFBarcode($p->hbn, 'C128');
	echo $bc->getBarcodeSVGcode(2, 40);
	?></td>
</tr>
</table>
</div>
</body>
</html>
