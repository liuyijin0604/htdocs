<h1><?=$this->t('View ExProdb');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'brand',
		'model',
		'hs',
		'code',
		'unit',
		'price',
		'weight',
		'tag',
		'note',
	),
)); ?>
