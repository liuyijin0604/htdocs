<ul class="table-view">
<?php
function randVc()
{
	return chr(rand(97, 122)) . rand(10, 99) . chr(rand(97, 122));
}

foreach ($tasks as $model) {
	foreach (array_reverse($model->actionTask->items) as $itm) {
		if ($itm->op_id != Yii::app()->user->id) {
			continue;
		}

		if (in_array($model->type, [3010])) {
			// if (!empty($itm->mdata['pli'])) {
			// 	$itm->mdata['pl'] = WmsLocation::model()->findByPk($itm->mdata['pli'])->name;
			// }
			// echo '<li class="table-view-cell table-view-cell-full">',
			// $itm->mdata['sn'] . '<p>',
			// $itm->mdata['pl'], ' <a class="del_item pull-right" href="' . $this->createUrl('job/delItem', ['id' => $itm->id]) . '" data-vc="' . randVc() . '"><span class="icon icon-trash" style="font-size:1.2em"></span></a></li>';
		} elseif (in_array($model->type, [1010, 1020, 1030])) {
			echo '<li class="table-view-cell table-view-cell-full">',
			$itm->mdata['gn'], ' (Qty: ', sprintf('%d', $itm->mdata['uq']), ')<p>',
			$itm->mdata['pl'], ' <a class="del_item pull-right" href="' . $this->createUrl('job/delItem', ['id' => $itm->id]) . '" data-vc="' . randVc() . '"><span class="icon icon-trash" style="font-size:1.2em"></span></a></li>';
		} elseif (in_array($model->type, [3020, 3030])) {
			echo '<li class="table-view-cell table-view-cell-full">',
			$itm->mdata['sn'], ' (Qty: ', sprintf('%d', $itm->mdata['uq']), ')<p>',
			$itm->mdata['pl'], ' <a class="del_item pull-right" href="' . $this->createUrl('job/delItem', ['id' => $itm->id]) . '" data-vc="' . randVc() . '"><span class="icon icon-trash" style="font-size:1.2em"></span></a></li>';
		} elseif (in_array($model->type, [2030, 2040])) {
			echo '<li class="table-view-cell table-view-cell-full">', $itm->mdata['pl'], ' <a class="del_item pull-right" href="' . $this->createUrl('job/delItem', ['id' => $itm->id]) . '" data-vc="' . randVc() . '"><span class="icon icon-trash" style="font-size:1.2em"></span></a></li>';
		}
	}
}
?>
</ul>