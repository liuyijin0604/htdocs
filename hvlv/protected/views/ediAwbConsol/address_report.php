
<?php
/* @var $this ShipmentScanController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Address Report',
);
//
//$this->menu=array(
//	array('label'=>'Create ShipmentScan', 'url'=>array('create')),
//	array('label'=>'Manage ShipmentScan', 'url'=>array('admin')),
//);
Yii::app()->clientScript->registerScript('search', "

$('.thesearch-form form').submit(function(){
	$('#address-report-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="pane">
	<div style="position: absolute;right: 140px;"> 
<a class="tab_link"  href="<?=$this->createUrl('imcoConsol/getImparcelsNeedAddressModifiedForManifest');?>" title="IAddressM"><div style="background-position:-48px -688px" class="icon"></div> Waiting Parcels</a>
</div>
    <div style="position: absolute;right: 20px;"> 
<a href="#" class="export_search" target="_blank" data-baseurl="<?=$this->createUrl('imcoConsol/exportAddressReport', ['type' => 'xls']);?>"><div style="background-position:-48px -688px" class="icon"></div> Export Report</a>
</div>
<h1>Address Report</h1>

<div class="thesearch-form">
<?php $this->renderPartial('address_search',
	array('model'=>$model)
); ?>
</div>

<div style="width: 100%">
<?php


    $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'address-report-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(true,30,$ec),
    'filter'=>$model,
    'columns'=>array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
		'ref',
            array('name'=>'awb_name','header'=>'awb','type'=>'raw','value'=>'empty($data->consol)?"":$data->consol->awb',),
            array('name'=>'agent_name', 'value'=>'@$data->agent->name'),
        'pkg',
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ImParcel[status]', $model->status, $this->t(ImParcel::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		'cnee.postcode',
		array('header'=>'Submit Address','value'=>'@$data->mdata["oldAddress"]["address"]." ".@$data->mdata["oldAddress"]["suburb"]." ".@$data->mdata["oldAddress"]["state"]." ".@$data->mdata["oldAddress"]["postcode"]'),
       array('header'=>'Modified Address','value'=>'@$data->mdata["newAddress"]["address"]." ".@$data->mdata["newAddress"]["suburb"]." ".@$data->mdata["newAddress"]["state"]." ".@$data->mdata["newAddress"]["postcode"]'),
                array('header'=>'Submit Suburb','value'=>'@$data->mdata["oldAddress"]["suburb"]'),
                array('header'=>'Modified Suburb','value'=>'@$data->mdata["newAddress"]["suburb"]'),
		array('name' => 'created', 'value' => 'substr($data->created,0,10)'),
		array('header'=>'editStreetCode','value'=>'Addr::getEditStreetCode(@$data->mdata["editStreetCode"]);'),
                array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',//{view}
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->hbn'),
				),
			),
		),
	),
    )
    );
?>
</div>

</div>
<script>
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
        $('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize()+'&'+$('.search-form form', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '&' + q);
	});
    });
</script>
        
