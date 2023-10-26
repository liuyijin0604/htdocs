<div class="container-fluid">
          <?php
$this->widget('zii.widgets.CBreadcrumbs', array(
     'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		'Task List',
	),
));
      
$options=['class'=>'form-control'];
?>
 <?php if(Yii::app()->user->grp<='40'):?>
      <div style="text-align: right">
          <a href="<?=$this->createUrl('cartage/create')?>" class="btn btn-primary" style="margin-bottom: 5px">Create</a>
        </div>
     <?php $options['prompt']='All'; 
     ?>
  <?php endif;?>
  <?php $selectedList=[
                10 => 'Scheduled',
		80 => 'Confirmed',
		100 => 'Cancelled'];
    if (empty($model->status)) {
      $model->status = 10;
    }
  ?>
 <?php if(Yii::app()->user->grp>'40'):?>
  <?php  $selectedList=[10 => 'Scheduled'];
  $model->status=10;
  $model->org_id=Yii::app()->user->org;
        $org= Org::model()->find('id=:id',array(':id'=>$model->org_id));?>
  <?php echo "<h4>Welcome user from ".strtolower( $org->name)."</h4>"?>
  <?php endif;?>
  <?php 
  $ec = new CDbCriteria;
  $ec->order = 't.id DESC';
   $this->widget(
   'application.extensions.booster.TbExtendedGridView',
    array('id'=>'task-list-grid',
        'filter' => $model,
        'fixedHeader' => true,
//        'type' => 'striped bordered',
        'headerOffset' => 40,
        'responsiveTable' => true,
        'template' => "{summary}\n{items}\n{pager}",
        'dataProvider' => $model->search($ec),

         'columns' => array(
            'id',
            array('header' => 'Terminal', 'name' => 'to_id', 'value' => '$data->to_org->name'),
            array('header' => '客户', 'name' => 'job_owner', 'value' => '@$data->job->owner->name'),
            array('header' => 'Ref', 'name' => 'ref'),
            array('name' => 'note', 'value' => '@$data->getOpNote()'),
            array('header' => '板数', 'name' => 'plt'),
            array('header' => 'AWB', 'name' => 'job_awb', 'type' => 'raw', 'value' => '$data->getJobAwb()'),
            array('header' => 'Flight No.', 'name' => 'job_flight', 'type' => 'raw', 'value' => '$data->getJobFlight()'),
            array('header' => 'ETD', 'name' => 'scd_time'),
            array(
                'name'=>'status',
                'value'=>'$data->getStatus()',
                'filter'=>CHtml::dropDownList('Cartage[status]',$model->status,$selectedList,$options)
            ),
         
                   array(
                'class'=>'CButtonColumn',     //'oButtonColumn',
		'template'=>'{Update}{Cancel}',
                'buttons'=>array(
//                    'take'=>array(
//                        'visible'=>'$data->status==10',
//                         'ajax'=>true,
//                         'click'=>'js:function(){var id=$(this).parent().parent().children(":nth-child(1)").text(); var z=confirm("Are you sure to take the task?");'
//                        . 'if(z){$.get("../../take?id="+id+"&user="+'.Yii::app()->user->id.',function(){$("task-list-grid").yiiGridView.update("task-list-grid");})};return false;}',
////                          'url'=>'Yii::app()->createUrl("cart/cartage/take",array("id"=>$data->id))',
//                           'options' => array('class' => ' glyphicon glyphicon-ok'),                   
//                        ),
                    
                       'Take'=>array(
                          'visible'=>'$data->status==10',
			   'url' => 'Yii::app()->createUrl("cart/cartage/takeConfirm",["id" => $data->id])',
			   'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-ok', 'title' => 'Finish'),                  
                        ),
                    
                        'View'=>array(
                        'url'=>'Yii::app()->createUrl("cart/cartage/viewDetail",["id" => $data->id])',
                        'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-eye-open', 'title' => 'View Notes'),  
                      ),

                        'Update' => array(
                          'url'=>'Yii::app()->createUrl("cart/cartage/update",["id" => $data->id])',
                          'options' => array('class' => 'glyphicon glyphicon-eye-open', 'title' => 'Update'),  
                        ),
                       'Confirm'=>array(
                           'visible'=>'Yii::app()->user->grp<=40&&$data->status==70',
                           'url' => 'Yii::app()->createUrl("cart/cartage/finishConfirm",["id" => $data->id])',
                           'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-ok'),                   
                        ),
//
                       'Cancel'=>array(
                           'visible'=>'Yii::app()->user->grp<=40 && $data->status != 100',
                           'url' => 'Yii::app()->createUrl("cart/cartage/cancelConfirm",["id" => $data->id])',
                           'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-remove'),                   
                        ),
                     'Log'=>array(
                        'visible'=>Yii::app()->user->grp<='40'?'true':'false',
                        'url'=>'Yii::app()->createUrl("cart/cartage/viewLog",["id" => $data->id])',
                        'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-file', 'title' => 'View Log'),  
                    ),
                  
//                
            ),
         ),
     ),
   )
);?>
</div>
<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-body">
	  </div>
	  <div class="modal-footer">
		<button type="button" id='modal_close' class="btn btn-default" data-dismiss="modal"><?=$this->t('Close');?></button>
	  </div>
	</div>
  </div>
</div>
</div>
<script>
     $(function(){$('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
		$('#modal-tracking').modal();
		$('#modal-tracking .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});
    });
</script>