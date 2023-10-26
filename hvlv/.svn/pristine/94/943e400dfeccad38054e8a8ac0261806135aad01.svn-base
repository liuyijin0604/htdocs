<?php
//opcache_invalidate(__FILE__);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$is_lbx = (!empty($endTrans->mdata['fw_satchelSize']) && ($label->weight <= 0.1));
?>
<div style="background-color:#000;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="60%" valign="top">
			<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/fastway_logo_bw_invert.svg" width="70%" />
		</td>
		<td valign="top">
			<div style="width:90%; border: 3px solid #fff; padding: 5px 10px; font-size: 40px; font-weight: bold; text-align:center; color:#fff;">
			<?=isset($endTrans->mdata['fw_toRf'])? $endTrans->mdata['fw_toRf'].' '.$endTrans->mdata['fw_subDepotCode'] : (isset($endTrans->mdata['fw_dest_code'])? $endTrans->mdata['fw_dest_code'] : 'SYD');
			?><br /><span style="font-size:0.5em"><?=isset($endTrans->mdata['fw_toCf'])? $endTrans->mdata['fw_toCf'] : ''?></span></div>
		</td>
	</tr>
</table>
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><td valign="top"><b>Label No:</b> <span style="font-size: 1.2em"><?= $label->ref; ?></span><br /><br /><b>To:</b></td>
		<td valign="top" style="font-size:0.8em">Reference:<br /><?php echo $label->hbn; ?></span><?php if (!empty($label->cref)) {
				echo '<br /><span style="font-size:1.1em; font-weight:600;">'.$label->cref . '</span>';
			} ?>
			<?php if (!empty($label->mdata['cust_ref1'])) {
				echo '<br/>'.$label->mdata['cust_ref1'];
			}?></td></tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="65%" valign="top">
			<div style="height:360px;overflow:hidden">
				<p style="font-size:1.4em;padding: 5px;"><?=ucwords(strtolower($label->cnee->name)).(empty($label->cnee->company) || $label->cnee->name == $label->cnee->company? '' : ', '.substr(ucwords(strtolower($label->cnee->company)), 0, 33)); ?><br/>
					<?= nl2br($label->cnee->apAddress());?><br>
					<b>Ph:</b> <?= $label->cnee->tel; ?></p>
			</div>
		</td>
		<td valign="top">
			<div style="height: 125px; margin: -5px 30px 0 0; text-align:center;">
				<?php
				$scan_number=$label->hbn;
				$b=2.3;
				if ($label->agent_id==1002) {
					$scan_number=$label->cref;
					$b=2;
				}
				?>
				<p>Internal use only<br />
				 <img src="data:image/svg+xml;base64,<?php
				   $dm = new TCPDF2DBarcode(chr(232).$scan_number, 'DATAMATRIX');
				  echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(4, 4, 'black')));
				  ?>" width="80" />
				  </p>
			</div>
			From:<br /><?php
	   // echo $label->cnor->name, '<br />', $label->cnor->fullAddress(array('suburb', 'state', 'postcode')), '<br />', $label->cnor->tel;
		$name=$label->cnor->name;
		$org=$label->agent;
		if (!empty($org->extra['delivery_label_name'])) {
			$name=$org->extra['delivery_label_name'];
		}
		if($label->mdata['chargecode']=="9320")
		{
			$warehouseAddress = "Warehouse 6 45-53 Davies Road Padstow NSW 2211<br/>Buyer is Not to return in person";
		}else
		{
		$warehouseAddress = '1/233 Milperra Rd, 
		Bankstown Aerodrome NSW 2200<br/>Buyer is Not to return in person';
		
		$orgRate = null;
		if(!empty($label->mdata['org_rate_id']))
		{
			$orgRate = OrgRate::model()->findByPk($label->mdata['org_rate_id']);
		}
		if(!empty($orgRate)&&preg_match('/mel/i', $orgRate->code))
		{
			$warehouseAddress = '3B/8 Judge St, 
		Sunshine VIC 3020<br/>Buyer is Not to return in person';
		}



		}
		/*if (in_array($label->agent_id, [1427, 1656])) { //d2z
			$warehouseAddress="18 Grimes Court <br />DERRIMUT VIC 3026";
		}*/
		if(empty($label->withoutSender))
		{
			echo $name, '<br />', $warehouseAddress;
		}
		?>
		</td>
	</tr>
	</table>
