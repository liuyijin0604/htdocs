<?php
$sl = new WmsStockLocation('search');
$sl->unsetAttributes();
$sl->qty = '>0';
if(!empty($_GET['WmsStockLocation'])){
	$sl->attributes = $_GET['WmsStockLocation'];
}
$sl->stock_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wms-stock-loc-grid',
	'cssFile' => false,
	'dataProvider'=>$sl->search(),
	'filter' => $sl,
	'columns'=>array(
		array('name' => 'loc_code', 'value' => '$data->loc->name'),
		array('header' => 'Area', 'value' => 'empty($data->loc->parent)? "" : $data->loc->parent->name'),
		array('header' => 'Warehouse', 'value' => '($data->loc->id)<=10? "":$data->loc->warehouse->name'),
		'qty',
		'updated',
	),
)); ?>