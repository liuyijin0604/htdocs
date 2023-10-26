<br/>
<div style="border: 0px #000 solid;">
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size: 2em;margin-top: -1em;">
	<tr>
    <td colspan="2" style="text-align: left">
      TO: AU-澳大利亚
    </td>
  </tr>
    <tr>
    <td colspan="2" style="text-align: left">
      客户编码: TLA
    </td>
  </tr>
    <tr>
    <td colspan="2" style="text-align: left">
     客户单号: <?=$model->tlano?>
    </td>
  </tr>
    <tr>
    <td colspan="2" style="text-align: left">
     渠道名称: <?=ImportZwStorage::$transportTypeShowsLabel[$model->channel_code]?>
    </td>
  </tr>
    <tr>
    <td colspan="2" style="text-align: left">
       渠道代码: <?=$model->channel_code."/".$api->fromInfo['postcode']?>
    </td>
  </tr>

  <tr>
    <td colspan="2" style="text-align: center;border-top: 1px solid #000;">
      <p style="font-size: 1em;margin-bottom: -2em;"><?php echo $model->putcode;?></p>
  </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center;">
    </br>
      <p><?php
         $bc = new TCPDFBarcode($model->putcode, 'C128');
    echo $bc->getBarcodeSVGcode(3, 170);
      ?></p>
  </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center;border-top: 1px solid #000;">
      <p style="font-size: 1em;margin-bottom: -1em;"><?php echo $model->putcode.'-'.sprintf('%03d', $pkg_sn);?></p>
  </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center;border-bottom: 1px solid #000;">
    </br>
      <p><?php
      	 $bc = new TCPDFBarcode($model->putcode.'-'.sprintf('%03d', $pkg_sn), 'C128');
		    echo $bc->getBarcodeSVGcode(3, 170);
      ?></p>
	</td>
  </tr>
  <tr style="font-size: 0.8em;">
    <td colspan="1" style="text-align: right;">
      当前件数： <?=$pkg_sn?>
    </td>
    <td colspan="1" style="text-align: right;">
     
    </td>
  </tr>
  <tr style="font-size: 0.8em;">
    <td colspan="1" style="text-align: right">
      总件数： <?=$model->goods_count?>
    </td>
    <td colspan="1" style="text-align: right">
     <?=date("Y-m-d H:i:s")?>
    </td>
  </tr>
</table>
</div>
