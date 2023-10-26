<?php if(isset($model->crm_comp)&&(($model->crm_comp->flag&1)>0)):?> 
<h5><p>Your already submit the Form! if you have further question, please contact our customer service!</p></h5>
<?php else:?>
<p style="color: red;"><b>Please ensure the information supplied is correct,especially the bank account details.Otherwise,we don't pay for the extra loss.(fees in AUS dollars)</b></p>
<p style="color: red;"><b>请确保你填写的信息正确,特别是银行信息。如提供错误信息而造成损失，我们将不进行额外赔付。费用单位为澳元</b></p>
  <div class="row">
                  <div class="rol col-md-12 col-md-12">
                      <div class="form-group">
                          <h2 style="margin: auto; width:300px;">Claim Form 理赔表</h2>
                      </div> 
                  </div>
              </div>
          <div class="row">
                  <div class="rol col-md-6 col-sm-6">
                      <div class="form-group">
                          <?php echo CHtml::label('Your Name,收件人姓名(*)', 'client_name'); ?>
                          <?php echo CHtml::textField('client_name', '', array('class' => 'form-control',)) ?>
                      </div>
                         <div class="form-group">
                          <?php echo CHtml::label('your mobile phone,你的手机号码(*)', 'client_telphone'); ?>
                          <?php echo CHtml::textField('client_telphone', '', array('class' => 'form-control',)) ?>
                      </div>
                      <div class="form-group">
                          <?php echo CHtml::label('Your Email,你的电子邮箱(*)', 'client_email'); ?>
                          <?php echo CHtml::textField('client_email', '', array('class' => 'form-control',)) ?>
                      </div>
                        <div class="form-group">
                          <?php echo CHtml::label('Freight 运费(*)', 'freight_fee'); ?>
                          <?php echo CHtml::textField('freight_fee', '', array('class' => 'form-control',)) ?>
                      </div>
                      <div class="form-group">
                          <?php echo CHtml::label('the value of claimed goods,损失的物品费用(*)', 'loss_fee'); ?>
                          <?php echo CHtml::textField('loss_fee', '', array('class' => 'form-control',)) ?>
                      </div>
                       <div class="form-group">
                          <?php echo CHtml::label('Claim Amount 索赔额(*)', 'claim_amount'); ?>
                          <?php echo CHtml::textField('claim_amount', '', array('class' => 'form-control',)) ?>
                      </div>
                        <div class="form-group">
                          <?php echo CHtml::label('Note(请详细罗列索赔物品信息，有助于我们给您索赔）', 'claim_note'); ?>
                          <?php echo CHtml::textArea('claim_note', '', array('class' => 'form-control','rows'=>6)) ?>
                      </div>
                    </div>
                 <div class="rol col-md-6 col-sm-6" >
                      <div class="form-group">
                          <?php echo CHtml::label('Payment Method(*)(一般选择others, 当选择credit Note,钱将以credit note 形式支付给代理。选择credit Note 无需填写银行信息。', 'payment_method'); ?>
                          <br/>
                          <?php echo CHtml::radioButtonList('payment_method',0,array(0=>'Others',1=>'Credit Note'),
                                  array('labelOptions'=>array('style'=>'display: inline; float: none; font-weight: bold;'),'separator' => '&nbsp;&nbsp;&nbsp;&nbsp; ')) ?>
                      </div>
                       <div class="form-group">
                          <?php echo CHtml::label('Bank Name 银行名字（如用支付宝，请填写《支付宝》）(*)', 'bank_name'); ?>
                          <?php echo CHtml::textField('bank_name', '', array('class' => 'form-control',)) ?>
                      </div>
                      <div class="form-group">
                          <?php echo CHtml::label('Account Name 户名（支付宝的请填写用户名字）(*)', 'account_name'); ?>
                          <?php echo CHtml::textField('account_name', '', array('class' => 'form-control','placeholder'=>'Mr Zhang San' )) ?>
                      </div>
                      <div class="form-group">
                          <?php echo CHtml::label('BSB （澳洲银行账户请提供bsb)', 'account_bsb'); ?>
                          <?php echo CHtml::textField('account_bsb', '', array('class' => 'form-control')) ?>
                      </div>
                        <div class="form-group">
                          <?php echo CHtml::label('Account No 账号（支付宝的请填写支付宝账号）(*)', 'account_number'); ?>
                          <?php echo CHtml::textField('account_number', '', array('class' => 'form-control','placeholder'=>'5531-1149-3321-5587' )) ?>
                      </div>
                     <p><b>I declare that all the information I support is correct.</b></p>
                     <p><b>我确认我所提供的信息是正确的.</b></p>
                      <div class="form-group">
                            <?php echo CHtml::label('Signature', 'loa_signature'); ?>
                          <canvas style="border: 1px solid black; background-color:beige;"></canvas>
                          <?php echo CHtml::hiddenField('claim_signature'); ?>
                      </div>
                      <div class="form-group button">
                          <?php echo CHtml::submitButton('Clear', array('class' => 'btn btn-default clear_btn')); ?>
                          <?php echo CHtml::submitButton('Submit', array('class' => 'btn btn-default submit_sig')); ?>
                      </div>
                  </div>
  </div>

