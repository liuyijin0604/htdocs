<?php
/* @var $this DeconsolidationController */
/* @var $model Deconsolidation */

$this->breadcrumbs=array(
	'Deconsolidations'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Deconsolidation', 'url'=>array('index')),
	array('label'=>'Create Deconsolidation', 'url'=>array('create')),
	array('label'=>'View Deconsolidation', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Deconsolidation', 'url'=>array('admin')),
);
?>

<h1>Update Deconsolidation <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>