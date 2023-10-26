<?php
$this->widget('zii.widgets.grid.CGridView', [
    'id'=>$_GET["tabid"].'_custom_grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(true, 30),
    'filter'=>$model,
    'columns'=>[
        ['name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => @$data->shipment->id))."\" class=\"tab_link\" title=\"".@$data->shipment->hbn."\">".@$data->shipment->hbn."</a>"',],
        ['name'=>'ref','value' => '@$data->shipment->ref'],
        ['name'=>'agent_id','value' => '@$data->shipment->agent_id'],
        'barcode',
        'sno',
        ['header'=>'Warehouse','name' => 'location.warehouse.name',
            'filter'=>CHtml::dropDownList('WmsRackShipment[wid]', $model->wid, Org::dptList(), array('prompt'=>$this->t('All'))),],
        ['name' => 'shipment.status', 'value' => 'empty($data->shipment)?"":@$data->shipment->getStatus()',
            'filter'=>CHtml::dropDownList('WmsRackShipment[status]', $model->status, $this->t(ImParcel::$states), ['prompt'=>$this->t('All')]),],
        ['name' => 'shipment.pkg'],
        ['name' => 'shipment.weight'],
        ['name' => 'location_code','type'=>'raw','value' => '@$data->getLocation(true)'],
        ['name' => 'ground_label','type'=>'raw','value' => '$data->location->ground_label'],
        ['name' => 'ground_label_in_time','type'=>'raw','value' => '$data->location->ground_label_in_time'],
        ['header' => 'Pallets Number','value' => '@$data->shipment->mdata["amzon_pallet"]'],
        ['name' => 'memo','value' => '@$data->shipment->can'],
        'created'
    ],
]); ?>

<script type="text/javascript">
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel = tab.data('panel');
    
    var resetFilters = function(){
        $('.search-form form', panel).trigger('reset');
        $('#<?=$_GET["tabid"];?>_custom_grid', panel).yiiGridView('update', {data: 'ImParcel=reset'});
    };

    tab.bind('onOpen', function(){
        $('#<?=$_GET["tabid"];?>_custom_grid', panel).yiiGridView('update');
    });

    $('.search-button', panel).on('click', function(){
        $('.search-form', panel).toggle();
        return false;
    });

    $('.search-form form', panel).on('submit', function(){
        $('#<?=$_GET["tabid"];?>_custom_grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()});
        return false;
    }).find('.reset_btn').on('click', resetFilters);

});
</script>