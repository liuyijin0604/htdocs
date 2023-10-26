<?php
$from = !empty($_GET['from']) ? date('Y-m-d 00:00:00', strtotime($_GET['from'])) : '1970-01-01';
$to = !empty($_GET['to']) ? date('Y-m-d 23:59:59', strtotime($_GET['to'])) : date('Y-m-d H:i:s');
echo '<input type="hidden" id="from_hidden" value="' . $from . '">';
echo '<input type="hidden" id="to_hidden" value="' . $to . '">';

$orgs = [];
$spl = 'SELECT stock.org_id AS org, COUNT(DISTINCT ledger.location_id) AS num FROM wms_stock_ledger ledger JOIN wms_stock stock ON ledger.stock_id = stock.id JOIN org customer ON stock.org_id = customer.id JOIN org owner ON customer.by = owner.id JOIN wms_task_item item ON item.id = ledger.ti_id JOIN wms_task task ON task.id = item.task_id JOIN wms_task main ON task.link_id = main.id WHERE (JSON_VALUE(customer.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(customer.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')) AND ledger.location_id > 10 AND ledger.qty_in > 0 AND ledger.ts >= "' . $from . '" AND ledger.ts <= "' . $to .'" AND main.bwf & 8 = 0 AND main.bwf & 16 = 0 GROUP BY stock.org_id';
$temp = Yii::app()->db->createCommand($spl)->queryAll();
$pallet_in = [];
foreach ($temp as $v) {
	if (!in_array($v['org'], $orgs)) {
		$orgs[] = $v['org'];
	}
	$pallet_in[$v['org']] = $v['num'];
}

$spl = 'SELECT job.org_id AS org, COUNT(DISTINCT ledger.location_id) AS num FROM wms_task task JOIN wms_task_item item ON task.id = item.task_id JOIN wms_stock_ledger ledger ON item.id = ledger.ti_id JOIN wms_job job ON task.job_id = job.id JOIN org customer ON job.org_id = customer.id JOIN org owner ON customer.by = owner.id WHERE (JSON_VALUE(customer.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(customer.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')) AND ledger.location_id > 10 AND ledger.qty_out > 0 AND task.type IN (3010, 2030) AND ledger.ts >= "' . $from . '" AND ledger.ts <= "' . $to . '" AND task.bwf & 8 = 0 AND task.bwf & 16 = 0 GROUP BY job.org_id';
$temp = Yii::app()->db->createCommand($spl)->queryAll();
$pick_pallet = [];
foreach ($temp as $v) {
	if (!in_array($v['org'], $orgs)) {
		$orgs[] = $v['org'];
	}
	$pick_pallet[$v['org']] = $v['num'];
}

$spl = 'SELECT job.org_id AS org, COUNT(DISTINCT task.id) AS num FROM wms_task task JOIN wms_job job ON task.job_id = job.id JOIN org customer ON job.org_id = customer.id JOIN org owner ON customer.by = owner.id JOIN (SELECT * FROM log WHERE model = "WmsTask" AND JSON_VALUE(meta, "$.status") = "WIP") log ON log.lid = task.id WHERE (JSON_VALUE(customer.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(customer.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')) AND task.type = 2030 AND log.time >= "' . $from . '" AND log.time <= "' . $to . '" GROUP BY job.org_id';
$temp = Yii::app()->db->createCommand($spl)->queryAll();
$container_load = [];
foreach ($temp as $v) {
	if (!in_array($v['org'], $orgs)) {
		$orgs[] = $v['org'];
	}
	$container_load[$v['org']] = $v['num'];
}

$spl = 'SELECT job.owner_id AS org, SUM(cartage.plt) AS num FROM cartage JOIN job ON cartage.job_id = job.id JOIN org customer ON job.owner_id = customer.id JOIN org owner ON customer.by = owner.id WHERE (JSON_VALUE(customer.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(customer.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')) AND cartage.comp_time >= "' . $from . '" AND cartage.comp_time <= "' . $to . '" AND JSON_VALUE(cartage.meta, "$.proc_type") IN (1,3) GROUP BY job.owner_id';
$temp = Yii::app()->db->createCommand($spl)->queryAll();
$cartage = [];
foreach ($temp as $v) {
	if (!in_array($v['org'], $orgs)) {
		$orgs[] = $v['org'];
	}
	$cartage[$v['org']] = $v['num'];
}

$spl = 'SELECT job.owner_id AS org, SUM(cartage.plt) AS num FROM cartage JOIN job ON cartage.job_id = job.id JOIN org customer ON job.owner_id = customer.id JOIN org owner ON customer.by = owner.id WHERE (JSON_VALUE(customer.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(customer.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')) AND cartage.comp_time >= "' . $from . '" AND cartage.comp_time <= "' . $to . '" AND JSON_VALUE(cartage.meta, "$.proc_type") IN (2,3) GROUP BY job.owner_id';
$temp = Yii::app()->db->createCommand($spl)->queryAll();
$xray = [];
foreach ($temp as $v) {
	if (!in_array($v['org'], $orgs)) {
		$orgs[] = $v['org'];
	}
	$xray[$v['org']] = $v['num'];
}

$spl = 'SELECT job.owner_id AS org, SUM(JSON_VALUE(cartage.meta, "$.pmc")) AS num FROM cartage JOIN job ON cartage.job_id = job.id JOIN org customer ON job.owner_id = customer.id JOIN org owner ON customer.by = owner.id WHERE (JSON_VALUE(customer.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(customer.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')) AND cartage.comp_time >= "' . $from . '" AND cartage.comp_time <= "' . $to . '" GROUP BY job.owner_id';
$temp = Yii::app()->db->createCommand($spl)->queryAll();
$pmc = [];
foreach ($temp as $v) {
	if (!in_array($v['org'], $orgs)) {
		$orgs[] = $v['org'];
	}
	$pmc[$v['org']] = $v['num'];
}

