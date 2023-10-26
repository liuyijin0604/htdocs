<div class="row grid-view">
<table id="tsk_item" class="items">
<thead><tr><th width="25">#</th><th width="400">Stock</th><th width="40">Qty</th><th width="120">Plt Code</th><th>Notes</th></tr></thead>
<tbody>
<?php
$sts = [];
$pls = [];
$tq = 0;
foreach($model->items as $i => $itm){
	if(empty($itm->mdata['pl']) && !empty($itm->mdata['pli'])){
		$l = WmsLocation::model()->findByPk($itm->mdata['pli']);
		if($l) $itm->mdata['pl'] = $l->code;
	}
	echo '<tr class="'.($i%2==0? 'odd' : 'even').'" style="' . (!empty($itm->mdata['short']) ? 'color: red' : '') . '"><td>'.($i+1).'</td><td>'.$itm->mdata['sn'].'</td><td>'.(empty($itm->mdata['uq'])? '' : (!empty($itm->mdata['short']) ? '0/' : '') . $itm->mdata['uq']).'</td><td><a href="'.$this->createUrl('wmsTask/itemUpdate', ['id' => $itm->id]).'" class="jqm_link">'.(empty($itm->mdata['pl'])? 'EMPTY' : $itm->mdata['pl']).'</a></td><td>'.(empty($itm->mdata['nt'])? '' : $itm->mdata['nt']).'</td></tr>';

	$sts[] = $itm->mdata['si'];
	$pls[] = $itm->mdata['pl'];
	if(!empty($itm->mdata['uq'])) $tq += $itm->mdata['uq'];
}
if (empty($i)) $i = 0;
if (!empty($model->mainTask)) {
	foreach ($model->mainTask->items as $k => $itm) {
		if (empty($itm->mdata['si'])) {
			echo '<tr class="'.(($i+$k)%2==0? 'odd' : 'even').'"><td>'.($i+$k+1).'</td><td>'.$itm->mdata['sn'].'</td><td>'.(empty($itm->mdata['uq'])? '' : '0' . '/<span style="color:red;font-weight:bold">' . $itm->mdata['uq'] . '</span>').'</td><td><a href="'.$this->createUrl('wmsTask/itemUpdate', ['id' => $itm->id]).'" class="jqm_link">'.(empty($itm->mdata['pl'])? '' : $itm->mdata['pl']).'</a></td><td>'.(empty($itm->mdata['nt'])? '' : $itm->mdata['nt']).'</td></tr>';
		}
	}
}
?>
</tbody>
<tfoot>
	<tr><td>&nbsp;</td><td class="tot_si" align="right"><?=sizeof(array_unique($sts));?></td><td class="tot_uq" align="right"><?=$tq;?></td><td class="tot_pl" align="right"><?=sizeof(array_unique($pls));?></td><td>&nbsp;</td></tr>
</tfoot>
</table>
</div>

<?php
if (!empty($model->mainTask)) {
$nos = WmsSerialNo::model()->findAll('task_id = :id', [':id' => $model->mainTask->id]);
if (!empty($nos)) { ?>
<div class="row grid-view">
<table id="serial_no_item" class="items">
<thead><tr><th>Serial #</th></tr></thead>
<tbody>
<?php foreach ($nos as $i => $no) echo '<tr class="'.($i%2==0? 'odd' : 'even').'"><td>' . $no->sn . '</td></tr>'; ?>
</tbody>
</table>
</div>
<?php }} ?>