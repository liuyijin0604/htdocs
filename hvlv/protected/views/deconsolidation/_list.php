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
if ($status == Deconsolidation::STATUS_NEW) {
    $this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
        'id' => $_GET["tabid"] . '_deconsolidation_grid_new',
        'cssFile' => false,
        'dataProvider' => $deconsolidation->search($depot, $status),
        'filter' => $deconsolidation,
        'columns' => [
            array(
                'header' => 'Hbn',
                'type' => 'raw',
                'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"',
            ),
            array('header' => 'Ref', 'type' => 'raw', 'value' => '$data->shipment->ref'),
            array('header' => 'Agent Id', 'type' => 'raw', 'value' => '$data->shipment->agent_id'),
            ['header' => 'Consol No', 'type' => 'raw', 'value' => 'empty($data->shipment->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->shipment->consol_id))."\" class=\"tab_link\" title=\"".@$data->shipment->consol->no."\">".@$data->shipment->consol->no."</a>"'],
            array('header' => 'Service Type', 'type' => 'raw', 'value' => '$data->getServiceType()'),
            array('header' => 'AWB No.', 'type' => 'raw', 'value' => '!empty($data->shipment->consol->awb) ? $data->shipment->consol->awb : @$data->shipment->consol->container_no'),
            array('header' => 'Postcode', 'type' => 'raw', 'value' => '$data->shipment->cnee->postcode'),
            array('header' => 'Memo', 'type' => 'raw', 'value' => '$data->shipment->can'),
            array('header' => 'Unpacking Date', 'type' => 'raw', 'value' => '@$data->shipment->consol->mdata["ContainerUnloadDate"]'),
            array('header' => 'Status', 'type' => 'raw', 'value' => '$data->shipment->getStatus()'),
            array('header' => 'Note', 'type' => 'raw', 'value' => '$data->shipment->note'),
            array('header' => 'Create Time', 'type' => 'raw', 'value' => '$data->create_time'),
            array('header' => 'Due Date', 'type' => 'raw', 'value' => '$data->getDueDate()', 'cssClassExpression' => '$data->getDueDateColor()'),
            array('header' => 'Goods Available Address', 'type' => 'raw', 'value' => '$data->shipment->getAvailabelLabel()'),
            array('header' => 'Package', 'type' => 'raw', 'value' => '$data->shipment->pkg'),
            array('header' => 'Weight', 'type' => 'raw', 'value' => '$data->shipment->weight'),
            array('header' => 'Rack', 'type' => 'raw', 'value' => '$data->shipment->rack'),
            array('header' => 'Assign User', 'type' => 'raw', 'value' => 'CHtml::dropDownList("assign_user_" . $data->id,"", $data->getAvailableUsers())'),
            array('header' => 'Action', 'type' => 'raw', 'value' => 'CHtml::Button("Assign To Warehouse", ["id" => $data->id, "class" => "label-preparation-done"])'),
        ],
    ]);
} else if ($status == Deconsolidation::STATUS_WAREHOUSE_PROCESSING) {
    $this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
        'id' => $_GET["tabid"] . '_deconsolidation_grid_warehouse',
        'cssFile' => false,
        'dataProvider' => $deconsolidation->search($depot, $status),
        'filter' => $deconsolidation,
        'columns' => [
            array(
                'header' => 'Hbn',
                'type' => 'raw',
                'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"',
            ),
            array('header' => 'Ref', 'type' => 'raw', 'value' => '$data->shipment->ref'),
            array('header' => 'Agent Id', 'type' => 'raw', 'value' => '$data->shipment->agent_id'),
            ['header' => 'Consol No', 'type' => 'raw', 'value' => 'empty($data->shipment->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->shipment->consol_id))."\" class=\"tab_link\" title=\"".@$data->shipment->consol->no."\">".@$data->shipment->consol->no."</a>"'],
            array('header' => 'Service Type', 'type' => 'raw', 'value' => '$data->getServiceType()'),
            array('header' => 'AWB No.', 'type' => 'raw', 'value' => '!empty($data->shipment->consol->awb) ? $data->shipment->consol->awb : @$data->shipment->consol->container_no'),
            array('header' => 'Postcode', 'type' => 'raw', 'value' => '$data->shipment->cnee->postcode'),
            array('header' => 'Memo', 'type' => 'raw', 'value' => '$data->shipment->can'),
            array('header' => 'Unpacking Date', 'type' => 'raw', 'value' => '@$data->shipment->consol->mdata["ContainerUnloadDate"]'),
            array('header' => 'Status', 'type' => 'raw', 'value' => '$data->shipment->getStatus()'),
            array('header' => 'Note', 'type' => 'raw', 'value' => '$data->shipment->note'),
            array('header' => 'Create Time', 'type' => 'raw', 'value' => '$data->create_time'),
            array('header' => 'Assign To Warehouse Time', 'type' => 'raw', 'value' => '$data->op_complete_time'),
            array('header' => 'Due Date', 'type' => 'raw', 'value' => '$data->getDueDate()', 'cssClassExpression' => '$data->getDueDateColor()'),
            array('header' => 'Goods Available Address', 'type' => 'raw', 'value' => '$data->shipment->getAvailabelLabel()'),
            array('header' => 'Package', 'type' => 'raw', 'value' => '$data->shipment->pkg'),
            array('header' => 'Weight', 'type' => 'raw', 'value' => '$data->shipment->weight'),
            array('header' => 'Rack', 'type' => 'raw', 'value' => '$data->shipment->rack'),
            array('header' => 'Assigned User', 'type' => 'raw', 'value' => '$data->getAssignedUser()'),
        ],
    ]);
} else if ($status == Deconsolidation::STATUS_COMPLETE) {
    $this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
        'id' => $_GET["tabid"] . '_deconsolidation_grid_complete',
        'cssFile' => false,
        'dataProvider' => $deconsolidation->search($depot, $status),
        'filter' => $deconsolidation,
        'columns' => [
            array(
                'name' => 'hbn',
                'type' => 'raw',
                'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"',
            ),
            array('header' => 'Ref', 'type' => 'raw', 'value' => '$data->shipment->ref'),
            array('header' => 'Agent Id', 'type' => 'raw', 'value' => '$data->shipment->agent_id'),
            ['header' => 'Consol No', 'type' => 'raw', 'value' => 'empty($data->shipment->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->shipment->consol_id))."\" class=\"tab_link\" title=\"".@$data->shipment->consol->no."\">".@$data->shipment->consol->no."</a>"'],
            array('header' => 'Service Type', 'type' => 'raw', 'value' => '$data->getServiceType()'),
            array('header' => 'AWB No.', 'type' => 'raw', 'value' => '!empty($data->shipment->consol->awb) ? $data->shipment->consol->awb : @$data->shipment->consol->container_no'),
            array('header' => 'Postcode', 'type' => 'raw', 'value' => '$data->shipment->cnee->postcode'),
            array('header' => 'Memo', 'type' => 'raw', 'value' => '$data->shipment->can'),
            array('header' => 'Unpacking Date', 'type' => 'raw', 'value' => '@$data->shipment->consol->mdata["ContainerUnloadDate"]'),
            array('header' => 'Status', 'type' => 'raw', 'value' => '$data->shipment->getStatus()'),
            array('header' => 'Note', 'type' => 'raw', 'value' => '$data->shipment->note'),
            array('header' => 'Error', 'type' => 'raw', 'value' => '@$data->mdata["errors"]'),
            array('header' => 'Create Time', 'type' => 'raw', 'value' => '$data->create_time'),
            array('header' => 'Assign To Warehouse Time', 'type' => 'raw', 'value' => '$data->op_complete_time'),
            array('header' => 'Complete Time', 'type' => 'raw', 'value' => '$data->warehouse_complete_time'),
            array('header' => 'Due Date', 'type' => 'raw', 'value' => '$data->getDueDate()'),
            array('header' => 'Goods Available Address', 'type' => 'raw', 'value' => '$data->shipment->getAvailabelLabel()'),
            array('header' => 'Package', 'type' => 'raw', 'value' => '$data->shipment->pkg'),
            array('header' => 'Weight', 'type' => 'raw', 'value' => '$data->shipment->weight'),
            array('header' => 'Rack', 'type' => 'raw', 'value' => '$data->shipment->rack'),
            array('header' => 'Assigned User', 'type' => 'raw', 'value' => '$data->getAssignedUser()'),
        ],
    ]);
} else if ($status == Deconsolidation::STATUS_WAITING_ERROR_CHECK) {
    $this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
        'id' => $_GET["tabid"] . '_deconsolidation_grid_error',
        'cssFile' => false,
        'dataProvider' => $deconsolidation->search($depot, $status),
        'filter' => $deconsolidation,
        'columns' => [
            array(
                'header' => 'Hbn',
                'type' => 'raw',
                'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"',
            ),
            array('header' => 'Ref', 'type' => 'raw', 'value' => '$data->shipment->ref'),
            array('header' => 'Agent Id', 'type' => 'raw', 'value' => '$data->shipment->agent_id'),
            ['header' => 'Consol No', 'type' => 'raw', 'value' => 'empty($data->shipment->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->shipment->consol_id))."\" class=\"tab_link\" title=\"".@$data->shipment->consol->no."\">".@$data->shipment->consol->no."</a>"'],
            array('header' => 'Service Type', 'type' => 'raw', 'value' => '$data->getServiceType()'),
            array('header' => 'AWB No.', 'type' => 'raw', 'value' => '!empty($data->shipment->consol->awb) ? $data->shipment->consol->awb : @$data->shipment->consol->container_no'),
            array('header' => 'Postcode', 'type' => 'raw', 'value' => '$data->shipment->cnee->postcode'),
            array('header' => 'Memo', 'type' => 'raw', 'value' => '$data->shipment->can'),
            array('header' => 'Unpacking Date', 'type' => 'raw', 'value' => '@$data->shipment->consol->mdata["ContainerUnloadDate"]'),
            array('header' => 'Status', 'type' => 'raw', 'value' => '$data->shipment->getStatus()'),
            array('header' => 'Note', 'type' => 'raw', 'value' => '$data->shipment->note'),
            array('header' => 'Error', 'type' => 'raw', 'value' => '$data->mdata["errors"]'),
            array('header' => 'Create Time', 'type' => 'raw', 'value' => '$data->create_time'),
            array('header' => 'Assign To Warehouse Time', 'type' => 'raw', 'value' => '$data->op_complete_time'),
            array('header' => 'Complete Time', 'type' => 'raw', 'value' => '$data->warehouse_complete_time'),
            array('header' => 'Due Date', 'type' => 'raw', 'value' => '$data->getDueDate()', 'cssClassExpression' => '$data->getDueDateColor()'),
            array('header' => 'Goods Available Address', 'type' => 'raw', 'value' => '$data->shipment->getAvailabelLabel()'),
            array('header' => 'Package', 'type' => 'raw', 'value' => '$data->shipment->pkg'),
            array('header' => 'Weight', 'type' => 'raw', 'value' => '$data->shipment->weight'),
            array('header' => 'Rack', 'type' => 'raw', 'value' => '$data->shipment->rack'),
            array('header' => 'Assigned User', 'type' => 'raw', 'value' => '$data->getAssignedUser()'),
            array('header' => 'Action', 'type' => 'raw', 'value' => 'CHtml::Button("Error Check Done", ["id" => "complete_" . $data->id, "class" => "error-check-done"])'),
        ],
    ]);
}

