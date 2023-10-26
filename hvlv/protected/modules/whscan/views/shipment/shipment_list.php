<style>
    .cloumn_red{
        background-color:red;
    }
</style>
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
    'links' => array(
           'Shipment List',
    ),
));
?>
<h2>Shipment</h2>
<br>
<?php $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'task_grid_view',
    //'filter'=>$model,
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
        array('name' => 'rack','type'=>'raw','value' => '$data->getRackName(false,true)'),
        'postcode',
        'weight',
        array('name' => 'Scan Date','type'=>'raw','value' => '$data->getScanTime()','cssClassExpression'=> '($data->isScanTimeToday())? "red": ""'),
        //array('header'=>'value','value'=>'$data->dvalue'),
                //array('header'=>'extra Info','type'=>'raw','value'=>'$data->getWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]',$model->bwf,$this->t(ImParcel::$bwfs),array('prompt'=>'All','class'=>'form-control'))),
               
        array(
            'class'=>'application.extensions.booster.TbButtonColumn',
            'template'=>'{SortHeld}{PutAway}',
            'buttons'=>array(
                'SortHeld' => array(
                        'visible'=>'empty($data->getRackName(false,true))?true:false',
                        'imageUrl'=>false,
                        'url' => 'Yii::app()->createUrl("whscan/shipment/held")',
                        //'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Job Details'), 'title' => '$data->id'],
                        'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-edit','label'=>$this->t('View'),'title' => 'View'),  
                    ),
                'PutAway' => array(
                    'visible'=>'empty($data->getRackName(false,true))?false:true',
                    'imageUrl'=>false,
                    'url' => 'Yii::app()->createUrl("whscan/shipment/putAwayPallet")',
                        //'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Job Details'), 'title' => '$data->id'],
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