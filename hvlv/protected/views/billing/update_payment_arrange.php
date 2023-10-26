<style>
.uploading {
	background: url("https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif") no-repeat 0 0 !important;
	background-size: 20px 20px !important;
	background-color: white !important;
}
</style>

<h1>Update <?=$model->no?></h1>

<h3>Select Invoice</h3>
<?php
$link = new PaymentArrangeBilling;
$link->payment_arrange_id = $model->id;
$ec = new CDbCriteria;
$ec->with = 'billing';
$ec->order = 'billing.org_id ASC, t.id ASC';
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
		array('name' => 'billing.currency', 'value' => '$data->billing->getCurrency()'),
		array('header' => 'GST', 'name' => 'billing.gst'),
		array('name' => 'billing.total'),
		array('header' => 'Dispute', 'type' => 'raw', 'value' => '$data->billing->getDisputeAmount(true)'),
		array('header' => 'Paid', 'value' => 'number_format($data->billing->total - $data->billing->getDisputeAmount() - $data->billing->getBalance(), 2, ".", "")'),
		array('name' => 'billing.balance', 'value' => '$data->billing->getBalance()'),
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
			array('name' => 'name', 'header' => 'Org Name'),
			array('name' => 'currency', 'header' => 'Currency'),
			array('name' => 'subtotal', 'header' => 'Total (Excl. GST)', 'footer' => '<b>' . AppHelper::money_format('%i', number_format($sum_subtotal, 2, '.', ''))  . '</b>'),
			array('name' => 'gst', 'header' => 'GST', 'footer' => '<b>' . AppHelper::money_format('%i', number_format($sum_gst, 2, '.', ''))  . '</b>'),
			array('name' => 'total', 'header' => 'Total (Incl. GST)', 'footer' => '<b>' . AppHelper::money_format('%i', number_format($sum_total, 2, '.', ''))  . '</b>'),
			array('name' => 'dispute_paid', 'header' => 'Dispute + Paid', 'footer' => '<b>' . AppHelper::money_format('%i', number_format($sum_dispute_paid, 2, '.', ''))  . '</b>'),
			array('name' => 'balance', 'header' => 'Outstanding (Incl. GST)', 'footer' => '<b>' . AppHelper::money_format('%i', number_format($sum_balance, 2, '.', ''))  . '</b>'),
			array('name' => 'amount', 'header' => 'To Pay', 'footer' => '<b style="color: red">' . AppHelper::money_format('%i', number_format($sum_amount, 2, '.', ''))  . '</b>'),
			array('name' => 'bank', 'header' => 'Bank account', 'type' => 'raw'),
			array('name' => 'id', 'header' => 'Desc', 'type' => 'raw', 'value' => '"<input type=\"text\" name=\"desc[" . $data["id"] . "]\" value=\"" . (!empty($data["desc"]) ? $data["desc"] : "") . "\" />"'),
		),
	));
	?>
	<div class="row">
		<div class="row rowcol rowleft">
	            <?php echo $form->labelEx($model,'comment'); ?>
	             <?php echo $form->textArea($model,'comment',array('cols'=>60, 'rows' => 10,'disabled'=>'disabled')); ?>
				 
	    </div>
    </div>

    <?php
    echo "<div class='row'><h4>Signature:</h4><img src='".@$model->signature->signature."'/></div>";
    ?>


	<?php if ($model->status == PaymentArrange::PAYMENT_ARRANGE_STATUS_NEW||$model->status == PaymentArrange::PAYMENT_ARRANGE_STATUS_BOSS_APPROVED) { ?>
	<div class="row">
		<?php echo CHtml::dropDownList('bank_account', !empty($model->mdata['bank_account']) ? $model->mdata['bank_account'] : '', BankAccount::getAllForBilling(), ['empty' => 'Select One']); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Approve', ['class' => 'btn', 'name' => 'act_btn']) . '&nbsp;&nbsp;' . CHtml::submitButton('Reject', ['class' => 'btn', 'name' => 'act_btn']); ?>
	</div>
	<?php } ?>

<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
$(function() {
	var tab = $('#<?=$_GET["tabid"]?>');
	var panel = tab.data('panel');

	function uploading_on(obj) {
		obj.addClass('uploading');
		obj.val('    Loading');
		obj.prop('disabled', 'disabled');
	}

	function uploading_off(obj) {
		obj.removeClass('uploading');
		obj.val('Approve');
		obj.removeProp('disabled');
	}

	$(panel).off('keyup', '#billing-streamline-payment-arrange-select-grid .select-on-amount').on('keyup', '#billing-streamline-payment-arrange-select-grid .select-on-amount', function() {
		uploading_on($('.btn', panel));
		var amount = $(this);
		var id = $(this).data('id');
		if (parseFloat(amount.val()) > parseFloat($(this).data('max'))) {
			amount.val($(this).data('max'));
		}
		amount = amount.val();
		$.ajax({
			url: '<?=Yii::app()->createUrl("billing/updatePaymentArrangeAmount")?>',
			type: 'POST',
			data: { 'id': id, 'amount': amount },
			success: function(r) {
				$('#billing-streamline-payment-arrange-summary-grid', panel).yiiGridView('update', {
					complete: function(jqXHR, status) {
						if (status == 'success') {
							uploading_off($('.btn', panel));
						}
					}
				});
			}
		});
	});

	$('#billing-stream-approve-payment-arrange-form', panel).on('success', function() {
		$('#billing-streamline-pay-grid').yiiGridView('update');

		tab.trigger('close');
	});
});
</script>