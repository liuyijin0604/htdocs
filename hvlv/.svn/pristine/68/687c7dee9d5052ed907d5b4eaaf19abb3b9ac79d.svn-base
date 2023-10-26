
<?php 

	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
				'id'=>$_GET['tabid'].'unknown_shipment-grid',
				'cssFile' => false,
				'dataProvider'=> $model->search(true,30,"t.create_time"),
				'filter'=>$model,
				'columns'=>[
					['name'=>'container_no','spanable' => true, 'spanDepands' => ['$data->consol_id']],
					['name'=>'barcode'],
					['name'=>'ground_label'],
					['name'=>'create_time'],
					['name'=>'comment'],
					['class'=>'application.extensions.CSpanableGridView.oSpanableButtonColumn',
						'template'=>'{print}',
						'buttons'=>[
							'print'=>[
				    	      'imageUrl'=>false,
				    	      'visible'=>'true',
				    	      'options' => ['class' => 'grid_edit_btn', 'label' => 'print', 'data-win-class' => 'L','target'=>'_blank'],
				    	      'url' => 'Yii::app()->createUrl("whscan/shipment/generateTempLabel", ["tempId" => $data->id])',
				    	      'label' => 'print'
				    	    ]
						]
					],
				],
			]);

?>