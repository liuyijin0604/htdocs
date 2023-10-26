<h1><?=$this->t('Check Broker Billing');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'broker-check-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		array('name' => 'billing_cref', 'type' => 'raw', 'value' => '"<a href=\"" . Yii::app()->createUrl("billing/billingDetails", ["cref" => $data->billing_cref, "org_id" => @$data->af->supplier_id]) . "\" class=\"jqm_link\" data-win-class=\"XL\">" . $data->billing_cref . "</a>"'),
		array('name' => 'reconcile_id', 'value' => '@$data->af->history->id'),
		array('name' => 'date'),
		array('name' => 'shipment_id', 'type' => 'raw', 'value' => '$data->getShipment()'),
		array('name' => 'consol_id', 'type' => 'raw', 'value' => '$data->getConsol()'),
		array('name' => 'billing_subtotal'),
		array('name' => 'billing_gst'),
		array('name' => 'billing_total'),
		array('name' => 'inv_id', 'type' => 'raw', 'value' => '$data->getInvoice()'),
		array('name' => 'invoice_subtotal'),
		array('name' => 'invoice_gst'),
		array('name' => 'invoice_total'),
		array('name' => 'diff'),
		array('name' => 'status', 'type' => 'raw', 'value' => '$data->getStatus()', 'filter'=>CHtml::dropDownList('BillingInvoice[status]', $model->status, $this->t(BillingInvoice::$states), array('prompt'=>$this->t('All'))),),
		array(
			'class' => 'oButtonColumn',
			'template' => '{update} {log}',
			'buttons' => array(
				'update' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_edit_btn'),
					'url' => 'Yii::app()->createUrl("billing/brokerCheckUpdate", ["id" => $data->id])',
					'label' => 'Update',
				),
				'log' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'),
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("billing/brokerCheckLog", ["id" => $data->id])',
					'label' => 'Log',
				),
			),
		),
	),
)); ?>

<script>
$(function() {
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#broker-check-grid', panel).yiiGridView('update');
	});

	$(panel).on('click', '.ajax_link', function() {
		setTimeout(function() {
			$('#broker-check-grid', panel).yiiGridView('update');
		}, 5e2);
	});
});
</script>