<h2>Pick SKU <span style="font-size:0.8em">(Owner: <?=$model->customer->name;?>)</span></h2>
<div style="min-height: 400px;">
<div id="wms_sku-tabs" style="margin-bottom: 10px;">
  <ul>
  <li><a href="#jqmw_<?=$_GET["tabid"];?>_tab1">By Product</a></li>
  <li><a href="#jqmw_<?=$_GET["tabid"];?>_tab2">By Pallet</a></li>
  </ul>
  <div class="q_f" id="jqmw_<?=$_GET["tabid"];?>_tab1" style="padding: 5px;">
<div class="row grid-view">
<table id="tbl_prod_search" class="items">
<thead><tr><th width="25">#</th><th width="300">Product</th><th width="40">Qty</th><th width="120">Min Expiry</th><th width="120">Batch</th></tr></thead>
<tbody>
</tbody>
<tfoot>
	<tr><td><button class="add btn btn-info"><b>+</b></button></td><td class="tot_gi" align="right"></td><td class="tot_uq" align="right"></td><td>&nbsp;</td><td align="right"><input type="hidden" name="q" id="q_data" /><input type="hidden" name="plt" /><button class="btn_search">Search</button></td></tr>
</tfoot>
</table>
</div>
  </div>
  <div class="q_f" id="jqmw_<?=$_GET["tabid"];?>_tab2" style="padding: 5px;">
  	<input type="text" class="plt_code" placeholder="Pallet #" name="plt" />
  	<input type="hidden" name="q" />
    <button class="btn_search">Search</button>
  </div>
</div>

<div class="row">
<?php
$data = [];
$dps = [];

$sku = new WmsStock('search');
$sku->unsetAttributes();
$sku->org_id = $model->org_id;
$sku->dpt_id = $model->dpt_id;
$sku->qty = '>0';
$dp = $sku->search(true, 20, false, 't.expiry ASC, t.id ASC');
if(!empty($_GET['plt'])){
	$sku->loc_code = $_GET['plt'];
	$dp = $sku->search(true, 20, false, 't.expiry ASC, t.id ASC');
}elseif(!empty($_GET['q'])){
	$jd = json_decode($_GET['q'], true);
	foreach($jd as $q){
		$s = new WmsStock('search');
		$s->unsetAttributes();
		$s->org_id = $model->org_id;
		$s->qty = '>0';
		$s->prod_id = $q['gi'];
		if(!empty($q['ex'])) $s->expiry = '>='.$q['ex'];
		if(!empty($q['bn'])) $s->batch = $q['bn'];
		$dps[] = $s->search(true, 20, false, 't.expiry ASC, t.id ASC');
	}
}

if(!empty($dps)){
	foreach($dps as $dp){
		for($i = 0; $i < min(20, $dp->totalItemCount); $i++){
			array_push($data, $dp->data[$i]);
		}
	}

	$dp->data = $data;
}

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wms-pick_sku-grid',
	'cssFile' => false,
	'dataProvider'=> $dp,
	'afterAjaxUpdate'=>'function(id, data){ $("#jqmw_'.$_GET["tabid"].'").trigger("afterGridUpdate"); }',
	'columns'=>array(
		array('name' => 'prod_name', 'value' => 'empty($data->prod)? "" : $data->prod->name', 'htmlOptions' => array('class' => 'sku_name')),
		array('name' => 'prod_ean', 'value' => 'empty($data->prod)? "" : $data->prod->ean'),
		array('name' => 'expiry', 'htmlOptions' => array('class' => 'sku_exp')),
		array('name' => 'batch', 'htmlOptions' => array('class' => 'sku_batch')),
		array('name' => 'qty', 'footer'=> '', 'htmlOptions' => array('class' => 'sku_aq'), 'footerHtmlOptions'=> array('class' => 'tot_aq'),),
		array('name' => 'qty_res', 'footer' => '', 'htmlOptions' => array('class' => 'sku_aq'), 'footerHtmlOptions' => array('class' => 'tot_aq')),
		array('header' => 'Pick Qty', 'type' => 'raw', 'value' => '"<input class=\"sku_qty pq_".$data->prod_id."\" type=\"text\" name=\"sq_".$data->id."\" size=\"10\" />"', 'footer'=> 0, 'footerHtmlOptions'=> array('class' => 'tot_pq')),
		),
)); ?>
</div>
<div style="text-align:right;">
<?php
	echo CHtml::button('Add Stock', ['class' => 'btn_addsku']);
