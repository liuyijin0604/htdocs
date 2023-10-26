<div class="content-padded ">
<h3>Batch Sorting Check</h3>
<div class="sorting-check-form">
<form action="" method="post" data-bit="1">
	<input class="batch_no barcode required" type="search" placeholder="Batch NO" name="batch_no" />
	<button type="submit" class="btn btn-primary btn-block">Submit</button>
</form>
</div>
<div class="res">
</div>
</div>

<script type="text/javascript">
$(function(){
	$('.sorting-check-form input.batch_no').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(e) {
		$('.sorting-check-form form').submit();
	});

	$('.sorting-check-form form').on('success', function(e, r) {
		$('.res').html(r.data);
		$('.sorting-check-form').hide();
	});
});
</script>