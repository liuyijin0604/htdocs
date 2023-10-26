<?php
//opcache_invalidate(__FILE__);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);

$carrierServiceID = $label->getTollServiceType();
$serverLabel = 'PARCELS OVERNIGHT';
if ( $carrierServiceID  == TollAPI::TOLL_SERVICE_OFFPEAK ) {
    $serverLabel = 'PARCELS OFFPEAK';
}

?>
<div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td><b>CARRIER:</b> &nbsp; TOLL PRIORITY</td>
    </tr>
    <tr height="20px;">
        <td width="70%" valign="top" class="dto"
            style="background-color: #000000; color:white; font-size:28px;text-align: center;vertical-align: middle;">
            SERVICE: <?php echo $serverLabel; ?>
        </td>
        <td valign="middle" style="background-color: #ffffff; color:#000000; border-color: #000000; font-size:40px;text-align: center;vertical-align: middle;border-left:8px solid;">
           <div style="border: 1px solid; padding: 0;" ><span style="margin: 8px 4px;"> SYD </span></div>
        </td>
    </tr>

    <tr style="width: 100%;">
        <td style="width: 100%;"><b>CONNOTE #:</b> &nbsp; <?= $label->ref; ?><br/>
        <!--   <div style="text-align: center;">
            <?php
            $bc = new TCPDFBarcode($label->ref, 'C128');
            $bc->getBarcodeSVG(2.0, 60, 'black');
            ?>
               </div> -->
        </td>
    </tr>

</table>

<br/>

<div style="padding-left: 40px;;padding-right: 40px;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="80%" valign="top" class="di" style="min-height: 300px;">
                <div style="height:280px; overflow:hidden">
                    <p style="font-size:1.4em;padding: 5px;"><b>TO:</b><br/><?= ucwords(strtolower($label->cnee->name)); ?><br/>
                        <?php
                         if ( !empty($label->cnee->company) ) {

                             $companys =  explode(' ', $label->cnee->company, 4);
                             $company = '';
                             $index = 0;
                             foreach ( $companys as $com ) {
                                 if ( !empty($company) )  $company .= ' ';
                                 $company .= $com;
                                 $index++;
                                 if ( $index >= 3 )  break;
                             }
                            echo ucwords(strtolower($company)) . '<br/>';
                         }
                        ?>
                        <?= nl2br($label->cnee->apAddress()); ?><br/>
                      <div style="padding-top: 8px;">  <?= '<b>Customer Contact:</b> <br/>' .  $label->cnee->tel; ?></div>
                    </p>
                </div>
                <div style="padding-top: -15px;font-size: 18px;padding-bottom: 8px;"> <b>Special Instructions:</b><br/> N/A</div>
                <div>
                    <b>From:</b>
                    <?php
                   // echo $label->cnor->name, '<br />', $label->cnor->fullAddress(array('suburb', 'state', 'postcode')), ',02-99257111';
                    ?>
                    Jerry <br/>
                    6C The Crescent<br/>
                    Kingsgrove NSW 2208<br/>
                    02-99257111

                </div>
                <div style="margin-top: 2px;">
                    <b>Desc. of Goods: </b>Non Hazardous Cargo
                </div>
                <div>
                    <b>Reference: </b><?php echo $label->hbn; ?><br/><?php if (!empty($label->cref)) echo $label->cref; ?>
                </div>

                <!--
                <p>&nbsp; Delivered by:</p><br/>
                <img src="<?= Yii::app()->request->hostInfo . Yii::app()->baseUrl; ?>/images/PCAE_Logo_large.png"
                     alt="PCA Express Logo" width="300" align="left"/> -->
            </td>
            <td style="padding-left: 5px;text-align: center;" valign="top">
                                <div style="border: 1px solid;"> DG'S:<br><b>NO</b></div>
                                <div style="border-left: 1px solid;border-bottom: 1px solid;border-right: 1px solid"><b>Item No.</b><br><?=  $pkg_sn . ' /  ' . $label->pkg; ?></div>
                                <div style="border-left: 1px solid;border-bottom: 1px solid;border-right: 1px solid"><b>DESPATCH <br> DATE:</b><br><span style="font-size: 14px;"><?=  date('Y-m-d',strtotime('+4 days',strtotime($label->created))); ?></span></div>
                                <div style="border-left: 1px solid;border-bottom: 1px solid;border-right: 1px solid"><b>CON <br> WBGHT:</b><br><?=  $label->weight; ?>Kg <br>
                                    <b>CON <br> CUBC:<br>(m<sup>3</sup>):</b><br><?= number_format($label->cbm,4,'.',''); ?> <br>
                                    <b>ITEM <br> WBGHT:</b> <br><?=  number_format($label->weight / $label->pkg,1,'.',''); ?>Kg
                                </div>
            </td>
          </tr>
    </table>
</div>


<div style="-webkit-transform: rotate(-90deg); text-align:center;position:absolute;left: -370px; top:550px; width:820px; height: 80px; overflow:hidden;">
    <p style="font-size: 19px;">THIS ITEM WILL BE SUBJECT TO SECURITY SCREENING AND CLEARING</p>
</div>

<div style="-webkit-transform: rotate(90deg); text-align:center;position:absolute;right:-460px; top:530px; width:1000px; height: 80px; overflow:hidden;">
    <p  style="font-size: 19px;">CARRIER'S TERMS AND CONDITIONS APPLY. DANGEROUS GOODS NOT CONSIGNED WITHIN</p>
</div>

<div style="width:80%;margin-top:-8px;margin-left: 50px;padding-right: 50px;height: 4px;background-color: #000000;">
    <!--
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="4px">
    <tr>
        <td height="2px;" width="90%" valign="top" class="dto" style="background-color: #000000; color:white; font-size:10px;text-align: center;vertical-align: middle;">
        </td>
    </tr>
</table> -->
</div>

<div style="padding-right: 40px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td style="font-size: 18px;text-align: right;"><b>Item No. <br/><?= $label->ref . sprintf('%03d',$pkg_sn) ?></b></td>
    </tr>
</table>
</div>

<div style="bottom: 10px;  margin-top: 0px;text-align: center;width: 100%;">
    <p style="padding:5px;">
        <?php

        // get Toll barcode
        // formart :
        // 1	2	3	4	5	6	7	8	9	10	11	12	13	14	15	16	17	18	19	20	21
        // Unique Tracking Barcode
        // ID	Receiver’s Postcode	Service Code	Item Number	Check Digit
        //  T	N	N	N	N	A	A	A	A	A	A	N	N	N	N	N	N	N	N	N	N
        //
        $barcode = 'T' . $label->cnee->postcode . $carrierServiceID . $label->ref  . sprintf('%03d',$pkg_sn);
        $toll = new TollAPI();
        $barcode .= $toll->getCheckDigital($barcode);
        $bc = new TCPDFBarcode($barcode, 'C128');
        $bc->getBarcodeSVG(2.5, 128, 'black');
        ?></p>
    <p style="padding: 15px 0;"><?= $barcode; ?></p>
</div>


<div style="bottom: 2px; text-align: center; width: 100%;">
<b>DECLARATION BY:</b> PCA Express
</div>

</div>
<!--
<div style="padding-top:5px; font-weight:bold;">http://www.pcaexpress.com.au &nbsp; &nbsp; &nbsp; Tel: 1800 518 000</div>
-->