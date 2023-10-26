<h3><?=$this->t('New UBI RTS');?></h3>
<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id' => 'invoice-reconciliation-form-ubi',
		'enableAjaxValidation' => false,
	)); ?>
	<div class="row">
		<?php echo CHtml::label('UBI Invoice no:', 'invoice_no'); ?>
		<?php echo CHtml::textField('invoice_no', ''); ?>
	</div>
	<div class="row">
		<?php echo CHtml::label('Invoice Issue date:', 'invoice_issue_date'); ?>
		<?php echo CHtml::textField('invoice_issue_date', '', array('class' => 'date_input', 'id' => 'invoice_issue_date' . $_GET['tabid'])); ?>
	</div>
	<div class="row">
		<?php echo CHtml::label('Export errs', 'Export errs'); ?>
		<?php echo CHtml::checkbox('export', 1); ?>
	</div>
	<div class="row" style="margin-top: 20px;">
		<label for="postw-batch">Load Source Invoice Data From Courier</label>
		<input type="file" name="inv_file" id="inv_file" />

	</div>
<br/>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit', ['id' => 'btn-save']); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = tab.data('panel');
		tab.off('reload_tab').on('reload_tab', function() {
			var t = $('.ui-tabs', panel);
			t.tabs('load', t.tabs('option','active'));
		});

		var win = $('#jqmw_<?=$_GET["tabid"];?>');

		$('form#invoice-reconciliation-form-ubi', win).on('success', function(e, r) {
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		});

	});
</script>
