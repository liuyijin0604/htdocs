<?php
echo "<h2>Log</h2>";
$modelClass = get_class($model);
if($modelClass=='ImParcelArchive')
{
	$modelClass='ImParcel';
	$log = new LogArchive;
}else
{
	$log = new Log;
}

$log->model = $modelClass;
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
if($model->agent_id = 3321)
{
echo "<h2>Change Label</h2>";
$log = new DfeChangeLabel();
$log->pid = $model->id;
$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>$_GET["tabid"].'_log-grid-change',
			'cssFile' => false,
			'summaryText'=>'',
			'dataProvider'=> $log->search(),
			'columns'=>array(
				array(
		            'name'=>'dfe_ref'
		        ),
				['name'=>'sn'],
				['name'=>'allied_ref'],
				['name'=>'create'],
				['name'=>'user_id','value'=>'empty($data->user_id)?$data->user_id:User::model()->findByPk($data->user_id)->getName()']
				,
			),
		));
}

echo "<h2>Customs Log</h2>";
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
		['name' => 'type', 'value' => '$data->getType()', 'filter' => CHtml::dropDownList('Edimsg[type]', $m->type, $this->t(Edimsg::$types), array('prompt' => $this->t('All')))],
		'mid',
		['name' => 'sender', 'value' => '$data->getSender()', ],
		['name' => 'receiver', 'value' => '$data->getRecvr()', ],
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}{error}',
			'buttons'=>array(
				'view' => array(
					'imageUrl'=>false,
					'url' => 'Yii::app()->createUrl("edimsg/view", array("id" => $data->id))',
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'error' => array(
					'imageUrl' => false,
					'visible' => '$data->avaliChangeError()',
					'url' => 'Yii::app()->createUrl("edimsg/confirmError", array("id" => $data->id, "tabid" => "'.$_GET['tabid'].'"))',
					'options' => array('class' => 'jqm_link grid_delete_btn'),
				),
			),
		),
	),
)); ?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-notes-form',
	'enableClientValidation'=>true,
	'action'=>$this->createUrl('imParcel/notes', array('id' => $model->id)),
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
	
	$('form#shipment-notes-form', panel).on({'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_log-grid', panel).yiiGridView('update');
		},
		'reset': true
	}
	);
});
</script>