<?php
/* @var $this WordReplaceController */
/* @var $model WordReplace */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'word-replace-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'pre_word',array('label'=>'Previous Word')); ?>
		<?php echo $form->textField($model,'pre_word',array('size'=>40,'maxlength'=>40)); ?>
		<?php echo $form->error($model,'pre_word'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'replace_word',array('label'=>'New Word')); ?>
		<?php echo $form->textField($model,'replace_word',array('size'=>40,'maxlength'=>40)); ?>
		<?php echo $form->error($model,'replace_word'); ?>
	</div>
        <div class="row">
		<?php echo $form->labelEx($model,'type',array('label'=>'Type')); ?>
                <?php echo $form->RadioButtonList($model,'type', WordReplace::$types,array('labelOptions'=>array('class'=>'radio_label'),'separator'=>'&nbsp;&nbsp;'))
		//<?php echo $form->textField($model,'status'); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status',array('label'=>'Active')); ?>
                <?php echo $form->RadioButtonList($model,'status',array(1=>"Enable",0=>"Disable"),array('labelOptions'=>array('class'=>'radio_label'),'separator'=>'&nbsp;&nbsp;'))
		//<?php echo $form->textField($model,'status'); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('note','note'); ?>
		<?php echo CHtml::textArea('mdata[note]',@$model->mdata['note'],array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->