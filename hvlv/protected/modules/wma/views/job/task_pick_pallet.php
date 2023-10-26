<div class="data-form">
<?php

if(empty($plt)){
	$done = false;
	$data = '';
}else{
	
}
if(empty($plt)){
	echo '<span style="color: #c00;">Pallet not found!</span>';
}else{
	$sl = WmsStockLocation::model()->findAll('location_id = :l AND qty > 0', [':l' => $plt->id]);
	// if(sizeof($sl) > 1){
	// 	echo '<span style="color: #c00;">Pallet has mixed content!</span><br />';
	// }

	// //product info
	// $st = $sl[0]->stock;
	// $si = [];
	// foreach($model->mainTask->items as $itm){
	// 	if(empty($itm->mdata['pq']) && empty($itm->mdata['uq'])) continue;
	// 	if(!isset($si[$itm->mdata['si']])) $si[$itm->mdata['si']] = 0;
	// 	$si[$itm->mdata['si']] += floatval($itm->mdata['pq']);
	// }
	// $psi = [];
	// foreach($model->items as $itm){
	// 	if(empty($itm->mdata['pq']) && empty($itm->mdata['uq'])) continue;
	// 	if(!isset($psi[$itm->mdata['si']])) $psi[$itm->mdata['si']] = 0;
	// 	$psi[$itm->mdata['si']] += floatval($itm->mdata['pq']);
	// }

	// $match = false;
	foreach($sl as $l){
		$st = $l->stock;
		$pik = '';
		if(isset($si[$st->id])){
			$pik = (empty($psi[$st->id])? 0 : $psi[$st->id]). '/'. $si[$st->id]. ' Pallets picked';
			$match = true;
		}

		echo '<div class="table-view-cell table-view-cell-full">', $st->prod->name, ' (', sprintf('%d', $l->qty),')<span class="icon icon-info"></span><br />', $pik, '<p><i>', $st->prod->ean,'</i><br />Exp: ', $st->expiry,' Bat: ', $st->batch, '</p>';
		$more = $st->prod->brand.' '.$st->prod->model.'<br />';
		foreach($st->prod->packs as $k){
			if($k->type != 20) continue;
			$more .= sprintf('%d', $k->qty). '/'.$k->getType().'<br />';
		}
		echo '<div class="more">', $more, '</div></div>';
	}

	$match = false;
	foreach ($model->mainTask->items as $itm) {
		if (empty($itm->mdata['pl'])) {
			continue;
		}
		if ($itm->mdata['pl'] == $plt->name) {
			$match = true;
		}
	}


if(!$match){
	echo '<span style="color: #c00;">Stock not in this picking task!</span>';
}else{
?>
<form action="<?=$this->createUrl('job/entry', ['id' => $model->id]);?>" method="post">
	<textarea placeholder="Notes" name="meta[nt]" rows="2"></textarea>
	<input type="hidden" name="meta[pli]" value="<?=$plt->id;?>" />
	<button type="submit" class="btn btn-primary btn-block">Save</button>
</form>
<?php }} ?>
</div>

<script type="text/javascript">
$(function(){
	$('.data-form form').on('success', function(e, r){
		$('#ajax-modal').trigger('loadModal');
	});
});
</script>