
<h1>Update TNE Amount</h1>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'afbilling-update-tne-amount-form',
	'enableAjaxValidation' => false,
)); ?>
<div class="form">
	<div class="row">
		<div class="rowcol">
			<?php echo CHtml::label('Update Amount', 'amount'); ?>
			<?php echo CHtml::textField('amount', $model->subtotal, ['size' => '20']); ?>
		</div>
	</div>

	<input type="hidden" name="afid" value="<?php echo $model->id; ?>">

	<div class="row buttons" style="margin-bottom: 20px;">
		<?php echo CHtml::submitButton('Update');?>
	</div>
</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
	$(function(){

		var win = $("#jqmw_<?=$_GET['tabid'];?>");
		$('#afbilling-update-tne-amount-form', win).on('success', function(){
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		});

	});
</script>