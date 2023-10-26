<div class="form">

	<?php if (!empty($model->mdata['other_org'])) {
		foreach ($model->mdata['other_org'] as $k => $org) {
			$org = Org::model()->findByPk($org);
			echo ($k+1) . '. ' . $org->name . '<br />';
		}
	} ?>

	<br />

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'group-org-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row rowcol">
		<label>Another Org:</label>
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

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Confirm')); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>

<script>
$(function() {
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>').data('panel');

	$('#group-org-form', win).on('success', function() {
		setTimeout(function() {
			$('.popCancel', win).trigger('click');
			$('#group_org', tab).trigger('click');
		}, 1e3);
	});
});
</script>