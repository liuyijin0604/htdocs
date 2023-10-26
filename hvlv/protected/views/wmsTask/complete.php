<div class="form" style="height: 800px;">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'wms-task-complete-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row rowcol">
		<?php echo CHtml::label('Agent', 'owner_id'); ?>
		<?php echo CHtml::hiddenField('owner_id', '', array('data-ov' => ''));
		$acname1 = empty($_GET["tabid"]) ? 'agent_ac' : $_GET["tabid"] . '_agent_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $acname1,
			'sourceUrl' => array('org/ownerSuggest'),
			'value' => '',
			'options' => array(
				'showAnim' => 'fold',
				'minLength' => 2,
				'delay' => 200,
				'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]).trigger("change"); return false; }',
				'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
			),
			'htmlOptions' => array(
				'size' => '30',
			),
		));
		?>
	</div>

	<div class="row">
		<?php
		$task = new WmsTask('search');
		$task->unsetAttributes();
		if (!empty($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}

		if (!empty($_GET['owner_id'])) {
			$task->cust_name = $_GET['owner_id'];
		} else {
			$task->cust_name = 'none';
		}

		$ec = new CDbCriteria;
		$ec->addCondition('t.status IN (20, 30)');

		$this->widget('zii.widgets.grid.CGridView', array(
			'id' => 'wms-task-complete-grid',
			'selectableRows' => 2,
			'cssFile' => false,
			'dataProvider' => $task->search(true, 30, $ec),
			'filter' => $task,
			'columns' => array(
				array(
					'id' => 'selectedItems',
					'class' => 'CCheckBoxColumn',
				),
				array('name' => 'id', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("wmsTask/update", ["id" => $data->id])."\" class=\"tab_link\" title=\"".$data->getNo()."\">".$data->getNo()." ".$data->getUpdate()."</a>"'),
				array('name' => 'status', 'value' => '$data->getStatus()', 'filter' => CHtml::dropDownList('WmsTask[status]', $task->status, ['20' => 'Scheduled', '30' => 'WIP'], ['empty' => 'All']),),
				array('name' => 'schd_time'),
			),
		));
		?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Confirm', array('id' => 'submit_btn')); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

<script>
$(function() {
	var tab = $('#jqmw_<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	$('#owner_id', panel).on('change', function() {
		tab.trigger('searchTask');
	});

	$('#<?=$_GET["tabid"];?>_agent_ac', panel).on('keyup', function() {
		if (!$('#<?=$_GET["tabid"];?>_agent_ac', panel).val()) {
			$('#owner_id').val('');
			tab.trigger('searchTask');
		}
	});

	tab.on('searchTask', function() {
		$.fn.yiiGridView.update('wms-task-complete-grid', {
			data: {'owner_id': $('#owner_id').val()}
		});
		$('#<?=$_GET["tabid"];?>_agent_ac', panel).prop('disabled', true);
	});
});
</script>