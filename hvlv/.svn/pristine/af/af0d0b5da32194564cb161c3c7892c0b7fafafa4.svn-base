<style>
input[type=radio] {
	-ms-transform: scale(2); /* IE */
	-moz-transform: scale(2); /* FF */
	-webkit-transform: scale(2); /* Safari and Chrome */
	-o-transform: scale(2); /* Opera */
	transform: scale(2);
}
.return-option-form div {
	padding: 0;
	margin: 0;
}
.return-option-form .row {
	padding: 5px 0;
}
</style>

<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
		'Task List' => $this->createUrl('task/index'),
		'Return Option',
	),
));
?>
<h2>Option List for Task-<?=!empty($model) ? $model->getNo() : ''?></h2>
<div class="form return-option-form" style="padding-top: 5px">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'wms-task-return-option-form',
		'action' => $this->createUrl('task/returnOption', ['id' => $_GET['id']]),
		'enableAjaxValidation' => false,
	));
	?>
	<?php foreach (WmsTask::$return_services as $k => $service) { ?>
		<div class="row">
			<div class="col col-md-1 col-sm-2" style="padding-top: 5px; padding-left: 7px; width: 3%">
				<div class="form-group">
					<input type="radio" name="optionRadio" id="radio_<?=$k?>"  value="<?=$k?>" style="margin-top: 3px;" />
				</div>
			</div>
			<div class="col col-md-8 col-sm-12">
				<div class="form-group">
					<?php echo CHtml::label($service, 'radio_' . $k, ['style' => 'font-size: 20px; padding-left: 20px']); ?>
				</div>
			</div>
		</div>
	<?php } ?>

	<div class="form-group" style="padding-top: 20px">
		<?php echo CHtml::hiddenField('act_btn', 'save'); ?>
		<?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary ajax-link', 'name' => 'save', 'style' => 'width: 80px')); ?>
		<a href="<?=$this->createUrl('task/index')?>" class="ajax-link" id="return-btn" style="width: 80px; float: right; text-decoration: none">Return</a>
	</div>
	<?php $this->endWidget(); ?>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
	$('#wms-task-return-option-form').on('submit', function() {
		let checked = false;
		$('input[name="optionRadio"]').each(function() {
			if ($(this).prop('checked')) {
				checked = true;
			}
		});

		if (!checked) {
			$('#notifc').notify({message: {text: "Please choose one"},  type: 'danger'}).show();
			return false;
		}
	});

	$('#wms-task-return-option-form').on('success', function() {
		$('#return-btn').trigger('click');
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>