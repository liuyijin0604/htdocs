<div style="position: absolute; right: 20px;">
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('ediJob/securityDec', array('id'=>$model->id))?>" class="jqm_link">Secuirty Dec.</a></li>
	</ul>
</div>

<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-2"><div style="background-position:-48px -688px" class="icon"></div> CT Cartage</a>
<?php
$awbModel = EdiAwbConsol::model()->find('awb = :awb',[':awb' => $model->awb]);
if ( empty($awbModel) || empty($model->awb) ) {
	$awbModel = new EdiAwbConsol();
	$awbModel->owner_id = $model->owner_id;
}
?>
<a href="<?=$this->createURL('ediAwbConsol/update', ['id' => $awbModel->id]);?>" class="tab_link" title="<?=$awbModel->awb;?>"><div style="background-position:-48px -688px" class="icon"></div> Open Consol.</a>
<div id="<?=$_GET["tabid"];?>-dropdown-2" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<?php if (!empty($model->wmstasks)) { ?>
			<?php foreach ($model->wmstasks as $task) { ?>
				<?php $cc = ContainerCartage::model()->find('task_id = :task_id', [':task_id' => $task->id]);
					if (empty($cc)) { ?>
					<li><a href="<?=$this->createUrl('containerCartage/create', array('org_id' => $task->job->org_id, 'task_id' => $task->id))?>" title="New Container Cartage" class="tab_link"> <?=$task->getNo()?></a></li>
					<?php } else { ?>
					<li><a href="<?=$this->createUrl('containerCartage/update', array('id' => $cc->id))?>" title="Update Container Cartage" class="tab_link"> <?=$task->getNo()?></a></li>
					<?php } ?>
			<?php } ?>
		<?php } else { ?>
			<li>Please link task</li>
		<?php } ?>
	</ul>
</div>
</div>

<h2>AWB Editor</h2>
<div id="edi-awb-edit">

<?php
$this->renderPartial('//edi/tab_overview',array('model' => $awbModel));
?>
 </div>

<br/>
<h2 style="margin-bottom: 0">Job Details</h2>

<div class="form">
	<div class="row">
		<?php echo CHtml::label('Template','for_basic_tempate'); ?>
		<?php echo CHtml::dropDownList('org-edi-template', '0',array('0' => 'Select One')); ?>
		<?php echo CHtml::button('Load Template', ['id' => 'load-edi-template']); ?>
	</div>
</div>

<?php if (!empty($model->wmstasks)) { ?>
	<div class="form">
		<div class="row rowcol">
			<?php echo CHtml::label('Wms Task', 'wmstaks'); ?>
			<?php echo $model->getWmsTask(); ?>
		</div>
	</div>
	<br />
<?php } ?>

<?php if (!empty($model->mdata['cnl_order'])) { ?>
	<div class="form">
		<div class="row rowcol">
			<?php echo CHtml::label('Cainiao Order', 'cnlorder'); ?>
			<?php
				$cnlorder = CnlOrder::model()->findByPk($model->mdata['cnl_order']);
				echo '<a class="tab_link" href="/cnlOrder/update/' . $cnlorder->id . '" title="' . $cnlorder->orderCode . '">' . $cnlorder->orderCode . '<div style="background-position: -176px -544px" class="icon"></div></a>';
			?>
		</div>
	</div>
	<br />
<?php } ?>

