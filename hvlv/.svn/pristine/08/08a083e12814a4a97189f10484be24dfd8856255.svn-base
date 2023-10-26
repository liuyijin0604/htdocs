<style type="text/css">
    .report_list .grid-view table.items tbody {
        /* body takes all the remaining available space */
        flex: 1 1 auto;
        display: block;
        max-height: 2000px;
        overflow-y: none;
        overflow-x: none;
    }
/*<![CDATA[*/
@media only screen and (max-width: 767px)  {

        /* Force table to not be like tables anymore */
        #inspection_grid_view table,#inspection_grid_view thead,#inspection_grid_view tbody,#inspection_grid_view th,#inspection_grid_view td,#inspection_grid_view tr {
            display: block;
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        #inspection_grid_view thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }
        #inspection_grid_view thead tr.filters{
            position:relative;
            top: 0;
            left: 0;
        }

        #inspection_grid_view tr { border: 1px solid #ccc; }

        #inspection_grid_view td {
            /* Behave  like a "row" */
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 30%;
        }

        #inspection_grid_view td:before {
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
            $attributes = ['hbn'=>'HBN','ref'=>'Ref','pkg'=>'pkg','no'=>'Consol No.','scan_time'=>'Scan Time','status'=>'status','process_status'=>'Process Status','upload_time'=>'Upload Time'];
            $index = 1;
            foreach ($attributes as $key=>$value){
                echo " #inspection_grid_view td:nth-of-type(".$index."):before { content: '".$key." '; }";
                $index++;
            }

        ?>
    }
/*]]>*/
</style>

<!-- <div class="inspection_grid_view" style="height:200em;"> -->
<?php

