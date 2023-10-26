<h1>View Org - <?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array(
            'name'=>'type',
            'value'=>$model->getType(),
        ),
		'name',
		'code',
		'address',
		'suburb',
		'postcode',
		'country',
		'email',
		'phone',
		'fax',
		'desc',
		array(
            'name'=>'active',
            'value'=>$model->getStatus(),
        ),
	),
)); ?>
