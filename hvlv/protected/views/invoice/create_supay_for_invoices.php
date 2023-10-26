<h1>Create AliPay/WechatPay for Invoices</h1>

<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'supay-invoices-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row">
		<?php echo CHtml::label('Invoice No(s)', 'nos'); ?>
		<?php echo CHtml::textArea('nos', '', ['rows' => 10, 'cols' => 40]); ?>
		<?php echo CHtml::hiddenField('act_btn'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('AliPay'); ?>
		<?php echo CHtml::submitButton('WechatPay'); ?>
	</div>
	<br />
	<br />

	<div class="row">
		<?php echo CHtml::label('Payment Url', 'url'); ?>
		<?php echo CHtml::textArea('url', '', ['rows' => 10, 'cols' => 40]); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
$(function() {
	$('#supay-invoices-form input[type="submit"]').on('click', function() {
		if ($('#nos').val() == '') {
			alert('Invoice no(s) is empty');
			return false;
		} else if (confirm('Are sure to create ' + $(this).attr('value') + ' for invoices ' + $('#nos').val())) {
			$('#act_btn').val($(this).attr('value'));
			$('#supay-invoices-form').submit();
		}
	});

	$('#supay-invoices-form').on('success', function(e, r) {
		$('#url').val(r.url);
	});
});
</script>