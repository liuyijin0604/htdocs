<h1><?=$this->t('View ExChannel');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'code',
		'name',
		'pod',
		'status',
		'rules',
		'meta',
	),
)); ?>
