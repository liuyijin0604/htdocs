<?php
$stmodel = new WmsStockLocation('search');
$stmodel->unsetAttributes();
$stmodel->location_id = $model->id;
$stmodel->qty = '>0';
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wms-stock-loc-grid',
	'cssFile' => false,
	'dataProvider'=>$stmodel->search(),
	'columns'=>array(
		['header' => 'Name', 'value' => '$data->stock->prod->name'],
		['header' => 'EAN', 'value' => '$data->stock->prod->ean'],
		['header' => 'Expiry', 'value' => '$data->stock->expiry'],
		['header' => 'Batch', 'value' => '$data->stock->batch'],
		'qty',
		'updated',
	),
)); ?>