<?php
/* @var $this AutoCommandLogController */
/* @var $model AutoCommandLog */

$this->breadcrumbs=array(
	'Auto Command Logs'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List AutoCommandLog', 'url'=>array('index')),
	array('label'=>'Create AutoCommandLog', 'url'=>array('create')),
	array('label'=>'Update AutoCommandLog', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete AutoCommandLog', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage AutoCommandLog', 'url'=>array('admin')),
);
?>

<h1>View AutoCommandLog #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'command_name',
		'function_name',
		'type',
		'time',
	),
)); ?>
