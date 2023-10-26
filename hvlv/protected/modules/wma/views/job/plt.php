<style>
div.del_item {
	color: #007aff;
}
.plt-form label {
	font-size: 13px;
}
.plt-form .row .plt-col-4 {
	line-height: 33px;
}
</style>

<div class="plt-form">
	<form action="<?=$this->createUrl('job/plt', ['id' => $model->id])?>" method="post" data-bit="1">
		<input class="barcode rplt required" type="search" name="pl" placeholder="Pallet" />
		<?php $model = new WmsLocation; ?>
		<div id="_plt">
			<?php include('_plt.php') ?>
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
		$('.plt-his .table-view').append('<li class="table-view-cell table-view-cell-full" id="' + v.plt + '"><div class="row"><div class="col-8" align="left">' + v.plt + '</div><div class="col-4" align="left">' + (v.item ? v.item : '') + '</div><div class="col-4" align="left">' + (v.type ? (v.type == 'plastic' ? '塑料板' : '熏蒸板') : '') + '</div><div class="col-4" align="left">' + (v.port ? v.port : '') + '</div><div class="col-4" align="left">L: ' + (v.length ? v.length : 0) + 'cm</div><div class="col-4" align="left">W: ' + (v.width ? v.width : 0) + 'cm</div><div class="col-4" align="left">H: ' + (v.height ? v.height : 0) + 'cm</div><div class="col-4" align="left">Wt: ' + (v.weight ? v.weight : 0) + 'kg</div></div></li>');
	});

	$('.plt-form form').on('success', function(e, r) {
		if ($('#' + r.plt).html()) {
			$('#' + r.plt).html('<div class="row"><div class="col-8" align="left">' + r.plt + '</div><div class="col-4" align="left">' + (r.item ? r.item : '') + '</div><div class="col-4" align="left">' + (r.type ? (r.type == 'plastic' ? '塑料板' : '熏蒸板') : '') + '</div><div class="col-4" align="left">' + (r.port ? r.port : '') + '</div><div class="col-4" align="left">L: ' + (r.length ? r.length : 0) + 'cm</div><div class="col-4" align="left">W: ' + (r.width ? r.width : 0) + 'cm</div><div class="col-4" align="left">H: ' + (r.height ? r.height : 0) + 'cm</div><div class="col-4" align="left">Wt: ' + (r.weight ? r.weight : 0) + 'kg</div></div>');
		} else {
			$('.plt-his .table-view').append('<li class="table-view-cell table-view-cell-full" id="' + r.plt + '"><div class="row"><div class="col-8" align="left">' + r.plt + '</div><div class="col-4" align="left">' + (r.item ? r.item : '') + '</div><div class="col-4" align="left">' + (r.type ? (r.type == 'plastic' ? '塑料板' : '熏蒸板') : '') + '</div><div class="col-4" align="left">' + (r.port ? r.port : '') + '</div><div class="col-4" align="left">L: ' + (r.length ? r.length : 0) + 'cm</div><div class="col-4" align="left">W: ' + (r.width ? r.width : 0) + 'cm</div><div class="col-4" align="left">H: ' + (r.height ? r.height : 0) + 'cm</div><div class="col-4" align="left">Wt: ' + (r.weight ? r.weight : 0) + 'kg</div></div></li>');
		}

		if (!$('.klength').hasClass('active')) {
			$('input[name="meta[length]"]').val('');
		}
		if (!$('.kwidth').hasClass('active')) {
			$('input[name="meta[width]"]').val('');
		}
		if (!$('.kheight').hasClass('active')) {
			$('input[name="meta[height]"]').val('');
		}
		if (!$('.kweight').hasClass('active')) {
			$('input[name="meta[weight]"]').val('');
		}

		$('.plt-form input.rplt').val('');
		$('.plt-form input.rplt').focus();

		$('.plt-form input[name="prod"]').val('');
		$('.plt-form input[name="qty"]').val('');
		$('.plt-form textarea[name="note"]').val('');
	});

	$('.plt-form input.rplt').on('keydown', function(e) {
		if (e.which == 13) {
			var plt = $(this).val();
			$.ajax({
				method: 'GET',
				url: '<?=Yii::app()->createUrl("job/getPltInfo")?>',
				data: { 'plt': plt },
				success: function(r) {
					r = JSON.parse(r);
					$('#_plt').html(r.data);
				}
			});
			return false;
		}
	});

	$('.plt-his').on('touchend', 'div.del_item', function() {
		if (wmaApp.isScrolling) return;
		if (window.prompt('Please enter "' + $(this).data('vc') + '" to confirm delete') == $(this).data('vc')) {
			if (window.confirm('Are you sure to delete?')) {
				var item = $(this);
				$.ajax({
					method: 'POST',
					url: $(this).attr('href'),
					success: function() {
						item.parent().parent().parent().remove();
					}
				});
			}
		}
	});
});
</script>