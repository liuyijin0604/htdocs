<div style="width:100%;" id="tla-task-overview<?=$_GET['tabid']?>">
   <?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'my_tla_task_list_grid'.$_GET['tabid'],
    'htmlOptions'=>['style'=>'width: 100%'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],
    'filter'=>$dataProvider[1],
    'columns'=>[
        ['name'=>'no','header'=>'Gatepass No.',"value"=>'"<a target=\"_blank\" href=\"".Yii::app()->createUrl("gatepass/print",["id"=>$data["no"]])."\">".$data["no"]."</a>"','type'=>'raw'],
        ['name'=>'hbn'],
        ['name'=>'ref'],
        ['name'=>'location','type'=>'raw'],
        ['name'=>'scanout'],
        ['name'=>'status'],
        ['name'=>'packages'],
        ['name'=>'weight'],
        ['name'=>'driver'],
        ['name'=>'gatepass_time'],
        ['name'=>'isResort'],
    ],
   ]); ?>
</div>