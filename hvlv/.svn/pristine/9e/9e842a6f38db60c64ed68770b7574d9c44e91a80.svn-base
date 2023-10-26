<h1><?=$this->t('Create RTS Shipment Invoice');?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'hbn',
		'created',
		array('name' => 'status', 'value' => $model->getStatus()),
        'ref',
		array('name' => 'Estimate Amount', 'type' => 'raw','value' => $model->getRtsEstiInvoice())
	)
));
?>
<br />
<p>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'rts-invoice-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
	<?php echo CHtml::label('Amount','for_rts_invoice'); ?>
	<?php echo CHtml::textField('amount',$model->getRtsEstiInvoice()); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Create')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

</p>

<script type="text/javascript">
$(function(){
	var win = $("#jqmw_<?=$_GET['tabid'];?>");
	$('#rts-invoice-form', win).on('success', function(){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
});
</script>