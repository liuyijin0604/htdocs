<?php

?>
<img src="/images/tla_logo.png" alt="TLA" style="width :200px ; float:right"  />
<h1>Removal of inventory from Amazon Fulfillment Centres in Australia</h1>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'cs_qs_form',
        'action'=>Yii::app()->createUrl($this->route),
        'htmlOptions'=>["class"=>'ifrm-form'],
        'method'=>'post',
    )); ?>
       
        <br>
<p>TLA provides a service to remove FBA inventory from Amazon Fulfilment Centres and return it to sellers located Overseas.</p>
<p>Please fill out the following information. We will send you a Return Authorisation (RA No), please use this RA no. in your FBA Removal page as reference no.</p>
<p>TLA provides this service independently from Amazon and is not affiliated or associated in any way with Amazon.</p>
<div class="ufo-fieldtype-4 ufo-customform-row ufo-row-3341" style="margin-top:10px;">  

    <div  class="form-group">
    <label style="text-align:left">Seller Email<span class="required">*</span></label>
     <?php echo CHtml::textField('ImportsMetaform[seller_email]','',array("class"=>"form-control")); ?>
    </div>
    
    <div  class="form-group">
    <label style="text-align:left">Seller Name<span class="required">*</span></label>
     <?php echo CHtml::textField('ImportsMetaform[seller_name]','',array("class"=>"form-control")); ?>
    </div>

     <div  class="form-group">
    <label style="text-align:left">Seller Mobile</label>
     <?php echo CHtml::textField('ImportsMetaform[seller_mobile]','',array("class"=>"form-control")); ?>
    </div>
    
    <div  class="form-group">
    <label style="text-align:left">Seller Address<span class="required">*</span></label>
     <?php echo CHtml::textField('ImportsMetaform[seller_address]','',array("class"=>"form-control")); ?>
    </div>
    
    <div  class="form-group">
    <label style="text-align:left">Seller Company<span class="required">*</span></label>
     <?php echo CHtml::textField('ImportsMetaform[seller_company]','',array("class"=>"form-control")); ?>
    </div>
    
    <div  class="form-group">
    <label style="text-align:left">Product Detail<span class="required">*</span></label>
     <?php echo CHtml::textArea('ImportsMetaform[product_detail]','',array('cols'=>60, 'rows' => 3,"class"=>"form-control")); ?>
    </div>
    
    <div  class="form-group">
    <label style="text-align:left">Product Quantity</label>
     <?php echo CHtml::numberField('ImportsMetaform[product_quantity]','',array("class"=>"form-control")); ?>
     Please provide approximate quantity
    </div>
    
    <div  class="form-group">
    <label style="text-align:left">Product Dimension</label>
     <?php echo CHtml::numberField('ImportsMetaform[product_dimension]','',array("class"=>"form-control")); ?>
     Please provide approximate dimension (CBM)
    </div>
    
    <div  class="form-group">
    <label style="text-align:left">Product Weight<span class="required">*</span></label>
     <?php echo CHtml::numberField('ImportsMetaform[product_weight]','',array("class"=>"form-control")); ?>
     Please provide approximate weight (KG)
    </div>

    <div  class="form-group">
    <label style="text-align:left">Total Value (AUD)</label>
     <?php echo CHtml::numberField('ImportsMetaform[total_value]','',array("class"=>"form-control")); ?>
    </div>

    <div  class="form-group">
    <label style="text-align:left">Amazon Removal ID<span class="required">*</span></label> 
    <?php echo CHtml::textField('ImportsMetaform[amazon_removal_id]','',array("class"=>"form-control")); ?>
    </div>
    
    <div  class="form-group">
    <label style="text-align:left">Removal Type<span class="required">*</span></label>
     <?php echo CHtml::dropDownList('ImportsMetaform[removal_type]','storage',['storage'=>'storage','return'=>'return','disposal'=>'disposal',],["class"=>"form-control"]); ?>
    </div>
    

        <div class="form-group">
                 <?php echo CHtml::submitButton('submit',array("class"=>"form-control update","style"=>"width:250px;")); ?>
        </div>
        <div class="form-group">
          <input type="hidden" name="step" value="1">  
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
          }
          // else if($('#ImportsMetaform_seller_mobile').val()=='')
          // {
          //   alert('empty seller mobile');
          //    return false;
          // }
          else if($('#ImportsMetaform_seller_address').val()=='')
          {
            alert('empty seller address');
             return false;
          }
          else if($('#ImportsMetaform_seller_company').val()=='')
          {
            alert('empty seller company');
             return false;
          }
          else if($('#ImportsMetaform_product_detail').val()=='')
          {
            alert('empty product detail');
             return false;
          }
          else if($('#ImportsMetaform_product_weight').val()=='')
          {
            alert('empty product weight');
             return false;
          }
          else if($('#ImportsMetaform_amazon_removal_id').val()=='')
          {
            alert('empty removal id');
             return false;
          }
          else if($('#ImportsMetaform_removal_type').val()=='')
          {
            alert('empty total value');
             return false;
          }
    });
});


</script>