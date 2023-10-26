<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>Top Logistics Invoice</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
header { padding-bottom: 15px; }
footer { padding-top: 10px; page-break-after: always; }
table { page-break-inside: avoid; }
</style>
</head>

<body width="1120">
<header>
<?php
$inv = $invs[0];
$hdr = '_header_tla.php';
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
if (in_array($inv->to_id, Org::$displayJobRef) && !empty($inv->job->mdata['ref'])) {
	$displayJobref = true;
} else {
	$displayJobref = false;
}

if(Yii::app()->name == 'TLA'){
	$hdr = '_header_tla.php';
}
include($hdr);

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td style="text-align: center; font-size: 28px; font-weight: bold;padding: 10px;" colspan="2">Account Statement</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" width="50%"><table border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td valign="top" style="padding-bottom:3px;font-weight: bold;">Bill To:</td>
					</tr>
					<tr>
						<td valign="top" style="border: 1px #999 solid;padding:10px;" height="80" width="480"><span style="font-size:20px;font-weight:bold;"><?=!empty($inv->mdata['name']) ? $inv->mdata['name'] : $inv->cust->name;?></span>
							<br />
							<?=!empty($inv->mdata['address']) ? $inv->mdata['address'] : $inv->cust->getAddress();?></td>
					</tr>
				</tbody>
			</table></td>
			<td width="50%" align="right" valign="top"><br />
		<table cellpadding="5" width="440">
				<tbody>
					<tr>
						<td style="font-weight: bold" width="180">Date:</td>
						<td><?=empty($date) ? date('Y-m-d') : $date;?></td>
					</tr>
<?php
if(!empty($_POST['suborg_id'])){
	$subo = Org::model()->findByPk($_POST['suborg_id']);
	echo '<tr><td style="font-weight: bold" valign="top">Sub A/C:</td><td>', $subo->name, '</td></tr>';
}
?>
				</tbody>
			</table></td>
		</tr></table>
</header>
<table width="100%" cellspacing="0" class="chart">
		<thead>
			<tr style="background: rgba(100,100,100,0.4);">
				<th align="left" width="100">No</th>
				<?php if ($displayJobref) { ?>
					<th align="left">Ref</th>
				<?php } ?>
				<th align="left">Type</th>
				<th align="left" width="120">Date</th>
				<th align="left" width="120">Due</th>
				<th align="right" width="125">Total</th>
				<th align="right" width="125">Paid</th>
				<th align="right" width="125">Balance</th>
				<th align="right" width="125">Currency</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$tot = [0,0,0];
		foreach($invs as $i => $inv){
			if(!empty($_POST['suborg_id']) && $_POST['suborg_id'] != $inv->mdata['suborg']){
				continue;
			}
			$paid = empty($date) ? $inv->paid() : $inv->paidBefore($date);
			echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td valign="top">'.$inv->no.'</td>';
			if ($displayJobref) {
				echo '<td valign="top">' . @$inv->job->mdata['ref'] . '</td>';
			}
			echo '<td valign="top">'.$inv->getType().'</td><td>'.$inv->date.'</td><td>'.($inv->type != 100 ? $inv->due : $inv->posted).'</td><td align="right">'.AppHelper::money_format('%i', $inv->total).'</td><td align="right">'.AppHelper::money_format('%i', $paid).'</td><td align="right">'.AppHelper::money_format('%i', $inv->total - $paid)."</td><td>".$inv->getCurrency()."</td></tr>\n";
				$tot[0] += $inv->total;
				$tot[1] += $paid;
				$tot[2] += $inv->total - $paid;
		}
		if(!empty($ata) && $ata > 0){
			if (empty($data)) {
				// echo '<tr class="'.($i++%2 == 1? 'even' : 'odd').'"><td valign="top">&nbsp;</td><td valign="top">Credit</td><td>&nbsp;</td><td>&nbsp;</td><td align="right">&nbsp;</td><td align="right">'.AppHelper::money_format('%i', $ata).'</td><td align="right">'.AppHelper::money_format('%i', 0-$ata)."</td><td>&nbsp;</td></tr>\n";
				// $tot[1] += $ata;
				// $tot[2] -= $ata;
				if ( isset($ps)  ) {
					foreach ( $ps as $p ) {
						echo '<tr class="'.($i++%2 == 1? 'even' : 'odd').'"><td valign="top">'.$p['no'].'</td><td valign="top">&nbsp;</td><td>'.$p['ref'].'</td><td>&nbsp;</td><td align="right">&nbsp;</td><td align="right">'.AppHelper::money_format('%i', 0-$p['total']).'</td><td align="right">&nbsp;</td><td>'.Invoice::$currencies[$p['currency']].'</td></tr>';
						$tot[1] += $p['total'];
						$tot[2] -= $p['total'];
					}
				}
			} else {

			}
		}

		?>
			<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		</tbody>
		<tfoot>
		<tr><td>&nbsp;</td><th align="right" colspan="3">Total:</th><th align="right"><?php echo AppHelper::money_format('%i', $tot[0]);?></th><th align="right"><?php echo AppHelper::money_format('%i', $tot[1]);?></th><th align="right" style="font-size:1.2em;"><?php echo AppHelper::money_format('%i', $tot[2]);?></th><td>&nbsp;</td></tr>
		
		</tfoot>
</table>
<?php include('_pagination.php'); ?>
</body>
</html>
