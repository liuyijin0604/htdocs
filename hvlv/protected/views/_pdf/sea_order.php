<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>Sea Order</title>
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
$companyName = "Top Logistics";
$companyEmail = "imports@toplogistics.com.au";
$companyAddress="6C The Crescent";
$companyCity = "Kingsgrove NSW 2208";
if(!empty($behalf)){
  switch($behalf){
    case 'priority':
      $hdr = '_header_tla.php';
    break;
  }
  if(!empty($inv->mdata['suborg'])){
    $sorg = Org::model()->findByPk($inv->mdata['suborg']);
    $inv->mdata['name'] = $sorg->name;
    $inv->mdata['address'] = $sorg->getAddress();
  }
}
if(Yii::app()->name == 'TLA'){
  $hdr = '_header_tla.php';
  $companyName = Org::IM_COMPANY_NAME;  
  $companyEmail = Org::IM_EMAIL;
  $companyAddress= Org::IM_COMPANY_ADDRESS;
  $companyCity =  Org::IM_COMPANY_CITY;
}
include($hdr);
?>
      <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 30px;height: 50px; ">
       <tr>
       <td style="text-align: left; font-size: 30px;font-weight: bold;padding-bottom:10px;" width="50%">LCL Sea Delivery Order</td>
</tr>
</table>
</header>
    <table  width="100%" cellpadding="0" cellspacing="0"  height="130px;" >
        <tr>
            <td  width="50%" valign="top" style="padding-left:30px;" >
                <?= strtoupper($model->cnee->name)?><br/>
                <?= strtoupper($model->cnee->address)?><br/>
                <?= strtoupper($model->cnee->suburb." ".$model->cnee->state." ".$model->cnee->postcode)?>
            </td>
            <td  width="50%" align="right" valign="top">
             <table  width="90%"cellspacing="0" cellpadding="0"  class="chart1">
             <tr ><th class="even1" width="50%" align="left">SHIPMENT</th><td width="50%" style="font-weight:bold;"><?=$model->hbn?></td></tr>
             <tr ><th class="even1" width="50%" align="left">CONSOL</th><td width="50%"><?=@$model->consol->no?></td></tr>
             <tr ><th class="even1" width="50%" align="left">DATE</th><td width="50%"><?=date("d-M-y H:i")?></td></tr>
        </table>
            </td>
        </tr>
        
    </table>

    <div style="font-weight:bold;margin-bottom: 5px;">SHIPMENT DETAILS:</div>
    <table width="100%" cellspacing="0" class="chart">
        <tr class="even">
            <th width="50%" align="left">CONSIGNOR</th><th width="50%" align="left">CONSIGNEE</th>
        </tr >
        <tr height="130px;">
            <td valign="top" style="padding:8px;">
                <?= strtoupper($model->cnor->name)?><br/>
                <?= strtoupper($model->cnor->address)?><br/>
                <?= strtoupper($model->cnor->city." ".$model->cnor->state) ?><br/>
                <?= strtoupper($model->cnor->country)?>
            </td>
            <td valign="top" style="padding:8px;">
                <?= strtoupper($model->cnee->name)?><br/>
                <?= strtoupper($model->cnee->address)?><br/>
                <?= strtoupper($model->cnee->suburb." ".$model->cnee->state." ".$model->cnee->postcode) ?><br/>
                <?= strtoupper($model->cnee->country)?>
            </td>
        </tr>
        <tr class="even">
            <th width="50%" align="left">NOTIFY PARTY</th><th width="50%" align="left">GOODS AVAILABLE AT</th>
        </tr>
        <tr height="150px;">
            <td valign="top" style="padding:8px;">
                <?= strtoupper(@$model->notifier->name)?><br/>
                <?= strtoupper(@$model->notifier->address)?><br/>
                <?= strtoupper(@$model->notifier->suburb." ".@$model->notifier->state." ".@$model->notifier->postcode) ?><br/>
                <?= strtoupper(@$model->notifier->country)?>
            </td>
            <td valign="top" style="padding:8px;">
               <?=@$model->consol->mdata['arrival_cfs']?>
            </td>
        </tr>
        <tr class="even" align="left">
            <th>RELEASE TYPE</th> <th>ORDER NUMBERS/REFERENCE</th>
        </tr>
        <tr height="28px">
              <td ><?=isset($model->consol->mdata['sea_relase_type'])?$model->consol->mdata['sea_relase_type']:"EBL - Express Bill of Lading"?></td><td></td>
        </tr>
        <tr >
            <th align="left" class="even1" height="20px;">   
                  LINE
             </th>
            <td rowspan="2">
                <table cellpadding="0" cellspacing="0" width="100%" class="chart">
                    <tr class="even" align="left"><th width="50%">OCEAN BILL OF LANDING</th><th  width="50%">HOUSE BILL OF LANDING</th></tr>
                    <tr height="20px;"><td><?=@$model->consol->awb?></td><td><?=$model->hbn?></td></tr>
                </table>
            </td>
        </tr>
        <tr height="20px;">
            <td><?=@$model->consol->mdata['sea_carrier']?></td>
        </tr>
        <tr height="20px;" class="even" align="left">
            <th >VESSEL/VOYAGE/IMO(LIoyds)</th><th>COMMODITY TYPE</th>
       </tr>
        <tr  height="20px;">
            <td><?=@$model->consol->mdata['sea_vessel']?> / <?=$model->consol->flight?> / <?=$model->consol->airline?></td><td>GEN-General</td>
        </tr>
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" width="100%" class="chart">
                    <tr class="even" align="left"><th width="50%">PACKAGES</th><th  width="50%">WEIGHT</th></tr>
                    <tr height="20px;"><td><?=$model->pkg?> package(s)</td><td><?=$model->weight?> KG</td></tr>
                </table>
            </td>
            <td>
                 <table cellpadding="0" cellspacing="0" width="100%" class="chart">
                    <tr class="even" align="left"><th width="50%">VOLUME</th><th  width="50%">CHARGEABLE</th></tr>
                    <tr height="20px;"><td><?=@sprintf("%.3f",$model->cbm*$model->pkg)?> M<sup>3</sup></td><td><?=@sprintf("%.3f",$model->cbm*$model->pkg)?> M<sup>3</sup></td></tr>
                </table>
            </td>
        </tr>
        <tr class="even">
            <th><span style="display: inline-block;text-align: left;width: 70%;">ORIGIN</span><span style="display:inline-block; text-align: center;width: 30%;">ETD</span></th> 
            <th><span style="display: inline-block;text-align: left;width: 70%;">DESTINATION</span><span style="display:inline-block; text-align: center;width: 30%;">ETA</span></th>
        </tr>
        <tr>
            <td><span style="display: inline-block;text-align: left;width: 50%;"><?=@$model->consol->pol;?></span><span style="display:inline-block; text-align: right;width: 45%;"><?= empty($model->consol->etd)?"":date('d-M-y',strtotime($model->consol->etd))?></span></td>
            <td><span style="display: inline-block;text-align: left;width: 50%;"><?=@$model->consol->pod;?></span><span style="display:inline-block; text-align: right;width: 45%;"><?= empty($model->consol->eta)?"":date('d-M-y',strtotime($model->consol->eta))?></td>
        </tr>
        <tr class="even">
            <th><span style="display: inline-block;text-align: left;width: 70%;">PORT OF LOADING</span><span style="display:inline-block; text-align: center;width: 30%;">ETD</span></th> 
            <th><span style="display: inline-block;text-align: left;width: 70%;">PORT OF DISCHARGE</span><span style="display:inline-block; text-align: center;width: 30%;">ETA</span></th>
        </tr>
        <tr>
            <td><span style="display: inline-block;text-align: left;width: 50%;"><?=@$model->consol->mdata['sea_load_port'];?></span><span style="display:inline-block; text-align: right;width: 45%;"><?= empty($model->consol->etd)?"":date('d-M-y',strtotime($model->consol->etd))?></span></td>
            <td><span style="display: inline-block;text-align: left;width: 50%;"><?=@$model->consol->pod;?></span><span style="display:inline-block; text-align: right;width: 45%;"><?= empty($model->consol->eta)?"":date('d-M-y',strtotime($model->consol->eta))?></td>
        </tr>
 </table>
    <div style="font-weight:bold;margin-bottom: 5px;">ROUTING INFORMATION:</div>
    <table cellpadding="0" width="100%" cellspacing="0" class="chart">
        <tr class="even">
            <th>Type</th> <th width="25%">VESSEL/Voyage/IMO</th><th>CARRIER</th><th>LOAD</th><th>DISCH.</th><th>ETD</th><th>ETA</th>
        </tr>
        <tr>
            <td>Sea</td><td><?=@$model->consol->mdata['sea_vessel']?> / <?=$model->consol->flight?> / <?=$model->consol->airline?></td>
            <td><?=@$model->consol->mdata['sea_carrier']?></td><td><?=@$model->consol->mdata['sea_load_port'];?></td><td><?=@$model->consol->pod;?></td>
            <td><?= empty($model->consol->etd)?"":date('d-M-y',strtotime($model->consol->etd))?></td><td><?= empty($model->consol->eta)?"":date('d-M-y',strtotime($model->consol->eta))?></td>
        </tr>
         <tr class="even">
             <th>CONTAINER</th> <th >SEAL</th><th>SLOT/RELEASE</th><th>TYPE</th><th>WEIGHT(KG)</th><th>VOLUME(M<sup>3</sup>)</th><th>PACKS</th>
        </tr>
        <tr>
            <td><?=@$model->consol->mdata['container_no']?></td><td><?=@$model->consol->mdata['sea_seal']?> </td>
            <td></td><td><?=@DmawbConsol::$containerTypes[@$model->consol->mdata['sea_type']]." ".@$model->consol->mdata['cargo_type']?></td><td><?=@$model->weight;?> KG</td>
            <td><?=@sprintf("%.3f",$model->cbm*$model->pkg)?> M<sup>3</sup></td><td><?=(empty($type)||($type==2))?@$model->scanCount():$model->pkg?></td>
        </tr>
    </table>
    <table cellpadding="0" width="100%" cellspacing="0" class="chart">
        <tr class="even" align="left">
            <th width="50%">TRANSPORT COMPANY</th><th width="50%">DELIVERY REMARKS</th>
        </tr>
        <tr height="25px;">
            <td></td><td></td>
        </tr>
        <tr class="even" align="left">
            <th>MARKS AND NNUMBER</th><th>GOODS DESCRIPTION</th>
        </tr>
        <tr height="25px;">
            <td><?=empty($model->mdata['sea_mark_number'])? @$model->consol->mdata['sea_mark_number'] : $model->mdata['sea_mark_number']; ?></td><td><?=$model->getGoods()?></td>
        </tr>
    </table>
    <br/>
    <br/>
    <p>Yours Sincerely</p>
    <br/>
    <p><?=$companyName?></p>
    <br/>
    <p>Email:<?=$companyEmail?></p>
<?php include('_pagination.php'); ?>
</body>
</html>
