<h1><?=$this->t('View Payment');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array('name' => 'client', 'value' => $model->cust->name),
		'date',
		array('name' => 'status', 'value' => $model->getStatus()),
		array('name' => 'bank', 'value' => $model->getBank()),
		array('name' => 'amount', 'value' => $model->getCurrency().' '.$model->amount),
		array('name' => 'type', 'value' => $model->getType()),
		'ref',
		'note',
	),
));
?>
