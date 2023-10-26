<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'cg-order-update-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'status'),
        $form->dropDownList($model, 'status', CgOrder::$states); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
        $('form#cg-order-update-form', win).on('success', function(e, r){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });
    });
</script>