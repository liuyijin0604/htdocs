<?php if (Yii::app()->user->grp<=40):?>
<div class="form" >
	<?php $form=$this->beginWidget('CActiveForm', [
		'id'=>'task-form',
		'enableAjaxValidation'=>false,
	]); ?>
	<!--<pre id='err_message' style="border: none;background-color: white; color:red;"></pre>-->
	<div class="row">
	<div class="col col-sm-6">
	<div class="form-group">
	<legend>From:</legend>
	<label>Company Name<span class="required">*</span></label>
	<input type="text" class="form-control required" name='from[company]' placeholder="Company Name" />
	</div>
	<div class="row">
		<div class="col col-sm-6">
	<div class="form-group">
	<label>Contact Name<span class="required">*</span></label>
	<input type="text" class="form-control required" name='from[contact]' placeholder="Contact Name">
	</div>
		</div>
		<div class="col col-sm-6">
		<div class="form-group">
		<label for="fcontact">Phone<span class="required">*</span></label>
		<input type="text" class="form-control required" name='from[phone]' placeholder="Phone">
		</div>
		</div>
	</div>
	<div class="form-group">
	<label for="fcontact">Email</label>
	<input type="text" class="form-control" name='from[email]' placeholder="Email">
	</div>
	<div class="form-group">
	<label>Address<span class="required">*</span></label>
	<input type="text" class="form-control required" name='from[address]' placeholder="Address">
	</div>
	<div class="row">
		<div class="col col-sm-6">
		<div class="form-group">
		<label>Suburb<span class="required">*</span></label>
		<input type="text" class="form-control required" name='from[subrb]' placeholder="Suburb">
		</div>
		</div>
		<div class="col col-sm-4">
		<div class="form-group">
		<label>State<span class="required">*</span></label>
		<select class="form-control required" name='from[state]'>
			<option value="">Select One</option>
			<option value="ACT">ACT</option>
			<option value="NSW">NSW</option>
			<option value="NT">NT</option>
			<option value="QLD">QLD</option>
			<option value="SA">SA</option>
			<option value="TAS">TAS</option>
			<option value="VIC">VIC</option>
			<option value="WA">WA</option>
		</select>
		</div>
		</div>
		<div class="col col-sm-2">
		<div class="form-group">
		<label>Postcode<span class="required">*</span></label>
		<input type="text" class="form-control required" name='from[postcode]' placeholder="Postcode">
		</div>
		</div>
	</div>
 
	<div class="row">
		<div class="col col-sm-6">
		<div class="form-group">
		<label><input type="checkbox" name="from[option][tailgate]" /> Tailgate lifter required</label>
		</div>
		</div>
		<div class="col col-sm-6">
		<div class="form-group">
		<label><input type="checkbox" name="from[option][handload]" /> Driver to help load</label>
		</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label for="reference">Pickup Reference</label>
				<input type="text"  class="form-control" name="from[reference]" placeholder="Reference">
			</div>
		</div>
		<div class="col-sm-6">
	<label>Pickup Date/Time</label><span class="required">*</span>
	 <div class="input-group">
	 <?php echo CHtml::textField('from[datetime]', '', ['size' => 25, 'class' => 'datetime_input form-control required']); ?>
	 <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
	</div>	
	</div>
	</div>
	</div>
	

	<div class="col col-sm-6">
	<div class="form-group">
	<legend>To:</legend>
	<label>Company Name<span class="required">*</span></label>
	<input type="text" class="form-control required" name='to[company]' placeholder="Company Name" />
	</div>
	<div class="row">
		<div class="col col-sm-6">
	<div class="form-group">
	<label>Contact Name<span class="required">*</span></label>
	<input type="text" class="form-control required" name='to[contact]' placeholder="Contact Name">
	</div>
		</div>
		<div class="col col-sm-6">
		<div class="form-group">
		<label for="fcontact">Phone<span class="required">*</span></label>
		<input type="text" class="form-control required" name='to[phone]' placeholder="Phone">
		</div>
		</div>
	</div>
	<div class="form-group">
	<label for="fcontact">Email</label>
	<input type="text" class="form-control" name='to[email]' placeholder="Email">
	</div>
	<div class="form-group">
	<label>Address<span class="required">*</span></label>
	<input type="text" class="form-control required" name='to[address]' placeholder="Address">
	</div>
	<div class="row">
		<div class="col col-sm-6">
		<div class="form-group">
		<label>Suburb<span class="required">*</span></label>
		<input type="text" class="form-control required" name='to[subrb]' placeholder="Suburb">
		</div>
		</div>
		<div class="col col-sm-4">
		<div class="form-group">
		<label>State<span class="required">*</span></label>
		<select class="form-control" name='to[state]'>
			<option value="">Select One</option>
			<option value="ACT">ACT</option>
			<option value="NSW">NSW</option>
			<option value="NT">NT</option>
			<option value="QLD">QLD</option>
			<option value="SA">SA</option>
			<option value="TAS">TAS</option>
			<option value="VIC">VIC</option>
			<option value="WA">WA</option>
		</select>
		</div>
		</div>
		<div class="col col-sm-2">
		<div class="form-group">
		<label>Postcode<span class="required">*</span></label>
		<input type="text" class="form-control required" name='to[postcode]' placeholder="Postcode">
		</div>
		</div>
 
	</div>

		<div class="row">
			<div class="col col-sm-6">
			<div class="form-group">
			<label><input type="checkbox" name="to[option][forklift]" /> Forklift available</label>
			</div>
			</div>
			<div class="col col-sm-6">
			<div class="form-group">
			<label><input type="checkbox" name="to[option][handload]" /> Driver to help unload</label>
			</div>
			</div>
		</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label for="reference">Delivery Reference</label>
				<input type="text"  class="form-control" name="to[reference]"placeholder="Reference" />
			</div>
		</div>
		<div class="col-sm-6">
	<label>Delivery Date/Time</label>
	 <div class="input-group">
	 <?php echo CHtml::textField('to[datetime]', '', ['size' => 25, 'class' => 'datetime_input form-control']); ?>
	 <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
	</div>	
	</div>
	</div>
	</div>
	</div>
	<div style="margin-top:20px;">
		<legend>Goods Information</legend>
