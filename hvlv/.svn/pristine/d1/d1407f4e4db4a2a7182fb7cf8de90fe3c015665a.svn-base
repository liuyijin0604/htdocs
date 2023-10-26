<h3>Manage Dispute Task Op</h3>

<div style="right: 200px;position: absolute;">
      <a class="jqm_link" href="<?=$this->createUrl('tlaTask/createDisputeType');?>" title="Create Dispute Type"><div class="icon" style="background-position:-16px -0px"></div>Create Dispute Type</a>
</div>


<?php

$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
			'id'=>$_GET["tabid"]."_dispute_task_manage",
			'cssFile' => false,
			'dataProvider'=>$model->search(false),
			'filter'=>$model,
			'afterAjaxUpdate'=>'function(){initITTaskDetail999();}',
			'columns'=>[
				array('name' => 'content'),
				array('name' => 'dpt_id','header' => 'Warehouse','type'=>'raw','value'=>'@$data->warehouse->name','filter'=>CHtml::dropDownList(get_class($model).'[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), ['prompt'=>$this->t('All')])),
				array('name' => 'agent_id','header' => 'Agent','type'=>'raw','value'=>'@$data->agent->name'),
				array('name' => 'invoice_type','value'=>'@$data->getInvoiceType()'),
				array('name' => 'shipment_type','value'=>'@$data->getShipmentType()'),
				["header"=>"assign",'type' => 'raw',"value"=>'CHtml::dropDownList("assignUser".$data->id, @$data->assignUser->user_id, ImportsMail::$importscs_list_id, ["prompt"=>"select","class"=>"task_assignuser"])',"filter"=>false],
				['class'=>'oButtonColumn',
					'template'=>'{delete}',
					'buttons'=>[
						'delete' => [
							'url'=>'Yii::app()->createURL("tlaTask/deleteAssignDisputeTaskUser")."?id=".$data->id',
							'imageUrl'=>false,
							'visible'=>'true',
							'options' => ['class' => 'delete_type grid_edit_btn', 'label'=>$this->t('delete'), 'title' => '$data->id' , 'data-id' => '$data->id'],
						]
					],
				]
			],
		]);

?>
<script type="text/javascript">
	function initDisputeOpManage(){
		var tab = $('#<?=$_GET["tabid"];?>');
		

	    var panel=tab.data('panel');
		$(panel).off('change', '.task_assignuser').on('change', '.task_assignuser', function(){
			const tr = $(this).parents('tr');
			$.ajax({
				url: '<?=$this->createUrl("tlaTask/assignDisputeTaskUser")?>',
				type: "post",
				data: {"user_id":$('.task_assignuser', tr).val(),"id":$('.grid_edit_btn', tr).data('id')},
				success: function(r) {
					myApp.notice("Assign Done");
				 },
				error: function(e) {
					console.log(e);
				}
			});
		});

		$('.delete_type',panel).on('click',function(event){
			event.preventDefault();
			if(confirm("Are you Confirm to delete this type?")){
				$.get($(this).attr('href'),function(r){
					if(r=='done'||r==' done'){
						myApp.notice("Deleted!");
						var tab = $('#<?=$_GET["tabid"];?>');
						$('#<?=$_GET["tabid"]?>_dispute_task_manage', tab.data('panel')).yiiGridView('update');
					}
				});
			 }
			 return false;
		});

	}

	$(function(){
		initDisputeOpManage();
	})

</script>