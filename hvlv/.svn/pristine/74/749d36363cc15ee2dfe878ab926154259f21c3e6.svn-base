<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 101 --page-height 152 -T 5 -R 5 -B 5 -L 5 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />
<title>Barcode Label</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 20px; text-rendering: optimize-speed; width: 700px; padding-top: 15%;}
</style>
</head>
<body width="700">
<div style="padding: 30px; text-align:center"><div>
<?php Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);?>
<h3 style="margin-bottom: 50px;"><?=$model->name;?></h3>
<?php
$bc = new TCPDFBarcode($model->ean, preg_match('/^\d{13}$/', $model->ean)? 'EAN13' : 'C128');
echo $bc->getBarcodeSVGcode(4, 100);
?></div>
<p style="font-size:1.4em; padding: 10px"><?=$model->ean;?></p>
</div>
</body>
</html>
