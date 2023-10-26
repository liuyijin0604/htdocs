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
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(),
    'template' => "{summary}\n{items}\n{pager}",
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
		array('name' => 'consol_eta','header'=>'Eta', 'value' => '@$data->consol->eta'),
		array('header'=>'value','value'=>'$data->dvalue'),
                array('header'=>'extra Info','type'=>'raw','value'=>'$data->getWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]',$model->bwf,$this->t(ImParcel::$bwfs),array('prompt'=>'All','class'=>'form-control'))),
               
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

 <?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
		$('#modal-tracking').modal();
		$('#modal-tracking .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});

});
</script>
<?php $this->registerJS(ob_get_clean()); ?>