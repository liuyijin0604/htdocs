<div style="right: 20px;position: absolute;">
<a class="tab_link" href="<?=$this->createUrl('import/manageSystemDefaultFuel');?>" title="Manage System Default Fuel"><div class="icon" style="background-position:-16px 0"></div> Manage System Default Fuel</a> &nbsp; 
<a class="jqm_link" href="<?=$this->createUrl('import/createchargecode');?>" title="New Charge Code"><div class="icon" style="background-position:-16px 0"></div> New Charge Code</a> &nbsp; </div>
<h1><?=$this->t('Import Charge Code');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'im-charge-code-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'chargecode',
        array('name' => 'owner_name', 'value' => 'empty($data->owner)? "" : $data->owner->name'),
        'description',
        'note',
		'created',
        array('name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('ImportChargeCode[status]', $model->status, $this->t(ImportChargeCode::$states), array('prompt'=>$this->t('All')))),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}&nbsp;{log}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->chargecode'),
				),
				'log' => array(
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("import/importChargCodeLog", ["id" => $data->id])',
					'label' => 'Log'
				)
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#im-charge-code-grid', panel).yiiGridView('update');
	});

});
</script>
