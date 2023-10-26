<?php

$totalPacksAir = 0;
$totalWeightAir = 0;
$totalWeightAir1 = 0;
$totalDpPacksAir = 0;
$totalDpWeightAir = 0;
$totalPcsPacksAir = 0;
$totalPcsWeightAir = 0;
$totalCartonPacksAir = 0;
$totalCartonWeightAir = 0;

foreach ($dataProviderAir->getData() as $data) {
    
    $totalPacksAir += $data['total_packs'];

    $total_weight = str_replace(',', '', $data['total_weight']);
    $totalWeightAir += $total_weight;

    $total_weight1 = str_replace(',', '', $data['total_weight1']);
    $totalWeightAir1 += $total_weight1;
        
    $totalDpPacksAir += $data['dp_packs'];    

    $dp_weight = str_replace(',', '', $data['dp_weight']);
    $totalDpWeightAir += $dp_weight;

    $totalPcsPacksAir += $data['pcs_packs'];
    
    $pcs_weight = str_replace(',', '', $data['pcs_weight']);
    $totalPcsWeightAir += $pcs_weight;

    $totalCartonPacksAir += $data['carton_packs'];

    $carton_weight = str_replace(',', '', $data['carton_weight']);
    $totalCartonWeightAir += $carton_weight;
}

//Yii::app()->end();
$totalRowAir = array(
    'id' => 0,
    'depot' => 'Totals (Air):',
    'user' => '',
    'scan_date' => '',
    'dp_packs' => number_format($totalDpPacksAir, 2),
    'dp_weight' => number_format($totalDpWeightAir, 2),
    'pcs_packs' => number_format($totalPcsPacksAir, 2),
    'pcs_weight' => number_format($totalPcsWeightAir, 2),
    'carton_packs' => number_format($totalCartonPacksAir, 2),
    'carton_weight' => number_format($totalCartonWeightAir, 2),
    'total_packs' => number_format($totalPacksAir, 2),
    'total_weight' => number_format($totalWeightAir, 2),
    'total_weight1' => number_format($totalWeightAir1, 2),
    'time' => '',
);

$updatedDataAir = $dataProviderAir->getData();
$updatedDataAir[] = $totalRowAir;

$updatedDataProviderAir = new CArrayDataProvider($updatedDataAir, array(
    'pagination' => $dataProviderAir->getPagination(),
    'sort' => $dataProviderAir->getSort(),
));


$totalPacksSea = 0;
$totalWeightSea = 0;
$totalWeightSea1 = 0;
$totalDpPacksSea = 0;
$totalDpWeightSea = 0;
$totalPcsPacksSea = 0;
$totalPcsWeightSea = 0;
$totalCartonPacksSea = 0;
$totalCartonWeightSea = 0;

foreach ($dataProviderSea->getData() as $data) {
    $totalPacksSea += $data['total_packs'];

    $total_weight = str_replace(',', '', $data['total_weight']);
    $totalWeightSea += $total_weight;

    $total_weight1 = str_replace(',', '', $data['total_weight1']);
    $totalWeightSea1 += $total_weight1;
        
    $totalDpPacksSea += $data['dp_packs'];    

    $dp_weight = str_replace(',', '', $data['dp_weight']);
    $totalDpWeightSea += $dp_weight;

    $totalPcsPacksSea += $data['pcs_packs'];
    
    $pcs_weight = str_replace(',', '', $data['pcs_weight']);
    $totalPcsWeightSea += $pcs_weight;

    $totalCartonPacksSea += $data['carton_packs'];

    $carton_weight = str_replace(',', '', $data['carton_weight']);
    $totalCartonWeightSea += $carton_weight;
}

$totalRowSea = array(
    'id' => 0,
    'depot' => 'Totals (Sea):',
    'user' => '',
    'scan_date' => '',
    'dp_packs' => number_format($totalDpPacksSea, 2),
    'dp_weight' => number_format($totalDpWeightSea, 2),
    'pcs_packs' => number_format($totalPcsPacksSea, 2),
    'pcs_weight' => number_format($totalPcsWeightSea, 2),
    'carton_packs' => number_format($totalCartonPacksSea, 2),
    'carton_weight' => number_format($totalCartonWeightSea, 2),
    'total_packs' => number_format($totalPacksSea, 2),
    'total_weight' => number_format($totalWeightSea, 2),
    'total_weight1' => number_format($totalWeightSea1, 2),
    'time' => '',
);

