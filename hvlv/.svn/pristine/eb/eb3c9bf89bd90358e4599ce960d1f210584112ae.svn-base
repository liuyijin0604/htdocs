<?php
/* @var $this WordReplaceUsageLogController */
/* @var $model WordReplaceUsageLog */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'word-replace-usage-log-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'org_id'); ?>
		<?php echo $form->textField($model,'org_id'); ?>
		<?php echo $form->error($model,'org_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'original_word'); ?>
		<?php echo $form->textField($model,'original_word',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'original_word'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'replace_word'); ?>
		<?php echo $form->textField($model,'replace_word',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'replace_word'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'meta'); ?>
		<?php echo $form->textArea($model,'meta',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'meta'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'process_time'); ?>
		<?php echo $form->textField($model,'process_time'); ?>
		<?php echo $form->error($model,'process_time'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->