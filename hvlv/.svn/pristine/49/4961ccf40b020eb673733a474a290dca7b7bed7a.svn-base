<h1>
	<?php
	$no = explode('-', $batch_no);
	if ($no[2] == 1) {
		$no[2] = 'Single';
	} else if ($no[2] == 2) {
		$no[2] = 'Multi';
	}
	echo implode(' - ', $no);
	?>
</h1>
<div class="sorting-check-entry-form">
<form action="<?=$this->createUrl('job/batchSortingCheckEntry')?>" method="post" data-bit="1">
	<input class="prod barcode required" type="search" placeholder="Product Barcode" name="ean" />
	<input type="hidden" name="batch_no" value="<?=$batch_no?>" />
	<button type="submit" class="btn btn-primary btn-block">Submit</button>
</form>
</div>
<div class="res-entry">
</div>
<div class="sorting-remain">
<?php
foreach ($remains as $no => $remain) {
	echo '<h4>' . $no . '</h4>';
	foreach ($remain as $item) {
		echo '<p style="margin-left: 10px;">' . $item[0] . '/' . $item[1] . '&times;' . $item[2] . '</p>';
	}
}
?>
</div>

<script type="text/javascript">
$(function(){
	$('input.prod').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$('.sorting-check-entry-form form').submit();
		}
	});

	$('.sorting-check-entry-form form').on('success', function(e, r) {
		$('input.prod').val('');
		$('input.prod').focus();
		$('.res-entry').html('<h1 style="font-size: 30px; color: green">' + r.msg + '</h1>');

		setTimeout(function() {
			$('.res-entry').html('');
		}, 10e3);
	}).on('error', function(e, r) {
		$('input.prod').val('');
		$('input.prod').focus();
		$('.res-entry').html('<h1 style="font-size: 30px; color: red">' + r.msg + '</h1>');

		setTimeout(function() {
			$('.res-entry').html('');
		}, 10e3);
	});
});
</script>
