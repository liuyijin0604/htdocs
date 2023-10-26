<h3>Task-<?=$model->task_no." ".$model->getStatus()?></h3>
<div class="form">
<br>
<div class="form">
<?php 
	$id = $model->id;
	$url = $this->createUrl('message/update');
	if(isset($ids) && $ids != "")
	{
		$id = $ids;
	}
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'tla_task_acr_form',
	'enableAjaxValidation'=>false,
	'action'=> $url."?id=".$id));
?>
	<div class="row">
		<?php echo CHtml::label('Assign User','Assign User'); ?>
		<?php echo CHtml::hiddenField('user_id');
			$acname = empty($_GET["tabid"])? 'owner_ac' : $_GET["tabid"].'_owner_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('message/opSuggest'),
				'value' => '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val(""); return false; }',
				),
				'htmlOptions' => array(
					'size' => '50',
					'class'=>'form-control'
				),
		));
		?>

		<?php echo $form->error($model,'to_id'); ?>
	</div>
	
	<div class="row">
		<?php echo CHtml::label('Comment','comment'); ?>
		<?php echo CHtml::textArea('comment',@$model->comment,array('rows'=>2, 'cols' => 60,'class'=>'form-control')); ?>
		<?php echo CHtml::hiddenField('id',@$model->id); ?>
	</div>

	<div class="row buttons">
		<?php  echo CHtml::submitButton('Save Only',array('class'=>'update form-control'));?>
	</div>
	<br>
	<div class="row buttons">
		<?php  echo CHtml::submitButton('Close And Done',array('class'=>'close_task form-control'));?>
	</div>

	<br>
	<br>
	<div class="row">
			<?php echo CHtml::label('Reject Reason','Reject Reason'); ?>
			<?php echo CHtml::textArea('reject_reason',empty($model->mdata['reject_reason'])?"Please input the reject reason. For example, I can't meet the deadline, please delay it to be xxxx-xx-xx. Please don't put meaningless reasons, the task creator will mark the task after it is finished.":$model->mdata['reject_reason'],array('rows'=>3, 'cols' => 60,'class'=>'form-control')); ?>
	</div>
	<?php if(!in_array($model->status,[TlaTask::REJECT,TlaTask::CLOSE])):?>
	<div class="row buttons">
		 &nbsp;&nbsp;&nbsp;&nbsp;<?php  echo CHtml::submitButton('Reject Task',array('class'=>'reject_task form-control'));?>
	</div>
	<?php endif;?>

	<br>
	<br>
	<h3><?=CHtml::label($this->t('Files'),$this->t('Files'))?></h3>
	<div class="row buttons">
		<?php
			$fr = new FileRepo('search');
			$fr->unsetAttributes();
			$fr->theTypes[] = FileRepo::TLATASKFILETYPE;
			$fr->fid = $model->id;
			$mf = Acl::hasAccess('B:org/manageFile');
			$this->widget('zii.widgets.grid.CGridView', [
				'id'=>$_GET['tabid'].'_excofile-grid',
				'cssFile' => false,
				'summaryText'=>'',
				'dataProvider'=> $fr->search(),
				'filter'=>$fr,
				'columns'=>[
					['name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'],
					[
						'name'=>'size',
						'value'=>'$data->formatSize()',
						'filter' => false,
					],
					'date',
					'type',
					['name' => 'status', 'type'=>'raw', 'value' => '$data->getStatus()',
						'filter'=>CHtml::dropDownList('FileRepo[status]', $fr->status, FileRepo::$states, ['prompt'=>$this->t('All')]) ],
					[
						'class'=>'oButtonColumn',
						'template'=>'{update}',
						'buttons'=>[
							'update' => [
								'url'=>'Yii::app()->createUrl("imParcel/fileUpdate",array("id"=>$data->id))',
								'imageUrl'=>false,
								'visible'=>'true',
								'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->name'],
							],
						],
					],
				],
			]);
			?>
	</div>
	
	<?php //if(($model->status==CargoProcess::WAITINGDELIVERY ||$model->status==CargoProcess::WAITINGAGENTDELIVERY||($model->status==CargoProcess::AGENTDELIVERYDONE&&User::checkIsNotTruckUser()))&& Acl::hasAccess("C:cargoProcess/uploadPOD")&&$model->type!=CargoProcess::PICKUP_CARGO):?>
	<div class="row buttons">

			 <?php
			 	 $pphash = "";
				echo '<br /><h3>', CHtml::label($this->t('Upload Task Files'), '</h3>uploader');
				$pphash = FileRepo::uploadHash($model, FileRepo::TLATASKFILETYPE);

				$this->widget('application.extensions.plupload.PluploadWidget', [
					'config' => [
						'url' => $this->createUrl('message/uploadTaskFile/'.$pphash),
						'max_file_size' => Yii::app()->params['maxFileSize'],
						'unique_names' => true,
						'file_list_height' => 60,
						'visible_header' => false,
						'filters' => [
							['title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,png,txt'],
						],
						//'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
						'language' => Yii::app()->language,
						'max_file_number' => 2,
						'autostart' => false,
						'jquery_ui' => false,
						'reset_after_upload' => true,
					],
					'callbacks' => [
						'FileUploaded' => 'function(up,file,response){alert("Upload Task File Success");$("#'.$_GET["tabid"].'_excofile-grid").yiiGridView("update");}',
					],
					'id' => $_GET['tabid'].'_excofile_uploader_1',
				]);

				?>
	</div>		


