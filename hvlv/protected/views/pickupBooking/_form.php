<?php
/* @var $this PickupBookingController */
/* @var $model PickupBooking */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pickup-booking-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'create_date'); ?>
		<?php echo $form->textField($model,'create_date', array('disabled' => true)); ?>
		<?php echo $form->error($model,'create_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'booking_time'); ?>
		<?php echo $form->textField($model,'booking_time', array('disabled' => true)); ?>
		<?php echo $form->error($model,'booking_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model, 'status', PickupBooking::$state, array());?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_name'); ?>
		<?php echo $form->textField($model,'driver_name',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'driver_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'rego'); ?>
		<?php echo $form->textField($model,'rego',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'rego'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'company_name'); ?>
		<?php echo $form->textField($model,'company_name',array('size'=>60,'maxlength'=>150,'disabled' => true)); ?>
		<?php echo $form->error($model,'company_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'company_email'); ?>
		<?php echo $form->textField($model,'company_email',array('size'=>60,'maxlength'=>150,'disabled' => true)); ?>
		<?php echo $form->error($model,'company_email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'note'); ?>
		<?php echo $form->textArea($model,'note',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'note'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'fee'); ?>
		<?php echo $form->textField($model,'fee',array('size'=>5,'maxlength'=>5,'disabled' => true)); ?>
		<?php echo $form->error($model,'fee'); ?>
	</div>



	<div class="row">
		<div class="row rowcol">
			<?php echo CHtml::label("Container No.", 'container_no'); ?>
			<?php echo CHtml::textField('mdata[container_no]', $model->mdata['container_no'], array('size' => 20,'id'=>'container_no', 'disabled' => true)); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::label("House BL", 'house_bl'); ?>
			<?php echo CHtml::textField('mdata[house_bl]', $model->mdata['house_bl'], array('size' => 20,'id'=>'house_bl', 'disabled' => true)); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::label("Pallet Requirement", 'pallet_requirement'); ?>
			<?php echo CHtml::textField('mdata[pallet_requirement]', $model->mdata['pallet_requirement'], array('size' => 20,'id'=>'pallet_requirement', 'disabled' => true)); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::label("Shrink Wrap", 'shrink_wrap'); ?>
			<?php echo CHtml::textField('mdata[shrink_wrap]', $model->mdata['shrink_wrap'] == 'wrap'?"Yes":"No", array('size' => 20,'id'=>'shrink_wrap', 'disabled' => true)); ?>
		</div>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->