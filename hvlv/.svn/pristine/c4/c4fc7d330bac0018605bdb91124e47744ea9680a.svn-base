<style>
.checkbox-hidden input[type="checkbox"] {
	display: none;
}
</style>

<div style="right: 20px;position: absolute;">
	<a class="tab_link" href="<?=$this->createUrl('invoice/syncxero');?>" title="Sync Xero"><div class="icon" style="background-position:-16px 0"></div>Sync Xero</a>
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-192px -80px" class="icon"></div> Actions</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a class="jqm_link" href="<?=$this->createUrl('invoice/create', ['type' => 40]);?>">New Misc. Invoice</a></li>
		<li><a href="<?=$this->createUrl('invoice/exparinv');?>" title="Gen Invoices" class="tab_link">Generate Exp Parcel Invoices</a></li>
		<li><a class="jqm_link" data-win-class="XL" href="<?=$this->createUrl('invoice/rcvb');?>">Receivables Report</a></li>
		<li><a class="jqm_link" href="<?=$this->createUrl('invoice/emailStatments');?>">Email Statements</a></li>
		<li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('invoice/report', ['type' => 'search']);?>" target="_blank">Export Current Search</a></li>
		<?php if (Yii::app()->name != 'PEP') {
	?>
			<li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('invoice/report', ['type' => 'search2']); ?>" target="_blank">Export Current Search 2</a></li>
		<?php
} ?>
		<li><a class="jqm_link" href="<?=$this->createUrl('invoice/wtck');?>">Exp Invoice Weight Check</a></li>
		<li><a href="<?=$this->createUrl('invoice/wms2xero');?>" title="WMS Sync Xero" class="tab_link">Send WMS Inovice to Xero</a></li>
		<li><a class="jqm_link" href="<?=$this->createUrl('invoice/ExportByCustomer');?>">Export Customer Invoices</a></li>
		<li><a class="jqm_link" href="<?=$this->createUrl('invoice/genStateManual');?>" data-win-class="L">Generate Statement Manual</a></li>
		<li><a class="jqm_link" href="<?=$this->createUrl('invoice/unreconciledCRN');?>" data-win-class="L">Unreconciled CRN</a></li>
		<li><a class="jqm_link" href="<?=$this->createUrl('invoice/importExcelInv');?>" data-win-class="L">Import Excel Inv</a></li>
		<?php if (Acl::hasAccess('B:invoice/createSupayForInvoices')) { ?>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/createSupayForInvoices');?>">Create AliPay/WechatPay for Invoices</a></li>
		<?php } ?>
		<li><a class="jqm_link" href="<?=$this->createUrl('invoice/importInboundTransactions');?>" >Import Inbound Transactions</a></li>
	</ul>
</div>
</div>

<h1><?=$this->t('Invoices');?></h1>

