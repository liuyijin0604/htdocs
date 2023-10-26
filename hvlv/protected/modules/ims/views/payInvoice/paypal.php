<!DOCTYPE html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
</head>

<body>
<h2>Payment for Invoice <?=$inv->no;?></h2>
<?php
$paid = $inv->paid();
$bal = $inv->getBalance();
$psc = (($bal+0.3)/0.974)-$bal;
$tot = $inv->total;
?>
<table style="font-size:1.2em;min-width:500px;margin-bottom:2em;">
	<thead><tr><th align="left">Item</th><th align="right">Amount</th></tr></thead>
	<tbody>
		<tr><td>Invoice <?=$inv->no;?> Total</td><td align="right">$<?=AppHelper::money_format(null, $inv->total);?></td></tr>
		<?php if($paid > 0):?>
		<tr><td>Applied Amount</td><td align="right">-$<?=AppHelper::money_format(null, $paid);?></td></tr>
		<?php endif;?>
		<tr><td>PayPal Charge </td><td align="right">$<?=AppHelper::money_format(null, $psc);?></td></tr>
	<tbody>
	<tfoot style="font-size:1.5em;">
		<tr><th align="right">Total</th><th align="right">$<?=AppHelper::money_format(null, $bal+$psc);?></th></tr>
	</tfoot>
</table>
<script src="https://www.paypal.com/sdk/js?client-id=AVL9VKfCUXcM4engbDS_5i4Lb0T5nJXo9inEBBh5XJJtS4Gl0EZ6y7sglZYqEwRSrGIrZACU7TFQoQAp&locale=en_AU&currency=<?=Invoice::$currencies[$inv->currency]?>"></script>
<script src="/js/jquery.min.js"></script>
<div id="paypal-button-container"></div>
<script>
	var total = <?=$tot+$psc;?>;
	var btn = paypal.Buttons({
	createOrder: function() {
		return fetch('<?=$this->createUrl('payInvoice/paypal', ['action' => 'createOrder', 'token' => $_GET['token']]);?>', {
			method: 'post',
			headers: {
				'content-type': 'application/json'
			}
		}).then(function(res) {
			return res.json();
		}).then(function(data) {
			return data.orderID;
		});
	},
	onApprove: function(data) {
		return fetch('<?=$this->createUrl('payInvoice/paypal', ['action' => 'captureOrder']);?>', {
			method: 'post',
			headers: {
				'content-type': 'application/json'
			},
			body: JSON.stringify({
				orderID: data.orderID
			})
		}).then(function(res) {
			return res.json();
		}).then(function(details) {
			$('#paypal-button-container').html('<h3>Thank you for your payment</h3>');
		});
	}
	});
	if(total > 0) btn.render('#paypal-button-container');
	else $('#paypal-button-container').html('<h3>This invoice is already paid, no payment required</h3>');
</script>
</body>