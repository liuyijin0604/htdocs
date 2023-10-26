<div style="right: 20px;position: absolute;">
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div class="icon" style="background-position:-16px 0"></div> New Consol.</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a class="tab_link" href="<?=$this->createUrl('imcoConsol/createManif');?>" title="New Consol.">From Manifest</a></li>
		<li><a class="tab_link" href="<?=$this->createUrl('imcoConsol/createShips');?>" title="New Consol.">From Shipments</a></li>
	</ul>
</div>
</div>

<h1><?=$this->t('Import Consols').'('.$delivery_type.')';?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>'imco-consol-grid'.$_GET['tabid'],
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 60),
	'filter'=>$model,
	'columns'=>[
		['name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"',],
		['name' => 'awb', 'type' => 'raw', 'value' => '$data->AwbTracking()'],
		['header'=>'Customer','name' => 'owner_name','value' => '$data->getOwnerName()'],
		['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('ImcoConsol[status]', $model->status, $this->t($model::$states), ['prompt'=>$this->t('All')]),],
		['name' => 'service', 'value' => '$data->getService()', 'filter'=>CHtml::dropDownList('ImcoConsol[service]', $model->service, $this->t(ImcoConsol::$services), ['prompt'=>'All'])],
		['name' => 'dpt_id', 'value' => '$data->depot->name',
			'filter'=>CHtml::dropDownList('ImcoConsol[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), ['prompt'=>$this->t('All')]),],
		'flight',
		'pol',
		'pod',
		'eta',
		['header' => 'Shipments', 'value' => '$data->totShipments()'],
		['header' => 'Weight', 'value' => '$data->totWeight()'],
		['header'=>'extra Info','type'=>'raw','value'=>'$data->getConsolDGWarnings()'],
		/*'created',
		'meta',
		*/
		[
			'class'=>'oButtonColumn',
			'template'=>'{update}',//{view}
			'buttons'=>[
				'view' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn'],
				],
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
		$.fn.yiiGridView.update('imco-consol-grid<?=$_GET['tabid']?>', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#imco-consol-grid<?=$_GET['tabid']?>', panel).yiiGridView('update');
	});
});
</script>
