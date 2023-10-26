<style type="text/css">
video {
	object-fit: cover;
	transform: scale(0.3, 0.3); -webkit-transform: scale(0.3, 0.3);
	transform-origin: 0 0; -webkit-transform-origin: 0 0;
}
</style>
<div class="content-padded ">
<div id="action" class="tab-pane <?=!empty($model->mainTask->mdata['simple_in']) ? '' : 'active'?>">
	<?=$this->renderPartial('_task', ['model' => $model])?>
</div>
<div id="demand" class="tab-pane">
</div>
<div id="plt_sunnya" class="tab-pane">
</div>
<div id="plt" class="tab-pane <?=!empty($model->mainTask->mdata['simple_in']) ? 'active' : ''?>">
</div>
<div id="plt_ct" class="tab-pane">
</div>
<div id="notes" class="tab-pane">
</div>
<div id="summary" class="tab-pane">
</div>
<div id="sign" class="tab-pane">
</div>
<div id="courier" class="tab-pane">
</div>
<div id="label" class="tab-pane">
</div>
<div id="other" class="tab-pane">
</div>
<div id="double_check" class="tab-pane">
</div>
</div>
<div class="segmented-control tabs bottom">
	<?php if ($model->type == 1010 && !empty($model->mainTask->mdata['simple_in'])) { ?>
		<a class="control-item active" data-pane="plt">Plt</a>
	<?php } else { ?>
			<a class="control-item active" data-pane="action">Act</a>
		<?php if ($model->type == 1010 && !empty($model->mainTask->mdata['pi_cargo'])) { ?>
			<a class="control-item" data-pane="demand">D</a>
			<a class="control-item" data-pane="plt">Plt</a>
			<a class="control-item" data-pane="notes">N</a>
		<?php } else if ($model->type == 1010) { ?>
			<a class="control-item" data-pane="plt">Plt</a>
		<?php } else if (in_array($model->type, [3010, 3020, 3030]) && in_array($model->job->org_id, Org::$directLabel)) { ?>
			<a class="control-item" data-pane="courier">Courier</a>
		<?php } else if (in_array($model->type, [3020]) && !empty($model->job->customer->extra['carton_label'])) { ?>
			<a class="control-item" data-pane="label">Label</a>
		<?php } else if (in_array($model->type, [2030])) { ?>
			<a class="control-item" data-pane="plt_ct">Plt</a>
		<?php } else if (in_array($model->type, [3020, 3030]) && in_array($model->job->org_id, [1, Org::ORGID_3PL_SUNNYA])) { ?>
			<a class="control-item" data-pane="plt_sunnya">Sunnya Plt</a>
		<?php } ?>
		<?php if (in_array($model->type, [3020, 3030]) && in_array($model->job->org_id, [1, Org::ORGID_3PL_LIFESTYLE]) && preg_match('/SO/', $model->mainTask->ref)) { ?>
			<a class="control-item" data-pane="double_check">检查</a>
		<?php } ?>
		<a class="control-item" data-pane="other">O</a>
		<a class="control-item" data-pane="summary">Sum</a>
		<?php if (in_array($model->type, [2030, 3010, 3020, 3030])) { ?>
			<a class="control-item" data-pane="sign">Sign</a>
		<?php } ?>
	<?php } ?>
</div>
<div id="cam" style="position:absolute; top:0; left: 0;width:100%; height:100%;background: #000;z-index:99;display:none;"><div id="cam_live" sytle="max-width:100%;max-height:700px;"></div><div style="position:absolute; bottom: 10%; width: 50%;left:25%; text-align:center;"><input type="text" name="cam_note" id="cam_note" /><button type="button" class="btn btn-primary btn-block snap" style="border-radius:200px;width:100px;margin:0 auto;"><span class="icon icon-star-filled"></span> Snap</button><br /><button type="button" class="btn btn-negative cancel">Cancel</button></div></div>
<div id="instruction_modal" class="modal<?=empty($model->mainTask->mdata['note']) || !empty(Yii::app()->cache->get('ack' . $model->id))? '' : ' active';?>">
  <header class="bar bar-nav">
    <a class="icon icon-close pull-right" href="#instruction_modal"></a>
    <h1 class="title"><?=$model->mainTask->no;?> Notes</h1>
  </header>

  <div class="content">
    <p class="content-padded" style="font-size: 1.2em; color: #c00;"><?=nl2br(@$model->mainTask->mdata['note']);?></p>
  	<div class="bar bar-standard bar-footer">
  		<button class="btn btn-primary btn-block btn-close" style="font-size: 1.2em;">Acknowledge and Continue</button>
	</div>
  </div>
