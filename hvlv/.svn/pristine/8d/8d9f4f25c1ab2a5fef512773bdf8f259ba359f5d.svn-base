<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'invoice-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model, 'dpt_id', Org::dptList(), array('prompt'=>$this->t('Select One'))); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'dpmt'); ?>
		<?php echo $form->dropDownList($model, 'dpmt', Invoice::$dpmts, array('prompt'=>$this->t('Select One'))); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'to_id'); ?>
		<?php echo $form->hiddenField($model,'to_id', array('data-ov' => $model->to_id));
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => $model->type != 40 ? array('org/ownerSuggest') : array('org/exAgentSuggest'),
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
	<?php echo $form->textField($model,'date', ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'due'); ?>
	<?php echo $form->textField($model,'due', ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'currency'); ?>
	<?php echo $form->dropDownList($model, 'currency', $this->t(Invoice::$currencies)); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Consol No.','invconsole'); ?>
		<?php echo CHtml::textField('consolno','',['size' => '12']); ?>
	</div>

	<div class="row">
	<label>Items</label>
	<?php
	$il = new InvLine('search');
	$il->inv_id = empty($model->id)? -1 : $model->id;
	$this->widget('application.extensions.editablegrid.CEditableGridView', array(
		'id'=>'invline-grid',
		'cssFile' => false,
		'dataProvider'=>$il->search(),
		'formUrl' => $this->createUrl('invoice/linesGrid', array('id'=>empty($model->id)? 0 : $model->id)),
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
			array('header' => 'GL Code','name' => 'ccode', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5]),
			array('header' => 'Detail','name' => 'det', 'class' => 'CEditableColumn'),
			array('header' => 'Amount','name' => 'amount', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5]),
			array('header' => 'Qty','name' => 'qty', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5]),
			array('header' => 'Tax Rate', 'name' => 'tax','class' => 'CEditableColumn','type' => 'list',
				'filter'=> Invoice::$InvoiceRevenueTaxRate ),

			array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}'),
		),
	));
	?>
	</div>

	<!--
	<div class="row">
		<?php echo CHtml::checkBox('plusgst',false),'Plus GST'; ?>
	</div>
-->
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	
	$('form#invoice-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

	$("#Invoice_to_id",win).on('change',function(){
		var org_id=parseInt($(this).val());
			$.get("org/getDueDay?org_id="+org_id,function(r){
			if(r.match(/^\d{4}-\d{2}-\d{2}$/)){
				$("#Invoice_due",win).val(r);
			}
		});
	});

	$('form#invoice-form', win).on('submit', function() {
		if($('#Invoice_dpmt', win).val() != <?php echo Invoice::DPMT_3PL ?>){
			var consol = $('#consolno', win).val();
			if (!consol) {
				if (!window.confirm('Please confirm Consol No. is empty')) {
					return false;
				}
			}
		}
	});
});
</script>