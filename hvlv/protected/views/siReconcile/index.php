<h2>Si Reconcile</h2>
<div class="row buttons">

</div>
<h3>Overview</h3>
<style>
   .cloumn_red_1{
        color:red;
        font-weight: bold;
    }
   #si_reconcile_dp_view .type_row {
        color:rgb(119, 119, 218);
        text-align: center;
        font-size: 20px;
        font-weight: bold;
    }
</style>

<!-- 
<div style="width:50%">
    <?php  
    // $this->widget('zii.widgets.grid.CGridView',array(
    //         'id'=>'cargo_main_menue2'.$_GET['tabid'],
    //         'cssFile' => false,
    //         'dataProvider'=>$dataProvider[0],
    //         'filter'=>$dataProvider[1],
    //         'columns'=>array(
    //        array('name'=>'status','headerHtmlOptions' => array('style' => 'display:none'),'filterHtmlOptions' => array('style' => 'display:none'),
    //             'htmlOptions' => array('style' => 'display:none'),'type'=>'raw'),
    //        array('name'=>'status','value'=>'@CargoProcess::$processTypes[$data["status"]]'),
    //         'number',
    //         array('name'=>'day1','header'=>'<=1 days'),
    //         array('name'=>'day2','header'=>'2 days'),
    //         array('name'=>'day3','header'=>'>=3 days','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'),
          
    //    ),
    //));?>
</div> -->
<div class="pane" id="si_reconcile_dp_view" style="width:200px">
    <?php 
    $column=[];
    $column[]=array('name'=>'type','type'=>'raw','cssClassExpression'=>'"type_row"','value'=>'"<a href=\"".Yii::app()->createUrl("siReconcile/getReconcileList",array("type"=>$data["id"]))."\"  class=\"tab_link\" title=\"".$data["type"]."\" >".$data["type"]."</a>"');
//    $process=ConsolProcess::$states;
//    unset($process[80]);
//    foreach($process as $key=>$value){
//        $column[]=array('name'=>$key,'header'=>$value);
//    }
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'si_reconcile_main_menue'.$_GET['tabid'],
            'cssFile' => false,
            'dataProvider'=>$dataProvider1[0],
            'columns'=>$column,
    ));?>
</div>

<script>
    $(function(){
       var tab=$("<?=$_GET['tabid']?>");
       var panel=tab.data('panel');

   })
</script>

