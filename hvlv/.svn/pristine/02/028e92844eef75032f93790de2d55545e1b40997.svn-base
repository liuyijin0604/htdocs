
<?php 

	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
				'id'=>$_GET['tabid'].'unknown_shipment-grid',
				'cssFile' => false,
				'summaryText'=>'',
				'dataProvider'=> $model->search(true,30),
				'filter'=>$model,
				'columns'=>[
					['name'=>'container_no'],
					['header'=>'shipment','value'=>'empty($data->shipment_id)?"":$data->shipment->ref',
						'spanable' => true, 'spanDepands' => ['@$data->shipment_id']
					],
					['name'=>'barcode'],
					['name'=>'temp_barcode','value'=>function($data)
						{
							$file = FileRepo::model()->find("name like :name and type =:type",[":name"=>$data->temp_barcode."%",":type"=>FileRepo::UNKNOWN_SHIPMENT_PHOTO]);
							if(!empty($file))
							{
								echo "<a href=\"".Yii::app()->baseUrl."/filerepo/".$file->hash."/".$file->name."\" 		target=\"_blank\">".$data->temp_barcode."</a>";
							}
						}
					],
					['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('ImportsUnknownShipment[status]', $model->status, $this->t($model::$states), ['prompt'=>$this->t('All')]),],
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