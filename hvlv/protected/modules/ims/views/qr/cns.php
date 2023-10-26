<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' =>[
		'data-bit' => '0',
	]
));

if(empty($model->cnor)){
	$model->cnor = new Addr;
	$model->cnor->country = 'Australia';
}
if(empty($model->cnee)){
	$model->cnee = new Addr;
	$model->cnee->country = 'PR China';
}
?>
	<h2><?=$org->name;?></h2>
	<div class="row">
	<div class="col col-md-6 col-sm-12">
	<h3><?=$this->t('Shipper');?></h3>
	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'name'),
		CHtml::textField('Cnor[name]', $model->cnor->name, array('size'=>20, 'class' => 'form-control'));?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'tel'),
		CHtml::textField('Cnor[tel]', $model->cnor->tel, array('size'=>20, 'class' => 'form-control'));?>
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
				'name' => 'Cnor_sub_ac',
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
	<div class="row">
		<h3 class="pull-left" style="margin: 20px 15px 10px 15px;"><?=$this->t('Consignee');?></h3>
		<div class="dropdown" id="cnee_fill" style="display: none; margin-top: 15px;">
  <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
	<span class="glyphicon glyphicon-flash"></span> <?=$this->t('Quick Fill');?>
	<span class="caret"></span>
  </button>
  <ul class="dropdown-menu" id="cnees_list" aria-labelledby="dropdownMenu1"></ul>
