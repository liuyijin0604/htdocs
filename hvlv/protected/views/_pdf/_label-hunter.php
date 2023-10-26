<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
?>
<style>
#outer {
	transform: rotate(90deg);
	-ms-transform: rotate(90deg); 	/* IE 9 */
	-moz-transform: rotate(90deg); 	/* Firefox */
	-webkit-transform: rotate(90deg); /* Safari 和 Chrome */
	-o-transform: rotate(90deg); 	/* Opera */

	border: 5px solid black;
	position: relative;
	padding: 0;

	right: 160px;
	height: 650px;

	top: 200px;
	width: 1000px;
}
p {
	font-size: 25px;
	width: 95%;
}
.foot p {
	font-size: 22px;
	padding-left: 10px;
	line-height: 30px;
}
</style>

<div id="outer">
	<div style="float: left; display: inline-block; width: 550px">
		<p style="font-size: 35px; padding-left: 5px">To: <?=$label->cnee->name?></p>
		<p style="padding-left: 20px; padding-top: 20px"><?=$label->cnee->company?></p>
		<p style="padding-left: 20px; padding-top: 20px"><?=$label->cnee->address?></p>
		<p style="padding-left: 20px; padding-top: 20px"><?=$label->cnee->suburb?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$label->cnee->postcode?></p>
		<p style="padding-left: 20px; padding-top: 20px"><?=$label->cnee->tel?></p>
	</div>
	<div style="float: left; display: inline-block; width: 450px">
		<p style="margin-top: 10px"><span style="border: 5px solid black; font-size: 60px; font-weight: bold">&nbsp;&nbsp;<?=HunterPortcode::getPortcode($label->cnee->suburb, $label->cnee->postcode)?>&nbsp;&nbsp;</span></p>
		<p style="line-height: 40px; padding-top: 15px">
			<u>Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
			<u>Signature&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
			<u>Date/Time&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
			Account:<br />
			<span style="line-height: 30px">Items <?=$pkg_sn?>/<?=$label->pkg?>&nbsp;&nbsp;&nbsp;&nbsp;Wgt <?=$label->weight;?>kgs</span>
		</p>
	</div>
	<div style="float: left; display: inline-block; border-bottom: 3px solid black; width: 1000px">
		<p style="font-size: 35px; padding-left: 20px">
			CONSIGNMENT <?=$label->ref?>&nbsp;&nbsp;&nbsp;&nbsp;ROAD FREIGHT
		</p>
	</div>
	<div style="float: left; display: inline-block; border-bottom: 3px solid black; width: 1000px">
		<p style="padding: 10px; text-align: center">
			<?php
			$bc = new TCPDFBarcode($label->ref . sprintf('%03d', $pkg_sn) . sprintf('%03d', $label->pkg) . $label->cnee->postcode, 'C128');
			echo $bc->getBarcodeSVGcode(5, 80);
			?>
		</p>
	</div>
	<div style="float: left; display: inline-block; width: 370px" class="foot">
		<p>
			From: <?=$label->cnor->name?><br />
			6C The Crescent,<br />
			Kingsgove NSW 2208<br />
			<?=date('d/m/Y')?><br />
			Items <?=$pkg_sn?>/<?=$label->pkg?>&nbsp;&nbsp;&nbsp;&nbsp;Wgt <?=$label->weight;?>kgs
		</p>
	</div>
	<div style="float: left; display: inline-block; width: 370px" class="foot">
		<p>
			To: <?=$label->cnee->name?><br />
			<?=$label->cnee->company?><br />
			<?=$label->cnee->address?><br />
			<?=$label->cnee->suburb?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$label->cnee->postcode?><br />
			<?=$label->cnee->tel?>
		</p>
	</div>
	<div style="float: left; display: inline-block; width: 260px" class="foot">
		<p>
			Instructions:<br />
			CARTON<br />
			Ref: <?=$label->hbn?><br />
			Senders Ref: <?=$label->cref?></p>
		</p>
	</div>
</div>