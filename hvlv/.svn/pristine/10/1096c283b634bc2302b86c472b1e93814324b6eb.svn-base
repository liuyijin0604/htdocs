<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'erp-products-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'vendor_id'); ?>
        <?php echo $form->dropDownList($model, 'vendor_id',ErpVendors::vendorList(),array('empty' => 'Select One')); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'buy_price'); ?>
        <?php echo $form->textField($model,'buy_price',array('size'=>50,'maxlength'=>50)); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'sale_price'); ?>
        <?php echo $form->textField($model,'sale_price',array('size'=>50,'maxlength'=>50)); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'barcode'); ?>
        <?php echo $form->textField($model,'barcode',array('size'=>50,'maxlength'=>50)); ?>
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
	$('form#erp-products-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
});
</script>