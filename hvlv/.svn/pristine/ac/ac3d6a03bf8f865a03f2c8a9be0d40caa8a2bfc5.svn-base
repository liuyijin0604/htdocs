<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>A4 L65</title>
<style type="text/css">
* { box-sizing: border-box; }
body { margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 10px; }
.qbox { width: 20%; padding: 5mm 2mm 2mm 2mm; float: left; height: 26.7mm; font-size: 10px; font-weight: bold; text-align: center; transform: scale(0.95); }
.qbox .name { clear: both; text-align: center; font-size: 10px; }
.qbox .barcode { float: left; position: relative; transform: scale(0.8); width: 100%; }
.qbox .label { clear: both; text-align: center; font-size: 10px; }
#tpl { display: none; }
.clear { clear: both; page-break-after: always; }
@media print {
	@page {
		size: 210mm 297mm;
		margin: 10mm 0mm 0mm 0mm;
		padding: 0;
	}
}
</style>
</head>
<body>
	<?php
	Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
	$total = 0;
	foreach ($rs as $r) {
		if (in_array($total % 5, [0, 4])) {
			echo '<div class="qbox" style="width: 17%">';
		} else if (in_array($total % 5, [1, 3])) {
			echo '<div class="qbox" style="width: 21%">';
		} else {
			echo '<div class="qbox" style="width: 24%">';
		}
		echo '<span style="font-size: 12px">' . $r[0] . ': ' . $r[1] . '</span>';
		echo '</div>';
		$total += 1;
		for ($i = 0; $i < $r[1]; $i++) {
			if (in_array($total % 5, [0, 4])) {
				echo '<div class="qbox" style="width: 17%">';
			} else if (in_array($total % 5, [1, 3])) {
				echo '<div class="qbox" style="width: 21%">';
			} else {
				echo '<div class="qbox" style="width: 24%">';
			}
			$bc = new TCPDFBarcode($r[0], 'C128');
			echo '<div class="barcode">' . ($r[2] == 'Y' ? $bc->getBarcodeSVGcode(1, 30) : '') . '</div>';
			echo '<div class="label">' . $r[0] . '</div>';
			echo '</div>';
			$total += 1;
			if ($total % 65 == 0) {
				echo '<div class="clear"></div>';
			}
		}
	}
	?>
</div>
</body>
</html>
