<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="70%" valign="middle" class="dto" height="122">
			<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/PCAE_Logo_large.png" alt="PCA Express Logo" width="400" align="left" />
		</td>
		<td valign="bottom">
	<img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/pcae_qr_bw.png" width="120" align="right" />
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
			echo '<p style="font-size:1.2em; padding-top:5px;">',$label->cnee->getCnFullAddress(), '<br />', $label->cnee->tel, '</p>';
		}?>
		</td>
	</tr>
	<tr>
		<td colspan="2" valign="top" class="dto" style="border-top: 2px #000 solid;">
		<div style="height: 180px; overflow:hidden;"><p>货物描述/Goods Detail:</p><p style="font-size: 1.2em">
		<?php
			if(!$empty && !empty($label->eitems['g'])){
				$gs = [];
				foreach($label->eitems['g'] as $i => $g){
					$gs[] = $g.' &times; '.@$label->eitems['q'][$i];
				}
				echo implode(", ", $gs);
			}
		?></p>
		</div>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top: 2px #000 solid;">
	<tr>
		<td width="60%" valign="top" class="di" style="min-height: 50px;">
			<p>备注/Notes:</p>
		<div style="height: 65px; overflow:hidden;">
				<?=nl2br(trim($label->note));?>
				<?=empty($label->cref)? '' : '<br />Ref: '.$label->cref;?>
		</div>
		</td>
		<td style="border-left: 2px #000 solid; padding: 0;" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="wci">
			<tr>
				<td>日期/Date</td>
				<td><?=$label->created;?></td>
			</tr>
	<?php if($label->weight > 0 || $empty): ?>
			<tr>
				<td width="50%">重量/Weight</td>
				<td><?=$empty? '&nbsp;&nbsp;&nbsp;' : $label->weight;?> kg</td>
			</tr>
	<?php endif;
	if(0 && $label->cbm > 0): ?>
			<tr>
				<td>体积/Cubic</td>
				<td><?=round($label->cbm*10000)/10000;?> m<sup>3</sup></td>
			</tr>
	<?php endif; ?>
			<tr>
				<td>件数/Item</td>
				<td><?=$pkg_sn;?> of <?=$label->pkg;?></td>
			</tr>
		</table></td>
	</tr>
</table>
<div style="border-top: 2px #000 solid; border-bottom: 2px #000 solid; padding: 15px; text-align:center"><div><?php
	$bc = new TCPDFBarcode($label->hbn.($label->pkg > 1 ? '-'.$pkg_sn : ''), 'C128');
	echo $bc->getBarcodeSVGcode(3, 100);
	?></div><span style="font-size:1.4em"><?=$label->hbn.($label->pkg > 1 ? '-'.$pkg_sn : '');?></span>
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
