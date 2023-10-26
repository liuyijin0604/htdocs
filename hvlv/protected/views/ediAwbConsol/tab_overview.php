<div style="position:absolute">
<p>Parcels: <b><?=$model->totShipments();?></b> &nbsp; Total Weight: <b><?=$model->totWeight();?>KG</b></p>
</div>
<div style="text-align:right;">
<a class="jqm_link" href="<?=$this->createUrl('ediAwbConsol/ubm', array('id'=>$model->id));?>"><div style="background-position:-16px -432px" class="icon"></div> <?=$this->t('Underbond Move');?></a>
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export Manifests</a>
<!--a class="jqm_link" href="<?=$this->createUrl('ediAwbConsol/copyRef', array('id' => $model->id))?>"><div style="background-position:-176px -544px" class="icon"></div> Copy Ref</a>

<?php if (Acl::hasAccess("B:ediAwbConsol/localArrival")) { ?>
<a class="jqm_link" href="<?=$this->createUrl('ediAwbConsol/localArrival', array('id' => $model->id))?>"><div style="background-position:-176px -544px" class="icon"></div> Local Arrival</a>
<?php } ?>

<a class="jqm_link" href="<?=$this->createUrl('ediAwbConsol/allPickup', array('id' => $model->id))?>"><div style="background-position:-176px -544px" class="icon"></div> All Pickup</a>

<a class="jqm_link" href="<?=$this->createUrl('ediAwbConsol/allTruckDelivery', array('id' => $model->id))?>"><div style="background-position:-176px -544px" class="icon"></div> All Truck Delivery</a-->
</div>

<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/export', array('id'=>$model->id));?>" target="_blank"><?=$this->t('Shipments');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/arrivalNotice', array('id'=>$model->id));?>" target="_blank"><?=$this->t('Arrival Notice');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/seaManifest', array('id'=>$model->id));?>" target="_blank"><?=$this->t('Sea Manifest');?></a></li>
<li><a href="<?=$this->createUrl('ediAwbConsol/SeaDO', array('id'=>$model->id,'type'=>'cover'));?>" target="_blank"><?=$this->t('Sea Delivery Order');?></a></li>
<li><a class="jqm_link" href="<?=$this->createUrl('ediAwbConsol/SeaOutturnPage', array('id'=>$model->id,'type'=>'cover'));?>" target="_blank"><?=$this->t('Sea Outturn Together');?></a></li>
<?php if($model->type == 15):?>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/eparcel', array('id'=>$model->id));?>" target="_blank"><?=$this->t('eParcel (Multi)');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/sac', array('id'=>$model->id));?>" target="_blank"><?=$this->t('SAC');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/msac', array('id'=>$model->id));?>" target="_blank"><?=$this->t('SAC (Multi)');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/download', array('id'=>$model->id));?>" target="_blank"><?=$this->t('Courier Labels (PDF)');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/seaManifest', array('id'=>$model->id));?>" target="_blank"><?=$this->t('Sea Manifest');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/exportShipmentDetail', array('id'=>$model->id));?>" target="_blank"><?=$this->t('Shipments Detail');?></a></li>
<li><a href="<?=$this->createUrl('ediAwbConsol/SeaDO', array('id'=>$model->id,'type'=>'cover'));?>" target="_blank"><?=$this->t('Sea Delivery Order');?></a></li>
<li><a class="jqm_link" href="<?=$this->createUrl('ediAwbConsol/SeaOutturnPage', array('id'=>$model->id,'type'=>'cover'));?>" target="_blank"><?=$this->t('Sea Outturn Together');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/storageLabel', array('id' => $model->id));?>" target="_blank"><?=$this->t('Storage Label');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/d2zAutomation', array('id' => $model->id));?>" target="_blank"><?=$this->t('AMI Automation');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/blueAutomation', array('id' => $model->id));?>" target="_blank"><?=$this->t('Short Manifest');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/blueAutomation', array('id' => $model->id, 'format'=>'startrack'));?>" target="_blank"><?=$this->t('Long Manifest');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/webMation', array('id' => $model->id));?>" target="_blank"><?=$this->t('BNE Web Automation');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/gvAutomation', array('id' => $model->id));?>" target="_blank"><?=$this->t('GV Automation');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/exportCargoReceipt', array('id' => $model->id));?>" target="_blank"><?=$this->t('Export Cargo Receipt');?></a></li>
<li><a class="export" href="<?=$this->createUrl('ediAwbConsol/dgDocument', array('id'=>$model->id));?>" target="_blank"><?=$this->t('DG Document');?></a></li>
<?php endif; ?>
	</ul>
