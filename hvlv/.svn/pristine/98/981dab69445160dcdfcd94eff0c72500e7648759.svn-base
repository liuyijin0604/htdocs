<?php
$log = new Log;
$log->model = get_class($model);
$log->lid = $model->id;
$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>$_GET["tabid"].'_log-grid',
			'cssFile' => false,
			'summaryText'=>'',
			'dataProvider'=> $log->search(),
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
		            'name'=>'meta',
		            'value'=>'$data->getExtra()',
		        ),
			),
		));
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wmsprod-notes-form',
	'enableClientValidation'=>true,
	'action'=>$this->createUrl('wmsProd/notes', array('id' => $model->id)),
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