?>
</div>
</div>
<div id="sn_context" class="dropdown dropdown-tip dropdown-anchor-left">
	<ul class="dropdown-menu">
		<li><a href="#" class="insert_line">Insert Line</a></li>
		<li><a href="#" class="del_line">Delete Line</a></li>
	</ul>
</div>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var t = $('#tbl_prod_search', win);

	t.data('line', '<tr><td class="sn"></td><td><input type="hidden" class="in_gi tci" name="gi" /><input type="text" class="in_gn" name="gn" style="width:100%" /></td><td><input type="text" class="in_uq tci" name="uq" style="width:100%" /></td><td><input type="text" class="in_ex tci date_input" name="ex" style="width:100%" /></td><td><input type="text" class="in_bn" name="pl" style="width:100%" /></td></tr>').on('afterAddLine', function(){
		$('tbody tr', t).each(function(i){
			$(this).removeClass('odd even');
 			$(this).addClass(i%2 == 0? 'odd' : 'even');
 			$('.sn', this).text(i + 1);
		});
	}).on('contextmenu', 'td.sn', function(){
		var p = $(this).position();
  		$('#sn_context', win).data('tarow', $(this).parents('tr')).css({left: p.left+10, top: p.top+50}).toggle();
  		return false;
	}).on('calcTot', function(){
		var countUniq = function(c){
			var vl = [];
			$('input.'+c, t).each(function(){
				var v = $(this).val();
				if(v == '') return;
				if($.inArray(v,vl) == -1) vl.push(v);
			});

			return vl.length;
		};

		var tally = function(c){
			var tt = 0;
			$('input.'+c, t).each(function(){
				var v = $(this).val();
				if(v == '') return;
				tt += Number(v);
			});

			return tt;
		};
		
		$('td.tot_gi', t).text(countUniq('in_gi'));
		$('td.tot_uq', t).text(tally('in_uq'));
	}).on('paste','tbody input[type=text]',function(e){
		if (window.clipboardData && window.clipboardData.getData){
			pastedText = window.clipboardData.getData('Text');
		} else if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData) {
			pastedText = e.originalEvent.clipboardData.getData('text/plain');
		}

		var rn = -1;
		var cn = -1;
		var me = $(this);
		var rows = pastedText.trim().split(/[\n\r]+/);
		var cells = [];
		
		$('tbody tr', t).each(function(i){
			if($(this).is(me.parents('tr'))){
				var e = rows.length - $('tbody tr', t).length + i;
				if(e > 0) for(var c = 0; c < e; c++) addLine();
				return false;
			}
		});

		$('tbody tr', t).each(function(i){
			if(rn == -1){
				if($(this).is(me.parents('tr'))) rn = 0;
				else return;
			}
			if(rn >= rows.length) return false;
			cells = rows[rn].trim().split(/\t/);
			$('input[type=text]', this).each(function(j){
				if(rn == 0 && $(this).is(me)) cn = j;
				if(cn < 0 || j < cn) return;
				if(j >= cells.length + cn) return false;
				if($(this).hasClass('in_gn')) $(this).trigger('focus');
				if(rn > -1) $(this).val(cells[j-cn].trim());
				if($(this).hasClass('ui-autocomplete-input')) $(this).autocomplete('search');
			});
			rn++;
		});

		//$('input.in_cq', t).trigger('change');
		t.trigger('caclTot');
		return false;
	}).on('focus', 'input.in_gn', function(){
		var me = $(this);
		if(me.data('acinit') == 1) return;
		me.autocomplete({
			'source': 'wmsProd/suggest?oid=<?=$model->org_id;?>',
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
	}).on('change', 'input.in_uq', function(){
		t.trigger('calcTot');
	});

	var ac_select = function(me, ui){
		me.val(ui.label).data({
					'qpc' : ui.qpc,
					'qpp' : ui.qpp,
				});
		me.prevAll("input[type=hidden]").val(ui.value).data("ov",ui.value);
		t.trigger('calcTot');
	};

	$('#wms_sku-tabs', win).tabs();

	var addLine = function(at){
		var at = at || false;
		if($('tbody tr', t).length > 2000){
			myApp.alert('Too many lines, maximum 2000 allowed');
			return false;
		}
		t.trigger('beforeAddLine');
		if(at) at.before($(t.data('line')));
		else $('tbody', t).append($(t.data('line')));
		t.trigger('afterAddLine');
	};

	$('button.add', t).on('click', function(){
		addLine();
		return false;
	}).trigger('click');

	win.on('change', 'input.sku_qty', function(){
		var q = Number($(this).val());
		if(q < 0) $(this).val('');
		var tally = function(c){
			var tt = 0;
			$('#wms-pick_sku-grid input.'+c, win).each(function(){
				var v = $(this).val();
				if(v == '') return;
				tt += Number(v);
			});

			return tt;
		};
		
		$('#wms-pick_sku-grid td.tot_pq', win).text(tally('sku_qty'));
	});

	win.on('click', 'button.btn_search', function(){
		if(t.is(':visible')){
			var ia = [];
			$('tbody tr', t).each(function(){
				var o = {};
				$('input', this).each(function(){
					o[$(this).attr('name')] = $(this).val();
				});
				ia.push(o);
			});
			$('#q_data', t).val(JSON.stringify(ia));
		}
		$('#wms-pick_sku-grid', win).yiiGridView('update', {data: $('input', $(this).parent()).serialize()});
		return false;
	});

	win.on('afterGridUpdate', function(){
		if(t.is(':visible')){
			$('tbody tr .in_gi', t).each(function(){
				var me = $(this);
				var q = $('.in_uq', me.parents('tr')).val();
				if(me.val() == '') return;

				$('#wms-pick_sku-grid input.sku_qty.pq_'+me.val(), win).each(function(){
					var aq = Number($(this).parent().prev().text());
					if(aq <= q){
						$(this).val(aq);
						q -= aq;
					}else{
						$(this).val(q);
						q = 0;
						return false;
					}
				});
				if(q > 0) myApp.alert('Not enough stock for #'+$('.sn', me.parents('tr')).text()+', short '+q);
			});
			
			$($('#wms-pick_sku-grid input.sku_qty', win)[0]).trigger('change');
		}

		if($('.plt_code:visible', win).length > 0){
			$('#wms-pick_sku-grid input.sku_qty', win).each(function(){
				var aq = Number($(this).parent().prev().text());
				$(this).val(aq);
			});
		}
	});

	$('.btn_addsku', win).on('click', function(){
		var sku = [];
		$('input.sku_qty', win).each(function(){
			var me = $(this);
			var tr = $(this).parents('tr');
			var q = Number(me.val());
			if(q > 0){
				var e = '';
				var exp = tr.find('td.sku_exp').text().trim();
				var bat = tr.find('td.sku_bat').text().trim();
				if(exp != '') e += 'Exp: '+exp;
				if(bat != '') e = e==''? 'Bat: '+bat : e+', Bat: '+bat;
				sku.push({'i': me.attr('name').substr(3), 'n': tr.find('td.sku_name').text()+(e==''? '' : ' ('+e+')'), 'q': q});
			}
		});
		win.data('opener').trigger('addStock', [sku]);
		win.jqmHide();
	});
});
</script>