<style>
.uploading {
	background: url("https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif") no-repeat 0 0 !important;
	background-size: 20px 20px !important;
	background-color: white !important;
}
</style>

<h3>Select Invoice</h3>
<?php
$model = new Billing;
$model->unsetAttributes();
$model->attributes = @$_GET['Billing'];
$model->isForArrange = 1;
$selected = Yii::app()->cache->get('billing_streamline_selected_' . session_id()) ? Yii::app()->cache->get('billing_streamline_selected_' . session_id()) : [];
$ec = new CDbCriteria;
$ec->addCondition('t.status IN (' . implode(',', [Billing::BILLING_STATUS_POSTED, Billing::BILLING_STATUS_PARTIALLY_PAID]) . ')');
$ec->addCondition('t.org_id NOT IN (' . implode(',', [Org::ORGID_IMPORT_EXPENSE]) . ')');
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'billing-streamline-select-grid',
	'selectableRows' => 2,
	'cssFile' => false,
	'dataProvider' => $model->search(true, 15, 't.date ASC, t.id ASC', $ec),
	'filter' => $model,
	'columns' => array(
		array(
			'id' => 'selectedItems',
			'class' => 'CCheckBoxColumn',
			'checked' => 'in_array($data->id, [' . implode(',', array_keys($selected)) . '])',
		),
		array('name' => 'dpt_id', 'value' => '$data->getDptName()', 'filter' => CHtml::dropDownList('Billing[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt' => $this->t('All'))),),
		array('name' => 'dpmt', 'value' => '$data->getDpmt()', 'filter' => CHtml::dropDownList('Billing[dpmt]', $model->dpmt, Invoice::$dpmts, array('prompt' => $this->t('All'))),),
		array('name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(5)'),
		array('name' => 'billing_cref', 'value' => '$data->billing_cref'),
		array('name' => 'date', 'value' => '$data->date'),
		array('name' => 'currency', 'value' => '$data->getCurrency()', 'filter' => CHtml::dropDownList('Billing[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt' => $this->t('All'))),),
		array('header' => 'GST', 'name' => 'gst'),
		array('name' => 'total', 'value' => '$data->total'),
		array('header' => 'Dispute', 'value' => '!empty($data->getCredit())?$data->getDisputeAmount()."(".$data->getCredit().")":$data->getDisputeAmount()'),
		array('header' => 'Paid', 'value' => 'number_format($data->realPaid(), 2, ".", "")'),
		array('name' => 'balance', 'value' => '$data->getBalance()'),
		array('header' => 'To Pay', 'type' => 'raw', 'value' => '"<input type=\"text\" name=\"amount[" . $data->id . "]\" class=\"select-on-amount\" data-max=\"" . $data->getBalance() . "\" value=\"" . (in_array($data->id, [' . implode(',', array_keys($selected)) . ']) ? $data->getPayAmount() : "") . "\" />"'),
	),
));

// Yii::app()->clientScript->registerScript('tr-event-propagation', '$("#billing-streamline-select-grid tr").click(function(event){
// 	event.stopPropagation();
// });');
?>

<br>

<h3>Summary</h3>
<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'billing-stream-pay-form',
	'action' => Yii::app()->createUrl('billing/submitArrange'),
	'enableAjaxValidation' => false,
)); ?>

	<?php
	$data = Billing::getPayList();
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
	$sum_balance = 0;
	$sum_amount = 0;
	foreach ($data as $line) {
		$sum_subtotal += $line['subtotal'];
		$sum_gst += $line['gst'];
		$sum_total += $line['total'];
		$sum_balance += $line['balance'];
		$sum_amount += $line['amount'];
	}
	$this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'billing-streamline-summary-grid',
		'cssFile' => false,
		'dataProvider' => $dataProvider,
		'summaryText' => '',
		'columns' => array(
			array('name' => 'name', 'header' => 'Org Name'),
			array('name' => 'currency', 'header' => 'Currency'),
			array('name' => 'subtotal', 'header' => 'Total (Excl. GST)', 'footer' => '<b>' . AppHelper::money_format('%i', $sum_subtotal)  . '</b>'),
			array('name' => 'gst', 'header' => 'GST', 'footer' => '<b>' . AppHelper::money_format('%i', $sum_gst)  . '</b>'),
			array('name' => 'total', 'header' => 'Total (Incl. GST)', 'footer' => '<b>' . AppHelper::money_format('%i', $sum_total)  . '</b>'),
			array('name' => 'balance', 'header' => 'Outstanding (Incl. GST)', 'footer' => '<b>' . AppHelper::money_format('%i', $sum_balance)  . '</b>'),
			array('name' => 'amount', 'header' => 'To Pay', 'footer' => '<b style="color: red">' . AppHelper::money_format('%i', $sum_amount)  . '</b>'),
		),
	));
	?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit Arrange', ['id' => 'btn-create-file']); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

