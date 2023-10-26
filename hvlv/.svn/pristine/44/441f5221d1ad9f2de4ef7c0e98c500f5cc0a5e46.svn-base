
<p>

<?php
$ec = new CDbCriteria;
$ec->addCondition('reconciled = 1');
?>

<?php $this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id'=> 'bank-rec-transactions-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search($ec),
	'filter'=>$model,
	'formUrl' => $this->createUrl('bank/editGrid', array('fid' => $model->id)),
	'afterSave' => "function(r){
		if(r.done == true){
			myApp.notice(r.msg, 5000);
		}else{
			myApp.alert(r.msg, false);
		}
		return r.done;
	}",
	'columns'=>array(
		'created',
		array('name' => 'desc', 'value' => '$data->getShortDesc()'),
		array('name' => 'debits', 'value' => '($data->debits != 0) ? $data->debits : ""', 'htmlOptions' => array('width' => '10%')),
		array('name' => 'credits', 'value' => '($data->credits != 0) ? $data->credits : ""', 'htmlOptions' => array('width' => '10%')),
		array('name' => 'cust_name', 'value' => '$data->getShortOrgName()'),
		array('header' => 'Status', 'type' => 'raw', 'value' => '$data->getStatus()', 'filter' => CHtml::dropDownList('BankStatement[reconciled]', $model->reconciled, $this->t(BankStatement::$states), array('prompt' => $this->t('All')))),
		array('header' => 'note', 'value' => '$data->getLastNote()', 'htmlOptions' => array('width' => '15%')),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{reconcile}{view}{log}',
			'buttons'=>array
			(
				'reconcile' => array(
					'imageUrl'=>false,
					'visible'=>'$data->reconciled == 0 ? true : false',
					'url' => 'Yii::app()->createUrl("bank/reconcile", ["fid" => $data->id])',
					// 'label' => '$data->reconciled > 0 ? "View" : "Reconcile" ',
					'label' => 'Reconcile',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'data-win-class' => 'XL', 'label'=>$this->t('Reconcile'), 'title' => '$data->desc'),
				),

				'view' => array(
					'imageUrl'=>false,
					'visible'=>'$data->reconciled == 1 && ($data->credits != 0 || $data->debits != 0) ? true : false',
					'url' => 'Yii::app()->createUrl("bank/reconcileView", ["fid" => $data->id])',
					// 'label' => '$data->reconciled > 0 ? "View" : "Reconcile" ',
					'label' => 'View',
					'options' => array('class' => 'jqm_link grid_view_btn', 'data-win-class' => 'XL', 'label'=>$this->t('View'), 'title' => '$data->desc'),
				),

				'log' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'),
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("bank/log", ["fid" => $data->id])',
					'label' => 'Log'
				),
			),
		),
		array(
			'class' => 'CEditableButtonColumn',
			'template' => '{edit} {cancel} {save} {delete}',
			'buttons' => array(
				'delete' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("bank/delete", ["id" => $data->id])',
					'visible' => 'Acl::hasAccess("B:bank/delete") && $data->reconciled == 0',
					'options' => array('class' => 'delete_btn'),
				),
			),
		),
	),
)); ?>
</p>

<script type="text/javascript">
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#bank-rec-transactions-grid', panel).yiiGridView('update');
	});
</script>
