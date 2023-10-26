<?php
$log = new Log;
$log->model = get_class($model);
$log->lid = $model->id;
$dataProvider = $log->search();
$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>$_GET["tabid"].'_log-grid',
			'cssFile' => false,
			'summaryText'=>'',
			'dataProvider'=> $dataProvider,
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
	'action'=>$this->createUrl('siReconcile/log', array('id' => $model->id)),
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));
?>
<h2>Add Notes</h2>

<?php echo $form->errorSummary($model); ?>

<div class="row">
	<?php echo CHtml::textArea('notes', $model->note, array('rows'=>4, 'cols' => 60)); ?>
</div>

<div class="row buttons">
	<?php echo CHtml::submitButton('Save'); ?>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"]?>');
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	tab.unbind('reload_reconcile_grid').bind('reload_reconcile_grid', function(){
		$('#<?=$_GET["tabid"];?>_reconcile_grid', tab.data('panel')).yiiGridView('update');
		return false;
	});

	$('form#shipment-notes-form', win).on({'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_log-grid', win).yiiGridView('update');
			tab.trigger('reload_reconcile_grid');
		},
		'reset': true
	}
	);

});
</script>