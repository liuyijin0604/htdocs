<div>
	<?php if ($result == 'success' || $result == 'SUCCESS') { ?>
	<h1><span class="text-success">支付成功，将在2秒后跳转</span></h1>
	<script>
		setTimeout(function () {
			window.location.href = '<?=$this->createUrl('site/index')?>';
		}, 4000);
	</script>
	<?php } else { ?>
	<h1><span class="text-danger">取消成功，将在2秒后跳转</span></h1>
	<script>
		setTimeout(function () {
			window.location.href = '<?=$this->createUrl('site/index')?>';
		}, 4000);
	</script>
	<?php } ?>
</div>
