<h1>Emails For Ticket <?=$shipmentQuestion->ShipmentQuestionSubmit->ticket?></h1>
<h1>Reference Number: <?=empty($shipmentQuestion->Shipment)?"":$shipmentQuestion->Shipment->ref?></h1>
<br>
<?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'imports-email-total-grid-list1',
    'cssFile' => false,
    'dataProvider'=>$model->search(true, 30, false, false),
    'filter'=>$model,
    'columns'=>[
        'no',
        ['name'=>'status', 'value'=>'$data->getStatus()','filter'=>CHtml::dropDownList('ImportsMail[status]', $model->status, ImportsMail::$states, ['prompt'=>'All'])],
        'from_email',
        ['name'=>'to_email','type'=>'raw','value'=>function ($data) {
            return CHtml::tag('div', ['title'=>$data->to_email], substr($data->to_email, 0, 25));
        }],
        ['name'=>'subject','type'=>'raw','value'=>function ($data) {
            return CHtml::tag('div', ['title'=>$data->subject], mb_substr($data->subject, 0, 60));
        }],
        ['name'=>'plain_body','type'=>'raw','value'=>function ($data) {
            return CHtml::tag('div', ['title'=>strip_tags($data->plain_body)], mb_substr(strip_tags($data->plain_body), 0, 25));
        }],
        'create_time',
        ['name'=>'date', 'header'=>'Reply Time'],
        [
            'class'=>'oButtonColumn',
            'template'=>'{check}',
            'buttons'=>[
                'check' => [
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'url' => 'Yii::app()->createUrl("ims/customerService/updateEmail", ["id" => $data->id])',
                    'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->no'],
                ]
            ],
        ],
    ]]);
        ?>
