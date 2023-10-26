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
$articleId= $barcodes[$pkg_sn-1];
$account = $orgRate->mdata['account_code'];
if(!empty($label->mdata['ubi_toll']))
{
	$packages = $label->mdata['ubi_toll'];
}else
{
	$packages = $label->packs;
}
$service = $orgRate->mdata['allied_service'];
$serviceLevel = $orgRate->mdata['allied_service_level'];

$javaAPI = new HvlvJavaAPI(HvlvJavaAPI::RATE_TYPE[$orgRate->mdata['ddpt_id']]);
$qrcodeStr="{$articleId}~{$label->ref}~{$label->cnee->postcode}~".strtoupper($label->cnee->suburb)."~{$label->cnee->state}~{$account}~{$label->pkg}~{$label->weight}~{$serviceLevel}~~~~~~{$label->cnee->address}";
?>
<?php
if($service=="LOCAL COURIER"):
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td width="100%" style="background-color: #000;">
		</td>
	</tr>
</table>
<?php endif?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td width="25%">
			<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/allied.png" width="180" />
		</td>
		<td width="40%">
			<p>
				Service: <?=$service?>
			</p>
		</td>
		<td width="35%">
			<p>
				Date:<?=date("Y-m-d")?>
			</p>
			<p style="margin-top: 1em;font-size: 1em;">
				Con Note:<?=$label->ref?>
			</p>
		</td>
	</tr>
</table>
<div style="display: inline-block;border: 3px solid #000;width:100%;font-size: 0.8em;font-family: Arial;font-weight: 400;margin-top: 1em;">
<div style="display: inline-block;border-bottom: 3px solid #000;width:100%;font-size: 0.8em;font-family: Arial;font-weight: 400;height: 20em;">
	<div style="border-right: 3px solid #000;width:50%;display: inline-block;height: 100%;vertical-align:top;">

	<div style="display: inline-block;font-size: 1.5em;width: 100%;line-height:1.5em;">
		<?php
				$name=!empty($default_rts)? Org::COMPANY_NAME_SL: $label->cnor->name;
		        $org=$label->agent;
		        if (!empty($org->extra['delivery_label_name'])) {
		            $name=$org->extra['delivery_label_name'];
		        }
			?>
			<?php if(empty($label->withoutSender) && $label->agent_id != 4372):?>
			<p style="margin-bottom: 0.8em;">From:&nbsp;&nbsp; <?=$name?></p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$javaAPI->getFromInfo()['address']?></p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$javaAPI->getFromInfo()['suburb']?></p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$javaAPI->getFromInfo()['state']?> <?=$javaAPI->getFromInfo()['postcode']?></p>
			<p>Contact: <?=$name?></p>
			<p>Phone: <?=Org::IM_COMPANY_PHONE?></p>
			<?php endif;?>
			<?php if(empty($label->withoutSender) && $label->agent_id == 4372): ?>
			<p style="margin-bottom: 0.8em;">From:&nbsp;&nbsp; Ninja Logistics</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3/47-53 Moxon Road</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Punchbowl</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NSW 2196</p>
			<p>Contact: Ninja Logistics</p>
			<p>Phone: <?=Org::IM_COMPANY_PHONE?></p>
			<?php endif;?>
			<p style="font-size: 0.8em;margin-top:0.5em;">Order Ref: <font style="font-size: 1.4em;font-weight:600"><?=$label->hbn."-".$pkg_sn?></font></p>
			<p style="font-size: 0.8em;">SKU:</p>
	</div>


	</div>
	<div style="width:49%;display: inline-block;height: 100%;vertical-align:top;">

		<div style="display: inline-block;font-size: 1.5em;width:100%;line-height:1.5em;">
			<p>To:&nbsp; <?=$label->cnee->name?></p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$label->cnee->company?></p>
			<div style="min-height: 3em;">
			<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$label->cnee->address?></p>
			</div>
			<p  style="font-weight: 600;font-size: 1.2em;width: 100%;">&nbsp;&nbsp;&nbsp;<?=$label->cnee->suburb?></p>
			<table style="font-weight: 600;font-size: 1.5em;width: 100%;">
					<tr>
						<td style="width: 33%;">
							<center>
							<?=$depotCode?>
							</center>
						</td>
						<td style="width: 33%;">
							<center>
							<?=$label->cnee->state?>
							</center>
						</td>
						<td style="width: 33%;">
							<center>
							<?=$label->cnee->postcode?>
							</center>
						</td>
					</tr>
			</table>

			<p>Contact: <?=$label->cnee->name?></p>
			<p>Phone: <?=$label->cnee->tel?></p>
		</div>


	</div>
</div>
<div style="display: inline-block;border-bottom: 3px solid #000;width:100%;font-size: 0.8em;font-family: Arial;font-weight: 400;height: 50em;">
	<div style="border-right: 3px solid #000;width:65%;display: inline-block;height: 35em;vertical-align:top;">

		<div style="display: inline-block;font-size: 1.5em;width: 100%;">
			<div style="display: inline-block;font-size: 1.5em;height:9em;margin-top: 1em;">
			<center>
				<img src="data:image/svg+xml;base64,<?php
    	    $dm = new TCPDF2DBarcode($qrcodeStr, 'QRCODE, H');
    	    echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(8, 8, 'black')));
    	    ?>" width="250" style="position:absolute;margin-left:1%;"/>
	        </center>
	        </div>
	        	<p style="font-size: 0.8em;">Dangerous Goods Enclosed: No</p>

	        	<p style="font-size: 1.2em;margin-top:5em;">Cref: <?=$label->cref?></p>

	    </div>


    </div>
    
    <div style="width:34%;display: inline-block;height: 35em;vertical-align:top;">

    	<div style="display: inline-block;font-size: 1.5em;margin-top: 1em;">
    	    <div style="min-height: 12em;">
    	    <p>Weight:&nbsp;&nbsp; <?=$label->weight?>&nbsp;&nbsp;&nbsp;&nbsp;Package:&nbsp; <?=$pkg_sn?> of <?=$label->pkg?></p>
    	    <p><?=$packages[$pkg_sn-1]['length']."cm x ".$packages[$pkg_sn-1]['width']."cm x ".$packages[$pkg_sn-1]['height']."cm Acrylic Decorations"?></p>

    	    </div>
    	    
    	</div>


    </div>

    <div style="width:100%;display: inline-block;vertical-align:top;">
    		</br>
    		<center>
			
			<?php
			$bc = new TCPDFBarcode(chr(241).$articleId, 'C128');
			 echo '<p style="padding:5px 0"><img src="data:image/png;base64,'.base64_encode($bc->getBarcodePngData(2.5, 80)).'" height="100"  style="margin-left:8px;"/></p>';
	        ?>
	        <h1 style="font-size: 3em;"><?=$articleId?></h1>
	        </center>
	 </div>
</div>




</div>
</div>

