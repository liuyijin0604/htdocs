<?php
/* @var $this WcaMembersController */
/* @var $model WcaMembers */

$this->breadcrumbs=array(
	'Wca Members'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List WcaMembers', 'url'=>array('index')),
	array('label'=>'Create WcaMembers', 'url'=>array('create')),
	array('label'=>'Update WcaMembers', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete WcaMembers', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage WcaMembers', 'url'=>array('admin')),
);
?>

<h1>View WcaMembers #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'email',
		'phone',
		'sent',
		'dt',
		'meta',
	),
)); ?>
