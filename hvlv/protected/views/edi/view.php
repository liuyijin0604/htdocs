<h1><?=$this->t('View Consol');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'fwd_id',
		'awb',
		'flight',
		'pol',
		'pod',
		'eta',
		'created',
		'status',
		'meta',
	),
)); ?>
