
<h1> Import Priority Cargo Billing to Xero </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'pc-billing-import-xero-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('billing/AjaxPcbImportXero'),
    ));
    ?>


    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">Only For Priority Cargo Billing currently </label> <br>
        <input type="file" name="pcb_file" id="pcb_file" />
    </div>

    <p style="margin-top:20px;"><input id="pc_billing_import_xero_btn" type="submit" value="Submit" /></p>

    <?php $this->endWidget(); ?>
</div>

<br/>


<div id="pc_billing_import_xero_result" style="margin: 10px 0; border: 1px solid;padding:20px;">
</div>



<script type="text/javascript">
    $(function(){

        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');


        $('form#pc-billing-import-xero-form', panel).data('custom_success', function(r){
            $('#pc_billing_import_xero_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#pc_billing_import_xero_btn', panel).attr('disabled', false);
            return true;
        });




    });
</script>
