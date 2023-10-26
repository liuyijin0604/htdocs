<style type="text/css">
    .cloumn_red_1
    {
        background-color: red;
    }

</style>
<?php
if($model->courier_id == 'TP'||$model->courier_id==4018)
{
      $this->widget('application.extensions.booster.TbExtendedGridView',array(
        'fixedHeader'=>true,
        'id'=>'gatepass_shipment_signature',
    //    'filter'=>$model,
        'type'=>'striped bordered',
        'headerOffset'=>40,
        'filter'=>$model,
        'responsiveTable'=>true,
        'dataProvider'=>$model->search(),
        'template' => "{summary}\n{items}\n{pager}",
        'afterAjaxUpdate'=>'function(){initGenerateVoice();}',
        'columns'=>array(
            array('header'=>'all','type'=>'raw','value'=>'CHtml::checkBox("shipmentId",false,["value"=>"$data->id"])','filter'=>'<input id="select_all" type="checkbox" value="1" name="select All"> all'.CHtml::hiddenField("gatepass_no",$model->gatepassNo,["class"=>"form-control"])),
            array('header'=>'HAWB/HBL','value'=>'$data->shipment->hbn','filter'=>CHtml::textField('gatepassShipment[hbn]',$model->hbn,["class"=>'form-control'])),
            array('header'=>'Reference number','value'=>'$data->getRef()','filter'=>CHtml::textField('gatepassShipment[ref]',$model->ref,["class"=>'form-control'])),
            array('name'=>'shipment.consol.service','value'=>'@Consol::$services[$data->shipment->consol->service]','filter'=>CHtml::dropDownList('gatepassShipment[service]',$model->service,Consol::$services,["class"=>'form-control','prompt'=>'Select'])),
            array('name'=>'shipment.can','filter'=>CHtml::textField('gatepassShipment[memo]',$model->memo,["class"=>'form-control'])),
            array('header'=>'Cnee Name','value'=>'$data->getCneeName()','filter'=>CHtml::textField('gatepassShipment[cname]',$model->cname,["class"=>'form-control'])),
            array('header'=>'Storage Fee','value'=>'$data->shipment->cargo_process->getStorageFee(0)'),
            array('header'=>'Storage Days','value'=>'$data->shipment->cargo_process->getStorageFee(1)'),
            array('header'=>'Storage Start Date','type'=>'raw','value'=>'$data->shipment->checkStorageInvoiceStatus(true)==0?$data->shipment->cargo_process->getStorageFee(2,true):""'),
            array('header'=>'Storage Paid Status','value'=>'$data->shipment->checkStorageInvoiceStatus()', 'cssClassExpression' => '$data->shipment->getGatepassStorageColor()'),
            array('header'=>'Packages','value'=>'$data->getPackages()','filter'=>CHtml::textField('gatepassShipment[pkg]',$model->pkg,["class"=>'form-control'])),
            array('header'=>'Weight','value'=>'$data->getWeight()','filter'=>CHtml::textField('gatepassShipment[weight]',$model->weight,["class"=>'form-control'])),
            // array('header'=>'Delivery/Pickup booking time','value' => '$data->shipment->cargo_process->getDeliveryBookingTime()','cssClassExpression' => '$data->shipment->cargo_process->getShowColor()'),
            ['header' => 'pickupBookingTime','header'=>'Pickup booking time','value' => '@$data->shipment->mdata["pickup_booking_time"]','cssClassExpression' => '$data->shipment->cargo_process->getShowColor("pickup")','filter'=>CHtml::textField('gatepassShipment[pickupBookingTime]',$model->pickupBookingTime,["class"=>'form-control'])],
            [
                'class'=>'oButtonColumn',
                'template'=>'{generate invoice}&nbsp;{view invoice}&nbsp;{cancel}',
                'buttons'=>[
                    'generate invoice' => [
                        'url'=>' Yii::app()->createURL("gatePass/createSeaStorageInvoice")."?shipment_id=".$data->shipment->id',
                        'imageUrl'=>false,
                        'visible'=>'$data->shipment->checkStorageInvoiceStatus(true)==0?true:false',
                        'options' => ['class' => 'grid_edit_btn createStorageInvoice', 'label'=>$this->t('Operation'), 'title' => '$data->shipment->id'],
                    ],
                    'view invoice' => [
                        'url'=>' Yii::app()->createURL("../invoice/print")."?id=".$data->shipment->getStorageInvoiceId()',
                        'imageUrl'=>false,
                        'visible'=>'$data->shipment->checkStorageInvoiceStatus(true)!=8&&$data->shipment->checkStorageInvoiceStatus(true)!=10&&$data->shipment->checkStorageInvoiceStatus(true)!=0&&$data->shipment->checkStorageInvoiceStatus(true)!=-1&&$data->shipment->checkStorageInvoiceStatus(true)!=12&&$data->shipment->checkStorageInvoiceStatus(true)!=13?true:false',
                        'options' => ['class' => 'grid_edit_btn','target'=>'_blank', 'label'=>$this->t('Operation')],
                    ],'cancel' => [
                        'url'=>' Yii::app()->createURL("gatePass/cancelPrepare")."?id=".$data->shipment->cargo_process->id',
                        'imageUrl'=>false,
                        'visible'=>'true',
                        'options' => ['class' => 'grid_edit_btn cancelPrepare','target'=>'_blank', 'label'=>$this->t('Cancel'), 'title' => '$data->shipment->cargo_process->id'],
                    ]
                ],
            ]
        ),
    ));
}else
{
  if($model->courier_id == 4018 )
  {
    $columns = array(
            array('header'=>' ','type'=>'raw','value'=>'CHtml::checkBox("shipmentId",true,["value"=>"$data->fid"])'),
            array('header'=>'HBN','value'=>'$data->imparcel->hbn'),
            array('name'=>'connote_no','value'=>'$data->getConnoteNumber()'),
            array('header'=>'Cnee Name','value'=>'$data->getCneeName()'),
            'sno',
            'scan_time',
        );
     $this->widget('application.extensions.booster.TbExtendedGridView',array(
        'fixedHeader'=>true,
        'id'=>'gatepass_shipment_signature',
    //    'filter'=>$model,
        'type'=>'striped bordered',
        'headerOffset'=>40,
        'responsiveTable'=>true,
        'dataProvider'=>$model->search(),
        'template' => "{summary}\n{items}\n{pager}",
        'columns'=>$columns,
    ));
  }elseif(in_array($model->courier_id,[2619,Org::ORGID_COURIER_SF,'fasthorse','AUP',4432,'auk',4117,'huaxin','IMILE']))
  {
    $columns = array(
            array('header'=>' ','type'=>'raw','value'=>'CHtml::checkBox("shipmentId",(!empty($data[\'isHeldedShipment\'])?true:false),["value"=>$data[\'id\']])'),
            array('name'=>'awb','header'=>'AWB/Container #'),
            array('name'=>'cargo_receipt_pallets'),
            array('name'=>'cargo_receipt_cages')
        );
     $this->widget('application.extensions.booster.TbExtendedGridView',array(
        'fixedHeader'=>true,
        'id'=>'gatepass_shipment_signature',
        'filter'=>$consols[1],
        'type'=>'striped bordered',
        'headerOffset'=>40,
        'responsiveTable'=>true,
        'dataProvider'=>$consols[0],
        'template' => "{summary}\n{items}\n{pager}",
        'columns'=>$columns,
    ));
  }else
  {
    $columns = array(
            array('header'=>' ','type'=>'raw','value'=>'CHtml::checkBox("shipmentId",true,["value"=>"$data->fid"])'),
            array('name'=>'connote_no','value'=>'$data->getConnoteNumber()'),
            array('header'=>'Cnee Name','value'=>'$data->getCneeName()'),
            'sno',
            'scan_time',
        );
     $this->widget('application.extensions.booster.TbExtendedGridView',array(
        'fixedHeader'=>true,
        'id'=>'gatepass_shipment_signature',
    //    'filter'=>$model,
        'type'=>'striped bordered',
        'headerOffset'=>40,
        'responsiveTable'=>true,
        'dataProvider'=>$model->search(),
        'template' => "{summary}\n{items}\n{pager}",
        'columns'=>$columns,
    ));
  }
}
?> 
<input type="hidden"  id="shipment_number" value="<?=sizeof($model->search()->data)?>">

