<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking 
$type = empty($type)?"Air":$type;
?>
<title>Sea Outturn Report</title>
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
           <td style="text-align: left; font-size: 30px;font-weight: bold;padding-bottom:10px;" width="50%"><?=$type?> Outturn Report</td>
   </tr>
</table>
</header>
    <table  width="100%" cellpadding="0" cellspacing="0"  height="100px;" >
        <tr>
            <td  width="50%" valign="top" style="padding-left:30px;" >
                <?= strtoupper(@$model->cnee->name)?><br/>
                <?= strtoupper(@$model->cnee->address)?><br/>
                <?= strtoupper(@$model->cnee->suburb." ".@$model->cnee->state." ".@$model->cnee->postcode)?>
            </td>
            <td  width="50%" align="right" valign="top">
             <table  width="90%"cellspacing="0" cellpadding="0"  class="chart1">
             <tr ><th class="even1" width="50%" align="left">SHIPMENT</th><td width="50%" style="font-weight:bold;"><?=$model->hbn?></td></tr>
             <tr ><th class="even1" width="50%" align="left">CONSOL</th><td width="50%"><?=@$model->consol->no?></td></tr>
             <tr ><th class="even1" width="50%" align="left">DATE</th><td width="50%"><?=date("d-M-y H:i")?></td></tr>
             <tr ><th class="even1" width="50%" align="left">AVAILABLE DATE</th><td width="50%"><?=!empty($model->consol->mdata['available_date'])?date("d-M-y H:i", (intval(strtotime($model->consol->mdata['available_date']))+60)):''?></td></tr>
             <tr ><th class="even1" width="50%" align="left">STORAGE STARTS</th><td width="50%">
                <?php
                    $storageStart = $model->getStorageStartDate();
                    if(!empty($storageStart))
                    {
                         echo date("d-M-y H:i", strtotime($model->getStorageStartDate()));
                    }
                ?>
            </td></tr>
        </table>
            </td>
        </tr>
    </table>
    <br/>
    <table width="100%" cellspacing="0" class="chart">
        <tr class="even">
            <th width="50%" align="left">CONSIGNOR</th><th width="50%" align="left">CONSIGNEE</th>
        </tr >
        <tr height="130px;">
            <td valign="top" style="padding:8px;">
                <?= strtoupper(@$model->cnor->name)?><br/>
                <?= strtoupper(@$model->cnor->address)?><br/>
                <?= strtoupper(@$model->cnor->city." ".@$model->cnor->state) ?><br/>
                <?= strtoupper(@$model->cnor->country)?>
            </td>
            <td valign="top" style="padding:8px;">
                <?= strtoupper(@$model->cnee->name)?><br/>
                <?= strtoupper(@$model->cnee->address)?><br/>
                <?= strtoupper(@$model->cnee->suburb." ".@$model->cnee->state." ".@$model->cnee->postcode) ?><br/>
                <?= strtoupper(@$model->cnee->country)?>
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
                <?php            
                    if(empty($model->consol->mdata['arrival_cfs']))
                    {
                        if(!empty($model->consol->dpt_id)&&$model->consol->dpt_id==218)
                        {
                             $availableAt=$companyName." <br/>18 Grimes Court <br/>Derrimut Vic 3030 <br/> Australia<br/>
                             Opening hours: 9:00 AM- 5:00 PM";
                        }else{
                            $availableAt=$companyName." <br/>{$companyAddress} <br/>{$companyCity} <br/> Australia<br/>
                             Opening hours: 9:00 AM- 6:00 PM";
                        }
                    }else
                    {
                         echo preg_replace('/open(ing)? time/i', '<br /><br />Open Time', $model->consol->mdata['arrival_cfs']); 
                         //echo strtoupper(@$model->consol->mdata['cfs_address']);    
                    }                 
               ?>
            </td>
        </tr>
        <tr class="even" align="left">
            <th>GOODS DESCRIPTION</th> <th>ORDER NUMBERS/REFERENCE</th>
        </tr>
        <tr height="28px">
            <td ><?= strtoupper($model->getGoods());?></td><td></td>
        </tr>
        <tr >
            <th align="left" class="even1" height="20px;">   
                  <?=$type=='Sea'?"CARRIER":"AIRLINE"?>
             </th>
            <td rowspan="2">
                <table cellpadding="0" cellspacing="0" width="100%" class="chart">
                    <tr class="even" align="left">
                        <th width="50%"><?=$type=='Sea'?"OCEAN BILL OF LANDING":"MAWB"?></th>
                        <th width="50%"><?=$type=='Sea'?"HOUSE BILL OF LANDING":"HAWB"?></th>
                    </tr>
                    <tr height="20px;"><td><?=@$model->consol->awb?></td><td><?=$model->hbn?></td></tr>
                </table>
            </td>
        </tr>
        <tr height="20px;">
            <td><?=@$model->consol->mdata['sea_carrier']?></td>
        </tr>
        <tr height="20px;" class="even" align="left">
            <th><?=$type=='Sea'?"VESSEL/VOYAGE/IMO(LIoyds)":"FLIGHT NO."?></th><th>COMMODITY TYPE</th>
       </tr>
        <tr  height="20px;">
            <td>
                <?php if($type=='Sea'):?>
                    <?=@$model->consol->mdata['sea_vessel']?> / <?=$model->consol->flight?> / <?=$model->consol->airline?>
                <?php else:?>
                    <?=$model->consol->flight?>
                <?php endif;?>
            </td><td>GEN-General</td>
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
                    <tr height="20px;"><td><?=@sprintf("%.2f",$model->cbm*$model->pkg)?> M<sup>3</sup></td><td><?=@sprintf("%.2f",$model->cbm*$model->pkg)?> M<sup>3</sup></td></tr>
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
  <div style="font-weight:bold;margin-bottom: 5px;">OUTTURN:</div>
 <table cellpadding="0" width="100%" cellspacing="0" class="chart">
     <tr class="even">
         <th width="13%"></th><th>MANIFEST</th><th>OUTTURN</th><th>SHORT</th><th>SURPLUS</th><th>PILLAGED</th><th>DAMAGED</th>
    </tr>
     <tr>
         <th class="even1">PACKAGES</th><td><?=$model->pkg?></td><td><?=$model->getOutPkg()?></td><td>
             <?php $short=$surplus=""; if($model->pkg>$model->getOutPkg()){
                 $short=$model->pkg-$model->getOutPkg();
              }else if($model->getOutPkg()>$model->pkg){
                 $surplus=$model->getOutPkg()-$model->pkg;
              }
              echo $short;?>      
         </td><td><?=$surplus?></td><td>0</td><td><?=isset($model->mdata['damaged_packs'])?$model->mdata['damaged_packs']:0?></td>
         </tr>
     <tr>
         <th class="even1">WEIGHT</th><td><?=$model->weight?> KG</td><td><?=sprintf("%.2f",$model->weight/$model->pkg*$model->getOutPkg())?> KG</td><td>
             <?php $shortWeight=$surplusWeight=""; 
             if($model->pkg>$model->getOutPkg()){
                 $shortWeight= sprintf("%.2f",$model->weight/$model->pkg*($model->pkg-$model->getOutPkg()));
              }else if($model->getOutPkg()>$model->pkg){
                 $surplusWeight= sprintf("%.2f",$model->weight/$model->pkg*($model->getOutPkg()-$model->pkg));
              }
              echo $shortWeight;