<div class="goods">
	<div class="row goods-row">
		<div class="col col-sm-2">
		<div class="form-group ">
			<label for="cbm">Type<span class="required">*</span></label>
			<select name="goods[type][]" class="form-control required">
				<option value="PLT">Pallet</option>
				<option value="CTN">Carton</option>
			</select>
		</div>
		 </div>

	<div class="col col-sm-4">
		<label>Dimension (CM)<span class="required">*</span></label>
	 	<div class="input-group">
		<input type="text" class="form-control required" name="goods[dim_l][]" placeholder="L" style="width:33%" /><input type="text" class="form-control required" name="goods[dim_w][]" placeholder="W" style="width:34%" /><input type="text" class="form-control required" name="goods[dim_h][]" placeholder="H" style="width:33%" />
		</div>
	</div>
	<div class="col col-sm-2">
		<label>Weight (KG)<span class="required">*</span></label>
		<input type="text"  class="form-control required" name="goods[weight][]" placeholder="Weight" />
	</div>
	<div class="col col-sm-2">
		<label>Quantity<span class="required">*</span></label>
		<input type="text"  class="form-control required" name="goods[qty][]" placeholder="Quantity" />
	</div>
	<div class="col col-sm-2 col_act">
		<label>&nbsp;</label><br />
		<a class="add_row" href="#" style="color:#6c6"><span class="glyphicon glyphicon-plus-sign" style="font-size:2em;"></span></a>
	</div>
	</div>
</div>
	</div>
	</div>
	<legend>Instruction Notes</legend>
	 <div class="form-group">
	<textarea class="form-control" id="notes" name='notes' rows="3"></textarea>
	</div>

	<div class="form-group">
	<?php echo CHtml::submitButton('Submit', $htmlOptions=['class'=>'btn btn-primary']); ?>
	</div>
	<?php $this->endWidget(); ?>
</div>
<?php endif;?>

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(function(){
	$('a.add_row').on('click', function(){
		var r = $(this).parents('.goods-row');
		var n = r.clone();
		n.find('label').remove();
		n.find('.col_act').html('<a class="del_row" href="#"><span class="glyphicon glyphicon-minus-sign" style="font-size:2em;"></span></a>');
		r.parent().append(n);
		return false;
	});
	$('.goods').on('click', 'a.del_row', function(){
		$(this).parents('.goods-row').remove();
		return false;
	});
	$('form#task-form').on('success', function(e, r){
		if(r.done){
			$(this).resetForm();
		}
	}).validate({
		errorPlacement: function(err, el) { return; }
	});
	 
});
</script>
