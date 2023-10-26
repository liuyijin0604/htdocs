<div class="plt-form">
	<form action="<?=$this->createUrl('job/bulkTaskPlt', ['ct' => $ct])?>" method="post">
		<input class="barcode rplt required" type="search" name="pl" placeholder="Pallet" />
		<div class="row">
			<input type="text" placeholder="Length (cm)" name="meta[length]" style="width: 40%" value="1.15" /> cm
			<input type="text" placeholder="Width (cm)" name="meta[width]" style="width: 40%" value="1.15" /> cm
		</div>
		<div class="row">
			<input type="text" placeholder="Height (cm)" name="meta[height]" style="width: 40%" /> cm
			<input type="text" placeholder="Weight (kg)" name="meta[weight]" style="width: 40%" /> kg
		</div>
		<button type="submit" class="btn btn-primary btn-block">Save</button>
	</form>
</div>

<div class="plt-his">
	<ul class="table-view">
	</ul>
</div>

<script type="text/javascript">
$(function(){
	var plts = '<?=json_encode($plts)?>';
	plts = JSON.parse(plts);
	plts.forEach(function(v, k) {
		$('.plt-his .table-view').append('<li class="table-view-cell table-view-cell-full" id="' + v.plt + '"><div class="row"><div class="col-4" align="left">' + v.plt + '</div><div class="col-2" align="right">' + (v.length ? v.length : 0) + 'cm</div><div class="col-2" align="right">' + (v.width ? v.width : 0) + 'cm</div><div class="col-2" align="right">' + (v.height ? v.height : 0) + 'cm</div><div class="col-2" align="right">' + (v.weight ? v.weight : 0) + 'kg</div></div></li>');
	});

	$('.plt-form form').on('success', function(e, r) {
		if ($('#' + r.plt).html()) {
			$('#' + r.plt).html('<div class="row"><div class="col-4" align="left">' + r.plt + '</div><div class="col-2" align="right">' + (r.length ? r.length : 0) + 'cm</div><div class="col-2" align="right">' + (r.width ? r.width : 0) + 'cm</div><div class="col-2" align="right">' + (r.height ? r.height : 0) + 'cm</div><div class="col-2" align="right">' + (r.weight ? r.weight : 0) + 'kg</div></div>');
		} else {
			$('.plt-his .table-view').append('<li class="table-view-cell table-view-cell-full" id="' + r.plt + '"><div class="row"><div class="col-4" align="left">' + r.plt + '</div><div class="col-2" align="right">' + (r.length ? r.length : 0) + 'cm</div><div class="col-2" align="right">' + (r.width ? r.width : 0) + 'cm</div><div class="col-2" align="right">' + (r.height ? r.height : 0) + 'cm</div><div class="col-2" align="right">' + (r.weight ? r.weight : 0) + 'kg</div></div></li>');
		}
	});

	$('.plt-form input.rplt').on('keydown', function(e) {
		if (e.which == 13) {
			var plt = $(this).val();
			if ($('#' + plt)) {
				$('#' + plt).find('.col-2').each(function(k, v) {
					if (k == 0) {
						$('.plt-form').find('input[name="meta[length]"]').val(parseInt($(this).html()));
					} else if (k == 1) {
						$('.plt-form').find('input[name="meta[width]"]').val(parseInt($(this).html()));
					} else if (k == 2) {
						$('.plt-form').find('input[name="meta[height]"]').val(parseInt($(this).html()));
					} else if (k == 3) {
						$('.plt-form').find('input[name="meta[weight]"]').val(parseInt($(this).html()));
					}
				});
			}
			return false;
		}
	});
});
</script>