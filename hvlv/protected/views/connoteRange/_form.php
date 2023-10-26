<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'connote-range-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>
	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model,'type'); ?>
	<?php echo $form->dropdownList($model,'type', ConnoteRange::$types, ['empty' => 'Select One']); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'status'); ?>
	<?php echo $form->dropdownList($model,'status', ConnoteRange::$states, ['empty' => 'Select One']); ?>
	</div>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model,'prefix'); ?>
<?php echo $form->textField($model,'prefix',array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'suffix'); ?>
<?php echo $form->textField($model,'suffix',array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model,'start'); ?>
<?php echo $form->textField($model,'start',array('size'=>10,'maxlength'=>9)); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'finish'); ?>
<?php echo $form->textField($model,'finish',array('size'=>10,'maxlength'=>9)); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'digits'); ?>
<?php echo $form->textField($model,'digits',array('size'=>2,'maxlength'=>1)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->