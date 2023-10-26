
<h1>Update Billing AWB</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'afbilling-update-inv-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="form">
	<div class="row">
		<div class="rowcol">
			<?php echo CHtml::label('Update Inv', 'inv'); ?>
			<?php echo CHtml::textField('inv', $model->af[0]->invoice_no, ['size' => '20']); ?>
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
		$('#afbilling-update-inv-form', win).on('success', function(){
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		});

	});
</script>