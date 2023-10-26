<?php
//opcache_invalidate(__FILE__);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="60%" valign="top">
			<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/fastway_logo_bw.svg" width="70%" />
		</td>
		<td valign="middle">
			<div style="background-color: #000; color:#fff; font-size:36px;text-align: center;vertical-align: middle; padding: 10px;">
			<?php
				if (isset($endTrans->mdata['fw_dest_code'])) {
					echo $endTrans->mdata['fw_dest_code'];
				}elseif(isset($endTrans->mdata['fw_toRf'])){
					echo trim($endTrans->mdata['fw_toRf'].' '.$endTrans->mdata['fw_subDepotCode']);
					if(isset($endTrans->mdata['fw_toCf'])) echo ' <span style="font-size:0.6em">'. $endTrans->mdata['fw_toCf'].'</span>';
				} else {
					echo 'SYD';
				}
			?>
		</div>
		</td>
	</tr>
</table>

<div style="margin-top: 20px;text-align: center;">
	<p style="padding:5px;">
		<?php
		$bc = new TCPDFBarcode($label->ref, 'C128');
		$bc->getBarcodeSVG(4, 120, 'black');
		?></p>
	<p style="padding: 15px 0;"><?= $label->ref; ?></p>
</div>

<div style="border: 2px #000 solid;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="60%" valign="top" class="di">
				<div style="height:380px;overflow:hidden">
					<p style="font-size:1.4em;padding: 5px;"><?=ucwords(strtolower($label->cnee->name)).(empty($label->cnee->company) || $label->cnee->name == $label->cnee->company? '' : ', '.substr(ucwords(strtolower($label->cnee->company)), 0, 33)); ?><br/>
						<?= nl2br($label->cnee->apAddress());?><br>
						<?= $label->cnee->tel; ?></p>
				</div>
			 
				 <div style="height: 150px;">
					<?php if ($label->created>="2017-10-25"): ?>
					<p style="font-size:1.3em; font-weight: bold;">Special Instructions: <br/></p>
					<p style="font-size:1.3em;"><?=empty($endTrans->mdata['fw_satchelSize'])? 'Signature On Delivery Required' : '';?></p>
					<?php endif;?>
				 </div>
				<div style="height: 100px; position: relative"> <div style="position: relative; bottom: 60px;">
							<p style="padding:5px; ">
								<?php
								$scan_number=$label->hbn;
								$b=2.3;
								if ($label->agent_id==1002) {
									$scan_number=$label->cref;
									$b=2;
								}
//                                if($label->agent_id!=1002){
//                                $bc = new TCPDFBarcode( $scan_number, 'C128');
//                                $bc->getBarcodeSVG($b, 80, 'black');
//                                }
								?>
							 <?php if ($label->agent_id==1002):?>
							<p style="margin-left:80px;">Internal use only</p>
							 <span style="position: relative; left: 100px;">
							 <img src="data:image/svg+xml;base64,<?php
							   $dm = new TCPDF2DBarcode(chr(232).$scan_number, 'DATAMATRIX');
							  echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(8, 8, 'black')));
							  ?>" width="120" style="position:absolute;"/>
							 </span>
							 <?php endif;?>
							  </p>
			  </div>
