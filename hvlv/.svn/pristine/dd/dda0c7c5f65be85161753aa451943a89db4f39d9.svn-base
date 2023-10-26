<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>PCA Express Packing List</title>
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
<?php include('_header_tla.php');?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td style="text-align: center; font-size: 28px; font-weight: bold;padding: 10px;" colspan="2">Packing List</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" width="50%"><table border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td valign="top" style="padding-bottom:3px;font-weight: bold;">Ship To:</td>
					</tr>
					<tr>
						<td valign="top" style="border: 1px #999 solid;padding:10px;" height="80" width="480"><span style="font-size:20px;"><?php
						$pt = WmsTask::model()->find('type = 2120 AND link_id = :t', [':t' => $model->id]);
						if(empty($pt)){
							echo 'Pickup';
						}else{
							echo $pt->mdata['cnee']['company'].'<br />',
							$pt->mdata['cnee']['name'].' &nbsp; '.$pt->mdata['cnee']['tel'].'<br />',
							$pt->mdata['cnee']['address'].' '.$pt->mdata['cnee']['suburb'].' '.$pt->mdata['cnee']['state'].$pt->mdata['cnee']['postcode'];
						}
						?>
						</span>
							<br />
							</td>
					</tr>
				</tbody>
			</table></td>
			<td width="50%" align="right" valign="top"><br />
		<table cellpadding="5" width="440">
				<tbody>
					<tr>
						<td style="font-weight: bold" valign="top" width="120">From:</td>
						<td><?=$model->job->customer->name;?></td>
					</tr>
					<tr>
						<td style="font-weight: bold">Date:</td>
						<td><?=date('Y-m-d');?></td>
					</tr>
					<tr>
						<td style="font-weight: bold">Job No.:</td>
						<td><?=$model->job->no;?>/<?=$model->getNo();?></td>
					</tr>
					<tr>
						<td style="font-weight: bold">Ref No.:</td>
						<td><?=$model->ref;?></td>
					</tr>
					<tr><td style="font-weight: bold" valign="top">PO #:</td>
					<td><?=empty($model->mdata['ordno'])? '' : $model->mdata['ordno'];?></td></tr>
				</tbody>
			</table></td>
		</tr></table>
</header>
<table width="100%" cellspacing="0" class="chart">
<thead>
	<tr style="background: rgba(100,100,100,0.4);">
		<th align="left" width="40">No</th>
		<th align="left" width="150">SKU</th>
		<th align="left" width="320">Item</th>
		<th align="left">Detail</th>
		<th align="left" width="100">Qty</th>
	</tr>
</thead>
<tbody>
<?php
foreach($model->items as $i => $itm){
	if(empty($itm->mdata['si'])) continue;
	$s = WmsStock::model()->findByPk($itm->mdata['si']);
	$sku = $s->getCustSKU();
	echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td>'.($i+1).'</td><td>'.(empty($sku)? $s->prod->ean : $sku).'</td><td>'.$s->prod->name.'</td><td>'.$s->prod->model.'</td><td>'.$itm->mdata['uq'].'</td></tr>';
}
?>
<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
</tbody>
<tfoot>
</tfoot>
</table>
</footer>
<?php include('_pagination.php'); ?>
</body>
</html>
