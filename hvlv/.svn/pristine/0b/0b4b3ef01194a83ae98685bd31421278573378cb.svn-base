<h1><?=$this->t('Upload Manifest');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'manifest-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row">
		<?php echo $form->labelEx($model,'fwd_id'); ?>
		<?php echo $form->hiddenField($model,'fwd_id');
			$acname = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'class' => 'required',
					'size' => '30',
				),
		));
		?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model,'type', Manifest::$types, array('empty' => 'Select One')); ?>
	</div>

	<div class="row dpt_row">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id', Org::dptList(), array('empty' => 'Select One')); ?>
	</div>

	<div class="row">
		<label for="manifest">Manifest - <small>.csv/.xls/.xlsx File</small></label>
		<input type="file" name="manifest" id="manifest" />
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Upload')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<div id="result"></div>
<script type="text/javascript">
$(function(){
	/*var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('form#manifest-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});*/
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('form#manifest-form', panel).data('custom_success', function(r){
		var rdiv = $('#result', panel);
		rdiv.empty();
		if(r.done == true){
			$('form#manifest-form', panel).resetForm();
			myApp.notice(r.msg, 5000);
		}else{
			rdiv.append('<h3>Errors:</h3><p class="red" style="font-weight:bold;">'+r.msg+'</p>');
		}
		if(r.warns && r.warns.length > 0){
			rdiv.append('<h3>Warns:</h3><p class="warn">'+r.warns.join('<br />')+'</p>');
		}
		$('input[type=submit]', panel).attr('disabled', false);
	});
});
</script>