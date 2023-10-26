<?php
/* @var $this SeizedShipmentController */
/* @var $model SeizedShipment */

$this->breadcrumbs=array(
	'Seized Shipments'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List SeizedShipment', 'url'=>array('index')),
	array('label'=>'Create SeizedShipment', 'url'=>array('create')),
	array('label'=>'Update SeizedShipment', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete SeizedShipment', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage SeizedShipment', 'url'=>array('admin')),
);
?>

<h1>View SeizedShipment #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'shipment_id',
		'consol_id',
		'create_time',
		'type',
		'shipment_status',
	),
)); ?>
