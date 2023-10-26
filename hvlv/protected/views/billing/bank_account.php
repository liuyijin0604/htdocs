<h1><?=$org->name . ' - ' . $model->getCurrency()?></h1>
<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'org-bank-account-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'bank_name'); ?>
		<?php echo $form->textField($model, 'bank_name'); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'bank_bsb'); ?>
		<?php echo $form->textField($model, 'bank_bsb'); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'bank_number'); ?>
		<?php echo $form->textField($model, 'bank_number'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
$(function() {
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('#org-bank-account-form').on('success', function() {
		$('#billing-streamline-payment-arrange-summary-grid').yiiGridView('update');
		$('.popCancel', win).trigger('click');
	});
});
</script>