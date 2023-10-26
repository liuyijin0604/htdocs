<h3>Si Reconcile Detail: Invoice--------<?=$model->apmanifest->ref?></h3>
<style>
    .cloumn_red{
        background-color:pink;
    }  
    .column_direct{
        color: green;
        font-weight: bold;
    }
    .jqmWindow
    {
    	width:900px;
    }
</style>

<div class="form">
<?php 
	$url = $this->createUrl('cargoProcess/update');
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cargo-process-acr_form',
	'enableAjaxValidation'=>false,
	'action'=> $url."?inline_pid=".$model->inline_pid)
	);
?>

	<div class="row buttons" style="position:absolute;top:3em;right:6em;">
			<?php echo "<a target='_blank'  href=\"" .$this->createUrl("siReconcile/exportErrorList",['inline_pid'=>$model->inline_pid])."\" title=\"exportErrorList\" ><span style=\"background-position:-48px -688px\" class=\"icon\"></span>export_error_list&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>"; ?>
			<?php echo "<a target='_blank' href=\"" .$this->createUrl("siReconcile/exportWeightDiff", ['inline_pid'=>$model->inline_pid])."\" title=\"exportWeightDiff\" ><span style=\"background-position:-48px -688px\" class=\"icon\"></span>export_weight_diff&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>"; ?>
			<?php echo "<a target='_blank'  href=\"" .$this->createUrl("siReconcile/exportRateDiff",['inline_pid'=>$model->inline_pid] )."\" title=\"exportRateCost\" ><span style=\"background-position:-48px -688px\" class=\"icon\"></span>export_rate_cost&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>"; ?>
	</div>
	</br>
	</br>
	</br>

<?php


if($model->parent->status==SiReconcile::ERROR_CHECKING_STATUS)
{
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
		'id'=>$_GET["tabid"].'_reconcile_grid_ap_details',
		'cssFile' => false,
		'dataProvider'=>$model->search(true, 30),
		'filter'=>$model,
		'columns'=>[		
			['name'=>'ref'],
			['name'=>'item_code'],
			['name'=>'type','value'=>'$data->getErrorTypes()'],
			['name'=>'value','header'=>'value'],
			['name'=>'my_value'],
			['name'=>'weight'],
			['name'=>'cust_check_weight'],
			['name'=>'manifest_weight'],
			['header'=>'Item Type',"value"=>'$data->getItemType()'],
			['header'=>'is_confirmed','value'=>'$data->getIsConfirmed()'],
			['class'=>'oButtonColumn',
				'template'=>'{operate}',
				'buttons'=>[
					'operate' => [
						'url'=>'Yii::app()->createURL("siReconcile/updateLine")."?id=".$data->id',
						'imageUrl'=>false,
						'visible'=>'$data->type>0?true:false',
						'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => 'Si Reconcile Detail'.'$data->id'],
					]
				],
			]
		],
	]);
}

if($model->parent->status==SiReconcile::WEIGHT_CHECKING_STATUS)
{
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
		'id'=>$_GET["tabid"].'_reconcile_grid_ap_details',
		'cssFile' => false,
		'dataProvider'=>$model->search(true, 30),
		'filter'=>$model,
		'columns'=>[		
			['name'=>'ref'],
			['name'=>'item_code'],
			['name'=>'type','value'=>'$data->getErrorTypes()'],
			['name'=>'value'],
			['name'=>'my_value'],
			['name'=>'weight'],
			['name'=>'cust_check_weight'],
			['name'=>'manifest_weight'],
			['header'=>'Item Type',"value"=>'$data->getItemType()'],
			['name'=>'weight_diff'],
			array('name'=>'chargeWeightDiff','value'=>'$data->getChargeWeightDiff()','filter'=>false,'cssClassExpression' => 'in_array($data->parent->org_id,SiReconcile::$cbmCourier)?$data->getChargeWeightDiff()>=10? "cloumn_red_rec" : "":$data->getChargeWeightDiff()>0? "cloumn_red_rec" : ""'),
			['header'=>'is_confirmed','value'=>'$data->getIsConfirmed()'],
		    array('header'=>'viewInvoice','type'=>'raw','value'=>'!empty($data->getWeightDiffInvoice())?"<a href=\"".Yii::app()->createURL("invoice/print", array("id" => $data->getWeightDiffInvoice()->id))."\" target=\"_blank\">Invoice".$data->getWeightDiffInvoice()->no."</a>":""'),
			['class'=>'oButtonColumn',
				'template'=>'{details}&nbsp;{Customer Invoice Diff}',
				'buttons'=>[
					'details' => [
						'url'=>'Yii::app()->createURL("siReconcile/getApmanifestDetail")."?id=".$data->id',
						'imageUrl'=>false,
						'visible'=>'$data->item_code=="eparcel"?true:false',
						'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => 'Si Reconcile Detail'.'$data->id'],
					],
					'Customer Invoice Diff' => [
		              'url'=>' Yii::app()->createURL("siReconcile/diffInvoiceCheck")."?id=".$data->id."&&parent_id=".$data->rec_id',
		              'imageUrl'=>false,
		              'visible'=>'$data->getChargeWeightDiff()>0?true:false',
		              'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
		            ]
				],
			]
		],
	]);
}

