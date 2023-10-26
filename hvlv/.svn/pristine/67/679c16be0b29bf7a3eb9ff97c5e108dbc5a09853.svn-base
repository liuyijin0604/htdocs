<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>LOA</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
table.head{font-size: 22px; line-height:40px;}
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
header { padding-bottom: 15px; }

p.the_body{
    font-size: 35px; line-height: 40px;
}
p.sig{
    font-size: 20px;line-height: 30px;
}
.line{
    text-decoration: underline;
}

</style>
</head>
<body width="1120">
    <div style="padding:50px;">
    <div style="height: 100px;"></div>  
    <table width="100%" cellspacing="0" cellpadding="0" class="head">
  <tbody>
      <tr>
          <td>COMPANY NAME:<span class="line"><?=isset($model->mdata['company_name'])?$model->mdata['company_name']:''?></span></td>
      </tr>
      <tr>
          <td>ADDRESSS:<span class="line"><?=isset($model->mdata['company_address'])?$model->mdata['company_address']:''?></span></td>
      </tr>
       <tr>
           <td>TELEPHONE No.:<span class="line"><?=isset($model->mdata['tel_number'])?$model->mdata['tel_number']:''?></span></td>
      </tr>
       <tr>
           <td>EMAIL:<span class="line"><?=isset($model->mdata['loa_email'])?$model->mdata['loa_email']:''?></span></td>
      </tr>
</td>
      </tr>
    <tr>
      <td style="text-align: center; font-size: 35px; font-weight: bold;padding: 10px; text-decoration: underline; text-decoration-color: red;" colspan="2">Customs Clearance & Freight Authorization Letter</td>
    </tr>
</table>
    <div style="height: 50px;"></div>
    <div style="height: 20px;">
        <p class="the_body">To Whom It May Concern,</p>
    </div>
     <div style="padding-top: 50px;">
         <p class="the_body">This is our authority (ABN:<span class="line"><?=isset($model->mdata['abn_number'])?$model->mdata['abn_number']:''?></span>) for all documents and freight to be passed onto Top Logistics or their nominated agent/handler, including freight collection, delivery, Customs clearance and etc.</p>
    </div>
    <div style="height: 350px;">
        
    </div>
<footer>
<p class="sig">Yours Faithfully,</p>
<p class="sig" style="line-height:150px;">Signature:<img src="<?=isset($model->mdata['loa_signature'])?$model->mdata['loa_signature']:''?>" /></p>
<p class="sig">Signed by:<?=isset($model->mdata['loa_client_name'])?$model->mdata['loa_client_name']:''?></p>
<p class="sig">Position:<?=isset($model->mdata['loa_position'])?$model->mdata['loa_position']:''?></p>
</footer>
</div>
</body>
</html>
