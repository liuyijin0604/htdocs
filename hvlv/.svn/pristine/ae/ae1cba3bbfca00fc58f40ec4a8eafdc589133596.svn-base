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
body{ font-family: "Microsoft YaHei", "微软雅黑", Verdana, Geneva, sans-serif; font-size: 22px; text-rendering: optimize-speed; width: 700px; background: #fff; }
table, p{ font-size: 22px; }
p.logo { text-align: center; padding-bottom: 30px; }
small { font-size: 16px; }
hr { border: none; }
.brt { border-right: 1px solid #000; }
.bbt { border-bottom: 1px solid #000; }
.p10 { padding: 10px; }
.cnee {font-size: 1.2em;}
</style>
</head>
<body width="700">
<div>
<div style="padding: 10px">
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="35%"><img src="<?=$burl?>/images/chex_logo.png" width="180" /></td>
<td><span style="font-size:1.4em">中國大陸郵件提單</span><br/>
<?php
$bc = new TCPDFBarcode($p->hbn, 'C128');
echo $bc->getBarcodeSVGcode(2, 50);
?><br/>
<?=$p->hbn;?></td>
</tr>
</table>
</div>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" width="70%" class="p10" height="60">寄件人:牵趣进出口有限公司(丰趣海淘)<br />
3/104a Derby St ,SilverwaterNSW 2128 Austrlia
</td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="70%" valign="top" class="p10" height="120"><span class="cnee">收件人:<?=$p->cnee->name;?><br />
電話:<?=$p->cnee->tel;?><br />
<?=$p->cnee->fullAddress();?></span><br /><br />
<div align="center"><?php
$bc = new TCPDFBarcode($p->cref, 'C128');
echo $bc->getBarcodeSVGcode(2, 80);
?><br />
<span style="font-size:1.2em"><?=$p->cref;?></span></div><br />
</td>
</tr>
</table>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" class="p10">
若無法投遞時,寄件人之指定事項<br />
口退回寄件人並由寄件人付運費 口拋棄<br />
1.茲證明本人所填寫資料屬實且無裝寄任何危險及禁寄物品<br />
2.本人已審閱並同意載運契約一切條款<br />
(未保價貨件每單賠償上限為100 美元)<br />
寄件人簽署____________<br />
日期:_____年______月_______日
</td>
</tr>
</table>
<hr />
<div class="p10" style="text-align:center" align="center">
<span style="font-size:1.2em;">总件數： 1  &nbsp; &nbsp; 总重量： <?=round($p->weight*100)/100;?>KG </span>
</div>
<hr />
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="padding:10px;">
<div style="overflow: hidden; height: 100px;">
<?php
$gd = array();
foreach($p->eitems['g'] as $i => $g){
    $gd[] = $g;
}
echo implode(', ', $gd);
?>
</div>
</td>
</tr>
</table>
</body>
</html>
