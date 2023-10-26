<div class="content-padded">
	

	<h3><?= $NAME ?> Booking Confirm For <?= $model->getRef() ?></h3>
	<div class="form">
		<?php
		$date = $model->mdata['delivery_booking_time'];
		$YEAR = date('Y', strtotime($date));
		$MONTH = date('m', strtotime($date));
		$DAY = date('d', strtotime($date));


		$form = $this->beginWidget('CActiveForm', array(
			'id' => 'winit-confirm-form',
			'enableAjaxValidation' => false,
		)); ?>

		<div class="row">

			<div class="col-12" style="padding-left: 5px;">
				<p>您指定的如下派送<?= $CNAME ?>货物</p>
			</div>

			<div class="col-12" style="padding-left: 5px;">
				<p><?= $model->getRef() ?></p>
			</div>

			<div class="col-12" style="padding-left: 5px;">
				<p><?= $model->shipment->pkg ?>ctns / <?= $model->shipment->weight ?>kg / <?= $model->shipment->getTotalCBM() ?>cbm</p>
			</div>

			<div class="col-12" style="padding-left: 5px;">
				<p><?= $model->shipment->cnee->fullAddress() ?></p>
			</div>

			<div class="col-12" style="padding-left: 5px;">
				<p>我司将定于<?= $YEAR ?>年<?= $MONTH ?>月<?= $DAY ?>日 送往<?= $CNAME ?>仓库</p>
			</div>

			<div class="col-12" style="padding-left: 5px;">
				<?php echo CHtml::hiddenField('caref', $model->getRef()); ?>
				<?php echo CHtml::submitButton($this->t('确认已预约'), array('class' => 'save_btn btn btn-primary btn-block')); ?>
			</div>
		</div>

		<?php $this->endWidget(); ?>

	</div>
</div>
