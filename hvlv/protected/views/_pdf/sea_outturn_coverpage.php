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
span{font-size:20px;}
</style>
</head>
<body width="1120" >
<header>
<?php 
  $hdr = '_header_tla.php';
  $companyName = Org::IM_COMPANY_NAME;  
  $companyEmail = Org::IM_EMAIL;
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
}
include($hdr);
?>


      <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 30px;height: 50px; ">
       <tr>
           <td style="text-align: left; font-size: 30px;font-weight: bold;padding-bottom:10px;" width="70%">Outturn Cover Sheet</td>
            <td style="text-align: left; font-size: 30px;font-weight: bold;padding-bottom:10px;" width="10%">Date: </td>
             <td style="text-align: left; font-size: 16px;padding-bottom:10px;" width="20%"><?=date('d-M-Y')?> </td>
      </tr>
</table>
</header>
    <table  width="100%" cellpadding="0" cellspacing="0"  height="100px;" >
        <tr>
            <td  width="20%" valign="top" style="text-align: left;padding-bottom:30px;font-size: 20px;" >
              <p style="border:1px solid #000;background-color:#f5f5f5;">
                ATTENTION
              </p>
            </td>
            <td  width="80%" valign="top" style="padding-left:30px;" >



                    <?php if(empty($shipment)):?>
                        <?php if(!empty($model->mdata['owner_id'])):?>
                            <?= Org::model()->findByPk($model->mdata['owner_id'])->name?><br/>

                         <?php else:?>
                            <?=$model->shipments[0]->agent->name?>
                        <?php endif;?>

                    <?php else:?>
                    <?=$shipment->cnee->name?>
                    <?php endif;?>

            </td>
        </tr>
         <tr>
            <td  width="20%" valign="top" style="text-align: left;padding-bottom:30px;font-size: 20px;" >
              <p style="border:1px solid #000;background-color:#f5f5f5;">
                EMAIL ADDRESS
              </p>
            </td>
            <td  width="80%" valign="top" style="padding-left:30px;" >
              <?php if(empty($shipment)):?>
                <?=$model->shipments[0]->agent->extra['outturn_email']?>
                <?php else:?>
                <?=$shipment->cnee->email?>
              <?php endif;?><br/>
            </td>
        </tr>
         <tr>
            <td  width="20%" valign="top" style="text-align: left;padding-bottom:30px;font-size: 20px;" >
              <p style="border:1px solid #000;background-color:#f5f5f5;">
                FROM 
              </p>
            </td>
            <td  width="80%" valign="top" style="padding-left:30px;" >
              <?=$companyName?>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="background-color:#000000;color:#FFFFFF">
               <span >Important Pick-up Instructions:</span>
           </td>
       </tr>
       <tr>
        <td colspan="2" style="line-height:40px;">
          <br/>
            <span >Dear Valued Customer,</span><span ><o:p></o:p></span></p>
            <p class=3DMsoNormal   ><span >&nbsp;</span><span ><o:p></o:p></span></p>
            <p class=3DMsoNormal   ><span  >We&#8217;d</span>
              <span >&nbsp;like to request your attention to our below important notes of pickups from our warehouse before organizing this. &nbsp;Failure to obey any of below may result in your futile pick-up and <?=$companyName?> won</span>
              <span  >&#8217;</span><span >t be </span>
              <span  >responsible</span><span >&nbsp;for this or any extra charges.</span><span ><o:p></o:p></span></p>
              <p class=3DMsoNormal   ><span >&nbsp;</span><span ><o:p></o:p></span></p>
              <p class=3D24   ><![if !supportLists]><span  >1.<span>&nbsp;</span></span></span><![endif]><b><u><span  >Booking is essential</span></u></b><span >:</span><span ><o:p></o:p></span></p>
              <p class=3DMsoNormal  >

                <![if !supportLists]><span style="font-family:Wingdings;mso-fareast-font-family:DengXian;mso-bidi-font-family:Calibri;
