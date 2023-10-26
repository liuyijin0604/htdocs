<h1>Bank Statement Reconcile </h1>
<br/>
<div class="bank-statement-detail">

	<div style="display: -webkit-inline-box;">
		<div style="width: 188px;"></div>
		<?php if ($model->debits != 0) { ?><div style="width: 96px; text-align: right; padding-right: 8px;">Spent</div><?php } ?>
		<?php if ($model->credits != 0) { ?>
			<div style="width: 96px; text-align: right; padding-right: 8px;" >Received</div>
			<div style="width: 96px; text-align: right; padding-left: 30px;" >Diff / Rounding</div>
		<?php } ?>
	</div>

	<div class="statement no-shadow">
		<div class="info" style="min-height:20px;">
			<div class="details"><span style="float:left;"><span style="color:#000000; font-weight:bold;">Date:</span> <?php echo $model->created; ?></span></div>
			<?php if ($model->debits != 0) { ?><div class="amount"> <?php echo number_format($model->debits,2);?></div><?php } ?>
			<?php if ($model->credits != 0) { ?>
				<div class="amount received" ><?php echo number_format($model->credits,2);?></div>
				<div style="padding-left: 34px;"><?php echo CHtml::textField('Diff', '0.00', array('size' => 10, 'maxlength' => 10, 'id' => 'diff')); ?></div>
			<?php } ?>
		</div>
		<div><span style="width: 500px"><?php echo $model->desc; ?></span></div>
	</div>

</div>

<?php
 $totAmount = $model->debits;
if ( $totAmount <= 0 )  $totAmount = $model->credits;
?>

<div style="margin-top: 10px;">
	<h3>1. Find & select matching transactions</h3>
	<div class="form">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'statements-reconcile-form',
			'enableAjaxValidation' => false,
		)); ?>

		<div class="row">
			<div class="col" style="width: 80%;">
				<div class="row rowcol rowleft">
					<?php echo CHtml::label('Search by name or reference or amount','fors') ?>
					<?php if (!empty($model->org->name)) {
						$name = $model->org->name;
					} else {
						$name = explode(' ', trim(preg_replace('/(WITHDRAWAL ONLINE)|(\d+)|(PYMT)/', '', $model->desc)))[0];
					} ?>
					<?php echo CHtml::textField('name', $name, array('size'=>30,'maxlength'=>50,'id' => 'search-name')); ?>
				</div>
				<div class="row rowcol">
					<?php echo CHtml::checkbox('payment', false, array('id' => 'search-payment')), $this->t('With payment'); ?>
					<?php echo CHtml::submitButton($this->t('Go'), array('class' => 'search_btn')); ?>
					<a id="clear-link"  href="#" title="Clear search results"><div class="icon" style="background-position:-16px 0"></div> Clear search result</a>
				</div>
			</div>
		</div>

		<?php $this->endWidget(); ?>

		<div class="row" style="width: 100%; max-height: 150px; overflow-y: auto;">
			<div id="all-transactions" class="grid-view" >
			<table class="items">
				<thead>
				<tr>
					<th><input type="checkbox" name="trans_selected" id="trans_selected"></th>
					<th width="10%">Date</th>
					<th width="20%">Customer</th>
					<?php if ($model->credits != 0) { ?>
					<th width="20%">Invoice No.</th>
					<?php } else if ($model->debits != 0) { ?>
					<th width="20%">Billing No.</th>
					<?php } ?>
					<th width="20%">Reference</th>
					<th width="10%">Status</th>
					<?php if ($model->debits != 0) { ?>
					<th width="10%">Amount</th>
					<?php } else if ($model->credits != 0) { ?>
					<th width="10%">Amount</th>
					<?php } ?>
					<th width="5%">Split</th>
				</tr>
				</thead>
				<tbody id="body-transactions">
				</tbody>
			</table>
			</div>
		</div>
		<div class="pager">
			<button id="prev-page-btn">< Previous</button>
			<button id="next-page-btn">Next ></button>
		</div>
	</div>
</div>


