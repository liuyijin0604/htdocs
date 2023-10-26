<div class="row grid-view">
<table id="tsk_item" class="items">
<thead><tr><th width="25">#</th><th width="130">Plt Code</th><th>Notes</th><th>Length(cm)</th><th>Width(cm)</th><th>Height(cm)</th><th>Volume(m3)</th><th>Weight(kg)</th><th>Type</th></tr></thead>
<tbody>
<?php
$pls = [];
$volumes = 0;
$weights = 0;
foreach($model->items as $i => $itm){
	$plt = WmsLocation::model()->find('code = :code', array(':code' => $itm->mdata['pl']));
	$volume = round(floatval(@$plt->extra['length']) * floatval(@$plt->extra['width']) * floatval(@$plt->extra['height']) / 1000000 * 1000) / 1000;
	$type = !empty($plt->extra['type']) ? $plt->extra['type'] == 'plastic' ? '塑料板' : '熏蒸板' : '';
	$href = '<a href="'.$this->createUrl('wmsTask/itemUpdate', ['id' => $itm->id]).'" class="jqm_link">'.$itm->mdata['pl'].'</a>';
	echo '<tr class="'.($i%2==0? 'odd' : 'even').'"><td>'.($i+1).'</td><td>'.$itm->mdata['pl'].'</td><td>'.$itm->mdata['nt'].'</td><td>'.(@$plt->extra['length'] ? $plt->extra['length'] : 0).'</td><td>'.(@$plt->extra['width'] ? $plt->extra['width'] : 0).'</td><td>'.(@$plt->extra['height'] ? $plt->extra['height'] : 0).'</td><td>' . $volume . '</td><td>'.(@$plt->extra['weight'] ? $plt->extra['weight'] : 0).'</td><td>'.$type.'</td></tr>';
	$pls[] = $itm->mdata['pli'];
	$volumes += $volume;
	$weights += floatval(@$plt->extra['weight']);
}
?>
</tbody>
<tfoot>
	<tr><td>&nbsp;</td><td class="tot_pl" align="right"><?=sizeof(array_unique($pls));?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><?=$volumes?></td><td><?=$weights?></td><td>&nbsp;</td></tr>
</tfoot>
</table>
</div>

<h1>Summary</h1>
<div class="row grid-view">
	<table id="summary" class="items" style="width: 50%">
		<thead>
			<tr><th>木板数</th><th>IPPC熏蒸木板数</th><th>塑料板数</th><!-- <th>chep板数</th><th>loscam板数</th> --></tr>
		</thead>
		<tbody>
			<tr><td><?=intval(@$model->mdata['pi_wood'])?></td><td><?=intval(@$model->mdata['pi_ippc'])?></td><td><?=intval(@$model->mdata['pi_plastic'])?></td><!-- <td><?=intval(@$model->mdata['pi_chep'])?></td><td><?=intval(@$model->mdata['pi_loscam'])?></td> --></tr>
		</tbody>
	</table>
</div>