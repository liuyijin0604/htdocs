<h2><?=$title;?></h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'filter-dataentry-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('report/export', ['m' => 'dataentry']),
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('From Date:','fd'); ?>
		<?php echo CHtml::textField('date[from]', empty($_POST['date']['from'])? date('Y-m-01') : $_POST['date']['from'], array('size' => 12, 'id' => 'fd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('To Date:','td'); ?>
		<?php echo CHtml::textField('date[to]', empty($_POST['date']['to'])? date('Y-m-d') : $_POST['date']['to'], array('size' => 12, 'id' => 'td_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<label><input type="checkbox" name="detail" value="1" /> Detailed Report</label>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
