<div style="overflow:auto">
	<div style="width:50%;" id="cargos-client-view<?=$_GET['tabid']?>">
   <?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'cargos-client-list-grid'.$_GET['tabid'],
    'htmlOptions'=>['style'=>'width: 70%'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],
    'filter'=>$dataProvider[1],
    'columns'=>[
        ['name'=>'status','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
        ['name'=>'status','value'=>'Message::$states[$data["status"]]'],
        'number',
        ['name'=>'day1','header'=>'<=1 days'],
        ['name'=>'day2','header'=>'2 days'],
        ['name'=>'day3','header'=>'>=3 days','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'],
    ],
   ]); ?>
</div>


<?php
$model->to_id = Yii::app()->user->id;
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'-in-message-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 10),
	'filter'=>$model,
	'columns'=>array(
		array('header' => 'View','type'=>'raw', 'value' => '$data->getStatusForList()'),	
		'time',
		array('name' => 'from_id', 'value' => '$data->getFrom()'),
		array('name' => 'status', 'value' => '$data->getStatus().$data->updateShown();',
			'filter'=>CHtml::dropDownList('Message[status]', $model->status, $this->t(Message::$states), array('prompt'=>$this->t('All'))),),
		array('name'=>'msg','type'=>'raw','value'=>'"<p style=\"word-wrap:break-word;width:200px;\">".$data->msg."</p>"'),
       ['class'=>'oButtonColumn',
            'template'=>'{Go to Tla Task}{Open Task}',
            'buttons'=>[
                'Go to Tla Task' => [
                    'url'=>'Yii::app()->createURL("tlaTask/getTlaTaskList")',
                    'imageUrl'=>false,
                    'visible'=>'$data->type ==1',
                    'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Go to task'), 'title' => 'TLA Task List'],
                ],
                'Open Task' => [
                    'url'=>'Yii::app()->createURL("tlaTask/operation")."?taskNo=".$data->getTaskNo()',
                    'imageUrl'=>false,
                    'visible'=>'$data->isTask()',
                    'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Open Task'), 'title' => 'Open Task'],
                ]
            ],
        ]
	),
));

echo '<script type="text/javascript">$.oMessage.getNotice();</script>';
?>
</div>
