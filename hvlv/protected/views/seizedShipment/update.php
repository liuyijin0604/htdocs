<?php
/* @var $this SeizedShipmentController */
/* @var $model SeizedShipment */

$this->breadcrumbs=array(
	'Seized Shipments'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List SeizedShipment', 'url'=>array('index')),
	array('label'=>'Create SeizedShipment', 'url'=>array('create')),
	array('label'=>'View SeizedShipment', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage SeizedShipment', 'url'=>array('admin')),
);
?>

<h1>Update SeizedShipment <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>