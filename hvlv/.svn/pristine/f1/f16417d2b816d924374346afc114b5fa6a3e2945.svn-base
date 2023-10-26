<h3><?=$name?></h3>
<?php

$thisData = $tlaTask->search(false, 200,false,true)->data;
$ids = join(',',array_column($thisData,'id'));
echo "<h2>Points of Current Page:<span id='totalPoints{$tlaTask->assignedUser}{$tlaTask->status}'>0</span></h2>";


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

<?php

if($tlaTask->assignedUser==0)
{
	$tabid = $_GET["tabid"].'_sub_IT_tla_task_grid'.$tlaTask->assignedUser;
}else
{
	if($tlaTask->status == TlaTask::CLOSE)
	{
		$tabid = $_GET["tabid"].'_sub_IT_tla_task_grid_close'.$tlaTask->assignedUser;
	}else
	{
		$tabid = $_GET["tabid"].'_sub_IT_tla_task_grid'.$tlaTask->assignedUser;
	}
}

	if($tlaTask->assignedUser==0)
	{
		$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
		'id'=>$tabid,
		'cssFile' => false,
		'dataProvider'=>$tlaTask->search(true, $pageSize,false,true),
		'filter'=>$tlaTask,
		'afterAjaxUpdate'=>'function(){initITTaskDetail999();}',
		'columns'=>[
			array('header' => 'Processing','type'=>'raw', 'value' => '$data->getStatusForList().$data->getPoints()."point"','filter'=>CHtml::label("","label",["id"=>"ids".$tlaTask->assignedUser.$tlaTask->status,"value"=>$ids])),
			array('name' => 'task_no'),
			array('name' => 'type','value'=>'$data->getInstance()->getType()','filter'=>CHtml::dropDownList('TlaTask[type]',@$tlaTask->type, TlaTask::$types,array('prompt'=>'Select'))),
			array('name' => 'comment','type'=>'raw','value'=>'$data->showComment()'),
			array('name' => 'dpt_id', 'value' => 'Org::dptList()[$data->dpt_id]',
					'filter'=>CHtml::dropDownList('TlaTask[dpt_id]', $tlaTask->dpt_id, Org::dptList(), ['prompt'=>$this->t('All')]),),
			array('name' => 'status','type'=>'raw', 'value' => '$data->getStatus()','filter'=>CHtml::dropDownList('TlaTask[status]',@$tlaTask->status, TlaTask::$processTypes,array('prompt'=>'Select'))),
			array('name' => 'createdUserName','value'=>'empty($data->user_id)?"System":$data->createUser->fname." ".$data->createUser->lname'),
			array('name' => 'create'),
			array('name' => 'ets'),
			array('name' => 'ete'),
			array('name' => 'tag','type' => 'raw',"value"=>'CHtml::dropDownList("tag".$data->id, $data->tag, TlaTask::$tags, ["class"=>"task_tag","style"=>"width:60px;"])','filter'=>CHtml::dropDownList('TlaTask[tag]',@$tlaTask->tag, TlaTask::$tags,array('prompt'=>'Select'))),
			["header"=>"assign",'type' => 'raw',"value"=>'CHtml::dropDownList("assignUser".$data->id, 0, User::getITUsers(true), ["class"=>"task_assignuser","style"=>"width:40px;"])',"filter"=>false],

			['class'=>'oButtonColumn',
				'template'=>'{operate}&nbsp;{close}&nbsp;{log}',
				'buttons'=>[
					'operate' => [
						'url'=>'Yii::app()->createURL("tlaTask/operation")."?id=".$data->id',
						'imageUrl'=>false,
						'visible'=>'true',
						'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id', 'data-id' => '$data->id'],
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
				],
			]
		],
	]);
}else
{
	if($tlaTask->status == TlaTask::CLOSE)
	{
			$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
			'id'=>$tabid,
			'cssFile' => false,
			'dataProvider'=>$tlaTask->search(true, $pageSize,false,true,true),
			'filter'=>$tlaTask,
			'afterAjaxUpdate'=>'function(){initITTaskDetail999();}',
			'columns'=>[
				array('header' => 'Processing','type'=>'raw', 'value' => '$data->getStatusForList().$data->getPoints()."point"','filter'=>CHtml::label("","label",["id"=>"ids".$tlaTask->assignedUser.$tlaTask->status,"value"=>$ids])),
				["header"=>"order",'type' => 'raw',"value"=>'CHtml::numberfield("pallet".$data->id, @$data->tlaTaskUsers[0]->task_order,["class"=>"task_order","style"=>"width:40px;"])',"filter"=>false],
				array('name' => 'task_no'),
				array('name' => 'type','value'=>'$data->getInstance()->getType()','filter'=>CHtml::dropDownList('TlaTask[type]',@$tlaTask->type, TlaTask::$types,array('prompt'=>'Select'))),
				array('name' => 'comment','type'=>'raw','value'=>'$data->showComment()'),
				array('name' => 'dpt_id', 'value' => 'Org::dptList()[$data->dpt_id]',
						'filter'=>CHtml::dropDownList('TlaTask[dpt_id]', $tlaTask->dpt_id, Org::dptList(), ['prompt'=>$this->t('All')]),),
				array('name' => 'status','type'=>'raw', 'value' => '$data->getStatus()','filter'=>CHtml::dropDownList('TlaTask[status]',@$tlaTask->status, TlaTask::$processTypes,array('prompt'=>'Select'))),
				array('name' => 'createdUserName','value'=>'empty($data->user_id)?"System":$data->createUser->fname." ".$data->createUser->lname'),
				array('name' => 'create'),
				array('name' => 'ete'),
				array('name'=>'tlaTaskUsers.close_time','header' => 'close time','value'=>'$data->tlaTaskUsers[0]->close_time'),
				array('name' => 'tag','type' => 'raw',"value"=>'CHtml::dropDownList("tag".$data->id, $data->tag, TlaTask::$tags, ["class"=>"task_tag","style"=>"width:60px;"])','filter'=>CHtml::dropDownList('TlaTask[tag]',@$tlaTask->tag, TlaTask::$tags,array('prompt'=>'Select'))),
				["header"=>"assign",'type' => 'raw',"value"=>'CHtml::dropDownList("assignUser".$data->id, null, User::getITUsers(true), ["prompt"=>"select","class"=>"task_assignuser","style"=>"width:40px;"])',"filter"=>false],
				['class'=>'oButtonColumn',
					'template'=>'{operate}&nbsp;{close}&nbsp;{log}',
					'buttons'=>[
						'operate' => [
							'url'=>'Yii::app()->createURL("tlaTask/operation")."?id=".$data->id',
							'imageUrl'=>false,
							'visible'=>'true',
							'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id', 'data-id' => '$data->id'],
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
					],
				]
			],
		]);
	}else
	{
			$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
			'id'=>$tabid,
			'cssFile' => false,
			'dataProvider'=>$tlaTask->search(true, $pageSize,false,true,true),
			'filter'=>$tlaTask,
			'afterAjaxUpdate'=>'function(){initITTaskDetail999();}',
			'columns'=>[
				array('header' => 'Processing','type'=>'raw', 'value' => '$data->getStatusForList().$data->getPoints()."point"','filter'=>CHtml::label("","label",["id"=>"ids".$tlaTask->assignedUser.$tlaTask->status,"value"=>$ids])),
				["header"=>"order",'type' => 'raw',"value"=>'CHtml::numberfield("pallet".$data->id, @$data->tlaTaskUsers[0]->task_order,["class"=>"task_order","style"=>"width:40px;"])',"filter"=>false],
				array('name' => 'task_no'),
				array('name' => 'type','value'=>'$data->getInstance()->getType()','filter'=>CHtml::dropDownList('TlaTask[type]',@$tlaTask->type, TlaTask::$types,array('prompt'=>'Select'))),
				array('name' => 'comment','type'=>'raw','value'=>'$data->showComment()'),
				array('name' => 'dpt_id', 'value' => 'Org::dptList()[$data->dpt_id]',
						'filter'=>CHtml::dropDownList('TlaTask[dpt_id]', $tlaTask->dpt_id, Org::dptList(), ['prompt'=>$this->t('All')]),),
				array('name' => 'status','type'=>'raw', 'value' => '$data->getStatus()','filter'=>CHtml::dropDownList('TlaTask[status]',@$tlaTask->status, TlaTask::$processTypes,array('prompt'=>'Select'))),
				array('name' => 'createdUserName','value'=>'empty($data->user_id)?"System":$data->createUser->fname." ".$data->createUser->lname'),
				array('name' => 'create'),
				array('name' => 'ets'),
				array('name' => 'ete'),
				["header"=>"assign",'type' => 'raw',"value"=>'CHtml::dropDownList("assignUser".$data->id, null, User::getITUsers(true), ["prompt"=>"select","class"=>"task_assignuser","style"=>"width:40px;"])',"filter"=>false],
				array('name' => 'tag','type' => 'raw',"value"=>'CHtml::dropDownList("tag".$data->id, $data->tag, TlaTask::$tags, ["class"=>"task_tag","style"=>"width:40px;"])','filter'=>CHtml::dropDownList('TlaTask[tag]',@$tlaTask->tag, TlaTask::$tags,array('prompt'=>'Select'))),
				['class'=>'oButtonColumn',
					'template'=>'{operate}&nbsp;{close}&nbsp;{log}',
					'buttons'=>[
						'operate' => [
							'url'=>'Yii::app()->createURL("tlaTask/operation")."?id=".$data->id',
							'imageUrl'=>false,
							'visible'=>'true',
							'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id', 'data-id' => '$data->id'],
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
					],
				]
			],
		]);
	}
}

