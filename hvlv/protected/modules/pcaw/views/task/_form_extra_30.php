<div class="container">
	<br>
	<?php if ($model->status < 30) { ?>
		<div style="right: 15%;position: absolute;margin-top:-20px;">
			<a href="<?=$this->createUrl('task/pickSelect', array('id' => $model->job_id));?>" class="tracking-modal-link" data-win-class="L"><span  class="glyphicon glyphicon-plus"></span> Add Stock</a>
		</div>
	<?php } ?>
	<br/>
</div>
<div class="form-group table-responsive">
	<table id="items" class="table table-striped table-bordered">
	<thead>
	<?php if($model->mdata['isSpecial']) {?>
	<tr><th>#</th><th width="30%"><?=$this->t('Product');?> <span class="required">*</span></th><th width="15%"><?=$this->t('SKU / EAN');?></th><th><?=$this->t('Pallets');?></th><th><?=$this->t('Cartons');?></th><th><?=$this->t('Qty');?></th><th><?=$this->t('Pallet Code');?></th><th><?=$this->t('Note');?></th></tr>
	<?php } else {?>
		<tr><th>#</th><th width="30%"><?=$this->t('Product');?> <span class="required">*</span></th><th width="15%"><?=$this->t('SKU / EAN');?></th><th><?=$this->t('Pallets');?></th><th><?=$this->t('Cartons');?></th><th><?=$this->t('Qty');?></th><th><?=$this->t('Pallet Code');?></th></tr>	
	<?php } ?>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">
				<b class="pull-right">Total:</b>
				<?php if ($model->status < 30) { ?>
					<button class="moreitem btn btn-normal"><span class="glyphicon glyphicon-plus" style="color: #be3426; font-size: 1.2em; padding: 3px 10px;"></span></button>
				<?php } ?>
			</td>
			<th id="tot_qty"></th><th id="tot_value">&nbsp;</th><th></th><th></th><th></th><th></th>
		</tr>
	</tfoot>
	</table>
