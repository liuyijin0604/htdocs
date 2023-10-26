<?php
echo '<a class="jqm_link" href="orgContact/create/'.$model->id.'"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('Add Contact').'</a>';
$contacts = new OrgContact('search');
$contacts->org_id = $model->id;
if(!empty($_GET['OrgContact'])){
	$contacts->setAttributes($_GET['OrgContact']);
}
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_org-contact-grid',
	'cssFile' => false,
	'dataProvider'=>$contacts->search(),
	'filter'=>$contacts,
	'columns'=>array(
		//array('name' => 'type', 'value' => '$data->getType()','filter'=>CHtml::dropDownList('OrgContact[type]', $contacts->type, OrgContact::$types, array('prompt'=>$this->t('All'))),	),
		'name',
		'position',
		array(
			'name'=>'email',
			'type' => 'raw',
			'value' => 'CHtml::link($data->email, "mailto:".$data->email)',
		),
		'phone',
		'direct',
		'mobile',
		array(
			'name'=>'status',
			'value' => '$data->getStatus();',
			'filter'=>CHtml::dropDownList('OrgContact[status]', $contacts->status, OrgContact::$states, array('prompt'=>$this->t('All'))),
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
			'class'=>'CButtonColumn',
			'template'=>'{update}',
			'buttons'=>array(			
				'update' => array(
					'imageUrl'=>false,
					'url'=>'Yii::app()->createUrl("orgContact/update", array("id"=>$data->id))',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update')),
				),
			),
		),
		),
	));
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	tab.unbind('reload_contact_grid').bind('reload_contact_grid', function(){
		$('#<?=$_GET["tabid"];?>_org-contact-grid', tab.data('panel')).yiiGridView('update');
		return false;
	});
});
</script>