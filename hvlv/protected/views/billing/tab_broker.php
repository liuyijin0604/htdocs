<?php
$afModel = new AFInvoiceReconciliationHistory();
$afModel->unsetAttributes();

$ec = new CDbCriteria;
$ec->with = ['af'];
$ec->addCondition('af.supplier_id IN (' . implode(',', Org::$brokers) . ')');

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'billing-stream-line-check-broker-grid',
	'cssFile' => false,
	'dataProvider' => $afModel->search($ec),
	'filter' => $afModel,
	'columns' => array(
		'id',
		array('name' => 'org', 'value' => '$data->af[0]->org->shortName(2)'),
		array('name' => 'created', 'header' => 'Import Date'),
		'invoice_total',
		'totalSuccess',
		'totalFailed',
		array('name' => 'attached_file_id', 'type' => 'raw', 'value' => '"<a href=\"". $data->getAttachedFileLink() ."\">" . $data->getAttachedFileName() . "</a>"'),
		array(
			'class' => 'oButtonColumn',
			'template' => '{check}',
			'buttons' => array(
				'check' => array(
					'imageUrl' => false,
					'options' => array('class' => 'tab_link grid_view_btn'),
					'url' => 'Yii::app()->createUrl("billing/brokerCheck", ["BillingInvoice[reconcile_id]" => $data->id])',
					'label' => 'Check',
				),
			),
		),
	))
);
?>

<script>
$(function() {
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.bind('onOpen', function() {
		$('#billing-stream-line-check-broker-grid', panel).yiiGridView('update');
	});
});
</script>