<style type="text/css">
	table
	{
		font-size: 0.9em;
		font-family: Arial;
		font-weight: 600;
	}
</style>
<?php
//opcache_invalidate(__FILE__);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
$barcodes =  $label->getParcelBarcode(null);
$articleId= $barcodes[$pkg_sn-1];
$port = SFAPI::getCode($label);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td width="60%">
			<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/sf_logo.png" width="180" />
		</td>
		<td>
			<p>
				Service tel:1300148168
			</p>
			<p style="margin-top: 1em;font-size: 0.6em;">
				Date printing:<?= Date("Y-m-d H:s:i")?>
			</p>
		</td>
	</tr>
</table>
<div style="border-top: 3px solid rgb(225,225,225);border-right: 3px solid rgb(225,225,225);border-left: 3px solid rgb(225,225,225);width:100%;display: inline-block;">
	<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="80%">
				<table width="100%">
					<tr>
						<td style="height: 6em;">
							<?php
								$bc = new TCPDFBarcode($articleId, 'C128');
								$bc->getBarcodeSVG(3.2, 110, 'black');
							?>
						</td>
					</tr>
					<tr>
						<td>
							<table width="100%">
								<tr>
									<td width="30%">
										<p>&nbsp;&nbsp;&nbsp;&nbsp;Parcel &nbsp;<?=$pkg_sn?>/<?=$label->pkg?></p>
									</td>
									<td>
										<p>子 Child AWB</p>
										<p style="font-size:1.2em;">母 Main AWB</p>
									</td>
									<td>
										<p><?=$articleId?></p>
										<p  style="font-size:1.2em;margin-top: 0.3em;"><?=$label->ref?></p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>


			</td>
			<td  width="20%">
				<p style="font-size:4em;-webkit-transform:translateY(8px);">T</p>
				<p style="font-size:8em;-webkit-transform:translateY(-36px) translateX(34px);">4</p>

			</td>
		</tr>
		
	</table>
</div>
<div style="display: inline-block;border: 3px solid #000;width:100%;font-size: 0.8em;font-family: Arial;font-weight: 600;margin-top: -2em;">
<div style="display: inline-block;border-bottom: 3px solid #000;width:100%;height:13em;">
	<p>
		<div style="font-size: 4em;display: inline-block;"><?=$port?></div>
	<div style="font-size: 4em;border: 1px solid #000;display: inline-block;line-height:0.9em;margin-left: 6.5em;"><span style="margin-left:0.1em;margin-right: 0.1em;">AU</span></div>
	</p>
	<div style="border: 2px solid #000; border-radius: 2em;width: 4em;height: 4em;display: inline-block;">
		<p style="-webkit-transform:translateY(20px) translateX(20px);">To</p>
	</div>
	<div style="display: inline-block;font-size: 1.2em;">
		<p><?=!empty($label->cnee->company)?ucwords(strtolower($label->cnee->company)):ucwords(strtolower($label->cnee->name))?></p>
		<p><?=!empty($label->cnee->company)?ucwords(strtolower($label->cnee->company)):ucwords(strtolower($label->cnee->name))?>&nbsp;<?=$label->cnee->tel?></p>
		<p><?=$label->cnee->address.' '.$label->cnee->suburb.' '.$label->cnee->state.' '.$label->cnee->postcode;?></p>
	</div>
</div>
<div style="display: inline-block;border-bottom: 3px solid #000;width:100%;font-size: 0.8em;font-family: Arial;font-weight: 600;height: 12em;">
	<div style="border-right: 3px solid #000;width:75%;display: inline-block;height: 100%;">
	</div>
</div>
<div style="display: inline-block;width:100%;font-size: 0.8em;font-family: Arial;font-weight: 600;height:9.9em;border-bottom: 3px solid #000;">
	<div style="width:50%;height:100%;border-right: 3px solid #000;display: inline-block;-webkit-transform:translateY(-54px);">
		<div style="border: 2px solid #000; border-radius: 2em;width: 4em;height: 4em;display: inline-block;-webkit-transform:translateY(-18px)">
			<p style="-webkit-transform:translateY(18px) translateX(8px);">From</p>
		</div>
		<div style="display: inline-block;font-size: 1.2em;width:18em;-webkit-transform:translateY(4px)">
			<?php
				$name=!empty($default_rts)? Org::COMPANY_NAME_SL: $label->cnor->name;
		        $org=$label->agent;
		        if (!empty($org->extra['delivery_label_name'])) {
		            $name=$org->extra['delivery_label_name'];
		        }
			?>
			<p><?=$name?></p>
			<p><?=$name?>&nbsp;</p><!--0299257111-->
			<p>1/233 Milperra Rd, Bankstown Aerodrome NSW 2200</p>
		</div>
	</div>

	<div style="width:48%;height:100%;display: inline-block;">
		<p style="width:100%;height: 80%;margin-left: 1em;margin-top: 0.5em;">
			<?php
				$bc = new TCPDFBarcode($articleId, 'C128');
				$bc->getBarcodeSVG(2, 70, 'black');
			?>
		</p>
		<p style="height: 20%;font-size: 1.4em;">
			母&nbsp;Main&nbsp;AWB&nbsp;<?=$label->ref?>
		</p>
	</div>
</div>

<div style="display: inline-block;width:100%;height:12em;border-bottom: 3px solid #000;-webkit-transform:translateY(-18px);padding-bottom: 1em;">
	<div style="width:50%;height:100%;border-right: 3px solid #000;display: inline-block;">
		<div style="display: inline-block;width:100%;border-bottom: 3px solid #000;">
			<p>Payment 付款方式:</p>
			<?php
				if($orgRate->mdata['api_type']!='syd_truck')
				{
					echo '<p>Shipper 寄件方 061***0229</p>';
				}else
				{
					echo '<p>Shipper 寄件方 061***0321</p>';
				}

			?>
		</div>
		<div style="display: inline-block;width:100%;border-bottom: 3px solid #000;">
			<p>Actual WT 实际重量:</p>
			<p>Charge WT 计费重量:</p>
		</div>
		<div style="display: inline-block;width:100%;height:4em;border-bottom: 3px solid #000;">
			<p>Feight 实际重量:</p>
			<p>VAS 增值服务:</p>
		</div>
		<div style="display: inline-block;width:100%;height:1.2em;">
			<p>Total Charge 费用合计:</p>
		</div>
	</div>

	<div style="width:48.9%;height:100%;display: inline-block;-webkit-transform:translateY(18px);">
		<div style="display: inline-block;width:100%;height:3.8em;border-bottom: 3px solid #000;">
			<p>Remark 备注:</p>
		</div>
		<div style="display: inline-block;width:100%;height:4em;border-bottom: 3px solid #000;">
			<p>Description 托寄物:</p>
			<?=$label->getGoodsItemsForLabel()?>
		</div>
		<div style="display: inline-block;width:100%;height:2em;border-bottom: 3px solid #000;">
			<p>Tax:DDU Receiver 到付</p>
		</div>
		<div style="display: inline-block;width:100%;">
			<p>Declared Value:<?=round($label->getAUDValue(),3)?>AUD</p>
		</div>
	</div>
</div>

<div style="display: inline-block;width:100%;height:2em;line-height:2em;">
	<p>
		Ref No.: <?=$label->hbn?>  
		<?php
		if($label->ot_id=10 && !empty($label->cref)){
		?>
			Task No.: <?=$label->cref?>
		<?php
		}
		?>
	</p>




</div>
</div>

