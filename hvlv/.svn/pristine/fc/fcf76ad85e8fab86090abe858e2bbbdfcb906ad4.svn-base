<h1><?=$this->t('Receivables Report');?></h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'filter-shipment-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('payment/export', ['type' => 'report']),
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

    <div class="row rowcol">
        <?php echo CHtml::label('From Date:','rcvb'); ?>
        <?php echo CHtml::textField('fromdate', date('Y-m-01', strtotime('-1 month')), ['size' => '12', 'class' => 'date_input']); ?>
    </div>

    <div class="row rowcol">
        <?php echo CHtml::label('To Date:','rcvb'); ?>
        <?php echo CHtml::textField('todate', date('Y-m-d', strtotime(date('Y-m-01').' -1 day')), ['size' => '12', 'class' => 'date_input']); ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('#fromdate', win).on('change', function(){
		var v = $(this).val();
		var ld = new Date(v.substr(0, 4), parseInt(v.substr(5,2)), 0).getDate();
		$('#todate', win).val(v.substr(0,8) + ld);
	});
});
</script>