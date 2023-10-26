
<h1> Tools-Invoice Diff </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'tid-form',
        'enableAjaxValidation'=>false
    ));
    ?>

    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">Load Source Invoice Data From Courier</label>
        <input type="file" name="inv_file" id="inv_file" />
    </div>
   <div class="row">
        <label for="rate_option">Cost Rate</label>
        <?php echo CHtml::dropDownList('rate_option', '', [ImportChargeCode::FASTWAY_ID_OLD => 'Fastway Syd 2017', ImportChargeCode::FASTWAY_ID => 'Fastway Syd 2020',ImportChargeCode::TNT_SYDNEY_ID => 'TNT'], ['empty' => 'Select One', 'required' => 'required']); ?>
    </div>
    
    <p style="margin-top:20px;"><input id="transform_btn" type="submit" value="Export" /></p>


    <?php $this->endWidget(); ?>
</div>
<div id="transform_result" style="margin: 10px 0; border: 1px solid;padding:20px;">
</div>



<script type="text/javascript">


    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
        var panel = win.data('panel');


        $('#transform_btn').on('click',function() {
            var formData = new FormData();
            formData.append('inv_file', $('#inv_file', win)[0].files[0]);
            formData.append('rate_option', $('#rate_option', win).val());

            $.ajax({
                url: '<?=Yii::app()->createUrl("invoice/toolsInvoiceDiff")?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(r) {
                    r = jQuery.parseJSON(r);
                    $('#transform_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
                    $('#transform_btn', panel).attr('disabled', false);
                },
                error: function(r) {
                    myApp.alert('System error', false);
                }
            });
            return false;
        });

    });
</script>
