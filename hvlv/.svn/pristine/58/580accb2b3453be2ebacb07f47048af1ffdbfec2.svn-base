<h1>Courier List</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'courier-list-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('wmsTask/export', array('t' => 'courierall')),
		'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
	)); ?>

	<?php
	echo '<div class="row rowcol rowleft">', CHtml::label('Deport', ''), '</div>';
	echo '<div class="row rowcol">', CHTML::dropDownList("dpt_id", 0, array("106" => "SYD", "218" => "MEL")), '</div>';
	echo '<div class="row rowcol rowleft">', CHtml::label('Recreate Shipment', ''), '</div>';
	echo '<div class="row rowcol">', CHTML::checkBox('recreate'), '</div>';
	echo '<div class="row rowcol rowleft">', CHtml::label('Org List', ''), '</div>';
	foreach ($orgs as $org) {
		$options[] = Org::ORGID_3PL_I_HOMDEC;
		//$options[] = Org::ORGID_3PL_V_MAX;
		$options[] = Org::ORGID_3PL_SUBEMO;
		$options[] = Org::ORGID_3PL_VMAX;
		if (in_array($org['org_id'], $options)) {
			echo '<div class="row rowcol">', CHtml::label(Org::getName($org['org_id'], false), 'orgs'), '</div>';
			echo '<div class="row rowcol">', CHtml::checkbox('orgs[' . $org['org_id'] . ']', ''), '</div>';
		}
	} ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Generate')); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>