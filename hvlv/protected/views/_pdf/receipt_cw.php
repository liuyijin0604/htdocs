<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$high = 130 + sizeof($r->eitems['g']) * 10;
?>
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 56 --page-height <?=$high;?> -T 2 -R 2 -B 2 -L 2 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --width 400 --quality 80" />
<title>Receipt Chemist Warehouse</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: 'DejaVu Sans Mono', sans-serif; font-size: 20px; text-rendering: optimize-speed; width: 360px; padding: 20px; }
table td{ padding: 5px; }
hr { border: none; border-top: 1px solid #000; margin: 5px 0;}
.big { font-size: 40px; }
</style>
</head>
<body width="400">
<?php
$date = date('M d Y h:iA', strtotime($d['date']));
?>
<p align="center">
<img src="https://os.toplogistics.com.au/images/cw_logo.png" width="200" style="margin:25px 0 0 0;" /><br />
ABN: 41 585 797 663
<p align="center" style="margin: 15px"><b>Tax Invoice</b></p>
<p align="center"><?=$date;?> - <?=$d['no'];?></p>
<hr style="margin-bottom: 0;" />
<table width="100%">
<tr><td width="20">&nbsp;</td><td>&nbsp;</td><td align="center" width="40">&nbsp;</td></tr>
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
	echo '<tr><td>', $r->eitems['q'][$i], '</td><td>', $r->receiptItemName($i), '</td><td align="right" valign="top">$',$st,'</td></tr>';
	$ti += floatval($r->eitems['q'][$i]);
	$tot += $st;
}
?>
<tr><td>&nbsp;</td><td>&nbsp;</td><td align="right" valign="top"><hr /></td></tr>
<tr><td></td><td><b class="big">TOTAL</b><br /><?=$ti;?> Item(s)</td><td align="right" valign="top" class="big"><b>$<?=$tot;?></b></td></tr>
</table>
<br />
<hr />
<p>* Indicates a taxable item<br />
Total includes GST $0.00</p>
<br />
<p style="font-size: 0.8em"><b>Thank-you for shopping at Chemist Warehouse Home of Real Brands with Real Savings</b><br />
Please note - Refunds or Exchanges will not be accepted after 7 days outside of statutory obligations. Medicines, baby formula nad vitamin supplements refunds or exchanges will also not be accepted outside normal statutory obligations.</p>
</body>
</html>
