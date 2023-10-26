<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 --page-size A6 -T 8 -R 8 -B 8 -L 8 -O Portrait" win-only="--disable-smart-shrinking" />
<title>PCA Express PUB</title>
<style type="text/css">
@media print {
	@page {
		size: 50mm;
		margin: 5mm;
	}
	body{ transform: none; width: 50mm; }
}
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Courier, sans-serif; font-size: 12px; text-rendering: optimize-speed; width: 50mm; background: #fff; line-height: 15px;}
p { margin-bottom: 10px; }
h4 { font-size: 14px; }
small { font-size: 10px; }
hr { border: none; border-bottom: 1px solid #000; margin: 10px 0;}
hr.dashed { border: none; border-bottom: 1px dashed #000; clear: both;}
ul.sls{ list-style:none; clear: both;}
ul.sls li { margin-right: 5px; float: left; }
ul.sls li:last-child { float: none; }
</style>
</head>

<body>
<p align="center"><img src="../images/PCAE_Logo_bw.png" width="80%" /></p>
<p>
Pickup #: <?=$man->ref;?><br />
Client: <?=$man->owner->name;?><br />
By: <?=$man->getDriver();?><br />
Time: <?=$man->created;?><br />
</p>
<hr class="dashed" />
<ul class="sls">
<?php
foreach($man->lines as $l){
	$p = $l->mm();
	echo '<li>'.$p->hbn.'</li>';
}
?>
</ul>
<hr class="dashed" />
<p><b>Total: <?=$man->countLines();?> pcs</b></p>
<script type="text/javascript">
window.print();
</script>
</body>
</html>
