<style>
.checkbox-hidden input[type="checkbox"] {
	display: none;
}
</style>

<h1>Auto Create OT Invoice for <?=$billing->billing_cref?></h1>
<br />

<?php if ($billing->org_id == Org::ORGID_COURIER_TNT) { ?>
<div class="form">
	<?php
	$this->beginWidget('CActiveForm', array(
		'id' => 'billing-ot-setup-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('billing/OTSetup', array('id' => $billing->id))
	));
	?>

	<div class="row">
		<label for="postw-batch">Load File</label> <a href="<?=$this->createUrl('billing/OTSetup', array('id' => $billing->id))?>" target="_blank"> Template File</a>
		<input type="file" name="file" id="file" />
	</div>
	<div class="row buttons" style="margin-bottom: 20px;">
		<?php echo CHtml::submitButton('Submit', ['id' => 'btn-save']); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>
<?php } ?>

<h2>Need Check</h2>
<?php
$model = new BillingLine('search');
$model->unsetAttributes();
$model->attributes = @$_GET['BillingLine'];
$model->billing_id = $billing->id;
$model->status = [1,2,3,11];
$model->dpmt = 10;

$ec = new CDbCriteria;
$ec->addCondition('flag > 0 AND flag NOT IN (1,2)');

$ledger_data = $model->search(false, 0, 't.id DESC', $ec)->getData();

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'ot-invoice-need-check-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(false, 0, 't.id DESC', $ec),
	'filter' => $model,
	'summaryText' => '',
	'columns' => array(
		array('name' => 'billing_ref', 'type' => 'raw', 'value' => '$data->getNo()'),
		'desc',
		array('name' => 'actual_amount', 'footer' => $billing->getCurrency() . ' ' . AppHelper::money_format('%i', $model->getTotal($ledger_data, 'actual_amount'))),
		array('name' => 'gst'),
		array('name' => 'gst_amount', 'footer' => $billing->getCurrency() . ' ' . AppHelper::money_format('%i', $model->getTotal($ledger_data, 'gst_amount'))),
	),
));
?>
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
	$model = new BillingLine('search');
	$model->unsetAttributes();
	$model->attributes = @$_GET['BillingLine'];
	$model->billing_id = $billing->id;
	$model->status = [1,2,3,11];
	$model->dpmt = 10;

	$ec = new CDbCriteria;
	$ec->addCondition('flag IN (1,2)');

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