<div style="font-size: 3em; line-height: 1.6em; font-weight: bold; margin-bottom: 35px;">
<p style="margin-bottom: 20px">MAWB: <?=$model->awb;?></p>
<p><?=substr($model->pol, 2).'-'.substr($model->pod, 2);?></p>
<p><?=$model->no;?></p>
<p>PLT: <?=$pc.'/'.$tplt;?></p>
</div>
<p style="font-size: 1.5em;margin-top:1.5em;">
<?php
echo 'P#'.$m->ref.' &nbsp; PCS: '.$c;
if(!empty($m->mdata['mvfrom'])){
	$oc = ExcoConsol::model()->findByPk($m->mdata['mvfrom']);
	if(!empty($oc)) echo ' &nbsp; &nbsp; ('.$oc->no.' - P#'.$m->mdata['opltno'].')';
}
?>
</p>