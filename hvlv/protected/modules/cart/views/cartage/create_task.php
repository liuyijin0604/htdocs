<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
            'Task List'=>array('cartage/task'),
         
	),
));
?>
<h2>Create Task</h2>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>