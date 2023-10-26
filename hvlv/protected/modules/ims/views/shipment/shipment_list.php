<style>
    .cloumn_red{
        background-color:red;
    }
</style>
<?php
$list = AppHelper::setting2List('pods'); ksort($list);
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
    'links' => array(
           'Held Shipment List',
    ),
));
?>
<h2>Shipment</h2>
<br>
<a id="export_search" style = "float:right;"target="_blank" href=""><div class="icon"></div> Export Current Search</a> 
<br>
<a id="upload_customs_files" style = "float:right;" href="<?=Yii::app()->createUrl("ims/shipment/uploadCustomsFiles")?>"><div class="icon"></div> Upload Customs Files</a> 
<br>
<?php $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'task_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(),
    'template' => "{summary}\n{items}\n{pager}",
    'afterAjaxUpdate' => 'js:function(id, data){ $(\'#egw0\').after(\'<div class="pull-right"><button type="button" data-toggle="modal" data-target="#modal-export" class="btn btn-default btn-sm">Export</button></div>\'); }',
    'columns'=>array(
            array('name' => 'hbn'),
        'ref',
                'cref',
       
            array('name'=>'awb_name','header'=>'awb','type'=>'raw','value'=>'empty($data->consol)?"":$data->consol->awb',),
            'pkg',
        array('header' => 'status', 'value' => '$data->getStatus()', ),
        array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
        'postcode',
        'weight',
        array('name' => 'consol_eta','header'=>'Eta', 'value' => '@$data->consol->eta','cssClassExpression' => '$data->getStorageWarning()?"cloumn_red":""'),
        array('name' => 'held_date','header'=>'Held Date', 'value' => '@ImParcelService::getTimePoint($data,6)','filter'=>CHtml::textField('ImParcel[heldDate]',$model->heldDate,["class"=>'form-control'])),
        array('header'=>'$','value'=>'@Invoice::$currencies[$data->currency]'),
        array('header'=>'value','value'=>'$data->dvalue'),        
        array('header'=>'extra Info','type'=>'raw','value'=>'$data->getWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]',$model->bwf,$this->t(ImParcel::$bwfs),array('prompt'=>'All','class'=>'form-control'))),
        array('name'=>'process_status','type'=>'raw','value'=>'$data->getProcessStatus()','filter'=>CHtml::dropDownList('ImParcel[process_status]',$model->process_status,$this->t(Yii::app()->user->grp == 72? ShipmentProcess::brokerStats() : ShipmentProcess::$statesdisplay),array('prompt'=>'All','class'=>'form-control'))),
        array('header'=>'Depot','type'=>'raw','value'=>'$data->consol?$data->consol->pod:""','filter'=>CHtml::dropDownList('ImParcel[depot]',$model->depot,$this->t($list),array('prompt'=>'All','class'=>'form-control'))),
               
        array(
            'class'=>'application.extensions.booster.TbButtonColumn',
            'template'=>'{View} &nbsp',
            'buttons'=>array(
                'View' => array(
                      'visible'=>'true',
                                'url' => 'Yii::app()->createUrl("imtk/shipment/view",array("id"=>$data->id))',
                                'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-edit','label'=>$this->t('View'),'title' => 'View'),  
                    ),
            ),
        )
    ),
    
));
?>
<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" id='modal_close' class="btn btn-default" data-dismiss="modal">////<?=$this->t('Close');?></button>
      </div>
    </div>
  </div>
</div>
</div>
<script type="text/javascript">
        function exportHeld()
    {
          var q = $('.filters input, .filters select').serialize();
            $(this).attr('href', '<?=$this->createUrl('shipment/exportHeld');?>'+ '?' + q);
            return true;
    }
    </script>

 <?php ob_start(); ?>
<script type="text/javascript">
$(function(){

    $('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
        $('#modal-tracking').modal();
        $('#modal-tracking .modal-body').load($(this).attr('href'));
        e.preventDefault();
    });
    $('#export_search').on('mousedown', function(){
        var q = $('.filters input, .filters select').serialize();
        $(this).attr('href', '<?=$this->createUrl('shipment/exportHeldList');?>'+ '?' + q);
        return true;
    });
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>