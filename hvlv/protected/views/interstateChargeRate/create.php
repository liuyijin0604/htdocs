<?php
/* @var $this InterstateChargeRateController */
/* @var $model InterstateChargeRate */

$this->breadcrumbs=array(
	'Interstate Charge Rates'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List InterstateChargeRate', 'url'=>array('index')),
	array('label'=>'Manage InterstateChargeRate', 'url'=>array('admin')),
);
?>

<h1>Create InterstateChargeRate</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>