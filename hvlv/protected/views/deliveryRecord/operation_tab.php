<h3>Operation-<?=$model->no?></h3>
<br>
<div class="form">
<?php 
	$id = $model->id;
	$url = $this->createUrl('deliveryRecord/operation');
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'delivery-record-acr_form',
	'enableAjaxValidation'=>false,
	'action'=> $url."?id=".$id)
	);
?>
	
	<br>
	<div class="row">
		<?php echo CHtml::dropDownList('status',@$model->status, DeliveryRecord::$states,array('prompt'=>'Select')); ?>
	</div>
	<div class="row buttons">
		<?php  echo CHtml::submitButton('update',array('class'=>'update'));?>
	</div>

	<div class="row buttons">
		<?php  echo CHtml::submitButton('Confirm',array('class'=>'confirm'));?>
	</div>

	<div class="row buttons">
		<?php  echo CHtml::submitButton('Cancel',array('class'=>'cancel'));?>
	</div>

<script type="text/javascript">
	$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tab = $('#<?=$_GET["tabid"];?>');
			var panel = $('#<?=$_GET["tabid"];?>').data('panel');

			tab.unbind('reload_delivery_record_grid').bind('reload_delivery_record_grid', function(){
				$('#<?=$_GET["tabid"];?>delivery-record-grid', tab.data('panel')).yiiGridView('update');
				return false;
			});

			$('.update',win).on('click',function(){
				var form = new FormData(document.getElementById("delivery-record-acr_form"));
					 $.ajax({
					            url: '<?=$url."?id=".$id."&&yt0=update"?>',
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
						             tab.trigger('reload_delivery_record_grid');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });	
				return false;
			});

			$('.confirm',win).on('click',function(){
				var form = new FormData(document.getElementById("delivery-record-acr_form"));
					 $.ajax({
					            url: '<?=$url."?id=".$id."&&yt0=confirm"?>',
					            type: "get",
					            data: [],
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
						             tab.trigger('reload_delivery_record_grid');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });	
				return false;
			});

			$('.cancel',win).on('click',function(){
				var form = new FormData(document.getElementById("delivery-record-acr_form"));
					 $.ajax({
					            url: '<?=$url."?id=".$id."&&yt1=cancel"?>',
					            type: "get",
					            data: [],
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
						             tab.trigger('reload_delivery_record_grid');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });	
				return false;
			});
			 



	});
</script>


<?php $this->endWidget();?>
		