<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'edi-job-overview-update-cost-form',
		'enableAjaxValidation'=>false,
	));
	?>
	<div class="row">
		<?php echo CHtml::label('Weight Gross','for-job-ov'); ?>
		<?php echo CHtml::textField('jbv_gw', floatval(@$model->mdata['jbv_gw']),['size' => '12']); ?>
		<?php echo CHtml::button('Auto Fill', ['id' => 'auto-fill']); ?>
		<!-- <?php echo CHtml::button('Update Cost',['id' => 'btn-update-cost']); ?> -->
		<?php echo CHtml::link('①Air Line Quotes Check',$this->createUrl('route/admin'),['class' => 'tab_link','title' => 'Air Line Quotes Check']); ?>&nbsp;&nbsp;
		<?php echo CHtml::link('②Terminal Rate Check',$this->createUrl('ediJob/terminalRate'),['class' => 'jqm_link','title' => 'Terminal Rate Check']); ?>&nbsp;&nbsp;
		<?php echo CHtml::link('③Invoice Rate Check',$this->createUrl('ediJob/invoiceRate', ['id' => $model->owner->id]),['class' => 'jqm_link','data-win-class' => 'XL','title' => 'Invoice Rate Check', 'id' => 'invoice_rate']); ?>&nbsp;&nbsp;&nbsp;
	</div>
	<div class="row rowcol rowleft">
		<?php echo CHtml::label('HC40', 'hc40'); ?>
		<?php echo CHtml::textField('hc40', @$model->mdata['hc40'], ['size' => '12']); ?>
	</div>
	<div class="row rowcol">
		<?php echo CHtml::label('HC20', 'hc20'); ?>
		<?php echo CHtml::textField('hc20', @$model->mdata['hc20'], ['size' => '12']); ?>
	</div>
	<div class="row rowcol">
		<?php echo CHtml::label('GP40', 'gp40'); ?>
		<?php echo CHtml::textField('gp40', @$model->mdata['gp40'], ['size' => '12']); ?>
	</div>
	<div class="row rowcol">
		<?php echo CHtml::label('GP20', 'gp20'); ?>
		<?php echo CHtml::textField('gp20', @$model->mdata['gp20'], ['size' => '12']); ?>
	</div>
	<div class="row rowcol">
		<?php echo CHtml::label('换板数', 'plt_change'); ?>
		<?php echo CHtml::textField('plt_change', @$model->mdata['plt_change'], ['size' => '12']); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Plts', 'plt'); ?>
		<?php echo CHtml::textField('plt', $model->getWmsTaskPlt(), ['size' => '12']); ?>
	</div>
	<div class="row rowcol">
		<?php echo CHtml::label('Ref', 'ref'); ?>
		<?php echo CHtml::textField('ref', @$model->mdata['ref'], ['size' => '12']); ?>
		<?php echo CHtml::button('Save', ['id' => 'plt-save']); ?>
	</div>
	<div class="row" id="update-cost-result"></div>
	<div class="row" id="air-cost-rate-set" style="display:none">
		<?php echo CHtml::label('Current Air Flight POL & POD related cost rate','for-job-ov-rate'); ?>
		<?php echo CHtml::textField('jbv_gw_rate','0',['size' => '12']) . ' / pk'; ?>
		<?php echo CHtml::button('Set Cost Rate',['id' => 'btn-update-cost-rate']); ?>
	</div>

	<?php $this->endWidget(); ?>
</div><!-- form -->


