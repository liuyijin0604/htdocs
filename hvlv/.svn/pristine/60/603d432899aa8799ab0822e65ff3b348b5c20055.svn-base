<h1><?=$this->t('View Invoice');?> <?php echo $model->id; ?></h1>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'no',
		array('name' => 'to_name', 'value' => $model->cust->name),
		'date',
		'due',
		array('name' => 'status', 'value' => $model->getStatus()),
		array('name' => 'type', 'value' => $model->getType()),
		array('name' => 'total', 'value' => $model->getCurrency().' '.$model->total),
	),
));
?>