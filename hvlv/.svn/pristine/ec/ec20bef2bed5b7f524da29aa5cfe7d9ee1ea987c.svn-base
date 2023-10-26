<?php
//opcache_invalidate(__FILE__);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$aid = empty($label->mdata['receipted'])? $label->ref.sprintf('%02s', $pkg_sn).'00093'.'02'.'0' : '57'.$label->ref.'13';
$aid .= AusPostAPI::aidChkDgt($aid);
?>
<div style="font-family: Helvetica, Verdana, Geneva, sans-serif; font-size: 20px; position: relative;">
	<div style="padding: 5px 0 15px 0;"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/parcelpost.svg" width="100%" /></div>

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="top">
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
				<div style="border: 4px solid #E5DBCC;margin-bottom:15px;">
					<div style="height:125px; overflow:hidden;">
						<p style="font-size:1.2em;padding: 5px;"><?php
							$oc = OrgContact::model()->find('org_id = :id AND status = 1 AND func & 8 > 1', [':id' => $label->agent_id]);
							if (empty($oc) || $default_rts):
								?>
								<?=$default_rts? 'PCA Express' : $label->cnor->name;?><br />
								6C The Crescent<br />
								Kingsgrove NSW 2208
							<?php else:
								echo $oc->name, '<br />',
								$oc->address, '<br />',
								strtoupper($oc->suburb. ' '. $oc->state. ' '. $oc->postcode);
							endif; ?></p>
					</div>
					<hr style="border-top: 4px solid #E5DBCC;" />
					<div style="padding:5px;">
						<b style="color:#D81E05;font-size:0.9em;">Aviation Security and Dangerous Goods Declaration</b><br />
						<p style="line-height:18px;font-size:0.92em;">The sender acknowledges that this article may be carried by air and
							will be subject to aviation security and clearing procedures; and the
							sender declares that the article does not contain any dangerous or
							prohibited goods, explosive or incendiary devices. A false declaration
							is a criminal offence.</p>
					</div>
					<hr style="border-top: 4px solid #E5DBCC;" />
					<p style="padding:5px;">
						<?php
						$bc = new TCPDFBarcode(empty($label->mdata['receipted'])? $label->ref : '997001'.$aid, 'C128');
						$bc->getBarcodeSVG(3, 50, 'black');
						?></p>
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
							Ref: <?=$label->cref;?><?php if (in_array($label->agent_id, [650])) {
							$bc = new TCPDFBarcode($label->cref, 'C128');
							echo '<p style="padding:5px 0">';
							$bc->getBarcodeSVG(2, 40, 'black');
							echo '</p>';
						}?>
						</p></div>
				</div>
			</td>
			<td width="130" valign="top" align="right">
				<p style="padding: 5px 0;"><b style="font-size:0.95em;">Postage Paid</b></p>
				<?php
				if (empty($label->mdata['receipted'])):
					?>
					<img src="data:image/svg+xml;base64,<?php
					$dm = new TCPDF2DBarcode(chr(232).'019931265099999891'.$aid.chr(29).'420'.$label->cnee->postcode.chr(29).'8008'.date('ymdHis'), 'DATAMATRIX');
					echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(4, 4, 'black')));
					?>" width="120" />
					<!--div style="padding-top:760px"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/poweredby_eparcel.svg" width="120" /></div-->
				<?php endif; ?>
			</td>
		</tr>
	</table>

	<div style="-webkit-transform: rotate(90deg); text-align:center;position:absolute;left: 235px; top:580px; width:760px; height: 150px; overflow:hidden;">
		<?php
		$bc = new TCPDFBarcode((empty($label->mdata['receipted'])? chr(241).'019931265099999891' : '997001').$aid, 'C128');
		echo '<div class="bc_center">', $bc->getBarcodeHTML(2.5, 120, 'black'), '</div>';
		?>
		<!--img src="data:image/svg+xml;base64,<?php
		//echo base64_encode(str_replace(chr(241), '', $bc->getBarcodeSVGcode(2, 105, 'black')));
		?>" height="120" /-->
		<p align="center" style="padding-top:6px; font-size:1.1em">AP Article Id: <?=$aid;?></p></div>
</div>
