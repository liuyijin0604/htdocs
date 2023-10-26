<h3>Operation-<?=$model->no?></h3>
<br>
<div class="form">
<?php 
	$id = $model->id;
	$url = $this->createUrl('deliveryRecord/'.$type);
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'delivery-record-acr_form',
	'enableAjaxValidation'=>false,
	'action'=> $url."?id=".$id)
	);
?>
	
		<div class="row">
			<?php echo CHtml::hiddenField('id', @$model->id, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'BookingTime')); ?>
			<div class="row rowcol">
				<?php echo CHtml::label('Booking Time','Booking Time'); ?>
				<?php echo CHtml::textField('booking_time', @$model->booking_time, array('size' => 30, 'maxlength' => 50, 'class'=>"datetime_input")); ?>
			</div>
			<div class="row rowcol">
				<?php echo CHtml::label('Name','Name'); ?>
				<?php echo CHtml::textField('name', @$model->name, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Name')); ?>
			</div>
			<div class="row rowcol">
				<?php echo CHtml::label('Mobile','Mobile'); ?>
				<?php echo CHtml::textField('mobile', @$model->mobile, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Mobile')); ?>
			</div>
			<div class="row rowcol">
				<?php echo CHtml::label('Damage','Damage'); ?>
				<?php echo CHtml::dropDownList('damage',@$model->damage, DeliveryRecord::$damage_states,array('prompt'=>'Select'))?>
			</div>
		</div>
		<div class="row">
			<div class="row rowcol">
				<?php echo CHtml::label('Rego','Rego'); ?>
				<?php echo CHtml::textField('rego', @$model->rego, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Rego')); ?>
			</div>
			<div class="row rowcol">
				<?php echo CHtml::label('Pallet','Pallet'); ?>
				<?php echo CHtml::textField('plt', @$model->plt, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Pallet')); ?>
			</div>
			<div class="row rowcol">
				<?php echo CHtml::label('Packages','Packages'); ?>
				<?php echo CHtml::textField('pkg', @$model->pkg, array('size' => 30, 'maxlength' => 50, 'placeholder' => '/Packs')); ?>
			</div>
		</div>

		<div class="row">
			<div class="row rowcol">
				<?php echo CHtml::label('Note','Note'); ?>
				<?php echo CHtml::textarea('note', @$model->note, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'Note')); ?>
			</div>
			<div class="row rowcol">
				<?php echo CHtml::label('Task','Task'); ?>
				<?php echo CHtml::textarea('task', @$model->task, array('size' => 30, 'maxlength' => 50, 'placeholder' => 'task')); ?>
			</div>
		</div>

	<br>
	<br>
	<br>
	<div class="row buttons">
	<?php  echo CHtml::submitButton('Save',array('class'=>'update'));?>
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
					save();	
				return false;
			});
			 
			function save()
			{
					var form = new FormData(document.getElementById("delivery-record-acr_form"));
					 $.ajax({
					            url: '<?=$url."?id=".$id?>',
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
			}

	});
</script>


<?php $this->endWidget();?>
		