</div>
	</div>

	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'name'),
		CHtml::textField('Cnee[name]', $model->cnee->name, array('size'=>15, 'class' => 'form-control'));?>
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
	<div class="col col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'state');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'Cnee_state_ac',
					'source' => 'js:function(q,r){ var t = q.term.replace(/\s+/, ""), d = '.json_encode(AppHelper::cnProvince()).', m = new RegExp($.ui.autocomplete.escapeRegex(t), "i"); r($.map(d, function(el){ if(m.test(el.label)){ return el; } })); }',
					'value' => empty($model->cnee->state)? '' : $model->cnee->state,
					'options' => array(
							'showAnim' => 'fold',
							'autoFocus' => true,
							'minLength' => 0,
							'delay' => 0,
							'select' => 'js:function(evt, ui){ $(this).blur().trigger("ac_after_select", ui); return false;}',
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
	<div class="col col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'city');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'Cnee_city_ac',
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

	<div class="col col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'suburb');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'Cnee_suburb_ac',
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
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'postcode'),
		CHtml::textField('Cnee[postcode]', $model->cnee->postcode, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
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
	<table id="items" class="table table-striped table-bordered" style="min-width:380px">
	<thead>
	<tr><th>#</th><th><?=$this->t('Type');?> <span class="required">*</span></th><th style="min-width:120px"><?=$this->t('Item Name');?> <span class="required">*</span></th><th style="min-width:60px"><?=$this->t('Qty');?>*</th><th style="min-width:70px"><?=$this->t('Value');?></th></tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
	<tr><td colspan="2"><button class="moreitem btn btn-normal"><span class="glyphicon glyphicon-plus" style="color: #be3426; font-size: 1.2em; padding: 3px 10px;"></span></button></td><th class="tright"><?=$this->t('Total');?>:</th><th id="tot_qty"></th><th id="tot_value">&nbsp;</th></tr>
	</tfoot>
	</table>
	</div>

	<div class="row">
	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'weight');?>
		<div class="input-group">
			<?php echo $form->textField($model,'weight',array('size'=>10,'maxlength'=>10, 'class' => 'form-control', 'id' => 'ExParcel_weight')); ?>
			<div class="input-group-addon">kg</div>
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
	</div>

	</div>

	<div class="form-group">
		<label><?=$this->t('Captcha');?></label>
		<div class="row">
		<div class="col col-sm-3 col-xs-6">
			<input type="text" class="form-control input-lg" id="vvc" name="vvc" autocomplete="off" />
		</div>
		<div class="col col-sm-3 col-xs-6">
			<img style="cursor:pointer;" alt="CAPTCHA" title="Click to reload" id="lf_capcha" src="<?=$this->createUrl('site/captcha').'?'.time();?>" />
		</div>
		</div>
	</div>
	
	<div class="form-group buttons">
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t('Create');?></button>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	var pitems = {};
	var addItem = function(add){
		var tb = $('#items tbody');
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0){
			tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td class="rid">'+(id+1)+'</td><td><select class="typsel form-control" name="items[type]['+id+']" data-ov="'+(pitems.type && pitems.type[id]? pitems.type[id] : '')+'"><option value=""><?=$this->t("Select One");?></option><option value="B"><?=$this->t("Baby Formula");?></option><option value="M"><?=$this->t("Milk Powder");?></option><option value="O"><?=$this->t("Other");?></option></select></td><td><input type="text" class="item_name'+(pitems.g && pitems.g[id] === false? ' error' : '')+' form-control" name="items[g]['+id+']" size="25" value="'+(pitems.g && pitems.g[id]? pitems.g[id] : '')+'" /></td><td><input type="text" class="item_qty'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="items[q]['+id+']" size="3" value="'+(pitems.q && pitems.q[id]? pitems.q[id]: '')+'" /></td><td><input type="text" class="item_tv'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="items[v]['+id+']" size="6" value="'+(pitems.v && pitems.v[id]? pitems.v[id]: '')+'" /></td></tr>');
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

	posApp.pleaseWait = '<?=$this->t("Please Wait");?>';
	
	//required fields
	$('#Cnee_name, #ExParcel_weight, #Cnor_name, #Cnor_tel, #Cnee_tel, #Cnee_address, #Cnee_state_ac, #Cnee_city_ac, #Cnee_postcode, #vvc').off('change').on('change', function(){
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
	$('#Cnee_city_ac, #Cnee_suburb_ac').on('autocompletecreate', function(){
		$(this).data('src', $(this).autocomplete('option', 'source'));
	});

	var cnee_state = $('#Cnee_state_ac');

	//cnor name/mobile ac

	//cnor suburb ac
	$('#Cnor_sub_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$("#Cnor_postcode").val(ui.item.pc);
		$('#Cnor_state').val(ui.item.st);
	});

	//cnee name/mobile ac

	//cnee state
	cnee_state.off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val('').val(ui.item.value).data('sid', ui.item.id);
		if(ui.item.ocid){
			$('#Cnee_city_ac').val(ui.item.value).data('cid', ui.item.ocid).focus();
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
	$('#Cnee_city_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value).data('cid', ui.item.id);
		if(ui.item.aname){
			$('#Cnee_suburb_ac').val(ui.item.aname);
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
	$('#Cnee_suburb_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value);
		$('#Cnee_postcode').val(ui.item.zip);
	}).on('focus', function(){
		if(!$(this).data('src')) $(this).data('src', $(this).autocomplete('option', 'source'));
		$(this).autocomplete({source : $(this).data('src')+'?cid='+$('#Cnee_city_ac').data('cid')}).autocomplete('search', $(this).val());
	}).on( "autocompletesearch", function(e, u){
		if($('#Cnee_city_ac').val() == '') return false;
	});

	//product ac
	$('#items').off('focus', '.item_name').on('focus', '.item_name', function(){
		var t = $(this);
		var tp = $('select.typsel', t.parents('tr'));
		if(!t.data('ac_inited')){
			t.autocomplete({
				'showAnim':'fold',
				'minLength':1,
				'delay':200,
				'autoFocus':true,
				'source' : posApp.baseUrl+'shipment/prodSuggest',
				'messages': {
					'noResults': '',
					'results': function() {}
				}
			}).on( "autocompletesearch", function(e, u){
				if(tp.val() == '') return false;
			});
			t.data({'ac_inited': true, 'src': posApp.baseUrl+'shipment/prodSuggest'});
		}
		$(this).autocomplete({source : $(this).data('src')+'?t='+tp.val()});
	});

	//tabindex control
	$('#Cnor_name').focus();

	$('form input, form select').off('keydown').on('keydown', function(evt){
		if(evt.keyCode == 9 && !evt.shiftKey){
			var j = false;
			switch($(this).attr('id')){
				case 'ExParcel_hbn':
					j = '#ExParcel_weight';
				break;
				break;
				case 'Cnor_tel':
					j = '#Cnee_name';
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
	
	var rpms = Rhaboo.persistent("myshipments");
	
	//preserve
	$('#shipment-form').on('success', function(evt, r){
		rpms.write('Cnor', {name: $('#Cnor_name').val(), tel: $('#Cnor_tel').val(), address: $('#Cnor_address').val(), 'sub_ac': $('#Cnor_sub_ac').val(), 'state': $('#Cnor_state').val(), 'postcode': $('#Cnor_postcode').val(), 'country': $('#Cnor_country').val(), 'email': $('#Cnor_email').val()});

		var cnees = rpms.Cnees || [];
		var ncnee = {name: $('#Cnee_name').val(), tel: $('#Cnee_tel').val(), address: $('#Cnee_address').val(), 'suburb_ac': $('#Cnee_suburb_ac').val(), 'state_ac': $('#Cnee_state_ac').val(), 'city_ac': $('#Cnee_city_ac').val(),'postcode': $('#Cnee_postcode').val(), 'country': $('#Cnee_country').val(), 'email': $('#Cnee_email').val()};

		for(var i = 0; i < cnees.length; i++){
			if(cnees[i].name == ncnee.name && cnees[i].tel == ncnee.tel){
				cnees.splice(i, 1);
				break;
			}
		}
		cnees.unshift(ncnee);
		if(cnees.length > 30) cnees.splice(30, cnees.length - 30);
		rpms.write('Cnees', cnees);

		var connotes = rpms.Connotes || [];
		connotes.unshift({id: r.id, hbn: r.hbn});
		if(connotes.length > 50) connotes.splice(50, connotes.length - 50);
		rpms.write('Connotes', connotes);

		posApp.toPage('<?=substr($this->createUrl("client/done"),0,-5);?>/' + r.id + '?c=' + r.hbn, false);
	});

	//read local data
	if(rpms.hasOwnProperty('Cnor')){
		for(var k in rpms.Cnor){
			$('#Cnor_'+k).val(rpms.Cnor[k]);
		}
	}

	//cnee selection
	if(rpms.hasOwnProperty('Cnees')){
		$('#cnee_fill').fadeIn();
		for(var i = 0; i < rpms.Cnees.length; i++){
			c = rpms.Cnees[i];
			$('#cnees_list').append('<li><a class="cnee_qf" href="#" data-idx="'+i+'">'+c.name+' '+c.tel+'</a></li>');
		}
		$('a.cnee_qf').on('click', function(e){
			for(var k in rpms.Cnees[$(this).data('idx')]){
				$('#Cnee_'+k).val(rpms.Cnees[$(this).data('idx')][k]);
			}
			e.preventDefault();
		});
	}
	
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>