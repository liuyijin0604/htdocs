<?php
/* @var $this AutoCommandsController */
/* @var $model AutoCommands */

$this->breadcrumbs=array(
	'Auto Commands'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List AutoCommands', 'url'=>array('index')),
	array('label'=>'Create AutoCommands', 'url'=>array('create')),
	array('label'=>'Update AutoCommands', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete AutoCommands', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage AutoCommands', 'url'=>array('admin')),
);
?>

<h1>View AutoCommands #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'command_name',
		'func_name',
		'day',
		'week_day',
		'hour',
		'minute',
	),
)); ?>
