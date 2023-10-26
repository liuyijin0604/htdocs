<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
           'Task List',
	),
));
?>
<h2>Jobs</h2>
<div style="right: 220px;position: absolute; top: 100px;">
<a class="tab_link" href="<?=$this->createUrl('task/create');?>" title="New Job"><div class="icon" style="background-position:-16px 0"></div> New Job</a> &nbsp;
</div>

<?php $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'job_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
        array('name'=>'no','type'=>'raw','value'=>'"<a href=\"".Yii::app()->createUrl("pcaw/task/jobUpdate",array("id"=>$data->id))."\">".$data->no."</a>"'),
          array('name' => 'type', 'value' => '$data->getType()', 
			'filter'=>CHtml::dropDownList('WmsJob[type]', $model->type, $this->t(WmsJob::$types), array('prompt'=>$this->t('All'),'class'=>'form-control')),),
           array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('WmsJob[status]', $model->status, $this->t(WmsJob::$states), array('prompt'=>$this->t('All'),'class'=>'form-control')),),
        array('name' => 'cust_name', 'value' => 'empty($data->customer)? "" : $data->customer->shortName(2)'),
		'po',
		'ref',
           'created',
  
    ),
   
        
    
));