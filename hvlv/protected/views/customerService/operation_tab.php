<h3>Operation-<?=$model->ShipmentQuestionSubmit->ticket?></h3>
<br>
<div class="form">
<?php 
	$id = $model->id;
	if(isset($ids) && $ids != "")
	{
		$id = $ids;
	}
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cs_update_form',
	'enableAjaxValidation'=>false)
	);
?>
	<div class="row">
		<label>Submit Org Name:&nbsp;&nbsp;&nbsp;&nbsp;<?=@$model->ShipmentQuestionSubmit->org->name?></label>
		<label>Email:&nbsp;&nbsp;&nbsp;&nbsp;<?=@$model->ShipmentQuestionSubmit->email?></label>
		<label>Phone:&nbsp;&nbsp;&nbsp;&nbsp;<?=@$model->ShipmentQuestionSubmit->phone?></label>
		<label>Customer Read:&nbsp;&nbsp;&nbsp;&nbsp;<?=@ShipmentQuestion::$readTypes2[$model->c_read]?></label>
		<label>Submit Type:&nbsp;&nbsp;&nbsp;&nbsp;<?=@ShipmentQuestionSubmit::$types[$model->ShipmentQuestionSubmit->type]?></label>
      
	</div>
	<div class="row">
		<?php echo CHtml::label('Customer Note','Customer Note'); ?>
		<?php echo CHtml::textArea('c_note',@$model->ShipmentQuestionSubmit->c_note,array('rows'=>10, 'cols' => 60,'disabled'=>'disabled')); ?>
 	</div>

	<div class="row">
		<?php echo CHtml::hiddenField('mdata[items]',@$model->mdata['items'],array('rows'=>10, 'cols' => 60)); ?>
 	</div>

	<div class="row">
		<?php echo CHtml::label('Service Note','s_note'); ?>
		<?php echo CHtml::textArea('s_note',@$model->s_note,array('rows'=>10, 'cols' => 60)); ?>
 	</div>

<!--  	<div class="row buttons">
	<?php  echo CHtml::submitButton('Send Email',array('class'=>'send_email'));?>
	</div> -->
	<br>
	<div class="row buttons">
	<?php  echo CHtml::submitButton('Save',array('class'=>'update'));?>
	</div>

	<br>
	<div class="row">
		<?php echo CHtml::label('Process Status:','Process Status:'); ?>
	</div>
	<div class="row buttons">
		<?php  echo CHtml::submitButton('Finish',array('class'=>'finish'));?>
		<?php  echo CHtml::submitButton('Unfinish',array('class'=>'unfinish'));?>
	</div>
	<br>

<?php $this->endWidget();?>

<?php 
$cInfo = empty($model->Shipment)?null:$model->Shipment->getCourierInfo();
if(!empty($cInfo)&&!empty($cInfo[3]))$toEmail = Org::model()->findByPk($cInfo[3])->extra['cs_email'];
?>
<?php if(!empty($toEmail)||!empty($model->ShipmentQuestionSubmit->email)):?>

<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cs_send_email_form',
	'enableAjaxValidation'=>false,
	'action'=> $this->createUrl('customerService/sendEmail'))
	);?>
	<div class="row buttons">
		<div class="row">
			<label class="left">Subject:</label>
			<input type="hidden" value=<?=$id?> name="id">
			<input type="hidden" value="courier" name="type" id ="type">
			<?php echo $form->textField(@$emailTpl,'subject',array('size' => 60,'maxlength' => 500,"id"=>"cs_send_email_subject"));?>
		</div>
	
		<div class="row" style="min-height:330px">
			<?php //echo $form->textArea($model,'body',array('rows'=>6, 'cols'=>50));
			$this->widget('ext.ckeditor.CKEditorWidget',array(
			  "model"=>$emailTpl,
			  "attribute"=>'content',
			  "id" => 'email_body_'.$_GET['tabid'],
			  "ckBasePath"=>'//cdn.ckeditor.com/4.4.7/standard/',
			  //"defaultValue"=>"Test Text",
			  "config" => array(
				  "height"=>"200px",
				  "width"=>"570px",
				  "toolbar"=>"Standard",
				  "id"=>"cs_send_email_subject"
				  ),
			  ));
			?>
			
		</div>

		<h1>Images:</h1>
