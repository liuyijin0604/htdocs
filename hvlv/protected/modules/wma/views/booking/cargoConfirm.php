<?php

$objShipment = $model->shipment;
$strHbn = $objShipment->hbn;
$numWeight = $objShipment->weight;
$numPkg = $objShipment->pkg;
$numCBM = $model->getTotalCBM();

// $strFullAddress = $objShipment->cnee->fullAddress();
$strFullAddressRaw = $objShipment->cnee->fullAddress();
$listFilter = [
	'1','2','3','4','5','6','7','8','9','0',
	'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
	'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
	' ',',','，','/','-',
];
$strFullAddress='';
for($i=0;$i < strlen($strFullAddressRaw);$i++){
	if(in_array($strFullAddressRaw[$i],$listFilter)){
		$strFullAddress.=$strFullAddressRaw[$i];
	}
}

// $strName = $objShipment->cnee->name;
$strName = str_replace('"',' ',$objShipment->cnee->name);
$strName = str_replace("'",' ',$strName);

$strTel = $objShipment->cnee->tel;
$strEmail = $objShipment->cnee->email;

$strContactNumber = '02 90668206';
if($objShipment->ddpt_id == Org::PCAE_DEPARTMENT_MELBOURNE){
	$strContactNumber = '03 91175890';
}

$strDeliveryTime = 'Available delivery dates will be shown after confirm button.';
$strDivDeliveryOrOther = '#divDelivery';
if($model->dpt_id != Org::TLA_DEPARTMENT_SYDNEY){
	$strDeliveryTime = 'Your shipment will be delivered in the next 5 business days.';
	$strDivDeliveryOrOther = '#divOther';
}

$isOverWeight = ($numWeight/$numPkg > 30 || $numWeight>250);

$strUnloadingH41='Your shipper has ordered standard curb side drop off service,';
$strUnloadingH42='Do you need delivery into ground floor of your house/garage ? (extra fee applies)';
$strUnloadingLine1='(No) Please drop it off at street level';
$strUnloadingLine2='(Yes) We need additional unload service';
$strOption1 = 'Unload to house/inside garage: $'.round($numUnloadingFee*1.1,2).' Inc GST';
$strOption1After = '*No climbing stairs only support elevator<br/>';
$strProvidDropOff = 'Shipment will be drop off at street level, near curbside.<br/>';

if($isOverWeight){
	$strUnloadingH41='Your shipper had ordered standard delivery (without unloading service),';
	$strUnloadingH42='Do you need curb side drop off service? (extra fee applies)';
	$strUnloadingLine1='(No) We can unload ourself';
	// $strUnloadingLine2='(Yes) We need curb side drop off service';
	$strOption1 = 'Drop off at curb side: $'.round($numUnloadingFee*1.1,2).' Inc GST';
	$strOption1After = '';
	$strProvidDropOff ='';
}


?>

<style type="text/css">
	p{
		font-size: 16px;
		color:black;
	}
	.display_none {
        display: none;
    }
	.btn_next{
		color: #fff;
    	background-color: #007aff;
    	border: 1px solid #007aff;
		width: 200px;
		height: 40px;
		float: right;
	}
	.btn_prev{
		color: #007aff;
    	/* background-color: #007aff; */
    	border: 1px solid #007aff;
		width: 200px;
		height: 40px;
		float: right;
		margin-right: 10px;
	}
	.sb10{
		position:sticky;
		bottom: 10px;
	}
</style>