<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'edi-job-overview-form',
	'enableAjaxValidation'=>false,
));
?>
	<?php echo $form->errorSummary($model); ?>


	<div class="row rowcol">
		<?php echo $form->labelEx($model,'dpmt'); ?>
		<?php echo $form->dropDownList($model, 'dpmt', Job::$dpmts, array('prompt'=>$this->t('Select One'))); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php echo $form->textField($model,'created', ['size' => '12', 'id' => 'created_'.$_GET["tabid"], 'readonly' => true]); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'due'); ?>
		<?php echo $form->textField($model,'due', ['size' => '12', 'id' => 'due_'.$_GET["tabid"], 'readonly' => true]); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'currency'); ?>
		<?php echo $form->dropDownList($model, 'currency', $this->t(Invoice::$currencies)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'inv_type'); ?>
		<?php echo $form->dropDownList($model, 'inv_type', $this->t(EdiJob::$inv_types)); ?>
	</div>

	<div class="row rowcol" style="position: relative; right: 20px; margin-left: 30px;">
		<?php
		if ( !empty($model->invoice) ) {
			echo '<a href="'.Yii::app()->createUrl("invoice/print", ["id" => $model->invoice[count($model->invoice)-1]->id]).'" target="_blank">'.$model->invoice[count($model->invoice)-1]->no.'</a> &nbsp; ';
			echo  CHtml::button('Update Invoice',['id' => 'btn-create-invoice']);
		} else {
			echo CHtml::button('Create Invoice', ['id' => 'btn-create-invoice']);
		}
		?>
	</div>

	<?php
	echo '<div class="row" id="rev_err">';
	if (!empty($model->mdata['rev_err'])) {
		foreach ($model->mdata['rev_err'] as $err) {
			echo '<span style="color:red">' . $err . '</span><br />';
		}
	}
	echo '</div>';
	?>
	<?php
	if (!empty($model->owner) && $model->owner->overCreditLimit()) {
		echo '<div class="row" id="org_credit">';
		echo 'Client ' . $model->owner->name . ' credit limit exceeded, overdue amount is <span style="color:red">' . (isset($model->owner->extra['creditlimit'])?floatval($model->owner->extra['creditlimit']):0)*$model->owner->getCurrentCreditOfLimit() . 'AUD</span> and credit usage is <span style="color:red">' . $model->owner->getCurrentCreditOfLimit()*100 . '%</span>';
		echo '</div>';
	}
	?>

	<br/>
	<h2>Invoice Details</h2>
	<div class="row" style="width: 80%">
		<?php
		$il = new JobLine('search');
		$il->unsetAttributes();
		$il->job_id = empty($model->id)? 0 : $model->id;

		$this->widget('application.extensions.editablegrid.CEditableGridView', array(
			'id' => $_GET["tabid"].'-update-edi-job-invoice-grid',
			'cssFile' => false,
			'dataProvider'=> $il->search(),
			'formUrl' => $this->createUrl('ediJob/linesInvoiceGrid', array('id'=>empty($model->id)? 0 : $model->id)),
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
					'value' => '$data->getCCodeDesc()' ,
					'filter'=> EdiJob::getChargeItemTypes()),
				array('header' => 'Description','name' => 'desc', 'class' => 'CEditableColumn'),
				array('header' => 'Qty/Kg','name' => 'qty', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-inv-amount']),
				array('header' => 'Rate','name' => 'rate', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-inv-amount']),
				array('header' => 'Amount','name' => 'invAmount','value'=> '$data->getInvAmount()','htmlOptions' => ['class' => 'show-inv-amount']),
				array('header' => 'GST', 'name' => 'inv_gst','class' => 'CEditableColumn', 'value' => '$data->getTaxType()','type' => 'list',
					'filter'=> Invoice::$InvoiceRevenueTaxRate ),
				array('class'=>'CEditableButtonColumn',
					'template' => '{edit} {cancel} {save} {delete}',
					'buttons'=>array
					(
						'delete' => array(
							'imageUrl' => false,
							'url' => 'Yii::app()->createUrl("ediJob/deleteInvoiceGridLine", ["id" => $data->id])',
							'options' => array('class' => 'delete_btn'),
						),
					),
				),
			),
		));
		?>
	</div>

	<h2>Cost Details</h2>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'edijob-switch-billingline-form',
		'enableAjaxValidation'=>false,
	)); ?>
	<!-- <div class="row buttons">
		<?php echo CHtml::button('Switch', ['id' => 'btn-switch-billingline']); ?>
	</div> -->
	<div class="row" style="width: 80%">
		<?php
		$il = new BillingLine('search');
		$il->unsetAttributes();
		$il->billing_ref = $model->no;

		$currency = 'AUD';
		$il_dp = $il->search();
		$il_data = $il_dp->getData();

		$this->widget('application.extensions.editablegrid.CEditableGridView', array(
			'id' => $_GET["tabid"].'-update-edi-job-cost-grid',
			'cssFile' => false,
			'selectableRows' => 2,
			'dataProvider'=> $il->search(),
			'formUrl' => $this->createUrl('ediJob/linesCostGrid', array('id'=>empty($model->id)? 0 : $model->id)),
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
				array('id' => 'selectedItems', 'class' => 'CCheckBoxColumn'),
				array('header' => 'Type','name' => 'item_code','class' => 'CEditableColumn','type' => 'list' ,
					'value' => '$data->getCCodeDesc()' ,
					'filter'=> EdiJob::getChargeItemTypes('cost')),

				array('header' => 'Description','name' => 'desc', 'class' => 'CEditableColumn'),
				array('header' => 'Qty/Kg','name' => 'qty', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-cost-amount']),
				array('header' => 'Rate','name' => 'price', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-cost-amount', 'style' => (!empty($aflines) ? 'color:red;' : '')], 'htmlOptions' => ['style' => (!empty($aflines) ? 'color:red;' : '')]),
				array('header' => 'Accrual Amount','name' => 'costAmount','value'=> '$data->getCostAmount()','htmlOptions' => ['class' => 'show-cost-amount'], 'footer' => $currency . ' ' . AppHelper::money_format("%i", $il->getTotal($il_data,'costAmount'))),
				array('header' => 'Actual','name' => 'actualAmount','value'=> '$data->actual_amount','htmlOptions' => ['class' => 'show-actual-amount'], 'footer' => $currency . ' ' . AppHelper::money_format("%i", $il->getTotal($il_data,'actualAmount'))),
				array('header' => 'Currency','name' => 'currency','value'=> '$data->getCurrency()','class' => 'CEditableColumn', 'type' => 'list', 'filter' => Invoice::$currencies),
				array('header' => 'Supplier','name' => 'org_id','class' => 'CEditableColumn','type' => 'autocomplete' ,
					'value' => 'empty($data->org_id) || empty($data->cust) ? "" : $data->cust->name' ,
					'acOptions' => array('source' => 'org/supplierSuggest' )
				),
				array('header' => 'GST', 'name' => 'gst','class' => 'CEditableColumn', 'value' => '$data->getCostTaxType()','type' => 'list',
					'filter'=> Invoice::$InvoiceCostTaxRate ),
				array('name' => 'billing_cref', 'class' => 'CEditableColumn'),

				array('class' => 'CEditableButtonColumn',
					'template' => '{edit} {cancel} {save} {delete}',
					'buttons' => array
					(
						'delete' => array(
							'imageUrl' => false,
							'visible' => '($data->status < 2 && $data->actual_amount == 0 && empty($data->billing_cref)) || ($data->actual_amount == 0 && $data->accrual_amount == 0 && empty($data->billing_cref))',
							'url' => 'Yii::app()->createUrl("ediJob/deleteCostGridLine", ["id" => $data->id])',
							'options' => array('class' => 'delete_btn'),
						),
					),
				),
			),
		));
		?>
	</div>
	<?php $this->endWidget(); ?>

	<h2>Main Jobs</h2>
	<?php
	if (!empty($model->mdata['main'])) {
		foreach ($model->mdata['main'] as $main) {
			echo '<a href="' . Yii::app()->createUrl('ediJob/update', array('id' => $main['id'])) . '" class="tab_link" title="' . $main['no'] . '">' . $main['no'] . '</a> ' . $main['note'] . '<br>';
		}
	} ?>
	<div class="form">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'edi-job-link-main-job-form',
			'enableAjaxValidation' => false
		));?>
		<div class="row rowcol rowleft">
			<?php echo CHtml::label('Main Job No', 'main_job_id'); ?>
			<?php echo CHtml::textField('main_job_no', '', ['size' => '12']); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::label('Note', 'main_job_note'); ?>
			<?php echo CHtml::textField('main_job_note', '', ['size' => '12']); ?>
			<?php echo CHtml::button('Add Link', ['id' => 'btn-add-link']); ?>
		</div>
		<?php $this->endWidget(); ?>
	</div>
	<br>

	<h2>Sub Jobs</h2>
	<?php
	if (!empty($model->mdata['sub'])) {
		foreach ($model->mdata['sub'] as $sub) {
			echo '<a href="' . Yii::app()->createUrl('ediJob/update', array('id' => $sub['id'])) . '" class="tab_link" title="' . $sub['no'] . '">' . $sub['no'] . '</a> ' . $sub['note'] . '<br>';
		}
	} ?>
	<br>
	<br>

	<?php
		if ( isset($aflines) && !empty($aflines) ) {

			echo '<div style="width: 70%;background-color: #a9a9a9;"><span style="font-size: 20px;font-weight: bold;">Please fix red item as following real cost details</span>';
			$this->widget('zii.widgets.grid.CGridView', array(
					'id'=>'op-af-billing-import-list-lines-grid',
					'cssFile' => false,
					'dataProvider'=>$aflines->search(),
					'columns'=>array(
						'glcode',
						['name' => 'desc', 'type' => 'raw', 'value' => 'nl2br($data->desc)'],
						'gst',
						'amount'
					))
			);
			echo '</div>';
		}
		$cst = $model->totCost();
	?>
	<div class = "row">
		<div class="rowcol">
			<?php if ($model->currency == 1) { ?>
			<label>Total Revenue: <span id="edi-job-totrevenue"><?php echo number_format($model->totRevenue(),2); ?></span></label>
			<label>Total Cost: <span id="edi-job-totcost"><?php echo number_format($cst[1],2), ' / ', number_format($cst[0],2); ?></span></label>
			<label>Total Profit: <span id="edi-job-totprofit"><?php echo number_format(floatval($model->totProfit()),2); ?></span></label>
			<?php } else if ($model->currency == 2) { ?>
			<label>Total Revenue: <span id="edi-job-totrevenue"><?php echo number_format($model->totRevenue()[0],2); ?>USD</span> &asymp; <span id="edi-job-totrevenue"><?php echo number_format($model->totRevenue()[1],2); ?>AUD</span></label>
			<label>Total Cost: <span id="edi-job-totcost"><?php echo number_format($cst[1],2), ' / ', number_format($cst[0],2); ?>AUD</span></label>
			<label>Total Profit: &asymp; <span id="edi-job-totprofit"><?php echo number_format($model->totProfit(false),2); ?>AUD</span></label>
			<?php } ?>
		</div>
	</div>


