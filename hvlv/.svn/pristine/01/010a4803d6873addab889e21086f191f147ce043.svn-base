<h1>Packing List</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'packing-list-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('wmsTask/export', array('t' => 'packingall')),
		'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
	)); ?>

	<?php foreach ($orgs as $org) {
		echo '<div class="row rowcol rowleft">', CHtml::checkbox('orgs[' . $org['org_id'] . ']', ''), '</div>';
		echo '<div class="row rowcol">', CHtml::label(Org::getName($org['org_id'], false), 'orgs'), '</div>';
	} ?>

	<!-- <div class="row rowcol">
		<label>Client:</label>
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
	</div> -->

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>