<?php
/* @var $this CargoBookingController */
/* @var $model CargoBooking */

$this->breadcrumbs=array(
	'Cargo Bookings'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List CargoBooking', 'url'=>array('index')),
	array('label'=>'Create CargoBooking', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#cargo-booking-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<style>
	.cargo-booking-report-table {
		font-family: Arial, Helvetica, sans-serif;
		border-collapse: collapse;
		width: 100%;
	}

	.cargo-booking-report-table td, .cargo-booking-report-table th {
		border: 1px solid #ddd;
		padding: 8px;
	}

	.cargo-booking-report-table tr:nth-child(even) {
		background-color: #f2f2f2;
	}

	.cargo-booking-report-table tr:hover {
		background-color: #ddd;
	}

	.cargo-booking-report-table th {
		padding-top: 12px;
		padding-bottom: 12px;
		text-align: left;
		background-color: #04AA6D;
		color: white;
	}
</style>

<h1>Manage Cargo Bookings</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<div class="container" id="cargo_booking_report_container_<?= $model->depot ?>" style="max-width:40%">
	<table class="cargo-booking-report-table" id="cargo_booking_report_table_<?= $model->depot ?>">
		<tr>
			<th>Today New</th>
			<th>Today Complete</th>
			<th>MTD Complete %</th>
		</tr>
		<tr>
			<td><?= $todayNewBookingCount ?></td>
			<td><?= $todayCompleteBookingCount ?></td>
			<td><?= $mtdCompletePercent ?>% (<?= $mtdCompleteBookingCount ?> / <?= $mtdNewBookingCount + $mtdCompleteBookingCount?>)</td>
		</tr>
	</table>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'cargo-booking-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'booking_number', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("cargoBooking/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->booking_number. "\">".$data->booking_number."</a>"',),
		array('name' => 'ref','header'=>'Connote', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn. "\">".$data->shipment->hbn."</a>"',),
		'create_date',
		'booking_date_1',
		'booking_date_2',
		'confirmed_date',
		array('name' => 'status', 'value' => 'CargoBooking::$bookingStatus[$data["status"]]', 'filter'=>CHtml::dropDownList('CargoBooking[status]', $model->status, $this->t($model::$bookingStatus), ['prompt'=>$this->t('All')])),
		'delivery_period',
		array('header' => 'Unloading', 'value' => '$data->mdata["need_unloading"]'),
		'fee',
		'comment',
		//array('header' => 'Invoice No.', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("invoice/print", $data->mdata["invoiceId"])).\">".$data->getInvoiceNo()."</a>"',),
		array('header'=>'Invoice No.', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("invoice/print", array("id" => @$data->mdata["invoiceId"]))."\" target=\"_blank\">".$data->getInvoiceNo()."</a>"',),
		/*
		'org_id',
		'shipment_id',
		'booking_number',
		'meta',
		'destination',
		'fee',
		'ref',
		'cargo_process_id',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
