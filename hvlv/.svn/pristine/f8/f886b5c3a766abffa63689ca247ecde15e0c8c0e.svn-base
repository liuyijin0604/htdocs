<?php
    if(empty($all_clear_wait)) $all_clear_wait = false;

    $this->widget('zii.widgets.grid.CGridView', array(
    'selectableRows' => 2,
    'id'=>'gp-imco-shipments-grid',
    'cssFile' => false,
    'dataProvider'=>$shipment_model->search($all_clear_wait,50),
    'filter'=>$shipment_model,
    'columns'=>array(
        array(
            'id'=>'selectedItems',
            'class'=>'CCheckBoxColumn',
        ),
        array(
          'name' => 'hbn','type' => 'raw','value'=> '"<input type=\"hidden\" name=\"consolid\" value=\"". $data->consol_id ."\">$data->hbn"'
        ),
        'ref',
        array('name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('ImParcel[status]', $shipment_model->status, $this->t(ImParcel::$states), array('prompt'=>$this->t('All'))),),
        'pkg',
        'weight')
)); ?>
