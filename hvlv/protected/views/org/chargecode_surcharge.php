<h1><?=$this->t('Chargecode Surcharge');?></h1>
<h5>View History Version<?php echo CHtml::dropDownList("SurchargeRateVersion", "", $versionList) ?></h5>
<a class="tab_link" id = "searchVersion" href=""></a>

<div class="form">
        <div class='row'>
            <div class='row rowcol'>

                <div class="icon" style="background-position:-16px 0"></div><a href="<?=$this->createUrl('org/exportSurcharge', ['id' => $model->id])."?type=chargecode";?>" target="_blank">Export</a>
                 </div>

                <div class="col rowcol" style="min-height:50px; margin:left: 50px">
                    <div class="form">
                            <?php
                            $form=$this->beginWidget('CActiveForm', array(
                                'id'=>'chargecode-surcharge-import-form',
                                'enableAjaxValidation'=>false,
                                'action' => $this->createUrl('org/AjaxImportSurchargeNew')
                            ));
                            ?>
                            <div class="row" style="margin-left:20px;">
                                <?php echo $form->hiddenField($model,'id'); ?>
                                <?php echo CHtml::hiddenField('chargecode_id',$model->id ); ?>
                                <?php echo CHtml::hiddenField('start_import',$omodel->start); ?>
                                <?php echo CHtml::hiddenField('surcharge_criteria_import',@$model->mdata['surcharge_criteria']); ?>

                                <input type="file" name="chargecode_surcharge_rate_file" id="chargecode_surcharge_rate_file" style="float:left;" />
                                <div class="icon" style="background-position:-16px 0">
                                 <p><input id="chargecode_surcharge_rate_import_btn" type="submit" value="Import" /></p>



                                <div id="import-inprogress-flag" class="" style="width: 40px; height: 40px;margin-top: 10px;"></div>
                            </div>
                            <?php $this->endWidget(); ?>
                        </div>
                    </div>
                </div>
        </div>
        <?php $form=$this->beginWidget('CActiveForm', array(
                'id'=>'import-charge-code-surcharge-form',
                'enableClientValidation'=>true,
                'clientOptions'=>array(
                        'validateOnSubmit'=>true,
                ),
        ));
        ?>
        <div class="row">
                <div class='row rowcol rowleft'>
                    <?php echo CHtml::label('Start Date','Start Date'); ?>
                    <?php echo CHtml::textField('start',empty($omodel)?"2000-01-01":$omodel->start, array('size' => 20,'class'=>"date_input",'id'=>'start'.$_GET['tabid'])); ?>
                </div>
            </div>

            <div class="row">
                <div class='row rowcol rowleft'>
                <?= CHtml::label('Criteria','criteria');?>
                <?= CHtml::dropDownList('surcharge_criteria',empty($model->mdata['surcharge_criteria'])?ImportChargeCode::ETD:$model->mdata['surcharge_criteria'], ImportChargeCode::$searchCriteria); ?>
                </div>
            </div>


            <div class='row' style="margin-top:50px;">
                <div class='row rowcol rowleft'>
                <?php echo CHtml::radioButton("surcharge_select",($selectType==0?1:0),['value'=>1,'id'=>'tld_selected']),'<span style="font-weight:BOLD;">TLD surcharge&nbsp;</span>'?>
                </div>
                <div class='row rowcol'>
                <?php echo CHtml::radioButton("surcharge_select",($selectType==1?1:0),['value'=>2,'id'=>'courier_selected']),'<span style="font-weight:BOLD;">Courier surcharge&nbsp;</span>'?>
                </div>
            </div>
            <div id = 'tld_surcharge' style="<?=$selectType==0?"":"display:none"?>">
                <div class="row">
                    <?php echo CHtml::label('fuel surcharge for top courier service','fuel_surcharge'); ?>
                    <?php echo CHtml::textField('mdata[truck_delivery][fuel_surcharge][p_percent]',isset($model->mdata['truck_delivery']['fuel_surcharge'])?$model->mdata['truck_delivery']['fuel_surcharge']:0,array('rows'=>5, 'cols' => 60))?>(<?="System Default:".ImportsSystemFuel::getThisMonthFuel("",ImportsSystemFuel::KP_TYPE)?>)
                     <?php echo CHtml::hiddenField('mdata[truck_delivery][fuel_surcharge][type]','fuel_surcharge',array('style'=>'width:50px'))?>
                </div>
                <div class="row">
                    <div class="row rowcol rowcol-left divBorder">
                        <div class="row">
                            
                        <?php foreach ($surcharge as $key => $surchargeObj){
                                if(strtoupper($surchargeObj->courier_type)!="TLD SURCHARGE") continue;

                            ?>
                            <div class="row rowcol rowcol-left" style="margin-top: 20px;">
                            <?php echo CHtml::label($surchargeObj->code,$surchargeObj->code) ?>
                                    <p>
                                    <?php echo CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][kgdwf]',$surchargeObj->kgdwf,array('style'=>'width:50px')),'-',CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][kgdwt]',$surchargeObj->kgdwt,array('style'=>'width:50px')),'KG Dead Weight'?>
                                        
                                    </p>
                                    <p>
                                    <?php echo CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][kgcwf]',$surchargeObj->kgcwf,array('style'=>'width:50px')),'-',CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][kgcwt]',$surchargeObj->kgcwt,array('style'=>'width:50px')),'KG Cubic Weight'?>
                                        
                                    </p>
                                    <p>
                                    <?php echo CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][meterf]',$surchargeObj->meterf,array('style'=>'width:50px')),'-',CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][metert]',$surchargeObj->metert,array('style'=>'width:50px')),'M Diagonal Length'?>
                                        
                                    </p>
                                    
                                    <p>
                                    <?php echo CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][length]',$surchargeObj->length,array('style'=>'width:50px')),'M length'?>
                                        
                                    </p>
                                    <p>
                                    <?php echo CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][width]',$surchargeObj->width,array('style'=>'width:50px')),'M width'?>
                                        
                                    </p>
                                    <p>
                                    <?php echo CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][height]',$surchargeObj->height,array('style'=>'width:50px')),'M height'?>
                                        
                                    </p>

                                    <p>
                                    <?php echo CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][cbm]',$surchargeObj->cbm,array('style'=>'width:50px')),'cbm'?>
                                    <?php echo CHtml::hiddenField('mdata[truck_delivery]['.$surchargeObj->code.'][type]',$surchargeObj->type,array('style'=>'width:50px'))?>
                                    </p>
                                    <p>_________________________________________</p>
                                    <p>
                                    <?php echo '$',CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][p_piece]',$surchargeObj->p_piece,array('style'=>'width:50px')),'/piece or ',CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][p_shipment]',$surchargeObj->p_shipment,array('style'=>'width:50px')),'/shipment',' or ',CHtml::textField('mdata[truck_delivery]['.$surchargeObj->code.'][p_percent]',$surchargeObj->p_percent,array('style'=>'width:50px')),'percent'?>
                                        
                                    </p>
                            </div>
                            
                        <?php } ?>
                        </div>
                    </div>
                </div> 
                
            </div>

            <div id = 'courier_surcharge' style="<?=$selectType==1?"":"display:none"?>">
                <div class="row">
                    <?php echo CHtml::label('fuel surcharge for courier','fuel_surcharge'); ?>
                    <?php echo CHtml::textField('mdata[courier_surcharge][fuel_surcharge][p_percent]',isset($model->mdata['courier_surcharge']['fuel_surcharge'])?$model->mdata['courier_surcharge']['fuel_surcharge']:0,array('rows'=>5, 'cols' => 60))?>(<?="System Default:".ImportsSystemFuel::getThisMonthFuel("",ImportsSystemFuel::COURIER_TYPE)?>)
                    <?php echo CHtml::hiddenField('mdata[courier_surcharge][fuel_surcharge][type]','fuel_surcharge',array('style'=>'width:50px'))?>
                </div>
                <div class="row">
                    <div class="row rowcol rowcol-left divBorder">
                            <div class="row">

                            <?php foreach ($surcharge as $key => $surchargeObj){
                                if(strtoupper($surchargeObj->courier_type)!="COURIER SURCHARGE") continue;

                            ?>
                                <div class="row rowcol rowcol-left" style="margin-top: 20px;">
                                    <?php echo CHtml::label($surchargeObj->code,$surchargeObj->code) ?>
                                        <p>
                                        <?php echo CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][kgdwf]',$surchargeObj->kgdwf,array('style'=>'width:50px')),'-',CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][kgdwt]',$surchargeObj->kgdwt,array('style'=>'width:50px')),'KG Dead Weight'?>
                                            
                                        </p>
                                        <p>
                                        <?php echo CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][kgcwf]',$surchargeObj->kgcwf,array('style'=>'width:50px')),'-',CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][kgcwt]',$surchargeObj->kgcwt,array('style'=>'width:50px')),'KG Cubic Weight'?>
                                            
                                        </p>
                                        <p>
                                        <?php echo CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][meterf]',$surchargeObj->meterf,array('style'=>'width:50px')),'-',CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][metert]',$surchargeObj->metert,array('style'=>'width:50px')),'M Diagonal Length'?>
                                            
                                        </p>
                                        
                                        <p>
                                        <?php echo CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][length]',$surchargeObj->length,array('style'=>'width:50px')),'M length'?>
                                            
                                        </p>
                                        <p>
                                        <?php echo CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][width]',$surchargeObj->width,array('style'=>'width:50px')),'M width'?>
                                            
                                        </p>
                                        <p>
                                        <?php echo CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][height]',$surchargeObj->height,array('style'=>'width:50px')),'M height'?>
                                            
                                        </p>

                                        <p>
                                        <?php echo CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][cbm]',$surchargeObj->cbm,array('style'=>'width:50px')),'cbm'?>
                                         <?php echo CHtml::hiddenField('mdata[courier_surcharge]['.$surchargeObj->code.'][type]',$surchargeObj->type,array('style'=>'width:50px'))?>
                                        </p>
                                        <p>_________________________________________</p>
                                        <p>
                                        <?php echo '$',CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][p_piece]',$surchargeObj->p_piece,array('style'=>'width:50px')),'/piece or ',CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][p_shipment]',$surchargeObj->p_shipment,array('style'=>'width:50px')),'/shipment',' or ',CHtml::textField('mdata[courier_surcharge]['.$surchargeObj->code.'][p_percent]',$surchargeObj->p_percent,array('style'=>'width:50px')),'percent'?>
                                            
                                        </p>
                                </div>
                            
                            <?php } ?>
                            </div>
                    </div>
                </div>
            </div>

            <div class="row buttons">
                <?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
            </div>
            <?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
        var panel = win.data('panel');
        $('#chargecode_surcharge_rate_import_btn',panel).on('click',function(){
            $('#start_import',panel).val($('#start<?=$_GET['tabid']?>',panel).val());
            $('#surcharge_criteria_import',panel).val($('#surcharge_criteria',panel).val());
            return true;
        })
        $('form#chargecode-surcharge-import-form', panel).data('custom_success', function(r){
            myApp.notice('Done', 5000);
            $('#chargecode_surcharge_rate_import_btn', panel).attr('disabled', false);
            return true;
        });
        $("input[name='surcharge_select']",panel).on('change',function(){
            if($(this).val()==1)
            {
               $("#tld_surcharge",panel).show();
               $("#courier_surcharge",panel).hide();
            }else
            {
               $("#tld_surcharge",panel).hide();
               $("#courier_surcharge",panel).show();
            }
        });

    });
</script>