color:rgb(0,0,0);font-size:11.0000pt;mso-font-kerning:0.0000pt;" ><span style='mso-list:Ignore;' >&#108;<span>&nbsp;</span></span></span><![endif]>



  <span >
    All freight being collected from TLA will require a 'Pick Up Booking' transaction to be completed by the Transport Operator ( "TO" here after) through <span style="font-weight: bold;">www.toplogistics.com.au/booking </span> prior to the vehicle arriving at the facility.
  </span>
  </p>
  
  <p class=3DMsoNormal  >
                <![if !supportLists]><span style="font-family:Wingdings;mso-fareast-font-family:DengXian;mso-bidi-font-family:Calibri;
color:rgb(0,0,0);font-size:11.0000pt;mso-font-kerning:0.0000pt;" ><span style='mso-list:Ignore;' >&#108;<span>&nbsp;</span></span></span><![endif]>

  <span >
   One booking number per lowest House Bill Of Lading. 
  </span>
  </p>

  <p class=3DMsoNormal  >

                <![if !supportLists]><span style="font-family:Wingdings;mso-fareast-font-family:DengXian;mso-bidi-font-family:Calibri;
color:rgb(0,0,0);font-size:11.0000pt;mso-font-kerning:0.0000pt;" ><span style='mso-list:Ignore;' >&#108;<span>&nbsp;</span></span></span><![endif]>



  <span >
   All 'Bookings' will require the vehicle registration number to be recorded by the TO prior to the vehicle arriving at the facility.
  </span>
  </p>

  <p class=3DMsoNormal  >

                <![if !supportLists]><span style="font-family:Wingdings;mso-fareast-font-family:DengXian;mso-bidi-font-family:Calibri;
color:rgb(0,0,0);font-size:11.0000pt;mso-font-kerning:0.0000pt;" ><span style='mso-list:Ignore;' >&#108;<span>&nbsp;</span></span></span><![endif]>



  <span >
  Wrapped pallets will not be broken down.
  </span>
  </p>

  <p class=3DMsoNormal  >

                <![if !supportLists]><span style="font-family:Wingdings;mso-fareast-font-family:DengXian;mso-bidi-font-family:Calibri;
color:rgb(0,0,0);font-size:11.0000pt;mso-font-kerning:0.0000pt;" ><span style='mso-list:Ignore;' >&#108;<span>&nbsp;</span></span></span><![endif]>



  <span style="font-weight: bold;" >
  CHEP pallets will be required for exchange for all wrapped pallets.
  </span>
  </p>

    <p class=3DMsoNormal  >

  For urgent pickup, please send you email to <a href="mailto:imports@toplogistics.com.au" ><u><span class="16"  style="mso-spacerun:'yes';font-family:Verdana;mso-fareast-font-family:DengXian;
color:rgb(5,99,193);text-decoration:underline;text-underline:single;
font-size:11.000pt;mso-font-kerning:0.0000pt;" >imports@toplogistics.com.au</span></u></a>, or please ring 02-90668207(10:00AM-5:30PM AEST)
  </span>
  </p>





              <br/>

              <p class=3D24   ><![if !supportLists]><span >
              <br/>

              2.<span>&nbsp;</span></span></span><![endif]><span >Our warehouse opening hours are:</span><span ><o:p></o:p></span></p>
              <p class=3DMsoNormal ><span >Bankstown Aerodrome, Sydney: &nbsp;9:00am-5:30pm</span><span ><o:p></o:p></span></p>
              <p class=3DMsoNormal ><span >Sunshine, Melbourne: 9:00am-5:30pm</span><span ><o:p></o:p></span></p>
              <p class=3DMsoNormal ><span >Coopers Plains, Brisbane：9:30 AM - 5:30 PM</span><span ><o:p></o:p></span></p>
              <p class=3DMsoNormal><span  >&nbsp;</span><span  ><o:p></o:p></span></p>
              <p class=3DMsoNormal ><span  >Thank you for your </span><span  >understanding</span><span  >&nbsp;and support.</span><span  ><o:p></o:p></span></p>
              <p class=3DMsoNormal   ><span  >Regards,</span><span  ><o:p></o:p></span></p>
              <p class=3DMsoNormal ><span  >&nbsp;</span><span  ><o:p></o:p></span></p><p class=3DMsoNormal ><span  ><?=$companyName?></span><span  ></span><span  >&nbsp;Imports Team</span><span ><o:p></o:p></span></p>
            </td>
        </tr>
    </table>

<?php include('_pagination.php'); ?>
</body>
</html>
