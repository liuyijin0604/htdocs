<div>
	<?php if ($result == 'success' || $result == 'SUCCESS') { ?>
	<h1><span class="text-success">重置成功，新密码已发送您的邮箱，将在2秒后跳转至首页</span></h1>
	<script>
		setTimeout(function () {
			window.location.href = '<?=$this->createUrl('site/index')?>';
		}, 4000);
	</script>
	<?php } else { ?>
	<h1><span class="text-danger">重置失败，将在2秒后跳转</span></h1>
	<script>
		setTimeout(function () {
			window.location.href = '<?=$this->createUrl('accounts/resetpassword')?>';
		}, 4000);
	</script>
	<?php } ?>
</div>