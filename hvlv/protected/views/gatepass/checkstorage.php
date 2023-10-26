<h1>Storage Fee Checking</h1>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'storage-check-form',
        'enableAjaxValidation'=>false,
    ));
    ?>
    <p>Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>
    <p>Check To Date:<?php echo CHtml::textField('check_to_date',"", array('size' => 20,'class'=>"date_input")); ?></p>
    <?php $this->endWidget(); ?>
    <div id="result_check_storage" style="display:none;margin: 20px; border: 1px solid;padding:30px 40px; font-weight: bold; font-size: 42px;">
    </div>

</div>


<script type="text/javascript">
    function generateDduInvoice($shipmentId,$checkToDate)
    {
        var data = {};
        data['shipmentId'] = $shipmentId;
        data['checkToDate'] = $checkToDate;
        data['invoiceShows'] = $('#invoice_shows').val();
        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("invoice/generateShipmentDdpDduInvoice") ;?>',
            data: data,
            dataType: 'json',
            success:function(r){
                if(r.status==1)
                {
                     $('#result_check_storage').html("Invoice: <a target='_blank' href='"+'<?php echo Yii::app()->createAbsoluteUrl("invoice/print") ;?>'+'?id='+r.invoiceId+"'>"+r.invoiceId+"</a>");
                }else
                {
                     $('#result_check_storage').html(r.msg);
                }
            }
        });
    }

    function generateDdpInvoice($shipmentId,$checkToDate)
    {
        var data = {};
        data['shipmentId'] = $shipmentId;
        data['checkToDate'] = $checkToDate;
        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("invoice/generateShipmentDdpDduInvoice") ;?>',
            data: data,
            dataType: 'json',
            success:function(r){
                if(r.status==1)
                {
                     $('#result_check_storage').html("Invoice: <a target='_blank' href='"+'<?php echo Yii::app()->createAbsoluteUrl("invoice/print") ;?>'+'?id='+r.invoiceId+"'>"+r.invoiceId+"</a>");
                }else
                {
                     $('#result_check_storage').html(r.msg);
                }
            }
        });
    }

    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('input#scan', panel).focus();

        $('#scan', panel).keydown(function(e){
            if (e.keyCode == 13 ) {
                $('input#scan', panel).focus();
                var barcode = $(this).val();
                if ( barcode.length > 0 ) {
                    var data = {};
                    data['barcode'] = $('#scan',panel).val();
                    data['check_to_date'] = $('#check_to_date',panel).val();
                    $.ajax({
                        type : 'POST',
                        url : '<?php echo Yii::app()->createAbsoluteUrl("gatepass/Checkstorage") ;?>',
                        data: data,
                        dataType: 'json',
                        success:function(r){
                            $('#result_check_storage', panel).html(r.msg).css('color', r.color).fadeIn(100, function(){
                             });
                        }
                    });
                }
            }
        });

        $('input#scan', panel).on('focus', function(){
            $(this).select();
        });

        $('input#scan', panel).focus();

    });
</script>