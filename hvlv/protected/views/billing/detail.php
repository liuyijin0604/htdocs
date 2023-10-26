<?php
$lines = new BillingLine();
$lines->unsetAttributes();
if (!empty($id)) {
	$lines->billing_id = $id;
} else if (!empty($cref)) {
	$lines->billing_cref = $cref;
}
if (!empty($org_id)) {
	$lines->org_id = $org_id;
}
$ledger_dp = $lines->search();
$ledger_data = $ledger_dp->getData();
$currency = !empty($ledger_data[0]) ? $ledger_data[0]->getCurrency() : '';
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => $_GET["tabid"] . '_billing-grid',
	'cssFile' => false,
	'summaryText' => '',
	'dataProvider' => $lines->search(),
	'columns' => array(
		array('name' => 'billing_ref', 'type' => 'raw', 'value' => '$data->getNo()'),
		array('name' => 'desc'),
		array('name' => 'charge_code', 'value' => '$data->getCCodeDesc()'),
		array('name' => 'actual_amount', 'footer' => $currency . ' ' . AppHelper::money_format('%i', $lines->getTotal($ledger_data, 'actual_amount'))),
		array('name' => 'gst', 'filter' => Invoice::$InvoiceCostTaxRate),
		array('name' => 'gst_amount', 'footer' => $currency . ' ' . AppHelper::money_format('%i', $lines->getTotal($ledger_data, 'gst_amount'))),
		array('name' => 'billing_cref'),
	),
));
