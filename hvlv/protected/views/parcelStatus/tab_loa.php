 <?php
 $companyName = "Top Logistics";
if($model->isTLA())
{
  $companyName = Org::IM_COMPANY_NAME;
}

 ?>        
<p>Note: you can print the LOA,sign,Scan and upload it.Or just use this LOA feature.</p>
<p>After you successfully submit this Loa form, you could find a pdf file on the Upload Documents Tab</p>
<p style="color: red;"><b>Once You sign This LOA Or Upload Scanned LOA, it means that you accept the price on the Commercial Invoice(attached on the email),which will be used for custom declaration.</b></p>
  <div class="row">
      <style>
          .bold_title{
              font-weight: bold;
          }
          .with_line{
              font-weight: bold;
              border-bottom: 1px solid black;
              display: inline-block;
              min-width: 200px;
           }
           .company{
               font-weight: bold;
               color:blue;
           }
      </style>
                  <div class="rol col-md-12 col-md-12">
                      <div class="form-group">
                          <p>
                               <center><h3>CLIENT’S LETTER HEAD</h3></center>
                          <div style="width: 100%;border-bottom: 2px solid black"></div>
                          <center><h2>AUTHORITY LETTER</h2></center>
                      </p>
                      <p>We, (<span class="bold_title">COMPANY NAME: </span><span class="with_line the_company_field" ></span>), hereby authorize <span class="company"><?=$companyName?>,</span> their subcontract brokers or nominees, 
                           to act on our behalf, in the customs clearance of air and sea freight shipments, pursuant to the requirements of Section 181(1) of the Customs Act 1901, as amended.
                       </p>
                       <p>
                        We further authorize <?=$companyName?>, to make customs and tax declarations on our behalf and to quote our Australian Business Number 
                        (<span style="font-weight: bold">ABN NUMBER: </span><span id="the_abn_field" class="with_line"></span>) (Leave blank for overseas or individual consignee) on all goods, unless otherwise indicated.
                       </p>
                       <p>
                           It is also agreed that <span class="company"><?=$companyName?>,</span> their sub contract brokers or nominees are authorized to act as a principal for (<span class="bold_title">COMPANY NAME:  </span><span class="with_line the_company_field" ></span>)  in accordance with Section 153-50 
                        of A New Tax System (Goods and Services Tax) Act 1999,as amended, and to make supplies or acquisitions to or from third parties in respect of the supply of services relating to the transportation,
                        customs clearance and delivery of goods from an overseas supplier.
                       </p>
                       <p>
                        In consideration of their acting as our customs brokers, we hereby indemnify them against any claims or demands made against them or arising from any customs or tax declarations made by them on our behalf.
                       </p>
                       <p>
                           To expedite clearance and delivery of our shipments, please advise <span class="company"><?=$companyName?>,</span> immediately after arrival of our shipments, and release any documents to them, as required.
                       </p>
                       <p>
                          We, (<span class="bold_title">COMPANY NAME:  </span><span class="with_line the_company_field" ></span>), agree to pay to <?=$companyName?> for all Duty, GST, Custom Clearance, Service and Storage Charges. <?=$companyName?> reserves rights to hold goods if any of the fees above are not fully paid.
                       </p>
                       <p>
                       This authority cancels and supersedes all previous authorities, unless specifically indicated.
                       </p>
                       <br/>
                       <p>
                        Yours faithfully.
                       </p>
                      </div> 
                  </div>
              </div>
          <div class="row">
                  <div class="rol col-md-6 col-sm-6">
                      <div class="form-group">
                          <?php echo CHtml::label('Company', 'company_name'); ?>
                          <?php echo CHtml::textField('company_name', '', array('class' => 'form-control',)) ?>
                      </div>
                         <div class="form-group">
                          <?php echo CHtml::label('Address', 'company_address'); ?>
                          <?php echo CHtml::textField('company_address', '', array('class' => 'form-control',)) ?>
                      </div>
                      <div class="form-group">
                          <?php echo CHtml::label('Telephone', 'tel_number'); ?>
                          <?php echo CHtml::textField('tel_number', '', array('class' => 'form-control',)) ?>
                      </div>
                        <div class="form-group">
                          <?php echo CHtml::label('Email', 'loa_email'); ?>
                          <?php echo CHtml::textField('loa_email', '', array('class' => 'form-control',)) ?>
                      </div>
                       <div class="form-group">
                          <?php echo CHtml::label('ABN', 'abn_number'); ?>
                          <?php echo CHtml::textField('abn_number', '', array('class' => 'form-control',)) ?>
                      </div>
                    </div>
                 <div class="rol col-md-6 col-sm-6" >
                       <div class="form-group">
                          <?php echo CHtml::label('Your Name', 'loa_client_name'); ?>
                          <?php echo CHtml::textField('loa_client_name', '', array('class' => 'form-control',)) ?>
                      </div>
                      <div class="form-group">
                          <?php echo CHtml::label('Postion', 'loa_position'); ?>
                          <?php echo CHtml::textField('loa_position', '', array('class' => 'form-control','placeholder'=>'Manager' )) ?>
                      </div>
                      <div class="form-group">
                            <?php echo CHtml::label('Signature', 'loa_signature'); ?>
                          <canvas style="border: 1px solid black; background-color:beige;"></canvas>
                          <?php echo CHtml::hiddenField('loa_signature'); ?>
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
                $('#abn_number').on('change input',function(){
                    $('#the_abn_field').html($(this).val());
                });
                $('#company_name').on('change input',function(){
                    $('.the_company_field').html($(this).val());
                });
                
              $('input.submit_sig').on("click", function (event) {
		var valid = false;
		if(signaturePad.isEmpty()){
			alert("Please provide signature first.");
                        return false;
		// }else if($('#company_name').val() == ''){
		// 	alert("Please provide the correct company Name");
  //                       return false;
		}else if($('#company_address').val() == ''){
			alert("Please provide the correct address");
                        return false;
		}else if($('#tel_number').val() == ''){
			alert("Please provide the correct telphone");
                        return false;
		}else if($('#loa_client_name').val() == ''){
			alert("Please provide Your correct Name.");
                        return false;
		}else if($('#loa_email').val() == ''){
			alert("Please provide Your correct Email Address.");
                        return false;
		} if($('#loa_position').val() == ''){
			alert("Please provide Your postion in the Company.");
                        return false;
		}else {
			$('#loa_signature').val(signaturePad.toDataURL());
                        var loa_signature=$('#loa_signature').val();
                        var company_name=$('#company_name').val();
                        var company_address=$('#company_address').val();
                        var tel_number=$('#tel_number').val();
                        var abn_number=$('#abn_number').val();
                        var loa_client_name=$('#loa_client_name').val();
                        var loa_email=$('#loa_email').val();
                        var loa_postion=$('#loa_position').val();
                        var data={loa_signature:loa_signature,company_name:company_name, loa_email:loa_email, loa_position:loa_postion,
                            company_address:company_address,abn_number:abn_number,tel_number:tel_number,loa_client_name:loa_client_name};
                        $.ajax({
                            url:'<?=$this->createUrl("parcelStatus/loa", ["id" => $model->id]);?>',
                            method: 'POST',
                            data:data,
                            success:function(res){
                                      if(res == 'done'){
                                      alert('Done', 5000);
                                      $('input[name="yt1"]').hide();
                                      }else{
                                      alert('falied');
                                      }
                                    $('body').trigger('reload_cus_process_grid');
                                  }
                                 });
			valid = true;
		              }
		return valid;
	     });
             });
</script>