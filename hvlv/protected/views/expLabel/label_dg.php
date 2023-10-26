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
body{ font-family: "Microsoft YaHei", Verdana, Geneva, sans-serif; font-size: 22px; text-rendering: optimize-speed; width: 700px; background: #fff; transform: scale(0.6);transform-origin: 0 0;}
table, p{ font-size: 21px; }
p.logo { text-align: center; padding-bottom: 30px; }
.barcode{ height:60px; overflow: hidden;}
h4 { font-size: 26px; }
small { font-size: 16px; }
hr { border: none; border-bottom: 1px solid #000; }
.cnee {font-size: 1.2em;}
td.brt { border-right: 1px solid #000;}
td.btp { border-top: 1px solid #000;}
td.p10 { padding: 10px;}
</style>
</head>
<body width="700">
<div style="border: 1px #000 solid;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td class="p10" width="250"><img src="<?=$burl?>/images/dg_logo.png" width="200" height="85" /></td>
<td class="p10" align="center" valign="middle"><div class="barcode"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128B');
	echo $bc->getBarcodeHTML(2, 60);
	?></div><?=$p->ref;?></td>
</tr><tr>
<td align="center" width="250" class="p10 btp brt" style="font-size:2em">DG301</td>
<td align="center" class="btp p10" style="padding-top:0;"><span style="font-size: 3em; font-weight: bold;"><?php $fjm = jjFJM::model()->getCode($p->cnee); echo $fjm['FJBM'];?></span><br /><span style="font-size:1.5em"><?=($fjm['SFMC'] == $fjm['CSMC']? $fjm['SFMC'] : $fjm['SFMC'].$fjm['CSMC']);?></span></tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td class="p10" width="50%"><b>收件</b>：<span class="cnee"><?=$p->cnee->name;?></span></td>
<td class="p10"><span class="cnee"><?=$p->cnee->tel;?></span></td>
</tr>
<tr>
<td height="65" valign="top" colspan="2" class="p10" style="padding-top:0">
<div class="cnee" style="overflow: hidden; height: 65px;">
<?=$p->cnee->getCnFullAddress();?> <?=$p->cnee->postcode;?>
</div></td>
</tr>
<tr>
<td width="50%" class="btp p10"><div style="height:25px; overflow:hidden;">寄方： <?=$p->cnor->name;?></div></td>
<td class="btp p10"><?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?></td>
</tr>
<tr>
<td height="50" valign="top" colspan="2" class="p10" style="padding-top:0">地址： <?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?></td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td class="p10 brt" width="32%">重量: <?=$p->shipWeight();?></td>
<td class="p10 brt" width="32%">件数: 1</td>
<td class="p10" style="font-weight:bold;font-size:1.4em">已验视 &nbsp; 已安检</td>
</tr>
<tr>
<td colspan="2" valign="top" class="p10 btp brt">内件描述：
<div style="overflow: hidden; height: 80px;">
<?php
$gd = [];
foreach($p->eitems['g'] as $i => $g){
    $gd[] = $g.'*'.$p->eitems['q'][$i];
}
echo implode(', ', $gd);
$whd = $p->genWHD();
?>
</div>
</td>
<td class="p10 btp">
<p>收件人签名:</p>
<br />
<br />
<p align="right">年 &nbsp; 月 &nbsp; 日</p>
</td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="p10"><div style="height:25px; overflow:hidden;">寄方： <?=$p->cnor->name;?></div></td>
<td class="p10"><?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?></td>
</tr>
<tr>
<td height="40" valign="top" colspan="2" class="p10" style="padding-top:0">地址： <?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?></td>
</tr>
<tr>
<td class="btp p10" width="50%"><b>收方</b>：<?=$p->cnee->name;?></td>
<td class="p10 btp"><?=$p->cnee->tel;?></td>
</tr>
<tr>
<td valign="top" colspan="2" class="p10" style="padding-top:0">
<div style="overflow: hidden; height: 55px;">
<?=$p->cnee->getCnFullAddress();?> <?=$p->cnee->postcode;?>
</div></td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding:10px; margin:0;" align="center"><div class="barcode"><?=$bc->getBarcodeHTML(2, 50);?></div><div class="connote"><?=$p->ref;?></div></td><td style="font-size: 2em;">全程陆运</td>
</tr>
<tr><td colspan="2" style="padding: 0 10px;">退件地址：福建省晋江市内坑镇陆地港快件中心201室</td></tr>
</table>
</div>
</body>
</html>
