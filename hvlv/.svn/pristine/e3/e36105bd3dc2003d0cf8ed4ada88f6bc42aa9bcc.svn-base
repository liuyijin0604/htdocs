<div class="container">
        <?php
$this->widget('zii.widgets.CBreadcrumbs', array(
     'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		'Finish List',
	),
));
?>
       
    <div style="background-color: #EEEEEE; padding:5px;">
        <h3>Task Finish List</h3>
    </div>
    
     <hr>
   <?php if(Yii::app()->user->grp>'40'):?>
  <?php  $model->org_id=Yii::app()->user->org?>
  <?php endif;?>
 
 <?php 
   $this->widget(
   'application.extensions.booster.TbExtendedGridView',
    array('id'=>'task-list-grid',
        'filter' => $model,
        'fixedHeader' => true,
        'type' => 'striped bordered',
        'headerOffset' => 40,
        'responsiveTable' => true,
        'template' => "{summary}\n{items}\n{pager}",
        'dataProvider' => $model->search2(),
        'template' => "{items}",
        
        'columns' => array(
            'id',
             array('name'=>'org_name',
                   'header'=>'Assign To',
                   'value'=>'$data->org->name',
                 ),
            'from_addr',
            'to_addr',
            'scd_time',
            
            array(
                'name'=>'status',
                'value'=>'$data->getStatus()',
                'filter'=>CHtml::dropDownList('Cartage[status]',$model->status,[70=>'Completed',80=>'Confirmed'],array('class'=>'form-control','prompt'=>'All')),
            ),
         
            'take_time',
            'comp_time',
                   array(
                'visible'=>Yii::app()->user->grp<='40'?true:FALSE,
                'class'=>'CButtonColumn',     //'oButtonColumn',
		'template'=>'{View}{Confirm}{Log}',
                'buttons'=>array(
                       'View'=>array(
                        'url'=>'Yii::app()->createUrl("cart/cartage/viewDetail",["id" => $data->id])',
                        'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-eye-open', 'title' => 'View Notes'),  
                      ),
                'Confirm'=>array(
                           'visible'=>Yii::app()->user->grp<=40?'true':'false',
                           'url' => 'Yii::app()->createUrl("cart/cartage/finishConfirm",["id" => $data->id])',
                           'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-ok'),                   
                        ),
                     'Log'=>array(
                        'visible'=>Yii::app()->user->grp<='40'?'true':'false',
                        'url'=>'Yii::app()->createUrl("cart/cartage/viewLog",["id" => $data->id])',
                        'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-ok', 'title' => 'View Log'),  
                        
                    ),
                      ),
                   
               
//
//                
            ),
      
        ),
    )
);
 
 ?>
</div>

<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
  <div class="modal-dialog">
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
<script>
    $(function(){$('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
		$('#modal-tracking').modal();
		$('#modal-tracking .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});
    });
</script>