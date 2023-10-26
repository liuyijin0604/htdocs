<div class="courier-form">
<form action="<?=$this->createUrl('job/courier', ['id' => $model->id]);?>" method="post" data-bit="3">
	<input class="prod barcode required" type="search" placeholder="Product Name/Barcode" name="prod" />
	<input class="prod barcode required" type="search" placeholder="Courier Barcode" name="courier" />
	<button type="submit" class="btn btn-primary btn-block" name="search"><span class="icon icon-search"></span>Check</button>
</form>
</div>
<div class="res"></div>

<script type="text/javascript">
$(function() {

	$('.courier-form input.prod').on('keydown', function(e){
		if(e.which == 13){
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(){
		var done = true;
		$('.courier-form input.prod').each(function(){
			if($(this).val() == ''){
				$(this).focus();
				done = false;
			}
		});
		if(done){
			$('.courier-form form').submit();
			return true;
		}
	});
	
	$($('.courier-form input.prod')[0]).focus();

	$('.courier-form').on('success', function(e, r) {
		if (r.done) {
			$('.res').html('<span style="color:green">' + r.msg + '</span>');
		} else {
			$('.res').html('<span style="color:red">' + r.msg + '</span>');
		}
		wmaApp.btnLoading($('button[type=submit]', this), true);
	});
});
</script>