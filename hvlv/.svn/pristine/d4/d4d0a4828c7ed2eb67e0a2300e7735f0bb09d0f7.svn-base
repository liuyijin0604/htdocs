<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-stock-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>
	<div class="row">
	<?php echo $form->labelEx($model,'prod_id'); ?>
<?php echo $form->textField($model,'prod_id',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'org_id'); ?>
<?php echo $form->textField($model,'org_id',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'qty'); ?>
<?php echo $form->textField($model,'qty'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'qty_res'); ?>
<?php echo $form->textField($model,'qty_res'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'expiry'); ?>
<?php echo $form->textField($model,'expiry'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'batch'); ?>
<?php echo $form->textField($model,'batch',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'sn'); ?>
<?php echo $form->textField($model,'sn',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'updated'); ?>
<?php echo $form->textField($model,'updated'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->