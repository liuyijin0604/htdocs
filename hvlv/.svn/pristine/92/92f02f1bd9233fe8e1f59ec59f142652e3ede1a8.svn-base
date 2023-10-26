<style>
#report-tabs li.active a {
	background-color: #14487E;
	color: #FFFFFF;
}
</style>
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
		'Reports',
	),
));
?>
<br>
<?php
$consumer_backorder = $this->renderPartial('consumer_backorder', [], true);
$retailer_backorder = $this->renderPartial('retailer_backorder', [], true);
// $goods_return = $this->renderPartial('goods_return', [], true);
$minimum_stock_alert = $this->renderPartial('minimum_stock_alert', [], true);
$delivery_despatch_consumer = $this->renderPartial('delivery_despatch_consumer', [], true);
$delivery_despatch_retailer = $this->renderPartial('delivery_despatch_retailer', [], true);
$inbound_delivery_report = $this->renderPartial('inbound_delivery_report', [], true);
$inbound_delivery_advice = $this->renderPartial('inbound_delivery_advice', [], true);
$this->widget('application.extensions.booster.TbTabs', array(
	'id' => 'report-tabs',
	'type' => 'tabs',
	'tabs' => array(
		array(
			'label' => 'Consumer Backorder',
			'content' => $consumer_backorder,
			'active' => true,
		),
		array(
			'label' => 'Retailer Backorder',
			'content' => $retailer_backorder,
		),
		// array(
		// 	'label' => 'Goods Return',
		// 	'content' => $goods_return,
		// ),
		array(
			'label' => 'Minimum Stock Alert',
			'content' => $minimum_stock_alert,
		),
		array(
			'label' => 'Delivery Despatch Consumer',
			'content' => $delivery_despatch_consumer,
		),
		array(
			'label' => 'Delivery Despatch Retailer',
			'content' => $delivery_despatch_retailer,
		),
		array(
			'label' => 'Inbound Delivery Report',
			'content' => $inbound_delivery_report,
		),
		array(
			'label' => 'Inbound Delivery Advice',
			'content' => $inbound_delivery_advice,
		),
	),
));
?>

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('a.export_search').on('mousedown', function() {
		var type = $(this).prop('id');
		var q = $('#' + type + '-grid .filters input, .filters select').serialize();
		var href = $(this).data('baseurl') + '&' + q;
		href = href.replace('.app&', '?');
		$(this).attr('href', href);
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>