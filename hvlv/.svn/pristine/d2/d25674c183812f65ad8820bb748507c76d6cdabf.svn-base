<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]          Packing List (Multi Items) <?=$no . ' - Multi'?>' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>PCA Express Packing List (Multi Items)</title>
<style type="text/css">
* { margin: 0; padding: 0; letter-spacing: normal !important; }
body { font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th { border: 1px #999 solid; padding: 2px; border-right: none; }
table.chart td { border-top: none; }
table.chart { border: none; border: 1px #999 solid; border-top: none; border-left: none; border-bottom: none; }
tr.even td, tr.even th { background: rgba(200, 200, 200, 0.6); }
tr { page-break-inside: avoid; }
div { width: 100%; }
div.qr { width: 100%; text-align: center; padding: 10px; }
</style>
</head>

<body width="1120">
	<h1 style="text-align: center;">Packing List (Multi Items) <?=$no . ' - Multi'?></h1>
	<p><?php foreach ($batch as $task) {
		echo $task->getNo() . ', ';
	} ?></p>
	<div class="qr">
		<?php
			Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
			$qr = new TCPDF2DBarcode(str_replace(' ', '', $no . '-' . WmsBatch::WMS_BATCH_TYPE_MULTI), 'QRCODE, H');
			echo $qr->getBarcodeSVG(5, 5, 'black');
		?>
	</div>
	<div class="task">
		<table width="100%" cellspacing="0" class="chart">
			<thead>
				<tr style="background: rgba(100,100,100,0.4);">
					<th align="left" width="40">No</th>
					<th align="left" width="150">SKU</th>
					<th align="left" width="360">Item</th>
					<th align="left" width="120">PLT</th>
					<th align="left" width="120">LOC</th>
					<th align="left" width="50">Qty</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$stocks = [];
				foreach ($batch as $task) {
					foreach ($task->items as $item) {
						if (empty($item->mdata['si'])) continue;
						if (empty($stocks[$item->mdata['si']])) {
							$stocks[$item->mdata['si']] = 0;
						}
						$stocks[$item->mdata['si']] += intval($item->mdata['uq']);
					}
				}

				$items = WmsTask::_nextItemBatchPick($stocks);
				$i = 0;
				foreach ($items as $item) {
					echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td>'.($i+1).'</td><td>'.$item[0].'</td><td>'.$item[1].'</td><td>'.$item[2].'</td><td>'.$item[3].'</td><td>'.$item[4].'</td></tr>';
				}
				?>
			</tbody>
		</table>
	</div>
</body>
</html>
