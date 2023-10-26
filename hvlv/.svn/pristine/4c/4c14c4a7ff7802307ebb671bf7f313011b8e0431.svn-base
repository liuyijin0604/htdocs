<div style="right: 20px;position: absolute;">
<a class="jqm_link" href="<?=$this->createUrl('payment/createCreditNote');?>"><div class="icon" style="background-position:-128px -480px"></div> New Credit Note</a> &nbsp; <a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-192px -80px" class="icon"></div> Actions</a>

<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a class="jqm_link" href="<?=$this->createUrl('payment/create');?>"><div class="icon" style="background-position:-128px -480px"></div> New Receipt</a></li>
		<li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('payment/export', ['type' => 'search']);?>" target="_blank">Export Current Search</a></li>
		<li><a class="jqm_link" href="<?=$this->createUrl('payment/report', ['type' => 'month']);?>">Receipts Report</a></li>
		<li><a class="tab_link" href="<?=$this->createUrl('payment/syncxero');?>" title="pull xero payments">Sync Xero</a></li>
	</ul>
</div>
</div>
<h1><?=$this->t('Receipts');?></h1>
<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>'payment-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>[
		'no',
		'date',
		'transaction_date',
		['name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(5)'],
		['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('Payment[status]', $model->status, $this->t($model::$states), ['prompt'=>$this->t('All')]),],
		['name' => 'type', 'value' => '$data->getType()',
			'filter'=>CHtml::dropDownList('Payment[type]', $model->type, $this->t($model::$types), ['prompt'=>$this->t('All')]),],
		['name' => 'bank', 'value' => '$data->getBank()',
			'filter'=>CHtml::dropDownList('Payment[bank]', $model->bank, $this->t($model::$banks), ['prompt'=>$this->t('All')]),],
		['name' => 'currency', 'value' => '$data->getCurrency()',
			'filter'=>CHtml::dropDownList('Payment[currency]', $model->currency, $this->t(Invoice::$currencies), ['prompt'=>$this->t('All')]),],
		['name' => 'dpmt', 'value' => '$data->getDpmt()',
				'filter'=>CHtml::dropDownList('Payment[dpmt]', $model->dpmt, $this->t(Invoice::$dpmts), ['prompt'=>$this->t('All')]),],
		'amount',
		['name' => 'gst', 'value' => '$data->getInvoiceGsts()'],
		'ata',
		'refund',
		['name' => 'invnos', 'value' => '$data->getInvNos(2)'],
		'ref',
		['name' => 'diff', 'value' => 'empty($data->mdata["diff"]) ? 0 : $data->mdata["diff"]'],
		array('name' => 'xero_id', 'value' => '$data->xero_id != "" && $data->xero_id != "00000000-0000-0000-0000-000000000000" ? "Yes" : "No"'),
		[
			'class'=>'oButtonColumn',
			'template'=>'{update} {log} {print}',
			'buttons'=>[
				'update' => [
					'imageUrl'=>false,
					'visible'=>'true',
					'url' => '$data->type != 5 ? Yii::app()->createUrl("payment/update", ["id" => $data->id]) : Yii::app()->createUrl("payment/updateCreditNote", ["id" => $data->id])',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'data-win-class' => 'L'],
				],
				'log' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("payment/log", ["fid" => $data->id])',
					'label' => 'Log'
				],
				'print' => [
					'imageUrl'=>false,
					'options' => ['class' => 'grid_print_btn','target'=>'_blank','label' => 'Print', 'data-win-class' => 'L'],
					'visible' =>'$data->type==5||$data->type==1',
					'url' => 'Yii::app()->createUrl("payment/printCreditNote", ["id" => $data->id])',
					'label' => 'Print'
				],
				'sync' => [
					'imageUrl' => false,
					'options' => ['class' => 'ajax_link grid_swap_btn'],
					'visible' => '$data->bank == 91 && ($data->xero_id == "" || $data->xero_id == "00000000-0000-0000-0000-000000000000")',
					'url' => 'Yii::app()->createUrl("payment/syncXeroNew", ["id" => $data->id])',
					'label' => 'Sync',
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
		$.fn.yiiGridView.update('payment-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#payment-grid', panel).yiiGridView('update');
	});

	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '&' + q);
	});

	$('.ajax_link.grid_swap_btn', panel).on('success', function(e, r) {
		myApp.notice(r.msg, 5000);
		$('#payment-grid').yiiGridView('update');
	});
});
</script>