?>
<script type="text/javascript">
	function initITTaskDetail999(){
		var tab = $('#<?=$_GET["tabid"];?>');
		
		tab.unbind('reload_tla_task_grid').bind('reload_tla_task_grid', function(){
                    $('#<?=$tabid?>', tab.data('panel')).yiiGridView('update');
                    return false;
    	});

	    var panel=tab.data('panel');
		$(panel).off('change', '.task_assignuser').on('change', '.task_assignuser', function(){
			const tr = $(this).parents('tr');
			$.ajax({
				url: '<?=$this->createUrl("tlaTask/assignUser")?>',
				type: "post",
				data: {"user_id":$('.task_assignuser', tr).val(),"id":$('.grid_edit_btn', tr).data('id')},
				success: function(r) {
					tab.trigger('reload_tla_task_grid');
					myApp.notice("Assign Done");
				 },
				error: function(e) {
					console.log(e);
				}
			});
		});

		$(panel).off('change', '.task_tag').on('change', '.task_tag', function(){
			const tr = $(this).parents('tr');
			$.ajax({
				url: '<?=$this->createUrl("tlaTask/changeTag")?>',
				type: "post",
				data: {"tag":$('.task_tag', tr).val(),"id":$('.grid_edit_btn', tr).data('id')},
				success: function(r) {
					if(r=="done")
					{
						tab.trigger('reload_tla_task_grid');
						myApp.notice("Tag Done");
					}else
					{
						myApp.alert(r, false);
					}
				 },
				error: function(e) {
					console.log(e);
				}
			});
		});

		$(panel).off('change', '.task_order').on('change', '.task_order', function(){
			const tr = $(this).parents('tr');
			$.ajax({
				url: '<?=$this->createUrl("tlaTask/orderChange")?>',
				type: "post",
				data: {"order":$('.task_order', tr).val(),"id":$('.grid_edit_btn', tr).data('id')},
				success: function(r) {
					tab.trigger('reload_tla_task_grid');
				 },
				error: function(e) {
					console.log(e);
				}
			});
		});

		$.ajax({
			url: '<?=$this->createUrl("tlaTask/getPoints")?>',
			type: "POST",
			data: {"id":$('#ids<?=$tlaTask->assignedUser.$tlaTask->status?>', panel).attr("value")},
			success: function(r) {
				$('#totalPoints<?=$tlaTask->assignedUser.$tlaTask->status?>', panel).html(r);
			 },
			error: function(e) {
				console.log(e);
			}
		});


		$('.close_task',panel).on('click',function(event){
			event.preventDefault();
			if(confirm("Are you Confirm to Close The Task?")){
				$.get($(this).attr('href'),function(r){
					console.log(r);
					if(r=='done'||r==' done'){
						myApp.notice("Close!");
						tab.trigger('reload_tla_task_grid');
					}
				});
			 }
		});


	}

	$(function(){
		initITTaskDetail999();
	})

</script>