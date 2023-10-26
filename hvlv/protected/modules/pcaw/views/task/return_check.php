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

<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
		'Task List' => $this->createUrl('task/index'),
		'Check List',
	),
));
?>
<h2>Check List for Task-<?=!empty($model) ? $model->getNo() : ''?></h2>
<div class="form return-check-form" style="padding-top: 5px">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'wms-task-return-check-form',
		'action' => $this->createUrl('task/returnCheck', ['id' => $_GET['id']]),
		'enableAjaxValidation' => false,
	));
	?>
	<?php foreach (WmsTask::$check_services as $k => $service) { ?>
		<div class="row">
			<div class="col col-md-1 col-sm-2" style="padding-top: 5px; padding-left: 7px; width: 3%">
				<div class="form-group">
					<?php $tick = !empty($model->mdata['check_' . $k]) ? true : false; ?>
					<?php echo CHtml::checkBox('check_' . $k, @$tick); ?>
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
					<?php echo CHtml::textArea('check_' . $k . '_detail', @$model->mdata['check_' . $k . '_detail'], ['cols' => 40, 'rows' => 3, 'class' => 'form-control']); ?>
				</div>
			</div>
		</div>
		<?php } ?>
	<?php } ?>

	<div class="form-group" style="padding-top: 20px">
		<?php echo CHtml::hiddenField('act_btn', 'save'); ?>
		<?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary ajax-link', 'name' => 'save', 'style' => 'width: 80px')); ?>&nbsp;&nbsp;
		<a href="<?=$this->createUrl('task/returnOption', ['id' => $model->id])?>" class="btn btn-secondary ajax-link" style="width: 80px; text-decoration: none; color: blue">Skip</a>
		<a href="<?=$this->createUrl('task/index')?>" class="ajax-link" id="return-btn" style="width: 80px; float: right; text-decoration: none">Return</a>
	</div>
	<?php $this->endWidget(); ?>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
	$('#wms-task-return-check-form').on('success', function() {
		$('#return-btn').trigger('click');
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>