<?php
/* @var $this GoogleReviewController */
/* @var $model GoogleReview */

$this->breadcrumbs=array(
	'Google Reviews'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List GoogleReview', 'url'=>array('index')),
	array('label'=>'Create GoogleReview', 'url'=>array('create')),
	array('label'=>'Update GoogleReview', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete GoogleReview', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage GoogleReview', 'url'=>array('admin')),
);
?>

<h1>View GoogleReview #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'hbn',
		'ref',
		'time_start',
		'time_done',
		'cnee_name',
		'cnee_email',
		'cnee_tel',
		'cnor_city',
		'cnor_state',
		'pkg',
		'weight',
		'time_arrived',
		'shipment_id',
		'cargo_process_id',
		'status',
	),
)); ?>
