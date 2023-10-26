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
.barcode{ overflow: hidden;}
small{ font-size: 16px;}
hr { border: none; border-bottom: 1px solid #000;}
td.brt { border-right: 1px solid #000;}
td.blt { border-left: 1px solid #000;}
td.btp { border-top: 1px solid #000;}
td.p10 { padding: 10px;}
td.p5 { padding: 5px;}
</style>
</head>
<body width="700">
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td style="font-size: 1.5em; font-weight: bold;">快递包裹</td><td width="40%" height="60" valign="top" class="p10"><img src="<?=$burl;?>/images/cae_logo.png" width="260" /></td>
<td align="right" class="p10" valign="top"><span style="font-size:0.8em">400-317-4008<br />www.cae.com.cn</span><br /><span style="font-weight:bold; font-size:1.2em">投递部</span></td>
</tr>
</table>
<div style="border: 1px #000 solid;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td height="120" valign="middle" class="p5 brt" style="font-size:1.2em">收<br />件</td><td class="p10" valign="top" style="font-size: 1.2em;" colspan="2"><?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?><br /><?=$p->cnee->getCnFullAddress();?>
</td></tr>
<tr><td height="90" valign="middle" class="btp p5 brt" style="font-size:1.2em">寄<br />件</td><td valign="top" class="p10 btp brt">PCAE &nbsp; 0299257100<br />
6C The Crescent, Kingsgrove, NSW 2208</td><td rowspan="2" class="p10 btp" width="30%" valign="top"><div align="center" style="font-size:1.2em; margin: -10px; padding: 10px; margin-bottom: 10px; border-bottom: 1px solid #000">服务</div>重量: <?=$p->shipWeight();?>KG<br />价值:<br />数量: 1<br /><p style="font-size:1.5em; text-align:center; margin-top:10px;">已视验</p></td></tr>
<tr><td colspan="2" class="p10 brt btp"><div class="barcode"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128');
	echo $bc->getBarcodeHTML(3, 75);
	?></div><?=$p->ref;?></td></tr>
<tr><td colspan="2" class="p10 btp brt" style="font-size:0.8em">快件送达收件人地址，经收件人或收件人（寄件人）允许的代收人签字，视为送达。您的签字代表您已经签收此包裹，并确认商品信息无误，包装完好，没有划痕，破损的表面质量问题</td><td width="30%" class="p10 btp">收件人:<p style="margin-top:15px">签收时间:</p></td></tr>
</table>
<hr style="margin-bottom:40px;" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="30%" align="center" valign="middle" class="p10"></td>
<td align="center" class="p5"><div class="barcode"><?php
	echo $bc->getBarcodeHTML(3, 60);?></div><?=$p->ref;?></td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td height="100" valign="middle" class="p5 btp brt" style="font-size:1.2em">收<br />件</td><td class="p10 btp"><?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?><br /><?=$p->cnee->getCnFullAddress();?>
</td></tr>
<tr><td height="80" valign="middle" class="btp p5 brt" style="font-size:1.2em">寄<br />件</td><td valign="top" class="p10 btp brt">PCAE &nbsp; 0299257100<br />
6C The Crescent, Kingsgrove, NSW 2208</td></tr>
<tr><td colspan="2" class="p10 btp" style="position:relative;">
<div style="position:absolute; right: 20px; top: 40px;">清远城区大宗<br />胡细康<br /><span style="font-size:1.2em;">已视验</span></div><div style="overflow: hidden; height: 100px;">
<?php
$gd = [];
foreach($p->eitems['g'] as $i => $g){
    $gd[] = $g;
}
echo implode(', ', $gd);
?></div></td></tr>
</table>
</div>
<table width="100%" cellspacing="0" cellpadding="0" style="padding: 5px 10px">
<tr>
<td width="50%"><?=$p->hbn;?></td>
<td align="right">&nbsp;</td>
</tr>
</table>
<!--hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" height="90" class="p10 brt">备注：</td>
<td align="center" valign="middle" rowspan="2" width="160"><img src="<?=$burl;?>/images/ems_qr.png" width="150" /></td>
</tr>
<tr>
<td valign="top" align="center" class="brt btp" style="padding: 10px 0;">网址：www.ems.com.cn &nbsp;&nbsp; 客服电话：11183</td>
</tr>
</table-->
</body>
</html>
