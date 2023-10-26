<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'payment-billing-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'org_id'); ?>
		<?php echo $form->hiddenField($model,'org_id', array('data-ov' => $model->org_id));
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => '',
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
	<?php echo $form->labelEx($model,'date'); ?>
	<?php echo $form->textField($model,'date', ['class' => 'date_input']); ?>
	</div>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model,'bank'); ?>
	<?php echo $form->dropDownList($model, 'bank', $this->t($model::$banks)); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'type'); ?>
	<?php echo $form->dropDownList($model, 'type', $this->t($model::$types), array('prompt'=>$this->t('Select One'))); ?>
	</div>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model,'currency'); ?>
	<?php echo $form->dropDownList($model, 'currency', $this->t(Invoice::$currencies)); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'amount'); ?>
<?php echo $form->textField($model,'amount',array('size'=>12,'maxlength'=>12)); ?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'diff'); ?>
		<?php echo CHtml::textField('diff', '0.00', array('size' => 12, 'maxlength' => 12)); ?>
	</div>
	<div class="row">
	<?php echo $form->labelEx($model,'ref'); ?>
<?php echo $form->textField($model,'ref',array('size'=>30,'maxlength'=>100)); ?>
	</div>
	<div class="row">
	<label>Outstanding Billings</label>
<?php
$billing = new Billing('search');
if (empty($_GET['Billing']['org_id'])) {
	$billing->org_id = -1;
} else {
	$billing->unsetAttributes();
	$billing->attributes = $_GET['Billing'];
	unset($billing->currency);
}
$ec = new CDbCriteria;
$ec->condition = "status IN (2,3)";

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'outbilling-grid',
	'cssFile' => false,
	'dataProvider'=>$billing->search(false, 0, 't.due ASC', $ec),
	'filter'=>$billing,
	'columns'=>array(
		'billing_cref',
		'no',
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('Billing[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'))),),
		'due',
		array('name' => 'total', 'value' => '$data->getCurrency()." ".$data->total'),
		array('header' => 'Balance', 'value' => !empty($_GET['Billing']['currency']) ? '$data->getBalance(' . $_GET['Billing']['currency'] . ')' : '$data->getBalance()'),
		array('header' => 'Allocate', 'type' => 'raw', 'value' => '"<input type=\"text\" class=\"alloc\" data-tot=\"".$data->getBalance()."\" name=\"alloc[".$data->id."]\" />"'),
		array('header' => 'Diff', 'type' => 'raw', 'value' => '"<input type=\"checkbox\" name=\"billing_diff[".$data->id."]\" />"')
	),
));
?>
	</div>
	<div class="row">
	<?php echo $form->labelEx($model,'note'); ?>
<?php echo $form->textArea($model,'note',array('rows'=>3, 'cols'=>40)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $("#jqmw_<?=$_GET['tabid'];?>");
	
	win.on('searchBilling', function(){
		$.fn.yiiGridView.update('outbilling-grid', {
			data: {'Billing[org_id]': $('#PaymentBilling_org_id').val(), 'Billing[currency]': $('#PaymentBilling_currency', win).val()}
		});
		return false;
	});

	$('#PaymentBilling_currency, #PaymentBilling_org_id', win).on('change', function(){
		win.trigger('searchBilling');
	});

	$('#payment-billing-form', win).on('success', function(){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

	$(win).on('change', 'input.alloc', function(){
		var t = Number($('#PaymentBilling_amount', win).val()) - Number($('#diff', win).val());
		var it = Number($(this).data('tot'));
		var v = $(this).val();

		$('input.alloc', win).not(this).each(function(){
			var v = Number($(this).val());
			if(v > 0) t = Math.round((t - v) * 100) / 100;
		});

		if(v > it) v = it;
		if(t < v) v = t;

		$(this).val(v);
	}).on('dblclick', 'input.alloc', function(){
		var t = Number($('#PaymentBilling_amount', win).val()) - Number($('#diff', win).val());
		var it = Number($(this).data('tot'));
		$(this).val('');
		$('input.alloc', win).each(function(){
			var v = Number($(this).val());
			if(v > 0) t = Math.round((t - v) * 100) / 100;
		});
		if(t <= 0) return;
		if(t >= it) $(this).val(it);
		else $(this).val(t);
	});

	$(win).on('dblclick', '#diff', function() {
		var t = Number($('#PaymentBilling_amount', win).val());
		var it = Number($(this).data('tot'));
		$(this).val('');
		$('input.alloc', win).each(function(){
			var v = Number($(this).val());
			if(v > 0) t = Math.round((t - v) * 100) / 100;
		});
		if(t >= it) $(this).val(it);
		else $(this).val(t);
	});
});
</script>