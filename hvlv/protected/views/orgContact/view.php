<h1><?=$this->t('View OrgContact');?> <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'name',
		'position',
		'address',
		'suburb',
		'state',
		'postcode',
		'country',
		'email',
		'phone',
		'fax',
		'mobile',
		'desc',
	),
)); ?>
