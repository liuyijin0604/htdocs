<div style="right: 20px;position: absolute;">
	<a class="jqm_link" href="<?=$this->createUrl('wmsProd/create');?>"><div class="icon" style="background-position:-16px 0"></div> New Product</a> &nbsp;
	<a class="jqm_link" href="<?=$this->createUrl('wmsProd/import');?>"><div class="icon" style="background-position:-16px 0"></div> Import Products</a> &nbsp;
	<a class="jqm_link" href="<?=$this->createUrl('wmsProd/printLabel');?>"><div class="icon" style="background-position:-128px -576px"></div> Print Labels</a>
</div>
<h1><?=$this->t('Manage Products');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wms-prod-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'type', 'value' => '$data->getType()', 
			'filter'=>CHtml::dropDownList('WmsProd[type]', $model->type, $this->t(WmsProd::$types), array('prompt'=>$this->t('All'))),),
		'ean',
		'name',
		'name_zh',
		'brand',
		'model',
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('WmsProd[status]', $model->status, $this->t(WmsProd::$states), array('prompt'=>$this->t('All'))),),
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
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"Product ".$data->name'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('wms-prod-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#wms-prod-grid', panel).yiiGridView('update');
	});
});
</script>
