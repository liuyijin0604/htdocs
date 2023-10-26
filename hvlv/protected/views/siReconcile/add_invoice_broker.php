<style>
.uploading {
	background: url("https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif") no-repeat 0 0 !important;
	background-size: 20px 20px !important;
	background-color: white !important;
}
</style>

<h1><?=$this->t('Adding Invoice');?></h1>


<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'invoice-reconciliation-form',
		'enableAjaxValidation'=>false,
	)); ?>

	<div class="row">
		<label for="invoice_amount" required="required">Import Invoice Amount</label>
		<?php echo CHtml::numberField('invoice_amount', '');?>
		<?php echo CHtml::hiddenField('type', '');?>
	</div>

	<div class="row">
		<label for="Currency" required="required">Currency</label>
		<?php echo CHtml::dropDownList('currency','1', Invoice::$currencies_s, array('empty' => 'Select One')); ?>
	</div>

	<div class="row" style="margin-top: 20px;">
		<label for="postw-batch">Load Source Invoice Data From Manual Excel</label>
		<input type="file" name="inv_file" id="inv_file" />
	</div>

	<div class="row">
		<label for="rate_option">Invoice Template Types</label>
		<?php echo CHtml::dropDownList('invoice_template', '', OrgRate::$BrokerTemplateTypes); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::button('Submit',['id' => 'btn-save']); ?>
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

		$(win).off('click', '#btn-save').on('click', '#btn-save', function() {
			uploading_on($('#btn-save'));

			var formData = new FormData();
			formData.append('invoice_no', "");
			formData.append('invoice_amount', $('#invoice_amount', win).val());
			formData.append('invoice_date', "");
			formData.append('inv_file', $('#inv_file', win)[0].files[0]);
			formData.append('rate_option', $('#rate_option', win).val());
			formData.append('invoice_template', $('#invoice_template', win).val());
			formData.append('currency',$('#currency',win).val());
			formData.append('type',<?=$type?>);
			$.ajax({
				url: '<?=$type == 'Old' ? Yii::app()->createUrl("siReconcile/addInvoice") : Yii::app()->createUrl("siReconcile/addInvoice")?>',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function(r) {
					uploading_off($('#btn-save'));
					r = JSON.parse(r);
					if (r.done) {
						myApp.notice(r.msg, 5000);
						win.data('opener').trigger('onOpen');
						win.jqmHide();
					} else {
						myApp.alert(r.msg, false);
					}
				},
				error: function(r) {
					uploading_off($('#btn-save'));
					myApp.alert('System error', false);
				}
			});
		});

	});
</script>
