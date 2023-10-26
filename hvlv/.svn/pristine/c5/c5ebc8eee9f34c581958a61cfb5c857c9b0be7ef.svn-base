<div class="data-form">
<?php
if(empty($plt)){
	echo '<span style="color: #c00;">Pallet not found!</span>';
}else{
	$orgs = [];
	$ss = WmsStock::model()->with('locs')->findAll('locs.location_id = :lid', [':lid' => $plt->location_id]);
	foreach ($ss as $s) {
		if (!in_array($s->customer->name, $orgs)) {
			$orgs[] = $s->customer->name;
		}
	}
	if (count($orgs) == 1 || count($orgs) == count(array_merge([$model->job->org_id], empty($model->mainTask->mdata['other_org']) ? [] : $model->mainTask->mdata['other_org']))) {
		echo '<div class="table-view-cell table-view-cell-full">', $plt->stock->prod->name, ' (', sprintf('%d', $plt->qty),')<p><i>', $plt->stock->prod->ean,'</i><br />Exp: ', $plt->stock->expiry,' Bat: ', $plt->stock->batch, '</p></div>';
	?>
	<form action="<?=$this->createUrl('job/entry', ['id' => $model->id]);?>" method="post">
		<textarea placeholder="Notes" name="meta[nt]" rows="2"></textarea>
		<input type="hidden" name="meta[pq]" value="1" />
		<input type="hidden" name="meta[pli]" value="<?=$plt->location_id;?>" />
		<input type="hidden" name="meta[pl]" value="<?=$plt->loc->code;?>" />
		<button type="submit" class="btn btn-primary btn-block">Save</button>
	</form>
	<?php } else {
		echo '<span style="color: #c00;">Pallet mix customers, cannot be picked!</span>';
	}
} ?>
</div>