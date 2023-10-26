<h1>Export Google Review Records</h1>

<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'google_review_report_form',
        'enableAjaxValidation' => false,
        'action' => $this->createUrl('googleReview/export'),
        'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
    )); ?>
    
    <div class="row rowcol rowleft">
		<?php echo CHtml::label('From Date:','fd'); ?>
		<?php echo CHtml::textField('from_date', '', array('size' => 12, 'id' => 'fd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>
    <div class="row rowcol">
		<?php echo CHtml::label('To Date:','td'); ?>
		<?php echo CHtml::textField('to_date', '', array('size' => 12, 'id' => 'td_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>
    <div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

    <?php $this->endWidget(); ?>
</div>