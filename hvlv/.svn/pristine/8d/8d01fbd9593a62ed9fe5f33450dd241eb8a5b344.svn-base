<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'intl-charge-code-form',
		'enableClientValidation'=>true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
		),
	));
	?>
	<div class="rowcol">
		<?php echo $form->errorSummary($model); ?>
		<label class="required" for="Intlchargecode_owner_id" aria-required="true">Customer <span class="required" aria-required="true">*</span></label>
		<?php echo $form->hiddenField($model,'org_id');
		$acname1 = empty($_GET["tabid"])? 'imchgcode_owner_ac' : $_GET["tabid"].'imchgcode_owner_ac';
		$ownername = ($model->isNewRecord || empty($model->owner)) ? '' : $model->owner->name;
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $acname1,
			'sourceUrl' => array('org/clientSuggest'),
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
			),
		));
		?>
	</div>
	<div class="row">
		<div class="row rowcol rowcol-left">
			<?php echo CHtml::label('Select Charge Weight Method','charge_wt'); ?>
			<?php echo $form->radioButtonList($model,'charge_wt',IntlChargeCode::$charge_weight,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
		</div>
	</div>
	<div class="row">
		<div class="row rowcol rowcol-left">
			<?php echo CHtml::label('Cubic Rate Factor','cubic_rate_factor'); ?>
			<?php echo CHtml::radioButtonList('mdata[cubic_rate_factor]', empty($model->mdata['cubic_rate_factor'])?0:$model->mdata['cubic_rate_factor'],IntlChargeCode::$cubic_factors,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'note'); ?>
		<?php echo $form->textArea($model,'note',array('rows'=>5, 'cols' => 60)); ?>
	</div>


	<div class="row">
		<?php echo CHtml::label('Invoice Rate:','forinvoice_rate'); ?>
		<?php
		if ( !$model->isNewRecord ) {
			$setZoneMapLink = '<a class="jqm_link" data-win-class="XL" href="wmsOrg/intlchgcodezonemap/'.$model->id.'.app"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('Import Zone Map').'</a> &nbsp;';
			echo $setZoneMapLink;
			$setFlexRateLink = '<a class="jqm_link" data-win-class="XL" href="wmsOrg/pcarate/' . $model->id . '.app"><div style="background-position:-16px 0" class="icon"></div>' . $this->t('Flex Rate By Zone') . '</a>';
			echo $setFlexRateLink;
		}
		?>
	</div>

	<div class="row imex">
		<?php echo $form->radioButtonList($model,'status', Chargecode::$switch, array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

	<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = tab.data('panel');

		$('#intl-charge-code-form', panel).on('success', function() {
			$('.popCancel').trigger('click');
		});
	});
</script>