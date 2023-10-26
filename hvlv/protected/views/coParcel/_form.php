<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'coparcel-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));
?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'owner_id'); ?>
		<?php echo $form->hiddenField($model,'owner_id');
			$acname = empty($_GET["tabid"])? 'owner_ac' : $_GET["tabid"].'_owner_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => $model->owner->name,
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val(""); return false; }',
				),
				'htmlOptions' => array(
					'size' => '50',
				),
		));
		?>

		<?php echo $form->error($model,'owner_id'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'agent_id'); ?>
		<?php echo $form->hiddenField($model,'agent_id');
			$acname = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('org/agentSuggest'),
				'value' => $model->agent->name,
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val(""); return false; }',
				),
				'htmlOptions' => array(
					'class' => 'required',
					'size' => '50',
				),
		));
		?>

		<?php echo $form->error($model,'agent_id'); ?>
	</div>
	<div class="row">
	<div class="col" style="margin-right: 25px">
	<h3>Shipper</h3>
	<div class="row">
		<?php echo $form->labelEx($model->cnor, 'name'); ?>
		<?php echo CHtml::textField('Cnor[name]', $model->cnor->name, array('size'=>60)); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnor, 'address'); ?>
		<?php echo CHtml::textField('Cnor[address]', $model->cnor->address, array('size'=>60)); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnee, 'suburb');
		$sacname = empty($_GET["tabid"])? 'cnee_sub_ac' : $_GET["tabid"].'_cnor_sub_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $sacname,
				'sourceUrl' => array('postcode/suggest'),
				'value' => ($model->cnee->suburb) ? $model->cnee->suburb : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnor[suburb]',
				),
		));
		?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnee, 'state'); ?>
		<?php echo CHtml::dropDownList('Cnor[state]', $model->cnee->state, array('ACT' => 'ACT - Australia Capital Territory', 'NSW' => 'NSW - New South Wales', 'NT' => 'NT - Northern Territory', 'QLD' => 'QLD - Queensland', 'SA' => 'SA - South Australia', 'TAS' => 'TAS - Tasmania', 'VIC' => 'VIC - Victoria', 'WA' => 'WA - Western Australia'), array('empty' => 'Select One')); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnor, 'postcode'); ?>
		<?php echo CHtml::textField('Cnor[postcode]', $model->cnor->postcode); ?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnor, 'country'); ?>
		<?php echo CHtml::textField('Cnor[country]', $model->cnor->country); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnor, 'tel'); ?>
		<?php echo CHtml::textField('Cnor[tel]', $model->cnor->tel); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnor, 'email'); ?>
		<?php echo CHtml::textField('Cnor[email]', $model->cnor->email, array('size'=>60)); ?>
	</div>
	</div>
	<div class="col">
	<h3>Consignee</h3>
	<div class="row">
		<?php echo $form->labelEx($model->cnee, 'name'); ?>
		<?php echo CHtml::textField('Cnee[name]', $model->cnee->name, array('size'=>60)); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnee, 'address'); ?>
		<?php echo CHtml::textField('Cnee[address]', $model->cnee->address, array('size'=>60)); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnee, 'suburb');
		$sacname2 = empty($_GET["tabid"])? 'cnee_sub_ac' : $_GET["tabid"].'_cnee_sub_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $sacname2,
				'sourceUrl' => array('postcode/suggest'),
				'value' => ($model->cnee->suburb) ? $model->cnee->suburb : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnee[suburb]',
				),
		));
		?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnee, 'state'); ?>
		<?php echo CHtml::dropDownList('Cnee[state]', $model->cnee->state, array('ACT' => 'ACT - Australia Capital Territory', 'NSW' => 'NSW - New South Wales', 'NT' => 'NT - Northern Territory', 'QLD' => 'QLD - Queensland', 'SA' => 'SA - South Australia', 'TAS' => 'TAS - Tasmania', 'VIC' => 'VIC - Victoria', 'WA' => 'WA - Western Australia'), array('empty' => 'Select One')); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnee, 'postcode'); ?>
		<?php echo CHtml::textField('Cnee[postcode]', $model->cnee->postcode); ?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnee, 'country'); ?>
		<?php echo CHtml::textField('Cnee[country]', $model->cnee->country); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnee, 'tel'); ?>
		<?php echo CHtml::textField('Cnee[tel]', $model->cnee->tel); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnee, 'email'); ?>
		<?php echo CHtml::textField('Cnee[email]', $model->cnee->email, array('size'=>60)); ?>
	</div>
	</div>
	</div>

	<div class="row">
	<h3>Goods</h3>
	<div class="row rowcol rowleft" style="margin-right: 25px">
		<?php echo $form->labelEx($model,'pkg'); ?>
		<?php echo $form->textField($model,'pkg', array('size' => 10)); ?>
	</div>
	<div class="row rowcol" style="margin-right: 25px">
		<?php echo $form->labelEx($model,'weight'); ?>
		<?php echo $form->textField($model,'weight', array('size' => 10)); ?> KG
	</div>
	<div class="row rowcol" style="margin-right: 25px">
		<?php echo $form->labelEx($model,'cbm'); ?>
		<?php echo $form->textField($model,'cbm', array('size' => 10)); ?> M<sup>3</sup>
	</div>
	
	<div class="row">
	<table id="items">
	<thead>
	<tr><th>#</th><th>Item <span class="required">*</span></th><th>Quantity</th><th>Unit Value</th><th>Sub Total</th></tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
	<tr><td colspan="2"><?php if($model->status < 50): ?><input type="button" value="More item" id="more_item" /><?php else: ?>&nbsp;<?php endif;?></td><th id="tot_qty"></th><td>&nbsp;</td><th id="tot_value">&nbsp;</th></tr>
	</tfoot>
	</table>
	</div>
	</div>

	<div class="row">
	<div class="row rowcol rowcol-left">
		<?php echo $form->labelEx($model,'insurance'); ?>
		<?php echo $form->textField($model,'insurance', array('size' => 10)); ?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model,'ref'); ?>
		<?php echo $form->textField($model,'ref', array('size' => 20)); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'note'); ?>
		<?php echo $form->textArea($model,'note',array('rows'=>5, 'cols' => 60)); ?>
	</div>
	<div class="row">
		<?php echo $form->checkbox($model,'pickup'); ?>
		<?php echo $form->labelEx($model,'pickup', array('class' => 'radio_label')); ?>
	</div>
	<div class="row pu-addr" style="display:none">
		<?php echo CHtml::label('Pickup Address', 'pickup-addr'); ?>
		<?php echo CHtml::textArea('mdata[pickup-addr]', @$model->mdata['pickup-addr'], array('rows'=>3, 'cols' => 60)); ?>
	</div>
	</div>

	<div class="row buttons">
		<?php if($model->status < 50) echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	
	var pitems = <?=json_encode(empty($model->eitems)? '' : $model->eitems);?> || {};

	var addItem = function(add){
		var tb = $('#items tbody', panel);
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0){
			tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td>'+(id+1)+'</td><td><input type="text" name="items[g]['+id+']" size="40" value="'+(pitems.g && pitems.g[id]? pitems.g[id] : '')+'"'+(pitems.g && pitems.g[id] === false? ' class="error"' : '')+' /></td><td><input type="text" class="item_qty'+(pitems.q && pitems.q[id] === false? ' error' : '')+'" name="items[q]['+id+']" size="5" value="'+(pitems.q && pitems.q[id]? pitems.q[id]: '')+'" /></td><td><input type="text" class="item_uv'+(pitems.uv && pitems.v[id] === false? ' error' : '')+'" name="items[v]['+id+']" size="10" value="'+(pitems.v && pitems.v[id]? pitems.v[id]: '')+'" /></td><td class="item_tot">&nbsp</td></tr>');
			id++;
		}
	};
	var calcTot = function(){
		var tqty = 0;
		var tval = 0;
		
		$('#items tbody tr', panel).each(function(){
			q = Number($('.item_qty', this).val()) || 0;
			if(q <= 0) return;
			p = Number($('.item_uv', this).val()) || 0;
			if(q <= 0) return;
			sub = q * p;
			$('.item_tot', this).text(Math.round(q*p*100)/100);
			tval += sub;
			tqty += q;
		});
		
		$('#tot_qty', panel).text(tqty);
		$('#tot_value', panel).text(Math.round(tval*100)/100);
	};
	$('#items', panel).off('change', '.item_qty,.item_uv').on('change', '.item_qty,.item_uv', calcTot);
	$('#CoParcel_pickup', panel).change(function(){
		if($(this).attr('checked')){
			$('.pu-addr', panel).show();
		}else{
			$('.pu-addr', panel).hide();
		}
	}).click(function(){
		$(this).trigger('change');
	}).trigger('change');
	
	$('#more_item', panel).click(function(){
		addItem(1);
	});
	addItem(pitems.name? pitems.name.length : 3);

	<?php if($model->status >= 50):?>
	$('form', panel).lockForm();
	<?php endif; ?>

	//suburb ac
	$('#<?=$sacname;?>', panel).on('ac_after_select', function(evt, ui){
		$("#Cnor_postcode", panel).val(ui.item['pc']);
		$('#Cnor_state', panel).val(ui.item['st']);
	});

	$('#<?=$sacname2;?>', panel).on('ac_after_select', function(evt, ui){
		$("#Cnee_postcode", panel).val(ui.item['pc']);
		$('#Cnee_state', panel).val(ui.item['st']);
	});

});
</script>