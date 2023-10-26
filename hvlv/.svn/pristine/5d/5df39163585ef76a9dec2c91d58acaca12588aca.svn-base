<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
  <td width="50%" valign="top"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/PCAE_Logo_large.png" alt="PCA Express Logo" width="600" height="130" align="center" /></td>
  <td valign="top"><p class="barcode">*<?=$label->hbn;?>*</p></td>
</tr>
</table>

<div style="border: 2px #000 solid;">
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom: 1px #000 solid;">
  <tr>
    <td valign="top" width="50%" height="150">
      <b>Sender:</b>
      <p><?=ucwords(strtolower($label->cnor->name));?><br />
        <?=$label->cnor->fullAddress(array('suburb', 'state', 'postcode'));?>
      </p>
    </td>
    <td valign="top" style="border-left: 2px solid #000;">
      <b>Receiver:</b>
    <p class="dto"><?=$label->cnee->labelAddress();?></p>
</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom: 1px #000 solid;">
  <tr>
    <td valign="top" class="dto" width="50%" height="150">
<b>Description of Goods</b>
<p>
    <?php
      if(!empty($label->eitems['g'])){
        $gs = [];
        foreach($label->eitems['g'] as $i => $g){
          $gs[] = $g.' &times; '.@$label->eitems['q'][$i];
        }
        echo implode(", ", $gs);
      }
    ?>
</p>
</td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom: 1px #000 solid;">
  <tr>
    <td width="65%" valign="top" class="di" height="100">
      <b>Delivery Instruction:</b>
      <p>
        <?=nl2br($label->note);?>
      </p>
    </td>
    <td style="border-left: 1px #000 solid; padding: 0;" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="wci">
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
        <td><b><?=$label->pkg;?></b></td>
      </tr>
    </table></td>
  </tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
  <td width="50%" valign="top" style="border-bottom: 1px #000 solid;" height="120">Delivered By:(Signature) </td>
    <td valign="top" style="border-left: 1px #000 solid;border-bottom: 1px #000 solid;">Received by: (signature)</td>
  </tr>
  <tr>
  <td width="50%" valign="bottom" style="border-bottom: 1px #000 solid;" height="60">Print Name: ___________________________</td>
    <td valign="bottom" style="border-left: 1px #000 solid;border-bottom: 1px #000 solid; padding-top:15px;">Print Name: ___________________________</td>
  </tr>
  <tr>
    <td width="50%" valign="bottom" height="40"></td>
    <td valign="bottom" style="border-left: 1px #000 solid;">Date: _____/_____/________</td>
  </tr>
</table>
</div>
