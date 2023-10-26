<?php
/* @var $this SeizedShipmentController */
/* @var $model SeizedShipment */

$this->breadcrumbs=array(
	'Seized Shipments'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List SeizedShipment', 'url'=>array('index')),
	array('label'=>'Create SeizedShipment', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#seized-shipment-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Seized Shipments</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'seized-shipment-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		['name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"" . Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id)) . "\" class=\"tab_link\" title=\"" . $data->shipment->hbn . "\">" . $data->shipment->getHbn() . "</a>"'],
		['name' => 'consol_no', 'type' => 'raw', 'value' => '"<a href=\"" . Yii::app()->createURL("imcoConsol/update", array("id" => $data->consol_id)) . "\" class=\"tab_link\" title=\"" . @$data->consol->no . "\">" . @$data->consol->no . "</a>"'],
		['name' => 'agent_id', 'value' => '$data->shipment->agent_id'],
		['name' => 'type', 'value' => 'SeizedShipment::$types["$data->type"]'],
		['name' => 'shipment_status', 'value' => 'ImParcel::$states["$data->shipment_status"]'],
		['name' => 'user_id', 'type' => 'raw', 'value' => '$data->user->getFullName()'],
		[
			'class'=>'oButtonColumn',
			'header' => 'Invoice',
			'template'=>'{print}',
			'buttons'=>[
				'print' => [
					'imageUrl'=>false,
					'options' => ['class' => 'grid_print_btn', 'target' => '_blank'],
					'visible' => '!empty($data->getInvoiceId()) ? true : false',
					'url' => 'Yii::app()->createUrl("invoice/print", ["id" => $data->getInvoiceId()])',
					'label' => 'Print',
				],
			],
		],
		'create_time',
	),
)); ?>
