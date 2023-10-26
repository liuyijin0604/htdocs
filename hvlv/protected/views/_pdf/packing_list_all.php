<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>PCA Express Packing List</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
header { padding-bottom: 15px; }
footer { padding-top: 10px; page-break-after: always; }
div { width: 100%; }
/*div.task { page-break-after: always; width: 100%; }*/
/*div.end { page-break-before: avoid; }*/
div.subtask { page-break-inside: avoid; }
div.items { display: inline-block; vertical-align: top; width: 100%; text-align: center; margin-top: 10px; }
div.qr { display: inline-block; vertical-align: top; width: 50%; }
span { font-size: 20px; }
</style>
</head>

<body width="1120">
	<?php foreach ($tasks as $org => $subtasks) { ?>
		<div class="task">
			<?php if (empty($subtasks)) continue; ?>
			<h1 style="text-align: center; font-size: 28px; font-weight: bold; padding: 10px;" colspan="2"><?=Org::model()->findByPk($org)->name . '&nbsp;&nbsp;&nbsp;&nbsp;' . date('Y-m-d')?></h1>
			<?php foreach ($subtasks as $loc => $loctasks) { ?>
				<?php foreach ($loctasks as $prod => $prodtasks) { ?>
					<?php foreach ($prodtasks as $model) { ?>
						<div class="subtask">
							<div class="items">
								<span><b>Task No:</b> <?=$model->getNo();?></span>
								<div class="qr">
									<?php
										Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
										$qr = new TCPDF2DBarcode('T' . sprintf('%06d', $model->id) . ' complete', 'QRCODE, H');
										echo $qr->getBarcodeSVG(5, 5, 'black');
									?>
								</div>
							</div>
							<div class="items">
								<table width="100%" cellspacing="0" class="chart" style="margin-bottom: 30px;">
									<thead>
										<tr style="background: rgba(100,100,100,0.4);">
											<th align="left" width="40">No</th>
											<th align="left" width="150">SKU</th>
											<th align="left" width="320">Item</th>
											<th align="left" width="150">PLT</th>
											<th align="left" width="150">LOC</th>
											<th align="left" width="100">Qty</th>
										</tr>
									</thead>
									<tbody>
									<?php
									$items = [];
									foreach($model->items as $i => $itm) {
										$qty = intval($itm->mdata['uq']);
										if (empty($itm->mdata['si'])) continue;
										$s = WmsStock::model()->findByPk($itm->mdata['si']);
										$locs = $s->getBestLocs($qty);
										if (!empty($locs[1])) {
											if (empty($items[sprintf('%06d', $locs[1][0][0]->loc->parent->wt) . $locs[1][0][0]->loc->name . $s->prod->ean])) {
												$items[sprintf('%06d', $locs[1][0][0]->loc->parent->wt) . $locs[1][0][0]->loc->name . $s->prod->ean] = [$s->prod->ean, $s->prod->name, $locs[1][0][0]->loc->name, $locs[1][0][0]->loc->parent->name, 0];
											}
											$items[sprintf('%06d', $locs[1][0][0]->loc->parent->wt) . $locs[1][0][0]->loc->name . $s->prod->ean][4] += $qty;
										} else {
											$items['no stock' . 'no stock' . $s->prod->ean] = [$s->prod->ean, $s->prod->name, 'no stock', 'no stock', $qty];
										}
									}
									ksort($items);
									$i = 0;
									foreach ($items as $item) {
										echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td>'.($i+1).'</td><td>'.$item[0].'</td><td>'.$item[1].'</td><td>'.$item[2].'</td><td>'.$item[3].'</td><td>'.$item[4].'</td></tr>';
									}
									?>
									</tbody>
									<tfoot>
									</tfoot>
								</table>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</div>
	<?php } ?>
	<div class="end"></div>
</body>
</html>
