
<h1><?=$this->t('Billing waiting for confirm');?></h1>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'billing-op-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="form">
	<div class="row rowcol">
		<?php echo CHtml::label('Invoice No.','for_op'); ?>
		<?php echo CHtml::textField('invoice_no','', ['size' => '12']); ?>
	</div>
<div class="row buttons" style="margin-bottom: 20px;">
	<?php
	echo CHtml::hiddenField('actual', 1);
	echo CHtml::submitButton('Confirm'); //CHtml::button('Confirm', array('class' => 'billing-op-btn' , 'type' => 'submit'));
	?>
</div>
</div>
<?php
// application.extensions.editablegrid.CEditableGridView
// zii.widgets.grid.CGridView
$ledger_dp = $model->search();
$ledger_data = $ledger_dp->getData();
$currency = 'AUD';
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id'=>'billing-op-grid',
	'selectableRows' => 2,
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'formUrl' => $this->createUrl('billing/updateLine'),
	'filter'=>$model,
	'showQuickBar' => false,
	'columns'=>array(
		array(
			'id'=>'selectedItems',
			'class'=>'CCheckBoxColumn',
		),

		array( 'header' => 'Job#','type' => 'raw', 'name' => 'billing_ref', 'value' => '$data->getNo()'),
		array('name' => 'awb', 'value' => '$data->awb'),
		'billing_cref',
		array('name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(3)'),
	  //  array('name' => 'status', 'value' => '$data->getStatus()',
		//    'filter'=>CHtml::dropDownList('Billing[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'))),),
	 //   array('name' => 'type', 'value' => '$data->getType()',
	   //     'filter'=>CHtml::dropDownList('Billing[type]', $model->type, $this->t($model::$types), array('prompt'=>$this->t('All'))),),
		'desc',
		array('name' => 'currency', 'value' => '$data->getCurrency()',
			'filter'=>CHtml::dropDownList('Billing[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt'=>$this->t('All'))),),

		array('name' => 'dpt_id', 'value' => '$data->getDptName()',
				'filter'=>CHtml::dropDownList('Billing[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt'=>$this->t('All'))),),
		'weight',
		'charge_weight',
		'date',
		'due',
		array('name' => 'accrual_amount', 'value' => '$data->accrual_amount', 'htmlOptions' => array('class' => 'accrual_amount')),
		array('name' => 'actual_amount','class' => 'CEditableColumn', 'footer' => $currency . ' ' . AppHelper::money_format("%i", $model->getTotal($ledger_data,'actual_amount'))),
		array(
			'class'=>'CEditableButtonColumn',
			'template'=>'{edit} {cancel} {save}',
		),
	),
)); ?>

<?php $this->endWidget(); ?>

<script type="text/javascript">
	$(function(){
		var tab = $("#<?=$_GET['tabid'];?>");
		var panel = tab.data('panel');

		tab.bind('onOpen', function(){
			$('#billing-op-grid', panel).yiiGridView('update');
		});

		$('form#billing-op-form', panel).on('success', function(e, r){
			$('#billing-op-grid', panel).yiiGridView('update');
			return true;
		});

		$('#billing-op-grid', panel).parent().off('change', '.select-on-check').on('change', '.select-on-check', function() {
			if ($(this).prop('checked')) {
				var accrual_amount = $(this).parent().parent().find('.accrual_amount').html();
				var input = $(this).parent().parent().find('input[name="BillingLine[actual_amount]"]');
				input.val(accrual_amount);
				input.next().html(accrual_amount);
			}
		});

	});
</script>