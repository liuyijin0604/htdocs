<?php
    $total = 0;
    $provide = [];
    $sql = "SELECT type, COUNT(*) as no,COUNT(if(status=10,1,null)) as n10, COUNT(if(status=20,1,null)) as n20  FROM `imports_mail` WHERE  type!=80 AND status not in (50,60,100) GROUP by type";
    $rs = Yii::app()->db->createCommand($sql)->queryAll();
    foreach ($rs as $index => $r) {
        $total += intval($r['no']);
        $provide[] = ['id' => $index + 1, 'rawType' => $r['type'], 'type' => (ImportsMail::$mailTypes+ImportsMail::$wmsMailTypes)[$r['type']], 'new' => $r['n10'], 'allocated' => $r['n20']];
    }
    $filtersForm = new FiltersForm;
    if (isset($_GET['FiltersForm'])) {
        $filtersForm->filters = $_GET['FiltersForm'];
    }
    $filteredData = $filtersForm->filter($provide);
    $dataprovider = new CArrayDataProvider($filteredData);
    $dataprovider->pagination = ['pageSize' => 10];
    $sort = new CSort();
    $sort->attributes = [
        'type' => [
            'asc' => 'type ASC',
            'desc' => 'type DESC',
        ],
    ];
    $sort->defaultOrder = "type ASC";
    $dataprovider->sort = $sort;
?>
<h4>统计： <?=$total?>票</h4>
<div style="width:100%" id="import-email-overview">
     <?php  $this->widget('zii.widgets.grid.CGridView', [
        'id'=>$_GET['tabid'].'importsmail-review-list-grid',
        'htmlOptions'=>['style'=>'width: 70%'],
        'afterAjaxUpdate'=>'function(r,s){$("#list_total_summary").html($(s).find("#list_total_summary").html());}',
        'cssFile' => false,
        'dataProvider'=>$dataprovider,
        'filter'=>$filtersForm,
        'columns'=>[
            ['name'=>'rawType','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
                'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
            ['name'=>'type','header'=>'Type'],
            ['name'=>'new', 'header'=>'New'],
            [ 'name'=>'allocated',  'header'=>'Allocated']
        ],
     ]);?>
</div>