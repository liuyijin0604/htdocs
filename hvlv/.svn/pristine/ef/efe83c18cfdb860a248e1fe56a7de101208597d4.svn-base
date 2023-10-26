<h1><?=strtoupper($this->t('Invoice List'));?></h1>


<div class="form">
<?php 
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'job-acr_form',
	'method'=>'post',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>["class"=>"ifrm-form"]
	),
	);
?>
<div class="form-group">
<?php
echo "From:",CHtml::textField('CargoProcess[searchFromS]',@$model->searchFrom,["class"=>'date_input  form-control']);
echo "To:",CHtml::textField('CargoProcess[searchToS]',@$model->searchTo,["class"=>'date_input  form-control']);
echo "</br>";
echo CHtml::submitButton($this->t('Search'), array('class' => 'save_btn form-control','id'=>'savebb','style' => 'width:10em;display:inline;')),'&nbsp;'; 
echo CHtml::submitButton($this->t('Export'), array('class' => 'export form-control','id'=>'export','style' => 'width:10em;display:inline;')); 
?>
</div>
<?php

$columns = array(
			['header' => 'Deliveried Status', 'value' => 'CargoProcess::$processTypes_driver_user[$data->isDriverDeliveried()]','filter'=>CHtml::hiddenField('CargoProcess[searchFrom]',@$model->searchFrom).CHtml::hiddenField('CargoProcess[searchTo]',@$model->searchTo)],
			'hbn',
    		//'pickup',
			array('name' =>'shipment.cbm','value'=>'$data->shipment->mdata[\'total_cbm\']'),
         	array('name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()','cssClassExpression' => '$data->getShowColor()','filter'=>CHtml::textField('CargoProcess[deliveryBookingTime]',@$model->getBookingTime(),['class'=>'form-control'])),
         	array('header'=>'dimension','type'=>'raw','value'=>'$data->shipment->getDimension()'),
         	array('name'=>'shipment.weight','type'=>'raw','value'=>'$data->shipment->weight.\'kg\''),
         	array('header'=>'distance','value'=>'$data->shipment->getDeliveryDistance().\'km\''),
         	'shipment.pkg',
			array('header'=>'Delivery Fee','type'=>'raw','value'=>'"$".@$data->getCRuleInvoice()["deliveryFee"]'),
			array('header'=>'Distance Fee','type'=>'raw','value'=>'"$".@$data->getCRuleInvoice()["distanceFee"]'),
			array('header'=>'Fuel Charge','type'=>'raw','value'=>'"$".@$data->getCRuleInvoice()["fuelCharge"]'),
			array('header'=>'amount','type'=>'raw','value'=>'"$".@$data->getCRuleInvoice()["amount"]'),);


	$columns[]= [
         		'header'=>'Operation',
         		'class'=>'oButtonColumn',
				'template'=>'{Detail}',
				'buttons'=>[
					'Detail' => [
						'url'=>' Yii::app()->createURL("dplatform/invoice/viewCargoInvoice")."?id=".$data->id',
						'imageUrl'=>false,
						'visible'=>'true',
						'options' => ['class' => 'jqm_link grid_view_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
					],
				],
			];

 $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'job_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->getJobList('my'),
    'template' => "{summary}\n{items}\n{pager}",
    'afterAjaxUpdate'=>'function(){initButtons();}',
    'columns'=>$columns,
    ),
    
); ?>

<?php $this->endWidget();?>

