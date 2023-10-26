<div class="form">
<?php $form=$this->beginWidget('CActiveForm', [
	'id'=>'payment-form',
	'enableAjaxValidation'=>false,
]); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'org_id'); ?>
		<?php echo $form->hiddenField($model, 'org_id', ['data-ov' => $model->org_id]);
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', [
				'name' => $acname1,
				'sourceUrl' => ['org/ownerSuggest'],
				'value' => '',
				'options' => [
					'showAnim' => 'fold',
					'minLength' => 2,
					'delay' => 200,
					'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]).trigger("change"); return false; }',
					'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				],
				'htmlOptions' => [
					'size' => '30',
				],
			]);
		?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model, 'date'); ?>
	<?php echo $form->textField($model, 'date', ['class' => 'date_input']); ?>
	</div>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model, 'bank'); ?>
	<?php echo $form->dropDownList($model, 'bank', $this->t($model::$banks)); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model, 'type'); ?>
	<?php echo $form->dropDownList($model, 'type', $this->t($model::$types), ['prompt'=>$this->t('Select One')]); ?>
	</div>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model, 'currency'); ?>
	<?php echo $form->dropDownList($model, 'currency', $this->t(Invoice::$currencies)); ?>
	</div>

	<!--div class="row rowcol">
	<?php echo $form->labelEx($model, 'rate'); ?>
	<?php echo CHtml::textField('rate', 1, ['size'=>12,'maxlength'=>12]); ?>
	</div-->

	<div class="row rowcol">
	<?php echo $form->labelEx($model, 'amount'); ?>
<?php echo $form->textField($model, 'amount', ['size'=>10,'maxlength'=>12]); ?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'diff'); ?>
		<?php echo CHtml::textField('diff', '0.00', ['size' => 10, 'maxlength' => 12]); ?>
	</div>

	<?php if (Yii::app()->name != 'PEP') { ?>
	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'refund'); ?>
		<?php echo $form->textField($model, 'refund', ['size' => 10, 'maxlength' => 12, 'value' => '0.00']); ?>
	</div>
	<?php } ?>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model, 'ref'); ?>
<?php echo $form->textField($model, 'ref', ['size'=>30,'maxlength'=>100]); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'allocate date'); ?>
		<?php echo CHtml::textField('alloc_date', '', ['class' => 'date_input']); ?>
	</div>

	<div id="cral"></div>
	<div id="oigc" class="row">
<?php
$inv = new Invoice('search');
if (empty($_GET['Invoice']['to_id'])) {
	$inv->to_id = -1;
} else {
	$inv->unsetAttributes();
	// $rate = $_GET['Invoice']['rate'] ? $_GET['Invoice']['rate'] : 1;
	// unset($_GET['Invoice']['rate']);
	$inv->attributes=$_GET['Invoice'];
	unset($inv->currency);
}
$ec = new CDbCriteria;
$ec->condition = "status IN (2,3,7)";

