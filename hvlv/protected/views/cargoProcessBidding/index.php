<?php
$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id'=>$_GET["tabid"].'_cargo_process_bidding_grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 50,false,true),
	'filter'=>$model,
	'columns'=>[
		['name' => 'shipment_id', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"','htmlOptions'=>array('style'=>'width: 120px')],
		['header'=>'Ref','type' => 'raw','value'=>'@$data->shipment->ref'],
		['header'=>'From','value'=>'@$data->getFromString()'],
		['header'=>'To','value'=>'@$data->getToString()'],
		['header'=>'Cargo Type','value'=>'@$data->getCargoType()'],
		['header'=>'Delivery Date','value'=>'@$data->getBiddingDate()'],
		['header'=>'Receivble Time','value'=>'@$data->getReceivbleTime()'],
		['header'=>'CBM','value'=>'@$data->cargo_process->getTotalCBM()'],
		['header'=>'Weight','value'=>'@$data->cargo_process->getWeight()'],
		//'op_cost',
		['header'=>'Best Price','value'=>'@$data->getReservePrice()'],
		'note',

		['class'=>'oButtonColumn',
			'template'=>'{Update}&nbsp;{Biddings1}&nbsp;{Biddings2}',
			'buttons'=>[
				'Update' => [
					'url'=>'Yii::app()->createURL("cargoProcessBidding/update")."?id=".$data->id',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->id'],
				],
				'Biddings1' => [
					'imageUrl'=>false, //jqm_link grid_view_btn
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Biddings', 'data-win-class' => 'L', 'title' => '@$data->getBiddingTabName()'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("cargoProcessBiddingDetail/index", ["id" => $data->id])',
					//'label' => 'Biddings'
				],
				'Biddings2' => [
					'imageUrl'=>false, //jqm_link grid_view_btn
					'options' => ['class' => 'tab_link grid_edit_btn', 'label' => 'Biddings', 'data-win-class' => 'L', 'title' => '@$data->getBiddingTabName()'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("cargoProcessBiddingDetail/index", ["id" => $data->id])',
					//'label' => 'Biddings'
				],
			],
		]
	],
]);
?>
