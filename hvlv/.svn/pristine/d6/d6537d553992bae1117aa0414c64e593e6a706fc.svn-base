<h1><?=$this->t('Manifest Converter');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'manifest-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Input Type','type'); ?>
		<?php echo CHtml::dropDownList('type', '', [10 => "i-Express", 20 => "Essentra", 99 => 'Other'], array('empty' => 'Select One')); ?>
	</div>

	<div class="row">
		<label for="manifest">Manifest - <small>.csv/.xls/.xlsx File</small></label>
		<input type="file" name="manifest" id="manifest" />
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Convert')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<div id="result"></div>
<iframe name="ifrm" id="ifrm" src="" border="0" style="display:none;"></iframe>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('form#manifest-form', panel).data('custom_success', function(r){
		var rdiv = $('#result', panel);
		rdiv.empty();
		if(r.done == true){
			$('form#manifest-form', panel).resetForm();
			$('#ifrm', panel).attr('src', '<?=$this->createUrl("manifest/convert");?>?download='+r.convert_of);
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