<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'general-cost-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model, 'dpt_id', Org::dptList(), array('prompt'=>$this->t('Select One'))); ?>
	</div>
    <div class="row rowcol">
        <?php echo $form->labelEx($model,'dpmt'); ?>
        <?php
            $allDpmts =  Invoice::$dpmts;
            $allDpmts[1] =  'Auto Split';
        ?>
        <?php echo $form->dropDownList($model, 'dpmt', $allDpmts, array('prompt'=>$this->t('Select One'))); ?>
    </div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'org_id'); ?>
		<?php echo $form->hiddenField($model,'org_id', array('data-ov' => $model->org_id));
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/supplierSuggest'),
				'value' => empty($model->cust)? '' : $model->cust->name,
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

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'date'); ?>
	<?php echo $form->textField($model,'date', ['size' => '12', 'class' => 'date_input', 'id' => 'general_date']); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'due'); ?>
	<?php echo $form->textField($model,'due', ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'transaction_date'); ?>
	<?php echo $form->textField($model,'transaction_date', ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'currency'); ?>
	<?php echo $form->dropDownList($model, 'currency', $this->t(Invoice::$currencies)); ?>
	</div>

    <div class="row rowcol">
        <?php echo CHtml::label('Billing No.','forgcost'); ?>
        <?php echo $form->textField($model,'billing_cref', ['size' => '12']); ?>
    </div>

    <div class="row">
	<label>Items</label>
	<?php
	$il = new BillingLine('search');
    $il->unsetAttributes();
	$il->billing_id = empty($model->id)? -1 : $model->id;
	$this->widget('application.extensions.editablegrid.CEditableGridView', array(
		'id'=>'general-cost-grid',
		'cssFile' => false,
		'dataProvider'=>$il->search(),
		'formUrl' => $this->createUrl('billing/generalCostlinesGrid', array( 'id' => empty($model->id) ? 0 : $model->id)),
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
            array('name' => 'item_code', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5]),
            array('name' => 'desc', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 30]),
            array('name' => 'qty', 'class' => 'CEditableColumn','inputOptions' => ['size' => 5]),
            array('name' => 'price', 'class' => 'CEditableColumn','inputOptions' => ['size' => 8]),

            array('header' => 'Charge Code','name' => 'charge_code','class' => 'CEditableColumn','type' => 'autocomplete' , 'inputOptions' => ['size' => 15],
                'value' => '$data->getCCodeDesc()' ,
                'acOptions' => array('source' => 'chargeCode/chargeCodeSuggest' )
            ),

           array('header' => 'Tax Rate', 'name' => 'gst','class' => 'CEditableColumn','type' => 'list',
                'filter'=> Invoice::$InvoiceCostTaxRate ),

            array('header' => 'Department', 'name' => 'dept','class' => 'CEditableColumn','type' => 'list',
                'filter'=> BillingLine::$general_cost_dpmts ,'htmlOptions' => ['class' => 'grid-department-col']),

			array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}'),
		),
	));
	?>
	</div>

    <?php if ( !$model->isNewRecord && $model->status < 3 ) : ?>
    <div class="row">
        <?php echo CHtml::checkBox('syncxero',false),'Push to Xero'; ?>
    </div>
    <?php endif; ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	
	$('form#general-cost-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

});
</script>