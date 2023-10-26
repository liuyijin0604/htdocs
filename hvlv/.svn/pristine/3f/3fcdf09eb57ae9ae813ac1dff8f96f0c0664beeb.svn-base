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
.bbt { border-bottom: 1px solid #999; }
</style>
</head>

<body width="1120">
<header>
<?php include('_header_tla.php');?>
<h2 style="text-align: center; font-size: 28px; font-weight: bold; padding: 30px 10px;" colspan="2">Delivery Order</h2>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" width="33%"><table border="0" cellpadding="5" width="90%">
			<tbody>
				<tr>
					<td width="100" height="40" valign="bottom">From:</td>
					<td class="bbt" valign="bottom"><?=$model->job->customer->name;?></td>
				</tr>
				<tr>
					<td height="40" valign="bottom">Time:</td>
					<td class="bbt" valign="bottom">&nbsp;</td>
				</tr>
			</tbody>
		</table></td>
		<td valign="top">
	<table border="0" cellpadding="5" width="90%">
			<tbody>
				<tr>
					<td width="100" height="40" valign="bottom">Driver:</td>
					<td class="bbt" valign="bottom">&nbsp;</td>
				</tr>
				<tr>
					<td height="40" valign="bottom">Rego:</td>
					<td class="bbt" valign="bottom">&nbsp;</td>
				</tr>
				<tr><td height="40" valign="bottom">PO #:</td>
				<td valign="bottom" class="bbt"><?=empty($model->mdata['ordno'])? '' : $model->mdata['ordno'];?></td></tr>
			</tbody>
		</table></td>
		<td valign="top" width="33%">
	<table border="0" cellpadding="5" width="90%">
			<tbody>
				<tr>
					<td width="100" height="40" valign="bottom">Date:</td>
					<td class="bbt" valign="bottom"><?=date('Y-m-d');?></td>
				</tr>
				<tr>
					<td height="40" valign="bottom">Job No.:</td>
					<td class="bbt" valign="bottom"><?=$model->job->no;?>/<?=$model->getNo();?></td>
				</tr>
				<tr>
					<td height="40" valign="bottom">Ref No.:</td>
					<td class="bbt" valign="bottom"><?=$model->ref;?></td>
				</tr>
			</tbody>
		</table></td>
	</tr>
</table>
<br />
</header>
<table width="100%" cellspacing="0" class="chart">
<thead>
	<tr style="background: rgba(100,100,100,0.4);">
		<th align="left" width="40">No</th>
		<th align="left" width="130">Plt No</th>
		<th align="left" width="150">SKU</th>
		<th align="left" width="320">Item</th>
		<th align="left">Detail</th>
		<th align="left" width="100">Qty</th>
	</tr>
</thead>
<tbody>
<?php
foreach($model->actionTask->items as $i => $itm){
	if(empty($itm->mdata['si'])) continue;
	$s = WmsStock::model()->findByPk($itm->mdata['si']);
	$sku = $s->getCustSKU();
	if(empty($itm->mdata['pl']) && !empty($itm->mdata['pli'])){
		$l = WmsLocation::model()->findByPk($itm->mdata['pli']);
		if($l) $itm->mdata['pl'] = $l->code;
	}
	echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td>'.($i+1).'</td><td>'.(empty($itm->mdata['pl'])? '' : $itm->mdata['pl']).'</td><td>'.(empty($sku)? $s->prod->ean : $sku).'</td><td>'.$s->prod->name.'</td><td>'.$s->prod->model.'</td><td>'.$itm->mdata['uq'].'</td></tr>';
}
?>
<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
</tbody>
<tfoot>
</tfoot>
</table>
<footer>
<br />
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" width="60%">
			<table border="0">
				<tr><td>Notes:</td></tr>
				<tr><td><b>All goods are received in good packing order, there is NO physical or visual damage to the freight</b></td></tr>
			</table>
		<td valign="top">
	<table border="0" cellpadding="5" width="100%">
			<tbody>
				<tr>
					<td width="130" style="font-weight: bold" height="100" valign="bottom">Driver<br />Signature:</td>
					<td class="bbt"><?php
						$f = FileRepo::model()->find(['condition' => 'type = 84 AND status = 20 AND mime = "image/png" AND fid = :fid', 'params' => [':fid' => $model->id], 'order' => 't.id DESC']);
						if(empty($f)){
							echo '&nbsp;';
						}else{
							echo '<img src="https://os.toplogistics.com.au'.$f->getUrl().'" style="max-height:100px;" />';
						}
					?></td>
				</tr>
				<tr>
					<td style="font-weight: bold" height="100" valign="bottom">WH Manager <br />Signature:</td>
					<td class="bbt">&nbsp;</td>
				</tr>
			</tbody>
		</table></td>
	</tr>
</table>
</footer>
<?php include('_pagination.php'); ?>
</body>
</html>