<div style="margin-top: 10px;">
	<h3 style="float:left;">2. Selected transactions</h3>
	<!-- <a class="jqm_link" id="new-overpayment-link"  href="<?=$this->createUrl('bank/createOverpayment', ['fid' => $model->id]);?>" data-url="<?=$this->createUrl('bank/createOverpayment');?>" title="New Payment" style="float:left;"><div class="icon" style="background-position:-16px 0"></div> New Payment</a> -->
	<div class="form">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'statements-reconcile-selected-form',
			'enableAjaxValidation'=>false,
		)); ?>
		<?php $this->endWidget(); ?>

		<div class="row" style="width: 100%; max-height: 100px; min-height: 100px; overflow-y: auto;">
		<div id="selected-transactions" class="grid-view" >
			<table class="items">
				 <tbody id="body-selected-transactions">
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>


<div style="margin-top: 10px; float: left;">
	<h3 style="float:left;">3a. Confirm</h3>
	<!-- <h3>3. The sum of your selected transactions must match the money received. Make adjustments, as needed.</h3> -->
	<div class="form">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'statements-reconcile-confirm-form',
			'enableAjaxValidation'=>false,
		)); ?>

	<div class="row">
		<div class="col" style="width: 200px;">
			<div id="reconcile-sub-total" style="line-height: 38px;"><span style="width: 96px;"> Subtotal</span> <span id="sub-total-amount" style="width: 96px;text-align: right;font-weight: bold;">0.00 </span> AUD </div>
		</div>
	</div>

	<div class="row">
		<div style="line-height: 28px;">
			<span>Total amount: </span> <span style="background-color: #000000;padding:4px;margin:4px;color:#ffffff;font-weight: bold;" id="total-amount"> <?php echo number_format($totAmount,2);?> AUD </span>
			<span style="color:#ff0000;" id="out-of-value-div" > Total is out by: <span><?php echo $totAmount; ?></span></span>
		</div>
	</div>

	<div class="row">
		<div class="col">
			<div class="row rowcol">
				<?php echo CHtml::submitButton($this->t('Reconcile'), array('id' => 'reconcile-confirm-btn', 'disabled' => 'disabled')); ?>
			</div>
			<div class="row rowcol" id="reconcile-result"></div>
		</div>
	</div>
	<?php $this->endWidget(); ?>

	</div>
</div>


<div style="margin-top: 10px; float: left; margin-left: 200px;">
	<h3 style="float:left;">3b. Payment</h3>

	<div class="form">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'statements-reconcile-payment-form',
			'enableAjaxValidation'=>false,
		)); ?>
		<?php $this->endWidget(); ?>
		<div class="row">
			<?php echo $form->labelEx($model,'org_id'), ' ', $form->hiddenField($model,'org_id');
				$acname = empty($_GET["tabid"])? 'org_ac' : $_GET["tabid"].'_org_ac';
				$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => $acname,
					'sourceUrl' => array('org/ownerSuggest'),
					'value' => empty($model->customer)? '' : $model->customer->name,
					'options' => array(
							'showAnim' => 'fold',
							'minLength' => 2,
							'delay' => 200,
							'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
							'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
					),
					'htmlOptions' => array(
						'class' => 'required',
						'size' => '30',
					),
			));
			?>
		</div>
		<?php echo CHtml::submitButton($this->t('Reconcile'), array('id' => 'reconcile-confirm-btn2')); ?>
	</div>

</div>

