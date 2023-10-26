<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'cgoods-stock-in-form',
		'enableAjaxValidation' => false,
	)); ?>

	<?php foreach ($prods as $prod) { ?>

		<div class="row rowcol">
			<?php echo $form->labelEx($prod, $prod->name); ?>
			<?php echo CHtml::textField('WmsProd[' . $prod->id . ']', '', array('size' => 30, 'maxlength' => 100)); ?>
		</div>

	<?php } ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div>

<script>
$(function() {
	var tab = $('#jqmw_<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	$('#cgoods-stock-in-form').on('success', function() {
		setTimeout(function() {
			$('.popCancel', panel).trigger('click');
			$('#cg-stock-grid').yiiGridView('update');
		}, 1000);
	});
});
</script>