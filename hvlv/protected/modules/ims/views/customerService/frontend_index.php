<?php
// $this->widget('zii.widgets.CBreadcrumbs', array(
//         'homeLink'=>CHtml::link('Home', array('site/index')),
// 	'links' => array(
//            'Customer Service',
// 	),
// ));
?>
<style>
    #Shipment_tracking_form {
        display: none;
    }
    
    #Note_file {
        display: none;
    }
</style>
<h1><?=Yii::t('shipmentquestion','Customer Service')?></h1>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'cs_qs_form',
        'action'=>Yii::app()->createUrl($this->route),
        'htmlOptions'=>["class"=>'ifrm-form'],
        'method'=>'post',
    )); ?>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Tracking Number').": ".$model->mhbns,Yii::t('shipmentquestion','Tracking Number').": ".$model->mhbns); ?>
        </div>
        <div class="form-group">
                <?php echo CHtml::hiddenField('ShipmentQuestionSubmit[mhbns]',$model->mhbns)?>
<!--                 <?php echo CHtml::label(Yii::t('shipmentquestion','Question'),'faq'); ?><span class="required">*</span>
                <?php echo CHtml::dropDownList('faq','',$faqList,["class"=>"form-control","style"=>"width:250px;"]); ?> -->
        </div>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Email'),'ShipmentQuestionSubmit[email]'); ?><span class="required">*</span>
                 <?php echo CHtml::textField('ShipmentQuestionSubmit[email]','',array("class"=>"form-control","type"=>"email")); ?>
        </div>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Question'),'faq'); ?><span class="required">*</span>
                <?php echo CHtml::dropDownList('ShipmentQuestionSubmit[faq]','',$faqList,["class"=>"form-control","style"=>"width:250px;"]); ?>
        </div>
        </br>
        <div class="form-group" id="Shipment_tracking_form">
            <?php echo CHtml::label(Yii::t('shipmentquestion','Question'),'tracking'); ?><span class="required">*</span>
            <?php echo CHtml::dropDownList('ShipmentQuestionSubmit[tracking]','',$trackingList,["class"=>"form-control","style"=>"width:250px;"]); ?>
        </div>
         <br>
        <div class="form-container" id="Note_file">
            <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Note'),'ShipmentQuestionSubmit[c_note]'); ?><span class="required">*</span>
                <?php echo CHtml::textArea('ShipmentQuestionSubmit[c_note]','',array('cols'=>60, 'rows' => 3,"class"=>"form-control")); ?>
            </div>
            <br>
            <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Upload Pictures'),'Upload Pictures'); ?>
                <?php
                $this->widget('CMultiFileUpload', array(
                    'model'=>$model,
                    'attribute'=>'photos',
                    'accept'=>'jpg|gif|png',
                    'htmlOptions'=>["accept"=>"image/gif, image/jpeg"],
                    'options'=>array(
                    ),
                    'denied'=>'File is not allowed',
                    'max'=>10, // max 10 files
                ));
                ?>
            </div>
        </div>
        <div class="form-group">
                 <?php echo CHtml::submitButton(Yii::t('shipmentquestion','submit'),array("class"=>"form-control update","style"=>"width:250px;")); ?>
        </div>



    <?php $this->endWidget(); ?>
</div>

<br>
<br>
<br>

<script type="text/javascript">

$(function(){


    $('#cs_qs_form').submit(function(event) {
        if($('#ShipmentQuestionSubmit_email').val()=='')
        {
            alert('require email');
            return false;
        }
        if($('#ShipmentQuestionSubmit_faq').val()=='')
        {
            alert('require question');
            return false;
        }
        if($('#ShipmentQuestionSubmit_c_note').val()=='' && $('#ShipmentQuestionSubmit_tracking').val() != '30')
        {
            alert('require note');
            return false;
        }
            event.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute('<?=RecaptchaAPI::RECAPTCHA_V3_SITE_KEY?>', {action: 'cneeShipmentQuery'}).then(function(token) {
                    $('#cs_qs_form').prepend('<input type="hidden" name="token" value="' + token + '">');
                    $('#cs_qs_form').prepend('<input type="hidden" name="action" value="subscribe_newsletter">');
                    $('#cs_qs_form').unbind('submit').submit();
                });;
            });
    });

    $('#cs_qs_form').on('success',function(){
      alert(" Submit Success ");
      window.location.href = 'https://www.toplogistics.com.au/';
    })


});

$(document).ready(
    function() {
        $('#ShipmentQuestionSubmit_faq').on('change', function() {
            if ($('#ShipmentQuestionSubmit_faq').val()== '1') {
                $('#Shipment_tracking_form').show();
                $('#Note_file').hide();
            } else {
                $('#Shipment_tracking_form').hide();
                $('#Note_file').show();
            }
        });

        $('#ShipmentQuestionSubmit_tracking').on('change', function() {
            if ($('#ShipmentQuestionSubmit_tracking').val() == '30') {
                $('#Note_file').hide();
                $('#ShipmentQuestionSubmit_c_note').val() = 'Track Shipment (Already with delivery courier)';
            } else if ($('#ShipmentQuestionSubmit_tracking').val() == '31' || $('#ShipmentQuestionSubmit_tracking').val() == '32' || $('#ShipmentQuestionSubmit_tracking').val() == '33' || $('#ShipmentQuestionSubmit_tracking').val() == '34' || $('#ShipmentQuestionSubmit_tracking').val() == '35')
            {
                $('#Note_file').show();
            }
        })
    }
);
 
</script>