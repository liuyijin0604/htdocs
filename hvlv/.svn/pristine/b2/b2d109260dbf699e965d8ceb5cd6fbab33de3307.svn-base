<style>
.uploading {
	background: url("https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif") no-repeat 0 0 !important;
	background-size: 20px 20px !important;
	background-color: white !important;
}
</style>
<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'billing-stream-approved-form',
	'action' => Yii::app()->createUrl('billing/submitAdminApprove'),
	'enableAjaxValidation' => false,
)); ?>

<h3>Arranged Payment Billings</h3>
<?php
$model = new PaymentArrangeBilling();
$model->unsetAttributes();
$model->attributes = @$_GET['PaymentArrange'];
$model->searchBillingForBossApprove = true;

$selected = Yii::app()->cache->get('billing_streamline_approved_selected_' . session_id()) ? Yii::app()->cache->get('billing_streamline_approved_selected_' . session_id()) : [];

$ec = new CDbCriteria;
$ec->order = 'created ASC';

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'billing-streamline-select-grid',
	'selectableRows' => 2,
	'cssFile' => false,
	'id' => 'billing-streamline-approved-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, 30, 't.id DESC'),
	'filter' => $model,
	'columns' => array(
		array(
			'id' => 'selectedApproveItems',
			'class' => 'CCheckBoxColumn',
			'checked' => 'in_array($data->id, [' . implode(',', array_keys($selected)) . '])',
		),
		array('name' => 'payment_arrange.no'),
		array('name' => 'billing.billing_cref'),
		array('name' => 'payment_arrange.created'),
		array('name' => 'payment_arrange.user_id', 'value' => '$data->payment_arrange->getUser()'),
		array('name' => 'status', 'value' => '$data->getStatus()', 'filter' => CHtml::dropDownList('PaymentArrange[status]', $model->status, $this->t(PaymentArrange::$states), array('prompt' => $this->t('All'))),),
		array('name' => 'currency', 'value' => '$data->getCurrency()', 'filter' => CHtml::dropDownList('PaymentArrange[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt' => $this->t('All'))),),
		array('name' => 'billing.total','header'=>'Billing Total'),
		array('name' => 'amount','header'=>'topay'),
	
	),
));
?>
	 <div class="form-group">
        <div style="clear:both"></div>
       <?php echo CHtml::label('Admin Signature', 'admin_signature'); ?>
       <div style="clear:both"></div>
       <canvas style="border: 1px solid black; background-color:beige;" ></canvas>
              <?php echo CHtml::hiddenField('admin_signature'); ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit Approved', ['id' => 'btn-create-file']); ?>
	</div>


<?php $this->endWidget(); ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/signature_pad.min.js"></script>
<script>
$(function() {
	var tab = $('#<?=$_GET["tabid"]?>');
	var panel = tab.data('panel');

	function doSelect(obj) {
		if (obj.hasClass('select-on-check')) {
			var action = obj.prop('checked');
		} else {
			var action = true;
		}
		if (action) {
			obj.parent().parent().css('background-color', 'rgb(188,231,116)');
		} else {
			obj.parent().parent().css('background-color', '#E5F1F4');
		}

		var id = obj.parent().parent().find('td:nth-child(1) input').val();
		$.ajax({
			url: '<?=Yii::app()->createUrl("billing/selectApprove")?>',
			type: 'POST',
			data: { 'id': id, 'action': action},
			success: function(r) {
				r = JSON.parse(r);
				if (r.done) {
					myApp.notice(r.msg, 5000);
					uploading_off($('#btn-create-file', panel));
				}
			}
		});
	}
	$(panel).unbind('click','.select-on-check');
	$(panel).unbind('click', '#billing-streamline-approved-grid tbody');
	$(panel).unbind('click', '#billing-streamline-approved-grid tr');
	$(panel).unbind('click', '#billing-streamline-approved-grid td');
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

	$(panel).off('change', '#billing-streamline-approved-grid .select-on-check').on('change', '#billing-streamline-approved-grid .select-on-check', function() {
		uploading_on($('#btn-create-file', panel));
		doSelect($(this));
	});

	$(panel).off('keyup', '#billing-streamline-approved-grid .select-on-amount').on('keyup', '#billing-streamline-approved-grid .select-on-amount', function() {
		uploading_on($('#btn-create-file', panel));
		doSelect($(this));
		$(this).parent().parent().find('td:nth-child(1) input').prop('checked', true);
	});

	$(panel).off('click', '#billing-streamline-approved-grid .select-on-check-all').on('click', '#billing-streamline-approved-grid .select-on-check-all', function() {
		if ($(this).prop('checked')) {
			$(panel).find('#billing-streamline-approved-grid .select-on-check').each(function() {
				if (!$(this).prop('checked')) {
					$(this).trigger('click');
				}
			});
		} else {
			$(panel).find('#billing-streamline-approved-grid .select-on-check').each(function() {
				if ($(this).prop('checked')) {
					$(this).trigger('click');
				}
			});
		}
	});

	$(panel).off('click', '#billing-streamline-approved-grid .select-on-check-all').on('click', '#billing-streamline-approved-grid .select-on-check-all', function() {
		if ($(this).prop('checked')) {
			$(panel).find('#billing-streamline-approved-grid .select-on-check').each(function() {
				if (!$(this).prop('checked')) {
					$(this).trigger('click');
				}
			});
		} else {
			$(panel).find('#billing-streamline-approved-grid .select-on-check').each(function() {
				if ($(this).prop('checked')) {
					$(this).trigger('click');
				}
			});
		}
	});

	$('#billing-stream-approved-form', panel).on('submit', function() {
		$('#admin_signature').val(signaturePad.toDataURL());
	});

	$('#billing-stream-approved-form', panel).on('success', function() {
		$('#billing-streamline-approved-grid', panel).yiiGridView('update');
		$('#billing-stream-approved-form', panel).trigger('reset');
	});

	var signaturePad = new SignaturePad(document.querySelector("canvas"));



});
</script>