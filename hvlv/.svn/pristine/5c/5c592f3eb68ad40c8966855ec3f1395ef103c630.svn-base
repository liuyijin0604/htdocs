<div id="<?=$_GET['tabid']?>_sms_manage">
<h3>Send SMS</h3>
<div class="form">
<?php 
$form=$this->beginWidget('CActiveForm',array(
    'id'=>'ex_crm_sms_form',
    'enableAjaxValidation'=>false,
     
));
?>
    <div class="row rowcol rowleft">
    <?php echo CHtml::label('country','country'); ?>
    <?php echo CHtml::dropDownList('country','CN', Array('AUS'=>'Australia','CN'=>"China"));//'CN'=>"China", ?>  
    </div>
    <div class="row rowcol">
   <?php echo $form->labelEx($model,'telephone'); ?>
   <?php echo $form->textField($model,'telephone',array('size'=>20)); ?>
    </div>
   <div class="row">
    <?php echo CHtml::label('短信模板','cn_sms_temp'); ?>
    <?php echo CHtml::dropDownList('cn_sms_temp','', ExCrm::$cn_sms_tp,array('prompt'=>'SELECT'));//'CN'=>"China", ?>  
    </div>
 <div class="row">
     <div>153/<span id='character_no'></span></div>
   <?php echo CHtml::textArea('msg_to_customer','',array('rows'=>5, 'cols' => 60, "maxlength"=>153)); ?>
 </div>

<div class="button">
    <?php echo CHtml::submitButton('Send')?>
</div>

<?php $this->endWidget();?>
</div>
</div>
<script>
    $(function(){
        var win = $('#<?=$_GET["tabid"];?>_sms_manage');
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');
        var textarea= $("textarea[name=msg_to_customer]",win);
        $('#msg_to_customer',win).on('keydown',function(){
            var numbers=$(this).val().length;
            $('#character_no').html(numbers);
        });
        
        if($('#country',win).val()=='CN'){
             textarea.attr('readonly',true);
            
        }
        
        $('#country',win).on('change',function(){
           
            if($(this).val()=='CN'){
              textarea.val('');
              textarea.attr('readonly',true);
              $('#character_no').html(0);
            }else if($(this).val()=='AUS'){
                   textarea.attr('readonly',false);
                
        }
        });
        
        $('#cn_sms_temp',win).on('change',function(){
               var index=$(this).val();
                $.get('<?=Yii::app()->createURL('exCrm/smsTp',array("id"=>$model->id))?>'+'?index='+index,function(r){
                          textarea.val(r);
                     });
               
        });
        
        
        
        $('form#ex_crm_sms_form').on({
            success:function(r){
                tab.trigger('reload_ticket_note');
            },
        });


           
    });
</script>
