<?php
$user = User::model()->findByPk(Yii::app()->user->id);
if (!empty($_GET['from']) && $_GET['from'] == 'return') {
	$listlink = ['Return List' => array('return/list')];
} else if (!empty($user->extra['3pl_portal']) && $user->extra['3pl_portal'] == 'new') {
	if (in_array($model->type, [1010,1020,1030])) {
		$listlink = ['Inbound List' => array('tasknv/inbound')];
	} else {
		$listlink = ['B2C List' => array('tasknv/bioc')];
	}
} else {
	$listlink = ['Task List' => array('task/index/dpt_id/'.$model->job->dpt_id.'/org_id/'.$model->job->org_id)];
}
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array_merge($listlink, array(
		'Update Task',
	)),
));
?>
<h2>Update Task-<?=!empty($model) ? $model->getNo() : ''?></h2>
<hr>
<?php
$main = $this->renderPartial('task_main', array('model' => $model), true);
$tabs = [];
$tabs[] = array(
	'label' => 'Main',
	'content' => $main,
	'active' => !in_array($model->type, [3050]) ? true : false,
);

foreach ($model->subTasks as $sub) {
	$task = $this->renderPartial('tab_task', array('model' => $sub), true);
	$tabs[] = array(
		'label' => $sub->getType(),
		'content' => $task,
		'active' => in_array($sub->type, [3050]) ? true : false,
	);
}
if($model->mdata['isSpecial']){
	foreach ($model->subTasks as $sub) {
		if (in_array($sub->type, [2110,2120, 3050])) {
			$tabs[] = array(
				'label' => 'Attachment',
				'content' => $this->renderPartial('_task_211_file', array('model' => $sub), true),
				'active' => in_array($sub->type, [3050]) ? true : false,
			);
		}
	}
}


$tab_task = $this->renderPartial('tab_task', array('model' => $model), true);
$this->widget('application.extensions.booster.TbTabs', array(
	'id' => 'viw-task-' . $model->id,
	'type' => 'tabs',
	'tabs' => $tabs,
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