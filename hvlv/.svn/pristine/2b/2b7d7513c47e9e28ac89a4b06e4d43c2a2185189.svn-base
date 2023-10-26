<style>
.uploading {
	background: url("https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif") no-repeat 0 0 !important;
	background-size: 20px 20px !important;
	background-color: white !important;
}
</style>

<h1><?=$this->t('Import Excel Invoice');?></h1>


<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'invoice-reconciliation-form',
		'enableAjaxValidation'=>false,
		 'htmlOptions'=>array('enctype'=>'multipart/form-data')
	)); ?>

	<div class="row">
		<label for="invoice_date" required="required">Import Invoice Date </label>
		<?php echo CHtml::textField('invoice_date', '', array('class' => 'date_input')); ?>
	</div>

	<div class="row">
		<label for="supplier_id" required="required">Client Id</label>
		<?php echo CHtml::numberField('supplier_id', ''); ?>
	</div>

	<div class="row" style="margin-top: 20px;">
		<label for="postw-batch">Load Source Invoice Data From Courier</label>
		<input type="file" name="inv_file" id="inv_file" />
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
			formData.append('invoice_amount', $('#invoice_amount', win).val());
			formData.append('invoice_date', $('#invoice_date', win).val());
			formData.append('inv_file', $('#inv_file', win)[0].files[0]);
			formData.append('org_id',$('#supplier_id',win).val());
			$.ajax({
				url: '<?=Yii::app()->createUrl("invoice/importExcelInv")?>',
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
