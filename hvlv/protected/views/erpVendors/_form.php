<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'erp-vendors-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'product_name'); ?>
		<?php echo $form->textField($model,'product_name',array('size'=>50,'maxlength'=>50)); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'product_code'); ?>
        <?php echo $form->textField($model,'product_code',array('size'=>50,'maxlength'=>50)); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'min_qty'); ?>
        <?php echo $form->textField($model,'min_qty',array('size'=>50,'maxlength'=>50)); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'price'); ?>
        <?php echo $form->textField($model,'price',array('size'=>50,'maxlength'=>50)); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'valid_from_time'); ?>
        <?php echo $form->textField($model,'valid_from_time',['class' => 'date_input']); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'valid_to_time'); ?>
        <?php echo $form->textField($model,'valid_to_time',['class' => 'date_input']); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'notes'); ?>
        <?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?>
    </div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('form#erp-vendors-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
});
</script>