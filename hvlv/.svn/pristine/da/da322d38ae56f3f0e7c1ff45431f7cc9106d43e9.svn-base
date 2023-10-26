<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking 
 $companyName = "Top Logistics";
if($model->shipment->isTLA())
{
  $companyName = Org::IM_COMPANY_NAME;
}
?>
<title>LOA</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
table.head{font-size: 22px; line-height:40px;}
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
header { padding-bottom: 15px; }
div.container{
    padding-left: 50px;
    padding-right:50px;
     font-size: 20px; line-height: 35px;
}
p.sig{
    font-size: 20px;line-height: 50px;
    font-weight: 600;
}
.line{
    text-decoration: underline;
}
span.input_field{
    border-bottom: 1px solid black;
    min-width: 200px;
    display: inline-block;
    font-weight: bold;
}
span.company_pca{
     font-weight: bold;
     color:blue;
}
</style>
</head>
<body width="1120">
    <div style="height: 100px;"></div>  
    <table width="100%" cellspacing="0" cellpadding="0" class="head">
  <tbody>
    <tr>
      <td style="text-align: center; font-size: 28px; font-weight: bold;padding: 10px;" colspan="2">CLIENT’S LETTER HEAD</td>
    </tr>
</table>
     <div style=" border-bottom: 2px solid black; width: 80%;margin: auto; padding-top: 25px;"></div>
     <div style="text-align: center; font-size: 30px; font-weight: bold;padding: 10px; margin: auto; padding-top: 25px;" ><span style="border-bottom:2px solid black;">AUTHORITY LETTER</span></div>
    <div style="height: 25px;"></div>
    <div style="padding-top: 50px; text-align: justify;" class="container">
        <p>We, (<span>COMPANY NAME: </span><span class="input_field"><?=$model->mdata['company_name']?></span>), hereby authorize <span class="company_pca"><?=$companyName?>,</span> their subcontract brokers or nominees, to act on our behalf, in the customs clearance of air and sea freight shipments, pursuant to the requirements of Section 181(1) of the Customs Act 1901, as amended.</p>
        <div style="height:15px;"></div>
        <p>We further authorize <span class="company_pca"><?=$companyName?>,</span> to make customs and tax declarations on our behalf and to quote our Australian Business Number (<span >ABN NUMBER:</span><span class="input_field"><?=$model->mdata['abn_number']?></span>) (Leave blank for overseas or individual consignee) on all goods, unless otherwise indicated. </p>
        <div style="height:15px;"></div>
        <p>It is also agreed that <span class="company_pca"> <?=$companyName?>,</span> their sub contract brokers or nominees are authorized to act as a principal for (<span>COMPANY NAME: </span><span class="input_field"><?=$model->mdata['company_name']?></span>) in accordance with Section 153-50 of A New Tax System (Goods and Services Tax) Act 1999, as amended, and to make supplies or acquisitions to or from third parties in respect of the supply of services relating to the transportation, customs clearance and delivery of goods from an overseas supplier.      </p>
        <div style="height:15px;"></div>
        <p>In consideration of their acting as our customs brokers, we hereby indemnify them against any claims or demands made against them or arising from any customs or tax declarations made by them on our behalf.</p>
        <div style="height:15px;"></div>
        <p>To expedite clearance and delivery of our shipments, please advise <span class="company_pca"><?=$companyName?>,</span> immediately after arrival of our shipments, and release any documents to them, as required.</p>
        <div style="height:15px;"></div>
        <p>We, (<span>COMPANY NAME: </span><span class="input_field"><?=$model->mdata['company_name']?></span>), agree to pay to <?=$companyName?> for all Duty, GST, Custom Clearance, Service and Storage Charges. <?=$companyName?> reserves rights to hold goods if any of the fees above are not fully paid.</p>
        <div style="height:15px;"></div>
        <p>This authority cancels and supersedes all previous authorities, unless specifically indicated.</p>
        <br/>
        <p>Yours faithfully</p>
        <br/>
         <p class="sig">Name: <?=isset($model->mdata['loa_client_name'])?$model->mdata['loa_client_name']:''?></p>
         <p class="sig">Company: <?=isset($model->mdata['company_name'])?$model->mdata['company_name']:''?></p>
         <p class="sig">Position: <?=isset($model->mdata['loa_position'])?$model->mdata['loa_position']:''?></p>
          <p class="sig">Date: <?=isset($model->mdata['sig_date'])?$model->mdata['sig_date']:date('Y-m-d')?></p>
         <p class="sig" style="line-height:150px;">Signature:<img src="<?=isset($model->mdata['loa_signature'])?$model->mdata['loa_signature']:''?>" /></p>
</div>
</body>
</html>
