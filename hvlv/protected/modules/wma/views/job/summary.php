<?php
if (in_array($model->type, [1010, 1020, 1030])) {
	$plt = [];
	$gi = [];
	foreach ($model->mainTask->items as $itm) {
		$gi[$itm->mdata['gi']][$itm->mdata['ex'].'_'.$itm->mdata['bn']]['req'][] = $itm;
	}
	foreach ($model->items as $itm) {
		$plt[$itm->mdata['pl']][] = $itm;
		if (!empty($gi[$itm->mdata['gi']][$itm->mdata['ex'].'_'.$itm->mdata['bn']]['req'])) {
			// same expiry and batch
			$gi[$itm->mdata['gi']][$itm->mdata['ex'].'_'.$itm->mdata['bn']]['res'][] = $itm;
		} else if (!empty($gi[$itm->mdata['gi']][$itm->mdata['ex'].'_']['req'])) {
			// same expiry
			$gi[$itm->mdata['gi']][$itm->mdata['ex'].'_']['res'][] = $itm;
		} else {
			// no expiry and batch
			$gi[$itm->mdata['gi']]['_']['res'][] = $itm;
		}
	}
	echo '<h4>Pallets (',sizeof($plt),')</h4><ul class="table-view" style="margin-bottom:20px;">';
	krsort($plt);
	foreach ($plt as $pn => $pl) {
		echo '<li class="table-view-cell table-view-cell-full">', $pn, '&nbsp;&nbsp;(', sizeof($pl), ')<p>';
		foreach ($pl as $itm) {
			echo '<div class="row" style="height: 10px;">', '<div class="col-3">', $itm->mdata['gn'], '</div><div class="col-3">', $itm->mdata['cq'], ' (', $itm->mdata['uq'], ')</div><div class="col-3">', $itm->mdata['ex'], '</div><div class="col-3">', $itm->mdata['bn'], '</div></div>';
		}
		echo '</p></li>';
	}
	echo '</ul><h4>Products (', sizeof($gi), ')</h4><ul class="table-view" style="margin-bottom:20px;">';
	foreach ($gi as $id => $gls) {
		foreach ($gls as $ex_bn => $gl) {
			echo '<li class="table-view-cell table-view-cell-full">';
			$prod = WmsProd::model()->findByPk($id);
			echo '<b>' . $prod->name . '</b><br />';
			$req = 0;
			$res = 0;
			if (!empty($gl['req'])) {
				foreach ($gl['req'] as $itm) {
					$req += intval(@$itm->mdata['uq']);
				}
			}
			if (!empty($gl['res'])) {
				foreach ($gl['res'] as $itm) {
					echo '<div class="row" style="height: 10px;"><div class="col-6">', $itm->mdata['pl'], '</div><div class="col-6">', $itm->mdata['cq'], ' (',$itm->mdata['uq'], ')</div></div><div class="row"><div class="col-6">Expiry</div><div class="col-6">', (!empty($itm->mdata['ex']) ? $itm->mdata['ex'] : '<span style="color:#c00">Empty</span>'), '</div><div class="col-6">Batch</div><div class="col-6">', (!empty($itm->mdata['bn']) ? $itm->mdata['bn'] : '<span style="color:#c00">Empty</span>'), '</div></div>';
					$res += intval(@$itm->mdata['uq']);
				}
			}
			$diff = $res - $req;
			if ($diff > 0) $diff = '+' . $diff;
			if ($diff != 0) $diff = '<span style="color:#c00">' . $diff . '</span>';
			echo '<b>Total:</b> ' . $res . ' &nbsp; Requested: ' . $req . ' &nbsp; Diff: ' . $diff;
			echo '</li>';
		}
	}
	echo '</ul>';

	if ($model->mainTask->status < 99) {
		echo '<form action="' . $this->createUrl('job/complete', ['id' => $model->id]) . '" method="post" id="pallet-in-form">';
		echo '<button type="submit" class="btn btn-primary btn-block">Complete</button>';
		echo '</form>';
	}
}elseif(in_array($model->type, [2030])){
	echo '<div class="table-view-cell table-view-cell-full" style="padding:0;border-bottom:none;margin-bottom:15px;">', $model->mainTask->ref,' (',@$model->mainTask->mdata['ctn_size'],') &nbsp; <span class="tog-sf icon icon-up"></span><br />';
	if(!empty($model->mdata['ordno'])) echo 'Order #: ', $model->mdata['ordno'], '<br />';
	echo '<div class="sum_entry_form"><form action="',$this->createUrl('job/sumEntry', ['id' => $model->link_id]),'" method="post">
	<input type="text" name="mdata[ctn_no]" value="',@$model->mainTask->mdata['ctn_no'],'" placeholder="Container #" />
	<input type="text" name="mdata[ctn_seal]" value="',@$model->mainTask->mdata['ctn_seal'],'" placeholder="Seal #" />
	<button type="submit" class="btn btn-primary btn-block">Save</button>
	</form></div>
	<h4>Pallets (',sizeof($model->items),')</h4><ul class="table-view">';
	foreach($model->items as $itm){
		if(empty($itm->mdata['pl']) || empty($itm->mdata['pli'])) continue;
		echo '<li class="table-view-cell table-view-cell-full">'.$itm->mdata['pl'].'</li>';
	}
	echo '</ul>';

	if ($model->mainTask->status < 99) {
		echo '<form action="' . $this->createUrl('job/complete', ['id' => $model->id]) . '" method="post">';
		echo '<button type="submit" class="btn btn-primary btn-block">Complete</button>';
		echo '</form>';
	}
} else if (in_array($model->type, [2040])) {
	if(!empty($model->mdata['ordno'])) echo 'Order #: ', $model->mdata['ordno'], '<br />';
	echo '<div class="sum_entry_form"><form action="',$this->createUrl('job/sumEntry', ['id' => $model->link_id]),'" method="post">
	<input type="text" name="mdata[pmc_no]" value="',@$model->mainTask->mdata['pmc_no'],'" placeholder="PMC #" />
	<button type="submit" class="btn btn-primary btn-block">Save</button>
	</form></div>
	<h4>Pallets (',sizeof($model->items),')</h4><ul class="table-view">';
	foreach($model->items as $itm){
		if(empty($itm->mdata['pl']) || empty($itm->mdata['pli'])) continue;
		echo '<li class="table-view-cell table-view-cell-full">'.$itm->mdata['pl'].'</li>';
	}
	echo '</ul>';

	if ($model->mainTask->status < 99) {
		echo '<form action="' . $this->createUrl('job/complete', ['id' => $model->id]) . '" method="post">';
		echo '<button type="submit" class="btn btn-primary btn-block">Complete</button>';
		echo '</form>';
	}
}elseif(in_array($model->type, [3010])){
	echo '<div class="table-view-cell table-view-cell-full" style="padding:0;border-bottom:none;margin-bottom:15px;">Ref #: ', $model->ref, ' &nbsp; ';
	if(!empty($model->mdata['ordno'])) echo 'Order #: ', $model->mdata['ordno'], '<span class="icon icon-info"></span>';
	echo '<div class="more">Ship to: ',@$model->mdata['st_co'],'<br /></div></div>';
	$pt = 0;
	$si = [];
	// foreach($model->mainTask->items as $itm){
	// 	if(empty($itm->mdata['pq']) || empty($itm->mdata['si'])) continue;
	// 	$pt += $itm->mdata['pq'];
	// 	if(!isset($si[$itm->mdata['si']])) $si[$itm->mdata['si']] = 0;
	// 	$si[$itm->mdata['si']] += $itm->mdata['pq'];
	// }
	$counted = [];
	foreach ($model->mainTask->items as $itm) {
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
	foreach($model->items as $itm){
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
}elseif(in_array($model->type, [3020, 3030])){
	echo '<div class="table-view-cell table-view-cell-full" style="padding:0;border-bottom:none;margin-bottom:15px;">Ref #: ', $model->ref, ' &nbsp; ';
	if(!empty($model->mdata['ordno'])) echo 'Order #: ', $model->mdata['ordno'], '<span class="icon icon-info"></span>';
	echo '<div class="more">Ship to: ',@$model->mdata['st_co'],'<br /></div></div>';
	$pt = 0;
	$si = [];
	foreach($model->mainTask->items as $itm){
		if(empty($itm->mdata['uq']) || empty($itm->mdata['si'])) continue;
		$pt += $itm->mdata['uq'];
		if(!isset($si[$itm->mdata['si']])) $si[$itm->mdata['si']] = 0;
		$si[$itm->mdata['si']] += $itm->mdata['uq'];
	}
	$psi = [];
	$ppt = 0;
	foreach($model->items as $itm){
		if(empty($itm->mdata['uq']) || empty($itm->mdata['si'])) continue;
		$ppt += $itm->mdata['uq'];
		if(!isset($psi[$itm->mdata['si']])) $psi[$itm->mdata['si']] = 0;
		$psi[$itm->mdata['si']] += $itm->mdata['uq'];
	}

	echo '<h4>SKU: ',sizeof($psi), '/', sizeof($si),' &nbsp; Qty: ', $ppt, '/', $pt,'</h4><ul class="table-view">';
	foreach($si as $id => $q){
		$s = WmsStock::model()->findByPk($id);
		if(empty($s)) continue;
		echo '<li class="table-view-cell table-view-cell-full">', (empty($psi[$id])? 0 : $psi[$id]), '/', $q, ' &times ', $s->prod->name, '<br />Exp: '.$s->expiry.' &nbsp; Batch#: '.$s->batch;
		if (empty($psi[$id]) || $psi[$id] != $q) {
			echo ' <a class="top_item pull-right" href="'.$this->createUrl('job/topItem',['task' => $model->id, 'id' => $id]).'" style="margin-right: 5px"><span class="icon icon-up" style="font-size:1.2em"></span> Top</a>';
		}
		echo ' <div class="more">';
		$sls = WmsStockLedger::model()->with('taskItem')->findAll('t.stock_id = :stock_id AND taskItem.task_id = :task_id AND location_id NOT IN (3,4)', [':stock_id' => $id, ':task_id' => $model->id]);
		foreach ($sls as $sl) {
			$remain = WmsStockLocation::model()->with('stock')->find(['select' => 'sum(t.qty) AS qty', 'condition' => 't.location_id = :location_id AND stock.prod_id = :prod_id', 'params' => [':prod_id' => $s->prod_id, ':location_id' => $sl->loc->id]]);
			echo '<div class="row"><div class="col-4">', $sl->loc->name, '<br />', (empty($sl->loc->pid)? '' : $sl->loc->parent->name), '</div><div class="col-4">', sprintf('%d', $sl->qty_out), ' <span style="color:green"><small>(remain:', $remain['qty'], ')</small></span></div><div class="col-4"><small>', substr($sl->ts,0,-3),'</small></div></div>';
		}
		// $s = WmsStock::model()->findByPk($id);
		// if(empty($s)) continue;
		// echo '<li class="table-view-cell table-view-cell-full">', (empty($psi[$id])? 0 : $psi[$id]), '/', $q, ' &times ', $s->prod->name, '<br />Exp: '.$s->expiry.' &nbsp; Batch#: '.$s->batch.' <div class="more">';
		// $i = 0;
		// foreach($s->locs as $p){
		// 	echo '<div class="row"><div class="col-6">', $p->loc->name, '<br />', (empty($p->loc->pid)? '' : $p->loc->parent->name), '</div><div class="col-2">', sprintf('%d', $p->qty), '</div><div class="col-4"><small>', substr($p->updated,0,-3),'</small></div></div>';
		// 	$i += $p->qty;
		// 	if($i > 2 * $q) break;
		// }
		echo '</div></li>';
	}
	echo '</ul>';

	echo '<h4>Num of Pallets:</h4><ul class="table-view">';
	echo ' <div class="more">';
	echo '<input id="numPalletsAction" class="plt barcode required" type="search" placeholder="Num of Pallets" name="plt" value="'.$model->mainTask->mdata['numPalletsAction'].'" />';
	echo '</div></li>';
	echo '</ul>';

	if ($model->mainTask->status < 99) {
		echo '<form action="' . $this->createUrl('job/complete', ['id' => $model->id]) . '" method="post">';
		if(!empty($model->mainTask->mdata['numPalletsAction'])){
			echo '<input id="numOfPalletsAction" name="numOfPalletsAction" type="hidden" value="'.$model->mainTask->mdata['numPalletsAction'].'"/>';
		}else{
			echo '<input id="numOfPalletsAction" name="numOfPalletsAction" type="hidden" value=""/>';
		}
		
		echo '<button type="submit" class="btn btn-primary btn-block">Complete</button>';
		echo '</form>';
	}
}
?>
<script type="text/javascript">
$('#numPalletsAction').change(function() 
{ 
    var selectedValue = parseInt(jQuery(this).val());
	$("#numOfPalletsAction").val(selectedValue);

});
</script>
<script type="text/javascript">
$('#numPalletsAction').load(function() 
{ 
    var selectedValue = parseInt(jQuery(this).val());
	$("#numOfPalletsAction").val(selectedValue);

});
</script>

<script type="text/javascript">
$(function(){
	$('span.tog-sf').off('touchend').on('touchend', function(){
		if($(this).hasClass("icon-up")){
			$(this).removeClass("icon-up").addClass('icon-down');
			$('.sum_entry_form').slideUp();
		}else{
			$(this).removeClass("icon-down").addClass('icon-up');
			$('.sum_entry_form').slideDown();
		}
	});

	$('form#pallet-in-form').on('success', function(e, r) {
		$('#summary').html(r.data);
	});

	$('#summary').off('touchend', 'a.top_item').on('touchend', 'a.top_item', function() {
		if (wmaApp.isScrolling) return;
		if (window.confirm('Are you sure to top?')) {
			$.get($(this).attr('href'));
			$('#action').load('<?=$this->createUrl("job/taskAct", ['id' => $model->id]);?>');
		}
		return false;
	});
});
</script>