<h1><?=$this->t('Swap Shipment');?></h1>

<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-swaparcel-form',
	'enableAjaxValidation'=>false,
));
?>
	<div class="row">
	<?php
	echo CHtml::label('Shipments','recs');
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'excon-man-grid',
	'cssFile' => false,
	'dataProvider' => $model->swapParcelSearch(),
	'filter' => null,
	'summaryText' => false,
	'columns'=>array(
		array('type' => 'raw', 'value' => '"<input class=\"radio\" type=\"radio\" name=\"pid\" value=\"".$data->id."\" checked />"', 'htmlOptions' => array('align' => 'center')),
		'hbn',
		array('name' => 'agent_name', 'value' => 'empty($data->agent)? "" : $data->agent->shortName(2)',),
		'dvalue',
		'weight',
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name',),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array('header' => 'Address', 'value' => '$data->cnee->fullAddress(array("city"))'),
	),
)); ?>
</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Swap'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	
	$('form#consol-swaparcel-form', win).on('success', function(e, r){
		win.data('opener').trigger('update-parcels-grid');
		win.jqmHide();
	});
});
</script>