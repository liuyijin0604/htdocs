<h1 style="font-size: 50px"><?=$plt?></h1>
<h1 style="font-size: 40px"><?=$task->job->customer->name . '&nbsp;&nbsp;&nbsp;'. $task->getNo() . ' / ' . $task->ref?></h1>

<?php
$stocks = [];
foreach ($ledgers as $ledger) {
	if (empty($stocks[$ledger->stock_id])) {
		$stocks[$ledger->stock_id] = 0;
	}
	$stocks[$ledger->stock_id] += $ledger->qty_out;
}
echo '<table border="0" style="font-size: 30px">';
foreach ($stocks as $id => $qty) {
	$stock = WmsStock::model()->findByPk($id);
	$pack = WmsProdPack::model()->find('prod_id = :pid AND type = 10', [':pid' => $stock->prod_id]);
	$org = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $stock->prod_id, ':oid' => $stock->org_id]);
	$cq = $stock->prod->uq2cq(intval($qty));
	echo '<tr><td>Prod. Name:</td><td>' . $stock->prod->name . '</td></tr>';
	echo '<tr><td>Prod. Brand:</td><td>' . $stock->prod->brand . '</td></tr>';
	echo '<tr><td>Prod. Model:</td><td>' . $stock->prod->model . '</td></tr>';
	echo '<tr><td>Prod. Outer:</td><td>' . @$pack->barcode . '</td></tr>';
	echo '<tr><td>Prod. EAN:</td><td>' . $stock->prod->ean . '</td></tr>';
	echo '<tr><td>Prod. SKU/PLU:</td><td>' . @$org->sku . '</td></tr>';
	echo '<tr><td>Expiry Date:</td><td>' . $stock->expiry . '</td></tr>';
	echo '<tr><td>Batch:</td><td>' . $stock->batch . '</td></tr>';
	echo '<tr><td>Cartons:</td><td>' . $cq . '</td></tr>';
	echo '<tr><td>Unit:</td><td>' . $qty . '</td></tr>';
}
echo '</table>';
?>
