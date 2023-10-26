<div class="content-padded ">
<h3>Batch Picking</h3>
<div class="picking-form">
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
	$('.picking-form input.batch_no').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
		}
	}).on('afterBarcode', function(e) {
		$('.picking-form form').submit();
	});

	$('.picking-form form').on('success', function(e, r) {
		$('.res').html(r.data);
		$('.picking-form').hide();
	});
});
</script>