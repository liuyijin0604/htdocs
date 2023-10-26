<h1><?=$this->t('View Email');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array('name' => 'type', 'value' => $model->getType()),
		'sent',
		array('name' => 'from', 'value' => $model->by->getName()),
		'to',
		'subject',
		array('name' => 'body', 'type' => 'raw'),
		array('name' => 'attachments', 'type' => 'raw', 'value' => $model->attachmentLinks()),
	),
)); ?>
