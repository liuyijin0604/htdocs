<style>
    .cloumn_red{
        background-color:pink;
    }  
    .column_direct{
        color: green;
        font-weight: bold;
    }
</style>

<?php

	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id'=>$_GET["tabid"].'_my_assigned_closed_sub_tla_task_grid',
	'cssFile' => false,
	'dataProvider'=>$tlaTask->search(true, 30,false,true),
	'filter'=>$tlaTask,
	'afterAjaxUpdate'=>'function(){initAccttTaskList'.$user_id.'();}',
	'columns'=>[
		array('header' => 'Processing','type'=>'raw', 'value' => '$data->getStatusForList().CHtml::hiddenField("tab","'.$tab.'")'),	
		array('name' => 'task_no'),
		array('name' => 'type','value'=>'$data->getInstance()->getType()','filter'=>CHtml::dropDownList('TlaTask[type]',@$tlaTask->type, TlaTask::$types,array('prompt'=>'Select'))),
		array('header' => 'Task Content','type'=>'raw','value'=>'$data->getInstance()->getTaskContent()'),
		array('name' => 'comment','type'=>'raw'),
		array('name' => 'dpt_id', 'value' => 'Org::dptList()[$data->dpt_id]',
				'filter'=>CHtml::dropDownList('TlaTask[dpt_id]', $tlaTask->dpt_id, Org::dptList(), ['prompt'=>$this->t('All')]),),
		array('name' => 'status','type'=>'raw', 'value' => '$data->getStatus()','filter'=>CHtml::dropDownList('TlaTask[status]',@$tlaTask->status, TlaTask::$processTypes,array('prompt'=>'Select'))),
		array('name' => 'assignedUserName','value'=>'$data->getAssignUsers()'),
		array('name' => 'ete'),
		array('name' => 'create'),
		array('name'=>'tlaTaskUsers.close_time','header' => 'close time','value'=>'@$data->tlaTaskUsers[0]->close_time'),
		array('name' => 'tag','value'=>'$data->getTag()','filter'=>CHtml::dropDownList('TlaTask[tag]',@$tlaTask->tag, TlaTask::$tags,array('prompt'=>'Select'))),
		['class'=>'oButtonColumn',
			'template'=>'{operate}&nbsp;{confirm}&nbsp;{Handling Dispute}&nbsp;{Check Si Reconcile}&nbsp;{log}',
			'buttons'=>[
				'operate' => [
					'url'=>'Yii::app()->createURL("tlaTask/operation")."?id=".$data->id',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
				],
				
				'confirm' => [
					'url'=>'Yii::app()->createURL("tlaTask/confirm")."?id=".$data->id',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'confirm_task grid_edit_btn', 'label'=>$this->t('Confirm'), 'title' => '$data->id' , 'data-id' => '$data->id'],
				],
				'Handling Dispute' => [
					'url'=>'$data->getInstance()->getOperationLink()',
					'imageUrl'=>false,
					'visible'=>'$data->type==DisputeTask::$my_type',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>'$data->getInstance()->getGoToLinkText()', 'title' => '$data->id' , 'data-id' => '$data->id'],
				],
				'Check Si Reconcile' => [
					'url'=>'$data->getInstance()->getOperationLink()',
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
			],
		]
	],
]);

?>