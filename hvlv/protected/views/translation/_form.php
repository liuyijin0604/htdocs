<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'translation-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropdownList($model,'type', Translation::$types, array('empty' => 'Select One')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'o'); ?>
		<?php echo $model->o; ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'t'); ?>
		<?php echo $form->textField($model,'t',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->