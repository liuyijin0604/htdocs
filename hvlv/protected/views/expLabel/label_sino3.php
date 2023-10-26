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
body{ font-family: "Microsoft YaHei", Verdana, Geneva, sans-serif; font-size: 24px; text-rendering: optimize-speed; width: 700px; background: #fff; transform: scale(0.6);transform-origin: 0 0;}
table, p{ font-size: 24px; line-height: 30px;}
p.logo { text-align: center; padding-bottom: 30px; }
.barcode{ height:65px; overflow: hidden;}
h4 { font-size: 26px; }
small { font-size: 16px; }
hr { border: none; border-bottom: 1px solid #000; }
.brt { border-right: 1px solid #000; }
.bbt { border-bottom: 1px solid #000; }
.p10 { padding: 10px; }
.cnee {font-size: 1.2em;}
</style>
</head>
<body width="700">
<div style="border: 1px #000 solid;">
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td ><img src="<?=$burl?>/images/jinli_logo.png" width="40%" /></td>
</tr>
</table>
</div>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" width="70%" class="brt p10" height="100"><div style="height:25px; overflow:hidden;">寄方： <?=$p->cnor->name;?></div>
<?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?><br />
<?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?>
</td>
<td valign="top" class="p10">
原寄地：<br />
澳大利亚<br />
打印时间:<br />
<?=date('Y-m-d');?>
</td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="70%" valign="top" class="brt p10" height="120"><b>收方</b>：<span class="cnee"><?=$p->cnee->name;?> &nbsp; <?=$p->cnee->tel;?><br />
<?=$p->cnee->getCnFullAddress();?></span>
</td>
<td valign="top" class="p10">
目的地:<br />
<span style="font-size:1.8em;line-height: 60px;"><?=$p->cnee->state;?></span>
</td>
</tr>
</table>
<hr />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td align="center" style="font-size:1.4em;line-height: 65px;">订单号: <?=$p->ref;?></td>
</tr>
</table>
</div>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="15%" align="center" valign="top" class="brt bbt p10">件数</td>
<td width="50%" align="center" valign="top" class="brt bbt p10">实际重量(Kg)</td>
<td width="35%" align="center" valign="top" class="bbt p10">申报金额</td>
</tr><tr>
<td align="center" valign="top" class="brt p10">1</td>
<td align="center" valign="top" class="brt p10"><?=$p->shipWeight();?></td>
<td align="center" valign="top" class="p10"><?=round($p->getDvalue());?></td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding:10px; margin:0;" align="center"><div class="barcode"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128B');
	echo $bc->getBarcodeSVGcode(2, 65);
	?></div><?=$p->ref;?></td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding:10px;" colspan="2" rowspan="3">物品详情：
<div style="overflow: hidden; height: 120px;">
<?php
$gd = [];
foreach($p->eitems['g'] as $i => $g){
    $gd[] = $g;
}
echo implode(', ', $gd);
?>
</div>
</td>
</tr>
</table>
<hr />
<div class="p10">
备注栏
</div>
<hr />
<div class="p10" style="height:80px">
<?=$p->hbn;?>
</div>
</div>
<script type="text/javascript">
window.print();
</script>
</body>
</html>
