<br>
<div class="form">
<?php 
$url = $this->createUrl('customerService/addTicket');
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cs_add_ticket_form',
	'enableAjaxValidation'=>false,
	'action'=> $url)
	);
?>

        <div class="row">
                <?php echo CHtml::label('mhbns','ShipmentQuestionSubmit[mhbns]'); ?>
                <?php echo CHtml::textArea('ShipmentQuestionSubmit[mhbns]','',array('cols'=>60, 'rows' => 5,"class"=>"form-control")); ?>
                <p><small>Up to 200 numbers.</small></p>
        </div>
        <br>
        <div class="row">
                <?php echo CHtml::label('faq','faq'); ?>
                <?php echo CHtml::dropDownList('faq','查件',CsFaq::getFaqList(0),["class"=>"form-control"]); ?>
        </div>
        <br>
        <div class="row">
                <?php echo CHtml::label('email','email'); ?>
                <?php echo CHtml::textField('ShipmentQuestionSubmit[email]','',["class"=>"form-control"]); ?>
        </div>
        <br>
        <div class="row">
                <?php echo CHtml::label('phone','phone'); ?>
                <?php echo CHtml::textField('ShipmentQuestionSubmit[phone]','',["class"=>"form-control"]); ?>
        </div>
        <br>
        <div class="row">
                <?php echo CHtml::label('note','ShipmentQuestionSubmit[c_note]'); ?>
                 <?php echo CHtml::textArea('ShipmentQuestionSubmit[c_note]','',array('cols'=>60, 'rows' => 10,"class"=>"form-control")); ?>
        </div>
        <br>
         <div class="row">

                 <?php echo CHtml::hiddenField('ShipmentQuestion[mdata][items]','',array('cols'=>60, 'rows' => 3,"class"=>"form-control")); ?>
        </div>
        <br>
        <div class="row">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Upload Pictures'),'Upload Pictures'); ?>
                 <?php
                 echo "<p><small>name the file as referenceNumber-1.jpg to match the reference numbers</small></p>";
                ?>
                 <?php
		              $this->widget('CMultiFileUpload', array(
		                 'model'=>$model,
		                 'attribute'=>'photos',
		                 'accept'=>'jpg|gif|png',
		                 'htmlOptions'=>["accept"=>"image/gif, image/jpeg"],
		                 'options'=>array(
		                 ),
		                 'denied'=>'File is not allowed',
		                 'max'=>10, // max 10 files
              ));
            ?>
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
					var form = new FormData(document.getElementById("cs_add_ticket_form"));
					 $.ajax({
					            url: '<?=$this->createUrl('customerService/addTicket')?>',
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
						             tab.trigger('reload_cs_grid');
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
		
