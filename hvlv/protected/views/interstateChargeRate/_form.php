<?php
/* @var $this InterstateChargeRateController */
/* @var $model InterstateChargeRate */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'interstate-charge-rate-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'departure_depot'); ?>
		<?php echo $form->textField($model,'departure_depot'); ?>
		<?php echo $form->error($model,'departure_depot'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'destination_depot'); ?>
		<?php echo $form->textField($model,'destination_depot'); ?>
		<?php echo $form->error($model,'destination_depot'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'org_id'); ?>
		<?php echo $form->textField($model,'org_id'); ?>
		<?php echo $form->error($model,'org_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'weight_low'); ?>
		<?php echo $form->textField($model,'weight_low',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'weight_low'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'weight_high'); ?>
		<?php echo $form->textField($model,'weight_high',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'weight_high'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'base'); ?>
		<?php echo $form->textField($model,'base',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'base'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'item'); ?>
		<?php echo $form->textField($model,'item',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'item'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'perkg'); ?>
		<?php echo $form->textField($model,'perkg',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'perkg'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'per_pallet'); ?>
		<?php echo $form->textField($model,'per_pallet',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'per_pallet'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'minimum'); ?>
		<?php echo $form->textField($model,'minimum',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'minimum'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->textField($model,'type'); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'start_date'); ?>
		<?php echo $form->textField($model,'start_date'); ?>
		<?php echo $form->error($model,'start_date'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->