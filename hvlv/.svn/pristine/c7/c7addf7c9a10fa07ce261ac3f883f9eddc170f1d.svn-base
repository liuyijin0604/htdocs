<style>
#return-form input[type="radio"] {
	-ms-transform: scale(1.5); /* IE 9 */
	-webkit-transform: scale(1.5); /* Chrome, Safari, Opera */
	transform: scale(1.5);
}
</style>

<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
	'Return List' => array('return/list'),
		'Update',
	),
));
?>
<h1>Update Return - <?=$model->getNo() . ' ' . $model->getReturnStatus()?></h1>

<div class="container" style="padding: 0">
	<div class="form">
		<?php
		$_GET['tabid'] = 112321231;
		$form = $this->beginWidget('CActiveForm', array(
			'id' => 'return-form',
			'action' => $this->createUrl('return/update', array('id' => $model->id)),
			'enableAjaxValidation' => false,
		)); ?>

		<h2>Receiver Address</h2>
		<?php
		if (!empty($model->mdata['redelivery_task'])) {
			$deliveryTask = WmsTask::model()->findByPk($model->mdata['redelivery_task'])->deliveryTask;
		} else {
			$deliveryTask = $model->deliveryTask;
		} ?>
		<?php echo $this->renderPartial('_form_delivery', array('form' => $form, 'model' => $deliveryTask), true); ?>

		<h2>Warehouse Action</h2>
		<?php if (empty($model->mdata['return_option'])) $model->mdata['return_option'] = 2; ?>
		<?php echo $form->radioButtonList($model, 'mdata[return_option]', $this->t(WmsTask::$return_options_courier_rts), array('labelOptions' => array('style' => 'font-size: 18px'), 'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 'disabled' => !empty($model->mdata['redelivery_task']) ? 'disabled' : '')); ?>
		<br>
		<br>

		<?php
		if (!empty($model->mdata['redelivery_task'])) {
			echo '<span style="color: red">Already generated re-delivery task, cannot be changed</span>';
		} else {
			echo CHtml::submitButton($this->t('Save'), array('class' => 'btn btn-primary', 'style' => 'font-size: 20px', 'id' => 'return-btn'));
		}
		?>

		<?php $this->endWidget(); ?>
	</div>
</div>
<br>

<script>
$(function() {
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	$('#return-form').on({
		'success': function(e, r) {
			if (window.location.href.indexOf('return/update') >= 0) {
				var url = window.location.href.substr(0, window.location.href.indexOf('return/update')) + 'return/list.app';
				pcawApp.toPage(url);
			}
		},
		'error': function(e, r) {
			$('#notifc').notify({message: {html: r.msg}, type: 'danger'}).show();
			$('#return-btn').prop('disabled', false);
		}
	});
});
</script>