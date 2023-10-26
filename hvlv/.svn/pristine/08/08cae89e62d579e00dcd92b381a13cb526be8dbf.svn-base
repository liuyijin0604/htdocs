<div class="data-form">
	<?php
	if (empty($p)) {
		echo '<span style="color:#c00">Product not found! <a href="'.$this->createUrl('prod/create').'" class="modal_link" title="Create Product" data-ignore="push">Create Product</a></span>';
	} else if (sizeof($p) > 1) {
		echo '<span>Multiple item found! Please check and fix <a href="'. $this->createUrl('prod/list'),'" data-transition="slide-in">product data</a></span>';
	} else {
		// product info
		echo '<div class="table-view-cell table-view-cell-full">', $p[0]->name, ($p[0]->hasPkgWt()? '' : ' <span style="color:#c00">**Weight Missing**</span>'), ' <span class="icon icon-info"></span><p><i>', $p[0]->ean,'</i></p>';
		$more = $p[0]->brand.' '.$p[0]->model;
		foreach($p[0]->packs as $k){
			$more .= '<div class="row"><div class="col-4">'. sprintf('%d', $k->qty). '/'.$k->getType().'</div><div class="col-4">'.$k->barcode.'</div><div class="col-4">'. $k->showDim().'</div></div>';
		}

		// task info
		$_GET['ct'] = preg_replace('/CT/', '', $_GET['ct']);
		$wmstasks = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $_GET['ct'] . '%'));
		$rs = [];
		foreach ($wmstasks as $model) {
			$rs = array_merge($rs, $model->getItem($p[0]->id));
		}
		if (empty($rs)) {
			echo '<span style="color:#c00">Product not found in this task!</span>';
		} else {
			$more .= '<div class="row"><div class="col-2">Task</div><div class="col-2">Exp</div><div class="col-2">Bat#</div><div class="col-2">Carton</div><div class="col-2">Unit</div></div>';
			foreach ($rs as $r) {
				$more .= '<div class="row"><div class="col-2">'.$r->task->getNo().'</div><div class="col-2">'.$r->mdata['ex'].'</div><div class="col-2">'.$r->mdata['bn'].'</div><div class="col-2">'. $r->mdata['cq'].'</div><div class="col-2">'.$r->mdata['uq'].'</div></div>';
			}
		}
		echo '<div class="more">', $more, '</div></div>';
	?>
	<form action="<?=$this->createUrl('job/bulkTaskEntry', array('ct' => $_GET['ct'], 'type' => $_GET['type']))?>" method="post">
		<div class="row input-addon">
			<input type="number" placeholder="Carton Qty" name="meta[cq]" style="width: 50%" data-last="<?=empty($_SESSION['entry_meta_'.$model->id]['cq'])? '' : $_SESSION['entry_meta_'.$model->id]['cq'];?>" />
			<input class="uqty" type="number" placeholder="Unit Qty" name="meta[uq]" style="width: 50%" data-last="<?=empty($_SESSION['entry_meta_'.$model->id]['uq'])? '' : $_SESSION['entry_meta_'.$model->id]['uq'];?>" />
		</div>
		<div class="row input-addon">
			<input type="text" class="txtdate" placeholder="Expiry" name="meta[ex]" style="width: 50%"  data-last="<?=empty($_SESSION['entry_meta_'.$model->id]['ex'])? '' : $_SESSION['entry_meta_'.$model->id]['ex'];?>" />
			<input type="text" placeholder="Batch#" name="meta[bn]" style="width: 50%" data-last="<?=empty($_SESSION['entry_meta_'.$model->id]['bn'])? '' : $_SESSION['entry_meta_'.$model->id]['bn'];?>" />
		</div>
		<textarea placeholder="Notes" name="meta[nt]" rows="2"></textarea>
		<?php if (!empty($_POST['plt'])) { ?>
			<input type="hidden" name="meta[pl]" value="<?=$_POST['plt']?>" />
		<?php } ?>
		<input type="hidden" name="meta[gi]" value="<?=$p[0]->id;?>" />
		<input type="hidden" name="meta[gn]" value="<?=$p[0]->name;?>" />
		<button type="submit" class="btn btn-primary btn-block">Save</button>
	</form>
	<?php } ?>
</div>

<script type="text/javascript">
$(function() {
	if ($('.locs_list .table-view-cell').length == 1) {
		var c = $($('.locs_list .table-view-cell')[0]);
		c.addClass('hi');
		$('input.rplt').val(c.data('plt'));
	}

	$('.locs_list .icon-download').on('touchend', function() {
		if(wmaApp.isScrolling) return;
		var c = $(this).parents('.table-view-cell');
		c.siblings().removeClass('hi');
		c.addClass('hi');
		$('input.rplt').val(c.data('plt'));
	});
});
</script>