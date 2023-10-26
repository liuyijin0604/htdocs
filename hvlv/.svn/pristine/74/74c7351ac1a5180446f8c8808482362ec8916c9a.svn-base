<br>
<div class="form">
<?php 
$url = $this->createUrl('tlaTask/createTlaTask');
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'add_tla_task_form',
	'enableAjaxValidation'=>false,
	'action'=> $url)
	);
	$model->type = 20;
	$model->dpt_id = 106;
?>
		<div class="row buttons" id="owner_ac">
			<div class="row">
				<?php echo CHtml::label('Assign User','Assign User', array('required' => 'required')); ?>
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
			 	<?php echo CHtml::hiddenField('noAssign',0); ?>
				<?php echo $form->labelEx($model,'type'); ?>
				 <?php echo $form->dropDownList($model,'type', TlaTask::$typesForCreate,array('prompt'=>'Select')); ?>
		</div>
		<?php if(Acl::hasAccess('B:tlaTask/createImportantTask')):?>
			<div class="row rowcol">
					<?php echo $form->labelEx($model,'tag'); ?>
					 <?php echo $form->dropDownList($model,'tag', TlaTask::$tags,array('prompt'=>'Select')); ?>
			</div>
		<?php else:?>
			<div class="row rowcol">
				<?php echo $form->labelEx($model,'tag'); ?>
				 <?php echo $form->dropDownList($model,'tag', TlaTask::$tags2,array('prompt'=>'Select')); ?>
			</div>
		<?php endif;?>

		<div class="row">
			<div class="row rowcol">
				<?php echo CHtml::label('Re-occuring','Re-occuring'); ?>
				 <?php echo CHtml::dropDownList('mdata[reoccuring]','',TlaTask::$reoccuringList,array('prompt'=>'None')); ?>
			</div>
			<div id = "reoccuring_div" style="display: none">
				<div class="row" >
					<div class="row rowcol">
		                <?php echo  CHtml::label('Days to finish', 'Days to finish' , array('required' => 'required')); ?>
		                 <?php 
		                 	echo CHtml::numberField('mdata[reoccuring_deadline_days]',0);
		                 ?>
		        	</div>
	        	</div>
				<div class="row" >
					<div class="row rowcol">
		                <?php echo  CHtml::label('reoccuring start date', 'reoccuring start date', array('required' => 'required')); ?>
		                 <?php 
		                 echo CHtml::textField('mdata[reoccuring_start_date]','', array('size' => 20,'class'=>"date_input")); ?>
		        	</div>

		        	<div class="row rowcol">
		                <?php echo  CHtml::label('reoccuring end date', 'reoccuring end date', array('required' => 'required')); ?>
		                 <?php 
		                 echo CHtml::textField('mdata[reoccuring_end_date]','', array('size' => 20,'class'=>"date_input")); ?>
		        	</div>
		        	<div class="row rowcol">
		        	<span>OR</span>
		        	</div>
		        	<div class="row rowcol">
		                <?php echo  CHtml::label('run times', 'run times', array('required' => 'required')); ?>
		                 <?php 
		                 	echo CHtml::numberField('mdata[reoccuring_run]','');
		                 ?>
		        	</div>
	        	</div>

			</div>
		</div>

		<div class="row rowcol rowleft">
				<?php echo $form->labelEx($model,'dpt_id',array('required' => 'required')); ?>
				 <?php echo $form->dropDownList($model,'dpt_id', Org::dptList(),array('prompt'=>'Select')); ?>
		</div>

		<div class="row rowcol">
			<div>
				<?php echo $form->labelEx($model, 'Customer', array('required' => 'required')); ?> 
			</div>
			<input class="width_item_input" type="hidden" id='TlaTask[agent_id]' value='' name='TlaTask[agent_id]'> 
			<?php
			$acname = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => '',
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
      
        <div class="row rowcol rowleft">
                <?php echo $form->labelEx($model,'comment'); ?>
                 <?php echo $form->textArea($model,'comment',array('value'=>'Note：If the task will be related to Invoice, Please list the details as backup.','cols'=>60, 'rows' => 10)); ?>
				 
        </div>

