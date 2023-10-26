<div style="right: 20px;position: absolute;">
	<a class="tab_link" href="<?=$this->createUrl('paymentBilling/syncxero');?>" title="pull xero payments"><div class="icon" style="background-position:-16px 0"></div>Sync Xero</a>
	<a class="jqm_link" href="<?=$this->createUrl('paymentBilling/create');?>"><div class="icon" style="background-position:-128px -480px"></div> New Payment</a> &nbsp; 
	<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-192px -80px" class="icon"></div> Actions</a>
	<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
		<ul class="dropdown-menu">
			<li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('paymentBilling/report', ['type' => 'search']);?>" target="_blank">Export Current Search</a></li>
		</ul>
	</div>
</div>
<h1><?=$this->t('Payments');?></h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'payment-billing-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		'no',
		'date',
		// 'transaction_date',
		array('name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(2)'),
		// array('name' => 'status', 'value' => '$data->getStatus()', 'filter' => CHtml::dropDownList('Payment[status]', $model->status, $this->t($model::$states), array('prompt' => $this->t('All'))),),
		// array('name' => 'type', 'value' => '$data->getType()', 'filter' => CHtml::dropDownList('Payment[type]', $model->type, $this->t($model::$types), array('prompt' => $this->t('All'))),),
		// array('name' => 'bank', 'value' => '$data->getBank()', 'filter' => CHtml::dropDownList('Payment[bank]', $model->bank, $this->t($model::$banks), array('prompt' => $this->t('All'))),),
		array('name' => 'currency', 'value' => '$data->getCurrency()', 
			'filter' => CHtml::dropDownList('Payment[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt' => $this->t('All'))),),
		'amount',
		// array('name' => 'gst', 'value' => '$data->getBillingGsts()'),
		// 'ata',
		array('name' => 'billingnos', 'value' => '$data->getBillingNos(2)'),
		// 'ref',
		// array(
		// 	'class' => 'oButtonColumn',
		// 	'template' => '{update}{log}{print}',
		// 	'buttons' => array
		// 	(
		// 		'update' => array(
		// 			'imageUrl' => false,
		// 			'visible' => 'true',
		// 			'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Update')),
		// 		),
		// 		'log' => array(
		// 			'imageUrl' => false,
		// 			'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'),
		// 			'visible' => 'true',
		// 			'url' => 'Yii::app()->createUrl("payment/log", ["fid" => $data->id])',
		// 			'label' => 'Log'
		// 		),
		// 		'print' => array(
		// 			'imageUrl' => false,
		// 			'options' => array('class' => 'grid_print_btn', 'target' => '_blank', 'label' => 'Print', 'data-win-class' => 'L'),
		// 			'visible' => '$data->type==5||$data->type==1',
		// 			'url' => 'Yii::app()->createUrl("payment/printCreditNote", ["id" => $data->id])',
		// 			'label' => 'Print'
		// 		),
		// 	),
		// ),
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
		$.fn.yiiGridView.update('payment-billing-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#payment-billing-grid', panel).yiiGridView('update');
	});

	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '&' + q);
	});
});
</script>