<script>
    $(function(){
               var signaturePad = new SignaturePad(document.querySelector("canvas"));
                $('input.clear_btn').on("click", function (event) {
		signaturePad.clear();
	        });
           $('input.submit_sig').on("click", function (event) {
		var valid = false;
		if(signaturePad.isEmpty()){
			alert("Please provide signature first.");
                        return false;
		}else if($('#client_name').val() == ''){
			alert("Please provide the correct Name");
                        return false;
		}else if($('#client_telphone').val() == ''){
			alert("Please provide the correct mobile number");
                        return false;
		}else if($('#client_email').val() == ''){
			alert("Please provide the correct email");
                        return false;
		}else if($('#freight_fee').val() == ''){
			alert("Please provide the correct freight fee");
                        return false;
		}else if($('#loss_fee').val() == ''){
			alert("Please provide the value of claimed goods.");
                        return false;
		}else if($("input[name=payment_method]:checked").val()==0){
                 if($('#claim_amount').val() == ''){
			alert("Please provide The claim amount.");
                        return false;
		}else if($('#bank_name').val() == ''){
			alert("Please provide the bank Name.");
                        return false;
		}else if($('#account_name').val() == ''){
			alert("Please provide the account Name.");
                        return false;
		}else if($('#account_number').val() == ''){
			alert("Please provide the account number.");
                        return false;
		 } 
               } 
		 $('#claim_signature').val(signaturePad.toDataURL());
                        var claim_signature=$('#claim_signature').val();
                        var client_name=$('#client_name').val();
                        var client_telphone=$('#client_telphone').val();
                        var client_email=$('#client_email').val();
                        var freight_fee=$('#freight_fee').val();
                        var loss_fee=$('#loss_fee').val();
                        var claim_amount=$('#claim_amount').val();
                        var bank_name=$('#bank_name').val();
                        var bank_bsb=$('#account_bsb').val();
                        var account_name=$('#account_name').val();
                        var account_number=$('#account_number').val();
                        var claim_note=$('#claim_note').val();
                        var payment_method=$("input[name=payment_method]:checked").val();
                        
                        var data={claim_signature:claim_signature,client_name:client_name, client_telphone:client_telphone, client_email:client_email,claim_note:claim_note,
                            freight_fee:freight_fee,loss_fee:loss_fee,claim_amount:claim_amount,bank_name:bank_name,bank_bsb:bank_bsb
                            ,account_name:account_name,account_number:account_number,payment_method:payment_method};
                        $.ajax({
                            url:'<?=$this->createUrl("claim/claimForm", ["id" => $model->id]);?>',
                            method: 'POST',
                            data:data,
                            success:function(res){
                                      if(res == 'done'){
                                      alert('Done', 5000);
                                      $('input[name="yt1"]').hide();
                                      location.reload();
                                      }else{
                                      alert('falied');
                                      }
                                  }
                                 });
			       valid = true;
		                 
		return valid;
	     });
             });
</script>
<?php endif;?>