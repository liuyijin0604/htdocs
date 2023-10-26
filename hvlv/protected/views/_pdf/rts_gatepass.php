<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--footer-center 'Page [page] of [toPage]' --footer-font-size 10 --footer-font-name 'Verdana' --dpi 150 -T 10 -R 10 -B 10 -L 10 -O Portrait" />
<?php //--disable-smart-shrinking ?>
<title>PCA Express RTS GatePass</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Verdana, Geneva, sans-serif; font-size: 18px; text-rendering: optimize-speed; width: 1120px; }
table.chart td, table.chart th{ border: 1px #999 solid; padding: 2px; border-right:none; border-bottom: none; }
table.chart{ border: none; border: 1px #999 solid; border-top: none; border-left: none; }
tr.even td, tr.even th{ background: rgba(200, 200, 200, 0.6); }
header { padding-bottom: 15px; }
footer { padding-top: 10px; page-break-after: always; }
</style>
</head>

<body width="1120">
<header>
<?php 
$hdr = '_header_tla.php';
include($hdr);
?>
<table width="100%" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
        <?php if ( isset($rtc) && $rtc ) :  ?>
            <td style="text-align: center; font-size: 28px; font-weight: bold;padding: 10px;" colspan="2">RTC Gate Pass</td>
        <?php else : ?>
            <td style="text-align: center; font-size: 28px; font-weight: bold;padding: 10px;" colspan="2">RTS Gate Pass</td>
        <?php endif; ?>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td valign="top" width="50%"><table border="0" cellspacing="0" cellpadding="0">
        <tbody>
          <tr>
            <td valign="top" style="padding-bottom:3px;font-weight: bold;">Picked up by:</td>
          </tr>
          <tr>
            <td valign="top" style="border: 1px #999 solid;padding:10px;" height="80" width="480"><span style="font-size:20px;font-weight:bold;"><?php if ( isset($m->mdata['rts_company']) )  { echo $m->mdata['rts_company'] ;}?></span>
              <br />

               <br/><br/>
             <span><b>Driver Name:</b> _____________________ <span><br/><br/><br/>
             <span><b>       Rego:</b> ____________________________ </span>
            </td>
          </tr>
        </tbody>
      </table></td>
      <td width="50%" align="left" valign="top"><br />
	  <table cellpadding="3" width="540">
        <tbody>
          <tr>
            <td style="font-weight: bold" width="180">Date:</td>
            <td><?=date('Y-m-d H:i', strtotime($m->created));?></td>
          </tr>
          <tr>
            <td style="font-weight: bold">Gate Pass No.:</td>
            <td><?=sprintf('%06d',$m->id);?></td>
          </tr>
          <tr>
            <td style="font-weight: bold">Ref No.:</td>
            <td><?=$m->ref?></td>
          </tr>
        <tr style="height: 160px;"> <td><b>Sign:</b></td><td> ______________________________</td></tr>
        </tbody>
      </table></td>
    </tr>
</table>
</header>
<table width="100%" cellspacing="0" class="chart"><thead>
        <tr style="background: rgba(100,100,100,0.4);">
          <th align="left" width="40">#</th>
          <th align="left">HBN</th>
            <th align="left">Ref #</th>
          <th align="right">Status</th>
          <th align="right">Packs</th>
          <th align="right">Weight</th>
        </tr>
        </thead>
        <tbody>
        <?php
		$qty = 0;
		$wei = 0;
        $i = 0;
 		foreach($m->lines as $i=>$l){
            $r = $l->mm();
			echo '<tr class="'.($i&2 > 0? 'even' : 'odd').'"><td align="center" valign="top">'.($i+1).'</td><td valign="top">'.$r->hbn .'</td><td valign="top">'.$r->ref .'</td><td align="right" valign="top">'.$r->getStatus().'</td><td valign="top" align="right">'.$r->pkg.'</td><td valign="top" align="right">'.$r->weight."</td></tr>";
			$qty += $r->pkg;
			$wei += $r->weight;
		}
		$i++;
		?>
		<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        </tbody>
		<tfoot>
		<tr><td colspan="4">&nbsp;</td><th align="right"><?=AppHelper::qty_format($qty);?></th><th align="right"><?=$wei;?>Kg</th></tr>
		</tfoot>
</table>
<?php include('_pagination.php'); ?>
</body>
</html>
