<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$high = 100 + sizeof($r->eitems['g']) * 10;
?>
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 56 --page-height <?=$high;?> -T 2 -R 2 -B 2 -L 2 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --width 400 --quality 80" />
<title>Receipt Costco</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: 'DejaVu Sans Mono', sans-serif; font-size: 20px; text-rendering: optimize-speed; width: 360px; padding: 20px; }
table td{ padding: 5px; }
hr { border: none; border-top: 1px dashed #000; margin: 5px 0;}
.invert { background: #000; color:#fff; }
</style>
</head>
<body width="400">
<?php
$date = date('d/m/Y H:i', strtotime($d['date']));
?>
<p align="center">
<img src="https://os.toplogistics.com.au/images/costco_logo.png" width="240" style="margin:20px 0" /><br />
** COSTCO Wholesale **<br />
MEMBER <?=$d['mn'];?>
</p>
<table width="100%">
<tr><td>&nbsp;</td><td align="center" width="40">&nbsp;</td></tr>
<?php
$ti = 0;
$tot = 0;
$poc_map = ['CNCA2' => 'CNCA2', 'CNJM2' => 'CNJMN', 'CNCS2' => 'CNCSX'];
if(isset($poc_map[$r->consol->poc])) $r->consol->poc = $poc_map[$r->consol->poc];

foreach($r->eitems['g'] as $i => $g){
	if(!empty($r->mdata['altItems']['v'][$i])){
		$v = $r->mdata['altItems']['v'][$i];
	}else{
		$pd = empty($r->eitems['pid'][$i])? false : ExProdb::model()->findByPk($r->eitems['pid'][$i]);
		$v = empty($pd)? floatval($r->eitems['t'][$i]) : (empty($pd->mdata['price_'.$r->consol->poc])? $pd->price : $pd->mdata['price_'.$r->consol->poc]);
	}
	$v = $v / $r->consol->exrate;

	$st = sprintf('%.2f', floatval($r->eitems['q'][$i]) * $v);

	echo '<tr><td>', $r->eitems['q'][$i], ' @ ', sprintf('%.2f', $v),'<br />', $r->receiptItemName($i), '</td><td align="right" valign="top"><br />',$st,'</td></tr>';
	$ti += floatval($r->eitems['q'][$i]);
	$tot += $st;
}
?>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>TOTAL</td><td align="right" class="invert">$<?=$tot;?></td></tr>
<tr><td>EFT/DEBIT</td><td align="right">$<?=$tot;?></td></tr>
</table>
<hr />
<br />
<p>
TOTAL NUMBER OF ITEMS SOLD = <?=$ti;?><br />
<span class="invert"><?=$date;?> 201<?=$d['no'];?><br />
</p>
<br />
<p align="center">
Thank you!<br />
Come Again</p>
</body>
</html>
