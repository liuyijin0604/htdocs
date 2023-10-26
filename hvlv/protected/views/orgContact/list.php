<h1><?=$this->t('Org. Contacts');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'org-contact-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//array('name' => 'type', 'value' => '$data->getType()','filter'=>CHtml::dropDownList('OrgContact[type]', $contacts->type, OrgContact::$types, array('prompt'=>$this->t('All'))),	),
		'name',
		'position',
		array('name' => 'org_name', 'value' => '$data->org->shortName()'),
		'email',
		'phone',
		'mobile',
		array('name' => 'status', 'value' => '$data->getStatus()',
			'filter' => CHtml::dropDownList('OrgContact[status]', $model->status, $model::$states, array('prompt'=>$this->t('All'))),
		),
		/*
		'address',
		'suburb',
		'state',
		'postcode',
		'country',
		'fax',
		'desc',
		'active',
		'meta',
		*/
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}{update}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"OC-".$data->id'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	tab.bind('onOpen', function(){
		$('#org-contact-grid', tab.data('panel')).yiiGridView('update');
	});
});
</script>
