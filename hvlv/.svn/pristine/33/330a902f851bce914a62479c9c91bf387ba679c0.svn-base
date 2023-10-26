<h1><?=$this->t('Weight Check Report');?></h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'filter-shipment-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('invoice/report', ['type' => 'wtck']),
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<div class="row rowcol">
		<label>Agent:</label>
		<?php echo CHtml::hiddenField('agent_id');
			$acname1 = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '30',
				),
		));
		?>
	</div>

    <div class="row">
        <?php echo CHtml::label('From','forimclient'); ?>
        <?php echo CHtml::textField('from_date',date('Y-m-d',strtotime('-7 days',time())),['class' => 'date_input']); ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

});
</script>