<div style="right: 20px;position: absolute;">
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('cnlOrder/report', ['type' => 'inv']);?>" class="jqm_link">Inovice Report</a></li>
		<li><a href="<?=$this->createUrl('cnlOrder/report', ['type' => 'sfr']);?>" class="jqm_link">Sea Freight Report</a></li>
	</ul>
</div>
</div>
<h1><?=$this->t('Cnl Orders');?></h1>
<?php echo CHtml::link($this->t('Advanced Search'), '#', ['class'=>'search-button']); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', [
	'model'=>$model,
]); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>'cnl-order-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>[
		['name' => 'orderCode', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("cnlOrder/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->orderCode."\">".$data->orderCode."</a>"'],
		['name' => 'focOrderCode', 'value' => 'preg_replace("/^FOC0+/", "", $data->focOrderCode)'],
		['name' => 'awb', 'type' => 'raw', 'value' => '$data->AwbTracking()'],
		['name' => 'bizType', 'value' => '$data->getBiztype()."/".$data->getIncoterm()', 'filter'=>CHtml::dropDownList('CnlOrder[bizType]', $model->bizType, $this->t($model::$bizTypes), ['prompt'=>$this->t('All')]),],
		['name' => 'cargoType', 'value' => '$data->getCargoType()', 'filter'=>CHtml::dropDownList('CnlOrder[cargoType]', $model->cargoType, $this->t($model::$cargoTypes), ['prompt'=>$this->t('All')]),],
		['name' => 'status', 'value' => '$data->getStatus()', 'filter'=>CHtml::dropDownList('CnlOrder[status]', $model->status, $this->t($model::$states), ['prompt'=>$this->t('All')]),],
		['name' => 'transportMode', 'value' => '$data->getTransportMode().($data->transportMode == 10? "/".$data->getContainerLoad() : "")', 'filter'=>CHtml::dropDownList('CnlOrder[transportMode]', $model->transportMode, $this->t($model::$transportModes), ['prompt'=>$this->t('All')]),],
		'sellerName',
		'pol',
		'pod',
		['name' => 'targetEta', 'value' => 'substr($data->targetEta, 0, 10)', ],
		['name' => 'cargoReadyDate', 'value' => 'substr($data->cargoReadyDate, 0, 10)', ],
		['name' => 'wmsg', 'type' => 'raw', 'value' => '$data->msgWarn()', 'filter'=>CHtml::dropDownList('CnlOrder[wmsg]', $model->wmsg, $this->t($model::$wmsgs), ['prompt'=>$this->t('All')]),],
		['name' => 'inv_no', 'type' => 'raw', 'value' => '$data->getInvoicesString()'],
		/*
		'refCode',
		'gmtModified',
		'feature',
		'remark',
		'meta',
		*/
		[
			'class'=>'oButtonColumn',
			'template'=>'{view}{update}',
			'buttons'=>[
				'view' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn'],
				],
				'update' => [
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->orderCode'],
				],
			],
		],
	],
]); ?>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('cnl-order-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#cnl-order-grid', panel).yiiGridView('update');
	});
});
</script>
