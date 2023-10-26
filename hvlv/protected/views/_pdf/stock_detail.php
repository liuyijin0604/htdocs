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
</style>
</head>

<body width="1120">
<header>
<table width="100%" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td valign="top" width="50%"><table border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td style="font-size: 28px; font-weight: bold;" colspan="2">Invoice Detail for: <?=$inv->no;?></td>
					</tr>
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
						<td><?=$inv->date;?></td>
					</tr>
					<tr>
						<td style="font-weight: bold">Invoice No.:</td>
						<td><?=empty($inv->no)? $inv->id : $inv->no;?></td>
					</tr>
					<?php
						if($inv->type == 60){ //WMS
							echo '<tr><td style="font-weight: bold" valign="top">Billing From:</td><td>', $inv->mdata['billfrom'], '</td></tr>';
							echo '<tr><td style="font-weight: bold" valign="top">Billing To:</td><td>', $inv->mdata['billto'], '</td></tr>';
							echo '<tr><td style="font-weight: bold" valign="top">Terms:</td><td>', $inv->mdata['payterm'], '</td></tr>';
						}

						if(!empty($mans)){
							echo '<tr><td style="font-weight: bold" valign="top">Manifest #:</td>
						<td>', implode(',', $mans), '</td></tr>';
						}
						if(!empty($cno)){
							echo '<tr><td style="font-weight: bold" valign="top">Consol #:</td>
						<td>', implode(',', $cno), '</td></tr>';
						}
						if(!empty($awb)){
							echo '<tr><td style="font-weight: bold" valign="top">MAWB:</td>
						<td>', implode(',', $awb), '</td></tr>';
						}
						if(!empty($inv->ref)){
							echo '<tr><td style="font-weight: bold" valign="top">Ref #:</td>
						<td>', $inv->ref, '</td></tr>';
						}
					?>
					<tr>
						<td style="font-weight: bold" width="180">Due Date:</td>
						<td><?=$inv->due;?></td>
					</tr>
				</tbody>
			</table></td>
		</tr></table>
</header>
<table width="100%" cellspacing="0" class="chart" style="font-size:0.8em">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
				<th align="left" width="100">Prod Name</th>
				<th align="left" width="100">Expiry</th>
				<th align="left" width="100">Batch</th>
				<th align="left" width="100">PLT No.</th>
				<th align="right" width="100">Amount</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			$rs = WmsStock::model()->findAll('org_id = :oid', [':oid' => $inv->to_id]);
			foreach ($rs as $stock) {
				$locs = [];
				foreach ($stock->locs as $sl) {
					$locs[$sl->loc->name] = $sl->qty;
				}
				$ledgers = WmsStockLedger::model()->findAll('stock_id = :sid AND location_id > 99 AND qty_out > 0 AND ts >= :fdate AND ts < :tdate', [':sid' => $stock->id, ':fdate' => date('Y-m-d H:i:s', strtotime($inv->mdata['billfrom'])), ':tdate' => date('Y-m-d H:i:s', strtotime($inv->mdata['billto'] . ' + 1 day'))]);
				foreach ($ledgers as $l) {
					if (empty($locs[$l->loc->name])) {
						$locs[$l->loc->name] = 0;
					}
					$locs[$l->loc->name] += $l->qty_out;
				}
				foreach ($locs as $loc => $qty) {
					echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td valign="top">' . $stock->prod->name . '</td><td valign="top">' . $stock->expiry . '</td><td valign="top">' . $stock->batch . '</td><td valign="top">' . $loc . '</td><td valign="top" align="right">' . $qty . '</td></tr>';
				}
			}
			?>
			<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		</tbody>
		<tfoot>
		</tfoot>
</table>
<footer>
</footer>
<?php include('_pagination.php'); ?>
</body>
</html>
