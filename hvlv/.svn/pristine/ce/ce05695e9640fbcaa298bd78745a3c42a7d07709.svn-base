<div style="right: 20px;position: absolute;">
<a class="tab_link" href="<?=$this->createUrl('exDirect/create');?>" title="New Order"><div class="icon" style="background-position:-16px 0"></div> New Order</a> &nbsp;
<a href="#" class="export_search" target="_blank" data-baseurl="<?=$this->createUrl('exDirect/export');?>"><div style="background-position:-48px -688px" class="icon"></div> Export Current Search</a>
</div>
<h1><?=$this->t('Direct Orders');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
));
?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'co-parcel-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("exDirect/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
		'ref',
		array('name' => 'agent_name', 'value' => 'empty($data->agent)? "" : $data->agent->shortName(2)', 'visible' => Acl::hasAccess('B:Export/SeeAllShipments'),),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ExDirect[status]', $model->status, $this->t(ExDirect::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'cnor_name', 'value' => '$data->cnor->name',),
		array('name' => 'cnor_tel', 'value' => '$data->cnor->tel',),
		array('name' => 'cnee_name', 'value' => '$data->cnee->name',),
		array('name' => 'cnee_tel', 'value' => '$data->cnee->tel',),
		'state',
		'value',
		array('name' => 'bwf', 'header' => 'Warnings', 'type' => 'raw', 'value' => '$data->getWarnings()','filter'=>CHtml::dropDownList('ExDirect[bwf]', $model->bwf, $this->t(ExDirect::$bwfs), array('prompt'=>$this->t('All'))),),
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
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->hbn'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	var resetFilters = function(){
		$('.search-form form', panel).trigger('reset');
		$('#co-parcel-grid', panel).yiiGridView('update', {data: 'ExDirect=reset'});
	};

	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});

	$('.search-form form', panel).on('submit', function(){
		$('#co-parcel-grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()});
		return false;
	}).find('.reset_btn').click(resetFilters);

	tab.on('gridUpdated', function(){
		$('tr.filters td:last-child', panel).empty().append($('<input type="button" id="reset_filter" value="Reset" />').on('click', resetFilters));
	}).trigger('gridUpdated');

	tab.bind('onOpen', function(){
		$('#co-parcel-grid', panel).yiiGridView('update');
	});

	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '?' + q);
	});
});
</script>
