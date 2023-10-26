<h1>Bidding Details: <?php echo $ref?></h1>
<?php
$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id'=>$_GET["tabid"].'_cargo_process_bidding_grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 50,false,true),
	'filter'=>$model,
	'columns'=>[	
		'driver_id',	
		['header'=>'Driver Name','value'=>'@$data->driver->name'],
		'choosedate',
		'cost',
		'note',
		['class'=>'oButtonColumn',
			'template'=>'{Accept}&nbsp;',//{Biddings}
			'buttons'=>[
				'Accept' => [
					'url'=>'Yii::app()->createURL("cargoProcessBidding/accept")."?id=".$data->id',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->id'],
				],
				// 'Biddings' => [
				// 	'imageUrl'=>false,
				// 	'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Bidding Details', 'data-win-class' => 'L'],
				// 	'visible' => 'true',
				// 	'url' => 'Yii::app()->createUrl("cargoProcessBiddingDetail/index", ["id" => $data->id])',
				// 	'label' => 'Log'
				// ],
			],
		]
	],
]);
?>