?>      
         </td><td><?=$surplusWeight?></td><td></td><td></td>
     </tr>
          <tr>
              <th class="even1">VOLUME</th><td><?=sprintf("%.3f",$model->cbm*$model->pkg)?>M<sup>3</sup></td><td><?=sprintf("%.3f",$model->cbm*$model->getOutPkg())?> M<sup>3</sup></td><td>
             <?php $shortCbm=$surplusCbm=""; 
             if($model->pkg>$model->getOutPkg()){
                 $shortCbm= sprintf("%.3f",$model->cbm*($model->pkg-$model->getOutPkg()));
              }else if($model->getOutPkg()>$model->pkg){
                 $surplusCbm= sprintf("%.3f",$model->cbm*($model->getOutPkg()-$model->pkg));
              }
              echo $shortCbm;
?>      
         </td><td><?=$surplusCbm?></td><td></td><td></td>
     </tr>
 </table>
  <div style="padding:2px;"></div>

     <table cellpadding="0" width="100%" cellspacing="0" class="chart">
        <tr class="even" align="left">
            <th>OUTTURN COMMENTS</th><th>MARKS AND NUMBER</th><th>REFERENCE NUMBER</th>
        </tr>
        <tr height="50px;">
            <td>
              <?php if(!empty($model->mdata['amzon_pallet'])){echo intval($model->mdata['amzon_pallet'])." ".$model->mdata['amzon_pallet_type']." Pallets";}  ?>
              <?=empty($model->mdata['outturn_comments'])?"":"</br>".$model->mdata['outturn_comments']  ?>
            </td>
            <td><?=@$model->mdata['sea_mark_number']?></td><td><?=$model->ref?></td>
        </tr>
    </table>
    <table cellpadding="0" width="100%" cellspacing="0" class="chart">
         <tr class="even">
             <th>CONTAINER</th> <th ><?=$type=='Sea'?"SEAL":""?></th><th>COUNT</th><th>TYPE</th><th>WEIGHT(KG)</th><th>VOLUME(M<sup>3</sup>)</th><th>PACKS</th>
        </tr>
        <tr>
            <td><?=@$model->consol->mdata['container_no']?></td>
            <td><?=@$model->consol->mdata['sea_seal']?> </td>
            <td>1</td>
            <td>
                <?php

                    if($type=='Sea')
                    {
                        echo @DmawbConsol::$containerTypes[@$model->consol->mdata['sea_type']]." ".@$model->consol->mdata['cargo_type'];
                    }else
                    {
                        echo "LCL";
                    }

                ?>
            </td>
            <td><?=@$model->weight;?> KG</td>
            <td><?=@sprintf("%.3f",$model->cbm*$model->pkg)?> M<sup>3</sup></td>
            <td><?=@$model->pkg?></td>
        </tr>
    </table>
    <table cellpadding="0" width="100%" cellspacing="0" class="chart">
         <tr class="even">
             <th class="even1" >Damage Files</th>
        </tr>
        <?php 
          $files = ImParcelService::getImParcelDamageFile($model);
          foreach ($files as $key => $value) {
            echo '<tr>';
            echo '<td><a href="'.$value.'">'.$value.'</a></td>';
             echo '</tr>';
          }
        ?>
    </table>
<?php include('_pagination.php'); ?>
</body>
</html>
