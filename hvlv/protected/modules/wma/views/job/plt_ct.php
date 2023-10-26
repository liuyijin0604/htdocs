<style>
#plt-ct-form .row1 .demand-col-6, .demand-col-12 {
	padding-left: 2.5%;
	padding-right: 2.5%;
}
#plt-ct-form .row1 .col-first {
	padding-left: 2.5%;
}
#plt-ct-form .row1 .col-last {
	padding-right: 2.5%;
}
#plt-ct-form .row1 .icon-check {
	color: #5cb85c;
}
#plt-ct-form .row1 .icon-close {
	color: #d9534f;
}
#plt-ct-form input[type="search"] {
	height: 34px !important;
}
#plt-ct-form .toggle.pi_ippc_clear {
	margin: 4px 0 15px;
}
#plt-ct-form .plt_no {
	text-align: center;
}
</style>
<form action="<?=$this->createUrl('job/pltCT', ['id' => $model->id]);?>" method="post" id="plt-ct-form" data-bit="1">
<?php if (in_array($model->type, [2030])) { ?>
	<div class="row1">
		<div class="col-6 demand-col-6">
			<label for="pi_wood">木板数</label>
			<input class="" type="search" placeholder="木板数" name="pi_wood" value="<?=!empty($model->mainTask->mdata['pi_wood'])?$model->mainTask->mdata['pi_wood']:0?>" />
		</div>
		<div class="col-6 demand-col-6">
			<label for="pi_ippc">IPPC熏蒸木板数</label>
			<input class="" type="search" placeholder="IPPC熏蒸木板数" name="pi_ippc" value="<?=!empty($model->mainTask->mdata['pi_ippc'])?$model->mainTask->mdata['pi_ippc']:0?>" />
		</div>
		<div class="col-6 demand-col-6">
			<label for="pi_plastic">塑料板数</label>
			<input class="" type="search" placeholder="塑料板数" name="pi_plastic" value="<?=!empty($model->mainTask->mdata['pi_plastic'])?$model->mainTask->mdata['pi_plastic']:0?>" />
		</div>
		<!-- <div class="col-6 demand-col-6">
			<label for="pi_chep">chep板数</label>
			<input class="" type="search" placeholder="chep板数" name="pi_chep" value="<?=!empty($model->mainTask->mdata['pi_chep'])?$model->mainTask->mdata['pi_chep']:0?>" />
		</div>
		<div class="col-6 demand-col-6">
			<label for="pi_loscam">loscam板数</label>
			<input class="" type="search" placeholder="loscam板数" name="pi_loscam" value="<?=!empty($model->mainTask->mdata['pi_loscam'])?$model->mainTask->mdata['pi_loscam']:0?>" />
		</div> -->
		<div class="col-12 demand-col-12">
			<hr />
		</div>
	</div>
	<div class="row1">
		<div class="col-12 demand-col-12">
			<button type="submit" class="btn btn-primary btn-block">Submit</button>
		</div>
	</div>
<?php } ?>
</form>
<script>
	$(function() {
		$('#plt-ct-form').on({
			'success': function() {
				$('#demand').load('<?=$this->createUrl("job/demand", ['id' => $model->id]);?>');
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
		$('#plt-ct-form input').on('keyup', function() {
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