<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'confirm-form',
	'enableAjaxValidation'=>false,
	'action' => $this->createUrl('exdirConsol/confirm', array('id' => $model->id)),
));
?>
	<div class="row">
		<label for="manifest">Upload Confirmation Manifest - <small>.csv/.xls/.xlsx File</small></label>
		<input type="file" name="manifest" id="manifest" />
	</div>

	<div id="result"></div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Submit')); ?> 
		<?php echo CHtml::resetButton($this->t('Reset'), array('id' => 'reset_btn')); ?>
	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('form#confirm-form', panel).data({dataType: 'html', custom_success: function(r){
		var rdiv = $('#result', panel);
		if(r.err){
			rdiv.html('<h3>Errors:</h3><p class="red" style="font-weight:bold;">'+r.err+'</p>');
		}else{
			if(r.comp){
				myApp.notice(r.html, 5000);
				tab.load();
			}else{
				$('form#confirm-form', panel).resetForm();
				rdiv.html(r.html);
			}
		}
		$('input[type=submit]', panel).attr('disabled', false);
		return true;
	}});
	$('#reset_btn', panel).click(function(){
		var t = $('.ui-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	});
});
</script>