<?php
    $this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
         'links' => array(
                   'Storage Fee Service',
         ),
        ));
?>

<h1><?=$this->t('Online Checking Storage Fee')?></h1><!-- 在线查询滞仓费 -->

<div class="form" style="min-height:300px;">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'storage-check-form',
        'enableAjaxValidation'=>false
    ));
    ?>
    <p>Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>
    <?php $this->endWidget(); ?>
    <div id="result" style="display:none;margin: 20px; border: 1px solid;padding:30px 40px; font-weight: bold; font-size: 2rem;">
    </div>

</div>

<div>
    <?php 
        if(Org::inVip())
        {
            echo '<h1>'.$this->t('DDP Shipments waiting for confirmation to release').'</h1>';//DDP 包裹已产生滞仓费待确认发货
            $this->widget('application.extensions.booster.TbExtendedGridView',array(
            'fixedHeader'=>true,
            'id'=>'task_grid_view',
            'filter'=>$model,
            'type'=>'striped bordered',
            'headerOffset'=>40,
            'responsiveTable'=>true,
            'dataProvider'=>$model->searchClientShipment(),
            'template' => "{summary}\n{items}\n{pager}",
            'columns'=>array(
                    array('name' => 'hbn'),
                'ref',
                        'cref',
                'can',
                    array('name'=>'awb_name','header'=>'awb','type'=>'raw','value'=>'empty($data->consol)?"":$data->consol->awb',),
                    'pkg',
                array('header' => 'status', 'value' => '$data->getStatus()', ),
                array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
                'postcode',
                'weight',
                array('header'=>'value','value'=>'$data->dvalue'),
                array('header'=>'Storage Fee','value'=>'$data->getStorageFee()[0]'),
                 'consol.eta',
                        array('header'=>'extra Info','type'=>'raw','value'=>'$data->getWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]',$model->bwf,$this->t(ImParcel::$bwfs),array('prompt'=>'All','class'=>'form-control'))),
                       
                array(
                    'class'=>'application.extensions.booster.TbButtonColumn',
                    'template'=>'{updateStatus} &nbsp',
                    'buttons'=>array(
                        'updateStatus' => array(
                              'visible'=>'true',
                                        'url' => 'Yii::app()->createUrl("/ims/shipment/updateStorageStatus",array("id"=>$data->id))',
                                        'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-edit','label'=>$this->t('update'),'title' => 'update'),  
                                        'label' => 'Update_Status'
                            ),
                    ),
                )
            ),
            
        ));
        }
?>
</div>

<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" id='modal_close' class="btn btn-default" data-dismiss="modal"><?=$this->t('Close');?></button>
      </div>
    </div>
  </div>
</div>

 <?php ob_start(); ?>
<script type="text/javascript">

    $(function(){
        $('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
            $('#modal-tracking').modal();
            $('#modal-tracking .modal-body').load($(this).attr('href'));
            e.preventDefault();
        });

        $('input#scan').focus();
        $('form#storage-check-form').on('success', function(r,s,x,f){
            $('#result').html(s.msg).css('color', 'green').fadeIn(100, function(){
            });
            return true;
        }).on('submit', function(){
            $('input#scan').focus();
        });
        $('input#scan').on('focus', function(){
            $(this).select();
        });

        $('input#scan').focus();



    });
</script>

<?php $this->registerJS(ob_get_clean()); ?>