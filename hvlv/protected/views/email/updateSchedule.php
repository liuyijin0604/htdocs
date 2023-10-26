<h1><?=$this->t('Update Schedule');?> <?php echo $model->id; ?></h1>
  <div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'email-schedule-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<label class="left">Date Time:</label>
		<?=$model->dt;?>
	</div>
	<div class="row">
		<label class="left">CT No.:</label>
		<input id="ctno" name="ctno" type="text" value="<?php echo $model->ctno;?>" size="18" />
	</div>
	<div class="row">
		<label class="left">By:</label>
		<?=$model->from->getName();?>
	</div>
	<div class="row">
		<label class="left">Send Date:</label>
		<input id="schedule" name="schedule" type="text" class="date_input" value="<?php echo $model->scheduled;?>" size="18" />
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Save'), array('name' => 'submit')); ?> &nbsp; <?php echo CHtml::submitButton($this->t('Delete Schedule'), array('name' => 'submit', 'class' => 'cancel')); ?>
	</div>
<?php $this->endWidget(); ?>


	<div class="row">
	<?php
		echo CHtml::label('Emails','');
		$m = new Emailog('search');
		if(isset($_GET['Emailog'])) $m->attributes=$_GET['Emailog'];
		$m->scid = $model->scid;
		$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'scheduled-emails-grid',
		'cssFile' => false,
		'dataProvider' => $m->search(),
		'filter' => $m,
		'columns'=>array(
			array('name' => 'from_id', 'value' => '$data->getFrom()'),
			array('name' => 'to_name', 'value' => '$data->getTo()'),
			'subject',
			'dt',
			),
		));
	?>
	</div>

</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('.cancel', win).click(function(){
		return window.confirm('Do you want to delete this schedule send?');
	});
	$('form#email-schedule-form', win).on('success', function(e, r){
		win.data('opener').load();
		win.jqmHide();
	});
});
</script>