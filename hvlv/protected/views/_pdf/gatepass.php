<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking?>
<title>Top Logistics GatePass</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none;font-size: 16px; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
header { padding-bottom: 15px; }
footer { padding-top: 10px; page-break-after: always; }
</style>
</head>

<body width="1120">
<header>
<?php include('_header_tla.php'); ?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td style="text-align: center; font-size: 28px; font-weight: bold;padding: 10px;" colspan="2"><?=!empty($m->mdata['is_pickuplist'])?"Pickup List":"Gate Pass"?></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" width="50%"><table border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td valign="top" style="padding-bottom:3px;font-weight: bold;">Picked up by:</td>
					</tr>
					<tr>
						<td valign="top" style="border: 1px #999 solid;padding:10px;" height="80" width="480"><span style="font-size:20px;font-weight:bold;"><?=$m->company;?></span>
							<br />
							<?=$m->driver;?><br />
							<?=$m->rego;?>
							 <br/><br/>
						 <span><b>Driver Name:</b> _____________________ <span><br/><br/><br/>
						 <span><b>       Rego:</b> ____________________________ </span>
						</td>
					</tr>
				</tbody>
			</table></td>
			<td width="50%" align="left" valign="top"><br />
		<table cellpadding="3" width="540">
				<tbody>
					<tr>
						<td style="font-weight: bold" width="180">Date:</td>
						<td><?=date('Y-m-d H:i', strtotime($m->created));?></td>
					</tr>
					<tr>
						<td style="font-weight: bold">Gate Pass No.:</td>
						<td><?=sprintf('%06d', $m->id);?></td>
					</tr>
					<tr>
						<td style="font-weight: bold">Ref No.:</td>
						<td><?=$m->ref?></td>
					</tr>
					<?php if(!empty($m->mdata['is_pickuplist'])):?>
					<tr>
						<td style="font-weight: bold">Job No.:</td>
						<td><?=@$m->mdata['job_no']?></td>
					</tr>
					<tr>
						<td style="font-weight: bold">Job Name.:</td>
						<td><?=@$m->mdata['job_name']?></td>
					</tr>
					<?php endif;?>
				<tr style="height: 160px;"> <td><b>Sign:</b></td><td> ______________________________</td></tr>
				</tbody>
			</table></td>
		</tr>
</table>
</header>
<table width="100%" cellspacing="0" class="chart"><thead>
				<?php if(!empty($m->mdata['is_pickuplist'])):?>
				<tr style="background: rgba(100,100,100,0.4);">
					<th align="left" width="40">#</th>
					<th align="left">AWB</th>
					<th align="left">Container No.</th>
					<th align="left" width="20%">HBN</th>
					<th align="left">REF</th>
					<th align="right" width="10%">ScanOut</th>
					<th align="right">Status</th>
					<th align="right">Packs</th>
					<th align="right">Weight</th>
					<th align="right">Driver</th>
				</tr>
				<?php else:?>
					<tr style="background: rgba(100,100,100,0.4);">
						<th align="left" width="40">#</th>
						<th align="left">AWB</th>
						<th align="left" width="20%">HBN</th>
						<th align="left">REF</th>
						<th align="left">Memo</th>
						<th align="right" width="10%">ScanOut</th>
						<th align="right">Status</th>
						<th align="right">Packs</th>
						<th align="right">Weight</th>
						<th align="right">Scanned</th>
					</tr>
				<?php
				endif;
				?>
				</thead>
				<tbody>
				<?php
		$qty = 0;
		$wei = 0;
		$sql = 'SELECT fid,json_value(m.meta,"$.caref") as caref,json_value(m.meta,"$.packages") as packages FROM mani_map m INNER JOIN shipment s ON m.fid = s.id WHERE mani_id = '.$m->id;
		if(!empty($m->mdata['is_pickuplist']))
		{
			$sql .=' ORDER BY m.id';
		}else
		{
			$sql .=' ORDER BY ref, consol_id';
		}
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		foreach($rs as $i=>$rc){
			$r = ImParcel::model()->findByPk($rc['fid']);
		// foreach ($m->lines as $i=>$l) {
			// $r = $l->mm();
			$gpscanOut = '';
			if (isset($r->mdata['gpscan_time'])) {
				if (is_array($r->mdata['gpscan_time'])) {
					$gpscanOut = implode('<br/>', $r->mdata['gpscan_time']);
				} else {
					$gpscanOut = $r->mdata['gpscan_time'];
				}
			}
			$containerNo = "";
			if($r->consol->service==Consol::SEACONSOL)
			{
				$containerNo = @$r->consol->mdata['container_no'];
			}
			$ref = empty($rc['caref'])?$r->ref:$rc['caref'];
			$packages = empty($rc['packages'])?$r->pkg:$rc['packages'];
			$weightStr = "";
			$packagesStr = "";
			if(!empty($packages))
			{
				$weight = round($r->weight*$packages/$r->pkg,2);
				$weightStr = $weight;
				$packagesStr = $packages;
			}else
			{
				$weight = 0;
				$weightStr = "";
			}

			//hbn + Rack location
			$strHbn = $r->hbn;
			$listRack = $r->funcGetRackNameList();
			foreach($listRack as $j=>$strRackName){
				$strHbn = $strHbn.'<br/><span style="float:right;"><span style="margin-left:5px;">'.$strRackName.'</span></span>';
			}

			if(!empty($r->cargo_process->mdata['tony_assign_driver'])){
				$assignDriver = $r->cargo_process->mdata['tony_assign_driver'];
			}else{
				$assignDriver = "";
			}
			
			if(!empty($m->mdata['is_pickuplist']))
			{
				echo '<tr class="'.($i&2 > 0? 'even' : 'odd').'"><td align="center" valign="top">'.($i+1).'</td><td valign="top">'.(!empty($r->consol->awb)?$r->consol->awb:'').'</td><td valign="top">'.$containerNo.'</td><td valign="top">'.$strHbn.'</td><td valign="top">'.$ref.'</td><td align="right" valign="top">'.$gpscanOut.'</td><td align="right" valign="top">'.$r->getStatus().'</td><td valign="top" align="right">'.$packagesStr.'</td><td valign="top" align="right">'.$weightStr.'</td><td valign="top" align="right">'.$assignDriver."</td></tr>\n";
			}else
			{
				echo '<tr class="'.($i&2 > 0? 'even' : 'odd').'"><td align="center" valign="top">'.($i+1).'</td><td valign="top">'.(!empty($r->consol->awb)?$r->consol->awb:'').'</td><td valign="top">'.$strHbn.'</td><td valign="top">'.$ref.'</td><td valign="top">'.$r->can.'</td><td align="right" valign="top">'.$gpscanOut.'</td><td align="right" valign="top">'.$r->getStatus().'</td><td valign="top" align="right">'.$packagesStr.'</td><td valign="top" align="right">'.$weightStr."</td><td valign='top'>".$r->scanCount()."</td></tr>\n";
			}
			$qty += $packages;
			$wei += $weight;
		}
		?>
		<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
				</tbody>
		<tfoot>
		<tr><td colspan="7">&nbsp;</td><th align="right"><?=AppHelper::qty_format($qty);?></th><th align="right"><?=$wei;?>Kg</th><th></th></tr>
		</tfoot>
</table>
<footer>
<p><img src="<?=$m->sig;?>" /></p>
<p>Signed by: <?=$m->driver;?></p>
</footer>
<?php include('_pagination.php'); ?>
</body>
</html>
