<style>
.btn {
	border:0;
	outline:0;
}
.badge {
	color: white;
	background-color: #D9534F;
}
</style>
<div class="card" style="border:0;">
<button type="button" class="btn" data-toggle="collapse" data-target="#card" style="width:100%; background-color:white;">Show / Hide</button>
</div>
<div class="card collapse in" style="border:0;" id="card">
<ul class="table-view">
	<li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/putaway');?>" data-transition="slide-in"><b>Put Away</b></a>
	</li>
	<!-- <li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/picking');?>" data-transition="slide-in"><b>Picking</b></a>
	</li> -->
	<!-- <li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/stocktake');?>" data-transition="slide-in"><b>Stock Take</b></a>
	</li> -->
	<li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/relocate');?>" data-transition="slide-in"><b>Stock Relocation</b></a>
	</li>
	<li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/storage', array('type' => 'in'));?>" data-transition="slide-in"><b>Storage In</b></a>
	</li>
	<li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/storage', array('type' => 'search'));?>" data-transition="slide-in"><b>Storage Out</b></a>
	</li>
	<li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/dayBookingSummary');?>" data-transition="slide-in"><b>Booking Record</b></a>
	</li>
	<li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/shipmentRackRecording');?>" data-transition="slide-in"><b>Shipment Rack Recording</b></a>
	</li>
	<li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/setPltInfo');?>" data-transition="slide-in"><b>Pallet Info</b></a>
	</li>
	<li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/ScanOut');?>" data-transition="slide-in"><b>Scan Out</b></a>
	</li>
	<li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/batchSortingCheck');?>" data-transition="slide-in"><b>Sorting Check</b></a>
	</li>
	<li class="table-view-cell">
		<a class="push-right" href="<?=$this->createUrl('job/splitSorting');?>" data-transition="slide-in"><b>Split Sorting</b></a>
	</li>
</ul>
</div>
<div class="row" style="padding: 5px 10px;">
	<div class="col-8"><input type="search" id="task_search" placeholder="Task No/Ref" name="ref" /></div>
	<div class="col-4"><button type="submit" id="task_search_btn" class="btn btn-primary" style="line-height: 32px; padding: 0 20px; margin-left: 10px; font-size: 1em;">Search</button></div>
</div>
<div class="row" style="padding: 0 10px 15px;">
	<div class="col-3"><button type="submit" id="pick_unit_btn" class="btn" style="width:90%; color:white; background-color:#5cb85c;">Pick Unit</button></div>
	<div class="col-3"><button type="submit" id="pallet_in_btn" class="btn" style="width:90%; color:white; background-color:#f0ad4e;">Pallet In</button></div>
</div>
<ul class="table-view lazy-load" data-more-url="<?=Yii::app()->request->baseUrl; ?>/?WmsTask_page=1"></ul>

<script type="text/javascript">
$(function(){
	var type = 0;

	$('#card').collapse('hide');

	$('#card').on('hide.bs.collapse', function() {
		$('#card').css('margin', '0');
	});
	$('#card').on('show.bs.collapse', function() {
		$('#card').css('margin', '10px');
	});
	$('#task_search_btn').on('click', function(){
		type = 0;
		$('.lazy-load').data({'more-url':'<?=Yii::app()->request->baseUrl; ?>/?WmsTask_page=1&q='+$('#task_search').val()}).empty().trigger('lazyLoad');
	});
	$('#pick_unit_btn').on('click', function() {
		type = 3030;
		$('.lazy-load').data({'more-url':'<?=Yii::app()->request->baseUrl; ?>/?WmsTask_page=1&q='+$('#task_search').val()+'&type=3030'}).empty().trigger('lazyLoad');
	});
	$('#pallet_in_btn').on('click', function() {
		type = 1010;
		$('.lazy-load').data({'more-url':'<?=Yii::app()->request->baseUrl; ?>/?WmsTask_page=1&q='+$('#task_search').val()+'&type=1010'}).empty().trigger('lazyLoad');
	});

	$('.lazy-load').data({'more-url':'<?=Yii::app()->request->baseUrl; ?>/?WmsTask_page=1&q='+$('#task_search').val()}).empty().trigger('lazyLoad');
});
</script>