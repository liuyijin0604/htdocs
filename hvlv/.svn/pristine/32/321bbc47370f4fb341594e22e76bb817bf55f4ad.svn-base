<h1><?=$this->t('change label');?></h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'change-label-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<label for="change_label">change_label - <small>.csv/.xls/.xlsx File</small></label>
		<input type="file" name="change_label" id="change_label" />
	</div>
	<div class="row">
		<a href="ims/change_label.xlsx" target="_blank">get Template</a>
	</div>
	<div class="row">
		<div class="rowcol">
			<?php echo CHtml::label('Create','Create'); ?>
				<?php echo CHtml::dropDownList('courier_id', '', array(
		ImportChargeCode::DFE_ID_TOP => 'DFE Sydney TOP',
		ImportChargeCode::DFE_MEL_ID_TOP => 'DFE Melbourne TOP',
		ImportChargeCode::DFE_BNE_ID_TOP => 'DFE Brisbane TOP'
	), array(ImportChargeCode::DFE_ID_TOP => 'DFE Sydney TOP')); ?>
		</div>
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
	$('form#change-label-form', panel).data('custom_success', function(r){
		var rdiv = $('#result', panel);
		rdiv.empty();
		if(r.done == true){
			$('form#change-label-form', panel).resetForm();
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