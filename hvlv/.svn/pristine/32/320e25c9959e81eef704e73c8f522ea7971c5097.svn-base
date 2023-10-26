<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'erp-product-inout-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

    <div class="row">
        <?php echo $form->labelEx($model,'warehouse_id'); ?>
        <?php echo $form->dropDownList($model, 'warehouse_id',ErpProductRoutes::warehouseList(),array('empty' => 'Select Warehouse')); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'product_id'); ?>
        <?php echo $form->dropDownList($model, 'product_id',ErpProductRoutes::productList(),array('empty' => 'Select Product')); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'quantity'); ?>
        <?php echo $form->textField($model,'quantity',array('size'=>50,'maxlength'=>50)); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'cost'); ?>
        <?php echo $form->textField($model,'cost',array('size'=>50,'maxlength'=>50)); ?>
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
	$('form#erp-product-inout-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
});
</script>