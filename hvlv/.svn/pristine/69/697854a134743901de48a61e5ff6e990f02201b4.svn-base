<h3><?=$name?></h3>
<?php

$thisData = $tlaTask->search(false, 200,false,true)->data;
$ids = join(',',array_column($thisData,'id'));
echo "<h2>Points of Current Page:<span id='totalPoints'>0</span></h2>";


?>
<style>
    .cloumn_red{
        background-color:pink;
    }  
    .column_direct{
        color: green;
        font-weight: bold;
    }
</style>
<div style="right: 200px;position: absolute;">
	<a class="export_search_ds" target="_blank" href="#" data-baseurl="<?=$this->createUrl('tlaTask/exportDisputeTask', ['typ' => '']);?>"><div style="background-position:-48px -688px" class="icon"></div> Export Dispute Task With Template</a>
</div>
<?php
	$user_id = empty($user_id)?User::currentUserID():$user_id;
	$columns = [
		array('header' => 'Processing','type'=>'raw', 'value' => '$data->getStatusForList().CHtml::hiddenField("tab","'.$tab.'").$data->getPoints()."point"','filter'=>CHtml::label("","label",["id"=>"ids","value"=>$ids])),
		["header"=>"order",'type' => 'raw',"value"=>'CHtml::numberfield("pallet".$data->id, @$data->myTlaTaskUser->task_order,["class"=>"task_order","style"=>"width:40px;"])',"filter"=>false],
		array('name' => 'task_no'),
		array('name' => 'type','value'=>'$data->getInstance()->getType()','filter'=>CHtml::dropDownList('TlaTask[type]',@$tlaTask->type, TlaTask::$types,array('prompt'=>'Select'))),
		array('name' => 'task_content','type'=>'raw'),
		array('name' => 'comment','type'=>'raw','value'=>'$data->showComment()'),
		array('name' => 'dpt_id', 'value' => 'Org::dptList()[$data->dpt_id]',
				'filter'=>CHtml::dropDownList('TlaTask[dpt_id]', $tlaTask->dpt_id, Org::dptList(), ['prompt'=>$this->t('All')]),),
		array('name' => 'status','type'=>'raw', 'value' => '$data->getStatus()','filter'=>CHtml::dropDownList('TlaTask[status]',@$tlaTask->status, TlaTask::$processTypes,array('prompt'=>'Select'))),
		array('name' => 'createdUserName','value'=>'empty($data->user_id)?"System":$data->createUser->fname." ".$data->createUser->lname'),
		//array('name' => 'create'),
		//array('name' => 'ets'),
		array('name' => 'ete','type'=>'raw','value'=>'$data->getMyEte(true)'),
		array('header'=>'Customer', 'value'=>'$data->getCustomerName()','filter'=>CHtml::textField('TlaTask[customer]',@$tlaTask->customer)),
		array('header'=>'Invoice','type' => 'raw','value'=>'empty($data->mdata["invoice_id"])?"":"<a href=\"".Yii::app()->createURL("invoice/update", array("id" => $data->mdata["invoice_id"]))."\" class=\"tab_link\" title=\"".$data->mdata["invoice_id"]."\">".$data->mdata["invoice_no"]."</a>"'),
		array('name' => 'tag','value'=>'$data->getTag()','filter'=>CHtml::dropDownList('TlaTask[tag]',@$tlaTask->tag, TlaTask::$tags,array('prompt'=>'Select'))),
		// 'ref',
		// ['name'=>'agent_id','value'=>'$data->shipment->agent_id'],
		// ['name' => 'shipment.consol_no', 'type'=>'raw', 'value' => 'empty($data->shipment->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->shipment->consol_id))."\" class=\"tab_link\" title=\"".@$data->shipment->consol->no."\">".@$data->shipment->consol->no."</a>"','filter'=>CHtml::textField('ImParcel[consol_no]', $cargo->consol_no)],
		// ['name' => 'shipment.consol.awb','filter'=>CHtml::textField('CargoProcess[awb]',@$cargo->awb)],
		// ['name'=>'shipment.cnee.postcode','filter'=>CHtml::textField('CargoProcess[postcode]',@$cargo->postcode)],
		// ['name'=>'shipment.can','filter'=>CHtml::textField('CargoProcess[memo]',@$cargo->memo)],
		// array('name'=>'assignedAgent','filter'=>CHtml::dropDownList('CargoProcess[assignedUser]',@$cargo->assignedUser, @User::getTruckUsers(),array('prompt'=>'Select'))),
		// ['name' => 'status','type'=>'raw', 'value' => '$data->getFullStatus()','filter'=>false],
		// ['name'=>'shipment.status','value' => '$data->shipment->getStatus()','cssClassExpression' => '$data->getShowColor("held")'],
		
		// ['name'=>'note','value'=>'$data->note'],
		// ['header'=>'First Process Time','value' => '$data->getFirstProcessTime()','cssClassExpression' => '$data->getShowColor("last_process_time")'],
		// ['header'=>'Last Process Time','value' => '$data->getLastProcessTime()','cssClassExpression' => '$data->getShowColor("last_process_time")'],
		// ['header'=>'goods available address', 'type' => 'raw','value'=>'$data->shipment->getAvailabelLabel()','filter'=>CHtml::textField('CargoProcess[goodsAvailableAddress]',@$cargo->goodsAvailableAddress)],
		// ['name'=>'shipment.pkg','cssClassExpression' => '$data->getShowColor("pkg")','filter'=>CHtml::textField('CargoProcess[pkg]',@$cargo->pkg),'value'=>'$data->getPackages()'],
		// ['name'=>'plt','type'=>'raw','value'=>'$data->getPltStr()'],
		// ['name'=>'shipment.weight','filter'=>CHtml::textField('CargoProcess[weight]',@$cargo->weight),'value'=>'$data->getWeight()'],
		// ['name' =>'shipment.cbm','value'=>'$data->getTotalCBM()','filter'=>CHtml::textField('CargoProcess[cbm]',@$cargo->cbm)],
		// ['name'=>'shipment.cnee.name','filter'=>CHtml::textField('CargoProcess[cname]',@$cargo->cname)],
		// ['name'=>'shipment.cnee.address','filter'=>CHtml::textField('CargoProcess[address]',@$cargo->address)],
		// ['name'=>'shipment.cnee.state','filter'=>CHtml::textField('CargoProcess[state]',@$cargo->state)],
		// ['header'=>'extra Info','type'=>'raw','value'=>'$data->shipment->getDGWarnings()','filter'=>CHtml::dropDownList('CargoProcess[exInfo]', $cargo->exInfo, $this->t(ImParcel::$exInfos), ['prompt'=>'All'])],
	];
	if($tlaTask->status==TlaTask::CLOSE)
	{
		$columns[] = array('name'=>'tlaTaskUsers.close_time','header' => 'close time','value'=>'$data->tlaTaskUsers[0]->close_time');
		$columns[] = 		['class'=>'oButtonColumn',
			'template'=>'{operate}&nbsp;{Handling Dispute}&nbsp;{Check Si Reconcile}&nbsp;{log}&nbsp;{invoice}',
			'buttons'=>[
				'operate' => [
					'url'=>'Yii::app()->createURL("tlaTask/operation")."?id=".$data->id."&&tab='.$tab.'"',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id', 'data-id' => '$data->id'],
				],
				'Handling Dispute' => [
					'url'=>'$data->getInstance()->getOperationLink()."&&tab='.$tab.'"',
					'imageUrl'=>false,
					'visible'=>'$data->type==DisputeTask::$my_type',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>'$data->getInstance()->getGoToLinkText()', 'title' => '$data->id' , 'data-id' => '$data->id'],
				],
				'Check Si Reconcile' => [
					'url'=>'$data->getInstance()->getOperationLink()."&&tab='.$tab.'"',
					'imageUrl'=>false,
					'visible'=>'$data->type==DisputeSupplierTask::$my_type',
					'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>'$data->getInstance()->getGoToLinkText()', 'title' => '$data->id' , 'data-id' => '$data->id'],
				],
				'log' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("tlaTask/log", ["id" => $data->id])',
					'label' => 'Log'
				],
				'invoice' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => 'Invoice', 'data-win-class' => 'L'],
					'visible' => 'empty($data->mdata["invoice_id"])&&!empty($data->mdata["agent_id"])',
					'url' => 'Yii::app()->createUrl("tlaTask/createInvoice", ["id" => $data->id])',
					'label' => 'Invoice'
				],
			],
		];
	}else
	{
		$columns[] = 		['class'=>'oButtonColumn',
			'template'=>'{operate}&nbsp;{Handling Dispute}&nbsp;{Check Si Reconcile}&nbsp;{close}&nbsp;{log}&nbsp;{invoice}',
			'buttons'=>[
				'operate' => [
					'url'=>'Yii::app()->createURL("tlaTask/operation")."?id=".$data->id."&&tab='.$tab.'"',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id', 'data-id' => '$data->id'],
				],
				'Handling Dispute' => [
					'url'=>'$data->getInstance()->getOperationLink()."&&tab='.$tab.'"',
					'imageUrl'=>false,
					'visible'=>'$data->type==DisputeTask::$my_type',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>'$data->getInstance()->getGoToLinkText()', 'title' => '$data->id' , 'data-id' => '$data->id'],
				],
				'Check Si Reconcile' => [
					'url'=>'$data->getInstance()->getOperationLink()."&&tab='.$tab.'"',
					'imageUrl'=>false,
					'visible'=>'$data->type==DisputeSupplierTask::$my_type',
					'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>'$data->getInstance()->getGoToLinkText()', 'title' => '$data->id' , 'data-id' => '$data->id'],
				],
				'close' => [
					'url'=>'Yii::app()->createURL("tlaTask/close")."?id=".$data->id',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'close_task grid_edit_btn', 'label'=>$this->t('Close'), 'title' => '$data->id' , 'data-id' => '$data->id'],
				],
				'log' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("tlaTask/log", ["id" => $data->id])',
					'label' => 'Log'
				],
				'invoice' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => 'Invoice', 'data-win-class' => 'L'],
					'visible' => 'empty($data->mdata["invoice_id"])&&!empty($data->mdata["agent_id"])',
					'url' => 'Yii::app()->createUrl("tlaTask/createInvoice", ["id" => $data->id])',
					'label' => 'Invoice'
				],
			],
		];
	}
