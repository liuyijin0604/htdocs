<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>Request for Missing Documents</title>
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
       <td style="text-align: left; font-size: 30px;font-weight: bold;padding-bottom:10px;" width="50%">Request for Missing Documents</td>
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
    <p>PLEASE EMAIL YOUR MANIFEST TO  <b><?=$companyEmail?></b></p>
    <p>LOWEST HBL IS REQUIRED BY ALL CFS FOR UNPACKING PURPOSES. PLEASE PROVIDE MANIFEST MINIMUM 24 </p>
    <p>HOURS PRIOR ETA. LATE RECEIPT OF THIS DOCUMENT MAY DELAY THE AVAILABILITY OF YOUR CARGO AND COULD</p>
    <p>INCURR ADDITIONAL COSTS PAYABLE DIRECTLY OF THE UNPACKING DEPOT.</p>
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
        <tr class="even" align="left">
            <th>GOODS DESCRIPTION</th> <th>ORDER NUMBERS/REFERENCE</th>
        </tr>
        <tr height="28px">
              <td ><?=$model->getGoods()?></td><td></td>
        </tr>
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" width="100%" class="chart">
                    <tr class="even" align="left"><th width="33%">PACKAGES</th><th  width="33%">WEIGHT</th><th>VOLUME</th></tr>
                    <tr height="20px;"><td><?=$model->pkg?> package(s)</td><td><?=$model->weight?> KG</td><td><?=@sprintf("%.3f",$model->cbm*$model->pkg)?> M<sup>3</sup></td></tr>
                </table>
            </td>
            <td>
                 <table cellpadding="0" cellspacing="0" width="100%" class="chart">
                    <tr class="even" align="left"><th width="50%">OCEAN BILL OF LADING</th><th  width="50%">HOUSE BILL OF LADING</th></tr>
                    <tr height="20px;"><td><?=@$model->consol->awb?></td><td><?=$model->hbn?></td></tr>
                </table>
            </td>
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
        <tr class="even" align="left">
             <th colspan="7">CONTAINERS</th>
        </tr>
        <tr>
            <td colspan="7"><?=@$model->consol->mdata['container_no']?>(
            <?=@DmawbConsol::$containerTypes[@$model->consol->mdata['sea_type']]." ".@$model->consol->mdata['cargo_type']?>)</td>
        </tr>
    </table>
    <p>We have not yet received documents for the shipment referenced herein.Please send the documents requested below, by E-mail</p>
    <p>attachment. If you have any problem that might delay the matter further, please contact the writer urgently.Otherwise, we look</p>
    <p>forward to receiving these documents as soon as possible.Without them, the completion of Customs formalities may not proceed and</p>
    <p> delivery will be delayed.</p>
    <p>- House Waybill/Bill of Lading - Original Required</p>
    <br/>
    <p>Yours Sincerely</p>
    <br/>
    <p><?=$companyName?></p>
    <br/>
    <p>Email:imports@toplogistics.com.au</p>
<?php include('_pagination.php'); ?>
</body>
</html>
