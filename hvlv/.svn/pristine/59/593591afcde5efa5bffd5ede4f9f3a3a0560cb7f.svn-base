<h2>Consol Process</h2>
<h3>Overview</h3>
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
<div style="display: inline-block;width: 68%;vertical-align: top;">
    <div style="display: inline-block;width:40%;" id="consol_process_kpi_view">
        <h3>MTD KPI Report</h3>
        <div style="width:50%">
        <?php  
        $this->widget('zii.widgets.grid.CGridView',array(
                'id'=>'cargo_main_menue-sub'.$_GET['tabid'],
                'cssFile' => false,
                'dataProvider'=>$dataProviderSummary[0],
                'filter'=>$dataProviderSummary[1],
                'columns'=>array(
               array('name'=>'name'),
                array('name'=>'success','header'=>'On Time'),
                array('name'=>'failure','header'=>'Delayed'),
                array('name'=>'total','header'=>'Total'),
                array('name'=>'percentage','header'=>'On Time Percentage')
              
            ),
        ));?>
        </div>
    </div>
    <div style="display: inline-block;width:40%;" id="consol_process_kpi_view">
        <h3>UB/SCR Daily Report</h3>
        <div style="width:50%">
        <?php  
        $this->widget('zii.widgets.grid.CGridView',array(
                'id'=>'cargo_main_menue-sub2'.$_GET['tabid'],
                'cssFile' => false,
                'dataProvider'=>$dataProviderSummary2[0],
                'filter'=>$dataProviderSummary2[1],
                'columns'=>array(
               array('name'=>'name'),
                array('name'=>'done','header'=>'Done'),
                array('name'=>'left','type'=>'raw','header'=>'Left','value'=>'"<a class=\"tab_link\" href=\"".Yii::app()->createUrl("consolProcess/showConsolList",["no"=>join(",",$data["consolNos"])])."\">".$data["left"]."</a>"'),
                array('name'=>'total','header'=>'Total'),
                array('name'=>'percentage','header'=>'Done Percentage')
              
            ),
        ));?>
        </div>
    </div>


    <div  class="pane" id="consol_process_dp_view_type">
    <?=$this->render('sub_type_list',array('dataProvider2'=>$dataProvider999,'dataProvider'=>$dataProvider,'name'=>$name,'dataProvider3'=>$dataProvider4));?>
    </div>



    <div class="pane" id="consol_process_dp_view" style="width:200px">
        
    </div>
</div>
<div style="display: inline-block;width: 30%;vertical-align: top;border-left: 2px solid #15538B">
    <div style="width:80%;display:inline-block;margin-left: 3em;margin-top: 3em;">
        <h3>Air Process</h3>
    <?php  
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'consol_main_menue-sub'.$_GET['tabid'],
            'cssFile' => false,
            'dataProvider'=>$dataProvider[0],
            'filter'=>$dataProvider[1],
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

    <div style="width:80%;display:inline-block;margin-left: 3em;margin-top: 3em;">
        <h3>Sea Process</h3>
    <?php  
    $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'consol_main_menue-sub'.$_GET['tabid'],
            'cssFile' => false,
            'dataProvider'=>$dataProvider4[0],
            'filter'=>$dataProvider4[1],
            'columns'=>array(
           array('name'=>'status','headerHtmlOptions' => array('style' => 'display:none'),'filterHtmlOptions' => array('style' => 'display:none'),
                'htmlOptions' => array('style' => 'display:none'),'type'=>'raw'),
           array('name'=>'status','value'=>'@ConsolProcess::$newSeaStates[$data["status"]]'),
            'number',
            array('name'=>'day1','header'=>'<=2 Week'),
            array('name'=>'day2','header'=>'<= 1 month'),
            array('name'=>'day3','header'=>'> 1 month','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'),
          
        ),
    ));?>
    </div>
</div>
<script>
    $(function(){
       var tab=$("<?=$_GET['tabid']?>");
       var panel=tab.data('panel');
       $("#consol_process_dp_view_type",panel).on('click',"table tbody td",function(){
         var typeId=parseInt($(this).parent().children(':nth-child(1)').html());
         var data = {};
        data['type_id'] = typeId;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("consolProcess/podList",array('tabid'=>$_GET['tabid'])) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#consol_process_dp_view').html(resp);
            },
        });
        return false;
       })
   })
</script>
