<?php
foreach ($models as $model) {
	if (in_array($model->type, [3010])) {
		echo '<div class="table-view-cell table-view-cell-full" style="padding:0;border-bottom:none;margin-bottom:15px;">Ref #: ', $model->ref, ' &nbsp; ';
		if(!empty($model->mdata['ordno'])) echo 'Order #: ', $model->mdata['ordno'], '<span class="icon icon-info"></span>';
		echo '<div class="more">Ship to: ',@$model->mdata['st_co'],'<br /></div></div>';
		$pt = 0;
		$si = [];
		$counted = [];
		foreach ($model->items as $itm) {
			if (!empty($itm->mdata['pli'])) {
				$locs = WmsStockLocation::model()->findAll('location_id = :lid', [':lid' => $itm->mdata['pli']]);
				foreach ($locs as $loc) {
					if (in_array($loc->id, $counted)) {
						continue;
					} else {
						$counted[] = $loc->id;
					}
					if (empty($si[$loc->stock_id])) {
						$si[$loc->stock_id] = 0;
					}
					$si[$loc->stock_id] += 1;
					$pt += 1;
				}
			} else if (!empty($itm->mdata['pl'])) {
				$locs = WmsStockLocation::model()->with('loc')->findAll('loc.name = :name', [':name' => $itm->mdata['pl']]);
				foreach ($locs as $loc) {
					if (in_array($loc->id, $counted)) {
						continue;
					} else {
						$counted[] = $loc->id;
					}
					if (empty($si[$loc->stock_id])) {
						$si[$loc->stock_id] = 0;
					}
					$si[$loc->stock_id] += 1;
					$pt += 1;
				}
			}
		}
		$psi = [];
		$ppt = 0;
		$upd = [];
		foreach($model->actionTask->items as $itm){
			if(empty($itm->mdata['pq']) || empty($itm->mdata['si'])) continue;
			$ppt += $itm->mdata['pq'];
			if(!isset($psi[$itm->mdata['si']])) $psi[$itm->mdata['si']] = [];
			$psi[$itm->mdata['si']][] = $itm->mdata['pli'];
			$upd[$itm->mdata['pli']] = $itm->ts;
		}

		echo '<h4>Pallets (',$ppt, '/', $pt,')</h4><ul class="table-view">';

		foreach($si as $id => $q){
			$s = WmsStock::model()->findByPk($id);
			echo '<li class="table-view-cell table-view-cell-full">', (empty($psi[$id])? 0 : sizeof($psi[$id])), '/', $q, ' &times ', $s->prod->name, '<br />Exp: '.$s->expiry.' &nbsp; Batch#: '.$s->batch.' <div class="more">';
			$i = 0;
			if(empty($psi[$id])) continue;
			foreach($psi[$id] as $pli){
				$loc = WmsLocation::model()->findByPk($pli);

				echo '<div class="row"><div class="col-6">', $loc->name, (empty($loc->pid)? '' : '<br />'.$loc->parent->name), '</div><div class="col-6"><small>', substr($upd[$pli],0,-3),'</small></div></div>';
				if($i++ > 2 * $q) break;
			}
			echo '</div></li>';
		}
		echo '</ul>';
	}
}
?>