<script type="text/javascript">
function initGenerateVoice()
{
    $('.createStorageInvoice').on('click',function(){
        if( confirm('Are you sure to generate invoice?')){
            let shipmentId = $(this).attr("title");
             $.ajax({
                        url: '<?=$this->createUrl("gatePass/createSeaStorageInvoice")?>'+'?shipment_id='+shipmentId,
                        type: "get",
                        data: [],
                        dataType:"json",
                        success: function(r) {
                            if(r.done)
                             {
                                $('#notifc').notify({message: {html: "success"}}).show();
                             }else
                             {
                                $('#notifc').notify({message: {html: r.msg}, type: 'danger'}).show();
                             }
                             $('#gatepass_shipment_signature').yiiGridView('update');
                         },
                        error: function(e) {
                            console.log(e);
                        }
                    });         
        }
        return false;
    });

    $('.cancelPrepare').on('click',function(){
        if( confirm('Are you sure to cancel prepare?')){
            let cid = $(this).attr("title");
             $.ajax({
                        url: '<?=$this->createUrl("gatePass/cancelPrepare")?>'+'?id='+cid,
                        type: "get",
                        data: [],
                        dataType:"json",
                        success: function(r) {
                            if(r.done)
                             {
                                $('#notifc').notify({message: {html: "success"}}).show();
                             }else
                             {
                                $('#notifc').notify({message: {html: r.msg}, type: 'danger'}).show();
                             }
                             $('#gatepass_shipment_signature').yiiGridView('update');
                         },
                        error: function(e) {
                            console.log(e);
                        }
                    });         
        }
        return false;
    });
    ccheck = 0;
     $('#select_all').on('click',function()
     {
         if(ccheck==0)
         {
             $("input[name='shipmentId']").click();
             ccheck = 1;
         }else
         {
             $("input[name='shipmentId']").removeAttr("checked");
             ccheck = 0;
         }
         return false;
     });
}
initGenerateVoice();
</script>
    
