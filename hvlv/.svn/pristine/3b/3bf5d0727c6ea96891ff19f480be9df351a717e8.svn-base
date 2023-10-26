<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 -T 5 -R 5 -B 5 -L 5 -O Portrait" win-only="--disable-smart-shrinking" />
<title>PCA Express Label</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 24px; text-rendering: optimize-speed; width: 1500px; }
p.logo { text-align: center; padding-bottom: 30px; }
.barcode{ font-family: IDAutomationHC39M; font-size: 36px; text-align: center; }
h1.dest { padding: 30px; font-size: 60px; text-align: center; }
h4 { font-size: 30px; }
small { font-size: 20px; }
div.page-break { clear:both; display: none; page-break-after: always; }
h2.connote { text-align: center; padding: 30px;}
table td{ padding: 10px; }
table td table td { padding: 5px; }
.sender { font-size: 22px; }
.dto{ font-size: 1.2em; }
div.label { width: 1450px; padding: 25px; height: 1027px; position: relative; float: left; overflow: hidden; }
</style>
</head>

<body width="1500">
<?php
$sn = 1;
foreach($rs as $label){
	echo '<div class="label">';
	include((empty($tpl)?  '_pod' : $tpl).'.php');
	echo '</div>';
	if($sn++ % 2 == 0) echo '<div class="page-break"></div>';
}
?>
</body>
</html>
