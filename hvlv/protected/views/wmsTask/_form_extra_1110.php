<div class="row grid-view">
<table id="tbl_item" class="items">
<thead><tr><th width="25">#</th><th width="150" class="col_pl">Pallet #</th><th width="150">Location #</th><th>Notes</th></tr></thead>
<tbody>
</tbody>
<tfoot>
	<tr><td><button class="add btn btn-info"><b>+</b></button></td><td class="col_pl tot_pl" align="right"></td><td class="tot_lo" align="right"></td><td>&nbsp;</td></tr>
</tfoot>
</table>
</div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	var t = $('#tbl_item', panel);

	t.data('line', '<tr><td class="sn"></td><td clss="col_pl"><input type="text" class="in_pl tci" name="pl" size="15" /></td><td><input type="hidden" class="in_li" name="li" /><input type="text" class="in_lo" name="lo" size="15" /></td><td><input type="text" class="in_nt" name="nt" style="width:100%" /></td></tr>');

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
		
		$('td.tot_pl', t).text(countUniq('in_pl'));
		$('td.tot_lo', t).text(countUniq('in_li'));
	});

	var ac_select = function(me, ui){
		me.val(ui.label);
		me.prevAll("input[type=hidden]").val(ui.value).data("ov",ui.value);
		t.trigger('caclTot');
	};

	$(t).on('focus', 'input.in_lo', function(){
		var me = $(this);
		if(me.data('acinit') == 1) return;
		me.autocomplete({
			'source': 'wmsLocation/suggest',
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
});
</script>