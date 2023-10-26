<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'erp-product-output-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

    <div class="row">
        <?php echo $form->labelEx($model,'warehouse_id'); ?>
        <?php echo $form->dropDownList($model, 'warehouse_id',ErpProductRoutes::warehouseList());?>
    </div>

    <div class="row rowcol">
        <?php echo $form->labelEx($model,'to_id'),
        $form->hiddenField($model,'to_id', array('data-ov' => $model->to_id));
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => 'toid',
            'sourceUrl' => array('ErpProductRoute/fromSuggest'),
            'value' => empty($model->agent)? '' : $model->agent->name,
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

    <?php $productList = ErpProductRoutes::productList();?>
    <div class="row">
        <?php echo $form->labelEx($model,'product_id'); ?>
        <?php echo $form->dropDownList($model, 'product_id',$productList , array('empty' => 'Select Product'));?>
    </div>

    <div class="row">
        <div class="rowcol">
            <?php echo CHtml::label('Name','forproduct',array('style' => 'width:160px;')); ?>
        </div>
        <div class="rowcol">
            <?php echo CHtml::label('Quantity','forproduct',array('style' => 'width:160px;')); ?>
        </div>
        <div class="rowcol">
            <?php echo CHtml::label('Inventory','forproduct'); ?>
        </div>
    </div>

    <div class="row" id="product-list">
        <?php foreach ( $productList as $productId => $productName ) : ?>
        <div class="row" id="product-list-<?php echo $productId; ?>">
            <div class="rowcol">
                <?php echo CHtml::label("$productName",'forproduct',array('class' => 'product-out-name-box')); ?>
            </div>
            <div class="rowcol">
                    <?php echo CHtml::textField('qty[]','0',array('size'=>20,'maxlength'=>20)); ?>
                    <?php echo CHtml::hiddenField('prod[]',"$productId"); ?>
            </div>
            <div class="rowcol">
                <?php echo  CHtml::label("$inventories[$productId]",'no',array('class' => 'inventory-qty','style' => 'width:50px;')); ?>
            </div>
            <div class="rowcol">
                <?php echo  CHtml::imageButton('/images/delete_icon.png',array('class' => 'btn-delete-product','style' => 'width:18px;height:18px;')); ?>
            </div>
        </div>
        <?php endforeach; ?>
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

    var inventories = <?php echo json_encode($inventories); ?>;

    function addNewProduct(productId,productName,inventory){
        var new_item = ' <div class="row" id="product-list-' + productId + '">';
        new_item +=  '<div class="rowcol">';
        new_item += '<label class="product-out-name-box" for="forproduct">' + productName + '</label></div>';
        new_item +=  '<div class="rowcol">'
        new_item +=  '<input size="20" maxlength="20" name="qty[]" id="qty" type="text" value="0"></div>';
        new_item += '<input type="hidden" value="' + productId+ '" name="prod[]" id="prod">'
        new_item +=  '<div class="rowcol">'
        new_item +=  '<label class="inventory-qty" style="width:50px;" for="no">' + inventory + '</label></div>';
        new_item +=  '<div class="rowcol">';
        new_item +=  '<input class="btn-delete-product" style="width:18px;height:18px;" src="/images/delete_icon.png" type="image" name="yt0"></div>';
        new_item +=  '</div>';

        $('#product-list').append(new_item);
    }

	$('form#erp-product-output-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

    // check quantity can't be more than current inventory
    $('input[type="submit"]').click(function(e){
        var parentEvent = e;
        if ( $('#product-list').children().length <= 0 ) {
            parentEvent.preventDefault();
            alert('Please select at least one product');
            return;
        }

        $('#product-list').children().each(function(e){
            var qtyElement = $(this).find("input#qty")
            var qty = parseInt( qtyElement.val() );
            var inventory = parseInt( $(this).find("label.inventory-qty").text() ) ;
            if ( qty > inventory ) {
                alert('Quantity must be less than inventory');
                parentEvent.preventDefault();
                qtyElement.focus();;
                return false;
            }
        });
    });


    $('#ErpProductRoutes_product_id').on('change',function(e){
        var productId = parseInt($(this).val());

        if ( !isNaN(productId) &&  !$('#product-list-'+ productId).length ) {
            var productName = $("#ErpProductRoutes_product_id option[value='" + productId + "']").text();
            addNewProduct(productId, productName, inventories[productId]);
        }
    });

    $('#ErpProductRoutes_warehouse_id').on('change',function(e){
        var warehouseId = parseInt($(this).val());
        $('#product-list').empty();
        $('#ErpProductRoutes_product_id option')[0].selected = true;

        // refresh all product inventories
        $.ajax({
            url: 'ErpProductRoute/refreshInventory',
            dataType: 'json',
            type: 'post',
            data : {'whid' : warehouseId},
            success: function(r){
                if ( r.success == 1 ) {
                    inventories = r.inventories;

                    // add all products again
                    var whid = r.warehouseId;
                    for ( var i in r.products ) {
                        addNewProduct(i, r.products[i],inventories[i]);
                    }

                    $('#ErpProductRoutes_product_id option')[1].selected = true;
                }
            },
            error: function(r, e){ alert(e); }
        });
    });

    $(document).on('click','.btn-delete-product',function(e){
        e.preventDefault();
        $(this).parent().parent().remove();

        // if delete all show select prodcut
        if ( $('#product-list').children().length <= 0 ) {
            $('#ErpProductRoutes_product_id option')[0].selected = true;
        }

    });

});
</script>