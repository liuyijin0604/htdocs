<?php
/* @var $this FileRepoController */
/* @var $model FileRepo */

$this->breadcrumbs=array(
	'File Repos'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List FileRepo', 'url'=>array('index')),
	array('label'=>'Create FileRepo', 'url'=>array('create')),
	array('label'=>'View FileRepo', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage FileRepo', 'url'=>array('admin')),
);
?>

<h1>Update FileRepo <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form_file', array('model'=>$model)); ?>