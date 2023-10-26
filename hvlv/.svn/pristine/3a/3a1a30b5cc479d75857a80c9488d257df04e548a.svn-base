<style>
.row1 .col-6, .col-12 {
	padding-left: 2.5%;
	padding-right: 2.5%;
}
.row1 .col-first {
	padding-left: 2.5%;
}
.row1 .col-last {
	padding-right: 2.5%;
}
.row1 .icon-check {
	color: #5cb85c;
}
.row1 .icon-close {
	color: #d9534f;
}
input[type="search"] {
	height: 34px !important;
}
.toggle.pi_ippc_clear {
	margin: 4px 0 15px;
}
.plt_no {
	text-align: center;
}
</style>
<form action="<?=$this->createUrl('job/demandConfirm', ['id' => $model->id]);?>" method="post" id="demand-confirm-form" data-bit="1">
<?php if (in_array($model->type, [1010])) { ?>
	<div class="row1">
		<div class="col-6">
			<span>需要入库<?=!empty($model->mainTask->mdata['pi_stockin']) ? '<span class="icon icon-check"></span>' : '<span class="icon icon-close"></span>'?></span>
		</div>
		<div class="col-6">
			<span>需要拍照<?=!empty($model->mainTask->mdata['pi_photo']) ? '<span class="icon icon-check"></span>' : '<span class="icon icon-close"></span>'?></span>
		</div>
		<div class="col-6">
			<span>需要提供重量<?=!empty($model->mainTask->mdata['pi_weight']) ? '<span class="icon icon-check"></span>' : '<span class="icon icon-close"></span>'?></span>
		</div>
		<div class="col-6">
			<span>需要点数<?=!empty($model->mainTask->mdata['pi_count']) ? '<span class="icon icon-check"></span>' : '<span class="icon icon-close"></span>'?></span>
		</div>
		<div class="col-6">
			<span>需要检查batch no<?=!empty($model->mainTask->mdata['pi_batch']) ? '<span class="icon icon-check"></span>' : '<span class="icon icon-close"></span>'?></span>
		</div>
		<div class="col-6">
			<span>需要检查有效期<?=!empty($model->mainTask->mdata['pi_expiry']) ? '<span class="icon icon-check"></span>' : '<span class="icon icon-close"></span>'?></span>
		</div>
		<div class="col-12">
			<span>需要分仓<?=!empty($model->mainTask->mdata['pi_split']) ? '<span class="icon icon-check"></span>，请使用电脑下载相关附件' : '<span class="icon icon-close"></span>'?></span>
		</div>
		<div class="col-12">
			<span>ETA: <?=!empty($model->mainTask->mdata['pi_eta']) ? $model->mainTask->mdata['pi_eta'] : '未填'?></span>
		</div>
		<div class="col-12">
			<span>ETD: <?=!empty($model->mainTask->mdata['pi_etd']) ? $model->mainTask->mdata['pi_etd'] : '未填'?></span>
		</div>
		<div class="col-12">
			<span>预期到达板数: <?=!empty($model->mainTask->mdata['pi_expect']) ? $model->mainTask->mdata['pi_expect'] : '未填'?></span>
		</div>
		<div class="col-12">
			<span>Other Requirement: <span style="color: #d9534f;"><?=!empty($model->mainTask->mdata['pi_other']) ? $model->mainTask->mdata['pi_other'] : '无'?></span></span>
		</div>
		<div class="col-12">
			<hr />
		</div>
	</div>
	<div class="row1">
		<?php if (!empty($model->mainTask->mdata['pi_photo'])) { ?>
		<a href="#" class="camera_photo" data-task_id="<?=$model->mainTask->id;?>" style="position:absolute; right:10px; top: 5px;"><img src="<?=Yii::app()->baseUrl;?>/../images/camera.svg" width="40" /></a>
		<?php } ?>
		<?php if (!empty($model->mainTask->mdata['pi_count'])) { ?>
		<div class="col-6">
			<label for="pi_count_number">数量</label>
			<input class="required" type="search" placeholder="数量" name="pi_count_number" value="<?=!empty($model->mainTask->mdata['pi_count_number'])?$model->mainTask->mdata['pi_count_number']:''?>" />
		</div>
		<?php } ?>
		<?php if (!empty($model->mainTask->mdata['pi_batch'])) { ?>
		<div class="col-6">
			<label for="pi_batch_number">Batch No.</label>
			<input class="required" type="search" placeholder="Batch No." name="pi_batch_number" value="<?=!empty($model->mainTask->mdata['pi_batch_number'])?$model->mainTask->mdata['pi_batch_number']:''?>" />
		</div>
		<?php } ?>
		<?php if (!empty($model->mainTask->mdata['pi_expiry'])) { ?>
		<div class="col-6">
			<label for="pi_expiry_number">有效期</label>
			<input class="txtdate required" type="search" placeholder="有效期" name="pi_expiry_number" value="<?=!empty($model->mainTask->mdata['pi_expiry_number'])?$model->mainTask->mdata['pi_expiry_number']:''?>" />
		</div>
		<?php } ?>
		<div class="col-6">
			<label for="pi_wood">木板数</label>
			<input class="" type="search" placeholder="木板数" name="pi_wood" value="<?=!empty($model->mainTask->mdata['pi_wood'])?$model->mainTask->mdata['pi_wood']:0?>" />
		</div>
		<div class="col-6">
			<label for="pi_ippc">IPPC熏蒸木板数</label>
			<input class="" type="search" placeholder="IPPC熏蒸木板数" name="pi_ippc" value="<?=!empty($model->mainTask->mdata['pi_ippc'])?$model->mainTask->mdata['pi_ippc']:0?>" />
		</div>
		<div class="col-6">
			<label for="pi_ippc_clear">熏蒸标是否清晰</label>
			<span><div class="toggle pi_ippc_clear <?=!empty($model->mainTask->mdata['pi_ippc_clear']) ? 'active' : ''?>"><div class="toggle-handle"></div></div></span>
			<input class="" type="hidden" name="pi_ippc_clear" id="pi_ippc_clear" value="<?=!empty($model->mainTask->mdata['pi_ippc_clear']) ? $model->mainTask->mdata['pi_ippc_clear'] : ''?>" />
		</div>
		<div class="col-6">
			<label for="pi_plastic">塑料板数</label>
			<input class="" type="search" placeholder="塑料板数" name="pi_plastic" value="<?=!empty($model->mainTask->mdata['pi_plastic'])?$model->mainTask->mdata['pi_plastic']:0?>" />
		</div>
		<div class="col-6">
			<label for="pi_chep">chep板数</label>
			<input class="" type="search" placeholder="chep板数" name="pi_chep" value="<?=!empty($model->mainTask->mdata['pi_chep'])?$model->mainTask->mdata['pi_chep']:0?>" />
		</div>
		<div class="col-6">
			<label for="pi_loscam">loscam板数</label>
			<input class="" type="search" placeholder="loscam板数" name="pi_loscam" value="<?=!empty($model->mainTask->mdata['pi_loscam'])?$model->mainTask->mdata['pi_loscam']:0?>" />
		</div>
		<div class="col-6">
			<label for="pi_change">换板数</label>
			<input class="" type="search" placeholder="换板数" name="pi_change" value="<?=!empty($model->mainTask->mdata['pi_change'])?$model->mainTask->mdata['pi_change']:0?>" />
		</div>
		<div class="col-12">
			<hr />
		</div>
	</div>
	<div class="row1">
		<div class="col-12">
			<button type="submit" class="btn btn-primary btn-block">Confirm</button>
		</div>
	</div>
<?php } ?>
</form>
<script>
	$(function() {
		$('#demand-confirm-form').on({
			'success': function() {
				$('#summary').load('<?=$this->createUrl("job/summary", ['id' => $model->id]);?>');
			}
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
		$('#demand-confirm-form input').on('keyup', function() {
			var number = parseInt($(this).val());
			number = isNaN(number) ? 0 : number;
    	$(this).val(number);
		});
		$('.toggle.pi_ippc_clear').on('click', function() {
			var value = $('.toggle.pi_ippc_clear').attr('class');
			if (value.indexOf('active') > -1) {
				$('#pi_ippc_clear').val(1);
			} else {
				$('#pi_ippc_clear').val(0);
			}
		});

		var plt = 0;
		$('.icon.icon-plus').on('click', function() {
			plt += 1;
			$(this).parent().parent().before('<div id="plt_' + plt + '"><div class="col-2"><input type="text" disabled="diabled" value="' + plt + '" /></div><div class="col-2"><input type="text" name="length_' + plt + '" id="length_' + plt + '" /></div><div class="col-2"><input type="text" name="width_' + plt + '" id="width_' + plt + '" /></div><div class="col-2"><input type="text" name="height_' + plt + '" id="height_' + plt + '" /></div><div class="col-2"><input type="text" name="vol_' + plt + '" id="vol_' + plt + '" disabled="diabled" /></div><div class="col-2"><input type="text" name="weight_' + plt + '" id="weight_' + plt + '" /></div></div>');

			$('input[id^=length_' + plt + ']').on('keyup', function() {
				var volumn = 0;
				for (var i = 1; i <= plt; i ++) {
					value = Number($('#length_' + i).val()) * Number($('#width_' + i).val()) * Number($('#height_' + i).val());
					volumn += value;
					$('#vol_' + i).val(value);
				}
				$('#tot_volumn').text(volumn);
			});

			$('input[id=width_' + plt + ']').on('keyup', function() {
				var volumn = 0;
				for (var i = 1; i <= plt; i ++) {
					value = Number($('#length_' + i).val()) * Number($('#width_' + i).val()) * Number($('#height_' + i).val());
					volumn += value;
					$('#vol_' + i).val(value);
				}
				$('#tot_volumn').text(volumn);
			});

			$('input[id=height_' + plt + ']').on('keyup', function() {
				var volumn = 0;
				for (var i = 1; i <= plt; i ++) {
					value = Number($('#length_' + i).val()) * Number($('#width_' + i).val()) * Number($('#height_' + i).val());
					volumn += value;
					$('#vol_' + i).val(value);
				}
				$('#tot_volumn').text(volumn);
			});

			$('input[id=weight_' + plt + ']').on('keyup', function() {
				var weight = 0;
				for (var i = 1; i <= plt; i ++) {
					weight += Number($('#weight_' + i).val());
				}
				$('#tot_weight').text(weight);
			});
		});
	});
</script>