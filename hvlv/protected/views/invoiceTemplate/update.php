<h1><?=$this->t($model->isNewRecord) ? 'Create Invoice Template' : 'Update Invoice Template'?></h1>
<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'inv-temp-form',
	)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'ref'); ?>
		<?php echo $form->textField($model, 'ref'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'currency'); ?>
		<?php echo $form->dropDownList($model, 'currency', $this->t(Invoice::$currencies)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'type'); ?>
		<?php if (empty($model->type)) $model->type = 75; ?>
		<?php echo $form->dropDownList($model, 'type', $model::$types); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'dpt_id'); ?>
		<?php echo $form->dropDownList($model, 'dpt_id', Org::dptList(), array('prompt'=>$this->t('Select One'))); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'dpmt'); ?>
		<?php echo $form->dropDownList($model, 'dpmt', Invoice::$dpmts, array('prompt'=>$this->t('Select One'))); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'status'); ?>
		<?php echo $form->dropDownList($model, 'status', $model::$states); ?>
	</div>

	<br />

	<h2>Invoice Details</h2>
	<?php
	$il = new InvTempLine('search');
	$il->unsetAttributes();
	$il->temp_id = empty($model->id) ? 0 : $model->id;

	$this->widget('application.extensions.editablegrid.CEditableGridView', array(
		'id' => 'inv-temp-line-grid',
		'cssFile' => false,
		'dataProvider' => $il->search(),
		'formUrl' => $this->createUrl('invoiceTemplate/lineGrid', array('id' => $model->id)),
		'summaryText' => '',
		'afterSave' => 'function(r) {
			if (r.done == true) {
				myApp.notice(r.msg, 5000);
			} else {
				myApp.notice(r.msg, false);
			}
			return r.done;
		}',
		'columns' => array(
			array('header' => 'Type', 'name' => 'ccode', 'class' => 'CEditableColumn', 'type' => 'list', 'value' => '$data->getCCodeDesc', 'filter' => EdiJob::getChargeItemTypes()),
			array('header' => 'Description', 'name' => 'desc', 'class' => 'CEditableColumn'),
			array('header' => 'Qty', 'name' => 'qty', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5, 'class' => 'change-inv-amount']),
			array('header' => 'Rate', 'name' => 'rate', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5, 'class' => 'change-inv-amount']),
			array('header' => 'Total', 'name' => 'amount_total', 'htmlOptions' => ['class' => 'show-inv-amount']),
			array('header' => 'GST', 'name' => 'gst', 'class' => 'CEditableColumn', 'value' => '$data->getTaxType()', 'type' => 'list', 'filter' => Invoice::$InvoiceRevenueTaxRate),
			array(
				'class' => 'CEditableButtonColumn',
				'template' => '{edit} {cancel} {save} {delete}',
				'buttons' => array(
					'delete' => array(
						'imageUrl' => false,
						'url' => 'Yii::app()->createUrl("invoiceTemplate/lineGrid", array("id" => $data->id))',
					)
				)
			),
		),
	));
	?>

	<h2>Org Details</h2>
	<?php
	$io = new InvTempOrg('search');
	$io->unsetAttributes();
	$io->temp_id = empty($model->id) ? 0 : $model->id;

	$this->widget('application.extensions.editablegrid.CEditableGridView', array(
		'id' => 'inv-temp-org-grid',
		'cssFile' => false,
		'dataProvider' => $io->search(),
		'formUrl' => $this->createUrl('invoiceTemplate/orgGrid', array('id' => $model->id)),
		'summaryText' => '',
		'afterSave' => 'function(r) {
			if (r.done == true) {
				myApp.notice(r.msg, 5000);
			} else {
				myApp.notice(r.msg, false);
			}
			return r.done;
		}',
		'columns' => array(
			array('name' => 'org_id', 'class' => 'CEditableColumn', 'type' => 'autocomplete', 'value' => 'empty($data->org) ? "" : $data->org->shortName(4)', 'acOptions' => array('source' => 'org/agentSuggest')),
			array('name' => 'start', 'class' => 'CEditableColumn', 'inputOptions' => array('class' => 'date_input')),
			array('name' => 'end', 'class' => 'CEditableColumn', 'inputOptions' => array('class' => 'date_input')),
			array('name' => 'freq', 'class' => 'CEditableColumn', 'type' => 'list', 'value' => '$data->getFreq()', 'filter' => $io::$freq),
			array('name' => 'status', 'class' => 'CEditableColumn', 'type' => 'list', 'value' => '$data->getStatus()', 'filter' => $io::$states),
			array(
				'class' => 'CEditableButtonColumn',
				'template' => '{edit} {cancel} {save} {delete}',
				'buttons' => array(
					'delete' => array(
						'imageUrl' => false,
						'url' => 'Yii::app()->createUrl("invoiceTemplate/orgGrid", array("id" => $data->id))',
					)
				)
			),
		),
	));
	?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord) ? 'Create' : 'Save'); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
var panel;
$(function() {
	var tab = $('#jqmw_<?=$_GET["tabid"];?>');
	panel = tab.data('panel');

	$('#inv-temp-line-grid', panel).on('click', '.add_btn', function() {
		$('.jqmw_cont', panel).css('height', parseFloat($('.jqmw_cont', panel).css('height')) + 30) + 'px';
	});

	$('#inv-temp-line-grid', panel).on('input', '.change-inv-amount', function(e) {
		var trElement = $(this).parent().parent();
		var qty = trElement.find('input[name="InvTempLine[qty][]"]').val();
		if (typeof qty == 'undefined' || qty == '') {
			qty = trElement.find('input[name="InvTempLine[qty]"]').val();
			if (typeof qty == 'undefined' || qty == '') {
				qty = 0;
			}
		}
		var amountElement = trElement.find('input[name="InvTempLine[rate][]"]');
		var rate = amountElement.val();
		if (typeof rate == 'undefined' || rate == '') {
			amountElement = trElement.find('input[name="InvTempLine[rate]"]');
			rate = amountElement.val();
			if (typeof rate == 'undefined' || rate == '') {
				rate = 0;
			}
		}
		amountElement = amountElement.parent().next();
		var totalAmount = myApp.formatNumber(parseFloat(qty) * parseFloat(rate), 2, '.', ',');
		amountElement.html('<span class="show-inv-amount">' + totalAmount + '</span>');
	});

	$('#inv-temp-form', panel).on('success', function() {
		$('.popCancel').trigger('click');
		$('#invoice-template-grid', panel).yiiGridView('update');
	});
});
</script>