<div class="content-padded">
	<div class="form">
	<?php 


	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'cargo-confirm-form',
		'enableAjaxValidation' => false,
	)); ?>
		
		<div class="row">
			<div id="divChangeAddress" class="col-12" style="padding-left: 5px;">
				<?php echo CHtml::hiddenField('inputIsChangeAddress',false);?>
				<h3>Please confirm details before delivery:</h3>
				<br/>
				<br/>
				<h4>Your shipment Information</h4> 
				<p>Ref No.<?=$strHbn?></p>
				<div>
					Weight: <?=$numWeight?> kg<br/>
					Qty: <?=$numPkg?> pcs<br/>
					Volume: <?=$numCBM?> cbm<br/>
				</div>
				<br/>
				<p>Your Address: <?=$strFullAddress?></p>
				<p>Your Name: <?=$strName?></p>
				<p>Your Mobile: <?=$strTel?></p>
				<p>Your Email: <?=$strEmail?></p>
				<br/>
				<h4>Is the address correct?</h4>
				<?php 
					echo CHtml::RadioButtonList('change_address','no_change_address', ['no_change_address'=>'Yes','change_address'=>'No, I want to change (extra fee applies)'],['labelOptions'=>['class'=>'radio_label'],'separator'=>'<br/>'])
				?>
				<br/>
				<br/>
				<button type="button" onclick="funcChangeAddress()" class="btn_next">Next</button>
			</div>
			<div id="divNewAddress" class="col-12 display_none" style="padding-left: 5px;">
				<?php echo CHtml::hiddenField('inputIsNewAddress',false);?>
				<h3>Please confirm delivery details: Steps 1/5 </h3>
				<br/>
				<br/>
				<h4>New Address</h4>
				Unit / House No. 
				<?php echo CHtml::textField('address_line') ?>
				Street name:
				<?php echo CHtml::textField('address_street') ?>
				Suburb:
				<?php echo CHtml::textField('address_suburb') ?>
				Postcode:
				<?php echo CHtml::textField('address_postcode') ?>
				<br/>
				<br/>
				<button type="button" onclick="funcNewAddress()" class="btn_next">Next</button>
				<button type="button" onclick="funcNewAddressPrev()" class="btn_prev">Previous</button>
			</div>
			<div id="divShipment" class="col-12 display_none" style="padding-left: 5px;">
				<h3>Please confirm delivery details: Steps 2/5 </h3>
				<br/>
				<br/>
				<h4>Is Your Address</h4> 
				<?php echo CHtml::RadioButtonList('address_type','residential', ['residential'=>'Residential','business'=>'Business'],['labelOptions'=>['class'=>'radio_label'],'separator'=>'<br/>'])?>
				<br/>
				<br/>
				<button type="button" onclick="funcNextShipment()" class="btn_next">Next</button>
				<button type="button" onclick="funcNextShipmentPrev()" class="btn_prev">Previous</button>
			</div>
			<!-- divUnloadingResidentialWeight -->
			<div id="divUnloadingResidentialWeight" class="col-12 display_none" style="padding-left: 5px;">
				<h3>Please confirm delivery details: Steps 3/5 </h3>
				<?php echo CHtml::hiddenField('inputIsUnloadingResidentialWeight',false);?>
				<br/>
				<br/>
				<h4><?=$strUnloadingH41?></h4><h4><?=$strUnloadingH42?></h4>
				<?php
					echo CHtml::RadioButtonList('residential_unloading','self', ['self'=>$strUnloadingLine1,'unloading_service_overweight'=>$strUnloadingLine2],['labelOptions'=>array('class'=>'radio_label'),'separator'=>'<br/>']);
				?>
				<br/>
				<br/>
				<button type="button" onclick="funcNextUnloadingResidentialWeight()" class="btn_next">Next</button>
				<button type="button" onclick="funcNextUnloadingResidentialWeightPrev()" class="btn_prev">Previous</button>
			</div>
			<div id="divUnloadingOptions" class="col-12 display_none" style="padding-left: 5px;">
				<h3>Please confirm delivery details: Steps 4/5 </h3>
				<br/>
				<br/>
				<h4>Extra services (optional):</h4>
					<span><input id="cb_unloading_street" name='cb_unloading_street' type='checkbox' value='1'  /><?=$strOption1?></span><br/>
					<?=$strOption1After?>
   					<span><input id='cb_unloading_unpack' name='cb_unloading_unpack' type='checkbox' value='2'  />Help to unpack boxes: $<?=round($numUnpackFee*1.1,2)?> Inc GST</span><br/>
  					<span><input id='cb_unloading_rabbish' name='cb_unloading_rabbish' type='checkbox' value='3'  />Rubbish removal : $<?=round($numRabbishFee*1.1,2)?> Inc GST</span><br/>
				<br/>
				<br/>
				<button type="button" onclick="funcUnloadingOptions()" class="btn_next">Next</button>
				<button type="button" onclick="funcUnloadingOptionsPrev()" class="btn_prev">Previous</button>
			</div>

			<!-- divUnloadingResidential -->
			<div id="divUnloadingResidential" class="col-12 display_none" style="padding-left: 5px;">
				<h3>Please confirm delivery details: Steps 3/5 </h3>
				<?php echo CHtml::hiddenField('inputIsUnloadingResidential',false);?>
				<br/>
				<br/>
				<h4>ATL</h4>
				<p>If no one onsite on the day of delivery, I/We authorised to <span style="color:red;">leave at the address.</span></p>
				<p>(a photo will be taken if ATL is selected)</p>
				<?php echo CHtml::RadioButtonList('leave_at_address','yes', ['yes'=>'YES','no'=>'NO'],['labelOptions'=>['class'=>'radio_label'],'separator'=>'<br/>'])?>
				<br/>
				<br/>
				<button type="button" onclick="funcNextUnloadingResidential()" class="btn_next">Next</button>
				<button type="button" onclick="funcNextUnloadingResidentialPrev()" class="btn_prev">Previous</button>
			</div>
			<!-- divUnloadingBusiness -->
			<div id="divUnloadingBusiness" class="col-12 display_none" style="padding-left: 5px;">
				<h3>Please confirm delivery details: Steps 3/5 </h3>
				<?php echo CHtml::hiddenField('inputIsUnloadingBusiness',false);?>
				<br/>
				<br/>
				<h4><?=$strUnloadingH41?></h4><h4><?=$strUnloadingH42?></h4>
				<?php
					echo CHtml::RadioButtonList('business_unloading','fork', ['fork'=>'We have a forklift','self'=>$strUnloadingLine1,'hand_unloading_service'=>$strUnloadingLine2],['labelOptions'=>['class'=>'radio_label'],'separator'=>'<br/>']);
				?>
				<!-- <p>This service only unload goods to street level.</p> -->
				<br/>
				<button type="button" onclick="funcNextUnloadingBusiness()" class="btn_next">Next</button>
				<button type="button" onclick="funcNextUnloadingBusinessPrev()" class="btn_prev">Previous</button>
			</div>
			<div id="divDelivery" class="col-12 display_none" style="padding-left: 5px;">
				<h3>Please confirm delivery details: Steps 5/5 </h3>
				<br/>
				<br/>
				<h4>Please select delivery time:</h4>
				<!-- <div id="divDeliveryNote"></div>
				<div id="divDeliveryTime"></div> -->
				<?php
					$objCargoProcessJob = new CargoProcessJob;
					$listJob = $objCargoProcessJob->getAvailableJob($model->id);
					echo CHtml::RadioButtonList('current_delivery_time','',$listJob,['labelOptions'=>['class'=>'radio_label'],'separator'=>'<br/>']);
				?>

				<br/>
				<br/>
				<button type="button" onclick="funcNextDelivery()" class="btn_next">Next</button>
				<button type="button" onclick="funcNextDeliveryPrev()" class="btn_prev">Previous</button>
			</div>
			<div id="divOther" class="col-12 display_none" style="padding-left: 5px;">
				<h3>Please confirm delivery details: Steps 5/5 </h3>
				<br/>
				<br/>
				<h4>Special notes: (30 words max)</h4>
				<p><?php echo CHtml::textarea('other_inquery','',array('maxlength'=>100,'rows'=>3,'cols'=>60))?></p>
				<p>*Important Notices:</p>
				<p>Change of address, specific delivery time request, exceed 15mins wait time, re-delivery and special unloading request may incur extra charges.</p>
				<p>The delivery service may be refused if wrong or misleading information is provided.</p>
				<br/>
				<button type="button" onclick="funcNextOther()" class="btn_next">Next</button>
				<button type="button" onclick="funcNextOtherPrev()" class="btn_prev">Previous</button>
			</div>
			<!-- <div id="divRedelivery" class="col-12 display_none" style="padding-left: 5px;">
				<br/>
				<br/>
				<h4>Redelivery F</h4>
				<p>Please make sure someone onsite on the day of delivery.</p>
				<p>(Re-delivery fee applied.)</p>
				<button type="button" onclick="funcNextRedelivery()" class="btn_next">Next</button>
			</div> -->
			<div id="divSummary" class="col-12 display_none" style="padding-left: 5px;">
				<h3>DELIVERY CONFIRMATION:</h3>
				<br/>
				<br/>
				<div id="divSummaryDetails">
				</div>
				<br/>
				<button type="button" onclick="funcSubmitPrev()" class="btn_prev">Previous</button>
				<br/>
				<br/>
				<br/>
				<?php echo CHtml::hiddenField('summary','');?>
				<?php echo CHtml::hiddenField('caref',$model->getRef());?>
				<?php echo CHtml::button($this->t('confirm'), ['class' => 'save_btn btn btn-primary btn-block sb10','onclick'=>'funcSubmit()']); ?>

			</div>


		</div>

	<?php $this->endWidget(); ?>

	</div>
