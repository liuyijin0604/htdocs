<?php
/* @var $this QuotesController */
/* @var $model Quotes */
/* @var $form CActiveForm */
?>

<!--<div id="additional-inputs">
	<?php echo CHtml::textfield('optional_text[0]', ''); ?>
</div>-->

<div class="wide form" id="createform" style="margin-left:40px">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'quotes-form',
	// 'htmlOptions'=>['ajax'=>array('success' =>"form.reset()")],
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation' => false,

));?>

	<p class="note pane">Fields with <span class="required ">*</span>are required.</p>

	<?php echo $form->errorSummary($model); ?>
		<div class="row">
		<?php echo $form->labelEx($modelR['route'], 'airline_id'); ?>
			 <?php echo CHtml::dropDownList('airline_name', $modelR['alist'], Quotes::airlineList()); ?>
		<?php echo $form->error($modelR['route'], 'airline_id'); ?>
	   </div>
<!--	    <div class="row">
		<?php echo $form->labelEx($modelR['route'], 'status'); ?>
		<?php echo CHtml::dropDownList('status', 1, array('1' => 'online', '0' => 'offline')); ?>
		<?php echo $form->textField($modelR['route'], 'status'); ?>
		<?php echo $form->error($modelR['route'], 'status'); ?>
	   </div>-->

		<div class="row on_flight">
			<div class="row buttons">
				<?php echo CHtml::Button('Add flight', array("id" => 'additional-link-flight')); echo '&nbsp;&nbsp;'; ?>
				<?php echo CHtml::Button('Remove flight', array("id" => 'less-link-flight')); echo '<br>'; ?>
			</div>
		</div>

		<div class="row on_flight">
			<?php echo $form->labelEx($modelR['route'], 'flight_no'); ?>
			<div id="flight-inputs">
				<tb><?php echo CHtml::textField('flight[0]', '', array('size' => 8, 'maxlength' => 10, 'class' => 'inp', 'required' => 'required')); ?></tb>
			</div>
		</div>

		<div class="row on_flight">
			<?php echo $form->labelEx($modelR['route'], 'deptime'); ?>
			<div id="deptime-inputs">
				<tb><?php echo CHtml::textField('deptime[0]', '', array('size' => 8, 'maxlength' => 10, 'class' => 'inp time_input')); ?></tb>
			</div>
		</div>

		<div class="row">
					<span style="white-space:nowrap;float:left;position: relative; left:-15px"><?php echo $form->labelEx($modelR['route'], 'destination', array('label' => 'Code-Destination')); ?></span>
		  <span> <?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
	'model' => $modelR['route'],
	'attribute' => 'destination',
	'source' => $this->createUrl('quotes/search'),
	'options' => array(
		'minLength' => '1',
	),
	'htmlOptions' => array(
		'style' => 'height:20px;',
	),
));
?>
				  </span>

		<?php echo $form->error($modelR['route'], 'destination'); ?>
	   </div>
		   <div class="row">
		<?php echo $form->labelEx($modelR['route'], 'departure'); ?>
			   <?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
	'model' => $modelR['route'],
	'attribute' => 'departure',
	'source' => $this->createUrl('quotes/search'),
	'options' => array(
		'minLength' => '1',
	),
	'htmlOptions' => array(
		'style' => 'height:20px;',
	),
));
?>
		<?php echo $form->error($modelR['route'], 'departure'); ?>
	   </div>

		<div class="row">
		<?php echo $form->labelEx($modelR['route'], 'stops'); ?>
		<?php echo $form->textField($modelR['route'], 'stops'); ?>
		<?php echo $form->error($modelR['route'], 'stops'); ?>
	   </div>
	   <div class="row">
		<?php echo $form->labelEx($modelR['route'], 'days'); ?>
		<?php echo $form->textField($modelR['route'], 'days'); ?>
		<?php echo $form->error($modelR['route'], 'days'); ?>
	   </div>

			<div class="row">
		<?php echo $form->labelEx($modelR['route'], 'info'); ?>
		<?php echo $form->textArea($modelR['route'], 'info'); ?>
		<?php echo $form->error($modelR['route'], 'info'); ?>
		</div>
		<!-- <div class="row">
		<span style="white-space:nowrap;"><?php echo $form->labelEx($modelR['route'], 'security_fuel', array('label' => 'Security Fuel/kg')); ?></span>
		<?php echo $form->textField($modelR['route'], 'security_fuel'); ?>
		<?php echo $form->error($modelR['route'], 'security_fuel'); ?>
		</div> -->

		<div class="row">
		<span style="white-space:nowrap;"><?php echo $form->labelEx($modelR['route'], 'cca_charge', array('label' => 'CCA Charge')); ?></span>
		<?php echo $form->textField($modelR['route'], 'cca_charge'); ?>
		<?php echo $form->error($modelR['route'], 'cca_charge'); ?>
		</div>
		<div class="row">
			<span style="white-space:nowrap;"><?php echo $form->labelEx($modelR['route'], 'awb_p', array('label' => 'AWB Fee / AWB')); ?></span>
		<?php echo $form->textField($modelR['route'], 'awb_p'); ?>
		<?php echo $form->error($modelR['route'], 'awb_p'); ?>
		</div>

		<div class="row">
		<?php echo $form->labelEx($model, 'uld_type'); ?>
		<?php echo CHtml::dropDownList('uld_type', 'pallet', array('pallet' => 'Pallet', 'AKE' => 'AKE', 'PMC' => 'PMC'), array('id' => 'uld_list',)); ?>
		<?php echo $form->error($model, 'uld_type'); ?>
		</div>
		<div class="row on_range">
			<div class="row buttons">
				<?php echo CHtml::Button('Add range', array("id" => 'additional-link')); echo '&nbsp;&nbsp;'; ?>
				<?php echo CHtml::Button('Remove range', array("id" => 'less-link')); echo '<br>'; ?>
			</div>

			<label>Range<span class="required">*</span></label>
			<div id="additional-inputs"><?php echo CHtml::textfield('range[0]', '', array('size' => 8, 'maxlength' => 10, 'class' => 'inp')); ?> </div>

			<?php echo $form->error($model, 'range'); ?>
		</div>

	<div class="row  on_range">
		<label>Price<span class="required">*</span></label>
		<div id="range-inputs"><?php echo CHtml::textfield('price[0]', '', array('size' => 8, 'maxlength' => 10, 'class' => 'inp')); ?> </div>
		<?php echo $form->error($model, 'price'); ?>
	</div>

	<div class="row  on_range">
		<label>Security Fuel</label>
		<div id="sec-inputs"><?php echo CHtml::textfield('sec_fuel[0]', '', array('size' => 8, 'maxlength' => 10, 'class' => 'inp')); ?> </div>
		<?php echo $form->error($model, 'sec_fuel'); ?>
	</div>

	<div  class="row" id="pkg">
		<?php echo $form->labelEx($model, 'pkg'); ?>
		<?php echo $form->textField($model, 'pkg', array('size' => 10, 'maxlength' => 10, 'id' => 'pkgin')); ?>
		<?php echo $form->error($model, 'pkg'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'min'); ?>
		<?php echo $form->textField($model, 'min', array('size' => 10, 'maxlength' => 10)); ?>
		<?php echo $form->error($model, 'min'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'date_eff'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'date_eff',
			// additional javascript options for the date picker plugin
			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'yy-mm-dd',
			),
			'htmlOptions' => array(
				'style' => 'height:20px;',
			)));
		?>
		<?php echo $form->error($model, 'date_eff'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'date_exp'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'date_exp',
			// additional javascript options for the date picker plugin
			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'yy-mm-dd',
			),
			'htmlOptions' => array(
				'style' => 'height:20px;',
			)));
		?>
		<?php echo $form->error($model, 'date_exp'); ?>
	</>

	<div class="row buttons">
	  <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('id' => 'submit')); ?>
	</div>

