<h3><?php
if($model->inspection->isCombine==1)
{
    echo "Courier: ".$model->inspection->courier->name;
}else
{
    echo "Ref: ".$model->shipment->ref;
}

?></h3>
<div class="form">
	<br>
	<h3><?=CHtml::label($this->t('Files'),$this->t('Files'))?></h3>
	<div class="row buttons">
		<?php
			$fr = new FileRepo('search');
			$fr->id = $model->file_id;
			$fr->status = '>0';
			$mf = Acl::hasAccess('B:org/manageFile');
			$this->widget('zii.widgets.grid.CGridView', [
				'id'=>'inspection-excofile-grid',
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
						'template'=>'{cancel}',
						'buttons'=>[
							'cancel' => [
								'url'=>'Yii::app()->createUrl("warehouseProcess/deleteFile",array("id"=>$data->id))',
								'imageUrl'=>false,
								'visible'=>'true',
								'options' => ['class' => '', 'label'=>$this->t('Cancel'), 'title' => '$data->name','onClick'=>'"return deleteInspectionFile(".$data->id.");"'],
							],
						],
					],
				],
			]);
			?>
	</div>
	
	<?php //if(($model->status==CargoProcess::WAITINGDELIVERY ||$model->status==CargoProcess::WAITINGAGENTDELIVERY||($model->status==CargoProcess::AGENTDELIVERYDONE&&User::checkIsNotTruckUser()))&& Acl::hasAccess("C:cargoProcess/uploadPOD")&&$model->type!=CargoProcess::PICKUP_CARGO):?>
<?php 
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment_wh_inspection_relations_form',
	'enableAjaxValidation'=>false,
	'action'=> 'warehouseProcess/uploadFile')
	);
?>

	<div class="row">
	<?php echo CHtml::label("Upload Photo", 'Upload Photo', array('required' => 'required')); ?>
		<input type="file" name="file" id="file" style="display:none;" > 
		<input id = "path" name="path" readonly class="form-control">
		<a href="" class="form-control" id = "takePhoto" ><center><span class="glyphicon glyphicon-camera"></span>Take Photo</center></a>
	</div>
	</br>
	</br>
	<div class="row">
		<?php echo CHtml::label("Is ok?", 'Is ok?', array('required' => 'required')); ?>
		<?php echo CHtml::radioButtonList('ShipmentWhInspectionRelations[check_status]',$model->check_status,ShipmentWhInspectionRelations::$check_status_list, array('labelOptions' => array('class' => 'radio_label','onClick'=>'return selectInspectionStatus();'), 'separator' => '&nbsp;&nbsp'));?>
	</div>
	</br>
	</br>
	<div class="row" style="display: none" id = "<?=$model->id?>comment">
		<?php echo CHtml::label("Comment", 'Comment', array('required' => 'required')); ?>
			<?php 
				echo $form->textField($model,'comment',['length'=>50,'class'=>'form-control']);
			 ?>
	</div>

	<div class="row">
		<br><br>
		<center>
         <?php echo CHtml::submitButton('submit',array("class"=>"form-control","style"=>"width:250px;","id"=>'shipment_wh_inspection_form_update')); ?>
     	</center>
	</div>

	<?php if(Acl::hasAccess("B:WarehouseProcess/doneInspection")):?>
	<div class="row">
		<div class="row" style="float: right">
         <?php echo CHtml::submitButton('confirm',array("class"=>"form-control","style"=>"width:6em;margin-right:2em;","id"=>'shipment_wh_inspection_form_confirm')); ?>
         </div>
	</div>
	<?php endif;?>

<?php $this->endWidget();?>

</div>

<script type="text/javascript">
	$(function(){
		
			$('#file').on('change',function(){
				$('#path').val($(this).val());
			});

			$('#takePhoto').on('click',function(){
				$('#file').click();
				return false;
			});

			var isOkStatus = 0;
			$('input:radio[name="ShipmentWhInspectionRelations[check_status]"]').on('change',function(){
				isOkStatus = $(this).val();
				if(isOkStatus==1)
				{
					$('#<?=$model->id?>comment').hide();
				}else
				{
					$('#<?=$model->id?>comment').show();
				}
			});

			 $('#shipment_wh_inspection_form_update').on('click',function(){
			 	if(isOkStatus==0)
			 	{
			 		 $('#notifc').notify({message: {html: "requiring check status"},type: 'danger'}).show();
			 		 return false;
			 	}

			 	if(isOkStatus==2)
			 	{
			 		if($('#ShipmentWhInspectionRelations_comment').val()=='')
			 		{
			 			$('#notifc').notify({message: {html: "requiring comment"},type: 'danger'}).show();
			 			return false;
			 		}
			 	}

				if( confirm('Are you sure to save?')){
					var form = new FormData(document.getElementById("shipment_wh_inspection_relations_form"));
					 $.ajax({
					            url: '<?=$this->createUrl('warehouseProcess/uploadFile',['id'=>$model->id])?>',
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                if(r=='done')
					                 {
					                 	$('#notifc').notify({message: {html: "Upload Task File Success"}}).show();
										freshInspectionListDirect();
										$('#modal_close').click();
										// $("#inspection-excofile-grid").yiiGridView("update");
									 }else
									 {
										$('#notifc').notify({message: {html: r},type: 'danger'}).show();
						             }
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });			
				}
				return false;
			});

			$('#shipment_wh_inspection_form_confirm').on('click',function(){
				if( confirm('Are you sure to confirm?')){
					 $.ajax({
					            url: '<?=$this->createUrl('warehouseProcess/doneInspection',['id'=>$model->id])?>',
					            type: "post",
					            data: [],
					            processData: false,
					            contentType: false,
					            success: function(r) {
					            	r=JSON.parse(r);
					                if(r.done)
					                 {
					                 	$('#notifc').notify({message: {html: "confirmed"}}).show();
										freshInspectionListDirect();
										$('#modal_close').click();
										// $("#inspection-excofile-grid").yiiGridView("update");
									 }else
									 {
										$('#notifc').notify({message: {html: r},type: 'danger'}).show();
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

		
