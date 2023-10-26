<?php
$sl = new WmsStockLedger('search');
$sl->unsetAttributes();
if(!empty($_GET['WmsStockLedger'])){
	$sl->attributes = $_GET['WmsStockLedger'];
}
$sl->stock_id = $model->id;
$ec = new CDbCriteria;
$ec->addCondition('location_id > 10 AND (qty_in != 0 OR qty_out != 0)');

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wms-stock-ldgr-grid2',
	'cssFile' => false,
	'dataProvider'=>$sl->search(true, 30, $ec),
	'filter'=>$sl,
	'columns'=>array(
		array('name' => 'loc_code', 'value' => '$data->loc->name'),
		array('name' => 'l2_code', 'value' => 'empty($data->l2_id)? "" : $data->l2->name'),
		array('header' => 'Warehouse', 'value' => '($data->loc->id)<=10? "":$data->loc->warehouse->name'),
		array('name' => 'task_id', 'type' => 'raw', 'value' => 'empty($data->taskItem->task)? "" : "<a class=\"tab_link\" href=\"".Yii::App()->createUrl("wmsTask/update", ["id" => empty($data->taskItem->task->link_id) ? $data->taskItem->task->id : $data->taskItem->task->link_id])."\" title=\"".$data->taskItem->task->getNo()."\">".$data->taskItem->task->getNo()."</a>"'),
		'qty_in',
		'qty_out',
		'ts',
		array('name' => 'balance', 'value' => '$data->stock->getBalance($data->ts)'),
	),
)); ?>