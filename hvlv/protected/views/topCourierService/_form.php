<style type="text/css">
	.display_none {
		display: none;
	}
</style>
<div style="right: 20px;text-align:right;position:absolute;">
</div>
<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'cargo-plan-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'ref', array('required' => 'required')); ?>
		<?php echo $form->textField($model, 'ref', ['size' => 20, 'required' => 'required']); ?>
	</div>

	<div class="row rowcol ">
		<?php echo $form->labelEx($model, 'schedule_time'); ?>
		<?php echo $form->textField($model, 'schedule_time', ['size' => 20, 'class' => 'datetime_input', 'id' => $_GET['tabid'] . '_dt_schedule']); ?>
	</div>

	<div class="row rowcol ">
	<?php echo $form->labelEx($model, 'due_time'); ?>
		<?php echo $form->textField($model, 'due_time', ['size' => 20, 'class' => 'datetime_input', 'id' => $_GET['tabid'] . '_dt_due']); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'creater'); ?>
		<?php echo CHtml::textField('CargoProcessPlan[creater]', $model->assigned->fname, ['disabled' => 'disabled']) ?>
	</div>

	<div class="row rowcol ">
		<?php echo $form->labelEx($model, 'ot_id'); ?>
		<?php echo $form->dropDownList($model, 'ot_id', $this->t(CargoProcessPlan::$dpmts), ['required' => 'required', 'empty' => 'Select One']); ?>
	</div>

	<div class="row rowcol ">
		<?php echo $form->labelEx($model, 'dpt_id'); ?>
		<?php echo $form->dropDownList($model, 'dpt_id', Org::dptList3PL(), ['required' => 'required', 'empty' => 'Select One']); ?>
	</div>

	<div class="row rowcol ">
		<?php echo $form->labelEx($model, 'status', array('required' => 'required')); ?>
		<?php echo $form->dropDownList($model, 'status',  $this->t(CargoProcessPlan::$cargoplan_states), ['empty' => 'Select One', 'required' => 'required']); ?>
	</div>

	<div class="row rowcol rowleft">
		<div>
			<?php echo $form->labelEx($model, 'agent_id', array('required' => 'required')); ?>
		</div>
		<input class="width_item_input" type="hidden" id='CargoProcessPlan[agent_id]' value='<?php echo $model->agent_id ?>' name='CargoProcessPlan[agent_id]'>
		<?php
		$acname = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $acname,
			'sourceUrl' => array('org/ownerSuggest'),
			'value' => empty($model->agent_id) ? '' : $model->agent->name,
			'options' => array(
				'showAnim' => 'fold',
				'minLength' => 2,
				'delay' => 200,
				'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
				'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
			),
			'htmlOptions' => array(
				'class' => 'required',
				'size' => '20',
			),
		));
		?>
	</div>

	<div class="row rowcol ">
		<?php echo $form->labelEx($model, 'invoice_by', array('required' => 'required')); ?>
		<?php echo $form->textField($model, 'invoice_by', ['size' => 20]); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'pallets'); ?>
		<?php echo $form->textField($model, 'pallets', ['size' => 20]); ?>
	</div>

	<div class="row rowcol ">
		<?php echo $form->labelEx($model, 'pallets_rate', array('required' => 'required')); ?>
		<?php echo $form->textField($model, 'pallets_rate', ['required' => 'required','size' => 20,'value'=>$model->getPalletRate()]); ?>
	</div>

	<div class="row rowcol ">
		<?php echo $form->labelEx($model, 'type', array('required' => 'required')); ?>
		<?php echo $form->dropDownList($model, 'type',  $this->t(CargoProcessPlan::$cargoplan_types), ['empty' => 'Select One', 'required' => 'required', 'onchange' => 'funcGetType(this.options[this.selectedIndex].value)']); ?>
	</div>

	<div class="row rowcol rowleft">
		<label>Pallets or Truck Type</label>
		<?php echo CHtml::textArea('CargoProcessPlan[pallets_note]', $model->pallets_note, array('style' => 'width:480px; height:50px;')); ?>
	</div>

	<div class="row rowcol rowleft">
		<label>Note</label>
		<?php echo CHtml::textArea('CargoProcessPlan[note]', $model->note, array('style' => 'width:480px; height:50px;')); ?>
	</div>

	<?php $cnee = new Addr; ?>

	<div id="divCnor" class="col-12 display_none">
		<div class="row rowcol rowleft">
			<h3>Pickup From Address </h3>
		</div></br>
		<div class="row rowcol rowleft">
			<?php echo $form->labelEx($cnee, 'name', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnor][name]', @$model->mdata['cnor']['name'], array('size' => 35, 'required' => 'required')); ?>
		</div>
		<div class="row rowcol">
			<?php echo $form->labelEx($cnee, 'tel', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnor][tel]', @$model->mdata['cnor']['tel'], array('required' => 'required')); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($cnee, 'address'); ?>
			<?php echo CHtml::textField('mdata[cnor][address]', @$model->mdata['cnor']['address'], array('size' => 60, 'required' => 'required')); ?>
		</div>
		<div class="row rowcol rowleft">
			<?php echo $form->labelEx($cnee, 'suburb'); ?>
			<?php echo CHtml::textField('mdata[cnor][suburb]', @$model->mdata['cnor']['suburb'], array('required' => 'required')); ?>
		</div>
		<div class="row rowcol">
			<?php echo $form->labelEx($cnee, 'city'); ?>
			<?php echo CHtml::textField('mdata[cnor][city]', @$model->mdata['cnor']['city'], array('required' => 'required')); ?>
		</div>
		<div class="row rowcol">
			<?php echo $form->labelEx($cnee, 'state'); ?>
			<?php echo CHtml::textField('mdata[cnor][state]', @$model->mdata['cnor']['state'], array('required' => 'required')); ?>
		</div>
		<div class="row rowcol rowleft">
			<?php echo $form->labelEx($cnee, 'postcode'); ?>
			<?php echo CHtml::textField('mdata[cnor][postcode]', @$model->mdata['cnor']['postcode'], array('required' => 'required')); ?>
		</div>
		<div class="row rowcol">
			<?php echo $form->labelEx($cnee, 'country', array('required' => 'required')); ?>
			<?php echo CHtml::dropDownList('mdata[cnor][country]', @$model->mdata['cnor']['country'], CargoProcessPlan::$countries, array('required' => 'required', 'options' => array('AU'=>array('selected'=>true)))); ?>
		</div>
	</div>

	<div id="divCnee" class="col-12 display_none">
		<div class="row rowcol rowleft">
			<h3>Delivery To Address </h3>
		</div></br>
		<div class="row rowcol rowleft">
			<?php echo $form->labelEx($cnee, 'name', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][name]', @$model->mdata['cnee']['name'], array('size' => 35, 'required' => 'required')); ?>
		</div>
		<div class="row rowcol">
			<?php echo $form->labelEx($cnee, 'tel', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][tel]', @$model->mdata['cnee']['tel'], array('required' => 'required')); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($cnee, 'address', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][address]', @$model->mdata['cnee']['address'], array('size' => 60, 'required' => 'required')); ?>
		</div>
		<div class="row rowcol rowleft">
			<?php echo $form->labelEx($cnee, 'suburb', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][suburb]', @$model->mdata['cnee']['suburb'], array('required' => 'required')); ?>
		</div>
		<div class="row rowcol">
			<?php echo $form->labelEx($cnee, 'city', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][city]', @$model->mdata['cnee']['city'], array('required' => 'required')); ?>
		</div>
		<div class="row rowcol">
			<?php echo $form->labelEx($cnee, 'state', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][state]', @$model->mdata['cnee']['state'], array('required' => 'required')); ?>
		</div>
		<div class="row rowcol rowleft">
			<?php echo $form->labelEx($cnee, 'postcode', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][postcode]', @$model->mdata['cnee']['postcode'], array('required' => 'required')); ?>
		</div>
		<div class="row rowcol">
			<?php echo $form->labelEx($cnee, 'country', array('required' => 'required')); ?>
			<?php echo CHtml::dropDownList('mdata[cnee][country]', @$model->mdata['cnee']['country'], CargoProcessPlan::$countries, array('required' => 'required', 'options' => array('AU'=>array('selected'=>true)))); ?>
		</div>
	</div>
	<div class="row buttons">
		<?php echo CHtml::hiddenField('meta_items'); ?>
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'), ['name' => 'act_btn']); ?>
	</div>
	<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
	$(function() {
		var tab = $("#<?= $_GET['tabid']; ?>");
		var panel = tab.data('panel');

		<?php if (!$model->isNewRecord) { ?>
			<?php if ($model->type == 10) { ?>
				$("#divCnee",panel).removeClass("display_none");
				$("#divCnor",panel).addClass("display_none");
			<?php } else if ($model->type == 20) { ?>
				$("#divCnee",panel).addClass("display_none");
				$("#divCnor",panel).removeClass("display_none");
			<?php } else if ($model->type == 30) { ?>
				$("#divCnee",panel).removeClass("display_none");
				$("#divCnor",panel).removeClass("display_none");
			<?php } else { ?>
				$("#divCnee",panel).addClass("display_none");
				$("#divCnor",panel).addClass("display_none");
			<?php } ?>
		<?php } ?>
		

		// $('a.type_switch').on('click', function() {
		// 	setTimeout(function() {
		// 		tab.trigger('reload_tab');
		// 	}, 2e2);
		// });
		var t = $('#mtsk_itms', panel);

		for (var i = 0; i < 3; i++) t.trigger('addLine');

		$('#cargo-plan-form', panel).on({
			'success': function(e, r) {
				<?php if ($model->isNewRecord) { ?>
					myApp.tabs.CreateTab({
						title: 'T-' + r.id,
						url: tab.data('url').replace(/\/create.+/, '/update/' + r.id),
					});
					tab.trigger('close');
				<?php } else { ?>
					tab.trigger('reload_tab');
				<?php } ?>
				$('tbody input', t).prop('disabled', false);
			},
			'error': function(e, r) {
				<?php if ($model->isNewRecord) { ?>
					myApp.tabs.CreateTab({
						title: 'T-' + r.id,
						url: tab.data('url').replace(/\/create.+/, '/update/' + r.id),
					});
					tab.trigger('close');
				<?php } else { ?>
					tab.trigger('reload_tab');
				<?php } ?>
				$('tbody input', t).prop('disabled', false);
			}
		}).on('beforeSerialize', function() {
			var ia = [];
			$('tbody tr', t).each(function() {
				var o = {};
				$('input', this).each(function() {
					o[$(this).attr('name')] = $(this).val();
				});
				ia.push(o);
			});
			$('#meta_items', this).val(JSON.stringify(ia));
			$('tbody input', t).prop('disabled', true);
			return true;
		});

		t.trigger('calcTot');

		if ($('#mdata_pi_cargo', panel).prop('checked')) {
			$('#div_cargo', panel).show();
		} else {
			$('#div_cargo', panel).hide();
		}
		$('#mdata_pi_cargo', panel).off('click').on('click', function() {
			if ($('#mdata_pi_cargo', panel).prop('checked')) {
				$('#div_cargo', panel).show();
			} else {
				$('#div_cargo', panel).hide();
			}
		});

		if ($('#mdata_simple_in', panel).prop('checked')) {
			$('#div_simple', panel).show();
		} else {
			$('#div_simple', panel).hide();
		}
		$('#mdata_simple_in', panel).off('click').on('click', function() {
			if ($('#mdata_simple_in', panel).prop('checked')) {
				$('#div_simple', panel).show();
			} else {
				$('#div_simple', panel).hide();
			}
		});

		$(panel).on('click', '#cargo-plan-form .ajax_link', function() {
			$(this).prev().remove();
			$(this).remove();
		});
	});

	function funcGetType(curTarget) {
		var tab = $("#<?= $_GET['tabid']; ?>");
		var panel = tab.data('panel');
		switch (curTarget) {
			case "10":
				$("#divCnee",panel).removeClass("display_none");
				$("#divCnor",panel).addClass("display_none");
				break;
			case "20":
				$("#divCnee",panel).addClass("display_none");
				$("#divCnor",panel).removeClass("display_none");
				break;
			case "30":
				$("#divCnee",panel).removeClass("display_none");
				$("#divCnor",panel).removeClass("display_none");
				break;
			default:
				$("#divCnee",panel).addClass("display_none");
				$("#divCnor",panel).addClass("display_none");
				break;
		}
	}
</script>