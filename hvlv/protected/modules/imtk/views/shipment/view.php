<?php
//$this->widget('zii.widgets.CBreadcrumbs', array(
//        'homeLink'=>CHtml::link('Home', array('site/index')),
//	'links' => array(
//            'list' => array('shipment/list'),
//            'view shipment',
//	),
//));
?>
<h2>View Shipment-<?=$model->hbn?>-<?=$model->getStatus()?></h2>
<hr>
<?php 
$main=$this->renderPartial('tab_main',array('model'=>$model,'org'=>$org),true);
$track=$this->renderPartial('tab_tracking',array('model'=>$model,'org'=>$org),true);
$tabs=[];
$tabs[]=array(
            'label'=>'Tracking',
            'content'=>$track,
            'active'=>true,
         );
$tabs[]=array(
            'label'=>'Main',
            'content'=>$main,
            
         );


$this->widget('application.extensions.booster.TbTabs',array(
    'id'=>'viw-task-'.$model->id,
    'type'=>'tabs',
    'tabs'=>$tabs,
 ));
?>


<script>
    
    $(function(){
      $('form#edit-notes-form').on('success',function(){
          $('#modal_close').trigger('click');
          $("task-list-grid").yiiGridView.update("task-list-grid");
     
        });
    });
</script>