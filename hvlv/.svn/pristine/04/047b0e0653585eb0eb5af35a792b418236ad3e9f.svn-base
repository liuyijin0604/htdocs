<h1><?=$this->t('Billing entry');?></h1>

<div style="right: 20px;top:20px;position: absolute;">
	<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('billing/createGeneral');?>" title="Create General Cost"><div class="icon" style="background-position:-16px 0"></div>New General Cost</a>
	<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-import-billing"><div style="background-position:-192px -80px" class="icon"></div> Import Billing</a>
	<div id="<?=$_GET["tabid"];?>-dropdown-import-billing" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
		<ul class="dropdown-menu">
			<li><a href="<?=$this->createUrl('billing/importBilling', ['type' => 'template']);?>" class="jqm_link">Through General Template</a></li>

			<li class="divider">--------------<b>Courier</b>-------------<br /></li>
			<li><a href="<?=$this->createUrl('invoice/reconciliationNew');?>" class="jqm_link">Fastway Old File</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/fastwayReconciliationNew');?>" target="_blank">Fastway New File</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/aupostReconciliationInvoice');?>" target="_blank">Aupost Invoice</a></li>
			<li><a href="<?=$this->createUrl('invoice/aupostReconciliation');?>" class="jqm_link">&nbsp;&nbsp;&nbsp;Aupost Manifest</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/aupostReconciliationRTS');?>" target="_blank">&nbsp;&nbsp;&nbsp;Aupost RTS</a></li>
			<li><a href="<?=$this->createUrl('invoice/startrackReconciliation');?>" class="jqm_link">Startrack</a></li>
			<!-- <li><a href="<?=$this->createUrl('invoice/d2zReconciliation');?>" class="jqm_link">D2z</a></li> -->
			<!-- <li><a href="<?=$this->createUrl('billing/importBilling', ['type' => 'd2z_thc']);?>" class="jqm_link">D2z THC</a></li> -->
			<li><a href="<?=$this->createUrl('invoice/tntReconciliationNew');?>" class="jqm_link">TNT</a></li>
			<li><a href="<?=$this->createUrl('billing/importBilling', ['type' => 'austway']);?>" class="jqm_link">Austway</a></li>
			<!-- <li><a href="<?=$this->createUrl('invoice/globavendReconciliation');?>" class="jqm_link">Globavend</a></li> -->

			<li class="divider">--------------<b>Broker</b>--------------<br /></li>
			<li><a href="<?=$this->createUrl('billing/importBilling', ['type' => 'fyn']);?>" class="jqm_link">FYN</a></li>
			<li><a href="<?=$this->createUrl('billing/importBilling', ['type' => 'master']);?>" class="jqm_link">Master</a></li>

			<li class="divider">-------------<b>Terminal</b>------------<br /></li>
			<li><a href="<?=$this->createUrl('billing/importBilling', ['type' => 'qantas']);?>" class="jqm_link">Qantas</a></li>
			<li><a href="<?=$this->createUrl('billing/importBilling', ['type' => 'menzies']);?>" class="jqm_link">Menzies</a></li>

			<li class="divider">--------------<b>Air&Sea</b>------------<br /></li>
			<li><a href="<?=$this->createUrl('billing/importBilling', ['type' => 'yuyang']);?>" class="jqm_link">YuYang</a></li>
			<li><a href="<?=$this->createUrl('billing/importBilling', ['type' => 'insurance']);?>" class="jqm_link">Air Insurance</a></li>
			<li><a href="<?=$this->createUrl('billing/importBilling', ['type' => 'tne']);?>" class="jqm_link">T & E</a></li>
			<li><a href="<?=$this->createUrl('billing/importBilling', ['type' => 'skyjet']);?>" class="jqm_link">SkyJet</a></li>
		</ul>
	</div>
</div>

