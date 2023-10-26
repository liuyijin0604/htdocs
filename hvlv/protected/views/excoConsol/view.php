<h1><?=$this->t('View Export Consol');?> <?php echo $model->no; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'no',
		'owner_id',
		'dpt_id',
		'type',
		'awb',
		'airline',
		'flight',
		'pol',
		'pod',
		'etd',
		'eta',
		'created',
		'status',
		'meta',
	),
)); ?>