<div class="form">
<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'invoice-batch-change-status-form',
		'enableAjaxValidation' => false,
		'action' => Yii::app()->createUrl('invoice/batchUpdate'),
	));

	if (Acl::hasAccess("B:Invoice/batchUpdate")) {
		echo CHtml::dropDownList('batch_status', 0, [2 => 'Posted']), ' ', CHtml::submitButton('Submit') . '<br />';
	}

	$this->widget('zii.widgets.grid.CGridView', [
		'id' => 'invoice-grid',
		'selectableRows' => Acl::hasAccess("B:Invoice/batchUpdate") ? 2 : 0,
		'cssFile' => false,
		'dataProvider' => $model->search(),
		'filter' => $model,
		'columns' => [
			array(
				'id' => 'selectedItems',
				'class' => 'CCheckBoxColumn',
				'cssClassExpression' => '($data->status == 1 && Acl::hasAccess("B:Invoice/batchUpdate")) ? "" : "checkbox-hidden"',
			),
			['name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("invoice/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"','filter'=>CHtml::textField(get_class($model).'[no]',$model->no)],
			['name'=>'model_no','header'=>'No','type'=>'raw','value'=>'$data->getModelUrl()'],
			['name'=>'model_awb','header'=>'AWB / Ocean Bill','value'=>'$data->getModelAWB()'],
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
			array('name' => 'sync_xero', 'value' => '$data->sync_xero == 1 ? "Yes" : "No"', 'filter' => CHtml::dropDownList('Invoice[sync_xero]', $model->sync_xero, [1 => 'Yes', 0 => 'No'], ['prompt' => 'All'])),
			// ['header' => 'Amount',  'type' => 'raw','value' => 'round($data->total-$data->gst,2).($data->status == 7? " (".$data->getBalance().")": "")'],
			'gst',
			['name' => 'total', 'value' => '$data->getCurrency()." ".round($data->total,2).($data->status == 7? " (".$data->getBalance().")": "")'],
			array('name' => 'is_paypal', 'header' => 'Paypal', 'value' => '$data->ifPaypal()', 'filter' => CHtml::dropDownList('Invoice[is_paypal]', $model->is_paypal, [1 => 'No', 2 => 'Yes'], ['prompt' => $this->t('All')])),

			['header' =>'CreditNote No', 'value'=>'$data->getCreditNote()'],

			/*
			'currency',
			'meta',
			*/
			[
				'class'=>'oButtonColumn',
				'template'=>'{email} {print} {update} {log}',
				'buttons'=>[
					'email' => [
						'imageUrl'=>false,
						'options' => ['class' => 'jqm_link grid_email_btn', 'label' => 'Email', 'data-win-class' => 'L'],
						'visible' => '!in_array($data->status, [1,10])',
						'url' => 'Yii::app()->createUrl("email/create", ["type" => 10, "fid" => $data->id])',
						'label' => 'Email'
					],
					'print' => [
						'imageUrl'=>false,
						'options' => ['class' => 'grid_print_btn', 'data-dropdown' => '#'.$_GET["tabid"].'-dropdown-print'],
						'url' => 'Yii::app()->createUrl("invoice/print", ["id" => $data->id])',
						'label' => 'Print',
					],
					'update' => [
						'imageUrl'=>false,
						'visible'=>'true',
						'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update')],
					],
					'log' => [
						'imageUrl'=>false,
						'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
						'visible' => 'true',
						'url' => 'Yii::app()->createUrl("invoice/log", ["fid" => $data->id])',
						'label' => 'Log'
					],
					'sync' => [
						'imageUrl' => false,
						'options' => ['class' => 'ajax_link grid_swap_btn'],
						'visible' => '$data->sync_xero == 0',
						'url' => 'Yii::app()->createUrl("invoice/syncXeroNew", ["id" => $data->id])',
						'label' => 'Sync',
					],
				],
			],
		],
	]);

	$this->endWidget();
?>
</div>

<div id="<?=$_GET["tabid"];?>-dropdown-print" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a class="print_inv" href="#" target="_blank">Invoice</a></li>
		<li><a class="print_invbal" href="#" target="_blank">Invoice with balance</a></li>
		<li><a class="print_invxls" href="#" target="_blank">Invoice as Excel</a></li>
		<li><a class="print_invobh" href="#" target="_blank">Invoice on behalf</a></li>
		<li><a class="print_inv_b" href="#" target="_blank">Invoice B</a></li>
	</ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('invoice-grid', {
			data: $(this).serialize()
		});
		return false;
	});

	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '&' + q);
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
		if ($(this).data('inv_b') > 0) {
			$('.print_inv_b', drop).attr('href', $(this).attr('href')+'?inv_b=1').parent().show();
		} else {
			$('.print_inv_b', drop).parent().hide();
		}
	});

	tab.on('onOpen', function(){
		$('#invoice-grid', panel).yiiGridView('update');
	});

	panel.on('success', 'a.ajax_link', function(e, r) {
		if (r.done == true) {
			myApp.notice(r.msg, 5000);
		} else {
			myApp.alert(r.msg, false);
		}
	});

	$('#invoice-batch-change-status-form', panel).on('success', function() {
		$('#invoice-grid', panel).yiiGridView('update');
	});
});
</script>
