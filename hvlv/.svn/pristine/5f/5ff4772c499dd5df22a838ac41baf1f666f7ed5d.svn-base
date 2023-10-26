<h3>Select Process Type</h3>
<div style="width:200px;display:inline-block;" id="consol_main_process_type_id" >    
    <?php  
    $column=[];
    $column[] = array('name'=>'id','headerHtmlOptions' => array('style' => 'display:none'), 'filterHtmlOptions' => array('style' => 'display:none'),
                        'htmlOptions' => array('style' => 'display:none'), 'type' => 'raw');

    $column[]=array('name'=>'type','type'=>'raw','value' => 
        '"<a href=\"".Yii::app()->createUrl(($data["type"]==10?"consolProcess/consolList":($data["type"]==999?"warehouseProcess/processIndex":"consolProcess/seaConsolList")),array("consol_type"=>$data["type"]))."\"  class=\"tab_link\" title=\"".$data["pod"]."-".$data["name"]."\" >".$data["name"]."</a>"',
         'cssClassExpression'=>'"type_row"');
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'consol_main_process_type'.$_GET['tabid'],
            'cssFile' => false,
            'ajaxUrl'=>Yii::app()->createUrl('consolProcess/typeList',array('tabid'=>$_GET['tabid'])),
            'dataProvider'=>$dataProvider2[0],
//            'filter'=>$dataProvider2[1],
            'columns'=>$column,
    ));?>
</div>


