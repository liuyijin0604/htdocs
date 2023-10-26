<p class="logo"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/PCAE_Logo_large.png" alt="PCA Express Logo" width="600" height="130" align="center" /></p>
<div style="border: 2px #000 solid;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="65%" valign="top" class="dto">
      <h4>Delivery To:</h4>
	  <?=$label->cnee->labelAddress();?>
    </td>
    <td style="border: 2px #000 solid;border-left: 4px #000 solid;" valign="middle">
  <h1 class="dest"><?=$label->cnee->getDest();?></h1>
</td>
  </tr>
</table>
<div style="border-top: 2px #000 solid; border-bottom: 2px #000 solid;">
<h2 class="connote"><?=$label->hbn;?></h2>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="65%" valign="top" class="di">
      <b>Delivery Instruction:</b>
      <p>
        <?=nl2br($label->note);?>
      </p>
    </td>
    <td style="border-left: 2px #000 solid; padding: 0;" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="wci">
	<?php if($label->weight > 0): ?>
      <tr>
        <td width="40%">Weight</td>
        <td><?=$label->weight;?> kg</td>
      </tr>
	<?php endif;
	if($label->cbm > 0): ?>
      <tr>
        <td>Cubic</td>
        <td><?=$label->cbm;?> m<sup>3</sup></td>
      </tr>
	<?php endif; ?>
      <tr>
        <td>Item</td>
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
    <td class="sender" width="40%" valign="top"><b>Sender:</b>
        <?php  $name=$label->cnor->name;
               $org=$label->agent;
               if(!empty($org->extra['delivery_label_name'])){
                 $name=$org->extra['delivery_label_name'];
                 }?>
      <p><?=ucwords(strtolower($name));?><br />
        <?=$label->cnor->fullAddress(array('suburb', 'state', 'postcode'));?>
      </p></td>
    <td align="center" valign="top" style="border-left: 2px #000 solid;"><p><small><strong>Aviation Security and Dangerous Goods Declaration</strong><br> 
      The sender acknowledges that this article may be carried by air and will be subject to aviation security and clearing procedures, and the sender declares that the article does not contain any dangerous or prohibited goods, explosive or incendiary devices. A false declaration is a criminal offence</small></p></td>
  </tr>
</table>
</div>
