<div style="position:absolute">
<p>Shipments: <b><?=$model->totShipments();?></b> &nbsp; Total Weight: <b><?=$model->totWeight();?>KG</b> &nbsp <?php
?></p>
</div>
<div style="text-align:right">
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
</div>

<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
	<li><a href="<?=$this->createUrl('exdirConsol/export', array('id' => $model->id));?>" target="_blank">Manifest</a></li>
	<?php if($model->status > 10): ?>
	<li><a href="<?=$this->createUrl('exdirConsol/download', array('type' => 'invoices', 'id' => $model->id));?>" target="_blank">Invoices</a></li>
	<?php endif; ?>
	</ul>
</div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-form',
	'enableAjaxValidation'=>false,
));
?>
	<?php echo $form->errorSummary($model); ?>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'exrate'); ?>
		1AUD = <?php echo $form->textField($model,'exrate',array('size'=>10,'maxlength'=>15)); ?> <a href="http://www.customs.gov.au/site/page4277.asp" target="_blank"><div class="icon" style="background-position:-96px -384px"></div></a>
		<?php echo $form->error($model,'exrate'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<?php echo $model->status < 20 ? '<div style="position: absolute;"><a class="jqm_link" href="'.$this->createURL('excoConsol/addParcel', array('id' => $model->id)).'"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('Add more orders').'</a></div>' : '';

$parcel = new ExDirect('search');
if(isset($_GET['ExDirect'])){
	$parcel->unsetAttributes();
	$parcel->attributes=$_GET['ExDirect'];
}
$parcel->consol_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_exparcel-grid',
	'cssFile' => false,
	'dataProvider'=>$parcel->search(),
	'filter'=>$parcel,
	'columns'=>array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("exDirect/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
		'ref',
		array('name' => 'agent_name', 'value' => 'empty($data->agent)? "" : $data->agent->shortName(2)',),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ExDirect[status]', $parcel->status, $this->t(ExDirect::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name',),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array('name' => 'cnee_addr', 'value' => '$data->cnee->fullAddress(array("city"))'),
		array('name' => 'bwf', 'header' => 'Warnings', 'type' => 'raw', 'value' => '$data->getWarnings()','filter'=>CHtml::dropDownList('ExDirect[bwf]', $parcel->bwf, $this->t(ExDirect::$bwfs), array('prompt'=>$this->t('All'))),),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{delete}',
			'buttons'=>array(
				'delete' => array(
					'imageUrl'=>false,
					'visible' => '$data->consol->status < 20',
					'options' => array('class' => 'grid_delete_btn', 'label'=>$this->t('Remove from Consol.')),
					'url' => 'Yii::app()->createURL("excoConsol/removeParcel", array("id" => $data->id))',
				),
			),
		),
	),
));
?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	
	$('form#consol-form', tab.data('panel')).on('success', function(e, r){
		tab.load();
	});

	$(document).off('click','#<?=$_GET["tabid"];?>_exparcel-grid a.grid_delete_btn');
	
	tab.off('update-parcels-grid').on('update-parcels-grid', function(){
		$('#<?=$_GET["tabid"]?>_exparcel-grid', tab.data('panel')).yiiGridView('update');
	});
});
</script>