<h3>Unknown RTS <?=$model->barcode?></h3>
<div class="form">

	<?php 
	$id = $model->id;
	$url = $this->createUrl('rtsProcess/updateUnknown');
	if(isset($ids) && $ids != "")
	{
		$id = $ids;
	}
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'unknown_operation_form',
	'enableAjaxValidation'=>false,
	'action'=> $url."?id=".$id)
	);
?>

<div class="row">
	<div class="col" style="margin-right: 25px">
		<div class="row rowcol-left">
				<?php echo CHtml::label('Hbn/ref/cref','Hbn/ref/cref'); ?>
				<?php echo CHtml::textField('ref',""); ?>
				
				<?php echo CHtml::label('sno','sno'); ?>
				<?php echo CHtml::textField('sno',0); ?>

				 <?php echo CHtml::button('Update', array('class' => 'update'));?>

		</div>

	</div>
</div>
<br>


<script type="text/javascript">
	$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tab = $('#<?=$_GET["tabid"];?>');
			var panel = $('#<?=$_GET["tabid"];?>').data('panel');

			 $('.update',win).on('click',function(){
				if( confirm('Are you sure to save?')){	
					save();	
				}
				return false;
			});



			function save()
			{
				var form = new FormData(document.getElementById("unknown_operation_form"));
				 $.ajax({
				            url: '<?=$url."?id=".$model->id?>',
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
					             tab.trigger('reload_rts_unknown_grid_view');
					         },
				            error: function(e) {
				                console.log(e);
				            }
				        });	
			}

		


	});
</script>


<?php $this->endWidget();?>
		