if($model->parent->status==SiReconcile::RATE_CHECKING_STATUS)
{
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
		'id'=>$_GET["tabid"].'_reconcile_grid_ap_details',
		'cssFile' => false,
		'dataProvider'=>$model->search(true, 30),
		'filter'=>$model,
		'columns'=>[		
			['name'=>'ref'],
			['name'=>'item_code'],
			['name'=>'type','type'=>'raw','value'=>'$data->getErrorTypes()'],
			['name'=>'value'],
			['name'=>'my_value'],
			['name'=>'weight'],
			['name'=>'cust_check_weight'],
			['name'=>'manifest_weight'],
			['header'=>'Item Type',"value"=>'$data->getItemType()'],
			['name'=>'diff','value'=>'$data->value-$data->my_value'],
			['header'=>'is_confirmed','value'=>'$data->getIsConfirmed()'],
			['header'=>'confirm_cost_ex_gst','value'=>'@$data->mdata["confirm_cost_ex_gst"]'],
		       array(
		          'class'=>'oButtonColumn',
		          'template'=>'{Customer Invoice Diff}',
		          'buttons'=>[
		            'Customer Invoice Diff' => [
		              'url'=>' Yii::app()->createURL("siReconcile/diffInvoiceCheck")."?id=".$data->id."&&parent_id=".$data->rec_id',
		              'imageUrl'=>false,
		              'visible'=>'$data->getChargeWeightDiff()>0?true:false',
		              'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
		            ],
		          ]
		        )
		],
	]);
}

if($model->parent->status==SiReconcile::SURCHARGE_CHECKING_STATUS)
{
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
		'id'=>$_GET["tabid"].'_reconcile_grid_ap_details',
		'cssFile' => false,
		'dataProvider'=>$model->search(true, 30),
		'filter'=>$model,
		'columns'=>[		
			['name'=>'ref'],
			['name'=>'item_code'],
			['name'=>'type','type'=>'raw','value'=>'$data->getErrorTypes()'],
			['name'=>'value'],
			['name'=>'my_value'],
			['header'=>'diff','value'=>'$data->value-$data->my_value'],
			['header'=>'is_confirmed','value'=>'$data->getIsConfirmed()'],
			['header'=>'confirm_cost_ex_gst','value'=>'@$data->mdata["confirm_cost_ex_gst"]'],
		],
	]);
}


if($model->parent->status==SiReconcile::LINKING_BILLING_STATUS)
{
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
		'id'=>$_GET["tabid"].'_reconcile_grid_ap_details',
		'cssFile' => false,
		'dataProvider'=>$model->search(true, 30),
		'filter'=>$model,
		'columns'=>[		
			['name'=>'ref'],
			['name'=>'item_code'],
			['name'=>'type','value'=>'$data->getErrorTypes()'],
			['name'=>'value'],
			['name'=>'my_value'],
			['name'=>'weight'],
			['name'=>'cust_check_weight'],
			['name'=>'manifest_weight'],
			['header'=>'Item Type',"value"=>'$data->getItemType()'],
			['name'=>'weight_diff'],
			['class'=>'oButtonColumn',
				'template'=>'{operate}&nbsp;{log}',
				'buttons'=>[
					'operate' => [
						'url'=>'Yii::app()->createURL("siReconcile/getReconcileDetail")."?id=".$data->id',
						'imageUrl'=>false,
						'visible'=>'true',
						'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => 'Si Reconcile Detail'.'$data->id'],
					],
					'log' => [
						'imageUrl'=>false,
						'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
						'visible' => 'true',
						'url' => 'Yii::app()->createUrl("siReconcile/log", ["id" => $data->id])',
						'label' => 'Log'
					],
				],
			]
		],
	]);
}

