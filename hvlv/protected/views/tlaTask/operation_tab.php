<h3>Operation <?=$model->task_no?></h3>
<div class="form">
<div class="row">
	<div class="col" style="margin-right: 25px">
		<div class="row rowcol-left">
				<?php echo CHtml::label('status','status'); ?>
				<?php echo CHtml::dropDownList('tla_task_status',@$model->status, TlaTask::$processTypes,array('prompt'=>'Select','disabled' => 'disabled')); ?>
				
				<div style="background-position:-240px -416px" class="icon"></div>
				 <?php echo CHtml::button('Update', array('class' => 'updateStatus'));?>

		</div>

	</div>
</div>
<br>
<div class="form">
<?php 
	$id = $model->id;
	$url = $this->createUrl('tlaTask/update');
	if(isset($ids) && $ids != "")
	{
		$id = $ids;
	}
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'tla_task_acr_form',
	'enableAjaxValidation'=>false,
	'action'=> $url."?id=".$id)
	);
?>
	<div class="row buttons">
		<div class="row">
			<?php echo CHtml::label('Assign User','Assign User'); ?>
			<?php echo CHtml::hiddenField('user_id');
				$acname = empty($_GET["tabid"])? 'owner_ac' : $_GET["tabid"].'_owner_ac';
				$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => $acname,
					'sourceUrl' => array('org/opSuggest'),
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
					),
			));
			?>

			<?php echo $form->error($model,'to_id'); ?>
		</div>
	</div>

	<div class="row rowcol">
			<div>
				<?php echo $form->labelEx($model, 'agent_id', array('required' => 'required')); ?> 
			</div>
			<input class="width_item_input" type="hidden" id='TlaTask[agent_id]' value='<?php echo @$model->mdata['agent_id'] ?>' name='TlaTask[agent_id]'> 
			<?php
			$OrgName = !empty($model->mdata['agent_id'])?Org::model()->findByPk($model->mdata['agent_id'])->name:"";
			$acname = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => empty($OrgName) ? '' : $OrgName,
				'options' => array(
					'showAnim' => 'fold',
					'minLength' => 2,
					'delay' => 200,
					'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
					'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'class' => 'required',
					'size' => '20',
				),
			));
			?> 
			<a>eg: "Top Logistics".</a>
		</div>
	
	<div class="row">
		<?php echo CHtml::label('Comment','comment'); ?>
		<?php echo CHtml::textArea('comment',@$model->comment,array('rows'=>10, 'cols' => 60)); ?>
	</div>

	 <div class="row">
             <?php echo $form->labelEx($model,'ets'); ?>
              <?php echo $form->textField($model,'ets', array('size' => 20,'class'=>"datetime_input",'id'=>'ets_jqmw_'.$_GET['tabid'])); ?>
               <?php echo $form->labelEx($model,'ete'); ?>
              <?php
              	if($model->user_id==User::currentUserID())
              	{
              		 echo $form->textField($model,'ete', array('size' => 20,'class'=>"datetime_input",'id'=>'ete_jqmw_'.$_GET['tabid']));
              	}else
              	{
              		 echo $form->textField($model,'ete', array('size' => 20,'class'=>"datetime_input",'id'=>'ete_jqmw_'.$_GET['tabid'],'disabled'=>'disabled'));
              	}
               ?>
     </div>
     <?php if(in_array($model->status,[TlaTask::CLOSE])&&$model->user_id==User::currentUserID()):?>
		<div class="row buttons">
			 <?php  echo CHtml::submitButton('Confirm Closed Task',array('class'=>'confirm_task'));?>
		</div>
	<?php endif;?>

	<?php if(in_array($model->status,[TlaTask::REJECT])):?>
		<div class="row buttons">
			 <?php  echo CHtml::submitButton('assign_again',array('class'=>'assign_again'));?>
		</div>
	<?php else:?>
		<div class="row buttons">
		<?php  echo CHtml::submitButton('Save',array('class'=>'update'));?>
	</div>
	<?php endif;?>

	<?php if(!in_array($model->type, [DisputeTask::$my_type])):?>
		<div class="row">
			<?php echo CHtml::label('Reject Reason','Reject Reason'); ?>
			<?php echo CHtml::textArea('reject_reason',empty($model->mdata['reject_reason'])?"Please input the reject reason. For example, I can't meet the deadline, please delay it to be xxxx-xx-xx. Please don't put meaningless reasons, the task creator will mark the task after it is finished.":$model->mdata['reject_reason'],array('rows'=>3, 'cols' => 60)); ?>
		</div>
		<?php if(!in_array($model->status,[TlaTask::REJECT,TlaTask::CLOSE])):?>
		<div class="row buttons">
			 &nbsp;&nbsp;&nbsp;&nbsp;<?php  echo CHtml::submitButton('Reject Task',array('class'=>'reject_task'));?>
		</div>
		<?php endif;?>
		<?php if(in_array($model->status,[TlaTask::CLOSE])&&$model->user_id==User::currentUserID()):?>
			<div class="row buttons">
				 <?php  echo CHtml::submitButton('Reject Closed Task',array('class'=>'reject_closed_task'));?>
			</div>
		<?php endif;?>
	<?php endif;?>


	<div class="row buttons">
		<?php
			$fr = new FileRepo('search');
			$fr->unsetAttributes();
			$fr->theTypes[] = FileRepo::TLATASKFILETYPE;
			$fr->fid = $model->id;
			$fr->status = [FileRepo::PENDING,FileRepo::ACTIVE];
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
						'url' => $this->createUrl('tlaTask/uploadTaskFile/'.$pphash),
						'max_file_size' => Yii::app()->params['maxFileSize'],
						'unique_names' => true,
						'file_list_height' => 60,
						'visible_header' => false,
						'filters' => [
							['title' => Yii::t('app', 'JPG, PDF, Word, Excel, zip files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,png,txt,csv,zip'],
						],
						//'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
						'language' => Yii::app()->language,
						'max_file_number' => 2,
						'autostart' => false,
						'jquery_ui' => false,
						'reset_after_upload' => true,
					],
					'callbacks' => [
						'FileUploaded' => 'function(up,file,response){alert("Upload Task File Success");$("#'.$_GET["tabid"].'").trigger("reload_excofile_grid");$("#'.$_GET["tabid"].'").trigger("reload_excofile_grid_file");}',
					],
					'id' => $_GET['tabid'].'_excofile_uploader_1',
				]);

				?>
	</div>		