<div style="padding: 10px;">
	<b>Special Instructions</b><br />
	<p style="text-align:center;font-size: 1.4em; font-weight:bold;"><?=(empty($endTrans->mdata['fw_satchelSize'])||$label->weight>0.5)? 'Signature On Delivery Required' : ($is_lbx? '300G - LETTER BOX' : 'ATL, Safe place only');?></p>
</div>
<div style="border: 3px solid #000;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" height="50px;">
		<tr style="font-size:20px;">
			<td width="20%" valign="middle"><b>Items</b><br />1 of 1</td>
			<td align="center" valign="middle"><b>Date</b><br /><?= $label->created; ?></td>
			<td align="center" valign="middle"><?=(empty($endTrans->mdata['fw_satchelSize'])||$label->weight>0.5)? '<b>Weight</b><br />'.$label->weight.'Kg' : '<b>Satchel Size</b><br />300';?></td>
		</tr>
	</table>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td><img src="data:image/svg+xml;base64,<?php
	$addr = str_split($label->cnee->address, 20);
	$dm_data = [substr(empty($label->cnee->company)? $label->cnee->name : $label->cnee->company, 0, 35), '', $addr[0], empty($addr[1])? '' : $addr[1], $label->cnee->suburb, '', $label->cnee->postcode, $label->cnee->tel, '', substr($label->cnee->name, 0, 25)];
	$dm = new TCPDF2DBarcode(implode('|', $dm_data), 'DATAMATRIX');
	echo base64_encode($dm->getBarcodeSVGcode(4, 4, 'black'));
	?>" width="150" /></td><td><?php
	if(!$is_lbx||$label->weight>0.5){
		echo '&nbsp;';
	}else{
		echo '<span style="font-size: 1.8em;font-weight: bold;">L<br />B<br />X</span>';
	}
	?></td>
	<td align="right" style="padding-right: 25px; position: relative;"><div style="font-size: 40px; font-weight: bold; -webkit-transform-origin: center; -webkit-transform: rotate(90deg); position:absolute; right: -28px; top: 50px;"><?=strtoupper($endTrans->mdata['fw_origin']);?></div>
<div style="text-align: center;">
	<p style="padding:5px;">
		<?php
		$bc = new TCPDFBarcode($label->ref, 'C128');
		$bc->getBarcodeSVG(3, 130, 'black');
		?></p>
	<p style="padding: 5px 0;font-size: 1.2em; line-height: 1em;"><?= $label->ref; ?></p>
</div></td></tr></table>
</div>
<?php if(!empty($label->mdata['cust_ref2'])):?>
<div style="position: absolute; margin-top:0.5em; padding: 5px 10px; font-weight: bold; text-align: center;"><?= strtoupper(substr($label->mdata['cust_ref2'], 0, 50))?></div>
<?php
endif;
if(!empty($endTrans->mdata['fw_toRf'])):
$sort = FastwayAPI::rf2sort($endTrans->mdata['fw_toRf'], $endTrans->mdata['fw_origin']);
$fw_sort_map = ['METRO' => 1, 'NSW' => 2, 'VIC' => 3, 'QLD' => 4, 'SA/WA' =>5, 'TAS' => 6];
$sort .= isset($fw_sort_map[$sort])? ' &nbsp; '.$fw_sort_map[$sort] : '';
?>
<p align="right" style="margin-top:0.5em"><b style="background: #000; color: #fff; font-size:1.3em; padding: 5px 15px;"><?=$sort;?></b></p>
<?php
endif;
if (!empty($label->mdata['show_sku']) && is_array($label->eitems['sku'])) {
	echo '<p>'.implode(', ', $label->eitems['sku']).'</p>';
}?>
