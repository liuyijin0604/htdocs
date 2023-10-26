<div class="form">
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' =>[
		'data-bit' => '1',
	]
));

if(empty($model->cnor)){
	$model->cnor = new Addr;
	$model->cnor->country = 'Australia';
	$u = User::model()->findByPk(Yii::app()->user->id);
	if(!empty($u->org->extra['always_last_shipper'])){
		$s = $model::model()->find(['condition' => 'agent_id = :agent', 'order' => 't.id DESC', 'params' => [':agent' => $u->org_id]]);
		if($s){
			$model->cnor = $s->cnor;
		}
	}
}
if(empty($model->cnee)){
	$model->cnee = new Addr;
	$model->cnee->country = 'Australia';
}
$model->pkg = 1;
?>

	<div class="row">
	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'pkg');?>
			<?php echo $form->textField($model,'pkg',array('size'=>10,'maxlength'=>10, 'class' => 'form-control')); ?>
	</div>
	</div>
	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'weight');?>
		<div class="input-group">
			<?php echo $form->textField($model,'weight',array('size'=>10,'maxlength'=>10, 'class' => 'form-control', 'id' => 'CoParcel_weight')); ?>
			<div class="input-group-addon">kg</div>
		</div>
	</div>
	</div>
	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'cbm');?>
		<div class="input-group">
			<?php echo $form->textField($model,'cbm',array('size'=>10,'maxlength'=>10, 'class' => 'form-control')); ?>
			<div class="input-group-addon">m<sup>3</sup></div>
		</div>
	</div>
	</div>

	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'insurance'); ?>
		<div class="input-group">
    		<div class="input-group-addon">$</div>
			<?php echo $form->textField($model,'insurance',array('size'=>6,'maxlength'=>10, 'class' => 'form-control')); ?>
		</div>
	</div>

	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'cref'); ?>
		<?php echo $form->textField($model, 'cref', array('size'=>10,'maxlength'=>20, 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="row">
	<div class="col col-md-6 col-sm-12">
	<h3><?=$this->t('Shipper');?></h3>
	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'name');
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'cnor_name_ac',
				'sourceUrl' => array('shipment/cnorSuggest'),
				'value' => ($model->cnor->name) ? $model->cnor->name : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'autoFocus' => true,
						'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnor[name]',
					'class' => 'form-control',
				),
		));
		?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'tel');
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'cnor_tel_ac',
				'sourceUrl' => array('shipment/cnorSuggest'),
				'value' => ($model->cnor->tel) ? $model->cnor->tel : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 4,
						'delay' => 200,
						'autoFocus' => true,
						'select' => 'js:function(evt, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnor[tel]',
					'class' => 'form-control',
				),
		));
		?>
	</div>
	</div>
	</div>
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'address'),
		CHtml::textField('Cnor[address]', $model->cnor->address, array('size'=>40, 'class' => 'form-control'));?>
	</div>

	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'suburb');
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'cnor_sub_ac',
				'sourceUrl' => array('shipment/auPcSuggest'),
				'value' => ($model->cnor->suburb) ? $model->cnor->suburb : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'autoFocus' => true,
						'select' => 'js:function(evt, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnor[suburb]',
					'class' => 'form-control',
				),
		));
		?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'state'),
		CHtml::dropDownList('Cnor[state]', $model->cnor->state, array('ACT' => 'ACT - Australia Capital Territory', 'NSW' => 'NSW - New South Wales', 'NT' => 'NT - Northern Territory', 'QLD' => 'QLD - Queensland', 'SA' => 'SA - South Australia', 'TAS' => 'TAS - Tasmania', 'VIC' => 'VIC - Victoria', 'WA' => 'WA - Western Australia'), array('empty' => $this->t('Select One'), 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'postcode'),
		CHtml::textField('Cnor[postcode]', $model->cnor->postcode, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'country'),
		CHtml::textField('Cnor[country]', $model->cnor->country, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'email'),
		CHtml::textField('Cnor[email]', $model->cnor->email, array('size'=>30, 'class' => 'form-control')); ?>
	</div>
	</div>
	<div class="col col-md-6 col-sm-12">
	<h3><?=$this->t('Consignee');?></h3>

	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'name');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'cnee_name_ac',
					'sourceUrl' => $this->createUrl('shipment/cneeSuggest', empty($model->cnee->id)? array() :array('id' => $model->cnee->id)),
					'value' => empty($model->cnee->name)? '' : $model->cnee->name,
					'options' => array(
							'showAnim' => 'fold',
							'minLength' => 2,
							'delay' => 200,
							'autoFocus' => true,
							'select' => 'js:function(evt, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
							'change' => 'js:function(evt, ui){ return false; }',
					),
					'htmlOptions' => array(
						'size' => '15',
						'name' => 'Cnee[name]',
						'class' => 'form-control',
					),
			));
		?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'tel'),
		CHtml::textField('Cnee[tel]', $model->cnee->tel, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'address'),
		CHtml::textField('Cnee[address]', $model->cnee->address, array('size'=>40, 'class' => 'form-control'));?>
	</div>

	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'suburb');
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'cnee_sub_ac',
				'sourceUrl' => array('shipment/auPcSuggest'),
				'value' => ($model->cnee->suburb) ? $model->cnee->suburb : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'autoFocus' => true,
						'select' => 'js:function(evt, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnee[suburb]',
					'class' => 'form-control',
				),
		));
		?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'state'),
		CHtml::dropDownList('Cnee[state]', $model->cnee->state, array('ACT' => 'ACT - Australia Capital Territory', 'NSW' => 'NSW - New South Wales', 'NT' => 'NT - Northern Territory', 'QLD' => 'QLD - Queensland', 'SA' => 'SA - South Australia', 'TAS' => 'TAS - Tasmania', 'VIC' => 'VIC - Victoria', 'WA' => 'WA - Western Australia'), array('empty' => $this->t('Select One'), 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="row">
	<div class="col col-sm-6 col-sx-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'postcode'),
		CHtml::textField('Cnee[postcode]', $model->cnee->postcode, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-6 col-sx-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'country'),
		CHtml::textField('Cnee[country]', $model->cnee->country, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'email'),
		CHtml::textField('Cnee[email]', $model->cnee->email, array('size'=>30, 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="form-group table-responsive">
	<table id="items" class="table table-striped table-bordered">
	<thead>
	<tr><th>#</th><th><?=$this->t('Item Name');?> <span class="required">*</span></th><th><?=$this->t('Qty');?></th><th><?=$this->t('Value');?></th></tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
	<tr><td colspan="2"><b class="pull-right">Total:</b><button class="moreitem btn btn-normal"><span class="glyphicon glyphicon-plus" style="color: #be3426; font-size: 1.2em; padding: 3px 10px;"></span></button></td><th id="tot_qty"></th><th id="tot_value">&nbsp;</th></tr>
	</tfoot>
	</table>
	</div>
	
	<div class="form-group buttons">
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t($model->isNewRecord ? 'Create' : 'Save');?></button>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	var pitems = <?=json_encode(empty($model->eitems)? '' : $model->eitems);?> || {};
	var addItem = function(add){
		var tb = $('#items tbody');
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0){
			tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td class="rid">'+(id+1)+'</td><td><input type="text" class="item_name'+(pitems.g && pitems.g[id] === false? ' error' : '')+' form-control" name="items[g]['+id+']" size="25" value="'+(pitems.g && pitems.g[id]? pitems.g[id] : '')+'" /></td><td><input type="text" class="item_qty'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="items[q]['+id+']" size="3" value="'+(pitems.q && pitems.q[id]? pitems.q[id]: '')+'" /></td><td><input type="text" class="item_tv'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="items[v]['+id+']" size="6" value="'+(pitems.v && pitems.v[id]? pitems.v[id]: '')+'" /></td></tr>');
			id++;
		}
		$('select.typsel', tb).each(function(){
			if($(this).data('ov') != ''){
				$(this).val($(this).data('ov'));
			}
		});
		calcTot();
	};
	var calcTot = function(){
		var tqty = 0;

		$('#items tbody tr').each(function(){
			tqty += Number($('.item_qty', this).val()) || 0;
		});
		
		$('#tot_qty').text(tqty);
	};
	
	//required fields
	$('#cnee_name_ac, #CoParcel_weight, #cnor_name_ac, #cnor_tel_ac, #cnee_name_ac, #Cnee_tel, #Cnee_address, #Cnee_postcode').off('change').on('change', function(){
		if($(this).val() == '' || $(this).val() == 0){
			$(this).parents('.form-group').addClass('has-warning').removeClass('has-success');
		}else{
			$(this).parents('.form-group').removeClass('has-warning').addClass('has-success');
		}
	}).trigger('change');

	$('#items').off('change', '.item_qty,.item_wt,.item_tv,.item_tax').on('change', '.item_qty,.item_wt,.item_tv,.item_tax', calcTot);

	$('#items').off('keydown', 'input[type=text]').on('keydown', 'input[type=text]', function(evt){
			if(evt.keyCode == 13){
				if(Number($(this).parents('tr').find('.rid').text()) == $('#items tbody tr').length) addItem(1);
				$(this).parents('tr').next().find('.item_name').focus();
				return false;
			}
	});
	$('.moreitem').off('click').on('click', function(e){
		addItem(1);
		$(this).parents('tr').next().find('.typsel').focus();
		e.preventDefault();
	});

	addItem(pitems.g? pitems.g.length : 1);

	//cnor name/mobile ac
	$('#cnor_name_ac, #cnor_tel_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		var mfs = ['address', 'state', 'postcode', 'country', 'email'];
		for(i in mfs) $('#Cnor_'+mfs[i]).val(ui.item[mfs[i]]);
		$('#cnor_name_ac').val(ui.item.name);
		$('#cnor_tel_ac').val(ui.item.tel);
		$('#cnor_sub_ac').val(ui.item.suburb);
	});

	//cnor suburb ac
	$('#cnor_sub_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$("#Cnor_postcode").val(ui.item.pc);
		$('#Cnor_state').val(ui.item.st);
	});

	//cnee name ac
	$('#cnee_name_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		var mfs = ['tel', 'address', 'postcode', 'state', 'country', 'email'];
		for(i in mfs) $('#Cnee_'+mfs[i]).val(ui.item[mfs[i]]);
		$("#cnee_sub_ac").val(ui.item.suburb);
		$("#Cnee_address").focus();
		$('#notifc').notify({message: {text: "Consignee atuofill"}}).show();
	});

	//cnee suburb ac
	$('#cnee_sub_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$("#Cnee_postcode").val(ui.item.pc);
		$('#Cnee_state').val(ui.item.st);
	});

	//tabindex control
	$('#CoParcel_weight').focus();

	$('form input, form select').off('keydown').on('keydown', function(evt){
		if(evt.keyCode == 9 && !evt.shiftKey){
			var j = false;
			switch($(this).attr('id')){
				case 'ExParcel_hbn':
					j = '#ExParcel_weight';
				break;
				case 'ExParcel_weight':
					j = '#cnor_name_ac';
				break;
				case 'cnor_tel_ac':
					j = '#cnee_name_ac';
				break;
				case 'cnee_sub_ac':
					j = '#items .item_name:first-of-type';
				break;
			}
			if(j){
				$(j).focus();
				return false;
			}
		}
	});
	
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>