<script type="text/javascript">
	$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tab = $('#<?=$_GET["tabid"];?>');
			var panel = $('#<?=$_GET["tabid"];?>').data('panel');

			tab.unbind('reload_tla_task_grid').bind('reload_tla_task_grid', function(){
				$('#<?=$_GET["tabid"];?><?=@$tab?>_sub_tla_task_grid', tab.data('panel')).yiiGridView('update');
				return false;
			});

			tab.unbind('reload_excofile_grid').bind('reload_excofile_grid', function(){
				$('#<?=$_GET["tabid"];?>_excofile-grid', win).yiiGridView('update');
				return false;
			});

			tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
				$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
			});

			win.unbind('reload_cargo_invoice_grid').bind('reload_cargo_invoice_grid',function(){
					$('#cargo_invoice_grid_<?=$_GET['tabid']?>',win).yiiGridView('update');
			});

			 $('.task_close',win).on('click',function(){
				if( confirm('Are you sure to Collect Info Done')){
						$.get('<?=$this->createUrl("cargoProcess/collectInfoDone")."?id=".$id?>',function(r){
								if(r=='done'){
										save();	
								 }else{
										myApp.alert(r, false);   
							 }
								tab.trigger('reload_tla_task_grid');
					 });
				
				}
			});

			
			$('#tla_task_status', win).next().on('dblclick', function(){
				if(window.confirm('Are you sure to override status?')){
					$(this).prev().attr('disabled', false);
				}
			});

			 $('.update',win).on('click',function(){
				if( confirm('Are you sure to save?')){	
					save();	
				}
				return false;
			});

			$('.reject_task',win).on('click',function(){
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
							            url: '<?=$this->createUrl("tlaTask/rejectTask")?>',
							            type: "post",
							            data: {"id":"<?=$id?>"},
							            success: function(r) {
							                if(r=='done')
							                 {
												myApp.notice('Done', 5000);
												$('.popCancel',win).click();
											 }else
											 {
												myApp.alert(r, false);   
								             }
								             tab.trigger('reload_tla_task_grid');
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

			$('.reject_closed_task',win).on('click',function(){
				if( confirm('Are you sure to reject this closed Task?')){	
					var form = new FormData(document.getElementById("tla_task_acr_form"));
					 $.ajax({
					            url: '<?=$url."?id=".$id?>',
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                
					            	$.ajax({
							            url: '<?=$this->createUrl("tlaTask/rejectClosedTask")?>',
							            type: "post",
							            data: {"id":"<?=$id?>"},
							            success: function(r) {
							                if(r=='done')
							                 {
												myApp.notice('Done', 5000);
												$('.popCancel',win).click();
											 }else
											 {
												myApp.alert(r, false);   
								             }
								             tab.trigger('reload_my_assigned_closed_tla_task_grid');
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

			$('.assign_again',win).on('click',function(){
				if( confirm('Are you sure to assign this Task?')){
					var form = new FormData(document.getElementById("tla_task_acr_form"));
					 $.ajax({
					            url: '<?=$url."?id=".$id?>',
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                var userId = $('#user_id',win).val();
					            	$.ajax({
							            url: '<?=$this->createUrl("tlaTask/assignTaskAgain")?>',
							            type: "post",
							            data: {"id":"<?=$id?>","user_id":userId},
							            success: function(r) {
							                if(r=='done')
							                 {
												myApp.notice('Done', 5000);
												$('.popCancel',win).click();
											 }else
											 {
												myApp.alert(r, false);   
								             }
								             tab.trigger('reload_tla_task_grid');
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

			 $('.confirm_task',win).on('click',function(){
				if( confirm('Are you sure to confirm this Task')){
						$.get('<?=$this->createUrl("tlaTask/confirm")."?id=".$id?>',function(r){
								if(r=='done'){
										myApp.notice('Done', 5000);
										$('.popCancel',win).click();
								 }else{
										myApp.alert(r, false);   
							 	}
								tab.trigger('reload_my_assigned_closed_tla_task_grid');
					 });
				
				}
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
					                if(r=='done')
					                 {
										myApp.notice('Done', 5000);
									 }else
									 {
										myApp.alert(r, false);   
						             }
						             tab.trigger('reload_tla_task_grid');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });	
			}

			 $('.updateStatus',win).on('click',function(){
				if( confirm('Are you sure to update status?')){
					var form = new FormData(document.getElementById("tla_task_acr_form"));
					 $.ajax({
					            url: '<?=$this->createUrl("tlaTask/updateStatus")."?id=".$id?>'+'&&status='+$('#tla_task_status',win).val(),
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                if(r=='done')
					                 {
										myApp.notice('Done', 5000);
									 }else
									 {
										myApp.alert(r, false);   
						             }
						             tab.trigger('reload_tla_task_grid');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });			
				}
				return false;
			});


			 $('.assignUser',win).on('click',function(){
				if( confirm('Are you sure to assign user?')){
					var form = new FormData(document.getElementById("tla_task_acr_form"));
					 $.ajax({
					            url: '<?=$this->createUrl("tlaTask/assignUser")."?id=".$id?>'+'&&user_id='+$('#assigned_user',win).val(),
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                if(r=='done')
					                 {
										myApp.notice('Done', 5000);
									 }else
									 {
										myApp.alert(r, false);   
						             }
						             tab.trigger('reload_tla_task_grid');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });			
				}
				return false;
			});

			$('#reject_reason',win).on('click',function(){
				if($(this).val()=="Please input the reject reason. For example, I can't meet the deadline, please delay it to be xxxx-xx-xx. Please don't put meaningless reasons, the task creator will mark the task after it is finished.")
				{
					$(this).val("");
				}
				return false;
			});


	});
</script>


<?php $this->endWidget();?>
		
