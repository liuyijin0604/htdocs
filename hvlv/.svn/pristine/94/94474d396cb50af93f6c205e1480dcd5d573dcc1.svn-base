<h2><?=$title;?></h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'filter-shipment-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('report/export', ['m' => 'consol', 'rpt' => $_GET['rpt']]),
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<?php echo $form->errorSummary($model); ?>
	<div class="row rowcol rowleft">
	<?php echo CHtml::label('Depot:','dt'); ?>
	<?php echo CHtml::dropDownList('wid', '', Org::dptList(), array('empty' => 'All')); ?>
	</div>
	
	<div class="row rowcol rowleft">
		<?php echo CHtml::label('From Date:','fd'); ?>
		<?php echo CHtml::textField('date[from]', empty($_POST['date']['from'])? date('Y-m-d', strtotime('-7 day')) : $_POST['date']['from'], array('size' => 12, 'id' => 'fd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('To Date:','td'); ?>
		<?php echo CHtml::textField('date[to]', empty($_POST['date']['to'])? date('Y-m-d') : $_POST['date']['to'], array('size' => 12, 'id' => 'td_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
