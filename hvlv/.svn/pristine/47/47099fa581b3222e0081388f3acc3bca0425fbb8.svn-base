<div class="content-padded">
	<h3>Delivery Record</h3>
	<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'sign-form',
		'enableAjaxValidation' => false,
	)); ?>
		<div class="row">
			<div class="col-6" style="padding-right: 5px;">
				Rego<?php echo CHtml::textField('rego', @$model->rego, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Company Name','disabled'=>'disabled')); ?>
			</div>
			<div class="col-6" style="padding-right: 5px;">
				<?php echo CHtml::hiddenField('id', @$model->id, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Rego')); ?>
				Company Name<?php echo CHtml::textField('mdata[company_name]',  @$model->mdata['company_name'], array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Company Name','disabled'=>'disabled')); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-4" style="padding-right: 5px;">
				Booking No<?php echo CHtml::textField('no', @$model->no, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'No','disabled'=>'disabled')); ?>
			</div>
			<div class="col-4" style="padding-right: 5px;">
				Booking Type<?php echo CHtml::dropDownList('mdata[booking_type]',@$model->mdata['booking_type'], DeliveryRecord::$booking_type, array('prompt'=>'Select','class'=>'form-control')); ?>
			</div>
			<div class="col-4" style="padding-left: 10px; padding-top: 7px;">
				<label for="damage" style="float: left; padding-right: 5px;">Damage</label>
				<span style="float: left;"><div class="toggle damage <?=empty($model->damage)?"":"active" ?>" ><div class="toggle-handle"></div></div></span>
				<input type="hidden" name="damage" id="damage" value="<?=$model->damage?>" />
			</div>
		</div>
		<div class="row">
			<div class="col-6" style="padding-left: 5px;">
				Pallets<?php echo CHtml::textField('plt',  @$model->plt, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Pallet')); ?>
			</div>
			<div class="col-6" style="padding-left: 5px;">
				Pkg<?php echo CHtml::textField('pkg',  @$model->pkg, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Pallet')); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-12" style="padding-left: 0; padding-right: 0;">
				Note<?php echo CHtml::textField('note', @$model->note, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Note')); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-12" style="padding-left: 0; padding-right: 0;">
					<?php
			  $this->widget('CMultiFileUpload', array(
			     'model'=>$model,
			     'attribute'=>'photos',
			     'accept'=>'jpg|gif|png',
			     'htmlOptions'=>["accept"=>"image/gif, image/jpeg"],
			     'options'=>array(
			     ),
			     'denied'=>'File is not allowed',
			     'max'=>10, // max 10 files
			  ));
			?>
			</div>
		</div>

		<div class="row">
			<canvas style="border: 1px solid #ddd; width: 100%"></canvas>
			<?php echo CHtml::hiddenField('sig'); ?>
		</div>


		<div class="row">
			<div class="col-6" style="padding-right: 5px;">
				<?php echo CHtml::button($this->t('Clear Signature'), array('class' => 'clear btn btn-primary btn-block')); ?>
			</div>
			<div class="col-6" style="padding-left: 5px;">
				<?php echo CHtml::submitButton($this->t('Save'), array('class' => 'save_btn btn btn-primary btn-block')); ?>
			</div>
		</div>


		<a href="#" class="camera_photo" data-task_id="" style="position:absolute; right:10px; top: 5px;"><img src="<?=Yii::app()->baseUrl;?>/../images/camera.svg" width="40" /></a>
		<div id="cam" style="position:absolute; top:0; left: 0;width:100%; height:100%;background: #000;z-index:99;display:none;"><div id="cam_live" sytle="max-width:100%;max-height:100%;"></div><div style="position:absolute; bottom: 10%; width: 50%;left:25%; text-align:center;"><input type="text" name="cam_note" id="cam_note" /><button type="button" class="btn btn-primary btn-block snap" style="border-radius:200px;width:100px;margin:0 auto;"><span class="icon icon-star-filled"></span> Snap</button><br /><button type="button" class="btn btn-negative cancel">Cancel</button></div></div>

	<?php $this->endWidget(); ?>

	</div>
</div>

<script type='text/javascript' src='<?php echo Yii::app()->request->baseUrl; ?>/../js/signature_pad.min.js'></script>
<script type="text/javascript">
	$(function(){
		var signaturePad = new SignaturePad(document.querySelector('canvas'), {
			backgroundColor: 'white',
			penColor: 'black'
		});
		$('input.clear').on('click', function (event) {
			signaturePad.clear();
		});

		$('#sign-form').on('submit', function (event) {
			$('#sig').val(signaturePad.toDataURL('image/jpeg'));
			return true;
		});

		$('#sign-form').on('success', function(e, r) {
			signaturePad.clear();
			$('a.camera_photo').data('task_id', r.id);
			$('a.camera_photo').trigger('touchend');
		});

		$('.toggle.damage').on('click', function() {
			var value = $('.toggle.damage').attr('class');
			if (value.indexOf('active') > -1) {
				$('#damage').val(1);
			} else {
				$('#damage').val(0);
			}
		});

		$('a.camera_photo').hide();
		$('a.camera_photo').on('touchend', function() {
			return;
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
		$('#cam button.cancel').on('touchend', function() {
			Webcam.reset();
			$('#cam').hide();
			return false;
		});
		$('#cam button.snap').on('touchend', function() {
			Webcam.snap(function(data_uri) {
				wmaApp.queuePhoto($('a.camera_photo').data('task_id'), data_uri, $('#cam #cam_note').val(), 'delivery_record');
			});
			$('#cam').hide().fadeIn();
			return false;
		});

	});


	/*<![CDATA[*/
	jQuery(function($) {
	jQuery("#DeliveryRecord_photos").MultiFile({'accept':'jpg\x7Cgif\x7Cpng','max':10,'STRING':{'denied':'File\x20is\x20not\x20allowed'}});
	});
	/*]]>*/

</script>