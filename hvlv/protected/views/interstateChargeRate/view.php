<?php
/* @var $this InterstateChargeRateController */
/* @var $model InterstateChargeRate */

$this->breadcrumbs=array(
	'Interstate Charge Rates'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List InterstateChargeRate', 'url'=>array('index')),
	array('label'=>'Create InterstateChargeRate', 'url'=>array('create')),
	array('label'=>'Update InterstateChargeRate', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete InterstateChargeRate', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage InterstateChargeRate', 'url'=>array('admin')),
);
?>

<h1>View InterstateChargeRate #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'departure_depot',
		'destination_depot',
		'org_id',
		'weight_low',
		'weight_high',
		'base',
		'item',
		'perkg',
		'per_pallet',
		'minimum',
		'type',
		'start_date',
	),
)); ?>
