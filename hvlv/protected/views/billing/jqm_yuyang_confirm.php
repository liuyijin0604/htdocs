
<h1>Confirm YuYang</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'tnebilling-confirm-form',
    'enableAjaxValidation'=>false,
)); ?>
<div class="form">

    <input type="hidden" name="afid" value="<?php echo $model->id; ?>">

    <div class="row">
        <?php
        echo CHtml::label('Department', 'dpmt');
        echo CHtml::dropDownList('dpmt', 'dpmt', [30 => 'Air/Sea', 40 => '3PL',], array('prompt' => $this->t('Select One'), 'required' => 'required'));
        ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Confirm');?>
    </div>
</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
    $(function(){

        var win = $("#jqmw_<?=$_GET['tabid'];?>");
        $('#tnebilling-confirm-form', win).on('success', function() {
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });

    });
</script>