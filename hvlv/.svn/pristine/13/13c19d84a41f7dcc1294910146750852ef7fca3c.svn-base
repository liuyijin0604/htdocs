<div>
	<?php if (!empty($result)) { ?>
		<?php if ($result == 'success' || $result == 'SUCCESS') { ?>
		<h1><span class="text-success">支付成功，将在2秒后跳转</span></h1>
		<script>
			setTimeout(function () {
				window.location.href = '<?=$this->createUrl('order/history')?>';
			}, 4000);
		</script>
		<?php } else { ?>
			<?php if (empty($msg)) { ?>
			<h1><span class="text-danger">取消成功，将在2秒后跳转</span></h1>
			<script>
				setTimeout(function () {
					window.location.href = '<?=$this->createUrl('site/index')?>';
				}, 4000);
			</script>
			<?php } else { ?>
			<h1><span class="text-danger"><?=$msg?>, redirect in 2 seconds...</span></h1>
			<script>
				setTimeout(function () {
					window.location.href = '<?=$this->createUrl('order/make')?>';
				}, 4000);
			</script>
			<?php } ?>
		<?php } ?>
	<?php } else if (!empty($invoice)) { ?>
		<p>
			<h2><span class="text-success">请银行转账至</span></h2><br />
			<h3>Bank: CommonWealth Bank Australia<br />
			Account Name: PCA Express Parcel<br />
			BSB Number: 062005<br />
			Account Number: <b style="background: #ff0">11563100</b> (new account no)<br />
			Reference: <?=$invoice?> <span class="text-danger">（*必须添加作为凭证）</span></h3>
		</p>
	<?php } ?>
</div>
