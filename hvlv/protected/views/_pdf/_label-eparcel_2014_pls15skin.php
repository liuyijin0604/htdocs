<?php
//opcache_invalidate(__FILE__);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$aid = $label->ref.sprintf('%02s', $pkg_sn).'50'.'2';
$aid .= AusPostAPI::aidChkDgt($aid).'0'.sprintf('%04s', $label->cnee->postcode);
?>
<div style="font-family: Helvetica, Verdana, Geneva, sans-serif; font-size: 20px; position: relative;">
	<div style="padding: 5px 0 0 0;"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/parcelpost.svg" width="110%"/></div>

	<table width="71%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="top" width="90%;">
				<b style="font-size:1.4em;">To:</b>
				<div style="border: 4px solid #E5DBCC;margin-bottom:15px;">
					<div style="height:210px; overflow:hidden">
						<p style="font-size:1.4em;padding: 5px;"><?=ucwords(strtolower($label->cnee->name));?><br />
							<?=nl2br($label->cnee->apAddress());?></p>
					</div>
					<hr style="border-top: 4px solid #E5DBCC;" />
					<p style="font-size:1.4em;padding: 5px;">Phone: <?=$label->cnee->tel;?></p>
					<hr style="border-top: 4px solid #E5DBCC;" />
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" width="30%" style="border-right: 4px solid #E5DBCC;">
								Dead weight<br />
								<p align="center"><b style="font-size:1.5em;line-height:45px"><?=round($label->weight/$label->pkg, 2);?>kg</b></p>
							</td>
							<td valign="top">Delivery features<br />
								<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/sign.svg" width="50" style="margin-top:5px;" />
								<div style="position:absolute; font-size:0.9em; margin-left:58px; margin-top:-42px;"><?=empty($label->mdata['receipted'])? 'Sign on' : 'Receipted';?><br />delivery</div>
							</td>
						</tr>
					</table>
				</div>
				<p style="padding-top:11px"><b style="font-size:1.2em;">From:</b></p>
				<div style="border: 4px solid #E5DBCC;margin-bottom:15px;height: 535px;">
					<div style="height:125px; overflow:hidden;">
						<p style="font-size:1.2em;padding: 5px;"><?php
							$oc = OrgContact::model()->find('org_id = :id AND status = 1 AND func & 8 > 1', [':id' => $label->agent_id]);
							if (empty($oc) || $default_rts):
								?>
								<?=$default_rts? Org::COMPANY_NAME_SL : $label->cnor->name;?><br />
						        <?=Org::IM_COMPANY_ADDRESS_TLA?><br />
						        <?=Org::IM_COMPANY_CITY_TLA?>
						<div style="font-size: 1.0em">Buyer is Not to return in person</div>
							<?php else:
								echo $oc->name, '<br />',
								$oc->address, '<br />',
								strtoupper($oc->suburb. ' '. $oc->state. ' '. $oc->postcode);
							endif; ?></p>
					</div>
					<hr style="border-top: 4px solid #E5DBCC;" />
					<div style="padding:5px;">
								<div style="width: 310px; border: 2px solid black;padding:2px; margin-left: 5px;">
			<p align="center" style="font-size:28px;font-weight: bold">Road Transport Only</p>
			<p align="center">Not to be moved by air</p>
			</div>
					</div>
					<hr style="border-top: 4px solid #E5DBCC;" />

					<hr style="border-top: 4px solid #E5DBCC;" />
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" width="63%" style="border-right: 4px solid #E5DBCC;">CON NO: <?=empty($label->mdata['receipted'])? $label->ref : $aid;?></td>
							<td valign="top">PARCEL: <?=$pkg_sn.'/'.$label->pkg;?></td>
						</tr>
					</table>
					<hr style="border-top: 4px solid #E5DBCC;" />
					<div style="height:135px;overflow:hidden;"><p style="padding:5px;">
							<?=$label->hbn;?><br />
							<?php
							// show RTS tranship original parcel No.
							if (isset($label->mdata['rts_org_no'])) {
								echo $label->mdata['rts_org_no'] . '(RTS Original)<br/>';
							}
							?>

							Ref: <?=$label->cref;?><?php if (!empty($label->mdata['cust_ref1'])) {
								echo '<br/>Ref1: '.$label->mdata['cust_ref1'];
							}?>
							<?php if (!empty($label->mdata['show_sku'])) {
								if (!empty($label->eitems['sku'][0])) {
									echo '<br/>sku1: '.$label->eitems['sku'][0];
								}
								if (!empty($label->eitems['sku'][1])) {
									echo '<br/>sku2: '.$label->eitems['sku'][1];
								}
							}?>
								<?php if (in_array($label->agent_id, [650])) {
								$bc = new TCPDFBarcode($label->cref, 'C128');
								echo '<p style="padding:5px 0">';
								$bc->getBarcodeSVG(2, 40, 'black');
								echo '</p>';
							}?>
						</p>
						<div style="position: absolute; bottom: 40px;">
							<p>Internal use only</p>
							<p style="padding:5px;">
								<?php
								$bc = new TCPDFBarcode($label->ref.($label->pkg > 1? '-'.$pkg_sn : ''), 'C128');
								$bc->getBarcodeSVG(3, 50, 'black');
								?></p>
						</div>
					</div>
				</div>
			</td>

		</tr>
	</table>



	<div style="-webkit-transform: rotate(90deg); text-align:center;position:absolute;left: 235px; top:430px; width:920px; height: 280px; overflow:hidden;">
		<?php
		$bc = new TCPDFBarcode('99700160'.$aid, 'C128');
		echo '<div class="bc_center">', $bc->getBarcodeHTML(3.5, 250, 'black'), '</div>';
		?>
		<!--img src="data:image/svg+xml;base64,<?php
		//echo base64_encode(str_replace(chr(241), '', $bc->getBarcodeSVGcode(2, 105, 'black')));
		?>" height="120" /-->
		<p align="center" style="padding-top:6px; font-size:1.1em">AP Article Id: <?=$aid;?></p></div>
</div>
