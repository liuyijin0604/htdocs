<div style="right: 20px;position: absolute;top:20px;">
    <a class="tab_link" href="<?=$this->createUrl('insurance/list');?>" title="Waiting for Approving"><div class="icon" style="background-position:-16px 0"></div>Approving</a>
    <a class="tab_link" href="<?=$this->createUrl('insurance/all');?>" title="Finished Claims"><div class="icon" style="background-position:-16px 0"></div>Finished</a>
</div>

<h1>Enter Shipment No. for Insurance Claim</h1>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'insurance-lodge-form',
        'enableAjaxValidation'=>false,
    ));
    ?>
    <p>Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>
    <?php $this->endWidget(); ?>
    <div id="result" style="display:none;margin: 20px; border: 1px solid;padding:30px 40px; font-weight: bold; font-size: 30px;">
    </div>
</div>


<div class="form" id="claim-lodge-form" style="display:none;">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'insurance-claim-lodge-form',
        'enableAjaxValidation'=>false,
    ));
    ?>
    <p> <input type="hidden" id="barcode" name="sid" value="" /></p>
    <p> Note: <br/>
        <textarea id="note" name="note" rows="4" cols="50"></textarea></p>

    <br/>
    <p>Upload Photos(only supports PNG or JPG):

        <br/>1. <input type="file" name="claim_pics1" id="claim_pics1" /><br/>
        <br/>2. <input type="file" name="claim_pics2" id="claim_pics2" /><br/>
        <br/>3. <input type="file" name="claim_pics3" id="claim_pics3" /><br/>
    </p>
    <br/>
    <input id="claim_btn" type="submit" value="Make a Claim" />

    <?php $this->endWidget(); ?>

    <div id="claim_result" style="display:none;margin: 20px; border: 1px solid;padding:30px 40px; font-weight: bold; font-size: 30px;">

</div>


<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('input#scan', panel).focus();
        $('form#insurance-lodge-form', panel).data('custom_success', function(r){
            $('#result', panel).text(r.msg).css('color', r.color).fadeIn(100, function(){
                if ( r.status == 1 ) {
                    $('#claim-lodge-form',panel).show();
                    $('#barcode',panel).val(r.id);
                } else {
                    $('#claim-lodge-form',panel).hide();
                    $('#barcode',panel).val('');
                }
            });

            // refresh returned item list
           // $('#im-rts-parcel-grid', panel).yiiGridView('update');

            return true;
        }).on('submit', function(){
            $('input#scan', panel).focus();
        });
        $('input#scan', panel).on('focus', function(){
            $(this).select();
        });

        $('input#scan', panel).focus();

        $('form#insurance-claim-lodge-form', panel).data('custom_success', function(r){
            $('#claim_result', panel).text(r.msg).css('color', r.color).fadeIn(100, function(){
                $('#claim_btn').prop('disabled',false);
            });
            return true;
        });

    });
</script>
