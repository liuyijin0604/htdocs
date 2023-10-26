<?php
/* @var $this DeconsolidationController */
/* @var $model Deconsolidation */
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
		<?php echo $form->label($model,'shipment_id'); ?>
		<?php echo $form->textField($model,'shipment_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'cargo_process_id'); ?>
		<?php echo $form->textField($model,'cargo_process_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'depot'); ?>
		<?php echo $form->textField($model,'depot'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_time'); ?>
		<?php echo $form->textField($model,'create_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'op_complete_time'); ?>
		<?php echo $form->textField($model,'op_complete_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'warehouse_complete_time'); ?>
		<?php echo $form->textField($model,'warehouse_complete_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'meta'); ?>
		<?php echo $form->textArea($model,'meta',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'has_problem'); ?>
		<?php echo $form->textField($model,'has_problem'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'assigned_user'); ?>
		<?php echo $form->textField($model,'assigned_user'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->