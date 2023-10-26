<?php $billing = Billing::model()->findByPk($id); ?>
<h1>Fix Lines - <?=$billing->billing_cref . ' - ' . (!empty($billing->cust->name) ? $billing->cust->name : '')?></h1>

<?php
$model = new BillingLine('search');
$model->unsetAttributes();
$model->attributes = @$_GET['BillingLine'];
$model->billing_id = $id;
$ledger_dp = $model->search();
$ledger_data = $ledger_dp->getData();
$billing = Billing::model()->findByPk($id);
?>

<div class="form">
<?php if ($billing->status <= 2 || Acl::hasAccess("B:Billing/delete")) { ?>
	<?php
	$this->beginWidget('CActiveForm', array(
		'id' => 'billing-fix-form',
		'enableAjaxValidation' => false,
	));
	?>

	<div class="row">
		<?php
			echo CHtml::label('Total', 'currency');
			echo CHtml::dropDownList('currency', $billing->currency, Billing::$currencies), '&nbsp;' . AppHelper::money_format('%i', $model->getTotal($ledger_data, 'actual_amount') + $model->getTotal($ledger_data, 'gst_amount'));
		?>
	</div>

	<div class="row">
		<?php
			echo CHtml::label('Exchange Rate', 'exrate');
			echo CHtml::textField('exrate', @$billing->mdata['exrate']);
		?>
	</div>

	<div class="row">
		<?php
			echo CHtml::label('Invoice', 'billing_cref');
			echo CHtml::textField('billing_cref', @$billing->billing_cref);
		?>
	</div>

	<div class="row">
		<?php
			echo CHtml::submitButton('Update');
			if ($billing->status >= 3) {
				echo ' ', CHtml::submitButton('Repost');
			}
		?>
	</div>

	<?php
	$this->endWidget();
	?>
<?php } ?>

	<div class="row">
		<?php
			echo CHtml::label('Selected Total', 'total');
			echo '<span id="total" style="color: red">0.00</span>';
		?>
	</div>
</div>

<?php
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id' => 'import-billingline-check-grid',
	'selectableRows' => 2,
	'showQuickBar' => false,
	'cssFile' => false,
	'dataProvider' => $model->search(),
	'formUrl' => $this->createUrl('billing/lineGrid', array('fid' => $model->id)),
	'filter' => $model,
	'columns' => array(
		array('id' => 'selectedItems', 'class' => 'CCheckBoxColumn'),
		array('name' => 'billing_ref', 'type' => 'raw', 'value' => '$data->getNo()'),
		array('name' => 'desc'),
		array('name' => 'type', 'value' => '$data->getType()', 'filter'=>CHtml::dropDownList('BillingLine[type]', $model->type, $this->t($model::$types), array('prompt'=>$this->t('All'))),),
		array('name' => 'charge_code', 'value' => '$data->getCCodeDesc()'),
		array('name' => 'status', 'value' => '$data->getStatus()'),
		array('name' => 'dpt_id', 'value' => '$data->getDptName()', 'filter' => CHtml::dropDownList('BillingLine[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt' => $this->t('All'))),),
		array('name' => 'dpmt', 'value' => '$data->getDpmt()', 'filter' => CHtml::dropDownList('BillingLine[dpmt]', $model->dpmt, Invoice::$dpmts, array('prompt' => $this->t('All'))),),
		array('name' => 'actual_amount', 'class' => 'CEditableColumn', 'footer' => $billing->getCurrency() . ' ' . AppHelper::money_format('%i', $model->getTotal($ledger_data, 'actual_amount'))),
		array('name' => 'gst', 'class' => 'CEditableColumn', 'type' => 'list', 'filter' => Invoice::$InvoiceCostTaxRate),
		array('name' => 'gst_amount', 'footer' => $billing->getCurrency() . ' ' . AppHelper::money_format('%i', $model->getTotal($ledger_data, 'gst_amount'))),
		array(
			'class' => 'CEditableButtonColumn',
			'template' => ($billing->status < 3 || Acl::hasAccess("B:Billing/delete")) ? '{edit}{cancel}{save}{delete}{fixaccrual}{fixinvoice}{retrieve}' : '{fixaccrual}{fixinvoice}',
			'buttons' => array(
				'delete' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("billing/importGridDelete", ["id" => $data->id])',
					'options' => array('class' => 'delete_btn'),
					'visible' => 'Acl::hasAccess("B:Billing/delete")',
				),
				'fixinvoice' => array(
					'imageUrl' => false,
					'visible' => '$data->status == 4',
					'url' => 'Yii::app()->createUrl("billing/invoiceFix", array("id" => $data->billing->id))',
					'options' => array('class' => 'grid_gallery_btn invoice_fix_btn'),
					'label' => 'Confirm Invoice',
				),
				'fixaccrual' => array(
					'imageUrl' => false,
					'visible' => '$data->status == 10',
					'url' => '$data->getNo($data->status)',
					'options' => array('class' => 'tab_link grid_gallery_btn'),
					'label' => 'Fix Accrual',
				),
				'retrieve' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("billing/sendOp", ["fid" => $data->id])',
					'label' => '',
					'options' => array('class' => 'ajax_link grid_swap_btn send_back_op'),
				),
			),
		),
	),
));
?>

