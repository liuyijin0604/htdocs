<h1><?=$this->t('My Total Email List')."-".User::getUserName($user_id)?></h1>
<br/>
<h4>统计： <?=$total?>票</h4>
<div style="width:50%" id="import-email-overview<?=$_GET['tabid']?>">
   <?php  $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'importsmail-review-list-grid'.$_GET['tabid'],
    'htmlOptions'=>['style'=>'width: 70%'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],
    'filter'=>$dataProvider[1],
    'columns'=>[
        ['name'=>'rawType','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
        ['name'=>'type','header'=>'Type'],
        ['name'=>'new', 'header'=>'New'],
        [ 'name'=>'allocated',  'header'=>'Allocated'],
        [ 'name'=>'replied',  'header'=>'Replied'],
        [ 'name'=>'closed',  'header'=>'Closed Without Reply']
    ],
   ]);?>
</div>
<?php echo CHtml::link('Advanced Search', 'serach', ['class'=>'search-button']); ?>
<div class="search-form">
<?php $this->renderPartial('_search', [
    'model'=>$model,
]); ?>
</div><!-- search-form -->
<?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'imports-email-total-grid'.$_GET['tabid'],
    'cssFile' => false,
    'dataProvider'=>$model->search(true, 30, false, false),
    'filter'=>$model,
    'columns'=>[
        'no',
        'ticket',
        ['name'=>'type', 'value'=>'$data->getType()','filter'=>CHtml::dropDownList('ImportsMail[type]', $model->type, ImportsMail::$mailTypes, ['prompt'=>'All'])],
        ['name'=>'status','value' => '$data->getStatus()','filter'=>CHtml::dropDownList('ImportsMail[status]', $model->status, ImportsMail::$states, ['prompt'=>'All'])],
        ['name'=>'op_id','header'=>'Assign To', 'type' => 'raw', 'value'=>'$data->getOpName()','filter'=>CHtml::dropDownList('ImportsMail[op_id]', $model->op_id, ImportsMail::$importscs_list_id, ['prompt'=>'All']) ],
        'from_email',
        ['name'=>'to_email','type'=>'raw','value'=>function ($data) {
            return CHtml::tag('div', ['title'=>$data->to_email], substr($data->to_email, 0, 25));
        }],
        ['name'=>'subject','type'=>'raw','value'=>function ($data) {
            return CHtml::tag('div', ['title'=>$data->subject], substr($data->subject, 0, 60));
        }],
    ['name'=>'plain_body','type'=>'raw','value'=>function ($data) {
      return CHtml::tag('div', ['title'=>strip_tags($data->plain_body)], mb_substr(strip_tags($data->plain_body), 0, 25));
    }],
        'create_time',
        ['header' => 'deadline','type'=>'raw','value' => '($data->flag&ImportsMail::FLAG_PARK)>0?("<div style=\"background-color:RGB(80,171,220)\">".$data->getDeadLine()."</div>"):$data->getDeadLine()'],
        ['name'=>'date', 'header'=>'Reply Time'],
        [
            'class'=>'oButtonColumn',
            'template'=>'{update}',
            'buttons'=>[
                'update' => [
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->no'],
                ],
            ],
        ],
            
        
    ]
]);
            ?>
<script>
    $(function(){
        var tab=$("#<?=$_GET['tabid']?>");
        var panel=tab.data('panel');
        $('.search-form').toggle();
        $('.search-button',panel).click(function(){
       $('.search-form').toggle();
    return false;
         });
        $('.search-form form',panel).submit(function(){
                $('#imports-email-total-grid<?=$_GET['tabid']?>',panel).yiiGridView('update', {
                        data: $(this).serialize()
                });
                return false;
        });
    })
</script>


    
