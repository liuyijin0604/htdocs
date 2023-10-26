<h1>Packing List (Single Item)</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'packing-list-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('wmsTask/export', array('t' => 'packingallsingleitem')),
		'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
	)); ?>

	<?php foreach ($orgs as $org) {
		//@Autohr:Nero @Date:2021/5/24 @Description:show all orgs.
		//if (!in_array($org['org_id'], [Org::ORGID_3PL_XCSOURCE, Org::ORGID_3PL_SMART_HUB, Org::ORGID_3PL_KWIKTEACH, Org::ORGID_3PL_LIFESTYLE, Org::ORGID_3PL_BIOPHYSICS])) continue;
		echo '<div class="row rowcol rowleft">', CHtml::checkbox('orgs[' . $org['org_id'] . ']', ''), '</div>';
		echo '<div class="row rowcol">', CHtml::label(Org::getName($org['org_id'], false), 'orgs'), '</div>';
	} ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?> <span style="color: red">请全部勾选一次打印，切勿分开</span>
	</div>

	<?php $this->endWidget(); ?>
</div>