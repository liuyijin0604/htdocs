<?php
$sl = new WmsStockLedger('search');
$sl->unsetAttributes();
if(!empty($_GET['WmsStockLedger'])){
	$sl->attributes = $_GET['WmsStockLedger'];
}
$sl->location_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wms-stock-ldgr-grid',
	'cssFile' => false,
	'dataProvider'=>$sl->search(),
	'filter'=>$sl,
	'columns'=>array(
		array('header' => 'Stock', 'value' => '$data->stock->stockName()'),
		array('header' => 'Customer', 'value' => '$data->stock->customer->shortName(2)'),
		array('name' => 'task_id', 'type' => 'raw', 'value' => 'empty($data->taskItem->task)? "" : "<a class=\"tab_link\" href=\"".Yii::App()->createUrl("wmsTask/update", ["id" => $data->taskItem->task->link_id])."\" title=\"".$data->taskItem->task->getNo()."\">".$data->taskItem->task->getNo()."</a>"'),
		'qty_in',
		'qty_out',
		'ts',
	),
)); ?>