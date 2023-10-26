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
hr { border: none; border-bottom: 1px dashed #000;}
td.brt { border-right: 1px dashed #000;}
td.blt { border-left: 1px dashed #000;}
td.btp { border-top: 1px dashed #000;}
td.p10 { padding: 10px;}
td.p5 { padding: 5px;}
</style>
</head>
<body width="700">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="25%" class="p10" style="padding-top:0"><img src="<?=$burl;?>/images/anzu_logo.png" height="70" width="150" /></td><td align="right" valign="bottom"><img src="<?=$burl;?>/images/ledex_logo.png" height="60" /></td></tr>
</table>

<div style="border: 1px #000 dashed;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td width="30%" align="center" class="p10"><span style="font-size:1.8em">快递包裹</span><!--img src="<?=$burl;?>/images/cnpost_logo.png" width="180" /--></td>
<td align="center" class="p10" valign="top"><div class="barcode"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128');
	echo $bc->getBarcodeSVGcode(3, 50);
	?></div><?=$p->ref;?></td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td width="72%" valign="top" height="80" class="p10 brt">寄件方：ANZU &nbsp; 0299257100<br />
6C The Crescent, Kingsgrove, NSW 2208</td>
<td class="p10">始发地<br/><span style="font-size:1.4em;">悉尼</span><br/><span style="font-size:0.8em;"><?=date('Y-m-d H:i');?></span></td>
</tr>
<tr><td height="90" valign="top" class="btp p10 brt" style="padding-top:10px;font-size: 1.2em;">收件方： <?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?><br />
<?=$p->cnee->getCnFullAddress();?>
</td><td valign="top" class="btp p10">目的地<br/><span style="font-size:1.2em;"><?=mb_substr($p->cnee->city, 0, 3);?></span></td></tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td colspan="3" class="p10">清关运单号: <?=$p->ref;?></td></tr>
<tr><td width="30%" class="p5 btp brt" align="center">件数: 1</td><td width="30%" class="p5 btp brt" align="center">重量: <?=$p->shipWeight();?>KG</td><td class="btp p5" align="center">申报价格: </td></tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td valign="middle" height="40" class="p5" width="60%" style="padding-left:10px;">如无法正常投递请退回原寄局处理</td><td class="p5" style="padding-left:25px"><span style="font-size:1.5em">已视验</span></td></tr>
<tr><td class="btp p10">收件方签名: </td><td class="btp">&nbsp;</td></tr>
<tr><td>&nbsp;</td><td class="p10" align="right">签收时间: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 年 &nbsp;&nbsp; 月 &nbsp;&nbsp; 日</td></tr>
</table>
<hr style="margin-bottom:20px;"/>
<div style="height:95px; overflow:hidden">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="30%" align="center" valign="top" class="p10"><span style="font-size:1.8em">快递包裹</span><!--img src="<?=$burl;?>/images/cnpost_logo.png" width="180" /--></td>
<td align="center" class="p5"><div class="barcode" style="height:60px;"><?php
	echo $bc->getBarcodeSVGcode(3, 60);?></div><?=$p->ref;?></td>
</tr>
</table>
</div>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td class="p10" valign="top" height="40">寄件方： ANZU &nbsp; 0299257100<br />
6C The Crescent, Kingsgrove, NSW 2208</td><?php
if($p->hasOriginTrace()):
?><td rowspan="2" class="blt" align="center"><span style="font-size:0.7em">海外直邮溯源码</span><div style="padding: 5px;">
<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$qr = new TCPDF2DBarcode('https://ot.pcaex.com/'.$p->hbn, 'QRCODE,H');
$qr->getBarcodeSVG(3, 3, 'black');
?>
</div><span style="font-size:0.7em">中检溯源技术支持</span></td>
<?php
endif;
?></tr>
<tr><td class="p10 btp" valign="top" height="70">收件方： <?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?><br />
<?=$p->cnee->getCnFullAddress();?></td>
</tr>
<tr><td colspan="2" class="p10 btp"><div style="overflow: hidden; height: 60px;">配货信息：
<?php
$gd = [];
foreach($p->eitems['g'] as $i => $g){
	if($p->eitems['type'][$i] == 'B') $g = '婴儿奶粉';
    $gd[] = $g.'x'.$p->eitems['q'][$i];
}
echo implode(' ', $gd);
?></div></td></tr>
</table>
</div>
<table width="100%" cellspacing="0" cellpadding="0" style="padding: 5px 10px">
<tr>
<td width="50%">备注: 单号: <?=$p->hbn;?></td>
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
