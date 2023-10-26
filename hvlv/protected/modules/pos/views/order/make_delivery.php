<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('Home / 主页', array('site/index')),
    'links' => array(
           'Confirm A Delivery / 确认收货',
    ),
));
?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'scan-form',
	'enableAjaxValidation'=>false,
));
?>
<div class="form-group">
    <h1><label>Order Barcode / 订单条码: </label></h1>
    <input class="form-control input-lg" id="scan" type="text" size="30" name="barcode" autocomplete="off" />
</div>


        <input type="hidden" id="scan-code" name="scan_code" value="">

<?php $this->endWidget(); ?>
<div id="scan-result" style="background-color: white;margin-top:10px;">

</div>


    <table style="text-align: right;width:100%;">
        <tfoot>
        <tr><td id="show-confirm" style="display: none;"><?php echo CHtml::button('Confirm', array('class' => 'btn btn-primary deliverydone')); ?></td></tr>
        </tfoot>
    </table>

</div>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){

	$('input#scan').focus();

        $('form#scan-form').on('success', function(e,r){

            $('#show-confirm').show();
            $('input#scan-code').val($('input#scan').val());
            $('input#scan').val('');
            $('input#scan').focus();


            $('#scan-result').html(r.data);
            return true;

        }).on('submit', function(){
            $('#scan_code').val($('#scan').val());
		    $('input#scan').focus();
	    });

    $('input#scan').on('focus', function(){
		$(this).select();
	});

    $('.deliverydone').click(function(e){
        var linkUrl = '<?php echo $this->createUrl('confirmDelivery') ;?>';
        var data = {'bc' : $('#scan-code').val()};
        $.ajax({
            type: 'POST',
            url: linkUrl,
            dataType: 'json',
            data : data,
            success: function (r) {
                $('#show-confirm').hide();
                if ( r.done) {
                    $('#scan-result').html(r.data);
                } else {
                    $('#scan-result').html(r.err);
                }
            }
        });
    });

});
</script>
<?php $this->registerJS(ob_get_clean()); ?>