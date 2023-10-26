<div class="content-padded">
	<h3>Shipment Rack Recording</h3>
	<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'wms-shipment-rack-form',
		'enableAjaxValidation' => false,
	)); ?>
		<div class="row">
			<div class="col-12" style="padding-right: 5px;">
				Code of Rack<span class="required">*</span><?php echo CHtml::textField('WmsRackShipment[code]', '', array('size' => 30, 'maxlength' => 50, 'placeholder' => 'No')); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-12" style="padding-right: 5px;">
				Change from Rack(Using in changing)<?php echo CHtml::textField('WmsRackShipment[ccode]', '', array('size' => 30, 'maxlength' => 50, 'placeholder' => 'No')); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-12" style="padding-right: 5px;">
				Barcodes of Shipments(Input xxxx:10 can record 10 pls)<span class="required">*</span><?php echo CHtml::textArea('mhbns','',array('cols'=>60, 'rows' => 15,"class"=>"form-control")); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-12" style="padding-left: 5px;">
				<?php echo CHtml::submitButton($this->t('Save'), array('class' => 'save_btn btn btn-primary btn-block')); ?>
			</div>
		</div>

	<?php $this->endWidget(); ?>

	</div>
</div>

<script type="text/javascript">
	$(function(){


	});



</script>