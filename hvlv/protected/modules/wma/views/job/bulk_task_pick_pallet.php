<div class="data-form">
	<?php
	if (empty($plt)) {
		echo '<span style="color: #c00;">Pallet not found!</span>';
	} else {
		$sl = WmsStockLocation::model()->findAll('location_id = :l', [':l' => $plt->id]);
		// if (sizeof($sl) > 1) {
		// 	echo '<span style="color: #c00;">Pallet has mixed content!</span><br />';
		// }
		// // product info
		// $_GET['ct'] = preg_replace('/CT/', '', $_GET['ct']);
		// $wmstasks = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $_GET['ct'] . '%'));
		// $st = $sl[0]->stock;
		// $si = [];
		// foreach ($wmstasks as $model) {
		// 	foreach ($model->items as $itm) {
		// 		if (empty($itm->mdata['pq']) && empty($itm->mdata['uq'])) continue;
		// 		if (!isset($si[$itm->mdata['si']])) $si[$itm->mdata['si']] = 0;
		// 		$si[$itm->mdata['si']] += floatval($itm->mdata['pq']);
		// 	}
		// 	$psi = [];
		// 	foreach ($model->actionTask->items as $itm) {
		// 		if (empty($itm->mdata['pq']) && empty($itm->mdata['uq'])) continue;
		// 		if (!isset($psi[$itm->mdata['si']])) $psi[$itm->mdata['si']] = 0;
		// 		$psi[$itm->mdata['si']] += floatval($itm->mdata['pq']);
		// 	}
		// }

		// $match = false;
		foreach ($sl as $l) {
			$st = $l->stock;
			$pik = '';
			if (isset($si[$st->id])) {
				$pik = (empty($psi[$st->id])? 0 : $psi[$st->id]). '/'. $si[$st->id]. ' Pallets picked';
				$match = true;
			}

			echo '<div class="table-view-cell table-view-cell-full">', $st->prod->name, ' (', sprintf('%d', $l->qty), ')<span class="icon icon-info"></span><br />', $pik, '<p><i>', $st->prod->ean, '</i><br />Exp: ', $st->expiry, ' Bat: ', $st->batch, '</p>';
			$more = $st->prod->brand . ' ' . $st->prod->model . '<br />';
			foreach ($st->prod->packs as $k) {
				if ($k->type != 20) continue;
				$more .= sprintf('%d', $k->qty) . '/' . $k->getType() . '<br />';
			}
			echo '<div class="more">', $more, '</div></div>';
		}

		$match = false;
		$models = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $_GET['ct'] . '%'));
		foreach ($models as $model) {
			foreach ($model->items as $itm) {
				if (empty($itm->mdata['pl'])) {
					continue;
				}
				if ($itm->mdata['pl'] == $plt->name) {
					$match = true;
				}
			}
		}

		if (!$match) {
			echo '<span style="color: #c00;">Stock not in this picking task!</span>';
		} else {
		?>
		<form action="<?=$this->createUrl('job/bulkTaskEntry', ['ct' => $_GET['ct'], 'type' => $_GET['type']]);?>" method="post" data-bit="2">
			<textarea placeholder="Notes" name="meta[nt]" rows="2"></textarea>
			<input type="hidden" name="meta[pq]" value="1" />
			<input type="hidden" name="meta[si]" value="<?=$st->id;?>" />
			<input type="hidden" name="meta[sn]" value="<?=$st->stockName();?>" />
			<input type="hidden" name="meta[pli]" value="<?=$plt->id;?>" />
			<button type="submit" class="btn btn-primary btn-block">Save</button>
		</form>
		<?php
		}
	}
	?>
</div>
