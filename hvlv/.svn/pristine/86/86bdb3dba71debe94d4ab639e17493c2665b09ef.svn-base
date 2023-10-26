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
div{line-height: 26px; display: inline-block;}
span{word-break: break-all; white-space: normal;}  
.item_table{}
.item_table tr{}
.item_table tr td{height: 40px;}
</style>
</head>

<body width="1120" >
<header>
<?php #include('_header_tla.php');?>
</header>
    <div style="border:solid 1px black;border-bottom: none;width: 100%;">
        <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 10px; ">
         <tr>
           <td style="text-align: center; font-size: 28px;font-weight: bold;padding: 10px;border-left:solid 1px black;border-bottom:solid 1px black;">
           Bill of Landing</td>
           <td style="border-bottom:solid 1px black;font-size: ">Combined transport shipment or port to port shipment</td>
        </tr>
        </table>
         <div style="font-size:16px;">
            <table height="100%">
                <tr>
                <td width='50%'>
             <div style="width:100%;display:inline-block;height:100%;border-right:solid 1px black;">
                <div style=" font-size:16px;width:100%;font-weight:bold;margin-bottom: 5px;border-bottom:solid 1px black;">SHIPPER: &nbsp;</div>
                <div style="width:100%;">
                    <span style="margin-left: 10px;">   <?php echo strtoupper($model->cnor->name); ?> </span><br/>
                    <span style="margin-left: 10px;"><?php echo strtoupper($model->cnor->address); ?></span><br/>
                    <span style="margin-left: 10px;"><?php echo strtoupper($model->cnor->suburb . ' ' . $model->cnor->state . ' ' . $model->cnor->postcode.' ' . $model->cnor->country); ?> </span> <br/>
                </div>
               <div style=" width:100%;font-size:16px;font-weight:bold;margin-bottom: 5px;border-top:solid 1px black;border-bottom:solid 1px black;">CONSIGNEE: &nbsp;</div>
               <div>
                    <span style="margin-left: 10px;">   <?php echo strtoupper($model->cnee->name); ?> </span><br/>
                    <span style="margin-left: 10px;"><?php echo strtoupper($model->cnee->address); ?></span><br/>
                    <span style="margin-left: 10px;"><?php echo strtoupper($model->cnee->suburb . ' ' . $model->cnee->state . ' ' . $model->cnee->postcode.' ' . $model->cnee->country); ?> </span> <br/>
                </div>

                <div style="width:100%;font-size:16px;font-weight:bold;margin-bottom: 5px;border-top:solid 1px black;border-bottom:solid 1px black;">Notifying Party: &nbsp;</div>
               <div>
                    <?php if(empty($model->notifier->name)):?>
                        <span style="margin-left: 10px;">   <?php echo strtoupper($model->cnee->name); ?> </span><br/>
                        <span style="margin-left: 10px;"><?php echo strtoupper($model->cnee->address); ?></span><br/>
                        <span style="margin-left: 10px;"><?php echo strtoupper($model->cnee->suburb . ' ' . $model->cnee->state . ' ' . $model->cnee->postcode.' ' . $model->cnee->country); ?> </span> <br/>
                    <?php else: ?>
                         <span style="margin-left: 10px;">   <?php echo strtoupper($model->notifier->name); ?> </span><br/>
                        <span style="margin-left: 10px;"><?php echo strtoupper($model->notifier->address); ?></span><br/>
                        <span style="margin-left: 10px;"><?php echo strtoupper($model->notifier->suburb . ' ' . $model->notifier->state . ' ' . $model->notifier->postcode.' ' . $model->notifier->country); ?> </span> <br/>
                    <?php endif;?>
                </div>
                <div style="font-size:16px;font-weight:bold;width:100%;display: inline-block;border-top:solid 1px black;border-bottom:solid 1px black;">
                    <div style="width:50%;display:inline-block;border-right:solid 1px black;">PORT OF LADING</div><div style="width:49.5%;display:inline-block;">PORT OF DISCHARGE</div>
                </div>
                <div style="width:100%;display: inline-block;border-bottom:solid 1px black;">
                    <div style="width:50%;display:inline-block;border-right:solid 1px black;"><?=$model->consol->pol?></div>
                    <div style="width:48.6%;display:inline-block;"><?=$model->consol->pod?></div>
                </div>

                <div style="font-size:16px;font-weight:bold;width:100%;display: inline-block;border-bottom:solid 1px black;">
                    <div style="width:50%;display:inline-block;border-right:solid 1px black;">INTENDED VESSEL & VOY</div><div style="width:49.5%;display:inline-block;">TERM</div>
                </div>
                <div style="width:100%;display: inline-block;border-bottom:solid 1px black;">
                    <div style="width:50%;display:inline-block;border-right:solid 1px black;"><?=@$model->consol->mdata['sea_vessel']?>/<?=@$model->consol->flight?></div>
                    <div style="width:48.6%;display:inline-block;"></div>
                </div>
             </div>

                </td>
                <td width="50%">

             <div style="width:100%;height:100%;">
                <div style="margin-bottom: 5px;width:100%;">
                    <table width="100%">
                        <tr>
                            <td style="font-size:16px;font-weight:bold;line-height:24px;width:50%;border-bottom:solid 1px black;border-right:solid 1px black;">
                                BILL OF LANDING NUMBER: &nbsp;
                            </td>
                            <td style="font-size:16px;font-weight:bold;line-height:24px;width:50%;border-bottom:solid 1px black;">
                                <?=$model->hbn?>
                            </td>
                        </tr>
                    </table>
                </div>
               <div style="width:100%;line-height:1em;font-size:3em;text-align: center">
               </br>
                <?php $owner = Org::model()->findByPk(@$model->consol->mdata["owner_id"]);?>
                   <span style="margin-left: 10px;">   <?php echo strtoupper(@$owner->name); ?> </span><br/>
                    <!-- <span style="margin-left: 10px;"><?php echo strtoupper(@$owner->address); ?></span><br/>
                    <span style="margin-left: 10px;"><?php echo strtoupper(@$owner->suburb . ' ' . @$owner->state . ' ' . @$owner->postcode.' ' . @$owner->country); ?> </span> <br/> -->
               </div>
             </div>

                </td>
            </tr>
            </table>
         </div>

         <div>
             <table border="1" cellspacing="0" cellpadding="0" style="border-right:none" class="item_table">
                 <tr><th width="20%">MARK & NUMBERS</th><th width="15%">QTY & TYPE OF PKG</th><th width="45%">DESCRIPTION OF GOODS</th><th width="10%">G.W （KGS）</th><th width="10%" style='border-right:none'>Measurement （CBM）</th></tr>
                 <?php
                 $j = 0;
                 $itemName = join("</br>",$model->eitems['g']);
                    echo "<tr>"."<td>1</td>"."<td>".$model->pkg."</td>"."<td>".$itemName."</td>"."<td>".$model->weight."</td>"."<td style='border-right:none'>".$model->getTotalCBM()."</td>"."</tr>";
                    for ($i=0; $i < 9; $i++) { 
                       echo  "<tr><td>&nbsp;</td><td></td><td></td><td></td><td style='border-right:none'></td></tr>";
                    }
                 ?>

                 <tr><td colspan="2">SHIPPED ON BOARD DATE: <?=@$model->consol->etd?></td><td></td><td></td><td></td></tr>
                 <tr><td colspan="2">CONTAINER NO.: <?=@$model->consol->mdata['container_no']?></td><td></td><td></td><td></td></tr>
                 <tr></tr>
             </table>
         </div>

         <div style="border-bottom:solid 1px black;width: 100%;">
            <div style="width: 50%;margin-top: 30px;margin-bottom: 10px;">Consignee signature:</div><div style="width: 50%;">Date of receiving:</div>
         </div>
    </div>

</footer>
<?php include('_pagination.php'); ?>
</body>
</html>