?>

<script type="text/javascript">
    $(function () {
        var tab = $('#<?= $_GET["tabid"]; ?>');

        tab.unbind('reload_deconsolidation_grid').bind('reload__deconsolidation_grid', function() {
            $('#<?= $_GET["tabid"]; ?>_deconsolidation_grid_new', tab.data('panel')).yiiGridView('update');
            $('#<?= $_GET["tabid"]; ?>_deconsolidation_grid_warehouse', tab.data('panel')).yiiGridView('update');
            $('#<?= $_GET["tabid"]; ?>_deconsolidation_grid_error', tab.data('panel')).yiiGridView('update');
            $('#<?= $_GET["tabid"]; ?>_deconsolidation_grid_complete', tab.data('panel')).yiiGridView('update');
            return false;
        });
        
        var panel=tab.data('panel');
        $('body').on('click', '.label-preparation-done', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            debugger;
            let deconsolidationId = e.target.id;
            let assignedUser = $('#assign_user_' + deconsolidationId).val();
            let formData = new FormData();
            formData.append('id', deconsolidationId);
            formData.append('user_id', assignedUser);
            $.ajax({
                url: '<?= $this->createUrl("deconsolidation/assginToWarehouse") ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                enctype: 'multipart/form-data',

                success: function (response) {
                    tab.trigger('reload_deconsolidation_grid');
					myApp.notice("Assign Done");
                }
            })
        });

        $('body').on('click', '.error-check-done', function (e) {
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

                success: function (response) {
                    tab.trigger('reload_deconsolidation_grid');
					myApp.notice("Assign Done");
                }
            })
        });
    });
</script>