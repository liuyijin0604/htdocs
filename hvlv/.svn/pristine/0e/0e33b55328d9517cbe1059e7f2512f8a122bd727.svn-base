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
body{ font-family: "Microsoft YaHei", Verdana, Geneva, sans-serif; font-size: 21px; text-rendering: optimize-speed; width: 700px; background: #fff; transform: scale(0.6);transform-origin: 0 0;}
table, p{ font-size: 21px; }
p.logo { text-align: center; padding-bottom: 30px; }
.barcode{ overflow: hidden;}
h4 { font-size: 26px; }
small { font-size: 16px; }
hr { border: none; border-bottom: 1px solid #000; }
hr.dashed { border-bottom: 1px dashed #000; }
</style>
</head>
<body width="700">
<div style="border: 1px #000 solid;">
<div style="padding: 5px 10px; text-align: right;">www.gztopex.com</div>
<hr />
<div style="padding: 5px 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="40%" align="center"><span style="font-size:1.6em; font-weight: bold;">快递包裹</span></td>
<td align="right" valign="top" style="padding-right:20px;"><div class="barcode"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128');
	echo $bc->getBarcodeSVGcode(2,60);
	?></div><?=$p->ref;?></td>
</tr>
</table>
</div>
<hr />
<div style="padding: 5px 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="30%">目的地<br /><span style="font-size:1.4em;">广州转</span></td>
<td valign="center" align="center"><span style="font-size:1.4em;">(投递局)</span></td>
<td width="30%"><div align="center">已验视</div>单位: 西区邮政局<br />验视人: 罗德全</td>
</tr>
</table>
</div>
<hr />
<div style="padding: 5px 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%">收件人： <?=$p->cnee->name;?></td>
<td>电话：<?=$p->cnee->tel;?></td>
</tr>
<tr>
<td height="75" valign="top" colspan="2" style="padding-top:10px;">地址： <?=$p->cnee->getCnFullAddress();?> &nbsp; 邮编：<?=$p->cnee->postcode;?></td>
</tr>
</table>
</div>
<hr />
<div style="padding: 5px 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%"><div style="height:25px; overflow:hidden;">寄件人： <?=$p->cnor->name;?></div></td>
<td>电话： <?=empty($p->cnor->tel)? '0299257100': $p->cnor->tel;?></td>
</tr>
<tr>
<td height="50" valign="top" colspan="2" style="padding-top:10px;">地址： <?=empty($p->cnor->address)? 'U12/1901 Botany Road, Banksmeadow, NSW 2019': $p->cnor->fullAddress();?></td>
</tr>
</table>
</div>
<hr />
<div style="padding: 5px 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="35%">保价声明价值: &nbsp; &nbsp; &nbsp; 元</td>
<td width="25%">保费: &nbsp; &nbsp; &nbsp; 元</td>
<td>包装费: &nbsp; &nbsp; &nbsp; 元</td>
</tr>
<tr>
<td>付款方式:</td>
<td>月结账号:</td>
<td>&nbsp;</td>
</tr>
</table>
</div>
<hr />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" valign="top" height="40">收件人/代收人:</td>
<td valign="top">签收日期:</td>
</tr>
</table>
</div>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td><div style="text-align:center;">
<div class="barcode" style="padding-top:10px;"><?php echo $bc->getBarcodeSVGcode(2, 60);?></div><?=$p->ref;?></div></td>
<td width="50%" align="right"><img src="<?=$burl?>/images/topex_logo.png" width="200" /></td>
</tr>
</table>
</div>
<hr />
<div style="padding: 5px 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%">收件人： <?=$p->cnee->name;?></td>
<td>电话：<?=$p->cnee->tel;?></td>
</tr>
<tr>
<td height="75" valign="top" colspan="2" style="padding-top:10px;">地址： <?=$p->cnee->getCnFullAddress();?></td>
</tr>
</table>
</div>
<hr />
<div style="padding: 5px 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%"><div style="height:25px; overflow:hidden;">寄件人： <?=$p->cnor->name;?></div></td>
<td>电话： <?=empty($p->cnor->tel)? '0299257100': $p->cnor->tel;?></td>
</tr>
<tr>
<td height="50" valign="top" colspan="2" style="padding-top:10px;">地址： <?=empty($p->cnor->address)? 'U12/1901 Botany Road, Banksmeadow, NSW 2019': $p->cnor->fullAddress();?></td>
</tr>
</table>
</div>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding: 5px 10px;"><div style="overflow: hidden; height: 60px;">
内件描述： 
<?php
$gd = [];
$tq = 0;
foreach($p->eitems['g'] as $i => $g){
    $qty = ($p->eitems['u'][$i] == '千克')? $p->eitems['w'][$i] : $p->eitems['q'][$i];
    $gd[] = $g.'x'.sprintf('%01.2f', $qty);
    $tq += $qty;
}
echo implode('; ', $gd);
?>
</div>
</td>
</tr>
<tr>
<td style="padding: 5px 10px;">数量： <?=$tq;?> &nbsp; &nbsp; 重量：<?=$p->shipWeight();?> kg &nbsp; &nbsp; 货值: &nbsp; &nbsp; 元</td></tr>
</table>
<hr />
<div style="padding: 5px 10px;"
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top">关联单号： <?=$p->hbn;?></td>
</tr>
</table>
</div>
</div>
<script type="text/javascript">
window.print();
</script>
</body>
</html>
