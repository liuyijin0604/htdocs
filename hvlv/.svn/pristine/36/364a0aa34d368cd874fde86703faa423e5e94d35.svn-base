<h3>Si Reconcile Detail: Invoice--------<?=$model->parent->parent->inv_no?></h3>
<style>
    .cloumn_red{
        background-color:pink;
    }  
    .column_direct{
        color: green;
        font-weight: bold;
    }
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
	'action'=> $url)
	);
?>
<?php
if($model->parent->org_id==Org::ORGID_COURIER_EIZ)
{
echo "Select All",CHtml::checkbox("si_reconcile_id_all",0,["onChange"=>" return select_all_dispute();"]);
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
		'id'=>$_GET["tabid"].'_reconcile_grid_details',
		'cssFile' => false,
		'dataProvider'=>$model->search(false),
		'filter'=>$model,
		'columns'=>[	
			['header'=>'select','type'=>'raw','value'=>'CHtml::checkbox("si_reconcile_id",0,["value"=>$data->id,"onChange"=>" return calculateDisputeAmount()"])'],	
			['name'=>'ref'],
			['name'=>'item_code'],
			['name'=>'type','value'=>'$data->getErrorTypes()'],
			['name'=>'value'],
			['name'=>'my_value'],
			['header'=>'surcharge','value'=>'ReconcileService::getEizNeedDisputeLineId($data->rec_id,$data->id)[0]'],
			['header'=>'extra','value'=>'ReconcileService::getEizNeedDisputeLineId($data->rec_id,$data->id)[1]'],
			['header'=>'dispute_amount_ex_gst','type'=>'raw','value'=>'CHtml::numberField("DisputeLine[dispute_amount_ex_gst]",round(ReconcileService::getEizNeedDisputeLineId($data->rec_id,$data->id)[2],4),["id"=>$data->id."dispute_amount_ex_gst"])'],
			['header'=>'note','type'=>'raw','value'=>'CHtml::textarea("DisputeLine[note]","",["id"=>$data->id."note"])']
		],
	]);

}else
{
echo "Select All",CHtml::checkbox("si_reconcile_id_all",0,["onChange"=>" return select_all_dispute();"]);
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
		'id'=>$_GET["tabid"].'_reconcile_grid_details',
		'cssFile' => false,
		'dataProvider'=>$model->search(false),
		'filter'=>$model,
		'columns'=>[	
			['header'=>'select','type'=>'raw','value'=>'CHtml::checkbox("si_reconcile_id",0,["value"=>$data->id,"onChange"=>" return calculateDisputeAmount()"])'],	
			['name'=>'ref'],
			['name'=>'item_code'],
			['name'=>'type','value'=>'$data->getErrorTypes()'],
			['name'=>'value'],
			['name'=>'my_value'],
			['name'=>'weight'],
			['name'=>'cust_check_weight'],
			['name'=>'manifest_weight'],
			['name'=>'weight_diff'],
			['header'=>'dispute_amount_ex_gst','type'=>'raw','value'=>'CHtml::numberField("DisputeLine[dispute_amount_ex_gst]",empty($data->mdata["confirm_cost_ex_gst"])?$data->value:round($data->value-$data->mdata["confirm_cost_ex_gst"],4),["id"=>$data->id."dispute_amount_ex_gst"])'],
			['header'=>'Invoice Det','type'=>'raw','value'=>'CHtml::textarea("DisputeLine[invoice_det]",(empty($data->mdata["invoice_det"])?($data->ref."/".$data->item_code):$data->mdata["invoice_det"]),["id"=>$data->id."invoice_det"])'],
			['header'=>'note','type'=>'raw','value'=>'CHtml::textarea("DisputeLine[note]","",["id"=>$data->id."note"])'],

			['header'=>'Surcharge Invoice','type'=>'raw','value'=>function($data){
				return !empty($data->mdata["surcharge_inv_id"])?"<a href=\"https://os.toplogistics.com.au/top/invoice/print.app?id=".$data->mdata["surcharge_inv_id"]."\" target=\"_blank\">Invoice".$data->mdata["surcharge_inv_id"]."</a>":"";
			}],
		],
	]);
}