</div>




<script type="text/javascript">
	numPkg = <?=$numPkg?>;
	numWeight = <?=$numWeight?>;
	strFullAddress = "<?=$strFullAddress?>";

	isUnloadingResidentialWeight = false;
	isUnloadingResidential = false;
	isUnloadingBusiness = false;
	// isRedelivery = false;

	strSummaryChangeAddress = '';
	strSummaryNewAddress = '';
	strSummaryAddressType = '';
	strSummaryUnloading = '';
	strSummaryUnloadingOptions='';
	// strSummaryRedelivery = '';
	strSummaryDeliveryTime = '';

	strDivPrevShipment = '';
	strDivPrevUnloadingOptions = '';
	strDivPrevDelivery = '';
	strDivPrevOther = '';

	function funcChangeAddress(){
		$("#divChangeAddress").addClass("display_none");
		strChangeAddress = $("input[name='change_address']:checked").val();
		if(strChangeAddress == 'no_change_address'){
			strDivPrevShipment = 'divChangeAddress';
			$("#divShipment").removeClass("display_none");
		}
		else{ // change address
			$("#divNewAddress").removeClass("display_none");
		}
	}

	function funcNewAddress(){

		strLine = $("#address_line").val();
		strStreet = $("#address_street").val();
		strSuburb = $("#address_suburb").val();
		strPostcode = $("#address_postcode").val();
		strFullAddress = strLine+' '+strStreet+' '+strSuburb+' '+strPostcode;

		var listData = new FormData();
            listData.append("caref",$('#caref').val());
			listData.append("strSuburb",strSuburb); 
            listData.append("numPostcodeNew",strPostcode);

            htmlobj = $.ajax({
                type:"POST",
                url: "<?=$this->createUrl('booking/changeAddressFee');?>",
                data: listData,
                async: false,
                contentType: false,
                processData: false,
            });
            obj = JSON.parse(htmlobj.responseText);
            if(obj.isSuccess){
				numChangeAddressFee = Math.round(obj.numFee*110)/100;
				if (confirm('$'+numChangeAddressFee+' Fee Applies. Do you want to change address?')== true ){
					$("#inputIsChangeAddress").val(true);
					$("#divNewAddress").addClass("display_none");
					strDivPrevShipment = 'divNewAddress';
					$("#divShipment").removeClass("display_none");
				} else {
					$("#inputIsChangeAddress").val(false);
					$("#divNewAddress").addClass("display_none");
					$("#divChangeAddress").removeClass("display_none");
				}
            }
			else{
				alert(obj.strMessage);
			}
	}

	function funcNewAddressPrev(){
		$("#inputIsChangeAddress").val(false);
		$("#divNewAddress").addClass("display_none");
		$("#divChangeAddress").removeClass("display_none");
	}

	function funcNextShipment(){
		$("#divShipment").addClass("display_none");
		strAddressType = $("input[name='address_type']:checked").val();
		if(strAddressType == 'residential'){
			if(numWeight<200 &&numWeight/numPkg < 25 ){
				$("#divUnloadingResidential").removeClass("display_none");
				isUnloadingResidential =true;
				$("#inputIsUnloadingResidential").val(true);
			}
			else{
				$("#divUnloadingResidentialWeight").removeClass("display_none");
				isUnloadingResidentialWeight = true;
				$("#inputIsUnloadingResidentialWeight").val(true);
			}
			strSummaryAddressType = "<h4>Is Your Address</h4>Residental<br/><br/>";
		}
		else{//business
			$("#divUnloadingBusiness").removeClass("display_none");
			isUnloadingBusiness = true;
			$("#inputIsUnloadingBusiness").val(true);
			strSummaryAddressType = "<h4>Is Your Address</h4>Business<br/><br/>";
		}
	}

	function funcNextShipmentPrev(){
		$("#divShipment").addClass("display_none");
		$("#"+strDivPrevShipment).removeClass("display_none");
	}
	

	function funcNextUnloadingResidentialWeight(){
		$("#divUnloadingResidentialWeight").addClass("display_none");
		strDivPrevUnloadingOptions='divUnloadingResidentialWeight';
		if($("input[name='residential_unloading']:checked").val() == "self"){
			strDivPrevDelivery = "divUnloadingResidentialWeight";
			strDivPrevOther = "divUnloadingResidentialWeight";
			$("<?=$strDivDeliveryOrOther?>").removeClass("display_none");
			strSummaryUnloading = "<h4><?=$strUnloadingH41?></h4><h4><?=$strUnloadingH42?></h4><?=$strUnloadingLine1?><br/><br/>";
			strSummaryUnloadingOptions = "<h4>The Services We Provide</h4><?=$strDeliveryTime?> <br/>Email notification will be sent 24 hours before the delivery day.<br/><br/><h4>The Services We Do Not Provide</h4>Unloading service<br/>Help to unpack boxes<br/>Rubbish removal<br/><br/>";
		}
		else if($("input[name='residential_unloading']:checked").val() == "hand_unloading_service"){
			$("#divUnloadingOptions").removeClass("display_none");
			strSummaryUnloading = "<h4><?=$strUnloadingH41?></h4><h4><?=$strUnloadingH42?></h4><?=$strUnloadingLine2?><br/><br/>";
		}
		else if($("input[name='residential_unloading']:checked").val() == "unloading_service_overweight"){
			$("#divUnloadingOptions").removeClass("display_none");
			strSummaryUnloading = "<h4><?=$strUnloadingH41?></h4><h4><?=$strUnloadingH42?></h4><?=$strUnloadingLine2?><br/><br/>";
		}
	}

	function funcNextUnloadingResidentialWeightPrev(){
		// $("input[name='residential_unloading'][value='self']").attr("checked",true); 
		// $("input[name='residential_unloading'][value='hand_unloading_service']").removeAttr("checked")
		// $("input[name='residential_unloading'][value='unloading_service_overweight']").removeAttr("checked")
		$("#divUnloadingResidentialWeight").addClass("display_none");
		$("#divShipment").removeClass("display_none");
	}


	function funcUnloadingOptions (){
		$("#divUnloadingOptions").addClass("display_none");

		var strHtmlProvide = '';
		var strHtmlNoProvide = '';
		if($('#cb_unloading_street').is(':checked')){
			strHtmlProvide+= '<?=$strOption1?><br/><?=$strOption1After?>';
		}
		else{
			strHtmlNoProvide+= '<?=$strOption1?><br/><?=$strOption1After?>'
		}
		if($('#cb_unloading_unpack').is(':checked')){
			strHtmlProvide+= 'Help to unpack boxes: $<?=round($numUnpackFee*1.1,2)?> Inc GST<br/>';
		}
		else{
			strHtmlNoProvide+= 'Help to unpack boxes <br/>';
		}
		if($('#cb_unloading_rabbish').is(':checked')){
			strHtmlProvide+= 'Rubbish removal : $<?=round($numRabbishFee*1.1,2)?> Inc GST<br/>';
		}
		else{
			strHtmlNoProvide+= 'Rubbish removal<br/>';
		}

		$("#divServicesProvide").html(strHtmlProvide);
		$("#divServicesNoProvide").html(strHtmlNoProvide);

		strSummaryUnloadingOptions ='<h4>The Services We Provide</h4><?=$strProvidDropOff?><?=$strDeliveryTime?> <br/>Email notification will be sent 24 hours before the delivery day.<br/>';
		strSummaryUnloadingOptions += strHtmlProvide;
		strSummaryUnloadingOptions += '<br/><h4>The Services We Do Not Provide</h4>';
		strSummaryUnloadingOptions += strHtmlNoProvide;
		strSummaryUnloadingOptions +='<br/>';

		strDivPrevDelivery = "divUnloadingOptions";
		strDivPrevOther = "divUnloadingOptions";
		$("<?=$strDivDeliveryOrOther?>").removeClass("display_none");
	}

	function funcUnloadingOptionsPrev(){
		$("#cb_unloading_street").removeAttr("checked");
		$("#cb_unloading_unpack").removeAttr("checked");
		$("#cb_unloading_rabbish").removeAttr("checked");
		$("#divUnloadingOptions").addClass("display_none");
		$("#"+strDivPrevUnloadingOptions).removeClass("display_none");
	}

	function funcNextUnloadingResidential(){
		$("#divUnloadingResidential").addClass("display_none");
		strDivPrevUnloadingOptions='divUnloadingResidentialWeight';
		strLeaveAtAddress = $("input[name='leave_at_address']:checked").val();
		if(strLeaveAtAddress == 'yes'){
			strSummaryUnloading = "<h4>ATL</h4><p>If no one onsite on the day of delivery, I/We authorised to <span style='color:red;'>leave at the address.</span></p><p>(a photo will be taken if ATL is selected)</p>YES<br/><br/>";
		}
		else{
			strSummaryUnloading = "<h4>ATL</h4><p>If no one onsite on the day of delivery, I/We authorised to <span style='color:red;'>leave at the address.</span></p><p>(a photo will be taken if ATL is selected)</p>NO<br/><br/>";
		}
		strSummaryUnloadingOptions = "<h4>The Services We Provide</h4><?=$strProvidDropOff?><?=$strDeliveryTime?> <br/>Email notification will be sent 24 hours before the delivery day.<br/><br/><h4>The Services We Do Not Provide</h4>Unload to house/inside garage<br/>*No climbing stairs only support elevator<br/>Help to unpack boxes<br/>Rubbish removal<br/><br/>";
		strDivPrevDelivery = "divUnloadingResidential";
		strDivPrevOther = "divUnloadingResidential";
		$("<?=$strDivDeliveryOrOther?>").removeClass("display_none");
		
	}

	function funcNextUnloadingResidentialPrev(){
		// $("input[name='leave_at_address'][value='yes']").attr("checked",true); 
		// $("input[name='leave_at_address'][value='no']").removeAttr("checked");
		$("#divUnloadingResidential").addClass("display_none");
		$("#divShipment").removeClass("display_none");
	}

	function funcNextUnloadingBusiness(){
		$("#divUnloadingBusiness").addClass("display_none");
		strDivPrevUnloadingOptions='divUnloadingBusiness';
		if($("input[name='business_unloading']:checked").val() == "fork"){
			strSummaryUnloading = "<h4><?=$strUnloadingH41?></h4><h4><?=$strUnloadingH42?></h4>We have a forklift<br/><br/>";
			strSummaryUnloadingOptions = "<h4>The Services We Provide</h4><?=$strProvidDropOff?><?=$strDeliveryTime?> <br/>Email notification will be sent 24 hours before the delivery day.<br/><br/><h4>The Services We Do Not Provide</h4>Unload to house/inside garage<br/>*No climbing stairs only support elevator<br/>Help to unpack boxes<br/>Rubbish removal<br/><br/>";
			strDivPrevDelivery = "divUnloadingBusiness";
			strDivPrevOther="divUnloadingBusiness";
			$("<?=$strDivDeliveryOrOther?>").removeClass("display_none");
		}
		else if($("input[name='business_unloading']:checked").val() == "self"){
			strSummaryUnloading = "<h4><?=$strUnloadingH41?></h4><h4><?=$strUnloadingH42?></h4><?=$strUnloadingLine1?><br/><br/>";
			strSummaryUnloadingOptions = "<h4>The Services We Provide</h4><?=$strProvidDropOff?><?=$strDeliveryTime?> <br/>Email notification will be sent 24 hours before the delivery day.<br/><br/><h4>The Services We Do Not Provide</h4>Unload to house/inside garage<br/>*No climbing stairs only support elevator<br/>Help to unpack boxes<br/>Rubbish removal<br/><br/>";
			strDivPrevDelivery = "divUnloadingBusiness";
			strDivPrevOther="divUnloadingBusiness";
			$("<?=$strDivDeliveryOrOther?>").removeClass("display_none");
		}
		else if($("input[name='business_unloading']:checked").val() == "hand_unloading_service"){
			strSummaryUnloading = "<h4><?=$strUnloadingH41?></h4><h4><?=$strUnloadingH42?></h4><?=$strUnloadingLine2?><br/><br/>";
			// strSummaryUnloading += "This service only unload goods to street level.<br/><br/>";
			$("#divUnloadingOptions").removeClass("display_none");
		}
		else if($("input[name='business_unloading']:checked").val() == "unloading_service_overweight"){
			strSummaryUnloading = "<h4>Your shipper had ordered standard delivery (without unloading service),</h4><h4>Do you need curb side drop off service? (Extra fee applies)</h4><p>(Yes) We curb side drop off service<br/><br/>";
			strSummaryUnloadingOptions = "<h4>The Services We Provide</h4><?=$strProvidDropOff?><?=$strDeliveryTime?> <br/>Email notification will be sent 24 hours before the delivery day.<br/><br/><h4>The Services We Do Not Provide</h4>Unload to house/inside garage<br/>*No climbing stairs only support elevator<br/>Help to unpack boxes<br/>Rubbish removal<br/><br/>";
			$("#divUnloadingOptions").removeClass("display_none");
		}
	}

	function funcNextUnloadingBusinessPrev(){
		$("input[name='business_unloading'][value='fork']").attr("checked",true); 
		$("#divUnloadingBusiness").addClass("display_none");
		$("#divShipment").removeClass("display_none");
	}

	function funcNextDelivery(){
		strDeliveryTime = $("input[name='current_delivery_time']:checked").val();
		if(strDeliveryTime == null){
			alert('Please select delivery time.');
			return;
		}

		$("#divDelivery").addClass("display_none");
		strDeliveryTime = $("input[name='current_delivery_time']:checked").next().text();
		strSummaryDeliveryTime = '<h4>Delivery Time</h4>'+strDeliveryTime+'<br/><br/>';
		
		strDivPrevOther = "divDelivery";
		$("#divOther").removeClass("display_none");
	}

	function funcNextDeliveryPrev(){
		$("#divDelivery").addClass("display_none");
		$("#"+strDivPrevDelivery).removeClass("display_none");
	}

	function funcNextOther(){
		$("#divOther").addClass("display_none");
		$("#divSummary").removeClass("display_none");

		//summary details
		strDetailHtml = '<h4>Your shipment Information</h4> <p>Ref No.<?=$strHbn?></p><div>Weight: <?=$numWeight?> kg<br/>Qty: <?=$numPkg?> pcs<br/>Volume: <?=$numCBM?> cbm<br/></div><br/>';
		strDetailHtml+=strSummaryDeliveryTime;
		strDetailHtml+='<p>Your Address: '+strFullAddress+'</p><p>Your Name: <?=$strName?></p><p>Your Mobile: <?=$strTel?></p><p>Your Email: <?=$strEmail?></p><br/><br/>';

		strDetailHtml+=strSummaryChangeAddress;
		strDetailHtml+=strSummaryNewAddress;
		strDetailHtml+=strSummaryAddressType;
		strDetailHtml+=strSummaryUnloading;
		strDetailHtml+=strSummaryUnloadingOptions;
		// strDetailHtml+=strSummaryRedelivery;
		

		// strDetailHtml+="<h4>Delivery Time Frame</h4><p>Business hours: 9:00-17:00 Monday to Friday.</p><p>Delivery will be within 5 business day after confirmation.</p><p>You will be informed by SMS text the day before delivery.</p><br/>";
		strDetailHtml+="<h4>Special notes: (30 words max)</h4>";
		strDetailHtml += $("#other_inquery").val();
		strDetailHtml +="<br/><br/><p>*Important Notices:</p><p>Change of address, specific delivery time request, exceed 15mins wait time, re-delivery and special unloading request may incur extra charges.</p><p>The delivery service may be refused if wrong or misleading information is provided.</p><br/><p>Contact Us:</p><p>Top Logistics Australia</p><p>Email: cartage@toplogistics.com.au</p><br/><br/>";

		$("#summary").val(strDetailHtml);
		$("#divSummaryDetails").html(strDetailHtml);
	}

	function funcNextOtherPrev(){
		$("#divOther").addClass("display_none");
		$("#"+strDivPrevOther).removeClass("display_none");

	}
	function funcSubmit(){
		strUrl = window.location.href;
		listData = $('#cargo-confirm-form').serializeArray();
        htmlobj = $.ajax({
            type: "POST",
            url: strUrl,
            data: listData,
            async: false
        });
        obj = JSON.parse(htmlobj.responseText);
        if (obj.isSuccess) {
			window.location.replace(strUrl);
        }
        else{
        }

	}

	function funcSubmitPrev(){
		$("#divSummary").addClass("display_none");
		$("#divOther").removeClass("display_none");
	}



</script>


