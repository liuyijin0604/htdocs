<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>Air Way Bill</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px black solid; padding: 2px; border-right:none; border-bottom: none; padding: 5px;}
table.chart{border: 1px black solid;  }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
header { padding-bottom: 15px; }
footer { padding-top: 10px; page-break-after: always; }
</style>
</head>

<body width="1120" >
<header>
<?php include('_header_tla.php');?>
    <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 30px; ">
 <tr>
   <td style="text-align: center; font-size: 28px;font-weight: bold;padding: 10px;" colspan="2">House Waybill</td>
</tr>
</table>
</header>
    <div  style="font-size:25px;padding-left: 2px;padding-bottom: 10px; margin-bottom: 5px;">Air Way Bill:<?= preg_replace('/[^\d]/i',"",@$model->consol->awb)?></div>
    <div style="border:solid 1px black;border-bottom: none;width: 100%;">
         <div style="font-size:25px;padding-left: 5px;padding-bottom: 10px;">
             <div style="width: 700px; float: left">
                <div style=" font-size:22px; padding-left: 5px;margin-bottom: 5px;">Shipper Name And Address: &nbsp;</div>
                <span style="margin-left: 10px;">   <?php echo strtoupper($model->cnor->name); ?> </span><br/>
                <span style="margin-left: 10px;"><?php echo strtoupper($model->cnor->address); ?></span><br/>
                <span style="margin-left: 10px;"><?php echo strtoupper($model->cnor->suburb . ' ' . $model->cnor->state . ' ' . $model->cnor->postcode.' ' . $model->cnor->country); ?> </span> <br/>
             </div>
             <div style="float:left;width:200px; border-left:solid 1px black;height: 190px;">
               <div style=" font-size:30px; font-weight:bold; margin-left: 10px; white-space: nowrap; align: center; height: 40px">House WayBill</div>
                <span style="margin-left:10px; font-size:30px;">   <?php echo $model->hbn ?> </span>
             </div>
         </div>

        <div style="clear:both;font-size:25px;height: 140px;border-top: solid 1px black;border-bottom: solid 1px black;padding-left: 2px;padding-top: 2px;padding-bottom: 10px;">
               <div style=" font-size:22px; padding-left: 5px;margin-bottom: 5px;">Consignee Name And Address: &nbsp;</div>
               <?php $cnee=(empty($model->receiver->name)&&empty($model->receiver->address))?$model->cnee:$model->receiver;?>
                <span style="margin-left: 10px;">   <?php echo strtoupper($cnee->name); ?> </span><br/>
                <span style="margin-left: 10px;"><?php echo strtoupper($cnee->address); ?></span><br/>
                <span style="margin-left: 10px;"><?php echo strtoupper($cnee->suburb . ' ' . $cnee->state . ' ' . $cnee->postcode); ?> </span> <br/>
        </div>

        <div style="display: -webkit-inline-box; height: 100px;">
            <div style="font-size:22px;padding-left:15px;width: 400px;border-right: solid 1px black;">Airport of departure/date: <br><div style="padding-top:2px; "> <?php echo @$model->consol->pol; ?>/<?php echo @$model->consol->etd?></div></div>
            <div style="width: 400px;padding-left:15px;font-size: 22px;margin-left: 2px; border-right: solid 1px black;" >Airport of Arrival/date:<br/><div style="padding-top:2px;"><?php echo @$model->consol->pod?>/<?php echo @$model->consol->eta?></div></div>
            <div style="width: 400px;padding-left:15px;font-size: 22px;margin-left: 2px;" >Flight:<br/><div style="padding-top:2px;" ><?php echo @$model->consol->flight?></div></div>
      </div>
    </div>
    <table  class="chart" style="width: 100%;font-size:22px;border-collapse: collapse;" cellspacing="1" cellpadding="1">
        <tr><th align="center" width="140">No of Packages</th><th align="center" width="140">Gross Weight</th><th align="center" width="140">Chargeable Weight</th><th>Currency</th><th>Value</th></tr>
        <tr><td width="100"><?=@$model->pkg?></td><td width="100"><?=@$model->weight?></td><td width="100"><?=@$model->weight?></td><td width="100"><?= ImParcel::$currencyTypes[@$model->currency]?></td><td width="100"><?=$model->dvalue?></td></tr>
    </table>

</footer>
<?php include('_pagination.php'); ?>
</body>
</html>
