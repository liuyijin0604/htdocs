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
<div style="padding: 8px;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="250"><img src="<?=$burl?>/images/sinoex_logo.png" width="220" /><br /><img src="<?=$burl?>/images/zspeed.png" width="120" style="padding-left:20px" /></td>
<td align="center" valign="middle"><div class="barcode">*<?=$p->ref;?>*</div><div class="connote"><?=$p->ref;?></div></td>
</tr>
</table>
</div>
<div style="border: 1px #000 solid;">
<div style="padding: 8px;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td>寄件： <?=$p->cnor->name;?> &nbsp; <?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?>
<div style="height:25px; overflow:hidden;">
<?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?>
</div>
</td>
</tr>
</table>
</div>
<hr />
<div style="padding: 8px;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="cnee">收件：<?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?>
<div style="overflow: hidden; height: 70px;">
<?=$p->cnee->getCnFullAddress();?>
</div>
</td>
</tr>
</table>
</div>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" width="50%" style="padding: 8px;">
付款方式：<br />
计费重量：<?=$p->shipWeight();?>KG<br />
报价金额：<?=$p->value;?>元
</td>
<td valign="top" style="padding: 8px; border-left: 1px solid #000;">
签收人/代收人：<br /><br />
签收时间：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 年 &nbsp;&nbsp;&nbsp; 月 &nbsp;&nbsp;&nbsp; 日 &nbsp;&nbsp;&nbsp; 时
</td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" width="55%" style="padding: 8px;">
订单号：<span class="cnee"><?=$p->hbn;?></span>
</td>
<td valign="top" style="padding: 8px;">件数：1pcs &nbsp; 重量：<?=$p->weight;?>KG
</td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding: 8px;" width="50%">配货信息：
<div style="overflow: hidden; height: 140px;">
<?php
foreach($p->eitems['g'] as $i => $g){
	if($i > 3) break;
    $qty = $p->eitems['q'][$i];
    echo '(',($i+1),') ', $g.'*'.$qty, '<br />';
}
?>
</div>
</td><td style="border-left: 1px solid #000">
<div style="overflow: hidden; height: 140px;">
<?php
foreach($p->eitems['g'] as $i => $g){
	if($i < 4 || $i > 8) continue;
    $qty = $p->eitems['q'][$i];
    echo '(',($i+1),') ', $g.'*'.$qty, '<br />';
}
?>
</div>
</td>
</tr>
</table>
<hr />
<div style="padding: 8px;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td height="50" valign="top"><img src="<?=$burl?>/images/sinoex_logo.png" height="50" /> <img src="<?=$burl?>/images/zspeed.png" height="45" style="padding-left:15px" /></td>
</tr>
</table>
</div>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" width="50%" style="padding: 8px;">
寄件： <?=$p->cnor->name;?> &nbsp; <?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?>
<div style="height: 120px; overflow:hidden;">
<?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?>
</div>
</td>
<td valign="top" style="padding: 8px; border-left: 1px solid #000;">
收件： <?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?>
<div style="height: 120px; overflow:hidden;">
<?=$p->cnee->getCnFullAddress();?>
</div>
</td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding: 8px; margin:0;" valign="top" height="75">
备注：
</td>
</tr>
</table>
</div>
<script type="text/javascript">
window.print();
</script>
</body>
</html>
