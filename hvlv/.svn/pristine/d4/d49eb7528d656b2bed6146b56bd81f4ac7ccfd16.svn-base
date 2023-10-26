<?php
$parcel = new ImParcel('search');
if(isset($_GET['ImParcel'])){
    $parcel->unsetAttributes();
    $parcel->attributes=$_GET['ImParcel'];
}
$mids = $model->getFids();
$parcel->mids = empty($mids) ? [-1] : $mids;

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => $_GET["tabid"] . '_rtslist-grid',
    'cssFile' => false,
    'dataProvider' => $parcel->search(),
    'filter' => $parcel,
    'columns' => array(
        array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
        array('header' => 'Original Connote', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->getRTSOrgShipNoId()))."\" class=\"tab_link\" title=\"".$data->getRTSOrgShipNo()."\">".$data->getRTSOrgShipNo()."</a>"',),
        array('name' => 'status', 'value' => '$data->getStatus()',
            'filter' => CHtml::dropDownList('ImParcel[status]', $parcel->status, $this->t(ImParcel::$states), array('prompt' => $this->t('All'))),),
        'weight',
        array('header' => 'Postcode','type' => 'raw', 'value' => 'empty($data->cnee)? "" : $data->cnee->postcode'),
        array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name'),
        array('header' => 'Estimate Invoice','type' => 'raw', 'value' => '$data->getRtsEstiInvoice()'),
        array('header' => 'Invoice No.','type' => 'raw', 'value' => '$data->getRtsInvoiceNo()'),
         array(
            'class' => 'oButtonColumn',
            'template' => '{update}',
            'buttons' => array(
                'update' => array(
                    'imageUrl' => false,
                    'url' => 'Yii::app()->createURL("rts/createInvoice", array("id" => $data->id))',
                    'visible' => 'true',
                    'label' => $this->t('Invoice'),
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'title' => '$data->hbn'),
                ),
            ),
        ),
    ),
));
?>

<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');
        tab.on('onOpen', function(){
            $('#<?=$_GET['tabid'];?>_rtslist-grid', panel).yiiGridView('update');
        });
    });
</script>
