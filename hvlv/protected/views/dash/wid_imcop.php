<?php
        $filtersForm=new FiltersForm;
        if (isset($_GET['FiltersForm'])) {
            $filtersForm->filters=$_GET['FiltersForm'];
        }
        $provide=[];
        $sql='SELECT count(*) as number, s.status as status FROM `consol` t INNER JOIN `consol_process` s ON t.id=s.fid WHERE t.status!=100 AND t.type in (15,70) AND s.status<80 AND s.main_type=1 group by s.status ';
        $rs=Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($rs as $id=>$r) {
            $n1=0;
            $n2=0;
            $n3=0;
            $sql='SELECT count(*) as number FROM `consol` t INNER JOIN `consol_process` s ON t.id=s.fid WHERE t.type in(15,70) AND t.status!=100 AND s.status=:status AND s.main_type=1 AND DATEDIFF(NOW(),eta)<=1';
            $n1=Yii::app()->db->createCommand($sql)->bindValues([':status'=>$r['status']])->queryScalar();
            $sql='SELECT count(*) as number FROM `consol` t INNER JOIN `consol_process` s ON t.id=s.fid WHERE t.type in(15,70)  AND t.status!=100 AND s.status=:status AND s.main_type=1 AND DATEDIFF(NOW(),eta)=2';
            $n2=Yii::app()->db->createCommand($sql)->bindValues([':status'=>$r['status']])->queryScalar();
            $sql='SELECT count(*) as number FROM `consol` t INNER JOIN `consol_process` s ON t.id=s.fid WHERE t.type in(15,70)  AND t.status!=100 AND s.status=:status AND s.main_type=1 AND DATEDIFF(NOW(),eta)>=3';
            $n3=Yii::app()->db->createCommand($sql)->bindValues([':status'=>$r['status']])->queryScalar();
            $provide[]=['id'=>$id,'number'=>$r['number'],'status'=> $r['status'],'day1'=>$n1,'day2'=>$n2,'day3'=>$n3];
        }
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
   #consol_process_dp_view .type_row,#consol_main_process_type_id .type_row {
        color:rgb(119, 119, 218);
        text-align: center;
        font-size: 20px;
        font-weight: bold;
    }
</style>
<div style="width:100%">
    <?php  
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'consol_main_menue2'.$_GET['tabid'],
            'cssFile' => false,
            'dataProvider'=>$dataprovider,
            'filter'=>$filtersForm,
            'columns'=>array(
           array('name'=>'status','headerHtmlOptions' => array('style' => 'display:none'),'filterHtmlOptions' => array('style' => 'display:none'),
                'htmlOptions' => array('style' => 'display:none'),'type'=>'raw'),
           array('name'=>'status','value'=>'@ConsolProcess::$states[$data["status"]]'),
            'number',
            array('name'=>'day1','header'=>'<=1 days'),
            array('name'=>'day2','header'=>'2 days'),
            array('name'=>'day3','header'=>'>=3 days','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'),
          
        ),
    ));?>
</div>