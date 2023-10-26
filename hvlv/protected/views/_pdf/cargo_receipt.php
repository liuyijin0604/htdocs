<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>Cargo Receipt</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 20px; text-rendering: optimize-speed; width: 1120px; }
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
div.page-break { page-break-after: always; }
</style>
</head>
<body width="1120" >
<header>
<?php 
if(!isset($model))
{
  $model = $models[0];
}

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
}
if(Yii::app()->name == 'TLA'&&!($model->agent_id == 1474&&$cargoType ==CargoProcess::NORMAL_CARGO)){
  $hdr = '_header_tla.php';
  $companyName = Org::IM_COMPANY_NAME;  
  $companyEmail = Org::IM_EMAIL;
  $companyAddress= Org::IM_COMPANY_ADDRESS;
  $companyCity =  Org::IM_COMPANY_CITY;
}
include($hdr);
?>

      <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 80px;height: 50px; ">
       <tr>
           <td style="text-align: center; font-size: 45px;font-weight: bold;padding-bottom:10px;" width="100%">Cargo Receipt</td>
      </tr>
</table>
</header>
    <div style="font-size:25px;margin-top: 25px; text-align: justify;margin-bottom: 25px;">
        <p>This is to confirm that we have received the delivery of below consignment in full and good condition:</p>
    </div>
    <table width="100%" cellspacing="0" class="chart">
        <tr class="even">
            <th width="25" align="center">Reference NO.</th><th width="25%" align="center">PO NO.</th><th width="25%">FBA NO.</th><th  width="10%">CTNs</th><th width="10%">Weight(KG)</th><th width="5%">CBM</th>
        </tr >
        <tr  >
            <td  align="center" height="300px;" style="padding:8px; font-size:28px;">
                    <?=@$model->ref;?>
            <td align="center" style="padding:8px; font-size:28px;">
                 <?php 
                 $index=0;
                 if(!empty($model->mdata['amazon_po'])){
                       foreach (preg_split("/[;,，\.\n\s+]/", $model->mdata['amazon_po']) as $h){
                           if(empty(trim($h)))    continue;
                           echo trim($h)."<br/>";
                           $index++;
                       }
                 }?>
            </td>
            <td align="center" style="padding:8px; font-size:28px;">
                    <?php if(!empty($model->mdata['amazon_shipment_ids'])){
                       foreach (preg_split("/[;,，\.\n\s+]/", $model->mdata['amazon_shipment_ids']) as $h){
                            if(empty(trim($h)))    continue;
                           echo trim($h)."<br/>";
                       }
                 }?>
            </td>
            <td align="center" style="padding:8px; font-size:28px;">
                        <?=@$model->pkg?>
            </td>
            <td align="center" style="padding:8px; font-size:28px;">
                      <?=@$model->weight?>
            </td>
            <td align="center" style="padding:8px; font-size:28px;">
                      <?=@$model->mdata['total_cbm']?>
            </td>
        </tr>
 </table>
    <?php if($index>20){
    echo '<div class="page-break"></div>';
    }?>
    <div style="font-size:30px;line-height: 60px;margin-top: 50px;">
    <div>
        <p>Booking No.:<?= @$model->amazon_info->booking_ref?></p>
    </div>
    <?php if(!isset($image)):?>

    <div>Delivery to:<br /><p style="font-size:25px;">
                <?php 
                 $address='';
                if(!empty($model->mdata['cargo_receipt_addr'])&&($model->mdata['cargo_receipt_addr']!="")){
                   $address=$model->mdata['cargo_receipt_addr'];
                }else
                {
                  $address = @$model->cnee->name.",".@$model->cnee->address.",".@$model->cnee->suburb.",".@$model->cnee->state.",".@$model->cnee->postcode.";    Tel:".@$model->cnee->tel;
                  if(!empty($model->cnee->company))
                  {
                    $address.=";    Company:".@$model->cnee->company;
                  }
                }
                echo $address;
                ?>
      <br />Booking Time:<?=@$model->amazon_info->amazon_booking_time?></p>
    </div>

      <div>
          <p>Receiver(Pls Print Full Names): <span style="display: inline-block;width: 500px; border-bottom: 2px solid black"></span></p>
      </div>
      <div>
          <p>Signature: <span style="display: inline-block;width:500px; border-bottom: 2px solid black"></span></p>
      </div>
        <div>
          <p>Date: <span style="display: inline-block;width: 500px; border-bottom: 2px solid black"></span></p>
      </div>
    <?php endif;?>

    <?php 
      if(!empty($model->mdata['cargo_receipt_addr'])){
        $address = $model->mdata['cargo_receipt_addr'];
      }else{
        $address = @$model->cnee->address.",".@$model->cnee->suburb.",".@$model->cnee->state.",".@$model->cnee->postcode;
      }
    ?>


    <?php if(isset($image)&&is_array($image)):?>

      <div>Delivery to:<br /><p style="font-size:25px;"> <?php echo @$address;?><br />Booking Time:<?=@$model->amazon_info->amazon_booking_time?></p>
      </div>
          <div>
              <p>Receiver(Pls Print Full Names): 
                <span style="display: inline-block;width: 500px; border-bottom: 2px solid black">
                  <?= $image['sprint'];?>
              </span>
              </p>
          </div>
          <div>
              <p>Signature: 
                <img src="<?=$image['sig']?>" style="display: inline-block;width: 300px;height:100px;  border-bottom: 2px solid black"></img>;
               </p>
          </div>
            <div>
              <p>Date: 
              <span style="display: inline-block;width: 500px; border-bottom: 2px solid black">
                
                  <?=$image['sdate'];?>

              </span></p>
          </div>


        <?php elseif(isset($image)):?>
          <div>
             <?= CHtml::image(Yii::app()->request->hostInfo.Yii::app()->baseUrl."/filerepo/".$image->hash."/".$image->name,$image->name,['width'=>'900']) ?>
          </div>


    <?php endif;?>


    <div>
<?php include('_pagination.php'); ?>
</body>
</html>
