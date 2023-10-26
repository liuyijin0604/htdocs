<style>
.plt-form .row .plt-col-4 {
	line-height: 33px;
}
</style>

<div class="content-padded">
	<div class="plt-form">
		<form action="<?=$this->createUrl('job/setPltInfo')?>" method="post" data-bit="1">
			<input class="barcode rplt required" type="search" name="plt" placeholder="Pallet" />
			<?php $model = new WmsLocation; ?>
			<div id="_plt">
				<?php include('_plt.php') ?>
			</div>
			<button type="submit" class="btn btn-primary btn-block">Save</button>
		</form>
	</div>
</div>

<script type="text/javascript">
$(function(){
	$('.plt-form input.rplt').on('keydown', function(e) {
		if (e.which == 13) {
			var plt = $(this).val();
			$.ajax({
				method: 'GET',
				url: '<?=Yii::app()->createUrl("job/getPltInfo")?>',
				data: { 'plt': plt },
				success: function(r) {
					r = JSON.parse(r);
					$('#_plt').html(r.data);
				}
			});
			return false;
		}
	});

	$('.plt-form form').on('success', function(e, r) {
		$('.plt-form input.rplt').val('');
		$('#_plt').html(r.data);
	});
});
</script>