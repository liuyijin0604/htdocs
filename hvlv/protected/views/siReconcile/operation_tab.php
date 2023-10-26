<h3>Operation</h3>
<div class="form">
<?php 
	$id = $model->id;
	$url = $this->createUrl('siReconciliation/updateLine');
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'si-reconciliation-acr_form',
	'enableAjaxValidation'=>false,
	'action'=> $url."?id=".$id)
	);
?>
<?php echo CHtml::hiddenField('id',$id)?>
<?php if(in_array($model->parent->parent->type,[SiReconcile::TYPE_BROKER,SiReconcile::TYPE_MANUAL])):?>
<div class="row buttons">
	<label><?=$model->parent->parent->type==SiReconcile::TYPE_BROKER?"Shipment HBN:":"Consol No:"?></label>

	 <?php   echo $form->textField($model,'ref'); ?>
</div>
<div class="row buttons">
	<?php  echo CHtml::submitButton('save',array('class'=>'save'));?>
</div>
<?php else:?>

<div class="row buttons">
	<?php  echo CHtml::submitButton('refresh parcel link',array('class'=>'refresh'));?>
</div>
<?php endif;?>
<script type="text/javascript">
	$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tab = $('#<?=$_GET["tabid"];?>');
			var panel = $('#<?=$_GET["tabid"];?>').data('panel');

			tab.unbind('reload_reconcile_grid').bind('reload_reconcile_grid', function(){
				$('#<?=$_GET["tabid"];?>_reconcile_grid', tab.data('panel')).yiiGridView('update');
				return false;
			});

			tab.unbind('reload_excofile_grid').bind('reload_excofile_grid', function(){
				$('#<?=$_GET["tabid"];?>_excofile-grid', win).yiiGridView('update');
				return false;
			});
			tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
				$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
			});

			$('.refresh',win).on('click',function(){
				if( confirm('Are you sure to refresh?')){	
					save('refresh','reload_reconcile_grid');	
				}
				return false;
			});

			$('.save',win).on('click',function(){
				if( confirm('Are you sure to save and refresh?')){	
					save('save','reload_reconcile_grid');	
				}
				return false;
			});
			 
			function save($action,$trigger)
			{
				var form = new FormData(document.getElementById("si-reconciliation-acr_form"));
				$.ajax({
				            url: '<?=$this->createUrl('siReconcile/updateLine')."?id=".$id?>'+"&&action="+$action,
				            type: "post",
				            data: form,
				            processData: false,
				            contentType: false,
				            success: function(r) {
				            	r = JSON.parse(r);
				                if(r.done)
				                 {
									myApp.notice(r.msg, 5000);
								 }else
								 {
									myApp.alert(r.msg, false);   
					             }
					             tab.trigger($trigger);
					         },
				            error: function(e) {
				                console.log(e);
				            }
				        });	
			}



	});
</script>


<?php $this->endWidget();?>
		