if($model->parent->status==SiReconcile::SI_RECONCILE_DONE_STATUS)
{
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
		'id'=>$_GET["tabid"].'_reconcile_grid_ap_details',
		'cssFile' => false,
		'dataProvider'=>$model->search(true, 30),
		'filter'=>$model,
		'columns'=>[		
			['name'=>'ref'],
			['name'=>'item_code'],
			['name'=>'type','value'=>'$data->getErrorTypes()'],
			['name'=>'value'],
			['name'=>'my_value'],
			['name'=>'weight'],
			['name'=>'cust_check_weight'],
			['name'=>'manifest_weight'],
			['header'=>'Item Type',"value"=>'$data->getItemType()'],
			['name'=>'weight_diff'],
			array('name'=>'chargeWeightDiff','value'=>'$data->getChargeWeightDiff()','filter'=>false,'cssClassExpression' => 'in_array($data->parent->org_id,SiReconcile::$cbmCourier)?$data->getChargeWeightDiff()>=10? "cloumn_red_rec" : "":$data->getChargeWeightDiff()>0? "cloumn_red_rec" : ""'),
		    array('header'=>'viewInvoice','type'=>'raw','value'=>'!empty($data->getWeightDiffInvoice())?"<a href=\"".Yii::app()->createURL("invoice/print", array("id" => $data->getWeightDiffInvoice()->id))."\" target=\"_blank\">Invoice".$data->getWeightDiffInvoice()->no."</a>":""'),
		       array(
		          'class'=>'oButtonColumn',
		          'template'=>'{Customer Invoice Diff}',
		          'buttons'=>[
		            'Customer Invoice Diff' => [
		               'url'=>' Yii::app()->createURL("siReconcile/diffInvoiceCheck")."?id=".$data->id."&&parent_id=".$data->rec_id',
		              'imageUrl'=>false,
		              'visible'=>'$data->getChargeWeightDiff()>0?true:false',
		              'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
		            ],
		          ]
		        ),
			['class'=>'oButtonColumn',
				'template'=>'{operate}&nbsp;{log}',
				'buttons'=>[
					'operate' => [
						'url'=>'Yii::app()->createURL("siReconcile/getReconcileDetail")."?id=".$data->id',
						'imageUrl'=>false,
						'visible'=>'true',
						'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => 'Si Reconcile Detail'.'$data->id'],
					],
					'log' => [
						'imageUrl'=>false,
						'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
						'visible' => 'true',
						'url' => 'Yii::app()->createUrl("siReconcile/log", ["id" => $data->id])',
						'label' => 'Log'
					],
				],
			]
		],
	]);
}

; ?>



<script type="text/javascript">
	$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tab = $('#<?=$_GET["tabid"];?>');
			var panelSi = $('#<?=$_GET["tabid"];?>').data('panel');

			win.unbind('reload_reconcile_grid_details').bind('reload_reconcile_grid_details', function(){
				$('#<?=$_GET["tabid"];?>_reconcile_grid_details', tab.data('panel')).yiiGridView('update');
				return false;
			});

			win.unbind('reload_excofile_grid').bind('reload_excofile_grid', function(){
				$('#<?=$_GET["tabid"];?>_excofile-grid', tab.data('panel')).yiiGridView('update');
				return false;
			});

			tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
				$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
			});

			function uploading_on(obj) {
				obj.addClass('uploading');
				obj.val('    Uploading');
				obj.prop('disabled', 'disabled');
			}

			function uploading_off(obj) {
				obj.removeClass('uploading');
				obj.val('Submit');
				obj.removeProp('disabled');
			}
			$(panelSi).off('click', '#btn-save').on('click', '#btn-save', function() {
				uploading_on($('#btn-save'));

				var formData = new FormData();
				formData.append('id', '<?=$model->rec_id?>');
				formData.append('confirm_inv_file', $('#confirm_inv_file', panelSi)[0].files[0]);
				$.ajax({
					url: '<?=Yii::app()->createUrl("siReconcile/ajaxImportReconciliationConfirmList")?>',
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function(r) {
						uploading_off($('#btn-save'));
						r = JSON.parse(r);
						if (r.done) {
							myApp.notice(r.msg, 5000);
						} else {
							myApp.alert(r.msg, false);
						}
					},
					error: function(r) {
						uploading_off($('#btn-save'));
						myApp.alert('System error', false);
					}
				});
		});
	});
</script>


<?php $this->endWidget();?>