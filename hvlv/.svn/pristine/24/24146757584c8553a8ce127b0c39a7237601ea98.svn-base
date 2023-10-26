<div class="content-padded">
	<h3>Delivery Record</h3>
	<div class="form">
	<?php 
	$showTime = empty(@$model->getBookingTime())?"Select Booking Time":@$model->getBookingTime();
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'sign-form',
		'enableAjaxValidation' => false,
	)); ?>
		<div class="row">
			<?php echo CHtml::link('selectTime',$this->createUrl('booking/selectTime'), array('id'=>'selectTime','style'=>'display:none','data-transition'=>'slide-in')); ?>
			<?php echo CHtml::hiddenField('id', @$model->id, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'BookingTime')); ?>
			<?php echo CHtml::hiddenField('uuid', @$model->uuid, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Uuid')); ?>
			<div class="col-12" style="padding-right: 5px;">
				<input type="date" value="<?=@$model->getBookingDate()?>" min="2019-09-01" max="2060-09-26" placeholder = 'SelectBookingDateFirst' format='yyyy-mm-dd' name='bookingDate' id='bookingDate'/>
				<?php echo CHtml::textField('bookingTime', @$model->getBookingTime(), array('size' => 30, 'maxlength' => 50, 'placeholder' => $showTime)); ?>
			</div>
			<div class="col-6" style="padding-right: 5px;">
				<?php echo CHtml::textField('name', @$model->name, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Name')); ?>
			</div>
			<div class="col-6" style="padding-right: 5px;">
				<?php echo CHtml::textField('mobile', @$model->mobile, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Mobile')); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-6" style="padding-right: 5px;">
				<?php echo CHtml::textField('mdata[company_name]', @$model->mdata['company_name'], array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Company Name')); ?>
			</div>
			<div class="col-6" style="padding-right: 5px;">
				<?php echo CHtml::dropDownList('mdata[booking_type]',@$model->mdata['booking_type'], DeliveryRecord::$booking_type, array('prompt'=>'Select','class'=>'form-control')); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-6" style="padding-right: 5px;">
				<?php echo CHtml::textField('rego', @$model->rego, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Rego')); ?>
			</div>
			<div class="col-3" style="padding-left: 5px;">
				<?php echo CHtml::textField('plt', @$model->plt, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Pallet')); ?>
			</div>
			<div class="col-3" style="padding-left: 5px;">
				<?php echo CHtml::textField('pkg', @$model->pkg, array('size' => 30, 'maxlength' => 50, 'placeholder' => '/Packs')); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-12" style="padding-left: 0; padding-right: 0;">
				<?php echo CHtml::textarea('note', @$model->note, array('size' => 30, 'maxlength' => 150, 'placeholder' => 'Note')); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-6" style="padding-left: 5px;">
				<?php echo CHtml::submitButton($this->t('Submit'), array('class' => 'save_btn btn btn-primary btn-block')); ?>
			</div>
		</div>

	<?php $this->endWidget(); ?>

	</div>
</div>

<script type="text/javascript">

	$('#uuid').val(pcadbApp.uuid());
	
	$(window).on("load", function() {
		if (!window.localStorage) 
		{
		    alert('This browser does NOT support');
		}else
		{
			$('#uuid').val(pcadbApp.uuid());
		}
	});


	$('#bookingTime').on('touchend',function(){
		let dickedDate = $('#bookingDate').val();
		$('#selectTime').attr('href','<?=$this->createUrl('booking/selectTime')?>'+"?date="+dickedDate);
		var btn = document.getElementById('selectTime');  
		var event = document.createEvent('Events');
		event.initEvent('touchend', true, true); 
		btn.dispatchEvent(event); 

		return false;
	});
	$('#bookingTime').val(pcadbApp.selectTime());
	pcadbApp.selectTime('');


	$('#bookingTime').click(function(){
		var btn = this;  
		var event = document.createEvent('Events');
		event.initEvent('touchend', true, true); 
		btn.dispatchEvent(event); 
	});

	pcadbApp.initLink();


</script>