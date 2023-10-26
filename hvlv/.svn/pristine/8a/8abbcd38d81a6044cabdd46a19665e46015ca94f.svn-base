<h1>View User #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array(
            'name'=>'type',
            'value'=>$model->getType(),
        ),
		array(
            'name'=>'org_id',
            'value'=>$model->org->name,
        ),
		'title',
		'fname',
		'lname',
		'email',
		'phone',
		'fax',
		'mobile',
		'since',
		array(
            'name'=>'active',
            'value'=>$model->getActive(),
        ),
	),
)); ?>