<script>
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
		obj.val('Submit Arrange');
		obj.removeProp('disabled');
	}

	function doSelect(obj) {
		var id = obj.parent().parent().find('td:nth-child(1) input').val();
		var balance = obj.parent().parent().find('td:nth-last-child(2)').html();
		var amount = obj.parent().parent().find('td:nth-last-child(1) input');
		if (obj.hasClass('select-on-check')) {
			var action = obj.prop('checked');
		} else {
			var action = true;
		}
		if (!amount.val() && action) {
			amount.val(balance);
			amount = balance;
		} else if (!action) {
			amount.val('');
			amount = 0;
		} else {
			if (parseFloat(amount.val()) > parseFloat(obj.data('max'))) {
				amount.val(obj.data('max'));
			}
			amount = amount.val();
		}
		if (action) {
			obj.parent().parent().css('background-color', 'rgb(188,231,116)');
		} else {
			obj.parent().parent().css('background-color', '#E5F1F4');
		}
		$.ajax({
			url: '<?=Yii::app()->createUrl("billing/selectPay")?>',
			type: 'POST',
			data: { 'id': id, 'action': action, 'amount': amount },
			success: function(r) {
				r = JSON.parse(r);
				if (r.done) {
					myApp.notice(r.msg, 5000);
				}
				$('#billing-streamline-summary-grid', panel).yiiGridView('update', {
					complete: function(jqXHR, status) {
						if (status == 'success') {
							uploading_off($('#btn-create-file', panel));
						}
					}
				});
			}
		});
	}

	$(panel).unbind('click', '#billing-streamline-select-grid tbody');
	$(panel).unbind('click', '#billing-streamline-select-grid tr');
	$(panel).unbind('click', '#billing-streamline-select-grid td');

	$(panel).off('change', '#billing-streamline-select-grid .select-on-check').on('change', '#billing-streamline-select-grid .select-on-check', function() {
		uploading_on($('#btn-create-file', panel));
		doSelect($(this));
	});

	$(panel).off('keyup', '#billing-streamline-select-grid .select-on-amount').on('keyup', '#billing-streamline-select-grid .select-on-amount', function() {
		uploading_on($('#btn-create-file', panel));
		doSelect($(this));
		$(this).parent().parent().find('td:nth-child(1) input').prop('checked', true);
	});

	$(panel).off('click', '#billing-streamline-select-grid .select-on-check-all').on('click', '#billing-streamline-select-grid .select-on-check-all', function() {
		if ($(this).prop('checked')) {
			$(panel).find('#billing-streamline-select-grid .select-on-check').each(function() {
				if (!$(this).prop('checked')) {
					$(this).trigger('click');
				}
			});
		} else {
			$(panel).find('#billing-streamline-select-grid .select-on-check').each(function() {
				if ($(this).prop('checked')) {
					$(this).trigger('click');
				}
			});
		}
	});

	$(panel).off('click', '#billing-streamline-select-grid .select-on-check-all').on('click', '#billing-streamline-select-grid .select-on-check-all', function() {
		if ($(this).prop('checked')) {
			$(panel).find('#billing-streamline-select-grid .select-on-check').each(function() {
				if (!$(this).prop('checked')) {
					$(this).trigger('click');
				}
			});
		} else {
			$(panel).find('#billing-streamline-select-grid .select-on-check').each(function() {
				if ($(this).prop('checked')) {
					$(this).trigger('click');
				}
			});
		}
	});

	$('#billing-stream-pay-form', panel).on('success', function() {
		$('#billing-streamline-select-grid', panel).yiiGridView('update');
		$('#billing-streamline-summary-grid', panel).yiiGridView('update');
		$('#billing-stream-pay-form', panel).trigger('reset');
	});
});
</script>