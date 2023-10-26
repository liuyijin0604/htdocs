<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 100 --page-height 150 -T 6 -R 6 -B 6 -L 6 -O Landscape" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --zoom 0.6 --width 420 --quality 80" />

<!--<title>PCA Express Label</title>
@Author:Nero Wang @Date: 2021/5/18 @Description Change PCA logo-->
<title>TLA Express Label</title>
<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$burl = Yii::app()->request->hostInfo.Yii::app()->baseUrl;
?>
<style type="text/css">
@media print {
	@page {
		size: 150mm 100mm;
		margin: 8mm;
	}
	body{ transform: none; width: 150mm; height: 100mm; }
}
*{ margin: 0; padding: 0; letter-spacing: normal !important; box-sizing: border-box; }
body{ font-family: Arial, Verdana, Geneva, sans-serif; font-size: 21px; text-rendering: optimize-speed; width: 1000px; background: #fff; transform: scale(0.6);transform-origin: 0 0;}
.label{ padding: 0; width: 100%; min-height: 630px; clear:both; page-break-after: always; }
.label p { padding: 5px 10px; }
.label hr { border: none; border-top: 1px solid #000; clear: both;}
.label h3 { font-size: 1.5em; line-height: 1.5em; margin: 0; padding: 5px; }
.label h2 { font-size: 2.5em; margin: 0; padding: 5px;}
.label p.binfo { font-size: 2em; line-height: 1.4em; font-weight: bold; }
.label p.qtyinfo { font-size: 1.5em; line-height: 1.4em; font-weight: bold; }
.label .connote { font-size: 2em; line-height: 1em; padding-left: 20px; }
</style>
</head>
<body>
<?php
$isau = substr($prod[0], 0, 2) == '01';
for($i = $_POST['pns']; $i <= $_POST['pne']; $i++):
	$c = $_POST['batch'].sprintf('%03d', $i);
?>
<div class="label">
<div style="float: left; padding: 10px 30px 20px 10px;">
	<img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMjAuMSIgaGVpZ2h0PSIxMjIuOSI+CiAgPHBhdGggZmlsbD0iIzBENDMwOCIgZD0iTTYyLjYgNi4xYzE3LjYgOC40IDM1LjUgMTYuOCA1MC45IDI0LjUuNy4zIDEgLjYgMSAxLjNsLS4xIDg1LjN2LjdIOC41Yy0uNiAwLS45LS4yLS45LS43LS4yLTI4LjQtLjEtNTYuNy0uMS04NS4xIDAtMSAuMi0xLjIgMS0xLjVDMjYuNCAyMiA0Mi44IDE0LjEgNTkuNCA2LjJjMS43LTEgMS43LTEgMy4yLS4xek05LjcgMTE0YzAgMSAuOSAxLjcgMS45IDEuNyAzNi4zLjQgNjUuOS0uMyA5OC45LjUgMS4yIDAgMi4xLS44IDItMlY4MS44YzAtLjctLjItMS40LS41LTItLjQtLjUtLjktMS0xLjctMS0uNCAwLS45LS4xLTEuMy0uMmwtMi40LS45Qzg2LjkgNjkuNCA2Ny44IDY1LjggNDcuNyA2N2MtMTMuNi41LTI0LjcgNi41LTM0LjEgMTEtLjcuMi0xLjMuNi0yIC43LS42LjEtMSAuNC0xLjQuOS0uNC41LS40IDEuMi0uNCAxLjkgMCAxMS4yLS4xIDIxLS4xIDMyLjV6bTAtNzguMmMuMSAxLjIuOSAxLjcgMiAxLjQuOS0uMyAxLjgtLjUgMi44LS41IDExLjUtLjMgMjEuMi42IDMxLjkgMi45IDguNiAyLjggMTYuNCA1LjEgMjIuOSAxMC41LjkuNiAxLjIuNyAyLjIuMiAxMS4xLTIuOCAxOS44LTUuMSAzOC4zLTMuOWwxIC40Yy41LjIgMSAuMyAxLjItLjIuMi0uNy41LTEuMy41LTJ2LTkuOGMwLTEtLjMtMi0xLjMtMi42LTE0LjMtNy4zLTMyLjMtMTUuNy01MC4zLTIzLjktOC42IDMuNy0xNCA2LjQtMjAuOSA5LjktMS43IDItMS42IDUuNC0xLjYgNy44LS4zIDEtLjIgMS42LTEuNCAxLjgtLjYtLjEtLjgtLjctLjgtMS4xLS4xLTIuMy42LTMuOS43LTYgMC0uNS0uNC0uOC0uOS0uNy0uNCAwLS43LjItMSAuNC04LjEgNC4xLTE2LjcgOC4yLTI0LjEgMTEuNi0xLjEgMS4yLTEuMiAyLjMtMS4yIDMuOHptMS44IDIuOWMtMS4zIDAtMS43LjUtMS43IDEuNnYzNi42Yy4xLjYuNyAxIDEuMi41LjgtLjUgMS40LTEuMiAyLjItMS44IDkuOS02LjggMTguOC05LjMgMzAuMy0xMS4zbDIuNi0uNmMuMSAwIC4yIDAgLjMtLjJsMS4xLTEuNEM1Mi43IDU3IDU5LjYgNTMuOCA2NiA1MmMuNC0uMS45LS4zLjctLjktMTQuOS04LjQtMjkuOC0xMS4zLTQ0LjMtMTEuN2wtNy43LS4xYy0xLS4xLTIuNC0uMy0zLjItLjZ6bTk2LjkgMzcuMmwyLjQgMS44Yy44LjYgMS43IDAgMS43LTEuMVY1MC41YzAtLjgtLjItMS41LS40LTIuMy0uMi0uNi0uNy0uNy0xLjMtLjQtLjYuMy0xLjIuNi0xLjkuNy0yMS42LS43LTQwLjQgNi4zLTU5LjMgMTMuOWwtLjQuNWMwIC4yLjMuNC41LjQuNy4yIDEuNS40IDIuMy40IDIyLjguMyAzMS43LjMgNTYuNCAxMi4yeiIvPgogIDxwYXRoIGZpbGw9IiMwRDQzMDgiIGQ9Ik00OS45IDMzLjNoLTIuOGMtLjIgMC0uNC0uMS0uNi0uMi0uMS0uMS0uMy0uMi0uMy0uNCAwLS4xLjItLjMuNC0uMyAyLS43IDQtMSA2LTEuMiAxMC4zLTEuNCAyMS4xLTIuOSAzMC42LTQuMWgzbC44LjZjLS4zLjMtLjUuNy0uOC44bC0yLjQuNmMtMTAuMiAxLjItMjEuNSAyLjgtMzAuNyA0LjEtMS4xLjItMi40LjItMy4yLjF6TTQ1LjUgMzUuNmwxLS4yYzEzLjIgMi42IDI2LjcgNi4yIDM4LjYgOC40LjIgMCAuNC4zLjQuNWwtLjMuNWMtOS40LjEtMjIuOS00LjUtMzYuNy03LjItMS4yLS4zLTIuMy0uNi0zLTJ6TTcxLjcgMTUuOWMuMy4xLjYuMi43LjQuMi4zIDAgLjYtLjMgMS03LjcgNC40LTE3LjEgOC0yMy42IDExLjZsLTIuOCAxYy0uMi4xLS41IDAtLjYtLjFWMjljLjgtMS4zIDIuMi0xLjkgMy41LTIuNiA3LjYtMy45IDEzLjUtNi40IDIwLjUtOS43LjYtLjQgMi40LS44IDIuNi0uOHpNNDMuNSAzMS45Yy0uMy0uNi0uOC0uOC0xLjMtMS0xLjEtLjQtMi4xLTEuMS0zLjQtLjhsLTIuNi43Yy0uOC40LTEuOC43LTEuNiAydi4xYy4zIDEuMS42IDIuMiAxLjQgMy4xIDEgLjcgMi4zLjkgMy41LjUuOC0uMyAxLjYtLjUgMi4zLTEgLjYtLjMgMS0uOSAxLjQtMS40LjUtLjYuNy0xLjUuMy0yLjJ6bS0yLjQgMS41Yy0uNCAxLTIuMSAxLjgtMy4xIDEuNS0uNS0uMS0xLjEtLjctMS4xLTEuMy0uMS0uNi4zLTEuMS43LTEuMyAxLjEtLjMgMi0uOCAzLjEtLjIuNi4zLjYuOC40IDEuM3pNNDEuNCAyOGMtLjYgMC0uOC0uMi0uNy0uNyAwLS4zIDAtLjYuMi0uOGwyLTIuNGMzLjEtMy44IDQuOS02LjIgOC4zLTkuNC4zLS4xLjctLjIgMS0uMS4xLjEuMi42LjEuOS0yLjcgNC02LjQgOC05IDExLjItLjcuOS0xLjQgMS4xLTEuOSAxLjN6TTMwLjQgMzIuOGMuMy4yLjYuNy4yIDEuMi0uMy41LS44LjUtMS4zLjMtMy44LTEuMy04LjItMS42LTExLjgtMS42LS43IDAtMS4yLS4yLTEuNS0xcy0uMS0xIC43LTFjNS4xLjMgOS44IDAgMTMuNyAyLjF6TTMxIDI1LjZsLjkuNyAxLjMgMi40Yy41LjcuNSAxLjIgMCAxLjYtLjYuNC0xLjIuMy0xLjctLjQtLjYtLjktMS4xLTEuOC0xLjUtMi44LS4zLS42LjMtMS4zIDEtMS41eiIvPgogIDxnIGZpbGw9IiMwRDQzMDgiPgogICAgPHBhdGggZD0iTTI0LjMgOTUuNGMtMS40LjUtMi4xLjgtMy41IDEuMy0xLjItMi45LTEuNy00LjQtMi45LTcuMy0uNy4zLTEuMS40LTEuOC43bC0xLTIuNWMuNy0uMyAxLjEtLjQgMS44LS43LS4yLS41LS4zLS43LS41LTEuMi0uOC0yIC4yLTMuOCAyLjgtNC43IDEuMS0uNCAyLjMtLjYgMy42LS42LjEgMSAuMSAxLjYuMiAyLjYtLjkgMC0xLjcuMi0yLjIuMy0uNS4yLS43LjUtLjUgMXMuMy43LjQgMS4yYzEtLjQgMS41LS41IDIuNS0uOS4zIDEgLjUgMS42LjkgMi42LTEgLjMtMS41LjUtMi40LjggMSAzIDEuNiA0LjUgMi42IDcuNHpNMzEuNCA4Ni4yYy4xIDAgLjIgMCAuMy0uMS0uMS0uMi0uMS0uMy0uMS0uNS0uMS0uNS0uNi0uOC0xLjItLjYtMS4zLjMtMi40IDEtMy40IDItLjYtLjktLjktMS4zLTEuNS0yLjIgMS40LTEuMiAyLjktMiA0LjQtMi41IDIuOS0uOCA0LjkuMiA1LjMgMi4zLjQgMS42LjYgMi40LjkgNCAuMiAxLjEuNyAyLjEgMS4zIDIuOS0xLjMuMy0yIC41LTMuMy44LS4yLS4yLS42LS44LS42LS44LS4yIDAtMS4zIDEuNS0yLjMgMS44LTIuMi42LTQtLjEtNC42LTItLjgtMiAuOS00LjEgNC44LTUuMXptMS4xIDMuM2MtLjEtLjUtLjItLjctLjMtMS4yLS4xIDAtLjEgMC0uMi4xLTEuNS40LTIuMyAxLjMtMiAyIC4xLjQuNC42IDEgLjQuNy0uMiAxLjItLjcgMS41LTEuM3pNNDQuNyA4Mi4zYy0uNC0uMS0uOCAwLTEuNCAwLS44LjEtMS4zLjYtMS43IDEuMy41IDIuOC43IDQuMiAxLjIgNy0xLjQuMi0yLjIuNC0zLjYuNy0uOS00LjEtMS4zLTYuMi0yLjEtMTAuNCAxLjYtLjMgMi40LS41IDMuOS0uNy4xLjMuMS40LjEuNy40LS43IDEuMy0xLjIgMi4xLTEuMy40LS4xLjktLjEgMS40IDAgMCAxIDAgMS42LjEgMi43ek01MS4yIDg5LjVjLTEuNC4xLTIuMi4yLTMuNi40LS41LTQuMi0uOC02LjMtMS4zLTEwLjUgMS42LS4yIDIuNC0uMyAzLjktLjR2LjVjLjYtLjYgMS4zLS45IDIuMi0xIDEuNS0uMSAzLjIgMS4xIDMuMyAxLjEuMyAwIDEuNy0xLjQgMi44LTEuNCAyLjUtLjEgMy45IDEuMiAzLjggMy4zIDAgMyAwIDQuNS0uMSA3LjVoLTMuNmMtLjEtMi45LS4xLTQuNC0uMi03LjMgMC0uNS0uMy0uOC0uOS0uNy0uNSAwLS45LjMtMS4zLjkuMSAyLjkuMiA0LjMuMyA3LjItMS40LjEtMi4yLjEtMy42LjItLjItMi45LS4zLTQuNC0uNS03LjMgMC0uNS0uMy0uNy0uOS0uNy0uNSAwLS45LjQtMS4yIDEgLjUgMi45LjYgNC4zLjkgNy4yek02OC41IDg5LjJjLTEuNS0uMS0yLjItLjEtMy42LS4ybC41LTE1YzEuNy4xIDIuNS4xIDQuMS4ybC0xIDE1ek03Ny4xIDgzLjNoLjNjMC0uMiAwLS4zLjEtLjUuMS0uNi0uMy0uOS0uOS0xLTEuMy0uMi0yLjYgMC0zLjkuNS0uMi0xLS4zLTEuNi0uNS0yLjYgMS43LS42IDMuNC0uOCA1LS42IDIuOS40IDQuNCAyLjEgNCA0LjItLjMgMS42LS40IDIuNS0uNyA0LjEtLjIgMS4xLS4xIDIuMi4xIDMuMi0xLjMtLjItMi0uMy0zLjQtLjUtLjEtLjItLjItLjktLjItLjktLjEtLjEtMS44LjktMi44LjgtMi4zLS4zLTMuNi0xLjYtMy41LTMuNi4yLTIuNCAyLjUtMy42IDYuNC0zLjF6bS0uMiAzLjVjLjEtLjUuMS0uNy4yLTEuMmgtLjJjLTEuNi0uMi0yLjUuMy0yLjYgMS4xIDAgLjQuMi43LjcuOC44IDAgMS40LS4yIDEuOS0uN3pNODQuNCA4MC42YzEuNi4zIDIuNC41IDMuOS44LS4xLjMtLjEuNC0uMi43LjgtLjUgMS43LS42IDIuNy0uMyAyLjUuNiAzLjcgMi41IDMuMSA0LjVsLTIgNy0zLjUtMWMuNy0yLjggMS4xLTQuMSAxLjgtNi45LjEtLjYtLjEtMS0uOC0xLjItLjYtLjItMS4zIDAtMS44LjYtLjYgMi44LTEgNC4xLTEuNiA2LjktMS40LS4zLTIuMS0uNS0zLjYtLjguOC00IDEuMi02LjEgMi0xMC4zek0xMDMuNiA5Ny40Yy0xLjQtLjYtMi0uOC0zLjQtMS4zLjEtLjIuMS0uNC4yLS42LS44LjMtMS41LjQtMi40IDAtMi43LS45LTMuOS0zLjMtMi45LTYuNiAxLjEtMy41IDQuMS00LjggNy43LTMuNS40LjEuOC4zIDEuMS42LjYtMS42LjktMi41IDEuNS00LjEgMS42LjYgMi4zLjkgMy45IDEuNS0yLjMgNS42LTMuNCA4LjQtNS43IDE0em0tNC4xLTQuMmMuNi4yIDEuMi4yIDEuOC0uMS43LTEuOCAxLTIuNyAxLjctNC41LS40LS4yLS44LS40LTEuMi0uNS0xLjItLjQtMi40LjItMy4xIDItLjUgMS42LS4yIDIuNy44IDMuMXoiLz4KICA8L2c+CiAgPGcgZmlsbD0iIzBENDMwOCI+CiAgICA8cGF0aCBkPSJNNDUuOCAxMDkuMWgtMy43di0uN2MtLjYuNi0xLjMuOS0yLjMuOS0yLjkgMC00LjgtMi00LjgtNS42IDAtMy44IDIuMy02IDUuOS02IC40IDAgLjggMCAxLjEuMnYtNC42aDMuN2wuMSAxNS44em0tNS4zLTIuNmMuNiAwIDEuMi0uMyAxLjYtLjh2LTUuMWMtLjQtLjEtLjgtLjEtMS4yLS4xLTEuMiAwLTIuMSAxLjEtMi4xIDMuMSAwIDEuOC43IDIuOSAxLjcgMi45ek01My43IDEwMmguM3YtLjVjMC0uNi0uNC0uOS0xLS45LTEuMiAwLTIuNS4zLTMuNyAxbC0uNy0yLjdjMS42LS44IDMuMS0xLjIgNC42LTEuMiAyLjggMCA0LjQgMS41IDQuNCAzLjd2NC40YzAgMS4yLjIgMi4zLjYgMy4zaC0zLjVjLS4yLS4yLS4zLS42LS40LS45LS44LjgtMS43IDEuMi0yLjcgMS4yLTIuNCAwLTMuOS0xLjMtMy45LTMuNCAwLTIuNiAyLjEtNCA2LTR6bS4yIDMuN3YtMS4zaC0uMmMtMS42IDAtMi41LjYtMi41IDEuNSAwIC41LjMuNy44LjcuOS0uMSAxLjUtLjQgMS45LS45ek02My42IDk2LjVjLS44LjgtMi4yLjgtMyAwcy0uOC0yIDAtMi44IDIuMi0uOCAzIDAgLjggMiAwIDIuOHptLjQgMTIuNmgtMy43Vjk3LjlINjR2MTEuMnpNNzMuNCAxMDAuN2MtLjMtLjEtLjgtLjItMS4zLS4yLS44IDAtMS40LjMtMS44IDEuMXY3LjRoLTMuN1Y5Ny45aDMuN3YuOGMuNS0uNiAxLjQtMSAyLjEtMSAuNCAwIC44LjEgMS4zLjJsLS4zIDIuOHpNODAuNSAxMDQuMmwyLTYuM2gzLjlMODIgMTA5LjVjLS43IDEuNy0xLjkgMy4xLTMuNiA0LjFsLTIuMi0yLjRjMS4yLS43IDIuMS0xLjYgMi41LTIuN2wuMS0uMi00LTEwLjVoMy45bDEuOCA2LjR6Ii8+CiAgPC9nPgo8L3N2Zz4K" height="150" alt="logo" />
</div>
	<div style="float: left;max-width: 800px;position:relative;">
	<h3><?=$prod['1'];?></h3>
	<div style="position: absolute;top:100px;left:170px;width:500px;"><h2><span style="font-size: 0.7em">SKU:</span> <?=$prod[0];?></h2></div>
</div>
<hr />
	<div style="float: left; width: 50%">
	<p>Batch Information:</p>
	<p class="binfo">LOT: <?=$_POST['batch'];?><br />
MFG: <?=date($isau? 'd/m/Y' : 'Y/m/d', strtotime($_POST['mfd']));?><br />
<?=$isau? 'USE BY' : 'EXP';?>: <?=date($isau? 'd/m/Y' : 'Y/m/d', strtotime($_POST['bbd']));?></p>
</div><div style="float:right; padding: 30px 80px;">
	<img src="data:image/svg+xml;base64,<?php
            $dm = new TCPDF2DBarcode(chr(232).'01'.sprintf('%014d', str_replace('-', '', $prod[0])).'10'.$_POST['batch'].chr(29).'11'.date('ymd', strtotime($_POST['mfd'])).'17'.date('ymd', strtotime($_POST['bbd'])).'243'.$c.chr(29).'30'.$_POST['qty'], 'DATAMATRIX');
            echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(8, 8, 'black')));
            ?>" width="160" alt="2d" />
</div>
<hr /><div style="float:left; padding: 10px 75px; border-right: 1px solid #000;"><div class="barcode">
<?php
	$bc = new TCPDFBarcode($c, 'C128');
	echo $bc->getBarcodeSVGcode(4, 100);
?></div>
	<div class="connote"><?=$c;?></div>
</div>
	<div style="float: left;">
	<p>Quantity &amp; Net Weight:</p>
	<p class="qtyinfo">QTY: <?=$_POST['qty'];?> &times; <?=($prod[3]*1000);?>g Tins<br />
Net Weight: <?=($_POST['qty']*$prod[3]);?>kg</p></div>
<hr />
<p>Manufacturer: Farmland Dairy Pty Ltd<?=$isau? '' : '<span style="padding-left: 350px">Country of Origin: <b style="font-size:1.2em">Australia</b></span>'?><br />
Address: UNIT 1, 360 CHISHOLM ROAD AUBURN NSW 2144<?=$isau? '' : '<span style="padding-left: 245px">Reg. No. 1933</span>'?></p>
</div>
<?php
endfor;
?>
</div>
</body>
</html>
