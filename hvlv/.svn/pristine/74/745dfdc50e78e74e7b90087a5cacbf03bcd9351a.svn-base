<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="wkhtmltopdf" content="--dpi 100 --page-width 101 --page-height 152 -T 3 -R 3 -B 3 -L 3 -O Portrait" win-only="--disable-smart-shrinking" />
	<meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />
	<?php //--disable-smart-shrinking ?>
	<title>
		<?='PCA Express Stockin Ticket'?>
	</title>
	<style type="text/css">
		* { margin: 0; padding: 0; letter-spacing: normal !important; box-sizing: border-box; }
		body { font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 700px; }
		header { padding-bottom: 15px; }
		footer { padding-top: 10px; page-break-after: always; }
		div { margin: 0; padding: 0; font-size: 0; display: inline-block; vertical-align: top; }
		.container1 {
			width: 100%;
			height: 120px;
			text-align: left;
		}
		.container2 {
			width: 100%;
		}
		.container2 .row1,.row2,.row3,.row4 {
			width: 100%;
			box-sizing: content-box;
			margin-bottom: 20px;
		}
		.container2 .col1,.col2,.col3 {
			height: 100%;
			box-sizing: border-box;
			position: relative;
		}
		.row1 {
			height: 12%;
		}
		.row2 {
			height: 22%;
		}
		.row3 {
			height: 18%;
		}
		.row4 {
			height: 5%;
		}
		.col1,.col2 {
			width: 100%;
		}
		.col3 {
			width: 48%;
		}
		.titleDIV {
			width: 100%;
			border: 5px solid #00467F;
			background-color: #00467F;
		}
		span.title {
			vertical-align: center;
			font-weight: bold;
			font-size: 20px;
			color: white;
			padding-left: 10px;
		}
		p {
			font-size: 32px;
			color: black;
			width: 100%;
			padding: 0px;
			margin: 0px;
		}
		p.content1 {
			font-size: 100px;
			text-align: center;
			border: 5px solid #00467F;
			padding: 20px 0;
		}
		p.content2 {
			vertical-align: center;
			text-align: center;
			font-size: 35px;
			width:  100%;
			border: 5px solid #00467F;
			padding: 15px 0;
		}
		p.content3 {
			vertical-align: center;
			text-align: right;
			width:  100%;
		}
	</style>
</head>

<body>
	<?php if (!empty($model->mdata['pi_expect'])) {
		for ($i = 1; $i <= $model->mdata['pi_expect']; $i++) {
			include('_wmstask_ticket_stockin.php');
		}
	} else {
		include('_wmstask_ticket_stockin.php');
	} ?>
	<?php include('_pagination.php'); ?>
</body>
</html>