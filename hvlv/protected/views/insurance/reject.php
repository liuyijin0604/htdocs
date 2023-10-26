<h1><?=$this->t('Reject the Claim');?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
    'attributes'=>array(
        'id',
        'note',
        'date_added',
        array('name' => 'status', 'value' => $model->getStatus()),
        array('name' => 'Customer', 'type' => 'raw','value' => $model->shipment->agent->name),
        array('name' => 'Amount', 'type' => 'raw','value' => $model->shipment->insurance)
    )
));
?>
<br />
<p>

<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'insurance-reject-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <div class="row">
        <?php echo CHtml::label('Reason','for_insurace_reject'); ?>
        <?php echo CHtml::textField('reason',''); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($this->t('Reject')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

</p>

<script type="text/javascript">
    $(function(){
        var win = $("#jqmw_<?=$_GET['tabid'];?>");
        $('#insurance-reject-form', win).on('success', function(){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });
    });
</script>