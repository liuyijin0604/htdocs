<style>
  p.letters {
    word-wrap: break-word;
  }
</style>
<h1 style="float: right;font-size: 2.5em;"><?php echo $label->getBlueLabel($pkg_sn,true);?></h1>
<?php
$strWarehouse = '';
$objCnee = $label->cnee;
$strAddress = strtoupper($objCnee->address);

// C/O D1B/ 350 Parramata Road
if($objCnee->postcode == 2140 && preg_match('/D1B\s*(\/|,|，|\s)\s*350\s*PARRAMATA/',$strAddress)){
  $strWarehouse = 'GC';
}
// C/O Warehouse 2, 54 Ferndell St
if($objCnee->postcode == 2142 && preg_match('/2\s*(\/|,|，|\s)\s*54\s*FERNDELL/',$strAddress)){
  $strWarehouse = 'WI';
}
// G2/391 Park Road
if($objCnee->postcode == 2143 && preg_match('/G2\s*(\/|,|，|\s)\s*391\s*PARK/',$strAddress)){
  $strWarehouse = 'PX';
}
if(!empty($strWarehouse)){
  echo '<div style="position:absolute;left:10px;"><div style="width:200px;text-align:center;background-color:black;color:white;font-size:4.5em;">'.$strWarehouse.'</div></div>';
}
?>

<div style="position:absolute;left:342px;">
<div style="height: 60px;"></div>
<div style="width:290px;text-align:center;background-color:black;color:white;font-size:3.0em;"><?=$label->getTLDRegion()?></div>
</div>

<br/>
<p class="logo"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/australia_logistic.png" alt="PCA Express Logo" width="600" height="130" align="center" /></p>
<div style="border: 2px #000 solid;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="65%" valign="top" class="dto">
      <h4>Delivery To:</h4>
	  <?=$label->cnee->labelAddress('max-width:26rem;word-wrap:break-word;');?>
    </td>
    <td style="border: 2px #000 solid;border-left: 4px #000 solid;" valign="middle">
  <h1 class="dest"><?=$label->cnee->getDest();?></h1>
</td>
  </tr>
</table>
<div style="border-top: 2px #000 solid; border-bottom: 2px #000 solid;">
<p class="connote" style="font-size:2.3em;text-align: center; padding: 10px;font-weight: bold;"><?=$label->hbn;?></p>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
  <tr>
    <td width="50%" valign="top" class="di">
      <b>Delivery Instruction:</b>
      <p class="letters">
        <?=nl2br($label->note);?>
      </p>
      <b>Customer Ref:</b>
      <p><?=$label->cref?></p>
    </td>
    <td style="border-left: 2px #000 solid; padding: 0;" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="wci">
	<?php if($label->weight > 0&&empty($isWDTPallet)): ?>
      <tr>
        <td width="30%">Package: </td>
        <td>
          <?=!empty($label->packs[$pkg_sn-1])?$label->packs[$pkg_sn-1]['weight']:round($label->weight/$label->pkg);?> kg / 
          <?=round($label->cbm,2);?> m<sup>3</sup>
        </td>
      </tr>
	<?php endif;
	if($label->cbm > 0): ?>
      <!-- <tr>
        <td>Cubic</td>
         <?php if(!empty($isWDTPallet)):?>
              <td><?=$label->getTotalCBM();?> m<sup>3</sup></td>
        <?php else:?>
              <td><?=$label->cbm;?> m<sup>3</sup></td>
         <?php endif;?>
      </tr> -->
	<?php endif; ?>
      <tr>
        <?php if(!empty($isWDTPallet)):?>
            <td>Item</td>
            <td style="font-size:1.5em"><?=$label->pkg;?></td>
          </tr>
          <tr>
            <td>Pallet</td>
            <td style="font-size:1.5em"><?=$pkg_sn;?> of <?=$label->mdata['amzon_pallet'];?></td>
          <?php else:?>
            <td>Item</td>
            <td style="font-size:1.5em"><?=$pkg_sn;?> of <?=$label->pkg;?></td>
          <?php endif;?>
      </tr>
      <tr>
        <td width="30%">Total: </td>
        <td><?=round($label->weight,2)?> kg / <?=round($label->getTotalCBM(),2);?> m<sup>3</sup></td>
      </tr>


    </table></td>
  </tr>
</table>
<div style="border-top: 2px #000 solid; border-bottom: 2px #000 solid; padding: 15px; text-align:center"><div><?php
	$bc = new TCPDFBarcode($label->ref.($label->pkg > 1 ? '-'.$pkg_sn : ''), 'C128');
	echo $bc->getBarcodeSVGcode(3, 100);
	?></div><span style="font-size:2.4em"><?=$label->ref.($label->pkg > 1 ? '-'.$pkg_sn : '');?></span>
</div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed">
  <tr>
      <td class="sender" width="45%" valign="top">
        <?php if(empty($label->withoutSender)):?>
        <b>Sender:</b>
        <?php  
               $ddepot = $label->ddepot;
               if($ddepot==null)
               {
                  $ddepot = Org::model()->findByPk(Org::PCAE_DEPARTMENT_SYDNEY);
               }

               $name=$label->cnor->name;
               $org=$label->agent;
               if(!empty($org->extra['delivery_label_name']))
               {
                 $name=$org->extra['delivery_label_name'];
               }?>
      <p><?=ucwords(strtolower($name));?><br />
      <p ><?php if($label->mdata['chargecode']!="2136") echo $ddepot->fullAddress(array('suburb', 'state', 'postcode'));?> </p>
      </p>
      <?php endif; ?>
    </td>
    <td align="center" width="60%" valign="top" style="border-left: 2px #000 solid;"><p><small>
      <?php if(!empty($label->mdata['is_dg'])):?>
         <strong style="font-size:2em;">Contain Dangerous Goods</strong>
      <?php else:?>
        <strong>Aviation Security and Dangerous Goods Declaration</strong><br>
        The sender acknowledges that this article may be carried by air and will be subject to aviation security and clearing procedures, and the sender declares that the article does not contain any dangerous or prohibited goods, explosive or incendiary devices. A false declaration is a criminal offence
      <?php endif;?>
      </small></p></td>
  </tr>
</table>
</div>

<?php

  $blueLabelObj = $label->getBlueLabelWithIndex($pkg_sn);
  if(!empty($blueLabelObj))
  {
    echo '</br>';
    echo '</br>';
    echo '</br>';
    echo '</br>';
    echo '</br>';
    echo '</br>';
    echo '</br>';
    echo '</br>';
    echo '</br>';
    echo '</br>';
    echo '</br>';
    echo '</br>';
    echo '</br>';
    echo '<div style="height:30em;margin-top:5em;text-align:center">';
    echo '<h1 style = "font-size:8em;">#'.$blueLabelObj->blue_label.' - '.'</h1>';
    echo '<h1 style = "font-size:7em;">'.(($pkg_sn-$blueLabelObj->label_lo)+1).'/'.(($blueLabelObj->label_hi-$blueLabelObj->label_lo)+1).'</h1>';
    echo '<div>';
    $pp = $allPage==1?$pp=1:$pp=(2*$allPage)-1;
     echo '<div style="position:absolute;top:'.(($pp*1050)+($pp-1)*28.065).'px;right:5px;"><div style="text-align:center;font-size:1em;">Created: '.$label->created.'</div></div>';
  }else
  {
     echo '<div style="position:absolute;top:'.(($allPage*1050)+($allPage-1)*28.065).'px;right:5px;"><div style="text-align:center;font-size:1em;">Created: '.$label->created.'</div></div>';
  }
?>