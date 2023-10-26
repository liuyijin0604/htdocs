<h3><?=$model->getType();?> <?php if (in_array($model->type, [3020,3030])) { ?><button class="btn btn-negative hold-btn">Hold订单</button><?php } ?> <span class="exp-ef icon icon-down" style="display:none;"></span></h3>
<div class="entry-form">
<?php if ($model->mainTask->status < 99 || ($model->mainTask->status == 99 && !empty($model->mainTask->pickupTask) && empty($model->mainTask->pickupTask->mdata['pickup']))) { ?>
<form action="<?=$this->createUrl('job/task', ['id' => $model->id]);?>" method="post" data-bit="3">
<?php if (in_array($model->type, [3010])) { ?>
	<?php
	$np = $model->nextPltPick();
	echo '<div class="table-view-cell table-view-cell-full" style="margin: -15px -15px 0 -15px;">';
	echo '<b>Next: ' . $np . '</b>';
	echo '</div>';
	?>
	<input class="plt barcode required" type="search" placeholder="Pallet Barcode" name="plt" />
<?php } elseif (in_array($model->type, [1010, 1030])) { ?>
	<div class="input-addon"><input class="plt barcode required" type="search" placeholder="Pallet Barcode" name="plt" />
	<span><div class="toggle kplt"><div class="toggle-handle"></div></div></span>
	</div>
	<div class="input-addon"><input class="prod barcode required" type="search" placeholder="Product Name/Barcode" name="prod" />
	<span><div class="toggle kprod"><div class="toggle-handle"></div></div></span>
	</div>
<?php } elseif (in_array($model->type, [1020, 7020])) { ?>
	<input class="prod barcode required" type="search" placeholder="Product Name/Barcode" name="prod" />
<?php } elseif (in_array($model->type, [2030, 2040])) { ?>
	<h4><?=(empty($model->mainTask->mdata['ctn_no']) ? $model->mainTask->ref : ($model->mainTask->mdata['ctn_no']) . ' (' . $model->mainTask->mdata['ctn_size'] . ')');?></h4><a href="#" class="camera_photo" data-task_id="<?=$model->mainTask->id;?>" style="position:absolute; right:10px; top: 5px;"><img src="<?=Yii::app()->baseUrl;?>/../images/camera.svg" width="40" /></a>
	<input class="plt barcode required" type="search" placeholder="Pallet Barcode" name="plt" />
<?php } elseif (in_array($model->type, [3020, 3030])) {
	$np = $model->nextItemPick();
	if ($np[0] == 9) {
		// easyship auto complete
		// if (in_array($model->job->org_id, Org::$easyships)) {
		// 	$model->mainTask->status = 99;
		// 	$model->mainTask->update('status');
		// }
		echo '<div class="task_complete"></div>';
	} elseif ($np[0] == 3) {
		echo '<h4 style="color: #c00">Not enough stock to complete this task</h4>';
		foreach ($np[1] as $sid => $s) {
			if (empty($s)) {
				continue;
			}

			echo '<p>' . $s->stockName() . '</p>';
		}
	} elseif ($np[0] == 1) {
		echo '<div class="table-view-cell table-view-cell-full" style="margin: -15px -15px 0 -15px;">';
		if (!$np[1][0]) {
			echo '<b style="color: #c00">Not enough stock for</b>';
		}
		foreach ($np[1][1] as $i => $l) {
			if ($i == 0) {
				echo '<b>' . $np[2] . '</b> &times; ' . $l[0]->stock->stockName() . '<p>' . $l[0]->stock->prod->ean . '</p>';
				$bl = $l[0]->loc->parent->code;
				$sid = $l[0]->stock_id;
			}
			echo '<div class="more" data-plt="' . $l[0]->loc->code . '">' . $l[0]->loc->code . ' (' . $l[0]->loc->parent->name . ') &nbsp; Qty: ' . $l[0]->qty . '</div>';
		}
		echo '</div>';
		?>
		<input type="hidden" name="sid" value="<?=$sid;?>" />
		<input type="hidden" name="pq" value="<?=$np[2];?>" />
		<input class="plt barcode required" type="search" placeholder="Pallet Barcode" name="plt" value="<?=$bl;?>" />
		<input class="prod barcode required" type="search" placeholder="Product Name/Barcode" name="prod" />
	<?php
	}
} else if (in_array($model->type, [3050])) {
	$this->widget('CMultiFileUpload', array(
		'model' => $model,
		'attribute' => 'photos',
		'accept' => 'jpg|gif|png',
		'htmlOptions' => ['accept' => 'image/gif, image/jpeg', 'style' => 'margin-bottom: 20px'],
		'options' => array(),
		'denied' => 'File is not allowed',
		'max' => 10, // max 10 files
	));
}
if (in_array($model->type, [3050])) { ?>
	<button type="submit" class="btn btn-primary btn-block" name="search"><span class="icon icon-share"></span>&nbsp;Upload</button>
<?php } else { ?>
	<button type="submit" class="btn btn-primary btn-block" name="search"><span class="icon icon-search"></span>Search</button>
<?php } ?>
</form>
<?php } else {
	echo '<div class="task_complete"></div>';
} ?>
</div>
<div class="res">
</div>
<div class="his">
</div>


