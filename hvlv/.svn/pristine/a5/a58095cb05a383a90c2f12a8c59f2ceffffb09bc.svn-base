<?php
    $filtersForm=new FiltersForm;
    if (isset($_GET['FiltersForm'])) {
        $filtersForm->filters=$_GET['FiltersForm'];
    }
    $provide=[];
    $sql="SELECT (@i :=@i + 1) AS id, cg.org_id, o.name,count(*) as number, (SELECT count(*) FROM `shipment_question_submit` c  join shipment_question s2  on c.id = s2.submit_id
         join org o2 on c.org_id = o2.id WHERE DATEDIFF(NOW(),date)<=1 and cg.org_id=c.org_id and s2.process_type!= ".ShipmentQuestion::TYPE_FINISHED." ) as day1
        , (SELECT count(*) FROM `shipment_question_submit` c join shipment_question s2  on c.id = s2.submit_id
         join org o2 on c.org_id = o2.id WHERE DATEDIFF(NOW(),date)<=2 and DATEDIFF(NOW(),date)>1 and cg.org_id=c.org_id  and s2.process_type!= ".ShipmentQuestion::TYPE_FINISHED." ) as day2
        , (SELECT count(*) FROM `shipment_question_submit` c join shipment_question s2  on c.id = s2.submit_id
         join org o2 on c.org_id = o2.id WHERE DATEDIFF(NOW(),date)>2 and cg.org_id=c.org_id  and s2.process_type!= ".ShipmentQuestion::TYPE_FINISHED.") as day3 from
        (SELECT @i := 0) AS it,`shipment_question_submit` cg
         join shipment_question s  on cg.id = s.submit_id
         join org o on cg.org_id = o.id
        WHERE s.process_type!= ".ShipmentQuestion::TYPE_FINISHED." group by cg.org_id order by id";

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
        'name' => [
            'asc' => 'name ASC',
            'desc' => 'name DESC',
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
   #consol_process_dp_view .type_row,#consol_main_process_type_id .type_row {
        color:rgb(119, 119, 218);
        text-align: center;
        font-size: 20px;
        font-weight: bold;
    }
</style>
<div style="width:100%">
    <?php
        $this->widget('zii.widgets.grid.CGridView', [
            'id' => 'custom-client-list-grid',
            'htmlOptions' => ['style' => 'width: 90%'],
            'afterAjaxUpdate'=>'function(r,s){$("#custom_summary").html($(s).find("#custom_summary").html());}',
            'cssFile' => false,
            'dataProvider' => $dataprovider,
            'filter' => $filtersForm,
            'columns' => [
            ['name' => 'org_id', 'headerHtmlOptions' => ['style' => 'display:none'], 'filterHtmlOptions' => ['style' => 'display:none'],
                'htmlOptions' => ['style' => 'display:none'], 'type' => 'raw'],
            ['name' => 'name', 'type' => 'raw'],
            'number',
            ['name' => 'day1', 'header' => '24 hours', 'type' => 'raw'],
            ['name' => 'day2', 'header' => '24-48 hours', 'type' => 'raw'],
            ['name' => 'day3', 'header' => '48+ hours', 'type' => 'raw'],
            ],
        ]);
    ?>
</div>