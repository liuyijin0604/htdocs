<?php
/* @var $this SeizedShipmentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Seized Shipments',
);

$this->menu=array(
	array('label'=>'Create SeizedShipment', 'url'=>array('create')),
	array('label'=>'Manage SeizedShipment', 'url'=>array('admin')),
);
?>

<h1>Seized Shipments</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
