<h1><?=$this->t('Update Broker Check')?></h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'broker-check-update-form',
		'enableAjaxValidation' => false,
	));?>

	<div class="row">
		<?php echo $form->labelEx($model, 'inv_id'); ?>
		<?php echo $form->textField($model, 'invoice[no]'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'shipment_id'); ?>
		<?php
		if (!empty($model->shipment->hbn)) {
			echo $form->textField($model, 'shipment[hbn]');
		} else {
			echo $form->textField($model, 'shipment[ref]');
		}
		?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'consol_id'); ?>
		<?php echo $form->textField($model, 'consol[no]'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'note'); ?>
		<?php echo $form->textArea($model, 'note', ['rows' => 5, 'cols' => 50]); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Update'); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>

<script>
$(function() {
	var tab = $("#jqmw<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	$('#broker-check-update-form', panel).on('success', function() {
		$('.popCancel', panel).trigger('click');
		$('#broker-check-grid').yiiGridView('update');
	});
});
</script>