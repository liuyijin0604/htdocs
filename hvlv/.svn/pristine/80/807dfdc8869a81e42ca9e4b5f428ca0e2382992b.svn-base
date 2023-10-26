<h1>Confirm Import AWB</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'afbilling-update-awb-import-form',
	'enableAjaxValidation'=>false,
)); ?>
<div class="form">
	<?php if ($model->supplier_id == 1133) { ?>
		<div class="row">
			<div class="rowcol">
				<?php echo CHtml::label('Add AWB / No', 'awb'); ?>
				<?php echo CHtml::textArea('awb', '', ['rows' => 10, 'cols' => 50]); ?>
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
		$('#afbilling-update-awb-import-form', win).on('success', function(){
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		});

	});
</script>