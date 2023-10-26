<h1><?=$this->t('View Accs');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'pid',
		'type',
		'status',
		'code',
		'bwf',
		'meta',
	),
)); ?>
