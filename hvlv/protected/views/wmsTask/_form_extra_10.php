<div class="row grid-view">
<table id="mtsk_itms" class="items tsk_item">
<thead><tr><th width="25">#</th><th width="130" class="col_pl">Plt Code</th><th width="200">Product</th><th width="40">Cartons</th><th width="40">Qty</th><th width="100">Expiry Date</th><?=in_array($model->job->org_id, Org::$airsea_heshengyuan) ? '<th width="100">Mfr. Date</th>' : ''?><th width="100">Batch #</th><th>Notes</th></tr></thead>
<tbody>
</tbody>
<tfoot>
	<tr><td><button class="add btn btn-info"><b>+</b></button></td><td class="col_pl tot_pl" align="right"></td><td class="tot_gn" align="right"></td><td class="tot_cq" align="right"></td><td class="tot_uq" align="right"></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
</tfoot>
</table>
</div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	var t = $('#mtsk_itms', panel);

	t.data('line', '<tr><td class="sn"></td><td clss="col_pl"><input type="hidden" class="in_pli tci" name="pli" /><input type="text" class="in_pl" name="pl" style="width:100%" /></td><td><input type="hidden" class="in_gi tci" name="gi" /><input type="text" class="in_gn" name="gn" style="width:100%" /></td><td><input type="text" class="in_cq tci" name="cq" size="5" /></td><td><input type="text" class="in_uq tci" name="uq" size="5" /></td><td><input type="text" class="in_ex date_input" name="ex" size="10" /></td><?=in_array($model->job->org_id, Org::$airsea_heshengyuan) ? '<td><input type="text" class="in_mfr date_input" name="mfr" size="10" /></td>' : ''?><td><input type="text" class="in_bn" name="bn" size="10" /></td><td><input type="text" class="in_nt" name="nt" style="width:100%" /></td></tr>');

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
		
		$('td.tot_pl', t).text(countUniq('in_pl'));
		$('td.tot_gn', t).text(countUniq('in_gi'));
		$('td.tot_cq', t).text(tally('in_cq'));
		$('td.tot_uq', t).text(tally('in_uq'));
	});

	t.on('change', 'input.in_cq, input.in_uq', function(){
		var tr = $(this).parents('tr');
		var qpc = tr.find('input.in_gn').data('qpc') || 0;
		if(qpc > 0){
			if($(this).hasClass('in_cq')) tr.find('input.in_uq').val(qpc * $(this).val());
			//if($(this).hasClass('in_uq')) tr.find('input.in_cq').val(Math.ceil($(this).val() / qpc));
		}
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

	$(t).on('focus', 'input.in_gn', function(){
		var me = $(this);
		if(me.data('acinit') == 1) return;
		me.autocomplete({
			'source': 'wmsProd/suggest?oid=<?=$model->job->org_id;?>',
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

	t.on('focus', 'input.in_pl', function(){
		var me = $(this);
		if(me.data('acinit') == 1) return;
		me.autocomplete({
			'source': 'wmsLocation/suggest',
			'showAnim': 'fold',
			'minLength': 2,
			'delay': 200,
			'select': function(event, ui){
				me.val(ui.item.label);
				me.prevAll("input[type=hidden]").val(ui.item.value).data("ov",ui.item.value);
				return false;
			},
			'response': function(evt, ui){
				if(ui.content.length == 1){
					ui.item = ui.content[0];
					me.val(ui.item.label);
					me.prevAll("input[type=hidden]").val(ui.item.value).data("ov",ui.item.value);
				}else if(ui.content.length == 0){
					me.prevAll("input[type=hidden]").val(me.prevAll("input[type=hidden]").data("ov"));
					me.val('');
				}
				return false;
			},
		}).data('acinit', 1);
	});
});
</script>