<ul class="table-view">
<?php
$count = 0;
foreach(array_reverse($model->items) as $itm){
	$count ++;
	if ($count > 10 && $model->type == 3010) break;
	// if($itm->op_id != Yii::app()->user->id && User::model()->findByPk(Yii::app()->user->id)->type != 0) continue;

	if(in_array($model->type, [3010])){
		if (!empty($itm->mdata['pli'])) {
			$itm->mdata['pl'] = WmsLocation::model()->findByPk($itm->mdata['pli'])->name;
		}
		echo '<li class="table-view-cell table-view-cell-full">',
		$itm->mdata['sn'] . '<p>',
		$itm->mdata['pl'];
		if ($model->mainTask->status != 99 || (isset(Yii::app()->user->grp) && Yii::app()->user->grp == 0)) {
			echo '<a class="del_item pull-right" href="' . $this->createUrl('job/delItem', ['id' => $itm->id]) . '" data-vc="' . $this->randVc() . '"><span class="icon icon-trash" style="font-size:1.2em"></span></a>';
		}
		echo '</p></li>';
	}elseif(in_array($model->type, [1010, 1020, 1030])){
		$pas = '';
		if(!empty($itm->mdata['pas'])){
			$plt = new WmsLocation;
			$plt->bwf = $itm->mdata['pas'];
			$pas = ' <b>'.AppHelper::bwf2warning($plt,true).'</b>';
		}
		echo '<li class="table-view-cell table-view-cell-full">',
		$itm->mdata['gn'],' (Qty: ', sprintf('%d', $itm->mdata['uq']), ')<p>'
		,$itm->mdata['pl'], $pas;
		if ($model->mainTask->status != 99 || (isset(Yii::app()->user->grp) && Yii::app()->user->grp == 0)) {
			echo ' <a class="del_item pull-right" href="'.$this->createUrl('job/delItem',['id' => $itm->id]).'" data-vc="'.$this->randVc().'"><span class="icon icon-trash" style="font-size:1.2em"></span></a>';
		}
		echo '</p></li>';
	}elseif(in_array($model->type, [3020, 3030])){
		echo '<li class="table-view-cell table-view-cell-full">';
		echo $itm->mdata['sn'],' (Qty: ', sprintf('%d', $itm->mdata['uq']), ')<p>'
		,$itm->mdata['pl'];
		if ($model->mainTask->status != 99 || (isset(Yii::app()->user->grp) && Yii::app()->user->grp == 0)) {
			echo ' <a class="del_item pull-right" href="'.$this->createUrl('job/delItem',['id' => $itm->id]).'" data-vc="'.$this->randVc().'"><span class="icon icon-trash" style="font-size:1.2em"></span></a>';
		}
		echo '</p>';
		if ($model->mainTask->status != 99 && $itm->recSerial()) {
			echo '<a class="btn btn-primary modal_link" style="font-size: 1em; position: relative; margin: 20px 0 -15px 15px;" href="'.$this->createUrl('job/recSerial',['id' => $itm->id]).'">Serial <b>'.$itm->serialScanned().'/'.$itm->totalUq().'</b></a>';
		}
		echo '</li>';
	}elseif(in_array($model->type, [2030, 2040])){
		echo '<li class="table-view-cell table-view-cell-full">', $itm->mdata['pl'],'<p>';
		if ($model->mainTask->status != 99 || (isset(Yii::app()->user->grp) && Yii::app()->user->grp == 0)) {
			echo ' <a class="del_item pull-right" href="'.$this->createUrl('job/delItem',['id' => $itm->id]).'" data-vc="'.$this->randVc().'"><span class="icon icon-trash" style="font-size:1.2em"></span></a>';
		}
		echo '</p></li>';
	}
}
?>
</ul>