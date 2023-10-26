<?php
/* @var $this PickupBookingSlotController */
/* @var $model PickupBookingSlot */

$this->breadcrumbs=array(
	'Pickup Booking Slots'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List PickupBookingSlot', 'url'=>array('index')),
	array('label'=>'Create PickupBookingSlot', 'url'=>array('create')),
	array('label'=>'Update PickupBookingSlot', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete PickupBookingSlot', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage PickupBookingSlot', 'url'=>array('admin')),
);
?>

<h1>View PickupBookingSlot #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'depot',
		'slot_time',
		'status',
	),
)); ?>
