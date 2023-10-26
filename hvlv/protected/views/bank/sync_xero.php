<h1><?=$this->t('Confirm Sync Charge Code with Xero');?></h1>

<br>
<div class="form">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id'=>'chargecode-sync-xero',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('bank/syncXero', array('confirm' => true)),
	));
	?>
	<p><input id="sync_xero_btn" type="submit" value="Confirm" /></p>
</div>
<?php $this->endWidget(); ?>

<script type="text/javascript">
$(function() {
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	var win = $("#jqmw_<?=$_GET['tabid'];?>");

	$('form#chargecode-sync-xero', win).on('success', function(e, r) {
		$('.popCancel').trigger('click');
		$('#<?=$_GET['tabid'];?>' + '_ledger-grid', panel).yiiGridView('update');
	});
});
</script>
