<?php

?>
<img src="/images/tla_logo.png" alt="TLA" style="width :200px ; float:right"  />
<h1>Unsubscribe</h1>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'cs_qs_form',
        'action'=>Yii::app()->createUrl($this->route),
        'htmlOptions'=>["class"=>'ifrm-form'],
        'method'=>'post',
    )); ?>
       
    <br>

    <label style="text-align:left">Email Address<span class="required">*</span></label>
     <?php echo CHtml::textField('email_address','',array("class"=>"form-control")); ?>
    </div>
    <br>
    <br>
    

        <div class="form-group">
                 <?php echo CHtml::submitButton('submit',array("class"=>"form-control update","style"=>"width:250px;")); ?>
        </div>



    <?php $this->endWidget(); ?>
</div>