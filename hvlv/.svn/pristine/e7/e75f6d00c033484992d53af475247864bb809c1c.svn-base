<style>
.demo--label{margin:0;display:inline-block}
.demo--radio{display:none}
.demo--radioInput{background-color:#fff;border:1px solid rgba(0,0,0,0.15);display:inline-block;height:16px;margin-top:-1px;vertical-align:middle;width:16px;line-height:1}
.demo--radio:checked + .demo--radioInput:after{background-color:#57ad68;border-radius:100%;content:"";display:inline-block;height:10px;margin:2px;width:10px}
.demo--checkbox.demo--radioInput,.demo--radio:checked + .demo--checkbox.demo--radioInput:after{border-radius:0}
</style>
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink'=>CHtml::link('主页', array('site/index')),
	'links' => array(
		'我要投保',
	),
));
?>
<br>
<div class="panel panel-success">
	<div class="panel-heading">我要投保</div>
	<div class="panel-body">
		<form role="form" action="<?=$this->createUrl('order/purchase')?>" method="get">
			<?php
				if (empty($shipments)) {
					echo '<div class="form-group"><h3>无运单信息匹配</h3></div>';
				} else {
					if (!empty($test)) {
						echo '<div class="form-group"><input type="hidden" value="' . $test . '" name="test"></div>';
					}
			?>
			<table class="table table-bordered" style="margin-bottom:0;">
				<thead>
					<th></th>
					<th>运单号</th>
					<th>状态</th>
					<th>收件人</th>
					<th>联系方式</th>
					<th>地址</th>
					<th>投保金额</th>
				</thead>
				<tbody>
					<?php foreach ($shipments as $index => $shipment) { ?>
					<tr <?php if ($shipment['status'] == 0) echo 'class="danger"'; 
										else if ($shipment['status'] == 1) echo 'class="success"';
										else if ($shipment['status'] == 2) echo 'class="info"'; ?>>
						<td width="1%">
							<?php if ($shipment['status'] == 1) { ?>
							<div class="demo--label"">
								<input type="checkbox" name="shipments[<?=$shipment['id']?>]" value="<?=$shipment['id']?>" class="demo--radio" />
								<span class="demo--checkbox demo--radioInput" id="shipments[<?=$shipment['id']?>]"></span>
							</div>
							<?php } ?>
						</td>
						<td><?=$index?></td>
						<td>
							<?php if ($shipment['status'] == 0) {
								echo '<span class="text-danger">不存在</span>';
							} else if ($shipment['status'] == 1) {
								echo '<span class="text-success">未投保</span>';
							} else if ($shipment['status'] == 2) {
								echo '<span class="text-success">已投保</span>';
							} ?>
						</td>
						<td><?=$shipment['cnee']?></td>
						<td><?=$shipment['tel']?></td>
						<td><?=$shipment['address']?></td>
						<td>
							<?php if ($shipment['status'] == 0) {
								echo '<span class="text-muted">$0.00</span>';
							} else if ($shipment['status'] == 1) {
								echo '$5.50';
							} else if ($shipment['status'] == 2) {
								echo '<span class="text-muted">$5.50 (已付)</span>';
							} ?>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td><div class="demo--label"">
								<input type="checkbox" class="demo--radio" name="checkall" />
								<span class="demo--checkbox demo--radioInput" id="checkall"></span>
							</div></td><td></td><td></td><td></td><td></td><td class="text-right">总计：</td><td>$<span id="amount">0.00</span></td>
					</tr>
				<tbody>
			</table>
			<div class="form-group" style="margin-bottom:0;">
				<label class="radio-inline">
					<h3><input type="radio" name="optionRadio" id="poli"  value="poli" style="margin-top: 12px;" checked /><img src="<?=Yii::app()->createAbsoluteUrl('/')?>/images/poli.png" width="100" /></h3>
				</label>
				<label class="radio-inline">
					<h3><input type="radio" name="optionRadio" id="paypal"  value="paypal" style="margin-top: 8px;" /><img src="<?=Yii::app()->createAbsoluteUrl('/')?>/images/paypal.png" width="100" /></h3>
				</label>
				<label class="radio-inline">
					<h3><input type="radio" name="optionRadio" id="wechatpay"  value="wechatpay" style="margin-top: 21px;" /><img src="<?=Yii::app()->createAbsoluteUrl('/')?>/images/wechatpay.png" width="100" /></h3>
				</label>
				<label class="radio-inline">
					<h3><input type="radio" name="optionRadio" id="aplipay" value="alipay" style="margin-top: 18px;" /><img src="<?=Yii::app()->createAbsoluteUrl('/')?>/images/alipay.png" width="100" /></h3>
				</label>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-success">支付</button>
				<a class="btn btn-default" href="<?=$this->createUrl('site/index')?>">返回</a>
			</div>
			<?php		
			}
			?>
		</form>
	</div>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function () {
	$('span[id^=shipment]').on('click', function() {
		var id = $(this).attr('id');
		$('input:checkbox[name="' + id + '"]').click();
	});

	$('input:checkbox[name^=shipment]').on('click', function() {
		var num = $('input:checkbox[name^=shipment]:checked').length;
		$('#amount').text((num * 5.5).toFixed(2));
	});

	$('#checkall').on('click', function() {
		$('input:checkbox[name="checkall"]').click();
		$('span[id^=shipment]').click();
	})
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>