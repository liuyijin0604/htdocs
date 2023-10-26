<style>
input[type=checkbox] {
	-ms-transform: scale(2); /* IE */
	-moz-transform: scale(2); /* FF */
	-webkit-transform: scale(2); /* Safari and Chrome */
	-o-transform: scale(2); /* Opera */
	transform: scale(2);
}
.return-check-form div {
	padding: 0;
	margin: 0;
}
.return-check-form .row {
	padding: 5px 0;
}
</style>

<h2>Check List</h2>
<div class="form return-check-form" style="padding-top: 5px">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'wms-task-return-check-form',
		'action' => $this->createUrl('task/updateTask', ['id' => $model->id]),
		'enableAjaxValidation' => false,
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data')
	));
	?>
	<?php foreach (WmsTask::$check_services as $k => $service) { ?>
		<div class="row">
			<div class="col col-md-1 col-sm-2" style="padding-top: 5px; padding-left: 7px; width: 3%">
				<div class="form-group">
					<?php $tick = !empty($model->mdata['check_' . $k]) ? true : false; ?>
					<?php echo CHtml::checkBox('meta[check_' . $k .']', @$tick); ?>
				</div>
			</div>
			<div class="col col-md-8 col-sm-12">
				<div class="form-group">
					<?php echo CHtml::label($service, 'check_' . $k, ['style' => 'font-size: 20px; padding-left: 20px']); ?>
				</div>
			</div>
		</div>
		<?php if ($service == 'Other') { ?>
		<div class="row">
			<div class="col col-md-8 col-sm-12">
				<div class="form-group">
					<?php echo CHtml::textArea('meta[check_' . $k . '_detail]', @$model->mdata['check_' . $k . '_detail'], ['cols' => 40, 'rows' => 3, 'class' => 'form-control']); ?>
				</div>
			</div>
		</div>
		<?php } ?>
	<?php } ?>

	<div class="form-group" style="padding-top: 20px">
		<?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary ajax-link')); ?>&nbsp;&nbsp;
		<a type="button" href="<?=$this->createUrl('task/returnOption', ['id' => @$model->mainTask->mdata['origin_task']])?>" class="btn btn-success ajax-link">Next</a>
	</div>
	<?php $this->endWidget(); ?>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>