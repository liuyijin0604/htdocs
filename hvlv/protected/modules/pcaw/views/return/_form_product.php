<div class="container" style="padding: 0">
	<div class="form-group table-responsive">
		<table id="return_items" class="table table-striped table-bordered">
		<thead><tr><th width="25">#</th><th width="500">Product</th><th width="40">SKU/Barcode</th><th width="40">Qty</th></tr></thead>
		<tbody>
		</tbody>
		<tfoot>
			<tr><th><button class="moreitem btn btn-normal" style="cursor: pointer"><span class="glyphicon glyphicon-plus" style="color: #be3426; font-size: 1.2em; padding: 3px 10px;"></span></button></th><th>&nbsp;</th><th>&nbsp;</th><th id="tot_qty" align="right"></th></tr>
		</tfoot>
		</table>
	</div>
</div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	var calcTot = function() {
		var tqty = 0;

		$('#return_items tbody tr').each(function() {
			tqty += Number($('.in_uq', this).val()) || 0;
		});
		
		$('#tot_qty').text(tqty);
	}


	<?php
	$items = [];
	if (!empty($model->items)) {
		foreach ($model->items as $k => $i) {
			$items[] = $i->mdata;
		}
	}
	?>
	var pitems = <?=json_encode($items)?> || [];
	var addItem = function(add) {
		var tb = $('#return_items tbody');
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0) {
			tb.append('<tr class="' + (id%2 == 0 ? 'even' : 'odd') + '"><td>' + (id+1) + '</td>\n\
				<td style="width: 55%"><input type="text" class="in_sn item_name form-control" name="item[' + id + '][sn]" value="' + (pitems[id] ? (pitems[id].sn ? pitems[id].sn : '') : '') + '"' + (status > 10 ? ' readonly="readonly"' : '') + ' /></td>\n\
				<td style="width: 25%"><input type="text" class="in_sku form-control" name="item[' + id + '][sku]" value="' + (pitems[id] ? (pitems[id].sku ? pitems[id].sku : '') : '') + '"'+(status > 10 ? ' readonly="readonly"' : '') + ' /></td>\n\
				<td style="width: 20%"><input type="text" class="in_uq form-control" name="item[' + id + '][uq]" value="' + (pitems[id] ? (pitems[id].uq ? pitems[id].uq : '') : '') + '"' + (status > 10 ? ' readonly="readonly"' : '') + ' /></td>\n\
				</tr>');
			id += 1;
		}
		calcTot();
	}
	addItem(pitems ? pitems.length : 1);

	$('.moreitem').off('click').on('click', function(e) {
		addItem(1);
		e.preventDefault();
	});

	$('#return_items').off('keyup', '.in_uq').on('keyup', '.in_uq', function() {
		calcTot();
	});
});
</script>