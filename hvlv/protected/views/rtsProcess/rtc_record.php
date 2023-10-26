<?php
echo '<h1>Shipment RTC Record</h1>';
?>


<?php $this->widget('zii.widgets.grid.CGridView',array(
    'cssFile' => false,
    'id'=>'rts_waiting_resend_grid_view',
    'filter'=>$model,
    'dataProvider'=>$model->search(true,50),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
            array('header' => 'RTC GatePass ID','value'=>'$data->rtc_gp_id'),
            array('header' => 'Gatepass Ref','value'=>'$data->rtcGatepass->ref'),
            array('header' => 'RTC Packages','type'=>'raw','value'=>'$data->getRTSPackagesCount()'),
            ['header' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('ShipmentRtsRecordConfirm[status]', $model->status, $this->t(ShipmentRtsRecordConfirm::$rtc_states)),],
            ['name' => 'create_time','header'=>'RTC Submit Time'],
            ['class'=>'oButtonColumn',
                'template'=>'{uploadGatePass}&nbsp;{label}&nbsp;{gatepass}&nbsp;{invoice}&nbsp;{log}',
                'buttons'=>[
                    'uploadGatePass' => array(
                    'url' => 'Yii::app()->createURL("rtsProcess/uploadGatePass", array("id" => $data->id))',
                    'imageUrl' => false,
                    'label' => 'UploadGatePass',
                    'options' => array('class' => 'jqm_link grid_view_btn', 'target' => '_blank'),
                    'visible' => 'true',
                    ),
                    'label' => array(
                    'url' => 'Yii::app()->createURL("rts/rtclabels", array("id" => $data->rtc_gp_id))',
                    'imageUrl' => false,
                    'label' => 'Labels',
                    'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
                    'visible' => 'true',
                    ),
                    'gatepass' => array(
                        'url' => 'Yii::app()->createURL("rts/rtcgatepass", array("id" => $data->rtc_gp_id))',
                        'imageUrl' => false,
                        'label' => 'GatePass',
                        'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
                        'visible' => 'true',
                    ),
                    'invoice' => array(
                        'url' => 'Yii::app()->createURL("rts/rtcinvoices",array("id" => $data->rtc_gp_id))',
                        'imageUrl'=>false,
                        'label' => 'Invoice',
                        'options' => array('class' => 'grid_view_btn tab_link'),
                        'visible' => 'true',
                    ),
                    'log' => [
                        'imageUrl'=>false,
                        'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
                        'visible' => 'true',
                        'url' => 'Yii::app()->createUrl("rtsProcess/unknownLog", ["id" => $data->id])',
                        'label' => 'Log'
                    ],
                ],
            ]
        ),
    )
    
);
?>

<script type="text/javascript">
$(function() {
    

});
</script>