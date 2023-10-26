<?php
/* @var $this WordReplaceUsageLogController */
/* @var $model WordReplaceUsageLog */
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
		<?php echo $form->label($model,'org_id'); ?>
		<?php echo $form->textField($model,'org_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'original_word'); ?>
		<?php echo $form->textField($model,'original_word',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replace_word'); ?>
		<?php echo $form->textField($model,'replace_word',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'meta'); ?>
		<?php echo $form->textArea($model,'meta',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'process_time'); ?>
		<?php echo $form->textField($model,'process_time'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->