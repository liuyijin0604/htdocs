<?php
/* @var $this PickupBookingController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Pickup Bookings',
);

$this->menu=array(
	array('label'=>'Create PickupBooking', 'url'=>array('create')),
	array('label'=>'Manage PickupBooking', 'url'=>array('admin')),
);
?>

<h1>Pickup Bookings</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
