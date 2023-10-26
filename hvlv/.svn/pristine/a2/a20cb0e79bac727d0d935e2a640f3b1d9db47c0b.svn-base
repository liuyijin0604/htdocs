<div style="right: 20px;position: absolute;">
<a class="tab_link" href="<?=$this->createUrl('dmawbConsol/create');?>" title="New Consol."><div class="icon" style="background-position:-16px 0"></div> New Consol.</a>
<a class="export_search" target="_blank" href="<?=$this->createUrl('dmawbConsol/ExportCurrentSearch', ['typ' => '']);?>"><div style="background-position:-48px -688px" class="icon"></div> Export Current Search</a>

</div>
<h1><?=$this->t('Direct MAWB Consols').'-('.$delivery_type.')';?></h1>

<?php echo CHtml::link($this->t('Advanced Search'), '#', ['class'=>'search-button']); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', [
	'model'=>$model,
]);
?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>'dmawb-consol-grid'.$_GET['tabid'],
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>[
		['name' => 'no','type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("dmawbConsol/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'],
		['name'=>'owner_name', 'type' => 'raw','value'=>'empty($data->owner_id)?"":(empty($data->owner->name)?"":$data->owner->name)'],
		['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('DmawbConsol[status]', $model->status, $this->t(DmawbConsol::$states), ['prompt'=>$this->t('All')]),],
		['name' => 'service', 'value' => '$data->getService()', 'filter'=>CHtml::dropDownList('DmawbConsol[service]', $model->service, $this->t(DmawbConsol::$services), ['prompt'=>'All'])],
		['name' => 'dpt_id', 'value' => '$data->depot->name',
			'filter'=>CHtml::dropDownList('DmawbConsol[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), ['prompt'=>$this->t('All')]),],
		['name' => 'awb', 'type' => 'raw', 'value' => '$data->AwbTracking()'],
		['header'=>'House Bill','value'=>'@$data->mdata["house_bill"]'],
		['header' => 'Shipments', 'value' => '@$data->mdata["shipments"]'],
		['header' => 'Weight', 'value' => '@$data->mdata["cgb_wt"]'],
		['name' => 'pol', 'filter'=>CHtml::dropDownList('DmawbConsol[pol]', $model->pol, $this->t(AppHelper::setting2List('pols')), ['prompt'=>$this->t('All')]),],
		['name' => 'pod', 'filter'=>CHtml::dropDownList('DmawbConsol[pod]', $model->pod, $this->t(AppHelper::setting2List('pods')), ['prompt'=>$this->t('All')]),],
		'eta',
		['header'=>'extra Info','type'=>'raw','value'=>'$data->getConsolDGWarnings()'],
		[
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>[
				'update' => [
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->no'],
				],
			],
		],
	],
]); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('dmawb-consol-grid<?=$_GET['tabid']?>', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#dmawb-consol-grid<?=$_GET['tabid']?>', panel).yiiGridView('update');
	});

	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize()+'&'+$('.search-form form', panel).serialize();
		$(this).attr('href', '<?=$this->createUrl('dmawbConsol/exportCurrentSearch', ['typ' => ''])?>' + '&' + q);
	});
});
</script>
