<?php
/* @var $this CargoBookingController */
/* @var $model CargoBooking */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_date'); ?>
		<?php echo $form->textField($model,'create_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'booking_date_1'); ?>
		<?php echo $form->textField($model,'booking_date_1'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'booking_date_2'); ?>
		<?php echo $form->textField($model,'booking_date_2'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'delivery_period'); ?>
		<?php echo $form->textField($model,'delivery_period',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'org_id'); ?>
		<?php echo $form->textField($model,'org_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'shipment_id'); ?>
		<?php echo $form->textField($model,'shipment_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'booking_number'); ?>
		<?php echo $form->textField($model,'booking_number',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'meta'); ?>
		<?php echo $form->textArea($model,'meta',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'destination'); ?>
		<?php echo $form->textField($model,'destination'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'fee'); ?>
		<?php echo $form->textField($model,'fee',array('size'=>5,'maxlength'=>5)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ref'); ?>
		<?php echo $form->textField($model,'ref',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'cargo_process_id'); ?>
		<?php echo $form->textField($model,'cargo_process_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->