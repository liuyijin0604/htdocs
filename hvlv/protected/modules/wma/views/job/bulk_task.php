<style type="text/css">
video {
	object-fit: cover;
	transform: scale(0.3, 0.3); -webkit-transform: scale(0.3, 0.3);
	transform-origin: 0 0; -webkit-transform-origin: 0 0;
}
</style>
<div class="content-padded">
	<div id="action" class="tab-pane active">
		<h3><?=$type === 'in' ? 'Pallets In' : 'Pallets Out';?> <span class="exp-ef icon icon-down" style="display:none;"></span></h3>
		<div class="entry-form">
			<form action="<?=$this->createUrl('job/bulkTask', array('ct' => $ct, 'type' => $type))?>" method="post" data-bit="2">
				<?php if ($type === 'in') { ?>
					<div class="input-addon">
						<input class="plt barcode required" type="search" placeholder="Pallet Barcode" name="plt" />
						<span><div class="toggle kplt"><div class="toggle-handle"></div></div></span>
					</div>
					<div class="input-addon">
						<input class="prod barcode required" type="search" placeholder="Product Name/Barcode" name="prod" />
						<span><div class="toggle kprod"><div class="toggle-handle"></div></div></span>
					</div>
				<?php } else { ?>
					<input class="plt barcode required" type="search" placeholder="Pallet Barcode" name="plt" />
				<?php } ?>
				<button type="submit" class="btn btn-primary btn-block" name="search"><span class="icon icon-search"></span>Search</button>
			</form>
		</div>
		<div class="res"></div>
		<div class="his"></div>
	</div>

	<div id="plt" class="tab-pane"></div>
</div>
<div id="summary" class="tab-pane">
</div>

<div class="segmented-control tabs bottom">
	<a class="control-item active" data-pane="action">Action</a>
	<?php if ($type === 'in') { ?>
		<a class="control-item" data-pane="plt">Pallet</a>
	<?php } ?>
	<a class="control-item" data-pane="summary">Sum</a>
</div>

<script>
	$(function() {
		$('.entry-form input.plt').focus().on('keydown', function(e) {
			if (e.which == 13) {
				$(this).trigger('afterBarcode');
				return false;
			}
		}).on('afterBarcode', function() {
			if ($('.entry-form .kprod').hasClass('active') || $('input.prod').length == 0) {
				$('.entry-form form').submit();
				return true;
			}
			if ($('input.prod').length > 0) $('input.prod').focus();
		});

		$('.entry-form input.prod').on('afterBarcode', function() {
			if ($('input.plt').length == 0 || $('input.plt').val() != '') $('.entry-form form').submit();
		});

		$('span.exp-ef').on('click touchend', function() {
			$('.entry-form').slideDown();
			$('span.exp-ef').hide();
		});

		$('.entry-form form').on('success', function(e, r) {
			wmaApp.btnLoading($('button[type=submit]', this), true);
			$('.res').html(r.data);
			$('div.his').empty();
			if (r.done) {
				$('.entry-form').slideUp();
				$('span.exp-ef').show();
				if ($('input.rplt.required').length > 0) {
					$('input.rplt.required').focus();
				} else {
					$('input.uqty').focus();
				}
				if ($('.entry-form form .kprod').hasClass('active')) {
					$('.data-form form input').each(function() {
						var lv = $(this).data('last') || false;
						if (lv) $(this).val(lv);
					});
				}
			} else {
				if ($('input.prod').length > 0) $('input.prod').val('').focus();
				else $('input.plt').val('').focus();
			}
			$('.his').trigger('showHistorys');
		});

		$('.data-form form').on('error', function(e, r) {
			$('.entry-form').slideDown();
			$('span.exp-ef').hide();
			$('.res').empty();
			$('.his').trigger('showHistorys');
		});

		$('#ajax-modal').on('success', '.data-form form', function(e, r) {
			$('.entry-form').slideDown();
			$('span.exp-ef').hide();
			$('.res').empty();
			var ef = $('.entry-form form');
			if ($('.kplt', ef).hasClass('active') || $('.plt', ef).length == 0) {
				$('.prod', ef).focus();
			} else {
				$('.plt', ef).val('');
			}
			if ($('.kprod', ef).hasClass('active') || $('.prod', ef).length == 0) {
				$('.plt', ef).focus();
			} else {
				$('.prod', ef).val('');
			}
			$('.his').trigger('showHistorys');
		});

		$('.his').on('showHistorys', function() {
			$(this).load('job/historys?ct=' + '<?=$ct?>');
		}).on('touchend', 'a.del_item', function() {
			if (wmaApp.isScrolling) return;
			if (window.prompt('Please enter "' + $(this).data('vc') + '" to confirm delete') == $(this).data('vc')) {
				if (window.confirm('Are you sure to delete?')) {
					$.get($(this).attr('href'), function() {
						$('.his').trigger('showHistorys');
					});
				}
			}
			return false;
		}).trigger('showHistorys');

		$('#plt').on('showing', function() {
			$(this).load('<?=$this->createUrl("job/bulkTaskPlt", ["ct" => $ct]);?>');
		});

		$('#summary').on('showing', function() {
			$(this).load('<?=$this->createUrl("job/bulkTaskSummary", ["ct" => $ct]);?>');
		});
	});
</script>