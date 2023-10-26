<style>
.checkbox-hidden input[type="checkbox"] {
	display: none;
}
</style>

<h1>Auto Create OT Invoice for <?=$billing->billing_cref?></h1>
<br />

<div class="form">
	<?php
	$this->beginWidget('CActiveForm', array(
		'id' => 'billing-ot-form',
		'enableAjaxValidation' => false,
	));
	?>

	<h2>Auto invoice</h2>
	<?php
	$bls = BillingLine::model()->findAll('billing_id = :bid', [':bid' => $billing->id]);
	$blids = [];
	foreach ($bls as $bl) {
		$consol = Consol::model()->find('no = :no', ['no' => $bl->billing_ref]);
		if ($consol->totWeight() < $consol->mdata['awb_wt']) {
			$blids[] = $bl->id;
		}
	}

	$model = new BillingLine('search');
	$model->unsetAttributes();
	$model->attributes = @$_GET['BillingLine'];
	$model->billing_id = $billing->id;
	$model->status = [1,2,3,11];
	$model->desc = 'Linehaul charges';

	$ec = new CDbCriteria;
	$ec->addCondition('id IN (' . implode(',', $blids) . ')');

	$ledger_data = $model->search(false, 0, 't.id DESC', $ec)->getData();

	$this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'ot-invoice-grid',
		'selectableRows' => 2,
		'cssFile' => false,
		'dataProvider' => $model->search(false, 0, 't.id DESC', $ec),
		'filter' => $model,
		'summaryText' => '',
		'columns' => array(
			array('id' => 'selectedItems', 'class' => 'CCheckBoxColumn'),
			array('name' => 'billing_ref', 'type' => 'raw', 'value' => '$data->getNo()'),
			'desc',
			array('name' => 'actual_amount', 'footer' => $billing->getCurrency() . ' ' . AppHelper::money_format('%i', $model->getTotal($ledger_data, 'actual_amount'))),
			array('name' => 'gst'),
			array('name' => 'gst_amount', 'footer' => $billing->getCurrency() . ' ' . AppHelper::money_format('%i', $model->getTotal($ledger_data, 'gst_amount'))),
			array('header' => 'Pkg Weight', 'value' => '!empty($data->consol) ? $data->consol->totWeight() : ""'),
			array('header' => 'AWB Weight', 'value' => '!empty($data->consol) ? $data->consol->mdata["awb_wt"] : ""'),
			array('header' => 'OT Invoice', 'type' => 'raw', 'value' => '$data->getOT()'),
		),
	));
	?>

	<div class="row">
		<?php echo CHtml::submitButton('Create'); ?>
	</div>

	<?php
	$this->endWidget();
	?>
</div>

<script>
$(function() {
	var win = $('#<?=$_GET["tabid"]?>');
	var panel = win.data('pane');

	setTimeout(function() {
		$('#selectedItems_all', panel).trigger('click');
	}, 1e2);

	$('form', panel).on('success', function() {
		$('#ot-invoice-grid', panel).yiiGridView('update');
	});
});
</script>