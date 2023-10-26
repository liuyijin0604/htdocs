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
<td width="250"><img src="<?=$burl?>/images/zh_logo.png" height="110" width="150" /></td>
<td align="center" valign="middle"><img src="data:image/svg+xml;base64,<?php
$bc = new TCPDFBarcode($p->ref, 'C128');
echo base64_encode($bc->getBarcodeSVGcode(2, 40, 'black'));
?>" width="90%" /><br />
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
<td valign="top" colspan="3" style="padding-top:10px;"><div class="cnee" style="overflow: hidden; height: 70px;">
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
<td width="50%">申报价格：<?=sprintf('%.02f', round($p->getDvalue()));?> USD</td>
<td>重量：<?=$p->shipWeight();?>kg</td>
</tr>
</table>
</div>
<hr class="dashed" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding:10px;border-right:1px solid #000;">内件信息：
<div style="overflow: hidden; height: 160px;">
<?php
$gd = [];
foreach($p->eitems['g'] as $i => $g){
    $qty = $p->eitems['q'][$i];
    $gd[] = $g.'*'.sprintf('%01.2f', $qty);
}
echo implode('; ', $gd);
$whd = $p->genWHD();
?>
</div>
</td>
<td valign="top" width="25%" style="padding:10px;">
件数：1<br /><br />
签收人：
</td>
</tr>
</table>
<hr class="dashed" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="250"><img src="<?=$burl?>/images/zh_logo.png" height="90" width="140" /></td>
<td valign="top" style="padding:10px; margin:0;" valign="top"><div style="text-align:center;"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128');
	echo $bc->getBarcodeSVGcode(3, 60);
	?><br /><?=$p->ref;?></div></td>
</tr>
</table>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="40%">收件人：<?=$p->cnee->name;?></td>
<td width="40%">电话：<?=$p->cnee->tel;?></td>
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
<td width="40%">发件人：<?=$p->cnor->name;?></td>
<td width="40%">电话：<?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?></td>
</tr>
<tr>
<td valign="top" colspan="3" style="padding-top:10px;"><div style="overflow: hidden; height: 25px;">地址： <?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?></div></td>
</tr>
</table>
</div>
<hr class="dashed" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td colspan="2" valign="top" style="padding:10px;" valign="top" style="font-size:1.4em">备注：已验视 &nbsp; 单位：西区邮政局 &nbsp; 验视人: 罗德权</td>
</tr>
<tr>
<td style="padding: 5px 10px;">关联单号：<b><?=$p->hbn;?></b></td>
<!--td width="50%" align="right" style="padding:5px 10px;">官方网址：www.5ocean.cn</td-->
</tr>
</table>
</div>
</body>
</html>