<?php $this->endWidget();?>

</div><!-- form -->
<script type="text/javascript">
$(function() {
	var tab = $("#<?=$_GET["tabid"];?>");
	tab = tab.data('panel');

	$("#datepicker", tab).datepicker();
	$("#additional-link", tab).bind("click", function() {
		var id = "range";
		var vid = "price"
		var sid = "sec_fuel";
		var inp = "inp";
		var size = $("#additional-inputs > tb input", tab).size() + 1;
		// console.log(id + size);
		$("#additional-inputs", tab).append("<tb id="+id+size+"><input type=text class="+inp+" name="+id+"["+size+"] size=8 maxlength=10></tb> ");
		$("#range-inputs", tab).append("<tb id="+vid+size+"><input type=text class="+inp+" name="+vid+"["+size+"] size=8 maxlength=10></tb> ");
		$("#sec-inputs", tab).append("<tb id="+sid+size+"><input type=text class="+inp+" name="+sid+"["+size+"] size=8 maxlength=10></tb> ");
	});

	$("#createform", tab).submit(function(e) {
		$("#pkgin", tab).val("");
		$(".inp", tab).val("");
	});

	$("#pkg", tab).hide();

	$("#less-link", tab).bind("click", function() {
		var id = "range";
		var vid = "price";
		var sid = "sec_fuel";
		var size = $("#additional-inputs > tb input", tab).size();
		// console.log(id+size);
		$("#"+id+size, tab).remove();
		$("#"+vid+size, tab).remove();
		$("#"+sid+size, tab).remove();
	});

	$('#uld_list', tab).on('change', function() {
		var seleted = $(this).val();
		if (seleted == "pallet") {
			$(".on_range", tab).show();
			$("#pkg", tab).hide();
		} else {
			$(".on_range", tab).hide();
			$("#pkg", tab).show();
		}
	});

	$('#additional-link-flight', tab).on('click', function() {
		var fid = 'flight';
		var did = 'deptime';
		var size = $('#flight-inputs > tb input', tab).size();

		$('#flight-inputs', tab).append('<tb id="'+fid+size+'"><input type="text" class="inp" name="'+fid+'['+size+']" size="8" maxlength="10" required=required /></tb> ');
		$('#deptime-inputs', tab).append('<tb id="'+did+size+'"><input type="text" class="inp time_input" name="'+did+'['+size+']" size="8" maxlength="10"  required=required /></tb> ');
	});

	$('#less-link-flight', tab).on('click', function() {
		var fid = 'flight';
		var did = 'deptime';
		var size = $('#deptime-inputs > tb input', tab).size() - 1;

		$('#'+fid+size, tab).remove();
		$('#'+did+size, tab).remove();

		if (size == 0) {
			$('#flight-inputs', tab).append('<tb id="'+fid+size+'"><input type="text" class="inp" name="'+fid+'['+size+']" size="8" maxlength="10"></tb> ');
			$('#deptime-inputs', tab).append('<tb id="'+did+size+'"><input type="text" class="inp time_input" name="'+did+'['+size+']" size="8" maxlength="10"></tb> ');
		}
	});
});
</script>
