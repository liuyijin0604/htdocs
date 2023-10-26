<h1><?=$this->t('Org Rate Surcharge');?></h1>

<div class="form">
        <div class='row'>
            <div class='row rowcol'>

                <div class="icon" style="background-position:-16px 0"></div><a href="<?=$this->createUrl('org/exportSurcharge', ['id' => $model->id])."?type=orgrate";?>" target="_blank">Export</a>
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
                                <?php echo CHtml::hiddenField('rate_id',$model->id ); ?>
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
            <div id = 'tld_surcharge'>
                <div class="row">
                    <div class="row rowcol rowcol-left divBorder">
                        <div class="row">
                            
                        <?php foreach ($surcharge as $key => $surchargeObj){
                            ?>
                            <div class="row rowcol rowcol-left" style="margin-top: 20px;">
                            <?php echo CHtml::label($surchargeObj->code,$surchargeObj->code) ?>
                                    <p>
                                    <?php echo CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][kgdwf]',$surchargeObj->kgdwf,array('style'=>'width:50px')),'-',CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][kgdwt]',$surchargeObj->kgdwt,array('style'=>'width:50px')),'KG Dead Weight'?>
                                        
                                    </p>
                                    <p>
                                    <?php echo CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][kgcwf]',$surchargeObj->kgcwf,array('style'=>'width:50px')),'-',CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][kgcwt]',$surchargeObj->kgcwt,array('style'=>'width:50px')),'KG Cubic Weight'?>
                                        
                                    </p>
                                    <p>
                                    <?php echo CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][meterf]',$surchargeObj->meterf,array('style'=>'width:50px')),'-',CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][metert]',$surchargeObj->metert,array('style'=>'width:50px')),'M Diagonal Length'?>
                                        
                                    </p>
                                    
                                    <p>
                                    <?php echo CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][length]',$surchargeObj->length,array('style'=>'width:50px')),'M length'?>
                                        
                                    </p>
                                    <p>
                                    <?php echo CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][width]',$surchargeObj->width,array('style'=>'width:50px')),'M width'?>
                                        
                                    </p>
                                    <p>
                                    <?php echo CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][height]',$surchargeObj->height,array('style'=>'width:50px')),'M height'?>
                                        
                                    </p>

                                    <p>
                                    <?php echo CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][cbm]',$surchargeObj->cbm,array('style'=>'width:50px')),'cbm'?>
                                    <?php echo CHtml::hiddenField('mdata[org_rate]['.$surchargeObj->code.'][type]',$surchargeObj->type,array('style'=>'width:50px'))?>
                                    </p>
                                    <p>_________________________________________</p>
                                    <p>
                                    <?php echo '$',CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][p_piece]',$surchargeObj->p_piece,array('style'=>'width:50px')),'/piece or ',CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][p_shipment]',$surchargeObj->p_shipment,array('style'=>'width:50px')),'/shipment',' or ',CHtml::textField('mdata[org_rate]['.$surchargeObj->code.'][p_percent]',$surchargeObj->p_percent,array('style'=>'width:50px')),'percent'?>
                                        
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