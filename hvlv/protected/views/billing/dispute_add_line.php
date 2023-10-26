<h1>Add Line for <?=$model->invoice_no?></h1>

<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'billing-courier-dispute-add-line-form',
	'enableAjaxValidation' => false,
)); ?>

	<br>

	<div class="row">
		<?php echo $form->labelEx($line, 'shipment_no'); ?>
		<?php echo $form->textField($line, 'shipment_no', ['size' => 20]); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Consol No', 'consol'); ?>
		<?php echo CHtml::textField('consol', '', ['size' => 20]); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($line, 'value'); ?>
		<?php echo $form->textField($line, 'value', ['size' => 20]); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Code', 'code'); ?>
		<?php echo $form->textField($line, 'mdata[code]', ['size' => 20]); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Update'), ['id' => 'dispute-add-line-btn']); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

<script>
$(function() {
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('#billing-courier-dispute-add-line-form').on('success', function() {
		$('.popCancel', win).trigger('click');
		$('div[id*="dispute-grid"]').yiiGridView('update');
	});
});
</script>