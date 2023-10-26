<div class="content-padded">
	<h3>Storage Out</h3>
	<div class="storageout-form">
		<div class="suggest">
			<?php echo $slogs; ?>
		</div>
		<form action="<?=$this->createUrl('job/storage', array('type' => 'out'))?>" method="post">
			<input class="parcel barcode required" type="search" placeholder="Parcel Barcode" name="parcel" />
			<div class="storages" style="margin-left: 5px;"></div>
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

	$('.storageout-form form').on('success', function(e, r) {
		if (r.slogs) {
			$('.suggest').html(r.slogs);
		} else {
			$('.suggest').html('');
		}
	});
});
</script>