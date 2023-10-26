<div class="content-padded ">
	<h3>RTS Check</h3>
	<div class="rts-check-form">
		<form action="" method="post" data-bit="1">
			<input class="barcode required" type="search" placeholder="Barcode" name="barcode" />
			<button type="submit" class="btn btn-primary btn-block">Submit</button>
		</form>
	</div>
	<div class="res">
	</div>
</div>

<script type="text/javascript">
$(function(){
	$('.rts-check-form input.barcode').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
		}
	}).on('afterBarcode', function(e) {
		$('.rts-check-form form').submit();
	});

	$('.rts-check-form form').on('success', function(e, r) {
		var audio = new Audio();
		audio.src = 'https://os.toplogistics.com.au/site/voice/' + r['sound'] + '.mp3';
		audio.play();

		$('.rts-check-form input.barcode').val('');
		$('.rts-check-form input.barcode').focus();
	});
});
</script>