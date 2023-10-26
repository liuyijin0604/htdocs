<div style="right: 20px;position: absolute;">
<a class="jqm_link" href="<?=$this->createUrl('wmsOrg/createchargecode');?>" title="New Charge Code"><div class="icon" style="background-position:-16px 0"></div> New Charge Code</a> &nbsp; </div>
<h1><?=$this->t('Intl Charge Code');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'intl-charge-code-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'chargecode',
        array('name' => 'owner_name', 'value' => 'empty($data->owner)? "" : $data->owner->name'),
        'note',
		'created',
        array('name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('IntlChargeCode[status]', $model->status, $this->t(IntlChargeCode::$states), array('prompt'=>$this->t('All')))),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'url' => 'Yii::app()->createUrl("wmsOrg/updatechargecode", ["id" => $data->id])',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->chargecode'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#intl-charge-code-grid', panel).yiiGridView('update');
	});

});
</script>
