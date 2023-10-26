<h1><?=$this->t('Stock Report');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'stock-report-form',
	'enableAjaxValidation' => false,
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<div class="row">
		<?php echo CHtml::label('Owner','org_id'); ?>
		<?php echo CHtml::hiddenField('org_id');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => empty($_GET["tabid"])? 'org_id_ac' : $_GET["tabid"].'_org_id_ac',
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
					'class' => 'required',
					'size' => '50',
				),
		));
		?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Warehouse:','hw'); ?>
		<?php echo CHtml::dropDownList('dpt_id', 106, Org::dptList(), array('empty' => 'All')); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('To date:', 'todate'); ?>
		<?php echo CHtml::textField('todate', '', array('class' => 'date_input', 'autocomplete' => 'off')) . '&nbsp;<span style="color: red">23:59:59</span>'; ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Include empty stock', 'incempty'); ?>
		<?php echo CHtml::checkbox('incempty', 0); ?>
	</div>

	<div class="row buttons">
		<input id="sq" type="hidden" name="q" />
		<?php echo CHtml::submitButton($this->t('Export')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	
	$('#stock-report-form', win).validate();

});
</script>