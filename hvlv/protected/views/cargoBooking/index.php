<?php
/* @var $this CargoBookingController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Cargo Bookings',
);

$this->menu=array(
	array('label'=>'Create CargoBooking', 'url'=>array('create')),
	array('label'=>'Manage CargoBooking', 'url'=>array('admin')),
);
?>

<h1>Cargo Bookings</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
