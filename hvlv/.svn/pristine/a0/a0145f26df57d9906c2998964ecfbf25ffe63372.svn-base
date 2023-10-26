<div class="data-form">
<?php
if(empty($p)){
	echo '<span style="color:#c00">Product not found! <a href="'.$this->createUrl('prod/create').'" class="modal_link" title="Create Product" data-ignore="push">Create Product</a></span>';
}elseif(sizeof($p) > 1){
	echo '<span>Multiple item found! Please check and fix <a href="'. $this->createUrl('prod/list'),'" data-transition="slide-in">product data</a></span>';
}else{
	//product info
	echo '<div class="table-view-cell table-view-cell-full">', $p[0]->name, ($p[0]->hasPkgWt()? '' : ' <span style="color:#c00">**Weight Missing**</span>'), ' <span class="icon icon-info"></span><p><i>', $p[0]->ean,'</i></p>';
	$more = $p[0]->brand.' '.$p[0]->model;
	foreach($p[0]->packs as $k){
		$more .= '<div class="row"><div class="col-4">'. sprintf('%d', $k->qty). '/'.$k->getType().'</div><div class="col-4">'.$k->barcode.'</div><div class="col-4">'. $k->showDim().'</div></div>';
	}
	//task info
	if($model->link_id > 0){
		$rs = $model->mainTask->getItem($p[0]->id);
		if(empty($rs)){
			echo '<span style="color:#c00">Product not found in this task!</span>';
		}else{
			$more .= '<div class="row"><div class="col-3">Exp</div><div class="col-3">Bat#</div><div class="col-3">Carton</div><div class="col-3">Unit</div></div>';
			foreach($rs as $r){
				$more .= '<div class="row"><div class="col-3">'.@$r->mdata['ex'].'</div><div class="col-3">'.@$r->mdata['bn'].'</div><div class="col-3">'. @$r->mdata['cq'].'</div><div class="col-3">'.@$r->mdata['uq'].'</div></div>';
			}
		}
	}
	echo '<div class="more">', $more, '</div></div>';

	//locations
	if(empty($_POST['plt']) && $model->type == 1020){
		$sls = WmsStockLocation::model()->with('stock', 'loc.parent')->findAll(['condition' => 'stock.prod_id = :pid AND stock.org_id = :oid AND t.qty > 0 AND (parent.type = 30 OR loc.type = 50 OR loc.type = 60)', 'params' => [':pid' => $p[0]->id, ':oid' => $model->job->org_id]]);
		echo '<div class="locs_list">';
		$los = [];
		$ltq = [];
		foreach($sls as $sl){
			if(!isset($ltq[$sl->location_id])) $ltq[$sl->location_id] = 0;
			$ltq[$sl->location_id] += $sl->qty;
			$los[$sl->location_id] = $sl->loc;
		}
		foreach($los as $l=>$loc){
			echo '<div class="table-view-cell table-view-cell-full" data-plt="'.$loc->code.'"><span class="icon icon-download pull-right"></span>'.$loc->code.' ('.$loc->parent->name.')<p>Qty: '.$ltq[$l].'</p></div>';
		}
		echo '</div>';
	}
?>
<form action="<?=$this->createUrl('job/entry', ['id' => $model->id]);?>" method="post">
<?php
if(empty($_POST['plt']) && $model->type == 1020):
?>
<input class="barcode rplt required" type="search" name="meta[pl]" placeholder="Pallet/Location" />
<?php endif; ?>
	<div class="row input-addon">
		<input type="number" placeholder="Carton Qty" name="meta[cq]" style="width: 50%" data-last="<?=empty($_SESSION['entry_meta_'.$model->id]['cq'])? '' : $_SESSION['entry_meta_'.$model->id]['cq'];?>" />
		<input class="uqty" type="number" placeholder="Unit Qty" name="meta[uq]" style="width: 50%" data-last="<?=empty($_SESSION['entry_meta_'.$model->id]['uq'])? '' : $_SESSION['entry_meta_'.$model->id]['uq'];?>" />
	</div>
	<div class="row input-addon">
		<input type="date" class="txtdate" placeholder="Expiry" name="meta[ex]" style="width: 50%"  data-last="<?=empty($_SESSION['entry_meta_'.$model->id]['ex'])? '' : $_SESSION['entry_meta_'.$model->id]['ex'];?>" />
		<input type="text" placeholder="Batch#" name="meta[bn]" style="width: 50%" data-last="<?=empty($_SESSION['entry_meta_'.$model->id]['bn'])? '' : $_SESSION['entry_meta_'.$model->id]['bn'];?>" />
	</div>
	<?php if (in_array($model->job->org_id, Org::$airsea_heshengyuan)) { ?>
		<div class="row input-addon">
			<div class="col-6" style="position: relative; height: 40px">
				<label style="width: 100%; position: absolute; transform: translate(-50%, -50%); top: 50%; left: 55%">Manufacturing Date</label>
			</div>
			<div class="col-6">
				<input type="date" placeholder="Mfr. Date" name="meta[mfr]" data-last="<?=empty($_SESSION['entry_meta_'.$model->id]['mfr'])? '' : $_SESSION['entry_meta_'.$model->id]['mfr'];?>" />
			</div>
		</div>
	<?php } ?>
	<textarea placeholder="Notes" name="meta[nt]" rows="2"></textarea>
<?php
if(!empty($_POST['plt'])):
	if(!empty($model->mainTask->mdata['pi_airsea'])){
		echo '<div class="row"><select name="meta[pas]" class="required"><option value="">请选择空海运托盘</option><option value="8"> Air 空运</option><option value="16">Sea 海运</option></select></div>';
	}
?>
	<input type="hidden" name="meta[pl]" value="<?=$_POST['plt']?>" />
<?php endif; ?>
	<input type="hidden" name="meta[gi]" value="<?=$p[0]->id;?>" />
	<input type="hidden" name="meta[gn]" value="<?=$p[0]->name;?>" />
	<button type="submit" class="btn btn-primary btn-block">Save</button>
</form>
<?php } ?>
</div>

<script type="text/javascript">
$(function(){
	if($('.locs_list .table-view-cell').length == 1){
		var c = $($('.locs_list .table-view-cell')[0]);
		c.addClass('hi');
		$('input.rplt').val(c.data('plt'));
	}
	$('.locs_list .icon-download').on('touchend', function(){
		if(wmaApp.isScrolling) return;
		var c = $(this).parents('.table-view-cell');
		c.siblings().removeClass('hi');
		c.addClass('hi');
		$('input.rplt').val(c.data('plt'));
	});
	$('.txtdate').on('change', function() {
		var now = new Date();
		var input = new Date($(this).val());
		if ((input - now) / (1000 * 60 * 60 * 24 * 7) < 1) {
			alert('输入的保质期小于1周，请检查');
			$(this).val('');
		}
	});
	$('.data-form form').on('success', function(e, r) {
		if (r.msg.match(/入库数字>100000，请检查/)) {
			audio = new Audio();
			audio.src = 'https://os.toplogistics.com.au/site/voice/stockin_error.mp3';
			audio.play();
		}
	});
});
</script>