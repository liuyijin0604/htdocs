<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'reprint-pltLabel-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => ['class' => 'ifrm-form', 'target' => '_blank'],
)); ?>
	<div class="row">
		<label>Pallet No.(s)</label>
		<textarea name="q" rows="20"></textarea>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Generate')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#pltLabel-form', win).on('submit', function(r){
		setTimeout(function(){
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		}, 1e3);
	});

});
</script>