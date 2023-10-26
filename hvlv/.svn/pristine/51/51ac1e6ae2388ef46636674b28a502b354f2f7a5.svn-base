  <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'import-charge-code-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
    ));
    ?>
 <div class="row">
     <div class="col-md-3">
         <div class="form-group">
        <?php echo $form->errorSummary($model); ?>
        <label class="required" for="Importchargecode_owner_id" aria-required="true">Customer <span class="required" aria-required="true">*</span></label>
        <?php echo $form->hiddenField($model,'org_id');
        $acname1 = empty($_GET["tabid"])? 'imchgcode_owner_ac' : $_GET["tabid"].'imchgcode_owner_ac';
        $ownername = empty($model->owner) ? '' : $model->owner->name;
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => $acname1,
            'sourceUrl' => array('accounts/ownerSuggest'),
            'value' => $ownername,
            'options' => array(
                'showAnim' => 'fold',
                'minLength' => 2,
                'delay' => 200,
                'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
                'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
            ),
            'htmlOptions' => array(
                'size' => '30',
                'class'=>'form-control'
            ),
        ));
        ?>
         </div>
     </div>
    </div>
<div class="row">
    <div class="form-group col-md-8">
        <label>Select A Courier</label>
        <div class="form-check">
            <?php
            $defSelected = $model->couriersObj;
            $rateList = array(
//                38 => 'StarTrack Road Sydney',
//                52 => 'Fastway',
//                101 => 'eParcel ex GST Sydney',
//                137 => 'D2Z eTower eParcel',
//                193 => 'D2z Country Rate eParcel',
                ImportChargeCode::D2Z_COUNTRY_3PORT=>'D2Z Country 3PORT',
                ImportChargeCode::D2Z_ETOWER_3PORT=>'D2Z Etower 3PORT',
                ImportChargeCode::D2Z_COUNTRY_SYD=>'D2Z Etower Syd',
                ImportChargeCode::D2Z_ETOWER_SYD=>'D2Z Country Syd',
                ImportChargeCode::FASTWAY_D2Z=>"FASTWAY FOR D2Z",
            );
            echo CHtml::checkBoxList('selected_rates', $defSelected, $rateList, array(
                'template' => '{input}{label}',
                'separator' => '',
                'labelOptions' => array(
                    'class' => 'form-check-label'),
                'class' => 'form-check-input',
            ));
            ?>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-md-10 form-group">
             <?php echo CHtml::label('Select Charge Weight Method','charge_wt'); ?>
            <div class="form-check">
             <?php  echo $form->radioButtonList($model,'charge_wt',ImportChargeCode::$charge_weight,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
        </div>
        </div>
    </div>
      <div class="row">
         <div class="col-md-10 form-group">
             <?php echo CHtml::label('Cubic Rate Factor','cubic_rate_factor'); ?>
             <div class="form-check">
             <?php  echo CHtml::radioButtonList('mdata[cubic_rate_factor]', empty($model->mdata['cubic_rate_factor'])?0:$model->mdata['cubic_rate_factor'],ImportChargeCode::$cubic_factors,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
             </div>
             </div>
      </div>
    <div class="row">
        <div class="col-md-6 form-group">
        <?php echo $form->labelEx($model,'note'); ?>
        <?php echo $form->textArea($model,'note',array('rows'=>5, 'cols' => 60,'class'=>'form-control')); ?>
        </div>
        </div>


    <div class="row">
        <?php echo CHtml::label('Invoice Rate:','forinvoice_rate'); ?>
        <?php
        if ( !$model->isNewRecord ) {
            $setZoneMapLink = '<a class="tracking-modal-link" data-win-class="XL" href="'.Yii::app()->createUrl("accounts",array('chgcodezonemap'=>$model->id)).'"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('Import Zone Map').'</a> &nbsp;';
            echo $setZoneMapLink;
            $setFlexRateLink = '<a class="tracking-modal-link" data-win-class="XL" href="'.Yii::app()->createUrl("accounts",array('pcarate'=>$model->id)).'"><div style="background-position:-16px 0" class="icon"></div>' . $this->t('Flex Rate By Zone') . '</a>';
            echo $setFlexRateLink;
        }
        ?>
    </div>
    <div class="row">
        <div class="form-group">
        <?php echo $form->radioButtonList($model,'status', Chargecode::$switch, array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
        </div>
   </div>
   <div class="row buttons">
        <?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'),array('class'=>'btn btn-default')); ?>
    </div>
    <?php $this->endWidget(); ?>
<!-- Tracking Modal -->
<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-body">
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->t('Close');?></button>
	  </div>
	</div>
  </div>
</div>
 <?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
		$('#modal-tracking').modal();
		$('#modal-tracking .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>
