<div style="right: 20px;position: absolute;">
  <!--  <div class="icon" style="background-position:-16px 0"></div><a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('billing/mngEdiChargecodeMap');?>" title="Manage Chargecode Map">Manage Chargecode Map</a>
-->
    <div class="icon" style="background-position:-16px 0"></div><a class="tab_link"  href="<?=$this->createUrl('billing/portCostImport');?>" title="Port Cost Import">Port Cost Import</a>
    <div class="icon" style="background-position:-16px 0"></div><a class="tab_link"  href="<?=$this->createUrl('billing/pcb2Xero');?>" title="Priority Cargo Billing to Xero">Priority Cargo Billing to Xero</a>

</div>
<h1> Exports Service Billing Import </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'export-invoice-import-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('billing/AjaxEdiInvoiceImport'),
    ));
    ?>


    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">Only For EDI invoice currently <small>.xlsx File</small></label> <br>
        <input type="file" name="export_ar_file" id="export_ar_file" />
    </div>

    <p style="margin-top:20px;"><input id="edi_invoice_import_btn" type="submit" value="Submit" /></p>

    <?php $this->endWidget(); ?>
</div>

<br/>
<div id="progress"></div>
<div id="message"></div>


<div id="edi_invoice_import_result" style="margin: 10px 0; border: 1px solid;padding:20px;">
</div>



<script type="text/javascript">

    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        var timer;

        // The function to refresh the progress bar.
        function refreshProgress() {
            // We use Ajax again to check the progress by calling the checker script.
            // Also pass the session id to read the file because the file which storing the progress is placed in a file per session.
            // If the call was success, display the progress bar.
            $.ajax({
                url: "<?php echo Yii::app()->createAbsoluteUrl("billing/AjaxCheckEdiInvoiceProgress") ;?>",
                dataType: 'json',
                success:function(data){
                    $("#progress",panel).html('<div class="bar" style="width:' + data.percent + '%"></div>');
                    $("#message",panel).html(data.message);
                    // If the process is completed, we should stop the checking process.
                    if ( data.percent == 100 ) {
                        window.clearInterval(timer);
                        timer = window.setInterval(completed, 1000);
                    }
                }
            });
        }

        function completed() {
            $("#message",panel).html("Completed");
            window.clearInterval(timer);
        }

        // Trigger the process in web server.
        // Refresh the progress bar every 1 second.
        $('input[type="submit"]',panel).click(function(e){
         //   alert('ok');
             timer = window.setInterval(refreshProgress, 1000);
        });

        $('form#export-invoice-import-form', panel).data('custom_success', function(r){
            $('#edi_invoice_import_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#edi_invoice_import_btn', panel).attr('disabled', false);
            return true;
        });




    });
</script>
