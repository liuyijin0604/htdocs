<?php
/* @var $this WcaMembersController */
/* @var $model WcaMembers */

$this->breadcrumbs=array(
	'Wca Members'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List WcaMembers', 'url'=>array('index')),
	array('label'=>'Create WcaMembers', 'url'=>array('create')),
	array('label'=>'View WcaMembers', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage WcaMembers', 'url'=>array('admin')),
);
?>

<h1>Update WcaMembers <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>