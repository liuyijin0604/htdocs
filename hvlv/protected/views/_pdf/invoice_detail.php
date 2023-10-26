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
</style>
</head>

<body width="1120">
<header>
<table width="100%" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td valign="top" width="50%"><table border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td style="font-size: 28px; font-weight: bold;" colspan="2">Invoice Detail for: <?=$inv->no;?></td>
					</tr>
					<tr>
						<td valign="top" style="padding-bottom:3px;font-weight: bold;">Bill To:</td>
					</tr>
					<tr>
						<td valign="top" style="border: 1px #999 solid;padding:10px;" height="80" width="480"><span style="font-size:20px;font-weight:bold;"><?=$inv->mdata['name'];?></span>
							<br />
							<?=$inv->mdata['address'];?></td>
					</tr>
				</tbody>
			</table></td>
			<td width="50%" align="right" valign="top"><br />
		<table cellpadding="5" width="440">
				<tbody>
					<tr>
						<td style="font-weight: bold" width="180">Date:</td>
						<td><?=$inv->date;?></td>
					</tr>
					<tr>
						<td style="font-weight: bold">Invoice No.:</td>
						<td><?=empty($inv->no)? $inv->id : $inv->no;?></td>
					</tr>
			<?php 
						if(in_array($inv->type, [10,39])) $awb = [$inv->mdata['awb']];
						if($inv->type == 10){ //Import
							if(!empty($inv->lines)){
								$cno = [];
								$awb = [];
								$mans = [];
								foreach($inv->lines as $il){
									if($il->model == 'Consol'){
										if(!empty($il->mdata['awb'])) $awb[] = $il->mdata['awb'];
										if(!empty($il->mdata['cono'])) $cno[] = $il->mdata['cono'];
									}elseif($il->model == 'Manifest'){
										$mans[] = $il->fid;
									}
								}
							}
						}elseif($inv->type == 20){ //Export

						}elseif($inv->type == 30){ //Direct

						}elseif($inv->type == 60){ //WMS
							echo '<tr><td style="font-weight: bold" valign="top">Billing From:</td><td>', $inv->mdata['billfrom'], '</td></tr>';
							echo '<tr><td style="font-weight: bold" valign="top">Billing To:</td><td>', $inv->mdata['billto'], '</td></tr>';
							echo '<tr><td style="font-weight: bold" valign="top">Terms:</td>
						<td>', $inv->mdata['payterm'], '</td></tr>';

						}

						if(!empty($mans)){
							echo '<tr><td style="font-weight: bold" valign="top">Manifest #:</td>
						<td>', implode(',', $mans), '</td></tr>';
						}
						if(!empty($cno)){
							echo '<tr><td style="font-weight: bold" valign="top">Consol #:</td>
						<td>', implode(',', $cno), '</td></tr>';
						}
						if(!empty($awb)){
							echo '<tr><td style="font-weight: bold" valign="top">MAWB:</td>
						<td>', implode(',', $awb), '</td></tr>';
						}
						if(!empty($inv->ref)){
							echo '<tr><td style="font-weight: bold" valign="top">Ref #:</td>
						<td>', $inv->ref, '</td></tr>';
						}
                    ?>

					<tr>
						<td style="font-weight: bold" width="180">Due Date:</td>
						<td><?=$inv->due;?></td>
					</tr>
				</tbody>
			</table></td>
		</tr></table>
</header>
<?php
include('_inv'.$inv->type.'_detail.php');
?>
<footer>
</footer>
<?php include('_pagination.php'); ?>
</body>
</html>
