<div class="data-form">
<?php
if(!empty($err)){
	echo '<span style="color:#c00">'.implode('<br />', $err).'</span>';
}else{
if(!empty($rs[0])){
	echo '<b>'.$rs[0]->stock->prod->name.'</b>';
}
?>
<form action="<?=$this->createUrl('job/entry', ['id' => $model->id]);?>" method="post">
<?php
foreach($rs as $i=>$r):
if($r->stock_id != $_POST['sid']) continue;
$pq = min($_POST['pq'], $r->qty);
?>
<div class="row" style="border-top: 1px solid #aaa; padding-top: 5px;">
	<div class="col-6"><p style="font-size:1em;margin-bottom: 0;">Exp: <?=$r->stock->expiry;?><br />Bat: <?=$r->stock->batch;?></p></div>
	<?php
	$cq = 0;
	foreach ($model->mainTask->items as $item) {
		if ($item->mdata['si'] == $_POST['sid']) {
			$cq += intval(@$item->mdata['cq']);
		}
	}

	if($cq > 0){
		$cq = min($r->stock->prod->uq2cq($_POST['pq'], 'floor'), $r->stock->prod->uq2cq($r->qty, 'floor'));
	}

	if (!empty($cq)) {
	?>
	<div class="col-6"><input class="pkq" type="number" placeholder="Qty" name="ck[<?=$r->id;?>]" style="width: 60px" data-stock="<?=$r->stock->stockName();?>" max="<?=$cq;?>" min="0" value="<?=$cq?>" /> / <b><?=sprintf('%d', $cq);?></b> <span style="color:red">Cartons</span>
	</div>
	<?php } else { ?>
	<div class="col-6"><input class="pkq" type="number" placeholder="Qty" name="pk[<?=$r->id;?>]" style="width: 60px" data-stock="<?=$r->stock->stockName();?>" max="<?=$pq;?>" min="0" value="<?=$pq?>" /> / <b><?=sprintf('%d', $pq);?></b> <span style="color:red">Units</span>
	</div>
	<?php } ?>

	<?php if (in_array(Yii::app()->user->id, WmsTask::$op) || (isset(Yii::app()->user->grp) && Yii::app()->user->grp == 0)) { ?>
	<div class="col-6"><p style="font-size:1em;margin-bottom: 0;">缺货数量</p></div>
	<?php if (!empty($cq)) { ?>
	<div class="col-6"><input class="pkq" type="number" placeholder="Qty" name="shortck[<?=$r->id;?>]" style="width: 60px" data-stock="<?=$r->stock->stockName();?>" max="<?=$cq;?>" min="0" value="0" /> / <b><?=sprintf('%d', $cq);?></b> <span style="color:red">Cartons</span>
	</div>
	<?php } else { ?>
	<div class="col-6"><input class="pkq" type="number" placeholder="Qty" name="shortpk[<?=$r->id;?>]" style="width: 60px" data-stock="<?=$r->stock->stockName();?>" max="<?=$pq;?>" min="0" value="0" /> / <b><?=sprintf('%d', $pq);?></b> <span style="color:red">Units</span>
	</div>
	<?php } ?>
	<?php } ?>

	<!-- <?php if (in_array($model->job->org_id, [1275]) && $r->stock->prod->name == 'Ecomatters 285W Solar PV Module') { ?>
	<div class="row">
		<input type="text" placeholder="序列号" name="serial" id="serial" required="required" />
	</div>
	<?php } ?> -->
</div>
<?php endforeach; ?>
<button type="submit" class="btn btn-primary btn-block">Save</button>
</form>
<?php } ?>
</div>
<script type="text/javascript">
$(function(){
	$('.pkq:first').focus();
	$('.data-form form').on('success', function(e, r){
		$('#ajax-modal').trigger('loadModal');
	});
	$('#serial').on('keydown', function(e) {
		if (e.which == 13) {
			return false;
		}
	});
	$('.data-form form').on('submit', function(e) {
		if ($('.pkq').val() == '') {
			alert('Please input qty');
			return false;
		}
		var total = '<?=$cq ? $cq : $pq?>';
		$('.data-form form').find('.pkq').each(function() {
			total -= parseInt($(this).val());
		});
		if (total < 0) {
			alert('Qty cannot exceed total');
			return false;
		}
	});
});
</script>