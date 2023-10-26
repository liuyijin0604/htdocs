<div class="content-padded ">
<h3>Scan Out</h3>
<div class="scanout-form">
<form action="" method="post" data-bit="1">
	<input class="ship barcode required" type="search" placeholder="Parcel Ref" name="ship" />
	<button type="submit" class="btn btn-primary btn-block">Submit</button>
</form>
</div>
<div class="undo">
</div>
<div class="res">
</div>
</div>

<script type="text/javascript">
$(function(){
	$('input.ship').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$('.scanout-form form').submit();
		}
	});

	$('.scanout-form form').on('success', function(e, r) {
		$('input.ship').focus();
	});
});
</script>
