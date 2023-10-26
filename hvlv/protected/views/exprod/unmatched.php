<h1><?=$this->t('Unmatched Products');?></h1>
<?php
$rs = ExParcel::model()->findAll('status < 100 AND bwf & 8 > 0');
$ums = [];
foreach($rs as $r){
	foreach($r->eitems['g'] as $i => $g){
		if(empty($r->eitems['pid'][$i]) && empty($r->eitems['mp'][$i])){
			if(!isset($ums[$g])) $ums[$g] = [];
			$ums[$g][] = array($r->eitems['type'][$i], $r);
		}
	}
}
?>
<div class="grid-view">
<p align="right">Total: <?=sizeof($ums);?></p>
<table class="items">
<thead>
<tr><th>Goods</th><th>Parcels</th><th>Action</th></tr>
</thead>
<tbody>
<?php
foreach($ums as $g=>$rs){
	$ids = [];
	$hbns = [];
	foreach($rs as $ar){
		$r = $ar[1];
		$hbns[] = '<a href="'.$this->createUrl('exParcel/update', array('id' => $r->id)).'" class="tab_link" title="'.$r->hbn.'">'.$r->hbn.'</a>';
		$ids[] = $r->id;
	}
	echo '<tr><td>', $ar[0],': ', $g, '</td><td>', implode(', ',$hbns), '</td><td><a href="'.$this->createUrl('exprod/create', array('g' => $g, 't' => ExProdb::$rtypes[$ar[0]])).'" class="jqm_link">Add Product</td>';
}
?>
</tbody>
</table>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.off('onOpen').on('onOpen', function(){
		tab.load();
	});
});
</script>