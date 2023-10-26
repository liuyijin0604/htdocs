
<h1> Port Service Billing Import </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'port-billing-import-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('billing/AjaxPortInvoiceImport'),
    ));
    ?>


    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">Only For Port Billing currently (<a href="/template/port_billing_template.xlsx" target="_blank">Get template file</a>)</label> <br>
        <input type="file" name="port_file" id="port_file" />
    </div>

    <p style="margin-top:20px;"><input id="port_billing_import_btn" type="submit" value="Submit" /></p>

    <?php $this->endWidget(); ?>
</div>

<br/>


<div id="port_billing_import_result" style="margin: 10px 0; border: 1px solid;padding:20px;">
</div>



<script type="text/javascript">
    $(function(){

        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');


        $('form#port-billing-import-form', panel).data('custom_success', function(r){
            $('#port_billing_import_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#port_billing_import_btn', panel).attr('disabled', false);
            return true;
        });




    });
</script>
