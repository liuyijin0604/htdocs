<h1><?=$this->t('Create Consol');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'dmawb-create-consol-form',
	'enableAjaxValidation'=>false,
));
?>	
	<div class="row rowcol">
	<?php echo $form->errorSummary($model); ?>
	<label class="required" for="DmawbConsol_owner_id" aria-required="true">Customer<span class="required" aria-required="true">*</span></label>
		<?php echo $form->hiddenField($model,'owner_id');
			$acname1 = empty($_GET["tabid"])? 'owner_ac' : $_GET["tabid"].'_owner_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/clientSuggest'),
				'value' => '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '30',
				),
		));
		?>
	</div>
       <div class="row rowcol">
          <?php 
            $model->service=10;
			echo $form->labelEx($model,'service'); ?>
            <?php echo $form->dropDownList($model,'service', ImcoConsol::$services);?>
        </div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'awb'); ?>
		<?php echo $form->textField($model,'awb',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'airline'); ?>
		<?php echo $form->textField($model,'airline',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'flight'); ?>
		<?php echo $form->textField($model,'flight',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'pol'); ?>
		<?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pols')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'pod'); ?>
		<?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pods')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'etd'); ?>
		<?php echo $form->textField($model,'etd', array('size' => 12, 'id' => 'etd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'eta'); ?>
		<?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

    <div class="row rowcol rowleft">
        <?php echo CHtml::label('AWB Weight','awb_wt');?>
        <?php echo CHtml::textField('mdata[awb_wt]', @$model->mdata['awb_wt'], array('size'=>5)); ?>Kg
    </div>

    <div class="row rowcol">
        <?php echo CHtml::label('Chargable Wt.','cgb_wt');?>
        <?php echo CHtml::textField('mdata[cgb_wt]', @$model->mdata['cgb_wt'], array('size'=>5)); ?>Kg
    </div>

    <div class="row rowcol rowleft">
        <?php echo CHtml::label('Pcs','for_shipments'); ?>
        <?php echo CHtml::textField('mdata[shipments]', @$model->mdata['shipments'], array('size'=>5)); ?>
    </div>

    <div class="row rowcol rowleft">
        <?php echo CHtml::label('Report Value(USD)','for_dvalue'); ?>
        <?php echo CHtml::textField('mdata[dvalue]', @$model->mdata['dvalue'], array('size'=>5)); ?>
    </div>




    <div class="row">
        <?php echo CHtml::label('Item','for_item'); ?>
        <?php echo CHtml::textField('mdata[item]', @$model->mdata['item'], array('size'=>100)); ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Create'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
        var panel=tab.data('panel');
	$('form#dmawb-create-consol-form', tab.data('panel')).on('success', function(e, r){
		var url = tab.data('url').replace('dmawbConsol/create','dmawbConsol/update/'+r.id);
		tab.data('url', url).trigger('load');
	});
         $('#DmawbConsol_service',panel).on('change',function(){
            if($(this).val()==10){
                $('label[for="DmawbConsol_awb"]',panel).html('AWB No.');
                $('label[for="DmawbConsol_airline"]',panel).html('Airline');
                $('label[for="DmawbConsol_flight"]',panel).html('Flight No.');
            }else{
                $('label[for="DmawbConsol_awb"]',panel).html('Ocean Bill');
                $('label[for="DmawbConsol_airline"]',panel).html('Vessel id(IMO)');
                $('label[for="DmawbConsol_flight"]',panel).html('Voyage');
             }
        });
});
</script>