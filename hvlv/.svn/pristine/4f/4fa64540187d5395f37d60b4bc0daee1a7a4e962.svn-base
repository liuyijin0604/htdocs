<h1>Unreconciled Credit Note</h1>

<?php
$model = new PayInv('search');
$model->sync_xero = 0;
$ec = new CDbCriteria;
$ec->with = 'payment';
$ec->addCondition('payment.type = 5 AND payment.bank = 91 AND payment.status = 6');

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'unreconciled-crn-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, 30, $ec),
	'columns' => array(
		'invoice.cust.name',
		'invoice.no',
		'payment.no',
		'amount',
	),
));
?>