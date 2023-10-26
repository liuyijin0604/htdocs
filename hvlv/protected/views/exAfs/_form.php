<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ex-parcel-form',
	'enableAjaxValidation'=>false,
));

$can_edit = $model->status < 20 || Acl::hasAccess('B:Export/UpdateParcelAfterConsolidation');
if(empty($model->cnor)){
	$model->cnor = new Addr;
	$model->cnor->country = 'Australia';
}
if(empty($model->cnee)){
	$model->cnee = new Addr;
	$model->cnee->country = 'PR China';
}
$was = $model->getWarnings(false);
if(!empty($was)){
	echo '<div class="row">',
	CHtml::label('Warnings','');
	foreach($was as $b=>$w){
		echo '<a href="'.$this->createUrl('exAfs/removeWarn', array('id' => $model->id, 'b' => $b)).'" class="remove_warn" title="Click to remove"><span class="warn">'.$w.'</span></a> &nbsp;';
	}
	echo '</div>';
}
?>
	<?php if(Acl::hasAccess('C:org/exAgentSuggest')): ?>
	<div class="row rowcol">
		<?php echo $form->labelEx($model,'agent_id'),
			$form->hiddenField($model,'agent_id', array('data-ov' => $model->agent_id));
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/exAgentSuggest'),
				'value' => empty($model->agent)? '' : $model->agent->name,
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'autoFocus' => true,
						'select' => 'js:function(evt, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(evt, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'class' => 'required',
					'size' => '25',
				),
		));
		?>

	</div>
	<?php endif; ?>


	<?php if($model->isNewRecord): ?>
	<div class="row rowcol">
		<?php echo $form->labelEx($model,'hbn'),
		$form->textField($model,'hbn',array('size'=>15,'maxlength'=>50)); ?>
	</div>
	<?php endif; ?>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'ref'),
		$form->textField($model,'ref',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'cref'); ?>
		<?php echo $form->textField($model, 'cref', array('size'=>15,'maxlength'=>50, 'class' => 'form-control')); ?>
	</div>

	<?php if(!$model->isNewRecord && Acl::hasAccess('B:Export/StatusOverride')): ?>
	<div class="row rowcol">
		<?php echo $form->labelEx($model,'status'),
		$form->dropDownList($model, 'status', ExAfs::$states, array('disabled' => 'disabled')); ?>
		 <div style="background-position:-240px -416px" class="icon"></div>
	</div>
	<?php endif; ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'pkg'),
		$form->textField($model,'pkg', array('size'=>6,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'weight'),
		$form->textField($model,'weight', array('size'=>6,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHTML::label('Dclr Wt.','dcwt'),
		CHtml::textField('meta[dcwt]', @$model->mdata['dcwt'], array('size'=>6,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'currency'),
		$form->dropdownList($model,'currency', Invoice::$currencies); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'value'),
		$form->textField($model,'value',array('size'=>8,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'tariff'),
		$form->textField($model,'tariff',array('size'=>6,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'insurance'),
		$form->textField($model,'insurance',array('size'=>6,'maxlength'=>10)); ?>
	</div>

	<div class="row">
	<div class="col" style="margin-right: 25px">
	<h3><?=$this->t('Shipper');?></h3>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnor, 'name');
		$acname21 = empty($_GET["tabid"])? 'cnor_name_ac' : $_GET["tabid"].'_cnor_name_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname21,
				'sourceUrl' => array('exAfs/cnorSuggest'),
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
				),
		));
		?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnor, 'tel');
		$acname22 = empty($_GET["tabid"])? 'cnor_tel_ac' : $_GET["tabid"].'_cnor_tel_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname22,
				'sourceUrl' => array('exParcel/cnorSuggest'),
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
				),
		));
		?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnor, 'address'),
		CHtml::textField('Cnor[address]', $model->cnor->address, array('size'=>40));?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnor, 'suburb');
		$acname23 = empty($_GET["tabid"])? 'cnor_sub_ac' : $_GET["tabid"].'_cnor_sub_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname23,
				'sourceUrl' => array('postcode/suggest'),
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
				),
		));
		?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnor, 'state'),
		CHtml::dropDownList('Cnor[state]', $model->cnor->state, array('ACT' => 'ACT - Australia Capital Territory', 'NSW' => 'NSW - New South Wales', 'NT' => 'NT - Northern Territory', 'QLD' => 'QLD - Queensland', 'SA' => 'SA - South Australia', 'TAS' => 'TAS - Tasmania', 'VIC' => 'VIC - Victoria', 'WA' => 'WA - Western Australia'), array('empty' => $this->t('Select One'), 'style' => 'width:100px')); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnor, 'postcode'),
		CHtml::textField('Cnor[postcode]', $model->cnor->postcode, array('size'=>15)); ?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnor, 'country'),
		CHtml::textField('Cnor[country]', $model->cnor->country, array('size'=>15)); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnor, 'email'),
		CHtml::textField('Cnor[email]', $model->cnor->email, array('size'=>30)); ?>
	</div>
	</div>
	<div class="col">
	<h3><?=$this->t('Consignee');?></h3>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnee, 'name');
			$acname31 = empty($_GET["tabid"])? 'cnee_name_ac' : $_GET["tabid"].'_cnee_name_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => $acname31,
					'sourceUrl' => $this->createUrl('exAfs/cneeSuggest', empty($model->cnee->id)? array() :array('id' => $model->cnee->id)),
					'value' => empty($model->cnee->name)? '' : $model->cnee->name,
					'options' => array(
							'showAnim' => 'fold',
							'minLength' => 2,
							'delay' => 200,
							'autoFocus' => false,
							'select' => 'js:function(evt, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
							'change' => 'js:function(evt, ui){ return false; }',
					),
					'htmlOptions' => array(
						'size' => '15',
						'name' => 'Cnee[name]',
					),
			));
		?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnee, 'tel'),
		CHtml::textField('Cnee[tel]', $model->cnee->tel, array('size'=>15)); ?>
	</div>
	<div class="row rowcol">
		<?php 
			if(!empty($model->cnee->cnid) && $model->cnee->cnid->joint > 0){
				echo '<a class="jqm_link" href="'.$this->createUrl('cnID/photo', array('id' => $model->cnee->cnid_id)).'" style="position:absolute; margin-left: 90px; margin-top: -4px;"><div class="icon" style="background-position:-96px -768px"></div> View</a>';
			}elseif($model->cnee->id > 0){
				echo '<a class="jqm_link" href="'.$this->createUrl('cnID/attach', array('id' => $model->cnee->id)).'" style="position:absolute; margin-left: 90px; margin-top: -4px;"><div class="icon" style="background-position:-112px -768px"></div> Upload</a>';
			}
			echo $form->labelEx($model->cnee, 'cnid_id');
			echo CHtml::hiddenField('Cnee[cnid_id]', $model->cnee->cnid_id);
			$acname32 = empty($_GET["tabid"])? 'cnee_cnid_ac' : $_GET["tabid"].'_cnee_cnid_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => $acname32,
					'sourceUrl' => array('cnID/suggest'),
					'value' => ($model->cnee->cnid) ? $model->cnee->cnid->no : '',
					'options' => array(
							'showAnim' => 'fold',
							'minLength' => 2,
							'delay' => 200,
							'autoFocus' => false,
							'select' => 'js:function(evt, ui){ $(this).val(ui.item["no"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
							'change' => 'js:function(evt, ui){ if(ui.item == null && $(this).prevAll("input[type=hidden]").val() != $(this).prevAll("input[type=hidden]").data("ov")) $(this).prevAll("input[type=hidden]").val(""); return false; }',
							'response' => 'js:function(evt, ui){ if(ui.content.length == 1 && ui.content[0].exm){	myApp.notice("ID Exact Match");	$(this).val(ui.content[0].no); $(this).prevAll("input[type=hidden]").val(ui.content[0].value).data("ov", ui.content[0].value); $(this).trigger("ac_after_select", null); }; return false; }',
							'focus' => 'js:function(evt,ui){ $(this).trigger("show_id_photo", ui); }',
					),
					'htmlOptions' => array(
						'size' => '20',
					),
			));
			
		?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnee, 'state');
			$acname33 = empty($_GET["tabid"])? 'cnee_state_ac' : $_GET["tabid"].'_cnee_state_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => $acname33,
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
					),
			));
		?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnee, 'city');
			$acname34 = empty($_GET["tabid"])? 'cnee_city_ac' : $_GET["tabid"].'_cnee_city_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => $acname34,
					'sourceUrl' => array('cnZip/suggestCity'),
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
					),
			));
		?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnee, 'suburb');
			$acname35 = empty($_GET["tabid"])? 'cnee_suburb_ac' : $_GET["tabid"].'_cnee_suburb_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => $acname35,
					'sourceUrl' => array('cnZip/suggestSuburb'),
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
					),
			)); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnee, 'address'),
		CHtml::textField('Cnee[address]', $model->cnee->address, array('size'=>40)); ?>
	</div>
	
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnee, 'postcode'),
		CHtml::textField('Cnee[postcode]', $model->cnee->postcode, array('size'=>15)); ?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnee, 'country'),
		CHtml::textField('Cnee[country]', $model->cnee->country, array('size'=>15)); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnee, 'email'),
		CHtml::textField('Cnee[email]', $model->cnee->email, array('size'=>30)); ?>
	</div>
	</div>
	</div>

	<div class="row">
	<table id="items">
	<thead>
	<tr><th>#</th><th><?=$this->t('Item Name');?> <span class="required">*</span></th><th><?=$this->t('品名');?></th><th><?=$this->t('Qty');?> <span class="required">*</span></th><th><?=$this->t('Value');?></th><th><?=$this->t('HS Code');?></th></tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
	<tr><td colspan="2">&nbsp;</td><th class="tright"><?=$this->t('Total');?>:</th><th id="tot_qty"></th><th id="tot_value">&nbsp;</th><th>&nbsp;</th></tr>
	</tfoot>
	</table>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'note'),
		$form->textArea($model,'note'); ?>
	</div>
	
	<?php if($can_edit): ?>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>
	<?php endif; ?>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var tid = '<?=$_GET["tabid"];?>';
	var tab = $('#'+tid);
	var panel = tab.data('panel');
	
	var pitems = <?=json_encode(empty($model->eitems)? '' : $model->eitems);?> || {};
	var addItem = function(add){
		var tb = $('#items tbody', panel);
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0){
			tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td class="rid">'+(id+1)+'</td><td><input type="text" class="item_name'+(pitems.g && pitems.g[id] === false? ' error' : '')+'" name="items[g]['+id+']" size="35" value="'+(pitems.g && pitems.g[id]? pitems.g[id] : '')+'" /></td><td><input type="text" class="item_name_zh" name="items[g_zh]['+id+']" size="25" value="'+(pitems.g_zh && pitems.g_zh[id]? pitems.g_zh[id] : '')+'" /></td><td><input type="text" class="item_qty'+(pitems.q && pitems.q[id] === false? ' error' : '')+'" name="items[q]['+id+']" size="3" value="'+(pitems.q && pitems.q[id]? pitems.q[id]: '')+'" /></td><td><input type="text" class="item_tv'+(pitems.v && pitems.v[id] === false? ' error' : '')+'" name="items[v]['+id+']" size="6" value="'+(pitems.v && pitems.v[id]? pitems.v[id]: '')+'" /></td><td><input type="text" name="items[hs]['+id+']" size="15" value="'+(pitems.hs && pitems.hs[id]? pitems.hs[id] : '')+'" /></td></tr>');
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
		var tqty = 0, twt = 0, tval = 0, ttax = 0;

		$('#items tbody tr', panel).each(function(){
			tval += Number($('.item_tv', this).val()) || 0;
			twt += Number($('.item_wt', this).val()) || 0;
			tqty += Number($('.item_qty', this).val()) || 0;
			ttax += Number($('.item_tax', this).val()) || 0;
		});
		
		$('#tot_qty', panel).text(tqty);
		$('#tot_weight', panel).text(Math.round(twt*100)/100);
		$('#tot_value', panel).text(Math.round(tval*100)/100);
		$('#tot_tax', panel).text(Math.round(ttax*100)/100);
	};
	
	//required fields
	$('#'+tid+'_agent_ac, #ExAfs_hbn, #ExAfs_weight, #<?=$acname21;?>, #'+tid+'_cnee_name_ac, #Cnee_tel, #'+tid+'_cnee_cnid_ac, #Cnee_address, #<?=$acname33;?>, #<?=$acname34;?>', panel).off('change').on('change', function(){
		if($(this).val() == ''){
			$(this).addClass('warn');
		}else{
			$(this).removeClass('warn');
		}
	}).trigger('change');

	$('#items', panel).off('change', '.item_qty,.item_wt,.item_tv,.item_tax').on('change', '.item_qty,.item_wt,.item_tv,.item_tax', calcTot);

	$('#items', panel).off('keydown', 'input[type=text]').on('keydown', 'input[type=text]', function(evt){
			if(evt.keyCode == 13){
				if(Number($(this).parents('tr').find('.rid').text()) == $('#items tbody tr', panel).length) addItem(1);
				$(this).parents('tr').next().find('.typsel').focus();
				return false;
			}
	});

	addItem(pitems.g? pitems.g.length : 1);

	//ac baseurl
	$('#<?=$acname21;?>, #<?=$acname22;?>, #<?=$acname32;?>, #<?=$acname34;?>, #<?=$acname35;?>', panel).off('autocompletecreate').on('autocompletecreate', function(){
		$(this).data('src', $(this).autocomplete('option', 'source'));
	});
	var cnee_state = $('#<?=$acname33;?>', panel);

	//cnor name/mobile ac
	$('#<?=$acname21;?>, #<?=$acname22;?>', panel).off('ac_after_select').on('ac_after_select', function(evt, ui){
		var mfs = ['address', 'state', 'postcode', 'country', 'email'];
		for(var i=0; i < mfs.length; i++) $('#Cnor_'+mfs[i], panel).val(ui.item[mfs[i]]);
		$('#<?=$acname21;?>', panel).val(ui.item.name);
		$('#<?=$acname22;?>', panel).val(ui.item.tel);
		$('#<?=$acname23;?>', panel).val(ui.item.suburb);
	}).on('focus', function(){
		$(this).autocomplete({source : $(this).data('src')+'?agt='+$('#ExAfs_agent_id', panel).val()});
	}).on( "autocompletesearch", function(e, u){
		if($('#ExAfs_agent_id', panel).val() == '') return false;
	});

	//cnor suburb ac
	$('#<?=$acname23;?>', panel).off('ac_after_select').on('ac_after_select', function(evt, ui){
		$("#Cnor_postcode", panel).val(ui.item.pc);
		$('#Cnor_state', panel).val(ui.item.st);
	});

	//cnee name ac
	$('#<?=$acname31;?>', panel).off('ac_after_select').on('ac_after_select', function(evt, ui){
		var mfs = ['tel', 'address', 'postcode', 'country', 'email'];
		for(var i=0; i < mfs.length; i++) $('#Cnee_'+mfs[i], panel).val(ui.item[mfs[i]]);
		$("#Cnee_cnid_id", panel).val(ui.item.cnid).data('ov', ui.item.cnid);
		$('#<?=$acname32;?>', panel).val(ui.item.cnid_no);
		cnee_state.val(ui.item.state);
		$('#<?=$acname34;?>', panel).val(ui.item.city);
		$("#<?=$acname35;?>", panel).val(ui.item.suburb);
		$("#Cnee_address", panel).focus();
		myApp.notice("Consignee atuofill");
	});

	//id ac
	var sipt;
	$('#<?=$acname32;?>', panel).on('focus', function(){
		$(this).autocomplete({source : $(this).data('src')+'?name='+$('#<?=$acname31;?>', panel).val()+'&tel='+$('#Cnee_tel', panel).val()});
		if($(this).val() == ''){ //suggest based on name
			$(this).autocomplete('search', 'name');
		}
	}).on('ac_after_select', function(evt, ui){
		var cn = $("#Cnee_name", panel);
		if(cn.val() == "" && ui != null) cn.val(ui.item.name);
		cnee_state.focus();
		clearTimeout(sipt);
		$('.id_photo', panel).remove();
	}).on('show_id_photo', function(evt,ui){
		clearTimeout(sipt);
		$('.id_photo', panel).remove();
		var pp = $(panel).position();
		sipt = setTimeout(function(){
			$(panel).append($('<div class="id_photo" style="position:absolute;top:20px;left: 0;"><img src="cnID/photo/'+ui.item.value+'?front=1" width="600"></div>').css({top: pp.top, left: pp.left}));
		}, 750);
	}).on('blur', function(){
		clearTimeout(sipt);
		$('.id_photo', panel).remove();
	});

	//cnee state
	cnee_state.off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value).data('sid', ui.item.id);
		if(ui.item.ocid){
			$('#<?=$acname34;?>', panel).val(ui.item.value).data('cid', ui.item.ocid).focus();
			$('#Cnee_postcode', panel).val(ui.item.oczip);
		}
	}).on('focus',function(){
		$(this).autocomplete('search', $(this).val());
	});
	var cneeSid = function(){
		var v = cnee_state.val();
		if(v != ''){
			var s = cnee_state.autocomplete('option', 'source');
			for(var i = 0; i < s.length; i++){
				if(s[i].value == v) cnee_state.data('sid', s[i].id);
			}
		}
	};

	//cnee city
	$('#<?=$acname34;?>', panel).off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value).data('cid', ui.item.id);
		if(ui.item.aname){
			$('#<?=$acname35;?>', panel).val(ui.item.aname);
		}
		if(ui.item.zip){
			$('#Cnee_postcode', panel).val(ui.item.zip);
		}
	}).on('focus', function(){
		cneeSid();
		$(this).autocomplete({source : $(this).data('src')+'?sid='+cnee_state.data('sid')}).autocomplete('search', $(this).val());
	}).on( "autocompletesearch", function(e, u){
		if(cnee_state.val() == '') return false;
	});

	//cnee suburb
	$('#<?=$acname35;?>', panel).off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value);
		$('#Cnee_postcode', panel).val(ui.item.zip);
	}).on('focus', function(){
		$(this).autocomplete({source : $(this).data('src')+'?cid='+$('#<?=$acname34;?>', panel).data('cid')}).autocomplete('search', $(this).val());
	}).on( "autocompletesearch", function(e, u){
		if($('#<?=$acname34;?>', panel).val() == '') return false;
	});

	//product ac
	$('#items', panel).off('focus', '.item_name').on('focus', '.item_name', function(){
		var t = $(this);
		var tp = $('select.typsel', t.parents('tr'));
		if(!t.data('ac_inited')){
			t.autocomplete({
				'showAnim':'fold',
				'minLength':1,
				'delay':200,
				'autoFocus':true,
				'source':'exprod/suggest'
			}).on( "autocompletesearch", function(e, u){
				if(tp.val() == '') return false;
			});
			t.data({'ac_inited': true, 'src': 'exprod/suggest'});
		}
		$(this).autocomplete({source : $(this).data('src')+'?t='+tp.val()});
	});

	//bind reload_tab
	tab.off('reload_tab').on('reload_tab', function(){
		var t = $('.ui-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	});

	//tabindex control
	$('#'+tid+'_agent_ac', panel).focus();

	$('form input, form select', panel).off('keydown').on('keydown', function(evt){
		if(evt.keyCode == 9 && !evt.shiftKey){
			var j = false;
			switch($(this).attr('id')){
				case 'ExAfs_hbn':
					j = '#ExAfs_weight';
				break;
				case 'ExAfs_weight':
					j = '#<?=$acname21;?>';
				break;
				case '<?=$acname22;?>':
					j = '#<?=$acname31;?>';
				break;
				case 'Cnee_address':
					j = '#items .typsel:first-of-type';
				break;
				case 'Cnee_tel':
				 j = '#'+tid+'_cnee_cnid_ac';
				break;
			}
			if(j){
				$(j, panel).focus();
				return false;
			}
		}
	});

	//warning removal
	$('a.remove_warn', panel).on('click', function(){
		if(window.confirm("Are you sure to remove this warning?")){
			$.get($(this).attr('href'), function(){
				panel.trigger('reload_tab');
			});
		}
		return false;
	});

	var cnTelChk = function(pn){
		var regMobile = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
		var regPhone = /^(([0\+]\d{2,3}[- ]{1})?(0\d{2,3})[- ]{1})?(\d{7,8})$/;
		return regMobile.test(pn) || regPhone.test(pn);
	};

	$('#Cnee_tel', panel).off('change').on('change', function(){
		if(!cnTelChk($(this).val())){
			myApp.alert('Phone number invalid ('+$(this).val()+') e.g.: 13123456789, 010-12345678');
		}
	});

	//reload on status/warning change
	$('form#ex-parcel-form', panel).on('success', function(){
		if($('a.remove_warn', panel).length > 0) tab.load();
		//$('form#ex-parcel-form input[type=hidden]', panel).val('');
	});

	<?php if(!$model->isNewRecord && Acl::hasAccess('B:Export/StatusOverride')): ?>
	$('#ExAfs_status', panel).next().on('dblclick', function(){
		if(window.confirm('Are you sure to override status?')){
			$(this).prev().attr('disabled', false);
		}
	});
	<?php endif; ?>

	<?php if($model->isNewRecord): ?>
	$('#ExAfs_hbn', panel).on('change', function(){
		var hbn = $(this).val();
		$.get('exAfs/checkHbn?hbn='+hbn, function(r){
			if(r > 0){
				if(window.confirm(hbn+' already exist, go to this shipment?')){
					myApp.tabs.CreateTab({
						title: hbn,
						url: 'exAfs/update/'+r,
					});
				}
			}
		});
	});
	<?php endif; ?>

	<?php if(!$can_edit):?>
		$('form', panel).lockForm();
	<?php endif; ?>
});
</script>
