<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Landscape" />
<?php //--disable-smart-shrinking?>
<title><?=$type?> Manifest</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 16px; text-rendering: optimize-speed; width: 1600px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
table.chart1 td, table.chart1 th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart1{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
table.charts td, table.charts th{ border:none }
table.charts{ border: none;  }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
.even1 { background: rgba(200, 200, 200, 0.6);}
header { padding-bottom: 15px; }
footer { padding-top: 10px; page-break-after: always; }
</style>
</head>
<body width="1600" >
<header>
<?php 
$hdr = '_header_tla.php';
$companyName = Org::IM_COMPANY_NAME;
$companyEmail = Org::IM_EMAIL;
$companyAddress=Org::IM_COMPANY_ADDRESS;
$companyCity =  Org::IM_COMPANY_CITY;
$companyPhone = Org::IM_COMPANY_PHONE_SHOW;
if(!empty($behalf)){
  switch($behalf){
    case 'priority':
      $hdr = '_header_tla.php';
    break;
  }
  if(!empty($inv->mdata['suborg'])){
    $sorg = Org::model()->findByPk($inv->mdata['suborg']);
    $inv->mdata['name'] = $sorg->name;
    $inv->mdata['address'] = $sorg->getAddress();
  }
}
if(Yii::app()->name == 'TLA'){
  $hdr = '_header_tla.php';
  $companyName = strtoupper(Org::IM_COMPANY_NAME);  
  $companyEmail = Org::IM_EMAIL;
  $companyAddress= strtoupper(Org::IM_COMPANY_ADDRESS);
  $companyCity =  strtoupper(Org::IM_COMPANY_CITY);
}
include($hdr);
?>
</header>
<table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 30px;height: 50px; ">
 <tr>
	 <td style="text-align: left; font-size: 30px;font-weight: bold;padding-bottom:10px;" width="50%"><?=$title?></td>
	 <td align="right" width="50%">
		 <table   width="90%"cellspacing="0" cellpadding="0"  class="chart1">
			 <tr ><th class="even1" width="50%" align="left">CONSOL</th><td width="50%"><?=@$model->no?></td></tr>
			 <tr ><th class="even1" width="50%" align="left"><?=$type=="Sea"?"OCEAN BILL OF LANDING":"MAWB"?></th><td width="50%"><?=@$model->awb?></td></tr>
			 <tr ><th class="even1" width="50%" align="left">DATE</th><td width="50%"><?=date("d-M-y H:i")?></td></tr>
		</table>
	 </td>
</tr>
</table>
	<div style="font-weight:bold;margin-bottom: 5px;">CONSOL DETAILS:</div>
	<table width="100%" cellspacing="0" class="chart1">
		<tr class="even">
			<th width="33%" align="left">Export Agent</th><th width="33%" align="left">Import Agent</th><th width="34%" align="left">Arrival CFS</th>
		</tr>
		<tr>
			<td>
				   <table width="100%" height="150" cellspacing="0" cellpadding="0" class="charts">
						<tr valign="top"><td colspan="2">The Freight Manager</td></tr>
						<tr valign="bottom"><td width="50%">Phone:</td><td width="50%">Fax:</td></tr>
					</table>
			</td>
			<td>
				<table width="100%"  height="150;" cellspacing="0" cellpadding="0" class="charts">
						<tr valign="top"><td colspan="2">The Freight Manager<br/>
								<?=$companyName?><br/>
								<?=$companyAddress?><br/>
								<?=$companyCity?><br/>
								AUSTRALIA<br/>
							</td></tr>                       
						<tr valign="bottom"><td width="60%">Phone:<?=$companyPhone?></td><td width="40%">Fax:</td></tr>
				</table>
			</td>
			<td>
				 <table width="100%"  height="150;" cellspacing="0" cellpadding="0" class="charts">
						<tr valign="top"><td colspan="2"><?=@nl2br($model->mdata['arrival_cfs']);?></td></tr>                       
						<tr valign="bottom"><td width="50%">Phone:</td><td width="50%">Fax:</td></tr>
				</table>
			</td>
		</tr>
		<tr>
			  <td valign="top">   
				  <table width="100%"cellspacing="0" cellpadding="0" class="chart1">
					<tr class="even" height="30"><th align="left">TOTAL WEIGHT</th><th align="left">TOTAL VOLUME</th><th align="left">CHARGEABLE</th></tr>                       
					<tr valign="bottom"><td width="33%"><?=@$model->mdata['awb_wt'];?>KG</td><td width="33%"><?=@sprintf("%.3f", $model->mdata['cbm'])?>M<sup>3</sup></td><td width="34%"><?=@$model->mdata['cgb_wt'];?>KG</td></tr>
				</table>
			 </td>
			  <td>
				<table width="100%" height="60" cellspacing="0" cellpadding="0" class="chart1">
					<tr valign="top" class="even" height="30"><th align="left">CARRIER</th></tr>                       
					<tr valign="bottom"><td align="left"><?=@$model->mdata['sea_carrier']?></td></tr>
				</table>
			</td>
		 <td>
				<table width="100%" height="60" cellspacing="0" cellpadding="0" class="chart1">
					<tr valign="top" class="even" height="30"><th align="left">CUSTOMER ENTRY NUMBEER</th></tr>                       
					<tr valign="bottom"><td align="left"></td></tr>
				</table>
			</td>
		</tr>
		<tr>
			  <td>   
				  <table width="100%" height="60" cellspacing="0" cellpadding="0" class="chart1">
					  <tr valign="top" class="even" height="30"><th align="left">PACAGES</th><th align="left">MASTER FREIGHT</th></tr>                       
					<tr valign="bottom"><td width="50%" align="left"><?=$type=="Sea"?@$model->mdata['shipments']:@$model->mdata['b&l_pcs']?> Package(s)</td ><td width="50%" align="left"></td></tr>
				</table>
			 </td>
			  <td>
				<table width="100%" height="60" cellspacing="0" cellpadding="0" class="chart1">
					<tr valign="top" class="even" height="30"><th align="top"><span  style="width: 50%;display: inline-block; text-align:left;">ORIGIN</span><span style="width: 48%;display: inline-block;text-align:right;" >ETD</span></th></tr>                       
					<tr valign="bottom"><td><span  style="width: 50%;display: inline-block; text-align:left;"><?=@$model->pol?></span><span style="width: 48%;display: inline-block;text-align:right;" ><?= empty($model->etd)?"":date('d-M-y', strtotime($model->etd))?></span></td></tr>
				</table>
			</td>
		 <td>
				<table width="100%" height="60" cellspacing="0" cellpadding="0" class="chart1">
					<tr valign="top" class="even" height="30"><th><span  style="width: 50%;display: inline-block; text-align:left;">DESTINATION</span><span style="width: 48%;display: inline-block;text-align:right;" >ETA</span></th></tr>                       
					<tr valign="bottom"><td><span  style="width: 50%;display: inline-block; text-align:left;"><?=@$model->pod?></span><span style="width: 48%;display: inline-block;text-align:right;" ><?= empty($model->eta)?"":date('d-M-y', strtotime($model->eta))?></span></td></tr>
				</table>
			</td>
		</tr>
				<tr>
			  <td>   
				  <table width="100%" height="60" cellspacing="0" cellpadding="0" class="chart1">
					  <tr valign="top" class="even" height="30"><th align="left">CARRIER BOOKING REFERENCE</th></tr>                       
					<tr valign="bottom"><td align="left"></td></tr>
				</table>
			 </td>
			  <td>
				<table width="100%" height="60" cellspacing="0" cellpadding="0" class="chart1">
					<tr valign="top" class="even" height="30"><th align="left">AGENT'S REFERENCE</th></tr>                       
					<tr valign="bottom"><td align="left"></td></tr>
				</table>
			</td>
		 <td>
				<table width="100%" height="60" cellspacing="0" cellpadding="0" class="chart1">
					<tr valign="top" class="even" height="30"><th align="left">LAST FOREIGN PORT</th></tr>                       
					<tr valign="bottom"><td align="left"></td></tr>
				</table>
			</td>
		</tr>
	</table>
	   <div style="font-weight:bold;margin-bottom: 5px;">ROUTING INFORMATION:</div>
		<table width="100%" cellspacing="0" class="chart1">
		<tr class="even">
			<th width="33%" align="left">Mode</th><th width="33%" align="left"><?=$type=="Sea"?"Vessel/Voyage/IMO(LIoyds)":"Airline/Flight No."?></th><th width="34%" align="left">Carrier</th>
		</tr>
		 <tr>
				<td><?=strtoupper($type)?></td>
				<td><?php if($type=="Sea"):?><?=@$model->mdata['sea_vessel']?> / <?=$model->flight?> / <?=$model->airline?>
					<?php else:?><?=@$model->mdata['sea_vessel']?> / <?=$model->airline?> / <?=$model->flight?>
					<?php endif;?>
				</td>

				<td><?=$model->mdata['sea_carrier']?></td>
		 </tr>
		  <?php if($type == "Sea"):?>
			<tr class="even">
			 <th width="33%" align="left">Load</th><th width="33%" align="left">Disch.</th><th width="34%" align="left"><span style="display: inline-block;text-align: left;width: 48%;">ETD</span><span style="display:inline-block; text-align: right;width: 48%;">ETA</span></th>
			</tr>
		  <tr>
				<td><?=@$model->mdata['sea_load_port']?></td><td><?=@$model->pod?></td><td><span style="display: inline-block;text-align: left;width: 48%;"><?= empty($model->etd)?"":date('d-M-y', strtotime($model->etd))?></span><span style="display:inline-block; text-align: right;width: 48%;"><?= empty($model->eta)?"":date('d-M-y', strtotime($model->eta))?></span></td>
		 </tr>
		<?php endif;?>
		</table>
	   <div style="height:5px;"></div>
	   <?php if($type == "Sea"):?>
		  <table width="100%" cellspacing="0" class="chart1">
		<tr class="even">
			<th width="25%" align="left">CONTAINER</th><th width="25%" align="left">SEAL</th><th width="25%" align="left">TYPE</th><th width="25%" align="left">TARE WEIGHT</th>
		</tr>
		 <tr>
			 <td><?=@$model->mdata["container_no"]?></td><td><?=@$model->mdata['sea_seal']?></td><td><?=@DmawbConsol::$containerTypes[$model->mdata['sea_type']]." ".@$model->mdata['cargo_type']?></td><td><?=@$model->mdata['tare_wt']?></td>
		 </tr>
			<tr class="even">
				<th width="25%" align="left">NET WEIGHT</th><th width="25%" align="left">GROSS WEIGHT</th><th width="25%" align="left">VOLUME</th><th width="25%" align="left">PACKAGES</th>
			</tr>
		  <tr>
			  <td><?=@$model->mdata['awb_wt'];?>KG</td><td><?=empty($model->mdata['tare_wt'])?@$model->mdata['awb_wt']:$model->mdata['tare_wt']+floatval(@$model->mdata['awb_wt']);?>KG</td><td><?=@sprintf("%.3f", $model->mdata['cbm']);?> M<sup>3</sup></td><td><?=@$model->mdata['b&l_pcs'];?></td>
		 </tr>
		</table>
	<?php endif?>
		<div style="font-weight:bold;margin-bottom: 5px;">SHIPMENT DETAILS:</div>
<?php
if($model->service == 10):
?>
	<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr class="even">
				<th align="left">Sub-Master</th>
				<th align="left" width="320">CONSIGNOR</th>
				<th align="left" width="320">CONSIGNEE</th>
				<th align="left" width="240">NATURE OF GOODS</th>
				<th align="left" width="300">HANDLING INSTRUCTIONS</th>
			</tr>
		</thead>
		<tbody>
		<tr style="page-break-inside: avoid;">
			<td style="min-height:140px" valign="top"><table width="100%" cellspacing="0" cellpadding="0" class="chart1">
				<tr><td class="even1" width="120">SMWB:</td><td><?=$model->no;?></td></tr>
				<tr><td class="even1">Job Ref:</td><td>&nbsp;</td></tr>
				<tr><td class="even1">Wt/Vol/Pkg:</td><td><?=@$model->mdata['awb_wt'];?>KG/<?=@sprintf("%.3f", $model->mdata['cbm'])?>M<sup>3</sup>/<?=$type=="Sea"?@$model->mdata['shipments']:@$model->mdata['b&l_pcs']?> PKG</td></tr>
				<tr><td class="even1">Shipper Ref:</td><td>&nbsp;</td></tr>
			</table></td>
			<td valign="top"><?=@$model->mdata['cnor'];?><br/>
			<?=@$model->mdata['cnor_addr'];?></td>
			<td valign="top"><?=@$model->mdata['cnee'];?><br/>
			<?=@$model->mdata['cnee_addr'];?></td>
			<td valign="top">&nbsp;</td>
			<td valign="top">&nbsp;</td>
		</tr>
		</tbody>
	</table>
<?php
endif;
?>
		<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr class="even">
				<th align="left">HOUSE</th>
				<th align="left" width="320">CONSIGNOR</th>
				<th align="left" width="320">CONSIGNEE</th>
				<th align="left" width="240">NATURE OF GOODS</th>
				<th align="left" width="300">HANDLING INSTRUCTIONS</th>
			</tr>
		</thead>
		<tbody>
<?php
foreach ($model->shipments as $p):
	if($p->hbn == $model->no || $p->status == 42 || ($p->bwf & 4096) > 0) continue;
?>
		<tr style="page-break-inside: avoid;">
			<td style="min-height:140px" valign="top"><table width="100%" cellspacing="0" cellpadding="0" class="chart1">
				<tr><td class="even1" width="120">HBL:</td><td><?=$p->hbn;?></td></tr>
				<tr><td class="even1">Job Ref:</td><td><?=$p->ref;?></td></tr>
				<tr><td class="even1">Wt/Vol/Pkg:</td><td><?=@$p->weight;?>KG /<?=@sprintf("%.3f", $p->cbm*$p->pkg)?> M<sup>3</sup> / <?=(($title=="Grouped LCL Delivery Orders")?(!empty($mtype)&&$mtype==2?$p->pkg:$p->scanCount()):$p->pkg)?> PKG</td></tr>
				<tr><td class="even1">Shipper Ref:</td><td><?=$p->cref;?></td></tr>
			</table></td>
			<td valign="top"><?=@$p->cnor->name;?><br/>
			<?php if (!empty($p->cnor->company)) {
				echo $p->cnor->company."<br/>";
			}?>
			<?=$p->cnor->address.",".$p->cnor->city;?></br>
			<?=$p->cnor->state.", ".$p->cnor->country;?><br /><br />
			Phone: <?=$p->cnor->tel;?></td>
			<td valign="top"><?=@$p->cnee->name;?><br/>
			<?php if (!empty($p->cnee->company)) {
				echo $p->cnee->company."<br/>";
			}?>
			<?=$p->cnee->address;?><br/>
			<?=$p->cnee->suburb." ".$p->cnee->state." ".$p->cnee->postcode;?><br/>
			Australia<br /><br />
			Phone: <?=$p->cnee->tel;?></td>
			<td valign="top"><table cellpadding="0" cellspacing="0" class="chart1" width="100%">
				<tr><th class="even1">Release:</th><td><?=@$p->mdata['sea_relase_type'];?></td></tr>
				<tr><th class="even1">Type:</th><td>GEN(General)</td></tr>
				<tr><th class="even1" valign="top">Goods:</th><td><?=strtoupper(substr($p->getGoods(), 0, 30))." ETC";?></td></tr>
			</table></td>
			<td valign="top"><table cellpadding="0" cellspacing="0" class="chart1" width="100%">
				<tr><td height="60"><?=empty($p->mdata['sea_instruction'])? '&nbsp;' : $p->mdata['sea_instruction'];?></td></tr>
				<tr><th class="even1" height="30" align="left">Marks &amp; Numbers</th></tr>
				<tr><td><?=empty($p->mdata['sea_mark_number'])? '&nbsp;' : $p->mdata['sea_mark_number'];?></td></tr>
			</table></td>
		</tr>
<?php endforeach; ?>
			<tr><td class="plc1" colspan="6">&nbsp;</td></tr>
		</tbody>
		</table>
<?php //include('_pagination.php'); ?>
</body>
</html>
