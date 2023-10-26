
<h1> Air Freight Billing Import - Air Insurance </h1>
<label for="air_insurance_excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="<?=$this->createUrl('billing/IFTemplate')?>" target="_blank">Template file</a>)</label>
<div class="form">
	<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'export-af-billing-import-form-airinsurance',
		'enableAjaxValidation'=>false,
		'action' => $this->createUrl('billing/AjaxIFInvoiceImport'),
	));
	?>


	<div class="row" style="margin-top: 20px;">
		<div class="rowcol">
		<label for="postw-batch">Only For EDI invoice currently <small>.xlsx File</small></label> <br>
		<input type="file" name="af_file" id="af_file" />
		</div>
		<div class="rowcol">
			<?php echo CHtml::label('In case overwrite old one , please tick it','ref'); ?>
			<?php echo CHtml::checkBox('overwrite') . ' Overwrite'; ?>
		</div>
		<div class="rowcol">
			<?php echo CHtml::label('In case need to match each line, please tick it','ref'); ?>
			<?php echo CHtml::checkBox('linematch') . ' Line Match Mode'; ?>
		</div>
	</div>
	<p style="margin-top:20px;"><input id="af_billing_import_btn" type="submit" value="Submit" /></p>
	<?php $this->endWidget(); ?>
</div>

<br/>
<div id="progress_ifimport" style="display: none;"></div>
<div id="message_ifimport" style="display: none;"></div>
<div id="af_billing_import_result_ifimport" style="margin: 10px 0; border: 1px solid;padding:20px;display: none;">
</div>

<?php
	$afModel = new AFInvoiceReconciliationHistory();
	$afModel->unsetAttributes();

	$ec = new CDbCriteria;
	$ec->with = ['af'];
	$ec->addCondition('af.supplier_id = 964 AND JSON_VALUE(t.meta, "$.insurance") = 1');

	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'af-billing-import-list-grid-airinsurance',
		'cssFile' => false,
		'dataProvider'=>$afModel->search($ec),
		'filter' => $afModel,
		'columns'=>array(
			'id',
			'created',
			array( 'name' => 'op_id','value' => '!empty($data->op) ? $data->op->name : ""'),

			array('name' => 'invoice_total','type' => 'raw',
				'value' => '"<a href=\"".Yii::app()->createURL("billing/afInvoiceImportAllView",["id"=> $data->id])."\" class=\"tab_link\" title=\".$data->invoice_total.\">".$data->invoice_total."</a>"'),


			array('name' => 'success','type' => 'raw',
				'value' => '$data->totalSuccess > 0 ? "<a href=\"".Yii::app()->createUrl("billing/afInvoiceImportSuccessView",["id"=>$data->id])."\" class=\"tab_link\" title=\"Success\">".$data->totalSuccess."</a>" : "0"'),

			array('name' => 'failed', 'type' => 'raw', 'value' => ' $data->totalFailed > 0 ? "<a href=\"".Yii::app()->createURL("billing/afInvoiceImportFailedView",["id"=> $data->id])."\" class=\"tab_link\" title=\"Fix Needed\">".$data->totalFailed."</a> (Post By GC : ".$data->getPostByGeneralCostCount().", Rejected : " . $data->getMatchedRejectedCount() . ")" : "0"'),

			array('name' => 'total', 'value' => '$data->total'),

			array('name' => 'totalSyncXeroSuccess','type' => 'raw',
				'value' => '"Success(<a class=\"tab_link\" title = \"Sync Success\" href=\"".Yii::app()->createURL("billing/afbSyncXeroSuccessView",["id"=> $data->id])."\">" . $data->getTotalSyncXeroSuccess() . "</a>) Failed(<a class=\"tab_link\" title = \"Sync failed\" href=\"".Yii::app()->createURL("billing/afbSyncXeroFailedView",["id"=> $data->id])."\">" . $data->getTotalSyncXeroFailed() . "</a>) Todo(" . $data->getTotalSyncXeroTodo() . ")"' ),

			array('name' => 'report_file_id','type' => 'raw','value' => '"<a href=\"". $data->getReportFileLink() ."\">Download</a>"'),
			array('name' => 'attached_file_id','type' => 'raw','value' => '"<a href=\"". $data->getAttachedFileLink() ."\">Download</a>"'),

			array(
				'class'=>'oButtonColumn',
				'template'=>'{update}{log}{xero}',
				'buttons'=>array(
					'update' => array(
						'imageUrl' => false,
						'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => 'Update Inv'),
						'visible' => 'true',
						'url' => 'Yii::app()->createUrl("billing/afbUpdateInv", ["fid" => $data->id])',
						'label' => 'Update Inv'
					),
					'log' => array(
						'imageUrl'=>false,
						'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'),
						'visible' => 'true',
						'url' => 'Yii::app()->createUrl("billing/afbimportResult", ["fid" => $data->id])',
						'label' => 'Log'
					),
					'xero' => array(
						'imageUrl'=>false,
						'options' => array('class' => 'jqm_link grid_swap_btn push_xero_op', 'label' => 'PushXero'),
						'visible' => 'true',
						'url' => 'Yii::app()->createUrl("billing/afbtoXero", ["fid" => $data->id])',
						'label' => 'Push Xero'
					),
				),
			),

		))
	); ?>

<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = tab.data('panel');
		var subtab = '<?=$_GET["tab"];?>';
		var timer;

		// The function to refresh the progress bar.
		function refreshProgress() {
			// We use Ajax again to check the progress by calling the checker script.
			// Also pass the session id to read the file because the file which storing the progress is placed in a file per session.
			// If the call was success, display the progress bar.
			$.ajax({
				url: "<?php echo Yii::app()->createAbsoluteUrl("billing/AjaxCheckEdiInvoiceProgress") ;?>",
				dataType: 'json',
				success:function(data){
					$("#progress_" + subtab, panel).html('<div class="bar" style="width:' + data.percent + '%"></div>');
					$("#message_" + subtab, panel).html(data.message);
					// If the process is completed, we should stop the checking process.
					if ( data.percent == 100 ) {
						window.clearInterval(timer);
						timer = window.setInterval(function(){
							window.clearInterval(timer);
							$("#message_" + subtab, panel).html("Completed");
							$("#progress_" + subtab, panel).hide();
							$("#message_" + subtab, panel).hide();
						}, 5e3);
					}
				}
			});
		}

		tab.bind('onOpen', function(){
			$('#af-billing-import-list-grid-airinsurance', panel).yiiGridView('update');
		});


		// Trigger the process in web server.
		// Refresh the progress bar every 1 second.
		$('form#export-af-billing-import-form-airinsurance input[type="submit"]',panel).click(function(e){
			//   alert('ok');
			$("#progress_" + subtab, panel).show();
			$("#message_" + subtab, panel).hide();
			$('#af_billing_import_result_' + subtab, panel).hide();
			timer = window.setInterval(refreshProgress, 1000);
		});

		$('form#export-af-billing-import-form-airinsurance', panel).data('custom_success', function(r){
			$('#af_billing_import_result_' + subtab, panel).show();
			$('#af_billing_import_result_' + subtab, panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
			$('form#export-af-billing-import-form-airinsurance #af_billing_import_btn', panel).attr('disabled', false);
			$('#af-billing-import-list-grid-airinsurance', panel).yiiGridView('update');
			return true;
		});

	});
</script>
