<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-receipt-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>
	
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id',Org::dptList(), array('empty' => 'Select One'), array('class' => 'required')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'fwd_id'); ?>
		<?php echo $form->hiddenField($model,'fwd_id', array('data-ov' => $model->fwd_id));
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/exAgentSuggest'),
				'value' => empty($model->agent)? '' : $model->agent->name,
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'class' => 'required',
					'size' => '30',
				),
		));
		?>

	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Total','total');?>
		$<?php echo CHtml::textField('mdata[total]', @$model->mdata['total'], array('size'=>5)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php $model->created = date('Y-m-d H:i:s'); echo $form->textField($model,'created',array('size' => 25, 'class' => 'datetime_input')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->