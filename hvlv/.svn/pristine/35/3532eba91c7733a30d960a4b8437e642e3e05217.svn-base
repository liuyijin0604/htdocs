<h2>view All Report</h2>

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

<div style="width:50%">
    <h3>FBA</h3>
    <?php  
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'cargo_main_menue2'.$_GET['tabid'],
            'cssFile' => false,
            'dataProvider'=>$dataProviderFBA[0],
            'filter'=>$dataProviderFBA[1],
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

<div style="width:50%">
    <h3>B2B</h3>
    <?php  
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'cargo_main_menue2'.$_GET['tabid'],
            'cssFile' => false,
            'dataProvider'=>$dataProviderB2B[0],
            'filter'=>$dataProviderB2B[1],
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

<div style="width:50%">
    <h3>Normal</h3>
    <?php  
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'cargo_main_menue2'.$_GET['tabid'],
            'cssFile' => false,
            'dataProvider'=>$dataProviderNormal[0],
            'filter'=>$dataProviderNormal[1],
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

<div style="width:50%">
        <h3>Pickup</h3>
        <?php  
        $this->widget('zii.widgets.grid.CGridView',array(
                'id'=>'cargo_main_menue2'.$_GET['tabid'],
                'cssFile' => false,
                'dataProvider'=>$dataProviderPickup[0],
                'filter'=>$dataProviderPickup[1],
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

<h3>Overview</h3>
<div style="width:50%">
    <?php  
    foreach ($dataProvider[0] as $key => $thisDataProvider) {
        if($key != 0)
        {
            echo "<br>"."Truck Agent:".$truckUserList[$key];
        }
         $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'cargo_main_menue_agent'.$key.$_GET['tabid'],
            'cssFile' => false,
            'dataProvider'=>$thisDataProvider,
            'filter'=>$dataProvider[1],
            'columns'=>array(
           array('name'=>'status','headerHtmlOptions' => array('style' => 'display:none'),'filterHtmlOptions' => array('style' => 'display:none'),
                'htmlOptions' => array('style' => 'display:none'),'type'=>'raw'),
           array('name'=>'status','value'=>'@CargoProcess::$processTypes[$data["status"]]'),
            'number',
            array('name'=>'day1','header'=>'<=1 days'),
            array('name'=>'day2','header'=>'2 days'),
            array('name'=>'day3','header'=>'>=3 days','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'),
          
        ),
    ));
    }
   ?>
</div>


