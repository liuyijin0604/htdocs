<div style="right: 20px;position: absolute;">
<a class="tab_link" href="<?=$this->createUrl('exdirConsol/create');?>" title="New Consol."><div class="icon" style="background-position:-16px 0"></div> New Consol.</a>
</div>
<h1><?=$this->t('Direct Consols');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'loco-consol-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("exdirConsol/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"',),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ExdirConsol[status]', $model->status, $this->t(ExdirConsol::$states), array('prompt'=>$this->t('All'))),),
		array('header' => 'Orders', 'value' => '$data->totShipments()'),
		array('header' => 'Value', 'value' => '$data->totValue()'),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',//{view}
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
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
		$.fn.yiiGridView.update('loco-consol-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#loco-consol-grid', panel).yiiGridView('update');
	});
});
</script>
