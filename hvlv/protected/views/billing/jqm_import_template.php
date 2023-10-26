<h1> Import Billing - <?php
if ($type == 'template') {
	echo 'Through General Template';
} else if ($type == 'austway') {
	$str = 'Austway';
	echo $str;
// } else if ($type == 'globavend') {
// 	$str = 'Globavend Customs & Linehaul';
// 	echo $str;
} else if ($type == 'd2z_thc') {
	$str = 'D2Z THC';
	echo $str;
}
?></h1>
<div class="form">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'import-billing-template-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('billing/importBilling', array('type' => $type)),
	));
	?>

	<?php if (in_array($type, ['globavend', 'd2z_thc', 'austway'])) { ?>
	<div class="row" style="margin-top: 20px;">
		<?php echo CHtml::label($str . ' Invoice no:', 'invoice_no');?>
		<?php echo CHtml::textField('invoice_no', '');?>
	</div>
	<?php } ?>

	<div class="row">
		<?php echo CHtml::label('Invoice Issue date:', 'invoice_date');?>
		<?php echo CHtml::textField('invoice_date', '', array('class' => 'date_input', 'id' => 'invoice_date' . $_GET['tabid']));?>
	</div>

	<?php if (in_array($type, ['globavend', 'd2z_thc'])) { ?>
	<div class="row">
		<?php echo CHtml::label('Amount Type:', 'amt_type'); ?>
		<?php echo CHtml::radioButtonList('amt_type', '', array('nogst' => 'No GST', 'gstincl' => 'GST Include', 'gstexcl' => 'GST Exclude'), array('labelOptions' => array('style' => 'display: inline; float: none; font-weight: bold;'), 'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;')); ?>
	</div>
	<?php } ?>

	<div class="row" style="margin-top: 20px;">
		<label for="import_billing_template">Excel - <small>.csv/.xls/.xlsx File</small> (
			<?php if (in_array($type, ['d2z_thc'])) {
				echo '<a href="' . Yii::app()->createUrl('billing/template', ['type' => 'd2z_thc']) . '" target="blank">Template file</a>';
			} else if ($type == 'austway') {
				echo '<a href="' . Yii::app()->createUrl('billing/template', ['type' => 'austway']) . '" target="blank">Template file</a>';
			} else if ($type == 'template') {
				echo '<a href="../template/import_billing_template.xlsx" target="blank">Template file</a>';
			} ?>
		)</label>
		<input type="file" name="import_billing_template" id="import_billing_template" />
	</div>

	<div class="row" style="margin-top: 20px;">
		<input id="import_billing_btn" type="submit" value="Submit" />
	</div>

	<?php $this->endWidget();?>
</div>

<script type="text/javascript">
$(function() {
	$('form#import-billing-template-form').on('success', function(e, r) {
		setTimeout(function() {
			$('.popCancel').trigger('click');
		}, 5e2);
	});
});
</script>