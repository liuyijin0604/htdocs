<ul class="table-view lazy-load">
<?php if(empty($dp->data)): ?>
	<li class="table-view-cell">No Result</li>
<?php
else:
if(!empty($plts)){
$pc = count($plts);
echo '<li class="table-view-cell table-view-cell-full">';
if($pc > 1){
	echo 'Pallets count: <b>'.$pc.'</b><br />';
}
foreach($plts as $plt){
	echo $plt->code.'<br />';
}
echo '</li>';
}
foreach($dp->data as $r):
?>
	<li class="table-view-cell table-view-cell-full">
	<?php
	$ls = '<div class="row"><div class="col-6">Location</div><div class="col-2">Qty</div><div class="col-4"><small>Updated</small></div></div>';
	$tqty = $r->qty;
	foreach($r->locs as $p){
		if($loc){
			if(($loc->id == $p->loc->id || $loc->id == $p->loc->pid)){
				$tqty = $p->qty;
			}else{
				continue;
			}
		}
		$ls .= '<div class="row"><div class="col-6">'. $p->loc->name. (empty($p->loc->pid)? '' : '<br />'.$p->loc->parent->name).'</div><div class="col-2">'. sprintf('%d', $p->qty). '</div><div class="col-4"><small>'. substr($p->updated,0,-3). '</small></div></div>';
	}
	$ls .= empty($ls)? '' : '</div>';

	echo $r->prod->name, ' (Qty: ', sprintf('%d', $tqty), ')<p>',$r->customer->name,'<br />Exp: ',$r->expiry,' &nbsp; Batch: ', $r->batch, (empty($loc) && $r->qty_res > 0? '<br />Reserved: '.sprintf('%d', $r->qty_res) : ''),'<div class="more">
	', $ls, '</p>';
	?>
	</li>
<?php
endforeach;
endif;
?>
</ul>