<script type="text/javascript">
	var mytab;
	var totalDebits,totalCredits;
	var totalSelectedAmount = 0;
	var totalAdjustment = 0;
	var diff = 0;

	var invoices = <?php echo json_encode($invoices); ?>;
	var page = <?=empty($page) ? 0 : $page?>;

	if ((Object.keys(invoices).length - page * 20) < 20) {
		$('#next-page-btn').hide();
	} else {
		$('#next-page-btn').show();
	}
	if (page == 0) {
		$('#prev-page-btn').hide();
	} else {
		$('#prev-page-btn').show();
	}

	function addOneInvoice(inv){
		var panel = $(mytab).data('panel');
		$('#body-transactions',panel).children('tr').each(function(e){
			var id = $(this).data('id');
			if ( id == inv.index ) {
				return false;
			}
		});

		var existingNum = $('#body-transactions tr',panel).length;
		var trClassType = 'odd';
		if ( existingNum % 2 == 0 ) trClassType = 'even';
		var invElements = '<tr class="' + trClassType + ' one-transaction father" data-id="' + inv.index + '">';
		invElements +=  '<td><input type="checkbox" name="selected_inv" style="margin-left: 20px; margin-right: 20px;"></td>';
		invElements +=  '<td>' + inv.date + '</td>';
		invElements +=  '<td>' + inv.cust + '</td>';
		invElements +=  '<td>' + inv.no + '</td>';
		invElements +=  '<td>' + inv.ref + '</td>';
		invElements += '<td>' + inv.status + '</td>';
		if (inv.model == 'Invoice' || inv.model == 'Payment') {
			invElements +=  '<td>' + inv.amt + '</td>';
		} else if (inv.model == 'Billing' || inv.model == 'PaymentB') {
			invElements +=  '<td>' + inv.amt + '</td>';
		}
		if ( inv.model == 'Invoice' || inv.model == 'Billing' ) {
			invElements += '<td width="40px"><input type="number" class="split-inv-input" name="split_inv" value="' + inv.split + '"></td>';
		} else {
			invElements +=  '<td></td>';
		}
		var obj = $(invElements);
		$('#body-transactions',panel).append(obj.fadeIn());

	}

	function addOneInvoice2SelectedGrid(inv){
		var panel = $(mytab).data('panel');
		var selectedBody =  $('#body-selected-transactions',panel);
		var existing = false;
		selectedBody.children('tr').each(function(e){
			var id = $(this).data('id');
			if ( id == inv.index ) {
				existing = true;
				return false;
			}
		});
		if ( existing ) return false;

		var existingNum = $('#body-selected-transactions tr',panel).length;
		var trClassType = 'odd';
		if ( existingNum % 2 == 0 ) trClassType = 'even';
		var invElements = '<tr class="' + trClassType + ' one-selected-transaction father" data-id="' + inv.index + '" data-amount="' + inv.amt + '">';
		invElements +=  '<td><input type="checkbox" name="final_selected_inv" checked="checked"></td>';
		invElements +=  '<td>' + inv.date + '</td>';
		invElements +=  '<td>' + inv.cust + '</td>';
		invElements +=  '<td>' + inv.no + '</td>';
		if ( inv.split > 0 ) {
			invElements +=  '<td class="selected-inv-amount">' + inv.split + '</td>';
		} else {
			invElements +=  '<td class="selected-inv-amount">' + inv.amt + '</td>';
		}
		var obj = $(invElements);
		selectedBody.append(obj.fadeIn());
		refreshMatchedAmount();
	}

	function oneInvSelected(invId){
		var inv = invoices[invId];
		if ( inv.index == invId ) {
			addOneInvoice2SelectedGrid(inv);
		}
	}

	function unSelectOneInv(invId){
			var inv = invoices[invId];
			if ( inv.index == invId ) {
				var panel = $(mytab).data('panel');
				var selectedBody =  $('#body-selected-transactions',panel);
				selectedBody.children('tr').each(function(e){
					var id = $(this).data('id');
					if ( id == inv.index ) {
					   $(this).remove();
						if ( inv.split > 0 ) {
							invAmount = inv.split;
						} else {
							invAmount = inv.amt;
						}
						refreshMatchedAmount();
					}
				});
			}
	}

	function getSelectedAmount(){
		var totalAmount = 0;
		var panel = $(mytab).data('panel');
		$('#body-selected-transactions tr',panel).each(function(e){
			var selectedBox = $(this).find('input[name="final_selected_inv"]');
			if ( selectedBox.prop('checked') ) {
				var invId = $(this).data('id');
				var inv = invoices[invId];
				if (inv.index == invId) {
					if ( inv.split > 0 ) {
						totalAmount += parseFloat(inv.split);
					} else {
						totalAmount += parseFloat(inv.amt);
					}
				}
			}
		});
		return totalAmount;
	}

	function refreshMatchedAmount(){
		var panel = $(mytab).data('panel');

		totalSelectedAmount = getSelectedAmount();
		totalSelectedAmount = parseFloat(totalSelectedAmount.toFixed(2));

		$('#sub-total-amount',panel).html(totalSelectedAmount);
		var allAmount = totalAdjustment + totalSelectedAmount;
		allAmount = parseFloat(allAmount.toFixed(2));
		var minusValue = totalCredits - allAmount - diff;
		minusValue = minusValue.toFixed(2);
		minusValue = parseFloat(minusValue);
		$('#out-of-value-div',panel).find('span').html(minusValue);
		$('#out-of-value-div',panel).show();
		$('#reconcile-confirm-btn',panel).prop('disabled',false);
		if ( allAmount == 0 || (allAmount > (totalCredits - diff + 1) && allAmount > (totalCredits - diff) * 1.0005) ) {
			$('#reconcile-confirm-btn',panel).prop('disabled',true);
		}
	}

	$(function(){

		mytab = '#jqmw_<?=$_GET["tabid"];?>';
		var panel = $(mytab).data('panel');
		totalCredits = <?php echo json_encode($totAmount); ?>;
		//totalCredits = totalCredits.toFixed(2);
		totalCredits = parseFloat(totalCredits);
		// get all related invoices
		for (var key in invoices) {
			if (invoices.hasOwnProperty(key)) {
				addOneInvoice(invoices[key]);
			}
		}

		$(mytab).bind('onOpen', function(){
			// search again many new overpayment creatred
			$('.search_btn',panel).trigger('click');
		});

		// when one trasaction has been clicked
		// switch selected check box between selected or un-selected
		$('#all-transactions',panel).on('click','.one-transaction',function(e){
			if (e.target.name == 'selected_inv' || e.target.name == 'split_inv') return;
			var selectedInvCheckbox = $(this).find('input[name="selected_inv"]');
			var invId = $(this).data('id');
			if ( selectedInvCheckbox.prop('checked') ) {
				unSelectOneInv(invId);
				selectedInvCheckbox.prop('checked',false);
			} else {
				oneInvSelected(invId);
				selectedInvCheckbox.prop('checked',true);
			}
		});

		$('#all-transactions',panel).on('click','input[name="selected_inv"]',function(e){
			var selectedInvCheckbox = $(this);
			var invId = $(this).parent().parent().data('id');
			if ( !selectedInvCheckbox.prop('checked') ) {
				unSelectOneInv(invId);
			} else {
				oneInvSelected(invId);
			}
		});

		$('#selected-transactions',panel).on('click','.one-selected-transaction',function(e){
			if (e.target.name == 'final_selected_inv') return;
			var selectedInvCheckbox = $(this).find('input[name="final_selected_inv"]');

			if ( selectedInvCheckbox.prop('checked') ) {
			   // unSelectOneInv(invId);
				selectedInvCheckbox.prop('checked',false);
			} else {
			   // oneInvSelected(invId);
				selectedInvCheckbox.prop('checked',true);
			}
			refreshMatchedAmount();
		});

		$('#selected-transactions',panel).on('click','input[name="final_selected_inv"]',function(e){
			var selectedInvCheckbox = $(this);
			refreshMatchedAmount();
		});

		$('.split-inv-input',panel).on('input',function(e){
			var split = parseFloat($(this).val());
			if ( isNaN(split) ) split = 0;
			if ( split < 0 ) {
				$(this).val(0);
				split = 0;
			}
			var invId = $(this).parent().parent().data('id');
			if (invoices[invId].index == invId) {
				invoices[invId].split = split;
				// update selected invoice real amount
				var realAmount = split;
				if ( split <= 0 ) {
					realAmount = invoices[invId].amt;
				}
				$('#body-selected-transactions tr',panel).each(function(e){
						var destId = $(this).data('id');
						if ( destId == invId ) {
							realAmount = parseFloat(realAmount.toFixed(2));
							$(this).find('.selected-inv-amount').html(realAmount);
						}
				});
			}
			refreshMatchedAmount();
		});

		$('#clear-link',panel).click(function(e){
			e.preventDefault();
			$('#body-transactions',panel).html('');
			for (var key in invoices) {
				addOneInvoice(invoices[key]);
			}
		});

		$('.search_btn',panel).click(function(e){
			e.preventDefault();
			var searchName = $('#search-name').val();
			var searchAmount = $('#search-amount').val();
			var searchPayment = $('#search-payment').prop('checked');
			//  if ( searchAmount.length <= 0 && searchName.length <= 0 ) {
			//      alert('Please type searching content');
			//      return;
			//  }
			var data = {'sname' : searchName, 'samt' : searchAmount, 'payment' : searchPayment};
			$.ajax({
				type: 'POST',
				url: '<?php echo Yii::app()->createAbsoluteUrl("bank/ajaxSearchTransaction", array("fid" => $model->id)) ;?>' + '&page=' + page,
				data: data,
				dataType: 'json',
				success: function (resp) {
					if (resp.success == 1) {

						// clear all old data
						$('#body-transactions',panel).html('');

						// push all search results into local array
						for (var key in resp.data) {
							if (resp.data.hasOwnProperty(key)) {
								if ( typeof invoices[key] == 'undefined' ) {
									invoices[key] =  resp.data[key];
								}
							}
							addOneInvoice(invoices[key]);
						}

						if (resp.data.length === 0) {
							invoices = [];
						}

						if (page == 0) {
							$('#prev-page-btn').hide();
						} else {
							$('#prev-page-btn').show();
						}
						if ((Object.keys(invoices).length - page * 20) < 20) {
							$('#next-page-btn').hide();
						} else {
							$('#next-page-btn').show();
						}
						
						$('#all-transactions',panel).off('click').on('click','.one-transaction',function(e){
							if (e.target.name == 'selected_inv' || e.target.name == 'split_inv') return;
							var selectedInvCheckbox = $(this).find('input[name="selected_inv"]');
							var invId = $(this).data('id');
							if ( selectedInvCheckbox.prop('checked') ) {
								unSelectOneInv(invId);
								selectedInvCheckbox.prop('checked',false);
							} else {
								oneInvSelected(invId);
								selectedInvCheckbox.prop('checked',true);
							}
						});

						$('#all-transactions',panel).off('click').on('click','input[name="selected_inv"]',function(e){
							var selectedInvCheckbox = $(this);
							var invId = $(this).parent().parent().data('id');
							if ( !selectedInvCheckbox.prop('checked') ) {
								unSelectOneInv(invId);
							} else {
								oneInvSelected(invId);
							}
						});

						$('#selected-transactions',panel).off('click').on('click','.one-selected-transaction',function(e){
							if (e.target.name == 'final_selected_inv') return;
							var selectedInvCheckbox = $(this).find('input[name="final_selected_inv"]');

							if ( selectedInvCheckbox.prop('checked') ) {
							   // unSelectOneInv(invId);
								selectedInvCheckbox.prop('checked',false);
							} else {
							   // oneInvSelected(invId);
								selectedInvCheckbox.prop('checked',true);
							}
							refreshMatchedAmount();
						});

						$('#selected-transactions',panel).off('click').on('click','input[name="final_selected_inv"]',function(e){
							var selectedInvCheckbox = $(this);
							refreshMatchedAmount();
						});

						$('#trans_selected').click(function() {
							if ($(this).prop('checked')) {
								$('#all-transactions', panel).find('input[name="selected_inv"]').each(function() {
									var selectedInvCheckbox = $(this);
									var invId = $(this).parent().parent().data('id');
									oneInvSelected(invId);
									selectedInvCheckbox.prop('checked', true);
								});
							} else {
								$('#all-transactions', panel).find('input[name="selected_inv"]').each(function() {
									var selectedInvCheckbox = $(this);
									var invId = $(this).parent().parent().data('id');
									unSelectOneInv(invId);
									selectedInvCheckbox.prop('checked', false);
								});
							}
						});

						$('.split-inv-input',panel).off('input').on('input',function(e){
							var split = parseFloat($(this).val());
							if ( isNaN(split) ) split = 0;
							if ( split < 0 ) {
								$(this).val(0);
								split = 0;
							}
							var invId = $(this).parent().parent().data('id');
							if (invoices[invId].index == invId) {
								invoices[invId].split = split;
								// update selected invoice real amount
								var realAmount = split;
								if ( split <= 0 ) {
									realAmount = invoices[invId].amt;
								}
								$('#body-selected-transactions tr',panel).each(function(e){
										var destId = $(this).data('id');
										if ( destId == invId ) {
											realAmount = parseFloat(realAmount.toFixed(2));
											$(this).find('.selected-inv-amount').html(realAmount);
										}
								});
							}
							refreshMatchedAmount();
						});
					} else {
					}
					$('#trans_selected').prop('checked', false);
				}
			});
		});

		$('#trans_selected').click(function() {
			if ($(this).prop('checked')) {
				$('#all-transactions', panel).find('input[name="selected_inv"]').each(function() {
					var selectedInvCheckbox = $(this);
					var invId = $(this).parent().parent().data('id');
					oneInvSelected(invId);
					selectedInvCheckbox.prop('checked', true);
				});
			} else {
				$('#all-transactions', panel).find('input[name="selected_inv"]').each(function() {
					var selectedInvCheckbox = $(this);
					var invId = $(this).parent().parent().data('id');
					unSelectOneInv(invId);
					selectedInvCheckbox.prop('checked', false);
				});
			}
		});

		$('#prev-page-btn', panel).click(function(e) {
			page --;
			page = Math.max(page, 0);
			$('.search_btn', panel).trigger('click');
		});

		$('#next-page-btn', panel).click(function(e) {
			page ++;
			$('.search_btn', panel).trigger('click');
		});

		$('#diff', panel).on('keyup', function() {
			if (isNaN($(this).val())) {
				diff = 0;
			} else if (diff == '-') {
				diff = '-';
			} else {
				diff = Number($(this).val());
				$('#total-amount', panel).html(parseFloat(totalCredits - diff).toFixed(2) + ' AUD');
				$('#out-of-value-div span', panel).html(parseFloat(totalCredits - diff - totalSelectedAmount).toFixed(2));
			}
		});


		$('#reconcile-confirm-btn',panel).click(function(e){
			$(this).prop('disabled',true);
			e.preventDefault();

			var bsid = <?php echo json_encode($model->id); ?>;

			//  get all selected transactions
			var selectedTransactionns = {};
			$('#body-selected-transactions tr',panel).each(function(e){
				var tId = $(this).data('id');
				var selectedBox = $(this).find('input[name="final_selected_inv"]');
				if ( selectedBox.prop('checked') ) {
					selectedTransactionns[tId] = invoices[tId];
				}
			});

			$('#reconcile-result',panel).html('');
			// $('#reconcile-confirm-btn',panel).prop('disabled',true);
			var data = {'bsid' : bsid , 'adj_amt': totalAdjustment ,'sts' : selectedTransactionns, 'diff' : diff};
			$.ajax({
				type: 'POST',
				url: '<?php echo Yii::app()->createAbsoluteUrl("bank/ajaxStatementReconcile") ;?>',
				data: data,
				dataType: 'json',
				success: function (resp) {
					if (resp.success == 1) {
						$('#reconcile-result',panel).html('<span style="color:green"> Reconciled successfully!</span>');
						setTimeout(function() {
							$('.popCancel', panel).trigger('click');
							$('#bank-unrec-transactions-grid').yiiGridView('update');
							$('#bank-all-transactions-grid').yiiGridView('update');

							resp.balance = resp.balance ? resp.balance : 0;
							resp.reconciled = resp.reconciled ? resp.reconciled : 0;
							resp.unreconciled = resp.unreconciled ? resp.unreconciled : 0;
							$('#statementBalance').text(resp.balance.toFixed(2));
							$('#reconciledAmount').text(resp.reconciled.toFixed(2));
							$('#unreconciledAmount').text(resp.unreconciled.toFixed(2));
						}, 1000);
					} else {
						$('#reconcile-result',panel).html(resp.msg);
						// $('#reconcile-confirm-btn',panel).prop('disabled',false);
					}
				}
			});
		});


		$('#reconcile-confirm-btn2',panel).click(function(e){
			$(this).prop('disabled',true);
			e.preventDefault();

			var bsid = <?php echo json_encode($model->id); ?>;
			var org = $('#BankStatement_org_id').val();

			$('#reconcile-result',panel).html('');
			// $('#reconcile-confirm-btn',panel).prop('disabled',true);
			var data = {'bsid' : bsid, 'org' : org};
			$.ajax({
				type: 'POST',
				url: '<?php echo Yii::app()->createAbsoluteUrl("bank/ajaxStatementReconcile2") ;?>',
				data: data,
				dataType: 'json',
				success: function (resp) {
					if (resp.success == 1) {
						$('#reconcile-result',panel).html('<span style="color:green"> Reconciled successfully!</span>');
						setTimeout(function() {
							$('.popCancel', panel).trigger('click');
							$('#bank-unrec-transactions-grid').yiiGridView('update');
							$('#bank-all-transactions-grid').yiiGridView('update');

							resp.balance = resp.balance ? resp.balance : 0;
							resp.reconciled = resp.reconciled ? resp.reconciled : 0;
							resp.unreconciled = resp.unreconciled ? resp.unreconciled : 0;
							$('#statementBalance').text(resp.balance.toFixed(2));
							$('#reconciledAmount').text(resp.reconciled.toFixed(2));
							$('#unreconciledAmount').text(resp.unreconciled.toFixed(2));
						}, 1000);
					} else {
						myApp.alert(resp.msg, false);
						$('#reconcile-confirm-btn2', panel).prop('disabled', false);
					}
				}
			});
		});

	});
</script>
 