<div class="form" style="height: 600px;">

	<?php $model->setScenario('add');
	$form = $this->beginWidget('CActiveForm', [
		'id' => 'accounting-billing-entry-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('billing/create'),
	]); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'dpt_id'); ?>
		<?php
			$model->dpt_id = 106;
			echo $form->dropDownList($model, 'dpt_id', Org::dptList());
		?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'dpmt'); ?>
		<?php
			$allDpmts = Invoice::$dpmts;
			$allDpmts[1] = 'Auto Split';
		?>
		<?php echo $form->dropDownList($model, 'dpmt', $allDpmts, ['prompt' => $this->t('Select One')]); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'org_id'); ?>
		<?php echo $form->hiddenField($model, 'org_id', ['data-ov' => $model->org_id]);
			$acname1 = empty($_GET['tabid']) ? 'agent_ac' : $_GET['tabid'] . '_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', [
				'name' => $acname1,
				'sourceUrl' => ['org/supplierSuggest'],
				'value' => empty($model->cust) ? '' : $model->cust->name,
				'options' => [
					'showAnim' => 'fold',
					'minLength' => 2,
					'delay' => 200,
					'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]).trigger("change"); return false; }',
					'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				],
				'htmlOptions' => [
					'size' => 30,
				],
			]);
		?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'billing_cref'); ?>
		<?php echo $form->textField($model, 'billing_cref', ['size' => 20]); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'currency'); ?>
		<?php echo $form->dropDownList($model, 'currency', $this->t(Invoice::$currencies)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'date'); ?>
		<?php echo $form->textField($model, 'date', ['size' => 15, 'class' => 'date_input']); ?>
	</div>

	<div class="row">
		<label>Items</label>
		<?php
		$il = new BillingLine('search');
		$il->unsetAttributes();
		$il->billing_id = empty($model->id) ? -1 : $model->id;
		$this->widget('application.extensions.editablegrid.CEditableGridView', [
			'id' => 'billing-add-line-grid',
			'cssFile' => false,
			'dataProvider' => $il->search(),
			'summaryText' => '',
			'afterSave' => 'function(r) {
				if (r.done == true) {
					myApp.notice(r.msg, 5000);
				} else {
					myApp.alert(r.msg, false);
				}
				return r.done;
			}',
			'columns' => [
				['name' => 'billing_ref', 'class' => 'CEditableColumn'],
				['name' => 'desc', 'class' => 'CEditableColumn', 'type' => 'autocomplete', 'inputOptions' => ['size' => 30], 'value' => '$data->desc', 'acOptions' => ['source' => 'billing/lineDescSuggest']],
				['name' => 'note', 'class' => 'CEditableColumn'],
				['name' => 'qty', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 10, 'value' => 1]],
				['name' => 'price', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 10]],
				['name' => 'actual_total'],
				['name' => 'charge_code', 'class' => 'CEditableColumn', 'type' => 'autocomplete', 'value' => '$data->getCCodeDesc()', 'acOptions' => ['source' => 'chargeCode/chargeCodeSuggest2']],
				['header' => 'Tax Rate', 'name' => 'gst', 'class' => 'CEditableColumn', 'type' => 'list', 'filter' => Invoice::$InvoiceCostTaxRate, 'inputOptions' => ['options' => ['INPUT' => ['selected' => 'selected']]]],
				['class' => 'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}'],
			],
		]);
		?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Create')); ?>
		<?php echo CHtml::resetButton($this->t('Reset')); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>

<div id="file_upload"></div>

<script type="text/javascript">
$(function() {
	var tab = $('<?=$_GET["tabid"]?>');
	var panel = tab.data('pane');

	$('#billing-add-line-grid .add_btn', panel).on('click', function() {
		$('#billing-add-line-grid .items tbody td.empty').parent().remove();
		var r = $(this).parents('tr').clone();
		$('.add_btn', r).after('<a class="delete_btn" title="Delete" style="cursor:pointer">Delete</a>');
		$('.add_btn', r).remove();
		$('input', r).each(function() {
			var n = $(this).attr('name');
			$(this).attr('name', n + '[]');
		});
		$('select', r).each(function() {
			var n = $(this).attr('name');
			$(this).attr('name', n + '[]');
			$(this).val($('#billing-add-line-grid select[name="' + n + '"]').val());
		});
		$('#billing-add-line-grid .items tbody').append(r);
		$(this).parents('tr').find('input').val('');
		$(this).parents('tr').find('select').val('');
		$(this).parents('tr').find('input[name*="qty"]').val(1);
		$(this).parents('tr').find('option[value="INPUT"]').prop('selected', 'selected');
		$(this).parents('tr').find('span').html('');
		return false;
	});

	$('#billing-add-line-grid', panel).on('keyup', '#billing-add-line-grid_qty, #billing-add-line-grid_price', function() {
		var trElement = $(this).parent().parent();
		var qty = trElement.find('#billing-add-line-grid_qty').val();
		var price = trElement.find('#billing-add-line-grid_price').val();
		var total = myApp.formatNumber(parseFloat(qty) * parseFloat(price), 2, '.', ',');
		trElement.find('#billing-add-line-grid_price').parent().next().html('<span>' + total + '</span>');
	});

	$('#billing-add-line-grid', panel).on('autocompletecreate, focus', '.egacol_charge_code', function(){
		var dpmt = $('select#Billing_dpmt' ,panel).val();
		if(dpmt == ''){
			myApp.alert('Please select department first');
		}else{
			$(this).autocomplete({source : 'chargeCode/chargeCodeSuggest2?dpmt='+dpmt});
		}
	});

	$('#accounting-billing-entry-form', panel).on('success', function(e, r) {
		$('#file_upload').html(r.data);
	});

	$('input[type="reset"]', panel).on('click', function() {
		$('tbody').html('<tr><td colspan="8" class="empty"><span class="empty">No results found.</span></td></tr>');
		$('#billing-add-line-grid tfoot tr td:nth-child(5)').html('');
		$('#file_upload').html('');
	});

	$('#billing-add-line-grid', panel).on('click', '.delete_btn', function() {
		$(this).parent().parent().remove();
		return false;
	});

	$('#Billing_billing_cref', panel).on('change', function() {
		$.ajax({
			url: 'billing/billingcrefCheck',
			dataType: 'json',
			type: 'get',
			data: { 'no' : $(this).val() },
			success: function(r) {
				if (!r.done) {
					myApp.alert(r.msg);
				}
			}
		});
	});

	$('li a', panel).on('click', function() {
		$('#<?=$_GET["tabid"];?>-dropdown-import-billing', panel).hide(600);
	});
});
</script>