<h2> New Overpayment </h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'bank-new-overpayment-form',
	'enableAjaxValidation'=>false,
)); ?>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'org_id'); ?>
		<?php echo $form->hiddenField($model,'org_id', array('data-ov' => $model->org_id));
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => empty($model->org_id)? '' : $model->cust->name,
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
	<div class="row">
        <div class="rowcol">
	        <?php echo $form->labelEx($model,'currency'); ?>
	        <?php echo $form->dropDownList($model, 'currency', $this->t(Invoice::$currencies)); ?>
        </div>
        <div class="rowcol">
            <?php echo $form->labelEx($model,'ref'); ?>
            <?php echo $form->textField($model,'ref'); ?>
        </div>
        <div class="rowcol">
            <?php echo $form->labelEx($model,'amount'); ?>
            <?php echo $form->textField($model,'amount'); ?>
        </div>

	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t( 'Create')); ?>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	
	$('form#bank-new-overpayment-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
});
</script>