<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php //--disable-smart-shrinking ?>
	<title>
		<?='PCA Express Pickup Ticket'?>
	</title>
	<style type="text/css">
		* { margin: 0; padding: 0; letter-spacing: normal !important; }
		body { font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
		header { padding-bottom: 15px; }
		footer { padding-top: 10px; page-break-after: always; }
		div { margin: 0; padding: 0; font-size: 0; display: inline-block; vertical-align: top; }
		.container1 {
			width: 100%;
			height: 150px;
			text-align: center;
		}
		.container2 {
			width: 100%;
			height: 1400px;
			border: 30px solid #EBEFF0;
		}
		.container2 .row1,.row2,.row3,.row4 {
			width: 100%;
			box-sizing: content-box;
		}
		.container2 .col1,.col2,.col3,.col4,.col5 {
			height: 100%;
			box-sizing: border-box;
			position: relative;
		}
		.container2 .col2,.col3,.col4,.col5 {
			border-top: 15px solid #EBEFF0;
			border-bottom: 15px solid #EBEFF0;
		}
		.row1 {
			height: 20%;
		}
		.row2 {
			height: 58%;
		}
		.row3 {
			height: 22%;
		}
		.row4 {
			height: 34%;
		}
		.col1,.col5 {
			width: 70%;
		}
		.col2 {
			width: 30%;
		}
		.col3 {
			width: 100%;
		}
		.col4 {
			width: 50%;
		}
		span.title {
			display: inline-block;
			position: relative;
			left: 10px;
			top: 10px;
			font-size: 25px;
			color: grey;
		}
		p {
			position: absolute;
			font-size: 40px;
			color: black;
			width: 100%;
			padding: 0px;
			margin: 0px;
		}
		p.content1 {
			font-size: 150px;
			text-align: center;
			bottom: 25%;
		}
		p.content2 {
			text-align: right;
			bottom: 0px;
			right: 5%;
		}
		p.content3 {
			text-align: center;
			bottom: 20px;
		}
		p.content4 {
			top: 15%;
			transform: rotate(90deg);
			-webkit-transform: rotate(90deg);
		}
	</style>
</head>

<body>
	<div class="container1">
		<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl.'/images/'.(Yii::app()->name == 'PEP'? 'PEP_logo.png' : 'PCAE_Logo.png');?>" alt="PCA Express" width="398" height="87" />
	</div>
	<div class="container2">
		<div class="row1">
			<div class="col3" style="border-top: 0;">
				<span class="title">Task No</span>
				<p class="content1"><?=empty($model->mainTask) ? $model->getNo() : $model->mainTask->getNo();?></p>
			</div>
		</div>
		<div class="row3">
			<div class="col3">
				<span class="title">Access PIN</span>
				<?php Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true); ?>
				<p class="content1"><?php
					$bc = new TCPDFBarcode($model->mdata['pin'], 'C128');
					$bc->getBarcodeSVG(7, 150, 'black');
				?></p>
				<p class="content3"><?=$model->mdata['pin'];?></p>
			</div>
		</div>
		<div class="row2">
			<div class="row3">
				<div class="col3">
					<span class="title">Date</span>
					<p class="content2"><?=date('Y-m-d', strtotime($model->mdata['date']));?></p>
				</div>
			</div>
			<div class="row3">
				<div class="col3">
					<span class="title">Trading Hours(GMT +10:00)</span>
					<p class="content2"><?='9:00 to 17:00';?></p>
				</div>
			</div>
			<div class="row3">
				<div class="col3">
					<span class="title">Receptionist</span>
					<p class="content2">Kevin Mao / George Zhou</p>
				</div>
			</div>
			<div class="row4">
				<div class="col3" style="border-bottom: 0;">
					<span class="title">Address</span>
					<p class="content2">6C The Crescent<br />
					Kingsgrove, NSW 2208</p>
				</div>
			</div>
		</div>
	</div>
</body>