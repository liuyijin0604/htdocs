<?php 
    $column=[];
    $column[]=array('name'=>'pod_id','headerHtmlOptions' => array('style' => 'display:none'), 'filterHtmlOptions' => array('style' => 'display:none'),
                        'htmlOptions' => array('style' => 'display:none'), 'type' => 'raw');
    $column[]=array('name'=>'pod','header'=>'Branch-'.$_GET["name"],'type'=>'raw','value' => 
        '"<a href=\"".Yii::app()->createUrl(($_GET["type_id"]==10?"consolProcess/consolList":($_GET["type_id"]==999?"warehouseProcess/processIndex":"consolProcess/seaConsolList")),array("pod_id"=>$data["pod_id"],"consol_type"=>$_GET["type_id"]))."\"  class=\"tab_link\" title=\"".$data["pod"]."-".$_GET["name"]."\" >".$data["pod"]."</a>"',
         'cssClassExpression'=>'"type_row"');
//    $process=ConsolProcess::$states;
//    unset($process[80]);
//    foreach($process as $key=>$value){
//        $column[]=array('name'=>$key,'header'=>$value);
//    }
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'consol_main_menue'.$_GET['tabid'],
            'cssFile' => false,
            'dataProvider'=>$dataProvider1[0],
            'columns'=>$column,
));?>