<?php
/* @var $this QuotesController */
/* @var $model Quotes */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'quotes-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'route_id'); ?>
		<?php echo $form->textField($model,'route_id'); ?>
		<?php echo $form->error($model,'route_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'uld_type'); ?>
		<?php echo $form->textField($model,'uld_type',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'uld_type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'wt_lo'); ?>
		<?php echo $form->textField($model,'wt_lo'); ?>
		<?php echo $form->error($model,'wt_lo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'wt_hi'); ?>
		<?php echo $form->textField($model,'wt_hi'); ?>
		<?php echo $form->error($model,'wt_hi'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pkg'); ?>
		<?php echo $form->textField($model,'pkg',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'pkg'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'min'); ?>
		<?php echo $form->textField($model,'min',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'min'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date_eff'); ?>
		<?php echo $form->textField($model,'date_eff'); ?>
		<?php echo $form->error($model,'date_eff'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date_exp'); ?>
		<?php echo $form->textField($model,'date_exp'); ?>
		<?php echo $form->error($model,'date_exp'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->