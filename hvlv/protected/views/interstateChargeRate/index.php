<?php
/* @var $this InterstateChargeRateController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Interstate Charge Rates',
);

$this->menu=array(
	array('label'=>'Create InterstateChargeRate', 'url'=>array('create')),
	array('label'=>'Manage InterstateChargeRate', 'url'=>array('admin')),
);
?>

<h1>Interstate Charge Rates</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
