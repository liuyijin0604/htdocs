<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cnl-order-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>
	<div class="row">
	<?php echo $form->labelEx($model,'cargoType'); ?>
<?php echo $form->textField($model,'cargoType'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'bizType'); ?>
<?php echo $form->textField($model,'bizType'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'status'); ?>
<?php echo $form->textField($model,'status'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'incoterm'); ?>
<?php echo $form->textField($model,'incoterm'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'orderCode'); ?>
<?php echo $form->textField($model,'orderCode',array('size'=>30,'maxlength'=>30)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'refCode'); ?>
<?php echo $form->textField($model,'refCode',array('size'=>30,'maxlength'=>30)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'pol'); ?>
<?php echo $form->textField($model,'pol',array('size'=>5,'maxlength'=>5)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'pod'); ?>
<?php echo $form->textField($model,'pod',array('size'=>5,'maxlength'=>5)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'targetEta'); ?>
<?php echo $form->textField($model,'targetEta'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'cargoReadyDate'); ?>
<?php echo $form->textField($model,'cargoReadyDate'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'containerLoad'); ?>
<?php echo $form->textField($model,'containerLoad'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'transportMode'); ?>
<?php echo $form->textField($model,'transportMode'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'gmtModified'); ?>
<?php echo $form->textField($model,'gmtModified'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'feature'); ?>
<?php echo $form->textArea($model,'feature',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'remark'); ?>
<?php echo $form->textArea($model,'remark',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'meta'); ?>
<?php echo $form->textArea($model,'meta',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->