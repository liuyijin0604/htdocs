<div style="border: 2px #000 solid;-webkit-transform: rotate(90deg); text-align:center;position:relative;left:-150px; top:180px; width:980px; height:680px; ">
<table width="100%" height="300px" border="0" >
		<tr style="position: relative">
		<td width="100%" style="padding-left: 20px;position:absolute;top:10px;"align="left">
				<div style="font-size: 34px" ><b>Delivery To:</b></div>
			<div style="font-size:38px;line-height: 40px;max-width:75%;">
		<?=$label->cnee->name;?><br>
					<?=$label->cnee->address;?><br>
					<?=$label->cnee->suburb.','.$label->cnee->state.','.$label->cnee->postcode;?><br>
					<?='Phone:'.$label->cnee->tel;?>
			</div>
				<div style="border:2px #000 solid;height:150px;width: 200px;position: absolute; right:50px; top:20px;">
						<div align="center" style="font-size: 25px"><br>POSTAGE<br>
						PAID
						AUSTRALIA
					 </div>
				</div>
		</td>   
	</tr>
</table>
<div style="border-top: 2px #000 solid;height: 150px; border-bottom: 2px #000 solid; font-size: 34px;padding-left: 10px"  align="left"  >
		<p style=" margin-top: 10px;font-size: 28px; text-align: left;line-height:30px;"><?=$label->hbn;?> &nbsp; &nbsp; Customer ref:<?=$label->cref?>
			<?php if (!empty($label->mdata['show_sku'])) {
	if (!empty($label->eitems['sku'][0])) {
		echo '<br/>sku1: '.$label->eitems['sku'][0];
	}
	if (!empty($label->eitems['sku'][1])) {
		echo '<br/>sku2: '.$label->eitems['sku'][1];
	}
	if (!empty($label->eitems['sku'][2])) {
		echo '<br/>sku3: '.$label->eitems['sku'][2];
	}
}?>
				</p>
			
		</div>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr >
		<td  style="margin-top: -110px;text-align: left; position:relative;padding-left: 10px" width="50%">
				
				<p style="position:absolute; top:15px; left: 15px; font-size:25px">Letter Type:<?=empty($label->mdata['letter_aupost'])?'letter': ImParcel::$letter_types[$label->mdata['letter_aupost']]?></p>

					<div style="font-size: 24px">Sender:</div>
		<?php
			$oc = OrgContact::model()->find('org_id = :id AND status = 1 AND func & 8 > 1', [':id' => $label->agent_id]);
			if (empty($oc) || $default_rts):
					$name = $default_rts? 'PCA Express' : $label->cnor->name;
					$org=$label->agent;
					if (!empty($org->extra['delivery_label_name'])) {
					 	$name=$org->extra['delivery_label_name'];
					}
					?>
					<?=$name?><br />
					6C The Crescent<br />
					Kingsgrove NSW 2208
			<?php else:
					echo $oc->name, '<br />',
					$oc->address, '<br />',
					strtoupper($oc->suburb. ' '. $oc->state. ' '. $oc->postcode);
			endif; ?>



			</td>
			<td align="center" style="border-left: 2px #000 solid;height:279.5px;position: relative;">
					<div style="position:absolute; top:30px;right:30px">
						 <p style="padding:5px;">
				<?php
				$bc = new TCPDFBarcode($label->ref, 'C128');
				echo '<img src="data:image/png;base64,'.base64_encode($bc->getBarcodePngData(3, 100)).'" height="100"  />';
				?></p>
		<p ><?= $label->ref; ?></p>
					</div>
			</td>
	</tr>
</table>
</div>

