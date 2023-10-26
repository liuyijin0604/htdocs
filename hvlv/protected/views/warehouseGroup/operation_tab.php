<h3>Operation</h3>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'warehouse-group-acr_form',
	'enableAjaxValidation'=>false,
				'action'=> $this->createUrl('warehouseGroup/saveStatus',array('id'=>$model->id)),
));
?>

<div class="row rowcol-left">
				<?php echo CHtml::button(($model->status==UserWarehouseGroupRule::ACTIVE)?'Inactive':'Active', array('class' => 'saveStatus'));?>
</div>

<?php $this->endWidget();?>

<script type="text/javascript">
	$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tab = $('#<?=$_GET["tabid"];?>');
			var panel = $('#<?=$_GET["tabid"];?>').data('panel');

			 $('.saveStatus',win).on('click',function(){
				$.get('<?=$this->createUrl("warehouseGroup/saveStatus")."?id=".$model->id?>',function(r){
								myApp.notice('Done', 5000);
								$('#warehouse-group-rule-grid', tab.data('panel')).yiiGridView('update');
				});
			});
	});
</script>
		