<br>
	<?php

		$fr = new FileRepo('search');
		$fr->unsetAttributes();
		if (empty($_GET['FileRepo'])) {
			$fr->theTypes = [133];
		} else {
			$fr->attributes=$_GET['FileRepo'];
			if (empty($fr->theTypes)) {
				$fr->theTypes = [133];
			}
		}
		$fr->fid = $model->id;
		$mf = Acl::hasAccess('C:CustomerService/getImages');

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

		echo '<br />', CHtml::label($this->t('Upload Files'), 'uploader');
		$pphash = FileRepo::uploadHash($model, 133);
		$this->widget('application.extensions.plupload.PluploadWidget', [
			'config' => [
				'url' => $this->createUrl('filerepo/upload/'.$pphash),
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
				'FileUploaded' => 'function(up,file,response){$("#'.$_GET["tabid"].'").trigger("reload_excofile_grid");}',
			],
			'id' => $_GET['tabid'].'_excofile_uploader',
		]);
	?>


		<?php 
			if(@$model->checkIsSendCourierEmail())echo CHtml::submitButton('Resend email to '.@$cInfo[0],array('class'=>'setc'));else echo CHtml::submitButton('Send email to '.@$cInfo[0],array('class'=>'setc'));?>
		<?php if(@$model->checkIsSendCustomerEmail())echo CHtml::submitButton('Resend email to notice customer',array('class'=>'setnc'));else echo CHtml::submitButton('Send email to notice customer',array('class'=>'setnc'));?>
		<?php echo CHtml::checkbox('with_files',0,array('class'=>'with_files'))." Send With Files "?>

		<?php if(!empty($model->ShipmentQuestionSubmit->phone))
		{
			if(@$model->checkIsSendPhone())echo CHtml::submitButton('Resend sms to notice customer',array('class'=>'sendSms'));else echo CHtml::submitButton('Send sms to notice customer',array('class'=>'sendSms'));
		}?>



	</div>
<?php $this->endWidget();?>
<?php endif;?>
<br>
<br>
<br>

<script type="text/javascript">
	$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tab = $('#<?=$_GET["tabid"];?>');
			var panel = $('#<?=$_GET["tabid"];?>').data('panel');

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

		
			

			 $('.update',win).on('click',function(){
				if( confirm('Are you sure to save?')){
					var form = new FormData(document.getElementById("cs_update_form"));
					 $.ajax({
					            url: '<?=$this->createUrl('customerService/update')."?id=".$id?>',
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
						             tab.trigger('reload_cs_grid');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });			
				}
				return false;
			});

			 $('.finish',win).on('click',function(){
				if( confirm('Are you sure to finish?')){
					var form = new FormData(document.getElementById("cs_update_form"));
					 $.ajax({
					            url: '<?=$this->createUrl('customerService/finish')."?id=".$id?>',
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
						             tab.trigger('reload_cs_grid');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });			
				}
				return false;
			});

			 $('.unfinish',win).on('click',function(){
				if( confirm('Are you sure to unfinish?')){
					var form = new FormData(document.getElementById("cs_update_form"));
					 $.ajax({
					            url: '<?=$this->createUrl('customerService/unfinish')."?id=".$id?>',
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
						             tab.trigger('reload_cs_grid');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });			
				}
				return false;
			});

			 $('.setc',win).on('click',function(){
				if( confirm('Are you sure to send email to courier?')){
					$("#type").val("courier");
					myApp.notice("done");//can't get form return need to improve
					$(this).hide();
					return true;	
				}
				return false;
			});

			 $('.setnc',win).on('click',function(){
				if( confirm('Are you sure to send email to customer?')){
					$("#type").val("customer");
					myApp.notice("done");//can't get form return need to improve
					$(this).hide();
					return true;		
				}
				return false;
			});

			  $('.sendSms',win).on('click',function(){
				if( confirm('Are you sure to send sms to customer\'s phone?')){
					$("#type").val("sms");
					myApp.notice("done");//can't get form return need to improve
					return true;		
				}
				return false;
			});

	});
</script>



		
