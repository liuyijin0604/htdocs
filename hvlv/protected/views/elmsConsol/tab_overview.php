<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'elms_ov_consol-form',
	'enableAjaxValidation'=>false,
));
?>
	<?php echo $form->errorSummary($model); ?>

	<div class="row rowcol rowleft">
	<label class="required" for="Elms_Consol_owner_id" aria-required="true">Customer <span class="required" aria-required="true">*</span></label>
		<?php echo $form->hiddenField($model,'owner_id');
			$acname1 = empty($_GET["tabid"])? 'owner_ac' : $_GET["tabid"].'_owner_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/clientSuggest'),
				'value' => empty($model->owner_id)? '' : $model->owner->name,
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '30',
				),
		));
		?>
	</div>

	<div class="row rowcol">
		<label>MS#</label>
		<?=$model->awb;?>
	</div>


	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'pol'); ?>
		<?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pods')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'pod'); ?>
		<?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pols')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'etd'); ?>
		<?php echo $form->textField($model,'etd', array('size' => 12, 'id' => 'etd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'eta'); ?>
		<?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>


<?php $this->endWidget(); ?>
</div><!-- form -->


<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	
	$('form#elms_ov_consol-form', panel).on('success', function(e, r){
		tab.load();
	});
});
</script>