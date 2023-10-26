<?php
/* @var $this PickupBookingController */
/* @var $model PickupBooking */

$this->breadcrumbs=array(
	'Pickup Bookings'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List PickupBooking', 'url'=>array('index')),
	array('label'=>'Create PickupBooking', 'url'=>array('create')),
	array('label'=>'Update PickupBooking', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete PickupBooking', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage PickupBooking', 'url'=>array('admin')),
);
?>

<h1>View PickupBooking #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'create_date',
		'booking_time',
		'status',
		'driver_name',
		'rego',
		'company_name',
		'company_email',
		'note',
		'fee',
		'meta',
	),
)); ?>
