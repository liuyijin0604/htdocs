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
<?php include('_header_tla.php');?>
      <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 80px;height: 50px; ">
       <tr>
           <td style="text-align: center; font-size: 45px;font-weight: bold;padding-bottom:10px;" width="100%">Cargo Receipt</td>
      </tr>
</table>
</header>
        <div style="font-size:30px;line-height: 60px;margin-top: 50px;">
        <div>
         Delivery to:<br/><i> <p style="font-size:25px;">
            <?php if($model->dpt_id ==Org::TLA_DEPARTMENT_MELBOURNE):?>
                1/414 Lower Dandenong Rd, Braeside, VIC 3195
            <?php else:?>
                4PX: Block G2, 391 Park Road, Regents Park, NSW 2143
            <?php endif;?>
            </p></i>
      </div>
    <?php if((isset($image)&&is_array($image))||!isset($image)):?>
      <div>
          <p>Receiver(Pls Print Full Names): 
            <span style="display: inline-block;width: 500px; border-bottom: 2px solid black">
              <?php if(isset($image)&&is_array($image))
              {
                 echo $image['sprint'];
              }
              ?>
          </span>
          </p>
      </div>
      <div>
          <p>Signature: 
            <?php if(isset($image)&&is_array($image)):?>
              <img src="<?=$image['sig']?>" style="display: inline-block;width: 300px;height:100px;  border-bottom: 2px solid black"></img>;
           </p>

            <?php else:?>
               <span style="display: inline-block;width:500px; border-bottom: 2px solid black"></span></p>
           <?php endif;?>
      </div>
        <div>
          <p>Date: 
          <span style="display: inline-block;width: 500px; border-bottom: 2px solid black">
            
              <?php if(isset($image)&&is_array($image))
              {
                 echo $image['sdate'];
              }
              ?>

          </span></p>
      </div>
    <?php endif;?>
    <?php if(isset($image)&&!is_array($image)):?>
            <div>
               <?= CHtml::image(Yii::app()->request->hostInfo.Yii::app()->baseUrl."/filerepo/".$image->hash."/".$image->name,$image->name,['width'=>'900']) ?>
            </div>
    <?php endif;?>

    <div>
    <div style="font-size:25px;margin-top: 25px; text-align: justify;margin-bottom: 25px;">
        <p>This is to confirm that we have received the delivery of below consignment in full and good condition:</p>
    </div>
    <table width="100%" cellspacing="0" class="chart">
        <tr class="even">
            <th width="25%" align="center"><?=@$model->service==10?"MAWB":"Container No.";?></th><th width="25%" align="center">Pallets</th><th width="25%">Pieces</th><th width="25%">Note</th>
        </tr >
        <tr  >
            <td  align="center" style="padding:8px;">
                    <?=@$model->service==10?$model->awb:@$model->mdata["container_no"];?>
            </td>
            <td align="center" style="padding:8px;">
               <?=@$model->mdata['cargo_receipt_pallets'];?>
            </td>
            <td align="center" style="padding:8px;">
               <?=@$model->mdata['cargo_receipt_pieces'];?>
            </td>
           <td align="center" style="padding:8px;">
               
            </td>
        </tr>
           <tr  >
            <td  align="center" style="padding:8px;">
                &nbsp;
            </td>
            <td align="center" style="padding:8px;">
               &nbsp;
            </td>
            <td align="center" style="padding:8px;">
               &nbsp;
            </td>
           <td align="center" style="padding:8px;">
               &nbsp;
            </td>
        </tr>
           <tr  >
            <td  align="center" style="padding:8px;">
                &nbsp;
            </td>
            <td align="center" style="padding:8px;">
               &nbsp;
            </td>
            <td align="center" style="padding:8px;">
               &nbsp;
            </td>
           <td align="center" style="padding:8px;">
               &nbsp;
            </td>
        </tr>
           <tr  >
            <td  align="center" style="padding:8px;">
               &nbsp;
            </td>
            <td align="center" style="padding:8px;">
               &nbsp;
            </td>
            <td align="center" style="padding:8px;">
               &nbsp;
            </td>
           <td align="center" style="padding:8px;">
               &nbsp;
            </td>
        </tr>
          
 </table>
      </br>
      <div>
          <p style="font-weight: bold;">Driver: 
          <span style="display: inline-block;width: 300px; border-bottom: 2px solid black">

          </span></p>
      </div>

<?php include('_pagination.php'); ?>
</body>
</html>
