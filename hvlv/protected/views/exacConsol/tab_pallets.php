<div style="text-align:right">
<a href="<?=$this->createUrl('exacConsol/palletRpt', array('id'=>$model->id));?>" target="_blank"><div style="background-position:-96px -768px" class="icon"></div> Pallets Report</a>
</div>
<?php
$rs = Manifest::model()->findAll('type = 60 AND consol_id = :cid', [':cid' => $model->id]);
if(!empty($rs)){
	$tc = 0;
	$tw = 0;
	$sids = [];
	echo '<ul id="oth_pallets" class="sortables" style="list-style:none;">';
	foreach($rs as $m){
		$w = 0;
		$ids = empty($m)? [] : $m->getFids();
		if(!empty($ids)){
			$sql = 'SELECT SUM(weight) FROM shipment WHERE id IN('.implode(',', $ids).')';
			$w = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		$c = sizeof($ids);
		if(empty($c)) continue;
		echo '<li data-mid="'.$m->id.'">Pallet #'.$m->ref.' &nbsp; Count: '.$c.' Weight: '.(round($w*100)/100).'kg</li>';
		$sids = array_merge($sids, $ids);
		$tc += $c;
		$tw += $w;
	}
	echo '</ul>';
	echo '<p><b>Total: '.$tc.' shipments, '.$tw.'kg</b></p>';
}

?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	//bind reload_tab
	tab.off('reload_tab').on('reload_tab', function(){
		var t = $('.ui-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	});
});
</script>