<h1><?=$this->t('View WmsJob');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'org_id',
		'oc_id',
		'sales_id',
		'pm_id',
		'no',
		'po',
		'ref',
		'type',
		'status',
		'bwf',
		'meta',
	),
)); ?>
