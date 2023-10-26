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
		<td colspan="2" valign="top" class="dto">
			<h4>收件人/To: <span><?=($empty? '' : $label->cnee->name);?></span></h4>
		<?php
		if($empty){
			echo '地址/Address:<br /><br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="45%">电话/Tel:</td><td>身份证/ID:</td></tr></table>';
		}else {
			echo '<p style="font-size:1.1em; padding-top:5px;">',$label->cnee->getCnFullAddress(), '<br />', $label->cnee->tel, '</p>';
		}?>
		</td>
	</tr>
	<tr>
		<td colspan="2" valign="top" class="dto" style="border-top: 2px #000 solid;">
		<div style="height: 180px; overflow:hidden;"><p>货物描述/Goods Detail:</p>
		<?php
			if(!$empty && !empty($label->eitems['g'])){
				$gs = [];
				foreach($label->eitems['g'] as $i => $g){
					$gs[] = $g.' &times; '.@$label->eitems['q'][$i].' -- $'.@$label->eitems['v'][$i];
				}
				echo implode(", ", $gs);
			}
		?>
		</div>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top: 2px #000 solid;">
	<tr>
		<td width="65%" valign="top" class="di" style="min-height: 40px;">
			<p>备注/Notes:</p>
		<div style="height: 60px; overflow:hidden;">
				<?=nl2br($label->note);?>
		</div>
		</td>
		<td style="border-left: 2px #000 solid; padding: 0;" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="wci">
	<?php if($label->weight > 0 || $empty): ?>
			<tr>
				<td width="60%">重量/Weight</td>
				<td><?=$empty? '&nbsp;&nbsp;&nbsp;' : $label->weight;?> kg</td>
			</tr>
	<?php endif;
	if($label->cbm > 0): ?>
			<tr>
				<td>体积/Cubic</td>
				<td><?=$label->cbm;?> m<sup>3</sup></td>
			</tr>
	<?php endif; ?>
			<tr>
				<td>件数/Item</td>
				<td><?=$pkg_sn;?> of <?=$label->pkg;?></td>
			</tr>
		</table></td>
	</tr>
</table>
<div style="border-top: 2px #000 solid; border-bottom: 2px #000 solid;">
<p class="barcode" style="padding: 15px 0;">*<?=$label->hbn.($label->pkg > 1 ? '-'.$pkg_sn : '');?>*</p>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="47%" valign="top"><b>寄件人/Sender:</b>
			<p>
	  <?php
    if($empty){
      echo '<br /><br /><br />电话/Tel:<br />签名/Signature:<br /><br /><br />日期/Date:';
    }else {
      echo $label->cnor->name, '<br />', $label->cnor->fullAddress(array('suburb', 'state', 'postcode')), '<br />', $label->cnor->tel;
    }?>
			</p></td>
		<td align="center" valign="top" style="border-left: 2px #000 solid;"><p><small><strong>Aviation Security and Dangerous Goods Declaration</strong><br> 
			The sender acknowledges that this article may be carried by air and will be subject to aviation security and clearing procedures, and the sender declares that the article does not contain any dangerous or prohibited goods, explosive or incendiary devices. A false declaration is a criminal offence</small></p></td>
	</tr>
</table>
</div>
<div style="padding-top:5px; font-weight:bold;">http://www.pcaexpress.com.au &nbsp; &nbsp; &nbsp; Tel: 1800 518 000</div>
