<h3>Message To Customer</h3>
<div class="form">
<?php 
$form=$this->beginWidget('CActiveForm',array(
    'id'=>'clear_log_form',
    'enableAjaxValidation'=>false,
     
));?>
<div class="row">
   
    <?php echo CHtml::textArea('msg_cust',@$model->mdata['msg_cust'],array('rows'=>5, 'cols' => 60)); ?>
 </div>

<div class="button">
    <?php echo CHtml::submitButton('submit')?>
</div>

<?php $this->endWidget();?>
</div></div>
