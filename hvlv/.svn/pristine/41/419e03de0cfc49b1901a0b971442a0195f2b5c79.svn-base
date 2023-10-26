<table style="font-size: 2em;line-height: 1.3em;">
<tr><td style="border-right: none;" width="520"><img src="<?=$burl?>/images/sino_logo.png" width="500" /></td><td style="border-left: none;font-size:1.2em">中外运空运</td></tr>
<tr><td colspan="2">主单号: <b><?=$model->awb;?>  &nbsp; &nbsp; <?=$pc.'/'.$tplt;?> &nbsp; &nbsp;  <?=$model->poc=='CNXMN'? 'BC':'XY';?></b></td></tr>
<tr><td colspan="2">托盘内含件数：<b><?=$c;?></b><br /><b><?=$model->no;?></b></td></tr>
<tr><td colspan="2">备注：<b>转厦门跨境电商产业园<br />(快件)</b></td></tr>
</table>
<p>
<?php
echo 'P#'.$m->ref.' &nbsp; Count: '.$c;
if(!empty($m->mdata['mvfrom'])){
	$oc = ExcoConsol::model()->findByPk($m->mdata['mvfrom']);
	if(!empty($oc)) echo ' ('.$oc->no.' - P#'.$m->mdata['opltno'].')';
}
?>
</p>