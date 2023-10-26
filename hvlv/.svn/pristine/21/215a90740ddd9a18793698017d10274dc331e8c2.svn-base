<div class="content-padded">
	<h3>Storage In</h3>
	<div class="storagein-form">
		<form action="<?=$this->createUrl('job/storage', array('type' => 'in'))?>" method="post">
			<input class="parcel barcode required" type="search" placeholder="Parcel Barcode" name="parcel" />
			<div class="suggest"></div>
			<input class="storage barcode required" type="search" placeholder="Location Barcode" name="storage" />
			<input class="quantity required" type="search" placeholder="Quantity" name="qty" />
			<button type="submit" class="btn btn-primary btn-block">Save</button>
		</form>
	</div>
</div>

<script type="text/javascript">
$(function() {
	$('input.parcel').focus().on('keydown', function(e) {
		if (e.which == 13 && $('input.storage').val() == '') {
			$.ajax({
				'type': 'post',
				'url': '<?=$this->createUrl('job/storageSuggest')?>',
				'data': { 'parcel' : $(this).val() },
				success: function(r) {
					r = JSON.parse(r);
					$('.suggest').html(r.result);
				}
			});
			$('input.storage').focus();
			return false;
		} else if (e.which == 13 && $('input.quantity').val() == '') {
			$('input.quantity').focus();
			return false;
		}
	});

	$('input.storage').on('keydown', function(e) {
		if (e.which == 13 && $('input.parcel').val() == '') {
			$('input.parcel').focus();
			return false;
		} else if (e.which == 13 && $('input.quantity').val() == '') {
			$('input.quantity').focus();
			return false;
		}
	});

	$('.storagein-form form').on('success', function() {
		$('.suggest').html('');
	});
});
</script>