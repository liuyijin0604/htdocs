<div class="label-form">
<form action="<?=$this->createUrl('job/label', ['id' => $model->id]);?>" method="post" data-bit="3">
	<div class="input-addon">
		<input class="shipment barcode required" type="search" placeholder="Courier Barcode" name="shipment" />
		<span><div class="toggle kshipment"><div class="toggle-handle"></div></div></span>
	</div>
	<div class="input-addon">
		<input class="prod barcode required" type="search" placeholder="Product Barcode" name="prod" />
		<span><div class="toggle kprod"><div class="toggle-handle"></div></div></span>
	</div>
</form>
</div>
<div class="res"></div>
<br />
<div class="hisLabel"></div>

<script type="text/javascript">
$(function() {

	$('.label-form input.barcode').on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function() {
		var done = true;
		$('.label-form input.barcode').each(function() {
			if ($(this).val() == '') {
				$(this).focus();
				done = false;
			}
		});

		if (done) {
			$('.label-form form').submit();
			return true;
		}
	});

	$($('.label-form input.barcode')[0]).focus();

	var result_id = 1;
	$('.label-form').on('success', function(e, r) {
		if (r.done) {
			$('.res').append('<span class="result" style="color:green" id="result_' + (result_id++) + '">' + r.msg + '<br /></span>');
		} else {
			$('.res').append('<span class="result" style="color:red" id="result_' + (result_id++) + '">' + r.msg + '<br /></span>');
		}
		$('.hisLabel').trigger('showHistory');

		let id = result_id;

		setTimeout(function() {
			$('.res #result_' + (id-2)).fadeOut(800, function() {
				$('.res #result_' + (id-2)).remove();
			});
		}, 1500);

		wmaApp.btnLoading($('button[type=submit]', this), true);
	});

	if (!$('.label-form .kshipment').hasClass('active')) {
		$('input.shipment').val('');
		$('input.shipment').trigger('focus');
	} else if (!$('.label-form .kprod').hasClass('active')) {
		$('input.prod').val('');
		$('input.prod').trigger('focus');
	} else {
		$('input.shipment').trigger('focus');
	}

	$('.hisLabel').on('showHistory', function() {
		$(this).load('<?=$this->createUrl('job/historyLabel', ['id' => $model->id]);?>');
	}).on('touchend', 'a.del_item', function() {
		if (wmaApp.isScrolling) return;
		if (window.prompt('Please enter "' + $(this).data('vc') + '" to confirm delete') == $(this).data('vc')) {
			if (window.confirm('Are you sure to delete?')) {
				$.get($(this).attr('href'), function() {
					$('.hisLabel').trigger('showHistory');
				});
			}
		}
		return false;
	}).trigger('showHistory');

});
</script>