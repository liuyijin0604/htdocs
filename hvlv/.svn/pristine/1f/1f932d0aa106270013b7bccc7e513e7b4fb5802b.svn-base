<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="wkhtmltopdf" content="--dpi 100 --page-width 101 --page-height 152 -T 3 -R 3 -B 3 -L 3 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />
	<title>PCA Express Label</title>
	<style type="text/css">
	*{ margin: 0; padding: 0; letter-spacing: normal !important; }
	body{ font-family: Verdana, Geneva, sans-serif; font-size: 20px; text-rendering: optimize-speed; width: 700px; }
	p.logo { text-align: center; padding-bottom: 30px; }
	.barcode{ font-family: IDAutomationHC39M; font-size: 36px; padding: 30px; text-align: center; }
	h1.dest { padding: 30px; font-size: 60px; text-align: center; }
	h4 { font-size: 26px; }
	small { font-size: 16px; }
	div.page-break { page-break-after: always; }
	h2.connote { text-align: center; padding: 30px;}
	.sender { font-size: 17px; }
	table td{ padding: 10px; }
	table td table td { padding: 5px; }
	div.bc_center div{ margin: 0 auto; }
	.dto{ font-size: 22px; }
	hr { border: none; border-top: #000 1px solid;}
	</style>
</head>
<body width="700">
	<?php Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true); ?>
	<br/>
	<div style="border: 2px #000 solid;">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="65%" valign="top" class="dto">
					<h4>Delivery To:</h4>
					<?=$shipment->cnee->labelAddress();?>
				</td>
				<td style="border: 2px #000 solid;border-left: 4px #000 solid;" valign="middle">
					<h1 class="dest"><?=$shipment->getStatus();?></h1>
				</td>
			</tr>
		</table>
		<div style="border-top: 2px #000 solid; border-bottom: 2px #000 solid;">
			<h2 class="connote"><?=$shipment->hbn;?></h2>
		</div>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="65%" valign="top" class="di">
					<b>Delivery Instruction:</b>
					<p><?=nl2br($shipment->note);?></p>
					<b>Customer Ref:</b>
					<p><?=$shipment->cref?></p>
				</td>
				<td style="border-left: 2px #000 solid; padding: 0;" valign="top">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="wci">
						<?php if($shipment->weight > 0): ?>
							<tr>
								<td width="40%">Weight</td>
								<td><?=$shipment->weight;?> kg</td>
							</tr>
						<?php endif;
						if($shipment->cbm > 0): ?>
							<tr>
								<td>Cubic</td>
								<td><?=$shipment->cbm;?> m<sup>3</sup></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td>Item</td>
							<td><?=$shipment->pkg;?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div style="border-top: 2px #000 solid; border-bottom: 2px #000 solid; padding: 15px; text-align:center">
			<div>
				<?php $bc = new TCPDFBarcode($shipment->hbn, 'C128');
					echo $bc->getBarcodeSVGcode(3, 100);
				?>
			</div>
			<span style="font-size:1.4em"><?=$shipment->hbn;?></span>
		</div>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed">
			<tr>
				<td class="sender" width="40%" valign="top"><b>Sender:</b>
					<?php
					$name = $shipment->cnor->name;
					$org = $shipment->agent;
					if (!empty($org->extra['delivery_label_name'])) {
						$name = $org->extra['delivery_label_name'];
					}
					?>
					<p>
						<?=ucwords(strtolower($name));?><br />
						<p><?=$shipment->cnor->fullAddress(array('suburb', 'state', 'postcode'));?></p>
					</p>
				</td>
				<td align="center" width="60%" valign="top" style="border-left: 2px #000 solid;"><p>
					<?php
					$names = $shipment->eitems['g'];
					$qty = $shipment->eitems['q'];
					foreach ($names as $k => $name) {
						if ($name) {
							echo '<span>' . $name . ' x ' . $qty[$k] . '</span><br />';
						}
					}
					?>
				</p></td>
			</tr>
		</table>
	</div>
</body>
</html>
