<?php
$rs = Product::model()->findAll('status = 20 ORDER BY name');
foreach($rs as $r){
	if(empty($r->mdata['retailer'][$ret])) continue;
	echo '<p><label>', CHtml::CheckBox('p[]', true, array('value' => $r->id)), ' ', $r->mdata['retname'][$ret],'(', $r->sn,')','</label></p>';
}