<script type="text/javascript">
	$(function(){
			
			 $('.task_close').on('click',function(){
				if( confirm('Are you sure to Collect Info Done')){
						$.get('<?=$this->createUrl("cargoProcess/collectInfoDone")."?id=".$id?>',function(r){
								if(r=='done'||r==' done'){
										save();	
								 }else{
										$('#notifc').notify({message: {html: 'Failed'}, type: 'danger'}).show();  
							 }
					 });
				
				}
			});

			
			$('#tla_task_status').next().on('dblclick', function(){
				if(window.confirm('Are you sure to override status?')){
					$(this).prev().attr('disabled', false);
				}
			});

			 $('.update').on('click',function(){
				if( confirm('Are you sure to save?')){	
					save();	
				}
				return false;
			});
			 
			function save()
			{
					var form = new FormData(document.getElementById("tla_task_acr_form"));
					 $.ajax({
					            url: '<?=$url."?id=".$id?>',
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                if(r=='done'||r==' done')
					                 {
										$('#notifc').notify({message: {html: 'Done'}}).show();
									 }else
									 {
										$('#notifc').notify({message: {html: 'Failed'}, type: 'danger'}).show();  
						             }
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });	
			}


			$('.reject_task').on('click',function(){
				if( confirm('Are you sure to reject this Task?')){	
					var form = new FormData(document.getElementById("tla_task_acr_form"));
					 $.ajax({
					            url: '<?=$url."?id=".$id?>',
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                
					            	$.ajax({
							            url: '<?=$this->createUrl("message/rejectTask")?>',
							            type: "post",
							            data: {"id":"<?=$id?>"},
							            success: function(r) {
							                if(r=='done')
							                 {
												$('#notifc').notify({message: {html: 'Done'}}).show();
											 }else
											 {
												$('#notifc').notify({message: {html: r}, type: 'danger'}).show();  
								             }
								         },
							            error: function(e) {
							                console.log(e);
							            }
							        });
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });
				}
				return false;
			});

			$('.close_task').on('click',function(){
				if( confirm('Are you sure to close this Task?')){	
					var form = new FormData(document.getElementById("tla_task_acr_form"));
					 $.ajax({
					            url: '<?=$url."?id=".$id?>',
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                
					            	$.ajax({
							            url: '<?=$this->createUrl("message/closeTask")?>',
							            type: "post",
							            data: {"id":"<?=$id?>"},
							            success: function(r) {
							                if(r=='done')
							                 {
												$('#notifc').notify({message: {html: 'Done'}}).show();
											 }else
											 {
												$('#notifc').notify({message: {html: 'Failed'}, type: 'danger'}).show();  
								             }
								         },
							            error: function(e) {
							                console.log(e);
							            }
							        });
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });
				}
				return false;
			});


			 $('.updateStatus').on('click',function(){
				if( confirm('Are you sure to update status?')){
					var form = new FormData(document.getElementById("tla_task_acr_form"));
					 $.ajax({
					            url: '<?=$this->createUrl("message/updateStatus")."?id=".$id?>'+'&&status='+$('#tla_task_status').val(),
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                if(r=='done'||r==' done')
					                {
										$('#notifc').notify({message: {html: 'Done'}}).show();
									}else
									{
										$('#notifc').notify({message: {html: 'Failed'}, type: 'danger'}).show();  
						            }
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });			
				}
				return false;
			});


			 $('.assignUser').on('click',function(){
				if( confirm('Are you sure to assign user?')){
					var form = new FormData(document.getElementById("tla_task_acr_form"));
					 $.ajax({
					            url: '<?=$this->createUrl("message/assignUser")."?id=".$id?>'+'&&user_id='+$('#assigned_user').val(),
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                if(r=='done'||r==' done')
					                 {
					                 	$('#notifc').notify({message: {html: 'Done'}}).show();  
									 }else
									 {
										$('#notifc').notify({message: {html: 'Failed'}, type: 'danger'}).show();  
						             }
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });			
				}
				return false;
			});


	});
</script>


<?php $this->endWidget();?>
		
