<?php
/* @var $this RouteController */
/* @var $model Route */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'route-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'airline_id'); ?>
		<?php echo CHtml::dropDownList('airline_name', $model->airline_id, Quotes::airlineList()); ?>
		<?php echo $form->error($model,'airline_id'); ?>
	</div>

	<hr />

	<div class="row on_flight">
		<?php echo $form->labelEx($model,'flight_no'); ?>
		<div id="flight-inputs">
			<?php $flights = explode(' ', $model->flight_no);
			foreach ($flights as $k => $flight) {
				if (empty($flight)) {
					unset($flights[$k]);
				}
			}
			$flights = array_values($flights);
			foreach ($flights as $k => $flight) { ?>
				<tb id="flight<?=$k?>"><?php echo CHtml::textField('flight[' . $k . ']', $flight, array('size' => 8, 'maxlength' => 10, 'class' => 'inp')); ?></tb>
			<?php } ?>
		</div>
	</div>

	<div class="row on_flight">
		<?php echo $form->labelEx($model,'deptime'); ?>
		<div id="deptime-inputs">
			<?php $deptimes = explode(' ', $model->mdata['deptimes']);
			foreach ($flights as $k => $flight) { ?>
				<tb id="deptime<?=$k?>"><?php echo CHtml::textField('deptime[' . $k . ']', @$deptimes[$k], array('size' => 8, 'maxlength' => 10, 'class' => 'inp time_input')); ?></tb>
			<?php } ?>
		</div>
	</div>

	<div class="row on_flight">
		<label>&nbsp;</label>
		<div id="button-inputs">
			<?php foreach ($flights as $k => $flight) { ?>
				<tb style="padding-left: 18px; padding-right: 18px" id="button<?=$k?>"><?php echo '<a style="cursor: pointer" class="del_flight"><div style="background-position: -272px -128px" class="icon"></div></a>&nbsp;&nbsp;<a style="cursor: pointer" class="add_flight"><div style="background-position: -16px 0" class="icon"></div></a>'; ?></tb>
			<?php } ?>
		</div>
	</div>

	<hr />

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo CHtml::dropDownList('status', $model->status, Route::$states); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<span style="white-space:nowrap"><?php echo $form->labelEx($model, 'destination'); ?></span>
		<span> <?php
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'Route[destination]',
				'value' => $model->destination,
				'id' => 'Route_destination_' . $_GET['tabid'],
				'sourceUrl' => $this->createUrl('quotes/search'),
				'options' => array(
					'minLength' => '1',
				),
				'htmlOptions' => array(
					'style' => 'height:20px;',
				),
			));
			?>
		</span>

		<?php echo $form->error($model, 'destination'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'departure'); ?>
		<?php
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'Route[departure]',
				'value' => $model->departure,
				'id' => 'Route_departure_' . $_GET['tabid'],
				'sourceUrl' => $this->createUrl('quotes/search'),
				'options' => array(
					'minLength' => '1',
				),
				'htmlOptions' => array(
					'style' => 'height:20px;',
				),
			));
		?>
		<?php echo $form->error($model, 'departure'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'stops'); ?>
		<?php echo $form->textField($model,'stops'); ?>
		<?php echo $form->error($model,'stops'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'days'); ?>
		<?php echo $form->textField($model,'days'); ?>
		<?php echo $form->error($model,'days'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'info'); ?>
		<?php echo $form->textArea($model,'info',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'info'); ?>
	</div>

	<div class="row">
		<span style="white-space:nowrap;"><?php echo $form->labelEx($model, 'cca_charge', array('label' => 'CCA Charge')); ?></span>
		<?php echo $form->textField($model, 'cca_charge'); ?>
		<?php echo $form->error($model, 'cca_charge'); ?>
	</div>

	<div class="row">
		<span style="white-space:nowrap;"><?php echo $form->labelEx($model, 'awb_p', array('label' => 'AWB Fee / AWB')); ?></span>
		<?php echo $form->textField($model, 'awb_p'); ?>
		<?php echo $form->error($model, 'awb_p'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Uld Type <span class="required">*</span>', 'uld_type'); ?>
		<?php echo CHtml::dropDownList('uld_type', @$model->quotes[0]->uld_type, array('pallet' => 'Pallet', 'AKE' => 'AKE', 'PMC' => 'PMC'), array('id' => 'uld_list',)); ?>
	</div>

	<hr />

	<div class="row on_range">
		<label>Range<span class="required">*</span></label>
		<div id="additional-inputs">
			<?php if (!empty($model->quotes)) {
				foreach ($model->quotes as $k => $quote) { ?>
				<tb id="range<?=$k?>"><?php echo CHtml::textfield('range[' . $k . ']', $quote->wt_lo, array('size' => 8, 'maxlength' => 10, 'class' => 'inp')); ?> </tb>
				<?php }
			} else { ?>
				<tb id="range0"><?php echo CHtml::textfield('range[0]', '', array('size' => 8, 'maxlength' => 10, 'class' => 'inp')); ?> </tb>
			<?php } ?>
		</div>
	</div>

	<div class="row on_range">
		<label>Price<span class="required">*</span></label>
		<div id="range-inputs">
			<?php if (!empty($model->quotes)) {
				foreach ($model->quotes as $k => $quote) { ?>
				<tb id="price<?=$k?>"><?php echo CHtml::textfield('price[' . $k . ']', $quote->pkg, array('size' => 8, 'maxlength' => 10, 'class' => 'inp')); ?> </tb>
				<?php }
			} else { ?>
				<tb id="price0"><?php echo CHtml::textfield('price[0]', '', array('size' => 8, 'maxlength' => 10, 'class' => 'inp')); ?> </tb>
			<?php } ?>
		</div>
	</div>

	<div class="row on_range">
		<label>Security Fuel</label>
		<div id="sec-inputs">
			<?php if (!empty($model->quotes)) {
				foreach ($model->quotes as $k => $quote) { ?>
				<tb id="sec_fuel<?=$k?>"><?php echo CHtml::textfield('sec_fuel[' . $k . ']', $quote->sec_fuel, array('size' => 8, 'maxlength' => 10, 'class' => 'inp')); ?> </tb>
				<?php }
			} else { ?>
				<tb id="sec_fuel0"><?php echo CHtml::textfield('sec_fuel[0]', '', array('size' => 8, 'maxlength' => 10, 'class' => 'inp')); ?> </tb>
			<?php } ?>
		</div>
	</div>

	<div class="row on_range">
		<label>&nbsp;</label>
		<div id="button-inputs-price">
			<?php if (!empty($model->quotes)) {
				foreach ($model->quotes as $k => $quote) { ?>
				<tb style="padding-left: 18px; padding-right: 18px" id="button_price<?=$k?>"><?php echo '<a style="cursor: pointer" class="del_price"><div style="background-position: -272px -128px" class="icon"></div></a>&nbsp;&nbsp;<a style="cursor: pointer" class="add_price"><div style="background-position: -16px 0" class="icon"></div></a>'; ?></tb>
				<?php }
			} else { ?>
				<tb style="padding-left: 18px; padding-right: 18px" id="button_price0"><?php echo '<a style="cursor: pointer" class="del_price"><div style="background-position: -272px -128px" class="icon"></div></a>&nbsp;&nbsp;<a style="cursor: pointer" class="add_price"><div style="background-position: -16px 0" class="icon"></div></a>'; ?></tb>
			<?php } ?>
		</div>
	</div>

	<hr />

	<div  class="row" id="pkg">
		<?php echo CHtml::label('Pkg <span class="required">*</span>', 'pkg'); ?>
		<?php echo CHtml::textField('pkg', @$model->quotes[0]->pkg, array('size' => 10, 'maxlength' => 10, 'id' => 'pkgin')); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Min <span class="required">*</span>', 'min'); ?>
		<?php echo CHtml::textField('min', @$model->quotes[0]->min, array('size' => 10, 'maxlength' => 10)); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Date Eff <span class="required">*</span>', 'date_eff'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'name' => 'date_eff',
			'value' => @$model->quotes[0]->date_eff,
			'id' => 'date_eff_' . $_GET['tabid'],
			// additional javascript options for the date picker plugin
			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'yy-mm-dd',
			),
			'htmlOptions' => array(
				'style' => 'height:20px;',
				'required' => 'required',
			)));
		?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function() {
	var tab = $("#<?=$_GET["tabid"];?>");
	tab = tab.data('panel');

	$("#additional-link", tab).bind("click", function() {
		var id = "range";
		var vid = "price"
		var sid = "sec_fuel";
		var inp = "inp";
		var size = $("#additional-inputs > tb input", tab).size();
		// console.log(id + size);
		$("#additional-inputs", tab).append("<tb id="+id+size+"><input type=text class="+inp+" name="+id+"["+size+"] size=8 maxlength=10></tb> ");
		$("#range-inputs", tab).append("<tb id="+vid+size+"><input type=text class="+inp+" name="+vid+"["+size+"] size=8 maxlength=10></tb> ");
		$("#sec-inputs", tab).append("<tb id="+sid+size+"><input type=text class="+inp+" name="+sid+"["+size+"] size=8 maxlength=10></tb> ");
	});
	$("#pkg", tab).hide();

	$("#less-link", tab).bind("click", function() {
		var id = "range";
		var vid = "price";
		var sid = "sec_fuel";
		var inp = "inp";
		var size = $("#additional-inputs > tb input", tab).size() - 1;
		// console.log(id+size);
		$("#"+id+size, tab).remove();
		$("#"+vid+size, tab).remove();
		$("#"+sid+size, tab).remove();

		if (size == 0) {
			$("#additional-inputs", tab).append("<tb id="+id+size+"><input type=text class="+inp+" name="+id+"["+size+"] size=8 maxlength=10></tb> ");
			$("#range-inputs", tab).append("<tb id="+vid+size+"><input type=text class="+inp+" name="+vid+"["+size+"] size=8 maxlength=10></tb> ");
			$("#sec-inputs", tab).append("<tb id="+sid+size+"><input type=text class="+inp+" name="+sid+"["+size+"] size=8 maxlength=10></tb> ");
		}
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

		$('#flight-inputs', tab).append('<tb id="'+fid+size+'"><input type="text" class="inp" name="'+fid+'['+size+']" size="8" maxlength="10"></tb> ');
		$('#deptime-inputs', tab).append('<tb id="'+did+size+'"><input type="text" class="inp time_input" name="'+did+'['+size+']" size="8" maxlength="10"></tb> ');
	});

	$('#less-link-flight', tab).on('click', function() {
		var fid = 'flight';
		var did = 'deptime';
		var size = $('#flight-inputs > tb input', tab).size() - 1;

		$('#'+fid+size, tab).remove();
		$('#'+did+size, tab).remove();

		if (size == 0) {
			$('#flight-inputs', tab).append('<tb id="'+fid+size+'"><input type="text" class="inp" name="'+fid+'['+size+']" size="8" maxlength="10"></tb> ');
			$('#deptime-inputs', tab).append('<tb id="'+did+size+'"><input type="text" class="inp time_input" name="'+did+'['+size+']" size="8" maxlength="10"></tb> ');
		}
	});

	$(tab).on('click', '.add_flight', function() {
		var id = $(this).parent().attr('id').replace('button', '');

		var fid = 'flight';
		var did = 'deptime';
		var bid = 'button';
		var size = $('#flight-inputs > tb input', tab).size();

		$('tb[id*="flight' + id + '"]', tab).after(' <tb id="'+fid+size+'"><input type="text" class="inp" name="'+fid+'['+size+']" size="8" maxlength="10"></tb>');
		$('tb[id*="deptime' + id + '"]', tab).after(' <tb id="'+did+size+'"><input type="text" class="inp time_input" name="'+did+'['+size+']" size="8" maxlength="10"></tb>');
		$('tb[id*="button' + id + '"]', tab).after(' <tb style="padding-left: 18px; padding-right: 18px" id="'+bid+size+'"><a style="cursor: pointer" class="del_flight"><div style="background-position: -272px -128px" class="icon"></div></a>&nbsp;&nbsp;<a style="cursor: pointer" class="add_flight"><div style="background-position: -16px 0" class="icon"></div></a></tb>');
	});

	$(tab).on('click', '.del_flight', function() {
		var id = $(this).parent().attr('id').replace('button', '');

		var fid = 'flight';
		var did = 'deptime';
		var bid = 'button';
		var size = $('#flight-inputs > tb input', tab).size() - 1;

		$('#'+fid+id, tab).remove();
		$('#'+did+id, tab).remove();
		$('#'+bid+id, tab).remove();

		if (size == 0) {
			$('#flight-inputs', tab).append('<tb id="'+fid+size+'"><input type="text" class="inp" name="'+fid+'['+size+']" size="8" maxlength="10"></tb> ');
			$('#deptime-inputs', tab).append('<tb id="'+did+size+'"><input type="text" class="inp time_input" name="'+did+'['+size+']" size="8" maxlength="10"></tb> ');
			$('#button-inputs', tab).append('<tb style="padding-left: 18px; padding-right: 18px" id="'+bid+size+'"><a style="cursor: pointer" class="del_flight"><div style="background-position: -272px -128px" class="icon"></div></a>&nbsp;&nbsp;<a style="cursor: pointer" class="add_flight"><div style="background-position: -16px 0" class="icon"></div></a></tb>');
		}
	});

	$(tab).on('click', '.add_price', function() {
		var id = $(this).parent().attr('id').replace('button_price', '');

		var rid = 'range';
		var vid = 'price';
		var sid = 'sec_fuel';
		var bid = 'button_price';
		var inp = 'inp';
		var size = $('#additional-inputs > tb input', tab).size();

		$('#additional-inputs tb[id*="range' + id + '"]', tab).after(' <tb id='+rid+size+'><input type=text class='+inp+' name='+rid+'['+size+'] size=8 maxlength=10></tb>');
		$('#range-inputs tb[id*="price' + id + '"]', tab).after(' <tb id='+vid+size+'><input type=text class='+inp+' name='+vid+'['+size+'] size=8 maxlength=10></tb>');
		$('#sec-inputs tb[id*="sec_fuel' + id + '"]', tab).after(' <tb id='+sid+size+'><input type=text class='+inp+' name='+sid+'['+size+'] size=8 maxlength=10></tb>');
		$('#button-inputs-price tb[id*="button_price' + id + '"]', tab).after(' <tb style="padding-left: 18px; padding-right: 18px" id="'+bid+size+'"><a style="cursor: pointer" class="del_price"><div style="background-position: -272px -128px" class="icon"></div></a>&nbsp;&nbsp;<a style="cursor: pointer" class="add_price"><div style="background-position: -16px 0" class="icon"></div></a></tb>');
	});

	$(tab).on('click', '.del_price', function() {
		var id = $(this).parent().attr('id').replace('button_price', '');

		var rid = 'range';
		var vid = 'price';
		var sid = 'sec_fuel';
		var bid = 'button_price';
		var inp = 'inp';
		var size = $('#additional-inputs > tb input', tab).size() - 1;

		$('#'+rid+id, tab).remove();
		$('#'+vid+id, tab).remove();
		$('#'+sid+id, tab).remove();
		$('#'+bid+id, tab).remove();

		if (size == 0) {
			$("#additional-inputs", tab).append("<tb id="+id+size+"><input type=text class="+inp+" name="+id+"["+size+"] size=8 maxlength=10></tb> ");
			$("#range-inputs", tab).append("<tb id="+vid+size+"><input type=text class="+inp+" name="+vid+"["+size+"] size=8 maxlength=10></tb> ");
			$("#sec-inputs", tab).append("<tb id="+sid+size+"><input type=text class="+inp+" name="+sid+"["+size+"] size=8 maxlength=10></tb> ");
			$('#button-inputs-price', tab).append('<tb style="padding-left: 18px; padding-right: 18px" id="'+bid+size+'"><a style="cursor: pointer" class="del_price"><div style="background-position: -272px -128px" class="icon"></div></a>&nbsp;&nbsp;<a style="cursor: pointer" class="add_price"><div style="background-position: -16px 0" class="icon"></div></a></tb> ');
		}
	})
});
</script>