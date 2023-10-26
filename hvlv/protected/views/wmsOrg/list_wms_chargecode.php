<div style="right: 20px;position: absolute;">
<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('wmsOrg/createWmsChargecode');?>" title="New Charge Code"><div class="icon" style="background-position:-16px 0"></div> New Charge Code</a> &nbsp; </div>
<h1><?=$this->t('Wms Charge Code');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'wms-charge-code-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		'chargecode',
		'note',
		'v_from',
		'v_to',
		'created',
		array('name' => 'status', 'value' => '$data->getStatus()',
			'filter' => CHtml::dropDownList('WmsChargeCode[status]', $model->status, $this->t(WmsChargeCode::$states), array('prompt' => $this->t('All')))),
		array(
			'class' => 'oButtonColumn',
			'template' => '{update}',
			'buttons' => array
			(
				'update' => array(
					'imageUrl' => false,
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("wmsOrg/updateWmsChargecode", ["id" => $data->id])',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '$data->chargecode'),
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
		$('#wms-charge-code-grid', panel).yiiGridView('update');
	});
});
</script>