$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id'=>$_GET["tabid"].$tab.'_sub_tla_task_grid',
	'cssFile' => false,
	'dataProvider'=>$tlaTask->search(true, 200,false,true),
	'afterAjaxUpdate'=>'function(){initTaskList'.$user_id.'();}',
	'filter'=>$tlaTask,
	'columns'=>$columns,
]);

?>
<script type="text/javascript">
	function initTaskList<?=$user_id?>(){

		var tab = $('#<?=$_GET["tabid"];?>');
	    var panel=tab.data('panel');

		$(panel).off('change', '.task_order').on('change', '.task_order', function(){
			const tr = $(this).parents('tr');
			$.ajax({
				url: '<?=$this->createUrl("tlaTask/orderChange")?>',
				type: "post",
				data: {"order":$('.task_order', tr).val(),"id":$('.grid_edit_btn', tr).data('id')},
				success: function(r) {
					var tab = $('#<?=$_GET["tabid"];?>');
					$('#<?=$_GET["tabid"].$tab?>_sub_tla_task_grid', tab.data('panel')).yiiGridView('update');
				 },
				error: function(e) {
					console.log(e);
				}
			});
		});

		$('.close_task',panel).on('click',function(event){
			event.preventDefault();
			if(confirm("Are you Confirm to Close The Task?")){
				$.get($(this).attr('href'),function(r){
					console.log(r);
					if(r=='done'||r==' done'){
						myApp.notice("Close!");
						var tab = $('#<?=$_GET["tabid"];?>');
						$('#<?=$_GET["tabid"].$tab?>_sub_tla_task_grid', tab.data('panel')).yiiGridView('update');
					}
				});
			 }
		});

		$.ajax({
			url: '<?=$this->createUrl("tlaTask/getPoints")?>',
			type: "POST",
			data: {"id":$('#ids', panel).attr("value")},
			success: function(r) {
				var tab = $('#<?=$_GET["tabid"];?>');
				$('#totalPoints', tab.data('panel')).html(r);
			 },
			error: function(e) {
				console.log(e);
			}
		});

		$('a.export_search_ds', panel).on('mousedown', function(){
	        var q = $('.filters input, .filters select', panel).serialize();
	        $(this).attr('href', $(this).data('baseurl') + '&' + q);
	    });


	}

	$(function(){
		 initTaskList<?=$user_id?>();
	})

</script>