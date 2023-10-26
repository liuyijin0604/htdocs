<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 101 --page-height 152 -T 3 -R 3 -B 3 -L 3 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />
<?php $label_title="PCA Express Label"; 
?>
<title><?=$label_title?></title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 20px; text-rendering: optimize-speed; width: 700px; }
p.logo { text-align: center; padding-bottom: 30px; }
.barcode{ font-family: IDAutomationHC39M; font-size: 36px; padding: 30px; text-align: center; }
h1.dest { padding: 30px; font-size: 60px; text-align: center; }
h4 { font-size: 26px; }
small { font-size: 16px; }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
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
    <br/>
<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
include(dirname(__FILE__) . '/' . '_held_shipments' . '.php');
echo '<div class="page-break"></div>';
?>
</body>
</html>
