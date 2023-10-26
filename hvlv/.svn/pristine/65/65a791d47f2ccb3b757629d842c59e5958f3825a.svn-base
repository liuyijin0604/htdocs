<style type="text/css">
.grid-container {
    overflow: auto;
    max-height: 800px; /* Set a maximum height for the container to enable scrolling */
}

.grid-container .grid-view thead {
    position: sticky;
    top: 0;
    background-color: #f2f2f2; /* Adjust the background color of the fixed header */
    z-index: 1; /* Ensure the header stays above the content */
}

</style>
<div style="position: absolute; right:80px;">
	<a class="export_search" target="_blank" href="<?=$this->createUrl('consolProcess/exportSurplus', ['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type'],'podName'=>$podName,'tab'=>$tab]);?>"><div style="background-position:-48px -688px" class="icon"></div>Export Current Search</a>
    <div class="form">
        <div class='row'>
        <div class="rowcol rowleft">
            <a class="tab_link"  href="<?=$this->createUrl("consolProcess/surplusShipmentList", ['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type'],'podName'=>$podName,'ImportsUnknownShipment[status]'=>ImportsUnknownShipment::PROCESSDONE,'tab'=>$tab])?>" title="<?=$podName?> Done Surplus Shipments" ><span style="background-position:-48px -688px" class="icon"></span>Surplus Shipments Done List</a>
        </div>
        </div>
        
    </div>
</div>
<?php
	echo "<h1>{$podName} Surplus Shipments List</h1>";
?>

<div class="grid-container">
<?php
	$tempBarcode = [];
	if($model->agentId ==-1)
	{
		$tempBarcode = [	['name'=>'temp_barcode','value'=>function($data)
							{
								$file = FileRepo::model()->find("name like :name and type =:type",[":name"=>$data->temp_barcode."%",":type"=>FileRepo::UNKNOWN_SHIPMENT_PHOTO]);
								if(!empty($file))
								{
									echo "<a href=\"".Yii::app()->baseUrl."/filerepo/".$file->hash."/".$file->name."\" 		target=\"_blank\">".$data->temp_barcode."</a>";
								}else
								{
									echo $data->temp_barcode;
								}
							}
		]];
	}
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
				'id'=>$_GET['tabid'].$tab.'unknown_shipment-grid',
				'cssFile' => false,
				'dataProvider'=> $model->search(true,300,"t.create_time"),
				'filter'=>$model,
				'columns'=>array_merge([
					['name' => 'dpt_id', 'value' => '@$data->depot->name', 'filter' => CHtml::hiddenField('tab',$tab).CHtml::dropDownList(get_class($model).'[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), ['prompt' => $this->t('All')])],
					['name'=>'barcode']],
					$tempBarcode,
					[
					['name'=>'container_no','type'=>'raw',
					'value'=>'empty($data->consol_id) ? $data->container_no : "<a href=\"" . Yii::app()->createURL(Consol::getTheConsolType($data->consol_id) == 70 ? "dmawbConsol/update" : "imcoConsol/update", array("id" => $data->consol_id)) . "\" class=\"tab_link\" title=\"" . @$data->container_no . "\">" . @$data->container_no . "</a>"'
					],
					['name' => 'shipment.status','header'=>'Shipment Status','value' => '!empty($data->shipment_id)?$data->shipment->getStatus():""', 'filter' => CHtml::dropDownList(get_class($model).'[shipmentStatus]', $model->shipmentStatus, $this->t(ImParcel::$states), ['prompt' => $this->t('All')])],
					['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('ImportsUnknownShipment[status]', $model->status, $this->t($model::$states), ['prompt'=>$this->t('All')]),],
					['name'=>'ground_label'],
					['name'=>'create_time','value'=>'date("d/m/Y",strtotime($data->create_time))'],
					['name'=>'client_upload_time',"value"=>'$data->client_upload_time!="0000-00-00 00:00:00"?date("d/m/Y",strtotime($data->client_upload_time)):""'],
					['header'=>'Check In','value'=>'!empty($data->shipment_id)?@$data->shipment->mdata["scan_time"]:""'],
					['name'=>'customer_comment'],
					['name'=>'comment','header'=>'TLA Notes'],
					['name'=>'sent_surplus_outturn','value'=>'ImportsUnknownShipment::$sentStatus[$data->sent_surplus_outturn]'],
					['name'=>'sent_unpacking_outturn','value'=>'$data->getUnpackingStatus()'],
					['name'=>'exist_shipment_id','value'=>'$data->exist_shipment_id>0?"Y":""'],
					
					['class'=>'application.extensions.CSpanableGridView.oSpanableButtonColumn',
						'template'=>'{operation}{close}{log}{print}',
						'buttons'=>[
							'operation'=>[
				    	      'imageUrl'=>false,
				    	      'visible'=>'true',
				    	      'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => 'operation', 'data-win-class' => 'L'],
				    	      'url' => 'Yii::app()->createUrl("consolProcess/exceptionShipmentOperation", ["id" => $data->id])',
				    	      'label' => 'operation'
				    	    ],
							'close'=>[
				    	      'imageUrl'=>false,
				    	      'visible'=>'true',
				    	      'options' => ['class' => 'close_unknown_shipment grid_edit_btn', 'label' => 'Close', 'data-win-class' => 'L'],
				    	      'url' => 'Yii::app()->createUrl("consolProcess/closeUnknownShipment", ["id" => $data->id])',
				    	      'label' => 'Close'
				    	    ],
							'log' => [
								'imageUrl'=>false,
								'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
								'visible' => 'true',
								'url' => 'Yii::app()->createUrl("consolProcess/surplusLog", ["id" => $data->id])',
								'label' => 'Log'
							],'print'=>[
				    	      'imageUrl'=>false,
				    	      'visible'=>($model->agentId==-1?'true':'false'),
				    	      'options' => ['class' => 'grid_edit_btn', 'label' => 'print', 'data-win-class' => 'L','target'=>'_blank'],
				    	      'url' => 'Yii::app()->createUrl("whscan/shipment/generateTempLabel")."?tempId=".$data->id',
				    	      'label' => 'print'
				    	    ]
						]
					],
				]),
			]);


?>
</div>


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

	$('a.export_search').on('mousedown', function(){
		var q = $('.filters input, .filters select').serialize();
		$(this).attr('href', '<?=$this->createUrl('consolProcess/exportSurplus',['pod_id'=>$_GET['pod_id'],'consol_type'=>$_GET['consol_type'],'podName'=>$podName]);?>&' + q);
	});

	})
</script>