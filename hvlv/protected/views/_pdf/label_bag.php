<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 101 --page-height 152 -T 3 -R 3 -B 3 -L 3 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />

<title>bag_label</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 20px; text-rendering: optimize-speed; width: 700px; }
p.logo { text-align: center; padding-bottom: 30px; }
.barcode{ font-family: IDAutomationHC39M; font-size: 36px; padding: 30px; text-align: center; }
h1.dest { padding: 30px; font-size: 60px; text-align: center; }
h4 { font-size: 26px; }
small { font-size: 16px; }
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
<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
if(!isset($default_rts)) $default_rts = false;

?>

<div>
	<div style="border:solid 1px #000000;width: 100%;">
		<div style="display: -webkit-inline-box; height: 130px;">
			<div style="width:360px;"><span
					style="font-family: Times New Roman; font-weight:bold;font-size:3em;text-align: center;margin-left: 50px;"></span>
				<br/>
			</div>
			<div style ="width:280px;border: solid 10px black; margin: 4px 0px 0px 30px;height:100px;font-weight:bold;font-size:3em;line-height:100px;text-align:center"><?=$bag->mdata['sort_code']?></div>
		</div>

		<div style="font-family: Times New Roman;font-size:28px;border-bottom: solid 1px black;border-bottom: solid 1px black;padding-left: 2px;padding-top: 20px;padding-bottom: 20px;min-height: 20px;">
			<p style="text-align: center; font-size:25px"><b><?php echo $bag->no ?></b></p>
		</div>

		<div style="font-family: Times New Roman;font-size:28px;border-bottom: solid 1px black;padding-left: 2px;padding-top: 20px;padding-bottom: 20px;min-height: 120px;text-align: center">
				<p>
					<?php
					$bc = new TCPDFBarcode($bag->no, 'C128');
					$bc->getBarcodeSVG(3, 150, 'black');
					?></p>
				<p style="font-size:25px;padding-top:20px;"><b><?php echo $bag->no ?></b></p>
		</div>

		<div style="font-family: Times New Roman;font-weight: bold;font-size:28px;border-bottom: solid 1px black;padding-left: 2px;padding-top: 10px;padding-bottom: 10px;min-height: 120px;">
				<p style="padding-left:20px;">
					<!-- PCA Express -->
				</p>
		</div>


		<div style="display: -webkit-inline-box;">
			<div style="font-family: Times New Roman;font-size:1.4em;font-weight: bold;padding: 10px;width: 300px;border-right: solid 1px black;text-align: center;">Total Quantity<br> <?php echo $bag->pkg?></div>
			
			<div style="min-width: 300px;padding: 10px; font-family: Times New Roman;font-weight: bold; font-size:1.4em;text-align: center;">Total Weight<br><div><?php echo $bag->weight ; ?>KG</div>
			</div>
		</div>
		<div style="bottom: 10px;  margin-top: 4px;text-align: center;width: 100%;border-top: solid 1px black;height:400px;">
			<!-- <div style ="float:right;margin-top:270px;font-size:3em;font-weight:bold;margin-right:10px;border: solid 10px black; width:180px;height:80px;">B</div> -->
		</div>
	</div>


</div>
</body>
</html>
