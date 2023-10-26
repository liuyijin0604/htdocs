<div class="content-padded">
<a class="btn btn-positive modal_link pull-right" href="<?=$this->createUrl('job/task',['id' => $model->task_id]);?>">Back to Task</a>
<?php
$stk = WmsStock::model()->findByPk($model->mdata['si']);
?>
<h2 align="center"><span class="sn_count" style="font-size: 1.5em;"><?=$model->serialScanned();?></span> / <span class="sn_total"><?=$model->totalUq();?></span></h2>
<div style="margin-bottom: 10px;"><?=$model->mdata['sn'];?><br />
<p><?=$stk->prod->ean;?></p></div>
<div class=""></div>
<form class="sn-form" action="<?=$this->createUrl('job/recSerial',['id' => $model->id]);?>" method="post" data-bit="3">
	<input class="snbc barcode required hi" type="search" placeholder="Serial Barcode" name="sn" value="" autocomplete="off">
	<button type="submit" class="btn btn-primary btn-block" name="search"><span class="icon icon-plus"></span>Add</button>
</form>
<div class="res" style="padding: 10px 0; color: #c00;">
</div>
<div class="his" style="padding: 10px 0;">
<ul class="table-view">
<?php
$sns = WmsSerialNo::model()->findAll('task_id = :tid AND stock_id = :sid', [':tid' => $model->task->link_id, ':sid' => $model->mdata['si']]);
foreach($sns as $ns){
	echo '<li class="table-view-cell table-view-cell-full"><p>'.$ns->sn;
	if ($model->task->mainTask->status < 99) {
		echo '<a class="del_item pull-right" href="'.$this->createUrl('job/delSerial',['id' => $ns->id]).'" data-vc="'.$this->randVc().'"><span class="icon icon-trash" style="font-size:1.2em"></span></a>';
	}
	echo '</p></li>';
}
?>
</ul></div>
</div>
<script type="text/javascript">
$(function(){
	$('.sn-form input.snbc').focus().on('keydown', function(e){
		if(e.which == 13){
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(){
		if($('.sn-form .snbc').hasClass('active')){
			$('.sn-form form').submit();
			return true;
		}
	});

	$('.sn-form').on('success', function(e, r){
		wmaApp.btnLoading($('button[type=submit]', this), true);
		if($('input.snbc').length > 0) $('input.snbc').val('').focus();

		if(r.done){
			$('.his .table-view').prepend('<li class="table-view-cell table-view-cell-full"><p>'+r.sn+' <a class="del_item pull-right" href="<?=substr($this->createUrl('job/delSerial'),0,-5);?>/'+r.id+'" data-vc="'+r.vc+'"><span class="icon icon-trash" style="font-size:1.2em"></span></a></p></li>');
			$('.his').trigger('update');
			$('.res').html('');
		}else{
			$('.res').html(r.msg);
		}
	});

	$('.his').on('touchend', 'a.del_item', function(){
		var me = $(this);
		if(window.prompt('Please enter "'+me.data('vc')+'" to confirm delete') == me.data('vc')){
			if(window.confirm('Are you sure to delete?')){
				$.get(me.attr('href'), function(){
					me.parents('.table-view-cell').remove();
					$('.his').trigger('update');
				});
			}
		}
		return false;
	}).on('update', function(){
		var sncount = $('.his a.del_item').length;
		$('.sn_count').text(sncount);
		if(sncount >= Number($('.sn_total').text())){
			$('.sn-form').fadeOut();
		}else{
			$('.sn-form').show();
		}
	}).trigger('update');
});
</script>