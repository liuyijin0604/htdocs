<br>
<div class="form">
<?php 
$url = $this->createUrl('tlaTask/createDisputeType');
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'add_tla_task_form',
	'enableAjaxValidation'=>false,
	'action'=> $url)
	);
?>

		<div class="row">
				<?php echo $form->labelEx($model,'content'); ?>
				 <?php echo $form->textField($model,'content'); ?>
		</div>

		<div class="row">
				<?php echo $form->labelEx($model,'dpt_id'); ?>
				 <?php echo $form->dropDownList($model,'dpt_id', Org::dptList(),array('prompt'=>'Select')); ?>
		</div>

		<div class="row">
				<?php echo $form->labelEx($model,'invoice_type'); ?>
				<?php
					$types = explode(",",$model->invoice_type);
					foreach(Invoice::$types as $v => $c)
					{
						echo '<label style="width:180px;float:left;">'.CHtml::checkBox('invoice_type[]', in_array($v,$types), ['value' => $v]).' '.$c.'</label>';
					}
				?>
		</div>

		<div class="row">
				<?php echo $form->labelEx($model,'shipment_type'); ?>
				<?php
					$types = explode(",",$model->shipment_type);
					foreach(CsFaq::$shipment_types as $v => $c)
					{
						echo '<label style="width:180px;float:left;">'.CHtml::checkBox('shipment_type[]', in_array($v,$types), ['value' => $v]).' '.$c.'</label>';
					}
				?>
		</div>


		<div class="row">
			<div>
				<?php echo $form->labelEx($model, 'Customer'); ?> 
			</div>
			<input class="width_item_input" type="hidden" id='CsFaq[agent_id]' value='' name='CsFaq[agent_id]'> 
			<?php
			$acname = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => '',
				'options' => array(
					'showAnim' => 'fold',
					'minLength' => 2,
					'delay' => 200,
					'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
					'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'class' => 'required',
					'size' => '20',
				),
			));
			?> 
			<a>eg: "Top Logistics".</a>
		</div>

		<div class="row buttons" id="owner_ac">
			<div class="row">
				<?php echo CHtml::label('Assign User','Assign User'); ?>
				<?php echo CHtml::hiddenField('user_id');
					$acname = empty($_GET["tabid"])? 'owner_ac' : $_GET["tabid"].'_owner_ac';
					$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
						'name' => $acname,
						'sourceUrl' => array('org/opSuggest'),
						'value' => '',
						'options' => array(
								'showAnim' => 'fold',
								'minLength' => 2,
								'delay' => 200,
								'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
								'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val(""); return false; }',
						),
						'htmlOptions' => array(
							'size' => '50',
						),
				));
				?>

				<?php echo $form->error($model,'to_id'); ?>
			</div>
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
					var form = new FormData(document.getElementById("add_tla_task_form"));
					 $.ajax({
					            url: '<?=$this->createUrl('tlaTask/createDisputeType')?>',
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
						               if(r=='done')
						                {
									myApp.notice('Done', 5000);
									$('.popCancel',win).click();
								}else
								{
									myApp.alert(r, false);   
						            	}
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
		
