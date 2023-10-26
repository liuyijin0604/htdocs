<?php
    $filtersForm=new FiltersForm;
    if (isset($_GET['FiltersForm'])) {
        $filtersForm->filters=$_GET['FiltersForm'];
    }
    $reportService = new ReportService();
    $pt = ['',date("Y-m-01"),date("Y-m-d"),10,'','',101,''];
    $provides=$reportService->getWeightDataForDash(101,$filtersForm);
    $dataprovider = $provides[0];
    $attributes = $provides[1];

     $pt = ['',date("Y-m-01"),date("Y-m-d"),10,'','',115,''];
    $provides2=$reportService->getWeightDataForDash(115,$filtersForm);
    $columns1 = [];
    foreach ($attributes as $key=>$value)
    {
           $columns1[]=['name'=>$value,'header'=>$key]; 
    }
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
<div class="row">
    <div class="col">
        <h3>Aupost</h3>
        <?php
            $this->widget('zii.widgets.grid.CGridView', array(
                'id'=>$_GET['tabid'].'im-pl-sum-report-grid1',
                'htmlOptions'=>array('style'=>'width: 100%'),
                'cssFile' => false,
                'dataProvider'=>$dataprovider,
                'filter'=>$filtersForm,
                'columns'=>$columns1,
                ));
        ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <h3>Fastway</h3>
        <?php
            $this->widget('zii.widgets.grid.CGridView', array(
                'id'=>$_GET['tabid'].'im-pl-sum-report-grid1',
                'htmlOptions'=>array('style'=>'width: 100%'),
                'cssFile' => false,
                'dataProvider'=>$provides2[0],
                'filter'=>$filtersForm,
                'columns'=>$columns1,
                ));
        ?>
    </div>
</div>