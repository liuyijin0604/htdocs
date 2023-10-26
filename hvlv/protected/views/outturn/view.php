<h1><?=$this->t('View Outturn');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'dt',
		'status',
		'meta',
	),
)); ?>
