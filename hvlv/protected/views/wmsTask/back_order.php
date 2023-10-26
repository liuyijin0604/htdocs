<style>
.uploading {
	background: url("https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif") no-repeat 0 0 !important;
	background-size: 20px 20px !important;
	background-color: white !important;
}
</style>

<h1>Back Order</h1>

<div class="form">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'wmstask-back-order-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('wmsTask/backOrder', array('confirm' => true)),
	));
	?>

	<h3>Check Alsis & Biophysics</h3>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit', ['id' => 'btn-save']); ?>
	</div>

</div>
<?php $this->endWidget(); ?>

<script type="text/javascript">
$(function() {
	var win = $("#jqmw_<?=$_GET['tabid'];?>");

	function uploading_on(obj) {
		obj.addClass('uploading');
		obj.val('    Uploading');
		obj.prop('disabled', 'disabled');
	}

	function uploading_off(obj) {
		obj.removeClass('uploading');
		obj.val('Submit');
		obj.removeProp('disabled');
	}

	$('form#wmstask-back-order-form', win).on('submit', function() {
		uploading_on($('#btn-save'));
	});

	$('form#wmstask-back-order-form', win).on('success', function() {
		uploading_off($('#btn-save'));
		$('.popCancel', win).trigger('click');
	});
});
</script>