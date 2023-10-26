<?php
	echo "<h1>{$podName} Exception Shipments List</h1>";
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
				'id'=>$_GET['tabid'].'unknown_shipment-grid',
				'cssFile' => false,
				'summaryText'=>'',
				'dataProvider'=>$dataProvider[0],
				'filter'=>$dataProvider[1],
				'columns'=>[
					['name'=>'container_no','header'=>'awb/container_no'],
					['name'=>'barcode','type'=>'raw','value'=>'(empty($data->container_no)||empty($data->consol_id))?$data->barcode:$data->getBarcodes(true)'],
					['name'=>'ground_label','value'=>'(empty($data->container_no)||empty($data->consol_id))?$data->ground_label:""'],
					['name'=>'status','value'=>'$data->getStatus()',
					'filter'=>CHtml::dropDownList('FiltersForm[status]', $status, $this->t(ImportsUnknownShipment::$states), ['prompt'=>$this->t('All')]),],
					['header'=>'unknown_barcodes','value'=>'(empty($data->container_no)||empty($data->consol_id))?"YES":$data->getConsolUnknownBarcodes()'],
					['header'=>'shipment_without_consol','value'=>'(empty($data->container_no)||empty($data->consol_id))?"":$data->getEmptyConsolShipment()'],
					['header'=>'closed_barcodes','value'=>'(empty($data->container_no)||empty($data->consol_id))?"":$data->getClosedUnknownBarcodes()'],
					['header'=>'notbelong_barcodes','value'=>'(empty($data->container_no)||empty($data->consol_id))?"":$data->getConsolNotBelongShipments()'],
					['name'=>'create_time'],

					['class'=>'application.extensions.CSpanableGridView.oSpanableButtonColumn',
						'template'=>'{operation}{send_surplus_email}{detail}{close}',
						'buttons'=>[
							'operation'=>[
				    	      'imageUrl'=>false,
				    	      'visible'=>'(empty($data->container_no)||empty($data->consol_id))?true:false',
				    	      'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => 'operation', 'data-win-class' => 'L'],
				    	      'url' => 'Yii::app()->createUrl("consolProcess/exceptionShipmentOperation", ["id" => $data->id])',
				    	      'label' => 'operation'
				    	    ],
				    	    'send_surplus_email'=>[
				    	      'imageUrl'=>false,
				    	      'visible'=>'(empty($data->container_no)||empty($data->consol_id))?false:true',
				    	      'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => 'detail', 'data-win-class' => 'L'],
				    	      'url' => 'Yii::app()->createUrl("consolProcess/createSurplusEmailM", ["id" => $data->id])',
				    	      'label' => 'Send SURPLUS Email'
				    	    ],
				    	    'detail'=>[
				    	      'imageUrl'=>false,
				    	      'visible'=>'(empty($data->container_no)||empty($data->consol_id))?false:true',
				    	      'options' => ['class' => 'tab_link grid_edit_btn', 'label' => 'detail', 'data-win-class' => 'L'],
				    	      'url' => 'Yii::app()->createUrl("consolProcess/getExceptionConsolDetail", ["id" => $data->id])',
				    	      'label' => 'detail'
				    	    ],
							'close'=>[
				    	      'imageUrl'=>false,
				    	      'visible'=>'(empty($data->container_no)||empty($data->consol_id))?true:false',
				    	      'options' => ['class' => 'close_unknown_shipment grid_edit_btn', 'label' => 'Close', 'data-win-class' => 'L'],
				    	      'url' => 'Yii::app()->createUrl("consolProcess/closeUnknownShipment", ["id" => $data->id])',
				    	      'label' => 'Close'
				    	    ]
						]
					],
				],
			]);


?>


<script>
	$(function(){
		var tab=$("#<?=$_GET['tabid']?>");
		var panel=tab.data('panel');


		$(panel).on('click',' .close_unknown_shipment',function(event){
			event.preventDefault();
			if(confirm("Are you Confirm to Close?")){
				$.get($(this).attr('href'),function(r){
					if(r=='done'){
						myApp.notice("Close!");
						$('#<?=$_GET['tabid']?>unknown_shipment-grid',panel).yiiGridView('update');
					}
				});
			 }
	});

	})
</script>