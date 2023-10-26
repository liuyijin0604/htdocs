<?php
/* @var $this AutoCommandsController */
/* @var $model AutoCommands */
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
		<?php echo $form->label($model,'command_name'); ?>
		<?php echo $form->textField($model,'command_name',array('size'=>60,'maxlength'=>150)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'func_name'); ?>
		<?php echo $form->textField($model,'func_name',array('size'=>60,'maxlength'=>150)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'day'); ?>
		<?php echo $form->textField($model,'day'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'week_day'); ?>
		<?php echo $form->textField($model,'week_day'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'hour'); ?>
		<?php echo $form->textField($model,'hour'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'minute'); ?>
		<?php echo $form->textField($model,'minute'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->