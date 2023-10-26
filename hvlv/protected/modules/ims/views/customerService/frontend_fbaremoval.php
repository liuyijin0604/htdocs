<?php
// $this->widget('zii.widgets.CBreadcrumbs', array(
//         'homeLink'=>CHtml::link('Home', array('site/index')),
// 	'links' => array(
//            'Customer Service',
// 	),
// ));
?>
<h1>Removal of inventory from Amazon Fulfillment Centres in Australia</h1>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'cs_qs_form',
        'action'=>Yii::app()->createUrl($this->route),
        'htmlOptions'=>["class"=>'ifrm-form'],
        'method'=>'post',
    )); ?>
       
        <br>
<p>PCA Express provides a service to remove FBA inventory from Amazon Fulfilment Centres and return it to sellers located in China.</p>
<p>Please fill out the following information. We will send you a Return Authorisation (RA No), please use this RA no. in your FBA Removal page as reference no.</p>
<p>PCA Express provides this service independently from Amazon and is not affiliated or associated in any way with Amazon.</p>
<div class="ufo-fieldtype-4 ufo-customform-row ufo-row-3341" style="margin-top:10px;">       
    <div  class="form-group">
    <label style="text-align:left">Seller Name<span class="required">*</span></label>
     <?php echo CHtml::textField('ImportsMetaform[seller_name]','',array("class"=>"form-control")); ?>
    </div>

     <div  class="form-group">
    <label style="text-align:left">Seller Email<span class="required">*</span></label>
     <?php echo CHtml::textField('ImportsMetaform[seller_email]','',array("class"=>"form-control")); ?>
    </div>

     <div  class="form-group">
    <label style="text-align:left">Seller Mobile<span class="required">*</span></label>
     <?php echo CHtml::textField('ImportsMetaform[seller_mobile]','',array("class"=>"form-control")); ?>
    </div>

    <div  class="form-group">
    <label style="text-align:left">Product detail<span class="required">*</span></label>
     <?php echo CHtml::textArea('ImportsMetaform[product_detail]','',array('cols'=>60, 'rows' => 3,"class"=>"form-control")); ?>
     Please provide quantity, dimension and weight
    </div>

    <div  class="form-group">
    <label style="text-align:left">Total Value (AUD)<span class="required">*</span></label>
     <?php echo CHtml::numberField('ImportsMetaform[total_value]','',array("class"=>"form-control")); ?>
    </div>

    <div  class="form-group">
    <label style="text-align:left">FBA PO Number</label> 
    <?php echo CHtml::textField('ImportsMetaform[fba_po_number]','',array("class"=>"form-control")); ?>
    </div>

    <div  class="form-group">
    <label style="text-align:left">Shipment ID</label>
     <?php echo CHtml::textField('ImportsMetaform[fba_shipment_id]','',array("class"=>"form-control")); ?>
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
          if($('#ImportsMetaform_seller_name').val()=='')
          {
            alert('empty seller name');
            return false;
          }else if($('#ImportsMetaform_seller_email').val()=='')
          {
            alert('empty seller email');
             return false;
          }else if($('#ImportsMetaform_seller_mobile').val()=='')
          {
            alert('empty seller mobile');
             return false;
          }else if($('#ImportsMetaform_product_detail').val()=='')
          {
            alert('empty product detail');
             return false;
          }else if($('#ImportsMetaform_total_value').val()=='')
          {
            alert('empty total value');
             return false;
          }
            event.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute('<?=RecaptchaAPI::RECAPTCHA_V3_SITE_KEY?>', {action: 'fbaRemoval'}).then(function(token) {
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


</script>