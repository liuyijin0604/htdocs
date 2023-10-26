<h1><?=$this->t('View ConnoteRange');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'type',
		'status',
		'model',
		'fid',
		'prefix',
		'suffix',
		'start',
		'finish',
		'digits',
		'meta',
	),
)); ?>
