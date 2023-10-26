<h1>
    <?php
    if ( $data['op'] == 'add') {
        echo $this->t('Add inventory for : ') . $data['name'];
    } else {
        echo $this->t('Minus inventory for : ') . $data['name'];
    }
    ?>
</h1>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'cg-inventory-op-form',
     //   'url' => 'cg/inventoryOp',
        'enableAjaxValidation'=>false,
    )); ?>

    <div class="row">
        <?php echo CHtml::label('Quantity','for_add_inventory'); ?>
        <?php echo CHtml::textField('qty','0',['size' => 25]); ?>
    </div>


    <div class="row">
        <?php echo CHtml::label('Note','for_add_inventory'); ?>
        <?php echo CHtml::textArea('note','',['row' => 18,'col' => 140]); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton( 'Confirm',['id' => 'inventory_op_btn']); ?>
    </div>

<?php $this->endWidget(); ?>

    <script type="text/javascript">
        $(function(){
            var win = $('#jqmw_<?=$_GET["tabid"];?>');
            $('form#cg-inventory-op-form', win).on('success', function(e, r){
                win.data('opener').trigger('onOpen');
                win.jqmHide();
            });

            $('#inventory_op_btn',win).click(function(e){
                var qty = parseInt($('#qty',win).val());
                if ( qty <= 0 ) {
                    alert('Inventory quantity must be more than zero!');
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });
    </script>