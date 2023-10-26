<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>Top Logistics Invoice</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
header { padding-bottom: 15px; }
footer { padding-top: 10px; page-break-after: always; }
table { page-break-inside: avoid; }
</style>
</head>

<body width="1120">
<header>
<?php
$inv = $invs[0];
include('_header_tla.php');
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td style="text-align: center; font-size: 28px; font-weight: bold;padding: 10px;" colspan="2">Tax Invoice</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" width="50%"><table border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td valign="top" style="padding-bottom:3px;font-weight: bold;">Bill To:</td>
					</tr>
					<tr>
						<td valign="top" style="border: 1px #999 solid;padding:10px;" height="80" width="480"><span style="font-size:20px;font-weight:bold;"><?=$inv->mdata['name'];?></span>
							<br />
							<?=$inv->mdata['address'];?></td>
					</tr>
				</tbody>
			</table></td>
			<td width="50%" align="right" valign="top"><br />
		<table cellpadding="5" width="440">
				<tbody>
					<tr>
						<td style="font-weight: bold" width="180">Date:</td>
						<td><?=empty($date) ? date('Y-m-d') : $date;?></td>
					</tr>
				</tbody>
			</table></td>
		</tr></table>
</header>
<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
				<th align="left" width="100">No</th>
				<th align="left">Type</th>
				<th align="left" width="120">Date</th>
				<th align="left" width="120">Due</th>
				<th align="right" width="125">Total</th>
				<th align="right" width="125">Currency</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$tot = [0,0,0];
		foreach($invs as $i => $inv){
			echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td valign="top">'.$inv->no.'</td><td valign="top">'.$inv->getType().'</td><td>'.$inv->date.'</td><td>'.($inv->type != 100 ? $inv->due : $inv->posted).'</td><td align="right">'.AppHelper::money_format('%i', $inv->total).'</td><td>'.$inv->getCurrency().'</td></tr>';
				$tot[0] += $inv->total;
		}
		?>
			<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		</tbody>
		<tfoot>
		<tr><td>&nbsp;</td><th align="right" colspan="3">Total:</th><th align="right"><?php echo AppHelper::money_format('%i', $tot[0]);?></th><td>&nbsp;</td></tr>
		
		</tfoot>
</table>
<?php include('_pagination.php'); ?>
</body>
</html>
