<h1><?=$this->t('Manage WmsTask');?></h1>

<div class="form">
	<div class="rowcol rowleft" style="margin-right: 10%; width: 40%">
	<h3><?=$this->t('Operator');?></h3>
	<?php
	$operator = new WmsTaskOperator('search');
	$operator->unsetAttributes();
	if (isset($_GET['WmsTaskOperator'])) {
		$operator->attributes = $_GET['WmsTaskOperator'];
	}
	$this->widget('application.extensions.editablegrid.CEditableGridView', array(
		'id' => 'wms-task-operator-grid',
		'cssFile' => false,
		'dataProvider' => $operator->search(),
		'summaryText' => '',
		'formUrl' => $this->createUrl('wmsTaskManage/operatorGrid', array('id' => $operator->id)),
		'afterSave' => 'function(r) {
			if (r.done == true) {
				myApp.notice(r.msg, 5000);

			} else {
				myApp.alert(r.msg, false);
			}
			return r.done;
		}',
		'columns' => array(
			array('name' => 'op_id', 'value' => '$data->getOpName()', 'class' => 'CEditableColumn', 'type' => 'autocomplete', 'acOptions' => array('source' => 'wmsTaskManage/operatorSuggest')),
			array('name' => 'status', 'value' => '$data->getStatus()'),
			array(
				'class' => 'CEditableButtonColumn',
				'template' => '{edit}{cancel}{save}{delete}{active}{manage}',
				'htmlOptions' => ['style' => 'width: 85px'],
				'buttons' => array(
					'delete' => array(
						'imageUrl' => false,
						'options' => array('class' => 'delete_btn'),
						'url' => 'Yii::app()->createUrl("wmsTaskManage/operatorGridDelete", ["id" => $data->id])',
						'visible' => '$data->status == 1',
					),
					'active' => array(
						'imageUrl' => false,
						'options' => array('class' => 'ajax_link grid_swap_btn'),
						'url' => 'Yii::app()->createUrl("wmsTaskManage/operatorGridActive", ["id" => $data->id])',
						'visible' => '$data->status != 1',
						'label' => '',
					),
					'manage' => array(
						'imageUrl' => false,
						'options' => array('class' => 'jqm_link', 'data-win-class' => 'XXL'),
						'url' => 'Yii::app()->createUrl("wmsTaskManage/rankOperatorTask", ["id" => $data->id])',
						'label' => 'Manage',
					),
				),
			),
		),
	)); ?>
	</div>

	<div class="rowcol" style="width: 40%; display: none">
	<h3><?=$this->t('Object');?></h3>
	<?php
	$object = new WmsTaskObject('search');
	$object->unsetAttributes();
	if (isset($_GET['WmsTaskObject'])) {
		$object->attributes = $_GET['WmsTaskObject'];
	}
	$this->widget('application.extensions.editablegrid.CEditableGridView', array(
		'id' => 'wms-task-object-grid',
		'cssFile' => false,
		'dataProvider' => $object->search(),
		'summaryText' => '',
		'formUrl' => $this->createUrl('wmsTaskManage/objectGrid', array('id' => $object->id)),
		'afterSave' => 'function(r) {
			if (r.done == true) {
				myApp.notice(r.msg, 5000);
			} else {
				myApp.alert(r.msg, false);
			}
			return r.done;
		}',
		'columns' => array(
			array('name' => 'name', 'class' => 'CEditableColumn'),
			array('name' => 'type', 'value' => '$data->getType()', 'class' => 'CEditableColumn', 'type' => 'list', 'filter' => WmsTaskObject::$types),
			array('name' => 'status', 'value' => '$data->getStatus()'),
			array(
				'class' => 'CEditableButtonColumn',
				'template' => '{edit}{cancel}{save}{delete}{active}',
				'buttons' => array(
					'delete' => array(
						'imageUrl' => false,
						'options' => array('class' => 'delete_btn'),
						'url' => 'Yii::app()->createUrl("wmsTaskManage/objectGridDelete", ["id" => $data->id])',
						'visible' => '$data->status == 1',
					),
					'active' => array(
						'imageUrl' => false,
						'options' => array('class' => 'ajax_link grid_swap_btn'),
						'url' => 'Yii::app()->createUrl("wmsTaskManage/objectGridActive", ["id" => $data->id])',
						'visible' => '$data->status != 1',
						'label' => '',
					),
				),
			),
		),
	)); ?>
	</div>
</div>

<script type="text/javascript">
$(function() {
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.bind('onOpen', function() {
		$('#wms-task-object-grid', panel).yiiGridView('update');
		$('#wms-task-operator-grid', panel).yiiGridView('update');
	});

	$(panel).on('click', 'a.ajax_link', function() {
		if (!confirm('Are you sure you want to active this item?')) {
			return false;
		}
	});

	$(panel).on('success', 'a.ajax_link', function() {
		$('#wms-task-object-grid', panel).yiiGridView('update');
		$('#wms-task-operator-grid', panel).yiiGridView('update');
	});
});
</script>