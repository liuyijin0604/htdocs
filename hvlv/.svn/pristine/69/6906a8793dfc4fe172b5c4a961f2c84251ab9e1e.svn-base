<?php
/* @var $this ExImageController */
/* @var $model ExImage */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ex-image-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'file_adr'); ?>
		<?php echo $form->textField($model,'file_adr',array('size'=>40,'maxlength'=>40)); ?>
		<?php echo $form->error($model,'file_adr'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'hbn'); ?>
		<?php echo $form->textField($model,'hbn',array('size'=>40,'maxlength'=>40)); ?>
		<?php echo $form->error($model,'hbn'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'islinked'); ?>
		<?php echo $form->textField($model,'islinked'); ?>
		<?php echo $form->error($model,'islinked'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'agent_id'); ?>
		<?php echo $form->textField($model,'agent_id',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'agent_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pdf_number'); ?>
		<?php echo $form->textField($model,'pdf_number',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'pdf_number'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date'); ?>
		<?php echo $form->textField($model,'date'); ?>
		<?php echo $form->error($model,'date'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->