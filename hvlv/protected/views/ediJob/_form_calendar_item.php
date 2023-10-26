<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'calendar-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row">
		<?php echo CHtml::label('Task # or Job #', 'no'); ?>
		<?php echo CHtml::textField('no', $model->no, ['size' => 20]); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'time'); ?>
		<?php echo $form->textField($model, 'time', ['class' => 'time_input', 'size' => 20]); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'ref'); ?>
		<?php echo $form->textArea($model, 'ref', ['rows' => 10, 'cols' => 80]); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script>
$(function() {
	var win = $("#jqmw_<?=$_GET['tabid'];?>");

	$('#calendar-form', win).on('success', function() {
		$('#calendar select#type').trigger('change');

		setTimeout(function() {
			$('.popCancel').trigger('click');
		}, 1e3);
	});
});
</script>