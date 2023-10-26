
<h1>Update Billing AWB</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'afbilling-update-awb-form',
	'enableAjaxValidation'=>false,
)); ?>
<div class="form">
	<?php if (in_array($model->supplier_id, [954, 964])) { ?>
	<div class="row">
		<div class="rowcol">
			<?php echo CHtml::label('Update AWB / No', 'awb'); ?>
			<?php echo CHtml::textField('awb', $model->awb, ['size' => '20']); ?>
		</div>
	</div>
	<?php } else if ($model->supplier_id == 1133) {
		if (!empty($model->mdata['model'])) {
			foreach ($model->mdata['model'] as $k => $item) { ?>
		<div class="row">
			<div class="rowcol">
				<?php echo CHtml::label('Update AWB / No', 'awb'); ?>
				<?php echo CHtml::textField('update_awb[' . $k . ']', $item['awb'], ['size' => '20']); ?>
			</div>
		</div>
		<?php }} ?>
		<div class="row">
			<div class="rowcol">
				<?php echo CHtml::label('Add AWB / No', 'awb'); ?>
				<?php echo CHtml::textField('awb', '', ['size' => '20']); ?>
			</div>
		</div>
	<?php } else if (in_array($model->supplier_id, Org::$brokers)) { ?>
	<div class="row">
		<div class="rowcol">
			<?php echo CHtml::label('Update AWB / No', 'awb'); ?>
			<?php echo CHtml::textField('awb', $model->awb, ['size' => '20']); ?>
		</div>
	</div>
	<div class="row">
		<div class="rowcol">
			<?php echo CHtml::label('Update Shipment', 'shipment'); ?>
			<?php echo CHtml::textField('mdata[shipno]', $model->mdata['shipno'], ['size' => '20']); ?>
		</div>
	</div>
	<?php } ?>

	<?php if (!in_array($model->supplier_id, array_merge([1133], Org::$brokers))) { ?>
	<div class="row">
		<div class="rowcol">
			<?php echo CHtml::checkBox('togc',false) . ' Post by General Cost'; ?>
		</div>
	</div>
	<?php } ?>


<input type="hidden" name="afid" value="<?php echo $model->id; ?>">

<div class="row buttons" style="margin-bottom: 20px;">
	<?php echo CHtml::submitButton('Update');?>
</div>
	</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
	$(function(){

		var win = $("#jqmw_<?=$_GET['tabid'];?>");
		$('#afbilling-update-awb-form', win).on('success', function(){
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		});

	});
</script>