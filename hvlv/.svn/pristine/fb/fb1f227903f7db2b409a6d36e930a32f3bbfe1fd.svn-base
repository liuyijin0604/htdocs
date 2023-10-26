<?php
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
?>
<br/>
<p class="logo"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl;?>/images/australia_change.png" alt="PCA Express Logo" width="500" height="130" align="center"/></p>
<div style="width:100%;display:block;background-color:black;margin-top:2px;height:7px;border-radius:0px 0px 12px 12px;">
	&nbsp;
</div>
<div>
<h2 class="connote" style="font-size:2.8em"><?=$label->hbn;?></h2>
</div>
<div style="border: 2px #000 solid;margin-top: -20px;">

<div style="border-top: 2px #000 solid; border-bottom: 2px #000 solid; padding: 15px; text-align:center"><div><?php
  $bc = new TCPDFBarcode($label->ref.($label->pkg >= 1 ? '-'.$pkg_sn : ''), 'C128');
  echo $bc->getBarcodeSVGcode(3, 100);
  ?></div><span style="font-size:2.4em"><?=$label->ref.($label->pkg >= 1 ? '-'.$pkg_sn : '');?></span>
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50%" valign="top" class="di">
      <b>Delivery Instruction:</b>
      <p>
        <?=nl2br($label->note);?>
      </p>
      <b>Customer Ref:</b>
      <p><?=$label->cref?></p>
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
        <td style="font-size:1.5em"><?=$pkg_sn;?> of <?=$label->pkg;?></td>
      </tr>
    </table></td>
  </tr>
</table>
<div style="border-top: 2px #000 solid; border-bottom: 2px #000 solid; padding: 15px; text-align:center">
</div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed">
  <tr>
      <td class="sender" width="50%" valign="top">
        <h4>Delivery To:</h4>
    <?=$label->cnee->labelAddress('max-width:20rem;word-wrap:break-word;');?>
      </td>
    <td class="sender" width="50%" valign="top" style="border-left: 2px #000 solid;"><b>Sender:</b>
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
      </p></td>
  </tr>
</table>
</div>

<div style="text-align:center;margin-top:5px;"><div>

  <img src="data:image/svg+xml;base64,<?php
  $dm = new TCPDF2DBarcode($label->ref.($label->pkg >= 1 ? '-'.$pkg_sn : ''), 'QRCODE,L');
  echo base64_encode(str_replace([chr(232),chr(29)], ['',''], $dm->getBarcodeSVGcode(12, 12, 'black')));
  ?>" width="200" height='200' />

</div>