$this->widget('zii.widgets.grid.CGridView', [
	'id'=>'outinv-grid',
	'cssFile' => false,
	'ajaxUrl' => Yii::app()->createUrl($this->route, ['Invoice[to_id]' => empty($_GET['Invoice']['to_id'])? -1 : $_GET['Invoice']['to_id']]),
	'dataProvider'=>$inv->search(false, 0, 't.due ASC', $ec),
	'filter'=>$inv,
	'columns'=>[
		['name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("invoice/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'],
		['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('Invoice[status]', $model->status, $this->t($model::$states), ['prompt'=>$this->t('All')]),],
		['name' => 'type', 'value' => '$data->getType()',
			'filter'=>CHtml::dropDownList('Invoice[type]', $model->type, $this->t($model::$types), ['prompt'=>$this->t('All')]),],
		'due',
		['name' => 'total', 'value' => '$data->getCurrency()." ".$data->total'],
		['header' => 'Balance', 'value' => '$data->getBalance()'],
		['header' => 'Allocate', 'type' => 'raw', 'value' => '"<input type=\"text\" class=\"alloc\" data-tot=\"".$data->getBalance()."\" name=\"alloc[".$data->id."]\" />"'],
		// ['header' => 'Diff', 'type' => 'raw', 'value' => '"<input class=\"diff\" type=\"checkbox\" name=\"inv_diff[".$data->id."]\" />"']
	],
]);
?>
	</div>
	<div class="row">
	<?php echo $form->labelEx($model, 'note'); ?>
<?php echo $form->textArea($model, 'note', ['rows'=>3, 'cols'=>40]); ?>
	</div>

    <!--
	<div class="row">
		<label><?php echo CHtml::checkbox('post');?> Post Receipt</label>
	</div>
-->
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $("#jqmw_<?=$_GET['tabid'];?>");
	var cmap = <?=json_encode(Invoice::$currencies);?>;
	var invcs = [];
	var bc = 'AUD';
	
	win.on('searchInvoice', function(){
		$.fn.yiiGridView.update('outinv-grid', {
			data: {'Invoice[to_id]': $('#Payment_org_id').val(), 'Invoice[currency]': $('#Payment_currency', win).val(), 'tabid': '<?=$_GET["tabid"];?>'}
		});
		return false;
	});

	$('#oigc', win).on('afterUpdate', function(){
		bc = cmap[$('#Payment_currency', win).val()];
		invcs = [bc];
		$('tbody tr', this).each(function(){
			var c = $($('td', this)[4]).text().substring(0,3);
			$(this).data('currency', c);
			if(!invcs.includes(c)) invcs.push(c);
		});
		$('#cral', win).empty();
		for(var i in invcs){
			if(invcs[i] == bc){
				r = '<div class="row rowcol rowleft"><label>&nbsp;</label><input type="checkbox" class="mccb basec" id="mccb_'+invcs[i]+'" value="'+invcs[i]+'" checked /> '+invcs[i]+'</div><div class="row rowcol"><label>Ex. Rate</label><input type="input" class="crate" id="rate_'+invcs[i]+'" size="7" value="1" readonly /></div><div class="row rowcol"><label>Amount</label><input type="input" id="amt_'+invcs[i]+'" class="camt basec" size="10" value="'+(Number($('#Payment_amount', win).val()) - Number($('#diff', win).val()))+'" readonly /></div><div class="row rowcol"><label>Balance</label><input type="input" id="bal_'+invcs[i]+'" class="basebal" size="10" readonly /></div>';
			}else{
				r = '<div class="row rowcol rowleft"><input type="checkbox" class="mccb" id="mccb_'+invcs[i]+'" value="'+invcs[i]+'" /> '+invcs[i]+'</div><div class="row rowcol"><input type="input" class="crate" id="rate_'+invcs[i]+'" name="exrate['+invcs[i]+']" size="7" /></div><div class="row rowcol"><input type="input" class="camt" id="amt_'+invcs[i]+'" size="10" /></div><div class="row rowcol"><input type="input" id="bal_'+invcs[i]+'" size="10" readonly /></div>';
			}
			$('#cral', win).append(r);
		}
		$($('#cral .mccb', win)[0]).trigger('change');
	});

	$('#Payment_currency, #Payment_org_id', win).on('change', function(){
		win.trigger('searchInvoice');
	});

	$('#payment-form', win).on('success', function(){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

	$('#Payment_amount', win).on('change', function(){
		$('#oigc', win).trigger('afterUpdate');
	});

	$('#diff', win).on('change', function(){
		$('#oigc', win).trigger('afterUpdate');
	});

	var upBal = function(){
		var tot = {};

		$('input.mccb', win).each(function(){
			if(!tot[$(this).val()]) tot[$(this).val()] = 0;
			if($(this).hasClass('basec') || !$(this).prop('checked')) return;
			var exr = Number($('#rate_'+$(this).val(), win).val());
			if(exr == 0) return;
			tot[bc] += Number($('#amt_'+$(this).val(), win).val()) / exr;
		});
		// tot[bc] += Number($('#diff', win).val());
		$('input.alloc', win).each(function(){
			if($(this).prop('disabled')) return;
			tot[$(this).parents('tr').data('currency')] += Number($(this).val());
		});
		for(var i in tot){
			$('#bal_'+i).val((Number($('#amt_'+i, win).val()) - tot[i]).toFixed(3));
		}
	};

	$(win).on('change', 'input.alloc', function(){
		var t = Number($('#bal_'+$(this).parents('tr').data('currency'), win));
		var it = Number($(this).data('tot'));
		var v = $(this).val();

		if(v > it) v = it;
		if(t < v) v = t;
		$(this).val(v);
		upBal();
	}).on('dblclick', 'input.alloc', function(){
		var t = Number($('#bal_'+$(this).parents('tr').data('currency'), win).val());
		var it = Number($(this).data('tot'));

		$(this).val('');
		if(t <= 0) {
			upBal();
			return;
		}
		if(t >= it) $(this).val(it);
		else $(this).val(t);
		upBal();
	}).on('change', 'input.mccb', function(){
		var cs = [];
		$('input.mccb:checked', win).each(function(){
			cs.push($(this).val());
		});
		if(!$(this).prop('checked') && !$(this).hasClass('basec')){
			$('#amt_'+$(this).val(), win).val('');
		}
		$('#oigc tbody tr', win).each(function(){
			var c = cs.includes($(this).data('currency'));
			$('.alloc, .diff', this).prop('disabled', !c);
			if(!c){
				$('.alloc', this).val('');
				$('.diff', this).prop('checked', false);
			}
		});
		upBal();
	}).on('change', 'input.camt', function(){
		upBal();
		var b = Number($('input.basebal', win).val());
		if(b < 0){
			$(this).val('');
			upBal();
		}
	}).on('change', 'input.crate', function(){
		upBal();
		var b = Number($('input.basebal', win).val());
		if(b < 0){
			$(this).val('');
			upBal();
		}
	}).on('dblclick', 'input.camt', function(){
		var b = Number($('input.basebal', win).val());
		var c = $(this).attr('id').substring(4,7);
		var r = Number($('#rate_'+c).val());
		if(b == 0 || !$('#mccb_'+c).prop('checked') || r == 0) return;
		$(this).val((b*r).toFixed(3));
		upBal();
	}).on('dblclick', 'input.crate', function(){
		var b = Number($('input.basebal', win).val());
		var c = $(this).attr('id').substring(5,8);
		var a = Number($('#amt_'+c).val());
		if(b == 0 || !$('#mccb_'+c).prop('checked') || a == 0) return;
		$(this).val((a/b).toFixed(10));
		upBal();
	});

	$(win).on('dblclick', '#diff', function() {
		var b = Number($('input.basebal', win).val());
		if(b < 10){
			$(this).val(b);
			upBal();
		}
	});
});
</script>