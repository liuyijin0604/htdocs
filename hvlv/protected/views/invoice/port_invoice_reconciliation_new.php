<h1><?=$this->t('New Port Invoice Reconciliaiton');?></h1>


<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'invoice-reconciliation-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">Load Source Invoice Data From Port</label>
        <input type="file" name="inv_file" id="inv_file" />
    </div>

    <div class="row rowcol">
        <?php echo CHtml::label('Invoice Ref.','ref'); ?>
        <?php echo CHtml::textField('ref','',['size' => '12']); ?>
    </div>

    <div class="row rowcol">
        <?php echo CHtml::label('In case replace old one , please tick it','ref'); ?>
        <?php echo CHtml::checkBox('replace') . ' Replace'; ?>
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

        $('form#invoice-reconciliation-form', win).on('success', function(e, r){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });

    });
</script>
