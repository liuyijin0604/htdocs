<h3>update dispute status</h3>
<style type="text/css">
	.jqmWindow {
	    display: none;
	    position: absolute;
	    top: 12%;
	    left: 26.5%;
	    margin-left: -200px;
	    width: 1200px;
	    background-color: #EBF0FA;
	    color: #333;
	    border: 1px solid black;
	    padding: 12px;
		max-height: 75%;
	}

</style>
<div class="form">
<?php 
	$url = $this->createUrl('cargoProcess/update');
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cargo-process-acr_form',
	'enableAjaxValidation'=>false,
	'action'=> $url."?id=".$model->rec_id)
	);
?>

<div class="row buttons" style="display: inline-block;">
		<?php
			$uploadType = FileRepo::CREDIT_NOTE_FILE;
			$fr = new FileRepo('search');
			$fr->unsetAttributes();
			$fr->theTypes[] = $uploadType;
			$fr->fid = $model->dispute_id;
			$mf = Acl::hasAccess('B:org/manageFile');
			$this->widget('zii.widgets.grid.CGridView', [
				'id'=>$_GET['tabid'].'_excofile-credit-grid',
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
</br>

<div class="row buttons">

		<?php
		    $uploadType = FileRepo::CREDIT_NOTE_FILE;
		 	$pphash = "";
			echo '<br /><h3>', CHtml::label($this->t('Upload credit note File'), '</h3>');
			$pphash = FileRepo::uploadHash($model->parent, $uploadType);
			$this->widget('application.extensions.plupload.PluploadWidget', [
				'config' => [
					'url' => substr($this->createUrl('/filerepo/upload'), 0,-4)."/".$pphash,
					'max_file_size' => Yii::app()->params['maxFileSize'],
					'unique_names' => true,
					'file_list_height' => 60,
					'visible_header' => false,
					'filters' => [
						['title' => Yii::t('app', 'pdf,png,jpg,Excel files'), 'extensions' => 'pdf,jpg,png,jpeg,csv,xls,xlsx'],
					],
					//'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
					'language' => Yii::app()->language,
					'max_file_number' => 2,
					'autostart' => false,
					'jquery_ui' => false,
					'reset_after_upload' => true,
				],
				'callbacks' => [
					'FileUploaded' => 'function(up,file,response){alert("Import Confirm Invoice File Success");$("#'.$_GET["tabid"].'").trigger("reload_excofile-credit-grid");}',
				],
				'id' => $_GET['tabid'].'_excofile_uploader_1',
			]);
		?>
</div>
</br>
</br>
<?php

	$this->widget('application.extensions.editablegrid.CEditableGridView', [
		'id'=>$_GET["tabid"].'_reconcile_grid_details',
		'cssFile' => false,
		'dataProvider'=>$model->search(true),
		'filter'=>$model,
		'showQuickBar' => false,
		'formUrl' => Yii::app()->createUrl('siReconcile/updateDisputeRef'),
		'afterSave' => "function(r){
			if(r.done == true){
				myApp.notice(r.msg, 5000);
			}else{
				myApp.alert(r.msg, false);
			}
			return r.done;
		}",
		'columns'=>[		
			['name'=>'line_ref', 'header' => 'Ref', 'class' => 'CEditableColumn', 'type' => 'input', 'value' => '$data->line->ref'],
			['name'=>'line.type','value'=>'$data->line->getErrorTypes()'],
			['name'=>'line.value','value'=>'$data->getAllValue()'],
			['name'=>'line.my_value','value'=>'$data->getAllMyValue()'],
			['name'=>'line.weight'],
			['name'=>'line.cust_check_weight'],
			['name'=>'line.manifest_weight'],
			['name'=>'line.weight_diff'],
			['name'=>'dispute_amount_ex_gst','value'=>'$data->getAllDisputeValue()'],
			['name'=>'note'],
			['header'=>'isUpdated','value'=>'empty($data->status)?"No":"Yes"'],
			['header'=>'credit_amount','type'=>'raw','value'=>'CHtml::numberField("DisputeLine[credit_amount_ex_gst]",($data->status==0?$data->getAllDisputeValue():$data->getAllCreditValue()),["title"=>$data->id])'],
			['header'=>'Surcharge Invoice','type'=>'raw','value'=>function($data){
				return $data->getAllSurchargeInvoice();
			}],

			array('class' => 'CEditableButtonColumn',
				'template' => '{edit} {cancel} {save}',
			),
		],
	]);

; ?>
</br>
<div class="row buttons">
	<div class="row rowcol">
		<?php   echo CHtml::button('update credited amount',array('class'=>'update_credited_amount','id'=>'update_credited_amount'));?>
	</div>
	<div class="row rowcol">
		<?php   echo CHtml::button('generate invoice to customer',array('class'=>'generate invoice to customer','id'=>'generate_invoice_to_customer'));?>
	</div>
</div>


<script type="text/javascript">
$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tabSi = $('#<?=$_GET["tabid"];?>');
			var panelSi = $('#<?=$_GET["tabid"];?>').data('panel');

			tabSi.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
				$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
			});

			tabSi.unbind('reload_excofile-credit-grid').bind('reload_excofile-credit-grid', function(){
				$('#<?=$_GET["tabid"];?>_excofile-credit-grid').yiiGridView('update');
				return false;
			});

			function uploading_on(obj) {
				obj.addClass('uploading');
				obj.val('    Uploading');
				obj.prop('disabled', 'disabled');
			}

			function uploading_off(obj) {
				obj.removeClass('uploading');
				obj.val('update_credited_amount');
				obj.removeProp('disabled');
			}
			$('#update_credited_amount',win).on('click',function() {
				uploading_on($('#update_credited_amount',win));
				var ids =new Array();
				var creditAmountExGst =new Array();
				var note =new Array();
				$("input[name='DisputeLine[credit_amount_ex_gst]']",win).each(function() {  
	            	ids.push($(this).attr("title"));
	            	creditAmountExGst.push($(this).val());
	      		}); 

				var formData = new FormData();
				formData.append('ids', ids);
				formData.append('creditAmountExGst',creditAmountExGst);
				
				$.ajax({
					url: '<?=Yii::app()->createUrl("siReconcile/updateCreditedAmount")?>',
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function(r) {
						uploading_off($('#update_credited_amount'));
						r = JSON.parse(r);
						if (r.done) {
							myApp.notice(r.msg, 5000);
						} else {
							myApp.alert(r.msg, false);
						}
					},
					error: function(r) {
						uploading_off($('#update_credited_amount'));
						myApp.alert('System error', false);
					}
				});
			});
		});
</script>


<?php $this->endWidget();?>