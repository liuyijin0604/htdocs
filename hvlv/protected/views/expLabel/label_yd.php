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
body{ font-family: "Microsoft YaHei", "微软雅黑", Verdana, Geneva, sans-serif; font-size: 21px; text-rendering: optimize-speed; width: 700px; background: #fff; transform: scale(0.6);transform-origin: 0 0;}
table, p{ font-size: 21px; }
p.logo { text-align: center; padding-bottom: 30px; }
.barcode{ height:70px; overflow: hidden;}
.connote{ font-size: 24px; }
h4 { font-size: 26px; }
small { font-size: 16px; }
hr { border: none; border-bottom: 1px solid #000; }
hr.dashed { border: none; border-bottom: 1px dashed #000; }
.cnee {font-size: 1.2em;}
</style>
</head>
<body width="700">
<div style="padding: 8px;">
<table width="100%" cellspacing="0" cellpadding="0" style="font-size:0.8em">
<tr>
<td valign="top" width="32%">
始发网点: Orig.Branch<br />
寄件人: Shipper<br />
寄件人电话: Tel<br />
寄件人地址: Shipper's Address<br />
</td>
<td valign="top">
澳大利亚<br />
<?=$p->cnor->name;?><br />
<?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?><br />
<?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?>
</td>
<td width="15%" align="right">
<h2 style="padding-right:10px">国际件</h2>
</td>
</tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="15%" rowspan="3">
送达<br />
地址<br />
Dest.Inf<br />
</td>
<td width="30%">
收件人: Receiver
</td>
<td valign="top" style="font-size: 1.2em">
<?=$p->cnee->name;?>
</div>
</td>
</tr>
<tr>
<td>
收件人电话: Tel
</td>
<td valign="top" style="font-size: 1.2em">
<?=$p->cnee->tel;?>
</td>
</tr>
<tr>
<td valign="top">
收件人地址:<br />
Address
</td>
<td valign="top" style="font-size: 1.2em">
<div style="overflow: hidden; height: 100px;">
<?=$p->cnee->getCnFullAddress();?>
</div>
</td>
</tr>
</table>
<hr style="margin:15px 0 5px 0" />
<div align="center">
<div class="barcode"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128');
	echo $bc->getBarcodeSVGcode(3, 70);
	?></div>
<div>运单编号 Waybill# <?=$p->ref;?></div>
</div>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding: 5px; border-right: 1px solid #000;width:50%">
收件人/代签人：<br />
Name of Sign-off
</td><td valign="top" style="padding: 5px;">
签收时间：<div style="line-height:50px;">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 年 &nbsp;&nbsp;&nbsp;&nbsp; 月 &nbsp;&nbsp;&nbsp;&nbsp; 日 &nbsp;&nbsp;&nbsp;&nbsp; 时</div>
</td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding: 5px;" width="40%">
托寄物品简述:
Desc.</td>
<td valign="top" style="padding: 5px;">
订单编号: Order# &nbsp; <b><?=$p->hbn;?></b>
</td>
</tr>
</table>
<div style="overflow: hidden; height: 90px;padding: 5px;">
<?php
foreach($p->eitems['g'] as $i => $g){
    $qty = $p->eitems['q'][$i];
    echo ($i>0? '&nbsp;&nbsp;' : '').'(',($i+1),') ', $g.'*'.$qty;
}
?>
</div>
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding: 5px;" valign="top">
总件数 # Of Pkgs &nbsp; <b>1</b>
</td>
<td valign="top" style="padding: 5px;" valign="top">快件类型 Shipment Type: &nbsp; 包裹Parcel</td>
</tr>
</table>

<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td align="center" valign="bottom">
<?php
	echo $bc->getBarcodeSVGcode(2, 55);
?>
</td>
<td width="200">
<img src="<?=$burl?>/images/yunda_logo.png" width="200" />
</td>
</tr>
</table>
<hr style="margin-top:15px" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding: 5px; border-right: 1px solid #000;width:80%">
<table width="100%" cellspacing="0" cellpadding="0" style="font-size:0.8em">
<tr>
<td valign="top" width="40%">
始发网点: Orig.Branch<br />
寄件人: Shipper<br />
寄件人电话: Tel<br />
寄件人地址: Shipper's Address<br />
</td>
<td valign="top">
澳大利亚<br />
<?=$p->cnor->name;?><br />
<?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?><br />
<div style="overflow: hidden; height: 60px;">
<?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?>
</div>
</td>
</tr>
<tr>
<td>
收件人: Receiver
</td>
<td valign="top" style="font-size:1.2em">
<?=$p->cnee->name;?>
</div>
</td>
</tr>
<tr>
<td>
收件人电话: Tel
</td>
<td valign="top" style="font-size:1.2em">
<?=$p->cnee->tel;?>
</td>
</tr>
<tr>
<td valign="top">
收件人地址:<br />
Address
</td>
<td valign="top" style="font-size:1.2em">
<div style="overflow: hidden; height: 80px;">
<?=$p->cnee->getCnFullAddress();?>
</div>
</td>
</tr>
</table>
</td><td valign="top" style="padding: 5px;">
&nbsp;
</td>
</tr>
</table>

</div>
<script type="text/javascript">
window.print();
</script>
</body>
</html>
