<?php
/* @var $this DeconsolidationController */
/* @var $model Deconsolidation */

$this->breadcrumbs=array(
	'Deconsolidations'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Deconsolidation', 'url'=>array('index')),
	array('label'=>'Create Deconsolidation', 'url'=>array('create')),
	array('label'=>'Update Deconsolidation', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Deconsolidation', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Deconsolidation', 'url'=>array('admin')),
);
?>

<h1>View Deconsolidation #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'shipment_id',
		'cargo_process_id',
		'status',
		'depot',
		'create_time',
		'op_complete_time',
		'warehouse_complete_time',
		'meta',
		'has_problem',
		'assigned_user',
	),
)); ?>
