<style>
.uploading {
	background: url("https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif") no-repeat 0 0 !important;
	background-size: 20px 20px !important;
	background-color: white !important;
}
</style>

<h1><?=$model->no?> Requiring Admin Approve</h1>

<h3>Billings in payment</h3>
<?php

$link = new PaymentArrangeBilling;
$link->payment_arrange_id = $model->id;
$ec = new CDbCriteria;
$ec->with = 'billing';
$ec->order = 'billing.org_id ASC, t.id ASC';



$data = $link->search(false, 0, '', $ec)->data[0];

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'billing-streamline-payment-arrange-select-grid',
	'cssFile' => false,
	'dataProvider' => $link->search(false, 0, '', $ec),
	'columns' => array(
		array('name' => 'billing.dpt_id', 'value' => '$data->billing->getDptName()'),
		array('name' => 'billing.dpmt', 'value' => '$data->billing->getDpmt()'),
		array('name' => 'billing.client', 'value' => ' ( empty($data->billing->org_id) || empty($data->billing->cust) ) ? "" : $data->billing->cust->shortName(5)'),
		array('name' => 'billing.billing_cref','type'=>'raw','value'=>'!empty($data->billing->getSiReconcileId())?"<a class=\"tab_link\" href=\"".Yii::app()->createURL("siReconcile/getReconcileDpmt")."?id=".$data->billing->getSiReconcileId()."\" title=\"SRDetail".$data->billing->getSiReconcileId()."\">".$data->billing->billing_cref."</a>":$data->billing->billing_cref'),
		array('header' => 'Si Reconcile Type','value'=>'!empty($data->billing->getSiReconcileType())?SiReconcile::$types[$data->billing->getSiReconcileType()]:""'),
		array('name' => 'billing.date'),
		array('name' => 'billing.total'),
		array('header' => 'diff','type'=>'raw','value'=>'!empty($data->billing->getSiReconcileId())?($data->billing->getSiReconcileDiff()>0?($data->billing->getSiReconcileType()==SiReconcile::TYPE_MANUAL?"isManual":"<span class=\'warn_red\'>".$data->billing->siReconcileDiff."</span>"):$data->billing->siReconcileDiff):0'),
		array('header' => 'Dispute', 'type' => 'raw', 'value' => '$data->billing->getDisputeAmount(true)'),
		array('header' => 'To Pay', 'type' => 'raw', 'value' => '"<input type=\"text\" name=\"amount[" . $data->id . "]\" class=\"select-on-amount\" data-id=\"" . $data->id . "\" data-max=\"" . $data->billing->getBalance() . "\" value=\"" . $data->amount . "\" />"', 'visible' => $model->status == PaymentArrange::PAYMENT_ARRANGE_STATUS_NEW),
		array('header' => 'To Pay', 'type' => 'raw', 'value' => '$data->amount', 'visible' => $model->status != PaymentArrange::PAYMENT_ARRANGE_STATUS_NEW),
	),
));
?>

<br>

