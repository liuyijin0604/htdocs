<?php
/* @var $this ExImageController */
/* @var $model ExImage */

$this->breadcrumbs=array(
	'Ex Images'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ExImage', 'url'=>array('index')),
	array('label'=>'Manage ExImage', 'url'=>array('admin')),
);
?>

<h1>Create ExImage</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>