<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);

$dept = $label->getStartrackDept();
$route = StarTrackAPI::API_PRODUCT_ID . $label->cnee->postcode.$dept;
$pIndex = sprintf('%04d',$pkg_sn);
$articleId = $label->ref . StarTrackAPI::API_PRODUCT_ID . '0' . $pIndex;

// TYPE A = chr(103)
// TYPE B = chr(104)
// TYPE C = chr(105)

$articleBarcode = chr(104) . substr($label->ref,0,4) .chr(105) .substr($label->ref,4,8) . chr(104) . StarTrackAPI::API_PRODUCT_ID . '0' . chr(105).$pIndex;

?>

<div>
	<div style="border:solid 1px #000000;width: 100%;">
		<div style="display: -webkit-inline-box; height: 90px;">
			<div style="width:180px;border-right: solid 1px black;"><span
					style="font-family: Times New Roman; font-size:42px;text-align: center;margin-left: 50px;"><b><?php echo StarTrackAPI::API_PRODUCT_ID; ?></b></span>
				<br/><img style="margin-left:10px;"
					src="data:image/png;base64,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"/>
			</div>
			<div style ="width:400px;border-right: solid 1px black; padding: 10px 15px 2px 2px;">CONNOTE:<br/> <span style="font-size: 40px;font-weight: bold;"><?php echo $label->ref;?></span></div>

		</div>


		<div style="font-family: Times New Roman;font-size:28px;border-top: solid 1px black;border-bottom: solid 1px black;padding-left: 2px;padding-top: 10px;padding-bottom: 10px;min-height: 120px;">
				TO: &nbsp;<?php echo strtoupper($label->cnee->name).(empty($label->cnee->company) || $label->cnee->name == $label->cnee->company? '' : ', '.substr(ucwords(strtolower($label->cnee->company)), 0, 33)); ?> <br/>
				<span style="margin-left: 46px;"><?php echo strtoupper($label->cnee->address); ?>, <?php echo strtoupper($label->cnee->suburb . ' ' . $label->cnee->state . ' ' . $label->cnee->postcode); ?> </span>
		</div>

		<div style="display: -webkit-inline-box;">
			<div style="font-family: Times New Roman;font-size:24px;padding-left: 2px;width: 160px;border-right: solid 1px black;">PH: <br> <?php echo preg_replace('/[\(\)\s]+/', '', $label->cnee->tel); ?></div>
			<?php
			$sl = strlen($label->cnee->suburb);
			$size = 40;
			if($sl > 26){
				$size = 28;
			}elseif($sl > 18){
				$size = 36;
			}
			?>
			<div style="width: 537px;padding: 5px; font-family: Times New Roman;font-weight: bold; font-size:<?=$size;?>px;"><?php echo strtoupper($label->cnee->suburb) ; ?><div style="float:right; margin-right:15px;"><?php echo $label->cnee->postcode ; ?></div>
			</div>
		</div>

	</div>

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="30%"><span style="font-family: Times New Roman; font-size:45px;font-weight: bold;line-height: 30px;">AU</span></td>
			<td width="30%" align="right"><span style="font-family: Times New Roman; font-size:45px;font-weight: bold;line-height: 30px;"><?php echo $dept;?></span></td>
			<td rowspan="2" align="right" valign="top" height="260" style="padding-top:30px;"><div style="width:175px">
				<?php
				$dm = new TCPDF2DBarcode($label->getStartrackQRData($pkg_sn), 'QRCODE');
				echo $dm->getBarcodeSVG(3.5, 3.5, 'black');
				?></div></td>
		</tr>
		<tr>
			<td width="60%" colspan="2" valign="top">
				<p>
					<?php
					$bc = new TCPDFBarcode($route, 'C128');
					$bc->getBarcodeSVG(3, 150, 'black');
					?></p>
				<p style="margin-left: 120px; font-size:25px"><b><?php echo $route ?></b></p>
			<td>
			</tr>
		</table>
	</div>

	<div style="border: solid 1px #000000; font-family: Arial; margin-top: -30px; ">
		<div>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td>
							<?php
								$name=$label->cnor->name;
								$org=$label->agent;
								if(!empty($org->extra['delivery_label_name'])){
									$name=$org->extra['delivery_label_name'];
								}?>
							<span> FROM : <?php echo empty($name) ? 'PCA Express' : strtoupper($name); ?></span> <br/>
							<span style="margin-left: 60px;"> 6C The Crescent </span><br/>
							<span style="margin-left: 60px;"> Kingsgrove NSW 2208</span>
						</td>
						<td style="text-align: right;" valign="top">PH : &nbsp;<?php echo empty($label->cnor->tel) ? '04-16843474' : $label->cnor->tel; ?><br>    </td>
					</tr>
				</table>
		</div>
<div style="border-top:solid 1px #000000;height: 1px; width: 100%;"></div>
		<div>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="65%" style="padding-top:2px;padding-bottom:1px">
							REF:<SPAN style= "font-size:2em"> <?php echo $label->hbn; ?></SPAN><br/>
							   <?php
							// show RTS tranship original parcel No.
							if ( isset( $label->mdata['rts_org_no'] ) ) {
								echo $label->mdata['rts_org_no'] . '(RTS Original)<br/>';
							}
							?>
							CREF: <?php echo $label->cref; ?>
							<?php if(!empty($label->mdata['cust_ref1'])) echo '<br/>CREF1: '.$label->mdata['cust_ref1'];?>
							<?php if(!empty($label->mdata['amazon_po'])) echo '<br/>Amazon PO#:'.$label->mdata['amazon_po'];?>
						</td>
						<td width="35%" >
							NOT BEFORE: <br/>
							NOT AFTER:<?php if(!empty($label->mdata['amazon_po'])) echo '2.30 PM';?>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top:1px;padding-bottom:1px">
							<span style="padding-right: 4px; margin-top: -10px;">DATE:&nbsp;<?php echo !empty($label->trans) ? date('d/m/Y',strtotime($label->trans[0]->time)) : date('d/m/Y'); ?> UNIT:&nbsp;ITM</span>
							<span style="padding-right: 4px;"> ITEM <span style="font-size:22px;font-weight: bold;"><?php echo $pkg_sn; ?> </span>OF <span style="font-size: 30px;font-weight: bold;"><?php echo $label->pkg; ?> </span></span>
							<span style="padding-right:4px;">WEIGHT <?php echo round($label->weight);?>KG</span>
							<span>CUBE:&nbsp;<?php echo round($label->cbm,3); ?></span>
						</td>
					</tr>
				</table>
		</div>

	</div>


	<div style="bottom: 10px;  margin-top: 4px;text-align: center;width: 100%;">
		<p style="padding:5px;">
			<?php
			$bc = new TCPDFBarcode($articleBarcode, 'C128M');
			$bc->getBarcodeSVG(3, 175, 'black');
			?></p>

		<p style="padding: 8px 0; font-size: 25px">Article ID: <?= $articleId; ?></p>
	</div>

</div>