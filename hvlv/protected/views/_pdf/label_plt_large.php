<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 102 --page-height 152 -T 3 -R 3 -B 3 -L 3 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />
<title>TLA Pallet Label</title>
<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
$burl = Yii::app()->request->hostInfo.Yii::app()->baseUrl;
?>
<style type="text/css">
@media print {
	@page {
		size: 100mm 150mm;
		margin: 5mm;
	}
	body{ transform: none; width: 100mm; height: 150mm; }
}
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: "Microsoft YaHei", "微软雅黑", Verdana, Geneva, sans-serif; font-size: 21px; text-rendering: optimize-speed; width: 1000px; background: #fff; transform: scale(0.6);transform-origin: 0 0;}
.connote{ font-size: 120px; }
.label{ padding-top: 400px; clear:both; page-break-after: always; text-align: center;}
.rotate {
	-webkit-transform: rotate(90deg);
	-moz-transform: rotate(90deg);
	-o-transform: rotate(90deg);
	-ms-transform: rotate(90deg);
	transform: rotate(90deg);
}
</style>
</head>
<body>
	<?php
	foreach($cs as $c):
	?>
	<div class="rotate">
	<div class="label">
	<div class="barcode">
	<?php
		$bc = new TCPDFBarcode($c, 'C128');
		echo $bc->getBarcodeSVGcode(4, 280);
	?></div>
	<div class="connote"><?=$c;?></div>
<div style="font-size:90px; padding:15px;"><?=$dup? '*REPRINT*' : '';?></div>
</div>
</div>
	</div>
	<?php
	endforeach;
	?>
</body>
</html>