$updatedDataSea = $dataProviderSea->getData();
$updatedDataSea[] = $totalRowSea;

$updatedDataProviderSea = new CArrayDataProvider($updatedDataSea, array(
    'pagination' => $dataProviderSea->getPagination(),
    'sort' => $dataProviderSea->getSort(),
));
?>
<?php if (isset($dataProviderGroup)): ?>
    <h2 style="color: blue;">Air Scan Group Report</h2>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'air-scan-report-group-grid',
    'cssFile' => false,
    'dataProvider' => $dataProviderGroup,
    'filter' => $filtersForm,
    'columns' => array(
        array('name' => 'depot', 'header' => 'Depot'),
        array('name' => 'scan_date', 'header' => 'scan_date'),
        array('name' => 'A', 'header' => 'A'),
        array('name' => 'B', 'header' => 'B'),
        array('name' => 'C', 'header' => 'C'),
        array('name' => 'D', 'header' => 'D'),
        array('name' => 'A_w', 'header' => 'A(Weight)'),
        array('name' => 'B_w', 'header' => 'B(Weight)'),
        array('name' => 'C_w', 'header' => 'C(Weight)'),
        array('name' => 'D_w', 'header' => 'D(Weight)'),
        array('name' => 'A_m', 'header' => 'A(users)'),
        array('name' => 'B_m', 'header' => 'B(users)'),
        array('name' => 'C_m', 'header' => 'C(users)'),
        array('name' => 'D_m', 'header' => 'D(users)')
    ),
));
?>
<?php endif; ?>

<?php if (isset($dataProviderAir)): ?>
    <h2 style="color: blue;">Air Scan Report</h2>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'air-scan-report-grid',
    'cssFile' => false,
    'dataProvider' => $updatedDataProviderAir,
    'filter' => $filtersForm,
    'columns' => array(
        array('name' => 'depot', 'header' => 'Depot'),
        array('name' => 'user', 'header' => 'User'),
        array('name' => 'scan_date', 'header' => 'scan_date'),
        array('name' => 'dp_packs', 'header' => 'dp_scan'),
        array('name' => 'dp_weight', 'header' => 'dp_weight'),
        array('name' => 'pcs_packs', 'header' => 'pcs_scan'),
        array('name' => 'pcs_weight', 'header' => 'pcs_weight'),
        array('name' => 'carton_packs', 'header' => 'carton_scan'),
        array('name' => 'carton_weight', 'header' => 'carton_weight'),
        array('name' => 'total_packs', 'header' => 'Total Packs'),
        array('name' => 'total_weight', 'header' => 'Total Weight'),
        array('name' => 'total_weight1', 'header' => 'Original Total Weight'),
        array('name' => 'time', 'header' => 'time(hours)'),
    ),
));
?>
<?php endif; ?>

<?php if (isset($dataProviderSea)): ?>
    <h2 style="color: blue;">Sea Scan Report</h2>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'sea-scan-report-grid',
    'cssFile' => false,
    'dataProvider' => $updatedDataProviderSea,
    'filter' => $filtersForm1,
    'columns' => array(
        array('name' => 'depot', 'header' => 'Depot'),
        array('name' => 'user', 'header' => 'User'),
        array('name' => 'scan_date', 'header' => 'scan_date'),
        array('name' => 'dp_packs', 'header' => 'dp_scan'),
        array('name' => 'dp_weight', 'header' => 'dp_weight'),
        array('name' => 'pcs_packs', 'header' => 'pcs_scan'),
        array('name' => 'pcs_weight', 'header' => 'pcs_weight'),
        array('name' => 'carton_packs', 'header' => 'carton_scan'),
        array('name' => 'carton_weight', 'header' => 'carton_weight'),
        array('name' => 'total_packs', 'header' => 'Total Packs'),
        array('name' => 'total_weight', 'header' => 'Total Weight'),
        array('name' => 'total_weight1', 'header' => 'Original Total Weight'),
        array('name' => 'time', 'header' => 'time(hours)'),
    ),
));
?>
<?php endif; ?>

</div>

<script type="text/javascript">
    
</script>