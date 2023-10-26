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
$m = new Edimsg('search');
if(isset($_GET['Edimsg'])){
	$m->unsetAttributes();
	$m->attributes=$_GET['Edimsg'];
}
$m->fid = $model->id;
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'edimsg-grid-'.$_GET['tabid'],
	'cssFile' => false,
	'dataProvider'=>$m->search(),
	'filter'=>$m,
	'columns'=>array(
		'dt',
		array(
            'name'=>'status',
            'value'=>'$data->getStatus()',
			'filter'=>CHtml::dropDownList('Edimsg[status]', $m->status, $this->t(Edimsg::$states), array('prompt'=>$this->t('All'))),
        ),
		['name' => 'type', 'value' => '$data->getType()', ],
		'mid',
		['name' => 'sender', 'value' => '$data->getSender()', ],
		['name' => 'receiver', 'value' => '$data->getRecvr()', ],
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}',
			'buttons'=>array(
				'view' => array(
					'imageUrl'=>false,
					'url' => 'Yii::app()->createUrl("edimsg/view", array("id" => $data->id))',
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
			),
		),
	),
)); ?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'dmawb-console-notes-form',
	'enableClientValidation'=>true,
	'action'=>$this->createUrl('dmawbConsol/notes', array('id' => $model->id)),
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
	
	$('form#dmawb-console-notes-form', panel).on({'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_log-grid', panel).yiiGridView('update');
		},
		'reset': true
	}
	);
});
</script>