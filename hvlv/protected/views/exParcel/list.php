<div style="right: 40px;position: absolute;">
    <a class="tab_link" href="<?=$this->createUrl('exParcel/uimage');?>" title="<?=$this->t('Check Image');?>"><div class="icon" style="background-position:-16px 0"></div> <?=$this->t('Check Image');?></a> &nbsp; 
<a class="tab_link" href="<?=$this->createUrl('exParcel/create');?>" title="<?=$this->t('New Shipment');?>"><div class="icon" style="background-position:-16px 0"></div> <?=$this->t('New Shipment');?></a> &nbsp; 
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> <?=$this->t('Export');?></a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('exParcel/export');?>" class="jqm_link"><?=$this->t('Current Search');?></a></li>
		<li><a href="<?=$this->createUrl('exParcel/idReport');?>" target="_blank" data-baseurl=""><?=$this->t('Chinese ID Report');?></a></li>
		<li><a href="<?=$this->createUrl('exParcel/dupReport');?>" target="_blank" data-baseurl=""><?=$this->t('Duplicate Report');?></a></li>
	</ul>
</div>
</div>
<h1><?=$this->t('Export Shipments');?></h1>
<p><?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
));
?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'ex-parcel-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'js:function(id, data){ $("#'.$_GET["tabid"].'").trigger("gridUpdated");}',
	'columns'=>array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("exParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"'),
		'ref',
		array('name' => 'consol_no', 'type'=>'raw', 'value' => 'empty($data->consol_id)? "" : "<a href=\"".Yii::app()->createURL("excoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".$data->consol->no."\">".$data->consol->no."</a>"',),
		array('name' => 'agent_name', 'value' => 'empty($data->agent)? "" : $data->agent->shortName(2) . "-" . $data->agent->id', 'visible' => Acl::hasAccess('B:Export/SeeAllShipments'),),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ExParcel[status]', $model->status, $this->t($model->statusList()), array('prompt'=>$this->t('All'))),),
		array('name' => 'odpt_id', 'type'=>'raw', 'value' => '$data->getOdpt()', 'filter'=>CHtml::dropDownList('ExParcel[odpt_id]', $model->odpt_id, Org::dptList(), array('prompt'=>$this->t('All'))), 'visible' => Acl::hasAccess('B:Export/AllDepots')),
		array('name' => 'odpt_id', 'type'=>'raw', 'value' => '$data->getOdpt()', 'filter'=>CHtml::dropDownList('ExParcel[odpt_id]', $model->odpt_id, ['-1' => 'Unknown'], array('prompt'=>$this->t('All'))), 'visible' => !Acl::hasAccess('B:Export/AllDepots')),		
		array('name' => 'styp', 'value' => '$data->getStyp()', 
			'filter'=>CHtml::dropDownList('ExParcel[styp]', $model->styp, $model::$styps, array('prompt'=>$this->t('All'))),),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee->name)? "" : $data->cnee->name', 'htmlOptions' => array('width' => 10)),
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor->name)? "" : $data->cnor->name',),
		'state',
		array('name' => 'weight', 'htmlOptions' => array('width' => 10)),
		//'tariff',
		array('header' => $this->t('Location'), 'type' => 'raw', 'value' => '$data->getLocation()', 'htmlOptions' => array('width' => 10)),
		array('name' => 'bwf', 'header' => $this->t('Warnings'), 'type' => 'raw', 'value' => '$data->getWarnings()','filter'=>CHtml::dropDownList('ExParcel[bwf]', $model->bwf, $this->t(ExParcel::$bwfs), array('prompt'=>$this->t('All'))), 'htmlOptions' => array('width' => 10)),
		//array('name' => 'prod', 'value' => '$data->getProds()'),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array(
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
		$('#ex-parcel-grid', panel).yiiGridView('update', {data: 'ExParcel=reset'});
	};

	tab.bind('onOpen', function(){
		$('#ex-parcel-grid', panel).yiiGridView('update');
	});

	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});

	$('.search-form form', panel).on('submit', function(){
		$('#ex-parcel-grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()});
		return false;
	}).find('.reset_btn').click(resetFilters);

	$(panel).on('keypress', '.filters input', function(){
		$('#ex-parcel-grid', panel).yiiGridView('abortUpdate');
	});

	tab.on('gridUpdated', function(){
		$('tr.filters td:last-child', panel).empty().append($('<input type="button" id="reset_filter" value="Reset" />').on('click', resetFilters));
	}).trigger('gridUpdated');
});
</script>
