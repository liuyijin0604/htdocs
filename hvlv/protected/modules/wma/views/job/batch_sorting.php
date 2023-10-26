<div class="content-padded ">
<h3>Batch Sorting</h3>
<div class="sorting-form">
<form action="" method="post" data-bit="1">
	<input class="batch_no barcode required" type="search" placeholder="Batch NO" name="batch_no" />
	<input class="shelf_number barcode required" type="search" placeholder="Shelf NO" name="shelf_number" />
	<button type="submit" class="btn btn-primary btn-block">Submit</button>
</form>
</div>
<div class="res">
</div>
</div>

<script type="text/javascript">
$(function(){
	$('.sorting-form input.batch_no').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
		}
	}).on('afterBarcode', function(e) {
		if ($('.sorting-form input.shelf_number').val()) {
			$('.sorting-form form').submit();
		} else {
			$('.sorting-form input.shelf_number').focus();
		}
	});

	$('.sorting-form input.shelf_number').on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
		}
	}).on('afterBarcode', function(e) {
		$('.sorting-form form').submit();
	});

	$('.sorting-form form').on('success', function(e, r) {
		$('.res').html(r.data);
		$('.sorting-form').hide();
	});
});
</script>