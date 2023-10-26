<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait --page-size A4" />
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

<body>
	<h1 style="font-size: 70px; margin-bottom: 20px; text-align: center">Shipping Mark</h1>

	<table border="1" style="font-size: 40px" width="100%">
		<tr style="line-height: 100px">
			<td align="center" width="35%">SPD审批单号:</td>
			<td align="center" width="65%"><?=$task->ref?></td>
		</tr>
	</table>

	<table border="1" style="font-size: 40px" width="100%">
		<tr style="line-height: 100px"><td>&nbsp;<?=$task->job->customer->name?></td></tr>
	</table>

	<table border="1" style="font-size: 40px" width="100%">
		<tr style="line-height: 100px">
			<td width="10%">&nbsp;&nbsp;</td>
			<td width="60%">&nbsp;Product Name</td>
			<td width="30%">&nbsp;Expiry Date</td>
		</tr>

		<?php
		$stocks = [];
		foreach ($ledgers as $ledger) {
			if (empty($stocks[$ledger->stock_id])) {
				$stocks[$ledger->stock_id] = 0;
			}
			$stocks[$ledger->stock_id] += $ledger->qty_out;
		}
		$count = 1;
		foreach ($stocks as $id => $qty) {
			$stock = WmsStock::model()->findByPk($id);
			$pack = WmsProdPack::model()->find('prod_id = :pid AND type = 10', [':pid' => $stock->prod_id]);
			$org = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $stock->prod_id, ':oid' => $stock->org_id]);
			$cq = $stock->prod->uq2cq(intval($qty));
			echo '<tr style="line-height: 100px"><td>&nbsp;' . ($count++) . '</td><td style="font-size: 30px">&nbsp;' . $stock->prod->name . '</td><td>&nbsp;' . $stock->expiry . '</td></tr>';
		}
		?>

	</table>

	<table border="1" style="font-size: 40px" width="100%">
		<tr style="line-height: 100px">
			<td width="35%">&nbsp;Pallet No:</td>
			<td width="65%">&nbsp;</td>
		</tr>
	</table>

	<table border="1" style="font-size: 40px" width="100%">
		<tr style="line-height: 100px">
			<td width="35%">&nbsp;Dimension:</td>
			<td width="65%">&nbsp;</td>
		</tr>
	</table>
</body>

</html>