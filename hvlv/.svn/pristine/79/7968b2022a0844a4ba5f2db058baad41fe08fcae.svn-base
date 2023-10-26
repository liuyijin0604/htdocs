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
.barcode{ height:60px; overflow: hidden;}
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
<td width="40%" class="brt p10"><div style="font-size:30px; text-align:center"><?php
$rate = $p->rating($p->consol->poc, $p->consol->exrate, true);
echo !empty($rate[1]) && preg_match('/^J/', $rate[1]->zone)? '快递包裹' : '标准快递'; ?></div><div style="position:absolute;"><small>时间: <?=date('Y-m-d H:i');?></small></div></td>
<td align="center" class="p10" valign="top"><div class="barcode"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128');
	echo $bc->getBarcodeSVGcode(2, 60);
	?></div><?=$p->ref;?></td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td width="60%" valign="top" height="85" class="p10 brt">寄件： <?=$p->cnor->name;?><br />
<?=$p->cnor->fullAddress()?></td>
<td align="center" class="p10"></td>
</tr>
<tr><td height="120" valign="top" colspan="2" class="btp p10" style="padding-top:10px;font-size: 1.2em;">收件： <?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?><br />
<?=$p->cnee->getCnFullAddress();?>
</td></tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td width="40%" valign="top" class="p10 brt">付款方式：<br />计费重量(KG)：<br />保价金额(元)：</td>
<td class="p10">收件人\代收人：<br />
签收时间： &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;年 &nbsp;&nbsp;&nbsp;&nbsp;月 &nbsp;&nbsp;&nbsp;&nbsp;日 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;时<br /><small>快件送达收货人地址，经收件人或收件人授权的代收人签字，视为送达。</small></td>
</tr>
</table>
<hr />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td align="center"><div class="barcode" style="height:30px;"><?php
	$bc2 = new TCPDFBarcode($p->hbn, 'C128');
	echo $bc2->getBarcodeSVGcode(2, 30);
	?></div>订单号：<b><?=$p->hbn;?></b></td>
<td width="40%" align="center" valign="top">件数：1 &nbsp; 重量: <?=$p->shipWeight();?>kg</td>
</tr>
<tr><td colspan="3"><div style="overflow: hidden; height: 150px;">
配货信息： 
<?php
$gd = [];
foreach($p->eitems['g'] as $i => $g){
    $gd[] = $g.'x'.$p->eitems['q'][$i];
}
echo implode(' ', $gd);
?></div>
</td></tr>
</table>
</div>
<hr />
<div style="padding-top:15px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="90%" align="center" class="p10"><div class="barcode" style="height:80px;"><?php
	echo $bc->getBarcodeSVGcode(3, 80);?></div><?=$p->ref;?></td>
<td align="center" valign="top" class="p10"><img src="<?=$burl;?>/images/ems_logo.jpg" width="175" /></td>
</tr>
</table>
</div>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" width="40%" class="brt p10" height="135">寄件： <?=$p->cnor->name;?><br />
<?=$p->cnor->fullAddress()?></td>
<td valign="top" class="p10">收件： <?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?><br />
<?=$p->cnee->getCnFullAddress();?></td>
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
<script type="text/javascript">
window.print();
</script>
</body>
</html>
