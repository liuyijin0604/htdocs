<?php if (empty($model->mainTask->mdata['simple_in'])) { ?>

<div class="row grid-view">
<table id="tsk_item" class="items">
<thead><tr><th width="25">#</th><th width="130">Plt Code</th><th width="360">Product</th><th width="60">Carton Qty</th><th width="60">Unit Qty</th><th width="100">Expiry Date</th><?=in_array($model->job->org_id, Org::$airsea_heshengyuan) ? '<th width="100">Mfr. Date</th>' : ''?><th width="100">Batch #</th><th>Notes</th><th>Still In WH</th></tr></thead>
<tbody>
<?php
$pls = [];
$gis = [];
$tq = 0;
$tc = 0;
$os = [];
foreach($model->items as $i => $itm){
	$os[$i] = $itm->mdata['pl'];
}
asort($os);
$rs = [];
$i=0;
$pps = [];
$plts = [];
foreach($os as $k => $v){
	$itm = $model->items[$k];
	if(!empty($itm->mdata['uq']) && empty($itm->mdata['cq'])){
		if(empty($pps[$itm->mdata['gi']])){
			$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid AND qty > 0', [':pid' => $itm->mdata['gi']]);
			$pps[$itm->mdata['gi']] = $pp;
		}else{
			$pp = $pps[$itm->mdata['gi']];
		}
		if(!empty($pp) && !empty($pp->qty) && $itm->mdata['uq'] % $pp->qty == 0){
			$itm->mdata['cq'] = $itm->mdata['uq'] / $pp->qty;
		}
	}
	if (empty($itm->mdata['pl'])) continue;

	if(empty($plts[$itm->mdata['pl']])){
		$plt = WmsLocation::model()->find('code = :code', [':code' => $itm->mdata['pl']]);
		$plts[$itm->mdata['pl']] = $plt;
	}else{
		$plt = $plts[$itm->mdata['pl']];
	}

	$href = ($model->mainTask->status < 99? '<a href="'.$this->createUrl('wmsTask/itemUpdate', ['id' => $itm->id]).'" class="jqm_link">'.$itm->mdata['pl'].'</a>' : $itm->mdata['pl']).(empty($plt->bwf)? '' : ' '.AppHelper::bwf2warning($plt,true,true));
	echo '<tr class="'.($i%2==0? 'odd' : 'even').'"><td>'.($i+1).'</td><td>'.$href.'</td><td>'.$itm->mdata['gn'].'</td><td>'.$itm->mdata['cq'].'</td><td>'.$itm->mdata['uq'].'</td><td>'.$itm->mdata['ex'].'</td><td>'.@$itm->mdata['mfr'].'</td><td>'.$itm->mdata['bn'].'</td><td>'.$itm->mdata['nt'].'</td><td>'.($plt->notEmpty() ? '<span style="color: green">Yes</span>' : '<span style="color: red">No</span>').'</td></tr>';
	$pls[] = $itm->mdata['pl'];
	$gis[] = $itm->mdata['gi'];
	$tc += floatval($itm->mdata['cq']);
	$tq += floatval($itm->mdata['uq']);
	$i++;
}
?>
</tbody>
<tfoot>
	<tr><td>&nbsp;</td><td class="tot_pl" align="right"><?=sizeof(array_unique($pls));?></td><td class="tot_gn" align="right"><?=sizeof(array_unique($gis));?></td><td class="tot_cq" align="right"><?=$tc;?></td><td class="tot_uq" align="right"><?=$tq;?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
</tfoot>
</table>
</div>

<?php } else { ?>

<div class="row grid-view">
<table id="tsk_item" class="items">
<thead><tr><th width="25">#</th><th width="200">Plt Code</th><th width="160">Length(cm)</th><th width="160">Width(cm)</th><th width="160">Height(cm)</th><th width="160">Volume(m3)</th><th width="160">Weight(kg)</th><th width="160">Type</th><th width="160">Port</th><th width="160">Note</th><th>Still In WH</th></tr></thead>
<tbody>
<?php
$pls = [];
$volumes = 0;
$weights = 0;
$os = [];
$plts = [];
foreach ($model->items as $i => $itm) {
	$os[$i] = $itm->mdata['pl'];
}
asort($os);
$rs = [];
$i = 0;
foreach ($os as $k => $v) {
	$itm = $model->items[$k];
	if(empty($plts[$itm->mdata['pl']])){
		$plt = WmsLocation::model()->find('code = :code', [':code' => $itm->mdata['pl']]);
		$plts[$itm->mdata['pl']] = $plt;
	}else{
		$plt = $plts[$itm->mdata['pl']];
	}
	$volume = round(floatval(@$plt->extra['length']) * floatval(@$plt->extra['width']) * floatval(@$plt->extra['height']) / 1000000 * 1000) / 1000;
	$port = !empty($plt->extra['port']) ? oList::kvp('ex_port')[$plt->extra['port']] : '';
	$type = !empty($plt->extra['type']) ? $plt->extra['type'] == 'plastic' ? '塑料板' : '熏蒸板' : '';

	if (empty($sum[$port][$type])) {
		$sum[$port][$type] = array('volume' => 0, 'weight' => 0);
	}
	$sum[$port][$type]['volume'] += $volume;
	$sum[$port][$type]['weight'] += floatval(@$plt->extra['weight']);

	$plt->extra['length'] = @$plt->extra['length'];
	$plt->extra['width'] = @$plt->extra['width'];
	$plt->extra['height'] = @$plt->extra['height'];

	if (empty($sum2[$port][$plt->extra['length']. '-' . $plt->extra['width'] . '-' . $plt->extra['height']])) {
		$sum2[$port][$plt->extra['length']. '-' . $plt->extra['width'] . '-' . $plt->extra['height']] = array('qty' => 0, 'weight' => 0, 'volume' => 0);
	}
	$sum2[$port][$plt->extra['length']. '-' . $plt->extra['width'] . '-' . $plt->extra['height']]['qty'] ++;
	$sum2[$port][$plt->extra['length']. '-' . $plt->extra['width'] . '-' . $plt->extra['height']]['weight'] += floatval(@$plt->extra['weight']);
	$sum2[$port][$plt->extra['length']. '-' . $plt->extra['width'] . '-' . $plt->extra['height']]['volume'] += $volume;

	$href = $model->mainTask->status < 99? '<a href="'.$this->createUrl('wmsTask/itemUpdate', ['id' => $itm->id]).'" class="jqm_link">'.$itm->mdata['pl'].'</a>' : $itm->mdata['pl'];
	echo '<tr class="'.($i%2==0? 'odd' : 'even').'"><td>'.($i+1).'</td><td>'.$href.'</td><td>'.(@$plt->extra['length'] ? $plt->extra['length'] : 0).'</td><td>'.(@$plt->extra['width'] ? $plt->extra['width'] : 0).'</td><td>'.(@$plt->extra['height'] ? $plt->extra['height'] : 0).'</td><td>' . $volume . '</td><td>'.(@$plt->extra['weight'] ? $plt->extra['weight'] : 0).'</td><td>'.$type.'</td><td>'.$port.'</td><td>'.$plt->notes.'</td><td>'.($plt->notEmpty() ? '<span style="color: green">Yes</span>' : '<span style="color: red">No</span>').'</td></tr>';
	$pls[] = $itm->mdata['pl'];
	$volumes += $volume;
	$weights += floatval(@$plt->extra['weight']);
	$i++;
}
?>
</tbody>
<tfoot>
	<tr><td>&nbsp;</td><td class="tot_pl" align="right"><?=sizeof(array_unique($pls));?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><?=$volumes?></td><td><?=$weights?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
</tfoot>
</table>
</div>

<?php if (!empty($sum)) { ?>
<h1>Summary</h1>
<div class="row grid-view">
	<table id="summary" class="items" style="width: 50%">
		<tbody>
		<?php $i = 0; foreach ($sum as $port => $sumtype) { ?>
			<?php foreach ($sumtype as $type => $item) { ?>
				<tr class="<?=($i++ % 2 == 0) ? 'odd' : 'even'?>"><td width="180"><?=$port?></td><td width="180"><?=$type?></td><td width="180"><?=$item['volume'].' m3'?></td><td width="180"><?=$item['weight'].' kg'?></td></tr>
			<?php } ?>
		<?php } ?>
		<tr class="<?=($i++ % 2 == 0) ? 'odd' : 'even'?>"><td width="180">&nbsp;</td><td width="180">&nbsp;</td><td width="180"><?=$volumes.' m3'?></td><td width="180"><?=$weights.' kg'?></td></tr>
		</tbody>
	</table>
</div>
<?php } ?>

<?php if (!empty($sum2)) { ?>
<br />
<h1>Summary</h1>
<div class="row grid-view">
	<table id="summary2" class="items" style="width: 50%">
		<tbody>
			<?php $i = 0; foreach ($sum2 as $port => $sumdim) { ?>
				<?php $weights = 0; $volumes = 0; foreach ($sumdim as $info) {
					$weights += $info['weight'];
					$volumes += $info['volume'];
				}	?>
				<tr class="<?=($i++ % 2 == 0) ? 'odd' : 'even'?>"><td width="180"><?=$port?> - TOTAL WEIGHT: <?=$weights?> KG</td></tr>
				<?php foreach ($sumdim as $dim => $info) {
					$length = explode('-', $dim)[0];
					$width = explode('-', $dim)[1];
					$height = explode('-', $dim)[2];
				?>
				<tr class="<?=($i++ % 2 == 0) ? 'odd' : 'even'?>"><td width="180">DIMS <?=$length?>x<?=$width?>x<?=$height?> CM x <?=$info['qty']?></td></tr>
				<?php } ?>
				<tr class="<?=($i++ % 2 == 0) ? 'odd' : 'even'?>"><td width="180">TOTAL VOLUME: <?=round($volumes * 100) / 100?> m3</td></tr>
				<tr class="<?=($i++ % 2 == 0) ? 'odd' : 'even'?>"><td width="180">&nbsp;</td></tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php } ?>

<?php } ?>