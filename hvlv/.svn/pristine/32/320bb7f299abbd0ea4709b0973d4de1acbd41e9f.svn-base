<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'method' => 'get',
	)); ?>


	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'Depot');
		?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->checkBox($model, 'isSyd', array('value' => '1', 'uncheckValue' => '0', 'checked' => 'checked'));
		?>&nbsp;SYD
	</div>
	<div class="row rowcol">
		<?php echo $form->checkBox($model, 'isMel', array('value' => '1', 'uncheckValue' => '0'));
		?>&nbsp;MEL
	</div>

	<div class="row rowcol">
		<?php echo $form->checkBox($model, 'isBne', array('value' => '1', 'uncheckValue' => '0'));
		?>&nbsp;BNE
	</div>
	<div class="row rowcol">
		<?php echo $form->checkBox($model, 'isPer', array('value' => '1', 'uncheckValue' => '0'));
		?>&nbsp;PER
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'staDate'); ?>
		<?php echo $form->textField($model, 'staDate', ['class' => 'date_input', 'id' => $_GET['tabid'] . '_sta_due','value'=>date('Y-m-01',strtotime(date("Y-m-d")))]); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->label($model, 'endDate'); ?>
		<?php echo $form->textField($model, 'endDate', ['class' => 'date_input', 'id' => $_GET['tabid'] . '_end_due','value'=>date("Y-m-d")]); ?>
	</div>
	<!-- <div class="row rowcol rowleft">
		<?php //echo $form->label($model, 'fromDate'); ?>
		<?php //echo $form->textField($model, 'fromDate', ['class' => 'date_input', 'id' => $_GET['tabid'] . '_fr_due','value'=>date('Y-m-01',strtotime(date("Y-m-d")))]); ?>
	</div>

	<div class="row rowcol">
		<?php //echo $form->label($model, 'toDate'); ?>
		<?php //echo $form->textField($model, 'toDate', ['class' => 'date_input', 'id' => $_GET['tabid'] . '_to_due','value'=>date("Y-m-d")]); ?>
	</div> -->
	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'type'); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->checkBox($model, 'isFBA', array('value' => '1', 'uncheckValue' => '0'));
		?>&nbsp;FBA
	</div>

	<div class="row rowcol">
		<?php echo $form->checkBox($model, 'isB2B', array('value' => '1', 'uncheckValue' => '0'));
		?>&nbsp;B2B
	</div>

	<div class="row rowcol">
		<?php echo $form->checkBox($model, 'isB2C', array('value' => '1', 'uncheckValue' => '0'));
		?>&nbsp;B2C
	</div>

	<div class="row rowcol">
		<?php echo $form->checkBox($model, 'isInterState', array('value' => '1', 'uncheckValue' => '0'));
		?>&nbsp;InterState
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::checkBox("reCalculate",false);
		?>&nbsp;ReCalculate - only use if new OT invoice create for shipment;
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); //array('style'=>'margin-left:10px;')
		?>
		<?php echo CHtml::button('Export',array(
            "id"=>'export_button',
            )); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- search-form -->