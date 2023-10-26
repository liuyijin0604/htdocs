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
.barcode{ height:70px; overflow: hidden;}
h4 { font-size: 26px; }
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
<td width="375">
<img src="<?=$burl?>/images/cnexp_logo2.png" width="360" />
</td>
<td align="center" valign="top"><div class="barcode"><?php
	$bc = new TCPDFBarcode($p->ref, 'C128');
	echo $bc->getBarcodeSVGcode(2,60);
	?></div><?=$p->ref;?></td>
</tr>
</table>
</div>
<hr class="dashed" />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%"><div style="height:25px; overflow:hidden;">寄件人/From： <?=$p->cnor->name;?></div></td>
<td>电话/Tel： <?=empty($p->cnor->tel)? '0299257100': $p->cnor->tel;?></td>
</tr>
<tr>
<td height="50" valign="top" colspan="2" style="padding-top:10px;">地址/Address： <?=empty($p->cnor->address)? 'U12/1901 Botany Road, Banksmeadow, NSW 2019': $p->cnor->fullAddress();?></td>
</tr>
</table>
</div>
<hr />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%">收件人/To： <?=$p->cnee->name;?></td>
<td>电话/Tel：<?=$p->cnee->tel;?></td>
</tr>
<tr>
<td height="60" valign="top" colspan="2" style="padding-top:10px;">地址/Address： <?=$p->cnee->getCnFullAddress();?></td>
</tr>
<tr>
<td width="50%">
大客户代码：</td>
<td>邮编/Post Code：<?=$p->cnee->postcode;?></td>
</tr>
</table>
</div>
<table width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #000; border-left: none; border-right: none;">
<tr>
<td valign="top" width="430" style="padding:10px;border-right: 1px solid #000; border-bottom:1px solid #000;" colspan="2" rowspan="3">内件描述/Name &amp; Description of Contents：
<div style="overflow: hidden; height: 100px;">
<?php
$gd = [];
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
$whd = $p->genWHD();
?>
</div>
</td>
<td colspan="3" style="padding: 10px; border-bottom:1px solid #000;" valign="top">实际重量：<?=$p->shipWeight();?> kg</td>
</tr>
<tr>
<td colspan="3" style="padding: 10px; border-bottom:1px solid #000;" valign="top">体积重量：<?=$p->shipWeight();?> kg</td>
</tr>
<tr>
<td style="padding: 10px; border-bottom:1px solid #000; border-right:1px solid #000; width:50px; font-size:18px;">长/L</td>
<td style="padding: 10px; border-bottom:1px solid #000; border-right:1px solid #000; width:50px; font-size:18px;">宽/W</td>
<td style="padding: 10px; border-bottom:1px solid #000; width:50px; font-size:18px;">高/H</td>
</tr>
<tr>
<td style="padding: 10px; border-right:1px solid #000;">申报价值/Value：<?=round($p->getDvalue());?></td>
<td style="padding: 10px; border-right:1px solid #000;">原产地/Origin：AUS</td>
<td style="padding: 10px; border-right:1px solid #000; font-size:18px;" align="right"><div style="position:absolute;"><?=$whd[0];?></div>cm</td>
<td style="padding: 10px; border-right:1px solid #000; font-size:18px;" align="right"><div style="position:absolute;"><?=$whd[1];?></div>cm</td>
<td style="padding: 10px; font-size:18px;" align="right"><div style="position:absolute;"><?=$whd[2];?></div>cm</td>
</tr>
</table>
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%">
<img src="<?=$burl?>/images/ems_logo.jpg" width="220" style="margin-bottom:10px;" /><br />
进口口岸: 福州
</td>
<td><div style="text-align:center;">
<div class="barcode" style="height:70px"><?php echo $bc->getBarcodeSVGcode(2, 60);?></div><?=$p->ref;?></div></td>
</tr>
</table>
</div>
<div style="padding:15px 0 0 0">
<hr class="dashed" />
</div>
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%"><div style="height:25px; overflow:hidden;">寄件人/From： <?=$p->cnor->name;?></div></td>
<td>电话/Tel： <?=empty($p->cnor->tel)? '0299257100': $p->cnor->tel;?></td>
</tr>
<tr>
<td height="50" valign="top" colspan="2" style="padding-top:10px;">地址/Address： <?=empty($p->cnor->address)? 'U12/1901 Botany Road, Banksmeadow, NSW 2019': $p->cnor->fullAddress();?></td>
</tr>
</table>
</div>
<hr />
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%">收件人/To： <?=$p->cnee->name;?></td>
<td>电话/Tel：<?=$p->cnee->tel;?></td>
</tr>
<tr>
<td height="60" valign="top" colspan="2" style="padding-top:10px;">地址/Address： <?=$p->cnee->getCnFullAddress();?></td>
</tr>
</table>
</div>
<table width="100%" cellspacing="0" cellpadding="0" style="border-top:1px solid #000;">
<tr>
<td width="430" style="padding:10px; margin:0;" valign="top">收件人签名：<br /><br /><br />
签收时间： &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;年 &nbsp;&nbsp;&nbsp;月 &nbsp;&nbsp;&nbsp;日 &nbsp;&nbsp;&nbsp;时</td>
<td style="border-left: 1px solid #000; padding: 10px; margin:0;" valign="top"><div style="line-height:35px">
关联单号：<br /><?=$p->hbn;?><br />原寄地：澳大利亚</div></td>
</tr>
</table>
</div>
<script type="text/javascript">
window.print();
</script>
</body>
</html>
