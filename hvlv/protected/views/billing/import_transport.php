
<h1> Import Billing From Transport Invoices </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'transport-billing-import-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('billing/AjaxImportTransport'),
    ));
    ?>

    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">Invoices <small>.xlsx</small>File (<a href="/ims/pcae_transport_billing_template.xlsx" target="_blank">Get template file</a>)</label>  <br>
        <input type="file" name="invoice_file" id="invoice_file" />
    </div>

    <p style="margin-top:20px;"><input id="billing_import_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>

</div>
<div id="import_result" style="margin: 10px 0; border: 1px solid;padding:20px;">
</div>


<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');


        $('form#transport-billing-import-form', panel).data('custom_success', function(r){
            $('#import_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#billing_import_btn', panel).attr('disabled', false);
            return true;
        });

    });
</script>
