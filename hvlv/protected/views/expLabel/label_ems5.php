<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 102 --page-height 152 -T 3 -R 3 -B 3 -L 3 -O Portrait" />
<meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />
<title>PCA Express Label</title>
<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
$burl = Yii::app()->request->hostInfo.Yii::app()->baseUrl;
?>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: "Microsoft YaHei", "微软雅黑", Verdana, Geneva, sans-serif; font-size: 19px; text-rendering: optimize-speed; width: 700px; background: #fff;}
table, p{ font-size: 19px; }
p.logo { text-align: center; padding-bottom: 30px; }
.barcode{ height:60px; overflow: hidden;}
h4 { font-size: 22px; }
small { font-size: 16px; }
hr { border: none; border-bottom: 1px solid #000; }
hr.dashed { border: none; border-bottom: 1px dashed #000; }
</style>
</head>
<body width="700">
<div style="border: 1px #000 solid;">
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="250"><img src="<?=$burl;?>/images/cnpost_logo.png" width="220" /></td>
<td align="center" valign="top"><div class="barcode"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128B');
	echo $bc->getBarcodeHTML(2, 60);
	?></div><?=$p->ref;?></td>
</tr>
</table>
</div>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%"><div style="height:25px; overflow:hidden;">寄件人/<b>From</b>： <?=$p->cnor->name;?></div></td>
<td>电话/<b>Tel</b>： <?=empty($p->cnor->tel)? '280856368': $p->cnor->tel;?></td>
</tr>
<tr>
<td height="60" valign="top" colspan="2" style="padding-top:10px;">地址/<b>Address</b>： <?=empty($p->cnor->address)? '7/89 DERBY ST, SILVERWATER, NSW 2128': $p->cnor->fullAddress();?></td>
</tr>
</table>
</div>
<hr />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%">收件人/<b>To</b>： <?=$p->cnee->name;?></td>
<td>电话/<b>Tel</b>：<?=$p->cnee->tel;?></td>
</tr>
<tr>
<td height="60" valign="top" colspan="2" style="padding-top:10px;">地址/<b>Address</b>： <?=$p->cnee->fullAddress();?></td>
</tr>
<tr>
<td width="50%">&nbsp;</td>
<td>邮编/<b>Post Code</b>：<?=$p->cnee->postcode;?></td>
</tr>
</table>
</div>
<table width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #000; border-left: none; border-right: none;">
<tr>
<td valign="top" width="480" style="padding:10px;">内件描述/<b>Name &amp; Description of Contents</b>：</td>
<td style="padding: 10px;" valign="top">实际重量： <b><?=round($p->shipWeight(),2);?>kg</b></td>
</tr>
<tr>
<td style="padding: 0 10px;">
<div style="overflow: hidden; height: 120px;">
<?php
$gd = array();
foreach($p->eitems['g'] as $i => $g){
	if($p->consol->poc == 'CNPEK'){
		$g = preg_replace('/[\&]+/','', $g);
		$g = preg_replace('/(1|2|3|4|一|二|三|四)段/','', $g);
		$g = preg_replace('/(全|脱)脂/','', $g);
		$g = preg_replace('/可瑞康|爱他美金装|白金版爱他美奶/','Nutricia纽迪希亚', $g);
		$g = str_replace('羊奶粉','奶粉', $g);
	}
    $qty = ($p->eitems['u'][$i] == '千克')? $p->eitems['w'][$i] : $p->eitems['q'][$i];
    $gd[] = $g.'*'.sprintf('%01.2f', $qty);
}
echo implode('; ', $gd);
?>
</div>
</td>
<?php
if($p->hasOriginTrace()):
?><td align="center" rowspan="2"><span style="font-size:0.7em">海外直邮溯源码</span><div style="padding: 5px;">
<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$qr = new TCPDF2DBarcode('https://ot.pcaex.com/'.$p->hbn, 'QRCODE,H');
$qr->getBarcodeSVG(3, 3, 'black');
?>
</div><span style="font-size:0.7em">中检溯源技术支持</span></td>
<?php
endif;
?>
</tr><tr>
<td style="padding: 0 0 5px 10px;">大客户，本人签收，送货上门，礼貌投递!</td>
</tr>
</table>
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%">收件人签名：</td>
<td>签收时间： &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;年 &nbsp;&nbsp;&nbsp;&nbsp;月 &nbsp;&nbsp;&nbsp;&nbsp;日 &nbsp;&nbsp;&nbsp;&nbsp;时</td>
</tr>
</table>
</div>
<div style="padding:15px 0">
<hr class="dashed" />
</div>
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%"><div style="height:25px; overflow:hidden;">寄件人/<b>From</b>： <?=$p->cnor->name;?></div></td>
<td>电话/<b>Tel</b>： <?=empty($p->cnor->tel)? '280856368': $p->cnor->tel;?></td>
</tr>
<tr>
<td height="60" valign="top" colspan="2" style="padding-top:10px;">地址/<b>Address</b>： <?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?></td>
</tr>
</table>
</div>
<hr />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%">收件人/<b>To</b>： <?=$p->cnee->name;?></td>
<td>电话/<b>Tel</b>：<?=$p->cnee->tel;?></td>
</tr>
<tr>
<td height="60" valign="top" colspan="2" style="padding-top:10px;">地址/<b>Address</b>： <?=$p->cnee->fullAddress();?></td>
</tr>
</table>
</div>
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" width="430" style="padding:10px; margin:0;" valign="top"><div style="text-align:center;"><div class="barcode" style="height:50px;"><?php echo $bc->getBarcodeHTML(2, 50); ?></div><?=$p->ref;?></div>
<div style="padding-top:10px">进口口岸：湖南郴州 &nbsp; 原寄地：澳大利亚</div>
</td>
<td style="padding: 10px; margin:0;" valign="top"><img src="<?=$burl;?>/images/xg_logo.png" width="100" /> <div style="font-size:1.3em;position:absolute; margin-top:-60px;margin-left: 105px">湘港物流</div><br />
<img src="<?=$burl;?>/images/cnpost_logo.png" width="200" style="margin-top:10px" /></td>
</tr>
</table>
</div>
关联单号： <b><?=$p->hbn;?></b>
</body>
</html>
