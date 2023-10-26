<?php
/* @var $this DeconsolidationController */
/* @var $model Deconsolidation */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'deconsolidation-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'shipment_id'); ?>
		<?php echo $form->textField($model,'shipment_id'); ?>
		<?php echo $form->error($model,'shipment_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cargo_process_id'); ?>
		<?php echo $form->textField($model,'cargo_process_id'); ?>
		<?php echo $form->error($model,'cargo_process_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'depot'); ?>
		<?php echo $form->textField($model,'depot'); ?>
		<?php echo $form->error($model,'depot'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'create_time'); ?>
		<?php echo $form->textField($model,'create_time'); ?>
		<?php echo $form->error($model,'create_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'op_complete_time'); ?>
		<?php echo $form->textField($model,'op_complete_time'); ?>
		<?php echo $form->error($model,'op_complete_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'warehouse_complete_time'); ?>
		<?php echo $form->textField($model,'warehouse_complete_time'); ?>
		<?php echo $form->error($model,'warehouse_complete_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'meta'); ?>
		<?php echo $form->textArea($model,'meta',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'meta'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'has_problem'); ?>
		<?php echo $form->textField($model,'has_problem'); ?>
		<?php echo $form->error($model,'has_problem'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'assigned_user'); ?>
		<?php echo $form->textField($model,'assigned_user'); ?>
		<?php echo $form->error($model,'assigned_user'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->