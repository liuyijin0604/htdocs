<?php
    $filtersForm=new FiltersForm;
    if (isset($_GET['FiltersForm'])) {
        $filtersForm->filters=$_GET['FiltersForm'];
    }
    $provide=[];
    $sql="SELECT (@i :=@i + 1) AS id,status,count(*) as number, (SELECT count(*) FROM `cargo_process` c WHERE DATEDIFF(NOW(),date)<=1 and c.status=cg.status ) as day1
        , (SELECT count(*) FROM `cargo_process` c WHERE DATEDIFF(NOW(),date)<3 and DATEDIFF(NOW(),date)>1 and c.status=cg.status ) as day2
        , (SELECT count(*) FROM `cargo_process` c WHERE DATEDIFF(NOW(),date)>=3 and c.status=cg.status ) as day3
        FROM `cargo_process` cg,
        (SELECT @i := 0) AS it  WHERE status<".CargoProcess::PROCESSDONE." group by status";
    $provide=Yii::app()->db->createCommand($sql)->queryAll();
    $filteredData=$filtersForm->filter($provide);
    $dataprovider=new CArrayDataProvider($filteredData);
    $dataprovider->pagination=['pageSize' =>10,];
    $sort=new CSort();
    $sort->attributes=[
        'number'=>[
            'asc'=>'number ASC',
            'desc'=>'number DESC',
        ],
        'status'=>[
            'asc'=>'status ASC',
            'desc'=>'status DESC',
        ],
    ];
    $sort->defaultOrder = "status ASC";
    $dataprovider->sort=$sort;
?>
<style>
   .cloumn_red_1{
        color:red;
        font-weight: bold;
    }
   #cargo_process_dp_view .type_row,#cargo_main_process_type_id .type_row {
        color:rgb(119, 119, 218);
        text-align: center;
        font-size: 20px;
        font-weight: bold;
    }
</style>
<div style="width:100%">
    <?php  
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'cargo_main_menue2'.$_GET['tabid'],
            'cssFile' => false,
            'dataProvider'=>$dataprovider,
            'filter'=>$filtersForm,
            'columns'=>array(
           array('name'=>'status','headerHtmlOptions' => array('style' => 'display:none'),'filterHtmlOptions' => array('style' => 'display:none'),
                'htmlOptions' => array('style' => 'display:none'),'type'=>'raw'),
           array('name'=>'status','value'=>'@CargoProcess::$processTypes[$data["status"]]'),
            'number',
            array('name'=>'day1','header'=>'<=1 days'),
            array('name'=>'day2','header'=>'2 days'),
            array('name'=>'day3','header'=>'>=3 days','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'),
          
        ),
    ));?>
</div>