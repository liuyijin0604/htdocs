<?php
/* @var $this GoogleReviewController */
/* @var $model GoogleReview */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'google-review-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'hbn'); ?>
		<?php echo $form->textField($model,'hbn',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'hbn'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ref'); ?>
		<?php echo $form->textField($model,'ref',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'ref'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'time_start'); ?>
		<?php echo $form->textField($model,'time_start'); ?>
		<?php echo $form->error($model,'time_start'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'time_done'); ?>
		<?php echo $form->textField($model,'time_done'); ?>
		<?php echo $form->error($model,'time_done'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cnee_name'); ?>
		<?php echo $form->textField($model,'cnee_name',array('size'=>60,'maxlength'=>200)); ?>
		<?php echo $form->error($model,'cnee_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cnee_email'); ?>
		<?php echo $form->textField($model,'cnee_email',array('size'=>60,'maxlength'=>200)); ?>
		<?php echo $form->error($model,'cnee_email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cnee_tel'); ?>
		<?php echo $form->textField($model,'cnee_tel',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'cnee_tel'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cnor_city'); ?>
		<?php echo $form->textField($model,'cnor_city',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'cnor_city'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cnor_state'); ?>
		<?php echo $form->textField($model,'cnor_state',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'cnor_state'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pkg'); ?>
		<?php echo $form->textField($model,'pkg',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'pkg'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'weight'); ?>
		<?php echo $form->textField($model,'weight',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'weight'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'time_arrived'); ?>
		<?php echo $form->textField($model,'time_arrived'); ?>
		<?php echo $form->error($model,'time_arrived'); ?>
	</div>

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

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->