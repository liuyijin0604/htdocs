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
<div>
<img src="<?=$burl?>/images/sinoex_logo.png" width="220" />
<?php if($p->consol->poc == 'CNCA2'):?>
<img src="<?=$burl?>/images/zspeed.png" width="120" style="padding-left:10px" />
<?php endif; ?>
<div style="font-size:32px; font-weight:bold; float: right;padding: 10px 25px;"><?=empty($p->mdata['dtb'])? $p->cnee->city : $p->mdata['dtb'];?></div>
</div>
<div style="border: 1px #000 dashed; clear: both;">
<div align="center" style="padding: 5px;">
<div class="barcode"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128');
	echo $bc->getBarcodeSVGcode(3, 70);
	?></div><div class="connote"><?=$p->ref;?></div>
</div>
<hr class="dashed" />
<div style="padding: 5px;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td>
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td style="padding: 5px;">寄件人: <?=$p->cnor->name;?><div style="float:right; width:240px">电话: <?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?></div>
<div style="height:60px; overflow:hidden;">
地址: <?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?>
</div>
</td>
</tr>
</table>
</div>
<hr class="dashed" />
<div style="padding: 5px;">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="cnee">收件人: <?=$p->cnee->name;?><div style="float:right; width:280px">电话: <?=$p->cnee->tel;?></div>
<div style="overflow: hidden; height: 100px;">
地址: <?=$p->cnee->getCnFullAddress();?>
</div>
</td>
</tr>
</table>
</div>
<hr class="dashed" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding: 5px; border-right: 1px dashed #000;width:50%">
签收人/代收人：<br /><br /><br /><br />
</td><td valign="top" style="padding: 5px;">
签收时间：<br /><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 年 &nbsp;&nbsp;&nbsp;&nbsp; 月 &nbsp;&nbsp;&nbsp;&nbsp; 日 &nbsp;&nbsp;&nbsp;&nbsp; 时
</td>
</tr>
</table>
</td>
<td width="25" align="center" style="border-left: 1px dashed #000;">
签<br />收<br />联
</td>
</tr>
</table>
<hr class="dashed" />
<br />
<hr class="dashed" />
<div style="height:80px">
<div style="font-size:32px;font-weight:bold;padding: 5px;position:absolute;margin-top:25px;">国际快件</div>
<div style="text-align:center;float:right; width: 60%;"><div class="barcode" style="height:50px"><?php echo $bc->getBarcodeSVGcode(2, 40);
	?></div><?=$p->ref;?></div>
</div>
<hr class="dashed">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td>
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td style="padding: 5px;">
寄件： <?=$p->cnor->name;?> &nbsp; <?=empty($p->cnor->tel)? '0286669222': $p->cnor->tel;?>
<div style="height: 30px; overflow:hidden;">
<?=empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208': $p->cnor->fullAddress();?>
</div>
</td></tr>
</table>
<hr class="dashed">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td style="padding:5px">
收件： <?=$p->cnee->name, ' &nbsp; ', $p->cnee->tel;?>
<div style="height: 40px; overflow:hidden;">
<?=$p->cnee->getCnFullAddress();?>
</div>
</td>
</tr>
</table>
</td><td width="30" style="border-left: 1px dashed #000;" align="center">收<br />件<br />联
</td></tr>
</table>
<hr class="dashed" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding: 5px;">
<div style="overflow: hidden; height: 90px;">
内件描述:
<?php
foreach($p->eitems['g'] as $i => $g){
    $qty = $p->eitems['q'][$i];
    echo ($i>0? '&nbsp;&nbsp;' : '').'(',($i+1),') ', $g.'*'.$qty;
}
?>
</div>
</td>
</tr>
</table>
<hr class="dashed" />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding: 8px; margin:0;" valign="top" height="45">
参考号: <?=$p->hbn;?>
</td>
<td width="200" align="center"><b style="font-size:1.4em">YTO</b><!--img src="<?=$burl?>/images/yto_logo.png" width="180" /--></td>
</tr>
</table>
</div>
</div>
</div>
<script type="text/javascript">
window.print();
</script>
</body>
</html>
