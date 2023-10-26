<h1> Update Billing Actual Amount </h1>
<br/>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'update-actual-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <input type="hidden" name="bid" value="<?php echo $model->id; ?>">
    <div class="row">
        <label> Supplier : <?php echo $model->cust->shortName(2); ?> </label>
    </div>
    <div class="row">
        <label> Accrual Amount : <?php echo $model->accrual_amount; ?> </label>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'actual_amount'); ?>
        <?php echo $form->textField($model,'actual_amount', ['size' => '12',]); ?>
    </div>


    <div class="row buttons">
        <?php echo CHtml::submitButton('Update'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');

        $('form#update-actual-form', win).on('success', function(e, r){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });
    });
</script>