<?php
$model = new Invoice('search');
unset($model->attributes);
unset($model->date);
unset($model->bflag);
unset($model->due);
unset($model->gst);
unset($model->posted);
unset($model->sent);
$model->ref = 'WT DIFF';
$this->widget('zii.widgets.grid.CGridView', [
	'id' => 'created-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => [
		['name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("invoice/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'],
		['name'=>'model_no','header'=>'No','type'=>'raw','value'=>'$data->getModelUrl()'],
		['name'=>'model_awb','header'=>'AWB','value'=>'$data->getModelAWB()'],
		//array('header' => 'AWB',  'type' => 'raw','value' => 'empty($data->mdata["awb"])? "" : $data->mdata["awb"]'),
		['name' => 'to_name', 'value' => 'empty($data->cust) ? "" : $data->cust->shortName(5)'],
		['name' => 'suborg_name', 'value' => '$data->subOrgName(5)'],
		['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('Invoice[status]', $model->status, $this->t($model::$states), ['prompt'=>$this->t('All')]),],
		['name' => 'type', 'value' => '$data->getType()',
			'filter'=>CHtml::dropDownList('Invoice[type]', $model->type, $this->t($model::$types2), ['prompt'=>$this->t('All')]),],
		['name' => 'pay_type', 'value' => '$data->getPayType()',
			'filter'=>CHtml::dropDownList('Invoice[pay_type]', $model->pay_type, $this->t(Payment::$types), ['prompt'=>$this->t('All')]),],
		['name' => 'dpmt', 'value' => '$data->getDpmt()',
			'filter'=>CHtml::dropDownList('Invoice[dpmt]', $model->dpmt, $this->t(Invoice::$dpmts), ['prompt'=>$this->t('All')]),],
		['name' => 'dpt_id', 'value' => '$data->getBranch()',
			'filter'=>CHtml::dropDownList('Invoice[dpt_id]', $model->dpt_id, Org::dptList(), ['prompt'=>$this->t('All')]),],
		['name' => 'currency', 'value' => '$data->getCurrency()', 'filter' => CHtml::dropDownList('Invoice[currency]', $model->currency, $this->t(Invoice::$currencies), ['prompt' => $this->t('All')])],
		'date',
		'due',
		'posted',
		'gst',
		['name' => 'total', 'value' => '$data->getCurrency()." ".round($data->total,2).($data->status == 7? " (".$data->getBalance().")": "")'],
		[
			'class'=>'oButtonColumn',
			'template'=>'{print}',
			'buttons'=>[
				'print' => [
					'imageUrl'=>false,
					'options' => ['class' => 'grid_print_btn', 'data-dropdown' => '#'.$_GET["tabid"].'-dropdown-print', 'data-obh' => 'empty($data->mdata["suborg"])? 0 : 1'],
					'url' => 'Yii::app()->createUrl("invoice/print", ["id" => $data->id])',
					'label' => 'Print',
				],
			],
		],
	],
]);
?>
</div>

<div id="<?=$_GET["tabid"];?>-dropdown-print" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a class="print_inv" href="#" target="_blank">Invoice</a></li>
		<li><a class="print_invbal" href="#" target="_blank">Invoice with balance</a></li>
		<li><a class="print_invxls" href="#" target="_blank">Invoice as Excel</a></li>
		<li><a class="print_invobh" href="#" target="_blank">Invoice on behalf</a></li>
	</ul>
</div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.on('onOpen', function(){
		$('#created-grid', panel).yiiGridView('update');
	});

	panel.on('click', 'a.grid_print_btn', function(){
		var drop = $("#<?=$_GET["tabid"];?>-dropdown-print");
		$('.print_inv', drop).attr('href', $(this).attr('href'));
		$('.print_invbal', drop).attr('href', $(this).attr('href')+'?bal=1');
		$('.print_invxls', drop).attr('href', $(this).attr('href')+'?xls=1');
		if($(this).data('obh') > 0){
			$('.print_invobh', drop).attr('href', $(this).attr('href')+'?obh=1').parent().show();
		}else{
			$('.print_invobh', drop).parent().hide();
		}
	});
});
</script>
