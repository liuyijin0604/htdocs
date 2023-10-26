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

<?php
if(preg_match('/WDT/', $model->no)&&!empty($model->shipments))
{
	$shipment = $model->shipments[0];
	$shipmentModel = $shipment->getParentShipment();
	echo "<h2>Change Label</h2>";
	$log = new DfeChangeLabel();
	$log->pid = $shipmentModel->id;
	$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>$_GET["tabid"].'_log-grid-change',
				'cssFile' => false,
				'summaryText'=>'',
				'dataProvider'=> $log->search(),
				'columns'=>array(
					array(
			            'name'=>'dfe_ref','header'=>'3PL_label','value'=>'str_replace("PICKUP", "WDT", $data->dfe_ref)'
			        ),
					['name'=>'sn'],
					['name'=>'allied_ref'],
					['name'=>'create'],
					['name'=>'user_id','value'=>'empty($data->user_id)?$data->user_id:User::model()->findByPk($data->user_id)->getName()']
					,
				),
			));

	echo "<h2>Label Relation</h2>";
	$log = new ImportsShipmentRelations();
	$log->pid = $shipmentModel->id;
	$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>$_GET["tabid"].'_log-grid-change_relation',
				'cssFile' => false,
				'summaryText'=>'',
				'dataProvider'=> $log->search(),
				'columns'=>array(
					['name'=>'pid','header'=>'Original','value'=>'str_replace("PICKUP", "WDT", $data->original->ref)'],
					['name'=>'cid','header'=>'Change','value'=>'$data->sub_shipment->ref'],
					['name'=>'label_lo','header'=>'Original_label_from'],
					['name'=>'label_hi','header'=>'Original_label_to'],
					['header'=>'Change_label_from','value'=>'1'],
					['header'=>'Change_label_to','value'=>'$data->sub_shipment->pkg'],
					['name'=>'blue_label']
				),
			));

}

?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'console-notes-form',
	'enableClientValidation'=>true,
	'action'=>$this->createUrl('imcoConsol/notes', array('id' => $model->id)),
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
	
	$('form#console-notes-form', panel).on({'success': function(e,r){
			$('#<?=$_GET["tabid"]?>_log-grid', panel).yiiGridView('update');
		},
		'reset': true
	}
	);
});
</script>