<script type="text/javascript">
$(function(){
	$('.entry-form input.plt').focus().on('keydown', function(e){
		if(e.which == 13){
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(){
		if($('.entry-form .kprod').hasClass('active') || $('input.prod').length == 0){
			$('.entry-form form').submit();
			return true;
		}
		if($('input.prod').length > 0) $('input.prod').focus();
	});

	$('.entry-form input.prod').on('afterBarcode', function(){
		if($('input.plt').length == 0 || $('input.plt').val() != '') $('.entry-form form').submit();
	});

	$('span.exp-ef').on('click touchend', function(){
		$('.entry-form').slideDown();
		$('span.exp-ef').hide();
	});

	$('.entry-form form').on('success', function(e, r){
		wmaApp.btnLoading($('button[type=submit]', this), true);
		$('.res').html(r.data);
		$('div.his').empty();
		if(r.done){
			// single item stock in
			if ('<?=!empty($model->mainTask->mdata["single_item"])?>') {
				$('div.his').trigger('showHistory');
				if (!$('.entry-form form .kprod').hasClass('active')) {
					$('.entry-form form .prod').val('');
				}
				if (!$('.entry-form form .kplt').hasClass('active')) {
					$('.entry-form form .plt').val('');
				}
				return false;
			}

			$('.entry-form').slideUp();
			$('span.exp-ef').show();
			if($('input.rplt.required').length > 0){
				$('input.rplt.required').focus();
			}else{
				$('input.uqty').focus();
			}
			if($('.entry-form form .kprod').hasClass('active')){
				$('.data-form form input').each(function(){
					var lv = $(this).data('last') || false;
					if(lv)	$(this).val(lv);
				});
			}
		}else{
			if($('input.prod').length > 0) $('input.prod').val('').focus();
			else $('input.plt').val('').focus();
		}
	});

	$('.his').off('showHistory').on('showHistory', function(){
		$(this).load('<?=$this->createUrl('job/history', ['id' => $model->id]);?>');
	}).on('touchend', 'a.del_item', function(){
		if(wmaApp.isScrolling) return;
		if(window.prompt('Please enter "'+$(this).data('vc')+'" to confirm delete') == $(this).data('vc')){
			if(window.confirm('Are you sure to delete?')){
				$.get($(this).attr('href'), function(){
					$('.his').trigger('showHistory');
				});
			}
		}
		return false;
	}).trigger('showHistory');

	$('.task_complete').off('showTaskComplete').on('showTaskComplete', function() {
		$(this).load('<?=$this->createUrl("job/taskComplete", ["id" => $model->id]);?>');
	}).on('touchend', 'a.switch_item', function() {
		if (wmaApp.isScrolling) return;
		if (window.prompt('Please enter "' + $(this).data('vc') + '" to confirm switch') == $(this).data('vc')) {
			if (window.confirm('Are you sure to switch?')) {
				$.get($(this).attr('href'), function(r) {
					r = JSON.parse(r);
					if (r.done) {
						$('#notifc').notify({message: {html: 'Successfully'}, closable: false}).show();
					} else {
						$('#notifc').notify({message: {html: 'Failed'}, closable: false, type: 'danger'}).show();
					}
					$('.task_complete').trigger('showTaskComplete');
				});
			}
		}
		return false;
	}).trigger('showTaskComplete');

	if($('.entry-form input').length == 0) $('.entry-form button[name=search]').hide();
});
</script>