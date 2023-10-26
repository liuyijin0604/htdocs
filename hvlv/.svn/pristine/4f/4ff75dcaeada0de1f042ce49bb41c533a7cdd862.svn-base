
<h1><?=$this->t('Outturn Shortage Report');?></h1>

<?php 
 $states = array(
     '' => 'All',
    'New' => 'New',
    'Picked Up' => 'Picked Up',
    'Received' => 'Received',
    'Manifested' => 'Manifested',
    'Export Clear' => 'Export Clear',
    'Dispatched' => 'Dispatched',
    'Air Arrival' => 'Air Arrival',
    'Reported' => 'Reported',
    'Held' => 'Held',
    'Duty Held' => 'Duty Held',
    'Cleared' => 'Cleared',
    'Sorted' => 'Sorted',
    'Courier' => 'Courier',
    'RTS Received' => 'RTS Received',
    'RTS Reshipping waiting' => 'RTS Reshipping waiting',
    'RTS Reshipping done' => 'RTS Reshipping done',
    'RTS RTC waiting' => 'RTS RTC waiting',
    'RTS RTC done' => 'RTS RTC done',
    'RTS Done' => 'RTS Done',
    'Delivered' => 'Delivered',
    'Cancelled' => 'Cancelled',
);

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'im-parcel-shortage-grid',
    'cssFile' => false,
    'dataProvider'=>$dp,
    'filter'=>$filtersForm,
    'columns'=>array(
       array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data["id"]))."\" class=\"tab_link\" title=\"".$data["hbn"]."\">".$data["hbn"]."</a>"',),
        'ref',
       array('name'=>'awb','type' => 'raw', 'value' => '$data["awb"]',),
        'pkg',
        'scanned',
         array('name' => 'consol_no', 'type'=>'raw', 'value' => 'empty($data["consol_id"])? "" : "<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data["consol_id"]))."\" class=\"tab_link\" title=\"".$data["consol_no"]."\">".$data["consol_no"]."</a>"',),
         array('name' => 'status', 'value' => '$data["status"]',
              'filter'=>$states),             
        array('name' => 'cnee_name', 'value' => '$data["cnee_name"]',),
        'postcode',
        'weight',
        'location',
        'tranship',
        array('header'=>'ETA','name'=>'created'),
        array('name'=>'bwf','header'=>'Warnings'),
    
    ),
//    'columns'=>array(
//        array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
//        'ref',
//        array('header' => 'Awb', 'type' => 'raw', 'value' => 'empty($data->consol)? "" : $data->consol->awb',),
//        'pkg',
//        array('header' => 'Scanned', 'value' => '$data->getOutPkg()', ),
//        array('name' => 'consol_no', 'type'=>'raw', 'value' => 'empty($data->consol_id)? "" : "<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".$data->consol->no."\">".$data->consol->no."</a>"',),
//        array('name' => 'status', 'value' => '$data->getStatus()',
//            'filter'=>CHtml::dropDownList('ImParcel[status]', $model->status, $this->t(ImParcel::$states), array('prompt'=>$this->t('All'))),),
//        array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
//        'postcode',
//        'weight',
//        array('header' => 'Location', 'type' => 'raw', 'value' => '$data->getLocation()',),
//        array('header' => 'Tranship', 'value' => '$data->getTranships()'),
//        array('name' => 'created', 'value' => 'substr($data->created,0,10)'),
//        array('name' => 'bwf', 'header' => 'Warnings', 'type' => 'raw', 'value' => '$data->getWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]', $model->bwf, $this->t(ImParcel::$bwfs), array('prompt'=>$this->t('All'))),),
//    ),
)); ?>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');

        tab.bind('onOpen', function(){
            $('#im-parcel-shortage-grid', panel).yiiGridView('update');
        });


    });
</script>
