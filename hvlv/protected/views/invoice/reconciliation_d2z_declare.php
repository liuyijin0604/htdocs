<h3><?=$this->t('D2Z Reconciliaiton '.$type);?></h3>
<div class="form">

    <?php 
    $action = ($type=='RTS')?$this->createUrl("invoice/d2zReconciliationRTS"):$this->createUrl("invoice/d2zReconciliationDeclare");

    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'invoice-reconciliation-form-d2z',
        'enableAjaxValidation'=>false,
        'action'=>$action
    )); ?>
    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">Load <?=$type?> Invoice Data From Courier</label>
        <input type="file" name="inv_file" id="inv_file" />
         <div class="row">
            <?php echo CHtml::label('D2Z Invoice no:','invoice_no');?>
            <?php echo CHtml::textField('invoice_no','');?>
        </div>
        <div class="row">
            <?php echo CHtml::label('Invoice Issue date:','invoice_issue_date');?>
            <?php echo CHtml::textField('invoice_issue_date','',array('class'=>'date_input','id'=>'invoice_issue_date'.$_GET['tabid']));?>
        </div>
        <div class="row">
            <?php echo CHtml::label('Export errs','Export errs');?>
            <?php echo CHtml::checkbox('export',1);?>
        </div>
    </div>
<br/>
    <div class="row buttons">
        <?php echo CHtml::submitButton('Submit',['id' => 'btn-save']); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        tab.off('reload_tab').on('reload_tab', function(){
            var t = $('.ui-tabs', panel);
            t.tabs('load', t.tabs('option','active'));
        });

        var win = $('#jqmw_<?=$_GET["tabid"];?>');

        $('form#invoice-reconciliation-form-d2z', win).on('success', function(e, r){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });

    });
</script>
