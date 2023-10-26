<h1>Packing List (Multi Items)</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'packing-list-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('wmsTask/export', array('t' => 'packingallmultiitems')),
		'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
	)); ?>

	<?php foreach ($orgs as $org) {
		//if (!in_array($org['org_id'], [Org::ORGID_3PL_KWIKTEACH, Org::ORGID_3PL_LIFESTYLE, Org::ORGID_3PL_NINJA_SHARK, Org::ORGID_3PL_BIOPHYSICS])) continue;
		echo '<div class="row rowcol rowleft">', CHtml::checkbox('orgs[' . $org['org_id'] . ']', ''), '</div>';
		echo '<div class="row rowcol">', CHtml::label(Org::getName($org['org_id'], false), 'orgs'), '</div>';
	} ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?> <span style="color: red">请全部勾选一次打印，切勿分开</span>
	</div>

	<?php $this->endWidget(); ?>
</div>