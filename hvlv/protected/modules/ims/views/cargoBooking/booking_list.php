<?php $this->widget('application.extensions.booster.TbExtendedGridView', array(
    'fixedHeader' => true,
    'id' => 'task_grid_view',
    'filter' => $model,
    'type' => 'striped bordered',
    'headerOffset' => 40,
    'responsiveTable' => true,
    'dataProvider' => $model->search(),
    'template' => "{summary}\n{items}\n{pager}",
    'columns' => array(
        'booking_number',
        array('name' => 'hbn','header'=>'Connote', 'type' => 'raw', 'value' => '$data->hbn'),
        array('name' => 'other_ref','header'=>'Other Reference', 'type' => 'raw', 'value' => '$data->other_ref'),
        array('name' => 'create_date', 'header' => 'Create Date', 'value'=>'Yii::app()->dateFormatter->format("yyyy-MM-dd H:M", strtotime($data->create_date))'),
        array('name' => 'booking_date_1', 'header' => 'First Choice', 'value'=>'Yii::app()->dateFormatter->format("yyyy-MM-dd", strtotime($data->booking_date_1))'),
        array('name' => 'booking_date_2', 'header' => 'Second Choice', 'value'=>'!empty($data->booking_date_2)?Yii::app()->dateFormatter->format("yyyy-MM-dd", strtotime($data->booking_date_2)):""'),
        array('name' => 'confirmed_date', 'header' => 'Confirmed Date', 'value'=>'!empty($data->confirmed_date)?Yii::app()->dateFormatter->format("yyyy-MM-dd", strtotime($data->confirmed_date)):""'),
        array('header'=>'status','type'=>'raw','value'=>'CargoBooking::$bookingStatus[$data["status"]]','filter'=>CHtml::dropDownList('CargoBooking[status]',$model->status,$this->t($model::$bookingStatus),array('prompt'=>'All','class'=>'form-control'))),
        array('name' => 'fee','header'=>'Fee(Ex. gst)', 'type' => 'raw', 'value' => '$data->fee'),
        array('header' => 'POD', 'type' => 'raw', 'value' => '$data->getPodUrl()'),
        array('header' => 'Invoice No', 'type' => 'raw', 'value' => '$data->getInvoiceNo()'),
    ),

));
?>

