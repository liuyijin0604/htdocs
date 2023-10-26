<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'imparcel-rts-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));
?>

<div class="row">
	<h3>Consignee</h3>
	<div class="col">
	<div class="row">
		<?php echo $form->labelEx($model->cnee, 'name'); ?>
		<?php echo CHtml::textField('Cnee[name]', $model->cnee->name, array('size'=>60)); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnee, 'address'); ?>
		<?php echo CHtml::textField('Cnee[address]', $model->cnee->address, array('size'=>60)); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnee, 'suburb');
		$sacname = empty($_GET["tabid"])? 'cnee_sub_ac' : $_GET["tabid"].'_cnee_sub_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $sacname,
			'sourceUrl' => array('postcode/suggest'),
			'value' => ($model->cnee->suburb) ? $model->cnee->suburb : '',
			'options' => array(
				'showAnim' => 'fold',
				'minLength' => 2,
				'delay' => 200,
				'select' => 'js:function(event, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
			),
			'htmlOptions' => array(
				'size' => '20',
				'name' => 'Cnee[suburb]',
			),
		));
		?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($model->cnee, 'state'); ?>
		<?php echo CHtml::dropDownList('Cnee[state]', $model->cnee->state, array('ACT' => 'ACT - Australia Capital Territory', 'NSW' => 'NSW - New South Wales', 'NT' => 'NT - Northern Territory', 'QLD' => 'QLD - Queensland', 'SA' => 'SA - South Australia', 'TAS' => 'TAS - Tasmania', 'VIC' => 'VIC - Victoria', 'WA' => 'WA - Western Australia'), array('empty' => 'Select One')); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model->cnee, 'postcode'); ?>
		<?php echo CHtml::textField('Cnee[postcode]', $model->cnee->postcode); ?>
	</div>
		<div class="row rowcol">
		<?php echo $form->labelEx($model->cnee, 'country'); ?>
		<?php echo CHtml::textField('Cnee[country]', $model->cnee->country); ?>
		 </div>
		<div class="row rowcolr" >
		<?php echo $form->labelEx($model->cnee, 'tel'); ?>
		<?php echo CHtml::textField('Cnee[tel]', $model->cnee->tel); ?>
	   </div>
	  <div class="row" style="display:inline-block">
		<?php echo CHtml::label('Couriers', 'courier'); ?>
		<?php echo CHtml::dropdownlist('courier_rts', $defaultSelect, array('aus'=>'Australian Post','aus-mel'=>'Australian Post MEL','aus-per'=>'Australian Post PER','aus-bne'=>'Australian Post BNE','fw'=>'Fastway','st-syd'=>'Startrack Sydney', 'st-mel'=>'Startrack Melbourne', 'tnt-syd-top'=>'TNT Sydney Top', 'tnt-mel-top'=>'TNT Melbourne Top','tnt-bne-top'=>'TNT Brisbane Top','tnt-per-top'=>'TNT Perth Top', 'hunter' => 'Hunter Express', 'd2z-syd' => 'D2z Sydney', 'd2z-mel' => 'D2z Melbourne', 'd2z-bri' => 'D2z Brisbane', 'd2z-per' => 'D2z Perth', 'd2z-local' => 'D2z Local','ubi-toll-syd' => 'UBI Toll Sydney','ubi-toll-mel' => 'UBI Toll Melbourne','my-toll-syd' => 'My Toll Sydney','my-toll-mel' => 'My Toll Melbourne'),array('prompt'=>'Previous Courier')); //'eiz-toll-syd' => 'Eiz Toll Sydney','eiz-toll-mel' => 'Eiz Toll Melbourne','eiz-toll-bne' => 'Eiz Toll Brisbane'?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model->cnee, 'email'); ?>
		<?php echo CHtml::textField('Cnee[email]', $model->cnee->email, array('size'=>60)); ?>
	</div>
	</div>
</div>


<div class="row buttons">
	<?php echo CHtml::submitButton('Save'); ?>
</div>

<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
$(function(){
	var win = $("#jqmw_<?=$_GET['tabid'];?>");
	$('form#imparcel-rts-form', win).on('success', function(r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

	$('#<?=$_GET['tabid'];?>_cnee_sub_ac', win).off('ac_after_select').on('ac_after_select', function(evt, ui){
		$("#Cnee_postcode", win).val(ui.item.pc);
		$('#Cnee_state', win).val(ui.item.st);
	});
	
});
</script>
