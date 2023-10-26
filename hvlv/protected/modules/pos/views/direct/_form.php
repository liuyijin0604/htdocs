<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' =>[
		'data-bit' => '1',
	]
));

$u = User::model()->findByPk(Yii::app()->user->id);
if(empty($model->cnor)){
	$model->cnor = new Addr;
	$model->cnor->country = 'Australia';
	if(!empty($u->org->extra['always_last_shipper'])){
		$s = $model::model()->find(['condition' => 'agent_id = :agent', 'order' => 't.id DESC', 'params' => [':agent' => $u->org_id]]);
		if($s){
			$model->cnor = $s->cnor;
		}
	}
}
if(empty($model->cnee)){
	$model->cnee = new Addr;
	$model->cnee->country = 'PR China';
}
?>
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

	<div class="row">
	<div class="col col-sm-4 col-sx-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'state');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'cnee_state_ac',
					'source' => AppHelper::cnProvince(),
					'value' => empty($model->cnee->state)? '' : $model->cnee->state,
					'options' => array(
							'showAnim' => 'fold',
							'autoFocus' => true,
							'minLength' => 0,
							'delay' => 0,
							'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false;}',
							'response' => 'js:function(evt, ui){ if(ui.content.length == 0){ $(this).data("sid", 0); }; return false; }',
							'change' => 'js:function(evt, ui){ if($(this).data("sid") == 0) $(this).val(""); return false; }',
					),
					'htmlOptions' => array(
						'size' => '15',
						'name' => 'Cnee[state]',
						'class' => 'form-control',
					),
			));
		?>
	</div>
	</div>
	<div class="col col-sm-4 col-sx-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'city');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'cnee_city_ac',
					'sourceUrl' => array('shipment/cnCitySuggest'),
					'value' => empty($model->cnee->city)? '' : $model->cnee->city,
					'options' => array(
							'showAnim' => 'fold',
							'minLength' => 0,
							'delay' => 100,
							'autoFocus' => true,
							'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false;}',
							'response' => 'js:function(evt, ui){ if(ui.content.length == 0){ $(this).data("cid", 0); }; return false; }',
							'change' => 'js:function(evt, ui){ if($(this).data("cid") == 0) $(this).val(""); return false; }',
					),
					'htmlOptions' => array(
						'size' => '15',
						'name' => 'Cnee[city]',
						'class' => 'form-control',
					),
			));
		?>
	</div>
	</div>

	<div class="col col-sm-4 col-sx-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'suburb');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'cnee_suburb_ac',
					'sourceUrl' => array('shipment/cnSuburbSuggest'),
					'value' => empty($model->cnee->suburb)? '' : $model->cnee->suburb,
					'options' => array(
							'showAnim' => 'fold',
							'minLength' => 0,
							'delay' => 100,
							'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false; }',
					),
					'htmlOptions' => array(
						'size' => '15',
						'name' => 'Cnee[suburb]',
						'class' => 'form-control',
					),
			)); ?>
	</div>
	</div>
	</div>
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'address'),
		CHtml::textField('Cnee[address]', $model->cnee->address, array('size'=>40, 'class' => 'form-control')); ?>
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
	<tr><th>#</th><th><?=$this->t('Item Name');?> <span class="required">*</span></th><th><?=$this->t('Qty');?>*</th><th>Price</th></tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
	<tr><td colspan="2"><button class="moreitem btn btn-normal"><span class="glyphicon glyphicon-plus" style="color: #be3426; font-size: 1.2em; padding: 3px 10px;"></span></button></td><th class="tright"><?=$this->t('Total');?>:</th><th id="tot">&nbsp;</th></tr>
	</tfoot>
	</table>
	</div>
	<div class="row">
	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'insurance'); ?>
		<div class="input-group">
    		<div class="input-group-addon">$</div>
			<?php echo $form->textField($model,'insurance',array('size'=>6,'maxlength'=>10, 'class' => 'form-control')); ?>
		</div>
	</div>
	</div>

	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'cref'); ?>
		<?php echo $form->textField($model, 'cref', array('size'=>10,'maxlength'=>20, 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>
	
	<div class="form-group buttons">
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t($model->isNewRecord ? 'Create' : 'Save');?></button>
		<?php if($model->status == 10):?>
			<button type="button" id="btn_pay" class="btn btn-info btn-lg"><?=$this->t('Confirm Payment');?></button>
		<?php endif; ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	var pitems = <?=json_encode(empty($model->eitems)? '' : $model->eitems);?> || {};
	var prodb = <?=json_encode($model->getProdb($u->org));?>;
	var prodList = function(id){
		var t = '<select class="item_name form-control" name="items[g]['+id+']">'
		for(var i = 0; i < prodb.length; i++){
			t += '<option value="'+prodb[i].name+'" data-id="'+prodb[i].id+'">'+prodb[i].name+'</option>';
		}
		return t+'</select>';
	};

	var addItem = function(add){
		var tb = $('#items tbody');
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0){
			tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td class="rid">'+(id+1)+'</td><td>'+prodList(id)+'</td><td><input type="text" class="item_qty'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="items[q]['+id+']" size="3" value="'+(pitems.q && pitems.q[id]? pitems.q[id]: '')+'" /></td><td class="item_price"></td></tr>');
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
		var tot = 0;
		$('#items tbody tr').each(function(r){
			var pid = $('.item_name option:selected', this).data('id');
			var p = 0;
			var q = Number($('.item_qty', this).val());
			var oq = pitems.q && pitems.q[r]? pitems.q[r] : -1;
			if(q == oq){
				$('.item_price', this).html('<input type="hidden" name="items[v]['+r+']" value="'+pitems.v[r]+'" />$'+pitems.v[r]);
				tot += Number(pitems.v[r]);
			}else{
				for(var i = 0; i < prodb.length; i++){
					if(prodb[i].id == pid){
						p = Number(prodb[i].price);
						if(q < prodb[i].min){
							$('#notifc').notify({message: {html: '数量最少'+prodb[i].min}, type: 'danger'}).show();
							$('.item_qty', this).parent().addClass('has-error');
						}else if(q % prodb[i].step > 0){
							$('#notifc').notify({message: {html: '数量必须是'+prodb[i].step+'的倍数'}, type: 'danger'}).show();
							$('.item_qty', this).parent().addClass('has-error');
						}else{
							$('.item_qty', this).parent().removeClass('has-error');
						}
					}
				}
				if($.inArray($('#cnee_state_ac').val(), ['上海市','浙江省','江苏省','安徽省']) == -1) p += 1;
				p = q * p;
				$('.item_price', this).html('<input type="hidden" name="items[v]['+r+']" value="'+p+'" />$'+p);
				tot += p;
			}
		});
		
		$('#tot').text('$'+tot);
	};
	
	//required fields
	$('#cnee_name_ac, #ExParcel_weight, #cnor_name_ac, #cnor_tel_ac, #cnee_name_ac, #Cnee_tel, #Cnee_address, #cnee_state_ac, #cnee_city_ac, #Cnee_postcode').off('change').on('change', function(){
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
				$(this).parents('tr').next().find('.typsel').focus();
				return false;
			}
	});
	$('.moreitem').off('click').on('click', function(e){
		addItem(1);
		$(this).parents('tr').next().find('.typsel').focus();
		e.preventDefault();
	});

	addItem(pitems.g? pitems.g.length : 1);

	//ac baseurl
	$('#cnee_city_ac, #cnee_suburb_ac').on('autocompletecreate', function(){
		alert('ac');
		$(this).data('src', $(this).autocomplete('option', 'source'));
	});

	var cnee_state = $('#cnee_state_ac');

	//cnor name/mobile ac
	$('#cnor_name_ac, #cnor_tel_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		var mfs = ['address', 'state', 'postcode', 'country', 'email'];
		for(var i=0; i < mfs.length; i++) $('#Cnor_'+mfs[i]).val(ui.item[mfs[i]]);
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
		var mfs = ['tel', 'address', 'postcode', 'country', 'email'];
		for(var i=0; i < mfs.length; i++) $('#Cnee_'+mfs[i]).val(ui.item[mfs[i]]);
		cnee_state.val(ui.item.state);
		$('#cnee_city_ac').val(ui.item.city);
		$("#cnee_suburb_ac").val(ui.item.suburb);
		$("#Cnee_address").focus();
		$('#notifc').notify({message: {text: "Consignee atuofill"}}).show();
	});

	//cnee state
	cnee_state.off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value).data('sid', ui.item.id);
		if(ui.item.ocid){
			$('#cnee_city_ac').val(ui.item.value).data('cid', ui.item.ocid).focus();
			$('#Cnee_postcode').val(ui.item.oczip);
		}
	}).on('focus',function(){
		$(this).autocomplete('search', $(this).val());
	});

	var cneeSid = function(){
		var v = cnee_state.val();
		if(v != ''){
			var s = cnee_state.autocomplete('option', 'source');
			for(i in s){
				if(s[i].value == v) cnee_state.data('sid', s[i].id);
			}
		}
	};

	//cnee city
	$('#cnee_city_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value).data('cid', ui.item.id);
		if(ui.item.aname){
			$('#cnee_suburb_ac').val(ui.item.aname);
		}
		if(ui.item.zip){
			$('#Cnee_postcode').val(ui.item.zip);
		}
	}).on('focus', function(){
		cneeSid();
		if(!$(this).data('src')) $(this).data('src', $(this).autocomplete('option', 'source'));
		$(this).autocomplete({source : $(this).data('src')+'?sid='+cnee_state.data('sid')}).autocomplete('search', $(this).val());
	}).on( "autocompletesearch", function(e, u){
		if(cnee_state.val() == '') return false;
	});

	//cnee suburb
	$('#cnee_suburb_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value);
		$('#Cnee_postcode').val(ui.item.zip);
	}).on('focus', function(){
		if(!$(this).data('src')) $(this).data('src', $(this).autocomplete('option', 'source'));
		$(this).autocomplete({source : $(this).data('src')+'?cid='+$('#cnee_city_ac').data('cid')}).autocomplete('search', $(this).val());
	}).on( "autocompletesearch", function(e, u){
		if($('#cnee_city_ac').val() == '') return false;
	});

	//tabindex control
	$('#ExParcel_weight').focus();

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
				case 'Cnee_address':
					j = '#items .typsel:first-of-type';
				break;
			}
			if(j){
				$(j).focus();
				return false;
			}
		}
	});
	
	$('#btn_pay').on('click', function(){
		var t = $(this);
		if(window.confirm('Please confirm '+$('#tot').text()+' has been received?')){
			$.post(window.location.href, {pay: 1}, function(){
				t.hide();
				$('#notifc').notify({message: {html: '确认成功'}}).show();
			});
		}
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>