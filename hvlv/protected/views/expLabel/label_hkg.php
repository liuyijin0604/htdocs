<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 102 --page-height 152 -T 3 -R 3 -B 3 -L 3 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />
<title>PCA Express Label</title>
<?php
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
.barcode{ font-family: code39; font-size: 28px; text-align: center; height:80px; overflow: hidden;}
.connote{ font-size: 28px; }
h4 { font-size: 26px; }
small { font-size: 16px; }
hr { border: none; border-bottom: 1px solid #000; }
hr.dashed { border: none; border-bottom: 1px dashed #000; }
.cnee {font-size: 1.2em;}
</style>
</head>
<body width="700">
<div style="border: 1px #000 solid;">
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="250"><img src="<?=$burl;?>/images/PCAE_Logo.png" width="240" /></td>
<td align="center" valign="middle"><div class="barcode">*<?=$p->hbn;?>*</div><div class="connote"><?=$p->hbn;?></div></td>
</tr>
</table>
</div>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%"><div style="height:25px; overflow:hidden;">寄件人/From： <?=$p->cnor->name;?></div></td>
<td>电话/Tel： <?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?></td>
</tr>
<tr>
<td height="65" valign="top" colspan="2" style="padding-top:10px;">地址/Address： <?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?></td>
</tr>
</table>
</div>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%"><b>收件人/To</b>：<span class="cnee"><?=$p->cnee->name;?></span></td>
<td><b>电话/Tel</b>：<span class="cnee"><?=$p->cnee->tel;?></span></td>
</tr>
<tr>
<td height="65" valign="top" colspan="2" style="padding-top:10px;"><b>地址/Address</b>：<br />
<div class="cnee" style="overflow: hidden; height: 90px;">
<?=$p->cnee->getCnFullAddress();?>
</div></td>
</tr>
</table>
</div>
<hr class="dashed" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding:10px;" colspan="2" rowspan="3">内件描述/<b>Name &amp; Description of Contents</b>：
<div style="overflow: hidden; height: 240px;">
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
</tr>
</table>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td height="90" valign="top">订单号/Order Number: </td>
</tr>
</table>
</div>
<hr class="dashed" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding:10px; margin:0;" valign="top"><div style="text-align:center;"><div class="barcode">*<?=$p->hbn;?>*</div><div class="connote"><?=$p->hbn;?></div></div></td>
</tr>
</table>
</div>
<script type="text/javascript">
window.print();
</script>
</body>
</html>
