
<div class="container-fluid">
    <?php
$this->widget('zii.widgets.CBreadcrumbs', array(
     'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		'Taking List',
	),
));
?>
    <?php  echo '<div class="row"><div class="col col-md-10 col-sm-8">';?>
    <div style="background-color: #EEEEEE; padding:5px;">
        <h3>Work List</h3>
    </div>
    <br>
  <?php if(Yii::app()->user->grp>'40'):?>
  <?php  $model->org_id=Yii::app()->user->org?>
  <?php endif;?>
  <?php 
 
   $this->widget(
   'application.extensions.booster.TbExtendedGridView',
    array('id'=>'take-list-grid',
        'filter' => $model,
        'fixedHeader' => true,
        'type' => 'striped bordered',
        'headerOffset' => 40,
        'responsiveTable' => true,
        'template' => "{summary}\n{items}\n{pager}",
        // 40px is the height of the main navigation at bootstrap
        'dataProvider' => $model->search1(),
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
                'filter'=>CHtml::dropDownList('Cartage[status]',$model->status,[20=>'Accepted'],['class'=>'form-control'])
            ),
         
            'take_time',
                   array(
                'class'=>'CButtonColumn',     //'oButtonColumn',
		'template'=>'{Finish}&nbsp{View}&nbsp{Log}',
                'buttons'=>array(
                    'Finish'=>array(
                          'visible'=>'true',
			   'url' => 'Yii::app()->createUrl("cart/cartage/editFinish",["id" => $data->id])',
			   'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-ok', 'title' => 'Finish'),                  
                        ),
                       'View'=>array(
                        'url'=>'Yii::app()->createUrl("cart/cartage/viewDetail",["id" => $data->id])',
                        'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-eye-open', 'title' => 'View Notes'),  
                      ),
                    'Log'=>array(
                        'visible'=>Yii::app()->user->grp<='40'?'true':'false',
                        'url'=>'Yii::app()->createUrl("cart/cartage/viewLog",["id" => $data->id])',
                        'options' => array('class' => 'tracking-modal-link glyphicon glyphicon-ok', 'title' => 'View Log'),  
                        
                    ),
                ),
              
            ),
      
        ),
    )
);
 
 ?>
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