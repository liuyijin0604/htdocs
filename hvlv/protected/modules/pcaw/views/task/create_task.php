<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
           'Task List'=>array('task/index/dpt_id/'.$model->job->dpt_id.'/org_id/'.$model->job->org_id),
            'Create',
	),
));
?>
<h1><?=$model->job->no;?> - <?=$model->getType();?></h1>
<?php echo $this->renderPartial('task_form', array('model'=>$model)); ?>