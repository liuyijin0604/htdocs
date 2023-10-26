
<?php
/* @var $this ShipmentScanController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Manifest Address Report',
);
//

?>
<div class="pane">
<div style="position: absolute;right: 20px;"> 
<a class="export_search" target="_blank" href="<?=$this->createUrl('imcoConsol/exportAddressManifestReport', ['type' => 'xls']);?>">
	<div style="background-position:-48px -688px" class="icon"></div> Export Manifest Report
</a>
</div>
<h1>Address Report</h1>	

<div style="width: 100%">
<?php


    $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'address-report-manifest-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(true),
    'filter'=>$model,
    'columns'=>array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
        'ref',
            array('name'=>'agent_name', 'value'=>'@$data->agent->name'),
        array('name' => 'status', 'value' => '$data->getStatus()', 
            'filter'=>CHtml::dropDownList('ImParcel[status]', $model->status, $this->t(ImParcel::$states), array('prompt'=>$this->t('All'))),),
        array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
        'cnee.postcode',
        array('header'=>'Submit Address','value'=>'@$data->mdata["oldAddress"]["address"]." ".@$data->mdata["oldAddress"]["suburb"]." ".@$data->mdata["oldAddress"]["state"]." ".@$data->mdata["oldAddress"]["postcode"]'),
        array('header'=>'Modified Address','value'=>'@$data->mdata["newAddress"]["address"]." ".@$data->mdata["newAddress"]["suburb"]." ".@$data->mdata["newAddress"]["state"]." ".@$data->mdata["newAddress"]["postcode"]'),
        array('header'=>'Submit Suburb','value'=>'@$data->mdata["oldAddress"]["suburb"]'),
        array('header'=>'Modified Suburb','value'=>'@$data->mdata["newAddress"]["suburb"]'),  
        array('header'=>'Modified Suburb','value'=>'@$data->mdata["newAddress"]["suggest_suburb"]'),      
		array('name' => 'created'),
		array('header' => 'isDiff','value'=>'@$data->mdata["is_diff"]','filter'=>CHtml::textfield('ImParcel[is_diff]',$model->is_diff)),
        array('header' => 'editStreetCode','value'=>'@Addr::getEditStreetCode(@$data->mdata["editStreetCode"])','filter'=>CHtml::dropDownList('ImParcel[editStreetCode]', $model->editStreetCode, $this->t(Addr::$editStreetCode), ['prompt'=>'All']))
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
        $(this).attr('href', $(this).attr('href') + '&' + q);
    });
      
    });
</script>
        
