<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'erp-product-return-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

    <div class="row rowcol">
        <?php echo $form->labelEx($model,'from_id'),
        $form->hiddenField($model,'from_id', array('data-ov' => $model->from_id));
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => 'toid',
            'sourceUrl' => array('ErpProductRoute/driverSuggest'),
            'value' => empty($model->fdriver)? '' : $model->fdriver->name,
            'options' => array(
                'showAnim' => 'fold',
                'minLength' => 2,
                'delay' => 200,
                'autoFocus' => true,
                'select' => 'js:function(evt, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
                'change' => 'js:function(evt, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
            ),
            'htmlOptions' => array(
                'class' => 'required',
                'size' => '25',
            ),
        ));
        ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'warehouse_id'); ?>
        <?php echo $form->dropDownList($model, 'warehouse_id',ErpProductRoutes::warehouseList(),array(
                'empty' => 'Select Warehouse',
                'ajax' => array(
                    'type' => 'POST',
                    'url' => CController::createUrl('UpdateInventory'),
                    'update' => '#inventory_qty'
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
                    'update' => '#inventory_qty'
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
            <?php echo  CHtml::label('0','no',array('id' => 'inventory_qty')); ?>
        </div>
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
	$('form#erp-product-return-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
});
</script>