<script type="text/javascript">
	$(function() {
		var tab = $("#jqmw_<?=$_GET['tabid'];?>");
		var panel = tab.data('panel');

		$('.save_btn.add_btn', panel).hide();

		// $('.tab_link', panel).on('click', function() {
		// 	$('.popCancel').trigger('click');
		// });

		$('.invoice_fix_btn').on('click', function() {
			$.ajax({
				type: 'POST',
				url: $(this).attr('href'),
				success: function(r) {
					r = JSON.parse(r);
					myApp.notice(r.msg, 5000);
					setTimeout(function() {
						$('.popCancel').trigger('click');
						$("#<?=$_GET['tabid'];?>").trigger('onOpen');
					}, 1e3);
				}
			});
			return false;
		});

		$(tab).on('change', 'input[id*="actual_amount"]', function() {
			var actual_amount = $(this).val();
			var gst = $(this).parent().next().find('select').val();

			if (gst === 'EXEMPTEXPENSES') {
				$(this).parent().next().next().html('0.00');
			} else if (gst === 'INPUT') {
				$(this).parent().next().next().html(Math.round(actual_amount * 0.1 * 100) / 100);
			}
		});

		$(tab).on('change', 'select', function() {
			var gst = $(this).val();
			var actual_amount = $(this).parent().prev().find('input').val();

			if (gst === 'EXEMPTEXPENSES') {
				$(this).parent().next().html('0.00');
			} else if (gst === 'INPUT') {
				$(this).parent().next().html(Math.round(actual_amount * 0.1 * 100) / 100);
			}
		});

		$('#billing-fix-form', tab).on('success', function() {
			var title = $('h1', tab).html();
			var no = $('#billing_cref', tab).val();
			title = title.split(' - ');
			title[1] = no;
			title = title.join(' - ');
			$('h1', tab).html(title);
			$('#import-billingline-check-grid').yiiGridView('update');
		});

		$('.popCancel', tab).on('click', function() {
			$('#billing-streamline-post-grid').yiiGridView('update');
		});

		$(tab).on('click', 'tr', function(e) {
			selectedTotal();
		});

		$(tab).on('click', '.select-on-check-all', function() {
			setTimeout(function() {
				selectedTotal();
			}, 1e1);
		});

		function selectedTotal() {
			var total = 0;

			$('.select-on-check', tab).each(function() {
				if ($(this).prop('checked')) {
					var item = $(this).parent().parent();

					var actualAmount = parseFloat(item.find('td:nth-child(7)').find('span').html());
					if (isNaN(actualAmount)) actualAmount = 0;

					var gstValue = parseFloat(item.find('td:nth-child(9)').html());
					if (isNaN(gstValue)) gstValue = 0;

					total += actualAmount + gstValue;
				}
			});

			if (total < 0) total = 0;
			total = myApp.formatNumber(total, 2, '.', ',');

			$('#total', tab).html(total);
		}

		$(tab).on('click', '.send_back_op', function(e) {
			if (!confirm('Are your sure send back to operator?')) {
				e.stopPropagation();
				e.preventDefault();
			}
		});

		$('.send_back_op').on('success', function() {
			$('#import-billingline-check-grid').yiiGridView('update');
			setTimeout(function() {
				selectedTotal();
			}, 1e3);
		});
	});
</script>