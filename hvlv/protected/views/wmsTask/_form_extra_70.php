<div class="row grid-view">
<table id="mtsk_itms" class="items tsk_item">
<thead><tr><th width="25">#</th><th width="500">Product</th><th width="40">SKU</th><th width="40">Qty</th></tr></thead>
<tbody>
</tbody>
</table>
</div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	var t = $('#mtsk_itms', panel);

	t.data('line', '<tr><td class="sn"></td><td><input type="text" class="in_sn" name="sn" style="width:100%" /></td><td><input type="text" class="in_sku" name="sku" style="width:100%" /></td><td><input type="text" class="in_uq" name="uq" style="width:100%" /></td></tr>');
});
</script>