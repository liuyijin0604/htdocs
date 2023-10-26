<?php
/* @var $this PickupBookingSlotController */
/* @var $model PickupBookingSlot */

$this->breadcrumbs=array(
	'Pickup Booking Slots'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List PickupBookingSlot', 'url'=>array('index')),
	array('label'=>'Create PickupBookingSlot', 'url'=>array('create')),
	array('label'=>'View PickupBookingSlot', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage PickupBookingSlot', 'url'=>array('admin')),
);
?>

<h1>Update PickupBookingSlot <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>