; ?>
	<div class="row buttons">
		<input type="file" name="dispute_file" id="dispute_file" />
	</div>
	</br>
	<div class="row buttons">
			<p>Disput Amount Ex Gst: <span id="<?=$_GET["tabid"]?>DisputAmount">0.00</span></p>
	</div>
	<div class="row buttons">
	<div class="row rowcol">
		<?php   echo CHtml::button('create dispute case',array('class'=>'create_dispute_case','id'=>'create_dispute_case'));?>
	</div>
	<div class="row rowcol">
		<?php   echo CHtml::button('generate invoice to customer',array('class'=>'generate invoice to customer','id'=>'generate_invoice_to_customer'));?>
	</div>
</div>




<script type="text/javascript">
			function select_all_dispute()
			{
				 var selectAll = $('#si_reconcile_id_all');
				 if(selectAll.prop("checked")==true)
				 {
				 	$("input[name='si_reconcile_id']").prop("checked",true);
				 }else
				 {
				 	$("input[name='si_reconcile_id']").prop("checked",false);
				 }
				 calculateDisputeAmount();
				return false;
			}

			function calculateDisputeAmount()
			{
				var amount = 0.0;
				$("input[name='si_reconcile_id']:checked").each(function() {  
	            	amount+=$('#'+$(this).attr("value")+"dispute_amount_ex_gst").val()*1;
	      		}); 
	      		$('#<?=$_GET["tabid"]?>DisputAmount').html(amount+" ");
			}
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
				obj.val('create dispute case');
				obj.removeProp('disabled');
			}
			$('#create_dispute_case',win).on('click',function() {
				uploading_on($('#create_dispute_case'));
				var ids =new Array();
				var disputeAmountExGsts =new Array();
				var note =new Array();
				$("input[name='si_reconcile_id']:checked").each(function() {  
	            	ids.push($(this).attr("value"));
	            	disputeAmountExGsts.push($('#'+$(this).attr("value")+"dispute_amount_ex_gst").val());
	            	note.push($('#'+$(this).attr("value")+"note").val()); 
	      		}); 

				var formData = new FormData();
				formData.append('ids', ids);
				formData.append('disputeAmountExGsts',disputeAmountExGsts);
				formData.append('dispute_file', $('#dispute_file', win)[0].files[0]);
				formData.append('note', note);
				formData.append('dpmt', <?=empty($model->dpmt)?10:$model->dpmt?>);
				
				$.ajax({
					url: '<?=Yii::app()->createUrl("siReconcile/createDisputeCase")?>',
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function(r) {
						uploading_off($('#create_dispute_case'));
						r = JSON.parse(r);
						if (r.done) {
							myApp.notice(r.msg, 5000);
						} else {
							myApp.alert(r.msg, false);
						}
					},
					error: function(r) {
						uploading_off($('#create_dispute_case'));
						myApp.alert('System error', false);
					}
				});
		});


		$('#generate_invoice_to_customer',win).on('click',function() {
			uploading_on($('#generate_invoice_to_customer',win));
			var ids =new Array();
			var disputeAmountExGsts =new Array();
			var disputeInvoiceDets = new Array();
			var note =new Array();
			$("input[name='si_reconcile_id']:checked").each(function() {  
	        	ids.push($(this).attr("value"));
	        	disputeAmountExGsts.push($('#'+$(this).attr("value")+"dispute_amount_ex_gst").val());
	        	disputeInvoiceDets.push($('#'+$(this).attr("value")+"invoice_det").val());
	        	note.push($('#'+$(this).attr("value")+"note").val()); 
	      	}); 

			var formData = new FormData();
			formData.append('ids', ids);
			formData.append('disputeAmountExGsts',disputeAmountExGsts);
			formData.append('disputeInvoiceDets',disputeInvoiceDets);
			
			$.ajax({
				url: '<?=Yii::app()->createUrl("siReconcile/generateDisputeInvoiceToCustomer")?>',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function(r) {
					uploading_off($('#generate_invoice_to_customer'));
					r = JSON.parse(r);
					if (r.done) {
						myApp.notice(r.msg, 5000);
					} else {
						myApp.alert(r.msg, false);
					}
				},
				error: function(r) {
					uploading_off($('#generate_invoice_to_customer'));
					myApp.alert('System error', false);
				}
			});
		});
	});
</script>


<?php $this->endWidget();?>