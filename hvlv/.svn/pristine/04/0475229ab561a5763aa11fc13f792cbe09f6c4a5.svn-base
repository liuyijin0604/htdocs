<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'outturn-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<label for="manifest">Scanner File - <small>.csv/.xls/.xlsx File</small></label>
		<input type="file" name="manifest" id="manifest" />
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#cn-id-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

});
</script>