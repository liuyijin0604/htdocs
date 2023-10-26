<h2><?=$title;?></h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'filter-shipment-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('report/export', ['m' => 'shipments', 'rpt' => $_GET['rpt']]),
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row rowcol rowleft">
	<?php echo CHtml::label('Depot:','dt'); ?>
	<?php echo CHtml::dropDownList('wid', '', Org::dptList(), array('empty' => 'Select One', 'class' => 'required')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('From Date:','fd'); ?>
		<?php echo CHtml::textField('date[from]', empty($_POST['date']['from'])? date('Y-m-01', strtotime('-1 month')) : $_POST['date']['from'], array('size' => 12, 'id' => 'fd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('To Date:','td'); ?>
		<?php echo CHtml::textField('date[to]', empty($_POST['date']['to'])? date('Y-m-01') : $_POST['date']['to'], array('size' => 12, 'id' => 'td_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'agent_id'); ?>
		<?php echo $form->hiddenField($model,'agent_id', array('data-ov' => $model->agent_id));
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/ownerSuggest'),
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

	<!--div class="row">
		<?php //echo $form->labelEx($model,'status'); ?>
		<?php 
		//if(empty($model->status)) $model->status = array_keys($model::$states);
		//echo $form->checkBoxList($model, 'status', $model::$states, array('checked' => 'checked', 'labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
	</div-->

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'cnor_name'); ?>
		<?php echo $form->textField($model,'cnor_name',array('size'=>25,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'cnee_name'); ?>
		<?php echo $form->textField($model,'cnor_name',array('size'=>25,'maxlength'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
