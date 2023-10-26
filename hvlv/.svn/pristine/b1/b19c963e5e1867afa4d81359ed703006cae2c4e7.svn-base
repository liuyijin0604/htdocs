<?php
if ($type == 'priority') {
	echo '<h1> Air Freight Billing Import - Priority </h1>';
	$action = $this->createUrl('billing/AjaxAfInvoiceImport');
} else if ($type == 'tne') {
	echo '<h1> Air Freight Billing Import - T&E </h1>';
	$action = $this->createUrl('billing/AjaxTneInvoiceImport');
} else if ($type == 'yuyang') {
	echo '<h1> Air Freight Billing Import - YuYang </h1>';
	$action = $this->createUrl('billing/AjaxYYInvoiceImport');
} else if ($type == 'fyn') {
	echo '<h1> Custom Broker Billing Import - FYN </h1>';
	$action = $this->createUrl('billing/AjaxBrokerInvoiceImport', array('type' => $type));
} else if ($type == 'master') {
	echo '<h1> Custom Broker Billing Import - Master </h1>';
	$action = $this->createUrl('billing/AjaxBrokerInvoiceImport', array('type' => $type));
} else if ($type == 'skyjet') {
	echo '<h1> Billing Import - SkyJet </h1>';
	$action = $this->createUrl('billing/AjaxSJInvoiceCheck');
} else if ($type == 'qantas') {
	echo '<h1> Billing Import - Qantas </h1>';
	$action = $this->createUrl('billing/AjaxQantasInvoiceImport');
} else if ($type == 'menzies') {
	echo '<h1> Billing Import - Menzies </h1>';
	$action = $this->createUrl('billing/AjaxMenziesInvoiceImport');
} else if ($type == 'insurance') {
	echo '<h1> Billing Import - Air Insurance </h1>';
	echo '<label for="air_insurance_excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="' . $this->createUrl('billing/IFTemplate') . '" target="_blank">Template file</a>)</label>';
	$action = $this->createUrl('billing/AjaxIFInvoiceImport');
}
?>

<div class="form">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'af-billing-import-form',
		'enableAjaxValidation' => false,
		'action' => $action,
	));
	?>

	<div class="row" style="margin-top: 20px;">
		<div class="rowcol">
		<label for="postw-batch">Only For EDI invoice currently <small>.xlsx File</small></label> <br>
		<input type="file" name="af_file" id="af_file" />
		</div>
		<div class="rowcol">
			<?php echo CHtml::label('In case overwrite old one , please tick it', 'ref'); ?>
			<?php echo CHtml::checkBox('overwrite') . ' Overwrite'; ?>
		</div>
		<div class="rowcol">
			<?php echo CHtml::label('In case need to match each line, please tick it', 'ref'); ?>
			<?php echo CHtml::checkBox('linematch') . ' Line Match Mode'; ?>
		</div>
	</div>
	<p style="margin-top:20px;"><input id="af_billing_import_btn" type="submit" value="Submit" /></p>

	<?php $this->endWidget();?>
</div>

<br/>
<div id="progress_import" style="display: none;"></div>
<div id="message_import" style="display: none;"></div>
<div id="af_billing_import_result" style="margin: 10px 0; border: 1px solid;padding:20px;display: none;">
</div>

<script type="text/javascript">
	$(function() {
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = tab.data('panel');
		var timer;

		// The function to refresh the progress bar.
		function refreshProgress() {
			// We use Ajax again to check the progress by calling the checker script.
			// Also pass the session id to read the file because the file which storing the progress is placed in a file per session.
			// If the call was success, display the progress bar.
			$.ajax({
				url: '<?php echo Yii::app()->createAbsoluteUrl("billing/AjaxCheckEdiInvoiceProgress") ;?>',
				dataType: 'json',
				success: function(data) {
					$('#progress_import', panel).html('<div class="bar" style="width:' + data.percent + '%"></div>');
					$('#message_import', panel).html(data.message);
					// If the process is completed, we should stop the checking process.
					if ( data.percent == 100 ) {
						window.clearInterval(timer);
						timer = window.setInterval(function(){
							window.clearInterval(timer);
							$('#message_import', panel).html('Completed');
							$('#progress_import', panel).hide();
							$('#message_import', panel).hide();
						}, 5e3);
					}
				}
			});
		}

		// Trigger the process in web server.
		// Refresh the progress bar every 1 second.
		$('form#af-billing-import-form input[type="submit"]', panel).click(function(e) {
			$('#progress_import', panel).show();
			$('#message_import', panel).hide();
			$('#af_billing_import_result', panel).hide();
			timer = window.setInterval(refreshProgress, 1000);
		});

		$('form#af-billing-import-form', panel).data('custom_success', function(r) {
			$('#af_billing_import_result', panel).show();
			$('#af_billing_import_result', panel).empty().prepend($('<p>' + r.msg + '</p>').fadeIn());
			$('form#af-billing-import-form #af_billing_import_btn', panel).attr('disabled', false);
			return true;
		});

	});
</script>