<?php $this->endWidget(); ?>

</div><!-- form -->


<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	
	//bind reload_tab
	tab.off('reload_tab').on('reload_tab', function(){
		var t = $('.ui-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	});

	function getOrgTemplate1() {
		var orgid = $('#EdiAwbConsol_owner_id',panel).val();
		if ( orgid <= 0 ) {
			alert('please select one owner!');
			$('#EdiAwbConsol_owner_id',panel).focus();
		} else {
			var data = { 'org' : orgid};
			$.ajax({
				type: 'POST',
				url: '<?php echo Yii::app()->createAbsoluteUrl("org/ajaxGetEdiTemplateList") ;?>',
				data: data,
				dataType: 'json',
				success: function(resp) {
					if ( resp.success == 1 ) {
						$('#org-edi-template',panel).empty();
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

	function refreshInvAmount(dest){
		var trElement = dest.parent().parent();

		var qty = trElement.find('input[name="JobLine[qty][]"]').val();
		if ( typeof qty == 'undefined' ) {
			qty = trElement.find('input[name="JobLine[qty]"]').val();
		}

		var amountElement = trElement.find('input[name="JobLine[rate][]"]');
		var rate = amountElement.val();
		if ( typeof rate == 'undefined' ) {
			amountElement = trElement.find('input[name="JobLine[rate]"]');
			rate = amountElement.val();
		}

		if ( typeof qty == 'undefined' || qty == "") qty = 0;
		if ( typeof rate == 'undefined' || rate == "") rate = 0;
		amountElement = amountElement.parent().next();
		var totalAmount = myApp.formatNumber(parseInt(qty) * parseFloat(rate),3, '.', ',');
		amountElement.html('<span class="edi-job-inv-amount">' + totalAmount +"</span>")

	}

	function refreshCostAmount(dest){
		var trElement = dest.parent().parent();

		var qty = trElement.find('input[name="BillingLine[qty][]"]').val();
		if ( typeof qty == 'undefined' ) {
			qty = trElement.find('input[name="BillingLine[qty]"]').val();
		}

		var amountElement = trElement.find('input[name="BillingLine[price][]"]');
		var rate = amountElement.val();
		if ( typeof rate == 'undefined' ) {
			amountElement = trElement.find('input[name="BillingLine[price]"]');
			rate = amountElement.val();
		}

		if ( typeof qty == 'undefined' || qty == "") qty = 0;
		if ( typeof rate == 'undefined' || rate == "") rate = 0;
		amountElement = amountElement.parent().next();
		var totalAmount = myApp.formatNumber(parseInt(qty) * parseFloat(rate),3, '.', ',');
		amountElement.html('<span class="edi-job-cost-amount">' + totalAmount +"</span>")

	}

	function refreshInvoiceTotal(){
		var r = $('#<?=$_GET["tabid"];?>-update-edi-job-invoice-grid .items tbody tr',panel);
		var totRevenue = 0;
		$(r).each(function(){
			var qty = $(this).find('input[name="JobLine[qty][]"]').val();
			if ( typeof qty == 'undefined' ) {
				qty = $(this).find('input[name="JobLine[qty]"]').val();
			}
			var rate = $(this).find('input[name="JobLine[rate][]"]').val();
			if ( typeof rate == 'undefined' ) {
				rate = $(this).find('input[name="JobLine[rate]"]').val();
			}

			var costAmount = $(this).find('input[name="JobLine[cost_amount][]"]').val();
			if ( typeof costAmount == 'undefined' ) {
				costAmount = $(this).find('input[name="JobLine[cost_amount]"]').val();
			}
			qty = parseInt(qty);
			rate = parseFloat(rate);
			costAmount = parseFloat(costAmount);
			if ( qty > 0 && rate > 0 ) {
				totRevenue += qty * rate;
			}

		});
		return totRevenue;
	}

	function refreshCostTotal(){
		var r = $('#<?=$_GET["tabid"];?>-update-edi-job-cost-grid .items tbody tr',panel);
		var totCost = 0;
		$(r).each(function(){
			var qty = $(this).find('input[name="BillingLine[qty][]"]').val();
			if ( typeof qty == 'undefined' ) {
				qty = $(this).find('input[name="BillingLine[qty]"]').val();
			}
			var rate = $(this).find('input[name="BillingLine[price][]"]').val();
			if ( typeof rate == 'undefined' ) {
				rate = $(this).find('input[name="BillingLine[price]"]').val();
			}
			qty = parseInt(qty);
			rate = parseFloat(rate);
			if ( qty > 0 && rate > 0 ) {
				totCost += qty * rate;
			}
		});
	   return totCost;
	}

	function refreshTotal(){
		var totCost = refreshCostTotal();
		var totRevenue = refreshInvoiceTotal();
		var totProfit = totRevenue - totCost;
		$('#edi-job-totrevenue',panel).html(myApp.formatNumber(totRevenue,3, '.', ','));
		$('#edi-job-totcost',panel).html(myApp.formatNumber(totCost,3, '.', ','));
		$('#edi-job-totprofit',panel).html(myApp.formatNumber(totProfit,3, '.', ','));
	}

	$(panel).on('#<?=$_GET["tabid"];?>-update-edi-job-invoice-grid input', '.change-event', function(e) {
		//console.log('input changed');
		refreshTotal();
		refreshInvAmount($(this));
	});

	$(panel).on('#<?=$_GET["tabid"];?>-update-edi-job-cost-grid input', '.change-event', function(e) {
		//console.log('input changed');
		refreshTotal();
		refreshCostAmount($(this));
	});

	$('#btn-update-cost-rate',panel).click(function(e){
		var weight = $('#jbv_gw',panel).val();
		if ( weight <= 0  ) {
			alert('Weight must be more than zero!');
			$('#jbv_gw',panel).focus();
			return;
		}
		var rate = $('#jbv_gw_rate',panel).val();
		if ( rate <= 0  ) {
			alert('Rate must be more than zero!');
			$('#jbv_gw_rate',panel).focus();
			return;
		}
		$(this).attr('disabled',true);
		$('#air-cost-rate-set',panel).hide();
		$.ajax({
			type: 'POST',
			url: '<?php echo Yii::app()->createAbsoluteUrl("ediJob/ajaxUpdateJobCostRate") ;?>',
			data: { 'jid' : <?php echo $model->id; ?>,'w' : weight,'r' : rate },
			dataType: 'json',
			success: function (resp) {
				$('#btn-update-cost-rate',panel).attr('disabled',false);
				if (resp.success == 1) {
					$('#update-cost-result',panel).html('<span style="color:#008000">Cost updated successfully</span>');
					$('#<?php echo $_GET["tabid"]; ?>-update-edi-job-cost-grid',panel).yiiGridView('update');
					$('#<?php echo $_GET["tabid"]; ?>-update-edi-job-invoice-grid',panel).yiiGridView('update');
				} else {
					$('#update-cost-result',panel).html('<span style="color:#ff0000">'+ resp.msg +'</span>');
				}
			}
		});
	});

	$('#btn-update-cost',panel).click(function(e){

		var weight = $('#jbv_gw',panel).val();
		if ( weight <= 0  ) {
			alert('Weight must be more than zero!');
			$('#jbv_gw',panel).focus();
			return;
		}
		$(this).attr('disabled',true);
		$('#air-cost-rate-set',panel).hide();
		$.ajax({
			type: 'POST',
			url: '<?php echo Yii::app()->createAbsoluteUrl("ediJob/ajaxUpdateJobCost") ;?>',
			data: { 'jid' : <?php echo $model->id; ?>,'w' : weight },
			dataType: 'json',
			success: function (resp) {
				$('#btn-update-cost',panel).attr('disabled',false);
				if (resp.success == 1) {
					$('#update-cost-result',panel).html('<span style="color:#008000">Cost updated successfully</span>');
					$('#<?php echo $_GET["tabid"]; ?>-update-edi-job-cost-grid',panel).yiiGridView('update');
					$('#<?php echo $_GET["tabid"]; ?>-update-edi-job-invoice-grid',panel).yiiGridView('update');

				} else {
					$('#update-cost-result',panel).html('<span style="color:#ff0000">'+ resp.msg +'</span>');
					if ( resp.ecode == 3 ) {
						// show air freight quote update dialog
						// for updating now
						$('#air-cost-rate-set',panel).show();
					}
				}
			}
		});
	});

	$('#btn-create-invoice',panel).click(function(e){
		$(this).attr('disabled',true);
		createInvoice();
	});

	var createInvoice = function(check = 0) {
		$.ajax({
			type: 'POST',
			url: '<?php echo Yii::app()->createAbsoluteUrl("ediJob/ajaxCreateInvoice") ;?>',
			data: { 'jid' : <?php echo $model->id; ?>, 'created' : $('#created_' + '<?=$_GET["tabid"];?>', panel).val(), 'due' : $('#due_' + '<?=$_GET["tabid"];?>', panel).val(), 'currency' : $('#EdiJob_currency', panel).val(), 'confirm' : check, 'inv_type': $('#EdiJob_inv_type', panel).val(), 'ref2': $('#ref2', panel).val() },
			dataType: 'json',
			success: function (resp) {
				$('#btn-create-invoice',panel).attr('disabled',false);
				if (resp.success == 1) {
					myApp.notice('Invoice create/update successfully', 5000);
					// alert('Invoice create/update successfully');
				} else {
					if (typeof resp.msg == 'object') {
						var msg = '';
						resp.msg.forEach(function(v, k) {
							msg += v + '\n';
						});
						if (msg.match('cost')) {
							// if (confirm(msg) && check == 0) {
								createInvoice(1);
							// }
						} else {
							alert(msg);
						}
					} else {
						if (resp.msg.match('cost')) {
							// if (confirm(resp.msg) && check == 0) {
								createInvoice(1);
							// }
						} else {
							alert(resp.msg);
						}
					}
				}
				if (resp.msg) {
					if (typeof resp.msg == 'object') {
						$('#rev_err').html(resp.msg.join('<br />'));
					} else {
						$('#rev_err').html('');
					}
				}
			}
		});
	}

	$('#btn-add-link', panel).click(function(e) {
		$(this).attr('disabled', true);
		$.ajax({
			type: 'POST',
			url: '<?php echo Yii::app()->createUrl("ediJob/mainJobLink"); ?>',
			data: { 'id' : <?php echo $model->id; ?>, 'main_job_no' : $('#main_job_no', panel).val(), 'main_job_note' : $('#main_job_note', panel).val() },
			dataType: 'json',
			success: function(resp) {
				if (resp.success == 1) {
					$('#btn-add-link', panel).attr('disabled', false);
					$('#main_job_no', panel).val('');
					$('#main_job_note', panel).val('');
					alert('Link main job successfully');
					$('.ui-tabs', panel).tabs('load', $('.ui-tabs', panel).tabs('option','active'));
				} else {
					$('#btn-add-link', panel).attr('disabled', false);
					alert(resp.msg);
				}
			}
		});
	});

	$('.select-on-check-all', panel).prop('disabled', true);

	$('.select-on-check', panel).off('click').on('click', function() {
		var checked = 0;
		$('.select-on-check', panel).each(function() {
			if ($(this).prop('checked')) {
				checked ++;
			}
		});

		if (checked >= 2) {
			$('.select-on-check', panel).each(function() {
				if (!$(this).prop('checked')) {
					$(this).prop('disabled', true);
				}
			});
		} else {
			$('.select-on-check', panel).each(function() {
				if (!$(this).prop('checked')) {
					$(this).prop('disabled', false);
				}
			});
		}
	});

	$('#btn-switch-billingline', panel).off('click').on('click', function() {
		var selected = [];
		$('.select-on-check', panel).each(function() {
			if ($(this).prop('checked')) {
				selected.push($(this).val());
			}
		});

		$.ajax({
			'type': 'POST',
			'url': '<?=Yii::app()->createUrl("ediJob/switchBillingLine")?>',
			'dataType': 'json',
			'data': { 'selected' : selected },
			success: function(resp) {
				if (resp.success) {
					alert('switch successfully');
					$('#<?php echo $_GET["tabid"]; ?>-update-edi-job-cost-grid',panel).yiiGridView('update');
				}
			}
		});
	});

	// $('#<?=$_GET["tabid"];?>-update-edi-job-invoice-grid', panel).off('change', 'select').on('change', 'select', function() {
	// 	var type = $(this).parent().parent().find('select').val();
	// 	var org = $('#EdiJob_owner_id').val();
	// 	var jid = '<?=$model->id?>';
	// 	var rate = $(this).parent().parent().find('input[name*="rate"]');

	// 	$.ajax({
	// 		'type': 'GET',
	// 		'url': '<?=Yii::app()->createUrl("ediJob/rateSuggest")?>',
	// 		'dataType': 'json',
	// 		'data': { 'type' : type, 'org' : org, 'jid' : jid },
	// 		success: function(resp) {
	// 			if (resp.rate) {
	// 				rate.val(resp.rate);
	// 				rate.css('color', 'red');
	// 			}
	// 		}
	// 	});
	// });

	$('#load-edi-template',panel).click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		$(this).attr('disabled', true);
		var tid = $('#org-edi-template',panel).find(':selected').val();
		if ( tid <= 0 ) {
			alert('please select one template!');
			$('#EdiJob_owner_id',panel).focus();
		} else {
			var data = { 'tid' : tid, 'jid' : '<?=$model->id?>' };
			$.ajax({
				type: 'POST',
				url: '<?php echo Yii::app()->createAbsoluteUrl("ediJob/ajaxAddEdiTemplate") ;?>',
				data: data,
				dataType: 'json',
				success: function(resp) {
					if ( resp.success == 1 ) {
						$('#<?=$_GET["tabid"];?>-update-edi-job-invoice-grid', panel).yiiGridView('update');
						$('#<?=$_GET["tabid"];?>-update-edi-job-cost-grid', panel).yiiGridView('update');
						refreshTotal();
						setTimeout(function() {
							$('#load-edi-template', panel).removeAttr('disabled');
						}, 3e3);
					} else {
						if (resp.errMsg) {
							alert(resp.errMsg);
						} else {
							alert('Sorry, failed to get related template');
						}
					}
				}
			});
		}
	});

	$('#auto-fill', panel).on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		$(this).attr('disabled', true);
		var flight = $('#EdiAwbConsol_flight', panel).val();
		var weight = $('#jbv_gw', panel).val();
		$.ajax({
			type: 'POST',
			url: '<?php echo Yii::app()->createAbsoluteUrl("ediJob/ajaxAutofill2", ["jid" => $model->id]);?>',
			data: { 'flight': flight, 'weight': weight },
			dataType: 'json',
			success: function(resp) {
				if (resp.success == 1) {
					$('#<?=$_GET["tabid"];?>-update-edi-job-invoice-grid', panel).yiiGridView('update');
					$('#<?=$_GET["tabid"];?>-update-edi-job-cost-grid', panel).yiiGridView('update');
					refreshTotal();
					myApp.notice('Success');
				} else {
					if (resp.msg) {
						alert(resp.msg);
					} else {
						alert('Sorry, failed to auto fill');
					}
				}
				setTimeout(function() {
					$('#auto-fill', panel).removeAttr('disabled');
				}, 2e3);
			}
		});
	});
	
	$(panel).on('click', '.ajax_link', function() {
		$(this).prev().remove();
		$(this).remove();
	});

	// edijob create from wmstask
	if ($('#EdiAwbConsol_owner_id',panel).val()) {
		getOrgTemplate1();
	}

	$('#invoice_rate', panel).on('click', function() {
		var org_id = $('#EdiAwbConsol_owner_id').val();
		$(this).attr('href', '/ediJob/invoiceRate/' + org_id + '.app');
	});

	$('#plt-save', panel).on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		$(this).attr('disabled', true);
		var plt = $('#plt', panel).val();
		var ref = $('#ref', panel).val();
		var jbv_gw = $('#jbv_gw', panel).val();
		var ref2 = $('#ref2', panel).val();
		var hc40 = $('#hc40', panel).val();
		var hc20 = $('#hc20', panel).val();
		var gp40 = $('#gp40', panel).val();
		var gp20 = $('#gp20', panel).val();
		var plt_change = $('#plt_change', panel).val();
		$.ajax({
			type: 'POST',
			url: '<?php echo Yii::app()->createAbsoluteUrl("ediJob/ajaxPlt", ["jid" => $model->id]);?>',
			data: { 'plt': plt, 'ref': ref, 'jbv_gw': jbv_gw, 'ref2': ref2, 'hc40': hc40, 'hc20': hc20, 'gp40': gp40, 'gp20': gp20, 'plt_change': plt_change },
			dataType: 'json',
			success: function(resp) {
				myApp.notice('Success');
				setTimeout(function() {
					$('#plt-save', panel).removeAttr('disabled');
				}, 2e3);
			}
		});

	});

	$('#EdiAwbConsol_awb', panel).on('change', function() {
		var value = $(this).val();
		$.ajax({
			type: 'GET',
			dataType: 'json',
			data: { 'awb': value, 'id': '<?=$awbModel->id?>' },
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