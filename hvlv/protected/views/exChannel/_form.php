<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ex-channel-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row rowcol rowlef">
	<?php echo $form->labelEx($model,'status'); ?>
	<?php echo $form->dropdownList($model,'status', ExChannel::$states, ['empty' => 'Select One']); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'code'); ?>
	<?php echo $form->textField($model,'code',array('size'=>5,'maxlength'=>5)); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'name'); ?>
	<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Suffix','suff');?>
		<?php echo CHtml::textField('mdata[suffix]', @$model->mdata['suffix'], ['size' => '10']); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'pod'); ?>
	<?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pols'), ['empty' => 'Select One']); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Dec. Type','dtype');?>
		<?php echo CHtml::radioButtonList('mdata[dtype]', @$model->mdata['dtype'], ['cc' => 'CC', 'bc' => 'BC'], ['labelOptions' => ['class' => 'radio_label'], 'separator' => '&nbsp;&nbsp']); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->