<style>
.uploading {
	background: url("https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif") no-repeat 0 0 !important;
	background-size: 20px 20px !important;
	background-color: white !important;
}
</style>

<h1><?=$this->t('Fastway ' . @$type . ' File');?></h1>


<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'invoice-reconciliation-form',
		'enableAjaxValidation'=>false,
	)); ?>

	<div class="row">
		<label for="invoice_amount" required="required">Fastway Invoice Amount</label>
		<?php echo CHtml::textField('invoice_amount', '');?>
	</div>

	<div class="row">
		<label for="invoice_date" required="required">Fastway Invoice Date (<a href="<?php echo $type == 'Old' ? 'https://os.toplogistics.com.au/filerepo/19f4a9a0a87745a1/1674879(3).csv' : 'https://os.toplogistics.com.au/filerepo/43c527ff7bd480c6/20080589.csv';?>" target="_blank">Example file</a>)</label>
		<?php echo CHtml::textField('invoice_date', '', array('class' => 'date_input')); ?>
	</div>

	<div class="row" style="margin-top: 20px;">
		<label for="postw-batch">Load Source Invoice Data From Courier</label>
		<input type="file" name="inv_file" id="inv_file" />
	</div>

	<div class="row">
		<?php echo CHtml::label('Export errs', 'Export errs');?>
		<?php echo CHtml::checkbox('export', 1);?>
	</div>

	<div class="row">
		<label for="rate_option">Cost Rate</label>
		<?php echo CHtml::dropDownList('rate_option', '', [ImportChargeCode::FASTWAY_ID_OLD => 'Fastway Syd 2017', ImportChargeCode::FASTWAY_ID => 'Fastway Syd 2020'], ['empty' => 'Select One', 'required' => 'required']); ?>
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
			formData.append('rate_option', $('#rate_option', win).val());

			$.ajax({
				url: '<?=$type == 'Old' ? Yii::app()->createUrl("invoice/reconciliationNew") : Yii::app()->createUrl("invoice/fastwayReconciliationNew")?>',
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
