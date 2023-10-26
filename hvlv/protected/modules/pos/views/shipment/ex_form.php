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
	if(!empty($org->extra['always_last_shipper'])){
		if(!empty($_COOKIE['last_cnor'])){
			$model->cnor->attributes = json_decode($_COOKIE['last_cnor'], true);
		}else{
			$s = $model::model()->find(['condition' => 'agent_id = :agent', 'order' => 't.id DESC', 'params' => [':agent' => $org->id]]);
			if($s)	$model->cnor = $s->cnor;
		}
		//if(!empty($_COOKIE['last_note'])) $model->note = $_COOKIE['last_note'];
	}
}
if(in_array(Yii::app()->user->org, [1, 839])){
	if(!empty($_COOKIE['last_cref'])) $model->cref = $_COOKIE['last_cref'];
}
if(empty($model->cnee)){
	$model->cnee = new Addr;
	$model->cnee->country = 'PR China';
}
?>

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

	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'cref'); ?>
		<?php echo $form->textField($model, 'cref', array('size'=>10,'maxlength'=>20, 'class' => 'form-control')); ?>
	</div>
	</div>
	
	<?php if(SellRate::hasExRate($org->id, 'V0')): ?>
	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<label>Service</label>
		<label>
		<?php echo CHtml::checkbox('serivce_pv', !empty($model->mdata['pv'])), $this->t(' Premium Express'); ?> 
		</label>
	</div>
	</div>
	<?php endif; ?>
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
	<div class="row">
	<div class="col col-sm-4 col-xs-6">
		<h3><?=$this->t('Consignee');?></h3>
	</div>
	<div class="col col-sm-8 col-xs-6" style="padding-top: 20px;">
		<a href="#" class="pasting-tog"><span class="glyphicon glyphicon-copy" style="font-size:1.5em;"></span></a>
	</div>
	</div>

	<div class="pasting-pane" style="display:none; position: absolute; opacity: 0.9; z-index:9; width: 100%; padding-right:25px;">
		<textarea id="addr_paste" class="form-control" style="width:100%; min-height: 280px; font-size: 1.5em; font-weight: bold; color: #14487E; box-shadow: 5px 5px 5px #999;" placeholder="<?=$this->t('Paste address here');?>"></textarea>
	</div>

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
	
	<div class="form-group">
		<label>Chinese ID</label>
		<?php echo CHtml::textField('Cnee[cnid_no]', $model->cnee->cnid_no, array('size'=>30, 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="form-group table-responsive">
	<table id="items" class="table table-striped table-bordered">
	<thead>
	<tr><th width="80">#</th><?php if(!SellRate::hasExRate($org->id, 'EC')): ?>
	<th><?=$this->t('Type');?> <span class="required">*</span></th>
	<?php endif; ?><th><?=$this->t('Item Name');?> <span class="required">*</span></th><th><?=$this->t('Qty');?>*</th></tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
	<tr><td><button class="moreitem btn btn-normal"><span class="glyphicon glyphicon-plus" style="color: #be3426; font-size: 1.2em; padding: 3px 10px;"></span></button></td><td>&nbsp;</td><th class="tright"><?=$this->t('Total');?>:</th><th id="tot_qty"></th></tr>
	</tfoot>
	</table>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($model, 'note'),
		$form->textArea($model, 'note', array('rows'=>2, 'cols' => 60, 'class' => 'form-control')); ?>
	</div>
	
	<?php if(!empty($org->extra['new_multi'])): ?>
	<div class="form-group">
		<label><?=$this->t('Multiple Connotes');?></label>
		<input type="number" class="form-control" max="10" min="1" name="multi" value="1" style="max-width: 60px" />
	</div>
	<?php endif; ?>

	<?php if(in_array(Yii::app()->user->org, [1, 839])): ?>
	<div class="form-group">
		<label><?=$this->t('Barcode');?></label>
		<?php echo $form->textField($model, 'hbn', array('size'=>30, 'class' => 'form-control')); ?>
	</div>
	<?php endif; ?>

	<div id="cost" style="text-align: right;"></div>
	<div class="form-group buttons">
	<?php if($model->isNewRecord): ?>
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t('Create');?></button> &nbsp;
		<a class="btn btn-info" href="<?=$this->createUrl('shipment/create');?>"><?=$this->t('Reset');?></a>
	<?php else: ?>
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t('Save');?></button> &nbsp;
		<a class="btn btn-info" href="<?=$this->createUrl('shipment/create', ['copy' => $model->id]);?>"><?=$this->t('Copy Shipment');?></a>
	<?php endif; ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	var pitems = <?=json_encode(empty($model->eitems)? '' : $model->eitems);?> || {type:[], g:[], q:[]};
	var ecr = <?=SellRate::hasExRate($org->id, 'EC')? 'true' : 'false';?>;
	var addItem = function(add){
		var tb = $('#items tbody');
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0){
			tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td class="rid">'+(id+1)+'</td>'+(ecr? '' : '<td><select class="typsel form-control" name="items[type]['+id+']" data-ov="'+(pitems.type && pitems.type[id]? pitems.type[id] : '')+'"><option value=""><?=$this->t("Select One");?></option><option value="B"><?=$this->t("Baby Formula");?></option><option value="M"><?=$this->t("Milk Powder");?></option><option value="O"><?=$this->t("Other");?></option></select></td>')+'<td><input type="hidden" class="item_pid" name="items[pid]['+id+']" value="'+(pitems.pid && pitems.pid[id]? pitems.pid[id] : 0)+'" /><input type="text" class="item_name'+(pitems.g && pitems.g[id] === false? ' error' : '')+' form-control" name="items[g]['+id+']" size="25" value="'+(pitems.g && pitems.g[id]? pitems.g[id] : '')+'" /></td><td><input type="text" class="item_qty'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="items[q]['+id+']" size="3" value="'+(pitems.q && pitems.q[id]? pitems.q[id]: '')+'" /></td></tr>');
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
	$('#cnee_name_ac, #ExParcel_weight, #cnor_name_ac, #cnor_tel_ac, #cnee_name_ac, #Cnee_tel, #Cnee_address, #cnee_state_ac, #cnee_city_ac, #Cnee_postcode').off('change').on('change', function(){
		if($(this).val() == '' || $(this).val() == 0){
			$(this).parents('.form-group').addClass('has-warning').removeClass('has-success');
		}else{
			$(this).parents('.form-group').removeClass('has-warning').addClass('has-success');
		}
	}).trigger('change');

	$('#items').off('change', '.item_qty,.item_wt,.item_tv,.item_tax').on('change', '.item_qty,.item_wt,.item_tv,.item_tax', calcTot);

	$('#items').off('keydown', 'input[type=text]').on('keydown', 'input[type=text]', function(evt){
			if(evt.which == 13){
				if(Number($(this).parents('tr').find('.rid').text()) == $('#items tbody tr').length) addItem(1);
				$(this).parents('tr').next().find(ecr? '.item_name' : '.typsel').focus();
				return false;
			}
	});
	$('.moreitem').off('click').on('click', function(e){
		addItem(1);
		$(this).parents('tr').next().find(ecr? '.item_name' : '.typsel').focus();
		e.preventDefault();
	});

	addItem(pitems.g? pitems.g.length : 1);

	//ac baseurl
	$('#cnee_city_ac, #cnee_suburb_ac').on('autocompletecreate', function(){
		//alert('ac');
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

	//product ac
	$('#items').off('focus', '.item_name').on('focus', '.item_name', function(){
		var t = $(this);
		var tp = $('select.typsel', t.parents('tr'));
		if(!t.data('ac_inited')){
			t.autocomplete({
				showAnim:'fold',
				minLength:1,
				delay:500,
				autoFocus:true,
				source : posApp.baseUrl+'shipment/prodSuggest',
				response: function(e, u){
					if(u.content.length == 1){
						var itm = u.content[0];
						$(this).parent().find('input.item_pid').val(itm.pid).data({'p': itm.price, 'r1': itm.r1, 'r2': itm.r2});
						$(this).val(itm.label);
						$(this).parents('td').next().find('.item_qty').focus();
					}
				},
				select:function(e, u){
					$(this).parent().find('input.item_pid').val(u.item.pid).data({'p': u.item.price, 'r1': u.item.r1, 'r2': u.item.r2});
					$('#cost').trigger('upCost');
				}
			}).on( "autocompletesearch", function(e, u){
				if(tp.val() == '') return false;
			});
			t.data({'ac_inited': true, 'src': posApp.baseUrl+'shipment/prodSuggest'});
		}
		$(this).autocomplete({source : $(this).data('src')+'?t='+tp.val()});
	});

	$('body').on('change', '#ExParcel_weight, .item_qty', function(){
		$('#cost').trigger('upCost');
	});

	//tabindex control
	$('#ExParcel_weight').focus();

	$('#items input').off('keydown').on('keydown', function(evt){
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

	$('a.pasting-tog').on('click', function(){
		var p = $('div.pasting-pane');
		if(p.is(':visible')){
			p.fadeOut(200);
		}else{
			p.fadeIn(200);
			$('textarea#addr_paste').focus();
		}
		return false;
	});

	var apt;
	$('textarea#addr_paste').on('keyup', function(){
		clearTimeout(apt);
		var me = $(this);
		var p = encodeURIComponent(me.val().trim());
		if(p == '') return false;
		apt = setTimeout(function(){
			$.get('pasteAddr.html?p='+p, function(r){
				var e = 0;
				if(r.name == ''){
					$('#notifc').notify({message: {html: '收件人姓名无法正确读取'}, type: 'danger'}).show();
					e++;
				}
				if(r.addr == '' || r.postcode == ''){
					$('#notifc').notify({message: {html: '地址信息无法正确读取'}, type: 'danger'}).show();
					e++;
				}
				if(r.tel == ''){
					$('#notifc').notify({message: {html: '收件人电话无法正确读取'}, type: 'danger'}).show();
					e++;
				}
					
				if(e < 2){
					for(var i in r){
						$('#'+'cnee_'+i+'_ac, #Cnee_'+i).val(r[i]);
					}
					if(e == 0) me.parent().fadeOut(200);
				}
				return false;
			}, 'json');
		}, 500);
	});

	var getPaste = function(e){
		var v = '';
		if (window.clipboardData && window.clipboardData.getData){
			v = window.clipboardData.getData('Text');
		} else if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData) {
			v = e.originalEvent.clipboardData.getData('text/plain');
		}
		return v;
	};

	$('input#cnor_name_ac').on('paste', function(e){
		var v = getPaste(e).replace(/[\s\.、。,，;；]+/g, ' ');
		var m = null;
		if(m = v.match(/[\d- \(\)]{9,20}/)){
			$('input#cnor_tel_ac').val(m[0].replace(/[^\d]+/g,''));
			v = v.replace(/^.+?(：|:)/g, '').replace(/[^\s]+(：|:)/g, ' ').replace(/[\d- \(\)]{9,20}/g, '').replace(/发件人|寄件人|发货人|电话/g, '');
			$('input#cnor_name_ac').val(v.trim());
			return false;
		}
	});

	$('input#cnee_name_ac').on('paste', function(e){
		var v = getPaste(e);
		if(v.match(/\d+/) || v.match(/\s+/g).length > 1){
			$('div.pasting-pane').fadeIn(200);
			$('textarea#addr_paste').val(v).focus().trigger('keyup');
			return false;
		}
	});

	$('#items').on('paste', 'input.item_name', function(e){
		var v = getPaste(e);
		var me = $(this);
		var ri = Number(me.parents('tr').find('td.rid').text()) - 1;
		clearTimeout(apt);
		apt = setTimeout(function(){
			$.get('pasteItem.html?p='+encodeURIComponent(v), function(r){
				if(r.q.length == 0) return;
				var k;
				for(var i in r.g){
					k = ri+Number(i);
					pitems.type[k] = r.type[i];
					pitems.q[k] = r.q[i];
					pitems.g[k] = r.g[i];
				}
				$('#items tbody tr').each(function(i){
					if(i >= ri) $(this).remove();
				});
				addItem(r.g.length);
			}, 'json');
		}, 500);
	});

	$('#ExParcel_hbn').on('keypress', function(e){
		if(e.which == 13){
			$('#shipment-form').trigger('submit');
			return false;
		}
	}).on('focus', function(){
		$(this).select();
	});

<?php if(SellRate::hasExRate($org->id, 'EC')):
	$ecr = SellRate::getExRate($org->id, 'EC');
?>
$('#cost').data('ec', <?=$ecr->perkg;?>).on('upCost', function(){
	var wt = Number($('#ExParcel_weight').val());
	var dc = $(this).data('ec') * wt;
	var t1 = 0;
	var t2 = 0;
	$('#items tbody tr').each(function(i){
		var p = $(this).find('.item_pid');
		var q = Number($(this).find('.item_qty').val());
		if(q == 0) return;
		t1 += p.data('p') * q * p.data('r1') / 100;
		t2 += p.data('p') * q * p.data('r2') / 100;
	});
	//console.log(t1);
	//console.log(t2);
	if(t1 < 50) t1 = 0;
	var tar = Math.min(t1, t2);
	tar = Math.round(tar / 4.75 * 10) / 10;
	var pkg = wt > 0? '<br/>($'+((dc+tar)/wt).toFixed(2)+'/kg)' : '';
	$(this).html('<p style="font-size:1.2em">Delivery Cost: $'+dc.toFixed(2)+'<br />Duty: $'+tar.toFixed(2)+'<br /><span style="font-size: 1.4em">Total: $'+(dc+tar).toFixed(2)+'</span>'+pkg+'</p>');
});
	
<?php endif; ?>
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>