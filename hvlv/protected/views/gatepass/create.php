<h2>Gate Pass</h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'gate-pass-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id', Org::dptList(), array('empty' => 'Select One')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'ref'); ?>
		<?php echo $form->textField($model, 'ref', array('size'=>20,'maxlength'=>30)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'company'); ?>
		<?php echo $form->textField($model, 'company', array('size'=>30,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'driver'); ?>
		<?php echo $form->textField($model, 'driver', array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'rego'); ?>
		<?php echo $form->textField($model, 'rego', array('size'=>10,'maxlength'=>10)); ?>
	</div>
	
	
	<div class="row">
	  <canvas style="border: 1px solid #ddd;"></canvas>
	  <?php echo CHtml::hiddenField('GatePass[sig]'); ?>
	  <?php echo CHtml::hiddenField('sids'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::button($this->t('Clear Signature'), array('class' => 'clear_btn')); ?> &nbsp;
		<?php echo CHtml::submitButton($this->t('Finish'), array('class' => 'save_btn')); ?>
	</div>

<?php $this->endWidget(); ?>
</div>
<div style="clear:both; margin-bottom: 15px;"></div>
<div id="outscan-tabs">
  <ul>
 <?php
 $tabs = array(
	array('parcel', $this->t('Parcel')),
	array('inventory', $this->t('Inventory')),
 );
 foreach($tabs as $tab){
	if(true){
		$href = strpos($tab[0], '/') === false? $this->createUrl('gatepass/create',array('tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
		echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
	}
 }
 ?>
  </ul>
</div>

<script type='text/javascript' src='<?php echo Yii::app()->request->baseUrl; ?>/js/signature_pad.min.js'></script>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('#outscan-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET["actab"])? 0 : $_GET["actab"]; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});

	var signaturePad = new SignaturePad(document.querySelector("canvas"));

	$('input.clear_btn', panel).on("click", function (event) {
		signaturePad.clear();
	});

	$('#gate-pass-form', panel).on("submit", function (event) {
		var valid = false;
		if(signaturePad.isEmpty()){
			alert("Please provide signature first.");
		}else if($('#sids', panel).val() == ''){
			alert("Please scan barcodes.");
		}else{
			$('#GatePass_sig', panel).val(signaturePad.toDataURL());
			valid = true;
		}
		return valid;
	});

	$('#gate-pass-form', panel).data({
		"reset": true,
	}).on('success', function(r){
		window.open('gatepass/print/'+r.id);
		signaturePad.clear();
		var t = $('#outscan-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	});
});
</script>