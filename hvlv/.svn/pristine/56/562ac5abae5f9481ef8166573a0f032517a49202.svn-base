<br>
<div class="form">
<?php 
$url = $this->createUrl('tlaTask/createTlaTask');
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'add_form',
	'enableAjaxValidation'=>false,
	'action'=> $url)
	);
	$model->type = ImportsSystemFuel::COURIER_TYPE;
?>	
		<div class="row">
			<div class="row rowcol">
					<?php echo $form->labelEx($model,'type'); ?>
					 <?php echo $form->dropDownList($model,'type', ImportsSystemFuel::$types,array('prompt'=>'Select')); ?>
			</div>
		</div>

        <div class="row">
			<div class="row rowcol">
                <?php echo $form->labelEx($model,'rate'); ?>
                 <?php echo $form->numberfield($model,'rate',["oninput"=>"if(value>1)value=1;if(value<-1)value=-1"]); ?>(-1~1)
            </div> 
        </div>
      
        <div class="row rowcol rowleft">
                <?php echo $form->labelEx($model,'from_day'); ?>
                 <?php echo $form->textField($model,'from_day',['class'=>"date_input"]); ?>
        </div>

        <div class="row rowcol rowleft">
                <?php echo $form->labelEx($model,'to_day'); ?>
                 <?php echo $form->textField($model,'to_day',['class'=>"date_input"]); ?>
        </div>

         <br>
        <div class="row">
                 <?php echo CHtml::submitButton('submit',array("class"=>"form-control update","style"=>"width:250px;")); ?>
        </div>

<script type="text/javascript">
	$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tab = $('#<?=$_GET["tabid"];?>');
			var panel = $('#<?=$_GET["tabid"];?>').data('panel');
			tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function(){
				$.post('files/status', {'id': $(this).data('id'), 'status': $(this).val() });
			});
			

			 $('.update',win).on('click',function(){
				if( confirm('Are you sure to save?')){
					var form = new FormData(document.getElementById("add_form"));
					 $.ajax({
					            url: '<?=$this->createUrl('import/createSystemFuel')?>',
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					            	r = JSON.parse(r);
					                if(r.done==true)
					                 {
										myApp.notice('Done', 5000);
										$('.popCancel',win).click();
									 }else
									 {
										myApp.alert(r.msg, false);   
						             }
						             tab.trigger('reload_system_fuel_grid');
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


<?php $this->endWidget();?>
		
