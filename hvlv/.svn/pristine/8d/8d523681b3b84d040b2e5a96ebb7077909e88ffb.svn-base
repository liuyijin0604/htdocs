<h1><?=$this->t('Receivables Report');?></h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'filter-shipment-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('billing/report', ['type' => 'rcvb']),
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>
	<!-- <div class="row rowcol rowleft">
	<?php echo CHtml::label('Depot:','dt'); ?>
	<?php echo CHtml::dropDownList('wid', '', Org::dptList(), array('empty' => 'ALL', 'class' => 'required')); ?>
	</div> -->
	
	<div class="row rowcol">
		<label>Supplier:</label>
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
        <?php echo CHtml::label('Date Before:','rcvb'); ?>
        <?php echo CHtml::textField('date','', ['size' => '12', 'class' => 'date_input']); ?>
    </div>

	<div class="row rowcol rowleft">
		<select name="rptyp">
			<!-- <option value="detail">Detailed Report</option> -->
			<option value="aging">Debtor Aging Report</option>
			<!-- <option value="pdf">Agent PDF statements</option> -->
			<!-- <option value="xls">Agent Excel statements</option> -->
			<!-- <option value="pdfnew">Agent PDF statemnts new</option> -->
			<!-- <option value="xlsnew">Agent Excel statemnts new</option> -->
		</select>
	</div>

	<div class="row rowcol">
		<!-- <label><input type="checkbox" name="wcredit" value="1" />Include Unreconciled Receipt (Exclude Credit Note)</label> -->
		<!-- <label><input type="checkbox" name="wcredit" value="2" />Include Unreconciled Receipt (Include Credit Note)</label> -->
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