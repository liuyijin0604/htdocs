
<h1>Confirm</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'billing-confirm-form',
	'enableAjaxValidation'=>false,
)); ?>
<div class="form">

	<input type="hidden" name="afid" value="<?php echo $model->id; ?>">

	<div class="row buttons">
		<?php echo CHtml::submitButton('Confirm');?>
	</div>
</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
	$(function(){

		var win = $("#jqmw_<?=$_GET['tabid'];?>");
		$('#billing-confirm-form', win).on('success', function() {
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		});

	});
</script>