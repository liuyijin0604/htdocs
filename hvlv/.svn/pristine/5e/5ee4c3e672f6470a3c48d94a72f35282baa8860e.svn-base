<h1><?=$this->t('My Messages');?></h1>

<div style="overflow:auto">
	<div style="width:50%;" id="cargos-client-view<?=$_GET['tabid']?>">
   <?php $this->widget('application.extensions.booster.TbExtendedGridView', [
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
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id'=>$_GET["tabid"].'-in-message-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 10),
	'filter'=>$model,
	'columns'=>array(
		array('header' => 'View','type'=>'raw', 'value' => '$data->getStatusForList().$data->updateShown()'),	
		'time',
		array('name' => 'from_id', 'value' => '$data->getFrom()'),
		'msg',
	),
)); 
echo '<script type="text/javascript">$.oMessage.getNotice();</script>';
?>
</div>
