<br/>
<div style="border: 0px #000 solid;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    <td width="40%" height="200px" rowspan="2" style="border: 1px #000 dashed;">
      <img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl.'/images/tla_logo.png';?>" alt="PCA Express" width="360" height="130"  />
    </td>
    <td  width="60%" height="100px" style="text-align: left;font-size: 2em;border: 1px #000 dashed;">
    	智慧集运
	</td>
  </tr>
  <tr>
    <td  width="60%" height="100px" style="text-align: right;font-size: 2em;border: 1px #000 dashed;">
    	共享仓储
	</td>
  </tr>
  <tr >
    <td colspan="2" style="text-align: center">
      <p style="font-size: 3em;line-height: 2em;"><?=$model->putcode?></p>
	</td>
  </tr>
 <!--  <tr>
    <td width="55%" style="border-top:4px #000 solid;">
      <p style="font-size: 1.7em;">渠道AU海运到港(普货</p>
	</td>
	<td width="45%" style="border-top:4px #000 solid;font-size: 1.5em;">
		<p>目的地：<?=$model->arrival_city=='Sydney'?'悉尼':'墨尔本';?></p>
	</td>
  </tr>
  <tr>
    <td width="55%" >
      <p style="font-size: 1.7em;">报关类型: 不需要报关</p>
	</td>
	<td width="45%" >
		<p style="font-size: 1.2em;">件数(Item): <?=$pkg_sn?>/<?=$model->goods_count?></p>
	</td>
  </tr> -->
  <tr>
    <td colspan="2" style="text-align: center;">
    </br>
      <p><?php
      	 $bc = new TCPDFBarcode($model->putcode.'-'.$pkg_sn, 'C128');
		echo $bc->getBarcodeSVGcode(3, 170);
      ?></p>
	</td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center">
      <p style="font-size: 2em"><?php echo $model->putcode.'-'.$pkg_sn;?></p>
	</td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center">
      <p style="font-size: 2em"><?=$pkg_sn?>/<?=$model->goods_count?></p>
	</td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center">
      <p style="font-size: 2em">Made in China</p>
	</td>
  </tr>
  <!-- <tr>
    <td colspan="2" style="text-align: center;font-size: 0.7em">
      <p>Declaration : This label is the goods classification information of the place of shipment and cannot be used as the basis for customs clearance. Please note</p>
	</td>
  </tr> -->
</table>
</div>
