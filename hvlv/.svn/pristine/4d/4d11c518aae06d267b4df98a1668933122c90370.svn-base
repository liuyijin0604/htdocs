<div style="right: 20px;position: absolute;">
<a class="tab_link" href="<?=$this->createUrl('exacConsol/create');?>" title="New Consol."><div class="icon" style="background-position:-16px 0"></div> New Consol.</a>
</div>
<h1><?=$this->t('Air Freight Consols');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'exac-consol-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'no','type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("exacConsol/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ExacConsol[status]', $model->status, $this->t(ExacConsol::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'awb' ),
		array('header' => 'Shipments', 'value' => '$data->totShipments()'),
		array('header' => 'Weight', 'value' => '$data->totWeight()'),
		array('name' => 'pol', 'filter'=>CHtml::dropDownList('ExacConsol[pol]', $model->pol, $this->t(AppHelper::setting2List('pods')), array('prompt'=>$this->t('All'))),),
		array('name' => 'pod', 'filter'=>CHtml::dropDownList('ExacConsol[pod]', $model->pod, $this->t(AppHelper::setting2List('pols')), array('prompt'=>$this->t('All'))),),
		'etd',
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->no'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('exac-consol-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#exac-consol-grid', panel).yiiGridView('update');
	});
});
</script>
