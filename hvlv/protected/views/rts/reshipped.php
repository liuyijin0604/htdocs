
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'im-rts-reshipping-parcel-grid',
    'cssFile' => false,
    'selectableRows' => 2,
    'dataProvider'=>$model->search(),
    //'filter'=>$model,
    'columns'=>array(
        array(
            'id'=>'selectedItems',
            'class'=>'CCheckBoxColumn',
        ),
        array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
        array('header' => 'Reshipping Connote',  'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->getRTSTranshipNoId()))."\" class=\"tab_link\" title=\"".$data->getRTSTranshipNo()."\">".$data->getRTSTranshipNo()."</a>"'),

        'ref',
        array('header' => 'Customer', 'type' => 'raw', 'value' => 'empty($data->agent)? "" : $data->agent->name'),
       array('header' => 'Received', 'type' => 'raw','value' => 'empty($data->mdata["rts_scan_date"])? "" : $data->mdata["rts_scan_date"]'),
        'note'
    ),
)); ?>
