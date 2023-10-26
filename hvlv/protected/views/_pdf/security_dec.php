<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait --page-size A4" />
<?php //--disable-smart-shrinking ?>
<title>PCA Express Security Declaration</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; box-sizing: border-box; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #333 solid; padding: 10px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #333 solid; border-top: none; border-left: none; border-collapse: collapse; }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
table.chart td.tdiv { padding: 0; }
.p1 { font-size: 1.2em; padding: 20px 60px; }
.p1 p { margin-bottom: 1em; line-height: 1.5em;}
td.tdiv .col { padding: 10px; float: left;}
.col.brt { border-right: 1px #333 solid; }
.col.bbt { border-bottom: 1px #333 solid; }
span.u { display:inline-block; border-bottom: 1px dashed #333; }
header { padding-bottom: 20px; }
footer { padding-top: 10px; page-break-after: always; }
</style>
</head>

<body width="1120">
<header>
<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
include('_header_tla.php');
$awbModel = EdiAwbConsol::model()->find('awb = :awb',[':awb' => $model->awb]);
?>
</header>
<div class="p1">
<h2 style="padding: 15px;" align="center">Qantas T1/T3 / Menzies / TOLL</h2>
<p>I, <span class="u" style="width: 200px;"><?=empty($model->mdata['secDecOpt']['asic'])? '' : (empty(EdiJob::$secDecOptions['asic'][$model->mdata['secDecOpt']['asic']])? $model->mdata['secDecOpt']['asic'] : EdiJob::$secDecOptions['asic'][$model->mdata['secDecOpt']['asic']]);?></span>, an authorised representative of<br />Top Logistics, a Regulated Air Cargo Agent (RACA),<br />declare that the cargo on MAWB No:</p><p align="center"><b style="font-size: 1.5em"><?=$model->awb;?></b></p><p>has been prepared for clearance in accordance with the requirements of our approved RACA Transport Security Program.</p>
<p>The cargo will be delivered by <span class="u" style="width: 400px;"><?php
if(!empty($model->mdata['secDecOpt']['trucking'])){
	$o = Org::model()->findByPk($model->mdata['secDecOpt']['trucking']);
	if(!empty($o)) echo $o->name;
}
?></span></p>
<p style="margin-top: 80px;">Signature: <span class="u" style="width: 300px;"></span></p>
<p>Date: <?=date('d/m/Y', empty($model->mdata['secDecOpt']['ts'])? null : $model->mdata['secDecOpt']['ts']);?></p>
<hr style="border:none; border-bottom: 1px solid #666; margin: 60px -20px;" />
<h2 style="padding: 15px;" align="center">Driver Lodgement Details</h2>
<p style="margin-top: 60px;">Name: <span class="u" style="width: 300px;"></span> &nbsp; Signature:<span class="u" style="width: 400px;"></span></p>
<p style="margin-top: 40px;">Date: <span class="u" style="width: 200px;"></span> ID#: <span class="u" style="width: 300px;"></span> Type: <span class="u" style="width: 200px;"></span></p>
</div>
<footer></footer>
<?php
include('_header_tla.php');
?>
<h1 style="padding: 15px; margin-top:40px;" align="center">Consignment Security Declaration</h1>
<table class="chart" width="100%">
<tr><td width="50%" valign="top"><p>Regulated Party Category (KC, RA or AO) & Identifier(of the regulated party issuing the security status)</p><p align="center">Top Logistics<br />AU/RA/00051-01</p><br /></td>
	<td valign="top"><p>Unique Consignment Identifier</p><br /><p align="center"><b><?=$model->awb;?></b></p><br /></td></tr>
<tr><td colspan="2"><div style="min-height: 80px;"><p>Content of Consignment</p><br/><?php
$cs = [];
foreach($model->mdata['secDecOpt']['content'] as $d){
	// other
	if ($d == 60) {
		$cs[] = EdiJob::$secDecOptions['content'][$d] . ': ' . $model->mdata['secDecOpt']['content_other'];
	} else {
		$cs[] = EdiJob::$secDecOptions['content'][$d];
	}
}
echo implode(', &nbsp; ', $cs);
?></div><p>[x] Consolidation</p></td></tr>
<tr><td valign="top" class="tdiv"><div class="col brt" style="width:50%; min-height: 100px;"><p>Origin</p><br /><?=$awbModel->pol;?></div><div class="col" style="width:50%;"><p>Destination</p><br /><?=$awbModel->pod;?></div></td>
	<td valign="top"><p>Transit/Transfer points (if known)</p><br /><div style="width:50%; float:left;">To: <?=$awbModel->pod;?></div><div style="width:50%; float:left;">By: </div></td></tr>
<tr><td colspan="2"><div style="width:19%; float:left;"><p>Security Status</p></div><div style="width:81%; float:left;"><p>Reasons for issuing the Security Status</p></div></td></tr>
<tr><td colspan="2"class="tdiv"><div class="col brt" style="width:19%; min-height: 282px;">SPX</div><div style="width:81%; min-height: 280px; float:left;">
	<div class="col brt bbt" style="width:33.3%; min-height: 40px;">Received from (codes)</div><div class="col brt bbt" style="width:33.3%; min-height: 40px;">Screening Method(codes)</div><div class="col bbt" style="width:33.3%; min-height: 40px;">Grounds for Exemption(codes)</div>
	<div class="col brt" style="width:33.3%; min-height: 240px;">RA</div><div class="col brt" style="width:33.3%; min-height: 240px;"><?=$model->mdata['secDecOpt']['screening'].'<br />'.EdiJob::$secDecOptions['screening'][$model->mdata['secDecOpt']['screening']];?></div><div class="col" style="width:33.3%; min-height: 240px;"><?=$model->mdata['secDecOpt']['exemption'].'<br />'.EdiJob::$secDecOptions['exemption'][$model->mdata['secDecOpt']['exemption']];?></div>
	</div></td></tr>
<tr><td colspan="2"><div style="min-height: 130px;"><p>Other Screening Method(s)<br />(if applicable)</p></div></td></tr>
<tr><td colspan="2" class="tdiv"><div class="col brt" style="width:65%; min-height: 120px;"><p style="margin-bottom: 20px;">Security Status Issued by<br /><br /><br /></p><div class="col" style="padding: 0; width: 50%">Name of Person or Employee ID</div><div class="col" style="padding: 0; width: 50%; border-bottom: 1px dashed #333;"><?=empty($model->mdata['secDecOpt']['asic'])? '' : $model->mdata['secDecOpt']['asic'];?></div></div><div class="col" style="width:35%;"><p style="margin-bottom: 60px;">Security Status Issued on</p><div class="col" style="padding:0; width: 60%">Date <?=date('d/m/Y', empty($model->mdata['secDecOpt']['ts'])? null : $model->mdata['secDecOpt']['ts']);?></div><div class="col" style="padding:0; width: 40%">Time <?=date('H:i', empty($model->mdata['secDecOpt']['ts'])? null : $model->mdata['secDecOpt']['ts']);?></div></div></td></tr>
<tr><td colspan="2"><div style="min-height: 130px;"><p>Regulated Party Category (KC, RA or AO) & Identifier<br />(of any regulated party who has accepted the security status given to a consignment by another regulated party)</p></div></td></tr>
<tr><td colspan="2" class="tdiv"><div class="col brt" style="width:75%; min-height: 200px;"><p>Additional Security Information</p><br /><?=EdiJob::$secDecOptions['info'][$model->mdata['secDecOpt']['info']];?></div><div class="col" style="width:25%;padding-left: 60px;"><p style="margin: 0 0 15px -50px;">Authentication</p><img src="data:image/svg+xml;base64,<?php
	$tok = hash('crc32b', $model->awb.$model->mdata['secDecOpt']['ts'].$model->mdata['secDecOpt']['asic']);
	$dm = new TCPDF2DBarcode('https://www.pcaexpress.com.au/client/validation/secdec/'.$model->id.'?token='.$tok, 'QRCODE');
	echo base64_encode($dm->getBarcodeSVGcode(4, 4, 'black'));
	?>" width="120" /><br /><b><?=$tok;?></div></td></tr>
</table>
</body>
</html>