$sql = 'SELECT SUM(plt) AS num FROM delivery_record WHERE created >= "' . $from . '" AND created <= "' . $to . '"';
$temp = Yii::app()->db->createCommand($sql)->queryAll();
$delivery_plt = $temp[0]['num'];

$sql = 'SELECT COUNT(*) AS num FROM delivery_record WHERE created >= "' . $from . '" AND created <= "' . $to . '"';
$temp = Yii::app()->db->createCommand($sql)->queryAll();
$delivery_truck = $temp[0]['num'];

$spl = 'SELECT owner.id AS org, COUNT(DISTINCT(wsl.location_id)) AS num FROM wms_stock_location wsl JOIN wms_stock ws ON wsl.stock_id = ws.id JOIN org owner ON owner.id = ws.org_id JOIN wms_location wl on wl.id = wsl.location_id WHERE (JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')) AND wsl.qty > 0 AND wsl.location_id > 10 AND wl.pid != 5 GROUP BY owner.id';
$temp = Yii::app()->db->createCommand($spl)->queryAll();
$total_pallet = [];
foreach ($temp as $v) {
	if (!in_array($v['org'], $orgs)) {
		$orgs[] = $v['org'];
	}
	$total_pallet[$v['org']] = $v['num'];
}

$spl = 'SELECT owner.id AS org, COUNT(DISTINCT(wsl.location_id)) AS num FROM wms_stock_location wsl JOIN wms_stock ws ON wsl.stock_id = ws.id JOIN org owner ON owner.id = ws.org_id JOIN wms_location wl on wl.id = wsl.location_id WHERE (JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')) AND wsl.qty > 0 AND wsl.location_id > 10 AND wl.pid != 5 AND wl.pid > 10 GROUP BY owner.id';
$temp = Yii::app()->db->createCommand($spl)->queryAll();
$inloc_pallet = [];
$inloc_pallet_percent = [];
foreach ($temp as $v) {
	if (!in_array($v['org'], $orgs)) {
		$orgs[] = $v['org'];
	}
	$inloc_pallet[$v['org']] = $v['num'];
	$inloc_pallet_percent[$v['org']] = $v['num'] / max(1, intval(@$total_pallet[$v['org']]));
}
?>

<?php
$filtersForm = new FiltersForm();
if (isset($_GET['FiltersForm'])) {
	$filtersForm->filters = $_GET['FiltersForm'];
}
$provide = [];
foreach ($orgs as $org) {
	if ($org == Org::ORGID_3PL_COBAYER) continue;
	$org = Org::model()->findByPk($org);
	$provide[] = ['id' => $org->id, 'name' => $org->name, 'pallet_in' => intval(@$pallet_in[$org->id]), 'pick_pallet' => intval(@$pick_pallet[$org->id]), 'container_load' => intval(@$container_load[$org->id]), 'cartage' => intval(@$cartage[$org->id]), 'xray' => intval(@$xray[$org->id]), 'pmc' => intval(@$pmc[$org->id]), 'total_pallet' => intval(@$total_pallet[$org->id]), 'inloc_pallet' => intval(@$inloc_pallet[$org->id]), 'inloc_pallet_percent' => round(floatval(@$inloc_pallet_percent[$org->id]) * 100, 2) . '%', 'delivery_record' => ''];
}
$filteredData = $filtersForm->filter($provide);
$sort = new CSort;
$sort->defaultOrder = 'id ASC';
$sort->attributes = array('id', 'name', 'pallet_in', 'pick_pallet', 'container_load', 'cartage', 'xray', 'pmc', 'total_pallet', 'inloc_pallet');
$dataProvider = new CArrayDataProvider($filteredData, array(
	'pagination' => false,
	'sort' => $sort,
));

echo '<br>';
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'report-grid',
	'cssFile' => false,
	'dataProvider' => $dataProvider,
	'filter' => $filtersForm,
	'columns' => array(
		array('name' => 'name', 'header' => 'Org'),
		array('name' => 'pallet_in', 'header' => '入库板数', 'footer' => array_sum($pallet_in)),
		array('name' => 'pick_pallet', 'header' => '出库板数', 'footer' => array_sum($pick_pallet)),
		array('name' => 'container_load', 'header' => '出库柜数', 'footer' => array_sum($container_load)),
		array('name' => 'cartage', 'header' => '送机场板数', 'footer' => array_sum($cartage)),
		array('name' => 'xray', 'header' => 'Xray板数', 'footer' => array_sum($xray)),
		array('name' => 'pmc', 'header' => 'PMC', 'footer' => array_sum($pmc)),
		array('name' => 'total_pallet', 'header' => '现存板数', 'footer' => array_sum($total_pallet)),
		array('name' => 'inloc_pallet', 'header' => '绑位板数', 'footer' => array_sum($inloc_pallet)),
		array('name' => 'inloc_pallet_percent', 'header' => '绑位比例', 'footer' => round(array_sum($inloc_pallet) / max(1, array_sum($total_pallet)) * 100, 2) . '%'),
		array('name' => 'delivery_record', 'header' => '卸车板数', 'footer' => $delivery_plt . '(总车数: ' . $delivery_truck . ')'),
	),
));
?>