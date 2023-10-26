<h3>Operation-<?=$model->temp_barcode?></h3>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol_unknown_form',
	'enableAjaxValidation'=>false
));
?>
	
	<div class="row">
		<?php echo CHtml::label('Comment','Comment'); ?>
		<?php echo CHtml::textArea('comment',$model->comment,array('rows'=>4, 'cols' => 60)); ?>
 	</div>
	<div class="row buttons">
			 <?php  echo CHtml::button('Save', array('class' => 'update_comment'))
			 ?>
	</div>
	<?php $this->endWidget();?>
</div>


<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');
	tab.unbind('reload_unknown_grid').bind('reload_unknown_grid', function(){
		$('#<?=$_GET["tabid"];?>unknown_shipment-grid', tab.data('panel')).yiiGridView('update');
		return false;
	});
	$('.update_comment',win).on('click',function(){
			var form = new FormData(document.getElementById("consol_unknown_form"));
				$.ajax({
						url:'<?=Yii::app()->createURL("consolProcess/exceptionShipmentOperation",array("id"=>$model->id))?>',
						method:"POST",
						data:form,
						processData: false,
		    			contentType: false,
						success:function(r){
							 if(r == 'done'){
										myApp.notice('Done', 3000);
								}else{
										myApp.alert(r, false);
								}
								 tab.trigger('reload_unknown_grid');
							}
				});
				return false;
		});
		
});
</script>
