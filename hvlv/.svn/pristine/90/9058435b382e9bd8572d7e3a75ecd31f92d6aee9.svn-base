
<h1>Ready to Post to Xero</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'billing-bulkpost-xero-form',
	'enableAjaxValidation'=>false,
)); ?>
<div class="form">
<div class="row">
	<div class="rowcol">
		<?php echo CHtml::label('Transaction Date:','for_acounting'); ?>
		<?php echo CHtml::textField('tdate', $model->created, ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="rowcol">
		<?php echo CHtml::label('Invoice Date:','for_acounting'); ?>
		<?php echo CHtml::textField('date', $model->created, ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="rowcol">
		<?php echo CHtml::label('Invoice Due:','for_acounting'); ?>
		<?php echo CHtml::textField('due', $model->created, ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<?php if (in_array($model->af[0]->supplier_id, Org::$brokers)) { ?>
		<div class="rowcol">
			<?php echo CHtml::label('Add -1', 'add_1'); ?>
			<?php echo CHtml::checkbox('add_1', ''); ?>
		</div>
		<div class="rowcol">
			<?php echo CHtml::label('Add -2', 'add_2'); ?>
			<?php echo CHtml::checkbox('add_2', ''); ?>
		</div>
	<?php } ?>

</div>

<input type="hidden" name="fid" value="<?php echo $model->id; ?>">

<div class="row buttons" style="margin-bottom: 20px;">
	<?php echo CHtml::submitButton('Post');?>
</div>
	</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
	$(function(){

		var win = $("#jqmw_<?=$_GET['tabid'];?>");

		$('#billing-bulkpost-xero-form', win).on('success', function(){
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		});

	});
</script>