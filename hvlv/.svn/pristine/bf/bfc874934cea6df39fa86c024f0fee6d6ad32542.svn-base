<div class="row grid-view">
  <br />
<table id="tsk_item" class="table table-striped table-bordered">
<thead><tr><th width="25">#</th><th width="130">Plt Code</th><th width="360">Product</th><th width="100">Carton Qty</th><th width="100">Unit Qty</th><th width="100">Expiry Date</th><th width="100">Batch #</th><th>Notes</th></tr></thead>
<tbody>
<?php
$pls = [];
$gis = [];
$tq = 0;
$tc = 0;
foreach($model->items as $i => $itm){
	echo '<tr class="'.($i%2==0? 'odd' : 'even').'"><td>'.($i+1).'</td><td><a href="'.$this->createUrl('wmsTask/itemUpdate', ['id' => $itm->id]).'" class="jqm_link">'.'a'.'</a></td><td>'.$itm->mdata['gn'].'</td><td>'.$itm->mdata['cq'].'</td><td>'.$itm->mdata['uq'].'</td><td>'.$itm->mdata['ex'].'</td><td>'.$itm->mdata['bn'].'</td><td>'.$itm->mdata['nt'].'</td></tr>';
	$pls[] = $itm->mdata['pl'];
	$gis[] = $itm->mdata['gi'];
	$tc += floatval($itm->mdata['cq']);
	$tq += floatval($itm->mdata['uq']);
}
?>
</tbody>
<tfoot>
	<tr><td><b>Total:</b></td><td class="tot_pl" align="left"><?=sizeof(array_unique($pls));?></td><td class="tot_gn" align="left"><?=sizeof(array_unique($gis));?></td><td class="tot_cq" align="left"><?=$tc;?></td><td class="tot_uq" align="left"><?=$tq;?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
</tfoot>
</table>
</div>