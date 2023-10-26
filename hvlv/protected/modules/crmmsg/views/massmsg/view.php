<h1><?=$this->t('View CRM Mass Message');?> - <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		'id',
		'crm_msg_id',
		'wechat_msg_id',
		'type',
		'time',
		'meta'
	),
)); ?>