<h2>Split Consol</h2>
<div class="form" method="POST">
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'split-consol-form',
			'enableAjaxValidation'=>false)
		);
	?>
	<div class="row">
		<?php echo CHtml::label('Original Consol(Only original consol can be used for spliting','Original Consol No.'); ?>
		<?php echo CHtml::textField('no',''); ?>
 	</div>
 	
 	<div class="row">
	 	<div class="row rowcol">
			<?php echo CHtml::label('airline','airline'); ?>
			<?php echo CHtml::textField('airline','',array('size'=>15,'maxlength'=>50)); ?>
		</div>

		<div class="row rowcol">
			<?php echo CHtml::label('flight','flight'); ?>
			<?php echo CHtml::textField('flight','',array('size'=>15,'maxlength'=>50)); ?>
		</div>

		<div class="row rowcol">
			<?php echo CHtml::label('eta <span class="required">*</span>','eta <span class="required">*</span>'); ?>
			<?php echo CHtml::textField('eta','', array('size' => 12, 'id' => 'eta_shadow','class' => 'date_input')); ?>
		</div>
	</div>

	<div class="row">
 	<?php echo CHtml::submitButton("confirm");?>
 	</div>
</div>
<?php $this->endWidget();?>

<script type="text/javascript">
	$(function(){
		var win = $('#jqmw_<?=$_GET["tabid"];?>');
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = win.data('panel');
		tab.unbind('reload_consol_process_grid').bind('reload_consol_process_grid', function(){
			$('#<?=$_GET["tabid"];?>_consol_process_grid', tab.data('panel')).yiiGridView('update');
			return false;
		});
		$('#split-consol-form',win).data({dataType: 'html', custom_success: function(r){
			if(r.done === true){
				myApp.notice(r.msg, 5000);
			}else{
				myApp.alert(r.msg, false);
			}

			tab.trigger('reload_consol_process_grid');
			$('input[type=submit]', win).attr('disabled', false);
			return false;
		}});
	});


</script>