$this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'inspection_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(false),
    'columns'=>[
        ['name'=>'hbn','value'=>function($data)
        {
            if($data->isCombine==1)
            {
                echo $data->courier->name;
            }else
            {
                echo $data->relations[0]->shipment->hbn;
            }
        },'filter'=>'<input class="form-control" name="ShipmentWhInspection[hbn]" id="ShipmentWhInspection_hbn" type="text" value="'.@$model->hbn.'" onKeyPress="freshInspectionList();">'],
        ['name'=>'ref','value'=>function($data)
        {
             if($data->isCombine==1)
            {
                echo $data->courier->name;
            }else
            {
                echo $data->relations[0]->shipment->ref;
            }
        },'filter'=>'<input class="form-control" name="ShipmentWhInspection[ref]" id="ShipmentWhInspection_ref" type="text" value="'.@$model->ref.'"  onKeyPress="freshInspectionList();">'],
        ['name'=>'shipment.pkg','value'=>function($data)
        {
             if($data->isCombine==1)
            {
                $pkg = 0;
                foreach ($data->relations as $key => $relation) {
                    $pkg+=$relation->shipment->pkg;
                }

                echo $pkg;
            }else
            {
                echo $data->relations[0]->shipment->pkg;
            }
        }],
        ['name'=>'consol_no','type'=>'raw','header'=>'Consol No.','value'=>'@$data->consol->no.(empty($data->consol->mdata["container_no"])?"<p>".@$data->consol->awb."</p>":"<p>".@$data->consol->mdata["container_no"]."</p>")','filter'=>'<input class="form-control" name="ShipmentWhInspection[consol_no]" id="ShipmentWhInspection_consol_no" type="text"  value="'.@$model->consol_no.'" onKeyPress="freshInspectionList();">'],
        ['name'=>'scan_time','header'=>'Scan Time','filter'=>'<input class="form-control" name="ShipmentWhInspection[scan_time]" id="ShipmentWhInspection_scan_time" type="text"  value="'.@$model->scan_time.'" onKeyPress="freshInspectionList();">','value'=>function($data){

            if($data->isCombine==1)
            {
                $scanTimeAll = [];
                foreach ($data->relations as $key => $relation) {
                    $scanTime = json_decode($relation->scan_time,true);
                    $scanTimeAll = array_merge($scanTimeAll,$scanTime);
                }

                sort($scanTimeAll);
                if(count($scanTimeAll)==1)
                {
                     echo '<p>'.$scanTimeAll[0].'</p>';//echo '<p>'.$relation->shipment->hbn.":".$scanTime[0].'</p>';
                }else
                {
                    echo '<p>'.$scanTimeAll[0].'</p>';
                    echo '<p>'.end($scanTimeAll).'</p>';
                }
                
                return;
            }else
            {
               $scanTime = json_decode($data->relations[0]->scan_time,true);
                sort($scanTime);
                if(count($scanTime)==1)
                {
                    echo '<p>'.$scanTime[0].'</p>';
                    return;
                }

                echo '<p>'.$scanTime[0].'</p>';
                echo '<p>'.end($scanTime).'</p>';
                return;
            }
        }],
         ['header'=>'status','value'=> function($data)
         {
            if($data->isCombine==1)
            {
                // foreach ($data->relations as $key => $relation) {
                //    echo '<p>'.$relation->shipment->hbn.":".$relation->shipment->getStatus().'</p>';
                // }
                
                return;
            }else
            {
                foreach ($data->relations as $key => $relation) {
                   echo $relation->shipment->getStatus();
                        return;
                }
                return;
            }
         },'filter'=>CHtml::dropDownList('ShipmentWhInspection[status]', @$model->status, $this->t(ImParcel::$states), ['class'=>'form-control','prompt'=>$this->t('All'),"onChange"=>"freshInspectionListDirect();"])],
        ['header'=>'process_status','type'=>'raw','value'=> '"<center>".$data->getStatus()."</center>"','filter'=>CHtml::dropDownList('ShipmentWhInspection[process_status]', @$model->process_status, $this->t(ShipmentWhInspection::$states), ['class'=>'form-control','prompt'=>$this->t('All'),"onChange"=>"freshInspectionListDirect();"])],
        // ['name'=>'upload_time','filter'=>'<input class="form-control" name="ShipmentWhInspection[upload_time]" id="ShipmentWhInspection_upload_time" type="text"  value="'.@$model->upload_time.'" onKeyPress="freshInspectionList();">'],
        ['header'=>'Upload','value'=>function($data){

            foreach ($data->relations as $key => $relation)
            {
                $color = 'red';
                if($relation->process_status==ShipmentWhInspection::UPLOAD_STATUS)
                {
                    $color = 'green';
                }
                $str = "";
                if($relation->check_status==2)
                {
                     $str = 'style="background-color:rgb(255,240,240)"';
                }else if($relation->check_status==1)
                {
                    $str = 'style="background-color:rgb(150,185,125)"';
                }
               echo '<center '.$str.'><font color="'.$color.'" style="font-size:2em">●</font>'. CHtml::link('upload-'.($key+1),Yii::app()->createURL("/whscan/warehouseProcess/inspectionOperation")."?id=".$relation->id,['class'=>'message-modal-link']).'</center>';
            }

        }],
         ['class'=>'oButtonColumn',
            'template'=>'{confirm}',
            'buttons'=>[
                'confirm' => [
                    'url'=>' Yii::app()->createURL("/whscan/warehouseProcess/doneInspection")."?id=".$data->id',
                    'imageUrl'=>false,
                    'visible'=>'Acl::hasAccess("B:WarehouseProcess/doneInspection")',
                    'options' => ['class' => 'ajax_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
                ],
            ],
        ]

    ]
    
));
?>
<!-- 
</div> -->
<script type="text/javascript">
    
    $(function(){
        $('.ajax_link').on('click',function(){
            if(confirm('sure?'))
            {
                var url = $(this).attr('href');
                $.ajax({
                        url: url,
                        type: "get",
                        data: [],
                        success: function(r) {
                            freshInspectionListDirect();
                         },
                        error: function(e) {
                            console.log(e);
                        }
                }); 
            }
            return false;
        });
    })
</script>