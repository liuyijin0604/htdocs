<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>Sea Manifest</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 16px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
table.chart1 td, table.chart1 th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart1{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
table.charts td, table.charts th{ border:none }
table.charts{ border: none;  }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
.even1 {  background: rgba(200, 200, 200, 0.6);}
header { padding-bottom: 15px; }
footer { padding-top: 10px; page-break-after: always; }
</style>
</head>
<body width="1120" >
<header>
<?php 
$hdr = '_header_tla.php';
$companyName = Org::IM_COMPANY_NAME;
$companyEmail = Org::IM_EMAIL;
$companyAddress=Org::IM_COMPANY_ADDRESS;
$companyCity =  Org::IM_COMPANY_CITY;
$companyPhone = Org::IM_COMPANY_PHONE_SHOW;
if(!empty($behalf)){
  if(!empty($inv->mdata['suborg'])){
    $sorg = Org::model()->findByPk($inv->mdata['suborg']);
    $inv->mdata['name'] = $sorg->name;
    $inv->mdata['address'] = $sorg->getAddress();
  }
}
if(Yii::app()->name == 'TLA'){
  $companyName = strtoupper(Org::IM_COMPANY_NAME);  
  $companyEmail = Org::IM_EMAIL;
  $companyAddress= Org::IM_COMPANY_ADDRESS;
  $companyCity =  Org::IM_COMPANY_CITY;
}
include($hdr);
?>
</header>
        <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 30px;height: 50px; ">
 <tr>
     <td style="text-align: left; font-size: 30px;font-weight: bold;padding-bottom:10px;" width="50%">Sea Freight Manifest</td>
     <td align="right" width="50%">
         <table  width="90%"cellspacing="0" cellpadding="0"  class="chart1">
             <tr ><th class="even1" width="50%" align="left">CONSOL</th><td width="50%"><?=@$model->consol->no?></td></tr>
             <tr ><th class="even1" width="50%" align="left">OCEAN BILL OF LANDING</th><td width="50%"><?=@$model->consol->awb?></td></tr>
             <tr ><th class="even1" width="50%" align="left">DATE</th><td width="50%"><?=date("d-M-y H:i")?></td></tr>
        </table>
     </td>
</tr>
</table>
    <div style="font-weight:bold;margin-bottom: 5px;">CONSOL DETAILS:</div>
    <table width="100%" cellspacing="0" class="chart">
        <tr class="even">
            <th width="33%" align="left">Export Agent</th><th width="33%" align="left">Import Agent</th><th width="34%" align="left">Arrival CFS</th>
        </tr>
        <tr>
            <td>
                   <table width="100%" height="180px" cellspacing="0" cellpadding="0" class="charts">
                        <tr valign="top"><td colspan="2">The Freight Manager</td></tr>
                        <tr valign="bottom"><td width="50%">Phone:</td><td width="50%">Fax:</td></tr>
                    </table>
            </td>
            <td>
                <table width="100%"  height="180px;" cellspacing="0" cellpadding="0" class="charts">
                        <tr valign="top"><td colspan="2">The Freight Manager<br/>
                                <?=$companyName?><br/>
                                <?=$companyAddress?><br/>
                                <?=$companyCity?><br/>
                                AUSTRALIA<br/>
                            </td></tr>                       
                        <tr valign="bottom"><td width="60%">Phone:<?=$companyPhone?></td><td width="40%">Fax:</td></tr>
                </table>
            </td>
            <td>
                 <table width="100%"  height="180px;" cellspacing="0" cellpadding="0" class="charts">
                        <tr valign="top"><td colspan="2"><?=@$model->consol->mdata['arrival_cfs']?></td></tr>                       
                        <tr valign="bottom"><td width="50%">Phone:</td><td width="50%">Fax:</td></tr>
                </table>
            </td>
        </tr>
        <tr>
              <td>   
                  <table width="100%" height="50px" cellspacing="0" cellpadding="0" class="chart">
                    <tr valign="top" class="even" height="35px"><th align="left">TOTAL WEIGHT</th><th align="left">TOTAL VOLUME</th><th align="left">CHARGEABLE</th></tr>                       
                    <tr valign="bottom"><td width="33%"><?=@$model->weight;?>KG</td><td width="33%"><?=@sprintf("%.3f",$model->cbm*$model->pkg)?>M<sub>3</sub></td><td width="34%"><?=@sprintf("%.3f",$model->cbm*$model->pkg)?>M<sub>3</sub></td></tr>
                </table>
             </td>
              <td>
                <table width="100%" height="75px" cellspacing="0" cellpadding="0" class="chart">
                    <tr valign="top" class="even" height="35px"><th align="left">CARRIER</th></tr>                       
                    <tr valign="bottom"><td align="left"><?=@$model->consol->mdata['sea_carrier']?></td></tr>
                </table>
            </td>
         <td>
                <table width="100%" height="75px" cellspacing="0" cellpadding="0" class="chart">
                    <tr valign="top" class="even" height="35px"><th align="left">CUSTOMER ENTRY NUMBEER</th></tr>                       
                    <tr valign="bottom"><td align="left"></td></tr>
                </table>
            </td>
        </tr>
        <tr>
              <td>   
                  <table width="100%" height="75px" cellspacing="0" cellpadding="0" class="chart">
                      <tr valign="top" class="even" height="35px"><th align="left">PACAGES</th><th align="left">MASTER FREIGHT</th></tr>                       
                    <tr valign="bottom"><td width="50%" align="left"><?=$model->pkg?> Package(s)</td ><td width="50% align="left""></td></tr>
                </table>
             </td>
              <td>
                <table width="100%" height="75px" cellspacing="0" cellpadding="0" class="chart">
                    <tr valign="top" class="even" height="35px"><th align="top"><span  style="width: 50%;display: inline-block; text-align:left;">ORIGIN</span><span style="width: 48%;display: inline-block;text-align:right;" >ETD</span></th></tr>                       
                    <tr valign="bottom"><td><span  style="width: 50%;display: inline-block; text-align:left;"><?=@$model->consol->pol?></span><span style="width: 48%;display: inline-block;text-align:right;" ><?= empty($model->consol->etd)?"":date('d-M-y',strtotime($model->consol->etd))?></span></td></tr>
                </table>
            </td>
         <td>
                <table width="100%" height="75px" cellspacing="0" cellpadding="0" class="chart">
                    <tr valign="top" class="even" height="35px"><th><span  style="width: 50%;display: inline-block; text-align:left;">DESTINATION</span><span style="width: 48%;display: inline-block;text-align:right;" >ETA</span></th></tr>                       
                    <tr valign="bottom"><td><span  style="width: 50%;display: inline-block; text-align:left;"><?=@$model->consol->pod?></span><span style="width: 48%;display: inline-block;text-align:right;" ><?= empty($model->consol->eta)?"":date('d-M-y',strtotime($model->consol->eta))?></span></td></tr>
                </table>
            </td>
        </tr>
                <tr>
              <td>   
                  <table width="100%" height="75px" cellspacing="0" cellpadding="0" class="chart">
                      <tr valign="top" class="even" height="35px"><th align="left">CARRIER BOOKING REFERENCE</th></tr>                       
                    <tr valign="bottom"><td align="left"></td></tr>
                </table>
             </td>
              <td>
                <table width="100%" height="75px" cellspacing="0" cellpadding="0" class="chart">
                    <tr valign="top" class="even" height="35px"><th align="left">AGENT'S REFERENCE</th></tr>                       
                    <tr valign="bottom"><td align="left"></td></tr>
                </table>
            </td>
         <td>
                <table width="100%" height="75px" cellspacing="0" cellpadding="0" class="chart">
                    <tr valign="top" class="even" height="35px"><th align="left">LAST FOREIGN PORT</th></tr>                       
                    <tr valign="bottom"><td align="left"></td></tr>
                </table>
            </td>
        </tr>
    </table>
       <div style="font-weight:bold;margin-bottom: 5px;">ROUTING INFORMATION:</div>
        <table width="100%" cellspacing="0" class="chart">
        <tr class="even">
            <th width="33%" align="left">Mode</th><th width="33%" align="left">Vessel/Voyage/IMO(LIoyds)</th><th width="34%" align="left">Carrier</th>
        </tr>
         <tr>
                <td>SEA</td><td><?=@$model->consol->mdata['sea_vessel']?> / <?=$model->consol->flight?> / <?=$model->consol->airline?></td><td><?=$model->consol->mdata['sea_carrier']?></td>
         </tr>
            <tr class="even">
             <th width="33%" align="left">Load</th><th width="33%" align="left">Disch.</th><th width="34%" align="left"><span style="display: inline-block;text-align: left;width: 48%;">ETD</span><span style="display:inline-block; text-align: right;width: 48%;">ETA</span></th>
            </tr>
          <tr>
                <td><?=@$model->consol->mdata['sea_load_port']?></td><td><?=@$model->consol->pod?></td><td><span style="display: inline-block;text-align: left;width: 48%;"><?= empty($model->consol->etd)?"":date('d-M-y',strtotime($model->consol->etd))?></span><span style="display:inline-block; text-align: right;width: 48%;"><?= empty($model->consol->eta)?"":date('d-M-y',strtotime($model->consol->eta))?></span></td>
         </tr>
        </table>
       <div style="height:5px;"></div>
          <table width="100%" cellspacing="0" class="chart">
        <tr class="even">
            <th width="25%" align="left">CONTAINER</th><th width="25%" align="left">SEAL</th><th width="25%" align="left">TYPE</th><th width="25%" align="left">TARE WEIGHT</th>
        </tr>
         <tr>
             <td><?=@$model->consol->mdata["container_no"]?></td><td><?=@$model->consol->mdata['sea_seal']?></td><td><?=@DmawbConsol::$containerTypes[$model->consol->mdata['sea_type']]." ".@$model->consol->mdata['cargo_type']?></td><td></td>
         </tr>
            <tr class="even">
                <th width="25%" align="left">NET WEIGHT</th><th width="25%" align="left">GROSS WEIGHT</th><th width="25%" align="left">VOLUME</th><th width="25%" align="left">PACKAGES</th>
            </tr>
          <tr>
              <td><?=@$model->weight?>KG</td><td><?=@$model->weight?>KG</td><td><?=@sprintf("%.3f",$model->cbm*$model->pkg)?> M<sup>3</sup></td><td><?=$model->pkg?> PKG</td>
         </tr>
        </table>
        <div style="font-weight:bold;margin-bottom: 5px;">SHIPMENT DETAILS:</div>
        <table width="100%" cellspacing="0"  class="chart1">
         <tr class="even">
             <th width="100%" >HOUSE</th>
         </tr>
         <tr>
             <td> 
                 <table width="100%" cellspacing="0" height="60px;" class="chart1">
                     <tr class="even" height="30px;">
                         <th>HBL:</th><th>Job Ref:</th><th>Wgt/Vol/Pkg:</th><th>Origin:</th><th>Destination:</th><th>Shippers Ref:</th><th>Service Level:</th><th>Master:</th>
                     </tr>
                     <tr>
                        <td align="left"><?=@$model->hbn?></td><td align="left"></td> <td align="left"><?=@$model->weight?>KG /<?=@sprintf("%.3f",$model->cbm*$model->pkg)?> M<sup>3</sup> / <?=$model->pkg?> PKG </td><td align="left"><?=@$model->consol->pol?></td>  <td align="left"><?=@$model->consol->pod?></td><td align="left"></td> <td align="left"></td><td align="left"><?=@$model->consol->awb?></td>
                     </tr>
                 </table>
             </td>
         </tr>
        </table>
        <table width="100%" cellspacing="0"  class="chart1">
            <tr class="even" >
                <th width="50%" align="left">CONSIGNOR</th><th width="50%" align="left">CONSIGNEE</th>
            </tr>
            <tr height="180px;">
                <td>
                       <table width="100%" height="180px" cellspacing="0" cellpadding="0" class="charts">
                        <tr valign="top"><td colspan="2">
                                <?=@$model->cnor->name?><br/>
                                <?php if(!empty($model->cnor->company)) echo $model->cnor->company."<br/>";?>
                                <?=$model->cnor->address.",".$model->cnor->city;?></br>
                                <?=$model->cnor->state.", ".$model->cnor->country;?>
                            </td></tr>
                        <tr valign="bottom"><td width="50%">Phone:<?=$model->cnor->tel?></td><td width="50%">Fax:</td></tr>
                    </table>
                </td>
                <td>
                       <table width="100%" height="180px" cellspacing="0" cellpadding="0" class="charts">
                            <?php if(!empty(@$model->receiver->name)):?>
                            <tr valign="top">
                                <td colspan="2">
                                    <?=@$model->receiver->name?><br/>
                                    <?php if(!empty($model->receiver->company)) echo $model->receiver->company."<br/>";?>
                                    <?=$model->receiver->address;?><br/>
                                    <?=$model->receiver->suburb." ".$model->receiver->state." ".$model->receiver->postcode;?><br/>
                                    Australia
                                </td>
                            </tr>
                            <tr valign="bottom"><td width="50%">Phone:<?=$model->receiver->tel?></td><td width="50%">Fax:</td></tr>

                            <?php else:?>
                                
                            <tr valign="top">
                                <td colspan="2">
                                    <?=@$model->cnee->name?><br/>
                                    <?php if(!empty($model->cnee->company)) echo $model->cnee->company."<br/>";?>
                                    <?=$model->cnee->address;?><br/>
                                    <?=$model->cnee->suburb." ".$model->cnee->state." ".$model->cnee->postcode;?><br/>
                                    Australia
                                </td>
                            </tr>
                            <tr valign="bottom"><td width="50%">Phone:<?=$model->cnee->tel?></td><td width="50%">Fax:</td></tr>


                            <?php endif?>
                    </table>
                </td>
                
            </tr>
        </table>
              <table width="100%" cellspacing="0"  class="chart1">
            <tr class="even" >
                <th width="50%" align="left">NATURE OF GOODS</th><th width="50%" align="left">HANDLING INSTRUCTIONS</th>
            </tr>
            <tr height="180px;">
                <td>
                    <table cellpadding="0" cellspacing="0" class="chart1" height="180px;" width="100%">
                        <tr>
                            <th class="even1">Release:</th><td><?=@$model->consol->mdata['sea_relase_type']?></td> 
                        </tr>
                        <tr>
                            <th class="even1"> Type:</th><td>GEN(General)</td>
                        </tr>
                        <tr>
                            <th class="even1"> Goods Desciption</th><td><?=strtoupper(substr($model->getGoods(),0,30))." ETC"?></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table cellpadding="0" cellspacing="0" class="chart1" height="180px;" width="100%">
                        <tr height="90px;">
                           <td><?=empty($model->mdata['sea_instruction'])? @$model->consol->mdata['sea_instruction'] : $model->mdata['sea_instruction'];?></td> 
                        </tr>
                        <tr>
                            <th class="even1" height="30px;" align="left">Marks  & Numbers </th>
                        </tr>
                        <tr>
                            <td><?=empty($model->mdata['sea_mark_number'])? @$model->consol->mdata['sea_mark_number'] : $model->mdata['sea_mark_number']; ?></td>
                        </tr>
                    </table>
                </td>
                
            </tr>
        </table>

<?php include('_pagination.php'); ?>
</body>
</html>
