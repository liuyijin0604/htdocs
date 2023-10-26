<style>
#return-form input[type="radio"] {
	-ms-transform: scale(1.5); /* IE 9 */
	-webkit-transform: scale(1.5); /* Chrome, Safari, Opera */
	transform: scale(1.5);
}
</style>

<div class="container" style="padding: 0">
	<div class="form">
		<?php
		$_GET['tabid'] = 112321231;
		$form = $this->beginWidget('CActiveForm', array(
			'id' => 'return-form',
			'action' => $model->isNewRecord ? $this->createUrl('return/create', array('jid' => $_GET['jid'])) : $this->createUrl('return/update', array('id' => $model->id)),
			'enableAjaxValidation' => false,
		)); ?>

		<h2>Basic</h2>
		<div class="row">
			<div class="col col-md-2 col-sm-4">
				<div class="form-group">
					<?php echo $form->labelEx($model, 'ref'); ?>
					<?php echo $form->textField($model,'ref', ['size' => 25, 'class' => 'form-control']); ?>
				</div>
			</div>

			<div class="col col-md-4 col-sm-8">
				<div class="form-group">
					<?php echo CHtml::label('Weights(kg)', 'weight', array('required' => 'required')) . '<span style="color: red">Please split by "," for multiple parcels</span>'; ?>
					<?php echo CHtml::textField('mdata[weight]', @$model->mdata['weight'], array('class' => 'form-control', 'rows' => 5, 'required' => 'required')); ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col col-md-6 col-sm-6">
				<div class="form-group">
					<?php echo CHtml::label('Notes', 'mdata[note]'); ?>
					<?php echo $form->textArea($model, 'mdata[note]', array('class' => 'form-control', 'rows' => 5)); ?>
				</div>
			</div>
		</div>

		<h2>Sender Address</h2>
		<?php if (!empty($model->deliveryTask)) $deliveryTask = $model->deliveryTask;
		else $deliveryTask = new WmsTask; ?>
		<?php echo $this->renderPartial('_form_delivery', array('form' => $form, 'model' => $deliveryTask), true); ?>

		<h2>Parcel Information</h2>
		<?php echo $this->renderPartial('_form_product', array('model' => $model), true); ?>

		<h2>Warehouse Action</h2>
		<?php if (empty($model->mdata['return_option'])) $model->mdata['return_option'] = 2; ?>
		<?php echo $form->radioButtonList($model, 'mdata[return_option]', $this->t(WmsTask::$return_options_customer_return), array('labelOptions' => array('style' => 'font-size: 18px'), 'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;')); ?>
		<br>
		<br>

		<?php
		if (!empty($model->deliveryTask) && !empty($model->deliveryTask->mdata['return_shipment_id'])) {
			echo '<span style="color: red">Already generated return label, cannot be changed</span>';
		} else if (!empty($model->mdata['redelivery_task'])) {
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
		'submit':function(e,r) {
			var weights = $('#mdata_weight', panel).val().split(',');
			var flag = true;
			weights.forEach(function(v, k) {
				if (isNaN(v)) {
					$('#notifc').notify({message: {html: 'Weight is invalid'}, type: 'danger'}).show();
					flag = false;
				}
			});
			if (!flag) {
				return false;
			}
			$('#return-btn').prop('disabled', true);
		},
		'success': function(e, r) {
			if (window.location.href.indexOf('return/create') >= 0) {
				var url = window.location.href.substr(0, window.location.href.indexOf('return/create')) + 'return/list.app';
				pcawApp.toPage(url);
			} else if (window.location.href.indexOf('return/update') >= 0) {
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