</div>
<script type="text/javascript">
$(function(){
	$('#ajax-modal').off('submit').on('submit', '.data-form form', function(e, r) {
		var ef = $('.entry-form form');
		$('.plt', ef).attr('disabled', true);
		$('.prod', ef).attr('disabled', true);
	});

	$('#ajax-modal').off('success').on('success', '.data-form form', function(e, r){
		$('.entry-form').slideDown();
		$('span.exp-ef').hide();
		$('.res').empty();
		var ef = $('.entry-form form');
		$('.plt', ef).removeAttr('disabled');
		$('.prod', ef).removeAttr('disabled');
		if($('.kplt', ef).hasClass('active') || $('.plt', ef).length == 0){
			$('.prod', ef).focus();
		}else{
			$('.plt', ef).val('');
		}
		if($('.kprod', ef).hasClass('active') || $('.prod', ef).length == 0){
			$('.plt', ef).focus();
		}else{
			$('.prod', ef).val('');
		}
		if ($('.plt', ef).val() == '') {
			$('.plt', ef).focus();
		}
		$('.his').trigger('showHistory');
	});

	$('#ajax-modal').off('error').on('error', '.data-form form', function(e, r){
		var ef = $('.entry-form form');
		$('.plt', ef).removeAttr('disabled');
		$('.prod', ef).removeAttr('disabled');
	});

	if ('<?=!empty($model->mainTask->mdata['simple_in'])?>') {
		setTimeout(function() {
			$('#plt').trigger('showing');
		}, 1e2);
	}

	$('#demand').on('showing', function() {
		$(this).load('<?=$this->createUrl("job/demand", ["id" => $model->id]);?>');
	});

	$('#plt').on('showing', function() {
		$(this).load('<?=$this->createUrl("job/plt", ["id" => $model->id]);?>');
	});

	$('#plt_ct').on('showing', function() {
		$(this).load('<?=$this->createUrl("job/pltCT", ["id" => $model->id]);?>');
	});

	$('#plt_sunnya').on('showing', function() {
		$(this).load('<?=$this->createUrl("job/pltSunnya", ["id" => $model->id]);?>');
	});

	$('#notes').on('showing', function() {
		$(this).load('<?=$this->createUrl("job/note", ["id" => $model->id]);?>');
	});

	$('#summary').on('showing', function(){
		$(this).load('<?=$this->createUrl("job/summary", ['id' => $model->id]);?>');
	});

	$('#sign').on('showing', function() {
		$(this).load('<?=$this->createUrl("job/sign", ['id' => $model->id]);?>');
	});

	$('#courier').on('showing', function() {
		$(this).load('<?=$this->createUrl("job/courier", ['id' => $model->id]);?>');
	});

	$('#label').on('showing', function() {
		$(this).load('<?=$this->createUrl("job/label", ['id' => $model->id]);?>');
	});

	$('#other').on('showing', function() {
		$(this).load('<?=$this->createUrl("job/other", ['id' => $model->id]);?>');
	});

	$('#double_check').on('showing', function() {
		$(this).load('<?=$this->createUrl("job/doubleCheck", ['id' => $model->id]);?>');
	});

	$('a.camera_photo').on('touchend', function(){
		$('#cam').show();
		Webcam.set({
			width: 360,
			height: 480,
			dest_width: 1080,
			dest_height: 1440,
			image_format: 'jpeg',
			jpeg_quality: 90,
			constraints: {
				optional: [ {minWidth: 360} ]
			}
		});
		Webcam.attach('#cam_live');
		return false;
	});
	$('#cam button.cancel').on('touchend', function(){
		Webcam.reset();
		$('#cam').hide();
		return false;
	});
	$('#cam button.snap').on('touchend', function(){
		Webcam.snap(function(data_uri){
			wmaApp.queuePhoto($('a.camera_photo').data('task_id'), data_uri, $('#cam #cam_note').val());
		});
		$('#cam').hide().fadeIn();
		return false;
	});

	$('#instruction_modal .btn-close').on('touchend', function(){
		$('#instruction_modal').removeClass('active');
		$.ajax({
			type: 'GET',
			data: { 'id': '<?=$model->id?>' },
			url: '<?=Yii::app()->createUrl("job/ack")?>',
		});
	});

	$('#action .hold-btn').on('click', function() {
		if (confirm('确认hold订单 ' + '<?=$model->mainTask->getNo()?>')) {
			$.ajax({
				type: 'GET',
				data: { 'id': '<?=$model->id?>' },
				url: '<?=Yii::app()->createUrl("job/holdTask")?>',
				success: function() {
					$('.bar-nav .icon-close').trigger('touchend');
				}
			});
		}
	});
});
</script>