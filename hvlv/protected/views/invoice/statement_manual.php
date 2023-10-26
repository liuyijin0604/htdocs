<h1><?=$this->t('Generate Statement Through Manual Selection');?></h1>

<div class="form" style="height: 600px;">
	<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'gen-state-manual',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('invoice/genStateManual'),
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
));?>

	<div class="row rowcol">
		<label>Customer:</label>
		<?php echo CHtml::hiddenField('agent_id');
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
	<br>
	<label><input type="checkbox" name="excludedetails" value="1" /> Exclude Details</label>
	<br>

	<?php
	$inv = new Invoice('search');
	$inv->unsetAttributes();
	if (!empty($_GET['Invoice'])) {
		$inv->attributes = $_GET['Invoice'];
	}
	if (empty($_GET['toids'])) {
		$inv->to_id = -1;
	} else {
		if (!empty($_GET['toids'])) {
			$orgs = Org::model()->findAll('`by` = :to_id', [':to_id' => $_GET['toids']]);
			$_GET['toids'] = [$_GET['toids']];
			foreach ($orgs as $org) {
				$_GET['toids'][] = $org->id;
			}
		}
		$inv->toids = $_GET['toids'];
	}
	unset($inv->currency);
	$ec = new CDbCriteria;
	$ec->condition = 'status IN (1,2,3,7) AND total > 0';
	$ec->order = 'to_id DESC';

	$this->widget('zii.widgets.grid.CGridView', [
		'id' => 'outinv-grid',
		'cssFile' => false,
		'dataProvider' => $inv->search(false, 0, 't.date ASC, t.id ASC', $ec),
		'filter' => $inv,
		'selectableRows' => 2,
		'columns' => [
			array(
				'id'=>'selectedItems',
				'class'=>'CCheckBoxColumn',
			),
			['name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("invoice/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'],
			['name' => 'to_name', 'value' => 'empty($data->cust) ? "" : $data->cust->shortName(5)'],
			['name' => 'suborg_name', 'value' => '$data->subOrgName(5)'],
			['name' => 'status', 'value' => '$data->getStatus()',
				'filter' => CHtml::dropDownList('Invoice[status]', $inv->status, $this->t($inv::$states), ['prompt' => $this->t('All')])],
			['name' => 'type', 'value' => '$data->getType()',
				'filter' => CHtml::dropDownList('Invoice[type]', $inv->type, $this->t($inv::$types), ['prompt' => $this->t('All')])],
			['name' => 'dpmt', 'value' => '$data->getDpmt()',
				'filter'=>CHtml::dropDownList('Invoice[dpmt]', $inv->dpmt, $this->t(Invoice::$dpmts), ['prompt'=>$this->t('All')]),],
			'date',
			'due',
			['name' => 'total', 'value' => '$data->getCurrency()." ".$data->total'],
			['header' => 'Balance', 'value' => '$data->getBalance()'],
		],
	]);
	?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Generate PDF', ['name' => 'act_btn']); ?>
		<?php echo CHtml::submitButton('Generate Excel', ['name' => 'act_btn']); ?>
		<?php echo CHtml::submitButton('Generate ZIP', ['name' => 'act_btn']); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Generate WGCN Storage Excel', ['name' => 'act_btn']); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>

<script>
$(function() {
	var win = $("#jqmw_<?=$_GET['tabid'];?>");

	$('#agent_id', win).on('change', function() {
		$('#outinv-grid', win).yiiGridView('update', {
			data: 'toids=' + $('#agent_id').val()
		});
	});
});
</script>