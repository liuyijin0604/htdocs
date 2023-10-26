<style type="text/css">
/*<![CDATA[*/
@media only screen and (max-width: 767px)  {

        /* Force table to not be like tables anymore */
        #task_grid_view<?=$uid?> table,#task_grid_view<?=$uid?> thead,#task_grid_view<?=$uid?> tbody,#task_grid_view<?=$uid?> th,#task_grid_view<?=$uid?> td,#task_grid_view<?=$uid?> tr {
            display: block;
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        #task_grid_view<?=$uid?> thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }
        #task_grid_view<?=$uid?> thead tr.filters{
            position:relative;
            top: 0;
            left: 0;
        }

        #task_grid_view<?=$uid?> tr { border: 1px solid #ccc; }

        #task_grid_view<?=$uid?> td {
            /* Behave  like a "row" */
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 30%;
        }

        #task_grid_view<?=$uid?> td:before {
            /* Now like a table header */
            position: absolute;
            /* Top/left values mimic padding */
            top: 6px;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
        }

        .grid-view .button-column {
            text-align: left;
            width:auto;
        }
        /*
        Label the data
        */
        <?php
            $index = 1;
            foreach ($attributes as $key=>$value){
                echo " #task_grid_view".$uid." td:nth-of-type(".$index."):before { content: '".$key." '; }";
                $index++;
            }

        ?>
    }
/*]]>*/
</style>
<?php

    $columns=[];
    $columns1=[];
    foreach ($attributes as $key=>$value){
        if(is_array($value))
        {
            $columns[]=['name'=>$value[0],'header'=>$key,'type'=>'raw','filter'=>'false','value'=>'empty($data["'.$value[0].'"])?"无":$data["'.$value[0].'"]','cssClassExpression'=>$value[1]]; 
        }else
        {
            $columns[]=['name'=>$value,'header'=>$key,'type'=>'raw','filter'=>'false','value'=>'empty($data["'.$value.'"])?"无":$data["'.$value.'"]']; 
        }
    }
$this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'task_grid_view'.$uid,
    //'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model,
    'columns'=>$columns
    
));
?>