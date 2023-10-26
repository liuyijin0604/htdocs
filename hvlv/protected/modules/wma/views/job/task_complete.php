<?php
echo '<h4>This picking task is completed.</h4>';
if (isset($model->mainTask->status) && ($model->mainTask->status < 99)) {
	$model->mainTask->status = 30;
	$model->mainTask->save();
}
function randVc() {
	return chr(rand(97,122)).rand(10,99).chr(rand(97,122));
}
foreach ($model->mainTask->subTasks as $task) {
	if ($task->type == '2110') {
		if ($task->mainTask->status < 99) {
			echo '<a class="switch_item" href="'.$this->createUrl('job/switchDelivery',['id' => $task->id]).'" data-vc="'.randVc().'"><span class="icon icon-refresh" style="font-size:1.2em"></span> Switch To Delivery</a><br/>';
		}
		if (!empty($task->mdata['pin']) && empty($task->mdata['pincheck'])) {
			echo '<h4><span style="color: #3c763d">该订单自提，需签收</h4>';
			echo '<input type="hidden" name="pin" value="true" />';
			echo '<script>$(".entry-form button[name=search]").hide();</script>';
			echo '<button type="submit" class="btn btn-primary btn-block"><span class="icon icon-pencil"></span>Check Ticket</button>';
		} else if (empty($task->mdata['pickup'])) {
			echo '<h4><span style="color: #3c763d">该订单自提，需签收</h4>';
			echo '<input type="hidden" name="sign" value="true" />';
			echo '<script>$(".entry-form button[name=search]").hide();</script>';
			echo '<button type="submit" class="btn btn-primary btn-block"><span class="icon icon-pencil"></span>Sign</button>';
		}
	} else if ($task->type == '2120') {
		if ($task->mainTask->status < 99) {
			echo '<a class="switch_item" href="'.$this->createUrl('job/switchDelivery',['id' => $task->id]).'" data-vc="'.randVc().'"><span class="icon icon-refresh" style="font-size:1.2em"></span> Switch To Pick Up</a><br/>';
		}
		Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
		if ($model->mainTask->status < 99) {
			$qr = new TCPDF2DBarcode('T' . sprintf('%06d', $model->mainTask->id) . ' complete', 'QRCODE, H');
		} else if ($model->mainTask->status == 99) {
			$qr = new TCPDF2DBarcode('T' . sprintf('%06d', $model->mainTask->id) . ' reprint', 'QRCODE, H');
		}
		header('Content-Type: image/svg+xml');
		echo $qr->getBarcodeSVG(12, 12, 'black');
	}
}