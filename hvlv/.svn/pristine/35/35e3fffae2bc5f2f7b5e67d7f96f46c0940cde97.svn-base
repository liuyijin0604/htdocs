<h1><?=$this->t('Generate Report');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'shipment-receipt-form',
	'enableAjaxValidation' => false,
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row rowcol">
		<?php echo CHtml::label('Agent','fwd_id'); ?>
		<?php echo CHtml::hiddenField('fwd_id');
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/exAgentSuggest'),
				'value' => 'All Agents',
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
		<?php echo CHtml::label('From Date:','fd'); ?>
		<?php echo CHtml::textField('date[from]', date('Y-m-d', strtotime('-1 day')), array('size' => 12, 'id' => 'fd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('To Date:','td'); ?>
		<?php echo CHtml::textField('date[to]', date('Y-m-d', strtotime('-1 day')), array('size' => 12, 'id' => 'td_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row">
		<div class="rowcol">
			<?php echo CHtml::checkBox('forall', 0) . ''; ?>
		</div>
		<div class="rowcol">
			<?php echo CHtml::label(' All','fd'); ?>
		</div>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Export')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->