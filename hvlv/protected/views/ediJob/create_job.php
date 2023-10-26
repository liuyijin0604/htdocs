<h1><?=$this->t('Create Air Freight & 3PL Job');?></h1>


<div class="form">
	<?php
		$goods = EdiJob::$GOODS;
		$selectedGoods = array();
		if ( isset($awbModel->mdata['goods']) ) {
			$selectedGoods = $awbModel->mdata['goods'];
		}
		$leg2 = array('airline' => '','flight' => '','etd' => '','eta' => '', 'atd' => '', 'ata' => '');
		if ( isset($awbModel->mdata['leg2']) ) {
			$leg2 = $awbModel->mdata['leg2'];
		}
	?>

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'edi-job-form',
		'enableAjaxValidation'=>false,
	)); ?>

	<div class="row rowcol">
		<?php echo $form->labelEx($awbModel, 'owner_id'); ?>
		<?php echo $form->hiddenField($awbModel, 'owner_id', array('data-ov' => $awbModel->owner_id));
		$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $acname1,
			'sourceUrl' => array('org/exAgentSuggest'),
			'value' => empty($awbModel->owner->name) ? '' : $awbModel->owner->name,
			'options' => array(
				'showAnim' => 'fold',
				'minLength' => 2,
				'delay' => 200,
				'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]).trigger("change"); return false; }',
				'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
			),
			'htmlOptions' => array(
				'size' => '30',
			),
		));
		?>
	</div>

	<div class="rowcol">
		<?php echo $form->labelEx($awbModel, 'awb'); ?>
		<?php echo $form->textField($awbModel, 'awb', array('size' => 15, 'maxlength' => 50)); ?>
	</div>

	<div class="rowcol">
		<?php echo $form->labelEx($awbModel, 'pol'); ?>
		<?php $list = AppHelper::setting2List('pols'); ksort($list); ?>
		<?php echo $form->dropDownList($awbModel, 'pol', $list); ?>
	</div>

	<div class="rowcol">
		<?php echo $form->labelEx($awbModel, 'pod'); ?>
		<?php $list = AppHelper::setting2List('pols'); ksort($list); ?>
		<?php echo $form->dropDownList($awbModel, 'pod', $list); ?>
	</div>

	<div class="rowcol">
		<?php echo $form->labelEx($awbModel,'dpt_id'); ?>
		<?php if ($awbModel->dpt_id == 0) { $awbModel->dpt_id = 106; } ?>
		<?php echo $form->dropDownList($awbModel, 'dpt_id', Org::dptList(), array('prompt'=>$this->t('Select One'))); ?>
	</div>

	<div class="row rowcol rowleft" style="margin-top: 15px;font-weight: bold;">
		<span>Leg One</span>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($awbModel,'airline'); ?>
		<?php echo $form->textField($awbModel,'airline',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($awbModel,'flight'); ?>
		<?php echo $form->textField($awbModel,'flight',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($awbModel,'etd'); ?>
		<?php echo $form->textField($awbModel,'etd', array('size' => 12, 'id' => 'etd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($awbModel,'eta'); ?>
		<?php echo $form->textField($awbModel,'eta', array('size' => 12, 'id' => 'eta_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($awbModel,'mdata[atd]'); ?>
		<?php echo $form->textField($awbModel,'mdata[atd]', array('size' => 12, 'id' => 'atd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($awbModel,'mdata[ata]'); ?>
		<?php echo $form->textField($awbModel,'mdata[ata]', array('size' => 12, 'id' => 'ata_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol rowleft" style="font-weight: bold;">
		<span>Leg Two</span>
	</div>
	<div class="row rowcol">
		<?php echo CHtml::textField('airline',$leg2['airline'],array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo  CHtml::textField('flight',$leg2['flight'],array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::textField('etd2',$leg2['etd'], array('size' => 12, 'id' => 'etd2_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::textField('eta2',$leg2['eta'], array('size' => 12, 'id' => 'eta2_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::textField('atd2',$leg2['atd'], array('size' => 12, 'id' => 'atd2_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::textField('ata2',$leg2['ata'], array('size' => 12, 'id' => 'ata2_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row">

		<?php echo CHtml::label('Commodity','for_edi_awb_new_goods') ?>
		<div style="margin-top: 10px;"></div>
		<?php
		echo CHtml::checkBoxList('selected_goods',$selectedGoods,$goods,array(
			'template'=>'{input}{label}',
			'separator'=>'',
			'labelOptions'=>array(
				'style'=> 'padding-right:12px;min-width: 60px;float: left;'),
			'style'=>'float:left;',) );
		?>
	</div>

	<div class="row"style="margin-top: 20px;">
		<?php echo CHtml::label('Notes','for_edi_awb_new_notes') ?>
		<?php echo CHtml::textArea('notes', $awbModel->custom_log_note, array('rows'=>4, 'cols' => 60)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'dpmt'); ?>
		<?php if ($model->dpmt == 0) { $model->dpmt = 30; } ?>
		<?php if (Yii::app()->user->id == 305) { $model->dpmt = 40; } ?>
		<?php echo $form->dropDownList($model, 'dpmt', Job::$dpmts, array('prompt'=>$this->t('Select One'))); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php echo $form->textField($model,'created', ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'due'); ?>
		<?php echo $form->textField($model,'due', ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'currency'); ?>
		<?php echo $form->dropDownList($model, 'currency', $this->t(Invoice::$currencies)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Template','for_basic_tempate'); ?>
		<?php echo CHtml::dropDownList('org-edi-template', '0',array('0' => 'Select One')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::button($this->t('Load Template'), array('style' => 'margin-top: 15px;', 'id' => 'load-edi-template')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Weight Gross', 'jbv_gw'); ?>
		<?php echo CHtml::textField('jbv_gw', ''); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::button($this->t('Auto Fill'), array('style' => 'margin-top: 15px;', 'id' => 'auto-fill')); ?>
	</div>

	<br/>
	<div class="row" style="width: 70%">
		<h2>Invoice Details</h2>
		<?php
		$il = new JobLine('search');
		$il->unsetAttributes();
		$il->job_id = 0;

		$this->widget('application.extensions.editablegrid.CEditableGridView', array(
			'id' => 'edi-job-invoice-grid',
			'cssFile' => false,
			'dataProvider'=> $il->search(),
			'enableSorting' => false,
			'formUrl' => $this->createUrl('ediJob/linesInvoiceGrid', array('id' => 0 )),
			'summaryText' => '',
			'afterSave' => "function(r){
			if(r.done == true){
				myApp.notice(r.msg, 5000);
			}else{
				myApp.alert(r.msg, false);
			}
			return r.done;
		}",
			'columns'=>array(

				array('header' => 'Type','name' => 'ccode','class' => 'CEditableColumn','type' => 'list' ,
					'value' => '$data->getCCodeDesc' ,
					'filter'=> EdiJob::getChargeItemTypes()),

				array('header' => 'Description','name' => 'desc', 'class' => 'CEditableColumn'),
				array('header' => 'Qty/Kg','name' => 'qty', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-inv-amount']),
				array('header' => 'Rate','name' => 'rate', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-inv-amount']),
				//array('header' => 'Gst','name' => 'inv_gst', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5]),
				array('header' => 'Amount','name' => 'invAmount','htmlOptions' => ['class' => 'show-inv-amount']),
				array('header' => 'GST', 'name' => 'inv_gst','class' => 'CEditableColumn', 'value' => '$data->getTaxType()','type' => 'list',
					'filter'=> Invoice::$InvoiceRevenueTaxRate ),
				array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}'),
			),
		));
		?>
	</div>

	<div class="row" style="width: 70%">
		<div class="row rowcol">
			<h2>Cost Details</h2>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::button($this->t('Clear'), array('style' => 'position: relative', 'id' => 'clear_cost')); ?>
		</div>
		<?php
		$il = new BillingLine('search');
		$il->unsetAttributes();
		$il->org_id = -1;
		$this->widget('application.extensions.editablegrid.CEditableGridView', array(
			'id' => 'edi-job-cost-grid',
			'cssFile' => false,
			'dataProvider'=> $il->search(),
			'enableSorting' => false,
			'formUrl' => $this->createUrl('ediJob/linesCostGrid', array('id' => 0 )),
			'summaryText' => '',
			'afterSave' => "function(r){
			if(r.done == true){
				myApp.notice(r.msg, 5000);
			}else{
				myApp.alert(r.msg, false);
			}
			return r.done;
		}",
			'columns'=>array(
				array('header' => 'Type','name' => 'item_code','class' => 'CEditableColumn','type' => 'list' ,
					'value' => '$data->getCCodeDesc' ,
					'filter'=> EdiJob::getChargeItemTypes()),
				array('header' => 'Description','name' => 'desc', 'class' => 'CEditableColumn'),
				array('header' => 'Qty/Kg','name' => 'qty', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-cost-amount']),
				array('header' => 'Rate','name' => 'price', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-cost-amount']),
				array('header' => 'Amount','name' => 'costAmount','htmlOptions' => ['class' => 'show-cost-amount']),
				array('header' => 'Supplier','name' => 'org_id','class' => 'CEditableColumn','type' => 'autocomplete' ,
					'value' => 'empty($data->org_id) || empty($data->cust) ? "" : $data->cust->name' ,
					'acOptions' => array('source' => 'org/supplierSuggest' )
				),
				array('header' => 'GST', 'name' => 'gst','class' => 'CEditableColumn', 'value' => '$data->getCostTaxType()','type' => 'list',
					'filter'=> Invoice::$InvoiceCostTaxRate ),
				array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}'),
			),
		));
		?>
	</div>


	<div class = "row">
		<div class="rowcol">
			<label>Total Revenue: <span id="edi-job-totrevenue">0.00</span></label>
			<label>Total Cost: <span id="edi-job-totcost">0.00</span></label>
			<label>Total Profit: <span id="edi-job-totprofit">0.00</span></label>
		</div>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = tab.data('panel');

		function refreshInvoiceTotal(){
			var r = $('#edi-job-invoice-grid .items tbody tr',panel);
			var totRevenue = 0;
			$(r).each(function(){
				var qty = $(this).find('input[name="JobLine[qty][]"]').val();
				var rate = $(this).find('input[name="JobLine[rate][]"]').val();
				qty = parseFloat(qty);
				rate = parseFloat(rate);
				if ( qty > 0 && rate > 0 ) {
					totRevenue += qty * rate;
				}
			});
			return totRevenue;
		}

		function addOneLine2Cost(data){
			var addBtn = $('#edi-job-cost-grid .add_btn',panel);
			$('#edi-job-cost-grid .items tbody td.empty',panel).parent().remove();
			var r = addBtn.parents('tr').clone();
			// $('.add_btn', r).remove();
			$('.add_btn',r).replaceWith('<a href="" class="delete_btn" title="Remove">Remove</a>');

			$('input', r).each(function(){
				var n = $(this).attr('name');
				// if ( !$(this).hasClass('hasDatepicker') ) {
				$(this).attr('name', n + '[]');
				//  }
			});
			$('select', r).each(function(){
				var n = $(this).attr('name');
				$(this).attr('name', n+'[]');
			});
			$('#edi-job-cost-grid .items tbody',panel).append(r);
			addBtn.parents('tr').find('input').val('');

			// set related data
			$('select[name="BillingLine[item_code][]"]',r).val(data.item_code);
			$('#edi-job-cost-grid_desc',r).val(data.desc);
			$('#edi-job-cost-grid_qty',r).val(data.qty);
			$('#edi-job-cost-grid_price',r).val(data.price);
			$('#edi-job-cost-grid_org_id',r).val(data.org_id);
			$('input.egacol_org_id',r).val(data.supplier_name);
			$('select[name="BillingLine[gst][]"]',r).val(data.gst);

			var amountElement = $('#edi-job-cost-grid_price',r).parent().next();
			var totalAmount = myApp.formatNumber(parseFloat(data.qty) * parseFloat(data.price),3, '.', ',');
			amountElement.html('<span class="edi-job-cost-amount">' + totalAmount +"</span>");
		}

		function refreshCostTotal(){
			var r = $('#edi-job-cost-grid .items tbody tr',panel);
			var totCost = 0;
			$(r).each(function(){
				var qty = $(this).find('input[name="BillingLine[qty][]"]').val();
				var rate = $(this).find('input[name="BillingLine[price][]"]').val();
				qty = parseFloat(qty);
				rate = parseFloat(rate);
				if ( qty > 0 && rate > 0 ) {
					totCost += qty * rate;
				}
			});
			return totCost;
		}

		function addOneLine2Invoice(data){
			var addBtn = $('#edi-job-invoice-grid .add_btn',panel);
			$('#edi-job-invoice-grid .items tbody td.empty',panel).parent().remove();
			var r = addBtn.parents('tr').clone();
			// $('.add_btn', r).remove();
			$('.add_btn',r).replaceWith('<a href="" class="delete_btn" title="Remove">Remove</a>');

			$('input', r).each(function(){
				var n = $(this).attr('name');
			   // if ( !$(this).hasClass('hasDatepicker') ) {
					$(this).attr('name', n + '[]');
			  //  }
			});
			$('select', r).each(function(){
				var n = $(this).attr('name');
				$(this).attr('name', n+'[]');
			});
			$('#edi-job-invoice-grid .items tbody',panel).append(r);
			addBtn.parents('tr').find('input').val('');

			// set related data
			$('select[name="JobLine[ccode][]"]',r).val(data.ccode);
			$('#edi-job-invoice-grid_desc',r).val(data.desc);
			$('#edi-job-invoice-grid_qty',r).val(data.qty);
			$('#edi-job-invoice-grid_rate',r).val(data.rate);
			$('select[name="JobLine[inv_gst][]"]',r).val(data.inv_gst);

			var amountElement = $('#edi-job-invoice-grid_rate',r).parent().next();
			var totalAmount = myApp.formatNumber(parseFloat(data.qty) * parseFloat(data.rate),3, '.', ',');
			amountElement.html('<span class="edi-job-inv-amount">' + totalAmount +"</span>");
		}

		function refreshTotal(){
			var totRevenue = refreshInvoiceTotal();
			var totCost = refreshCostTotal();
			var totProfit = totRevenue - totCost;
			$('#edi-job-totrevenue',panel).html(myApp.formatNumber(totRevenue,3, '.', ','));
			$('#edi-job-totcost',panel).html(myApp.formatNumber(totCost,3, '.', ','));
			$('#edi-job-totprofit',panel).html(myApp.formatNumber(totProfit,3, '.', ','));
		}

		tab.off('reload_tab').on('reload_tab', function(){
			var t = $('.ui-tabs', panel);
			t.tabs('load', t.tabs('option','active'));
		});

		$('#load-edi-template',panel).click(function(e){
			e.preventDefault();
			e.stopPropagation();
			var tid = $('#org-edi-template',panel).find(':selected').val();
			if ( tid <= 0 || tid === undefined ) {
				alert('please select one template!');
				$('#EdiJob_owner_id',panel).focus();
			} else {
				var data = { 'tid' : tid};
				$.ajax({
					type : 'POST',
					url : '<?php echo Yii::app()->createAbsoluteUrl("org/ajaxGetEdiTemplate") ;?>',
					data: data,
					dataType: 'json',
					success:function(resp){
						if ( resp.success == 1 ) {
							$('#edi-job-invoice-grid .items tbody',panel).empty();
							$('#edi-job-cost-grid .items tbody',panel).empty();
							var invoices = resp.data.invoice;
							for ( var i = 0 ; i < invoices.length; i++ ) {
								addOneLine2Invoice(invoices[i]);
							}
							var costs = resp.data.cost;
							for ( var i = 0 ; i < costs.length; i++ ) {
								addOneLine2Cost(costs[i]);
							}
							refreshTotal();

						} else {
							alert('Sorry, failed to get related template');
						}
					}
				});
			}
		});

		$('#auto-fill', panel).on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			var airline = $('#EdiAwbConsol_airline', panel).val();
			var flight = $('#EdiAwbConsol_flight', panel).val();
			var wmstask = '<?=@$_GET['wmstask'];?>';
			var weight = $('#jbv_gw', panel).val();
			var pod = $('#EdiAwbConsol_pod', panel).val();
			var pol = $('#EdiAwbConsol_pol', panel).val();
			var org_id = $('#EdiAwbConsol_owner_id', panel).val();
			var data = { 'airline': airline, 'flight' : flight, 'wmstask' : wmstask, 'weight': weight, 'pod': pod, 'pol': pol, 'org_id': org_id };
			$.ajax({
				type: 'POST',
				url: '<?php echo Yii::app()->createAbsoluteUrl("ediJob/ajaxAutofill");?>',
				data: data,
				dataType: 'json',
				success: function(resp) {
					if (resp.success == 1) {
						var costs = resp.data.cost;
						if (costs) {
						for (var i = 0; i < costs.length; i++) {
							var desc = null;
							$('input[name="BillingLine[desc][]"]', panel).each(function(v, k) {
								if ($(this).val() == costs[i].desc) {
									desc = $(this);
								}
							});
							if (desc) {
								qty = desc.parent().parent().find('#edi-job-cost-grid_qty');
								qty.val(costs[i].qty);
								price = desc.parent().parent().find('#edi-job-cost-grid_price');
								price.val(costs[i].price);

								var amountElement = price.parent().next();
								var totalAmount = myApp.formatNumber(parseFloat(costs[i].qty) * parseFloat(costs[i].price), 3, '.', ',');
								amountElement.html('<span class="edi-job-cost-amount">' + totalAmount + '</span>');
							} else {
								addOneLine2Cost(costs[i]);
							}
						}
						}
						var invoices = resp.data.invoice;
						if (invoices) {
						for (var i = 0; i < invoices.length; i++) {
							var desc = null;
							$('input[name="JobLine[desc][]"]', panel).each(function(v, k) {
								if ($(this).val() == invoices[i].desc) {
									desc = $(this);
								}
							});
							if (desc) {
								qty = desc.parent().parent().find('#edi-job-invoice-grid_qty');
								qty.val(invoices[i].qty);
								rate = desc.parent().parent().find('#edi-job-invoice-grid_rate');
								rate.val(invoices[i].rate);

								var amountElement = rate.parent().next();
								var totalAmount = myApp.formatNumber(parseFloat(invoices[i].qty) * parseFloat(invoices[i].rate), 3, '.', ',');
								amountElement.html('<span class="edi-job-invoice-amount">' + totalAmount + '</span>');
							} else {
								addOneLine2Invoice(invoices[i]);
							}
						}
						}
						refreshTotal();
					} else {
						if (resp.msg) {
							alert(resp.msg);
						} else {
							alert('Sorry, failed to auto fill');
						}
					}
				}
			});
		});

		$('#edi-job-invoice-grid',panel).on('keydown', 'input, select', function(e){
			if (e.keyCode == 13) {
				$(this).parent().parent().find('.add_btn').trigger('click');
				return false;
			}
		});
		$('#edi-job-invoice-grid .add_btn',panel).on('click', function(){
			$('#edi-job-invoice-grid .items tbody td.empty',panel).parent().remove();
			var r = $(this).parents('tr').clone();
			// $('.add_btn', r).remove();
			$('.add_btn',r).replaceWith('<a href="" class="delete_btn" title="Remove">Remove</a>');

			$('input', r).each(function(){
				var n = $(this).attr('name');
				$(this).attr('name', n+'[]');

			});
			$('select', r).each(function(){
				var n = $(this).attr('name');
				$(this).attr('name', n+'[]');
				$(this).val($('select[name="' + n + '"]').val());
			});

			$('#edi-job-invoice-grid .items tbody',panel).append(r);
			$(this).parents('tr').find('input').val('');
			$(this).parents('tr').find('select').val('');
			$(this).parents('tr').find('span').html('');
			refreshTotal();
			return false;
		});

		$('#edi-job-cost-grid',panel).on('keydown', 'input, select', function(e){
			if (e.keyCode == 13) {
				$(this).parent().parent().find('.add_btn').trigger('click');
				return false;
			}
		});
		$('#edi-job-cost-grid .add_btn',panel).on('click', function(){
			$('#edi-job-cost-grid .items tbody td.empty',panel).parent().remove();
			var r = $(this).parents('tr').clone();
			// $('.add_btn', r).remove();
			$('.add_btn',r).replaceWith('<a href="" class="delete_btn" title="Remove">Remove</a>');

			$('input', r).each(function(){
				var n = $(this).attr('name');
				$(this).attr('name', n+'[]');

			});
			$('select', r).each(function(){
				var n = $(this).attr('name');
				$(this).attr('name', n+'[]');
				$(this).val($('select[name="' + n + '"]').val());
			});

			$('#edi-job-cost-grid .items tbody',panel).append(r);
			$(this).parents('tr').find('input').val('');
			$(this).parents('tr').find('select').val('');
			$(this).parents('tr').find('span').html('');
			refreshTotal();
			return false;
		});

		$('#edi-job-invoice-grid',panel).on('click','.delete_btn', function(e){

			if ( confirm( ' Are you sure you want to delete the item?') ) {
				$(this).parent().parent().remove();
			}
			e.preventDefault();
			e.stopPropagation();
			return false;
		});

		$('#edi-job-cost-grid',panel).on('click','.delete_btn', function(e, obj){
			if ( (obj && obj.confirm == false) || confirm( ' Are you sure you want to delete the item?') ) {
				$(this).parent().parent().remove();
			}
			e.preventDefault();
			e.stopPropagation();
			if ($('#edi-job-cost-grid tbody',panel).find('tr').length == 0) {
				$('#edi-job-cost-grid tbody',panel).append('<tr><td colspan="8" class="empty"><span class="empty">No results found.</span></td></tr>');
			}
			return false;
		});

		$('#edi-job-invoice-grid',panel).on('input','.change-event', function(e){
			refreshTotal();
		});
		$('#edi-job-cost-grid',panel).on('input','.change-event', function(e){
			refreshTotal();
		});

		$('#edi-job-invoice-grid',panel).on('input','.change-inv-amount', function(e){
			// console.log('input qty or rate changed');
			var trElement = $(this).parent().parent();
			var qty = trElement.find('input[name="JobLine[qty][]"]').val();
			if (!qty) {
				var qty = trElement.find('input[name="JobLine[qty]"]').val();
			}
			if ( typeof qty == 'undefined' || qty == "") qty = 0;
			var amountElement = trElement.find('input[name="JobLine[rate][]"]');
			if (amountElement.length == 0) {
				var amountElement = trElement.find('input[name="JobLine[rate]"]');
			}
			var rate = amountElement.val();
			if ( typeof rate == 'undefined' || rate == "") rate = 0;
			amountElement = amountElement.parent().next();
			var totalAmount = myApp.formatNumber(parseFloat(qty) * parseFloat(rate),3, '.', ',');
			amountElement.html('<span class="edi-job-inv-amount">' + totalAmount +"</span>");
		});

		$('#edi-job-cost-grid',panel).on('input','.change-cost-amount', function(e){
			// console.log('input qty or rate changed');
			var trElement = $(this).parent().parent();
			var qty = trElement.find('input[name="BillingLine[qty][]"]').val();
			if (!qty) {
				var qty = trElement.find('input[name="BillingLine[qty]"]').val();
			}
			if ( typeof qty == 'undefined' || qty == "") qty = 0;
			var amountElement = trElement.find('input[name="BillingLine[price][]"]');
			if (amountElement.length == 0) {
				var amountElement = trElement.find('input[name="BillingLine[price]"]');
			}
			var rate = amountElement.val();
			if ( typeof rate == 'undefined' || rate == "") rate = 0;
			amountElement = amountElement.parent().next();
			var totalAmount = myApp.formatNumber(parseFloat(qty) * parseFloat(rate),3, '.', ',');
			amountElement.html('<span class="edi-job-cost-amount">' + totalAmount +"</span>");
		});

		$('form#edi-job-form', panel).on('success', function(e, r){
			$('input[type="submit"]',panel).replaceWith('<label>Created Successfully!</label>');
			$('form#edi-job-form', panel).append('<a href="ediJob/update/'+r.id+'" title="Update Edi Job" class="tab_link" id="edijob_create">EdiJob</a>');
			$('#edijob_create', panel).trigger('click');
			$('.tabClose', tab).trigger('click');
		});

		$('#EdiAwbConsol_owner_id',panel).on('change', function() {
			getOrgTemplate();
		});

		function getOrgTemplate(){
			var orgid = $('#EdiAwbConsol_owner_id',panel).val();
			if ( orgid <= 0 ) {
				alert('please select one owner!');
				$('#EdiAwbConsol_owner_id',panel).focus();
			} else {
				var data = { 'org' : orgid};
				$.ajax({
					type : 'POST',
					url : '<?php echo Yii::app()->createAbsoluteUrl("org/ajaxGetEdiTemplateList") ;?>',
					data: data,
					dataType: 'json',
					success:function(resp){
						if ( resp.success == 1 ) {
							$('#org-edi-template',panel).empty();
							if (resp.data.length == 0) {
								var item = '<option value="No selection" >No selection</option>';
								$('#org-edi-template',panel).append(item);
							}
							for ( var i = 0 ; i < resp.data.length; i++ ) {
								var item = '<option value="' + resp.data[i].id + '" >'+resp.data[i].name+'</option>';
								$('#org-edi-template',panel).append(item);
							}

						} else {
							alert('Sorry, failed to get related template')
						}
					}
				});
			}
		}

		// edijob create from wmstask
		if ($('#EdiAwbConsol_owner_id',panel).val()) {
			getOrgTemplate();
		}

		$('#clear_cost', panel).on('click', function() {
			if ( confirm( ' Are you sure you want to delete all items?') ) {
				$('#edi-job-cost-grid .delete_btn', panel).trigger('click', [{ confirm : false }]);
			}
		});

		$('#EdiAwbConsol_awb', panel).on('change', function() {
			var value = $(this).val();
			$.ajax({
				type: 'GET',
				dataType: 'json',
				data: { 'awb': value },
				url: '<?=Yii::app()->createUrl("ediJob/checkAwb")?>',
				success: function(r) {
					if (r.done == false) {
						myApp.alert(r.msg, false);
						$('#EdiAwbConsol_awb', panel).val('');
					}
				}
			});
		});

	});
</script>
