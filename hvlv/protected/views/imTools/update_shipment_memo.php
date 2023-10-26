<h3><?=$this->t('Update Shipment Memo');?></h3>
<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'invoice-reconciliation-form-d2z',
        'enableAjaxValidation'=>false,
    )); ?>
    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">HBN-MEMO file</label>(<a href="/ims/update_memo_template.xlsx" target="_blank">Get template file</a>) <br>
        <input type="file" name="inv_file" id="inv_file" />

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
