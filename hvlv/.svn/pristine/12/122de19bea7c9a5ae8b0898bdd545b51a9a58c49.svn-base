<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$high = 100 + sizeof($r->eitems['g']) * 10;
?>
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 56 --page-height <?=$high;?> -T 2 -R 2 -B 2 -L 2 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --width 400 --quality 80" />
<title>Receipt Misc</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: 'DejaVu Sans Mono', sans-serif; font-size: 20px; text-rendering: optimize-speed; width: 360px; padding: 20px;}
table td{ padding: 5px; }
hr { border: none; border-top: 1px dotted #000; margin: 5px 0;}
</style>
</head>
<body width="400">
<?php
$stores = [
	'PRICE PHARMACY<br /> 172 Victoria Ave - Chatswood 2067<br /> Phone: 9411 8462<br /> ABN: 72 297 938 539',
	'Lucky Gifts Pty Ltd<br />Shop 2, 7 Goulburn St, Sydney, NSW 2000 <br />Phone 9281 3568',
	'Best Vitamines Market<br />Surrey Hills, NSW 2010 <br />Phone: 8033 0655',
	'BABY FOODS CO<br />Bankstown, NSW 2200<br />Phone: 8033 0655',
	'Pharmacy Driect Pty Ltd<br />208 Forest Road, Hurstville, NSW 2220 <br />Phone: 9580 2345',
];
$date = date('d/m/Y H:i', strtotime($d['date']));
?>
<br />
<p align="center"><?=$stores[$d['sid']];?><br /><br />
<b>Tax Invoice</b>
</p><br />
<p align="right" style="padding-right: 5px;">Receipt: <?=$d['no'];?><br />
Time: <?=$date;?>
</p>
<hr />
<table width="100%">
<tr><td></td><td align="center" width="40"></td></tr>
<?php
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
	echo '<tr><td>', $r->eitems['q'][$i], ' x ', $r->receiptItemName($i), '</td><td align="right" valign="top">$',$st,'</td></tr>';
	$tot += $st;
}
?>
<tr><td><b>TOTAL:</b></td><td align="right"><b>$<?=$tot;?></b></td></tr>
</table>
<hr />
<br />
<p align="center">Please retain receipt for refund<br />
or exchange purposes</p>
</body>
</html>
