<h2> Job Update</h2>
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
             'Task List'=>array('task/index'),
             'Update -'.$model->no
	
	),
));
$overview=$this->renderPartial('overview',array('model'=>$model),true);
$taskList=$this->renderPartial('task_list',array('model'=>$model,'tasks'=>$tasks),true);
?>
<br>
<hr>

<?php $this->widget('application.extensions.booster.TbTabs',array(
    'type'=>'tabs',
    'tabs'=>array(
        array(
            'label'=>'Overview',
            'content'=>$overview,
            'active'=>true,
         ),
          array('label' => 'Tasks', 'content' =>$taskList,'itemOptions'=>array('id'=>$model->id.'task_view')),
          array('label' => 'Logs', 'content' => 'Comming soon'),
        
        
    )
    
));

?>

<script>
    $(function(){
    $("#<?=$model->id?>task_view").on('click',function(){
          $("task_grid_view").yiiGridView.update("task_grid_view");   
    });
    });
   
</script>



