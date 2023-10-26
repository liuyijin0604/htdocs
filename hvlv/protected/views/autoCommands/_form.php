<?php
/* @var $this AutoCommandsController */
/* @var $model AutoCommands */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'auto-commands-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'command_name'); ?>
		<?php echo $form->textField($model,'command_name',array('size'=>60,'maxlength'=>150)); ?>
		<?php echo $form->error($model,'command_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'func_name'); ?>
		<?php echo $form->textField($model,'func_name',array('size'=>60,'maxlength'=>150)); ?>
		<?php echo $form->error($model,'func_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'day'); ?>
		<?php echo $form->textField($model,'day'); ?>
		<?php echo $form->error($model,'day'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'week_day'); ?>
		<?php echo $form->textField($model,'week_day'); ?>
		<?php echo $form->error($model,'week_day'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'hour'); ?>
		<?php echo $form->textField($model,'hour'); ?>
		<?php echo $form->error($model,'hour'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'minute'); ?>
		<?php echo $form->textField($model,'minute'); ?>
		<?php echo $form->error($model,'minute'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->