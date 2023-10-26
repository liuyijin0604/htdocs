<h3>Diff Invoice Check-<?=$model->shipment_no?></h3>
<div class="form">
<div class="row">
	<div class="col" style="margin-right: 25px">
		<div class="row rowcol-left">
				<?php echo CHtml::label('Courier Weight','courier_weight'); ?>
				<?php echo CHtml::textField('DiffInvoiceCheck[courier_weight]',$weight, array('id' => 'courier_weight',"style"=>"width:100px;")); ?>

				<?php echo CHtml::label('Courier Weight By chargecode--'.$cubicRate,'courier_weight_chargecode'); ?>
				<?php echo CHtml::textField('DiffInvoiceCheck[courier_weight_chargecode]',$courierWeightByChargeCode, array('id' => 'courier_weight_chargecode',"style"=>"width:100px;")); ?>
				-
				<?php echo CHtml::textField('DiffInvoiceCheck[customer_charged_weight]',$csChargeWeight, array('id' => 'customer_charged_weight',"style"=>"width:100px;")); ?>(our chagred weight)
				=
				<?php echo CHtml::textField('DiffInvoiceCheck[diff_weight]',$diffWeight, array('id' => 'customer_charged_weight',"style"=>"width:100px;")); ?>

		</div>

		<div class="row rowcol-left">
				<?php echo CHtml::label('Invoice Diff','courier_weight_charge'); ?>
				<?php echo CHtml::textField('DiffInvoiceCheck[courier_weight_chargecode_invoice]',$courierWeightInvoiceByChargeCode, array('id' => 'courier_weight_charge',"style"=>"width:100px;")); ?>
				-
				<?php echo CHtml::textField('DiffInvoiceCheck[charged_invoice]',$chargedInvoice, array('id' => 'charged_invoice',"style"=>"width:100px;")); ?>
				=
				<?php echo CHtml::textField('DiffInvoiceCheck[pay_in_back]',$payInBack, array('id' => 'pay_in_back',"style"=>"width:100px;")); ?>

		</div>

		<div class="row rowcol-left">
			<?php
				echo CHtml::button('Generate Weight Diff Invoice',array('class'=>'weight_diff_invoice'));
				$oInvoice = $model->getWeightDiffInvoice();
				if(!empty($oInvoice))
				{
					echo CHtml::link('View Invoice'.$oInvoice->no,$this->createUrl("invoice/print",["id"=>$oInvoice->id]),["target"=>"_blank"]);
				}
			?>
		</div>

	</div>
</div>
<br>
<div class="form">
<?php 
	$id = $model->id;
	$url = $this->createUrl('cargoProcess/update');
	if(isset($ids) && $ids != "")
	{
		$id = $ids;
	}
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cargo-process-acr_form',
	'enableAjaxValidation'=>false,
	'action'=> $url."?id=".$id)
	);
?>

<!-- 	
	<div class="row buttons">

			 <?php   echo CHtml::button('Collect Info Done',array('class'=>'collectInfo_done'));?>
	</div> -->




<script type="text/javascript">
	$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tab = $('#<?=$_GET["tabid"];?>');
			var panel = $('#<?=$_GET["tabid"];?>').data('panel');

			tab.unbind('reload_cargo_process_grid').bind('reload_cargo_process_grid', function(){
				$('#<?=$_GET["tabid"];?>_cargo_process_grid', tab.data('panel')).yiiGridView('update');
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

			 $('.collectInfo_done',win).on('click',function(){
				if( confirm('Are you sure to Collect Info Done')){
						$.get('<?=$this->createUrl("cargoProcess/collectInfoDone",array('id'=>$model->id))?>',function(r){
								if(r=='done'){
										save();	
								 }else{
										myApp.alert(r, false);   
							 }
								tab.trigger('reload_cargo_process_grid');
					 });
				
				}
			});

			$('#cargo_process_status', win).next().on('dblclick', function(){
				if(window.confirm('Are you sure to override status?')){
					$(this).prev().attr('disabled', false);
				}
			});

			$('.weight_diff_invoice',win).on('click',function(){
				if( confirm('Are you sure to Generate Weight Diff Invoice?'))
				{
						$.get('<?=$this->createUrl("invoice/generateReconciliationWeightDiffInvoice")."?id=".$model->id."&&parentId=".$model->parent_id?>',function(r){
							r = JSON.parse(r);
								if(r.done==true){
										window.open("<?=$this->createUrl("invoice/print")?>"+"?id="+r.id,"_blank");
								 }else{
										myApp.alert(r, false);   
							 }
					 });
				
				}
			});

	});
</script>


<?php $this->endWidget();?>
		
