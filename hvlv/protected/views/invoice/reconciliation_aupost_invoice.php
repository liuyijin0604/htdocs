<h1><?=$this->t('New Aupost Invoice');?></h1>


<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'aupost-invoice-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row">
		<label for="invoice_no" required="required">Aupost Invoice No</label>
		<?php echo CHtml::textField('invoice_no', '', ['required' => 'required']); ?>
	</div>

	<div class="row">
		<label for="invoice_date" required="required">Aupost Invoice Date</label>
		<?php echo CHtml::textField('invoice_date', '', ['class' => 'date_input', 'required' => 'required']); ?>
	</div>

	<div class="row">
		<label for="invoice_amount" required="required">Aupost Invoice Amount</label>
		<?php echo CHtml::textField('invoice_amount', '');?>
	</div>

	<div class="row" style="margin-top: 20px;">
		<label for="postw-batch">Load Invoice File Aupost</label>
		<input type="file" name="inv_file" id="inv_file" />
	</div>
<br/>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit',['id' => 'btn-save']); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = tab.data('panel');
		tab.off('reload_tab').on('reload_tab', function(){
			var t = $('.ui-tabs', panel);
			t.tabs('load', t.tabs('option','active'));
		});

		var win = $('#jqmw_<?=$_GET["tabid"];?>');

		$('form#aupost-invoice-form', win).on('success', function(e, r){
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		});

	});
</script>
