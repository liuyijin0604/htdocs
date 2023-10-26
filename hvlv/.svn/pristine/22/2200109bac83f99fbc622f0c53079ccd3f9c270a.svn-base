<div style="float: right"> 
<a id="copy_address" href="#"  ><span class="icon"></span>Copy Url</a>
 <input type="hidden" id="address_tocopy" value='' />
</div>
<h3>客人提交的理赔信息</h3>
<p>必须填写所需要的信息(*)才能转到财务去。当客人选择credit note 形式时，银行信息可以不用填。</p>
<div class="form">
 <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'compensation-detail-form'.$_GET['tabid'],
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));
 $comp=$model->crm_comp;
 ?> 
<div class="row rowcol">
             <?php echo CHtml::label('填写信息表','flag'); ?>
             <?php  echo CHtml::radioButtonList("CrmComp[flag]", ($comp->flag&1)>0?1:0, CrmComp::$flags,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
</div>
<div class="row">
             <?php echo CHtml::label('支付方式','payment_method'); ?>
             <?php  echo $form->radioButtonList($comp, @'mdata[payment_method]', array(0=>'Others',1=>'Credit Note'),array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
</div>
    <div class="row rowcol rowleft">
        <?php echo CHtml::label('Freight 运费:<span class="required">*</span>','freight_fee');?>
        <?php echo $form->numberField($comp,'freight_fee',array('class'=>'number','readonly'=>'true'));?>
    </div>
     <div class="row rowcol">
        <?php echo CHtml::label('good declare value  物品费用:<span class="required">*</span>','freight_fee');?>
        <?php echo $form->numberField($comp,'loss_fee',array('class'=>'number','readonly'=>'true'));?>
    </div>
      <div class="row rowcol ">
        <?php echo CHtml::label('Claim Amount 索赔额: <span class="required">*</span>','claim_amount');?>
        <?php echo  $form->numberField($comp,'claim_amount',array('class'=>'number','readonly'=>'true'));?>
    </div>

    <div class="row rowcol rowleft">
        <?php echo CHtml::label('Bank Name 银行名: <span class="required">*</span>','bank_name');?>
        <?php echo $form->textField($comp,'bank_name',array('size'=>50,'readonly'=>'true'));?>
    </div>
      <div class="row rowcol rowleft">
        <?php echo CHtml::label('BSB: ','bsb');?>
        <?php echo $form->textField($comp,'bsb',array('readonly'=>'true'));?>
    </div>
     <div class="row rowcol ">
        <?php echo CHtml::label('Account Name 账号名:<span class="required">*</span>','account_name');?>
        <?php echo $form->textField($comp,'account_name',array('readonly'=>'true'));?>
    </div>
    <div class="row rowcol ">
        <?php echo CHtml::label('Bank Account no. 银行账号:<span class="required">*</span>','account_number');?>
        <?php echo $form->textField($comp,'account_number',array('readonly'=>'true'));?>
    </div>
      <div class="row rowcol rowleft">
        <?php echo CHtml::label('customer Note: 客人提交的NOTE','client_note');?>
        <?php echo $form->textArea($comp,@'mdata[client_note]',array('rows'=>5,'cols'=>60,'readonly'=>'true'));?>
    </div>
   <div class="row rowcol rowleft">
    <h3>客服需填的信息</h3>
   </div>
   <div class="row rowcol rowleft ">
        <?php echo CHtml::label('Port 口岸:(如果是口岸责任，请选择口岸)','port');?>
        <?php echo  $form->dropDownList($comp,'port', ExChannel::getPocs(true),array('prompt'=>'Choose One'));?>
    </div>
   <div class="row rowcol ">
        <?php echo CHtml::label('口岸哪个环节出错','port_claim_area');?>
        <?php echo  $form->dropDownList($comp,@'mdata[port_claim_area]', ExCrm::$comp_port_response,array('prompt'=>'All'));?>
    </div>
     <div class="row rowcol ">
        <?php echo CHtml::label('口岸理赔类型','port_claim_reason');?>
        <?php echo  $form->dropDownList($comp,@'mdata[port_claim_reason]', ExCrm::$comp_port_type,array('prompt'=>'All'));?>
    </div>
      <div class="row rowcol rowleft">
        <?php echo CHtml::label('币种<span class="required">*</span>','approve_currency');?>
        <?php echo  $form->dropDownList($comp,@'mdata[approve_currency]', array(1 => 'AUD',3 => 'RMB'),array('prompt'=>'Choose One'));?>
    </div>
    <div class="row rowcol ">
        <?php echo CHtml::label('Approved ammount 批准索赔额: <span class="required">*</span>','approve_ammount');?>
        <?php echo  $form->numberField($comp,'approve_ammount',array('step'=>"0.01"));?>
    </div>
    <div class="row rowcol rowleft">
        <?php echo CHtml::label('Claim Reason 理赔原因<span class="required">*</span>','claim_reason');?>
        <?php echo $form->textArea($comp,@'mdata[claim_reason]',array('rows'=>2,'cols'=>60));?>
    </div>
     <div class="row rowcol rowleft">
        <?php echo CHtml::label('核定情况<span class="required">*</span>','confirm_reason');?>
        <?php echo $form->textArea($comp,@'mdata[confirm_reason]',array('rows'=>2,'cols'=>60));?>
    </div>
    <div class="row button">
        <?php echo CHtml::submitButton('Save');?>
    </div>
    <?php $this->endWidget(); ?>
    
</div>
<script>
    $(function(){
       var tab=$("#<?=$_GET['tabid']?>");
       var panel=tab.data('panel');
       
      $('input[name="CrmComp[mdata][payment_method]"]:not(:checked)').attr('disabled', true);
      $("#copy_address",panel).on('click',function(e){
       var type=$('#link_type',panel).val();
        e.preventDefault();
        $.ajax({
            url: '<?=$this->createUrl("exCrm/copyLink",["id"=>$model->id]);?>',
            async:false,
            success:function(msg){
                if(msg){
                  copyToClipboard(msg);   
                  myApp.notice('Copy Successfully',500);
                   }
             }
        });
        });
         function copyToClipboard(msg) {
         var temp = $("<input>",panel);
             $("body").append(temp);
             temp.val(msg);
             temp.select();
             document.execCommand("copy");
             temp.remove();
       }
       
       $('#compensation-detail-form<?=$_GET['tabid']?>',panel).on('submit',function(){
          if($('#CrmComp_port',panel).val()){
              if(!$('#CrmComp_mdata_port_claim_reason',panel).val()){
                   myApp.alert('请选择口岸理赔类型！');
                  return false;
              }  
              if(!$('#CrmComp_mdata_port_claim_area',panel).val()){
                  myApp.alert('请选择哪个环节出问题了!');
                  return false;
            }
       }
   });
   });
</script>


