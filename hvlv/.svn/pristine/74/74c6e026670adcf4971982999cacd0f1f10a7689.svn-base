<h1><?=$this->t('View Manifest');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'awb',
		array('name' => 'status', 'value' => $model->getStatus()),
		array('name' => 'fwd_id', 'value' => $model->agent->name),
		'pol',
		'pod',
		'eta',
		array('name' => 'amf_id', 'value' => $model->aman->name),
		array('name' => 'tmf_id', 'value' => empty($model->tman)? 'N/A' : $model->tman->name),
		array('name' => 'by_id', 'value' => $model->creator->name),
		'created',
	),
)); ?>
