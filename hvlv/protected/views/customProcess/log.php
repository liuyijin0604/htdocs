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
	'id'=>'shipment-notes-form',
	'enableClientValidation'=>true,
	'action'=>$this->createUrl('customProcess/notes', array('id' => $model->id)),
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
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('form#shipment-notes-form', win).on({'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_log-grid', win).yiiGridView('update');
		},
		'reset': true
	   }
	);
});
</script>