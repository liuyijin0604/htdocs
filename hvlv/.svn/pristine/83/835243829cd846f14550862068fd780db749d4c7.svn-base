
<div style="right: 20px;position: absolute;top:20px;">
     <a class="jqm_link" href="<?=$this->createUrl('rts/checkRtsStatus');?>" title="Check RTS Status"><div class="icon" style="background-position:-16px 0"></div>Check RTS Status</a>
    <a class="jqm_link" href="<?=$this->createUrl('rts/importRtc');?>" title="Received RTS"><div class="icon" style="background-position:-16px 0"></div>Import RTC</a>
    <a class="tab_link" href="<?=$this->createUrl('rts/rrts');?>" title="Received RTS"><div class="icon" style="background-position:-16px 0"></div>Received RTS</a>
    <a class="tab_link" href="<?=$this->createUrl('rts/reshipping');?>" title="Reshipping"><div class="icon" style="background-position:-16px 0"></div>Reshipping</a>
    <a class="tab_link" href="<?=$this->createUrl('rts/rtc');?>" title="RTC"><div class="icon" style="background-position:-16px 0"></div>RTC</a>
    <a class="tab_link" href="<?=$this->createUrl('rts/finished');?>" title="Finished"><div class="icon" style="background-position:-16px 0"></div>Finished</a>
    <a target="_blank" class="export_search" href="<?=$this->createUrl('rts/discard')?>" title="Discard"><div class="icon" style="background-position:-48px -688px"></div>Discard</a>
    <a target="_blank" class="export_search" href="<?=$this->createUrl('rts/returnOrg', ['org_id' => 1206])?>" title="Discard BFE"><div class="icon" style="background-position:-48px -688px"></div>BFE Return</a>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'im-rts-parcel-grid',
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],     //$model->search(),
    'filter'=>$dataProvider[1],
    'columns'=>array(
        
        array('name' =>'hbn', 'header'=>'Connote','type' => 'raw','value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data["id"]))."\" class=\"tab_link\" title=\"".$data["hbn"]."\">".$data["hbn"]."</a>"',),
        array('name'=>'ref','header'=>'Ref', ),
        array('name'=>'department','header'=>'Department','value'=>'$data["department"]'),
         array('name'=>'rts_warehouse','header'=>'Warehouse','value'=>'$data["rts_warehouse"]'),
        array('name'=>'agent_name','header'=>'Customer'),
        array('name'=>'cnee_name','header'=>'Cnee Name'),
        array('name'=>'pkg','header'=>'Packages'),
        array('name'=>'rts_packages','header'=>'RTS Packages','value'=>'$data["rts_packages"]'),
        array('name'=>'postcode','header'=>'Postcode'),
        array('name'=>'weight','header'=>'Weight'),
        array('name'=>'received','header'=>'Received'),
        array('name'=>'note','header'=>'Note'),
        array('name'=>'location','header' => 'Location', 'value' => '@$data["location"]'),

        
      //  array('header' => 'Location', 'type' => 'raw', 'value' => '$data->getLocation()'),
    //    array('header' => 'Tranship', 'value' => '$data->getTranships()'),
//        array('name' => 'meta', 'header'=>'Received','type' => 'raw','value' => 'empty($data->mdata["rts_scan_date"])? "" : $data->mdata["rts_scan_date"]'),
      //  array('name' => 'bwf', 'header' => 'Warnings', 'type' => 'raw', 'value' => '$data->getWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]', $model->bwf, $this->t(ImParcel::$bwfs), array('prompt'=>$this->t('All'))),),
     
    /*
        array(
            'class'=>'oButtonColumn',
            'template'=>'{update}{disposal}',
            'buttons'=>array
            (
                'update' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'label' => 'Reshipping',
                    'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Reshipping'), 'title' => '$data->hbn'),
                ),

                'disposal' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'label' => 'Return',
                    'url' => 'Yii::app()->createUrl("rts/disposal", ["id" => $data->id])',
                    'options' => array('class' => 'tab_link grid_gallery_btn', 'label'=>$this->t('Return'), 'title' => '$data->hbn'),
                ),
            ),
        ),
    */
    ),
)); ?>


<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');
        tab.on('onOpen', function(){
            $('#im-rts-parcel-grid', panel).yiiGridView('update');
        });
    });
</script>