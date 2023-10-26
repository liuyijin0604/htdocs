<div class="content-padded">
	<h1>Quick Label <span id="count" style="color: red"><?=WmsTask::getQuickCourierLabelTasksRemain()?></span></h1>
	<div class="courier-form">
	<form action="<?=$this->createUrl('job/quickCourierLabel');?>" method="post" data-bit="3">
		<input class="prod barcode required" type="search" placeholder="Courier Barcode" name="courier" />
		<button type="submit" class="btn btn-primary btn-block" name="search"><span class="icon icon-search"></span>Complete</button>
	</form>
	</div>
	<div class="res"></div>
</div>

<script type="text/javascript">
$(function() {
	let lock = false;

	$('.courier-form input.prod').on('keydown', function(e){
		if(e.which == 13){
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(){
		window.clearInterval(retry);
		var retry = window.setInterval(function() {
			if (!lock) {
				lock = true;
				$('.courier-form form').submit();
				window.clearInterval(retry);
			}
		}, 1e1);
		return true;
	});
	
	$($('.courier-form input.prod')[0]).focus();

	$('.courier-form').on('success', function(e, r) {
		lock = false;
		if (r.done) {
			$('.courier-form input.prod').val('');
			$('.res').html('<span style="color:green">' + r.msg + '</span>' + $('.res').html());

			$('#count').html(parseInt($('#count').html())-1);
			
			var audio = new Audio();
			audio.src = 'https://os.toplogistics.com.au/site/voice/confirmed.mp3';
			audio.play();
		} else {
			$('.res').html('<span style="color:red">' + r.msg + '</span>' + $('.res').html());
			
			var audio = new Audio();
			audio.src = 'https://os.toplogistics.com.au/site/voice/beep_err.mp3';
			audio.play();
		}
		wmaApp.btnLoading($('button[type=submit]', this), true);
	});
});
</script>