<h1>Quick Courier Label</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'quick-courier-label-form',
		'enableAjaxValidation' => false,
		'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
	));

	foreach ($sum as $org_id => $types) {
		$org = Org::model()->findByPk($org_id);
		foreach ($types as $type => $tasks) {
			echo '<div class="row rowcol rowleft">' . CHtml::checkbox('sum[' . $org_id . '][' . $type . ']', '') . '&nbsp;' . $org->name . ' - <b><span style="color: red">' . $type . '</span>(' . count($tasks) . ')</b></div>';
		}
	}
	?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>