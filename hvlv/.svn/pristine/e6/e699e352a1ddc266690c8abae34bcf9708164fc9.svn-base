<div class="content-padded">
	<h3>Storage Out</h3>
	<div class="storagesearch-form">
		<form action="<?=$this->createUrl('job/storage', array('type' => 'search'))?>" method="post">
			<input class="parcel barcode required" type="search" placeholder="Parcel Barcode" name="parcel" />
			<button type="submit" class="btn btn-primary btn-block"><span class="icon icon-search"></span>Search</button>
		</form>
	</div>
</div>

<script type="text/javascript">
$(function() {
	$('.storagesearch-form form').on('success', function(e, r) {
		$('#main-content').html(r.data);
	});
});
</script>