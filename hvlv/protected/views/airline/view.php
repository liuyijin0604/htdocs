<?php
/* @var $this AirlineController */
/* @var $model Airline */

$this->breadcrumbs=array(
	'Airlines'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Airline', 'url'=>array('index')),
	array('label'=>'Create Airline', 'url'=>array('create')),
	array('label'=>'Update Airline', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Airline', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Airline', 'url'=>array('admin')),
);
?>

<h1>View Airline #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'code',
	),
)); ?>
