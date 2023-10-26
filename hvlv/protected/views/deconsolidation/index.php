<?php
/* @var $this DeconsolidationController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Deconsolidations',
);

$this->menu=array(
	array('label'=>'Create Deconsolidation', 'url'=>array('create')),
	array('label'=>'Manage Deconsolidation', 'url'=>array('admin')),
);
?>

<h1>Deconsolidations</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
