<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'cgoods-stock-in-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row rowcol">
		<?php echo CHtml::label('Task Ref', 'ref'); ?>
		<?php echo CHtml::textField('ref', '', array('size' => 30, 'maxLength' => 100)); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Task Type', 'type'); ?>
		<?php echo CHtml::dropDownList('type', '', array('3030' => 'Pick Unit', '2030' => 'Container Load'), array('prompt' => $this->t('Select One'), 'required' => 'required')); ?>
	</div>

	<?php foreach ($products as $product) { ?>

		<div class="row rowcol">
			<?php echo CHtml::label($product['name'], $product['name']); ?>
			<?php echo CHtml::textField('WmsTaskItem[' . implode(',', $product['ids']) . ']', '', array('size' => 30, 'maxlength' => 100)); ?>
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