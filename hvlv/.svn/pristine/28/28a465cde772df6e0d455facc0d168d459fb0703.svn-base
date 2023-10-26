<?php
/* @var $this CargoBookingController */
/* @var $model CargoBooking */

$this->breadcrumbs=array(
	'Cargo Bookings'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List CargoBooking', 'url'=>array('index')),
	array('label'=>'Create CargoBooking', 'url'=>array('create')),
	array('label'=>'Update CargoBooking', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete CargoBooking', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage CargoBooking', 'url'=>array('admin')),
);
?>

<h1>View CargoBooking #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'create_date',
		'booking_date_1',
		'booking_date_2',
		'status',
		'delivery_period',
		'org_id',
		'shipment_id',
		'booking_number',
		'meta',
		'destination',
		'fee',
		'ref',
		'cargo_process_id',
	),
)); ?>
