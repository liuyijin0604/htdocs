<div class="form-group table-responsive" id="_form_extra_10">
	<table id="items" class="table table-striped table-bordered">
	<thead>
	<tr><th>#</th><th><?=$this->t('SKU');?></th><th><?=$this->t('Product');?> <span class="required">*</span></th><th><?=$this->t('Cartons');?></th><th><?=$this->t('Qty');?></th><th><?=$this->t('Expiry Date');?></th><th><?=$this->t('Batch');?></th></tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
		<tr><td colspan="2"><b class="pull-right">Total:</b><button class="moreitem btn btn-normal"><span class="glyphicon glyphicon-plus" style="color: #be3426; font-size: 1.2em; padding: 3px 10px;"></span></button></td><th id="tot_qty"></th><th id="tot_value">&nbsp;</th><th></th><th></th></tr>
	</tfoot>
	</table></div>


<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	<?php $items=[];
	if(!empty($model->items)){
		foreach ($model->items as $k=> $i){
			$items[]= array_merge($i->mdata,array('gn'=> WmsProd::getName($i->mdata['gi'])),array('sku' => WmsProd::model()->findByPk($i->mdata['gi'])->getSku($model->job->org_id)));
		}
	}
	?>

	var pitems = <?=json_encode($items)?> || [];
	var addItem = function(add){
		var tb = $('#items tbody');
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0){
			tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td class="rid">'+(id+1)+'</td>\n\
				<input type="hidden"  name="pli"/>\n\
				<input type="hidden"  name="pl"/>\n\
				<td><input type="text" class="in_sku item_sku form-control" name="sku" size="10" value="'+(pitems[id]?pitems[id].sku: '')+'" /></td>\n\
				<td><input type="hidden" id="in_gi'+id+'" name="gi" value="'+(pitems[id]?pitems[id].gi:'')+'"/><input type="text" class="in_gn item_name'+(pitems.g && pitems.g[id] === false? ' error' : '')+' form-control" name="gn"  size="10" value="'+(pitems[id]?pitems[id].gn: '')+'" /></td>\n\
				<td><input type="text" class="item_qty'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="cq" size="3" value="'+(pitems[id]? pitems[id].cq: '')+'" /></td>\n\
				<td><input type="text" class="item_tv'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="uq" size="6" value="'+(pitems[id]? pitems[id].uq: '')+'" /></td>\n\
				<td><input type="text" class="date_input item_tv'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="ex" size="6" value="'+(pitems[id]? pitems[id].ex: '')+'" /></td>\n\
				<td><input type="text" class="item_tv'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="bn" size="6" value="'+(pitems[id]? pitems[id].bn: '')+'" /></td>\n\
				<input type="hidden"  name="nt"/>\n\
				</tr>');
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
	
//	//required fields
//	$('#cnee_name_ac, #CoParcel_weight, #cnor_name_ac, #cnor_tel_ac, #cnee_name_ac, #Cnee_tel, #Cnee_address, #Cnee_postcode').off('change').on('change', function(){
//		if($(this).val() == '' || $(this).val() == 0){
//			$(this).parents('.form-group').addClass('has-warning').removeClass('has-success');
//		}else{
//			$(this).parents('.form-group').removeClass('has-warning').addClass('has-success');
//		}
//	}).trigger('change');

	$('#items').off('change', '.item_qty,.item_wt,.item_tv,.item_tax').on('change', '.item_qty,.item_wt,.item_tv,.item_tax', calcTot);

//	$('#items').off('keydown', 'input[type=text]').on('keydown', 'input[type=text]', function(evt){
//			if(evt.keyCode == 13){
//				if(Number($(this).parents('tr').find('.rid').text()) == $('#items tbody tr').length) addItem(1);
//				$(this).parents('tr').next().find('.item_name').focus();
//				return false;
//			}
//	});

	$('.moreitem').off('click').on('click', function(e){
		addItem(1);
		$(this).parents('tr').next().find('.typsel').focus();
		e.preventDefault();
	});

	addItem(pitems? pitems.length : 1);

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

	var ac_select = function(me, ui){
		me.val(ui.label).data({
					'qpc' : ui.qpc,
					'qpp' : ui.qpp,
				});
		me.prevAll("input[type=hidden]").val(ui.value).data("ov",ui.value);
		me.parent().prev().find("input[name='sku']").val(ui.sku);
	};

	$('#_form_extra_10').off('focus', 'input.in_gn').on('focus', 'input.in_gn', function(){
		var me = $(this);
//		if(me.data('acinit') == 1) return;
		me.autocomplete({
			'source': "<?=$this->createUrl('product/suggest',array('oid'=>$model->job->org_id))?>",
			'showAnim': 'fold',
			'minLength': 2,
			'delay': 200,
			'select': function(event, ui){
				ac_select($(this), ui.item);
				return false;
			},
			'response': function(evt, ui){
				if(ui.content.length == 1){
					ui.item = ui.content[0];
					ac_select($(this), ui.item);
				}else if(ui.content.length == 0){
					$(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov"));
					$(this).val('').data({
						'qpc' : 0,
						'qpp' : 0,
					});
				}
				return false;
			},
		}).data('acinit', 1);
	});

	$('#_form_extra_10').off('focus', 'input.in_sku').on('focus', 'input.in_sku', function(){
		var me = $(this);
		me.autocomplete({
			'source': "<?=$this->createUrl('product/suggest',array('oid'=>$model->job->org_id))?>",
			'showAnim': 'fold',
			'minLength': 2,
			'delay': 200,
			'select': function(event, ui){
				ele = $(this).parent().next().find('input.in_gn');
				ac_select(ele, ui.item);
				$(this).val(ui.item.sku);
				return false;
			},
			'response': function(evt, ui){
				ele = $(this).parent().next().find('input.in_gn');
				if(ui.content.length == 1){
					ui.item = ui.content[0];
					ac_select(ele, ui.item);
					$(this).val(ui.item.sku);
				}else if(ui.content.length == 0){
					ele.prevAll("input[type=hidden]").val(ele.prevAll("input[type=hidden]").data("ov"));
					ele.val('').data({
						'qpc' : 0,
						'qpp' : 0,
					});
				}
				return false;
			},
		}).data('acinit', 1);
	});

	//tabindex control
//	$('#CoParcel_weight').focus();

//	$('form input, form select').off('keydown').on('keydown', function(evt){
//		if(evt.keyCode == 9 && !evt.shiftKey){
//			var j = false;
//			switch($(this).attr('id')){
//				case 'ExParcel_hbn':
//					j = '#ExParcel_weight';
//				break;
//				case 'ExParcel_weight':
//					j = '#cnor_name_ac';
//				break;
//				case 'cnor_tel_ac':
//					j = '#cnee_name_ac';
//				break;
//				case 'cnee_sub_ac':
//					j = '#items .item_name:first-of-type';
//				break;
//			}
//			if(j){
//				$(j).focus();
//				return false;
//			}
//		}
//	});
	
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>