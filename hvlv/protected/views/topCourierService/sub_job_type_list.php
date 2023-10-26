<h3><?=$name?></h3>
<div>
    <div style="width:50%">
    <?php  
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'cargo_main_menue-sub'.$_GET['tabid'],
            'cssFile' => false,
            'dataProvider'=>$dataProvider3[0],
            'filter'=>$dataProvider3[1],
            'columns'=>array(
           array('name'=>'status','headerHtmlOptions' => array('style' => 'display:none'),'filterHtmlOptions' => array('style' => 'display:none'),
                'htmlOptions' => array('style' => 'display:none'),'type'=>'raw'),
           array('name'=>'status','value'=>'@CargoProcessJob::$processTypes[$data["status"]]'),
            'number',
            array('name'=>'day1','header'=>'<=1 days'),
            array('name'=>'day2','header'=>'2 days'),
            array('name'=>'day3','header'=>'>=3 days','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'),
          
        ),
    ));?>
    </div>
</div>
<div style="width:200px;" id="cargo_main_process_type_id" >    
    <?php  
    $column=[];
    $column=array(array('name'=>'type','type'=>'raw','value' => 
        '"<a href=\"".Yii::app()->createUrl($data["url"],array("pod_id"=>$_GET["pod_id"],"cargo_type"=>$data["type"]))."\"  class=\"tab_link\" title=\"".($data["pod"])."-".$data["name"]."\" >".$data["name"]."</a>"',
         'cssClassExpression'=>'"type_row"'));
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'cargo_main_process_type'.$_GET['tabid'],
            'cssFile' => false,
            'ajaxUrl'=>Yii::app()->createUrl('topCourierService/jobTypeList',array('tabid'=>$_GET['tabid'])),
                 'dataProvider'=>$dataProvider2[0],
//            'filter'=>$dataProvider2[1],
            'columns'=>$column,
    ));?>
</div>
