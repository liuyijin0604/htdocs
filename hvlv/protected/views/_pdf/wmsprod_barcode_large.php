<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 152 --page-height 101 -T 5 -R 5 -B 5 -L 5 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />
<title>Barcode Label</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 20px; text-rendering: optimize-speed; }
</style>
</head>
<body>
	<div>
		<div style="text-align:center">
			<h1><?=$model->name;?></h1>
			<div style="padding: 30px 0">
				<?php Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);?>
				<?php
				$bc = new TCPDFBarcode($model->ean, preg_match('/^\d{13}$/', $model->ean)? 'EAN13' : 'C128');
				echo $bc->getBarcodeSVGcode(3, 150);
				?>
			</div>
			<h1><?=$model->ean;?></h1>
		</div>
	</div>
</body>
</html>