</div>

				<!--
				<p>&nbsp; Delivered by:</p><br/>
				<img src="<?= Yii::app()->request->hostInfo . Yii::app()->baseUrl; ?>/images/PCAE_Logo_large.png"
					 alt="PCA Express Logo" width="300" align="left"/> -->
			</td>
			<td style="border-left: 2px #000 solid; padding-left: 5px;max-width: 290px" valign="top">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="wci">
						<tr height="120px;">
							<td style="text-align: center;padding-top: 20px;"><img src="data:image/svg+xml;base64,<?php
								$addr = str_split($label->cnee->address, 20);
								$dm_data = [substr(empty($label->cnee->company)? $label->cnee->name : $label->cnee->company, 0, 35), '', $addr[0], empty($addr[1])? '' : $addr[1], $label->cnee->suburb, '', $label->cnee->postcode, $label->cnee->tel, '', substr($label->cnee->name, 0, 25)];
								$dm = new TCPDF2DBarcode(implode('|', $dm_data), 'DATAMATRIX');
								echo base64_encode($dm->getBarcodeSVGcode(4, 4, 'black'));
								?>" width="120" /></td>
						</tr>
						<tr>
							<td><div style="min-height:200px; max-height:240px;overflow: hidden;">Reference: <br/><span style="font-size:1.5em"><?php echo $label->hbn; ?></span> <br/><?php if (!empty($label->cref)) {
									echo $label->cref.'<br />';
								} ?>
								<?php if (!empty($label->mdata['cust_ref1'])) {
									echo '<br/>'.$label->mdata['cust_ref1'];
								}?>
							 
							<?php
						   if (!empty($label->mdata['show_sku'])) {
							if (!empty($label->eitems['sku'][0])) {
								echo '<br/>'.substr($label->eitems['sku'][0], 0, 20);
							}
							if (!empty($label->eitems['sku'][1])) {
								echo '<br/>'.substr($label->eitems['sku'][1], 0, 20);
							}
							if (!empty($label->eitems['sku'][2])) {
								echo '<br/>'.substr($label->eitems['sku'][2], 0, 20);
							}
						   }?>
							<?php
						   // show RTS tranship original parcel No.
						  if (isset($label->mdata['rts_org_no'])) {
							echo $label->mdata['rts_org_no'] . '(RTS Original)<br/>';
						  }
							?>
							</div>
							</td>
						</tr>
						<tr>
							<td><div style="min-height:90px; max-height:110px">
								<?php
							   // echo $label->cnor->name, '<br />', $label->cnor->fullAddress(array('suburb', 'state', 'postcode')), '<br />', $label->cnor->tel;
								$name=$label->cnor->name;
								$org=$label->agent;
								if (!empty($org->extra['delivery_label_name'])) {
									$name=$org->extra['delivery_label_name'];
								}
								$warehouseAddress = '6C The Crescent, 
								Kingsgrove NSW 2208<br/>Buyer is Not to return in person';
								/*if (in_array($label->agent_id, [1427, 1656])) { //d2z
									$warehouseAddress="18 Grimes Court <br />DERRIMUT VIC 3026";
								}*/
								echo $name, '<br />', $warehouseAddress;

								?>
							</div>
							</td>
						</tr>
				</table>
			</td>
	</tr>
	</table>
<?php
$sort = FastwayAPI::rf2sort($endTrans->mdata['fw_toRf'], $endTrans->mdata['fw_origin']);
$fw_sort_map = ['METRO' => 1, 'NSW' => 2, 'VIC' => 3, 'QLD' => 4, 'SA/WA' =>5, 'TAS' => 6];
$sort .= isset($fw_sort_map[$sort])? ' &nbsp; &nbsp; '.$fw_sort_map[$sort] : '';
?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" height="50px;">
		<tr style="background-color: #000000;color:white;font-size:20px;">
			<td width="20%" valign="middle"><?=empty($endTrans->mdata['fw_satchelSize'])? $label->weight.'Kg' : 'LBX';?></td>
			<td align="center" valign="middle"><?=$label->created; ?></td>
			<td align="center" valign="middle"><?=empty($endTrans->mdata['fw_toRf'])? $pkg_sn . ' /  ' . $label->pkg : '<b style="font-size:1.3em">'.$sort.'</b>'; ?></td>
		</tr>
	</table>

</div>
 <?php if (!empty($label->mdata['cust_ref2'])):?>
<p style="font-size:1.15em;padding-top:0.8em; font-weight: bold;font-family: Helvetica, Verdana, Geneva, sans-serif;text-align: center;"><?= strtoupper(substr($label->mdata['cust_ref2'], 0, 50))?></p>
 <?php endif; ?>
<!--
<div style="padding-top:5px; font-weight:bold;">http://www.pcaexpress.com.au &nbsp; &nbsp; &nbsp; Tel: 1800 518 000</div>
-->