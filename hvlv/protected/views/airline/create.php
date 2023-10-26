<?php
/* @var $this AirlineController */
/* @var $model Airline */

$this->breadcrumbs=array(
	'Airlines'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Airline', 'url'=>array('index')),
	array('label'=>'Manage Airline', 'url'=>array('admin')),
);
?>
<div class="pane">
<h1>Create Airline</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>
</div>