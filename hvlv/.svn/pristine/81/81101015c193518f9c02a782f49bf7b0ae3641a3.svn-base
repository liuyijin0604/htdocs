<style>
#container-cartage-form #ContainerCartage_if_fq, #ContainerCartage_if_pra {
	-ms-transform: scale(1.5); /* IE 9 */
	-webkit-transform: scale(1.5); /* Chrome, Safari, Opera */
	transform: scale(1.5);
	margin-top: 5px;
	margin-left: 2px;
}
</style>

<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'container-cartage-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'org_id'); ?>
		<?php echo $form->hiddenField($model, 'org_id', array('data-ov' => $model->org_id));
		$acname1 = empty($_GET['tabid']) ? 'agent_ac' : $_GET['tabid'] . '_agent_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $acname1,
			'sourceUrl' => array('org/exAgentSuggest'),
			'value' => empty($model->org->name) ? '' : $model->org->name,
			'options' => array(
				'showAnim' => 'fold',
				'minLength' => 2,
				'delay' => 200,
				'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]).trigger("change"); return false; }',
				'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
			),
			'htmlOptions' => array(
				'size' => '30',
			),
		));
		?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'task_id'); ?>
		<?php echo $form->textField($model, 'task_id', array('style' => 'width: 240px', 'disabled' => 'disabled')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'container_load_time'); ?>
		<?php echo $form->textField($model, 'container_load_time', array('style' => 'width: 240px', 'class' => 'datetime_input', 'id' => 'container_load_time_' . $_GET['tabid'])); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'booking_release_no'); ?>
		<?php echo $form->textField($model, 'booking_release_no', array('style' => 'width: 240px', 'maxlength' => 100)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'vessel_name'); ?>
		<?php echo $form->textField($model, 'vessel_name', array('style' => 'width: 240px', 'maxlength' => 100)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'etd'); ?>
		<?php echo $form->textField($model, 'etd', array('style' => 'width: 240px', 'class' => 'date_input', 'id' => 'etd_' . $_GET['tabid'])); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'start_receiving_time'); ?>
		<?php echo $form->textField($model, 'start_receiving_time', array('style' => 'width: 240px', 'class' => 'datetime_input', 'id' => 'start_receiving_time_' . $_GET['tabid'])); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'cutoff_time'); ?>
		<?php echo $form->textField($model, 'cutoff_time', array('style' => 'width: 240px', 'class' => 'datetime_input', 'id' => 'cutoff_time_' . $_GET['tabid'])); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'cargo_estimate_ready_date'); ?>
		<?php echo $form->textField($model, 'cargo_estimate_ready_date', array('style' => 'width: 240px', 'class' => 'date_input', 'id' => 'cargo_estimate_ready_date_' . $_GET['tabid'])); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'empty_depot_name'); ?>
		<?php echo $form->dropDownList($model, 'empty_depot_name', ContainerCartage::$empty_depot_names, array('empty' => 'Select One', 'style' => 'width: 240px')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'full_return_name'); ?>
		<?php echo $form->dropDownList($model, 'full_return_name', ContainerCartage::$full_return_names, array('empty' => 'Select One', 'style' => 'width: 240px')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'container_type'); ?>
		<?php echo $form->dropDownList($model, 'container_type', ContainerCartage::$container_types, array('empty' => 'Select One', 'style' => 'width: 240px')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'if_fq'); ?>
		<?php echo $form->checkBox($model, 'if_fq'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'if_pra'); ?>
		<?php echo $form->checkBox($model, 'if_pra'); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'note'); ?>
		<?php echo $form->textArea($model, 'note', array('rows' => 15, 'cols' => 65)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>

<script type="text/javascript">
$(function() {
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	<?php if ($model->isNewRecord) { ?>
		$('form#container-cartage-form', panel).on('success', function(e, r) {
			$('input[type="submit"]',panel).replaceWith('<label>Created Successfully!</label>');
			$('form#container-cartage-form', panel).append('<a href="containerCartage/update/'+r.id+'" title="Update Container Cartage" class="tab_link" id="container_cartage_create"></a>');
			$('#container_cartage_create', panel).trigger('click');
			$('.tabClose', tab).trigger('click');
		});
	<?php } ?>
});
</script>