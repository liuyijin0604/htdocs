<?php
/* @var $this WordReplaceUsageLogController */
/* @var $model WordReplaceUsageLog */

$this->breadcrumbs=array(
	'Word Replace Usage Logs'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List WordReplaceUsageLog', 'url'=>array('index')),
	array('label'=>'Create WordReplaceUsageLog', 'url'=>array('create')),
	array('label'=>'Update WordReplaceUsageLog', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete WordReplaceUsageLog', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage WordReplaceUsageLog', 'url'=>array('admin')),
);
?>

<h1>View WordReplaceUsageLog #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'org_id',
		'original_word',
		'replace_word',
		'meta',
		'process_time',
	),
)); ?>
