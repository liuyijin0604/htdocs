<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="70%" valign="top" class="dto">
			<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/PCAE_Logo_large.png" alt="PCA Express Logo" width="400" align="left" />
		</td>
		<td valign="middle">
	<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/pcae_qr_bw.png" width="120" align="right" style="margin: -10px 5px" />
</td>
	</tr>
</table>
<div style="border: 2px #000 solid;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" valign="top" class="dto" style="text-align: center;">
			<h4>MANIFEST LABEL ID</h4>
		</td>
	</tr>
</table>
    <div style="margin-top: 20px; border-top: 2px #000 solid; border-bottom: 2px #000 solid;height: 400px;">
        <p class="barcode" style="padding: 15px 0;">*<?= 'PCAM-'.$label->id;?>*</p>
        <div style="height: 200px;margin-top: 80px;text-align: center;">
           <div style="display: inline-flex;">
               <div style="margin-left:160px;width: 160px;height: 40px;border-bottom: 1px solid black; float:left;"></div>
               <span style=" float:left;margin-bottom: -20px;"><b>OF</b></span>
               <div style="width: 160px;height: 40px;border-bottom: 1px solid black; float:left;"></div>
           </div>
        </div>

    </div>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:2px #000 solid;">
	<tr>
		<td width="47%" valign="top" class="di" style="min-height: 50px;">
			<p>备注/Notes:</p>
		<div style="height: 65px; overflow:hidden;">
		</div>
		</td>
		<td style="border-left: 2px #000 solid; padding: 0;" valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="wci">
			<tr>
				<td>日期/Date</td>
				<td><?=$label->created;?></td>
			</tr>

			<tr>
				<td>件数/Item</td>
				<td><?= $label->totPacks();?></td>
			</tr>
		</table></td>
	</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="47%" valign="top"><b>寄件人/Sender:</b>
			<p>
                <?php
                if ( !empty($label->owner->contacts) ) {
                    $contact = $label->owner->contacts[0];
                    echo $contact->name, '<br />', $contact->state . $contact->city . $contact->suburb . $contact->address, '<br />',$contact->phone;
                }
                ?>
			</p></td>
		<td align="center" valign="top" style="border-left: 2px #000 solid;"><p><small><strong>Aviation Security and Dangerous Goods Declaration</strong><br> 
			The sender acknowledges that this article may be carried by air and will be subject to aviation security and clearing procedures, and the sender declares that the article does not contain any dangerous or prohibited goods, explosive or incendiary devices. A false declaration is a criminal offence</small></p></td>
	</tr>
</table>
</div>
<div style="padding-top:5px; font-weight:bold;">http://www.pcaexpress.com.au &nbsp; &nbsp; &nbsp; Tel: 1800 518 000</div>
