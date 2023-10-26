<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>Top Logistics Australia GatePass</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
header { padding-bottom: 15px; }
footer { padding-top: 10px; page-break-after: always; }
</style>
</head>

<body width="1120">
<header>
<?php 
$hdr = '_header_tla.php';
include($hdr);
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td style="text-align: center; font-size: 28px; font-weight: bold;padding: 10px;" colspan="2">Gate Pass</td>
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
						<td valign="top" style="border: 1px #999 solid;padding:10px;" height="80" width="480"><span style="font-size:20px;font-weight:bold;"><?= 
						$model->type==10?"Self Pick UP":$model->getCourierName();
						?></span>
							<br />
							 <br/><br/>
						 <span><b>Driver Name:</b><?=$model->driver_name?><span><br/><br/><br/>
						 <span><b>       Rego:</b><?=$model->driver_rego?> </span>
						</td>
					</tr>
				</tbody>
			</table></td>
			<td width="50%" align="left" valign="top"><br />
		<table cellpadding="3" width="540">
				<tbody>
					<tr>
						<td style="font-weight: bold" width="180">Date:</td>
						<td><?=date('Y-m-d H:i', strtotime($model->create_time));?></td>
					</tr>
					<tr>
						<td style="font-weight: bold">Gate Pass No.:</td>
						<td><?=$model->no;?></td>
					</tr>
					<tr>
						<td style="font-weight: bold">Ref No.:</td>
						<td><?=$model->ref?></td>
					</tr>
						<tr style="height: 160px;"> <td><b>Sign:</b></td><td><img src="<?=isset($model->mdata['gatepass_signature'])?$model->mdata['gatepass_signature']:''?>" width="150" height="100"/></td></tr>
				</tbody>
			</table></td>
		</tr>
</table>
</header>
<table width="100%" cellspacing="0" class="chart"><thead>
				<tr style="background: rgba(100,100,100,0.4);">
					<th align="left" width="40">#</th>
					<th align="left">HBN</th>
					<th align="left">REF</th>
					<th align="right" width="140">ScanOut</th>
					<th align="right">Status</th>
					<th align="right">Location</th>
					<th align="right">Pks</th>
					<th align="right" width="30">Plt Qty</th>
<!--           <th align="right">Pack</th> -->
					<th align="right" width="40">Weight</th>
				</tr>
				</thead>
				<tbody>
				<?php
		$qty = 0;
		$wei = 0;
		$count = 1;
		$lines = $model->getAllLines();
		$lSize = count($lines);
		$allWeight = 0;
		$data = [];
		for ($i=0; $i < $lSize; $i++) 
		{ 
			$l = $lines[$i];
			$r = $l->shipment;
			$rc = null;
			if(!empty($l->mdata['cargo_process_id']))
			{
				$rc = CargoProcess::model()->findByPk($l->mdata['cargo_process_id']);
			}
			$check =true;
			$j = $i;
			$scan_time = "";
			$thisPacks = 0;
			while($check) {
				if($lines[$j]->connote_no==$l->connote_no)
				{
					$qty += 1;
					$wei += round($r->weight/$r->pkg,2);
					$scan_time .=$lines[$j]->sno."-".$lines[$j]->scan_time."</br>";
					$thisPacks++;
					$j++;
				}else
				{
					$check =false;
					$i = $j-1;
				}
				if($j==$lSize) $check =false; $i = $j-1;
			}
			
			$containerNo = $r->consol->service==Consol::SEACONSOL? @$r->consol->mdata['container_no'] : '';
			$ref = empty($rc)?$r->ref:$rc->mdata['caref'];
			$packages = empty($rc)?$r->pkg:$rc->mdata['left_packages'];
			$weight =empty($rc)?round($r->weight*$thisPacks/$r->pkg,2):round($r->weight*$packages/$r->pkg,2);
			$allWeight+=$weight;
			if(!isset($data[$r->consol_id])){
				$data[$r->consol_id] = ['awb' => $r->consol->awb, 'container' => $containerNo, 'rows' => []];
			}
			$data[$r->consol_id]['rows'][] = [$r->hbn, $ref, $scan_time, $l->getStatusAtScan(), $r->getRackName(true), $packages, @$r->mdata['amzon_pallet'], $weight];
		}
		$rc = 0;
		foreach($data as $cid => $consol){
			echo '<tr class="'.(($rc++)&2 > 0? 'even' : 'odd').'"><td>&nbsp;</td><td valign="top" colspan="8">AWB: '.$consol['awb'].(empty($consol['container'])? '' : ' &nbsp; Container: '.$consol['container'])."</td></tr>\n";
			foreach($consol['rows'] as $row){
				echo '<tr class="'.(($rc++)&2 > 0? 'even' : 'odd').'"><td align="center" valign="top">'.($count).'</td><td valign="top">'.$row[0].'</td><td valign="top">'.implode('</td><td align="right" valign="top">', array_slice($row, 1))."</td></tr>\n";
				$count++;
			}
		}
//		$i++;
		?>
		<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
				</tbody>
		<tfoot>
		<tr><td colspan="7">&nbsp;</td><th align="right"><?=AppHelper::qty_format($qty);?></th><th align="right"><?=$allWeight;?>Kg</th></tr>
		</tfoot>
</table>
<footer>
</footer>
<?php include('_pagination.php'); ?>
</body>
</html>
