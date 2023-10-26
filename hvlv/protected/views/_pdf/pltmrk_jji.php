<table style="font-size: 2em;line-height: 1.2em;">
<tr><td style="border-right: none;" width="370"><img src="<?=$burl?>/images/yse_logo.png" width="340" height="200" /></td><td style="border-left: none;padding-right: 60px;">厦门雅顺达国际物流有限公司</td></tr>
<tr><td colspan="2">主单号: <b><?=$model->awb;?>  &nbsp; &nbsp; <?=$pc.'/'.$tplt;?></b></td></tr>
<tr><td colspan="2">托盘内含件数：<b><?=$c;?></b><br /><b><?=$model->no;?></b></td></tr>
<tr><td colspan="2">备注：<b>转晋江陆地港快件中心<br />(快件)</b></td></tr>
</table>
<p style="page-break-after: always;">
<?php
echo 'P#'.$m->ref.' &nbsp; Count: '.$c;
if(!empty($m->mdata['mvfrom'])){
	$oc = ExcoConsol::model()->findByPk($m->mdata['mvfrom']);
	if(!empty($oc)) echo ' ('.$oc->no.' - P#'.$m->mdata['opltno'].')';
}
?>
</p>