<h3>Summary</h3>
<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'billing-stream-approve-payment-arrange-form',
	'enableAjaxValidation' => false,
)); ?>

	<?php
	$data = $model->getSummary();
	$filtersForm = new FiltersForm();
	$filteredData = $filtersForm->filter($data);
	$dataProvider = new CArrayDataProvider($filteredData, array(
		'pagination' => array(
			'pageSize' => 100,
		),
	));
	$sum_subtotal = 0;
	$sum_gst = 0;
	$sum_total = 0;
	$sum_dispute_paid = 0;
	$sum_balance = 0;
	$sum_amount = 0;
	foreach ($data as $line) {
		$sum_subtotal += $line['subtotal'];
		$sum_gst += $line['gst'];
		$sum_total += $line['total'];
		$sum_dispute_paid += $line['dispute_paid'];
		$sum_balance += $line['balance'];
		$sum_amount += $line['amount'];
	}
	$this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'billing-streamline-payment-arrange-summary-grid',
		'cssFile' => false,
		'dataProvider' => $dataProvider,
		'summaryText' => '',
		'columns' => array(
			array('name' => 'name', 'header' => 'Supplier Name'),
			array('name' => 'currency', 'header' => 'Currency'),
			array('name' => 'subtotal', 'header' => 'Total (Excl. GST)', 'footer' => '<b>' . AppHelper::money_format('%i', number_format($sum_subtotal, 2, '.', ''))  . '</b>'),
			array('name' => 'gst', 'header' => 'GST', 'footer' => '<b>' . AppHelper::money_format('%i', number_format($sum_gst, 2, '.', ''))  . '</b>'),
			array('name' => 'total', 'header' => 'Total (Incl. GST)', 'footer' => '<b>' . AppHelper::money_format('%i', number_format($sum_total, 2, '.', ''))  . '</b>'),
			array('name' => 'dispute_paid', 'header' => 'Dispute + Paid', 'footer' => '<b>' . AppHelper::money_format('%i', number_format($sum_dispute_paid, 2, '.', ''))  . '</b>'),
			array('name' => 'balance', 'header' => 'Outstanding (Incl. GST)', 'footer' => '<b>' . AppHelper::money_format('%i', number_format($sum_balance, 2, '.', ''))  . '</b>'),
			array('name' => 'amount', 'header' => 'To Pay', 'footer' => '<b style="color: red">' . AppHelper::money_format('%i', number_format($sum_amount, 2, '.', ''))  . '</b>')
		),
	));
	?>
	<div class="form-group">
	    <div class="row">
	        <div class="row rowcol rowleft">
	                <?php echo $form->labelEx($model,'comment'); ?>
	                 <?php echo $form->textArea($model,'comment',array('cols'=>60, 'rows' => 10)); ?>
					 
	        </div>
        </div>

		<div class="row">
	        <div style="clear:both"></div>
	       <?php echo CHtml::label('Admin Signature', 'admin_signature'); ?>
	       <div style="clear:both"></div>
	       <canvas style="border: 1px solid black; background-color:beige;" id="signaturepad"></canvas>
	              <?php echo CHtml::hiddenField('admin_signature'); ?>
	              <?php echo CHtml::hiddenField('operation'); ?>
        </div>
         <button type="submit" class="btn btn-primary clear_btn">Clear</button>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit Admin Approve', ['class' => 'btn', 'name' => 'act_btn','class'=>'act_btn']) . '&nbsp;&nbsp;' . CHtml::submitButton('Admin Reject', ['class' => 'btn', 'name' => 'reject_btn','class'=>'act_btn']); ?>
	</div>

<?php $this->endWidget(); ?>
</div>


<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/signature_pad.min.js"></script>
<script type="text/javascript">
$(function() {
	var tab = $('#<?=$_GET["tabid"]?>');
	var panel = tab.data('panel');
	var signaturePad = new SignaturePad(document.querySelector("canvas"));
	signaturePad.clear();

	function uploading_on(obj) {
		obj.addClass('uploading');
		obj.val('    Loading');
		obj.prop('disabled', 'disabled');
	}

	function uploading_off(obj,previous) {
		obj.removeClass('uploading');
		obj.val(previous);
		obj.removeProp('disabled');
	}
	var submitingLabel = "";
	$('.act_btn', panel).on('click', function() {
		console.log($(this));
		if($(this).val()=="Submit Admin Approve")
		{
			if(confirm("Are you sure to sign and approve this payment?"))
			{
				submitingLabel = $(this).val();
				uploading_on($(this));

				$('#admin_signature').val(signaturePad.toDataURL());
				if(signaturePad.isEmpty())
				{
					uploading_off($(this),submitingLabel);
					alert("Approving payment is requiring admin signature");
					return false;
				}

				$("#operation",panel).val("Submit Admin Approve");
				$('#billing-stream-approve-payment-arrange-form', panel).submit();
			}else
			{
				return false;
			}
		}else
		{
			if(!confirm("Are you sure to reject this payment?"))
			{
				return false;
			}else
			{
				submitingLabel = $(this).val();
				uploading_on($(this));

				if($('#PaymentArrange_comment',panel).val()=="")
				{
					uploading_off($(this),submitingLabel);
					alert("Payment reject is requiring comment");
					return false;
				}

				$("#operation",panel).val("Admin Reject");
				$('#billing-stream-approve-payment-arrange-form', panel).submit();
			}
		}
	});

	$('#billing-stream-approve-payment-arrange-form', panel).on('success', function() {
		$('#billing-streamline-approve-pay-grid').yiiGridView('update');

		tab.trigger('close');
	});


	$('button.clear_btn').on("click", function (event) {
	     signaturePad.clear();
	     return false;
    });


});
</script>