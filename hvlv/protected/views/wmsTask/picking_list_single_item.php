<h1>Picking List (Single Item)</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'picking-list-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('wmsTask/export', array('t' => 'pickinglistsingleitem')),
		'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
	)); ?>

	<?php
	echo '<div class="row">', CHtml::label('Deport', ''), '</div>';
	echo '<div class="row">', CHTML::dropDownList("dpt_id", 0, array("106" => "SYD", "218" => "MEL")), '</div>';

	foreach ($orgs as $org) {
		//@Autohr:Nero @Date:2021/5/24 @Description:show all orgs.
		//if (!in_array($org['org_id'], [Org::ORGID_3PL_XCSOURCE, Org::ORGID_3PL_SMART_HUB, Org::ORGID_3PL_KWIKTEACH, Org::ORGID_3PL_LIFESTYLE, Org::ORGID_3PL_BIOPHYSICS])) continue;
		//if (!in_array($org['org_id'], [Org::ORGID_3PL_I_HOMDEC,Org::ORGID_3PL_V_MAX])) continue;
		if (!in_array($org['org_id'], [Org::ORGID_3PL_I_HOMDEC, Org::ORGID_3PL_SUBEMO, Org::ORGID_3PL_VMAX])) continue;
		echo '<div class="row rowcol rowleft">', CHtml::checkbox('orgs[' . $org['org_id'] . ']', ''), '</div>';
		echo '<div class="row rowcol">', CHtml::label(Org::getName($org['org_id'], false), 'orgs'), '</div>';
	}
	?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?> <span style="color: red"></span>
	</div>

	<?php $this->endWidget(); ?>
</div>