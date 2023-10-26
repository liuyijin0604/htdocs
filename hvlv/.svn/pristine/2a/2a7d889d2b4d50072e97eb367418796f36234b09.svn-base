<?php
/* @var $this ExImageController */
/* @var $model ExImage */

$this->breadcrumbs=array(
	'Ex Images'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ExImage', 'url'=>array('index')),
	array('label'=>'Create ExImage', 'url'=>array('create')),
	array('label'=>'Update ExImage', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ExImage', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ExImage', 'url'=>array('admin')),
);
?>

<h1>View ExImage #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'file_adr',
		'hbn',
		'islinked',
		'agent_id',
		'pdf_number',
		'date',
	),
)); ?>
