<h1>Received Shippments</h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'im-rts-rts-parcel-grid',
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],     //$model->search(),
    'filter'=>$dataProvider[1],
    'columns'=>array(
        
        array('name' =>'hbn', 'header'=>'Connote','type' => 'raw','value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data["id"]))."\" class=\"tab_link\" title=\"".$data["hbn"]."\">".$data["hbn"]."</a>"',),
        array('name'=>'ref','header'=>'Ref', ),
        array('name'=>'agent_name','header'=>'Customer'),
        array('name'=>'cnee_name','header'=>'Cnee Name'),
        array('name'=>'ddpt_id','header'=>'Depot'),
        array('name'=>'pkg','header'=>'Packages'),
        array('name'=>'postcode','header'=>'Postcode'),
        array('name'=>'weight','header'=>'Weight'),
        array('name'=>'received','header'=>'Received'),
        array('name'=>'note','header'=>'Note'),
        array('header' => 'Location', 'value' => '!empty($data["used_location"]) ? "Already Picked" : @$data["location"]'),

            array(
                'class'=>'oButtonColumn',
                'template'=>'{update}{rtc}',
                'buttons'=>array
                (
                    'update' => array(
                        'imageUrl'=>false,
                        'visible'=>'true',
                        'url' => 'Yii::app()->createURL("rts/update",array("id" => $data["id"]))',
                        'label'=>$this->t('Mod'),
                        'options' => array('class' => 'jqm_link grid_edit_btn',  'title' => '$data["hbn"]'),
                    ),
                    'rtc' => array(
                        'imageUrl'=>false,
                        'visible'=>'true',
                        'url' => 'Yii::app()->createURL("rts/rtcop",array("id" => $data["id"]))',
                        'label'=>$this->t('RTC'),
                        'options' => array('class' => 'rts_rtc_btn grid_edit_btn',  'title' => '$data["hbn"]'),
                    ),
                ),
            ),
    ),
)); ?>

<script type="text/javascript">
    $(function(){

        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');
        var waitingResp = false;
        tab.on('onOpen', function(){
            $('#im-rts-rts-parcel-grid', panel).yiiGridView('update');
        });

        $(panel).on('click','.rts_rtc_btn',function(e){
            e.preventDefault();

            if ( !confirm( 'Are you sure return to customer?') ) { return;}

            if ( waitingResp ) return;
            waitingResp = true;
            var href = $(this).attr('href');
            $.ajax({
                type : 'GET',
                url : href,
                dataType: 'json',
                success:function(resp){
                    alert(resp.msg);
                    waitingResp = false;
                    $('#im-rts-rts-parcel-grid', panel).yiiGridView('update');
                }
            });
        });


    });
</script>