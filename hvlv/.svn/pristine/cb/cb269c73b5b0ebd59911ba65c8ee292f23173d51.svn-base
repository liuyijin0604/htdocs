<?php
// $this->widget('zii.widgets.CBreadcrumbs', array(
//         'homeLink'=>CHtml::link('Home', array('site/index')),
// 	'links' => array(
//            'Customer Service',
// 	),
// ));
?>
<h1><?=Yii::t('shipmentquestion','Search Amazon')?></h1>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'cs_qs_form',
        'action'=>Yii::app()->createUrl($this->route),
        'htmlOptions'=>["class"=>'ifrm-form'],
        'method'=>'post',
    )); ?>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Amazon Booking Ref'),'form[amazon_booking_ref]'); ?>
                 <?php echo CHtml::textField('form[amazon_booking_ref]','',array("class"=>"form-control","rows"=>"6")); ?>
        </div>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Amazon Booking Time'),'form[amazon_booking_time]'); ?><span class="required">*</span>
                <input type="date" name='form[amazon_booking_booking]' class='form-control' id = "form_amazon_booking_time"/>
        </div>
         <br>
        <div class="form-group">
                 <?php echo CHtml::submitButton('submit',array("class"=>"form-control update","style"=>"width:250px;","id"=>'submiting')); ?>
        </div>



    <?php $this->endWidget(); ?>
</div>

<br>
<br>
<br>

<script type="text/javascript">

$(function(){

    function uploading_on(obj) {
        obj.addClass('uploading');
        obj.val('    Submiting');
    }

    function uploading_off(obj) {
        obj.removeClass('uploading');
        obj.val('Submit');
        obj.removeProp('disabled');
    }

    $('#cs_qs_form').submit(function(event) {
            event.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute('<?=RecaptchaAPI::RECAPTCHA_V3_SITE_KEY?>', {action: 'amazonSearch'}).then(function(token) {
                    $('#cs_qs_form').prepend('<input type="hidden" name="token" value="' + token + '">');
                    $('#cs_qs_form').prepend('<input type="hidden" name="action" value="subscribe_newsletter">');
            var ref = $('#form_amazon_booking_time').val();
                    if(ref == '')
                    {
                        alert("Amazon booking time is empty");
                        return false;
                    }
                    uploading_on($('#submiting'));
                    $('#cs_qs_form').unbind('submit').submit();
                });
            });
    });

    $('#cs_qs_form').on('success',function(){
      alert(" Submit Success ");
      window.location.href = 'https://www.toplogistics.com.au/';
    })


});


</script>