</div>

<div class="form" style="min-height: 500px">
<?php if(($model->bwf&2)>0):?>
<div style="width:50%;position: absolute; right: 0px;">
  <?php echo $this->renderPartial('sea_operation_tab', array('model'=>$model));?>  
</div>
<?php endif;?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-form',
	'enableAjaxValidation'=>false,
));
?>
	<?php echo $form->errorSummary($model); ?>
	
	<div class="row rowcol">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id',Org::dptList()); ?>
		<?php echo $form->error($model,'dpt_id'); ?>
	</div>

	<?php if(!$model->isNewRecord && Acl::hasAccess('B:Import/StatusOverride')): ?>
	<div class="row rowcol">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model, 'status', ImcoConsol::$states, array('disabled' => 'disabled')); ?>
		 <div style="background-position:-240px -416px" class="icon"></div>
		<?php echo $form->error($model,'status'); ?>
	</div>
	<?php endif; ?>
    <div class="row rowcol">
        <label>Ignore This in the Cost</label>
      <?php echo CHtml::dropDownList('ignore_revenue', empty($model->mdata['ignore_revenue'])?0:$model->mdata['ignore_revenue'], ImcoConsol::$ignore, array('disabled' => 'disabled')); ?>
         <div style="background-position:-240px -416px" class="icon"></div>
        
    </div>

  <div class="row rowcol rowleft imex">
    <?php echo $form->labelEx($model,'service'); ?>
    <?php echo $form->dropDownList($model, 'service', ImcoConsol::$services); ?>
  </div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'awb'); ?>
		<?php echo $form->textField($model,'awb',array('size'=>15,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'awb'); ?>
	</div>

	<div class="row rowcol imex">
		<?php echo $form->labelEx($model,'airline'); ?>
		<?php echo $form->textField($model,'airline',array('size'=>15,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'airline'); ?>
	</div>

	<div class="row rowcol imex">
		<?php echo $form->labelEx($model,'flight'); ?>
		<?php echo $form->textField($model,'flight',array('size'=>15,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'flight'); ?>
	</div>
      <div class="row rowcol rowleft imex">
		<?php echo $form->labelEx($model,'pol'); ?>
    <?php $list = AppHelper::setting2List('pols'); ksort($list); ?>
		<?php echo $form->dropDownList($model,'pol', $list); ?>
		<?php echo $form->error($model,'pol'); ?>
	</div>

	<div class="row rowcol imex">
		<?php echo $form->labelEx($model,'pod'); ?>
    <?php $list = AppHelper::setting2List('pods'); ksort($list); ?>
		<?php echo $form->dropDownList($model,'pod', $list); ?>
		<?php echo $form->error($model,'pod'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'etd <span class="required">*</span>'); ?>
		<?php echo $form->textField($model,'etd', array('size' => 12, 'id' => 'etd_'.$_GET["tabid"],'class' => 'date_input')); ?>
		<?php echo $form->error($model,'etd'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'eta <span class="required">*</span>'); ?>
		<?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta_'.$_GET["tabid"],'class' => 'date_input')); ?>
		<?php echo $form->error($model,'eta'); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('AWB Weight','awb_wt');?>
		<?php echo CHtml::textField('mdata[awb_wt]', @$model->mdata['awb_wt'], array('size'=>5)); ?>Kg
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Chargable Wt.','cgb_wt');?>
		<?php echo CHtml::textField('mdata[cgb_wt]', @$model->mdata['cgb_wt'], array('size'=>5)); ?>Kg
	</div>

  <div class="row rowcol">
    <?php echo CHtml::label('B/L pcs', 'b&l_pcs'); ?>
    <?php echo CHtml::textField('mdata[b&l_pcs]', @$model->mdata['b&l_pcs'], array('size' => 5)); ?>
  </div>

  <div class="row rowcol">
    <?php echo CHtml::label('&nbsp;', 'blockacr'); ?>
    <label class="radio_label"><?php echo CHtml::checkBox('mdata[blockacr]', @$model->mdata['blockacr']); ?> 3rd Party Lodge ACR/SCR</label>
  </div>
    <div class="row rowcol">
    <?php echo CHtml::label('cargo_receipt_pieces', 'cargo_receipt_pieces'); ?>
    <?php echo CHtml::numberField('mdata[cargo_receipt_pieces]', @$model->mdata['cargo_receipt_pieces'], array('size' => 5)); ?>
  </div>
  <div class="row rowcol">
    <?php echo CHtml::label('cargo_receipt_pallets', 'cargo_receipt_pallets'); ?>
    <?php echo CHtml::numberField('mdata[cargo_receipt_pallets]', @$model->mdata['cargo_receipt_pallets'], array('size' => 5)); ?>
  </div>

      <div class="row air_type" >
                <?php echo CHtml::label('Air Type','air_type');?>
		<?php  echo CHtml::radioButtonList('mdata[air_type]' ,@$model->mdata['air_type'], ImcoConsol::$air_types, array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
      <label class="radio_label"> &nbsp;&nbsp; <?php echo CHtml::checkBox('gen_air_type_inv'); ?> Create Loose Invoice </label>
      </div>
      <div class="row">
          <div class="row rowcol rowcol-left">
            <?php echo CHtml::label('Available Date', 'cbm'); ?>
            <?php echo CHtml::textField('mdata[available_date]', @$model->mdata['available_date'], array('id' => 'available_date_'.$_GET["tabid"],'class' => 'date_input')); ?>
          </div>
          <div class="row rowcol rowcol-left">
            <?php echo CHtml::label('Storage Date', 'Storage Date'); ?>
            <?php echo CHtml::textField('mdata[input_storage_date]', @$model->mdata['input_storage_date'], array('id' => 'input_storage_date_'.$_GET["tabid"],'class' => 'date_input')); ?>
          </div>

          <div class="row rowcol">
            <?php echo CHtml::label("Choose Address", 'goods_available_address'); ?>
            <?php echo CHtml::dropDownList('mdata[goods_available_address]', @$model->mdata['goods_available_address'], DmawbConsol::$available_address,  array('prompt'=>'SELECT','style'=>"width:120px;")); ?>
          </div>
          <div class="row rowcol">
            <?php echo CHtml::label('Goods Available At (Address)', 'arrival_cfs'); ?>
            <?php echo CHtml::textField('mdata[arrival_cfs]', @$model->mdata['arrival_cfs'], array('size' => 50)); ?>
          </div>
      </div>


    <div class="row sea-input-fileds">
        <fieldset style="width:80%;position:relative;">
            <legend>Sea Information</legend>

            <div style="position:absolute;right: 20px;"><label>CFS Address:</label>
                    <textarea name="mdata[cfs_address]" rows="6" cols="30"><?=@$model->mdata['cfs_address'];?></textarea>
            </div>
            <div class="row">
            <div class="row rowcol rowcol-left">
                <?php echo CHtml::label('CBM.', 'cbm'); ?>
                <?php echo CHtml::textField('mdata[cbm]', @$model->mdata['cbm'], array('size' => 5)); ?>M<sup>3</sup>
            </div>
             <div class="row rowcol ">
                <?php echo CHtml::label('House Bill.', 'house_bill'); ?>
                <?php echo CHtml::textField('mdata[house_bill]', @$model->mdata['house_bill'], array('size' => 20)); ?>
            </div>
             <div class="row rowcol ">
                <?php echo CHtml::label('Parent Bill.', 'parent_bill'); ?>
                <?php echo CHtml::textField('mdata[parent_bill]', @$model->mdata['parent_bill'], array('size' => 20)); ?>
            </div>
                 <div class="row rowcol ">
                  <?php echo CHtml::label('Container Type', 'for_item'); ?>
                  <?php echo CHtml::radioButtonList('mdata[sea_type]', isset($model->mdata['sea_type']) ? $model->mdata['sea_type'] : 1, DmawbConsol::$containerTypes, array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>    
            </div>
            </div>
              <div class="row">
                <div class="row rowcol rowcol-left">
                    <?php echo CHtml::label('cargo Type', 'for_cargo_type'); ?>
                    <?php echo CHtml::radioButtonList('mdata[cargo_type]', isset($model->mdata['cargo_type']) ? $model->mdata['cargo_type'] : 'LCL', array('LCL' => 'LCL', "FCL" => 'FCL'), array('labelOptions' => array('class' => 'radio_label'), 'class' => 'sea_type', 'separator' => '&nbsp;&nbsp')); ?>    
                </div>
                <div class="row rowcol">
                    <?php echo CHtml::label('Container No', 'for_container_no'); ?>
                    <?php echo CHtml::textField('mdata[container_no]', @$model->mdata['container_no'], array('size' => 20)); ?>
                </div>
                <div class="row rowcol">
                    <?php echo CHtml::label('Seal', 'sea_seal'); ?>
                    <?php echo CHtml::textField('mdata[sea_seal]', @$model->mdata['sea_seal'], array('size' => 20)); ?>
                </div>
                 <div class="row rowcol">
                    <?php echo CHtml::label('Vessel Name', 'sea_vessel'); ?>
                    <?php echo CHtml::textField('mdata[sea_vessel]', @$model->mdata['sea_vessel'], array('size' => 20)); ?>
                </div>
              </div>
            <div class="row">
                <div class="row rowcol">
                    <?php echo CHtml::label('Load Port', 'sea_load_port'); ?>
                    <?php echo CHtml::textField('mdata[sea_load_port]', @$model->mdata['sea_load_port'], array('size' => 20)); ?>
                </div>
                    <div class="row rowcol">
                    <?php echo CHtml::label('Carrier', 'sea_carrier'); ?>
                    <?php echo CHtml::textField('mdata[sea_carrier]', @$model->mdata['sea_carrier'], array('size' => 20)); ?>
                </div>
            </div> 
            <div class="row">
                <div class="row rowcol">
                    <?php echo CHtml::label('Release Type', 'sea_relase_type'); ?>
                    <?php echo CHtml::textField('mdata[sea_relase_type]', @$model->mdata['sea_relase_type'], array('size' => 20)); ?>
                </div>
                 <div class="row rowcol">
                    <?php echo CHtml::label('Marks And Numbers', 'sea_mark_number'); ?>
                    <?php echo CHtml::textField('mdata[sea_mark_number]', @$model->mdata['sea_mark_number'], array('size' => 30)); ?>
                </div>
                 <div class="row rowcol">
                    <?php echo CHtml::label('Handling Instruction', 'sea_instruction'); ?>
                    <?php echo CHtml::textField('mdata[sea_instruction]', @$model->mdata['sea_instruction'], array('size' => 40)); ?>
                </div>
                  <div class="row rowcol">
                    <?php echo CHtml::label('Goods Description ( etc..)', 'item'); ?>
                    <?php echo CHtml::textField('mdata[item]', @$model->mdata['item'], array('size' => 30)); ?>
                </div>
            </div>
             <div class="row">
                <div class="row rowcol">
                <?php echo CHtml::label('Customs Declaration (AUD)','for_item'); ?>
                <?php echo CHtml::textField('mdata[inv_item_cd_sea]', isset($model->mdata['inv_item_cd_sea']) ? $model->mdata['inv_item_cd_sea'] : '85', array('size'=>20)); ?>
                </div>
                 <div class="row rowcol">    
                <?php echo CHtml::label('Custom Lines','for_item');?>
                <?php echo CHtml::textField('mdata[inv_item_custom_lines_sea]',isset($model->mdata['inv_item_custom_lines_sea'])?$model->mdata['inv_item_custom_lines_sea']:'',array('size'=>20));?>
                </div>
                <div class="row rowcol" id="document">
                    <?php echo CHtml::label('Sea Document Fee (AUD)','for_item'); ?>
                    <?php echo CHtml::textField('mdata[inv_item_ad_sea]', isset($model->mdata['inv_item_ad_sea']) ? $model->mdata['inv_item_ad_sea'] : '65', array('size'=>20)); ?>
                </div>
                <div class="row rowcol">
                    <?php echo CHtml::label('CMR Fee (AUD)','for_item'); ?>
                    <?php echo CHtml::textField('mdata[inv_item_cmr_sea]',isset($model->mdata['inv_item_cmr_sea']) ? $model->mdata['inv_item_cmr_sea'] : '25', array('size'=>20)); ?>
                </div>
                <div class="row rowcol">
                    <?php echo CHtml::label('SCA(AUD)','for_item'); ?>
                    <?php echo CHtml::textField('mdata[inv_item_sca]', isset($model->mdata['inv_item_sca']) ? $model->mdata['inv_item_sca'] : '25', array('size'=>20)); ?>
                </div>
            </div>
            <div class="row">
                <div class="rowcol">
                    <?php echo CHtml::label('Sea Port Fee (AUD)','for_item'); ?>
                    <?php echo CHtml::textField('mdata[inv_item_pf]', isset($model->mdata['inv_item_pf']) ? $model->mdata['inv_item_pf'] : '500', array('size'=>20)); ?><span class="lcl_type" <?=@$model->mdata['cargo_type']=="LCL"?"":"style ='display:none;'"?>>/cbm</span>
                </div>
                <div class="rowcol">
                    <?php echo CHtml::label('Time Slot (AUD)','for_item'); ?>
                    <?php echo CHtml::textField('mdata[inv_item_tsl]', isset($model->mdata['inv_item_tsl']) ? $model->mdata['inv_item_tsl'] : '60', array('size'=>20)); ?>
                </div>
                <div class="rowcol">
                    <?php echo CHtml::label('Delivery Fee(AUD)','for_item'); ?>
                    <?php echo CHtml::textField('mdata[inv_item_dlv_sea]', isset($model->mdata['inv_item_dlv_sea'])?$model->mdata['inv_item_dlv_sea']:650, array('size'=>20)); ?><span class="lcl_type" <?=@$model->mdata['cargo_type']=="LCL"?"":"style ='display:none;'"?>>/cbm</span>
                </div>
                <div class="rowcol">
                    <?php echo CHtml::label('Fuel Surcharge(AUD)','for_item'); ?>
                    <?php echo CHtml::textField('mdata[inv_item_fls]', isset($model->mdata['inv_item_fls'])?$model->mdata['inv_item_fls']:'13' , array('size'=>20)); ?>%
                </div>
              </div>
              <div class="row">
                  <div class="rowcol">
                  <?php echo CHtml::label('Customs Duties & Fees (AUD)','for_item'); ?>
                  <?php echo CHtml::textField('mdata[inv_item_cdty_sea]', @$model->mdata['inv_item_cdty_sea'], array('size'=>20)); ?>
                </div>
              </div>  
        </fieldset>
    </div>
    <div style="position: fixed; right: 10em; top:30em;">
       <h1 style="font-size: 3em;"> <?= $model->getConsolDGWarnings()?>&nbsp;#</h1>
    </div>
    

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	
	//bind reload_tab
	tab.off('reload_tab').on('reload_tab', function(){
		var t = $('.ui-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	});
         $(".air_type",panel).hide();
        var awb="<?=$model->awb?>";
        if(awb.match(/\d{3}\-\d{8}/i)&&$("#EdiAwbConsol_service",panel).val()!=20){
          $(".air_type",panel).show();
         }
         $("#EdiAwbConsol_awb",panel).off('change').on('change',function(){
             let awb=$(this).val();
             if(awb.match(/\d{3}\-\d{8}/i)&&$("#EdiAwbConsol_service",panel).val()!=20){
                  $(".air_type",panel).show();
             }else{
                 $(".air_type",panel).hide(); 
             }
         });

         var addr=<?=json_encode(DmawbConsol::$available_address)?>;
        $('#mdata_goods_available_address',panel).on('change',function(){
           var address=addr[$(this).val()];
           if(address){
             var theAddress = address.split('| ')[1];
             $('#mdata_arrival_cfs',panel).val(theAddress);
           }else
           {
              $('#mdata_arrival_cfs',panel).val("");
           }
        });

         function changeLabel(){
             if($("#EdiAwbConsol_service",panel).val()==20){
                   $("label[for='EdiAwbConsol_awb']",panel).html("Ocean Bill");
                   $("label[for='EdiAwbConsol_airline']",panel).html("Vessel id(IMO)");
                   $("label[for='EdiAwbConsol_flight']",panel).html("Voyage");
             }else{
                  $("label[for='EdiAwbConsol_awb']",panel).html("AWB No.");
                  $("label[for='EdiAwbConsol_airline']",panel).html("Airline");
                   $("label[for='EdiAwbConsol_flight']",panel).html("Flight No.");
            }
         }
         changeLabel();
         $("#EdiAwbConsol_service",panel).on('change',function(){
             changeLabel();
            let awb=$("#EdiAwbConsol_awb",panel).val();
            if($(this).val()==20){
             $(".air_type",panel).hide(); 
             $(".sea-input-fileds",panel).show();
            }else{
                if(awb.match(/\d{3}\-\d{8}/i)){
                  $(".air_type",panel).show();
             }else{
                 $(".air_type",panel).hide(); 
             }
              $(".sea-input-fileds",panel).hide();
            }
         });
         if($("#EdiAwbConsol_service",panel).val()==20){
               $(".sea-input-fileds",panel).show();
         }else{
               $(".sea-input-fileds",panel).hide();
         }

	<?php if(!$model->isNewRecord && Acl::hasAccess('B:Import/StatusOverride')): ?>
	$('#EdiAwbConsol_status', panel).next().on('dblclick', function(){
		if(window.confirm('Are you sure to override status?')){
			$(this).prev().attr('disabled', false);
		}
	});
        $('#ignore_revenue', panel).next().on('dblclick', function(){
		if(window.confirm('Are you sure to override status?')){
			$(this).prev().attr('disabled', false);
		}
	});
	<?php endif; ?>

       $(".sea_type").on('change',function(){
          if($(this).val()=="LCL")
          {
            $(".lcl_type").show();
          }else if($(this).val()=="FCL")
          {
             $(".lcl_type").hide();
          }
       });
});
</script>