<!--         <div class="row">
                <?php echo $form->labelEx($model,'ets'); ?>
                 <?php echo $form->textField($model,'ets', array('size' => 20,'class'=>"datetime_input",'id'=>'ets_jqmw_'.$_GET['tabid'])); ?>
        </div> -->

        <div class="row" id="ete_div">
                <?php echo $form->labelEx($model,'ete', array('required' => 'required')); ?>
                 <?php 
                 if(date("H")<17)
                 {
                 	$model->ete=date("Y-m-d 23:59:00");
                 }else
                 {
                 	$model->ete=HolidayHelper::getNextDay(date("Y-m-d"))." 23:59:00";
                 }
                 echo $form->textField($model,'ete', array('size' => 20,'class'=>"datetime_input")); ?>
        </div>
        <br>
        <div class="row">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Upload Files'),'Upload Files'); ?>
                 <?php
		              $this->widget('CMultiFileUpload', array(
		                 'model'=>$model,
		                 'attribute'=>'photos',
		                 'accept'=>'*',
		                 'options'=>array(
		                 ),
		                 'denied'=>'File is not allowed',
		                 'max'=>10, // max 10 files
              ));
            ?>
        </div>
         <br>
        <div class="row">
                 <?php echo CHtml::submitButton('submit',array("class"=>"form-control update","style"=>"width:250px;")); ?>
        </div>

<script type="text/javascript">
	$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tab = $('#<?=$_GET["tabid"];?>');
			var panel = $('#<?=$_GET["tabid"];?>').data('panel');
			tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
				$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
			});
			

			 $('.update',win).on('click',function(){
				if( confirm('Are you sure to save?')){
					var form = new FormData(document.getElementById("add_tla_task_form"));
					 $.ajax({
					            url: '<?=$this->createUrl('tlaTask/createTlaTask')?>',
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
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
				}
				return false;
			});
			$('#TlaTask_type',win).on('change',function(){
				if($(this).val()==70)
				{
					$('#owner_ac',win).hide();
					$('#noAssign',win).val(1);
				}else
				{
					$('#owner_ac',win).show();
					$('#noAssign',win).val(0);
				}
				return false;
			});

			$('#TlaTask_comment',win).on('focus', function(){
				if($(this).val()=='Note：If the task will be related to Invoice, Please list the details as backup.')
				{
					$(this).val('');
				}
			});

			$('#mdata_reoccuring',win).on('change',function(){
				if($(this).val()!="")
				{
					$('#reoccuring_div',win).show();
					$('#ete_div',win).hide();
				}else
				{
					$('#reoccuring_div',win).hide();
					$('#ete_div',win).show();
				}
			})

			$('#mdata_reoccuring_start_date',win).on('change',function(){
				if($(this).val()!="")
				{
					$('#mdata_reoccuring_run',win).attr('disabled','disabled');
				}else
				{
					$('#mdata_reoccuring_run',win).removeAttr('disabled');
				}
			})

			$('#mdata_reoccuring_end_date',win).on('change',function(){
				if($(this).val()!="")
				{
					$('#mdata_reoccuring_run',win).attr('disabled','disabled');
				}else
				{
					$('#mdata_reoccuring_run',win).removeAttr('disabled');
				}
			})

			$('#mdata_reoccuring_run',win).on('change',function(){
				if($(this).val()!="")
				{
					$('#mdata_reoccuring_start_date',win).attr('disabled','disabled');
					$('#mdata_reoccuring_end_date',win).attr('disabled','disabled');
				}else
				{
					$('#mdata_reoccuring_start_date',win).removeAttr('disabled');
					$('#mdata_reoccuring_end_date',win).removeAttr('disabled');
				}
			})

			$('#TlaTask_tag',win).on('change',function(){
				if($(this).val()==4)
				{
					var current = new Date();
					var ete = "";
					var month = current.getMonth()+1>10?current.getMonth()+1:"0"+(current.getMonth()+1);
					var day = current.getDate()>10?current.getDate():"0"+current.getDate();
					var hours = current.getHours()+4;
					var minute = current.getMinutes()>10?current.getMinutes():"0"+current.getMinutes();
					ete=current.getFullYear()+"-"+month+"-"+day+" "+hours+":"+minute+":00";
					$('#TlaTask_ete',win).val(ete);
				}

			});

	});
</script>


<?php $this->endWidget();?>
		
