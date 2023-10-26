<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
            'Task List'=>array('cartage/task'),
         
	),
));
?>
<h2>Update Task</h2>
<?php
$main = $this->renderPartial('_form', array('model' => $model), true);
$tabs = [];
$tabs[] = array(
	'label' => 'Main',
	'content' => $main,
	'active' => true,
);

$file = $this->renderPartial('_files', array('model' => $model), true);
$tabs[] = array(
	'label' => 'Files',
	'content' => $file,
);
$this->widget('application.extensions.booster.TbTabs', array(
	'id' => 'viw-task-' . $model->id,
	'type' => 'tabs',
	'tabs' => $tabs,
));
?>