</div>
<!-- Tracking Modal -->
<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->t('Close');?></button>
			</div>
		</div>
	</div>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	<?php $items=[];
	$mebs = [];
	$taskItems = $model->items;
	if(!empty($taskItems)){
		foreach ($taskItems as $k=> $i){
			if (intval(@$i->mdata['uq']) <= 0 && intval(@$i->mdata['cq']) <= 0 && intval(@$i->mdata['kituq']) <= 0) {
				continue;
			}
			$s = WmsStock::model()->findByPk($i->mdata["si"]);
			$meb = false;
			if(!empty($s) && !empty($mebs[$s->prod_id]) || (!empty($s) && $s->hasMultiEB())) {
				$meb = true;
				$mebs[$s->prod_id] = true;
				$i->mdata["meb"] = $meb;
				$i->mdata["remain"] = $s->qty - $s->qty_res + (!empty($i->mdata['uq']) ? $i->mdata['uq'] : 0);
			}
			if (!empty($s)) {
				$prod = $s->prod;
			} else if (!empty($i->mdata['pi'])) {
				$prod = WmsProd::model()->findByPk($i->mdata['pi']);
			} else {
				continue;
			}
			if (!empty($prod->orgs[0]->sku)) {
				$i->mdata['sku'] = $prod->orgs[0]->sku;
			} else if (!empty($prod)) {
				$i->mdata['sku'] = $prod->ean;
			}
			if (!empty($i->mdata['cq'])) {
				$i->mdata['cq'] = round($i->mdata['cq'] * 100) / 100;
			}
			$items[]= array_merge($i->mdata,array('gn'=>empty($i->mdata['gi'])?'':WmsProd::getName($i->mdata['gi'])));
		}
	}
	?>
		
	var pitems = <?=json_encode($items)?> || [];
	var addItem = function(add){
		var tb = $('#items tbody');
		var id = $('tr', tb).length;
		var add = add || 1;
	var status = <?=$model->status?>;
	while(add-- > 0){
		<?php if($model->mdata['isSpecial']) {?>
		tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td class="rid">'+(id+1)+'</td>\n\
			<td><input type="hidden" class="in_si" id="in_gi'+id+'" name="si" value="'+(pitems[id]?pitems[id].si: '')+'"/><input type="hidden" class="in_remain" id="in_remain'+id+'" name="remain" value="Remain: '+(pitems[id]?pitems[id].remain: '')+'"/><input type="text" class="in_sn item_name'+(pitems.g && pitems.g[id] === false? ' error' : '')+' form-control" id="sn'+id+'" name="sn"  size="10" value="'+(pitems[id]?pitems[id].sn: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="text" class="in_sku'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="sku" size="3" value="'+(pitems[id]? pitems[id].sku: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="text" class="in_pq'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="pq" size="3" value="'+(pitems[id]? pitems[id].pq: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="text" class="in_cq'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="cq" size="3" value="'+(pitems[id]? pitems[id].cq: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="text" class="in_uq'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="uq" size="6" value="'+(pitems[id]? (pitems[id].kituq ? pitems[id].kituq : pitems[id].uq): '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="text" class="in_pl'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="pl" size="6" value="'+(pitems[id]? pitems[id].pl: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="text" class="in_nt'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="nt" size="6" value="'+(pitems[id]? pitems[id].nt: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			</tr>');
			if (pitems[id] && (!pitems[id].si || pitems[id].si == 0)) {
					$('#sn'+id).css('background-color', 'rgba(255,0,0,0.5)');
			} else if (pitems[id] && pitems[id].meb) {
					$('#sn'+id).css('background-color', 'rgba(255, 96, 0, 0.5)');
			}
			id++;
		}
		<?php } else {?>
			tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td class="rid">'+(id+1)+'</td>\n\
			<td><input type="hidden" class="in_si" id="in_gi'+id+'" name="si" value="'+(pitems[id]?pitems[id].si: '')+'"/><input type="hidden" class="in_remain" id="in_remain'+id+'" name="remain" value="Remain: '+(pitems[id]?pitems[id].remain: '')+'"/><input type="text" class="in_sn item_name'+(pitems.g && pitems.g[id] === false? ' error' : '')+' form-control" id="sn'+id+'" name="sn"  size="10" value="'+(pitems[id]?pitems[id].sn: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="text" class="in_sku'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="sku" size="3" value="'+(pitems[id]? pitems[id].sku: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="text" class="in_pq'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="pq" size="3" value="'+(pitems[id]? pitems[id].pq: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="text" class="in_cq'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="cq" size="3" value="'+(pitems[id]? pitems[id].cq: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="text" class="in_uq'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="uq" size="6" value="'+(pitems[id]? (pitems[id].kituq ? pitems[id].kituq : pitems[id].uq): '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="text" class="in_pl'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="pl" size="6" value="'+(pitems[id]? pitems[id].pl: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			<td><input type="hidden" class="in_nt'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="nt" size="6" value="'+(pitems[id]? pitems[id].nt: '')+'"'+(status>=30?' readonly="readonly"':'')+' /></td>\n\
			</tr>');
			if (pitems[id] && (!pitems[id].si || pitems[id].si == 0)) {
					$('#sn'+id).css('background-color', 'rgba(255,0,0,0.5)');
			} else if (pitems[id] && pitems[id].meb) {
					$('#sn'+id).css('background-color', 'rgba(255, 96, 0, 0.5)');
			}
			id++;
		}	
		<?php } ?>
		$('select.typsel', tb).each(function(){
			if($(this).data('ov') != ''){
				$(this).val($(this).data('ov'));
			}
		});
		calcTot();
	$('input.in_sn').on('focus', function(){
		var me = $(this);
//		if(me.data('acinit') == 1) return;
		var org_id = $('#org_id').val();
		var url = "<?=$this->createUrl('product/suggest1',array('oid'=>$model->job->org_id, 'dpt_id' => $model->dpt_id))?>";
		if (org_id) {
			url = url.replace(/oid\/\d*/g, 'oid/' + org_id);
		}
		me.autocomplete({
			'source': url,
			'showAnim': 'fold',
			'minLength': 2,
			'delay': 200,
			'select': function(event, ui){
				ac_select($(this), ui.item);
				return false;
			},
			'response': function(evt, ui){
				me.prevAll("input[id^=in_gi]").val(0);
				if(ui.content.length == 1){
					ui.item = ui.content[0];
					ac_select($(this), ui.item);
				}else if(ui.content.length == 0){
					$(this).val('').data({
						'qpc' : 0,
						'qpp' : 0,
					});
				}
				return false;
			},
		}).data('acinit', 1).data("ui-autocomplete")._renderItem = function (ul, item) {
			var sn = item.label.match(/(.*?)\((.*?)\)/);
			if (sn) {
				return $('<li>').append($('<a>').html(sn[1] + '<span style="color: red;">(' + sn[2] + ' ' + item.remain + ')</span>')).appendTo(ul);
			} else {
				return $('<li>').append($('<a>').html(item.label + '<span style="color: red;">(' + item.remain + ')')).appendTo(ul);
			}
		};
	});
	};
		$('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
		$('#modal-tracking').modal();
		$('#modal-tracking .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});

	var calcTot = function(){
		var tqty = 0;

		$('#items tbody tr').each(function(){
			tqty += Number($('.item_qty', this).val()) || 0;
		});
		
		$('#tot_qty').text(tqty);
	};

	$('#items').off('change', '.item_qty,.item_wt,.item_tv,.item_tax').on('change', '.item_qty,.item_wt,.item_tv,.item_tax', calcTot);


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
		me.prevAll("input[id^=in_gi]").val(ui.value).data("ov",ui.value);
		me.prevAll("input[id^=in_remain]").val(ui.remain).data("ov",ui.remain);
		if (ui.meb) {
			me.css('background-color', 'rgba(255, 96, 0, 0.5)');
		} else {
			me.css('background-color', 'white');
		}
		
	};
			$(this).on('addStock', function(evt, sku){
		var emptyRow = function(){
			var tr = false;
			$('tbody tr', t).each(function(){
				if(Number($('.in_uq', this).val()) == 0){
					tr = $(this);
					return false;
				}
			});
			if(tr === false){
				tr = $(t.data('line'));
				$('tbody', t).append(tr);
			}
			return tr;
		};
		
		for(i in sku){
			tr = emptyRow();
			$('.in_si', tr).val(sku[i].i);
			$('.in_sn', tr).val(sku[i].n);
			$('.in_uq', tr).val(sku[i].q);
		}
	});
		
	$('input.in_sn').on('focus', function(){
		var me = $(this);
//		if(me.data('acinit') == 1) return;
		var org_id = $('#org_id').val();
		var url = "<?=$this->createUrl('product/suggest1',array('oid'=>$model->job->org_id, 'dpt_id' => $model->dpt_id))?>";
		if (org_id) {
			url = url.replace(/oid\/\d*/g, 'oid/' + org_id);
		}
		me.autocomplete({
			'source': url,
			'showAnim': 'fold',
			'minLength': 2,
			'delay': 200,
			'select': function(event, ui){
				ac_select($(this), ui.item);
				return false;
			},
			'response': function(evt, ui){
				$(this).prevAll("input[id^=in_gi]").val(0);
				if(ui.content.length == 1){
					ui.item = ui.content[0];
					ac_select($(this), ui.item);
				}else if(ui.content.length == 0){
					$(this).val('').data({
						'qpc' : 0,
						'qpp' : 0,
					});
				}
				return false;
			},
		}).data('acinit', 1).data("ui-autocomplete")._renderItem = function (ul, item) {
				var sn = item.label.match(/(.*?)\((.*?)\)/);
				if (sn) {
					return $('<li>').append($('<a>').html(sn[1] + '<span style="color: red;">(' + sn[2] + ' ' + item.remain + ')</span>')).appendTo(ul);
				} else {
					return $('<li>').append($('<a>').html(item.label + '<span style="color: red;">(' + item.remain + ')')).appendTo(ul);
				}
		};
	}).on('blur', function(){
		if(Number($(this).prevAll("input[id^=in_gi]").val()) == 0 && $(this).val() !== ''){
			$(this).css('background-color', 'rgba(255,0,0,0.5)');
		}
	});

	$('#items').on('keyup', 'input.in_uq', function() {
		var quantity = parseInt($(this).val());
		var line = $(this).parent().prevAll('.rid').text();
		var remain = $('input#in_remain' + (line - 1)).val();
		if (remain) {
			$(this).css('background-color', 'white');
			if (remain.match(/Remain: (\d+)/)) {
				remain = parseInt(remain.match(/Remain: (\d+)/)[1]);
				if (quantity > remain) {
					$(this).css('background-color', 'rgba(255,0,0,0.5)');
				}
			}
		}
	});

	$('#items').on('change', 'input.in_uq', function() {
		qpc = $(this).parent().parent().find('input.in_sn').data('qpc');
		qpp = $(this).parent().parent().find('input.in_sn').data('qpp');
		cq = $(this).val() % qpc == 0 ? $(this).val() / qpc : '';
		pq = $(this).val() % qpp == 0 ? $(this).val() / qpp : '';
		$(this).parent().parent().find('input.in_cq').val(cq);
		$(this).parent().parent().find('input.in_pq').val(pq);
	});

	$('#task-btn').on('click', function() {
		var flag = 0
		$('input.in_uq').each(function() {
			var quantity = parseInt($(this).val());
			var line = $(this).parent().prevAll('.rid').text();
			var remain = $('input#in_remain' + (line - 1)).val();
			if (remain) {
				if (remain.match(/Remain: (\d+)/)) {
					remain = parseInt(remain.match(/Remain: (\d+)/)[1]);
					if (quantity > remain) {
						alert('line ' + line + ' does not have enough stock');
						flag = 1;
					}
				}
			}
		});

		if (flag) {
			return false;
		}
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

	$('#org_id').on('change', function() {
		$('tbody').html('');
		addItem(1);
	});
	
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>