<ul class="table-view">
<?php
function randVc(){
	return chr(rand(97,122)).rand(10,99).chr(rand(97,122));
}
if (!empty($model->mainTask->deliveryTask->mdata['shipment_id'])) {
	foreach ($model->mainTask->deliveryTask->mdata['shipment_id'] as $shipment) {
		$shipment = Shipment::model()->findByPk($shipment);

		if (empty($shipment->eitems['g'])) {
			continue;
		}

		foreach ($shipment->eitems['g'] as $k => $prod) {
			echo '<li class="table-view-cell table-view-cell-full">',
			$shipment->ref, '<p>', $prod, ' <a class="del_item pull-right" href="' . $this->createUrl('job/delLabel', ['id' => $shipment->id, 'line' => $k]) . '" data-vc="' . randVc() . '"><span class="icon icon-trash" style="font-size:1.2em"></span></a></p></li>';
		}
	}
}