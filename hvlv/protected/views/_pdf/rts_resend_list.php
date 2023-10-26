<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait --page-size A4" />
<?php //--disable-smart-shrinking ?>
<title>Top Logistics Invoice</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
header { padding-bottom: 15px; }
footer { padding-top: 10px; page-break-after: always; }
</style>
</head>

<body width="1120">
<header>
<?php
$hdr = '_header_tla.php';
if(!empty($behalf)){
	switch($behalf){
		case 'priority':
			$hdr = '_header_tla.php';
		break;
	}
	if(!empty($inv->mdata['suborg'])){
		$sorg = Org::model()->findByPk($inv->mdata['suborg']);
		$inv->mdata['name'] = $sorg->name;
		$inv->mdata['address'] = $sorg->getAddress();
	}
}
if(Yii::app()->name == 'TLA'){
	$hdr = '_header_tla.php';
}
include($hdr);
?>
<h1>Shipment RTS Waiting Resend List</h1>
<table width="100%" cellspacing="0" cellpadding="0">
</table>
</header>
<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
					<th align="left" width="180">Location</th>
					<th align="left"width="430">HBN</th>
					<th align="right" width="125">REF</th>
					<th align="right" width="80">Barcode</th>
					<th align="right" width="125">newRef</th>
				</tr>
		</thead>
		<tbody>
		<?php
		foreach($data as $i =>$d)
		{
			echo '<tr class="'. ($i%2 == 1? 'even' : 'odd') . '"><td align="right">'.$d->getRTSLocation(true).'</td><td align="right">'.$d->originalShipment->hbn.'</td><td align="right">'.$d->originalShipment->ref.'</td><td align="right">'.$d->getRTSBarcodes(true).'</td><td align="right">'.$d->newShipment->ref.'</td></tr>';
		}
		?>
		</tbody>
</table>



</footer>
<?php include('_pagination.php'); ?>
</body>
</html>
