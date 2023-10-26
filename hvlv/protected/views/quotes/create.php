<?php
/* @var $this QuotesController */
/* @var $model Quotes */

$this->breadcrumbs=array(
	'Quotes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Quotes', 'url'=>array('index')),
	array('label'=>'Manage Quotes', 'url'=>array('admin')),
);
?>
<div class="pane">
<h1>Create Quotes With Airline</h1>
 

<?php $this->renderPartial('_form1', array(
			'model'=>$model,
			'modelR'=>$modelR,
		)); ?>

</div>