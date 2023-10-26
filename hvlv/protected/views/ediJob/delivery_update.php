<h3>Delivery Record</h3>
<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'delivery-record-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'task'); ?>
		<?php echo $form->textField($model, 'task'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>