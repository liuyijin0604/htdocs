<?php
$tabid = $_GET['tabid'];
?>
<style>
    .column_green {
        background-color: greenyellow;
    }

    .column_red {
        background-color: pink;
    }

    .column_yellow {
        background-color: orange;
    }

    .column_red_1 {
        background-color: red;
    }
</style>
<?php

$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
    'id' => $_GET["tabid"] . '_deconsolidation_grid_new',
    'cssFile' => false,
    'dataProvider' => $model->search('due_time'),
    'filter' => $model,
    'columns' => [
        array(
            'name' => 'connote',
            'type' => 'raw',
            'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"',
        ),
        array('header' => 'Ref', 'type' => 'raw', 'value' => '$data->shipment->ref'),
        //array('header' => 'Agent Id', 'type' => 'raw', 'value' => '$data->shipment->agent_id'),
        //['header' => 'Consol No', 'type' => 'raw', 'value' => 'empty($data->shipment->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->shipment->consol_id))."\" class=\"tab_link\" title=\"".@$data->shipment->consol->no."\">".@$data->shipment->consol->no."</a>"'],
        array('header' => 'WDT Consol', 'type' => 'raw', 'value' => 'empty($data->getWdtConsolId())? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->getWdtConsolId()))."\" class=\"tab_link\" title=\"".$data->getWdtConsolNo()."\">".$data->getWdtConsolNo()."</a>"'),
        array('name' => 'service', 'value' => '$data->getServiceType()', 'filter'=>CHtml::dropDownList('Deconsolidation[service]',$model->service, $this->t(Consol::$services),['prompt'=>$this->t('All')])),
        array('header' => 'AWB No.', 'type' => 'raw', 'value' => '!empty($data->shipment->consol->awb) ? $data->shipment->consol->awb : @$data->shipment->consol->container_no'),
        //array('header' => 'Postcode', 'type' => 'raw', 'value' => '$data->shipment->cnee->postcode'),
        array('header' => 'Memo', 'type' => 'raw', 'value' => '$data->shipment->can'),
        array('header' => 'Unpacking Date', 'type' => 'raw', 'value' => '@$data->shipment->consol->mdata["ContainerUnloadDate"]'),
        array('header' => 'Status', 'type' => 'raw', 'value' => '$data->shipment->getStatus()'),
        //array('header' => 'Note', 'type' => 'raw', 'value' => '$data->shipment->note'),
        array('header' => 'Create Time', 'type' => 'raw', 'value' => '$data->create_time'),
        //array('header' => 'Due Date', 'type' => 'raw', 'value' => '$data->getDueDate()', 'cssClassExpression' => '$data->getDueDateColor()'),
        array('name' => 'due_time', 'type' => 'raw', 'cssClassExpression' => '$data->getDueDateColor()'),
        array('header' => 'Goods Available Address', 'type' => 'raw', 'value' => '$data->shipment->getAvailabelLabel()'),
        array('header' => 'Package', 'type' => 'raw', 'value' => '$data->shipment->pkg'),
        array('header' => 'Weight', 'type' => 'raw', 'value' => '$data->shipment->weight'),
        array('header' => 'Rack', 'type' => 'raw', 'value' => '$data->shipment->rack'),
        array('header' => 'Assign User', 'type' => 'raw', 'value' => 'CHtml::dropDownList("assign_user_" . $data->id,"", $data->getAvailableUsers())'),
        [
            'class' => 'oButtonColumn',
            'template' => '{Print Label}&nbsp;{Export List}',
            'buttons' => [
                'Print Label' => [
                    'url' => ' Yii::app()->createURL("imParcel/export")."?typ=plabel&TplParcel%5Bconsol_no%5D=" . $data->getWdtConsolNo()',
                    'imageUrl' => false,
                    'visible' => 'true',
                    'options' => ['class' => 'grid_print_btn', 'target' => '_blank'],
                ],
                'Export List' => [
                    'url' => ' Yii::app()->createURL("imParcel/export")."?typ=&TplParcel%5Bconsol_no%5D=" . $data->getWdtConsolNo()',
                    'imageUrl' => false,
                    'visible' => 'true',
                    'options' => ['class' => 'grid_print_btn', 'target' => '_blank'],
                ],
            ],
        ],
        array('header' => 'Action', 'type' => 'raw', 'value' => 'CHtml::Button("Assign To Warehouse", ["id" => $data->id, "class" => "label-preparation-done"])'),  
    ],
]);


?>

<script type="text/javascript">
    $(function() {
        var tab = $('#<?= $_GET["tabid"]; ?>');

        tab.unbind('reload_deconsolidation_grid').bind('reload__deconsolidation_grid', function() {
            $('#<?= $_GET["tabid"]; ?>_deconsolidation_grid_new', tab.data('panel')).yiiGridView('update');
            $('#<?= $_GET["tabid"]; ?>_deconsolidation_grid_warehouse', tab.data('panel')).yiiGridView('update');
            $('#<?= $_GET["tabid"]; ?>_deconsolidation_grid_error', tab.data('panel')).yiiGridView('update');
            $('#<?= $_GET["tabid"]; ?>_deconsolidation_grid_complete', tab.data('panel')).yiiGridView('update');
            return false;
        });

        var panel = tab.data('panel');
        $('body').on('click', '.label-preparation-done', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            let deconsolidationId = e.target.id;
            let assignedUser = $('#assign_user_' + deconsolidationId).val();
            let formData = new FormData();
            formData.append('id', deconsolidationId);
            formData.append('user_id', assignedUser);
            $.ajax({
                url: '<?= $this->createUrl("deconsolidation/assignToWarehouse") ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                enctype: 'multipart/form-data',

                success: function(response) {
                    tab.trigger('reload_deconsolidation_grid');
                    myApp.notice("Assign Done");
                }
            })
        });

        $('body').on('click', '.error-check-done', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            let deconsolidationId = e.target.id.split("_").pop();
            let formData = new FormData();
            formData.append('id', deconsolidationId);
            $.ajax({
                url: '<?= $this->createUrl("deconsolidation/errorCheckDone") ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                enctype: 'multipart/form-data',

                success: function(response) {
                    tab.trigger('reload_deconsolidation_grid');
                    myApp.notice("Assign Done");
                }
            })
        });
    });
</script>