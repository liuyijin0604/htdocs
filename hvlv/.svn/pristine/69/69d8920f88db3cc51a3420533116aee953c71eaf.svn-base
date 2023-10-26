<?php
$log = new Log;
$log->model = get_class($model);
// $log->lid = $model->id;
$tasks = $model->subTasks;
$ids = [$model->id];
$task_types = [$model->id => 'Main'];
foreach ($tasks as $task) {
	$ids[] = $task->id;
	$task_types[$task->id] = $task->getType();
}
$log->lid = $ids;

$ec = new CDbCriteria;
if ($model->type == 6010) {
	$ec->addCondition('t.type != 6');
}

$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>$_GET["tabid"].'_log-grid',
			'cssFile' => false,
			'summaryText'=>'',
			'dataProvider'=> $log->search(50, $ec),
			'columns'=>array(
				'time',
				array(
					'name'=>'user_id',
					'value'=>'$data->getUser()',
				),
				array(
					'name'=>'type',
					'value'=>'$data->getType()',
				),
				array(
					'header'=>'Tab',
					'value'=>function($data,$row)use($task_types){
        				return $task_types[$data->lid];
    				},
				),
				array(
					'name'=>'meta',
					'value'=>'$data->getExtra()',
				),
			),
		));
?>

<?php if ($model->type != 6010) { ?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wmsprod-notes-form',
	'enableClientValidation'=>true,
	'action'=>$this->createUrl('wmsTask/notes', array('id' => $model->id)),
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));
?>
<h2>Add Notes</h2>

<?php echo $form->errorSummary($model); ?>

<div class="row">
	<?php echo CHtml::textArea('notes', '', array('rows'=>4, 'cols' => 60)); ?>
</div>

<div class="row buttons">
	<?php echo CHtml::submitButton('Save'); ?>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php } ?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	
	$('form#wmsprod-notes-form', panel).on({'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_log-grid', panel).yiiGridView('update');
		},
		'reset': true
	}
	);
});
</script>