<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'erp-product-inout-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

    <div class="row">
        <?php echo $form->labelEx($model,'warehouse_id'); ?>
        <?php echo $form->dropDownList($model, 'warehouse_id',ErpProductRoutes::warehouseList(),array(
                'empty' => 'Select Warehouse',
                'ajax' => array(
                    'type' => 'POST',
                    'url' => CController::createUrl('UpdateInventory'),
                    'update' => '#inventory-qty'
                )
            )
        );
        ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'product_id'); ?>
        <?php echo $form->dropDownList($model, 'product_id',ErpProductRoutes::productList(),array(
                'empty' => 'Select Product',
                'ajax' => array(
                    'type' => 'POST',
                    'url' => CController::createUrl('UpdateInventory'),
                    'update' => '#inventory-qty'
                )
            )
        );
        ?>
	</div>


    <div class="row">
        <div class="rowcol">
            <?php echo $form->labelEx($model,'quantity'); ?>
            <?php echo $form->textField($model,'quantity',array('size'=>50,'maxlength'=>50)); ?>
        </div>
        <div class="rowcol">
            <?php echo  CHtml::label('Inventory','no'); ?>
            <?php echo  CHtml::label('0','no',array('id' => 'inventory-qty','class' => 'inventory-qty')); ?>
        </div>
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

    // check quantity can't be more than current inventory
    $('input[type="submit"]').click(function(e){

        var inventory =  parseInt($('#inventory-qty').text());
        var qty = parseInt($('#ErpProductRoutes_quantity').val());

        if ( qty > inventory  || qty <= 0 ) {
            e.preventDefault();
            $('#ErpProductRoutes_quantity').focus();

            if ( qty > 0 ) {
                alert('Quantity must be less than inventory');
            } else {
                alert('Quantity must be more than zero');
            }
        }

    });

});
</script>