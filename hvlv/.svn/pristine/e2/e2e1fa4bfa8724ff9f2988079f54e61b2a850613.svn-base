<h3><?=$name?></h3>
<style>
	.cloumn_red_1{
        color:red;
        font-weight: bold;
    }   
    .cloumn_orange_1{
        color:orange;
        font-weight: bold;
    }
    .cloumn_red{
        background-color:pink;
    }  
    .column_direct{
        color: green;
        font-weight: bold;
    }
</style>

<?php 
// $cacheKey = "getDeconsolidation".$dpt_id;
// //echo "123".$dpt_id;
// $dataProvider = Yii::app()->cache->get($cacheKey);
// if(empty($dataProvider))
// {
// 	$dataProvider = $model->search(true, 10, false, false);
// 	Yii::app()->cache->set($cacheKey,$dataProvider,43200);
// }

$this->widget('zii.widgets.grid.CGridView', [
	'id'=>'im-parcel-grid'.@$modelType,
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 10, false, false),
	'filter'=>$model,
	'columns'=>[
		['name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',],
		'ref',
		//'can',
		'agent_id',
		//['name'=>'awb_name','header'=>'awb','type'=>'raw','value'=>'@$data->consol->service==10?@$data->consol->awb:@$data->consol->mdata["container_no"]',],
		//        array('header' => 'Awb', 'type' => 'raw', 'value' => 'empty($data->consol)? "" : $data->consol->awb',),
		//'pkg',
		['name' => 'consol_no', 'type'=>'raw', 'value' => 'empty($data->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>"',],
		['name' => 'consol.awb','value'=>'$data->consol->service==10?$data->consol->awb:@$data->consol->mdata["container_no"]','filter'=>CHtml::textField('CargoProcess[awb]',@$cargo->awb)],
		//['header' => 'Unpacking Date', 'value' => '@$data->consol->mdata["ContainerUnloadDate"]'],
		'postcode',
		'can',
		['header' => 'Unpacking Date', 'value' => '@$data->consol->mdata["ContainerUnloadDate"]'],
		['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('ImParcel[status]', $model->status, $this->t(ImParcel::$states), ['prompt'=>$this->t('All')]),],
		// ['name' => 'ddpt_id', 'value' => '@$data->ddepot->name',
		// 	'filter'=>CHtml::dropDownList('ImParcel[ddpt_id]', $model->ddpt_id, $this->t(Org::dptList()), ['prompt'=>$this->t('All')]),],
		
		['name' => 'created', 'value' => 'substr($data->created,0,10)'],
		['header'=>'goods available address', 'type' => 'raw','value'=>'$data->getAvailabelLabel()','filter'=>CHtml::dropDownList('ImParcel[goodsAvailableAddress]', @$model->goodsAvailableAddress, $this->t(DmawbConsol::$available_address_name), ['prompt'=>'All'])],
		//'weight',
		// ['header' => 'Location', 'type' => 'raw', 'value' => '$data->getLocation()',],
		// ['header' => 'Clear Log', 'type' => 'raw',   'value'=>function ($data) {
		// 	return CHtml::tag('div', ['title'=>@$data->mdata['clear_log'],], $data->getClearLog());
		// },],
		// ['header' => 'Tranship', 'value' => '$data->getTranships()'],
		//['name'=>'scan_no','header'=>'unScan', 'value' => '$data->pkg - $data->getOutPkg()'],
		//['name' => 'rack','type'=>'raw','value' => '$data->getRackName(false,true)'],
		//['name' => 'bag_tag', 'value' => '@$data->bag_tag->bag_tag','filter'=>CHtml::textField('ImParcel[bagTag]',$model->bagTag)],
		'pkg',
		["header"=>"PrePrint",'type' => 'raw',"value"=>'CHtml::dropDownList($data->id, $data->mdata["isreminderprint"], [0=>"No",1=>"Print",3=>"Warehouse"], ["class"=>"is_reminderprintshi","style"=>"width:90px;"])',"filter"=>false],
		
	],
]);

; ?>
<script type="text/javascript">
	$(function() {
		$('body').off('change', '.is_reminderprintshi').on('change', '.is_reminderprintshi', function(){
			const tr = $(this).parents('tr');
			var listData = new FormData();
			listData.append('action',$('.is_reminderprintshi', tr).val());
			listData.append('id',$('.is_reminderprintshi', tr).attr('id'));
			htmlobj = $.ajax({
				url: '<?=$this->createUrl("cargoProcess/updateReminder")?>',
				type: "post",
				data: listData,
				async: false,
            	contentType: false,
            	processData: false,
			});
			obj = JSON.parse(htmlobj.responseText);
			//alert(obj.isSuccess);
        	if (obj.isSuccess) {
				myApp.notice('Successful', 5000);
			}
			else{
				myApp.notice('Error', 5000);
			}
		});

		// $('#egw0').after('<div class="pull-right"><button type="button" data-toggle="modal" data-target="#modal-export" class="btn btn-default btn-sm">Export</button></div>');
	});
</script>