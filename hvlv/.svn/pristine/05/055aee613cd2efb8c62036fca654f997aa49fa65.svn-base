<div class="row rowcol rowleft">
<?php echo CHtml::label('Order No.', 'ordno'); ?>
<?php echo CHtml::textField('mdata[ordno]', @$model->mdata['ordno'], array('size'=>15)); ?>
</div>

<div class="row grid-view">
<div style="right: 20px;position: absolute;margin-top:-20px;">
<a href="<?=$this->createUrl('wmsTask/pickSelect', array('id' => $model->job_id));?>" class="jqm_link" data-win-class="L"><div style="background-position:-48px -688px" class="icon"></div> Add Stock</a>
</div>
<table id="mtsk_itms" class="items tsk_item">
<thead><tr><th width="25">#</th><th width="500">Stock</th><th width="40">Pallets</th><th width="40">Cartons</th><th width="40">Qty</th><th width="120">Plt Code</th><th>Notes</th></tr></thead>
<tbody>
</tbody>
<tfoot>
	<tr><td><button class="add btn btn-info"><b>+</b></button></td><td class="tot_si" align="right"></td><td class="col_pq tot_pq" align="right"></td><td class="tot_cq" align="right"></td><td class="tot_uq" align="right"></td><td>&nbsp;</td></tr>
</tfoot>
</table>
</div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	var t = $('#mtsk_itms', panel);

	t.data('line', '<tr><td class="sn"></td><td><input type="hidden" class="in_si tci" name="si" /><input type="hidden" class="in_id tci" name="id" /><input type="text" class="in_sn" name="sn" style="width:100%" /></td><td clss="col_pq"><input type="text" class="in_pq tci" name="pq" size="5" /></td><td><input type="text" class="in_cq tci" name="cq" size="5" /></td><td><input type="text" class="in_uq tci" name="uq" size="5" /></td><td><input type="hidden" class="in_pli tci" name="pli" /><input type="text" class="in_pl" name="pl" style="width:100%" /></td><td><input type="text" class="in_nt" name="nt" style="width:100%" /></td></tr>');

	t.on('calcTot', function(){
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
		
		$('td.tot_gn', t).text(countUniq('in_si'));
		$('td.tot_pq', t).text(tally('in_pq'));
		$('td.tot_cq', t).text(tally('in_cq'));
		$('td.tot_uq', t).text(tally('in_uq'));
	});

	t.on('change', 'input.in_pq,input.in_cq, input.in_uq', function(){
		var tr = $(this).parents('tr');
		var qpc = tr.find('input.in_sn').data('qpc') || 0;
		var qpp = tr.find('input.in_sn').data('qpp') || 0;
		if(qpc > 0){
			if($(this).hasClass('in_cq')) tr.find('input.in_uq').val(qpc * $(this).val());
			//if($(this).hasClass('in_uq')) tr.find('input.in_cq').val(Math.ceil($(this).val() / qpc));
		}
		if(qpp > 0){
			if($(this).hasClass('in_pq')) tr.find('input.in_uq').val(qpp * $(this).val());
		}
		t.trigger('calcTot');
	});

	var ac_select = function(me, ui){
		me.val(ui.label).data({
					'qpc' : ui.qpc,
					'qpp' : ui.qpp,
				});
		me.prevAll("input[type=hidden]").val(ui.value).data("ov",ui.value);
		if (ui.meb) {
			me.css('background-color', 'rgba(255, 96, 0, 0.5)');
		} else {
			me.css('background-color', 'white');
		}
		t.trigger('calcTot');
	};

	tab.on('addStock', function(evt, sku){
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

	t.on('change', 'input.in_pq', function(){
		var p =Number($(this).val());
		var tr = $(this).parents('tr');
		var l;
		if(p > 1 && window.confirm('Would you like to generate lines for '+p+' pallets?')){
			$(this).val(1);
			while(p-- > 1){
				tr.after(tr.clone());
			}
			t.trigger('afterAddLine').trigger('calcTot');
		}
	});

	t.on('focus', 'input.in_sn', function(){
		var me = $(this);
		if(me.data('acinit') == 1) return;
		me.autocomplete({
			// 'source': 'wmsStock/suggest?oid=<?=$model->job->org_id;?>',
			'source': 'wmsStock/suggest?oid=<?=$model->job->org_id;?>&dpt_id=<?=$model->dpt_id?>',
			'showAnim': 'fold',
			'minLength': 2,
			'delay': 200,
			'select': function(event, ui){
				ac_select(me, ui.item);
				return false;
			},
			'response': function(evt, ui){
				me.prevAll("input[type=hidden]").val(0);
				if(ui.content.length == 1){
					ui.item = ui.content[0];
					ac_select(me, ui.item);
				}else if(ui.content.length == 0){
					me.val('').data({
						'qpc' : 0,
						'qpp' : 0,
					});
				}
				return false;
			}
		}).data('acinit', 1).data("ui-autocomplete")._renderItem = function (ul, item) {
				var sn = item.label.match(/(.*?)\((.*?)\)/);
				if (sn) {
					return $('<li>').append($('<a>').html(sn[1] + '<span style="color: red;">(' + sn[2] + ')</span>')).appendTo(ul);
				} else {
					return $('<li>').append($('<a>').html(item.label)).appendTo(ul);
				}
     	};
	}).on('blur', 'input.in_sn', function(){
		if(Number($(this).prevAll("input[type=hidden]").val()) == 0 && $(this).val() !== ''){
			$(this).css('background-color', 'rgba(255,0,0,0.5)');
		}
	});

	t.on('focus', 'input.in_pl', function(){
		var me = $(this);
		me.autocomplete({
			'source': 'wmsStock/suggestPlt2?s='+me.parents('tr').find('input.in_si').val()+'&org_id=<?=$model->job->org_id?>',
			'showAnim': 'fold',
			'minLength': 2,
			'delay': 200,
			'select': function(event, ui){
				me.val(ui.item.v);
				me.prevAll("input[type=hidden]").val(ui.item.value).data("ov",ui.item.value);
				if(ui.item.si){
					tr = me.parents('tr');
					tr.find('input.in_si').val(ui.item.si);
					tr.find('input.in_sn').val(ui.item.sn);
				}
				return false;
			},
			'response': function(evt, ui){
				if(ui.content.length == 1){
					ui.item = ui.content[0];
					me.val(ui.item.v);
					me.prevAll("input[type=hidden]").val(ui.item.value).data("ov",ui.item.value);
					if(ui.item.si){
						tr = me.parents('tr');
						tr.find('input.in_si').val(ui.item.si);
						tr.find('input.in_sn').val(ui.item.sn);
					}
				}else if(ui.content.length == 0){
					me.prevAll("input[type=hidden]").val(me.prevAll("input[type=hidden]").data("ov"));
					me.val('');
				}
				return false;
			},
		});
	});
});
</script>