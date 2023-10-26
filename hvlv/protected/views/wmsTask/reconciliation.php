<h1>Reconciliation Report</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'courier-list-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('wmsTask/export', array('t' => 'reconciliation')),
		'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
	)); ?>

	<?php 
		echo '<div class="row ">', CHtml::label('Export Type',''), '</div>';
		echo '<div class="row ">', CHTML::dropDownList("type",0,array("0"=>"Invoice Number","1"=>"Org Id")), '</div>';
		//echo '<div class="row ">', CHtml::label('Please Input Invoice Number',''), '</div>';
		echo '<div class="row ">', CHtml::label('Invoice Number or Org Id',''), '</div>';
		echo '<div class="row ">', CHtml::textField('inv_id'), '</div>';
	?>

	<div class="row buttons">
		<?php 
			echo CHtml::submitButton($this->t('Generate')); 
		?>
	</div>

	<?php $this->endWidget(); ?>
</div>